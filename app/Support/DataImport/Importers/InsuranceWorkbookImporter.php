<?php

namespace App\Support\DataImport\Importers;

use App\Support\DataImport\AbstractJsonWorkbookImporter;
use App\Support\DataImport\ExcelSheetHelper;

class InsuranceWorkbookImporter extends AbstractJsonWorkbookImporter
{
    public function key(): string
    {
        return 'insurance';
    }

    public function label(): string
    {
        return 'Insurance Tracker';
    }

    public function page(): array
    {
        return ['label' => 'Insurance Tracker', 'route' => 'insurance-tracker.index'];
    }

    public function tables(): array
    {
        return ['insurance_tracker'];
    }

    protected function artisanCommand(): string
    {
        return 'import:insurance-tracker';
    }

    protected function artisanParams(string $jsonPath, array $sheetNames): array
    {
        return ['--file' => $jsonPath];
    }

    public function matches(string $originalName, array $sheetNames): bool
    {
        $name = strtoupper($originalName);
        if (str_contains($name, 'INSURANCE')) {
            return true;
        }
        foreach ($sheetNames as $s) {
            $u = strtoupper($s);
            if (str_contains($u, 'INSURANCE') && (str_contains($u, 'PLATE') || str_contains($u, 'TRACKER'))) {
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
            $header = ExcelSheetHelper::findHeaderMap($ws, ['PLATE NUMBER', 'PLATE']);
            $hasYear = $header && ExcelSheetHelper::col($header['map'], ['YEAR']) !== null;
            $hasPlate = $header && ExcelSheetHelper::col($header['map'], ['PLATE NUMBER', 'PLATE']) !== null;
            $supported = $hasYear && $hasPlate;
            $rows = $supported
                ? ExcelSheetHelper::countNonEmptyRows(
                    $ws,
                    $header['row'],
                    ExcelSheetHelper::col($header['map'], ['PLATE NUMBER', 'PLATE'])
                )
                : 0;
            $out[] = [
                'name' => $name,
                'supported' => $supported,
                'excel_rows' => $rows,
                'tables' => $this->tables(),
                'note' => $supported ? null : 'Needs PLATE NUMBER and YEAR headers.',
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
            $header = ExcelSheetHelper::findHeaderMap($ws, ['PLATE NUMBER', 'PLATE']);
            if (! $header) {
                continue;
            }
            $map = $header['map'];
            $cYear = ExcelSheetHelper::col($map, ['YEAR']);
            $cMake = ExcelSheetHelper::col($map, ['MAKE']);
            $cModel = ExcelSheetHelper::col($map, ['MODEL']);
            $cPlate = ExcelSheetHelper::col($map, ['PLATE NUMBER', 'PLATE']);
            $cSaleDate = ExcelSheetHelper::col($map, ['FORMATTED SALE DATE', 'SALE DATE', 'RESERVATION DATE']);
            $cSales = ExcelSheetHelper::col($map, ['SALES PERSON (RESERVED)', 'SALES PERSON', 'SALES']);
            $cTxn = ExcelSheetHelper::col($map, ['CASH/ FINANCING', 'CASH/FINANCING', 'CASH FINANCING', 'TRANSACTION']);
            $cSource = ExcelSheetHelper::col($map, ['WITH/WITHOUT INSURANCE', 'WITH WITHOUT INSURANCE', 'INSURANCE']);
            $cAmount = ExcelSheetHelper::col($map, ['INSURANCE AMOUNT', 'AMOUNT']);
            $cRelease = ExcelSheetHelper::col($map, ['FORMATTED RELEASE DATE', 'RELEASE DATE']);
            $cShowroom = ExcelSheetHelper::col($map, ['SHOWROOM']);

            if (! $cPlate || ! $cYear) {
                continue;
            }

            $highest = (int) $ws->getHighestDataRow();
            for ($r = $header['row'] + 1; $r <= $highest; $r++) {
                $plate = ExcelSheetHelper::normalizePlate(ExcelSheetHelper::cellString($ws, $cPlate, $r));
                if ($plate === '') {
                    continue;
                }
                $rows[] = [
                    'row' => $r,
                    'year' => ExcelSheetHelper::cellString($ws, $cYear, $r),
                    'make' => ExcelSheetHelper::cellString($ws, $cMake, $r),
                    'model' => ExcelSheetHelper::cellString($ws, $cModel, $r),
                    'number' => $plate,
                    'reservation_date' => ExcelSheetHelper::toDate(ExcelSheetHelper::cell($ws, $cSaleDate, $r)),
                    'sales' => ExcelSheetHelper::cellString($ws, $cSales, $r),
                    'transaction' => ExcelSheetHelper::cellString($ws, $cTxn, $r),
                    'amount' => ExcelSheetHelper::toFloat(ExcelSheetHelper::cell($ws, $cAmount, $r)),
                    'release_date' => ExcelSheetHelper::toDate(ExcelSheetHelper::cell($ws, $cRelease, $r)),
                    'source' => ExcelSheetHelper::cellString($ws, $cSource, $r),
                    'showroom' => ExcelSheetHelper::cellString($ws, $cShowroom, $r),
                ];
            }
        }
        $ss->disconnectWorksheets();

        return $rows;
    }
}
