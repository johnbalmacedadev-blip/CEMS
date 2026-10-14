<?php

namespace App\Console\Commands;

use App\Models\CashAddition;
use App\Models\DailyBudget;
use App\Models\PaymentMethod;
use App\Models\SoaManualEntry;
use App\Support\SoaCashVaultWorkbookParser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportSoaCashVaultCommand extends Command
{
    protected $signature = 'import:soa-cash-vault
                            {--file= : Path to SOA CASH VAULT workbook (.xlsx)}
                            {--year=2026 : Calendar year filter}
                            {--months= : Optional comma-separated months (1-12); default all}
                            {--channels=cash_vault,gcash : Channels to import}
                            {--fresh : Clear CASH VAULT + GCASH SOA rows for selected year/months before import}
                            {--skip-existing : Skip manual lines already in DB (default when not --fresh)}
                            {--dry-run : Parse and report without writing}';

    protected $description = 'Import SOA CASH VAULT 2026.xlsx into SOA (DailyBudget + SoaManualEntry for CASH VAULT and GCASH)';

    private const PM_CASH_VAULT = 'CASH VAULT';

    private const PM_GCASH = 'GCASH';

    private const IMPORT_NOTE = 'Imported from SOA CASH VAULT workbook';

    public function handle(): int
    {
        $path = $this->option('file') ?: storage_path('app/SOA_CASH_VAULT_2026.xlsx');
        if (! is_file($path)) {
            $this->error("File not found: {$path}");

            return self::FAILURE;
        }

        $year = (int) $this->option('year');
        $months = $this->parseMonths((string) ($this->option('months') ?? ''));
        $channels = $this->parseChannels((string) $this->option('channels'));
        if ($channels === []) {
            $this->error('No valid channels. Use cash_vault and/or gcash.');

            return self::FAILURE;
        }

        $pmIds = $this->ensurePaymentMethods();
        if ($pmIds === null) {
            return self::FAILURE;
        }

        $this->info('Parsing '.basename($path).' …');
        $parser = new SoaCashVaultWorkbookParser();
        $days = $parser->parse($path);

        $days = array_values(array_filter($days, function (array $day) use ($year, $months, $channels) {
            if (! in_array($day['channel'], $channels, true)) {
                return false;
            }
            $date = $day['entry_date'] ?? '';
            if (! preg_match('/^(\d{4})-(\d{2})-/', $date, $m)) {
                return false;
            }
            if ((int) $m[1] !== $year) {
                return false;
            }
            if ($months !== [] && ! in_array((int) $m[2], $months, true)) {
                return false;
            }

            return true;
        }));

        $lineCount = array_sum(array_map(fn ($d) => count($d['lines'] ?? []), $days));
        $vaultDays = count(array_filter($days, fn ($d) => $d['channel'] === 'cash_vault'));
        $gcashDays = count(array_filter($days, fn ($d) => $d['channel'] === 'gcash'));
        $this->info("Days: {$vaultDays} cash vault + {$gcashDays} gcash = ".count($days)." | Lines: {$lineCount}");

        $skipExisting = (bool) $this->option('skip-existing') || ! $this->option('fresh');

        if ($this->option('dry-run')) {
            if ($skipExisting) {
                $newLines = 0;
                foreach ($days as $day) {
                    $pmId = $pmIds[$day['channel']];
                    $existing = $this->existingLineKeys($pmId, $day['entry_date']);
                    foreach ($day['lines'] as $line) {
                        if (! isset($existing[$this->lineKey($line)])) {
                            $newLines++;
                        }
                    }
                }
                $this->info("Would create new manual lines: {$newLines}");
            }
            $this->warn('Dry run — no database writes.');

            return self::SUCCESS;
        }

        if ($this->option('fresh')) {
            $this->clearSoa($pmIds, $year, $months, $channels);
            $skipExisting = false;
        }

        $budgetsUpserted = 0;
        $linesCreated = 0;
        $linesSkipped = 0;
        $errors = 0;

        $bar = $this->output->createProgressBar(count($days));
        $bar->start();

        foreach ($days as $day) {
            try {
                DB::transaction(function () use ($day, $pmIds, $skipExisting, &$budgetsUpserted, &$linesCreated, &$linesSkipped) {
                    $pmId = $pmIds[$day['channel']];
                    $date = $day['entry_date'];
                    $starting = round((float) ($day['starting_balance'] ?? 0), 2);

                    $budget = DailyBudget::query()
                        ->where('payment_method_id', $pmId)
                        ->whereDate('budget_date', $date)
                        ->first();

                    if ($budget) {
                        $budget->starting_balance = $starting;
                        $budget->added_cash = 0;
                        $budget->notes = self::IMPORT_NOTE.' ('.$day['sheet'].')';
                        $budget->save();
                    } else {
                        $budget = new DailyBudget([
                            'payment_method_id' => $pmId,
                            'budget_date' => $date,
                            'starting_balance' => $starting,
                            'added_cash' => 0,
                            'notes' => self::IMPORT_NOTE.' ('.$day['sheet'].')',
                        ]);
                        $this->saveWithManualId($budget, ((int) DailyBudget::query()->max('id')) + 1);
                    }
                    $budgetsUpserted++;

                    $existing = $skipExisting ? $this->existingLineKeys($pmId, $date) : [];

                    foreach ($day['lines'] as $line) {
                        $key = $this->lineKey($line);
                        if ($skipExisting && isset($existing[$key])) {
                            $linesSkipped++;
                            continue;
                        }

                        $entry = new SoaManualEntry([
                            'payment_method_id' => $pmId,
                            'entry_date' => $date,
                            'description' => $line['description'],
                            'debit' => $line['debit'],
                            'credit' => $line['credit'],
                            'is_carry_over' => false,
                            'is_expense_budget' => false,
                            'expense_budget_tier' => null,
                        ]);
                        $this->saveWithManualId($entry, ((int) SoaManualEntry::query()->max('id')) + 1);
                        $existing[$key] = true;
                        $linesCreated++;
                    }
                });
            } catch (\Throwable $e) {
                $errors++;
                $this->newLine();
                $this->error(($day['entry_date'] ?? '?').' / '.($day['sheet'] ?? '?').': '.$e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->table(
            ['Metric', 'Count'],
            [
                ['Daily budgets upserted', $budgetsUpserted],
                ['Manual lines created', $linesCreated],
                ['Manual lines skipped (existing)', $linesSkipped],
                ['Errors', $errors],
                ['CASH VAULT budgets (year)', DailyBudget::where('payment_method_id', $pmIds['cash_vault'])->whereYear('budget_date', $year)->count()],
                ['CASH VAULT lines (year)', SoaManualEntry::where('payment_method_id', $pmIds['cash_vault'])->whereYear('entry_date', $year)->count()],
                ['GCASH budgets (year)', DailyBudget::where('payment_method_id', $pmIds['gcash'])->whereYear('budget_date', $year)->count()],
                ['GCASH lines (year)', SoaManualEntry::where('payment_method_id', $pmIds['gcash'])->whereYear('entry_date', $year)->count()],
            ]
        );

        return $errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * @return array{cash_vault: int, gcash: int}|null
     */
    private function ensurePaymentMethods(): ?array
    {
        $vault = PaymentMethod::where('name', self::PM_CASH_VAULT)->first();
        if (! $vault) {
            $vault = new PaymentMethod([
                'name' => self::PM_CASH_VAULT,
                'is_active' => true,
                'sort_order' => 0,
            ]);
            $this->saveWithManualId($vault, ((int) PaymentMethod::query()->max('id')) + 1);
        } elseif ((int) $vault->sort_order !== 0 || ! $vault->is_active) {
            $vault->sort_order = 0;
            $vault->is_active = true;
            $vault->save();
        }

        $gcash = PaymentMethod::where('name', self::PM_GCASH)->first();
        if (! $gcash) {
            $this->error('Payment method GCASH not found.');

            return null;
        }

        $this->line('  cash_vault → #'.$vault->id.' '.$vault->name);
        $this->line('  gcash → #'.$gcash->id.' '.$gcash->name);

        return [
            'cash_vault' => (int) $vault->id,
            'gcash' => (int) $gcash->id,
        ];
    }

    /**
     * @param  array{cash_vault: int, gcash: int}  $pmIds
     * @param  list<int>  $months
     * @param  list<string>  $channels
     */
    private function clearSoa(array $pmIds, int $year, array $months, array $channels): void
    {
        $ids = [];
        foreach ($channels as $ch) {
            if (isset($pmIds[$ch])) {
                $ids[] = $pmIds[$ch];
            }
        }
        if ($ids === []) {
            return;
        }

        $this->warn('Clearing SOA for '.implode(', ', $channels).' / year '.$year.($months !== [] ? ' months '.implode(',', $months) : '').'…');

        $budgetQ = DailyBudget::whereIn('payment_method_id', $ids)->whereYear('budget_date', $year);
        $cashQ = CashAddition::whereIn('payment_method_id', $ids)->whereYear('addition_date', $year);
        $entryQ = SoaManualEntry::whereIn('payment_method_id', $ids)->whereYear('entry_date', $year);

        if ($months !== []) {
            $budgetQ->whereIn(DB::raw('MONTH(budget_date)'), $months);
            $cashQ->whereIn(DB::raw('MONTH(addition_date)'), $months);
            $entryQ->whereIn(DB::raw('MONTH(entry_date)'), $months);
        }

        $b = (clone $budgetQ)->delete();
        $c = (clone $cashQ)->delete();
        $e = (clone $entryQ)->delete();
        $this->info("Deleted budgets={$b}, cash_additions={$c}, manual_entries={$e}");
    }

    /** @param  array{description: string, debit: ?float, credit: ?float}  $line */
    private function lineKey(array $line): string
    {
        return strtoupper(implode('|', [
            (string) ($line['description'] ?? ''),
            number_format((float) ($line['debit'] ?? 0), 2, '.', ''),
            number_format((float) ($line['credit'] ?? 0), 2, '.', ''),
        ]));
    }

    /** @return array<string, true> */
    private function existingLineKeys(int $pmId, string $date): array
    {
        $keys = [];
        foreach (
            SoaManualEntry::query()
                ->where('payment_method_id', $pmId)
                ->whereDate('entry_date', $date)
                ->get(['description', 'debit', 'credit']) as $row
        ) {
            $keys[$this->lineKey([
                'description' => $row->description,
                'debit' => $row->debit,
                'credit' => $row->credit,
            ])] = true;
        }

        return $keys;
    }

    private function saveWithManualId(\Illuminate\Database\Eloquent\Model $model, int $id): void
    {
        $model->id = $id;
        $model->incrementing = false;
        $model->save();
    }

    /** @return list<int> */
    private function parseMonths(string $raw): array
    {
        $raw = trim($raw);
        if ($raw === '') {
            return [];
        }
        $months = [];
        foreach (preg_split('/\s*,\s*/', $raw) ?: [] as $part) {
            $n = (int) $part;
            if ($n >= 1 && $n <= 12) {
                $months[] = $n;
            }
        }

        return array_values(array_unique($months));
    }

    /** @return list<string> */
    private function parseChannels(string $raw): array
    {
        $out = [];
        foreach (preg_split('/\s*,\s*/', strtolower(trim($raw))) ?: [] as $part) {
            if (in_array($part, ['cash_vault', 'gcash'], true)) {
                $out[] = $part;
            }
        }

        return array_values(array_unique($out));
    }
}
