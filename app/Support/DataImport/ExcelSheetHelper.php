<?php

namespace App\Support\DataImport;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelSheetHelper
{
    public static function load(string $path)
    {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);

        return $reader->load($path);
    }

    public static function sheetNames(string $path): array
    {
        $ss = self::load($path);
        $names = [];
        foreach ($ss->getWorksheetIterator() as $ws) {
            $names[] = $ws->getTitle();
        }
        $ss->disconnectWorksheets();

        return $names;
    }

    public static function normalizeHeader(?string $value): string
    {
        $v = strtoupper(trim(preg_replace('/\s+/', ' ', (string) $value)));
        $v = str_replace(['?', '.', '/', '\\', '-', '_', '(', ')', '#'], ' ', $v);
        $v = preg_replace('/\s+/', ' ', $v);

        return trim($v);
    }

    /**
     * @param  array<int, string>  $requiredAny
     * @return array{row:int, map:array<string,int>}|null
     */
    public static function findHeaderMap(Worksheet $ws, array $requiredAny, int $scanRows = 15, int $maxCols = 40): ?array
    {
        $highestCol = min($maxCols, Coordinate::columnIndexFromString($ws->getHighestDataColumn() ?: 'A'));

        for ($r = 1; $r <= $scanRows; $r++) {
            $map = [];
            for ($c = 1; $c <= $highestCol; $c++) {
                $raw = $ws->getCellByColumnAndRow($c, $r)->getValue();
                if ($raw === null || $raw === '') {
                    continue;
                }
                $key = self::normalizeHeader(is_string($raw) ? $raw : (string) $raw);
                if ($key !== '' && ! isset($map[$key])) {
                    $map[$key] = $c;
                }
            }
            foreach ($requiredAny as $need) {
                if (self::mapHas($map, $need)) {
                    return ['row' => $r, 'map' => $map];
                }
            }
        }

        return null;
    }

    public static function mapHas(array $map, string $needle): bool
    {
        $needle = self::normalizeHeader($needle);
        if (isset($map[$needle])) {
            return true;
        }
        foreach ($map as $k => $_) {
            if (str_contains($k, $needle) || str_contains($needle, $k)) {
                return true;
            }
        }

        return false;
    }

    public static function col(array $map, array $aliases): ?int
    {
        foreach ($aliases as $alias) {
            $n = self::normalizeHeader($alias);
            if (isset($map[$n])) {
                return $map[$n];
            }
            foreach ($map as $k => $c) {
                if ($k === $n || str_starts_with($k, $n) || str_contains($k, $n)) {
                    return $c;
                }
            }
        }

        return null;
    }

    public static function cell(Worksheet $ws, ?int $col, int $row)
    {
        if (! $col) {
            return null;
        }

        return $ws->getCellByColumnAndRow($col, $row)->getValue();
    }

    public static function cellString(Worksheet $ws, ?int $col, int $row): ?string
    {
        $v = self::cell($ws, $col, $row);
        if ($v === null || $v === '') {
            return null;
        }
        if (is_float($v) || is_int($v)) {
            if (is_float($v) && floor($v) == $v) {
                return (string) (int) $v;
            }

            return (string) $v;
        }
        $s = trim((string) $v);

        return $s === '' ? null : $s;
    }

    public static function toDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        try {
            if (is_numeric($value)) {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            }
            $s = trim((string) $value);
            if ($s === '') {
                return null;
            }
            $ts = strtotime($s);

            return $ts === false ? null : date('Y-m-d', $ts);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function toFloat($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_numeric($value)) {
            return (float) $value;
        }
        $s = preg_replace('/[^0-9.\-]/', '', (string) $value);
        if ($s === '' || $s === '-' || $s === '.') {
            return null;
        }

        return (float) $s;
    }

    public static function toBool($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        if (is_numeric($value)) {
            return (float) $value != 0.0;
        }
        $s = strtoupper(trim((string) $value));

        return in_array($s, ['Y', 'YES', 'TRUE', '1', 'X', 'DONE', 'CHECKED'], true);
    }

    public static function normalizePlate(?string $plate): string
    {
        return strtoupper(preg_replace('/\s+/', '', (string) $plate));
    }

    public static function countNonEmptyRows(Worksheet $ws, int $headerRow, ?int $keyCol, int $maxScan = 5000): int
    {
        if (! $keyCol) {
            return max(0, (int) $ws->getHighestDataRow() - $headerRow);
        }
        $highest = min((int) $ws->getHighestDataRow(), $headerRow + $maxScan);
        $n = 0;
        for ($r = $headerRow + 1; $r <= $highest; $r++) {
            $v = $ws->getCellByColumnAndRow($keyCol, $r)->getValue();
            if ($v !== null && trim((string) $v) !== '') {
                $n++;
            }
        }

        return $n;
    }
}
