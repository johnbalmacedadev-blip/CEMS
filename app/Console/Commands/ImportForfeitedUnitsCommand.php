<?php

namespace App\Console\Commands;

use App\Models\Make;
use App\Models\Vehicle;
use App\Models\VehicleForfeitDetail;
use App\Models\VehicleModel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportForfeitedUnitsCommand extends Command
{
    protected $signature = 'import:forfeited-units
                            {--file= : Path to JSON export}
                            {--branch=Flagship : Branch/location name}
                            {--force : Set status to Forfeited even if currently Released/Available/Reserved}
                            {--dry-run : Parse and report without writing}';

    protected $description = 'Import forfeited units + forfeit details from FLAGSHIP- FORFEITREFUND Excel JSON export';

    public function handle(): int
    {
        $path = $this->option('file') ?: storage_path('app/flagship_forfeited_units_import.json');

        if (! is_file($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        $rows = json_decode(file_get_contents($path), true);
        if (! is_array($rows) || $rows === []) {
            $this->error('JSON file is empty or invalid.');

            return self::FAILURE;
        }

        $branchName = trim((string) $this->option('branch'));
        $branch = \App\Models\BranchLocation::query()
            ->whereRaw('LOWER(name) = ?', [strtolower($branchName)])
            ->first();
        if (! $branch) {
            $this->error("Branch/location not found: {$branchName}");

            return self::FAILURE;
        }
        $branchId = $branch->id;
        $this->info("Branch location: {$branch->name} (id {$branchId})");
        $this->info('Forfeit rows to process: '.count($rows));

        if ($this->option('dry-run')) {
            $this->warn('Dry run — no database writes.');

            return self::SUCCESS;
        }

        $createdVehicles = 0;
        $updatedVehicles = 0;
        $skippedStatus = 0;
        $forfeitCreated = 0;
        $forfeitSkipped = 0;
        $errors = 0;

        $vehicleByPlate = [];
        foreach (Vehicle::query()->get(['id', 'plate_number', 'status']) as $vehicle) {
            $vehicleByPlate[$this->normalizePlate($vehicle->plate_number)] = $vehicle;
        }

        $makeCache = [];
        $modelCache = [];

        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach ($rows as $row) {
            try {
                DB::transaction(function () use (
                    $row,
                    $branchId,
                    &$vehicleByPlate,
                    &$makeCache,
                    &$modelCache,
                    &$createdVehicles,
                    &$updatedVehicles,
                    &$skippedStatus,
                    &$forfeitCreated,
                    &$forfeitSkipped
                ) {
                    $plate = $this->normalizePlate($row['plate_number'] ?? '');
                    if ($plate === '') {
                        throw new \RuntimeException('Missing plate number');
                    }

                    $amount = (float) ($row['forfeit_amount'] ?? 0);
                    $forfeitDate = $row['forfeit_date'] ?? null;
                    if ($amount <= 0 || ! $forfeitDate) {
                        throw new \RuntimeException('Missing forfeit amount or date');
                    }

                    $existing = $vehicleByPlate[$plate] ?? null;

                    if ($existing) {
                        $vehicle = Vehicle::findOrFail($existing->id);
                        $payload = [
                            'status' => 'Forfeited',
                            'branch_location_id' => $branchId,
                        ];
                        if (! $vehicle->make_id || ! $vehicle->year) {
                            [$makeId, $makeName] = $this->resolveMake($row['make'] ?? 'Unknown', $makeCache);
                            [$modelId, $modelName] = $this->resolveModel($makeId, $row['model'] ?? 'Unknown', $modelCache);
                            $payload = array_merge($payload, [
                                'year' => (int) ($row['year'] ?? $vehicle->year ?: 2000),
                                'make_id' => $makeId,
                                'model_id' => $modelId,
                                'make' => $makeName,
                                'model' => $modelName,
                            ]);
                        }
                        $vehicle->update($payload);
                        $updatedVehicles++;
                        $vehicleByPlate[$plate] = (object) [
                            'id' => $vehicle->id,
                            'plate_number' => $vehicle->plate_number,
                            'status' => 'Forfeited',
                        ];
                    } else {
                        [$makeId, $makeName] = $this->resolveMake($row['make'] ?? 'Unknown', $makeCache);
                        [$modelId, $modelName] = $this->resolveModel($makeId, $row['model'] ?? 'Unknown', $modelCache);

                        $vehicle = Vehicle::create([
                            'year' => (int) ($row['year'] ?? 2000),
                            'make_id' => $makeId,
                            'model_id' => $modelId,
                            'make' => $makeName,
                            'model' => $modelName,
                            'plate_number' => $plate,
                            'colour' => 'N/A',
                            'transmission' => 'Automatic',
                            'fuel_type' => 'Gasoline',
                            'kilometers' => 0,
                            'purchase_price' => 0,
                            'posted_price' => 0,
                            'sold_price' => 0,
                            'purchased_from' => 'N/A',
                            'purchase_date' => $row['date_reserved'] ?? $forfeitDate,
                            'with_tools' => false,
                            'with_matting' => false,
                            'with_spare_tire' => false,
                            'spare_key' => false,
                            'status' => 'Forfeited',
                            'branch_location_id' => $branchId,
                        ]);
                        $createdVehicles++;
                        $vehicleByPlate[$plate] = (object) [
                            'id' => $vehicle->id,
                            'plate_number' => $vehicle->plate_number,
                            'status' => 'Forfeited',
                        ];
                    }

                    $exists = VehicleForfeitDetail::query()
                        ->where('vehicle_id', $vehicle->id)
                        ->whereDate('forfeit_date', $forfeitDate)
                        ->where('forfeit_amount', $amount)
                        ->exists();

                    if ($exists) {
                        $forfeitSkipped++;

                        return;
                    }

                    VehicleForfeitDetail::create([
                        'vehicle_id' => $vehicle->id,
                        'previous_forfeit_date' => $row['previous_forfeit_date'] ?? null,
                        'forfeit_amount' => $amount,
                        'forfeit_date' => $forfeitDate,
                    ]);
                    $forfeitCreated++;
                });
            } catch (\Throwable $e) {
                $errors++;
                $this->newLine();
                $this->error('Row '.($row['row'] ?? '?').' plate '.($row['plate_number'] ?? '?').': '.$e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->table(
            ['Metric', 'Count'],
            [
                ['Created vehicles', $createdVehicles],
                ['Updated vehicles', $updatedVehicles],
                ['Forfeit details created', $forfeitCreated],
                ['Forfeit details skipped (dup)', $forfeitSkipped],
                ['Errors', $errors],
            ]
        );

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function normalizePlate(?string $plate): string
    {
        return strtoupper(preg_replace('/[^A-Z0-9]/i', '', (string) $plate) ?? '');
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

        $make = Make::query()
            ->whereRaw('UPPER(name) = ?', [$key])
            ->first();

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
        $key = $makeId.'|'.strtoupper(trim($name));
        if (trim($name) === '') {
            $name = 'Unknown';
            $key = $makeId.'|UNKNOWN';
        }
        if (isset($cache[$key])) {
            return $cache[$key];
        }

        $model = VehicleModel::query()
            ->where('make_id', $makeId)
            ->whereRaw('UPPER(name) = ?', [strtoupper(trim($name))])
            ->first();

        if (! $model) {
            $model = VehicleModel::create([
                'make_id' => $makeId,
                'name' => ucwords(strtolower($name)),
                'is_active' => true,
            ]);
        }

        $cache[$key] = [$model->id, $model->name];

        return $cache[$key];
    }
}
