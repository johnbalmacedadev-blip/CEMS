<?php

namespace App\Support;

use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

/**
 * Upload / analyze / import AVAILABLE-RESERVED-RELEASED style workbooks.
 */
class VehicleUnitsExcelImporter
{
    public const TAB_MAP = [
        'FLAGSHIP - UNITS' => [
            'key' => 'flagship_available',
            'status' => 'Available',
            'branch' => 'Flagship',
            'command' => 'import:available-units',
            'tables' => ['vehicles', 'vehicle_expenses', 'vehicle_status_details'],
            'layout' => 'units',
        ],
        'ANNEX - UNITS' => [
            'key' => 'annex_available',
            'status' => 'Available',
            'branch' => 'Annex',
            'command' => 'import:available-units',
            'tables' => ['vehicles', 'vehicle_expenses', 'vehicle_status_details'],
            'layout' => 'units',
        ],
        'FLAGSHIP - RESERVED' => [
            'key' => 'flagship_reserved',
            'status' => 'Reserved',
            'branch' => 'Flagship',
            'command' => 'import:reserved-units',
            'tables' => ['vehicles', 'vehicle_expenses', 'vehicle_status_details'],
            'layout' => 'units',
        ],
        'ANNEX - RESERVED' => [
            'key' => 'annex_reserved',
            'status' => 'Reserved',
            'branch' => 'Annex',
            'command' => 'import:reserved-units',
            'tables' => ['vehicles', 'vehicle_expenses', 'vehicle_status_details'],
            'layout' => 'units',
        ],
        'FLAGSHIP - RELEASED' => [
            'key' => 'flagship_released',
            'status' => 'Released',
            'branch' => 'Flagship',
            'command' => 'import:released-units',
            'tables' => ['vehicles', 'vehicle_expenses', 'vehicle_status_details'],
            'layout' => 'units',
        ],
        'ANNEX RELEASED' => [
            'key' => 'annex_released',
            'status' => 'Released',
            'branch' => 'Annex',
            'command' => 'import:released-units',
            'tables' => ['vehicles', 'vehicle_expenses', 'vehicle_status_details'],
            'layout' => 'units',
        ],
        'ANNEX - RELEASED' => [
            'key' => 'annex_released',
            'status' => 'Released',
            'branch' => 'Annex',
            'command' => 'import:released-units',
            'tables' => ['vehicles', 'vehicle_expenses', 'vehicle_status_details'],
            'layout' => 'units',
        ],
        'FLAGSHIP- FORFEITREFUND' => [
            'key' => 'flagship_forfeited',
            'status' => 'Forfeited',
            'branch' => 'Flagship',
            'command' => 'import:forfeited-units',
            'tables' => ['vehicles', 'vehicle_forfeit_details'],
            'layout' => 'forfeit',
        ],
    ];

    /** Lower runs first so Released always overwrites Available / Reserved / Forfeited. */
    public const STATUS_IMPORT_ORDER = [
        'Available' => 10,
        'Reserved' => 20,
        'Forfeited' => 30,
        'Released' => 40,
    ];

    public static function resolveTabMeta(string $name): ?array
    {
        if (isset(self::TAB_MAP[$name])) {
            return self::TAB_MAP[$name];
        }

        $compact = strtoupper(trim(preg_replace('/[\s\-]+/', ' ', $name) ?? ''));
        foreach (self::TAB_MAP as $key => $meta) {
            $keyCompact = strtoupper(trim(preg_replace('/[\s\-]+/', ' ', $key) ?? ''));
            if ($keyCompact === $compact) {
                return $meta;
            }
        }

        return null;
    }

    public static function storeUpload($uploadedFile): array
    {
        $dir = storage_path('app/imports');
        File::ensureDirectoryExists($dir);

        $token = (string) Str::uuid();
        $filename = $token.'.xlsx';
        $uploadedFile->move($dir, $filename);
        $path = $dir.DIRECTORY_SEPARATOR.$filename;

        $sheets = self::listSheets($path);

        return [
            'token' => $token,
            'path' => $path,
            'original_name' => method_exists($uploadedFile, 'getClientOriginalName')
                ? $uploadedFile->getClientOriginalName()
                : $filename,
            'sheets' => $sheets,
        ];
    }

    public static function pathForToken(string $token): string
    {
        $path = storage_path('app/imports/'.$token.'.xlsx');
        if (! is_file($path)) {
            throw new \RuntimeException('Import file expired or not found. Please upload again.');
        }

        return $path;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function listSheets(string $path): array
    {
        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        $out = [];

        foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
            $name = $worksheet->getTitle();
            $meta = self::resolveTabMeta($name);
            $rowCount = self::countPlateRows($worksheet, $meta['layout'] ?? 'units');
            $out[] = [
                'name' => $name,
                'supported' => $meta !== null,
                'key' => $meta['key'] ?? null,
                'status' => $meta['status'] ?? null,
                'branch' => $meta['branch'] ?? null,
                'tables' => $meta['tables'] ?? [],
                'excel_rows' => $rowCount,
                'note' => $meta ? null : 'This tab is not mapped for Unit Report import (skipped).',
            ];
        }

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return $out;
    }

    /**
     * Analyze selected sheets without writing.
     *
     * @param  array<int, string>  $sheetNames
     * @return array<string, mixed>
     */
    public static function analyze(string $token, array $sheetNames): array
    {
        $path = self::pathForToken($token);
        $parsed = self::parseSelectedSheets($path, $sheetNames);
        $vehicleByPlate = self::vehicleIndex();

        $tabs = [];
        $tables = [];
        $totals = [
            'excel_rows' => 0,
            'unique_plates' => 0,
            'will_create_vehicles' => 0,
            'will_update_vehicles' => 0,
            'will_skip' => 0,
        ];

        foreach ($parsed as $tab) {
            $create = 0;
            $update = 0;
            $skip = 0;
            $seen = [];

            foreach ($tab['rows'] as $row) {
                $plate = self::normalizePlate($row['plate_number'] ?? '');
                if ($plate === '' || isset($seen[$plate])) {
                    if ($plate !== '' && isset($seen[$plate])) {
                        $skip++;
                    }
                    continue;
                }
                $seen[$plate] = true;
                if (isset($vehicleByPlate[$plate])) {
                    $update++;
                } else {
                    $create++;
                }
            }

            $unique = count($seen);
            $tabs[] = [
                'name' => $tab['name'],
                'status' => $tab['meta']['status'],
                'branch' => $tab['meta']['branch'],
                'excel_rows' => count($tab['rows']),
                'unique_plates' => $unique,
                'will_create_vehicles' => $create,
                'will_update_vehicles' => $update,
                'duplicate_rows_in_sheet' => max(0, count($tab['rows']) - $unique),
                'tables' => $tab['meta']['tables'],
                'command' => $tab['meta']['command'],
            ];

            foreach ($tab['meta']['tables'] as $table) {
                $tables[$table] = ($tables[$table] ?? 0) + $unique;
            }

            $totals['excel_rows'] += count($tab['rows']);
            $totals['unique_plates'] += $unique;
            $totals['will_create_vehicles'] += $create;
            $totals['will_update_vehicles'] += $update;
            $totals['will_skip'] += $skip;
        }

        $tableSummary = [];
        foreach ($tables as $table => $approx) {
            $tableSummary[] = [
                'table' => $table,
                'action' => 'insert / update (upsert by plate)',
                'approx_rows_touched' => $approx,
                'description' => match ($table) {
                    'vehicles' => 'Unit master records (status, showroom, prices, specs)',
                    'vehicle_expenses' => 'Repair / expense summary columns from Excel',
                    'vehicle_status_details' => 'Sale / reservation / release / customer details',
                    'vehicle_forfeit_details' => 'Forfeit / refund detail rows',
                    default => 'Related import data',
                },
            ];
        }

        return [
            'token' => $token,
            'tabs' => $tabs,
            'tables' => $tableSummary,
            'totals' => $totals,
            'force' => true,
            'notes' => [
                'Existing plates will be updated (including status changes).',
                'Plates found on a Released sheet are set to Released and are not left as Available or Reserved.',
                'Released sheets keep the newest Excel release date when the same plate appears more than once.',
                'After import, Excel reconcile snapshot should be refreshed for mismatch notes.',
            ],
        ];
    }

    /**
     * @param  array<int, string>  $sheetNames
     * @return array<string, mixed>
     */
    public static function import(string $token, array $sheetNames): array
    {
        $path = self::pathForToken($token);
        $parsed = self::parseSelectedSheets($path, $sheetNames);
        $results = [];

        usort($parsed, function ($a, $b) {
            $oa = self::STATUS_IMPORT_ORDER[$a['meta']['status'] ?? ''] ?? 50;
            $ob = self::STATUS_IMPORT_ORDER[$b['meta']['status'] ?? ''] ?? 50;

            return $oa <=> $ob;
        });

        $releasedPlates = [];
        foreach ($parsed as $tab) {
            if (($tab['meta']['status'] ?? '') !== 'Released') {
                continue;
            }
            foreach ($tab['rows'] as $row) {
                $plate = self::normalizePlate($row['plate_number'] ?? '');
                if ($plate !== '') {
                    $releasedPlates[$plate] = true;
                }
            }
        }

        $tmpDir = storage_path('app/imports/'.$token.'_json');
        File::ensureDirectoryExists($tmpDir);

        foreach ($parsed as $tab) {
            $rows = $tab['rows'];
            $skippedReleasedOverlap = 0;
            if (
                in_array($tab['meta']['command'], ['import:available-units', 'import:reserved-units'], true)
                && $releasedPlates !== []
            ) {
                $kept = [];
                foreach ($rows as $row) {
                    $plate = self::normalizePlate($row['plate_number'] ?? '');
                    if ($plate !== '' && isset($releasedPlates[$plate])) {
                        $skippedReleasedOverlap++;
                        continue;
                    }
                    $kept[] = $row;
                }
                $rows = $kept;
            }

            if ($rows === []) {
                $results[] = [
                    'sheet' => $tab['name'],
                    'command' => $tab['meta']['command'],
                    'branch' => $tab['meta']['branch'],
                    'status' => $tab['meta']['status'],
                    'rows' => 0,
                    'skipped_released_overlap' => $skippedReleasedOverlap,
                    'exit_code' => 0,
                    'output' => $skippedReleasedOverlap > 0
                        ? "Skipped {$skippedReleasedOverlap} plate(s) also present on a Released sheet so status stays Released."
                        : 'No rows to import.',
                ];
                continue;
            }

            $jsonPath = $tmpDir.DIRECTORY_SEPARATOR.$tab['meta']['key'].'.json';
            File::put($jsonPath, json_encode($rows, JSON_UNESCAPED_UNICODE));

            $params = [
                '--file' => $jsonPath,
                '--branch' => $tab['meta']['branch'],
            ];
            if (in_array($tab['meta']['command'], ['import:available-units', 'import:reserved-units', 'import:forfeited-units'], true)) {
                $params['--force'] = true;
            }

            $exit = Artisan::call($tab['meta']['command'], $params);
            $output = trim(Artisan::output());
            if ($skippedReleasedOverlap > 0) {
                $output = trim($output."\nSkipped {$skippedReleasedOverlap} plate(s) also present on a Released sheet.");
            }
            $results[] = [
                'sheet' => $tab['name'],
                'command' => $tab['meta']['command'],
                'branch' => $tab['meta']['branch'],
                'status' => $tab['meta']['status'],
                'rows' => count($rows),
                'skipped_released_overlap' => $skippedReleasedOverlap,
                'exit_code' => $exit,
                'output' => $output,
            ];
        }

        // Refresh reconcile snapshot from this workbook when possible.
        try {
            self::refreshSnapshotFromWorkbook($path);
        } catch (\Throwable $e) {
            // non-fatal
        }

        return [
            'ok' => collect($results)->every(fn ($r) => (int) $r['exit_code'] === 0),
            'results' => $results,
        ];
    }

    /**
     * @param  array<int, string>  $sheetNames
     * @return array<int, array<string, mixed>>
     */
    protected static function parseSelectedSheets(string $path, array $sheetNames): array
    {
        $wanted = array_values(array_filter($sheetNames, fn ($n) => self::resolveTabMeta($n) !== null));
        if ($wanted === []) {
            throw new \InvalidArgumentException('Select at least one supported Excel tab.');
        }

        $reader = IOFactory::createReader('Xlsx');
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        $parsed = [];

        foreach ($wanted as $name) {
            $worksheet = $spreadsheet->getSheetByName($name);
            if (! $worksheet) {
                continue;
            }
            $meta = self::resolveTabMeta($name);
            $rows = $meta['layout'] === 'forfeit'
                ? self::extractForfeitRows($worksheet)
                : self::extractUnitRows($worksheet);

            $parsed[] = [
                'name' => $name,
                'meta' => $meta,
                'rows' => $rows,
            ];
        }

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        return $parsed;
    }

    protected static function countPlateRows($worksheet, string $layout): int
    {
        $highest = (int) $worksheet->getHighestDataRow();
        $count = 0;
        $plateCol = $layout === 'forfeit' ? 6 : 9;
        for ($r = 2; $r <= $highest; $r++) {
            $plate = self::normalizePlate((string) $worksheet->getCellByColumnAndRow($plateCol, $r)->getValue());
            if ($plate !== '' && self::looksLikePlate($plate)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected static function extractUnitRows($worksheet): array
    {
        $headers = [];
        $highestCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($worksheet->getHighestDataColumn());
        for ($c = 1; $c <= $highestCol; $c++) {
            $raw = $worksheet->getCellByColumnAndRow($c, 1)->getValue();
            $h = self::normHeader($raw);
            if ($h !== '') {
                $headers[$h] = $c;
            }
        }

        $col = function (string ...$needles) use ($headers): ?int {
            foreach ($needles as $needle) {
                foreach ($headers as $h => $c) {
                    if (str_contains($h, $needle)) {
                        return $c;
                    }
                }
            }

            return null;
        };

        $map = [
            'year' => $col('YEAR'),
            'make' => $col('MAKE'),
            'model' => $col('MODEL'),
            'variant' => $col('VARIANT'),
            'transmission' => $col('TRANSMISSION'),
            'fuel_type' => $col('FUEL'),
            'kilometers' => $col('KILOMETER'),
            'plate_number' => $col('PLATE'),
            'colour' => $col('COLOR', 'COLOUR'),
            'with_tools' => $col('WITH TOOLS'),
            'with_matting' => $col('WITH MATTING'),
            'with_spare_tire' => $col('WITH SPARE'),
            'purchase_price' => $col('PURCHASE PRICE'),
            'purchased_from' => $col('PURCHASED FROM'),
            'purchase_date' => $col('FORMATTED PURCHASE DATE', 'PURCHASE DATE'),
            'spare_key' => $col('SPARE KEY'),
            'paint_items' => $col('PAINT ITEMS'),
            'paint_costs' => $col('PAINT COSTS', 'PAINT COST'),
            'mechanical_electrical_items' => null,
            'mechanical_electrical_costs' => null,
            'cluster_items' => $col('SCANNER ITEMS', 'CLUSTER ITEMS'),
            'cluster_costs' => $col('SCANNER COSTS', 'SCANNER COST', 'CLUSTER COSTS'),
            'aircon_items' => $col('AIRCON ITEMS'),
            'aircon_cost' => $col('AIRCON COST', 'AIRCON COSTS'),
            'interior_items' => $col('INTERIOR ITEMS'),
            'interior_costs' => $col('INTERIOR COSTS', 'INTERIOR COST'),
            'papers_items' => $col('PAPERS ITEMS'),
            'papers_costs' => $col('PAPERS COSTS', 'PAPERS COST'),
            'tyres_battery_items' => null,
            'tyres_battery_cost' => null,
            'misc_items' => $col('MISC ITEMS'),
            'misc_costs' => $col('MISC COSTS', 'MISC COST'),
            'total_repair_items' => $col('TOTAL REPAIR ITEMS'),
            'total_repair_cost' => $col('TOTAL REPAIR COST'),
            'posted_price' => $col('POSTED - PRICE', 'POSTED PRICE'),
            'showroom' => $col('SHOWROOM'),
            'transfer_cost' => $col('TRANSFER COST', 'TRANSFER'),
            'agent_cost' => $col('AGENT COST'),
            'finance_revenue_1' => $col('FINANCE REVENUE 1'),
            'finance_revenue_2' => $col('FINANCE REVENUE 2'),
            'sale_date' => $col('FORMATTED SALE DATE', 'SALE DATE'),
            'release_date' => $col('FORMATTED RELEASE DATE', 'RELEASE DATE'),
            'sales_price' => $col('SALES PRICE', 'SALE PRICE'),
            'sale_reservation_amount' => $col('RESERVATION AMOUNT', 'SALE RESERVATION'),
            'sales_person_reserved' => $col('SALES PERSON (RESERVED)', 'SALES PERSON RESERVED'),
            'sales_person_release' => $col('SALES PERSON (RELEASE)', 'SALES PERSON RELEASE'),
            'cash_financing_raw' => $col('CASH/FINANCING', 'CASH FINANCING'),
            'sale_origin' => $col('SALE ORIGIN'),
            'insurance_raw' => $col('INSURANCE'),
            'customer_first_name' => $col('CUSTOMER FIRST', 'FIRST NAME'),
            'customer_last_name' => $col('CUSTOMER LAST', 'LAST NAME'),
            'customer_date_of_birth' => $col('DATE OF BIRTH', 'DOB'),
            'customer_gender' => $col('GENDER'),
            'customer_location' => $col('CUSTOMER LOCATION', 'LOCATION'),
            'customer_purpose' => $col('PURPOSE'),
            'days_from_reservation_to_release' => $col('DAYS FROM RESERVATION TO RELEASE'),
            'good_sales_review' => $col('GOOD SALES REVIEW'),
            'post_reservation_repairs' => $col('POST RESERVATION REPAIR'),
            'total_capital_repair_capital_posted' => $col('TOTAL CAPITAL'),
        ];

        foreach ($headers as $h => $c) {
            if (str_contains($h, 'MECHANICAL') && str_contains($h, 'ITEM')) {
                $map['mechanical_electrical_items'] = $c;
            } elseif (str_contains($h, 'MECHANICAL') && str_contains($h, 'COST')) {
                $map['mechanical_electrical_costs'] = $c;
            } elseif ((str_contains($h, 'TYRE') || str_contains($h, 'BATTERY')) && str_contains($h, 'ITEM')) {
                $map['tyres_battery_items'] = $c;
            } elseif ((str_contains($h, 'TYRE') || str_contains($h, 'BATTERY')) && str_contains($h, 'COST')) {
                $map['tyres_battery_cost'] = $c;
            }
        }

        if (! $map['plate_number']) {
            $map['plate_number'] = 9;
        }

        $rows = [];
        $highest = (int) $worksheet->getHighestDataRow();
        for ($r = 2; $r <= $highest; $r++) {
            $plateRaw = $worksheet->getCellByColumnAndRow($map['plate_number'], $r)->getValue();
            $plate = self::normalizePlate((string) $plateRaw);
            if ($plate === '' || ! self::looksLikePlate($plate)) {
                continue;
            }

            $get = function (?int $c) use ($worksheet, $r) {
                if (! $c) {
                    return null;
                }

                return $worksheet->getCellByColumnAndRow($c, $r)->getCalculatedValue();
            };

            $yearRaw = $get($map['year']);
            $year = self::parseYear($yearRaw);
            $make = trim((string) ($get($map['make']) ?? ''));
            if ($make === '') {
                continue;
            }

            $rows[] = [
                'row' => $r,
                'year' => $year,
                'year_raw' => is_string($yearRaw) ? $yearRaw : null,
                'make' => $make,
                'model' => trim((string) ($get($map['model']) ?? 'Unknown')) ?: 'Unknown',
                'variant' => self::nullableString($get($map['variant'])),
                'transmission' => trim((string) ($get($map['transmission']) ?? 'AUTOMATIC')),
                'fuel_type' => trim((string) ($get($map['fuel_type']) ?? 'GAS')),
                'kilometers' => (int) self::toNumber($get($map['kilometers'])),
                'plate_number' => $plate,
                'colour' => self::nullableString($get($map['colour'])),
                'with_tools' => self::toBool($get($map['with_tools'])),
                'with_matting' => self::toBool($get($map['with_matting'])),
                'with_spare_tire' => self::toBool($get($map['with_spare_tire'])),
                'purchase_price' => self::toNumber($get($map['purchase_price'])),
                'purchased_from' => self::nullableString($get($map['purchased_from'])),
                'purchase_date' => self::toDate($get($map['purchase_date'])),
                'spare_key' => self::toBool($get($map['spare_key'])),
                'paint_items' => self::nullableString($get($map['paint_items'])),
                'paint_costs' => self::toNumber($get($map['paint_costs'])),
                'mechanical_electrical_items' => self::nullableString($get($map['mechanical_electrical_items'])),
                'mechanical_electrical_costs' => self::toNumber($get($map['mechanical_electrical_costs'])),
                'cluster_items' => self::nullableString($get($map['cluster_items'])),
                'cluster_costs' => self::toNumber($get($map['cluster_costs'])),
                'aircon_items' => self::nullableString($get($map['aircon_items'])),
                'aircon_cost' => self::toNumber($get($map['aircon_cost'])),
                'interior_items' => self::nullableString($get($map['interior_items'])),
                'interior_costs' => self::toNumber($get($map['interior_costs'])),
                'papers_items' => self::nullableString($get($map['papers_items'])),
                'papers_costs' => self::toNumber($get($map['papers_costs'])),
                'tyres_battery_items' => self::nullableString($get($map['tyres_battery_items'])),
                'tyres_battery_cost' => self::toNumber($get($map['tyres_battery_cost'])),
                'misc_items' => self::nullableString($get($map['misc_items'])),
                'misc_costs' => self::toNumber($get($map['misc_costs'])),
                'total_repair_items' => self::nullableString($get($map['total_repair_items'])),
                'total_repair_cost' => self::toNumber($get($map['total_repair_cost'])),
                'posted_price' => self::toNumber($get($map['posted_price'])),
                'showroom' => self::nullableString($get($map['showroom'])),
                'transfer_cost' => self::toNumber($get($map['transfer_cost'])),
                'agent_cost' => self::toNumber($get($map['agent_cost'])),
                'finance_revenue_1' => self::toNumber($get($map['finance_revenue_1'])),
                'finance_revenue_2' => self::toNumber($get($map['finance_revenue_2'])),
                'sale_date' => self::toDate($get($map['sale_date'])),
                'release_date' => self::toDate($get($map['release_date'])),
                'sales_price' => self::toNumber($get($map['sales_price'])),
                'sale_reservation_amount' => self::toNumber($get($map['sale_reservation_amount'])),
                'sales_person_reserved' => self::nullableString($get($map['sales_person_reserved'])),
                'sales_person_release' => self::nullableString($get($map['sales_person_release'])),
                'cash_financing_raw' => self::nullableString($get($map['cash_financing_raw'])),
                'sale_origin' => self::nullableString($get($map['sale_origin'])),
                'insurance_raw' => self::nullableString($get($map['insurance_raw'])),
                'customer_first_name' => self::nullableString($get($map['customer_first_name'])),
                'customer_last_name' => self::nullableString($get($map['customer_last_name'])),
                'customer_date_of_birth' => self::toDate($get($map['customer_date_of_birth'])),
                'customer_gender' => self::nullableString($get($map['customer_gender'])),
                'customer_location' => self::nullableString($get($map['customer_location'])),
                'customer_purpose' => self::nullableString($get($map['customer_purpose'])),
                'days_from_reservation_to_release' => self::nullableNumber($get($map['days_from_reservation_to_release'])),
                'good_sales_review' => self::nullableString($get($map['good_sales_review'])),
                'post_reservation_repairs' => self::nullableString($get($map['post_reservation_repairs'])),
                'post_reservation_repairs_cost' => 0,
                'total_capital_repair_capital_posted' => self::toNumber($get($map['total_capital_repair_capital_posted'])),
            ];
        }

        return $rows;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected static function extractForfeitRows($worksheet): array
    {
        $rows = [];
        $highest = (int) $worksheet->getHighestDataRow();
        for ($r = 2; $r <= $highest; $r++) {
            $plate = self::normalizePlate((string) $worksheet->getCellByColumnAndRow(6, $r)->getValue());
            if ($plate === '' || ! self::looksLikePlate($plate)) {
                continue;
            }
            $rows[] = [
                'row' => $r,
                'sales_rep' => self::nullableString($worksheet->getCellByColumnAndRow(2, $r)->getValue()),
                'year' => self::parseYear($worksheet->getCellByColumnAndRow(3, $r)->getValue()),
                'make' => trim((string) ($worksheet->getCellByColumnAndRow(4, $r)->getValue() ?? 'Unknown')) ?: 'Unknown',
                'model' => trim((string) ($worksheet->getCellByColumnAndRow(5, $r)->getValue() ?? 'Unknown')) ?: 'Unknown',
                'plate_number' => $plate,
                'forfeit_amount' => self::toNumber($worksheet->getCellByColumnAndRow(7, $r)->getValue()),
                'date_reserved' => self::toDate($worksheet->getCellByColumnAndRow(9, $r)->getValue()),
                'forfeit_date' => self::toDate($worksheet->getCellByColumnAndRow(11, $r)->getValue()),
                'previous_forfeit_date' => null,
                'reason_of_forfeit' => null,
            ];
        }

        return $rows;
    }

    protected static function refreshSnapshotFromWorkbook(string $path): void
    {
        // Best-effort: if python export script exists, run it with this workbook.
        $script = base_path('scripts/export_excel_units_snapshot.py');
        if (! is_file($script)) {
            return;
        }
        $cmd = 'python '.escapeshellarg($script);
        $descriptors = [1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        $proc = proc_open($cmd, $descriptors, $pipes, base_path(), ['EXCEL_UNITS_SRC' => $path]);
        if (is_resource($proc)) {
            stream_get_contents($pipes[1]);
            stream_get_contents($pipes[2]);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($proc);
        }
    }

    /**
     * @return array<string, object>
     */
    protected static function vehicleIndex(): array
    {
        $map = [];
        foreach (Vehicle::query()->get(['id', 'plate_number', 'status']) as $vehicle) {
            $plate = self::normalizePlate($vehicle->plate_number);
            if ($plate !== '') {
                $map[$plate] = $vehicle;
            }
        }

        return $map;
    }

    protected static function normalizePlate(?string $plate): string
    {
        return strtoupper(preg_replace('/[^A-Z0-9]/i', '', (string) $plate) ?? '');
    }

    protected static function looksLikePlate(string $plate): bool
    {
        if (strlen($plate) < 3 || strlen($plate) > 15) {
            return false;
        }
        if (ctype_digit($plate) && strlen($plate) > 6) {
            return false;
        }

        return preg_match('/\d/', $plate) === 1;
    }

    protected static function normHeader($h): string
    {
        if ($h === null) {
            return '';
        }

        return preg_replace('/\s+/', ' ', strtoupper(str_replace("\n", ' ', (string) $h))) ?? '';
    }

    protected static function parseYear($value): int
    {
        if ($value === null || $value === '') {
            return 2000;
        }
        if (is_numeric($value)) {
            $y = (int) $value;

            return ($y >= 1980 && $y <= 2100) ? $y : 2000;
        }
        if (preg_match('/(19|20)\d{2}/', (string) $value, $m)) {
            return (int) $m[0];
        }

        return 2000;
    }

    protected static function toNumber($value): float
    {
        if ($value === null || $value === '') {
            return 0.0;
        }
        if (is_numeric($value)) {
            return (float) $value;
        }
        $s = strtoupper(str_replace([',', '₱', 'PHP', ' '], '', (string) $value));

        return is_numeric($s) ? (float) $s : 0.0;
    }

    protected static function nullableNumber($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return self::toNumber($value);
    }

    protected static function toBool($value): bool
    {
        if ($value === null) {
            return false;
        }

        return in_array(strtoupper(trim((string) $value)), ['YES', 'Y', 'TRUE', '1'], true);
    }

    protected static function nullableString($value): ?string
    {
        if ($value === null) {
            return null;
        }
        $s = trim((string) $value);

        return $s === '' ? null : $s;
    }

    protected static function toDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        try {
            if (is_numeric($value)) {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value))->toDateString();
            }
            $s = trim((string) $value);
            foreach (['Y-m-d', 'm/d/Y', 'm/d/y', 'd/m/Y', 'd/m/y'] as $fmt) {
                try {
                    return Carbon::createFromFormat($fmt, $s)->toDateString();
                } catch (\Throwable $e) {
                }
            }
            return Carbon::parse($s)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    }
}
