<?php

namespace App\Console\Commands;

use App\Models\Employee;
use App\Models\EmployeeDeduction;
use App\Support\EmployeeDeductionsWorkbookParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ImportEmployeeDeductionsCommand extends Command
{
    protected $signature = 'import:employee-deductions
                            {--file= : Path to EMPLOYEE LIST xlsx}
                            {--fresh : Clear existing deduction rows for imported years}
                            {--dry-run : Parse and report without writing}';

    protected $description = 'Import START OF DEDUCTION tabs from EMPLOYEE LIST workbook';

    public function handle(): int
    {
        $path = $this->option('file') ?: storage_path('app/EMPLOYEE_LIST.xlsx');
        if (! is_file($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        if (! Schema::hasTable('employee_deductions')) {
            $this->error('Table employee_deductions is missing. Run migrations first.');

            return self::FAILURE;
        }

        $parser = new EmployeeDeductionsWorkbookParser();
        $sheets = $parser->listDeductionSheets($path);
        $this->info('Deduction sheets: '.(implode(', ', $sheets) ?: '(none)'));

        $rows = $parser->parse($path);
        $this->info('Parsed deduction lines: '.count($rows));
        if ($rows === []) {
            $this->warn('No deduction rows found.');

            return self::SUCCESS;
        }

        $byYear = [];
        foreach ($rows as $row) {
            $byYear[$row['year']] = ($byYear[$row['year']] ?? 0) + 1;
        }
        foreach ($byYear as $year => $count) {
            $this->line("  {$year}: {$count}");
        }

        if ($this->option('dry-run')) {
            $this->warn('Dry run — no database writes.');

            return self::SUCCESS;
        }

        $years = array_keys($byYear);
        if ($this->option('fresh')) {
            $deleted = EmployeeDeduction::query()->whereIn('year', $years)->delete();
            $this->warn("Cleared {$deleted} existing deduction row(s) for years ".implode(', ', $years).'.');
        }

        $employeeIndex = $this->buildEmployeeIndex();
        $created = 0;
        $linked = 0;
        $unlinked = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach (array_chunk($rows, 100) as $chunk) {
            $payload = [];
            $now = now();
            foreach ($chunk as $row) {
                try {
                    $employeeId = $this->matchEmployee($employeeIndex, $row['first_name'], $row['middle_name'] ?? null, $row['last_name']);
                    if ($employeeId) {
                        $linked++;
                    } else {
                        $unlinked++;
                    }
                    $payload[] = [
                        'employee_id' => $employeeId,
                        'year' => (int) $row['year'],
                        'period_key' => $row['period_key'],
                        'period_label' => $row['period_label'],
                        'category' => $row['category'],
                        'ee_share' => $row['ee_share'],
                        'er_share' => $row['er_share'],
                        'first_name' => $row['first_name'],
                        'middle_name' => $row['middle_name'] ?? null,
                        'last_name' => $row['last_name'],
                        'source_sheet' => $row['source_sheet'] ?? null,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                } catch (\Throwable $e) {
                    $errors++;
                    $this->newLine();
                    $this->error(($row['source_sheet'] ?? '?').' row '.($row['row'] ?? '?').': '.$e->getMessage());
                }
                $bar->advance();
            }

            if ($payload !== []) {
                $nextId = ((int) EmployeeDeduction::query()->max('id')) + 1;
                foreach ($payload as $i => $item) {
                    $payload[$i]['id'] = $nextId + $i;
                }
                EmployeeDeduction::insert($payload);
                $created += count($payload);
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->table(
            ['Metric', 'Count'],
            [
                ['Deduction lines created', $created],
                ['Linked to employee', $linked],
                ['Unlinked (name only)', $unlinked],
                ['Errors', $errors],
                ['Total deduction rows', EmployeeDeduction::count()],
            ]
        );

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @return array{byFull: array<string,int>, byFirstLast: array<string,int>}
     */
    private function buildEmployeeIndex(): array
    {
        $byFull = [];
        $byFirstLast = [];
        foreach (Employee::query()->get(['id', 'first_name', 'middle_name', 'last_name']) as $emp) {
            $full = $this->nameKey($emp->first_name, $emp->middle_name, $emp->last_name);
            $fl = $this->nameKey($emp->first_name, null, $emp->last_name);
            $byFull[$full] = (int) $emp->id;
            if (! isset($byFirstLast[$fl])) {
                $byFirstLast[$fl] = (int) $emp->id;
            }
        }

        return compact('byFull', 'byFirstLast');
    }

    /**
     * @param  array{byFull: array<string,int>, byFirstLast: array<string,int>}  $index
     */
    private function matchEmployee(array $index, string $first, ?string $middle, string $last): ?int
    {
        $full = $this->nameKey($first, $middle, $last);
        if (isset($index['byFull'][$full])) {
            return $index['byFull'][$full];
        }
        $fl = $this->nameKey($first, null, $last);

        return $index['byFirstLast'][$fl] ?? null;
    }

    private function nameKey(?string $first, ?string $middle, ?string $last): string
    {
        return implode('|', [
            strtoupper(trim((string) $first)),
            strtoupper(trim((string) ($middle ?? ''))),
            strtoupper(trim((string) $last)),
        ]);
    }
}
