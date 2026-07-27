<?php

namespace App\Console\Commands;

use App\Models\CashAddition;
use App\Models\DailyBudget;
use App\Models\PaymentMethod;
use App\Models\SoaManualEntry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportSoaExpenses2025Command extends Command
{
    protected $signature = 'import:soa-expenses-2025
                            {--file= : Path to JSON export}
                            {--fresh : Delete existing 2025 SOA data for Flagship/Warehouse/Annex before import}
                            {--dry-run : Parse and report without writing}';

    protected $description = 'Import 2025 EXPENSES workbook into SOA (daily budgets, cash additions, manual debit lines)';

    private const TIER_PAYMENT_NAMES = [
        'flagship' => 'COST CENTER (FLAGSHIP BUDGET)',
        'warehouse' => 'COST CENTER (WAREHOUSE BUDGET)',
        'annex' => 'COST CENTER (ANNEX BUDGET)',
    ];

    public function handle(): int
    {
        $path = $this->option('file') ?: storage_path('app/soa_expenses_2025_import.json');
        if (! is_file($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        $days = json_decode(file_get_contents($path), true);
        if (! is_array($days) || $days === []) {
            $this->error('JSON file is empty or invalid.');

            return self::FAILURE;
        }

        $pmIds = $this->resolvePaymentMethodIds();
        if ($pmIds === null) {
            return self::FAILURE;
        }

        $expenseLines = array_sum(array_map(fn ($d) => count($d['expenses'] ?? []), $days));
        $this->info('Days: '.count($days).' | Expense lines: '.$expenseLines);

        if ($this->option('dry-run')) {
            $this->warn('Dry run — no database writes.');

            return self::SUCCESS;
        }

        if ($this->option('fresh')) {
            $this->clear2025SoaData(array_values($pmIds));
        }

        $budgets = 0;
        $cashAdds = 0;
        $debits = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar(count($days));
        $bar->start();

        foreach ($days as $day) {
            try {
                DB::transaction(function () use ($day, $pmIds, &$budgets, &$cashAdds, &$debits) {
                    $tier = strtolower((string) ($day['tier'] ?? ''));
                    if (! isset($pmIds[$tier])) {
                        throw new \RuntimeException('Unknown tier: '.$tier);
                    }
                    $pmId = $pmIds[$tier];
                    $date = $day['entry_date'] ?? null;
                    if (! $date) {
                        throw new \RuntimeException('Missing entry_date');
                    }

                    $starting = round((float) ($day['starting_balance'] ?? 0), 2);
                    $added = round((float) ($day['added_cash'] ?? 0), 2);
                    if ($added < 0) {
                        $added = 0.0;
                    }

                    DailyBudget::updateOrCreate(
                        [
                            'payment_method_id' => $pmId,
                            'budget_date' => $date,
                        ],
                        [
                            'starting_balance' => $starting,
                            'added_cash' => $added,
                            'notes' => 'Imported from EXPENSES 2025 ('.($day['sheet'] ?? $tier).')',
                        ]
                    );
                    $budgets++;

                    if ($added > 0) {
                        $exists = CashAddition::where('payment_method_id', $pmId)
                            ->whereDate('addition_date', $date)
                            ->where('description', 'ADDED CASH (Excel import)')
                            ->where('amount', $added)
                            ->exists();
                        if (! $exists) {
                            CashAddition::create([
                                'payment_method_id' => $pmId,
                                'addition_date' => $date,
                                'amount' => $added,
                                'description' => 'ADDED CASH (Excel import)',
                            ]);
                            $cashAdds++;
                        }
                    }

                    foreach ($day['expenses'] ?? [] as $exp) {
                        $amount = round((float) ($exp['amount'] ?? 0), 2);
                        $desc = trim((string) ($exp['description'] ?? ''));
                        if ($amount <= 0 || $desc === '') {
                            continue;
                        }

                        SoaManualEntry::create([
                            'payment_method_id' => $pmId,
                            'entry_date' => $date,
                            'description' => mb_substr($desc, 0, 1000),
                            'debit' => $amount,
                            'credit' => null,
                            'is_carry_over' => false,
                            'is_expense_budget' => false,
                            'expense_budget_tier' => null,
                        ]);
                        $debits++;
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
                ['Daily budgets upserted', $budgets],
                ['Cash additions created', $cashAdds],
                ['SOA debit lines created', $debits],
                ['Errors', $errors],
                ['Daily budgets total', DailyBudget::count()],
                ['Cash additions total', CashAddition::count()],
                ['SOA manual entries total', SoaManualEntry::count()],
            ]
        );

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @return array<string,int>|null
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
     * @param  list<int>  $paymentMethodIds
     */
    private function clear2025SoaData(array $paymentMethodIds): void
    {
        $this->warn('Clearing 2025 SOA data for Flagship / Warehouse / Annex...');

        DailyBudget::whereIn('payment_method_id', $paymentMethodIds)
            ->whereYear('budget_date', 2025)
            ->delete();

        CashAddition::whereIn('payment_method_id', $paymentMethodIds)
            ->whereYear('addition_date', 2025)
            ->delete();

        SoaManualEntry::whereIn('payment_method_id', $paymentMethodIds)
            ->whereYear('entry_date', 2025)
            ->delete();

        $this->info('Cleared existing 2025 SOA rows for those cost centers.');
    }
}
