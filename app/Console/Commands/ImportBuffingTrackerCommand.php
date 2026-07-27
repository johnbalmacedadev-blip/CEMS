<?php

namespace App\Console\Commands;

use App\Models\BuffingRecord;
use App\Models\Employee;
use App\Models\Vehicle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportBuffingTrackerCommand extends Command
{
    protected $signature = 'import:buffing-tracker
                            {--file= : Path to JSON export}
                            {--dry-run : Parse and report without writing}';

    protected $description = 'Import buffing tracker records from BUFFING TRACKER Excel JSON export';

    public function handle(): int
    {
        $path = $this->option('file') ?: storage_path('app/buffing_tracker_import.json');
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

        $vehicleByPlate = [];
        foreach (Vehicle::query()->get(['id', 'plate_number']) as $vehicle) {
            $vehicleByPlate[$this->normalizePlate($vehicle->plate_number)] = $vehicle->id;
        }

        $employeeByKey = [];
        foreach (Employee::query()->get(['id', 'first_name', 'last_name']) as $emp) {
            $employeeByKey[$this->normalizeName($emp->first_name)] = $emp->id;
            $employeeByKey[$this->normalizeName(trim($emp->first_name.' '.$emp->last_name))] = $emp->id;
            // first token match e.g. KRISTOFFER from KRISTOFFER JOHN IAN
            $firstToken = explode(' ', strtoupper(trim($emp->first_name)))[0] ?? '';
            if ($firstToken !== '' && ! isset($employeeByKey[$firstToken])) {
                $employeeByKey[$firstToken] = $emp->id;
            }
        }

        $makeCache = [];
        $modelCache = [];

        $created = 0;
        $skipped = 0;
        $employeesCreated = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach ($rows as $row) {
            try {
                DB::transaction(function () use (
                    $row,
                    &$vehicleByPlate,
                    &$employeeByKey,
                    &$created,
                    &$skipped,
                    &$employeesCreated
                ) {
                    $date = $row['buffing_date'] ?? null;
                    if (! $date) {
                        throw new \RuntimeException('Missing buffing date');
                    }

                    $employeeId = null;
                    $empName = trim((string) ($row['employee_name'] ?? ''));
                    if ($empName !== '') {
                        $key = $this->normalizeName($empName);
                        if (! isset($employeeByKey[$key])) {
                            // try first token
                            $token = explode(' ', $key)[0];
                            if (isset($employeeByKey[$token])) {
                                $employeeId = $employeeByKey[$token];
                            } else {
                                $parts = preg_split('/\s+/', $empName) ?: [$empName];
                                $first = $parts[0];
                                $last = count($parts) > 1 ? implode(' ', array_slice($parts, 1)) : 'Buffing';
                                $emp = Employee::create([
                                    'first_name' => mb_convert_case(mb_strtolower($first), MB_CASE_TITLE, 'UTF-8'),
                                    'last_name' => mb_convert_case(mb_strtolower($last), MB_CASE_TITLE, 'UTF-8'),
                                    'role' => 'BUFFER - GEN. STAFF',
                                    'status' => 'active',
                                    'notes' => 'Imported from Buffing Tracker',
                                ]);
                                $employeeByKey[$key] = $emp->id;
                                $employeeByKey[$this->normalizeName($first)] = $emp->id;
                                $employeeId = $emp->id;
                                $employeesCreated++;
                            }
                        } else {
                            $employeeId = $employeeByKey[$key];
                        }
                    }

                    $vehicleId = null;
                    $plate = $this->normalizePlate($row['plate_number'] ?? '');
                    $isTask = (bool) ($row['is_task'] ?? false);
                    $notes = $row['notes'] ?? null;

                    if (! $isTask && $plate !== '') {
                        if (isset($vehicleByPlate[$plate])) {
                            $vehicleId = $vehicleByPlate[$plate];
                        } else {
                            // Do not invent inventory units — keep plate in notes
                            $plateNote = 'Plate: '.$plate;
                            $notes = $notes ? $plateNote.' | '.$notes : $plateNote;
                        }
                    }

                    $status = $row['status'] ?? BuffingRecord::STATUS_PENDING;
                    if (! in_array($status, BuffingRecord::statusOptions(), true)) {
                        $status = BuffingRecord::STATUS_PENDING;
                    }

                    $exists = BuffingRecord::query()
                        ->whereDate('buffing_date', $date)
                        ->where('employee_id', $employeeId)
                        ->when(
                            $vehicleId,
                            fn ($q) => $q->where('vehicle_id', $vehicleId),
                            fn ($q) => $q->whereNull('vehicle_id')->where('notes', $notes)
                        )
                        ->where('status', $status)
                        ->exists();

                    if ($exists) {
                        $skipped++;

                        return;
                    }

                    BuffingRecord::create([
                        'vehicle_id' => $vehicleId,
                        'employee_id' => $employeeId,
                        'buffing_date' => $date,
                        'status' => $status,
                        'notes' => $notes,
                        'completed_at' => $status === BuffingRecord::STATUS_COMPLETED
                            ? $date.' 17:00:00'
                            : null,
                    ]);
                    $created++;
                });
            } catch (\Throwable $e) {
                $errors++;
                $this->newLine();
                $this->error(
                    ($row['sheet'] ?? '?').
                    ' row '.($row['row'] ?? '?').
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
                ['Buffing records created', $created],
                ['Skipped (dup)', $skipped],
                ['Employees created', $employeesCreated],
                ['Errors', $errors],
                ['Total buffing records', BuffingRecord::count()],
            ]
        );

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function normalizePlate(?string $plate): string
    {
        return strtoupper(preg_replace('/[^A-Z0-9]/i', '', (string) $plate) ?? '');
    }

    private function normalizeName(?string $name): string
    {
        return strtoupper(trim(preg_replace('/\s+/', ' ', (string) $name) ?? ''));
    }
}
