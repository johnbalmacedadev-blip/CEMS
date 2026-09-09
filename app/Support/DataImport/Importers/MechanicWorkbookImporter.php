<?php

namespace App\Support\DataImport\Importers;

use App\Support\DataImport\AbstractJsonWorkbookImporter;
use App\Support\DataImport\ExcelSheetHelper;

class MechanicWorkbookImporter extends AbstractJsonWorkbookImporter
{
    public function key(): string
    {
        return 'mechanic';
    }

    public function label(): string
    {
        return 'Mechanic Tracker';
    }

    public function page(): array
    {
        return ['label' => 'Mechanic Tracker', 'route' => 'mechanic-tracker.index'];
    }

    public function tables(): array
    {
        return ['mechanic_jobs'];
    }

    protected function artisanCommand(): string
    {
        return 'import:mechanic-tracker';
    }

    protected function artisanParams(string $jsonPath, array $sheetNames): array
    {
        return ['--file' => $jsonPath];
    }

    public function matches(string $originalName, array $sheetNames): bool
    {
        return str_contains(strtoupper($originalName), 'MECHANIC');
    }

    public function listSheets(string $path): array
    {
        $ss = ExcelSheetHelper::load($path);
        $out = [];
        foreach ($ss->getWorksheetIterator() as $ws) {
            $name = $ws->getTitle();
            $internal = ExcelSheetHelper::findHeaderMap($ws, ['MECHANIC', 'PLATE NUMBER', 'PLATE']);
            $external = ExcelSheetHelper::findHeaderMap($ws, ['CATEGORY', 'CATEOGRY', 'ITEM']);
            $supported = false;
            $rows = 0;
            $note = 'Not an internal/external job tab.';

            if ($internal && ExcelSheetHelper::col($internal['map'], ['DATE']) !== null
                && ExcelSheetHelper::col($internal['map'], ['MECHANIC']) !== null
                && ExcelSheetHelper::col($internal['map'], ['PLATE NUMBER', 'PLATE']) !== null) {
                $supported = true;
                $note = 'Internal jobs';
                $rows = ExcelSheetHelper::countNonEmptyRows(
                    $ws,
                    $internal['row'],
                    ExcelSheetHelper::col($internal['map'], ['PLATE NUMBER', 'PLATE', 'DATE'])
                );
            } elseif ($external && (
                ExcelSheetHelper::col($external['map'], ['CATEGORY', 'CATEOGRY']) !== null
                && ExcelSheetHelper::col($external['map'], ['ITEM']) !== null
                && ExcelSheetHelper::col($external['map'], ['COST']) !== null
            )) {
                $supported = true;
                $note = 'External jobs';
                $rows = ExcelSheetHelper::countNonEmptyRows(
                    $ws,
                    $external['row'],
                    ExcelSheetHelper::col($external['map'], ['ITEM', 'COST'])
                );
            }

            $out[] = [
                'name' => $name,
                'supported' => $supported,
                'excel_rows' => $rows,
                'tables' => $this->tables(),
                'note' => $supported ? $note : 'Not an internal/external job tab.',
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

            $internal = ExcelSheetHelper::findHeaderMap($ws, ['MECHANIC']);
            $isInternal = $internal
                && ExcelSheetHelper::col($internal['map'], ['DATE']) !== null
                && ExcelSheetHelper::col($internal['map'], ['MECHANIC']) !== null
                && ExcelSheetHelper::col($internal['map'], ['PLATE NUMBER', 'PLATE']) !== null;

            if ($isInternal) {
                $rows = array_merge($rows, $this->parseInternal($ws, $name, $internal));
                continue;
            }

            $external = ExcelSheetHelper::findHeaderMap($ws, ['CATEGORY', 'CATEOGRY', 'ITEM']);
            $isExternal = $external
                && ExcelSheetHelper::col($external['map'], ['CATEGORY', 'CATEOGRY']) !== null
                && ExcelSheetHelper::col($external['map'], ['ITEM']) !== null
                && ExcelSheetHelper::col($external['map'], ['COST']) !== null;

            if ($isExternal) {
                $rows = array_merge($rows, $this->parseExternal($ws, $name, $external));
            }
        }
        $ss->disconnectWorksheets();

        return $rows;
    }

    private function parseInternal($ws, string $sheet, array $header): array
    {
        $map = $header['map'];
        $cDate = ExcelSheetHelper::col($map, ['DATE']);
        $cMech = ExcelSheetHelper::col($map, ['MECHANIC']);
        $cYearModel = ExcelSheetHelper::col($map, ['YEAR/MODEL', 'YEAR MODEL', 'YEAR']);
        $cPlate = ExcelSheetHelper::col($map, ['PLATE NUMBER', 'PLATE']);
        $cEndorse = ExcelSheetHelper::col($map, ['ENDORSE']);
        $cDesc = ExcelSheetHelper::col($map, ['DESCRIPTION']);
        $cLabor = ExcelSheetHelper::col($map, ['LABOR']);
        $cParts = ExcelSheetHelper::col($map, ['PARTS']);
        $cCost = ExcelSheetHelper::col($map, ['PARTS COST', 'COST']);
        $cStatus = ExcelSheetHelper::col($map, ['STATUS']);

        $out = [];
        $highest = (int) $ws->getHighestDataRow();
        for ($r = $header['row'] + 1; $r <= $highest; $r++) {
            $date = ExcelSheetHelper::toDate(ExcelSheetHelper::cell($ws, $cDate, $r));
            $plate = ExcelSheetHelper::normalizePlate(ExcelSheetHelper::cellString($ws, $cPlate, $r));
            $mech = ExcelSheetHelper::cellString($ws, $cMech, $r);
            if (! $date && $plate === '' && ! $mech) {
                continue;
            }
            if (! $date) {
                continue;
            }
            $out[] = [
                'sheet' => $sheet,
                'row' => $r,
                'job_type' => 'Internal',
                'job_date' => $date,
                'mechanic' => $mech,
                'year_model' => ExcelSheetHelper::cellString($ws, $cYearModel, $r),
                'plate_number' => $plate !== '' ? $plate : null,
                'endorse' => ExcelSheetHelper::cellString($ws, $cEndorse, $r),
                'description' => ExcelSheetHelper::cellString($ws, $cDesc, $r),
                'labor' => ExcelSheetHelper::cellString($ws, $cLabor, $r),
                'parts' => ExcelSheetHelper::cellString($ws, $cParts, $r),
                'parts_cost' => ExcelSheetHelper::toFloat(ExcelSheetHelper::cell($ws, $cCost, $r)),
                'status' => ExcelSheetHelper::cellString($ws, $cStatus, $r),
                'category' => null,
                'unit_label' => null,
            ];
        }

        return $out;
    }

    private function parseExternal($ws, string $sheet, array $header): array
    {
        $map = $header['map'];
        $cDate = ExcelSheetHelper::col($map, ['DATE']);
        $cCat = ExcelSheetHelper::col($map, ['CATEGORY', 'CATEOGRY']);
        $cItem = ExcelSheetHelper::col($map, ['ITEM', 'DESCRIPTION']);
        $cCost = ExcelSheetHelper::col($map, ['COST', 'PARTS COST']);
        $cUnit = ExcelSheetHelper::col($map, ['UNIT']);
        $cStatus = ExcelSheetHelper::col($map, ['STATUS']);

        $out = [];
        $highest = (int) $ws->getHighestDataRow();
        for ($r = $header['row'] + 1; $r <= $highest; $r++) {
            $item = ExcelSheetHelper::cellString($ws, $cItem, $r);
            $cost = ExcelSheetHelper::toFloat(ExcelSheetHelper::cell($ws, $cCost, $r));
            $unit = ExcelSheetHelper::cellString($ws, $cUnit, $r);
            if (! $item && $cost === null && ! $unit) {
                continue;
            }

            $plate = null;
            if ($unit && preg_match('/([A-Za-z]{1,3}\d{2,4}[A-Za-z0-9]?|\d{2,4}[A-Za-z]{1,3})\b/', $unit, $m)) {
                $plate = ExcelSheetHelper::normalizePlate($m[1]);
            }

            $out[] = [
                'sheet' => $sheet,
                'row' => $r,
                'job_type' => 'External',
                'job_date' => ExcelSheetHelper::toDate(ExcelSheetHelper::cell($ws, $cDate, $r)),
                'mechanic' => null,
                'year_model' => $unit,
                'plate_number' => $plate,
                'endorse' => null,
                'description' => $item,
                'labor' => null,
                'parts' => null,
                'parts_cost' => $cost,
                'status' => ExcelSheetHelper::cellString($ws, $cStatus, $r) ?? 'Complete',
                'category' => ExcelSheetHelper::cellString($ws, $cCat, $r),
                'unit_label' => $unit,
            ];
        }

        return $out;
    }
}
