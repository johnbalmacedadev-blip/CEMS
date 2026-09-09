<?php

namespace App\Support\DataImport\Importers;

use App\Support\DataImport\AbstractJsonWorkbookImporter;
use App\Support\DataImport\ExcelSheetHelper;

class RecommendationWorkbookImporter extends AbstractJsonWorkbookImporter
{
    public function key(): string
    {
        return 'recommendation';
    }

    public function label(): string
    {
        return 'Recommendation Tracker';
    }

    public function page(): array
    {
        return ['label' => 'Recommendation Tracker', 'route' => 'recommendation-tracker.index'];
    }

    public function tables(): array
    {
        return ['recommendation_trackers'];
    }

    protected function artisanCommand(): string
    {
        return 'import:recommendation-tracker';
    }

    protected function artisanParams(string $jsonPath, array $sheetNames): array
    {
        return ['--file' => $jsonPath];
    }

    public function matches(string $originalName, array $sheetNames): bool
    {
        $name = strtoupper($originalName);

        return str_contains($name, 'RECOMENDATION') || str_contains($name, 'RECOMMENDATION');
    }

    public function listSheets(string $path): array
    {
        $ss = ExcelSheetHelper::load($path);
        $out = [];
        foreach ($ss->getWorksheetIterator() as $ws) {
            $name = $ws->getTitle();
            $header = ExcelSheetHelper::findHeaderMap($ws, ['PLATE NUMBER', 'PLATE'], 10, 50);
            $supported = $header
                && ExcelSheetHelper::col($header['map'], ['YEAR']) !== null
                && ExcelSheetHelper::col($header['map'], ['MAKE']) !== null
                && ExcelSheetHelper::col($header['map'], ['MODEL']) !== null
                && ExcelSheetHelper::col($header['map'], ['PLATE NUMBER', 'PLATE']) !== null;
            $rows = $supported
                ? ExcelSheetHelper::countNonEmptyRows(
                    $ws,
                    $header['row'],
                    ExcelSheetHelper::col($header['map'], ['PLATE NUMBER', 'PLATE'])
                )
                : 0;
            $out[] = [
                'name' => $name,
                'supported' => (bool) $supported,
                'excel_rows' => $rows,
                'tables' => $this->tables(),
                'note' => $supported ? null : 'Needs YEAR / MAKE / MODEL / PLATE headers.',
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
            $header = ExcelSheetHelper::findHeaderMap($ws, ['PLATE NUMBER', 'PLATE'], 10, 50);
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
            $cYear = ExcelSheetHelper::col($map, ['YEAR']);
            $cMake = ExcelSheetHelper::col($map, ['MAKE']);
            $cModel = ExcelSheetHelper::col($map, ['MODEL']);
            $cVariant = ExcelSheetHelper::col($map, ['VARIANT']);
            $cTrans = ExcelSheetHelper::col($map, ['TRANSMISSION']);
            $cFuel = ExcelSheetHelper::col($map, ['FUEL TYPE', 'FUEL']);
            $cKm = ExcelSheetHelper::col($map, ['KILOMETERS', 'KM', 'MILEAGE']);
            $cPlate = ExcelSheetHelper::col($map, ['PLATE NUMBER', 'PLATE']);
            $cColor = ExcelSheetHelper::col($map, ['COLOR', 'COLOUR']);
            $cTools = ExcelSheetHelper::col($map, ['WITH TOOLS']);
            $cMatt = ExcelSheetHelper::col($map, ['WITH MATTING']);
            $cSpare = ExcelSheetHelper::col($map, ['WITH SPARE TIRE']);
            $cPrice = ExcelSheetHelper::col($map, ['PURCHASE PRICE']);
            $cFrom = ExcelSheetHelper::col($map, ['PURCHASED FROM']);
            $cPurchDate = ExcelSheetHelper::col($map, ['FORMATTED PURCHASE DATE', 'PURCHASE DATE']);
            $cSpareKey = ExcelSheetHelper::col($map, ['SPARE KEY']);
            $cFinal = ExcelSheetHelper::col($map, ['FINAL STATUS']);

            // Fuzzy via ExcelSheetHelper::col for RECCOMENDATION typos
            $cPaintRec = ExcelSheetHelper::col($map, ['PAINT RECCOMENDATION', 'PAINT RECOMMENDATION', 'PAINT REC']);
            $cPaintDone = ExcelSheetHelper::col($map, ['PAINT COMPLETION']);
            $cMechRec = ExcelSheetHelper::col($map, ['MECHANICAL RECCOMENDATION', 'MECHANICAL RECOMMENDATION']);
            $cMechDone = ExcelSheetHelper::col($map, ['MECHANICAL COMPLETION']);
            $cElecRec = ExcelSheetHelper::col($map, ['ELECTRICAL RECCOMENDATION', 'ELECTRICAL RECOMMENDATION']);
            $cElecDone = ExcelSheetHelper::col($map, ['ELECTRICAL COMPLETION']);
            $cEcuRec = ExcelSheetHelper::col($map, ['ECU/CLUSTER RECCOMENDATION', 'ECU CLUSTER RECCOMENDATION', 'ECU/CLUSTER RECOMMENDATION', 'ECU']);
            $cEcuDone = ExcelSheetHelper::col($map, ['ECU/CLUSTER COMPLETION', 'ECU CLUSTER COMPLETION']);
            $cAcRec = ExcelSheetHelper::col($map, ['AIRCON RECCOMENDATION', 'AIRCON RECOMMENDATION']);
            $cAcDone = ExcelSheetHelper::col($map, ['AIRCON COMPLETION']);
            $cIntRec = ExcelSheetHelper::col($map, ['INTERIOR RECCOMENDATION', 'INTERIOR RECOMMENDATION']);
            $cIntDone = ExcelSheetHelper::col($map, ['INTERIOR COMPLETION']);
            $cTireRec = ExcelSheetHelper::col($map, ['TIRES RECCOMENDATION', 'TIRES RECOMMENDATION', 'TIRE REC']);
            $cTireDone = ExcelSheetHelper::col($map, ['TIRES COMPLETION', 'TIRE COMPLETION']);
            $cBattRec = ExcelSheetHelper::col($map, ['BATTERY RECCOMENDATION', 'BATTERY RECOMMENDATION']);
            $cBattDone = ExcelSheetHelper::col($map, ['BATTERY COMPLETION']);
            $cMiscRec = ExcelSheetHelper::col($map, ['MISC RECCOMENDATION', 'MISC RECOMMENDATION']);
            $cMiscDone = ExcelSheetHelper::col($map, ['MISC COMPLETION']);

            if (! $cPlate) {
                continue;
            }

            $highest = (int) $ws->getHighestDataRow();
            for ($r = $header['row'] + 1; $r <= $highest; $r++) {
                $plate = ExcelSheetHelper::normalizePlate(ExcelSheetHelper::cellString($ws, $cPlate, $r));
                $make = ExcelSheetHelper::cellString($ws, $cMake, $r);
                $model = ExcelSheetHelper::cellString($ws, $cModel, $r);
                if ($plate === '' && ! $make && ! $model) {
                    continue;
                }

                $rowNum = ExcelSheetHelper::cell($ws, $cNum, $r);
                if (is_numeric($rowNum)) {
                    $rowNum = (int) $rowNum;
                } else {
                    $rowNum = $r;
                }

                $rows[] = [
                    'row_num' => $rowNum,
                    'year' => ExcelSheetHelper::cellString($ws, $cYear, $r),
                    'make' => $make,
                    'model' => $model,
                    'variant' => ExcelSheetHelper::cellString($ws, $cVariant, $r),
                    'transmission' => ExcelSheetHelper::cellString($ws, $cTrans, $r),
                    'fuel_type' => ExcelSheetHelper::cellString($ws, $cFuel, $r),
                    'kilometers' => ExcelSheetHelper::toFloat(ExcelSheetHelper::cell($ws, $cKm, $r)),
                    'plate_number' => $plate !== '' ? $plate : null,
                    'color' => ExcelSheetHelper::cellString($ws, $cColor, $r),
                    'with_tools' => ExcelSheetHelper::cellString($ws, $cTools, $r),
                    'with_matting' => ExcelSheetHelper::cellString($ws, $cMatt, $r),
                    'with_spare_tire' => ExcelSheetHelper::cellString($ws, $cSpare, $r),
                    'purchase_price' => ExcelSheetHelper::toFloat(ExcelSheetHelper::cell($ws, $cPrice, $r)),
                    'purchased_from' => ExcelSheetHelper::cellString($ws, $cFrom, $r),
                    'purchase_date' => ExcelSheetHelper::toDate(ExcelSheetHelper::cell($ws, $cPurchDate, $r)),
                    'spare_key' => ExcelSheetHelper::cellString($ws, $cSpareKey, $r),
                    'final_status' => ExcelSheetHelper::cellString($ws, $cFinal, $r),
                    'paint_recommendation' => ExcelSheetHelper::cellString($ws, $cPaintRec, $r),
                    'paint_completion' => ExcelSheetHelper::cellString($ws, $cPaintDone, $r),
                    'mechanical_recommendation' => ExcelSheetHelper::cellString($ws, $cMechRec, $r),
                    'mechanical_completion' => ExcelSheetHelper::cellString($ws, $cMechDone, $r),
                    'electrical_recommendation' => ExcelSheetHelper::cellString($ws, $cElecRec, $r),
                    'electrical_completion' => ExcelSheetHelper::cellString($ws, $cElecDone, $r),
                    'ecu_cluster_recommendation' => ExcelSheetHelper::cellString($ws, $cEcuRec, $r),
                    'ecu_cluster_completion' => ExcelSheetHelper::cellString($ws, $cEcuDone, $r),
                    'aircon_recommendation' => ExcelSheetHelper::cellString($ws, $cAcRec, $r),
                    'aircon_completion' => ExcelSheetHelper::cellString($ws, $cAcDone, $r),
                    'interior_recommendation' => ExcelSheetHelper::cellString($ws, $cIntRec, $r),
                    'interior_completion' => ExcelSheetHelper::cellString($ws, $cIntDone, $r),
                    'tires_recommendation' => ExcelSheetHelper::cellString($ws, $cTireRec, $r),
                    'tires_completion' => ExcelSheetHelper::cellString($ws, $cTireDone, $r),
                    'battery_recommendation' => ExcelSheetHelper::cellString($ws, $cBattRec, $r),
                    'battery_completion' => ExcelSheetHelper::cellString($ws, $cBattDone, $r),
                    'misc_recommendation' => ExcelSheetHelper::cellString($ws, $cMiscRec, $r),
                    'misc_completion' => ExcelSheetHelper::cellString($ws, $cMiscDone, $r),
                ];
            }
        }
        $ss->disconnectWorksheets();

        return $rows;
    }
}
