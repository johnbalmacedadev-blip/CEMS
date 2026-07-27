<?php

namespace App\Console\Commands;

use App\Models\Employee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportEmployeesCommand extends Command
{
    protected $signature = 'import:employees
                            {--file= : Path to JSON export}
                            {--fresh : Truncate employees table before import}
                            {--dry-run : Parse and report without writing}';

    protected $description = 'Import employees from EMPLOYEE DETAILS Excel JSON export';

    public function handle(): int
    {
        $path = $this->option('file') ?: storage_path('app/employees_import.json');
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

        if ($this->option('fresh')) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            Employee::query()->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->warn('Employees table truncated.');
        }

        $created = 0;
        $updated = 0;
        $errors = 0;

        $existing = [];
        foreach (Employee::query()->get(['id', 'first_name', 'last_name', 'middle_name']) as $emp) {
            $existing[$this->nameKey($emp->first_name, $emp->middle_name, $emp->last_name)] = $emp->id;
        }

        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach ($rows as $row) {
            try {
                DB::transaction(function () use ($row, &$existing, &$created, &$updated) {
                    $first = trim((string) ($row['first_name'] ?? ''));
                    $last = trim((string) ($row['last_name'] ?? ''));
                    if ($first === '' || $last === '') {
                        throw new \RuntimeException('Missing name');
                    }

                    $middle = $row['middle_name'] ?? null;
                    $key = $this->nameKey($first, $middle, $last);

                    $contractType = $row['contract_type'] ?? null;
                    if ($contractType && ! in_array($contractType, ['PROBATIONARY', 'REGULAR'], true)) {
                        $contractType = null;
                    }

                    $payload = [
                        'first_name' => $first,
                        'middle_name' => $middle,
                        'last_name' => $last,
                        'contract_start' => $row['contract_start'] ?? null,
                        'contract_type' => $contractType,
                        'role' => $row['role'] ?? null,
                        'location' => $row['location'] ?? null,
                        'sss' => $row['sss'] ?? null,
                        'philhealth' => $row['philhealth'] ?? null,
                        'pagibig' => $row['pagibig'] ?? null,
                        'birthdate' => $row['birthdate'] ?? null,
                        'status' => $row['status'] ?? 'active',
                        'notes' => $row['notes'] ?? null,
                    ];

                    if (isset($existing[$key])) {
                        Employee::where('id', $existing[$key])->update($payload);
                        $updated++;
                    } else {
                        $emp = Employee::create($payload);
                        $existing[$key] = $emp->id;
                        $created++;
                    }
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
                ['Updated', $updated],
                ['Errors', $errors],
                ['Total employees', Employee::count()],
            ]
        );

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function nameKey(?string $first, ?string $middle, ?string $last): string
    {
        $parts = [
            strtoupper(trim((string) $first)),
            strtoupper(trim((string) ($middle ?? ''))),
            strtoupper(trim((string) $last)),
        ];

        return implode('|', $parts);
    }
}
