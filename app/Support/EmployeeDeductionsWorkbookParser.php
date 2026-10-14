<?php

namespace App\Support;

use App\Support\DataImport\ExcelSheetHelper;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EmployeeDeductionsWorkbookParser
{
    /**
     * @return list<array<string, mixed>>
     */
    public function parse(string $path): array
    {
        $ss = ExcelSheetHelper::load($path);
        $rows = [];
        foreach ($ss->getWorksheetIterator() as $ws) {
            $name = $ws->getTitle();
            if (! preg_match('/START\s+OF\s+DEDUCTION\s+(\d{4})/i', $name, $m)) {
                continue;
            }
            $year = (int) $m[1];
            $header = $this->findNameHeader($ws);
            if (! $header) {
                continue;
            }
            $rows = array_merge($rows, $this->parseSheet($ws, $name, $year, $header));
        }
        $ss->disconnectWorksheets();

        return $rows;
    }

    /**
     * @return list<string>
     */
    public function listDeductionSheets(string $path): array
    {
        $ss = ExcelSheetHelper::load($path);
        $out = [];
        foreach ($ss->getWorksheetIterator() as $ws) {
            $name = $ws->getTitle();
            if (preg_match('/START\s+OF\s+DEDUCTION\s+\d{4}/i', $name)) {
                $out[] = $name;
            }
        }
        $ss->disconnectWorksheets();

        return $out;
    }

    /**
     * @return array{row:int,map:array<string,int>}|null
     */
    private function findNameHeader(Worksheet $ws): ?array
    {
        $header = ExcelSheetHelper::findHeaderMap($ws, ['LAST NAME', 'FIRST NAME'], 25, 40);
        if ($header
            && ExcelSheetHelper::col($header['map'], ['FIRST NAME']) !== null
            && ExcelSheetHelper::col($header['map'], ['LAST NAME']) !== null) {
            return $header;
        }

        return null;
    }

    /**
     * @param  array{row:int,map:array<string,int>}  $header
     * @return list<array<string, mixed>>
     */
    private function parseSheet(Worksheet $ws, string $sheet, int $year, array $header): array
    {
        $map = $header['map'];
        $cFirst = ExcelSheetHelper::col($map, ['FIRST NAME']);
        $cMiddle = ExcelSheetHelper::col($map, ['MIDDLE NAME']);
        $cLast = ExcelSheetHelper::col($map, ['LAST NAME']);

        $periodCols = $this->detectPeriodColumns($map);
        $annualCols = $this->detectAnnualColumns($map, $periodCols);

        $out = [];
        $highest = (int) $ws->getHighestDataRow();
        for ($r = $header['row'] + 1; $r <= $highest; $r++) {
            $first = ExcelSheetHelper::cellString($ws, $cFirst, $r);
            $last = ExcelSheetHelper::cellString($ws, $cLast, $r);
            if (! $first || ! $last) {
                // Stop after long empty stretch past data.
                continue;
            }
            if (strcasecmp($first, 'FIRST NAME') === 0 || strcasecmp($last, 'LAST NAME') === 0) {
                continue;
            }
            if (preg_match('/personnel|terminated|resign|inactive|active/i', $first.$last)) {
                continue;
            }

            $middle = $cMiddle ? ExcelSheetHelper::cellString($ws, $cMiddle, $r) : null;
            if ($middle && in_array(strtoupper(trim($middle)), ['N/A', 'NA', 'NMN', '-'], true)) {
                $middle = null;
            }

            foreach ($periodCols as $col => $meta) {
                $ee = $this->toAmount(ExcelSheetHelper::cell($ws, $col, $r));
                $er = null;
                if (! empty($meta['er_col'])) {
                    $er = $this->toAmount(ExcelSheetHelper::cell($ws, $meta['er_col'], $r));
                }
                if ($ee === null && $er === null) {
                    continue;
                }
                $out[] = [
                    'year' => $year,
                    'period_key' => $meta['key'],
                    'period_label' => $meta['label'],
                    'category' => $meta['category'],
                    'ee_share' => $ee,
                    'er_share' => $er,
                    'first_name' => $first,
                    'middle_name' => $middle,
                    'last_name' => $last,
                    'source_sheet' => $sheet,
                    'row' => $r,
                ];
            }

            foreach ($annualCols as $meta) {
                $ee = $this->toAmount(ExcelSheetHelper::cell($ws, $meta['ee_col'], $r));
                $er = isset($meta['er_col'])
                    ? $this->toAmount(ExcelSheetHelper::cell($ws, $meta['er_col'], $r))
                    : null;
                if (($ee === null || $ee == 0.0) && ($er === null || $er == 0.0)) {
                    continue;
                }
                $out[] = [
                    'year' => $year,
                    'period_key' => $meta['key'],
                    'period_label' => $meta['label'],
                    'category' => $meta['category'],
                    'ee_share' => $ee,
                    'er_share' => $er,
                    'first_name' => $first,
                    'middle_name' => $middle,
                    'last_name' => $last,
                    'source_sheet' => $sheet,
                    'row' => $r,
                ];
            }
        }

        return $out;
    }

    /**
     * @param  array<string, int>  $map
     * @return array<int, array{key:string,label:string,category:string,er_col:?int}>
     */
    private function detectPeriodColumns(array $map): array
    {
        $months = [
            'JANUARY' => 1, 'JAN' => 1, 'FEBRUARY' => 2, 'FEB' => 2, 'MARCH' => 3, 'MAR' => 3,
            'APRIL' => 4, 'APR' => 4, 'MAY' => 5, 'JUNE' => 6, 'JULY' => 7, 'AUGUST' => 8, 'AUG' => 8,
            'SEPTEMBER' => 9, 'SEPT' => 9, 'SEP' => 9, 'OCTOBER' => 10, 'OCT' => 10,
            'NOVEMBER' => 11, 'NOV' => 11, 'DECEMBER' => 12, 'DEC' => 12,
        ];

        $eeCols = [];
        $erCols = [];
        foreach ($map as $label => $col) {
            $u = strtoupper(trim(preg_replace('/\s+/', ' ', $label) ?? ''));
            if (str_contains($u, 'EE SHARE') && preg_match('/\b(JANUARY|FEBRUARY|MARCH|APRIL|MAY|JUNE|JULY|AUGUST|SEPTEMBER|SEPT|OCTOBER|NOVEMBER|DECEMBER)\b/', $u, $m)) {
                $monthName = $m[1] === 'SEPT' ? 'SEPTEMBER' : $m[1];
                $eeCols[$col] = [
                    'key' => strtoupper($monthName),
                    'label' => ucfirst(strtolower($monthName)),
                    'category' => 'contribution',
                    'er_col' => null,
                ];
            } elseif (preg_match('/\b(JANUARY|FEBRUARY|MARCH|APRIL|MAY|JUNE|JULY|AUGUST|SEPTEMBER|OCTOBER|NOVEMBER|DECEMBER|JAN|FEB|MAR|APR|AUG|SEPT|SEP|OCT|NOV|DEC)\b(?:\s+(\d{1,2})\s+(\d{1,2}))?/i', $u, $m)
                && ! str_contains($u, 'TOTAL EE')
                && ! str_contains($u, 'TOTAL ER')
                && ! str_contains($u, 'RATE')
                && ! str_contains($u, 'EMPLOYEE SHARE')
                && ! str_contains($u, 'EMPLOYER SHARE')
                && ! in_array($u, ['FIRST NAME', 'MIDDLE NAME', 'LAST NAME', '#', 'EE SHARE', 'ER SHARE'], true)) {
                // Biweekly style after header normalize: "JAN 1 15"
                if (! empty($m[2]) && ! empty($m[3])) {
                    $mon = strtoupper($m[1]);
                    $full = [
                        'JAN' => 'JANUARY', 'JANUARY' => 'JANUARY',
                        'FEB' => 'FEBRUARY', 'FEBRUARY' => 'FEBRUARY',
                        'MAR' => 'MARCH', 'MARCH' => 'MARCH',
                        'APR' => 'APRIL', 'APRIL' => 'APRIL',
                        'MAY' => 'MAY',
                        'JUNE' => 'JUNE',
                        'JULY' => 'JULY',
                        'AUG' => 'AUGUST', 'AUGUST' => 'AUGUST',
                        'SEPT' => 'SEPTEMBER', 'SEP' => 'SEPTEMBER', 'SEPTEMBER' => 'SEPTEMBER',
                        'OCT' => 'OCTOBER', 'OCTOBER' => 'OCTOBER',
                        'NOV' => 'NOVEMBER', 'NOVEMBER' => 'NOVEMBER',
                        'DEC' => 'DECEMBER', 'DECEMBER' => 'DECEMBER',
                    ][$mon] ?? $mon;
                    $key = $full.'_'.$m[2].'_'.$m[3];
                    $eeCols[$col] = [
                        'key' => $key,
                        'label' => ucfirst(strtolower($full)).' '.$m[2].'–'.$m[3],
                        'category' => 'contribution',
                        'er_col' => null,
                    ];
                }
            }
        }

        // Pair 2026-style TOTAL EE SHARE MONTH with following TOTAL ER SHARE column.
        $paired = [];
        $eeList = $eeCols;
        ksort($eeList);
        foreach ($eeList as $eeCol => $meta) {
            if (str_starts_with($meta['key'], 'JANUARY') || strlen($meta['key']) <= 9) {
                // monthly total style — ER is typically next column in sheet
                $meta['er_col'] = $eeCol + 1;
            }
            $paired[$eeCol] = $meta;
        }

        // For biweekly sheets, ER columns at end are annual — don't pair per period.
        // Detect: if headers look like "JAN 1-15" without TOTAL EE SHARE, leave er_col null.
        $hasMonthlyTotal = false;
        foreach ($map as $label => $col) {
            if (stripos($label, 'TOTAL EE SHARE') !== false) {
                $hasMonthlyTotal = true;
                break;
            }
        }
        if (! $hasMonthlyTotal) {
            foreach ($paired as $col => $meta) {
                $paired[$col]['er_col'] = null;
            }
        }

        return $paired;
    }

    /**
     * Trailing annual SSS / PhilHealth / Pag-IBIG EE+ER columns.
     *
     * @param  array<string, int>  $map
     * @param  array<int, mixed>  $periodCols
     * @return list<array{key:string,label:string,category:string,ee_col:int,er_col:?int}>
     */
    private function detectAnnualColumns(array $map, array $periodCols): array
    {
        $maxPeriodCol = $periodCols ? max(array_keys($periodCols)) : 0;
        $out = [];

        // Build ordered header labels by column
        $byCol = [];
        foreach ($map as $label => $col) {
            $byCol[$col] = strtoupper(trim($label));
        }
        ksort($byCol);

        $labels = array_values($byCol);
        $cols = array_keys($byCol);

        for ($i = 0; $i < count($labels); $i++) {
            $col = $cols[$i];
            if ($col <= $maxPeriodCol) {
                continue;
            }
            $label = $labels[$i];
            if (! in_array($label, ['EE SHARE', 'TOTAL EE SHARE'], true) && ! str_ends_with($label, 'EE SHARE')) {
                continue;
            }
            // Look at nearby context from original map keys around this col — use sheet title context from previous non-share headers.
            $category = null;
            foreach ($map as $raw => $c) {
                if ($c >= $col - 3 && $c <= $col && preg_match('/\b(SSS|PHILHEALTH|PAG[\s-]?IBIG|PAGIBIG)\b/i', $raw, $m)) {
                    $category = strtolower(preg_replace('/[\s-]+/', '', $m[1]) ?? '');
                    if ($category === 'pagibig') {
                        $category = 'pagibig';
                    }
                }
            }
            // 2026 header row has SSS/PHILHEALTH above EE SHARE columns in row above — map may only have EE SHARE.
            if (! $category) {
                // Heuristic: first trailing EE/ER pair = SSS, second = PhilHealth, third = Pagibig
                $trailingIndex = count($out);
                $category = ['sss', 'philhealth', 'pagibig'][$trailingIndex] ?? null;
            }
            if (! $category) {
                continue;
            }
            $erCol = null;
            if (isset($cols[$i + 1]) && str_contains($labels[$i + 1], 'ER SHARE')) {
                $erCol = $cols[$i + 1];
            } else {
                $erCol = $col + 1;
            }
            $out[] = [
                'key' => strtoupper($category).'_YEAR',
                'label' => strtoupper($category) === 'SSS' ? 'SSS (year)' : (strtoupper($category) === 'PHILHEALTH' ? 'PhilHealth (year)' : 'Pag-IBIG (year)'),
                'category' => $category,
                'ee_col' => $col,
                'er_col' => $erCol,
            ];
        }

        return $out;
    }

    private function toAmount(mixed $value): ?float
    {
        if ($value === null || $value === '' || $value === '-') {
            return null;
        }
        if (is_numeric($value)) {
            return round((float) $value, 2);
        }
        $clean = preg_replace('/[^\d.-]/', '', (string) $value);

        return ($clean === '' || $clean === '-' || $clean === '.') ? null : round((float) $clean, 2);
    }
}
