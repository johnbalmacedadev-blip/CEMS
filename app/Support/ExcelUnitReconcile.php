<?php

namespace App\Support;

use App\Models\BranchLocation;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ExcelUnitReconcile
{
    public const SNAPSHOT_PATH = 'excel_units_snapshot.json';

    /**
     * Expected Released Unit Report counts by calendar month (Y-m) and branch,
     * from the compiled Excel sales totals (Feb-24 through Jul-26).
     *
     * @var array<string, array{Flagship:int, Annex:int}>
     */
    public const EXPECTED_RELEASED_SALES = [
        '2024-02' => ['Flagship' => 0, 'Annex' => 15],
        '2024-03' => ['Flagship' => 0, 'Annex' => 15],
        '2024-04' => ['Flagship' => 0, 'Annex' => 13],
        '2024-05' => ['Flagship' => 0, 'Annex' => 13],
        '2024-06' => ['Flagship' => 0, 'Annex' => 22],
        '2024-07' => ['Flagship' => 64, 'Annex' => 16],
        '2024-08' => ['Flagship' => 82, 'Annex' => 23],
        '2024-09' => ['Flagship' => 124, 'Annex' => 21],
        '2024-10' => ['Flagship' => 127, 'Annex' => 29],
        '2024-11' => ['Flagship' => 114, 'Annex' => 25],
        '2024-12' => ['Flagship' => 108, 'Annex' => 19],
        '2025-01' => ['Flagship' => 125, 'Annex' => 30],
        '2025-02' => ['Flagship' => 97, 'Annex' => 20],
        '2025-03' => ['Flagship' => 116, 'Annex' => 21],
        '2025-04' => ['Flagship' => 111, 'Annex' => 21],
        '2025-05' => ['Flagship' => 111, 'Annex' => 21],
        '2025-06' => ['Flagship' => 88, 'Annex' => 18],
        '2025-07' => ['Flagship' => 83, 'Annex' => 21],
        '2025-08' => ['Flagship' => 85, 'Annex' => 13],
        '2025-09' => ['Flagship' => 56, 'Annex' => 13],
        '2025-10' => ['Flagship' => 58, 'Annex' => 8],
        '2025-11' => ['Flagship' => 46, 'Annex' => 7],
        '2025-12' => ['Flagship' => 54, 'Annex' => 11],
        '2026-01' => ['Flagship' => 45, 'Annex' => 13],
        '2026-02' => ['Flagship' => 68, 'Annex' => 11],
        '2026-03' => ['Flagship' => 72, 'Annex' => 11],
        '2026-04' => ['Flagship' => 63, 'Annex' => 10],
        '2026-05' => ['Flagship' => 110, 'Annex' => 21],
        '2026-06' => ['Flagship' => 100, 'Annex' => 15],
        '2026-07' => ['Flagship' => 87, 'Annex' => 11],
    ];

    /** @var array<string, array<int, \App\Models\Vehicle>>|null */
    protected static $vehiclesByPlateCache = null;

    /**
     * When Released + release-date filters are set, align Unit Report to Excel release history
     * for the selected period. Only vehicles whose current database status is Released are listed.
     *
     * @return array<string, mixed>|null
     */
    public static function releasedDateFilterContext(Request $request): ?array
    {
        self::$vehiclesByPlateCache = null;

        $status = $request->get('status', 'Available');
        if ($status !== 'Released') {
            return null;
        }

        $releaseFrom = trim((string) $request->get('release_date_from', ''));
        $releaseTo = trim((string) $request->get('release_date_to', ''));
        if ($releaseFrom === '' && $releaseTo === '') {
            return null;
        }

        $snapshot = self::loadSnapshot();
        if (! $snapshot) {
            return null;
        }

        $branchId = $request->get('branch_location_id');
        $branchName = null;
        if ($branchId !== null && $branchId !== '' && is_numeric($branchId)) {
            $branchName = optional(BranchLocation::find((int) $branchId))->name;
        }

        $allVehicles = self::vehiclesByPlate();
        $platesMeta = []; // plate => excel row used for this filter
        $issues = [];
        $matchedVehicleIds = [];
        $locationCounts = [];
        $sourceName = $snapshot['source_name'] ?? null;
        $monthKey = self::filterMonthKey($releaseFrom, $releaseTo);
        $filterYear = null;
        $filterMonth = null;
        if ($monthKey !== null) {
            [$filterYear, $filterMonth] = array_map('intval', explode('-', $monthKey));
        }

        foreach ($snapshot['designations'] ?? [] as $designation) {
            if (($designation['status'] ?? null) !== 'Released') {
                continue;
            }

            $designationBranch = $designation['branch'] ?? null;
            if ($branchName && strcasecmp((string) $designationBranch, (string) $branchName) !== 0) {
                continue;
            }

            $excelRows = array_values(array_filter($designation['rows'] ?? [], function ($row) use ($releaseFrom, $releaseTo, $filterYear, $filterMonth) {
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
                // Compiled monthly sales follow the Excel month-section heading, not a later
                // RELEASE DATE or a totals row (e.g. "TOTAL RELEASED PROFIT").
                if ($filterYear !== null) {
                    $section = strtoupper(trim((string) ($row['section'] ?? '')));
                    if ($section !== '' && ! self::sectionMatchesMonth($section, $filterYear, $filterMonth)) {
                        return false;
                    }
                }

                return true;
            }));

            $byPlate = [];
            foreach ($excelRows as $row) {
                $plate = self::normalizePlate($row['plate'] ?? '');
                $excelRow = (int) ($row['excel_row'] ?? 0);
                if ($plate === '') {
                    $byPlate['NOPLATE_R'.$excelRow] = $row;
                    continue;
                }
                // Keep one row per plate unless Excel reused the plate on a different
                // vehicle in the same month (e.g. AAY3571 Fortuner + Wigo in Jan 2025).
                if (! isset($byPlate[$plate])) {
                    $byPlate[$plate] = $row;
                    continue;
                }
                if (self::sameExcelVehicleIdentity($byPlate[$plate], $row)) {
                    if ($excelRow > 0 && $excelRow < (int) ($byPlate[$plate]['excel_row'] ?? PHP_INT_MAX)) {
                        $byPlate[$plate] = $row;
                    }
                    continue;
                }
                $byPlate[$plate.'#'.$excelRow] = $row;
            }

            $expectedCount = self::expectedReleasedSales($monthKey, (string) $designationBranch);
            // Count month-section rows first (including later RELEASE DATE / blank date),
            // then only fill remaining slots from totals-block cars.
            $earlyBleeds = self::sectionBleedRows($designation['rows'] ?? [], $releaseFrom, $releaseTo, $byPlate);
            foreach ($earlyBleeds as $bleed) {
                self::addExcelRowToByPlate($byPlate, $bleed);
            }
            // Units listed after month totals (e.g. March 2026 Flagship NGS5223 / CAT2930 under
            // TOTAL RELEASED PROFIT) still count in compiled sales when the month is short.
            if ($filterYear !== null && $expectedCount !== null && count($byPlate) < $expectedCount) {
                foreach ($designation['rows'] ?? [] as $row) {
                    if (count($byPlate) >= $expectedCount) {
                        break;
                    }
                    $d = $row['release_date'] ?? null;
                    if (! is_string($d) || $d === '') {
                        continue;
                    }
                    if ($releaseFrom !== '' && $d < $releaseFrom) {
                        continue;
                    }
                    if ($releaseTo !== '' && $d > $releaseTo) {
                        continue;
                    }
                    $section = strtoupper(trim((string) ($row['section'] ?? '')));
                    if ($section !== '' && self::sectionMatchesMonth($section, $filterYear, $filterMonth)) {
                        continue;
                    }
                    if ($section !== '' && self::sectionBelongsToOtherMonth($section, $filterYear, $filterMonth)) {
                        continue;
                    }
                    self::addExcelRowToByPlate($byPlate, $row);
                }
            }

            $branch = BranchLocation::query()->where('name', $designationBranch)->first();
            // Count every Excel release-date row in this sheet/period (including blank-plate / missing-in-DB).
            $locationCounts[] = [
                'id' => $branch?->id,
                'name' => $designationBranch,
                'count' => count($byPlate),
                'tab' => $designation['tab'] ?? null,
            ];

            $dupByPlate = [];
            foreach ($designation['duplicates'] ?? [] as $dup) {
                $p = self::normalizePlate($dup['plate'] ?? '');
                if ($p !== '') {
                    $dupByPlate[$p] = $dup;
                }
            }

            $excludeRereleasesFromList = $expectedCount === null
                ? true
                : count($byPlate) > $expectedCount;

            foreach ($byPlate as $plateKey => $row) {
                $plate = self::normalizePlate($row['plate'] ?? '');
                $financialSlice = [
                    'total_revenue' => $row['total_revenue'] ?? null,
                    'total_costs' => $row['total_costs'] ?? null,
                    'total_profit' => $row['total_profit'] ?? null,
                    'sales_price' => $row['sales_price'] ?? null,
                    'release_date' => $row['release_date'] ?? null,
                    'excel_row' => $row['excel_row'] ?? null,
                    'branch' => $designationBranch,
                ];

                if (isset($platesMeta[$plateKey])) {
                    $existingBranches = $platesMeta[$plateKey]['branches'] ?? array_filter([
                        $platesMeta[$plateKey]['branch'] ?? null,
                    ]);
                    $branches = array_values(array_unique(array_filter([
                        ...$existingBranches,
                        $designationBranch,
                    ])));
                    $branchFinancials = $platesMeta[$plateKey]['branch_financials'] ?? [];
                    if ($designationBranch) {
                        $branchFinancials[$designationBranch] = $financialSlice;
                    }
                    // Keep earliest release_date row as the primary/default financials;
                    // always retain per-branch financials for location-filtered Sales totals.
                    $keepExisting = ($platesMeta[$plateKey]['release_date'] ?? '9999-99-99')
                        <= ($row['release_date'] ?? '9999-99-99');
                    if ($keepExisting) {
                        $platesMeta[$plateKey]['branches'] = $branches;
                        $platesMeta[$plateKey]['branch_financials'] = $branchFinancials;
                    } else {
                        $platesMeta[$plateKey] = array_merge($row, [
                            'branch' => $designationBranch,
                            'branches' => $branches,
                            'tab' => $designation['tab'] ?? null,
                            'branch_financials' => $branchFinancials,
                        ]);
                    }
                } else {
                    $platesMeta[$plateKey] = array_merge($row, [
                        'branch' => $designationBranch,
                        'branches' => array_values(array_filter([$designationBranch])),
                        'tab' => $designation['tab'] ?? null,
                        'branch_financials' => $designationBranch
                            ? [$designationBranch => $financialSlice]
                            : [],
                    ]);
                }

                $vehicle = self::matchVehicleForExcelRow($row, $allVehicles, $matchedVehicleIds);
                if ($vehicle) {
                    $matchedVehicleIds[$vehicle->id] = true;
                }
                $dup = $dupByPlate[$plate] ?? null;
                $dbRelease = optional(optional($vehicle)->statusDetail)->release_date;
                $dbReleaseStr = $dbRelease ? $dbRelease->format('Y-m-d') : null;
                $excelRelease = $row['release_date'] ?? null;

                $reusedPlateDifferentVehicle = self::excelPlateReusedOnDifferentVehicles($byPlate, $plate);

                if (! $reusedPlateDifferentVehicle && $dup && ($dup['newest_release'] ?? null) && ($dup['oldest_release'] ?? null)
                    && $dup['newest_release'] !== $dup['oldest_release']) {
                    $newest = $dup['newest_release'];
                    $oldOcc = self::firstOccurrenceInRange($dup['occurrences'] ?? [], $releaseFrom, $releaseTo, true);
                    if ($oldOcc && $excelRelease && $newest !== $excelRelease) {
                        $issues[] = [
                            'type' => 'duplicate_rerelease',
                            'plate' => $plate,
                            'excel_release_date' => $excelRelease,
                            'excel_row' => $row['excel_row'] ?? null,
                            'db_release_date' => $dbReleaseStr,
                            'newer_release_date' => $newest,
                            'newer_excel_row' => self::rowForRelease($dup['occurrences'] ?? [], $newest),
                            'branch' => $designationBranch,
                            'tab' => $designation['tab'] ?? null,
                            'make' => $row['make'] ?? optional($vehicle)->make,
                            'model' => $row['model'] ?? optional($vehicle)->model,
                            'year' => $row['year'] ?? optional($vehicle)->year,
                            'message' => sprintf(
                                '%s has multiple Excel releases. This filter uses %s (row %s). Database current release is %s (newer Excel row %s). %s',
                                $plate,
                                $excelRelease,
                                $row['excel_row'] ?? '?',
                                $dbReleaseStr ?: 'n/a',
                                self::rowForRelease($dup['occurrences'] ?? [], $newest) ?? '?',
                                $excludeRereleasesFromList
                                    ? 'Excluded from this month’s list so the count matches Excel sales; kept in the issue notice.'
                                    : 'Forced added with issue so this month’s count matches Excel sales.'
                            ),
                        ];
                        if ($excludeRereleasesFromList) {
                            unset($platesMeta[$plateKey]);
                            foreach ($locationCounts as $i => $loc) {
                                if (strcasecmp((string) ($loc['name'] ?? ''), (string) $designationBranch) === 0) {
                                    $locationCounts[$i]['count'] = max(0, (int) ($loc['count'] ?? 0) - 1);
                                    break;
                                }
                            }
                        } elseif (isset($platesMeta[$plateKey])) {
                            $platesMeta[$plateKey]['forced_rerelease_add'] = true;
                        }
                        continue;
                    } elseif ($dbReleaseStr && $excelRelease && $dbReleaseStr !== $excelRelease) {
                        $issues[] = [
                            'type' => 'release_date_mismatch',
                            'plate' => $plate,
                            'excel_release_date' => $excelRelease,
                            'excel_row' => $row['excel_row'] ?? null,
                            'db_release_date' => $dbReleaseStr,
                            'newer_release_date' => $dbReleaseStr,
                            'newer_excel_row' => self::rowForRelease($dup['occurrences'] ?? [], (string) $dbReleaseStr),
                            'branch' => $designationBranch,
                            'tab' => $designation['tab'] ?? null,
                            'make' => $row['make'] ?? optional($vehicle)->make,
                            'model' => $row['model'] ?? optional($vehicle)->model,
                            'year' => $row['year'] ?? optional($vehicle)->year,
                            'message' => sprintf(
                                '%s Excel release for this period is %s (row %s), but database release_date is %s. Included in this count to match Excel.',
                                $plate,
                                $excelRelease,
                                $row['excel_row'] ?? '?',
                                $dbReleaseStr
                            ),
                        ];
                    }
                } elseif ($vehicle && $dbReleaseStr && $excelRelease && $dbReleaseStr !== $excelRelease) {
                    $issues[] = [
                        'type' => 'release_date_mismatch',
                        'plate' => $plate,
                        'excel_release_date' => $excelRelease,
                        'excel_row' => $row['excel_row'] ?? null,
                        'db_release_date' => $dbReleaseStr,
                        'newer_release_date' => $dbReleaseStr,
                        'newer_excel_row' => null,
                        'branch' => $designationBranch,
                        'tab' => $designation['tab'] ?? null,
                        'make' => $row['make'] ?? optional($vehicle)->make,
                        'model' => $row['model'] ?? optional($vehicle)->model,
                        'year' => $row['year'] ?? optional($vehicle)->year,
                        'message' => sprintf(
                            '%s Excel release for this period is %s (row %s), but database release_date is %s. Included in this count to match Excel.',
                            $plate,
                            $excelRelease,
                            $row['excel_row'] ?? '?',
                            $dbReleaseStr
                        ),
                    ];
                }

                if (! empty($row['missing_plate'])) {
                    $issues[] = [
                        'type' => 'missing_plate',
                        'plate' => $plate,
                        'excel_release_date' => $excelRelease,
                        'excel_row' => $row['excel_row'] ?? null,
                        'db_release_date' => null,
                        'newer_release_date' => null,
                        'newer_excel_row' => null,
                        'branch' => $designationBranch,
                        'tab' => $designation['tab'] ?? null,
                        'make' => $row['make'] ?? null,
                        'model' => $row['model'] ?? null,
                        'year' => $row['year'] ?? null,
                        'message' => sprintf(
                            'Excel %s row %s (%s %s %s) has no plate number. Included in this period list/count to match Excel; not linked to a database unit.',
                            $designation['tab'] ?? 'sheet',
                            $row['excel_row'] ?? '?',
                            $row['year'] ?? '',
                            $row['make'] ?? '',
                            $row['model'] ?? ''
                        ),
                    ];
                } elseif (! $vehicle) {
                    $issues[] = [
                        'type' => 'missing_in_database',
                        'plate' => $plate,
                        'excel_release_date' => $excelRelease,
                        'excel_row' => $row['excel_row'] ?? null,
                        'db_release_date' => null,
                        'newer_release_date' => null,
                        'newer_excel_row' => null,
                        'branch' => $designationBranch,
                        'tab' => $designation['tab'] ?? null,
                        'make' => $row['make'] ?? null,
                        'model' => $row['model'] ?? null,
                        'year' => $row['year'] ?? null,
                        'message' => sprintf(
                            '%s is in Excel (%s row %s, release %s) but was not found in the database.',
                            $plate,
                            $designation['tab'] ?? 'sheet',
                            $row['excel_row'] ?? '?',
                            $excelRelease ?: 'n/a'
                        ),
                    ];
                } elseif (($vehicle->status ?? null) !== 'Released') {
                    $issues[] = [
                        'type' => 'status_not_released',
                        'plate' => $plate,
                        'excel_release_date' => $excelRelease,
                        'excel_row' => $row['excel_row'] ?? null,
                        'db_release_date' => $dbReleaseStr,
                        'newer_release_date' => null,
                        'newer_excel_row' => null,
                        'branch' => $designationBranch,
                        'tab' => $designation['tab'] ?? null,
                        'make' => $row['make'] ?? optional($vehicle)->make,
                        'model' => $row['model'] ?? optional($vehicle)->model,
                        'year' => $row['year'] ?? optional($vehicle)->year,
                        'message' => sprintf(
                            '%s is in Excel release history (%s row %s) but database status is %s. Shown in this Released period list to match Excel; also appears under its current status tab (e.g. Forfeited).',
                            $plate,
                            $designation['tab'] ?? 'sheet',
                            $row['excel_row'] ?? '?',
                            $vehicle->status ?: 'unknown'
                        ),
                    ];
                }
            }

            // Excel month-section rows whose RELEASE DATE falls outside this filter
            // (e.g. Annex "FEBRUARY 2024" section listing cars released in March).
            $bleeds = self::sectionBleedRows($designation['rows'] ?? [], $releaseFrom, $releaseTo, $byPlate);
            usort($bleeds, function ($a, $b) use ($releaseFrom, $releaseTo) {
                $score = function ($row) use ($releaseFrom, $releaseTo) {
                    $sale = $row['sale_date'] ?? null;
                    if (! is_string($sale) || $sale === '') {
                        return 0;
                    }
                    if ($releaseFrom !== '' && $sale < $releaseFrom) {
                        return 0;
                    }
                    if ($releaseTo !== '' && $sale > $releaseTo) {
                        return 0;
                    }

                    return 1;
                };

                return $score($b) <=> $score($a);
            });

            foreach ($bleeds as $bleed) {
                $plate = self::normalizePlate($bleed['plate'] ?? '');
                if ($plate === '') {
                    continue;
                }
                $vehicle = self::matchVehicleForExcelRow($bleed, $allVehicles);
                $dbRelease = optional(optional($vehicle)->statusDetail)->release_date;
                $issues[] = [
                    'type' => 'excel_section_outside_filter',
                    'plate' => $plate,
                    'excel_release_date' => $bleed['release_date'] ?? null,
                    'excel_row' => $bleed['excel_row'] ?? null,
                    'db_release_date' => $dbRelease ? $dbRelease->format('Y-m-d') : null,
                    'newer_release_date' => null,
                    'newer_excel_row' => null,
                    'branch' => $designationBranch,
                    'tab' => $designation['tab'] ?? null,
                    'make' => $bleed['make'] ?? optional($vehicle)->make,
                    'model' => $bleed['model'] ?? optional($vehicle)->model,
                    'year' => $bleed['year'] ?? optional($vehicle)->year,
                    'forced_added' => false,
                    'message' => sprintf(
                        '%s appears under Excel section "%s" (row %s) but its RELEASE DATE is %s, outside this filter. Sale date in Excel: %s.',
                        $plate,
                        $bleed['section'] ?? 'n/a',
                        $bleed['excel_row'] ?? '?',
                        $bleed['release_date'] ?? 'n/a',
                        $bleed['sale_date'] ?? 'n/a'
                    ),
                ];

                $sale = $bleed['sale_date'] ?? null;
                $saleInFilter = is_string($sale) && $sale !== ''
                    && ($releaseFrom === '' || $sale >= $releaseFrom)
                    && ($releaseTo === '' || $sale <= $releaseTo);
                $belowExpected = $expectedCount !== null && count($byPlate) < $expectedCount;

                if (isset($byPlate[$plate]) || isset($platesMeta[$plate])) {
                    continue;
                }
                if (! $belowExpected && ! $saleInFilter) {
                    continue;
                }

                $issues[array_key_last($issues)]['forced_added'] = true;
                $issues[array_key_last($issues)]['message'] = sprintf(
                    '%s appears under Excel section "%s" (row %s) but its RELEASE DATE is %s, outside this filter. Forced added with issue so this month’s count matches Excel sales. Sale date in Excel: %s.',
                    $plate,
                    $bleed['section'] ?? 'n/a',
                    $bleed['excel_row'] ?? '?',
                    $bleed['release_date'] ?? 'n/a',
                    $sale ?: 'n/a'
                );

                $byPlate[$plate] = $bleed;
                $platesMeta[$plate] = array_merge($bleed, [
                    'branch' => $designationBranch,
                    'branches' => array_values(array_filter([$designationBranch])),
                    'tab' => $designation['tab'] ?? null,
                    'forced_section_add' => true,
                    'branch_financials' => $designationBranch
                        ? [$designationBranch => [
                            'total_revenue' => $bleed['total_revenue'] ?? null,
                            'total_costs' => $bleed['total_costs'] ?? null,
                            'total_profit' => $bleed['total_profit'] ?? null,
                            'sales_price' => $bleed['sales_price'] ?? null,
                            'release_date' => $bleed['release_date'] ?? null,
                            'excel_row' => $bleed['excel_row'] ?? null,
                            'branch' => $designationBranch,
                        ]]
                        : [],
                ]);
                foreach ($locationCounts as $i => $loc) {
                    if (strcasecmp((string) ($loc['name'] ?? ''), (string) $designationBranch) === 0) {
                        $locationCounts[$i]['count'] = (int) ($loc['count'] ?? 0) + 1;
                        break;
                    }
                }
            }

            // Database plates in this branch/date range that Excel does not list for the period.
            if ($branch) {
                $dbExtras = Vehicle::query()
                    ->with(['statusDetail', 'make', 'vehicleModel'])
                    ->where('status', 'Released')
                    ->where('branch_location_id', $branch->id)
                    ->whereHas('statusDetail', function ($q) use ($releaseFrom, $releaseTo) {
                        if ($releaseFrom !== '') {
                            $q->whereDate('release_date', '>=', $releaseFrom);
                        }
                        if ($releaseTo !== '') {
                            $q->whereDate('release_date', '<=', $releaseTo);
                        }
                    })
                    ->get();

                foreach ($dbExtras as $extraVehicle) {
                    $plate = self::normalizePlate($extraVehicle->plate_number);
                    if ($plate === '' || isset($byPlate[$plate])) {
                        continue;
                    }
                    $dbRelease = optional($extraVehicle->statusDetail)->release_date;
                    $dbReleaseStr = $dbRelease ? $dbRelease->format('Y-m-d') : null;
                    $issues[] = [
                        'type' => 'extra_in_database',
                        'plate' => $plate,
                        'excel_release_date' => null,
                        'excel_row' => null,
                        'db_release_date' => $dbReleaseStr,
                        'newer_release_date' => null,
                        'newer_excel_row' => null,
                        'branch' => $designationBranch,
                        'tab' => $designation['tab'] ?? null,
                        'make' => $extraVehicle->make,
                        'model' => $extraVehicle->model,
                        'year' => $extraVehicle->year,
                        'message' => sprintf(
                            '%s is Released in the database for %s (release %s) but has no matching Excel RELEASE DATE row in this period for %s. Excluded from displayed count so totals follow Excel.',
                            $plate,
                            $designationBranch,
                            $dbReleaseStr ?: 'n/a',
                            $designation['tab'] ?? 'sheet'
                        ),
                    ];
                }
            }
        }

        if ($platesMeta === [] && $issues === []) {
            return null;
        }

        $vehicleIds = [];
        $excelOnlyPlates = [];
        foreach ($platesMeta as $plateKey => $row) {
            $vehicle = self::matchVehicleForExcelRow($row, $allVehicles);
            // Include Excel-matched plates even when DB status is no longer Released (e.g. Forfeited).
            if ($vehicle && ($vehicle->status ?? null) !== 'Archived') {
                $vehicleIds[] = $vehicle->id;
            } else {
                $excelOnlyPlates[] = $plateKey;
            }
        }
        $vehicleIds = array_values(array_unique($vehicleIds));

        $dateLabel = sprintf(
            '%s to %s',
            $releaseFrom !== '' ? $releaseFrom : '…',
            $releaseTo !== '' ? $releaseTo : '…'
        );

        $warning = null;
        if ($issues !== []) {
            $issueTypes = array_count_values(array_map(fn ($i) => $i['type'] ?? 'other', $issues));
            $typeBits = [];
            foreach ($issueTypes as $type => $count) {
                $label = match ($type) {
                    'duplicate_rerelease' => 're-release',
                    'release_date_mismatch' => 'date mismatch',
                    'missing_in_database' => 'missing in DB',
                    'missing_plate' => 'missing plate',
                    'extra_in_database' => 'extra in DB',
                    'status_not_released' => 'not Released in DB',
                    'excel_section_outside_filter' => 'forced add (section date)',
                    default => str_replace('_', ' ', (string) $type),
                };
                $typeBits[] = $count.' '.$label;
            }

            $warning = [
                'title' => 'Release data issues in this filtered result',
                'summary' => sprintf(
                    '%s issue(s) found while matching Excel release history for %s (%s). Showing %s Excel unit(s) (%s in database). Section rows with a later release date are forced into this month’s list. Re-release units stay in this notice only. Click to view details.',
                    number_format(count($issues)),
                    $dateLabel,
                    implode(', ', $typeBits),
                    number_format(count($platesMeta)),
                    number_format(count($vehicleIds))
                ),
                'issue_count' => count($issues),
                'excel_unique_plates' => count($platesMeta),
                'source_name' => $sourceName,
                'date_label' => $dateLabel,
                'issues' => $issues,
            ];
        }

        // Index issues by plate for per-row "View details" on the Unit Report list.
        $issuesByPlate = [];
        foreach ($issues as $issue) {
            $p = self::normalizePlate($issue['plate'] ?? '');
            if ($p === '') {
                continue;
            }
            $issuesByPlate[$p][] = $issue;
        }

        return [
            'mode' => 'excel_release_history',
            'plates' => array_keys($platesMeta),
            'plates_meta' => $platesMeta,
            'vehicle_ids' => $vehicleIds,
            'excel_only_plates' => $excelOnlyPlates,
            // Excel unique rows are the source of truth for this filtered period.
            'total' => count($platesMeta),
            'matched_vehicle_count' => count($vehicleIds),
            'excel_unique_plates' => count($platesMeta),
            'location_counts' => $locationCounts,
            'warning' => $warning,
            'issues_by_plate' => $issuesByPlate,
            'source_name' => $sourceName,
            'date_label' => $dateLabel,
        ];
    }

    /**
     * Build mismatch notes for non–release-date-aligned views.
     *
     * @return array<int, array<string, mixed>>
     */
    public static function notesForRequest(Request $request): array
    {
        // Released + date filter uses excel-aligned listing + clickable warning instead.
        if (self::releasedDateFilterContext($request) !== null) {
            return [];
        }

        self::$vehiclesByPlateCache = null;

        $snapshot = self::loadSnapshot();
        if (! $snapshot) {
            return [];
        }

        $status = $request->get('status', 'Available');
        if (in_array($status, ['all', 'Under Maintenance', 'Archived'], true)) {
            return [];
        }

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
     * Attach Excel tab + row number from the units snapshot (by plate).
     *
     * @param  iterable<int, \App\Models\Vehicle>  $vehicles
     */
    public static function attachExcelSourceRows(iterable $vehicles): void
    {
        $index = self::excelRowIndexByPlate();
        if ($index === []) {
            return;
        }

        foreach ($vehicles as $vehicle) {
            if (! $vehicle instanceof Vehicle) {
                continue;
            }

            $alreadyRow = $vehicle->getAttribute('excel_period_row');
            $plate = self::normalizePlate($vehicle->plate_number);
            $hits = $index[$plate] ?? [];

            if ($alreadyRow) {
                if (! $vehicle->getAttribute('excel_source_tab') && $hits !== []) {
                    $match = null;
                    foreach ($hits as $hit) {
                        if ((int) ($hit['excel_row'] ?? 0) === (int) $alreadyRow) {
                            $match = $hit;
                            break;
                        }
                    }
                    $match = $match ?? $hits[0];
                    $vehicle->setAttribute('excel_source_tab', $match['tab'] ?? null);
                }
                continue;
            }

            if ($hits === []) {
                continue;
            }

            $status = (string) ($vehicle->status ?? '');
            $preferred = array_values(array_filter($hits, function ($hit) use ($status) {
                return strcasecmp((string) ($hit['status'] ?? ''), $status) === 0;
            }));
            $pool = $preferred !== [] ? $preferred : $hits;
            usort($pool, function ($a, $b) {
                return ((int) ($a['excel_row'] ?? 0)) <=> ((int) ($b['excel_row'] ?? 0));
            });
            $best = $pool[count($pool) - 1];
            $vehicle->setAttribute('excel_period_row', $best['excel_row'] ?? null);
            $vehicle->setAttribute('excel_source_tab', $best['tab'] ?? null);
            if (count($pool) > 1) {
                $vehicle->setAttribute(
                    'excel_source_rows',
                    array_values(array_unique(array_map(fn ($h) => $h['excel_row'] ?? null, $pool)))
                );
            }
        }
    }

    /**
     * @return array<string, list<array{excel_row:?int,tab:?string,status:?string,branch:?string}>>
     */
    protected static function excelRowIndexByPlate(): array
    {
        static $cache = null;
        if ($cache !== null) {
            return $cache;
        }

        $snapshot = self::loadSnapshot();
        $index = [];
        foreach ($snapshot['designations'] ?? [] as $designation) {
            foreach ($designation['rows'] ?? [] as $row) {
                $plate = self::normalizePlate($row['plate'] ?? '');
                if ($plate === '' || str_starts_with($plate, 'NOPLATE')) {
                    continue;
                }
                $index[$plate][] = [
                    'excel_row' => $row['excel_row'] ?? null,
                    'tab' => $designation['tab'] ?? ($designation['label'] ?? null),
                    'status' => $designation['status'] ?? null,
                    'branch' => $designation['branch'] ?? null,
                ];
            }
        }

        return $cache = $index;
    }

    /**
     * Apply remaining Unit Report filters to an Eloquent query (excluding release-date whereHas).
     */
    public static function applyNonReleaseFilters($query, Request $request)
    {
        $search = $request->get('search');
        $yearFrom = $request->get('year_from');
        $yearTo = $request->get('year_to');
        $transmission = $request->get('transmission');
        $fuelType = $request->get('fuel_type');
        $bodyType = $request->get('body_type');
        $purchasedFrom = $request->get('purchased_from');
        $branchLocationId = $request->get('branch_location_id');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('make', 'LIKE', "%{$search}%")
                    ->orWhere('model', 'LIKE', "%{$search}%")
                    ->orWhere('variant', 'LIKE', "%{$search}%")
                    ->orWhere('plate_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('make', function ($makeQuery) use ($search) {
                        $makeQuery->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('vehicleModel', function ($modelQuery) use ($search) {
                        $modelQuery->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        if ($yearFrom !== null && $yearFrom !== '') {
            $query->where('year', '>=', (int) $yearFrom);
        }
        if ($yearTo !== null && $yearTo !== '') {
            $query->where('year', '<=', (int) $yearTo);
        }

        if (is_string($transmission) && $transmission !== '' && in_array($transmission, ['Manual', 'Automatic'], true)) {
            $query->where('transmission', $transmission);
        }

        if (is_string($fuelType) && $fuelType !== '' && in_array($fuelType, ['Diesel', 'Gasoline', 'Hybrid', 'Electric'], true)) {
            $query->where('fuel_type', $fuelType);
        }

        if (is_string($bodyType) && trim($bodyType) !== '') {
            $query->where('body_type', 'like', '%'.trim($bodyType).'%');
        }

        if (is_string($purchasedFrom) && trim($purchasedFrom) !== '') {
            $query->where('purchased_from', 'like', '%'.trim($purchasedFrom).'%');
        }

        if ($branchLocationId !== null && $branchLocationId !== '' && is_numeric($branchLocationId)) {
            $query->where('branch_location_id', (int) $branchLocationId);
        }

        return $query;
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
            if (! ($status === 'Released' && $excelRowCount > $excelUnique)) {
                return null;
            }
        }

        $allVehiclesByPlate = self::vehiclesByPlate();
        $reasons = [];

        foreach ($missingFromFilter as $plate => $row) {
            $vehicle = self::matchVehicleForExcelRow($row, $allVehiclesByPlate);
            if (! $vehicle) {
                $reasons[] = [
                    'type' => 'missing_in_database',
                    'plate' => $plate,
                    'message' => sprintf(
                        '%s is in Excel (%s row %s) but was not found in the database.',
                        $plate,
                        $designation['tab'] ?? 'sheet',
                        $row['excel_row'] ?? '?'
                    ),
                ];
                continue;
            }

            $reasons[] = [
                'type' => 'status_or_showroom_changed',
                'plate' => $plate,
                'message' => sprintf(
                    '%s is listed as %s / %s in Excel (row %s), but the database currently has status %s / showroom %s.',
                    $plate,
                    $status,
                    $branchName,
                    $row['excel_row'] ?? '?',
                    $vehicle->status ?: 'n/a',
                    optional($vehicle->branchLocation)->name ?: 'n/a'
                ),
            ];
        }

        $extraPlates = array_keys($extraInDb);
        foreach (array_slice($extraPlates, 0, 15) as $plate) {
            $reasons[] = [
                'type' => 'extra_in_database',
                'plate' => $plate,
                'message' => sprintf(
                    '%s is in the database as %s / %s but is not in the Excel tab for this designation.',
                    $plate,
                    $status,
                    $branchName
                ),
            ];
        }

        if ($status === 'Released' && $excelRowCount > $excelUnique && ! $hasReleaseFilter) {
            $reasons[] = [
                'type' => 'excel_duplicate_rows',
                'plate' => null,
                'message' => sprintf(
                    'Excel has %s data rows but only %s unique plates because some plates were released more than once.',
                    number_format($excelRowCount),
                    number_format($excelUnique)
                ),
            ];
        }

        if ($excelUnique === $dbCount && $reasons === []) {
            return null;
        }

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
                'Excel (%s) has %s unique plate(s). Database currently shows %s matching %s / %s unit(s).',
                $designation['tab'] ?? $designation['label'] ?? 'sheet',
                number_format($excelUnique),
                number_format($dbCount),
                $branchName,
                $status
            ),
            'reasons' => array_slice($reasons, 0, 40),
            'hidden_reason_count' => max(0, count($reasons) - 40),
        ];
    }

    /**
     * @return array<string, array<int, Vehicle>>
     */
    protected static function vehiclesByPlate(): array
    {
        if (self::$vehiclesByPlateCache !== null) {
            return self::$vehiclesByPlateCache;
        }

        $map = [];
        foreach (Vehicle::with(['statusDetail', 'branchLocation', 'make', 'vehicleModel', 'primaryImage', 'forfeitDetails'])->get() as $vehicle) {
            $plate = self::normalizePlate($vehicle->plate_number);
            if ($plate !== '') {
                $map[$plate][] = $vehicle;
            }
        }

        return self::$vehiclesByPlateCache = $map;
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  array<string, array<int, \App\Models\Vehicle>>  $vehiclesByPlate
     */
    public static function matchVehicleForExcelRow(array $row, array $vehiclesByPlate, array $usedIds = []): ?Vehicle
    {
        $plate = self::normalizePlate($row['plate'] ?? '');
        if ($plate === '') {
            return null;
        }

        return self::pickVehicleForExcelRow($vehiclesByPlate[$plate] ?? [], $row, $usedIds);
    }

    /**
     * Prefer year/make/model when the same plate maps to more than one vehicle.
     * Otherwise match by plate only (DB make is often a relation, not the Excel text).
     *
     * @param  array<int, \App\Models\Vehicle>  $candidates
     * @param  array<string, mixed>  $meta
     * @param  array<int, true>  $usedIds
     */
    public static function pickVehicleForExcelRow(array $candidates, array $meta, array $usedIds = []): ?Vehicle
    {
        $unused = [];
        foreach ($candidates as $vehicle) {
            if (! isset($usedIds[$vehicle->id])) {
                $unused[] = $vehicle;
            }
        }
        if ($unused === []) {
            return null;
        }

        $identityHits = [];
        foreach ($unused as $vehicle) {
            if (self::vehicleMatchesExcelIdentity($vehicle, $meta)) {
                $identityHits[] = $vehicle;
            }
        }
        if ($identityHits !== []) {
            return $identityHits[0];
        }

        return count($unused) === 1 ? $unused[0] : null;
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public static function vehicleMatchesExcelIdentity(Vehicle $vehicle, array $meta): bool
    {
        $norm = static function ($value): string {
            return strtoupper(preg_replace('/[^A-Z0-9]/', '', (string) $value) ?? '');
        };

        $attrs = $vehicle->getAttributes();
        $makeRel = $vehicle->relationLoaded('make') ? $vehicle->getRelation('make') : null;
        $makeName = is_object($makeRel) ? (string) ($makeRel->name ?? '') : '';
        if ($makeName === '') {
            $makeName = (string) ($attrs['make'] ?? '');
        }
        $modelRel = $vehicle->relationLoaded('vehicleModel') ? $vehicle->getRelation('vehicleModel') : null;
        $modelName = is_object($modelRel) ? (string) ($modelRel->name ?? '') : '';
        if ($modelName === '') {
            $modelName = (string) ($attrs['model'] ?? '');
        }
        if ($makeName === '' && $modelName === '') {
            return false;
        }

        $yearMeta = $meta['year'] ?? null;
        if ($yearMeta !== null && $yearMeta !== '' && (string) $vehicle->year !== (string) $yearMeta) {
            return false;
        }
        $makeMeta = $norm($meta['make'] ?? '');
        if ($makeMeta !== '' && $norm($makeName) !== $makeMeta) {
            return false;
        }
        $modelMeta = $norm($meta['model'] ?? '');
        if ($modelMeta !== '' && $norm($modelName) !== $modelMeta && ! str_contains($norm($modelName), $modelMeta) && ! str_contains($modelMeta, $norm($modelName))) {
            return false;
        }

        return true;
    }

    /**
     * @param  array<string, array<string, mixed>>  $byPlate
     */
    protected static function excelPlateReusedOnDifferentVehicles(array $byPlate, string $plate): bool
    {
        if ($plate === '') {
            return false;
        }
        $rows = [];
        foreach ($byPlate as $row) {
            if (self::normalizePlate($row['plate'] ?? '') === $plate) {
                $rows[] = $row;
            }
        }
        if (count($rows) < 2) {
            return false;
        }
        $first = $rows[0];
        foreach (array_slice($rows, 1) as $other) {
            if (! self::sameExcelVehicleIdentity($first, $other)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $a
     * @param  array<string, mixed>  $b
     */
    protected static function sameExcelVehicleIdentity(array $a, array $b): bool
    {
        $norm = static function ($value): string {
            return strtoupper(preg_replace('/[^A-Z0-9]/', '', (string) $value) ?? '');
        };

        return $norm($a['year'] ?? '') === $norm($b['year'] ?? '')
            && $norm($a['make'] ?? '') === $norm($b['make'] ?? '')
            && $norm($a['model'] ?? '') === $norm($b['model'] ?? '');
    }

    /**
     * @param  array<string, array<string, mixed>>  $byPlate
     * @param  array<string, mixed>  $row
     */
    protected static function addExcelRowToByPlate(array &$byPlate, array $row): void
    {
        $plate = self::normalizePlate($row['plate'] ?? '');
        $excelRow = (int) ($row['excel_row'] ?? 0);
        if ($plate === '') {
            $byPlate['NOPLATE_R'.$excelRow] = $row;

            return;
        }
        if (! isset($byPlate[$plate])) {
            $byPlate[$plate] = $row;

            return;
        }
        if (self::sameExcelVehicleIdentity($byPlate[$plate], $row)) {
            if ($excelRow > 0 && $excelRow < (int) ($byPlate[$plate]['excel_row'] ?? PHP_INT_MAX)) {
                $byPlate[$plate] = $row;
            }

            return;
        }
        $byPlate[$plate.'#'.$excelRow] = $row;
    }

    /**
     * True when the heading is a different calendar month than the Unit Report filter.
     */
    protected static function sectionBelongsToOtherMonth(string $section, int $year, int $month): bool
    {
        $section = strtoupper(trim($section));
        if ($section === '' || self::sectionMatchesMonth($section, $year, $month)) {
            return false;
        }

        $monthNames = [
            1 => ['JANUARY', 'JAN'],
            2 => ['FEBRUARY', 'FEBUARY', 'FEB'],
            3 => ['MARCH', 'MAR'],
            4 => ['APRIL', 'APR'],
            5 => ['MAY'],
            6 => ['JUNE', 'JUN'],
            7 => ['JULY', 'JUL'],
            8 => ['AUGUST', 'AUG'],
            9 => ['SEPTEMBER', 'SEPT', 'SEP'],
            10 => ['OCTOBER', 'OCT'],
            11 => ['NOVEMBER', 'NOV'],
            12 => ['DECEMBER', 'DEC'],
        ];
        foreach ($monthNames as $names) {
            foreach ($names as $needle) {
                if ($needle === 'MAY') {
                    if (preg_match('/\bMAY\b/', $section)) {
                        return true;
                    }
                    continue;
                }
                if (str_contains($section, $needle)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param  array<string, mixed>  $meta
     */
    public static function metaPlate(string $mapKey, array $meta): string
    {
        $plate = self::normalizePlate($meta['plate'] ?? '');
        if ($plate !== '') {
            return $plate;
        }
        $base = explode('#', $mapKey, 2)[0];

        return str_starts_with($base, 'NOPLATE_') ? '' : self::normalizePlate($base);
    }

    public static function normalizePlate(?string $plate): string
    {
        return strtoupper(preg_replace('/[^A-Z0-9]/', '', (string) $plate) ?? '');
    }

    /**
     * Rows listed under an Excel month section that matches the filter month, but whose
     * RELEASE DATE is outside the filter (common on Annex sheets).
     *
     * @param  array<int, array<string, mixed>>  $rows
     * @param  array<string, array<string, mixed>>  $countedByPlate
     * @return array<int, array<string, mixed>>
     */
    protected static function sectionBleedRows(array $rows, string $releaseFrom, string $releaseTo, array $countedByPlate): array
    {
        $monthKey = self::filterMonthKey($releaseFrom, $releaseTo);
        if ($monthKey === null) {
            return [];
        }

        [$year, $month] = array_map('intval', explode('-', $monthKey));

        $bleed = [];
        foreach ($rows as $row) {
            $plate = self::normalizePlate($row['plate'] ?? '');
            if ($plate === '' || isset($countedByPlate[$plate])) {
                continue;
            }
            $section = strtoupper((string) ($row['section'] ?? ''));
            if ($section === '' || ! self::sectionMatchesMonth($section, $year, $month)) {
                continue;
            }
            $d = $row['release_date'] ?? null;
            if (! $d) {
                $bleed[] = $row;
                continue;
            }
            if ($releaseFrom !== '' && $d >= $releaseFrom && ($releaseTo === '' || $d <= $releaseTo)) {
                continue;
            }
            $bleed[] = $row;
        }

        return $bleed;
    }

    /**
     * When from/to span exactly one calendar month, return Y-m; otherwise null.
     */
    protected static function filterMonthKey(string $releaseFrom, string $releaseTo): ?string
    {
        if ($releaseFrom === '' || $releaseTo === '') {
            return null;
        }
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $releaseFrom) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $releaseTo)) {
            return null;
        }
        try {
            $from = \Carbon\Carbon::createFromFormat('Y-m-d', $releaseFrom)->startOfDay();
            $to = \Carbon\Carbon::createFromFormat('Y-m-d', $releaseTo)->startOfDay();
        } catch (\Throwable $e) {
            return null;
        }
        if ($from->format('Y-m') !== $to->format('Y-m')) {
            return null;
        }
        if ($from->day !== 1 || $to->day !== $to->copy()->endOfMonth()->day) {
            return null;
        }

        return $from->format('Y-m');
    }

    /**
     * Whether an Excel section heading belongs to the filtered calendar month.
     * Flagship headings often omit the year (those are 2024); later years use "(2025)" / "2026".
     */
    protected static function sectionMatchesMonth(string $section, int $year, int $month): bool
    {
        $section = strtoupper(trim($section));
        if ($section === '') {
            return false;
        }

        $monthNames = [
            1 => ['JANUARY', 'JAN'],
            2 => ['FEBRUARY', 'FEBUARY', 'FEB'],
            3 => ['MARCH', 'MAR'],
            4 => ['APRIL', 'APR'],
            5 => ['MAY'],
            6 => ['JUNE', 'JUN'],
            7 => ['JULY', 'JUL'],
            8 => ['AUGUST', 'AUG'],
            9 => ['SEPTEMBER', 'SEPT', 'SEP'],
            10 => ['OCTOBER', 'OCT'],
            11 => ['NOVEMBER', 'NOV'],
            12 => ['DECEMBER', 'DEC'],
        ];
        $needles = $monthNames[$month] ?? [];
        $monthHit = false;
        foreach ($needles as $needle) {
            if ($needle === 'MAY') {
                if (preg_match('/\bMAY\b/', $section)) {
                    $monthHit = true;
                    break;
                }
                continue;
            }
            if (str_contains($section, $needle)) {
                $monthHit = true;
                break;
            }
        }
        if (! $monthHit) {
            return false;
        }

        preg_match_all('/\b(20\d{2})\b/', $section, $matches);
        $years = $matches[1] ?? [];
        if ($years === []) {
            return $year === 2024;
        }

        return in_array((string) $year, $years, true);
    }

    /**
     * Expected Released count for a branch in a calendar month, if compiled from Excel sales.
     */
    protected static function expectedReleasedSales(?string $monthKey, string $branch): ?int
    {
        if ($monthKey === null || $monthKey === '' || $branch === '') {
            return null;
        }
        foreach (self::EXPECTED_RELEASED_SALES[$monthKey] ?? [] as $name => $count) {
            if (strcasecmp($name, $branch) === 0) {
                return (int) $count;
            }
        }

        return null;
    }

    /**
     * Sum Excel TOTAL REVENUE / TOTAL COSTS for rows listed under each Released sheet's
     * month section heading (the same block Excel uses for monthly unit-revenue totals).
     *
     * @return array<string, array{units:int,total_revenue:float,total_costs:float}>|null
     */
    public static function sectionFinancialsForMonth(string $releaseFrom, string $releaseTo): ?array
    {
        $monthKey = self::filterMonthKey($releaseFrom, $releaseTo);
        if ($monthKey === null) {
            return null;
        }
        $snapshot = self::loadSnapshot();
        if (! $snapshot) {
            return null;
        }
        [$year, $month] = array_map('intval', explode('-', $monthKey));
        $out = [];
        foreach ($snapshot['designations'] ?? [] as $designation) {
            if (($designation['status'] ?? null) !== 'Released') {
                continue;
            }
            $branch = trim((string) ($designation['branch'] ?? ''));
            if ($branch === '') {
                continue;
            }
            $seen = [];
            $units = 0;
            $revenue = 0.0;
            $costs = 0.0;
            foreach ($designation['rows'] ?? [] as $row) {
                $section = strtoupper((string) ($row['section'] ?? ''));
                if (! self::sectionMatchesMonth($section, $year, $month)) {
                    continue;
                }
                $excelRow = (int) ($row['excel_row'] ?? 0);
                $seenKey = $excelRow > 0
                    ? 'R'.$excelRow
                    : (self::normalizePlate($row['plate'] ?? '') ?: 'NOPLATE_R0');
                if (isset($seen[$seenKey])) {
                    continue;
                }
                $seen[$seenKey] = true;
                $units++;
                $revenue += (float) ($row['total_revenue'] ?? 0);
                $costs += (float) ($row['total_costs'] ?? 0);
            }
            $out[$branch] = [
                'units' => $units,
                'total_revenue' => $revenue,
                'total_costs' => $costs,
            ];
        }

        return $out === [] ? null : $out;
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

    /**
     * Build an unsaved Vehicle stand-in for an Excel-only release row (missing plate / missing in DB).
     *
     * @param  array<string, mixed>  $meta
     */
    public static function makeExcelOnlyVehicle(string $plateKey, array $meta, string $status = 'Released'): Vehicle
    {
        $excelRow = (int) ($meta['excel_row'] ?? 0);
        $missingPlate = ! empty($meta['missing_plate']);
        $displayPlate = $missingPlate
            ? ''
            : (string) ($meta['plate_raw'] ?? $plateKey);

        $vehicle = new Vehicle([
            'year' => $meta['year'] ?? null,
            'make' => $meta['make'] ?? null,
            'model' => $meta['model'] ?? null,
            'variant' => $meta['variant'] ?? null,
            'transmission' => $meta['transmission'] ?? null,
            'fuel_type' => $meta['fuel_type'] ?? null,
            'colour' => $meta['colour'] ?? null,
            'plate_number' => $displayPlate !== '' ? $displayPlate : null,
            'status' => $status,
            'purchase_price' => $meta['purchase_price'] ?? null,
            'sold_price' => $meta['sales_price'] ?? null,
        ]);
        // Negative synthetic id keeps modals unique without colliding with real vehicles.
        $vehicle->id = $excelRow > 0 ? (0 - $excelRow) : -1;
        $vehicle->exists = false;
        $vehicle->setRelation('forfeitDetails', collect());
        $vehicle->setRelation('primaryImage', null);
        $vehicle->setRelation('branchLocation', null);
        $vehicle->setRelation('statusDetail', null);
        $vehicle->setRelation('make', null);
        $vehicle->setRelation('vehicleModel', null);

        $vehicle->setAttribute('excel_only', true);
        $vehicle->setAttribute('excel_plate_key', $plateKey);
        $vehicle->setAttribute('excel_missing_plate', $missingPlate);
        $vehicle->setAttribute('excel_period_release_date', $meta['release_date'] ?? null);
        $vehicle->setAttribute('excel_period_row', $meta['excel_row'] ?? null);
        $vehicle->setAttribute('excel_source_tab', $meta['tab'] ?? null);
        $vehicle->setAttribute('excel_period_branch', $meta['branch'] ?? null);
        $vehicle->setAttribute(
            'excel_period_branches',
            $meta['branches'] ?? array_values(array_filter([$meta['branch'] ?? null]))
        );
        $vehicle->setAttribute('excel_period_total_revenue', $meta['total_revenue'] ?? null);
        $vehicle->setAttribute('excel_period_total_costs', $meta['total_costs'] ?? null);
        $vehicle->setAttribute('excel_period_total_profit', $meta['total_profit'] ?? null);
        $vehicle->setAttribute('excel_period_sales_price', $meta['sales_price'] ?? null);
        $vehicle->setAttribute('excel_period_branch_financials', $meta['branch_financials'] ?? []);
        $vehicle->setAttribute('excel_aligned', true);
        $vehicle->setAttribute('excel_forced_section_add', ! empty($meta['forced_section_add']) || ! empty($meta['forced_rerelease_add']));
        $vehicle->setAttribute('excel_period_purchase_price', $meta['purchase_price'] ?? null);

        return $vehicle;
    }
}
