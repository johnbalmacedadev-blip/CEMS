<?php

namespace App\Support;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SoaCashVaultWorkbookParser
{
    /**
     * @return list<array{
     *   channel: string,
     *   sheet: string,
     *   entry_date: string,
     *   starting_balance: float,
     *   lines: list<array{description: string, debit: ?float, credit: ?float}>
     * }>
     */
    public function parse(string $path): array
    {
        $ss = IOFactory::load($path);
        $days = [];

        foreach ($ss->getWorksheetIterator() as $ws) {
            $channel = $this->channelFromSheet($ws->getTitle());
            if ($channel === null) {
                continue;
            }
            foreach ($this->parseSheet($ws, $channel) as $day) {
                $days[] = $day;
            }
        }

        $ss->disconnectWorksheets();

        return $days;
    }

    public function channelFromSheet(string $title): ?string
    {
        $u = strtoupper($title);
        if (str_contains($u, 'GCASH')) {
            return 'gcash';
        }
        if (str_contains($u, 'CASH VAULT') || str_contains($u, 'SOA CASH')) {
            return 'cash_vault';
        }

        return null;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function parseSheet(Worksheet $ws, string $channel): array
    {
        $highest = (int) $ws->getHighestDataRow();
        $days = [];
        $current = null;

        for ($r = 1; $r <= $highest; $r++) {
            $rawA = $ws->getCellByColumnAndRow(1, $r)->getValue();
            $rawB = $ws->getCellByColumnAndRow(2, $r)->getValue();
            $rawC = $ws->getCellByColumnAndRow(3, $r)->getValue();

            $label = strtoupper(trim((string) ($rawA ?? '')));
            $cLabel = strtoupper(trim((string) ($rawC ?? '')));

            if ($this->isDateHeaderRow($label, $rawB, $cLabel)) {
                if ($current !== null) {
                    $days[] = $current;
                }

                $date = $this->toDate($rawB);
                if (! $date) {
                    $current = null;
                    continue;
                }

                $starting = $this->toMoney($ws->getCellByColumnAndRow(4, $r)->getCalculatedValue());
                $current = [
                    'channel' => $channel,
                    'sheet' => $ws->getTitle(),
                    'entry_date' => $date,
                    'starting_balance' => $starting ?? 0.0,
                    'lines' => [],
                ];
                continue;
            }

            if ($current === null) {
                continue;
            }

            if ($label === '' || $label === 'DESCRIPTION' || $label === 'TOTAL AMOUNT' || $label === 'DATE') {
                if ($label === 'TOTAL AMOUNT') {
                    // End of day block; keep current until next DATE so trailing blanks are fine
                }
                continue;
            }

            $debit = $this->toMoney($this->safeCalculated($ws, 2, $r));
            $credit = $this->toMoney($this->safeCalculated($ws, 3, $r));

            // Col B on DATE rows is a serial; for line rows ignore non-numeric noise
            if ($debit !== null && $debit <= 0) {
                $debit = null;
            }
            if ($credit !== null && $credit <= 0) {
                $credit = null;
            }
            if ($debit === null && $credit === null) {
                continue;
            }

            $current['lines'][] = [
                'description' => mb_substr(trim((string) $rawA), 0, 1000),
                'debit' => $debit,
                'credit' => $credit,
            ];
        }

        if ($current !== null) {
            $days[] = $current;
        }

        return $days;
    }

    private function isDateHeaderRow(string $labelA, mixed $rawB, string $labelC): bool
    {
        if ($labelA === 'DATE' && $this->toDate($rawB)) {
            return true;
        }

        // Some GCash blocks: blank A, excel date in B, "STARTING BALANCE" in C
        if ($labelA === '' && $this->toDate($rawB) && str_contains($labelC, 'STARTING BALANCE')) {
            return true;
        }

        return false;
    }

    private function safeCalculated(Worksheet $ws, int $col, int $row): mixed
    {
        try {
            return $ws->getCellByColumnAndRow($col, $row)->getCalculatedValue();
        } catch (\Throwable) {
            return $ws->getCellByColumnAndRow($col, $row)->getValue();
        }
    }

    private function toDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }
        if (is_numeric($value)) {
            $n = (float) $value;
            // Excel serial dates for 2020–2035 roughly 43831–50754; amounts like 100000 must not convert
            if ($n < 40000 || $n > 60000) {
                return null;
            }
            try {
                return ExcelDate::excelToDateTimeObject($n)->format('Y-m-d');
            } catch (\Throwable) {
                return null;
            }
        }
        $s = trim((string) $value);
        if ($s === '') {
            return null;
        }
        $ts = strtotime($s);
        if ($ts === false) {
            return null;
        }

        return date('Y-m-d', $ts);
    }

    private function toMoney(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_string($value)) {
            $value = str_replace([',', '₱', 'PHP', ' '], '', $value);
            if ($value === '' || ! is_numeric($value)) {
                return null;
            }
        }
        if (! is_numeric($value)) {
            return null;
        }

        return round((float) $value, 2);
    }
}
