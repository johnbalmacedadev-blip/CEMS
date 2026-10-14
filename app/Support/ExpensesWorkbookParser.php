<?php

namespace App\Support;

use App\Support\DataImport\ExcelSheetHelper;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExpensesWorkbookParser
{
    /**
     * Parse selected sheets into day blocks with expense lines.
     *
     * @param  list<string>  $sheetNames
     * @param  list<int>  $months  1-12; empty = all months found
     * @return array<int, array<string, mixed>>
     */
    public function parse(string $path, array $sheetNames = [], array $months = []): array
    {
        $ss = ExcelSheetHelper::load($path);
        if ($sheetNames === []) {
            foreach ($ss->getWorksheetIterator() as $ws) {
                $sheetNames[] = $ws->getTitle();
            }
        }

        $days = [];
        foreach ($sheetNames as $name) {
            $ws = $ss->getSheetByName($name);
            if (! $ws) {
                continue;
            }
            $tier = $this->tierFromSheet($name);
            if (! $tier) {
                continue;
            }

            $streams = $this->streamsForSheet($name, $tier);
            $byDate = [];

            foreach ($streams as $stream) {
                foreach ($this->parseStream($ws, $name, $tier, $stream) as $day) {
                    $date = (string) ($day['entry_date'] ?? '');
                    if ($date === '') {
                        continue;
                    }
                    if ($months !== []) {
                        $monthNum = (int) date('n', strtotime($date));
                        if (! in_array($monthNum, $months, true)) {
                            continue;
                        }
                    }

                    if (! isset($byDate[$date])) {
                        $byDate[$date] = [
                            'sheet' => $name,
                            'tier' => $tier,
                            'entry_date' => $date,
                            'starting_balance' => null,
                            'added_cash' => 0.0,
                            'expenses' => [],
                        ];
                    }

                    if ($stream['use_cash'] && $day['starting_balance'] !== null) {
                        $byDate[$date]['starting_balance'] = $day['starting_balance'];
                        $byDate[$date]['added_cash'] = $day['added_cash'];
                    }

                    foreach ($day['expenses'] as $exp) {
                        $byDate[$date]['expenses'][] = $exp;
                    }
                }
            }

            foreach ($byDate as $day) {
                if (($day['expenses'] ?? []) === [] && $day['starting_balance'] === null) {
                    continue;
                }
                $days[] = $day;
            }
        }

        $ss->disconnectWorksheets();

        usort($days, function ($a, $b) {
            $cmp = strcmp((string) $a['entry_date'], (string) $b['entry_date']);
            if ($cmp !== 0) {
                return $cmp;
            }

            return strcmp((string) $a['tier'], (string) $b['tier']);
        });

        return $days;
    }

    /**
     * @return list<array{key:string,label:string,label_col:int,amount_col:int,unit_col:int,use_cash:bool}>
     */
    protected function streamsForSheet(string $sheetName, string $tier): array
    {
        $streams = [
            [
                'key' => 'daily',
                'label' => 'Daily Expenses',
                'label_col' => 1,
                'amount_col' => 2,
                'unit_col' => 3,
                'use_cash' => true,
            ],
        ];

        // Flagship tabs usually have Paper (D-F) and Online (G-I) blocks.
        if ($tier === 'flagship') {
            $streams[] = [
                'key' => 'paper',
                'label' => 'Paper Expenses',
                'label_col' => 4,
                'amount_col' => 5,
                'unit_col' => 6,
                'use_cash' => false,
            ];
            $streams[] = [
                'key' => 'online',
                'label' => 'Online Payment',
                'label_col' => 7,
                'amount_col' => 8,
                'unit_col' => 9,
                'use_cash' => false,
            ];
        }

        return $streams;
    }

    /**
     * @param  array{key:string,label:string,label_col:int,amount_col:int,unit_col:int,use_cash:bool}  $stream
     * @return array<int, array<string, mixed>>
     */
    protected function parseStream(Worksheet $ws, string $sheet, string $tier, array $stream): array
    {
        $highest = (int) $ws->getHighestDataRow();
        $labelCol = $stream['label_col'];
        $amountCol = $stream['amount_col'];
        $unitCol = $stream['unit_col'];
        $days = [];
        $current = null;

        for ($r = 1; $r <= $highest; $r++) {
            $labelRaw = $ws->getCellByColumnAndRow($labelCol, $r)->getValue();
            $amountRaw = $ws->getCellByColumnAndRow($amountCol, $r)->getValue();
            $unitRaw = $ws->getCellByColumnAndRow($unitCol, $r)->getValue();

            $date = ExcelSheetHelper::toDate($labelRaw);
            if ($date) {
                $nextLabel = strtoupper(trim((string) ($ws->getCellByColumnAndRow($labelCol, $r + 1)->getValue() ?? '')));
                $amountLooksEmpty = $amountRaw === null || $amountRaw === '' || ExcelSheetHelper::toDate($amountRaw);
                $isDayStart = str_contains($nextLabel, 'STARTING CASH')
                    || str_contains($nextLabel, 'ADDED CASH')
                    || $amountLooksEmpty;

                if ($isDayStart) {
                    if ($current && (($current['expenses'] ?? []) !== [] || $current['starting_balance'] !== null)) {
                        $days[] = $current;
                    }
                    $current = [
                        'sheet' => $sheet,
                        'tier' => $tier,
                        'stream' => $stream['key'],
                        'entry_date' => $date,
                        'starting_balance' => null,
                        'added_cash' => 0.0,
                        'expenses' => [],
                        'source_row' => $r,
                    ];
                    continue;
                }
            }

            if ($current === null) {
                continue;
            }

            $label = strtoupper(trim((string) ($labelRaw ?? '')));
            if ($label === '') {
                continue;
            }

            if (str_contains($label, 'STARTING CASH')) {
                $current['starting_balance'] = ExcelSheetHelper::toFloat($amountRaw) ?? 0.0;
                continue;
            }
            if (str_contains($label, 'ADDED CASH')) {
                $current['added_cash'] = ExcelSheetHelper::toFloat($amountRaw) ?? 0.0;
                continue;
            }
            if (str_contains($label, 'TOTAL CASH') || str_contains($label, 'TOTAL EXPENSES')) {
                continue;
            }
            if (str_contains($label, 'DAILY EXPENSES') || str_contains($label, 'PAPER EXPENSES') || str_contains($label, 'ONLINE PAYMENT')) {
                continue;
            }

            $amount = ExcelSheetHelper::toFloat($amountRaw);
            // Skip empty, non-positive, or absurd values (Excel serials / formula blowups).
            if ($amount === null || $amount <= 0 || $amount > 5000000) {
                continue;
            }

            $desc = trim((string) $labelRaw);
            $unit = is_string($unitRaw) ? trim($unitRaw) : '';
            if ($unit === '' && is_numeric($unitRaw)) {
                $unit = '';
            }

            $current['expenses'][] = [
                'description' => $desc,
                'amount' => $amount,
                'unit' => $unit !== '' ? $unit : null,
                'stream' => $stream['key'],
                'stream_label' => $stream['label'],
                'row' => $r,
            ];
        }

        if ($current && (($current['expenses'] ?? []) !== [] || $current['starting_balance'] !== null)) {
            $days[] = $current;
        }

        return $days;
    }

    public function tierFromSheet(string $name): ?string
    {
        $u = strtoupper($name);
        if (str_contains($u, 'FLAGSHIP')) {
            return 'flagship';
        }
        if (str_contains($u, 'WAREHOUSE')) {
            return 'warehouse';
        }
        if (str_contains($u, 'ANNEX')) {
            return 'annex';
        }

        return null;
    }

    /**
     * Extract a plate-like token from unit/description text.
     */
    public function extractPlate(?string $text): ?string
    {
        $t = strtoupper(trim((string) $text));
        if ($t === '') {
            return null;
        }
        if (preg_match('/\b([A-Z]{1,3}[0-9]{2,5}[A-Z0-9]{0,3})\b/', $t, $m)) {
            return ExcelSheetHelper::normalizePlate($m[1]);
        }

        return null;
    }
}
