<?php

namespace App\Console\Commands;

use App\Models\UnitMasterlist;
use App\Models\Vehicle;
use Illuminate\Console\Command;

class ImportUnitsMasterlistCommand extends Command
{
    protected $signature = 'import:units-masterlist
                            {--file= : Path to JSON export}
                            {--fresh : Truncate units_masterlist before import}
                            {--dry-run : Parse and report without writing}';

    protected $description = 'Import Units Masterlist from Pricelist MASTERLIST JSON export and link plates to vehicles';

    public function handle(): int
    {
        $path = $this->option('file') ?: storage_path('app/units_masterlist_import.json');
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
        }

        $vehicleByPlate = [];
        foreach (Vehicle::query()->get(['id', 'plate_number']) as $vehicle) {
            $key = UnitMasterlist::normalizePlate($vehicle->plate_number);
            if ($key !== '') {
                $vehicleByPlate[$key] = $vehicle->id;
            }
        }

        if ($this->option('fresh') && ! $this->option('dry-run')) {
            UnitMasterlist::query()->delete();
            $this->warn('Cleared existing units_masterlist rows.');
        }

        $created = 0;
        $updated = 0;
        $linked = 0;
        $unlinked = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach ($rows as $row) {
            try {
                $plate = isset($row['plate']) ? strtoupper(trim((string) $row['plate'])) : null;
                if ($plate === '') {
                    $plate = null;
                }

                $makeModel = trim((string) ($row['make_model'] ?? ''));
                if ($makeModel === '') {
                    $errors++;
                    $bar->advance();
                    continue;
                }

                $payload = [
                    'list_number' => $this->toInt($row['row_num'] ?? null),
                    'make_model' => $makeModel,
                    'plate_number' => $plate,
                    'variant' => $this->nullableString($row['variant'] ?? null),
                    'transmission' => $this->nullableString($row['transmission'] ?? null),
                    'fuel_type' => $this->nullableString($row['fuel_type'] ?? null),
                    'year' => $this->formatYear($row['year'] ?? null),
                    'mileage' => $this->toInt($row['mileage'] ?? null),
                    'price' => $this->toDecimal($row['price'] ?? null),
                    'low_down_payment_option' => $this->nullableString($row['low_down_payment_option'] ?? null),
                    'low_monthly_option' => $this->nullableString($row['low_monthly_option'] ?? null),
                    'vehicle_id' => null,
                ];

                if ($plate) {
                    $key = UnitMasterlist::normalizePlate($plate);
                    if (isset($vehicleByPlate[$key])) {
                        $payload['vehicle_id'] = $vehicleByPlate[$key];
                        $linked++;
                    } else {
                        $unlinked++;
                    }
                } else {
                    $unlinked++;
                }

                if ($this->option('dry-run')) {
                    $created++;
                    $bar->advance();
                    continue;
                }

                $existing = null;
                if ($plate) {
                    $existing = UnitMasterlist::query()
                        ->whereRaw("UPPER(REPLACE(REPLACE(COALESCE(plate_number, ''), ' ', ''), '-', '')) = ?", [
                            UnitMasterlist::normalizePlate($plate),
                        ])
                        ->first();
                }

                if ($existing) {
                    $existing->update($payload);
                    $updated++;
                } else {
                    UnitMasterlist::create($payload);
                    $created++;
                }
            } catch (\Throwable $e) {
                $errors++;
                $this->newLine();
                $this->error($e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Created: {$created}");
        $this->info("Updated: {$updated}");
        $this->info("Linked to vehicle profile: {$linked}");
        $this->info("No vehicle match: {$unlinked}");
        if ($errors) {
            $this->warn("Errors/skipped: {$errors}");
        }

        return self::SUCCESS;
    }

    private function nullableString($value): ?string
    {
        if ($value === null) {
            return null;
        }
        $s = trim((string) $value);

        return $s === '' ? null : $s;
    }

    private function formatYear($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            return (string) (int) $value;
        }

        return trim((string) $value);
    }

    private function toInt($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            return (int) round((float) $value);
        }
        if (preg_match('/([\d,]+)/', (string) $value, $m)) {
            return (int) str_replace(',', '', $m[1]);
        }

        return null;
    }

    private function toDecimal($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            return round((float) $value, 2);
        }
        $clean = preg_replace('/[^\d.]/', '', (string) $value);

        return $clean === '' ? null : round((float) $clean, 2);
    }
}
