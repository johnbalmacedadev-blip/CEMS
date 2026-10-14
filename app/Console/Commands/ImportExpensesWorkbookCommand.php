<?php

namespace App\Console\Commands;

use App\Models\CashAddition;
use App\Models\DailyBudget;
use App\Models\ExpenseItem;
use App\Models\ExpenseTransaction;
use App\Models\PaymentMethod;
use App\Models\SoaManualEntry;
use App\Models\Vehicle;
use App\Support\DataImport\ExcelSheetHelper;
use App\Support\ExpensesWorkbookParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportExpensesWorkbookCommand extends Command
{
    protected $signature = 'import:expenses-workbook
                            {file : Path to EXPENSES workbook (.xlsx)}
                            {--months=8,9,10 : Comma-separated months to import (1-12)}
                            {--year=2026 : Year filter}
                            {--fresh : Replace previous EXPENSES workbook imports for selected months}
                            {--skip-soa : Do not write SOA daily budgets / debit lines}
                            {--dry-run : Parse and report without writing}';

    protected $description = 'Import EXPENSES workbook tabs into expenses-inventory (ExpenseItem) and optionally SOA';

    private const IMPORT_TAG = 'EXPENSES Workbook Import';

    private const TIER_PAYMENT_NAMES = [
        'flagship' => 'COST CENTER (FLAGSHIP BUDGET)',
        'warehouse' => 'COST CENTER (WAREHOUSE BUDGET)',
        'annex' => 'COST CENTER (ANNEX BUDGET)',
    ];

    public function handle(): int
    {
        $path = (string) $this->argument('file');
        if (! is_file($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        $year = (int) $this->option('year');
        $months = $this->parseMonths((string) $this->option('months'));
        if ($months === []) {
            $this->error('No valid months provided.');

            return self::FAILURE;
        }

        $pmIds = $this->resolvePaymentMethodIds();
        if ($pmIds === null) {
            return self::FAILURE;
        }

        $wantedSheets = $this->resolveSheetNames($path, $year, $months);
        if ($wantedSheets === []) {
            $this->error('No matching Flagship/Annex/Warehouse sheets for the selected months.');

            return self::FAILURE;
        }

        $this->info('Sheets: '.implode(', ', $wantedSheets));
        $this->info('Months: '.implode(', ', $months).' / Year '.$year);

        $parser = new ExpensesWorkbookParser();
        $days = $parser->parse($path, $wantedSheets, $months);
        $expenseLines = array_sum(array_map(fn ($d) => count($d['expenses'] ?? []), $days));
        $this->info('Day blocks: '.count($days).' | Expense lines: '.$expenseLines);

        if ($this->option('dry-run')) {
            $this->warn('Dry run — no database writes.');
            $this->preview($days);

            return self::SUCCESS;
        }

        if ($this->option('fresh')) {
            $this->clearPreviousImports($pmIds, $year, $months);
        }

        $vehicleByPlate = [];
        foreach (Vehicle::query()->get(['id', 'plate_number']) as $vehicle) {
            $vehicleByPlate[ExcelSheetHelper::normalizePlate($vehicle->plate_number)] = (int) $vehicle->id;
        }

        $txCreated = 0;
        $itemsCreated = 0;
        $soaBudgets = 0;
        $soaCash = 0;
        $soaDebits = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar(count($days));
        $bar->start();

        foreach ($days as $day) {
            try {
                $expensesPreview = $day['expenses'] ?? [];
                $startingPreview = round((float) ($day['starting_balance'] ?? 0), 2);
                $addedPreview = max(0.0, round((float) ($day['added_cash'] ?? 0), 2));
                if ($expensesPreview === [] && $startingPreview <= 0 && $addedPreview <= 0) {
                    $bar->advance();
                    continue;
                }

                DB::transaction(function () use (
                    $day,
                    $pmIds,
                    $vehicleByPlate,
                    $parser,
                    &$txCreated,
                    &$itemsCreated,
                    &$soaBudgets,
                    &$soaCash,
                    &$soaDebits
                ) {
                    $tier = strtolower((string) ($day['tier'] ?? ''));
                    if (! isset($pmIds[$tier])) {
                        throw new \RuntimeException('Unknown tier: '.$tier);
                    }
                    $pmId = $pmIds[$tier];
                    $date = (string) ($day['entry_date'] ?? '');
                    if ($date === '') {
                        throw new \RuntimeException('Missing entry_date');
                    }

                    $starting = round((float) ($day['starting_balance'] ?? 0), 2);
                    $added = max(0.0, round((float) ($day['added_cash'] ?? 0), 2));
                    $expenses = $day['expenses'] ?? [];
                    $totalExpense = round(array_sum(array_map(fn ($e) => (float) ($e['amount'] ?? 0), $expenses)), 2);
                    $totalCash = $starting + $added;

                    // Production DB tables often lack AUTO_INCREMENT on id.
                    // Eloquent insertGetId() returns 0 without AUTO_INCREMENT and
                    // would overwrite a manually assigned id — disable incrementing.
                    $txId = ((int) ExpenseTransaction::query()->max('id')) + 1;
                    $transaction = new ExpenseTransaction([
                        'transaction_date' => $date,
                        'starting_cash' => $starting,
                        'added_cash' => $added,
                        'total_cash' => $totalCash,
                        'total_expense' => $totalExpense,
                        'cash_remaining' => $totalCash - $totalExpense,
                    ]);
                    $this->saveWithManualId($transaction, $txId);
                    $txCreated++;

                    foreach ($expenses as $exp) {
                        $amount = round((float) ($exp['amount'] ?? 0), 2);
                        $desc = trim((string) ($exp['description'] ?? ''));
                        if ($amount <= 0 || $amount > 5000000 || $desc === '') {
                            continue;
                        }

                        $unit = trim((string) ($exp['unit'] ?? ''));
                        $streamLabel = trim((string) ($exp['stream_label'] ?? ''));
                        $details = trim(implode(' · ', array_filter([
                            $streamLabel !== '' ? $streamLabel : null,
                            $unit !== '' ? $unit : null,
                            'from '.($day['sheet'] ?? 'workbook'),
                        ])));

                        $plate = $parser->extractPlate($unit) ?: $parser->extractPlate($desc);
                        $vehicleId = $plate && isset($vehicleByPlate[$plate]) ? $vehicleByPlate[$plate] : null;

                        $itemId = ((int) ExpenseItem::query()->max('id')) + 1;
                        $expenseItem = new ExpenseItem([
                            'expense_transaction_id' => $txId,
                            'expense_date' => $date,
                            'payment_method_id' => $pmId,
                            'description' => mb_substr($desc, 0, 255),
                            'description_details' => $details !== '' ? mb_substr($details, 0, 1000) : null,
                            'care_of' => null,
                            'requested_by' => self::IMPORT_TAG,
                            'approved_by' => null,
                            'store_shop' => null,
                            'receipt_checked' => false,
                            'cost' => $amount,
                            'payment_tag' => $vehicleId ? 'Vehicle' : 'Operating',
                            'expense_category' => null,
                            'vehicle_id' => $vehicleId,
                        ]);
                        $this->saveWithManualId($expenseItem, $itemId);
                        $itemsCreated++;
                    }

                    // Recalculate totals after filtering out absurd lines.
                    $actualTotal = round((float) ExpenseItem::query()
                        ->where('expense_transaction_id', $txId)
                        ->sum('cost'), 2);
                    ExpenseTransaction::query()->whereKey($txId)->update([
                        'total_expense' => $actualTotal,
                        'cash_remaining' => $totalCash - $actualTotal,
                    ]);

                    if (! $this->option('skip-soa')) {
                        $budget = DailyBudget::query()
                            ->where('payment_method_id', $pmId)
                            ->whereDate('budget_date', $date)
                            ->first();
                        if ($budget) {
                            $budget->forceFill([
                                'starting_balance' => $starting,
                                'added_cash' => $added,
                                'notes' => 'Imported from '.($day['sheet'] ?? 'EXPENSES workbook'),
                            ])->save();
                        } else {
                            $budget = new DailyBudget([
                                'payment_method_id' => $pmId,
                                'budget_date' => $date,
                                'starting_balance' => $starting,
                                'added_cash' => $added,
                                'notes' => 'Imported from '.($day['sheet'] ?? 'EXPENSES workbook'),
                            ]);
                            $this->saveWithManualId($budget, ((int) DailyBudget::query()->max('id')) + 1);
                        }
                        $soaBudgets++;

                        if ($added > 0) {
                            $exists = CashAddition::where('payment_method_id', $pmId)
                                ->whereDate('addition_date', $date)
                                ->where('description', 'ADDED CASH (Excel import)')
                                ->where('amount', $added)
                                ->exists();
                            if (! $exists) {
                                $cash = new CashAddition([
                                    'payment_method_id' => $pmId,
                                    'addition_date' => $date,
                                    'amount' => $added,
                                    'description' => 'ADDED CASH (Excel import)',
                                ]);
                                $this->saveWithManualId($cash, ((int) CashAddition::query()->max('id')) + 1);
                                $soaCash++;
                            }
                        }

                        foreach ($expenses as $exp) {
                            $amount = round((float) ($exp['amount'] ?? 0), 2);
                            $desc = trim((string) ($exp['description'] ?? ''));
                            if ($amount <= 0 || $amount > 5000000 || $desc === '') {
                                continue;
                            }
                            $unit = trim((string) ($exp['unit'] ?? ''));
                            if ($unit !== '') {
                                $desc .= ' — '.$unit;
                            }
                            $streamLabel = trim((string) ($exp['stream_label'] ?? ''));
                            if ($streamLabel !== '') {
                                $desc = '['.$streamLabel.'] '.$desc;
                            }

                            $soa = new SoaManualEntry([
                                'payment_method_id' => $pmId,
                                'entry_date' => $date,
                                'description' => mb_substr($desc, 0, 1000),
                                'debit' => $amount,
                                'credit' => null,
                                'is_carry_over' => false,
                                'is_expense_budget' => false,
                                'expense_budget_tier' => null,
                            ]);
                            $this->saveWithManualId($soa, ((int) SoaManualEntry::query()->max('id')) + 1);
                            $soaDebits++;
                        }
                    }
                });
            } catch (\Throwable $e) {
                $errors++;
                $this->newLine();
                $this->error(
                    'Day '.($day['entry_date'] ?? '?').' / '.($day['sheet'] ?? '?').': '.$e->getMessage()
                );
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->table(
            ['Metric', 'Count'],
            [
                ['Expense transactions created', $txCreated],
                ['Expense items created', $itemsCreated],
                ['SOA daily budgets upserted', $soaBudgets],
                ['SOA cash additions created', $soaCash],
                ['SOA debit lines created', $soaDebits],
                ['Errors', $errors],
            ]
        );

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @return list<int>
     */
    private function parseMonths(string $raw): array
    {
        $months = [];
        foreach (preg_split('/\s*,\s*/', trim($raw)) ?: [] as $part) {
            if ($part === '') {
                continue;
            }
            $n = (int) $part;
            if ($n >= 1 && $n <= 12) {
                $months[] = $n;
            }
        }

        return array_values(array_unique($months));
    }

    /**
     * @return array<string, int>|null
     */
    private function resolvePaymentMethodIds(): ?array
    {
        $map = [];
        foreach (self::TIER_PAYMENT_NAMES as $tier => $name) {
            $pm = PaymentMethod::where('name', $name)->first();
            if (! $pm) {
                $this->error("Payment method not found: {$name}");

                return null;
            }
            $map[$tier] = (int) $pm->id;
            $this->line("  {$tier} → #{$pm->id} {$pm->name}");
        }

        return $map;
    }

    /**
     * @param  list<int>  $months
     * @return list<string>
     */
    private function resolveSheetNames(string $path, int $year, array $months): array
    {
        $monthNames = [
            1 => 'JANUARY', 2 => 'FEBRUARY', 3 => 'MARCH', 4 => 'APRIL',
            5 => 'MAY', 6 => 'JUNE', 7 => 'JULY', 8 => 'AUGUST',
            9 => 'SEPTEMBER', 10 => 'OCTOBER', 11 => 'NOVEMBER', 12 => 'DECEMBER',
        ];
        $wantedMonthLabels = [];
        foreach ($months as $m) {
            $wantedMonthLabels[] = $monthNames[$m];
        }

        $out = [];
        foreach (ExcelSheetHelper::sheetNames($path) as $name) {
            $u = strtoupper($name);
            if (! str_contains($u, (string) $year)) {
                continue;
            }
            if (! str_contains($u, 'FLAGSHIP') && ! str_contains($u, 'ANNEX') && ! str_contains($u, 'WAREHOUSE')) {
                continue;
            }
            $monthHit = false;
            foreach ($wantedMonthLabels as $label) {
                if (str_contains($u, $label)) {
                    $monthHit = true;
                    break;
                }
            }
            if (! $monthHit) {
                continue;
            }
            $out[] = $name;
        }

        return $out;
    }

    /**
     * Persist a model with an explicit primary key on DBs that lack AUTO_INCREMENT.
     * Without this, Eloquent's insertGetId() returns 0 and overwrites the id in memory.
     */
    private function saveWithManualId(\Illuminate\Database\Eloquent\Model $model, int $id): void
    {
        $model->id = $id;
        $model->incrementing = false;
        $model->save();
    }

    /**
     * @param  array<string, int>  $pmIds
     * @param  list<int>  $months
     */
    private function clearPreviousImports(array $pmIds, int $year, array $months): void
    {
        $this->warn('Clearing previous EXPENSES workbook imports for selected months...');

        $itemQuery = ExpenseItem::query()
            ->where('requested_by', self::IMPORT_TAG)
            ->whereYear('expense_date', $year)
            ->whereIn(DB::raw('MONTH(expense_date)'), $months);

        $txIds = (clone $itemQuery)->pluck('expense_transaction_id')->unique()->filter()->values()->all();
        $deletedItems = (clone $itemQuery)->delete();
        $deletedTx = 0;
        if ($txIds !== []) {
            $deletedTx = ExpenseTransaction::whereIn('id', $txIds)->delete();
        }

        // Prior buggy imports left transactions with expense_transaction_id = 0 on items.
        $orphanTx = ExpenseTransaction::query()
            ->whereYear('transaction_date', $year)
            ->whereIn(DB::raw('MONTH(transaction_date)'), $months)
            ->whereDoesntHave('expenseItems')
            ->delete();
        $deletedTx += $orphanTx;

        $this->info("Removed {$deletedItems} expense item(s) and {$deletedTx} transaction(s).");

        if ($this->option('skip-soa')) {
            return;
        }

        $pmIdList = array_values($pmIds);
        DailyBudget::whereIn('payment_method_id', $pmIdList)
            ->whereYear('budget_date', $year)
            ->whereIn(DB::raw('MONTH(budget_date)'), $months)
            ->delete();

        CashAddition::whereIn('payment_method_id', $pmIdList)
            ->whereYear('addition_date', $year)
            ->whereIn(DB::raw('MONTH(addition_date)'), $months)
            ->where('description', 'ADDED CASH (Excel import)')
            ->delete();

        SoaManualEntry::whereIn('payment_method_id', $pmIdList)
            ->whereYear('entry_date', $year)
            ->whereIn(DB::raw('MONTH(entry_date)'), $months)
            ->delete();

        $this->info('Cleared SOA rows for selected months (Flagship/Warehouse/Annex).');
    }

    /**
     * @param  array<int, array<string, mixed>>  $days
     */
    private function preview(array $days): void
    {
        $sample = array_slice($days, 0, 5);
        foreach ($sample as $day) {
            $this->line(sprintf(
                '  %s [%s] start=%s added=%s expenses=%d',
                $day['entry_date'] ?? '?',
                $day['tier'] ?? '?',
                $day['starting_balance'] ?? 0,
                $day['added_cash'] ?? 0,
                count($day['expenses'] ?? [])
            ));
        }
        if (count($days) > 5) {
            $this->line('  …');
        }
    }
}
