<?php

namespace App\Support\DataImport\Importers;

use App\Support\DataImport\AbstractJsonWorkbookImporter;
use App\Support\DataImport\ExcelSheetHelper;

class TransferOrcrWorkbookImporter extends AbstractJsonWorkbookImporter
{
    public function key(): string
    {
        return 'transfer_orcr';
    }

    public function label(): string
    {
        return 'Transfer OR/CR';
    }

    public function page(): array
    {
        return ['label' => 'Transfer OR/CR', 'route' => 'transfer-orcr.index'];
    }

    public function tables(): array
    {
        return ['transfer_orcr', 'transfer_orcr_other_transactions'];
    }

    protected function artisanCommand(): string
    {
        return 'import:transfer-orcr';
    }

    protected function artisanParams(string $jsonPath, array $sheetNames): array
    {
        return ['--file' => $jsonPath];
    }

    public function matches(string $originalName, array $sheetNames): bool
    {
        $name = strtoupper($originalName);

        return str_contains($name, 'TRANSFER');
    }

    public function listSheets(string $path): array
    {
        $ss = ExcelSheetHelper::load($path);
        $out = [];
        foreach ($ss->getWorksheetIterator() as $ws) {
            $name = $ws->getTitle();
            $supported = $this->isSupportedSheet($name);
            $header = $supported
                ? ExcelSheetHelper::findHeaderMap($ws, ['PLATE', 'PLATE NO', 'PLATE NUMBER'], 10, 40)
                : null;
            if ($supported && ! $header) {
                $supported = false;
            }
            $rows = ($supported && $header)
                ? ExcelSheetHelper::countNonEmptyRows(
                    $ws,
                    $header['row'],
                    ExcelSheetHelper::col($header['map'], ['PLATE', 'PLATE NO', 'PLATE NUMBER'])
                )
                : 0;
            $out[] = [
                'name' => $name,
                'supported' => $supported,
                'excel_rows' => $rows,
                'tables' => $this->tables(),
                'note' => $supported
                    ? 'Branch: '.($this->branchFromSheet($name) ?? 'n/a')
                    : 'TRANSFER tabs only (excl. REGISTRATION / SUMMARY).',
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
            if (! $ws || ! $this->isSupportedSheet($name)) {
                continue;
            }
            $header = ExcelSheetHelper::findHeaderMap($ws, ['PLATE', 'PLATE NO', 'PLATE NUMBER'], 10, 40);
            if (! $header) {
                continue;
            }
            $map = $header['map'];
            $cDate = ExcelSheetHelper::col($map, ['DATE']);
            $cPlate = ExcelSheetHelper::col($map, ['PLATE NO', 'PLATE NUMBER', 'PLATE']);
            $cMake = ExcelSheetHelper::col($map, ['MAKE']);
            $cSeries = ExcelSheetHelper::col($map, ['SERIES', 'MODEL']);
            $cYear = ExcelSheetHelper::col($map, ['YEAR MODEL', 'YEAR']);
            $cTxn = ExcelSheetHelper::col($map, ['TRANSACTION TYPE', 'TRANSACTION']);
            $cRemark = ExcelSheetHelper::col($map, ['REMARK']);
            $cLto = ExcelSheetHelper::col($map, ['LTO FILE/NO', 'LTO FILE NO', 'LTO']);
            $cTransferSop = ExcelSheetHelper::col($map, ['TRANSFER SOP']);
            $cTransferOr = ExcelSheetHelper::col($map, ['TRANSFER OR']);
            $cOthers = ExcelSheetHelper::col($map, ['OTHERS']);
            $cOthersNote = ExcelSheetHelper::col($map, ['OTHERS NOTE', 'OTHERS REMARK']);
            $cPnp = ExcelSheetHelper::col($map, ['PNP CLEARANCE', 'PNP']);
            $cConfirm = ExcelSheetHelper::col($map, ['CONFIRMATION']);
            $cNotary = ExcelSheetHelper::col($map, ['NOTARY']);
            $cRd = ExcelSheetHelper::col($map, ['RD']);
            $cRdSop = ExcelSheetHelper::col($map, ['RD SOP']);
            $cRdOr = ExcelSheetHelper::col($map, ['RD OR']);
            $cRenewalSop = ExcelSheetHelper::col($map, ['RENEWAL SOP']);
            $cRenewalOr = ExcelSheetHelper::col($map, ['RENEWAL REG OR', 'RENEWAL REG. OR', 'RENEWAL OR']);
            $cSmoke = ExcelSheetHelper::col($map, ['SMOKE NA', 'SMOKE']);
            $cRemarks = ExcelSheetHelper::col($map, ['REMARKS']);
            $cStatus = ExcelSheetHelper::col($map, ['STATUS']);
            $cRelease = ExcelSheetHelper::col($map, ['RELEASE DATE']);

            if (! $cPlate) {
                continue;
            }

            $branch = $this->branchFromSheet($name);
            $highest = (int) $ws->getHighestDataRow();
            for ($r = $header['row'] + 1; $r <= $highest; $r++) {
                $plate = ExcelSheetHelper::normalizePlate(ExcelSheetHelper::cellString($ws, $cPlate, $r));
                $date = ExcelSheetHelper::toDate(ExcelSheetHelper::cell($ws, $cDate, $r));
                if ($plate === '' || ! $date) {
                    continue;
                }

                $othersRaw = ExcelSheetHelper::cell($ws, $cOthers, $r);
                $others = null;
                $othersNote = ExcelSheetHelper::cellString($ws, $cOthersNote, $r);
                if (is_numeric($othersRaw)) {
                    $others = (float) $othersRaw;
                } elseif (is_string($othersRaw) && trim($othersRaw) !== '') {
                    $asFloat = ExcelSheetHelper::toFloat($othersRaw);
                    if ($asFloat !== null && preg_match('/^[\d.,\s₱P]+$/u', trim($othersRaw))) {
                        $others = $asFloat;
                    } else {
                        $othersNote = $othersNote ?: trim($othersRaw);
                    }
                }

                $yearRaw = ExcelSheetHelper::cell($ws, $cYear, $r);
                $year = null;
                if (is_numeric($yearRaw)) {
                    $year = (int) $yearRaw;
                } elseif (is_string($yearRaw) && preg_match('/(20\d{2}|19\d{2})/', $yearRaw, $ym)) {
                    $year = (int) $ym[1];
                }

                $rows[] = [
                    'row' => $r,
                    'sheet' => $name,
                    'branch' => $branch,
                    'date' => $date,
                    'plate_number' => $plate,
                    'make' => ExcelSheetHelper::cellString($ws, $cMake, $r),
                    'series' => ExcelSheetHelper::cellString($ws, $cSeries, $r),
                    'year' => $year,
                    'transaction_type' => ExcelSheetHelper::cellString($ws, $cTxn, $r),
                    'remark' => ExcelSheetHelper::cellString($ws, $cRemark, $r),
                    'lto_file_no' => ExcelSheetHelper::cellString($ws, $cLto, $r),
                    'transfer_sop' => ExcelSheetHelper::toFloat(ExcelSheetHelper::cell($ws, $cTransferSop, $r)),
                    'transfer_or' => ExcelSheetHelper::toFloat(ExcelSheetHelper::cell($ws, $cTransferOr, $r)),
                    'others' => $others,
                    'others_note' => $othersNote,
                    'pnp_clearance' => ExcelSheetHelper::toFloat(ExcelSheetHelper::cell($ws, $cPnp, $r)),
                    'confirmation' => ExcelSheetHelper::toFloat(ExcelSheetHelper::cell($ws, $cConfirm, $r)),
                    'notary' => ExcelSheetHelper::toFloat(ExcelSheetHelper::cell($ws, $cNotary, $r)),
                    'rd' => ExcelSheetHelper::cellString($ws, $cRd, $r),
                    'rd_sop' => ExcelSheetHelper::toFloat(ExcelSheetHelper::cell($ws, $cRdSop, $r)),
                    'rd_or' => ExcelSheetHelper::toFloat(ExcelSheetHelper::cell($ws, $cRdOr, $r)),
                    'renewal_sop' => ExcelSheetHelper::toFloat(ExcelSheetHelper::cell($ws, $cRenewalSop, $r)),
                    'renewal_reg_or' => ExcelSheetHelper::toFloat(ExcelSheetHelper::cell($ws, $cRenewalOr, $r)),
                    'smoke_na' => ExcelSheetHelper::cellString($ws, $cSmoke, $r),
                    'remarks' => ExcelSheetHelper::cellString($ws, $cRemarks, $r),
                    'status' => ExcelSheetHelper::cellString($ws, $cStatus, $r),
                    'release_date' => ExcelSheetHelper::toDate(ExcelSheetHelper::cell($ws, $cRelease, $r)),
                    'other_transactions' => null,
                ];
            }
        }
        $ss->disconnectWorksheets();

        return $rows;
    }

    private function isSupportedSheet(string $name): bool
    {
        $u = strtoupper($name);
        if (str_contains($u, 'REGISTRATION') || str_contains($u, 'SUMMARY')) {
            return false;
        }
        if (str_contains($u, 'CONSOLIDATED DATA FOR PENDING') || str_contains($u, 'DONE TRANSFER')) {
            return true;
        }

        return str_contains($u, 'TRANSFER');
    }

    private function branchFromSheet(string $name): ?string
    {
        $u = strtoupper($name);
        if (str_contains($u, 'FLAGSHIP')) {
            return 'Flagship';
        }
        if (str_contains($u, 'ANNEX')) {
            return 'Annex';
        }

        return null;
    }
}
