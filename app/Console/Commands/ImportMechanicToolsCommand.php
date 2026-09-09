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

        if ($this->option('dry-run')) {
            $this->warn('Dry run — no database writes.');

            return self::SUCCESS;
        }

        if ($this->option('fresh')) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            Tool::query()->truncate();
            MechanicExpenseRecord::query()->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            $this->warn('tools_inventory + mechanic_expense_records truncated.');
        }

        $now = now();
        foreach (array_chunk($toolRows, 200) as $chunk) {
            $payload = [];
            foreach ($chunk as $row) {
                $payload[] = [
                    'name' => $row['name'],
                    'quantity' => $row['quantity'],
                    'amount' => $row['amount'],
                    'date_acquired' => $row['date_acquired'],
                    'entry_type' => $row['entry_type'] ?? 'purchase',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            DB::table('tools_inventory')->insert($payload);
        }

        $expensePayload = [];
        foreach ($partRows as $row) {
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
        }
        foreach ($externalRows as $row) {
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
        }
        foreach (array_chunk($expensePayload, 200) as $chunk) {
            DB::table('mechanic_expense_records')->insert($chunk);
        }

        $this->info('Tools in DB: '.Tool::count());
        $this->info('Parts in DB: '.MechanicExpenseRecord::parts()->count());
        $this->info('External in DB: '.MechanicExpenseRecord::external()->count());

        return self::SUCCESS;
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
