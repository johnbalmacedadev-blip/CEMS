<?php

namespace App\Support\DataImport\Importers;

use App\Support\DataImport\AbstractJsonWorkbookImporter;
use App\Support\DataImport\ExcelSheetHelper;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class SoaExpensesWorkbookImporter extends AbstractJsonWorkbookImporter
{
    protected ?int $detectedYear = null;

    public function key(): string
    {
        return 'soa_expenses';
    }

    public function label(): string
    {
        return 'SOA Expenses';
    }

    public function page(): array
    {
        return ['label' => 'SOA Expenses', 'route' => 'soa.create'];
    }

    public function tables(): array
    {
        return ['daily_budgets', 'cash_additions', 'soa_manual_entries'];
    }

    protected function artisanCommand(): string
    {
        if ($this->detectedYear === 2025) {
            return 'import:soa-expenses-2025';
        }

        return 'import:soa-expenses';
    }

    protected function artisanParams(string $jsonPath, array $sheetNames): array
    {
        if ($this->detectedYear === 2025) {
            return ['--file' => $jsonPath];
        }

        return [
            '--file' => $jsonPath,
            '--year' => $this->detectedYear ?? (int) date('Y'),
        ];
    }

    public function matches(string $originalName, array $sheetNames): bool
    {
        $name = strtoupper($originalName);
        if (str_contains($name, 'EXPENSES') && (str_contains($name, '2025') || str_contains($name, '2026'))) {
            return true;
        }
        $hasFlagship = false;
        foreach ($sheetNames as $s) {
            if (stripos($s, 'FLAGSHIP') !== false) {
                $hasFlagship = true;
                break;
            }
        }

        // Sheet-name heuristic when filename lacks year (FLAGSHIP day-block workbooks)
        return $hasFlagship && str_contains($name, 'EXPENSE');
    }

    public function listSheets(string $path): array
    {
        $this->detectYearFromPath($path);
        $ss = ExcelSheetHelper::load($path);
        $out = [];
        foreach ($ss->getWorksheetIterator() as $ws) {
            $name = $ws->getTitle();
            $u = strtoupper($name);
            $supported = (str_contains($u, 'FLAGSHIP') || str_contains($u, 'WAREHOUSE') || str_contains($u, 'ANNEX'))
                && ! str_contains($u, 'MALI PA FORMULA');
            $rows = 0;
            if ($supported) {
                $rows = $this->estimateDayBlocks($ws);
            }
            $out[] = [
                'name' => $name,
                'supported' => $supported,
                'excel_rows' => $rows,
                'tables' => $this->tables(),
                'note' => $supported
                    ? 'Tier: '.$this->tierFromSheet($name)
                    : 'Only FLAGSHIP / WAREHOUSE / ANNEX tabs (excl. MALI PA FORMULA).',
            ];
        }
        $ss->disconnectWorksheets();

        return $out;
    }

    protected function parseRows(string $path, array $sheetNames): array
    {
        $this->detectYearFromPath($path, $sheetNames);
        $ss = ExcelSheetHelper::load($path);
        $days = [];
        foreach ($sheetNames as $name) {
            $ws = $ss->getSheetByName($name);
            if (! $ws) {
                continue;
            }
            $u = strtoupper($name);
            if (str_contains($u, 'MALI PA FORMULA')) {
                continue;
            }
            if (! str_contains($u, 'FLAGSHIP') && ! str_contains($u, 'WAREHOUSE') && ! str_contains($u, 'ANNEX')) {
                continue;
            }
            $tier = $this->tierFromSheet($name);
            if (! $tier) {
                continue;
            }
            foreach ($this->parseDayBlocks($ws, $name, $tier) as $day) {
                $days[] = $day;
            }
        }
        $ss->disconnectWorksheets();

        return $days;
    }

    private function detectYearFromPath(string $path, array $sheetNames = []): void
    {
        $base = strtoupper(basename($path));
        if (preg_match('/\b(20\d{2})\b/', $base, $m)) {
            $this->detectedYear = (int) $m[1];

            return;
        }
        foreach ($sheetNames as $s) {
            if (preg_match('/\b(20\d{2})\b/', $s, $m)) {
                $this->detectedYear = (int) $m[1];

                return;
            }
        }
        // Try sheet titles from workbook
        try {
            $names = ExcelSheetHelper::sheetNames($path);
            foreach ($names as $s) {
                if (preg_match('/\b(20\d{2})\b/', $s, $m)) {
                    $this->detectedYear = (int) $m[1];

                    return;
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }
        $this->detectedYear = (int) date('Y');
    }

    private function tierFromSheet(string $name): ?string
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

    private function estimateDayBlocks($ws): int
    {
        $n = 0;
        $highest = min((int) $ws->getHighestDataRow(), 5000);
        $maxCol = min(4, Coordinate::columnIndexFromString($ws->getHighestDataColumn() ?: 'A'));
        for ($r = 1; $r <= $highest; $r++) {
            for ($c = 1; $c <= $maxCol; $c++) {
                if (ExcelSheetHelper::toDate($ws->getCellByColumnAndRow($c, $r)->getValue())) {
                    $n++;
                    break;
                }
            }
        }

        return $n;
    }

    /** @return array<int, array<string, mixed>> */
    private function parseDayBlocks($ws, string $sheet, string $tier): array
    {
        $highest = (int) $ws->getHighestDataRow();
        $days = [];
        $current = null;

        for ($r = 1; $r <= $highest; $r++) {
            $a = $ws->getCellByColumnAndRow(1, $r)->getValue();
            $b = $ws->getCellByColumnAndRow(2, $r)->getValue();
            $c = $ws->getCellByColumnAndRow(3, $r)->getValue();

            $date = ExcelSheetHelper::toDate($a);
            // Day header: date-like in first column, and next rows are STARTING CASH etc.
            if ($date && ($b === null || $b === '' || ! is_numeric($b) || ExcelSheetHelper::toDate($b))) {
                // Peek: is next row STARTING CASH?
                $nextLabel = strtoupper(trim((string) ($ws->getCellByColumnAndRow(1, $r + 1)->getValue() ?? '')));
                $isDayStart = str_contains($nextLabel, 'STARTING CASH')
                    || str_contains($nextLabel, 'ADDED CASH')
                    || ($b === null || $b === '');

                if ($isDayStart || ($b === null || $b === '')) {
                    if ($current && ($current['expenses'] !== [] || $current['starting_balance'] !== null)) {
                        $days[] = $this->finalizeDay($current);
                    }
                    $current = [
                        'sheet' => $sheet,
                        'tier' => $tier,
                        'entry_date' => $date,
                        'starting_balance' => null,
                        'added_cash' => 0,
                        'expenses' => [],
                        'source_row' => $r,
                    ];
                    continue;
                }
            }

            if ($current === null) {
                // Sometimes STARTING CASH appears without a preceding date serial visible — skip until date
                $label = strtoupper(trim((string) ($a ?? '')));
                if (str_contains($label, 'STARTING CASH') && ExcelSheetHelper::toFloat($b) !== null) {
                    // invent no date — skip orphan balances
                }
                continue;
            }

            $label = strtoupper(trim((string) ($a ?? '')));
            if ($label === '') {
                continue;
            }

            if (str_contains($label, 'STARTING CASH')) {
                $current['starting_balance'] = ExcelSheetHelper::toFloat($b) ?? 0;
                continue;
            }
            if (str_contains($label, 'ADDED CASH')) {
                $current['added_cash'] = ExcelSheetHelper::toFloat($b) ?? 0;
                continue;
            }
            if (str_contains($label, 'TOTAL CASH') || str_contains($label, 'TOTAL EXPENSES')) {
                continue;
            }

            $amount = ExcelSheetHelper::toFloat($b);
            if ($amount === null || $amount <= 0) {
                continue;
            }

            $desc = trim((string) $a);
            $unit = is_string($c) ? trim($c) : (is_numeric($c) ? null : trim((string) ($c ?? '')));
            if ($unit !== null && $unit !== '') {
                $desc .= ' — '.$unit;
            }

            $current['expenses'][] = [
                'description' => $desc,
                'amount' => $amount,
                'row' => $r,
            ];
        }

        if ($current && ($current['expenses'] !== [] || $current['starting_balance'] !== null)) {
            $days[] = $this->finalizeDay($current);
        }

        return $days;
    }

    private function finalizeDay(array $day): array
    {
        return [
            'sheet' => $day['sheet'],
            'tier' => $day['tier'],
            'entry_date' => $day['entry_date'],
            'starting_balance' => $day['starting_balance'] ?? 0,
            'added_cash' => $day['added_cash'] ?? 0,
            'expenses' => $day['expenses'],
            'source_row' => $day['source_row'],
        ];
    }
}
