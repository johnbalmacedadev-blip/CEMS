<?php

namespace App\Support\DataImport\Importers;

use App\Support\DataImport\AbstractJsonWorkbookImporter;
use App\Support\DataImport\ExcelSheetHelper;

class VideoPostingWorkbookImporter extends AbstractJsonWorkbookImporter
{
    public function key(): string
    {
        return 'video_posting';
    }

    public function label(): string
    {
        return 'Video Posting Tracker';
    }

    public function page(): array
    {
        return ['label' => 'Video Posting Tracker', 'route' => 'video-posting-tracker.index'];
    }

    public function tables(): array
    {
        return ['video_posting_records'];
    }

    protected function artisanCommand(): string
    {
        return 'import:video-posting-tracker';
    }

    protected function artisanParams(string $jsonPath, array $sheetNames): array
    {
        return ['--file' => $jsonPath];
    }

    public function matches(string $originalName, array $sheetNames): bool
    {
        $name = strtoupper($originalName);

        return str_contains($name, 'VLOG') || str_contains($name, 'POSTING');
    }

    public function listSheets(string $path): array
    {
        $ss = ExcelSheetHelper::load($path);
        $out = [];
        foreach ($ss->getWorksheetIterator() as $ws) {
            $name = $ws->getTitle();
            $header = ExcelSheetHelper::findHeaderMap($ws, ['VLOGGER'], 10, 30);
            $supported = $header !== null;
            // Skip plate-only helper sheets (e.g. Sheet11)
            if ($supported) {
                $hasOnlyPlate = ExcelSheetHelper::col($header['map'], ['VLOGGER']) === null;
                if ($hasOnlyPlate) {
                    $supported = false;
                }
            }
            $u = strtoupper($name);
            if (str_contains($u, 'SHEET11') || (str_contains($u, 'PLATE') && ! $header)) {
                $supported = false;
            }

            $rows = 0;
            if ($supported && $header) {
                $rows = ExcelSheetHelper::countNonEmptyRows(
                    $ws,
                    $header['row'],
                    ExcelSheetHelper::col($header['map'], ['VLOGGER', 'PLATE NO', 'PLATE'])
                );
            }

            $out[] = [
                'name' => $name,
                'supported' => $supported,
                'excel_rows' => $rows,
                'tables' => $this->tables(),
                'note' => $supported ? null : 'No VLOGGER header (plate-only / helper tab skipped).',
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
            $header = ExcelSheetHelper::findHeaderMap($ws, ['VLOGGER'], 10, 30);
            if (! $header || ExcelSheetHelper::col($header['map'], ['VLOGGER']) === null) {
                continue;
            }
            $map = $header['map'];
            $cVlogger = ExcelSheetHelper::col($map, ['VLOGGER']);
            $cCat = ExcelSheetHelper::col($map, ['CATEGORY']);
            $cShow = ExcelSheetHelper::col($map, ['SHOWROOM']);
            $cFeatured = ExcelSheetHelper::col($map, ['FEATURED CAR/S OR CLIENT', 'FEATURED CAR OR CLIENT', 'FEATURED']);
            $cPlate = ExcelSheetHelper::col($map, ['PLATE NO', 'PLATE NUMBER', 'PLATE']);
            $cActive = ExcelSheetHelper::col($map, ['ACTIVE UNIT', 'ACTIVE UNIT?']);
            $cUpload = ExcelSheetHelper::col($map, ['DATE UPLOADED TO G DRIVE', 'DATE UPLOADED', 'G DRIVE']);
            $cPosted = ExcelSheetHelper::col($map, ['DATE POSTED ON SOCIAL MEDIA', 'DATE POSTED', 'SOCIAL MEDIA']);
            $cFile = ExcelSheetHelper::col($map, ['NAME OF FILE IN G DRIVE', 'GDRIVE', 'FILE']);
            $cLink = ExcelSheetHelper::col($map, ['LINK TO POST', 'LINK', 'URL']);

            $highest = (int) $ws->getHighestDataRow();
            for ($r = $header['row'] + 1; $r <= $highest; $r++) {
                $vlogger = ExcelSheetHelper::cellString($ws, $cVlogger, $r);
                $link = ExcelSheetHelper::cellString($ws, $cLink, $r);
                $file = ExcelSheetHelper::cellString($ws, $cFile, $r);
                $plate = ExcelSheetHelper::cellString($ws, $cPlate, $r);
                if (! $vlogger && ! $link && ! $file && ! $plate) {
                    continue;
                }

                $activeRaw = ExcelSheetHelper::cell($ws, $cActive, $r);
                $active = ($activeRaw === null || $activeRaw === '')
                    ? null
                    : ExcelSheetHelper::toBool($activeRaw);

                $rows[] = [
                    'source_sheet' => $name,
                    'vlogger' => $vlogger,
                    'category' => ExcelSheetHelper::cellString($ws, $cCat, $r),
                    'showroom' => ExcelSheetHelper::cellString($ws, $cShow, $r),
                    'featured_car_or_client' => ExcelSheetHelper::cellString($ws, $cFeatured, $r),
                    'plate_number' => $plate ? strtoupper(trim($plate)) : null,
                    'active_unit' => $active,
                    'date_uploaded_gdrive' => ExcelSheetHelper::toDate(ExcelSheetHelper::cell($ws, $cUpload, $r)),
                    'date_posted_social' => ExcelSheetHelper::toDate(ExcelSheetHelper::cell($ws, $cPosted, $r)),
                    'gdrive_file_name' => $file,
                    'link_url' => $link,
                ];
            }
        }
        $ss->disconnectWorksheets();

        return $rows;
    }
}
