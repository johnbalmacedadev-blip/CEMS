<?php

namespace App\Console\Commands;

use App\Models\GasExpense;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportGasPoExpensesCommand extends Command
{
    protected $signature = 'import:gas-po-expenses
                            {--file= : Path to JSON export}
                            {--fresh : Truncate gas_expenses before import}
                            {--dry-run : Parse and report without writing}';

    protected $description = 'Import GAS P.O EXPENSE Excel JSON (2024/2025) into gas_expenses';

    public function handle(): int
    {
        $path = $this->option('file') ?: storage_path('app/gas_po_expense_import.json');
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
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            GasExpense::query()->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->warn('gas_expenses truncated.');
        }

        $created = 0;
        $errors = 0;
        $chunkSize = 200;

        $bar = $this->output->createProgressBar(count($rows));
        $bar->start();

        foreach (array_chunk($rows, $chunkSize) as $chunk) {
            $payload = [];
            $now = now();
            foreach ($chunk as $row) {
                try {
                    $plate = strtoupper(preg_replace('/\s+/', '', (string) ($row['plate_number'] ?? '')));
                    $amount = round((float) ($row['gas_amount'] ?? 0), 2);
                    $date = $row['date'] ?? null;
                    if ($plate === '' || ! $date || $amount <= 0) {
                        throw new \RuntimeException('Missing plate/date/amount');
                    }

                    $payload[] = [
                        'date' => $date,
                        'po_number' => $row['po_number'] ?? null,
                        'driver' => mb_substr((string) ($row['driver'] ?? 'UNKNOWN'), 0, 255),
                        'model' => mb_substr((string) ($row['model'] ?? 'UNKNOWN'), 0, 255),
                        'plate_number' => mb_substr($plate, 0, 50),
                        'gas_amount' => $amount,
                        'expense_sent_by' => mb_substr((string) ($row['expense_sent_by'] ?? 'UNKNOWN'), 0, 255),
                        'has_photo_video_in_groupchat' => ! empty($row['has_photo_video_in_groupchat']) ? 1 : 0,
                        'photo_po_slip' => ! empty($row['photo_po_slip']) ? 1 : 0,
                        'photo_fuel_gauge_before' => ! empty($row['photo_fuel_gauge_before']) ? 1 : 0,
                        'photo_fuel_gauge_after' => ! empty($row['photo_fuel_gauge_after']) ? 1 : 0,
                        'photo_car_license_plate_gas_boy' => ! empty($row['photo_car_license_plate_gas_boy']) ? 1 : 0,
                        'photo_receipt_next_to_gas_pump' => ! empty($row['photo_receipt_next_to_gas_pump']) ? 1 : 0,
                        'checked_by' => mb_substr((string) ($row['checked_by'] ?? 'Unchecked'), 0, 255),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                } catch (\Throwable $e) {
                    $errors++;
                    $this->newLine();
                    $this->error('Row '.($row['row'] ?? '?').' ('.$row['sheet'].'): '.$e->getMessage());
                }
                $bar->advance();
            }

            if ($payload !== []) {
                GasExpense::insert($payload);
                $created += count($payload);
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->table(
            ['Metric', 'Count'],
            [
                ['Created', $created],
                ['Errors', $errors],
                ['Total gas expenses', GasExpense::count()],
                ['2024', GasExpense::whereYear('date', 2024)->count()],
                ['2025', GasExpense::whereYear('date', 2025)->count()],
            ]
        );

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }
}
