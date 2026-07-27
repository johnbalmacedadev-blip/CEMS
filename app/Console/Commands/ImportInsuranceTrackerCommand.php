<?php

namespace App\Console\Commands;

use App\Models\InsuranceTracker;
use App\Models\Vehicle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportInsuranceTrackerCommand extends Command
{
    protected $signature = 'import:insurance-tracker
                            {--file= : Path to JSON export}
                            {--fresh : Truncate insurance_tracker before import}
                            {--dry-run : Parse and report without writing}';

    protected $description = 'Import INSURANCE TRACKER Excel JSON into insurance_tracker';

    public function handle(): int
    {
        $path = $this->option('file') ?: storage_path('app/insurance_tracker_import.json');
        if (! is_file($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        $rows = json_decode(file_get_contents($path), true);
        if (! is_array($rows) || $rows === []) {
            $this->error('JSON file is empty or invalid.');

            return self::FAILURE;
        }

        $this->info('Rows to import: '.count($rows));
        if ($this->option('dry-run')) {
            $this->warn('Dry run — no database writes.');

            return self::SUCCESS;
        }

        if ($this->option('fresh')) {
            InsuranceTracker::query()->truncate();
            $this->warn('insurance_tracker truncated.');
        }

        $plateMap = [];
        foreach (Vehicle::query()->whereNotNull('plate_number')->where('plate_number', '!=', '')->get(['id', 'plate_number']) as $v) {
            $key = strtoupper(preg_replace('/\s+/', '', (string) $v->plate_number));
            if ($key !== '' && ! isset($plateMap[$key])) {
                $plateMap[$key] = (int) $v->id;
            }
        }

        $created = 0;
        $linked = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach ($rows as $row) {
            try {
                DB::transaction(function () use ($row, $plateMap, &$created, &$linked) {
                    $plate = strtoupper(preg_replace('/\s+/', '', (string) ($row['number'] ?? '')));
                    $vehicleId = ($plate !== '' && isset($plateMap[$plate])) ? $plateMap[$plate] : null;
                    if ($vehicleId) {
                        $linked++;
                    }

                    InsuranceTracker::create([
                        'showroom' => $row['showroom'] ?? null,
                        'sales' => $row['sales'] ?? null,
                        'year' => $row['year'] !== null ? mb_substr((string) $row['year'], 0, 20) : null,
                        'make' => $row['make'] ?? null,
                        'model' => $row['model'] ?? null,
                        'number' => $plate !== '' ? mb_substr($plate, 0, 50) : null,
                        'vehicle_id' => $vehicleId,
                        'transaction' => $row['transaction'] ?? null,
                        'source' => $row['source'] ?? null,
                        'reservation_date' => $row['reservation_date'] ?? null,
                        'release_date' => $row['release_date'] ?? null,
                        'amount' => isset($row['amount']) ? round((float) $row['amount'], 2) : null,
                    ]);
                    $created++;
                });
            } catch (\Throwable $e) {
                $errors++;
                $this->newLine();
                $this->error('Row '.($row['row'] ?? '?').': '.$e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->table(
            ['Metric', 'Count'],
            [
                ['Created', $created],
                ['Linked to vehicles', $linked],
                ['Errors', $errors],
                ['Total insurance records', InsuranceTracker::count()],
            ]
        );

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }
}
