<?php

namespace App\Console\Commands;

use App\Models\MechanicExpenseRecord;
use App\Models\Tool;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ImportMechanicToolsCommand extends Command
{
    protected $signature = 'import:mechanic-tools
                            {--file= : Path to MECHANIC TOOLS AND EXPENSES.xlsx}
                            {--fresh : Truncate tools + parts/external before import}
                            {--skip-existing : Skip rows already in DB (default when not --fresh)}
                            {--dry-run : Parse and report without writing}';

    protected $description = 'Import TOOLS INVENTORY, PARTS, and EXTERNAL EXPENSES from MECHANIC TOOLS AND EXPENSES.xlsx';

    public function handle(): int
    {
        $path = $this->option('file');
        if (! $path || ! is_file($path)) {
            $this->error('Provide a valid --file= path to the Excel workbook.');

            return self::FAILURE;
        }

        $toolRows = $this->parseToolsInventory($path);
        $partRows = $this->parseParts($path);
        $externalRows = $this->parseExternalExpenses($path);

        $this->info('Tools rows: '.count($toolRows));
        $this->info('Parts rows: '.count($partRows));
        $this->info('External expense rows: '.count($externalRows));

        $skipExisting = (bool) $this->option('skip-existing') || ! $this->option('fresh');

        if ($this->option('dry-run')) {
            if ($skipExisting) {
                $existingTools = $this->existingToolKeys();
                $existingParts = $this->existingExpenseKeys('parts');
                $existingExt = $this->existingExpenseKeys('external');
                $newTools = count(array_filter($toolRows, fn ($r) => ! isset($existingTools[$this->toolKey($r)])));
                $newParts = count(array_filter($partRows, fn ($r) => ! isset($existingParts[$this->expenseKey('parts', $r)])));
                $newExt = count(array_filter($externalRows, fn ($r) => ! isset($existingExt[$this->expenseKey('external', $r)])));
                $this->info("Would create — tools: {$newTools}, parts: {$newParts}, external: {$newExt}");
            }
            $this->warn('Dry run — no database writes.');

            return self::SUCCESS;
        }

        if ($this->option('fresh')) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            Tool::query()->truncate();
            MechanicExpenseRecord::query()->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->warn('tools_inventory + mechanic_expense_records truncated.');
            $skipExisting = false;
        }

        $now = now();
        $toolsCreated = 0;
        $toolsSkipped = 0;
        $existingTools = $skipExisting ? $this->existingToolKeys() : [];
        foreach (array_chunk($toolRows, 200) as $chunk) {
            $payload = [];
            foreach ($chunk as $row) {
                $key = $this->toolKey($row);
                if ($skipExisting && isset($existingTools[$key])) {
                    $toolsSkipped++;
                    continue;
                }
                $payload[] = [
                    'name' => $row['name'],
                    'quantity' => $row['quantity'],
                    'amount' => $row['amount'],
                    'date_acquired' => $row['date_acquired'],
                    'entry_type' => $row['entry_type'] ?? 'purchase',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $existingTools[$key] = true;
            }
            if ($payload !== []) {
                $nextId = ((int) DB::table('tools_inventory')->max('id')) + 1;
                foreach ($payload as $i => $item) {
                    $payload[$i]['id'] = $nextId + $i;
                }
                DB::table('tools_inventory')->insert($payload);
                $toolsCreated += count($payload);
            }
        }

        $partsCreated = 0;
        $partsSkipped = 0;
        $extCreated = 0;
        $extSkipped = 0;
        $existingParts = $skipExisting ? $this->existingExpenseKeys('parts') : [];
        $existingExt = $skipExisting ? $this->existingExpenseKeys('external') : [];

        $expensePayload = [];
        foreach ($partRows as $row) {
            $key = $this->expenseKey('parts', $row);
            if ($skipExisting && isset($existingParts[$key])) {
                $partsSkipped++;
                continue;
            }
            $expensePayload[] = [
                'record_type' => 'parts',
                'description' => $row['description'],
                'amount' => $row['amount'],
                'repaired_by' => null,
                'unit_label' => null,
                'expense_date' => $row['expense_date'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $existingParts[$key] = true;
            $partsCreated++;
        }
        foreach ($externalRows as $row) {
            $key = $this->expenseKey('external', $row);
            if ($skipExisting && isset($existingExt[$key])) {
                $extSkipped++;
                continue;
            }
            $expensePayload[] = [
                'record_type' => 'external',
                'description' => $row['description'],
                'amount' => $row['amount'],
                'repaired_by' => $row['repaired_by'],
                'unit_label' => $row['unit_label'],
                'expense_date' => $row['expense_date'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $existingExt[$key] = true;
            $extCreated++;
        }
        foreach (array_chunk($expensePayload, 200) as $chunk) {
            $nextId = ((int) DB::table('mechanic_expense_records')->max('id')) + 1;
            foreach ($chunk as $i => $item) {
                $chunk[$i]['id'] = $nextId + $i;
            }
            DB::table('mechanic_expense_records')->insert($chunk);
        }

        $this->table(
            ['Metric', 'Count'],
            [
                ['Tools created', $toolsCreated],
                ['Tools skipped (existing)', $toolsSkipped],
                ['Parts created', $partsCreated],
                ['Parts skipped (existing)', $partsSkipped],
                ['External created', $extCreated],
                ['External skipped (existing)', $extSkipped],
                ['Tools in DB', Tool::count()],
                ['Parts in DB', MechanicExpenseRecord::parts()->count()],
                ['External in DB', MechanicExpenseRecord::external()->count()],
            ]
        );

        return self::SUCCESS;
    }

    /** @param  array<string, mixed>  $row */
    private function toolKey(array $row): string
    {
        return strtoupper(implode('|', [
            (string) ($row['entry_type'] ?? 'purchase'),
            (string) ($row['name'] ?? ''),
            (string) ($row['date_acquired'] ?? ''),
            (string) ($row['quantity'] ?? ''),
            number_format((float) ($row['amount'] ?? 0), 2, '.', ''),
        ]));
    }

    /** @param  array<string, mixed>  $row */
    private function expenseKey(string $type, array $row): string
    {
        return strtoupper(implode('|', [
            $type,
            (string) ($row['description'] ?? ''),
            number_format((float) ($row['amount'] ?? 0), 2, '.', ''),
            (string) ($row['expense_date'] ?? ''),
            (string) ($row['repaired_by'] ?? ''),
            (string) ($row['unit_label'] ?? ''),
        ]));
    }

    /** @return array<string, true> */
    private function existingToolKeys(): array
    {
        $keys = [];
        foreach (Tool::query()->get(['name', 'quantity', 'amount', 'date_acquired', 'entry_type']) as $row) {
            $keys[$this->toolKey([
                'entry_type' => $row->entry_type ?? 'purchase',
                'name' => $row->name,
                'date_acquired' => optional($row->date_acquired)->format('Y-m-d'),
                'quantity' => $row->quantity,
                'amount' => $row->amount,
            ])] = true;
        }

        return $keys;
    }

    /** @return array<string, true> */
    private function existingExpenseKeys(string $type): array
    {
        $keys = [];
        $query = $type === 'parts'
            ? MechanicExpenseRecord::parts()
            : MechanicExpenseRecord::external();
        foreach ($query->get() as $row) {
            $keys[$this->expenseKey($type, [
                'description' => $row->description,
                'amount' => $row->amount,
                'expense_date' => optional($row->expense_date)->format('Y-m-d'),
                'repaired_by' => $row->repaired_by,
                'unit_label' => $row->unit_label,
            ])] = true;
        }

        return $keys;
    }

    /**
     * @return array<int, array{name:string,quantity:int,amount:float,date_acquired:string,entry_type:string}>
     */
    public function parseToolsInventory(string $path): array
    {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $ss = $reader->load($path);
        $ws = $ss->getSheetByName('TOOLS INVENTORY') ?: $ss->getSheet(0);

        $highestCol = Coordinate::columnIndexFromString($ws->getHighestDataColumn() ?: 'A');
        $highestRow = (int) $ws->getHighestDataRow();

        $blocks = [];
        for ($c = 1; $c <= $highestCol; $c++) {
            $h = strtoupper(trim((string) $ws->getCellByColumnAndRow($c, 2)->getValue()));
            if (! str_contains($h, 'NAME OF TOOL')) {
                continue;
            }
            $qtyCol = null;
            $amtCol = null;
            $dateCol = null;
            for ($k = $c + 1; $k <= min($c + 4, $highestCol); $k++) {
                $hh = strtoupper(trim((string) $ws->getCellByColumnAndRow($k, 2)->getValue()));
                if ($hh === 'QUANTITY' && $qtyCol === null) {
                    $qtyCol = $k;
                } elseif ($hh === 'AMOUNT' && $amtCol === null) {
                    $amtCol = $k;
                } elseif ($hh === 'DATE' && $dateCol === null) {
                    $dateCol = $k;
                }
            }
            if ($qtyCol && $dateCol) {
                $blocks[] = [
                    'name' => $c,
                    'qty' => $qtyCol,
                    'amount' => $amtCol,
                    'date' => $dateCol,
                ];
            }
        }

        $rows = [];
        $seen = [];

        foreach ($blocks as $block) {
            $lastDate = null;
            for ($r = 3; $r <= $highestRow; $r++) {
                $name = trim((string) $ws->getCellByColumnAndRow($block['name'], $r)->getValue());
                $dateRaw = $ws->getCellByColumnAndRow($block['date'], $r)->getValue();
                if ($dateRaw !== null && $dateRaw !== '') {
                    $parsed = $this->toDate($dateRaw);
                    if ($parsed) {
                        $lastDate = $parsed;
                    }
                }

                if ($name === '' || strtoupper($name) === 'TOTAL') {
                    continue;
                }
                if (! $lastDate) {
                    continue;
                }

                $qtyRaw = $ws->getCellByColumnAndRow($block['qty'], $r)->getValue();
                $qty = (int) round((float) ($qtyRaw ?? 0));
                if ($qty < 1) {
                    $qty = 1;
                }

                $amount = 0.0;
                $hasAmountCell = false;
                if ($block['amount']) {
                    $amtRaw = $ws->getCellByColumnAndRow($block['amount'], $r)->getValue();
                    if ($amtRaw !== null && $amtRaw !== '') {
                        $amount = round((float) $amtRaw, 2);
                        $hasAmountCell = true;
                    }
                }

                if ($block['amount']) {
                    if (! $hasAmountCell) {
                        continue;
                    }
                    $entryType = 'purchase';
                } else {
                    $entryType = 'inventory';
                    $amount = 0.0;
                }

                $key = $entryType.'|'.strtoupper($name).'|'.$lastDate.'|'.$qty.'|'.$amount;
                if (isset($seen[$key])) {
                    continue;
                }
                $seen[$key] = true;

                $rows[] = [
                    'name' => mb_substr($name, 0, 255),
                    'quantity' => $qty,
                    'amount' => $amount,
                    'date_acquired' => $lastDate,
                    'entry_type' => $entryType,
                ];
            }
        }

        $ss->disconnectWorksheets();

        return $rows;
    }

    /**
     * @return array<int, array{description:string,amount:float,expense_date:string}>
     */
    public function parseParts(string $path): array
    {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $ss = $reader->load($path);
        $ws = $ss->getSheetByName('PARTS');
        if (! $ws) {
            $ss->disconnectWorksheets();

            return [];
        }

        $rows = [];
        $highest = (int) $ws->getHighestDataRow();
        for ($r = 2; $r <= $highest; $r++) {
            $desc = trim((string) $ws->getCellByColumnAndRow(2, $r)->getValue());
            if ($desc === '' || strtoupper($desc) === 'TOTAL') {
                continue;
            }
            $amount = round((float) ($ws->getCellByColumnAndRow(3, $r)->getValue() ?? 0), 2);
            $date = $this->toDate($ws->getCellByColumnAndRow(4, $r)->getValue());
            if (! $date) {
                continue;
            }
            $rows[] = [
                'description' => mb_substr($desc, 0, 255),
                'amount' => $amount,
                'expense_date' => $date,
            ];
        }
        $ss->disconnectWorksheets();

        return $rows;
    }

    /**
     * @return array<int, array{description:string,amount:float,repaired_by:?string,unit_label:?string,expense_date:string}>
     */
    public function parseExternalExpenses(string $path): array
    {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $ss = $reader->load($path);
        $ws = $ss->getSheetByName('EXTERNAL EXPENSES');
        if (! $ws) {
            $ss->disconnectWorksheets();

            return [];
        }

        $rows = [];
        $highest = (int) $ws->getHighestDataRow();
        for ($r = 2; $r <= $highest; $r++) {
            $desc = trim((string) $ws->getCellByColumnAndRow(2, $r)->getValue());
            if ($desc === '' || strtoupper($desc) === 'TOTAL') {
                continue;
            }
            $amount = round((float) ($ws->getCellByColumnAndRow(3, $r)->getValue() ?? 0), 2);
            $repairedBy = trim((string) ($ws->getCellByColumnAndRow(4, $r)->getValue() ?? ''));
            $unit = trim((string) ($ws->getCellByColumnAndRow(5, $r)->getValue() ?? ''));
            $date = $this->toDate($ws->getCellByColumnAndRow(6, $r)->getValue());
            if (! $date) {
                continue;
            }
            $rows[] = [
                'description' => mb_substr($desc, 0, 255),
                'amount' => $amount,
                'repaired_by' => $repairedBy !== '' ? mb_substr($repairedBy, 0, 255) : null,
                'unit_label' => $unit !== '' ? mb_substr($unit, 0, 255) : null,
                'expense_date' => $date,
            ];
        }
        $ss->disconnectWorksheets();

        return $rows;
    }

    private function toDate($value): ?string
    {
        try {
            if ($value === null || $value === '') {
                return null;
            }
            if (is_numeric($value)) {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            }
            $ts = strtotime(trim((string) $value));

            return $ts === false ? null : date('Y-m-d', $ts);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
