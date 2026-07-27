<?php

namespace App\Console\Commands;

use App\Models\BranchLocation;
use App\Models\Make;
use App\Models\Vehicle;
use App\Models\VehicleModel;
use App\Models\VehicleRegistration;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportVehicleRegistrationCommand extends Command
{
    protected $signature = 'import:vehicle-registration
                            {--file= : Path to JSON export (default: storage/app/vehicle_registration_import.json)}
                            {--dry-run : Parse and report without writing}';

    protected $description = 'Import vehicle registration records from Registration Flagship/Annex Excel JSON export';

    public function handle(): int
    {
        $path = $this->option('file') ?: storage_path('app/vehicle_registration_import.json');
        if (! is_file($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        $rows = json_decode(file_get_contents($path), true);
        if (! is_array($rows) || $rows === []) {
            $this->error('JSON file is empty or invalid.');

            return self::FAILURE;
        }

        $this->info('Rows to process: '.count($rows));
        if ($this->option('dry-run')) {
            $this->warn('Dry run — no database writes.');

            return self::SUCCESS;
        }

        $branches = [];
        foreach (BranchLocation::all() as $b) {
            $branches[strtolower($b->name)] = $b->id;
        }

        $vehicleByPlate = [];
        foreach (Vehicle::query()->get(['id', 'plate_number']) as $vehicle) {
            $vehicleByPlate[$this->normalizePlate($vehicle->plate_number)] = $vehicle->id;
        }

        $makeCache = [];
        $modelCache = [];

        $created = 0;
        $updated = 0;
        $vehiclesCreated = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach ($rows as $row) {
            try {
                DB::transaction(function () use (
                    $row,
                    $branches,
                    &$vehicleByPlate,
                    &$makeCache,
                    &$modelCache,
                    &$created,
                    &$updated,
                    &$vehiclesCreated
                ) {
                    $plate = $this->normalizePlate($row['plate_number'] ?? '');
                    if ($plate === '') {
                        throw new \RuntimeException('Missing plate');
                    }

                    $branchName = strtolower(trim((string) ($row['branch'] ?? '')));
                    $branchId = $branches[$branchName] ?? null;
                    if (! $branchId) {
                        throw new \RuntimeException('Unknown branch: '.($row['branch'] ?? ''));
                    }

                    if (! isset($vehicleByPlate[$plate])) {
                        [$makeId, $makeName] = $this->resolveMake($row['make'] ?? 'Unknown', $makeCache);
                        [$modelId, $modelName] = $this->resolveModel($makeId, $row['series'] ?? 'Unknown', $modelCache);
                        $vehicle = Vehicle::create([
                            'year' => (int) ($row['year'] ?? 2000),
                            'make_id' => $makeId,
                            'model_id' => $modelId,
                            'make' => $makeName,
                            'model' => $modelName,
                            'transmission' => 'Automatic',
                            'fuel_type' => 'Gasoline',
                            'kilometers' => 0,
                            'plate_number' => $plate,
                            'colour' => 'N/A',
                            'with_tools' => false,
                            'with_matting' => false,
                            'with_spare_tire' => false,
                            'purchase_price' => 0,
                            'posted_price' => 0,
                            'sold_price' => 0,
                            'purchased_from' => 'Registration Import',
                            'purchase_date' => $row['date'] ?? now()->toDateString(),
                            'spare_key' => false,
                            'status' => 'Released',
                            'branch_location_id' => $branchId,
                        ]);
                        $vehicleByPlate[$plate] = $vehicle->id;
                        $vehiclesCreated++;
                    }

                    $vehicleId = $vehicleByPlate[$plate];

                    $payload = [
                        'branch_location_id' => $branchId,
                        'date' => $row['date'],
                        'vehicle_id' => $vehicleId,
                        'renewal_reg_or' => $row['renewal_reg_or'],
                        'renewal_sop' => $row['renewal_sop'],
                        'smoke_na' => $row['smoke_na'],
                        'duplicate_plate' => $row['duplicate_plate'],
                        'migrate' => $row['migrate'],
                        'duplicate_cr' => $row['duplicate_cr'],
                        'pnp_clearance' => $row['pnp_clearance'],
                        'confirmation' => $row['confirmation'],
                        'remarks' => $this->nullableString($row['remarks'] ?? null),
                        'coc_no' => $this->nullableString($row['coc_no'] ?? null),
                        'status' => $this->nullableString($row['status'] ?? null),
                    ];

                    $existing = VehicleRegistration::query()
                        ->where('vehicle_id', $vehicleId)
                        ->whereDate('date', $row['date'])
                        ->where('branch_location_id', $branchId)
                        ->first();

                    if ($existing) {
                        $existing->update($payload);
                        $updated++;
                    } else {
                        VehicleRegistration::create($payload);
                        $created++;
                    }
                });
            } catch (\Throwable $e) {
                $errors++;
                $this->newLine();
                $this->error(
                    'Sheet '.($row['sheet'] ?? '?').
                    ' row '.($row['row'] ?? '?').
                    ' plate '.($row['plate_number'] ?? '?').
                    ': '.$e->getMessage()
                );
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->table(
            ['Metric', 'Count'],
            [
                ['Created registrations', $created],
                ['Updated registrations', $updated],
                ['Vehicles created', $vehiclesCreated],
                ['Errors', $errors],
                ['Total vehicle_registrations', VehicleRegistration::count()],
            ]
        );

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function normalizePlate(?string $plate): string
    {
        return strtoupper(preg_replace('/[^A-Z0-9]/i', '', (string) $plate) ?? '');
    }

    private function nullableString($value): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    /**
     * @param  array<string, array{0:int,1:string}>  $cache
     * @return array{0:int,1:string}
     */
    private function resolveMake(string $name, array &$cache): array
    {
        $key = strtoupper(trim($name));
        if ($key === '') {
            $key = 'UNKNOWN';
            $name = 'Unknown';
        }
        if (isset($cache[$key])) {
            return $cache[$key];
        }

        $make = Make::query()->whereRaw('UPPER(name) = ?', [$key])->first();
        if (! $make) {
            $make = Make::create([
                'name' => ucwords(strtolower($name)),
                'is_active' => true,
            ]);
        }

        $cache[$key] = [$make->id, $make->name];

        return $cache[$key];
    }

    /**
     * @param  array<string, array{0:int,1:string}>  $cache
     * @return array{0:int,1:string}
     */
    private function resolveModel(int $makeId, string $name, array &$cache): array
    {
        $trimmed = trim($name);
        if ($trimmed === '') {
            $trimmed = 'Unknown';
        }
        $key = $makeId.'|'.strtoupper($trimmed);
        if (isset($cache[$key])) {
            return $cache[$key];
        }

        $model = VehicleModel::query()
            ->where('make_id', $makeId)
            ->whereRaw('UPPER(name) = ?', [strtoupper($trimmed)])
            ->first();

        if (! $model) {
            $model = VehicleModel::create([
                'make_id' => $makeId,
                'name' => ucwords(strtolower($trimmed)),
                'is_active' => true,
            ]);
        }

        $cache[$key] = [$model->id, $model->name];

        return $cache[$key];
    }
}
