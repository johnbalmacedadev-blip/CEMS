<?php

namespace App\Support;

use App\Models\BranchLocation;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ExcelUnitReconcile
{
    public const SNAPSHOT_PATH = 'excel_units_snapshot.json';

    /** @var array<string, \App\Models\Vehicle>|null */
    protected static $vehiclesByPlateCache = null;

    /**
     * Build mismatch notes for the current Unit Report filters.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function notesForRequest(Request $request): array
    {
        self::$vehiclesByPlateCache = null;

        $snapshot = self::loadSnapshot();
        if (! $snapshot) {
            return [];
        }

        $status = $request->get('status', 'Available');
        if (in_array($status, ['all', 'Under Maintenance', 'Archived'], true)) {
            return [];
        }

        // Only show after the user applies filters (not on plain status-tab browsing).
        if (! self::hasAppliedFilters($request)) {
            return [];
        }

        $branchId = $request->get('branch_location_id');
        $branchName = null;
        if ($branchId !== null && $branchId !== '' && is_numeric($branchId)) {
            $branchName = optional(BranchLocation::find((int) $branchId))->name;
        }

        $releaseFrom = trim((string) $request->get('release_date_from', ''));
        $releaseTo = trim((string) $request->get('release_date_to', ''));

        $notes = [];
        foreach ($snapshot['designations'] ?? [] as $designation) {
            if (($designation['status'] ?? null) !== $status) {
                continue;
            }

            $designationBranch = $designation['branch'] ?? null;
            if ($branchName && strcasecmp((string) $designationBranch, (string) $branchName) !== 0) {
                continue;
            }

            $note = self::compareDesignation($designation, $releaseFrom, $releaseTo, $snapshot);
            if ($note) {
                $notes[] = $note;
            }
        }

        return $notes;
    }

    /**
     * True when the request has filters beyond the status tab alone.
     */
    public static function hasAppliedFilters(Request $request): bool
    {
        $keys = [
            'search',
            'branch_location_id',
            'year_from',
            'year_to',
            'transmission',
            'fuel_type',
            'body_type',
            'purchased_from',
            'reservation_date_from',
            'reservation_date_to',
            'reservation_date',
            'release_date_from',
            'release_date_to',
        ];

        foreach ($keys as $key) {
            $value = $request->get($key);
            if (is_string($value) && trim($value) !== '') {
                return true;
            }
            if (is_numeric($value)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string, mixed>|null
     */
    public static function loadSnapshot(): ?array
    {
        $path = storage_path('app/'.self::SNAPSHOT_PATH);
        if (! File::exists($path)) {
            return null;
        }

        $data = json_decode(File::get($path), true);

        return is_array($data) ? $data : null;
    }

    /**
     * @param  array<string, mixed>  $designation
     * @param  array<string, mixed>  $snapshot
     * @return array<string, mixed>|null
     */
    protected static function compareDesignation(array $designation, string $releaseFrom, string $releaseTo, array $snapshot): ?array
    {
        $status = $designation['status'];
        $branchName = $designation['branch'];
        $branch = BranchLocation::query()->where('name', $branchName)->first();

        $excelRows = $designation['rows'] ?? [];
        $hasReleaseFilter = $releaseFrom !== '' || $releaseTo !== '';

        if ($status === 'Released' && $hasReleaseFilter) {
            $excelRows = array_values(array_filter($excelRows, function ($row) use ($releaseFrom, $releaseTo) {
                $d = $row['release_date'] ?? null;
                if (! $d) {
                    return false;
                }
                if ($releaseFrom !== '' && $d < $releaseFrom) {
                    return false;
                }
                if ($releaseTo !== '' && $d > $releaseTo) {
                    return false;
                }

                return true;
            }));
        }

        $excelByPlate = [];
        foreach ($excelRows as $row) {
            $plate = self::normalizePlate($row['plate'] ?? '');
            if ($plate === '') {
                continue;
            }
            // Later Excel rows win (same behavior as import upsert).
            if (! isset($excelByPlate[$plate]) || ($row['excel_row'] ?? 0) >= ($excelByPlate[$plate]['excel_row'] ?? 0)) {
                $excelByPlate[$plate] = $row;
            }
        }

        $excelUnique = count($excelByPlate);
        $excelRowCount = count($excelRows);

        $dbQuery = Vehicle::query()->with(['statusDetail', 'branchLocation']);
        if ($status === 'Forfeited') {
            $dbQuery->where(function ($q) {
                $q->where('status', 'Forfeited')->orWhereHas('forfeitDetails');
            })->where('status', '!=', 'Archived');
        } else {
            $dbQuery->where('status', $status);
        }
        if ($branch) {
            $dbQuery->where('branch_location_id', $branch->id);
        }
        if ($status === 'Released' && $hasReleaseFilter) {
            $dbQuery->whereHas('statusDetail', function ($q) use ($releaseFrom, $releaseTo) {
                if ($releaseFrom !== '') {
                    $q->whereDate('release_date', '>=', $releaseFrom);
                }
                if ($releaseTo !== '') {
                    $q->whereDate('release_date', '<=', $releaseTo);
                }
            });
        }

        $dbVehicles = $dbQuery->get();
        $dbByPlate = [];
        foreach ($dbVehicles as $vehicle) {
            $plate = self::normalizePlate($vehicle->plate_number);
            if ($plate !== '') {
                $dbByPlate[$plate] = $vehicle;
            }
        }
        $dbCount = count($dbByPlate);

        $missingFromFilter = array_diff_key($excelByPlate, $dbByPlate);
        $extraInDb = array_diff_key($dbByPlate, $excelByPlate);

        if ($excelUnique === $dbCount && $missingFromFilter === [] && $extraInDb === []) {
            // Still surface note when Excel row count > unique (duplicate releases in sheet)
            if (! ($status === 'Released' && $excelRowCount > $excelUnique)) {
                return null;
            }
        }

        $allVehiclesByPlate = self::vehiclesByPlate();

        $reasons = [];
        $dupByPlate = [];
        foreach ($designation['duplicates'] ?? [] as $dup) {
            $plate = self::normalizePlate($dup['plate'] ?? '');
            if ($plate !== '') {
                $dupByPlate[$plate] = $dup;
            }
        }

        foreach ($missingFromFilter as $plate => $row) {
            $dup = $dupByPlate[$plate] ?? null;
            $vehicle = $allVehiclesByPlate[$plate] ?? null;

            if ($dup && $status === 'Released') {
                $dbReleaseStr = optional(optional($vehicle)->statusDetail)->release_date;
                $dbReleaseStr = $dbReleaseStr ? $dbReleaseStr->format('Y-m-d') : null;
                $newest = $dup['newest_release'] ?? null;
                $oldOcc = self::firstOccurrenceInRange($dup['occurrences'] ?? [], $releaseFrom, $releaseTo, $hasReleaseFilter);

                if ($oldOcc && $dbReleaseStr && $newest && $dbReleaseStr === $newest && $dbReleaseStr !== ($oldOcc['release_date'] ?? null)) {
                    $reasons[] = [
                        'type' => 'newer_release_overwrite',
                        'plate' => $plate,
                        'message' => sprintf(
                            '%s appears more than once in Excel (%s). Older release %s (row %s) was replaced in the database by newer release %s (row %s), so it is excluded from this filtered count.',
                            $plate,
                            $designation['tab'] ?? 'sheet',
                            $oldOcc['release_date'] ?? 'n/a',
                            $oldOcc['excel_row'] ?? '?',
                            $dbReleaseStr,
                            self::rowForRelease($dup['occurrences'] ?? [], $dbReleaseStr) ?? '?'
                        ),
                    ];
                    continue;
                }
            }

            if (! $vehicle) {
                $yearHint = '';
                $yearRaw = $row['year_raw'] ?? null;
                if (is_string($yearRaw) && trim($yearRaw) !== '' && ! is_numeric(trim($yearRaw))) {
                    $yearHint = sprintf(
                        ' Not imported originally because Excel YEAR was "%s" (text), not a plain year number.',
                        $yearRaw
                    );
                } elseif (($row['year'] ?? null) === null) {
                    $yearHint = ' Not imported originally because Excel YEAR was missing or not a plain year number (e.g. "2021 ACQ").';
                }
                $reasons[] = [
                    'type' => 'missing_in_database',
                    'plate' => $plate,
                    'message' => sprintf(
                        '%s is in Excel (%s row %s) but was not found in the database.%s',
                        $plate,
                        $designation['tab'] ?? 'sheet',
                        $row['excel_row'] ?? '?',
                        $yearHint
                    ),
                ];
                continue;
            }

            $dbStatus = $vehicle->status;
            $dbBranch = optional($vehicle->branchLocation)->name;
            $dbReleaseStr = optional($vehicle->statusDetail)->release_date;
            $dbReleaseStr = $dbReleaseStr ? $dbReleaseStr->format('Y-m-d') : null;
            $excelRelease = $row['release_date'] ?? null;

            if ($status === 'Released' && $hasReleaseFilter && $excelRelease && $dbReleaseStr && $dbReleaseStr !== $excelRelease) {
                $reasons[] = [
                    'type' => 'release_date_mismatch',
                    'plate' => $plate,
                    'message' => sprintf(
                        '%s is in Excel for release %s (row %s), but the database release date is %s — so it does not match this filter.',
                        $plate,
                        $excelRelease,
                        $row['excel_row'] ?? '?',
                        $dbReleaseStr
                    ),
                ];
                continue;
            }

            if (strcasecmp((string) $dbStatus, (string) $status) !== 0
                || ($branchName && strcasecmp((string) $dbBranch, (string) $branchName) !== 0)) {
                $reasons[] = [
                    'type' => 'status_or_showroom_changed',
                    'plate' => $plate,
                    'message' => sprintf(
                        '%s is listed as %s / %s in Excel (row %s), but the database currently has status %s / showroom %s.',
                        $plate,
                        $status,
                        $branchName,
                        $row['excel_row'] ?? '?',
                        $dbStatus ?: 'n/a',
                        $dbBranch ?: 'n/a'
                    ),
                ];
                continue;
            }

            $reasons[] = [
                'type' => 'not_in_filtered_result',
                'plate' => $plate,
                'message' => sprintf(
                    '%s is in Excel (row %s) but does not appear in the current filtered database result.',
                    $plate,
                    $row['excel_row'] ?? '?'
                ),
            ];
        }

        $extraPlates = array_keys($extraInDb);
        foreach (array_slice($extraPlates, 0, 15) as $plate) {
            $reasons[] = [
                'type' => 'extra_in_database',
                'plate' => $plate,
                'message' => sprintf(
                    '%s is in the database as %s / %s but is not in the Excel tab for this designation%s.',
                    $plate,
                    $status,
                    $branchName,
                    $hasReleaseFilter ? ' (within the selected release dates)' : ''
                ),
            ];
        }
        if (count($extraPlates) > 15) {
            $reasons[] = [
                'type' => 'extra_in_database_more',
                'plate' => null,
                'message' => sprintf('…and %d more database plate(s) not present in Excel for this view.', count($extraPlates) - 15),
            ];
        }

        if ($status === 'Released' && $excelRowCount > $excelUnique && ! $hasReleaseFilter) {
            $reasons[] = [
                'type' => 'excel_duplicate_rows',
                'plate' => null,
                'message' => sprintf(
                    'Excel has %s data rows but only %s unique plates because some plates were released more than once. The system stores one current release date per plate.',
                    number_format($excelRowCount),
                    number_format($excelUnique)
                ),
            ];
        }

        if ($excelUnique === $dbCount && $reasons === []) {
            return null;
        }

        $dateLabel = '';
        if ($status === 'Released' && $hasReleaseFilter) {
            $dateLabel = sprintf(
                ' (release %s to %s)',
                $releaseFrom !== '' ? $releaseFrom : '…',
                $releaseTo !== '' ? $releaseTo : '…'
            );
        }

        $reasonLimit = 40;
        $shownReasons = array_slice($reasons, 0, $reasonLimit);
        $hidden = max(0, count($reasons) - $reasonLimit);

        return [
            'key' => $designation['key'] ?? null,
            'label' => $designation['label'] ?? ($branchName.' '.$status),
            'tab' => $designation['tab'] ?? null,
            'source_name' => $snapshot['source_name'] ?? null,
            'excel_row_count' => $excelRowCount,
            'excel_unique_plates' => $excelUnique,
            'db_count' => $dbCount,
            'matches' => $excelUnique === $dbCount,
            'summary' => sprintf(
                'Excel (%s)%s has %s row(s) / %s unique plate(s). Database currently shows %s matching %s / %s unit(s).',
                $designation['tab'] ?? $designation['label'] ?? 'sheet',
                $dateLabel,
                number_format($excelRowCount),
                number_format($excelUnique),
                number_format($dbCount),
                $branchName,
                $status
            ),
            'reasons' => $shownReasons,
            'hidden_reason_count' => $hidden,
        ];
    }

    protected static function vehiclesByPlate(): array
    {
        if (self::$vehiclesByPlateCache !== null) {
            return self::$vehiclesByPlateCache;
        }

        $map = [];
        foreach (Vehicle::with(['statusDetail', 'branchLocation'])->get() as $vehicle) {
            $plate = self::normalizePlate($vehicle->plate_number);
            if ($plate !== '') {
                $map[$plate] = $vehicle;
            }
        }

        return self::$vehiclesByPlateCache = $map;
    }

    protected static function normalizePlate(?string $plate): string
    {
        return strtoupper(preg_replace('/[^A-Z0-9]/', '', (string) $plate) ?? '');
    }

    /**
     * @param  array<int, array<string, mixed>>  $occurrences
     * @return array<string, mixed>|null
     */
    protected static function firstOccurrenceInRange(array $occurrences, string $releaseFrom, string $releaseTo, bool $hasReleaseFilter): ?array
    {
        foreach ($occurrences as $occ) {
            $d = $occ['release_date'] ?? null;
            if (! $d) {
                continue;
            }
            if ($hasReleaseFilter) {
                if ($releaseFrom !== '' && $d < $releaseFrom) {
                    continue;
                }
                if ($releaseTo !== '' && $d > $releaseTo) {
                    continue;
                }
            }

            return $occ;
        }

        return $occurrences[0] ?? null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $occurrences
     */
    protected static function rowForRelease(array $occurrences, string $releaseDate): ?int
    {
        foreach ($occurrences as $occ) {
            if (($occ['release_date'] ?? null) === $releaseDate) {
                return isset($occ['excel_row']) ? (int) $occ['excel_row'] : null;
            }
        }

        return null;
    }
}
