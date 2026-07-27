<?php

namespace App\Console\Commands;

use App\Models\ExpenseItem;
use App\Models\ExpenseTransaction;
use App\Models\PaymentMethod;
use App\Models\Vehicle;
use App\Models\VehicleExpense;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportReleasedExpenseItemsCommand extends Command
{
    protected $signature = 'import:released-expense-items
                            {--force : Delete previous Excel Import expense items before importing}
                            {--dry-run : Report only, do not write}';

    protected $description = 'Create vehicle ExpenseItem rows from imported paint/scanner/etc. Excel expense columns (date+text kept together in Notes)';

    private const IMPORT_TAG = 'Excel Import';

    /**
     * Excel / VehicleExpense fields → UI description category names.
     * Skip total_repair_* — that is an aggregate of the category columns.
     */
    private const CATEGORY_MAP = [
        ['items' => 'paint_items', 'cost' => 'paint_costs', 'description' => 'Paint'],
        ['items' => 'mechanical_electrical_items', 'cost' => 'mechanical_electrical_costs', 'description' => 'Mechanical / Electrical'],
        ['items' => 'cluster_items', 'cost' => 'cluster_costs', 'description' => 'Cluster'], // SCANNER in Excel
        ['items' => 'aircon_items', 'cost' => 'aircon_cost', 'description' => 'Aircon'],
        ['items' => 'interior_items', 'cost' => 'interior_costs', 'description' => 'Interior'],
        ['items' => 'papers_items', 'cost' => 'papers_costs', 'description' => 'Paper'],
        ['items' => 'tyres_battery_items', 'cost' => 'tyres_battery_cost', 'description' => 'Tyers'],
        ['items' => 'misc_items', 'cost' => 'misc_costs', 'description' => 'Miscellaneous'],
    ];

    public function handle(): int
    {
        $paymentMethodId = PaymentMethod::query()->orderBy('id')->value('id');
        if (! $paymentMethodId) {
            $this->error('No payment methods found. Seed payment methods first.');
            return self::FAILURE;
        }

        if ($this->option('force') && ! $this->option('dry-run')) {
            $deleted = ExpenseItem::query()
                ->where('requested_by', self::IMPORT_TAG)
                ->delete();
            $this->warn("Removed {$deleted} previous Excel Import expense item(s).");
        }

        $expenses = VehicleExpense::query()->get();
        $this->info('Vehicle expense summary rows: '.$expenses->count());

        // plate (normalized) => vehicle id
        $vehicleByPlate = [];
        foreach (Vehicle::query()->get(['id', 'plate_number']) as $vehicle) {
            $vehicleByPlate[$this->normalizePlate($vehicle->plate_number)] = $vehicle;
        }

        $created = 0;
        $skipped = 0;
        $vehiclesTouched = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar($expenses->count());
        $bar->start();

        foreach ($expenses as $expense) {
            try {
                $norm = $this->normalizePlate($expense->plate_number);
                $vehicle = $vehicleByPlate[$norm] ?? null;

                if (! $vehicle) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                $lines = $this->buildLinesFromExpense($expense);
                if ($lines === []) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                // Skip if this vehicle already has Excel Import lines (unless --force already cleared them)
                $already = ExpenseItem::query()
                    ->where('vehicle_id', $vehicle->id)
                    ->where('requested_by', self::IMPORT_TAG)
                    ->exists();

                if ($already) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                if ($this->option('dry-run')) {
                    $created += count($lines);
                    $vehiclesTouched++;
                    $bar->advance();
                    continue;
                }

                DB::transaction(function () use ($vehicle, $lines, $paymentMethodId, &$created, &$vehiclesTouched) {
                    $txDate = collect($lines)->pluck('expense_date')->filter()->sort()->first()
                        ?: optional($vehicle->purchase_date)->toDateString()
                        ?: now()->toDateString();

                    $total = collect($lines)->sum('cost');

                    $transaction = ExpenseTransaction::create([
                        'transaction_date' => $txDate,
                        'starting_cash' => 0,
                        'added_cash' => 0,
                        'total_cash' => 0,
                        'total_expense' => $total,
                        'cash_remaining' => 0 - $total,
                    ]);

                    foreach ($lines as $line) {
                        ExpenseItem::create([
                            'expense_transaction_id' => $transaction->id,
                            'expense_date' => $line['expense_date'] ?? $txDate,
                            'payment_method_id' => $paymentMethodId,
                            'description' => $line['description'],
                            'description_details' => $line['description_details'],
                            'care_of' => null,
                            'requested_by' => self::IMPORT_TAG,
                            'approved_by' => null,
                            'store_shop' => null,
                            'receipt_checked' => false,
                            'cost' => $line['cost'],
                            'payment_tag' => 'Vehicle',
                            'expense_category' => null,
                            'vehicle_id' => $vehicle->id,
                        ]);
                        $created++;
                    }

                    $vehiclesTouched++;
                });
            } catch (\Throwable $e) {
                $errors++;
                $this->newLine();
                $this->error('Plate '.($expense->plate_number ?? '?').': '.$e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->table(
            ['Metric', 'Count'],
            [
                ['Vehicles with new expense items', $vehiclesTouched],
                ['Expense items created', $created],
                ['Skipped', $skipped],
                ['Errors', $errors],
            ]
        );

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @return list<array{description:string,description_details:?string,cost:float,expense_date:?string}>
     */
    private function buildLinesFromExpense(VehicleExpense $expense): array
    {
        $lines = [];

        foreach (self::CATEGORY_MAP as $map) {
            $itemsRaw = trim((string) ($expense->{$map['items']} ?? ''));
            $cost = (float) ($expense->{$map['cost']} ?? 0);

            if ($itemsRaw === '' && $cost <= 0) {
                continue;
            }

            // Keep date + text together in Notes (description_details)
            $note = $itemsRaw !== ''
                ? $this->formatSpecialNote($itemsRaw)
                : null;

            // Miscellaneous requires notes in the UI — ensure something is present
            if ($map['description'] === 'Miscellaneous' && ($note === null || $note === '')) {
                $note = 'Imported miscellaneous expense (no item text in Excel)';
            }

            $lines[] = [
                'description' => $map['description'],
                'description_details' => $note,
                'cost' => $cost,
                'expense_date' => $this->extractFirstDate($itemsRaw),
            ];
        }

        return $lines;
    }

    /**
     * Normalize Excel item text so date + description stay as one special note.
     */
    private function formatSpecialNote(string $raw): string
    {
        $text = preg_replace("/[ \t]+/", ' ', str_replace(["\r\n", "\r", "\n"], ' ', $raw));
        $text = trim($text);

        // If it already looks like "date - text" or "date / text", keep as-is
        if (preg_match('/^(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})\s*[\/\-]\s*(.+)$/u', $text, $m)) {
            return $m[1].' - '.$m[2];
        }

        // Multiple date chunks: keep them together as one note
        return $text;
    }

    private function extractFirstDate(?string $text): ?string
    {
        if ($text === null || trim($text) === '') {
            return null;
        }

        if (preg_match('/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{2,4})/', $text, $m)) {
            $day = (int) $m[1];
            $month = (int) $m[2];
            $year = (int) $m[3];
            if ($year < 100) {
                $year += 2000;
            }

            // Excel often uses D/M/Y (PH). If day > 12, must be D/M/Y.
            // If month > 12, swap. Otherwise prefer D/M/Y.
            if ($month > 12 && $day <= 12) {
                [$day, $month] = [$month, $day];
            }

            try {
                return Carbon::createFromDate($year, $month, $day)->toDateString();
            } catch (\Throwable $e) {
                try {
                    return Carbon::createFromDate($year, $day, $month)->toDateString();
                } catch (\Throwable $e2) {
                    return null;
                }
            }
        }

        return null;
    }

    private function normalizePlate(?string $plate): string
    {
        return strtoupper(preg_replace('/[^A-Z0-9]/i', '', (string) $plate) ?? '');
    }
}
