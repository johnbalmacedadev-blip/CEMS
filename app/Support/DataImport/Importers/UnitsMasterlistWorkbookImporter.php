<?php

namespace App\Support\DataImport\Importers;

use App\Support\DataImport\AbstractJsonWorkbookImporter;
use App\Support\DataImport\ExcelSheetHelper;

class UnitsMasterlistWorkbookImporter extends AbstractJsonWorkbookImporter
{
    public function key(): string
    {
        return 'units_masterlist';
    }

    public function label(): string
    {
        return 'Units Masterlist';
    }

    public function page(): array
    {
        return ['label' => 'Units Masterlist', 'route' => 'units-masterlist.index'];
    }

    public function tables(): array
    {
        return ['units_masterlist'];
    }

    protected function artisanCommand(): string
    {
        return 'import:units-masterlist';
    }

    protected function artisanParams(string $jsonPath, array $sheetNames): array
    {
        return ['--file' => $jsonPath];
    }

    public function matches(string $originalName, array $sheetNames): bool
    {
        $name = strtoupper($originalName);

        return str_contains($name, 'MASTERLIST') || str_contains($name, 'PRICELIST');
    }

    public function listSheets(string $path): array
    {
        $ss = ExcelSheetHelper::load($path);
        $out = [];
        foreach ($ss->getWorksheetIterator() as $ws) {
            $name = $ws->getTitle();
            $header = ExcelSheetHelper::findHeaderMap($ws, ['MAKE MODEL', 'PLATE'], 10, 30);
            $supported = $header
                && ExcelSheetHelper::col($header['map'], ['MAKE MODEL', 'MAKE']) !== null
                && ExcelSheetHelper::col($header['map'], ['PLATE', 'PLATE NUMBER']) !== null;
            $rows = $supported
                ? ExcelSheetHelper::countNonEmptyRows(
                    $ws,
                    $header['row'],
                    ExcelSheetHelper::col($header['map'], ['PLATE', 'PLATE NUMBER', 'MAKE MODEL'])
                )
                : 0;
            $out[] = [
                'name' => $name,
                'supported' => (bool) $supported,
                'excel_rows' => $rows,
                'tables' => $this->tables(),
                'note' => $supported ? null : 'Needs MAKE MODEL and PLATE headers.',
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
            $header = ExcelSheetHelper::findHeaderMap($ws, ['MAKE MODEL', 'PLATE'], 10, 30);
            if (! $header) {
                continue;
            }
            $map = $header['map'];
            $cNum = ExcelSheetHelper::col($map, ['NO', 'ROW NUM', 'ROW NUMBER']);
            if (! $cNum) {
                $rawFirst = trim((string) ($ws->getCellByColumnAndRow(1, $header['row'])->getValue() ?? ''));
                if ($rawFirst === '#' || strtoupper($rawFirst) === 'NO') {
                    $cNum = 1;
                }
            }
            $cMakeModel = ExcelSheetHelper::col($map, ['MAKE MODEL', 'MAKE/MODEL']);
            $cPlate = ExcelSheetHelper::col($map, ['PLATE', 'PLATE NUMBER']);
            $cVariant = ExcelSheetHelper::col($map, ['VARIANT']);
            $cTrans = ExcelSheetHelper::col($map, ['TRANSMISSION']);
            $cFuel = ExcelSheetHelper::col($map, ['FUEL TYPE', 'FUEL']);
            $cYear = ExcelSheetHelper::col($map, ['YEAR']);
            $cMileage = ExcelSheetHelper::col($map, ['MILEAGE', 'KILOMETERS', 'KM']);
            $cPrice = ExcelSheetHelper::col($map, ['PRICE']);
            $cLowDown = ExcelSheetHelper::col($map, ['LOW DOWN PAYMENT OPTION', 'LOW DOWN']);
            $cLowMonthly = ExcelSheetHelper::col($map, ['LOW MONTHLY OPTION', 'LOW MONTHLY']);

            if (! $cMakeModel) {
                continue;
            }

            $highest = (int) $ws->getHighestDataRow();
            for ($r = $header['row'] + 1; $r <= $highest; $r++) {
                $makeModel = ExcelSheetHelper::cellString($ws, $cMakeModel, $r);
                if (! $makeModel) {
                    continue;
                }

                $rowNum = ExcelSheetHelper::cell($ws, $cNum, $r);
                if (is_numeric($rowNum)) {
                    $rowNum = (int) $rowNum;
                } else {
                    $rowNum = $r - $header['row'];
                }

                $yearRaw = ExcelSheetHelper::cell($ws, $cYear, $r);
                $year = null;
                if (is_numeric($yearRaw)) {
                    $year = (int) $yearRaw;
                } elseif (is_string($yearRaw) && trim($yearRaw) !== '') {
                    $year = trim($yearRaw);
                }

                $mileage = ExcelSheetHelper::toFloat(ExcelSheetHelper::cell($ws, $cMileage, $r));
                $plate = ExcelSheetHelper::normalizePlate(ExcelSheetHelper::cellString($ws, $cPlate, $r));

                $rows[] = [
                    'row_num' => $rowNum,
                    'make_model' => $makeModel,
                    'plate' => $plate !== '' ? $plate : null,
                    'variant' => ExcelSheetHelper::cellString($ws, $cVariant, $r),
                    'transmission' => ExcelSheetHelper::cellString($ws, $cTrans, $r),
                    'fuel_type' => ExcelSheetHelper::cellString($ws, $cFuel, $r),
                    'year' => $year,
                    'mileage' => $mileage !== null ? (int) round($mileage) : null,
                    'price' => ExcelSheetHelper::toFloat(ExcelSheetHelper::cell($ws, $cPrice, $r)),
                    'low_down_payment_option' => ExcelSheetHelper::cellString($ws, $cLowDown, $r),
                    'low_monthly_option' => ExcelSheetHelper::cellString($ws, $cLowMonthly, $r),
                ];
            }
        }
        $ss->disconnectWorksheets();

        return $rows;
    }
}
