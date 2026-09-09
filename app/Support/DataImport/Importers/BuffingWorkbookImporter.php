<?php

namespace App\Support\DataImport\Importers;

use App\Support\DataImport\AbstractJsonWorkbookImporter;
use App\Support\DataImport\ExcelSheetHelper;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

class BuffingWorkbookImporter extends AbstractJsonWorkbookImporter
{
    public function key(): string
    {
        return 'buffing';
    }

    public function label(): string
    {
        return 'Buffing Tracker';
    }

    public function page(): array
    {
        return ['label' => 'Buffing Tracker', 'route' => 'buffing-tracker.index'];
    }

    public function tables(): array
    {
        return ['buffing_records'];
    }

    protected function artisanCommand(): string
    {
        return 'import:buffing-tracker';
    }

    protected function artisanParams(string $jsonPath, array $sheetNames): array
    {
        return ['--file' => $jsonPath];
    }

    public function matches(string $originalName, array $sheetNames): bool
    {
        $name = strtoupper($originalName);
        if (str_contains($name, 'BUFFING')) {
            return true;
        }
        foreach ($sheetNames as $s) {
            if (stripos($s, 'BUFFING TRACKER') !== false) {
                return true;
            }
        }

        return false;
    }

    public function listSheets(string $path): array
    {
        $ss = ExcelSheetHelper::load($path);
        $out = [];
        foreach ($ss->getWorksheetIterator() as $ws) {
            $name = $ws->getTitle();
            $u = strtoupper($name);
            $supported = str_contains($u, 'BUFFING TRACKER') && ! str_contains($u, 'DATA');
            $rows = 0;
            if ($supported) {
                $header = ExcelSheetHelper::findHeaderMap($ws, ['DATE'], 10, 50);
                if ($header) {
                    $rows = ExcelSheetHelper::countNonEmptyRows(
                        $ws,
                        $header['row'],
                        ExcelSheetHelper::col($header['map'], ['DATE'])
                    );
                } else {
                    $supported = false;
                }
            }
            $out[] = [
                'name' => $name,
                'supported' => $supported,
                'excel_rows' => $rows,
                'tables' => $this->tables(),
                'note' => $supported ? null : 'Not a buffing tracker data tab (skipped).',
            ];
        }
        $ss->disconnectWorksheets();

        return $out;
    }

    protected function parseRows(string $path, array $sheetNames): array
    {
        $ss = ExcelSheetHelper::load($path);
        $rows = [];
        foreach ($sheetNames as $name) {
            $ws = $ss->getSheetByName($name);
            if (! $ws) {
                continue;
            }
            $u = strtoupper($name);
            if (! str_contains($u, 'BUFFING TRACKER') || str_contains($u, 'DATA')) {
                continue;
            }

            $header = ExcelSheetHelper::findHeaderMap($ws, ['DATE'], 10, 50);
            if (! $header) {
                continue;
            }

            $headerRow = $header['row'];
            $dateCol = ExcelSheetHelper::col($header['map'], ['DATE']);
            if (! $dateCol) {
                continue;
            }

            $maxCol = min(60, Coordinate::columnIndexFromString($ws->getHighestDataColumn() ?: 'A'));
            $employees = [];
            for ($c = 1; $c <= $maxCol; $c++) {
                $raw = $ws->getCellByColumnAndRow($c, $headerRow)->getValue();
                if ($raw === null || $raw === '') {
                    continue;
                }
                $label = strtoupper(trim(preg_replace('/\s+/', ' ', (string) $raw)));
                if ($label === '' || $label === 'DATE') {
                    continue;
                }
                if (str_contains($label, 'TOTAL') || str_contains($label, 'FLAGSHIP TOTAL') || str_contains($label, 'ANNEX TOTAL')) {
                    continue;
                }
                $employees[] = ['col' => $c, 'name' => trim((string) $raw)];
            }

            $highest = (int) $ws->getHighestDataRow();
            for ($r = $headerRow + 1; $r <= $highest; $r++) {
                $date = ExcelSheetHelper::toDate(ExcelSheetHelper::cell($ws, $dateCol, $r));
                if (! $date) {
                    continue;
                }

                foreach ($employees as $emp) {
                    $c = $emp['col'];
                    $plateCell = ExcelSheetHelper::cellString($ws, $c, $r);
                    if (! $plateCell) {
                        continue;
                    }

                    $plate = $this->extractPlate($plateCell);
                    $model = null;
                    if ($plate === null) {
                        // Sometimes plate is in employee col and model in next; if no plate, skip
                        continue;
                    }

                    $next = ExcelSheetHelper::cellString($ws, $c + 1, $r);
                    if ($next && $this->extractPlate($next) === null && ! preg_match('/^(COMPLETED|IN PROGRESS|PENDING|DONE)/i', $next)) {
                        $model = $next;
                    } else {
                        // Plate cell may contain "PLATE MODEL"
                        $parts = preg_split('/\s+/', trim($plateCell)) ?: [];
                        if (count($parts) > 1) {
                            $maybeModel = $parts[count($parts) - 1];
                            if ($this->extractPlate($maybeModel) === null) {
                                $model = $maybeModel;
                            }
                        }
                    }

                    $rows[] = [
                        'row' => $r,
                        'sheet' => $name,
                        'buffing_date' => $date,
                        'employee_name' => $emp['name'],
                        'plate_number' => $plate,
                        'model' => $model,
                        'status' => 'Completed',
                        'is_task' => false,
                        'notes' => null,
                    ];
                }
            }
        }
        $ss->disconnectWorksheets();

        return $rows;
    }

    private function extractPlate(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }
        if (preg_match_all('/\b([A-Za-z]{1,3}[-\s]?\d{2,4}[A-Za-z0-9]?|\d{2,4}[-\s]?[A-Za-z]{1,3})\b/', $value, $m)) {
            foreach ($m[1] as $token) {
                $norm = ExcelSheetHelper::normalizePlate($token);
                if (preg_match('/[A-Z]/', $norm) && preg_match('/\d/', $norm) && strlen($norm) >= 5 && strlen($norm) <= 10) {
                    return $norm;
                }
            }
        }
        // Fallback: single alphanumeric token with letters+digits
        $token = ExcelSheetHelper::normalizePlate($value);
        if (preg_match('/^[A-Z0-9]+$/', $token) && preg_match('/[A-Z]/', $token) && preg_match('/\d/', $token) && strlen($token) >= 5 && strlen($token) <= 10) {
            return $token;
        }

        return null;
    }
}
