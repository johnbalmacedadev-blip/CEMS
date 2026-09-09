<?php

namespace App\Support\DataImport\Importers;

use App\Support\DataImport\AbstractJsonWorkbookImporter;
use App\Support\DataImport\ExcelSheetHelper;

class GasPoWorkbookImporter extends AbstractJsonWorkbookImporter
{
    public function key(): string
    {
        return 'gas_po';
    }

    public function label(): string
    {
        return 'Gas Expense PO Tracker';
    }

    public function page(): array
    {
        return ['label' => 'Gas Expense PO Tracker', 'route' => 'gas-expense-po-tracker.index'];
    }

    public function tables(): array
    {
        return ['gas_expenses'];
    }

    protected function artisanCommand(): string
    {
        return 'import:gas-po-expenses';
    }

    protected function artisanParams(string $jsonPath, array $sheetNames): array
    {
        return ['--file' => $jsonPath];
    }

    public function matches(string $originalName, array $sheetNames): bool
    {
        $name = strtoupper($originalName);
        if (! str_contains($name, 'GAS')) {
            return false;
        }

        return str_contains($name, 'P.O')
            || str_contains($name, 'PO')
            || str_contains($name, 'EXPENSE');
    }

    public function listSheets(string $path): array
    {
        $ss = ExcelSheetHelper::load($path);
        $out = [];
        foreach ($ss->getWorksheetIterator() as $ws) {
            $name = $ws->getTitle();
            $header = ExcelSheetHelper::findHeaderMap($ws, ['PLATE NUMBER', 'PLATE', 'GAS AMOUNT'], 10, 30);
            $supported = $header
                && ExcelSheetHelper::col($header['map'], ['DATE']) !== null
                && ExcelSheetHelper::col($header['map'], ['DRIVER']) !== null
                && ExcelSheetHelper::col($header['map'], ['PLATE NUMBER', 'PLATE']) !== null
                && ExcelSheetHelper::col($header['map'], ['GAS AMOUNT', 'AMOUNT']) !== null;
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
                'note' => $supported ? null : 'Needs DATE / DRIVER / PLATE / GAS AMOUNT headers.',
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
            $header = ExcelSheetHelper::findHeaderMap($ws, ['PLATE NUMBER', 'GAS AMOUNT', 'DRIVER'], 10, 30);
            if (! $header) {
                continue;
            }
            $map = $header['map'];
            $cDate = ExcelSheetHelper::col($map, ['DATE']);
            $cPo = ExcelSheetHelper::col($map, ['P O NUMBER', 'P.O NUMBER', 'PO NUMBER', 'PO']);
            $cDriver = ExcelSheetHelper::col($map, ['DRIVER']);
            $cModel = ExcelSheetHelper::col($map, ['MODEL']);
            $cPlate = ExcelSheetHelper::col($map, ['PLATE NUMBER', 'PLATE']);
            $cAmount = ExcelSheetHelper::col($map, ['GAS AMOUNT', 'AMOUNT']);
            $cSent = ExcelSheetHelper::col($map, ['WAS EXPENSE SENT', 'EXPENSE SENT', 'SENT BY']);
            $cPhotoGc = ExcelSheetHelper::col($map, ['PHOTO/VIDEO IN GROUPCHAT', 'GROUPCHAT']);
            $cPoSlip = ExcelSheetHelper::col($map, ['PHOTO OF PO SLIP', 'PO SLIP']);
            $cGaugeBefore = ExcelSheetHelper::col($map, ['FUEL GAUGE PRIOR', 'GAUGE PRIOR', 'BEFORE']);
            $cGaugeAfter = ExcelSheetHelper::col($map, ['FUEL GAUGE AFTER', 'GAUGE AFTER']);
            $cPlateGasBoy = ExcelSheetHelper::col($map, ['LICENSE PLATE AND GAS BOY', 'GAS BOY']);
            $cReceipt = ExcelSheetHelper::col($map, ['RECEIPT NEXT TO GAS PUMP', 'RECEIPT']);
            $cChecked = ExcelSheetHelper::col($map, ['CHECKED IN SHELL SOA', 'CHECKED BY', 'CHECKED']);

            if (! $cDate || ! $cPlate || ! $cAmount) {
                continue;
            }

            $highest = (int) $ws->getHighestDataRow();
            for ($r = $header['row'] + 1; $r <= $highest; $r++) {
                $plate = ExcelSheetHelper::normalizePlate(ExcelSheetHelper::cellString($ws, $cPlate, $r));
                $date = ExcelSheetHelper::toDate(ExcelSheetHelper::cell($ws, $cDate, $r));
                $amount = ExcelSheetHelper::toFloat(ExcelSheetHelper::cell($ws, $cAmount, $r));
                if ($plate === '' || ! $date || $amount === null || $amount <= 0) {
                    continue;
                }

                $checkedRaw = ExcelSheetHelper::cellString($ws, $cChecked, $r);
                // Prefer string label when present; otherwise bool via toBool
                $checkedBy = $checkedRaw;
                if ($checkedBy === null) {
                    $checkedBy = ExcelSheetHelper::toBool(ExcelSheetHelper::cell($ws, $cChecked, $r)) ? 'Checked' : 'Unchecked';
                }

                $rows[] = [
                    'sheet' => $name,
                    'row' => $r,
                    'date' => $date,
                    'po_number' => ExcelSheetHelper::cellString($ws, $cPo, $r),
                    'driver' => ExcelSheetHelper::cellString($ws, $cDriver, $r),
                    'model' => ExcelSheetHelper::cellString($ws, $cModel, $r),
                    'plate_number' => $plate,
                    'gas_amount' => $amount,
                    'expense_sent_by' => ExcelSheetHelper::cellString($ws, $cSent, $r),
                    'has_photo_video_in_groupchat' => ExcelSheetHelper::toBool(ExcelSheetHelper::cell($ws, $cPhotoGc, $r)),
                    'photo_po_slip' => ExcelSheetHelper::toBool(ExcelSheetHelper::cell($ws, $cPoSlip, $r)),
                    'photo_fuel_gauge_before' => ExcelSheetHelper::toBool(ExcelSheetHelper::cell($ws, $cGaugeBefore, $r)),
                    'photo_fuel_gauge_after' => ExcelSheetHelper::toBool(ExcelSheetHelper::cell($ws, $cGaugeAfter, $r)),
                    'photo_car_license_plate_gas_boy' => ExcelSheetHelper::toBool(ExcelSheetHelper::cell($ws, $cPlateGasBoy, $r)),
                    'photo_receipt_next_to_gas_pump' => ExcelSheetHelper::toBool(ExcelSheetHelper::cell($ws, $cReceipt, $r)),
                    'checked_by' => $checkedBy,
                ];
            }
        }
        $ss->disconnectWorksheets();

        return $rows;
    }
}
