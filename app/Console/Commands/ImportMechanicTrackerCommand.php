<?php

namespace App\Console\Commands;

use App\Models\MechanicJob;
use App\Models\Vehicle;
use Illuminate\Console\Command;

class ImportMechanicTrackerCommand extends Command
{
    protected $signature = 'import:mechanic-tracker
                            {--file= : Path to JSON export}
                            {--fresh : Truncate mechanic_jobs before import}
                            {--dry-run : Parse and report without writing}';

    protected $description = 'Import MECHANIC TRACKER Excel JSON into mechanic_jobs';

    public function handle(): int
    {
        $path = $this->option('file') ?: storage_path('app/mechanic_tracker_import.json');
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
            MechanicJob::query()->truncate();
            $this->warn('mechanic_jobs truncated.');
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
        $now = now();
        $chunkSize = 100;

        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach (array_chunk($rows, $chunkSize) as $chunk) {
            $payload = [];
            foreach ($chunk as $row) {
                try {
                    $date = $row['job_date'] ?? null;
                    if (! $date) {
                        throw new \RuntimeException('Missing job_date');
                    }
                    $plate = strtoupper(preg_replace('/\s+/', '', (string) ($row['plate_number'] ?? '')));
                    $vehicleId = ($plate !== '' && isset($plateMap[$plate])) ? $plateMap[$plate] : null;
                    if ($vehicleId) {
                        $linked++;
                    }

                    $payload[] = [
                        'job_date' => $date,
                        'job_type' => in_array($row['job_type'] ?? '', ['Internal', 'External'], true)
                            ? $row['job_type']
                            : 'Internal',
                        'mechanic' => $this->trim($row['mechanic'] ?? null, 255),
                        'category' => $this->trim($row['category'] ?? null, 100),
                        'year_model' => $this->trim($row['year_model'] ?? null, 255),
                        'plate_number' => $plate !== '' ? mb_substr($plate, 0, 50) : null,
                        'vehicle_id' => $vehicleId,
                        'endorse' => $this->trim($row['endorse'] ?? null, 255),
                        'description' => $row['description'] ?? null,
                        'labor' => $row['labor'] ?? null,
                        'parts' => $row['parts'] ?? null,
                        'parts_cost' => isset($row['parts_cost']) && $row['parts_cost'] !== null
                            ? round((float) $row['parts_cost'], 2)
                            : null,
                        'status' => $this->trim($row['status'] ?? null, 100),
                        'unit_label' => $this->trim($row['unit_label'] ?? null, 255),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                } catch (\Throwable $e) {
                    $errors++;
                    $this->newLine();
                    $this->error('Row '.($row['row'] ?? '?').' ('.($row['sheet'] ?? '?').'): '.$e->getMessage());
                }
                $bar->advance();
            }

            if ($payload !== []) {
                MechanicJob::insert($payload);
                $created += count($payload);
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->table(
            ['Metric', 'Count'],
            [
                ['Created', $created],
                ['Linked to vehicles', $linked],
                ['Errors', $errors],
                ['Internal', MechanicJob::where('job_type', 'Internal')->count()],
                ['External', MechanicJob::where('job_type', 'External')->count()],
                ['Total', MechanicJob::count()],
            ]
        );

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function trim(?string $value, int $max): ?string
    {
        if ($value === null) {
            return null;
        }
        $value = trim($value);

        return $value === '' ? null : mb_substr($value, 0, $max);
    }
}
