<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleExpense;
use App\Models\MiscellaneousTransaction;
use App\Models\SalesAgentCommission;
use App\Support\ExcelUnitReconcile;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AnalyticsReportController extends Controller
{
    protected array $financialReportOptions = [
        'reservations' => 'List and Count of Reservations',
        'releases' => 'List and Count of Releases',
        'gross_per_unit' => 'Total Gross Per Unit',
        'cost_per_unit' => 'Total Cost Per Unit',
        'net_per_unit' => 'Total Net Per Unit',
        'avg_gross_per_unit' => 'Avg Gross per Unit',
        'avg_cost_per_unit' => 'Avg Cost per Unit',
        'avg_net_per_unit' => 'Avg Net per Unit',
        'split_cash_financing' => 'Split Of Cash/Financing',
        'total_discount_cash' => 'Total Discount on Cash',
        'avg_discount_cash' => 'Avg Discount on Cash',
        'inventory_age_unsold' => 'Unit Inventory by Age (not sold)',
        'inventory_sales_age_sold' => 'Unit Inventory Sales by Age (sold)',
        'unit_repairs' => 'Unit Repairs',
    ];

    protected array $periodOptions = [
        'daily' => 'Daily',
        'weekly' => 'Weekly',
        'monthly' => 'Monthly',
        'quarterly' => 'Quarterly',
        'annually' => 'Annually',
        'range' => 'Custom Date Range',
    ];

    public function financial(Request $request)
    {
        return view('analytics-reports.financial', $this->buildFinancialMomPageData($request));
    }

    public function exportFinancial(Request $request)
    {
        // Legacy export endpoint kept for older bookmarks; Excel MoM page has no separate export yet.
        return redirect()->route('analytics-report.financial', $request->query());
    }

    protected function buildFinancialPageData(Request $request): array
    {
        $selected = (string) $request->get('report_type', 'reservations');
        if (! array_key_exists($selected, $this->financialReportOptions)) {
            $selected = 'reservations';
        }

        $period = (string) $request->get('period', 'monthly');
        if (! array_key_exists($period, $this->periodOptions)) {
            $period = 'monthly';
        }

        $monthlyView = (string) $request->get('monthly_view', 'grouped');
        if (! in_array($monthlyView, ['grouped', 'specific'], true)) {
            $monthlyView = 'grouped';
        }
        $selectedMonth = (string) $request->get('month_pick', '');

        $window = $this->resolveDateWindow($request, $period);
        if ($period === 'monthly' && $monthlyView === 'grouped') {
            // Monthly grouped mode should always summarize ALL months by default.
            $window = ['from' => null, 'to' => null];
        }
        if ($period === 'monthly' && $monthlyView === 'specific' && preg_match('/^\d{4}-\d{2}$/', $selectedMonth)) {
            $monthDate = Carbon::createFromFormat('Y-m', $selectedMonth);
            $window = [
                'from' => $monthDate->copy()->startOfMonth()->startOfDay(),
                'to' => $monthDate->copy()->endOfMonth()->endOfDay(),
            ];
        }
        $result = $this->buildFinancialReport($selected, $window);
        if ($period === 'monthly' && $monthlyView === 'grouped') {
            $result = $this->attachMonthlyGrouping($result);
        }

        return [
            'reportOptions' => $this->financialReportOptions,
            'selectedReport' => $selected,
            'selectedReportLabel' => $this->financialReportOptions[$selected] ?? $selected,
            'periodOptions' => $this->periodOptions,
            'selectedPeriod' => $period,
            'monthlyView' => $monthlyView,
            'selectedMonth' => $selectedMonth,
            'dateFrom' => $window['from'] ? $window['from']->format('Y-m-d') : '',
            'dateTo' => $window['to'] ? $window['to']->format('Y-m-d') : '',
            'activeRangeLabel' => $this->rangeLabel($window),
            'result' => $result,
            'statusCountMeta' => $this->statusCountMeta(),
        ];
    }

    public function sales(Request $request)
    {
        $data = $this->buildSalesPageData($request);

        return view('analytics-reports.sales', $data);
    }

    public function exportSales(Request $request)
    {
        $data = $this->buildSalesPageData($request);
        $baseName = 'car-sales-report-' . date('Y-m-d');

        $pdf = Pdf::loadView('analytics-reports.sales-export-pdf', $data)->setPaper('a4', 'landscape');

        return $pdf->download($baseName . '.pdf');
    }

    protected function buildSalesPageData(Request $request): array
    {
        $filterMode = (string) $request->get('filter_mode', '');
        if (! in_array($filterMode, ['period', 'car_type'], true)) {
            $filterMode = '';
        }

        // No results until the user picks a filter mode and clicks Update Report.
        $showResults = $filterMode !== '';

        $period = (string) $request->get('period', 'monthly');
        if (! array_key_exists($period, $this->periodOptions)) {
            $period = 'monthly';
        }

        $locationOptions = $this->salesLocationFilterOptions();
        $selectedLocation = trim((string) $request->get('location', ''));
        if ($selectedLocation !== '' && ! array_key_exists($selectedLocation, $locationOptions)) {
            $selectedLocation = '';
        }

        $carTypeOptions = [
            'make' => 'Sales by Make',
            'body_type' => 'Sales by Body Type',
            'model' => 'Sales by Model',
            'year_model' => 'Sales by Year Model',
        ];
        $selectedCarType = (string) $request->get('car_type', 'make');
        if (! array_key_exists($selectedCarType, $carTypeOptions)) {
            $selectedCarType = 'make';
        }
        $carSearch = trim((string) $request->get('car_search', ''));
        $activeExcelTab = (string) $request->get('excel_tab', 'by_model');
        $excelTabKeys = ['by_model', 'by_speed', 'by_make', 'by_body_type', 'by_year_model'];
        if (! in_array($activeExcelTab, $excelTabKeys, true)) {
            $activeExcelTab = 'by_model';
        }

        $selectedYear = $this->resolveSelectedYear($request);
        $monthRaw = $request->input('month', null);
        $selectedMonth = is_string($monthRaw) ? trim($monthRaw) : '';
        if ($selectedMonth !== '' && ! preg_match('/^(0?[1-9]|1[0-2])$/', $selectedMonth)) {
            $selectedMonth = '';
        }

        // Excel CAR SALES REPORTS use calendar month/year. Default car-type mode to monthly.
        if ($filterMode === 'car_type') {
            $period = 'monthly';
        }

        $window = $this->resolveDateWindow($request, $period);
        if ($period === 'monthly' && $selectedMonth !== '') {
            $monthNum = (int) $selectedMonth;
            $selectedMonth = sprintf('%02d', $monthNum);
            $window = [
                'from' => Carbon::create($selectedYear, $monthNum, 1)->startOfDay(),
                'to' => Carbon::create($selectedYear, $monthNum, 1)->endOfMonth()->endOfDay(),
            ];
        } elseif ($filterMode === 'car_type' && $period === 'monthly' && $selectedMonth === '') {
            // Whole selected year when month is "All months"
            $window = [
                'from' => Carbon::create($selectedYear, 1, 1)->startOfDay(),
                'to' => Carbon::create($selectedYear, 12, 31)->endOfDay(),
            ];
        }

        $analytics = [
            'summary' => [],
            'charts' => [],
            'tables' => [],
            'has_data' => false,
            'excel_aligned' => false,
        ];
        $carTypeReport = null;
        $excelTabs = null;

        if ($showResults && $filterMode === 'period') {
            $analytics = $this->buildSalesAnalytics($window, $selectedLocation);
        } elseif ($showResults && $filterMode === 'car_type') {
            $excelTabs = $this->buildExcelCarSalesTabs($window, $selectedLocation, $carSearch);
            // Keep single-table helper available for exports / legacy deep-links.
            $carTypeReport = $excelTabs['by_model'] ?? null;
        }

        return [
            'periodOptions' => $this->periodOptions,
            'selectedPeriod' => $period,
            'yearOptions' => $this->salesYearFilterOptions(),
            'selectedYear' => $selectedYear,
            'monthOptions' => [
                '' => 'All months in year',
                '01' => 'January',
                '02' => 'February',
                '03' => 'March',
                '04' => 'April',
                '05' => 'May',
                '06' => 'June',
                '07' => 'July',
                '08' => 'August',
                '09' => 'September',
                '10' => 'October',
                '11' => 'November',
                '12' => 'December',
            ],
            'selectedMonth' => $selectedMonth,
            'locationOptions' => $locationOptions,
            'selectedLocation' => $selectedLocation,
            'filterMode' => $filterMode,
            'showResults' => $showResults,
            'carTypeOptions' => $carTypeOptions,
            'selectedCarType' => $selectedCarType,
            'carSearch' => $carSearch,
            'carTypeReport' => $carTypeReport,
            'excelTabs' => $excelTabs,
            'activeExcelTab' => $activeExcelTab,
            'dateFrom' => $window['from'] ? $window['from']->format('Y-m-d') : '',
            'dateTo' => $window['to'] ? $window['to']->format('Y-m-d') : '',
            'activeRangeLabel' => $this->rangeLabel($window),
            'summary' => $analytics['summary'],
            'charts' => $analytics['charts'],
            'tables' => $analytics['tables'],
            'hasData' => (bool) ($analytics['has_data'] ?? false),
            'excelAlignedCounts' => (bool) ($analytics['excel_aligned'] ?? false),
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function salesLocationFilterOptions(): array
    {
        $options = ['' => 'All Locations'];
        foreach (\App\Models\BranchLocation::ordered()->get(['name']) as $branch) {
            $name = trim((string) $branch->name);
            if ($name !== '') {
                $options[$name] = $name;
            }
        }
        $options['No Location'] = 'No Location';

        return $options;
    }

    /**
     * Excel-style Car Sales tabular report by Make / Body Type / Model / Year Model.
     *
     * Columns: Rank, Label, Total Sales, % Sales, Total Releases, % Releases
     * Sales = units with sale_date in window; Releases = units with release_date in window.
     *
     * @return array{title:string,dimension_label:string,rows:array<int,array<string,mixed>>,totals:array<string,mixed>,has_data:bool}
     */
    protected function buildCarTypeSalesReport(
        array $window,
        string $carType,
        string $search = '',
        string $locationFilter = ''
    ): array {
        $dimensionMeta = match ($carType) {
            'body_type' => ['title' => 'Sales and Releases by Body Type', 'label' => 'Body Type'],
            'model' => ['title' => 'Sales and Releases by Model', 'label' => 'Model'],
            'year_model' => ['title' => 'Sales and Releases by Year Model', 'label' => 'Year Model'],
            default => ['title' => 'Sales and Releases by Make', 'label' => 'Make'],
        };

        $releaseVehicles = $this->soldVehiclesBase($window)
            ->filter(fn (Vehicle $v) => $this->vehicleMatchesSalesLocation($v, $locationFilter))
            ->values();

        $saleVehicles = Vehicle::with(['statusDetail', 'make', 'vehicleModel', 'branchLocation'])
            ->whereIn('status', ['Reserved', 'Released'])
            ->whereHas('statusDetail', fn ($q) => $q->whereNotNull('sale_date'))
            ->get()
            ->filter(function (Vehicle $v) use ($window, $locationFilter) {
                if (! $this->inWindow($v->statusDetail?->sale_date, $window)) {
                    return false;
                }

                return $this->vehicleMatchesSalesLocation($v, $locationFilter);
            })
            ->values();

        $salesBuckets = [];
        foreach ($saleVehicles as $vehicle) {
            $key = $this->carTypeDimensionKey($vehicle, $carType);
            $salesBuckets[$key] = ($salesBuckets[$key] ?? 0) + 1;
        }

        $releaseBuckets = [];
        foreach ($releaseVehicles as $vehicle) {
            $key = $this->carTypeDimensionKey($vehicle, $carType);
            $releaseBuckets[$key] = ($releaseBuckets[$key] ?? 0) + 1;
        }

        $labels = collect(array_unique(array_merge(array_keys($salesBuckets), array_keys($releaseBuckets))))
            ->filter(fn ($label) => $label !== '')
            ->values();

        if ($search !== '') {
            $needle = mb_strtolower($search);
            $labels = $labels->filter(fn ($label) => str_contains(mb_strtolower($label), $needle))->values();
        }

        $totalSales = (int) array_sum($salesBuckets);
        $totalReleases = (int) array_sum($releaseBuckets);
        // When searching, % should still be vs overall totals in the date window (Excel-style contribution).
        $pctSalesBase = max($totalSales, 0);
        $pctReleaseBase = max($totalReleases, 0);

        $rows = $labels
            ->map(function ($label) use ($salesBuckets, $releaseBuckets, $pctSalesBase, $pctReleaseBase) {
                $salesCount = (int) ($salesBuckets[$label] ?? 0);
                $releaseCount = (int) ($releaseBuckets[$label] ?? 0);

                return [
                    'label' => $label,
                    'sales_count' => $salesCount,
                    'sales_pct' => $pctSalesBase > 0 ? round(($salesCount / $pctSalesBase) * 100, 1) : 0.0,
                    'release_count' => $releaseCount,
                    'release_pct' => $pctReleaseBase > 0 ? round(($releaseCount / $pctReleaseBase) * 100, 1) : 0.0,
                ];
            })
            ->filter(fn (array $row) => ((int) $row['sales_count'] + (int) $row['release_count']) > 0)
            ->sortByDesc('release_count')
            ->values();

        $visibleSales = (int) $rows->sum('sales_count');
        $visibleReleases = (int) $rows->sum('release_count');

        return [
            'title' => $dimensionMeta['title'],
            'dimension_label' => $dimensionMeta['label'],
            'rows' => $rows->all(),
            'totals' => [
                'sales_count' => $search !== '' ? $visibleSales : $totalSales,
                'release_count' => $search !== '' ? $visibleReleases : $totalReleases,
            ],
            'has_data' => $rows->isNotEmpty(),
            'columns' => 'sales_releases',
        ];
    }

    /**
     * Excel "(5-9) CAR SALES REPORTS" — all five tables for the selected month/year window.
     * Labels come from live Make / Model / body_type / year data in the database.
     *
     * @return array<string, array<string, mixed>>
     */
    protected function buildExcelCarSalesTabs(array $window, string $locationFilter = '', string $search = ''): array
    {
        $releaseVehicles = $this->soldVehiclesBase($window)
            ->filter(fn (Vehicle $v) => $this->vehicleMatchesSalesLocation($v, $locationFilter))
            ->values();

        $saleVehicles = Vehicle::with(['statusDetail', 'make', 'vehicleModel', 'branchLocation'])
            ->whereIn('status', ['Reserved', 'Released'])
            ->whereHas('statusDetail', fn ($q) => $q->whereNotNull('sale_date'))
            ->get()
            ->filter(function (Vehicle $v) use ($window, $locationFilter) {
                if (! $this->inWindow($v->statusDetail?->sale_date, $window)) {
                    return false;
                }

                return $this->vehicleMatchesSalesLocation($v, $locationFilter);
            })
            ->values();

        $byModel = $this->buildSalesReleaseDimensionTable(
            'Sales and Releases by Model',
            'Model',
            $saleVehicles,
            $releaseVehicles,
            'model',
            $this->catalogModelLabels(),
            $search
        );

        $byMake = $this->buildSalesReleaseDimensionTable(
            'Sales and Releases by Make',
            'Make',
            $saleVehicles,
            $releaseVehicles,
            'make',
            $this->catalogMakeLabels(),
            $search
        );

        $byBody = $this->buildSalesReleaseDimensionTable(
            'Sales and Releases by Body Type',
            'Body Type',
            $saleVehicles,
            $releaseVehicles,
            'body_type',
            $this->catalogBodyTypeLabels(),
            $search
        );

        $byYear = $this->buildSalesReleaseDimensionTable(
            'Sales and Releases by Year Model',
            'Year Model',
            $saleVehicles,
            $releaseVehicles,
            'year_model',
            $this->catalogYearModelLabels(),
            $search
        );

        $bySpeed = $this->buildModelsBySpeedToSellTable($saleVehicles, $search);

        $hasData = (bool) (
            ($byModel['has_data'] ?? false)
            || ($bySpeed['has_data'] ?? false)
            || ($byMake['has_data'] ?? false)
            || ($byBody['has_data'] ?? false)
            || ($byYear['has_data'] ?? false)
        );

        return [
            'by_model' => $byModel,
            'by_speed' => $bySpeed,
            'by_make' => $byMake,
            'by_body_type' => $byBody,
            'by_year_model' => $byYear,
            'has_data' => $hasData,
        ];
    }

    /**
     * @param  Collection<int, Vehicle>  $saleVehicles
     * @param  Collection<int, Vehicle>  $releaseVehicles
     * @param  array<int, string>  $catalogLabels
     * @return array{title:string,dimension_label:string,rows:array<int,array<string,mixed>>,totals:array<string,mixed>,has_data:bool,columns:string}
     */
    protected function buildSalesReleaseDimensionTable(
        string $title,
        string $dimensionLabel,
        Collection $saleVehicles,
        Collection $releaseVehicles,
        string $carType,
        array $catalogLabels = [],
        string $search = ''
    ): array {
        $salesBuckets = [];
        foreach ($saleVehicles as $vehicle) {
            $key = $this->carTypeDimensionKey($vehicle, $carType);
            if ($key === '') {
                continue;
            }
            $salesBuckets[$key] = ($salesBuckets[$key] ?? 0) + 1;
        }

        $releaseBuckets = [];
        foreach ($releaseVehicles as $vehicle) {
            $key = $this->carTypeDimensionKey($vehicle, $carType);
            if ($key === '') {
                continue;
            }
            $releaseBuckets[$key] = ($releaseBuckets[$key] ?? 0) + 1;
        }

        $labels = collect(array_unique(array_merge(
            array_keys($salesBuckets),
            array_keys($releaseBuckets),
            $catalogLabels
        )))
            ->map(fn ($label) => trim((string) $label))
            ->filter(fn ($label) => $label !== '')
            ->unique(fn ($label) => mb_strtoupper($label))
            ->values();

        if ($search !== '') {
            $needle = mb_strtolower($search);
            $labels = $labels->filter(fn ($label) => str_contains(mb_strtolower($label), $needle))->values();
        }

        $totalSales = (int) array_sum($salesBuckets);
        $totalReleases = (int) array_sum($releaseBuckets);
        $pctSalesBase = max($totalSales, 0);
        $pctReleaseBase = max($totalReleases, 0);

        $rows = $labels
            ->map(function ($label) use ($salesBuckets, $releaseBuckets, $pctSalesBase, $pctReleaseBase) {
                $salesCount = (int) ($this->bucketValueForLabel($salesBuckets, $label));
                $releaseCount = (int) ($this->bucketValueForLabel($releaseBuckets, $label));

                return [
                    'label' => $label,
                    'sales_count' => $salesCount,
                    'sales_pct' => $pctSalesBase > 0 ? round(($salesCount / $pctSalesBase) * 100, 1) : 0.0,
                    'release_count' => $releaseCount,
                    'release_pct' => $pctReleaseBase > 0 ? round(($releaseCount / $pctReleaseBase) * 100, 1) : 0.0,
                ];
            })
            ->filter(fn (array $row) => ((int) $row['sales_count'] + (int) $row['release_count']) > 0)
            ->sort(function (array $a, array $b) {
                if ($a['release_count'] !== $b['release_count']) {
                    return $b['release_count'] <=> $a['release_count'];
                }
                if ($a['sales_count'] !== $b['sales_count']) {
                    return $b['sales_count'] <=> $a['sales_count'];
                }

                return strcasecmp($a['label'], $b['label']);
            })
            ->values();

        $visibleSales = (int) $rows->sum('sales_count');
        $visibleReleases = (int) $rows->sum('release_count');

        return [
            'title' => $title,
            'dimension_label' => $dimensionLabel,
            'rows' => $rows->all(),
            'totals' => [
                'sales_count' => $search !== '' ? $visibleSales : $totalSales,
                'release_count' => $search !== '' ? $visibleReleases : $totalReleases,
            ],
            'has_data' => $rows->isNotEmpty(),
            'columns' => 'sales_releases',
        ];
    }

    /**
     * Excel "MODELS BY SPEED TO SELL".
     *
     * @param  Collection<int, Vehicle>  $saleVehicles
     * @return array{title:string,dimension_label:string,rows:array<int,array<string,mixed>>,totals:array<string,mixed>,has_data:bool,columns:string}
     */
    protected function buildModelsBySpeedToSellTable(Collection $saleVehicles, string $search = ''): array
    {
        $salesBuckets = [];
        $daysBuckets = [];

        foreach ($saleVehicles as $vehicle) {
            $label = $this->vehicleModelLabel($vehicle);
            if ($label === '') {
                continue;
            }
            $salesBuckets[$label] = ($salesBuckets[$label] ?? 0) + 1;

            $saleDate = $vehicle->statusDetail?->sale_date
                ?? $vehicle->statusDetail?->release_date
                ?? $vehicle->getAttribute('excel_period_release_date');
            $purchase = $vehicle->purchase_date ?? $vehicle->created_at;
            if ($saleDate && $purchase) {
                $daysBuckets[$label][] = (float) Carbon::parse($purchase)->diffInDays(Carbon::parse($saleDate));
            }
        }

        $labels = collect(array_keys($salesBuckets))
            ->merge($this->catalogModelLabels())
            ->map(fn ($label) => trim((string) $label))
            ->filter(fn ($label) => $label !== '')
            ->unique(fn ($label) => mb_strtoupper($label))
            ->values();

        if ($search !== '') {
            $needle = mb_strtolower($search);
            $labels = $labels->filter(fn ($label) => str_contains(mb_strtolower($label), $needle))->values();
        }

        $allDays = [];
        $rows = $labels
            ->map(function ($label) use ($salesBuckets, $daysBuckets, &$allDays) {
                $salesCount = (int) ($this->bucketValueForLabel($salesBuckets, $label));
                $days = $this->bucketListForLabel($daysBuckets, $label);
                foreach ($days as $day) {
                    $allDays[] = $day;
                }
                $avgDays = count($days) > 0 ? round(array_sum($days) / count($days), 1) : 0.0;

                return [
                    'label' => $label,
                    'sales_count' => $salesCount,
                    'avg_days_to_sell' => $avgDays,
                ];
            })
            ->filter(fn ($row) => (int) $row['sales_count'] > 0)
            ->sort(function (array $a, array $b) {
                if ($a['sales_count'] !== $b['sales_count']) {
                    return $b['sales_count'] <=> $a['sales_count'];
                }

                return $a['avg_days_to_sell'] <=> $b['avg_days_to_sell'];
            })
            ->values();

        $totalSales = (int) $rows->sum('sales_count');

        return [
            'title' => 'Models by Speed to Sell',
            'dimension_label' => 'Model',
            'rows' => $rows->all(),
            'totals' => [
                'sales_count' => $totalSales,
                'avg_days_to_sell' => count($allDays) > 0 ? round(array_sum($allDays) / count($allDays), 1) : 0.0,
            ],
            'has_data' => $rows->isNotEmpty(),
            'columns' => 'speed',
        ];
    }

    /**
     * @param  array<string, int|float>  $buckets
     */
    protected function bucketValueForLabel(array $buckets, string $label): int|float
    {
        if (array_key_exists($label, $buckets)) {
            return $buckets[$label];
        }
        $needle = mb_strtoupper($label);
        foreach ($buckets as $key => $value) {
            if (mb_strtoupper((string) $key) === $needle) {
                return $value;
            }
        }

        return 0;
    }

    /**
     * @param  array<string, array<int, float>>  $buckets
     * @return array<int, float>
     */
    protected function bucketListForLabel(array $buckets, string $label): array
    {
        if (array_key_exists($label, $buckets) && is_array($buckets[$label])) {
            return $buckets[$label];
        }
        $needle = mb_strtoupper($label);
        foreach ($buckets as $key => $value) {
            if (mb_strtoupper((string) $key) === $needle && is_array($value)) {
                return $value;
            }
        }

        return [];
    }

    /**
     * @return array<int, string>
     */
    protected function catalogModelLabels(): array
    {
        return \App\Models\VehicleModel::query()
            ->where(function ($q) {
                $q->where('is_active', true)->orWhereNull('is_active');
            })
            ->orderBy('name')
            ->pluck('name')
            ->map(fn ($name) => trim((string) $name))
            ->filter(fn ($name) => $name !== '')
            ->unique(fn ($name) => mb_strtoupper($name))
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    protected function catalogMakeLabels(): array
    {
        return \App\Models\Make::query()
            ->where(function ($q) {
                $q->where('is_active', true)->orWhereNull('is_active');
            })
            ->orderBy('name')
            ->pluck('name')
            ->map(fn ($name) => trim((string) $name))
            ->filter(fn ($name) => $name !== '')
            ->unique(fn ($name) => mb_strtoupper($name))
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    protected function catalogBodyTypeLabels(): array
    {
        $fromDb = Vehicle::query()
            ->whereNotNull('body_type')
            ->where('body_type', '!=', '')
            ->distinct()
            ->orderBy('body_type')
            ->pluck('body_type')
            ->map(fn ($name) => trim((string) $name))
            ->filter(fn ($name) => $name !== '')
            ->all();

        $defaults = ['SEDAN', 'HATCHBACK', 'VAN', 'PICK UP', 'SUV/CROSSOVER', 'COUPE', 'SPORTS', 'WAGON'];

        return collect(array_merge($defaults, $fromDb))
            ->map(fn ($name) => trim((string) $name))
            ->filter(fn ($name) => $name !== '')
            ->unique(fn ($name) => mb_strtoupper($name))
            ->values()
            ->all();
    }

    /**
     * @return array<int, string>
     */
    protected function catalogYearModelLabels(): array
    {
        return Vehicle::query()
            ->whereNotNull('year')
            ->where('year', '!=', '')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn ($year) => trim((string) $year))
            ->filter(fn ($year) => $year !== '')
            ->unique()
            ->values()
            ->all();
    }

    protected function vehicleMatchesSalesLocation(Vehicle $v, string $locationFilter): bool
    {
        if ($locationFilter === '') {
            return true;
        }

        $locations = $v->getAttribute('excel_period_branches');
        if (! is_array($locations) || $locations === []) {
            $name = trim((string) ($v->getAttribute('excel_period_branch') ?? $v->branchLocation?->name ?? ''));
            $locations = $name !== '' ? [$name] : ['No Location'];
        }

        foreach ($locations as $locName) {
            if (strcasecmp((string) $locName, $locationFilter) === 0) {
                return true;
            }
        }

        return false;
    }

    protected function carTypeDimensionKey(Vehicle $v, string $carType): string
    {
        return match ($carType) {
            'body_type' => $this->vehicleBodyTypeLabel($v),
            'model' => $this->vehicleModelLabel($v),
            'year_model' => $this->vehicleYearModelLabel($v),
            default => $this->vehicleMakeLabel($v),
        };
    }

    protected function vehicleMakeLabel(Vehicle $v): string
    {
        if (is_object($v->make) && isset($v->make->name) && trim((string) $v->make->name) !== '') {
            return trim((string) $v->make->name);
        }
        $attrs = $v->getAttributes();
        $raw = trim((string) ($attrs['make'] ?? ''));

        return $raw !== '' ? $raw : 'Unknown';
    }

    protected function vehicleModelLabel(Vehicle $v): string
    {
        if (is_object($v->vehicleModel) && isset($v->vehicleModel->name) && trim((string) $v->vehicleModel->name) !== '') {
            return trim((string) $v->vehicleModel->name);
        }
        $attrs = $v->getAttributes();
        $raw = trim((string) ($attrs['model'] ?? ''));

        return $raw !== '' ? $raw : 'Unknown';
    }

    protected function vehicleBodyTypeLabel(Vehicle $v): string
    {
        $raw = trim((string) ($v->body_type ?? $v->getAttribute('body_type') ?? ''));

        return $raw !== '' ? $raw : 'Unspecified';
    }

    protected function vehicleYearModelLabel(Vehicle $v): string
    {
        $raw = trim((string) ($v->year ?? ''));

        return $raw !== '' ? $raw : 'Unknown';
    }

    /**
     * Calendar years available on the Sales Report year filter (newest first).
     *
     * @return array<int, string>
     */
    protected function salesYearFilterOptions(): array
    {
        $nowYear = (int) Carbon::now()->year;
        $years = [];
        for ($year = $nowYear; $year >= 2020; $year--) {
            $years[$year] = (string) $year;
        }

        return $years;
    }

    protected function resolveSelectedYear(Request $request): int
    {
        $nowYear = (int) Carbon::now()->year;
        $year = (int) $request->get('year', $nowYear);
        if ($year < 2020 || $year > $nowYear) {
            return $nowYear;
        }

        return $year;
    }

    public function salesExecutive(Request $request)
    {
        $period = (string) $request->get('period', 'monthly');
        if (! array_key_exists($period, $this->periodOptions)) {
            $period = 'monthly';
        }

        $viewMode = (string) $request->get('view', 'executives');
        if (! in_array($viewMode, ['team', 'agents', 'executives'], true)) {
            $viewMode = 'executives';
        }

        $selectedYear = $this->resolveSelectedYear($request);
        $monthRaw = $request->input('month', null);
        $selectedMonth = is_string($monthRaw) ? trim($monthRaw) : '';
        if ($selectedMonth !== '' && ! preg_match('/^(0?[1-9]|1[0-2])$/', $selectedMonth)) {
            $selectedMonth = '';
        }

        // First visit: jump to the latest month that actually has commissions or releases.
        if ($period === 'monthly' && $monthRaw === null && ! $request->filled('year')) {
            $latest = $this->latestSalesActivityYearMonth();
            $selectedYear = $latest['year'];
            $selectedMonth = $latest['month'];
        }

        $window = $this->resolveDateWindow($request, $period);
        if ($period === 'monthly') {
            if ($monthRaw === null && $selectedMonth !== '') {
                $monthNum = (int) $selectedMonth;
                $selectedMonth = sprintf('%02d', $monthNum);
                $window = [
                    'from' => Carbon::create($selectedYear, $monthNum, 1)->startOfDay(),
                    'to' => Carbon::create($selectedYear, $monthNum, 1)->endOfMonth()->endOfDay(),
                ];
            } elseif ($selectedMonth !== '') {
                $monthNum = (int) $selectedMonth;
                $selectedMonth = sprintf('%02d', $monthNum);
                $window = [
                    'from' => Carbon::create($selectedYear, $monthNum, 1)->startOfDay(),
                    'to' => Carbon::create($selectedYear, $monthNum, 1)->endOfMonth()->endOfDay(),
                ];
            }
            // else: empty month = full selected year (already from resolveDateWindow)
        }

        $analytics = $this->buildSalesExecutiveAnalytics($window, $viewMode);

        return view('analytics-reports.sales-executive', [
            'periodOptions' => $this->periodOptions,
            'selectedPeriod' => $period,
            'viewMode' => $viewMode,
            'yearOptions' => $this->salesYearFilterOptions(),
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'monthOptions' => [
                '' => 'All months in year',
                '01' => 'January',
                '02' => 'February',
                '03' => 'March',
                '04' => 'April',
                '05' => 'May',
                '06' => 'June',
                '07' => 'July',
                '08' => 'August',
                '09' => 'September',
                '10' => 'October',
                '11' => 'November',
                '12' => 'December',
            ],
            'dateFrom' => $window['from'] ? $window['from']->format('Y-m-d') : '',
            'dateTo' => $window['to'] ? $window['to']->format('Y-m-d') : '',
            'activeRangeLabel' => $this->rangeLabel($window),
            'summary' => $analytics['summary'],
            'charts' => $analytics['charts'],
            'tables' => $analytics['tables'],
            'metricReports' => $analytics['metric_reports'],
            'hasData' => $analytics['has_data'],
            'viewLabel' => $analytics['view_label'],
        ]);
    }

    public function suppliers(Request $request)
    {
        return view('analytics-reports.suppliers', $this->buildSuppliersPageData($request));
    }

    public function financing(Request $request)
    {
        return view('analytics-reports.financing', $this->buildFinancingPageData($request));
    }

    public function salesReports(Request $request)
    {
        return view('analytics-reports.sales-reports', $this->buildSalesReportsPageData($request));
    }

    public function currentInventory(Request $request)
    {
        return view('analytics-reports.current-inventory', $this->buildInventoryReportsPageData($request));
    }

    public function customers(Request $request)
    {
        return view('analytics-reports.customers', $this->buildCustomerReportsPageData($request));
    }

    protected function placeholderReport(string $title, string $icon, string $message)
    {
        return view('analytics-reports.placeholder', [
            'title' => $title,
            'icon' => $icon,
            'message' => $message,
        ]);
    }

    protected function buildInventoryReportsPageData(Request $request): array
    {
        $locationOptions = $this->salesLocationFilterOptions();
        $selectedLocation = $this->resolveSelectedLocationFilter(
            trim((string) $request->get('location', '')),
            $locationOptions
        );

        $selectedYear = $this->resolveSelectedYear($request);
        $monthRaw = $request->input('month', null);
        $selectedMonth = is_string($monthRaw) ? trim($monthRaw) : '';
        if ($selectedMonth !== '' && ! preg_match('/^(0?[1-9]|1[0-2])$/', $selectedMonth)) {
            $selectedMonth = '';
        }

        $showResults = $request->boolean('run')
            || $request->filled('year')
            || $request->filled('month')
            || $request->filled('location');

        if (! $request->filled('year') && ! $request->boolean('run') && $monthRaw === null) {
            $latest = $this->latestSalesActivityYearMonth();
            $selectedYear = $latest['year'];
            $selectedMonth = $latest['month'];
        }

        $now = Carbon::now()->endOfDay();
        if ($selectedMonth !== '') {
            $monthNum = (int) $selectedMonth;
            $selectedMonth = sprintf('%02d', $monthNum);
            $asOf = Carbon::create($selectedYear, $monthNum, 1)->endOfMonth()->endOfDay();
            $activeRangeLabel = Carbon::create($selectedYear, $monthNum, 1)->format('F Y');
        } else {
            // Year-only: inventory / sold window as of end of that year (or today if current year).
            $asOf = Carbon::create($selectedYear, 12, 31)->endOfDay();
            $activeRangeLabel = (string) $selectedYear;
        }
        if ($asOf->gt($now)) {
            $asOf = $now->copy();
            if ($selectedMonth === '') {
                $activeRangeLabel = (string) $selectedYear . ' (as of today)';
            }
        }

        $soldWindow = [
            'from' => $asOf->copy()->subMonthsNoOverflow(3)->startOfDay(),
            'to' => $asOf->copy(),
        ];

        $reports = $showResults
            ? $this->buildInventoryReportsTables($soldWindow, $asOf, $selectedLocation)
            : [
                'by_model' => ['title' => 'Inventory by Model', 'dimension_label' => 'Model', 'rows' => [], 'totals' => [], 'has_data' => false],
                'by_make' => ['title' => 'Inventory by Make', 'dimension_label' => 'Make', 'rows' => [], 'totals' => [], 'has_data' => false],
                'by_body_type' => ['title' => 'Inventory by Body Type', 'dimension_label' => 'Body Type', 'rows' => [], 'totals' => [], 'has_data' => false],
                'by_year_model' => ['title' => 'Inventory by Year Model', 'dimension_label' => 'Year Model', 'rows' => [], 'totals' => [], 'has_data' => false],
                'by_fuel_type' => ['title' => 'Inventory by Fuel Type', 'dimension_label' => 'Fuel Type', 'rows' => [], 'totals' => [], 'has_data' => false],
                'by_transmission' => ['title' => 'Inventory by Transmission', 'dimension_label' => 'Transmission', 'rows' => [], 'totals' => [], 'has_data' => false],
                'by_supplier' => ['title' => 'Inventory by Supplier', 'dimension_label' => 'Supplier', 'rows' => [], 'totals' => [], 'has_data' => false],
                'by_age' => ['title' => 'Inventory by Car Age', 'dimension_label' => 'Car Age', 'rows' => [], 'totals' => [], 'has_data' => false],
                'has_data' => false,
            ];

        $activeTab = (string) $request->get('excel_tab', 'by_model');
        $validTabs = ['by_model', 'by_make', 'by_body_type', 'by_year_model', 'by_fuel_type', 'by_transmission', 'by_supplier', 'by_age'];
        if (! in_array($activeTab, $validTabs, true)) {
            $activeTab = 'by_model';
        }

        return [
            'yearOptions' => $this->salesYearFilterOptions(),
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'monthOptions' => [
                '' => 'All months (as of today)',
                '01' => 'January',
                '02' => 'February',
                '03' => 'March',
                '04' => 'April',
                '05' => 'May',
                '06' => 'June',
                '07' => 'July',
                '08' => 'August',
                '09' => 'September',
                '10' => 'October',
                '11' => 'November',
                '12' => 'December',
            ],
            'locationOptions' => $locationOptions,
            'selectedLocation' => $selectedLocation,
            'showResults' => $showResults,
            'activeRangeLabel' => $activeRangeLabel,
            'soldWindowLabel' => $soldWindow['from']->format('M d, Y') . ' – ' . $soldWindow['to']->format('M d, Y'),
            'activeExcelTab' => $activeTab,
            'byModelReport' => $reports['by_model'],
            'byMakeReport' => $reports['by_make'],
            'byBodyTypeReport' => $reports['by_body_type'],
            'byYearModelReport' => $reports['by_year_model'],
            'byFuelTypeReport' => $reports['by_fuel_type'],
            'byTransmissionReport' => $reports['by_transmission'],
            'bySupplierReport' => $reports['by_supplier'],
            'byAgeReport' => $reports['by_age'],
            'hasData' => (bool) ($reports['has_data'] ?? false),
        ];
    }

    /**
     * Resolve location select to a canonical option key (case-insensitive).
     *
     * @param  array<string, string>  $locationOptions
     */
    protected function resolveSelectedLocationFilter(string $selectedLocation, array $locationOptions): string
    {
        if ($selectedLocation === '') {
            return '';
        }
        if (array_key_exists($selectedLocation, $locationOptions)) {
            return $selectedLocation;
        }
        foreach ($locationOptions as $key => $label) {
            if ($key === '') {
                continue;
            }
            if (strcasecmp((string) $key, $selectedLocation) === 0
                || strcasecmp((string) $label, $selectedLocation) === 0) {
                return (string) $key;
            }
        }

        return '';
    }

    /**
     * Unit was on-hand inventory at $asOf (purchased on/before, not yet released/sold/forfeited).
     */
    protected function vehicleInStockAsOf(Vehicle $v, Carbon $asOf): bool
    {
        $acquired = $v->purchase_date ?? $v->created_at;
        if (! $acquired || Carbon::parse($acquired)->startOfDay()->gt($asOf->copy()->startOfDay())) {
            return false;
        }

        if (in_array((string) $v->status, ['Available', 'Reserved'], true)) {
            return true;
        }

        $leave = $v->statusDetail?->release_date
            ?? $v->statusDetail?->sale_date
            ?? null;

        if (! $leave && $v->relationLoaded('forfeitDetails')) {
            $leave = $v->forfeitDetails->first()?->forfeit_date;
        } elseif (! $leave) {
            $leave = $v->forfeitDetails()->orderByDesc('forfeit_date')->value('forfeit_date');
        }

        if (! $leave) {
            return false;
        }

        // Still in stock at asOf if it left after that date.
        return Carbon::parse($leave)->startOfDay()->gt($asOf->copy()->startOfDay());
    }

    /**
     * Excel "(20-27) INVENTORY REPORTS" — stock as-of selected period + last-3-months sales metrics.
     *
     * @return array<string, mixed>
     */
    protected function buildInventoryReportsTables(array $soldWindow, Carbon $asOf, string $locationFilter = ''): array
    {
        $stockVehicles = Vehicle::with(['statusDetail', 'expense', 'make', 'vehicleModel', 'branchLocation', 'forfeitDetails'])
            ->where('status', '!=', 'Archived')
            ->get()
            ->filter(function (Vehicle $v) use ($asOf, $locationFilter) {
                if (! $this->vehicleInStockAsOf($v, $asOf)) {
                    return false;
                }

                return $this->vehicleMatchesSalesLocation($v, $locationFilter);
            })
            ->values();

        $soldVehicles = Vehicle::with(['statusDetail', 'expense', 'make', 'vehicleModel', 'branchLocation'])
            ->whereIn('status', ['Reserved', 'Released', 'Forfeited'])
            ->whereHas('statusDetail', fn ($q) => $q->whereNotNull('sale_date'))
            ->get()
            ->filter(function (Vehicle $v) use ($soldWindow, $locationFilter) {
                if (! $this->inWindow($v->statusDetail?->sale_date, $soldWindow)) {
                    return false;
                }

                return $this->vehicleMatchesSalesLocation($v, $locationFilter);
            })
            ->values();

        $byModel = $this->buildInventoryDimensionTable(
            'Inventory by Model and Sales',
            'Model',
            $stockVehicles,
            $soldVehicles,
            'model',
            $asOf
        );
        $byMake = $this->buildInventoryDimensionTable(
            'Inventory by Make',
            'Make',
            $stockVehicles,
            $soldVehicles,
            'make',
            $asOf
        );
        $byBody = $this->buildInventoryDimensionTable(
            'Inventory by Body Type',
            'Body Type',
            $stockVehicles,
            $soldVehicles,
            'body_type',
            $asOf
        );
        $byYear = $this->buildInventoryDimensionTable(
            'Inventory by Year Model',
            'Year Model',
            $stockVehicles,
            $soldVehicles,
            'year_model',
            $asOf
        );
        $byFuel = $this->buildInventoryDimensionTable(
            'Inventory by Fuel Type',
            'Fuel Type',
            $stockVehicles,
            $soldVehicles,
            'fuel_type',
            $asOf
        );
        $byTrans = $this->buildInventoryDimensionTable(
            'Inventory by Transmission Type',
            'Transmission',
            $stockVehicles,
            $soldVehicles,
            'transmission',
            $asOf
        );
        $bySupplier = $this->buildInventoryDimensionTable(
            'Inventory by Supplier',
            'Supplier',
            $stockVehicles,
            $soldVehicles,
            'supplier',
            $asOf
        );
        $byAge = $this->buildInventoryDimensionTable(
            'Inventory by Car Age',
            'Car Age',
            $stockVehicles,
            $soldVehicles,
            'inventory_age',
            $asOf,
            $this->inventoryAgeBucketLabels()
        );

        $hasData = (bool) (
            ($byModel['has_data'] ?? false)
            || ($byMake['has_data'] ?? false)
            || ($byBody['has_data'] ?? false)
            || ($byYear['has_data'] ?? false)
            || ($byFuel['has_data'] ?? false)
            || ($byTrans['has_data'] ?? false)
            || ($bySupplier['has_data'] ?? false)
            || ($byAge['has_data'] ?? false)
        );

        return [
            'by_model' => $byModel,
            'by_make' => $byMake,
            'by_body_type' => $byBody,
            'by_year_model' => $byYear,
            'by_fuel_type' => $byFuel,
            'by_transmission' => $byTrans,
            'by_supplier' => $bySupplier,
            'by_age' => $byAge,
            'has_data' => $hasData,
        ];
    }

    /**
     * @param  Collection<int, Vehicle>  $stockVehicles
     * @param  Collection<int, Vehicle>  $soldVehicles
     * @param  array<int, string>|null  $fixedLabels
     * @return array{title:string,dimension_label:string,rows:array<int,array<string,mixed>>,totals:array<string,mixed>,has_data:bool}
     */
    protected function buildInventoryDimensionTable(
        string $title,
        string $dimensionLabel,
        Collection $stockVehicles,
        Collection $soldVehicles,
        string $dimension,
        Carbon $asOf,
        ?array $fixedLabels = null
    ): array {
        $stockBuckets = [];
        $profitBuckets = [];
        foreach ($stockVehicles as $vehicle) {
            $key = $this->inventoryDimensionKey($vehicle, $dimension, $asOf, false);
            if ($key === '') {
                continue;
            }
            $stockBuckets[$key] = ($stockBuckets[$key] ?? 0) + 1;
            $profitBuckets[$key][] = $this->inventoryPotentialProfit($vehicle);
        }

        $soldBuckets = [];
        $speedBuckets = [];
        foreach ($soldVehicles as $vehicle) {
            $key = $this->inventoryDimensionKey($vehicle, $dimension, $asOf, true);
            if ($key === '') {
                continue;
            }
            $soldBuckets[$key] = ($soldBuckets[$key] ?? 0) + 1;
            $saleDate = $vehicle->statusDetail?->sale_date;
            $purchase = $vehicle->purchase_date ?? $vehicle->created_at;
            if ($saleDate && $purchase) {
                $speedBuckets[$key][] = (float) Carbon::parse($purchase)->diffInDays(Carbon::parse($saleDate));
            }
        }

        if ($fixedLabels !== null) {
            $labels = collect($fixedLabels);
        } else {
            $labels = collect(array_unique(array_merge(
                array_keys($stockBuckets),
                array_keys($soldBuckets)
            )))
                ->map(fn ($label) => trim((string) $label))
                ->filter(fn ($label) => $label !== '')
                ->unique(fn ($label) => mb_strtoupper($label))
                ->values();
        }

        $totalStock = (int) array_sum($stockBuckets);
        $allSpeed = [];
        $allProfit = [];

        $rows = $labels
            ->map(function ($label) use ($stockBuckets, $soldBuckets, $speedBuckets, $profitBuckets, $totalStock, &$allSpeed, &$allProfit) {
                $stock = (int) ($this->bucketValueForLabel($stockBuckets, $label));
                $sold = (int) ($this->bucketValueForLabel($soldBuckets, $label));
                $speeds = $this->bucketListForLabel($speedBuckets, $label);
                $profits = $this->bucketListForLabel($profitBuckets, $label);
                foreach ($speeds as $day) {
                    $allSpeed[] = $day;
                }
                foreach ($profits as $profit) {
                    $allProfit[] = $profit;
                }

                return [
                    'label' => $label,
                    'stock_count' => $stock,
                    'stock_pct' => $totalStock > 0 ? round(($stock / $totalStock) * 100, 1) : 0.0,
                    'sold_last_3_months' => $sold,
                    'avg_speed_days' => count($speeds) > 0 ? round(array_sum($speeds) / count($speeds), 1) : 0.0,
                    'avg_potential_profit' => count($profits) > 0 ? array_sum($profits) / count($profits) : 0.0,
                ];
            })
            ->filter(fn (array $row) => ((int) $row['stock_count'] + (int) $row['sold_last_3_months']) > 0);

        if ($fixedLabels === null) {
            $rows = $rows->sort(function (array $a, array $b) {
                if ($a['stock_count'] !== $b['stock_count']) {
                    return $b['stock_count'] <=> $a['stock_count'];
                }
                if ($a['sold_last_3_months'] !== $b['sold_last_3_months']) {
                    return $b['sold_last_3_months'] <=> $a['sold_last_3_months'];
                }

                return strcasecmp($a['label'], $b['label']);
            })->values();
        } else {
            $rows = $rows->values();
        }

        $rows = $rows->map(function (array $row, int $index) {
            $row['rank'] = $index + 1;

            return $row;
        });

        $visibleStock = (int) $rows->sum('stock_count');
        $visibleSold = (int) $rows->sum('sold_last_3_months');

        return [
            'title' => $title,
            'dimension_label' => $dimensionLabel,
            'rows' => $rows->all(),
            'totals' => [
                'stock_count' => $visibleStock,
                'stock_pct' => $visibleStock > 0 ? 100.0 : 0.0,
                'sold_last_3_months' => $visibleSold,
                'avg_speed_days' => count($allSpeed) > 0 ? round(array_sum($allSpeed) / count($allSpeed), 1) : 0.0,
                'avg_potential_profit' => count($allProfit) > 0 ? array_sum($allProfit) / count($allProfit) : 0.0,
            ],
            'has_data' => $rows->isNotEmpty(),
        ];
    }

    protected function inventoryDimensionKey(Vehicle $v, string $dimension, Carbon $asOf, bool $forSold): string
    {
        return match ($dimension) {
            'model' => $this->vehicleModelLabel($v),
            'make' => $this->vehicleMakeLabel($v),
            'body_type' => $this->vehicleBodyTypeLabel($v),
            'year_model' => $this->vehicleYearModelLabel($v),
            'fuel_type' => $this->vehicleFuelTypeLabel($v),
            'transmission' => $this->vehicleTransmissionLabel($v),
            'supplier' => $this->vehicleSupplierLabel($v),
            'inventory_age' => $this->inventoryAgeBucketForVehicle($v, $asOf, $forSold),
            default => 'Unknown',
        };
    }

    protected function inventoryPotentialProfit(Vehicle $v): float
    {
        $posted = (float) ($v->posted_price ?? 0);
        $purchase = (float) ($v->purchase_price ?? 0);
        $repair = (float) ($v->expense?->total_repair_cost ?? 0);
        $agent = (float) ($v->statusDetail?->agent_cost ?? 0);
        $transfer = (float) ($v->statusDetail?->transfer_cost ?? 0);

        if ($posted <= 0) {
            return 0.0;
        }

        return $posted - ($purchase + $repair + $agent + $transfer);
    }

    protected function vehicleFuelTypeLabel(Vehicle $v): string
    {
        $raw = strtolower(trim((string) ($v->fuel_type ?? '')));
        if ($raw === '') {
            return 'Unspecified';
        }
        if (str_contains($raw, 'diesel')) {
            return 'DIESEL';
        }
        if (str_contains($raw, 'hybrid')) {
            return 'HYBRID';
        }
        if (str_contains($raw, 'electric') || $raw === 'ev') {
            return 'ELECTRIC';
        }
        if (str_contains($raw, 'gas') || str_contains($raw, 'petrol')) {
            return 'GAS';
        }

        return mb_strtoupper(trim((string) $v->fuel_type));
    }

    protected function vehicleTransmissionLabel(Vehicle $v): string
    {
        $raw = strtolower(trim((string) ($v->transmission ?? '')));
        if ($raw === '') {
            return 'Unspecified';
        }
        if (str_contains($raw, 'manual')) {
            return 'MANUAL';
        }
        if (str_contains($raw, 'auto') || str_contains($raw, 'cvt') || str_contains($raw, 'dct')) {
            return 'AUTOMATIC';
        }

        return mb_strtoupper(trim((string) $v->transmission));
    }

    /**
     * @return array<int, string>
     */
    protected function inventoryAgeBucketLabels(): array
    {
        return [
            '< 1 MONTH',
            '1 MONTH - 2 MONTHS',
            '2 MONTHS - 3 MONTHS',
            '3 MONTHS - 4 MONTHS',
            '4 MONTHS - 5 MONTHS',
            '5 MONTHS - 6 MONTHS',
            '6 MONTHS - 7 MONTHS',
            '7 MONTHS - 8 MONTHS',
            '8 MONTHS - 9 MONTHS',
            '9 MONTHS - 10 MONTHS',
            '10 MONTHS - 11 MONTHS',
            '11 MONTHS -12 MONTHS',
            '1 YEAR - 1.5 YEARS',
            '1.5 YEARS - 2 YEARS',
            '2 YEARS AND ABOVE',
        ];
    }

    protected function inventoryAgeBucketForVehicle(Vehicle $v, Carbon $asOf, bool $forSold): string
    {
        $start = $v->purchase_date ?? $v->created_at;
        if (! $start) {
            return '';
        }

        $end = $asOf;
        if ($forSold) {
            $endDate = $v->statusDetail?->sale_date ?? $v->statusDetail?->release_date;
            if (! $endDate) {
                return '';
            }
            $end = Carbon::parse($endDate);
        }

        $days = (float) Carbon::parse($start)->diffInDays($end);

        return $this->inventoryAgeBucketFromDays($days);
    }

    protected function inventoryAgeBucketFromDays(float $days): string
    {
        if ($days < 30) {
            return '< 1 MONTH';
        }
        if ($days < 60) {
            return '1 MONTH - 2 MONTHS';
        }
        if ($days < 90) {
            return '2 MONTHS - 3 MONTHS';
        }
        if ($days < 120) {
            return '3 MONTHS - 4 MONTHS';
        }
        if ($days < 150) {
            return '4 MONTHS - 5 MONTHS';
        }
        if ($days < 180) {
            return '5 MONTHS - 6 MONTHS';
        }
        if ($days < 210) {
            return '6 MONTHS - 7 MONTHS';
        }
        if ($days < 240) {
            return '7 MONTHS - 8 MONTHS';
        }
        if ($days < 270) {
            return '8 MONTHS - 9 MONTHS';
        }
        if ($days < 300) {
            return '9 MONTHS - 10 MONTHS';
        }
        if ($days < 330) {
            return '10 MONTHS - 11 MONTHS';
        }
        if ($days < 365) {
            return '11 MONTHS -12 MONTHS';
        }
        if ($days < 548) { // ~1.5 years
            return '1 YEAR - 1.5 YEARS';
        }
        if ($days < 730) {
            return '1.5 YEARS - 2 YEARS';
        }

        return '2 YEARS AND ABOVE';
    }

    protected function buildCustomerReportsPageData(Request $request): array
    {
        $locationOptions = $this->salesLocationFilterOptions();
        $selectedLocation = $this->resolveSelectedLocationFilter(
            trim((string) $request->get('location', '')),
            $locationOptions
        );

        $selectedYear = $this->resolveSelectedYear($request);
        $monthRaw = $request->input('month', null);
        $selectedMonth = is_string($monthRaw) ? trim($monthRaw) : '';
        if ($selectedMonth !== '' && ! preg_match('/^(0?[1-9]|1[0-2])$/', $selectedMonth)) {
            $selectedMonth = '';
        }

        $showResults = $request->boolean('run')
            || $request->filled('year')
            || $request->filled('month')
            || $request->filled('location');

        if (! $request->filled('year') && ! $request->boolean('run') && $monthRaw === null) {
            $latest = $this->latestSalesActivityYearMonth();
            $selectedYear = $latest['year'];
            $selectedMonth = $latest['month'];
        }

        if ($selectedMonth !== '') {
            $monthNum = (int) $selectedMonth;
            $selectedMonth = sprintf('%02d', $monthNum);
            $window = [
                'from' => Carbon::create($selectedYear, $monthNum, 1)->startOfDay(),
                'to' => Carbon::create($selectedYear, $monthNum, 1)->endOfMonth()->endOfDay(),
            ];
            $activeRangeLabel = Carbon::create($selectedYear, $monthNum, 1)->format('F Y');
        } else {
            $window = [
                'from' => Carbon::create($selectedYear, 1, 1)->startOfDay(),
                'to' => Carbon::create($selectedYear, 12, 31)->endOfDay(),
            ];
            $activeRangeLabel = (string) $selectedYear;
        }

        $reports = $showResults
            ? $this->buildCustomerReportsTables($window, $selectedLocation)
            : [
                'by_age' => ['title' => 'Customers by Age', 'dimension_label' => 'Age', 'rows' => [], 'totals' => [], 'has_data' => false],
                'by_gender' => ['title' => 'Customers by Gender', 'dimension_label' => 'Sex', 'rows' => [], 'totals' => [], 'has_data' => false],
                'by_location' => ['title' => 'Customers by Location', 'dimension_label' => 'Location', 'rows' => [], 'totals' => [], 'has_data' => false],
                'has_data' => false,
            ];

        $activeTab = (string) $request->get('excel_tab', 'by_age');
        if (! in_array($activeTab, ['by_age', 'by_gender', 'by_location'], true)) {
            $activeTab = 'by_age';
        }

        return [
            'yearOptions' => $this->salesYearFilterOptions(),
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'monthOptions' => [
                '' => 'All months in year',
                '01' => 'January',
                '02' => 'February',
                '03' => 'March',
                '04' => 'April',
                '05' => 'May',
                '06' => 'June',
                '07' => 'July',
                '08' => 'August',
                '09' => 'September',
                '10' => 'October',
                '11' => 'November',
                '12' => 'December',
            ],
            'locationOptions' => $locationOptions,
            'selectedLocation' => $selectedLocation,
            'showResults' => $showResults,
            'activeRangeLabel' => $activeRangeLabel,
            'activeExcelTab' => $activeTab,
            'byAgeReport' => $reports['by_age'],
            'byGenderReport' => $reports['by_gender'],
            'byLocationReport' => $reports['by_location'],
            'hasData' => (bool) ($reports['has_data'] ?? false),
        ];
    }

    /**
     * Excel "(28-29) CUSTOMER REPORTS" (+ catalog #30 location):
     * sales/releases by customer age, gender, and customer location.
     *
     * @return array<string, mixed>
     */
    protected function buildCustomerReportsTables(array $window, string $locationFilter = ''): array
    {
        $saleVehicles = Vehicle::with(['statusDetail', 'branchLocation'])
            ->whereIn('status', ['Reserved', 'Released', 'Forfeited'])
            ->whereHas('statusDetail', fn ($q) => $q->whereNotNull('sale_date'))
            ->get()
            ->filter(function (Vehicle $v) use ($window, $locationFilter) {
                if (! $this->inWindow($v->statusDetail?->sale_date, $window)) {
                    return false;
                }

                return $this->vehicleMatchesSalesLocation($v, $locationFilter);
            })
            ->values();

        $releaseVehicles = $this->soldVehiclesBase($window)
            ->filter(fn (Vehicle $v) => $this->vehicleMatchesSalesLocation($v, $locationFilter))
            ->values();

        $byAge = $this->buildCustomerDimensionTable(
            'Customers by Age',
            'Age',
            $saleVehicles,
            $releaseVehicles,
            'age',
            $this->customerAgeBucketLabels()
        );
        $byGender = $this->buildCustomerDimensionTable(
            'Customers by Gender',
            'Sex',
            $saleVehicles,
            $releaseVehicles,
            'gender',
            ['Male', 'Female', 'Other', 'Unspecified']
        );
        $byLocation = $this->buildCustomerDimensionTable(
            'Customers by Location',
            'Location',
            $saleVehicles,
            $releaseVehicles,
            'customer_location'
        );

        return [
            'by_age' => $byAge,
            'by_gender' => $byGender,
            'by_location' => $byLocation,
            'has_data' => (bool) (
                ($byAge['has_data'] ?? false)
                || ($byGender['has_data'] ?? false)
                || ($byLocation['has_data'] ?? false)
            ),
        ];
    }

    /**
     * @param  Collection<int, Vehicle>  $saleVehicles
     * @param  Collection<int, Vehicle>  $releaseVehicles
     * @param  array<int, string>|null  $fixedLabels
     * @return array{title:string,dimension_label:string,rows:array<int,array<string,mixed>>,totals:array<string,mixed>,has_data:bool}
     */
    protected function buildCustomerDimensionTable(
        string $title,
        string $dimensionLabel,
        Collection $saleVehicles,
        Collection $releaseVehicles,
        string $dimension,
        ?array $fixedLabels = null
    ): array {
        $salesBuckets = [];
        foreach ($saleVehicles as $vehicle) {
            $key = $this->customerDimensionKey($vehicle, $dimension, false);
            if ($key === '') {
                continue;
            }
            $salesBuckets[$key] = ($salesBuckets[$key] ?? 0) + 1;
        }

        $releaseBuckets = [];
        foreach ($releaseVehicles as $vehicle) {
            $key = $this->customerDimensionKey($vehicle, $dimension, true);
            if ($key === '') {
                continue;
            }
            $releaseBuckets[$key] = ($releaseBuckets[$key] ?? 0) + 1;
        }

        if ($fixedLabels !== null) {
            $labels = collect($fixedLabels);
        } else {
            $labels = collect(array_unique(array_merge(
                array_keys($salesBuckets),
                array_keys($releaseBuckets)
            )))
                ->map(fn ($label) => trim((string) $label))
                ->filter(fn ($label) => $label !== '')
                ->unique(fn ($label) => mb_strtoupper($label))
                ->values();
        }

        $totalSales = (int) array_sum($salesBuckets);
        $totalReleases = (int) array_sum($releaseBuckets);

        $rows = $labels
            ->map(function ($label) use ($salesBuckets, $releaseBuckets, $totalSales, $totalReleases) {
                $sales = (int) ($this->bucketValueForLabel($salesBuckets, $label));
                $releases = (int) ($this->bucketValueForLabel($releaseBuckets, $label));

                return [
                    'label' => $label,
                    'sales_count' => $sales,
                    'sales_pct' => $totalSales > 0 ? round(($sales / $totalSales) * 100, 1) : 0.0,
                    'release_count' => $releases,
                    'release_pct' => $totalReleases > 0 ? round(($releases / $totalReleases) * 100, 1) : 0.0,
                ];
            })
            ->filter(fn (array $row) => ((int) $row['sales_count'] + (int) $row['release_count']) > 0);

        if ($fixedLabels === null) {
            $rows = $rows->sort(function (array $a, array $b) {
                if ($a['release_count'] !== $b['release_count']) {
                    return $b['release_count'] <=> $a['release_count'];
                }
                if ($a['sales_count'] !== $b['sales_count']) {
                    return $b['sales_count'] <=> $a['sales_count'];
                }

                return strcasecmp($a['label'], $b['label']);
            })->values();
        } else {
            $rows = $rows->values();
        }

        $rows = $rows->map(function (array $row, int $index) {
            $row['rank'] = $index + 1;

            return $row;
        });

        $visibleSales = (int) $rows->sum('sales_count');
        $visibleReleases = (int) $rows->sum('release_count');

        return [
            'title' => $title,
            'dimension_label' => $dimensionLabel,
            'rows' => $rows->all(),
            'totals' => [
                'sales_count' => $visibleSales,
                'sales_pct' => $visibleSales > 0 ? 100.0 : 0.0,
                'release_count' => $visibleReleases,
                'release_pct' => $visibleReleases > 0 ? 100.0 : 0.0,
            ],
            'has_data' => $rows->isNotEmpty(),
        ];
    }

    protected function customerDimensionKey(Vehicle $v, string $dimension, bool $forRelease): string
    {
        $detail = $v->statusDetail;

        return match ($dimension) {
            'age' => $this->customerAgeBucketLabel($detail, $forRelease
                ? ($v->getAttribute('excel_period_release_date') ?? $detail?->release_date ?? $detail?->sale_date)
                : ($detail?->sale_date)),
            'gender' => $this->customerGenderLabel($detail?->customer_gender),
            'customer_location' => $this->customerLocationLabel($detail?->customer_location),
            default => 'Unknown',
        };
    }

    /**
     * @return array<int, string>
     */
    protected function customerAgeBucketLabels(): array
    {
        return [
            '18-19',
            '20-24',
            '25-29',
            '30-34',
            '35-39',
            '40-44',
            '45-49',
            '50-54',
            '55-59',
            '60+',
            'Under 18',
            'Unknown',
        ];
    }

    protected function customerAgeBucketLabel($detail, $eventDate): string
    {
        $dob = $detail?->customer_date_of_birth ?? null;
        if (! $dob || ! $eventDate) {
            return 'Unknown';
        }

        try {
            $age = (int) Carbon::parse($dob)->diffInYears(Carbon::parse($eventDate));
        } catch (\Throwable $e) {
            return 'Unknown';
        }

        if ($age < 18) {
            return 'Under 18';
        }
        if ($age <= 19) {
            return '18-19';
        }
        if ($age <= 24) {
            return '20-24';
        }
        if ($age <= 29) {
            return '25-29';
        }
        if ($age <= 34) {
            return '30-34';
        }
        if ($age <= 39) {
            return '35-39';
        }
        if ($age <= 44) {
            return '40-44';
        }
        if ($age <= 49) {
            return '45-49';
        }
        if ($age <= 54) {
            return '50-54';
        }
        if ($age <= 59) {
            return '55-59';
        }

        return '60+';
    }

    protected function customerGenderLabel(?string $gender): string
    {
        $raw = strtolower(trim((string) $gender));
        if ($raw === '') {
            return 'Unspecified';
        }
        if ($raw === 'male' || $raw === 'm') {
            return 'Male';
        }
        if ($raw === 'female' || $raw === 'f') {
            return 'Female';
        }

        return 'Other';
    }

    protected function customerLocationLabel(?string $location): string
    {
        $raw = trim((string) $location);
        if ($raw === '') {
            return 'Unknown';
        }

        return mb_strtoupper(preg_replace('/\s+/', ' ', $raw) ?? $raw);
    }

    protected function buildSalesReportsPageData(Request $request): array
    {
        $locationOptions = $this->salesLocationFilterOptions();
        $selectedLocation = trim((string) $request->get('location', ''));
        if ($selectedLocation !== '' && ! array_key_exists($selectedLocation, $locationOptions)) {
            $selectedLocation = '';
        }

        $selectedYear = $this->resolveSelectedYear($request);
        $monthRaw = $request->input('month', null);
        $selectedMonth = is_string($monthRaw) ? trim($monthRaw) : '';
        if ($selectedMonth !== '' && ! preg_match('/^(0?[1-9]|1[0-2])$/', $selectedMonth)) {
            $selectedMonth = '';
        }

        $showResults = $request->boolean('run')
            || $request->filled('year')
            || $request->filled('month')
            || $request->filled('location');

        if (! $request->filled('year') && ! $request->boolean('run') && $monthRaw === null) {
            $latest = $this->latestSalesActivityYearMonth();
            $selectedYear = $latest['year'];
            $selectedMonth = $latest['month'];
        }

        if ($selectedMonth !== '') {
            $monthNum = (int) $selectedMonth;
            $selectedMonth = sprintf('%02d', $monthNum);
            $window = [
                'from' => Carbon::create($selectedYear, $monthNum, 1)->startOfDay(),
                'to' => Carbon::create($selectedYear, $monthNum, 1)->endOfMonth()->endOfDay(),
            ];
            $activeRangeLabel = Carbon::create($selectedYear, $monthNum, 1)->format('F Y');
        } else {
            $window = [
                'from' => Carbon::create($selectedYear, 1, 1)->startOfDay(),
                'to' => Carbon::create($selectedYear, 12, 31)->endOfDay(),
            ];
            $activeRangeLabel = (string) $selectedYear;
        }

        $reports = $showResults
            ? $this->buildSalesReportsTables($window, $selectedLocation)
            : [
                'day_of_week' => ['title' => 'Sales by Day of Week', 'rows' => [], 'totals' => [], 'has_data' => false],
                'days_to_sell' => ['title' => 'Days to Sell and Days to Release', 'rows' => [], 'totals' => [], 'has_data' => false],
                'has_data' => false,
            ];

        $activeTab = (string) $request->get('excel_tab', 'day_of_week');
        if (! in_array($activeTab, ['day_of_week', 'days_to_sell'], true)) {
            $activeTab = 'day_of_week';
        }

        return [
            'yearOptions' => $this->salesYearFilterOptions(),
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'monthOptions' => [
                '' => 'All months in year',
                '01' => 'January',
                '02' => 'February',
                '03' => 'March',
                '04' => 'April',
                '05' => 'May',
                '06' => 'June',
                '07' => 'July',
                '08' => 'August',
                '09' => 'September',
                '10' => 'October',
                '11' => 'November',
                '12' => 'December',
            ],
            'locationOptions' => $locationOptions,
            'selectedLocation' => $selectedLocation,
            'showResults' => $showResults,
            'activeRangeLabel' => $activeRangeLabel,
            'activeExcelTab' => $activeTab,
            'dayOfWeekReport' => $reports['day_of_week'],
            'daysToSellReport' => $reports['days_to_sell'],
            'hasData' => (bool) ($reports['has_data'] ?? false),
        ];
    }

    /**
     * Excel "(18-19) SALES REPORTS" — day-of-week + days-to-sell/release by month.
     *
     * @return array<string, mixed>
     */
    protected function buildSalesReportsTables(array $window, string $locationFilter = ''): array
    {
        $dayNames = [
            1 => 'MONDAY',
            2 => 'TUESDAY',
            3 => 'WEDNESDAY',
            4 => 'THURSDAY',
            5 => 'FRIDAY',
            6 => 'SATURDAY',
            7 => 'SUNDAY',
        ];
        $monthNames = [
            1 => 'JANUARY', 2 => 'FEBRUARY', 3 => 'MARCH', 4 => 'APRIL',
            5 => 'MAY', 6 => 'JUNE', 7 => 'JULY', 8 => 'AUGUST',
            9 => 'SEPTEMBER', 10 => 'OCTOBER', 11 => 'NOVEMBER', 12 => 'DECEMBER',
        ];

        $saleVehicles = Vehicle::with(['statusDetail', 'branchLocation'])
            ->whereIn('status', ['Reserved', 'Released'])
            ->whereHas('statusDetail', fn ($q) => $q->whereNotNull('sale_date'))
            ->get()
            ->filter(function (Vehicle $v) use ($window, $locationFilter) {
                if (! $this->inWindow($v->statusDetail?->sale_date, $window)) {
                    return false;
                }

                return $this->vehicleMatchesSalesLocation($v, $locationFilter);
            })
            ->values();

        $releaseVehicles = $this->soldVehiclesBase($window)
            ->filter(fn (Vehicle $v) => $this->vehicleMatchesSalesLocation($v, $locationFilter))
            ->values();

        $dowSales = array_fill(1, 7, 0);
        $dowReleases = array_fill(1, 7, 0);

        foreach ($saleVehicles as $vehicle) {
            $dow = (int) Carbon::parse($vehicle->statusDetail?->sale_date)->dayOfWeekIso;
            if ($dow >= 1 && $dow <= 7) {
                $dowSales[$dow]++;
            }
        }

        foreach ($releaseVehicles as $vehicle) {
            $event = $vehicle->getAttribute('excel_period_release_date')
                ?? $vehicle->statusDetail?->release_date
                ?? $vehicle->statusDetail?->sale_date;
            if (! $event) {
                continue;
            }
            $dow = (int) Carbon::parse($event)->dayOfWeekIso;
            if ($dow >= 1 && $dow <= 7) {
                $dowReleases[$dow]++;
            }
        }

        $totalSalesDow = array_sum($dowSales);
        $totalReleasesDow = array_sum($dowReleases);

        $dowRows = collect($dayNames)
            ->map(function ($label, $dow) use ($dowSales, $dowReleases, $totalSalesDow, $totalReleasesDow) {
                $sales = (int) ($dowSales[$dow] ?? 0);
                $releases = (int) ($dowReleases[$dow] ?? 0);

                return [
                    'rank' => (int) $dow,
                    'label' => $label,
                    'sales_count' => $sales,
                    'sales_pct' => $totalSalesDow > 0 ? round(($sales / $totalSalesDow) * 100, 1) : 0.0,
                    'release_count' => $releases,
                    'release_pct' => $totalReleasesDow > 0 ? round(($releases / $totalReleasesDow) * 100, 1) : 0.0,
                ];
            })
            ->filter(fn (array $row) => ((int) $row['sales_count'] + (int) $row['release_count']) > 0)
            ->values();

        // Days to sell / release by calendar month of the sale or release event.
        $monthBuckets = [];
        for ($m = 1; $m <= 12; $m++) {
            $monthBuckets[$m] = [
                'sales' => 0,
                'releases' => 0,
                'days_to_sell' => [],
                'days_to_release' => [],
            ];
        }

        foreach ($saleVehicles as $vehicle) {
            $saleDate = $vehicle->statusDetail?->sale_date;
            if (! $saleDate) {
                continue;
            }
            $m = (int) Carbon::parse($saleDate)->month;
            if ($m < 1 || $m > 12) {
                continue;
            }
            $monthBuckets[$m]['sales']++;
            $purchase = $vehicle->purchase_date ?? $vehicle->created_at;
            if ($purchase) {
                $monthBuckets[$m]['days_to_sell'][] = (float) Carbon::parse($purchase)->diffInDays(Carbon::parse($saleDate));
            }
        }

        foreach ($releaseVehicles as $vehicle) {
            $event = $vehicle->getAttribute('excel_period_release_date')
                ?? $vehicle->statusDetail?->release_date
                ?? $vehicle->statusDetail?->sale_date;
            if (! $event) {
                continue;
            }
            $m = (int) Carbon::parse($event)->month;
            if ($m < 1 || $m > 12) {
                continue;
            }
            $monthBuckets[$m]['releases']++;
            $purchase = $vehicle->purchase_date ?? $vehicle->created_at;
            if ($purchase) {
                $monthBuckets[$m]['days_to_release'][] = (float) Carbon::parse($purchase)->diffInDays(Carbon::parse($event));
            }
        }

        $allSellDays = [];
        $allReleaseDays = [];

        $monthRows = collect($monthNames)
            ->map(function ($label, $monthNum) use ($monthBuckets, &$allSellDays, &$allReleaseDays) {
                $bucket = $monthBuckets[$monthNum];
                $sellDays = $bucket['days_to_sell'];
                $releaseDays = $bucket['days_to_release'];
                foreach ($sellDays as $d) {
                    $allSellDays[] = $d;
                }
                foreach ($releaseDays as $d) {
                    $allReleaseDays[] = $d;
                }

                return [
                    'label' => $label,
                    'sales_count' => (int) $bucket['sales'],
                    'days_to_sell' => count($sellDays) > 0 ? round(array_sum($sellDays) / count($sellDays), 1) : 0.0,
                    'release_count' => (int) $bucket['releases'],
                    'days_to_release' => count($releaseDays) > 0 ? round(array_sum($releaseDays) / count($releaseDays), 1) : 0.0,
                ];
            })
            ->filter(fn (array $row) => ((int) $row['sales_count'] + (int) $row['release_count']) > 0)
            ->values();

        $monthSalesTotal = (int) $monthRows->sum('sales_count');
        $monthReleaseTotal = (int) $monthRows->sum('release_count');

        return [
            'day_of_week' => [
                'title' => 'Sales by Day of Week',
                'rows' => $dowRows->all(),
                'totals' => [
                    'sales_count' => $totalSalesDow,
                    'sales_pct' => $totalSalesDow > 0 ? 100.0 : 0.0,
                    'release_count' => $totalReleasesDow,
                    'release_pct' => $totalReleasesDow > 0 ? 100.0 : 0.0,
                ],
                'has_data' => $dowRows->isNotEmpty(),
            ],
            'days_to_sell' => [
                'title' => 'Days to Sell and Days to Release',
                'rows' => $monthRows->all(),
                'totals' => [
                    'sales_count' => $monthSalesTotal,
                    'days_to_sell' => count($allSellDays) > 0 ? round(array_sum($allSellDays) / count($allSellDays), 1) : 0.0,
                    'release_count' => $monthReleaseTotal,
                    'days_to_release' => count($allReleaseDays) > 0 ? round(array_sum($allReleaseDays) / count($allReleaseDays), 1) : 0.0,
                ],
                'has_data' => $monthRows->isNotEmpty(),
            ],
            'has_data' => $dowRows->isNotEmpty() || $monthRows->isNotEmpty(),
        ];
    }

    protected function buildFinancialMomPageData(Request $request): array
    {
        $locationOptions = $this->salesLocationFilterOptions();
        $selectedLocation = trim((string) $request->get('location', ''));
        if ($selectedLocation !== '' && ! array_key_exists($selectedLocation, $locationOptions)) {
            $selectedLocation = '';
        }

        $selectedYear = $this->resolveSelectedYear($request);
        $showResults = $request->boolean('run') || $request->filled('year') || $request->filled('location');

        if (! $request->filled('year') && ! $request->boolean('run')) {
            $latest = $this->latestSalesActivityYearMonth();
            $selectedYear = $latest['year'];
        }

        $window = [
            'from' => Carbon::create($selectedYear, 1, 1)->startOfDay(),
            'to' => Carbon::create($selectedYear, 12, 31)->endOfDay(),
        ];

        $reports = $showResults
            ? $this->buildFinancialMomReports($window, $selectedLocation)
            : [
                'sales_reservation' => ['title' => 'Sales and Reservation Summary', 'rows' => [], 'totals' => [], 'has_data' => false],
                'cash_financing' => ['title' => 'Cash and Financing Sales Summary', 'rows' => [], 'totals' => [], 'has_data' => false],
                'has_data' => false,
            ];

        $activeTab = (string) $request->get('excel_tab', 'sales_reservation');
        if (! in_array($activeTab, ['sales_reservation', 'cash_financing'], true)) {
            $activeTab = 'sales_reservation';
        }

        return [
            'yearOptions' => $this->salesYearFilterOptions(),
            'selectedYear' => $selectedYear,
            'locationOptions' => $locationOptions,
            'selectedLocation' => $selectedLocation,
            'showResults' => $showResults,
            'activeRangeLabel' => (string) $selectedYear,
            'activeExcelTab' => $activeTab,
            'salesReservationReport' => $reports['sales_reservation'],
            'cashFinancingReport' => $reports['cash_financing'],
            'hasData' => (bool) ($reports['has_data'] ?? false),
        ];
    }

    /**
     * Excel "(16-17) FINANCIAL REPORTS" — month-on-month summaries.
     *
     * @return array<string, mixed>
     */
    protected function buildFinancialMomReports(array $window, string $locationFilter = ''): array
    {
        $monthNames = [
            1 => 'JANUARY', 2 => 'FEBRUARY', 3 => 'MARCH', 4 => 'APRIL',
            5 => 'MAY', 6 => 'JUNE', 7 => 'JULY', 8 => 'AUGUST',
            9 => 'SEPTEMBER', 10 => 'OCTOBER', 11 => 'NOVEMBER', 12 => 'DECEMBER',
        ];

        $saleVehicles = Vehicle::with(['statusDetail', 'expense', 'branchLocation'])
            ->whereIn('status', ['Reserved', 'Released'])
            ->whereHas('statusDetail', fn ($q) => $q->whereNotNull('sale_date'))
            ->get()
            ->filter(function (Vehicle $v) use ($window, $locationFilter) {
                if (! $this->inWindow($v->statusDetail?->sale_date, $window)) {
                    return false;
                }

                return $this->vehicleMatchesSalesLocation($v, $locationFilter);
            })
            ->values();

        $releaseVehicles = $this->soldVehiclesBase($window)
            ->filter(fn (Vehicle $v) => $this->vehicleMatchesSalesLocation($v, $locationFilter))
            ->values();

        $byMonth = [];
        for ($m = 1; $m <= 12; $m++) {
            $byMonth[$m] = [
                'sales' => 0,
                'releases' => 0,
                'gross' => 0.0,
                'net' => 0.0,
                'cash_releases' => 0,
                'financing_releases' => 0,
                'discount_total' => 0.0,
                'amount_financed' => 0.0,
            ];
        }

        foreach ($saleVehicles as $vehicle) {
            $m = (int) Carbon::parse($vehicle->statusDetail?->sale_date)->month;
            if ($m >= 1 && $m <= 12) {
                $byMonth[$m]['sales']++;
            }
        }

        foreach ($releaseVehicles as $vehicle) {
            $detail = $vehicle->statusDetail;
            $event = $vehicle->getAttribute('excel_period_release_date')
                ?? $detail?->release_date
                ?? $detail?->sale_date;
            if (! $event) {
                continue;
            }
            $m = (int) Carbon::parse($event)->month;
            if ($m < 1 || $m > 12) {
                continue;
            }

            $byMonth[$m]['releases']++;

            $metrics = $this->supplierReleaseMetrics($vehicle, $locationFilter);
            $byMonth[$m]['gross'] += (float) $metrics['gross'];
            $byMonth[$m]['net'] += (float) $metrics['net'];

            $cash = strtolower(trim((string) ($detail?->cash_financing ?? '')));
            $finMetrics = $this->execFinancialMetrics($vehicle, $detail, $cash !== '' ? $cash : 'unknown');
            if ($finMetrics['is_cash']) {
                $byMonth[$m]['cash_releases']++;
                $byMonth[$m]['discount_total'] += (float) $finMetrics['discount'];
            }
            if ($finMetrics['is_financing']) {
                $byMonth[$m]['financing_releases']++;
                $byMonth[$m]['amount_financed'] += (float) $finMetrics['amount_financed'];
            }
        }

        $salesRows = collect($monthNames)
            ->map(function ($label, $monthNum) use ($byMonth) {
                $bucket = $byMonth[$monthNum];
                $sales = (int) $bucket['sales'];
                $releases = (int) $bucket['releases'];
                $gross = (float) $bucket['gross'];
                $net = (float) $bucket['net'];

                return [
                    'label' => $label,
                    'sales_count' => $sales,
                    'release_count' => $releases,
                    'total_gross' => $gross,
                    'avg_gross' => $releases > 0 ? $gross / $releases : 0.0,
                    'total_net' => $net,
                    'avg_net' => $releases > 0 ? $net / $releases : 0.0,
                ];
            })
            ->filter(fn (array $row) => (
                (int) $row['sales_count']
                + (int) $row['release_count']
                + (abs((float) $row['total_gross']) > 0 ? 1 : 0)
                + (abs((float) $row['total_net']) > 0 ? 1 : 0)
            ) > 0)
            ->values();

        $salesReleaseCount = (int) $salesRows->sum('release_count');
        $salesGross = (float) $salesRows->sum('total_gross');
        $salesNet = (float) $salesRows->sum('total_net');

        $cashFinRows = collect($monthNames)
            ->map(function ($label, $monthNum) use ($byMonth) {
                $bucket = $byMonth[$monthNum];
                $cash = (int) $bucket['cash_releases'];
                $financing = (int) $bucket['financing_releases'];
                $releases = (int) $bucket['releases'];
                $discount = (float) $bucket['discount_total'];
                $financed = (float) $bucket['amount_financed'];

                return [
                    'label' => $label,
                    'cash_releases' => $cash,
                    'discount_total' => $discount,
                    'avg_discount' => $cash > 0 ? $discount / $cash : 0.0,
                    'financing_releases' => $financing,
                    'financing_pct' => $releases > 0 ? round(($financing / $releases) * 100, 1) : 0.0,
                    'amount_financed' => $financed,
                    'avg_amount_financed' => $financing > 0 ? $financed / $financing : 0.0,
                ];
            })
            ->filter(fn (array $row) => (
                (int) $row['cash_releases']
                + (int) $row['financing_releases']
                + (abs((float) $row['discount_total']) > 0 ? 1 : 0)
                + (abs((float) $row['amount_financed']) > 0 ? 1 : 0)
            ) > 0)
            ->values();

        $cashCount = (int) $cashFinRows->sum('cash_releases');
        $finCount = (int) $cashFinRows->sum('financing_releases');
        $discountTotal = (float) $cashFinRows->sum('discount_total');
        $financedTotal = (float) $cashFinRows->sum('amount_financed');
        $releaseBase = (int) $salesRows->sum('release_count');

        return [
            'sales_reservation' => [
                'title' => 'Sales and Reservation Summary',
                'rows' => $salesRows->all(),
                'totals' => [
                    'sales_count' => (int) $salesRows->sum('sales_count'),
                    'release_count' => $salesReleaseCount,
                    'total_gross' => $salesGross,
                    'avg_gross' => $salesReleaseCount > 0 ? $salesGross / $salesReleaseCount : 0.0,
                    'total_net' => $salesNet,
                    'avg_net' => $salesReleaseCount > 0 ? $salesNet / $salesReleaseCount : 0.0,
                ],
                'has_data' => $salesRows->isNotEmpty(),
            ],
            'cash_financing' => [
                'title' => 'Cash and Financing Sales Summary',
                'rows' => $cashFinRows->all(),
                'totals' => [
                    'cash_releases' => $cashCount,
                    'discount_total' => $discountTotal,
                    'avg_discount' => $cashCount > 0 ? $discountTotal / $cashCount : 0.0,
                    'financing_releases' => $finCount,
                    'financing_pct' => $releaseBase > 0 ? round(($finCount / $releaseBase) * 100, 1) : 0.0,
                    'amount_financed' => $financedTotal,
                    'avg_amount_financed' => $finCount > 0 ? $financedTotal / $finCount : 0.0,
                ],
                'has_data' => $cashFinRows->isNotEmpty(),
            ],
            'has_data' => $salesRows->isNotEmpty() || $cashFinRows->isNotEmpty(),
        ];
    }

    protected function buildFinancingPageData(Request $request): array
    {
        $locationOptions = $this->salesLocationFilterOptions();
        $selectedLocation = trim((string) $request->get('location', ''));
        if ($selectedLocation !== '' && ! array_key_exists($selectedLocation, $locationOptions)) {
            $selectedLocation = '';
        }

        $selectedYear = $this->resolveSelectedYear($request);
        $showResults = $request->boolean('run') || $request->filled('year') || $request->filled('location');

        // First visit with defaults: jump to latest year that has financing activity.
        if (! $request->filled('year') && ! $request->boolean('run')) {
            $latest = $this->latestSalesActivityYearMonth();
            $selectedYear = $latest['year'];
        }

        $window = [
            'from' => Carbon::create($selectedYear, 1, 1)->startOfDay(),
            'to' => Carbon::create($selectedYear, 12, 31)->endOfDay(),
        ];

        $reports = $showResults
            ? $this->buildFinancingReports($window, $selectedYear, $selectedLocation)
            : [
                'summary' => ['title' => 'Financing Summary', 'rows' => [], 'totals' => [], 'has_data' => false],
                'term_length' => ['title' => 'Financing Term Length Summary', 'rows' => [], 'totals' => [], 'has_data' => false, 'available' => false],
                'by_company' => ['title' => 'Financing Summary by Company', 'rows' => [], 'totals' => [], 'has_data' => false],
                'finishing' => ['title' => 'Financing Loans Finishing in 3/2/1 Months', 'rows' => [], 'has_data' => false, 'available' => false],
                'has_data' => false,
            ];

        $activeTab = (string) $request->get('excel_tab', 'summary');
        if (! in_array($activeTab, ['summary', 'term_length', 'by_company', 'finishing'], true)) {
            $activeTab = 'summary';
        }

        return [
            'yearOptions' => $this->salesYearFilterOptions(),
            'selectedYear' => $selectedYear,
            'locationOptions' => $locationOptions,
            'selectedLocation' => $selectedLocation,
            'showResults' => $showResults,
            'activeRangeLabel' => (string) $selectedYear,
            'activeExcelTab' => $activeTab,
            'summaryReport' => $reports['summary'],
            'termLengthReport' => $reports['term_length'],
            'companyReport' => $reports['by_company'],
            'finishingReport' => $reports['finishing'],
            'hasData' => (bool) ($reports['has_data'] ?? false),
        ];
    }

    /**
     * Excel "(12-15) FINANCING REPORTS".
     *
     * @return array<string, mixed>
     */
    protected function buildFinancingReports(array $window, int $year, string $locationFilter = ''): array
    {
        $allReleased = $this->soldVehiclesBase($window)
            ->filter(fn (Vehicle $v) => $this->vehicleMatchesSalesLocation($v, $locationFilter))
            ->values();

        $financingReleased = $allReleased
            ->filter(function (Vehicle $v) {
                $cash = strtolower(trim((string) ($v->statusDetail?->cash_financing ?? '')));

                return str_contains($cash, 'financ');
            })
            ->values();

        $monthNames = [
            1 => 'JANUARY', 2 => 'FEBRUARY', 3 => 'MARCH', 4 => 'APRIL',
            5 => 'MAY', 6 => 'JUNE', 7 => 'JULY', 8 => 'AUGUST',
            9 => 'SEPTEMBER', 10 => 'OCTOBER', 11 => 'NOVEMBER', 12 => 'DECEMBER',
        ];

        $totalReleasesByMonth = array_fill(1, 12, 0);
        foreach ($allReleased as $vehicle) {
            $event = $vehicle->getAttribute('excel_period_release_date')
                ?? $vehicle->statusDetail?->release_date
                ?? $vehicle->statusDetail?->sale_date;
            if (! $event) {
                continue;
            }
            $m = (int) Carbon::parse($event)->month;
            if ($m >= 1 && $m <= 12) {
                $totalReleasesByMonth[$m]++;
            }
        }

        $financingByMonth = [];
        for ($m = 1; $m <= 12; $m++) {
            $financingByMonth[$m] = [
                'count' => 0,
                'amount' => 0.0,
            ];
        }

        foreach ($financingReleased as $vehicle) {
            $detail = $vehicle->statusDetail;
            $event = $vehicle->getAttribute('excel_period_release_date')
                ?? $detail?->release_date
                ?? $detail?->sale_date;
            if (! $event) {
                continue;
            }
            $m = (int) Carbon::parse($event)->month;
            if ($m < 1 || $m > 12) {
                continue;
            }
            $cash = strtolower(trim((string) ($detail?->cash_financing ?? 'financing')));
            $metrics = $this->execFinancialMetrics($vehicle, $detail, $cash);
            $financingByMonth[$m]['count']++;
            $financingByMonth[$m]['amount'] += (float) $metrics['amount_financed'];
        }

        $summaryRows = collect($monthNames)
            ->map(function ($label, $monthNum) use ($financingByMonth, $totalReleasesByMonth) {
                $count = (int) ($financingByMonth[$monthNum]['count'] ?? 0);
                $amount = (float) ($financingByMonth[$monthNum]['amount'] ?? 0);
                $totalRel = (int) ($totalReleasesByMonth[$monthNum] ?? 0);

                return [
                    'label' => $label,
                    'financing_releases' => $count,
                    'financing_pct' => $totalRel > 0 ? round(($count / $totalRel) * 100, 1) : 0.0,
                    'amount_financed' => $amount,
                    'avg_amount_financed' => $count > 0 ? $amount / $count : 0.0,
                ];
            })
            ->filter(fn (array $row) => (int) $row['financing_releases'] > 0 || (float) $row['amount_financed'] > 0)
            ->values();

        $summaryTotalsCount = (int) $summaryRows->sum('financing_releases');
        $summaryTotalsAmount = (float) $summaryRows->sum('amount_financed');
        $summaryTotalReleases = (int) array_sum($totalReleasesByMonth);

        $companyCatalog = array_values(array_unique(array_merge(
            \App\Models\VehicleStatusDetail::financingCompanyOptions(),
            ['SAFC'],
            $financingReleased
                ->map(fn (Vehicle $v) => trim((string) ($v->statusDetail?->financing_company ?? '')))
                ->filter(fn ($n) => $n !== '')
                ->all()
        )));

        $companyBuckets = [];
        foreach ($companyCatalog as $name) {
            $companyBuckets[mb_strtoupper($name)] = [
                'label' => mb_strtoupper($name),
                'count' => 0,
                'amount' => 0.0,
                'days' => [],
            ];
        }

        $totalFinancingReleases = $financingReleased->count();
        foreach ($financingReleased as $vehicle) {
            $detail = $vehicle->statusDetail;
            $company = trim((string) ($detail?->financing_company ?? ''));
            $key = $company !== '' ? mb_strtoupper($company) : 'UNKNOWN';
            if (! isset($companyBuckets[$key])) {
                $companyBuckets[$key] = [
                    'label' => $key,
                    'count' => 0,
                    'amount' => 0.0,
                    'days' => [],
                ];
            }
            $cash = strtolower(trim((string) ($detail?->cash_financing ?? 'financing')));
            $metrics = $this->execFinancialMetrics($vehicle, $detail, $cash);
            $companyBuckets[$key]['count']++;
            $companyBuckets[$key]['amount'] += (float) $metrics['amount_financed'];

            $days = $detail?->days_from_reservation_to_release;
            if ($days === null && $detail?->sale_date && $detail?->release_date) {
                $days = Carbon::parse($detail->sale_date)->diffInDays(Carbon::parse($detail->release_date));
            }
            if ($days !== null) {
                $companyBuckets[$key]['days'][] = (float) $days;
            }
        }

        $companyRows = collect($companyBuckets)
            ->map(function (array $bucket) use ($totalFinancingReleases) {
                $count = (int) $bucket['count'];
                $amount = (float) $bucket['amount'];
                $days = $bucket['days'];

                return [
                    'label' => $bucket['label'],
                    'financing_releases' => $count,
                    'financing_pct' => $totalFinancingReleases > 0 ? round(($count / $totalFinancingReleases) * 100, 1) : 0.0,
                    'amount_financed' => $amount,
                    'avg_amount_financed' => $count > 0 ? $amount / $count : 0.0,
                    'avg_days_to_release' => count($days) > 0 ? round(array_sum($days) / count($days), 1) : 0.0,
                ];
            })
            ->filter(fn (array $row) => (int) $row['financing_releases'] > 0 || (float) $row['amount_financed'] > 0)
            ->sortByDesc('financing_releases')
            ->values();

        $companyCount = (int) $companyRows->sum('financing_releases');
        $companyAmount = (float) $companyRows->sum('amount_financed');
        $companyDays = [];
        foreach ($companyBuckets as $bucket) {
            if ((int) $bucket['count'] <= 0) {
                continue;
            }
            foreach ($bucket['days'] as $day) {
                $companyDays[] = $day;
            }
        }

        return [
            'summary' => [
                'title' => 'Financing Summary',
                'rows' => $summaryRows->all(),
                'totals' => [
                    'financing_releases' => $summaryTotalsCount,
                    'financing_pct' => $summaryTotalReleases > 0
                        ? round(($summaryTotalsCount / $summaryTotalReleases) * 100, 1)
                        : 0.0,
                    'amount_financed' => $summaryTotalsAmount,
                    'avg_amount_financed' => $summaryTotalsCount > 0 ? $summaryTotalsAmount / $summaryTotalsCount : 0.0,
                ],
                'has_data' => $summaryRows->isNotEmpty(),
            ],
            'term_length' => [
                'title' => 'Financing Term Length Summary',
                'rows' => [],
                'totals' => [],
                'has_data' => false,
                'available' => false,
                'message' => 'Loan term length is not stored on vehicle financing records yet, so this Excel table cannot be calculated.',
            ],
            'by_company' => [
                'title' => 'Financing Summary by Company',
                'rows' => $companyRows->all(),
                'totals' => [
                    'financing_releases' => $companyCount,
                    'financing_pct' => 100.0,
                    'amount_financed' => $companyAmount,
                    'avg_amount_financed' => $companyCount > 0 ? $companyAmount / $companyCount : 0.0,
                    'avg_days_to_release' => count($companyDays) > 0 ? round(array_sum($companyDays) / count($companyDays), 1) : 0.0,
                ],
                'has_data' => $companyRows->isNotEmpty(),
            ],
            'finishing' => [
                'title' => 'Financing Loans Finishing in 3/2/1 Months',
                'rows' => [],
                'has_data' => false,
                'available' => false,
                'message' => 'Loan start date and term length are not stored on vehicle records yet, so finishing loans cannot be listed.',
            ],
            'has_data' => $summaryRows->isNotEmpty() || $companyRows->isNotEmpty(),
        ];
    }

    protected function buildSuppliersPageData(Request $request): array
    {
        $period = (string) $request->get('period', 'monthly');
        if (! array_key_exists($period, $this->periodOptions)) {
            $period = 'monthly';
        }

        $locationOptions = $this->salesLocationFilterOptions();
        $selectedLocation = trim((string) $request->get('location', ''));
        if ($selectedLocation !== '' && ! array_key_exists($selectedLocation, $locationOptions)) {
            $selectedLocation = '';
        }

        $selectedYear = $this->resolveSelectedYear($request);
        $monthRaw = $request->input('month', null);
        $selectedMonth = is_string($monthRaw) ? trim($monthRaw) : '';
        if ($selectedMonth !== '' && ! preg_match('/^(0?[1-9]|1[0-2])$/', $selectedMonth)) {
            $selectedMonth = '';
        }

        // First visit: jump to the latest month that has releases/sales activity.
        if ($period === 'monthly' && $monthRaw === null && ! $request->filled('year')) {
            $latest = $this->latestSalesActivityYearMonth();
            $selectedYear = $latest['year'];
            $selectedMonth = $latest['month'];
        }

        $window = $this->resolveDateWindow($request, $period);
        if ($period === 'monthly') {
            if ($selectedMonth !== '') {
                $monthNum = (int) $selectedMonth;
                $selectedMonth = sprintf('%02d', $monthNum);
                $window = [
                    'from' => Carbon::create($selectedYear, $monthNum, 1)->startOfDay(),
                    'to' => Carbon::create($selectedYear, $monthNum, 1)->endOfMonth()->endOfDay(),
                ];
            } else {
                $window = [
                    'from' => Carbon::create($selectedYear, 1, 1)->startOfDay(),
                    'to' => Carbon::create($selectedYear, 12, 31)->endOfDay(),
                ];
            }
        }

        $reports = $this->buildSupplierReports($window, $selectedLocation);

        return [
            'periodOptions' => $this->periodOptions,
            'selectedPeriod' => $period,
            'yearOptions' => $this->salesYearFilterOptions(),
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'monthOptions' => [
                '' => 'All months in year',
                '01' => 'January',
                '02' => 'February',
                '03' => 'March',
                '04' => 'April',
                '05' => 'May',
                '06' => 'June',
                '07' => 'July',
                '08' => 'August',
                '09' => 'September',
                '10' => 'October',
                '11' => 'November',
                '12' => 'December',
            ],
            'locationOptions' => $locationOptions,
            'selectedLocation' => $selectedLocation,
            'dateFrom' => $window['from'] ? $window['from']->format('Y-m-d') : '',
            'dateTo' => $window['to'] ? $window['to']->format('Y-m-d') : '',
            'activeRangeLabel' => $this->rangeLabel($window),
            'speedReport' => $reports['speed'],
            'grossNetReport' => $reports['gross_net'],
            'hasData' => $reports['has_data'],
        ];
    }

    /**
     * Excel "(10-11) SUPPLIER REPORTS":
     * 10) Units supplied, sales, releases, speed by supplier
     * 11) Gross and net of releases by supplier
     *
     * @return array{speed:array<string,mixed>,gross_net:array<string,mixed>,has_data:bool}
     */
    protected function buildSupplierReports(array $window, string $locationFilter = ''): array
    {
        $releaseVehicles = $this->soldVehiclesBase($window)
            ->filter(fn (Vehicle $v) => $this->vehicleMatchesSalesLocation($v, $locationFilter))
            ->values();

        $saleVehicles = Vehicle::with(['statusDetail', 'branchLocation', 'expense'])
            ->whereIn('status', ['Reserved', 'Released'])
            ->whereHas('statusDetail', fn ($q) => $q->whereNotNull('sale_date'))
            ->get()
            ->filter(function (Vehicle $v) use ($window, $locationFilter) {
                if (! $this->inWindow($v->statusDetail?->sale_date, $window)) {
                    return false;
                }

                return $this->vehicleMatchesSalesLocation($v, $locationFilter);
            })
            ->values();

        // Units supplied = vehicles acquired/purchased in the selected window.
        $suppliedVehicles = Vehicle::with(['branchLocation'])
            ->get()
            ->filter(function (Vehicle $v) use ($window, $locationFilter) {
                $purchaseDate = $v->purchase_date ?? $v->created_at;
                if (! $this->inWindow($purchaseDate, $window)) {
                    return false;
                }

                return $this->vehicleMatchesSalesLocation($v, $locationFilter);
            })
            ->values();

        $suppliedBuckets = [];
        foreach ($suppliedVehicles as $vehicle) {
            $key = $this->vehicleSupplierLabel($vehicle);
            $suppliedBuckets[$key] = ($suppliedBuckets[$key] ?? 0) + 1;
        }

        $salesBuckets = [];
        foreach ($saleVehicles as $vehicle) {
            $key = $this->vehicleSupplierLabel($vehicle);
            $salesBuckets[$key] = ($salesBuckets[$key] ?? 0) + 1;
        }

        $releaseBuckets = [];
        $daysBuckets = [];
        $grossBuckets = [];
        $netBuckets = [];

        foreach ($releaseVehicles as $vehicle) {
            $key = $this->vehicleSupplierLabel($vehicle);
            $releaseBuckets[$key] = ($releaseBuckets[$key] ?? 0) + 1;

            $metrics = $this->supplierReleaseMetrics($vehicle, $locationFilter);
            if ($metrics['days_to_sell'] !== null) {
                $daysBuckets[$key][] = $metrics['days_to_sell'];
            }
            $grossBuckets[$key] = ($grossBuckets[$key] ?? 0.0) + $metrics['gross'];
            $netBuckets[$key] = ($netBuckets[$key] ?? 0.0) + $metrics['net'];
        }

        $labels = collect(array_unique(array_merge(
            array_keys($suppliedBuckets),
            array_keys($salesBuckets),
            array_keys($releaseBuckets)
        )))->filter(fn ($label) => $label !== '')->values();

        $totalSupplied = (int) array_sum($suppliedBuckets);
        $totalSales = (int) array_sum($salesBuckets);
        $totalReleases = (int) array_sum($releaseBuckets);
        $pctSuppliedBase = max($totalSupplied, 0);
        $pctSalesBase = max($totalSales, 0);
        $pctReleaseBase = max($totalReleases, 0);

        $speedRows = $labels
            ->map(function ($label) use (
                $suppliedBuckets,
                $salesBuckets,
                $releaseBuckets,
                $daysBuckets,
                $pctSuppliedBase,
                $pctSalesBase,
                $pctReleaseBase
            ) {
                $suppliedCount = (int) ($suppliedBuckets[$label] ?? 0);
                $salesCount = (int) ($salesBuckets[$label] ?? 0);
                $releaseCount = (int) ($releaseBuckets[$label] ?? 0);
                $days = $daysBuckets[$label] ?? [];
                $avgDays = count($days) > 0 ? round(array_sum($days) / count($days), 1) : 0.0;

                return [
                    'supplier' => $label,
                    'supplied_count' => $suppliedCount,
                    'supplied_pct' => $pctSuppliedBase > 0 ? round(($suppliedCount / $pctSuppliedBase) * 100, 1) : 0.0,
                    'sales_count' => $salesCount,
                    'sales_pct' => $pctSalesBase > 0 ? round(($salesCount / $pctSalesBase) * 100, 1) : 0.0,
                    'release_count' => $releaseCount,
                    'release_pct' => $pctReleaseBase > 0 ? round(($releaseCount / $pctReleaseBase) * 100, 1) : 0.0,
                    'avg_days_to_sell' => $avgDays,
                ];
            })
            ->filter(function (array $row) {
                return ((int) $row['supplied_count']
                    + (int) $row['sales_count']
                    + (int) $row['release_count']) > 0;
            })
            ->sort(function (array $a, array $b) {
                if ($a['release_count'] !== $b['release_count']) {
                    return $b['release_count'] <=> $a['release_count'];
                }
                if ($a['sales_count'] !== $b['sales_count']) {
                    return $b['sales_count'] <=> $a['sales_count'];
                }
                if ($a['supplied_count'] !== $b['supplied_count']) {
                    return $b['supplied_count'] <=> $a['supplied_count'];
                }

                return strcasecmp($a['supplier'], $b['supplier']);
            })
            ->values();

        $visibleDays = [];
        foreach ($speedRows as $row) {
            $days = $daysBuckets[$row['supplier']] ?? [];
            foreach ($days as $day) {
                $visibleDays[] = $day;
            }
        }
        $speedTotals = [
            'supplied_count' => (int) $speedRows->sum('supplied_count'),
            'sales_count' => (int) $speedRows->sum('sales_count'),
            'release_count' => (int) $speedRows->sum('release_count'),
            'avg_days_to_sell' => count($visibleDays) > 0 ? round(array_sum($visibleDays) / count($visibleDays), 1) : 0.0,
        ];

        $grossNetRows = $labels
            ->map(function ($label) use ($releaseBuckets, $grossBuckets, $netBuckets) {
                $releaseCount = (int) ($releaseBuckets[$label] ?? 0);
                $gross = (float) ($grossBuckets[$label] ?? 0);
                $net = (float) ($netBuckets[$label] ?? 0);

                return [
                    'supplier' => $label,
                    'release_count' => $releaseCount,
                    'total_gross' => $gross,
                    'avg_gross' => $releaseCount > 0 ? $gross / $releaseCount : 0.0,
                    'total_net' => $net,
                    'avg_net' => $releaseCount > 0 ? $net / $releaseCount : 0.0,
                ];
            })
            ->filter(function (array $row) {
                return (int) $row['release_count'] > 0
                    && (abs((float) $row['total_gross']) + abs((float) $row['total_net'])) > 0;
            })
            ->sortByDesc('total_gross')
            ->values();

        $totalGross = (float) $grossNetRows->sum('total_gross');
        $totalNet = (float) $grossNetRows->sum('total_net');
        $grossReleaseCount = (int) $grossNetRows->sum('release_count');
        return [
            'speed' => [
                'title' => 'Units Supplied, Sales and Releases by Supplier and Speed to Sell',
                'rows' => $speedRows->all(),
                'totals' => $speedTotals,
                'has_data' => $speedRows->isNotEmpty(),
            ],
            'gross_net' => [
                'title' => 'Supplier Gross and Net of Releases',
                'rows' => $grossNetRows->all(),
                'totals' => [
                    'release_count' => $grossReleaseCount,
                    'total_gross' => $totalGross,
                    'avg_gross' => $grossReleaseCount > 0 ? $totalGross / $grossReleaseCount : 0.0,
                    'total_net' => $totalNet,
                    'avg_net' => $grossReleaseCount > 0 ? $totalNet / $grossReleaseCount : 0.0,
                ],
                'has_data' => $grossNetRows->isNotEmpty(),
            ],
            'has_data' => $speedRows->isNotEmpty() || $grossNetRows->isNotEmpty(),
        ];
    }

    protected function vehicleSupplierLabel(Vehicle $v): string
    {
        $raw = trim((string) ($v->purchased_from ?? $v->getAttribute('purchased_from') ?? ''));
        if ($raw === '') {
            return 'Unknown';
        }

        $normalized = preg_replace('/\s+/', ' ', $raw) ?? $raw;
        if (preg_match('/trade[\s\-]?in/i', $normalized) || preg_match('/^tradein\b/i', $normalized)) {
            return 'TRADE-IN';
        }

        return mb_strtoupper($normalized);
    }

    /**
     * @return array{gross:float,net:float,days_to_sell:?float}
     */
    protected function supplierReleaseMetrics(Vehicle $v, string $locationFilter = ''): array
    {
        $event = $v->getAttribute('excel_period_release_date')
            ?? $v->statusDetail?->release_date
            ?? $v->statusDetail?->sale_date
            ?? $v->updated_at;
        $purchase = $v->purchase_date ?? $v->created_at;

        $soldPrice = (float) ($v->sold_price ?? 0);
        if ($soldPrice <= 0) {
            $soldPrice = (float) ($v->statusDetail?->sales_price ?? 0);
        }
        $excelSalesPrice = $v->getAttribute('excel_period_sales_price');
        if ($excelSalesPrice !== null && $excelSalesPrice !== '') {
            $soldPrice = (float) $excelSalesPrice;
        }

        $purchasePrice = (float) ($v->purchase_price ?? 0);
        $repairCost = (float) ($v->expense?->total_repair_cost ?? 0);
        $agentCost = (float) ($v->statusDetail?->agent_cost ?? 0);
        $transferCost = (float) ($v->statusDetail?->transfer_cost ?? 0);
        $financeRevenue1 = (float) ($v->statusDetail?->finance_revenue_1 ?? 0);
        $financeRevenue2 = (float) ($v->statusDetail?->finance_revenue_2 ?? 0);

        $excelGross = $v->getAttribute('excel_period_total_revenue');
        $excelCosts = $v->getAttribute('excel_period_total_costs');
        $excelProfit = $v->getAttribute('excel_period_total_profit');
        $branchFinancials = $v->getAttribute('excel_period_branch_financials');
        $resolved = $this->resolveExcelFinancialsForLocation(
            is_array($branchFinancials) ? $branchFinancials : [],
            $locationFilter,
            $excelGross,
            $excelCosts,
            $excelProfit,
            $soldPrice,
            $event
        );

        $gross = ($resolved['total_revenue'] !== null && $resolved['total_revenue'] !== '')
            ? (float) $resolved['total_revenue']
            : ($soldPrice + $financeRevenue1 + $financeRevenue2);
        $unitCost = ($resolved['total_costs'] !== null && $resolved['total_costs'] !== '')
            ? (float) $resolved['total_costs']
            : ($purchasePrice + $repairCost + $agentCost + $transferCost);
        $net = ($resolved['total_profit'] !== null && $resolved['total_profit'] !== '')
            ? (float) $resolved['total_profit']
            : ($gross - $unitCost);

        $eventDate = $resolved['release_date'] ?? $event;
        $daysToSell = ($purchase && $eventDate)
            ? (float) Carbon::parse($purchase)->diffInDays(Carbon::parse($eventDate))
            : null;

        return [
            'gross' => $gross,
            'net' => $net,
            'days_to_sell' => $daysToSell,
        ];
    }

    /**
     * Sales team / agent / executive performance.
     *
     * Primary source: Sales Agent Commissions filtered by commission release_date
     * (same timeline as /sales-agent-commissions). Vehicle releases fill gaps for
     * units that have no commission row yet.
     *
     * Credit priority for vehicle-only rows:
     * sales_person_reserved → sales_person_release → sales_agent_name → Unassigned
     */
    protected function buildSalesExecutiveAnalytics(array $window, string $viewMode = 'team'): array
    {
        $salesAgents = \App\Models\SalesAgent::query()
            ->with('executiveAgent')
            ->get(['id', 'name', 'sales_agent_id', 'executive_agent_id', 'status']);

        $executives = \App\Models\ExecutiveAgent::query()
            ->with(['salesAgents:id,name,executive_agent_id'])
            ->get(['id', 'name', 'executive_code', 'status']);

        $agentNameMap = [];
        foreach ($salesAgents as $agent) {
            $key = $this->normalizePersonName($agent->name);
            if ($key !== '') {
                $agentNameMap[$key] = $agent;
            }
        }

        $execNameMap = [];
        foreach ($executives as $exec) {
            $key = $this->normalizePersonName($exec->name);
            if ($key !== '') {
                $execNameMap[$key] = $exec;
            }
        }

        $commissionRows = $this->commissionCreditRows($window, $agentNameMap, $execNameMap);
        $vehicles = $this->soldVehiclesBase($window);

        $vehicleRows = $vehicles->map(function (Vehicle $v) use ($agentNameMap, $execNameMap) {
            return $this->mapVehicleToExecRow($v, $agentNameMap, $execNameMap);
        })->filter(fn ($r) => ! empty($r['release_date_raw']))->values();

        // Prefer commission credit when a plate/vehicle already has a commission in-range.
        $coveredKeys = $commissionRows
            ->flatMap(function ($r) {
                $keys = [];
                if (! empty($r['vehicle_id'])) {
                    $keys[] = 'vid:'.$r['vehicle_id'];
                }
                $plate = $this->normalizePlateKey($r['plate'] ?? '');
                if ($plate !== '') {
                    $keys[] = 'plate:'.$plate;
                }

                return $keys;
            })
            ->unique()
            ->all();

        $vehicleOnly = $vehicleRows->reject(function ($r) use ($coveredKeys) {
            if (! empty($r['vehicle_id']) && in_array('vid:'.$r['vehicle_id'], $coveredKeys, true)) {
                return true;
            }
            $plate = $this->normalizePlateKey($r['plate'] ?? '');

            return $plate !== '' && in_array('plate:'.$plate, $coveredKeys, true);
        })->values();

        $rows = $commissionRows->concat($vehicleOnly)->values();
        $saleRows = $this->reservationCreditRows($window, $agentNameMap, $execNameMap);

        $teamRanking = $this->aggregatePerformerRanking($rows);
        $agentRanking = $this->aggregatePerformerRanking(
            $rows->filter(fn ($r) => in_array($r['role'], ['Sales Agent', 'External Agent'], true)
                || ($r['agent_name'] ?? null) !== null)
                ->map(function ($r) {
                    // Prefer explicit agent name when present
                    if (! empty($r['agent_name'])) {
                        $r['name'] = $r['agent_name'];
                        $r['name_key'] = $this->normalizePersonName($r['agent_name']);
                    }
                    return $r;
                })
                ->values()
        );

        $executiveRanking = $this->buildExecutiveRanking($rows, $executives);

        if ($viewMode === 'agents') {
            $activeRanking = $agentRanking;
            $viewLabel = 'Sales Agents';
        } elseif ($viewMode === 'executives') {
            $activeRanking = $executiveRanking;
            $viewLabel = 'Sales Executives';
        } else {
            $activeRanking = $teamRanking;
            $viewLabel = 'Sales Team';
        }

        $topByUnits = $activeRanking->take(10)->values();
        $topBySales = $activeRanking->sortByDesc('sales')->take(10)->values();

        $unitCount = $rows->count();
        $totalSales = (float) $rows->sum('sold_price');
        $performerCount = $activeRanking->count();
        $leader = $activeRanking->first();

        $monthlyByLeader = collect();
        if ($leader) {
            $leaderKey = $leader['name_key'] ?? $this->normalizePersonName($leader['name']);
            $leaderRows = $viewMode === 'executives'
                ? $rows->filter(function ($r) use ($leader) {
                    return $this->normalizePersonName($r['executive_name'] ?? '') === $this->normalizePersonName($leader['name'])
                        || $this->normalizePersonName($r['name']) === $this->normalizePersonName($leader['name']);
                })
                : $rows->filter(fn ($r) => ($r['name_key'] ?? '') === $leaderKey);

            if ($viewMode === 'agents') {
                $leaderRows = $rows->filter(function ($r) use ($leaderKey, $leader) {
                    $agentKey = $this->normalizePersonName($r['agent_name'] ?? $r['name']);
                    return $agentKey === $leaderKey || $this->normalizePersonName($r['name']) === $this->normalizePersonName($leader['name']);
                });
            }

            $monthlyByLeader = $leaderRows
                ->groupBy(fn ($r) => Carbon::parse($r['release_date_raw'])->format('Y-m'))
                ->sortKeys()
                ->map(function ($group, $ym) {
                    return [
                        'label' => Carbon::createFromFormat('Y-m', $ym)->format('M Y'),
                        'count' => $group->count(),
                        'sales' => (float) $group->sum('sold_price'),
                    ];
                })
                ->values();
        }

        return [
            'has_data' => $unitCount > 0 && $performerCount > 0,
            'view_label' => $viewLabel,
            'summary' => [
                ['label' => 'Released Units (range)', 'value' => $unitCount, 'is_currency' => false],
                ['label' => 'Total Sales Amount', 'value' => $totalSales, 'is_currency' => true],
                ['label' => $viewLabel.' in Ranking', 'value' => $performerCount, 'is_currency' => false],
                [
                    'label' => 'Top Performer',
                    'value' => $leader
                        ? ($leader['name'].' · '.number_format($leader['units']).' units')
                        : '—',
                    'is_text' => true,
                ],
            ],
            'charts' => [
                'top_units' => [
                    'title' => 'Top '.$viewLabel.' by Units Released',
                    'labels' => $topByUnits->pluck('name')->all(),
                    'data' => $topByUnits->pluck('units')->all(),
                    'type' => 'bar',
                    'index_axis' => 'y',
                    'dataset_label' => 'Units',
                ],
                'top_sales' => [
                    'title' => 'Top '.$viewLabel.' by Sales Amount',
                    'labels' => $topBySales->pluck('name')->all(),
                    'data' => $topBySales->pluck('sales')->all(),
                    'type' => 'bar',
                    'index_axis' => 'y',
                    'dataset_label' => 'Sales Amount (₱)',
                ],
                'leader_monthly' => [
                    'title' => $leader
                        ? ('Monthly Sales — '.$leader['name'])
                        : 'Monthly Sales — Top Performer',
                    'labels' => $monthlyByLeader->pluck('label')->all(),
                    'data' => $monthlyByLeader->pluck('sales')->all(),
                    'type' => 'line',
                    'dataset_label' => 'Sales Amount (₱)',
                ],
            ],
            'tables' => [
                'ranking' => $activeRanking->values()->all(),
                'team' => $teamRanking->values()->all(),
                'agents' => $agentRanking->values()->all(),
                'executives' => $executiveRanking->values()->all(),
                'recent_deals' => $rows
                    ->sortByDesc('release_date_raw')
                    ->take(25)
                    ->map(fn ($r) => [
                        'date' => Carbon::parse($r['release_date_raw'])->format('M d, Y'),
                        'plate' => $r['plate'],
                        'unit' => $r['unit'],
                        'name' => $r['name'],
                        'role' => $r['role'],
                        'sales' => $r['sold_price'],
                    ])
                    ->values()
                    ->all(),
            ],
            'metric_reports' => $this->buildSalesExecMetricReports($rows, $saleRows, $viewMode, $executives, $viewLabel),
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, array<string, mixed>>  $rows
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    protected function aggregatePerformerRanking(Collection $rows): Collection
    {
        return $rows
            ->groupBy(fn ($r) => $r['name_key'] !== '' ? $r['name_key'] : 'unassigned')
            ->map(function ($group) {
                $first = $group->first();
                $units = $group->count();
                $sales = (float) $group->sum('sold_price');

                return [
                    'name' => $first['name'] ?? 'Unassigned',
                    'name_key' => $first['name_key'] ?? 'unassigned',
                    'role' => $first['role'] ?? 'Sales Team',
                    'executive_name' => $group->pluck('executive_name')->filter()->unique()->implode(', ') ?: null,
                    'units' => $units,
                    'sales' => $sales,
                    'avg_sale' => $units > 0 ? $sales / $units : 0.0,
                    'share_units' => 0.0,
                    'share_sales' => 0.0,
                ];
            })
            ->sortByDesc('units')
            ->values()
            ->pipe(function (Collection $ranking) {
                $totalUnits = max((int) $ranking->sum('units'), 1);
                $totalSales = max((float) $ranking->sum('sales'), 0.00001);

                return $ranking->map(function ($row) use ($totalUnits, $totalSales) {
                    $row['share_units'] = round(($row['units'] / $totalUnits) * 100, 1);
                    $row['share_sales'] = round(($row['sales'] / $totalSales) * 100, 1);

                    return $row;
                });
            });
    }

    /**
     * Roll released-unit credit up to each executive (direct name match + linked sales agents).
     *
     * @param  \Illuminate\Support\Collection<int, array<string, mixed>>  $rows
     * @param  \Illuminate\Support\Collection<int, \App\Models\ExecutiveAgent>  $executives
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    protected function buildExecutiveRanking(Collection $rows, Collection $executives): Collection
    {
        $totalUnits = max($rows->count(), 1);
        $totalSales = max((float) $rows->sum('sold_price'), 0.00001);

        $ranking = $executives->map(function ($exec) use ($rows) {
            $names = collect([$exec->name])
                ->merge($exec->salesAgents->pluck('name'))
                ->map(fn ($n) => $this->normalizePersonName($n))
                ->filter()
                ->unique()
                ->values();

            $matched = $rows->filter(function ($r) use ($names, $exec) {
                if ($names->contains($r['name_key'] ?? '')) {
                    return true;
                }
                if ($this->normalizePersonName($r['executive_name'] ?? '') === $this->normalizePersonName($exec->name)) {
                    return true;
                }
                if ($this->normalizePersonName($r['agent_name'] ?? '') !== '' && $names->contains($this->normalizePersonName($r['agent_name']))) {
                    return true;
                }

                return false;
            });

            $units = $matched->count();
            $sales = (float) $matched->sum('sold_price');

            return [
                'name' => $exec->name,
                'name_key' => $this->normalizePersonName($exec->name),
                'role' => 'Executive',
                'executive_name' => $exec->name,
                'team_size' => $exec->salesAgents->count(),
                'units' => $units,
                'sales' => $sales,
                'avg_sale' => $units > 0 ? $sales / $units : 0.0,
                'share_units' => 0.0,
                'share_sales' => 0.0,
            ];
        })
            ->filter(fn ($r) => $r['units'] > 0)
            ->sortByDesc('units')
            ->values();

        // Also include people credited as Executive from status names that match no roster row
        $rosterKeys = $ranking->pluck('name_key')->all();
        $extra = $rows
            ->filter(fn ($r) => ($r['role'] ?? '') === 'Executive')
            ->groupBy('name_key')
            ->filter(fn ($_, $key) => ! in_array($key, $rosterKeys, true))
            ->map(function ($group) {
                $first = $group->first();
                $units = $group->count();
                $sales = (float) $group->sum('sold_price');

                return [
                    'name' => $first['name'],
                    'name_key' => $first['name_key'],
                    'role' => 'Executive',
                    'executive_name' => $first['name'],
                    'team_size' => null,
                    'units' => $units,
                    'sales' => $sales,
                    'avg_sale' => $units > 0 ? $sales / $units : 0.0,
                    'share_units' => 0.0,
                    'share_sales' => 0.0,
                ];
            })
            ->values();

        return $ranking->concat($extra)
            ->sortByDesc('units')
            ->values()
            ->map(function ($row) use ($totalUnits, $totalSales) {
                $row['share_units'] = round(($row['units'] / $totalUnits) * 100, 1);
                $row['share_sales'] = round(($row['sales'] / $totalSales) * 100, 1);

                return $row;
            });
    }

    protected function normalizePersonName(?string $name): string
    {
        $name = strtoupper(trim((string) $name));
        $name = preg_replace('/\s+/', ' ', $name) ?? $name;

        return $name;
    }

    protected function normalizePlateKey(?string $plate): string
    {
        return strtoupper(preg_replace('/[^A-Z0-9]/i', '', (string) $plate) ?? '');
    }

    /**
     * Latest calendar month that has commission or vehicle release activity.
     *
     * @return array{year:int,month:string}
     */
    protected function latestSalesActivityYearMonth(): array
    {
        $now = Carbon::now();

        try {
            $comm = SalesAgentCommission::query()->whereNotNull('release_date')->max('release_date');
            if ($comm) {
                // Prefer commission timeline so the report matches /sales-agent-commissions.
                $latest = Carbon::parse($comm);

                return [
                    'year' => (int) $latest->year,
                    'month' => $latest->format('m'),
                ];
            }
        } catch (\Throwable) {
            // table may be missing on fresh installs
        }

        try {
            $veh = \App\Models\VehicleStatusDetail::query()->whereNotNull('release_date')->max('release_date');
            if ($veh) {
                $latest = Carbon::parse($veh);

                return [
                    'year' => (int) $latest->year,
                    'month' => $latest->format('m'),
                ];
            }
        } catch (\Throwable) {
        }

        return ['year' => (int) $now->year, 'month' => $now->format('m')];
    }

    /**
     * @param  array<string, \App\Models\SalesAgent>  $agentNameMap
     * @param  array<string, \App\Models\ExecutiveAgent>  $execNameMap
     * @return array<string, mixed>
     */
    protected function mapVehicleToExecRow(Vehicle $v, array $agentNameMap, array $execNameMap): array
    {
        $detail = $v->statusDetail;
        $reserved = trim((string) ($detail?->sales_person_reserved ?? ''));
        $release = trim((string) ($detail?->sales_person_release ?? ''));
        $agentName = trim((string) ($detail?->sales_agent_name ?? ''));
        $saleOrigin = trim((string) ($detail?->sale_origin ?? ''));

        $creditName = $reserved !== '' ? $reserved
            : ($release !== '' ? $release
                : ($agentName !== '' ? $agentName : 'Unassigned'));

        $norm = $this->normalizePersonName($creditName);
        $matchedAgent = $agentNameMap[$norm] ?? null;
        $matchedExec = $execNameMap[$norm] ?? null;
        if (! $matchedExec && $matchedAgent?->executiveAgent) {
            $matchedExec = $matchedAgent->executiveAgent;
        }

        $event = $detail?->release_date ?? $detail?->sale_date ?? $v->updated_at;
        $soldPrice = (float) ($v->sold_price ?? 0);
        if ($soldPrice <= 0) {
            $soldPrice = (float) ($detail?->sales_price ?? 0);
        }

        $role = 'Sales Team';
        if ($matchedExec && $this->normalizePersonName($matchedExec->name) === $norm) {
            $role = 'Executive';
        } elseif ($matchedAgent) {
            $role = 'Sales Agent';
        } elseif ($agentName !== '' && $this->normalizePersonName($agentName) === $norm) {
            $role = 'External Agent';
        } elseif (strcasecmp($saleOrigin, 'Agent') === 0 && $agentName !== '') {
            $role = 'External Agent';
        }

        $metrics = $this->execFinancialMetrics(
            $v,
            $detail,
            strtolower(trim((string) ($detail?->cash_financing ?? '')))
        );

        return array_merge($metrics, [
            'vehicle_id' => $v->id,
            'name' => $creditName,
            'name_key' => $norm !== '' ? $norm : 'unassigned',
            'role' => $role,
            'reserved_by' => $reserved !== '' ? $reserved : null,
            'released_by' => $release !== '' ? $release : null,
            'agent_name' => $agentName !== '' ? $agentName : null,
            'sale_origin' => $saleOrigin !== '' ? $saleOrigin : null,
            'executive_name' => $matchedExec?->name,
            'sales_agent_record' => $matchedAgent?->name,
            'plate' => $v->plate_number,
            'unit' => $v->full_name,
            'release_date_raw' => $event ? Carbon::parse($event)->toDateString() : null,
            'sale_date_raw' => $detail?->sale_date ? Carbon::parse($detail->sale_date)->toDateString() : null,
            'source' => 'vehicle',
        ]);
    }

    /**
     * Commission rows keyed by commission.release_date (matches Sales Agent Commissions page).
     *
     * @param  array<string, \App\Models\SalesAgent>  $agentNameMap
     * @param  array<string, \App\Models\ExecutiveAgent>  $execNameMap
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    protected function commissionCreditRows(array $window, array $agentNameMap, array $execNameMap): Collection
    {
        try {
            $commissions = SalesAgentCommission::query()
                ->with(['salesAgent.executiveAgent', 'vehicle.statusDetail', 'vehicle.expense', 'vehicle.make', 'vehicle.vehicleModel'])
                ->whereNotNull('release_date')
                ->get();
        } catch (\Throwable) {
            return collect();
        }

        return $commissions
            ->filter(fn (SalesAgentCommission $c) => $this->inWindow($c->release_date, $window))
            ->map(function (SalesAgentCommission $c) use ($agentNameMap, $execNameMap) {
                $agent = $c->salesAgent;
                $agentName = trim((string) ($agent?->name ?: $c->agent_name ?: ''));
                if ($agentName === '') {
                    $agentName = 'Unassigned';
                }
                $norm = $this->normalizePersonName($agentName);
                $matchedAgent = $agent ?: ($agentNameMap[$norm] ?? null);
                $matchedExec = $matchedAgent?->executiveAgent;
                if (! $matchedExec) {
                    $matchedExec = $execNameMap[$norm] ?? null;
                }

                $vehicle = $c->vehicle;
                $detail = $vehicle?->statusDetail;
                $txn = strtoupper(trim((string) ($c->transaction_type ?? '')));
                $cashFinancing = strtolower(trim((string) ($detail?->cash_financing ?? '')));
                if ($txn === 'CASH') {
                    $cashFinancing = 'cash';
                } elseif ($txn === 'FINANCING') {
                    $cashFinancing = 'financing';
                }

                $metrics = $vehicle
                    ? $this->execFinancialMetrics($vehicle, $detail, $cashFinancing)
                    : [
                        'sold_price' => (float) ($c->amount ?? 0),
                        'posted_price' => 0.0,
                        'reservation_amount' => 0.0,
                        'is_cash' => $txn === 'CASH',
                        'is_financing' => $txn === 'FINANCING',
                        'discount' => 0.0,
                        'amount_financed' => $txn === 'FINANCING' ? (float) ($c->amount ?? 0) : 0.0,
                        'gross' => (float) ($c->amount ?? 0),
                        'net' => (float) ($c->amount ?? 0),
                    ];

                $unitLabel = $c->display_unit;
                if ($unitLabel === '—' && $c->plate_number) {
                    $unitLabel = $c->plate_number;
                }

                return array_merge($metrics, [
                    'vehicle_id' => $c->vehicle_id,
                    'name' => $agentName,
                    'name_key' => $norm !== '' ? $norm : 'unassigned',
                    'role' => $matchedAgent ? 'Sales Agent' : 'External Agent',
                    'reserved_by' => null,
                    'released_by' => null,
                    'agent_name' => $agentName,
                    'sale_origin' => 'Agent',
                    'executive_name' => $matchedExec?->name,
                    'sales_agent_record' => $matchedAgent?->name ?? $agentName,
                    'plate' => $c->plate_number ?: ($vehicle?->plate_number ?? ''),
                    'unit' => $unitLabel,
                    'release_date_raw' => $c->release_date ? Carbon::parse($c->release_date)->toDateString() : null,
                    'sale_date_raw' => $detail?->sale_date
                        ? Carbon::parse($detail->sale_date)->toDateString()
                        : ($c->release_date ? Carbon::parse($c->release_date)->toDateString() : null),
                    'commission_amount' => (float) ($c->amount ?? 0),
                    'exec_commission_amount' => (float) ($c->sales_executive_commission ?? 0),
                    'source' => 'commission',
                ]);
            })
            ->filter(fn ($r) => ! empty($r['release_date_raw']))
            ->values();
    }

    /**
     * Shared cash/financing/gross/net metrics for a vehicle (+ optional status detail).
     *
     * @return array<string, mixed>
     */
    protected function execFinancialMetrics(Vehicle $v, $detail, string $cashFinancing): array
    {
        $isCash = str_contains($cashFinancing, 'cash');
        $isFinancing = str_contains($cashFinancing, 'financ');
        $soldPrice = (float) ($v->sold_price ?? 0);
        if ($soldPrice <= 0) {
            $soldPrice = (float) ($detail?->sales_price ?? 0);
        }
        $reservationAmount = (float) ($detail?->sale_reservation_amount ?? 0);
        $postedPrice = (float) ($v->posted_price ?? 0);
        $purchasePrice = (float) ($v->purchase_price ?? 0);
        $repairCost = (float) ($v->expense?->total_repair_cost ?? 0);
        $agentCost = (float) ($detail?->agent_cost ?? 0);
        $transferCost = (float) ($detail?->transfer_cost ?? 0);
        $financeRevenue1 = (float) ($detail?->finance_revenue_1 ?? 0);
        $financeRevenue2 = (float) ($detail?->finance_revenue_2 ?? 0);
        $gross = $soldPrice + $financeRevenue1 + $financeRevenue2;
        $unitCost = $purchasePrice + $repairCost + $agentCost + $transferCost;
        $net = $gross - $unitCost;
        $discount = ($isCash && $postedPrice > 0) ? max($postedPrice - $soldPrice, 0) : 0.0;
        $amountFinanced = 0.0;
        if ($isFinancing) {
            $amountFinanced = max($soldPrice - $reservationAmount, 0);
            if ($amountFinanced <= 0) {
                $amountFinanced = $soldPrice;
            }
        }

        return [
            'sold_price' => $soldPrice,
            'posted_price' => $postedPrice,
            'reservation_amount' => $reservationAmount,
            'is_cash' => $isCash,
            'is_financing' => $isFinancing,
            'discount' => $discount,
            'amount_financed' => $amountFinanced,
            'gross' => $gross,
            'net' => $net,
        ];
    }

    /**
     * Reservation/sale rows (sale_date in window) credited primarily to sales_person_reserved.
     *
     * @param  array<string, \App\Models\SalesAgent>  $agentNameMap
     * @param  array<string, \App\Models\ExecutiveAgent>  $execNameMap
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    protected function reservationCreditRows(array $window, array $agentNameMap, array $execNameMap): Collection
    {
        return Vehicle::with(['statusDetail', 'expense', 'branchLocation'])
            ->whereIn('status', ['Reserved', 'Released'])
            ->whereHas('statusDetail', function ($q) {
                $q->whereNotNull('sale_date');
            })
            ->get()
            ->filter(fn (Vehicle $v) => $this->inWindow($v->statusDetail?->sale_date, $window))
            ->map(function (Vehicle $v) use ($agentNameMap, $execNameMap) {
                $detail = $v->statusDetail;
                $reserved = trim((string) ($detail?->sales_person_reserved ?? ''));
                $release = trim((string) ($detail?->sales_person_release ?? ''));
                $agentName = trim((string) ($detail?->sales_agent_name ?? ''));
                $creditName = $reserved !== '' ? $reserved
                    : ($release !== '' ? $release
                        : ($agentName !== '' ? $agentName : 'Unassigned'));
                $norm = $this->normalizePersonName($creditName);
                $matchedAgent = $agentNameMap[$norm] ?? null;
                $matchedExec = $execNameMap[$norm] ?? null;
                if (! $matchedExec && $matchedAgent?->executiveAgent) {
                    $matchedExec = $matchedAgent->executiveAgent;
                }

                $cashFinancing = strtolower(trim((string) ($detail?->cash_financing ?? '')));

                return [
                    'name' => $creditName,
                    'name_key' => $norm !== '' ? $norm : 'unassigned',
                    'agent_name' => $agentName !== '' ? $agentName : null,
                    'executive_name' => $matchedExec?->name,
                    'reservation_amount' => (float) ($detail?->sale_reservation_amount ?? 0),
                    'is_cash' => str_contains($cashFinancing, 'cash'),
                    'is_financing' => str_contains($cashFinancing, 'financ'),
                    'sale_date_raw' => $detail?->sale_date ? Carbon::parse($detail->sale_date)->toDateString() : null,
                ];
            })
            ->filter(fn ($r) => ! empty($r['sale_date_raw']))
            ->values();
    }

    /**
     * Excel-style Sales Exec metric reports (1–4).
     *
     * @param  \Illuminate\Support\Collection<int, array<string, mixed>>  $releaseRows
     * @param  \Illuminate\Support\Collection<int, array<string, mixed>>  $saleRows
     * @param  \Illuminate\Support\Collection<int, \App\Models\ExecutiveAgent>  $executives
     * @return array<string, array<string, mixed>>
     */
    protected function buildSalesExecMetricReports(
        Collection $releaseRows,
        Collection $saleRows,
        string $viewMode,
        Collection $executives,
        string $viewLabel
    ): array {
        $people = $this->metricReportPeople($releaseRows, $saleRows, $viewMode, $executives);

        $totalSales = max((int) $saleRows->count(), 0);
        $totalReleases = max((int) $releaseRows->count(), 0);
        $totalCashReleases = max((int) $releaseRows->where('is_cash', true)->count(), 0);
        $totalFinancingReleases = max((int) $releaseRows->where('is_financing', true)->count(), 0);

        $salesReservation = $people->map(function ($person) use ($releaseRows, $saleRows, $totalSales, $totalReleases) {
            $sales = $this->rowsForMetricPerson($saleRows, $person);
            $releases = $this->rowsForMetricPerson($releaseRows, $person);
            $reservationPool = $sales
                ->filter(fn ($r) => empty($r['is_cash']))
                ->pluck('reservation_amount')
                ->filter(fn ($amt) => (float) $amt > 0);
            $salesCount = $sales->count();
            $releaseCount = $releases->count();

            return [
                'name' => $person['name'],
                'sales_count' => $salesCount,
                'sales_pct' => $totalSales > 0 ? round(($salesCount / $totalSales) * 100, 1) : 0.0,
                'release_count' => $releaseCount,
                'release_pct' => $totalReleases > 0 ? round(($releaseCount / $totalReleases) * 100, 1) : 0.0,
                'avg_reservation' => $reservationPool->count() > 0 ? (float) $reservationPool->avg() : 0.0,
            ];
        })
            ->filter(fn ($r) => $r['sales_count'] > 0 || $r['release_count'] > 0)
            ->sortByDesc('release_count')
            ->values();

        $cashSummary = $people->map(function ($person) use ($releaseRows, $totalCashReleases) {
            $releases = $this->rowsForMetricPerson($releaseRows, $person)->where('is_cash', true);
            $count = $releases->count();
            $discountTotal = (float) $releases->sum('discount');

            return [
                'name' => $person['name'],
                'cash_releases' => $count,
                'cash_pct' => $totalCashReleases > 0 ? round(($count / $totalCashReleases) * 100, 1) : 0.0,
                'discount_total' => $discountTotal,
                'discount_avg' => $count > 0 ? $discountTotal / $count : 0.0,
            ];
        })
            ->filter(fn ($r) => $r['cash_releases'] > 0)
            ->sortByDesc('cash_releases')
            ->values();

        $financingSummary = $people->map(function ($person) use ($releaseRows, $totalFinancingReleases) {
            $releases = $this->rowsForMetricPerson($releaseRows, $person)->where('is_financing', true);
            $count = $releases->count();
            $financedTotal = (float) $releases->sum('amount_financed');

            return [
                'name' => $person['name'],
                'financing_releases' => $count,
                'financing_pct' => $totalFinancingReleases > 0 ? round(($count / $totalFinancingReleases) * 100, 1) : 0.0,
                'financed_total' => $financedTotal,
                'financed_avg' => $count > 0 ? $financedTotal / $count : 0.0,
            ];
        })
            ->filter(fn ($r) => $r['financing_releases'] > 0)
            ->sortByDesc('financing_releases')
            ->values();

        $grossNet = $people->map(function ($person) use ($releaseRows) {
            $releases = $this->rowsForMetricPerson($releaseRows, $person);
            $count = $releases->count();
            $grossTotal = (float) $releases->sum('gross');
            $netTotal = (float) $releases->sum('net');

            return [
                'name' => $person['name'],
                'units' => $count,
                'gross_total' => $grossTotal,
                'gross_avg' => $count > 0 ? $grossTotal / $count : 0.0,
                'net_total' => $netTotal,
                'net_avg' => $count > 0 ? $netTotal / $count : 0.0,
            ];
        })
            ->filter(fn ($r) => $r['units'] > 0)
            ->sortByDesc('gross_total')
            ->values();

        $personLabel = $viewMode === 'executives' ? 'Sales Exec' : ($viewMode === 'agents' ? 'Sales Agent' : 'Sales Person');

        return [
            'sales_reservation' => [
                'key' => 'sales_reservation',
                'title' => 'All Sales and Reservation by '.$viewLabel,
                'subtitle' => 'Individual & total sales/releases with average reservation (excludes spot cash)',
                'person_label' => $personLabel,
                'rows' => $salesReservation->all(),
                'totals' => [
                    'sales_count' => (int) $salesReservation->sum('sales_count'),
                    'release_count' => (int) $salesReservation->sum('release_count'),
                    'avg_reservation' => (function () use ($saleRows) {
                        $pool = $saleRows->filter(fn ($r) => empty($r['is_cash']) && (float) ($r['reservation_amount'] ?? 0) > 0);

                        return $pool->count() > 0 ? (float) $pool->avg('reservation_amount') : 0.0;
                    })(),
                ],
            ],
            'cash_summary' => [
                'key' => 'cash_summary',
                'title' => 'Cash Only Sales Summary',
                'subtitle' => 'Cash releases, share of cash releases, and discounts (posted − sold)',
                'person_label' => $personLabel,
                'rows' => $cashSummary->all(),
                'totals' => [
                    'cash_releases' => (int) $cashSummary->sum('cash_releases'),
                    'discount_total' => (float) $cashSummary->sum('discount_total'),
                    'discount_avg' => ((int) $cashSummary->sum('cash_releases') > 0)
                        ? ((float) $cashSummary->sum('discount_total') / (int) $cashSummary->sum('cash_releases'))
                        : 0.0,
                ],
            ],
            'financing_summary' => [
                'key' => 'financing_summary',
                'title' => 'Financing Only Sales Summary',
                'subtitle' => 'Financing releases and amount financed (sold price − reservation)',
                'person_label' => $personLabel,
                'rows' => $financingSummary->all(),
                'totals' => [
                    'financing_releases' => (int) $financingSummary->sum('financing_releases'),
                    'financed_total' => (float) $financingSummary->sum('financed_total'),
                    'financed_avg' => ((int) $financingSummary->sum('financing_releases') > 0)
                        ? ((float) $financingSummary->sum('financed_total') / (int) $financingSummary->sum('financing_releases'))
                        : 0.0,
                ],
            ],
            'gross_net' => [
                'key' => 'gross_net',
                'title' => 'Gross and Net Summary',
                'subtitle' => 'Gross = sold price + finance revenue; Net = gross − (purchase + repairs + agent + transfer)',
                'person_label' => $personLabel,
                'rows' => $grossNet->all(),
                'totals' => [
                    'units' => (int) $grossNet->sum('units'),
                    'gross_total' => (float) $grossNet->sum('gross_total'),
                    'gross_avg' => ((int) $grossNet->sum('units') > 0)
                        ? ((float) $grossNet->sum('gross_total') / (int) $grossNet->sum('units'))
                        : 0.0,
                    'net_total' => (float) $grossNet->sum('net_total'),
                    'net_avg' => ((int) $grossNet->sum('units') > 0)
                        ? ((float) $grossNet->sum('net_total') / (int) $grossNet->sum('units'))
                        : 0.0,
                ],
            ],
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, array<string, mixed>>  $releaseRows
     * @param  \Illuminate\Support\Collection<int, array<string, mixed>>  $saleRows
     * @param  \Illuminate\Support\Collection<int, \App\Models\ExecutiveAgent>  $executives
     * @return \Illuminate\Support\Collection<int, array{name:string,name_key:string,match_keys:array<int,string>}>
     */
    protected function metricReportPeople(
        Collection $releaseRows,
        Collection $saleRows,
        string $viewMode,
        Collection $executives
    ): Collection {
        if ($viewMode === 'executives') {
            $fromRoster = $executives->map(function ($exec) {
                $keys = collect([$exec->name])
                    ->merge($exec->salesAgents->pluck('name'))
                    ->map(fn ($n) => $this->normalizePersonName($n))
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                return [
                    'name' => $exec->name,
                    'name_key' => $this->normalizePersonName($exec->name),
                    'match_keys' => $keys,
                    'executive_key' => $this->normalizePersonName($exec->name),
                ];
            });

            $known = $fromRoster->pluck('name_key')->all();
            $extra = $releaseRows->concat($saleRows)
                ->filter(fn ($r) => ($r['role'] ?? '') === 'Executive' || ! empty($r['executive_name']))
                ->map(fn ($r) => [
                    'name' => $r['executive_name'] ?: $r['name'],
                    'name_key' => $this->normalizePersonName($r['executive_name'] ?: $r['name']),
                ])
                ->filter(fn ($r) => $r['name_key'] !== '' && ! in_array($r['name_key'], $known, true))
                ->unique('name_key')
                ->map(fn ($r) => [
                    'name' => $r['name'],
                    'name_key' => $r['name_key'],
                    'match_keys' => [$r['name_key']],
                    'executive_key' => $r['name_key'],
                ]);

            return $fromRoster->concat($extra)->values();
        }

        if ($viewMode === 'agents') {
            return $releaseRows->concat($saleRows)
                ->map(function ($r) {
                    $name = $r['agent_name'] ?: $r['name'];
                    $key = $this->normalizePersonName($name);

                    return [
                        'name' => $name,
                        'name_key' => $key !== '' ? $key : 'unassigned',
                        'match_keys' => [$key !== '' ? $key : 'unassigned'],
                    ];
                })
                ->unique('name_key')
                ->values();
        }

        return $releaseRows->concat($saleRows)
            ->map(fn ($r) => [
                'name' => $r['name'],
                'name_key' => $r['name_key'] ?: 'unassigned',
                'match_keys' => [$r['name_key'] ?: 'unassigned'],
            ])
            ->unique('name_key')
            ->values();
    }

    /**
     * @param  \Illuminate\Support\Collection<int, array<string, mixed>>  $rows
     * @param  array{name:string,name_key:string,match_keys?:array<int,string>,executive_key?:string}  $person
     * @return \Illuminate\Support\Collection<int, array<string, mixed>>
     */
    protected function rowsForMetricPerson(Collection $rows, array $person): Collection
    {
        $keys = collect($person['match_keys'] ?? [$person['name_key']])->filter()->values();
        $executiveKey = $person['executive_key'] ?? null;

        return $rows->filter(function ($r) use ($keys, $executiveKey) {
            if ($keys->contains($r['name_key'] ?? '')) {
                return true;
            }
            if ($executiveKey && $this->normalizePersonName($r['executive_name'] ?? '') === $executiveKey) {
                return true;
            }
            $agentKey = $this->normalizePersonName($r['agent_name'] ?? '');
            if ($agentKey !== '' && $keys->contains($agentKey)) {
                return true;
            }

            return false;
        })->values();
    }

    /**
     * Sales analytics for Released units: monthly volume/revenue and fast-selling mix.
     */
    protected function buildSalesAnalytics(array $window, string $locationFilter = ''): array
    {
        $vehicles = $this->soldVehiclesBase($window);
        $excelAligned = $vehicles->contains(fn (Vehicle $v) => (bool) $v->getAttribute('excel_aligned'));

        $rows = $vehicles->map(function (Vehicle $v) use ($locationFilter) {
            $attrs = $v->getAttributes();
            $make = trim((string) ($attrs['make'] ?? ''));
            $model = trim((string) ($attrs['model'] ?? ''));
            $bodyType = trim((string) ($attrs['body_type'] ?? ''));
            $event = $v->getAttribute('excel_period_release_date')
                ?? $v->statusDetail?->release_date
                ?? $v->statusDetail?->sale_date
                ?? $v->updated_at;
            $purchase = $v->purchase_date ?? $v->created_at;
            $soldPrice = (float) ($v->sold_price ?? 0);
            if ($soldPrice <= 0) {
                $soldPrice = (float) ($v->statusDetail?->sales_price ?? 0);
            }
            $excelSalesPrice = $v->getAttribute('excel_period_sales_price');
            if ($excelSalesPrice !== null && $excelSalesPrice !== '') {
                $soldPrice = (float) $excelSalesPrice;
            }
            $purchasePrice = (float) ($v->purchase_price ?? 0);
            $repairCost = (float) ($v->expense?->total_repair_cost ?? 0);
            $agentCost = (float) ($v->statusDetail?->agent_cost ?? 0);
            $transferCost = (float) ($v->statusDetail?->transfer_cost ?? 0);
            $financeRevenue1 = (float) ($v->statusDetail?->finance_revenue_1 ?? 0);
            $financeRevenue2 = (float) ($v->statusDetail?->finance_revenue_2 ?? 0);
            // Prefer Excel TOTAL REVENUE / TOTAL COSTS / TOTAL PROFIT when date-filtered against the workbook.
            $excelGross = $v->getAttribute('excel_period_total_revenue');
            $excelCosts = $v->getAttribute('excel_period_total_costs');
            $excelProfit = $v->getAttribute('excel_period_total_profit');

            // Dual-sheet plates (e.g. Flagship + Annex) keep per-branch Excel financials.
            // One location → that sheet. All Locations → sum every sheet so combined = Flagship + Annex.
            $branchFinancials = $v->getAttribute('excel_period_branch_financials');
            $resolved = $this->resolveExcelFinancialsForLocation(
                is_array($branchFinancials) ? $branchFinancials : [],
                $locationFilter,
                $excelGross,
                $excelCosts,
                $excelProfit,
                $soldPrice,
                $event
            );
            $excelGross = $resolved['total_revenue'];
            $excelCosts = $resolved['total_costs'];
            $excelProfit = $resolved['total_profit'];
            $soldPrice = $resolved['sales_price'];
            $event = $resolved['release_date'];

            $grossRevenue = ($excelGross !== null && $excelGross !== '')
                ? (float) $excelGross
                : ($soldPrice + $financeRevenue1 + $financeRevenue2);
            $unitCost = ($excelCosts !== null && $excelCosts !== '')
                ? (float) $excelCosts
                : ($purchasePrice + $repairCost + $agentCost + $transferCost);
            $daysToSell = ($purchase && $event)
                ? Carbon::parse($purchase)->diffInDays(Carbon::parse($event))
                : null;
            $location = trim((string) ($v->getAttribute('excel_period_branch') ?? $v->branchLocation?->name ?? ''));
            if ($locationFilter !== '') {
                $location = $locationFilter;
            } elseif ($location === '') {
                $location = 'No Location';
            }
            $locations = $v->getAttribute('excel_period_branches');
            if (! is_array($locations) || $locations === []) {
                $locations = [$location];
            }

            return [
                'plate' => (string) ($v->plate_number ?? ''),
                'make' => $make !== '' ? $make : 'Unknown',
                'model' => $model !== '' ? $model : 'Unknown',
                'make_model' => trim(($make !== '' ? $make : 'Unknown').' '.($model !== '' ? $model : 'Unknown')),
                'body_type' => $bodyType !== '' ? $bodyType : 'Unspecified',
                'location' => $location,
                'locations' => array_values(array_unique(array_map('strval', $locations))),
                'branch_financials' => is_array($branchFinancials) ? $branchFinancials : [],
                'sold_price' => $soldPrice,
                'finance_revenue_1' => $financeRevenue1,
                'finance_revenue_2' => $financeRevenue2,
                'gross_revenue' => $grossRevenue,
                'purchase_price' => $purchasePrice,
                'repair_cost' => $repairCost,
                'agent_cost' => $agentCost,
                'transfer_cost' => $transferCost,
                'unit_cost' => $unitCost,
                'unit_net' => $grossRevenue - $unitCost,
                'excel_profit' => ($excelProfit !== null && $excelProfit !== '') ? (float) $excelProfit : null,
                'days_to_sell' => $daysToSell,
                'release_date_raw' => $event ? Carbon::parse($event)->toDateString() : null,
            ];
        })->filter(fn ($r) => ! empty($r['release_date_raw']))->values();

        if ($locationFilter !== '') {
            $rows = $rows
                ->filter(function ($r) use ($locationFilter) {
                    $locations = $r['locations'] ?? [($r['location'] ?? '')];
                    foreach ($locations as $locName) {
                        if (strcasecmp((string) $locName, $locationFilter) === 0) {
                            return true;
                        }
                    }

                    return false;
                })
                ->values();
        }

        $unitCount = $rows->count();
        $totalSales = (float) $rows->sum('sold_price');
        $totalFinance1 = (float) $rows->sum('finance_revenue_1');
        $totalFinance2 = (float) $rows->sum('finance_revenue_2');
        $vehicleGrossSales = (float) $rows->sum('gross_revenue');
        // Miscellaneous (incl. Total Forfeit Profit) is always added to Gross Sales for the
        // selected location filter: All = every location, Flagship/Annex = that location only.
        $miscellaneousRows = $this->miscellaneousTransactionsInWindow($window, $locationFilter);
        $miscellaneousTotal = (float) $miscellaneousRows->sum('amount');
        $grossSales = $vehicleGrossSales + $miscellaneousTotal;
        $totalPurchase = (float) $rows->sum('purchase_price');
        $totalRepair = (float) $rows->sum('repair_cost');
        $totalAgent = (float) $rows->sum('agent_cost');
        $totalTransfer = (float) $rows->sum('transfer_cost');
        $totalCosts = (float) $rows->sum('unit_cost');
        $totalNet = $grossSales - $totalCosts;
        $avgSold = $unitCount > 0 ? $totalSales / $unitCount : 0.0;
        $daysValues = $rows->pluck('days_to_sell')->filter(fn ($d) => $d !== null);
        $avgDays = $daysValues->count() > 0 ? (float) $daysValues->avg() : 0.0;
        $unitsWithDays = $daysValues->count();
        $sourceNote = $excelAligned
            ? 'Unit list follows Unit Report Excel release history for the selected dates (includes Excel-matched units even if DB status is no longer Released, e.g. Forfeited). Location totals match Unit Report sheet counts.'
            : 'Unit list is Released vehicles whose release/sale date falls in the selected period.';
        $money = fn (float $amount): string => '₱'.number_format($amount, 2);
        $moneyLines = function (string $label, float $amount) use ($money): array {
            return ['label' => $label, 'value' => $money($amount)];
        };

        $locationPriority = ['Flagship' => 0, 'Annex' => 1];
        // Prefer Excel Unit Report location chips (per-sheet) so Flagship/Annex match /vehicles.
        $excelLocationCounts = $excelAligned
            ? $this->excelAlignedLocationCounts($window)
            : [];
        if ($excelAligned && $excelLocationCounts !== [] && $locationFilter === '') {
            $locationBreakdown = collect($excelLocationCounts)
                ->map(fn ($loc) => [
                    'label' => (string) ($loc['name'] ?? 'Unknown'),
                    'count' => (int) ($loc['count'] ?? 0),
                ])
                ->filter(fn ($row) => $row['label'] !== '')
                ->sortBy(function ($row) use ($locationPriority) {
                    $priority = $locationPriority[$row['label']] ?? 50;

                    return sprintf('%02d-%06d-%s', $priority, 999999 - (int) $row['count'], $row['label']);
                })
                ->values()
                ->all();
            $unitCount = (int) collect($locationBreakdown)->sum('count');
        } elseif ($excelAligned && $excelLocationCounts !== [] && $locationFilter !== '') {
            $matched = collect($excelLocationCounts)->first(
                fn ($loc) => strcasecmp((string) ($loc['name'] ?? ''), $locationFilter) === 0
            );
            $locationBreakdown = [[
                'label' => $locationFilter,
                'count' => (int) ($matched['count'] ?? $rows->count()),
            ]];
            $unitCount = (int) ($matched['count'] ?? $rows->count());
        } else {
            $locationBreakdown = $rows
                ->groupBy('location')
                ->map(fn ($group, $name) => [
                    'label' => $name,
                    'count' => $group->count(),
                ])
                ->sortBy(function ($row) use ($locationPriority) {
                    $priority = $locationPriority[$row['label']] ?? 50;

                    return sprintf('%02d-%06d-%s', $priority, 999999 - (int) $row['count'], $row['label']);
                })
                ->values()
                ->all();
        }

        $miscByMonth = $miscellaneousRows
            ->filter(fn ($r) => ! empty($r['transaction_date']))
            ->groupBy(fn ($r) => Carbon::parse($r['transaction_date'])->format('Y-m'))
            ->map(fn ($group) => (float) $group->sum('amount'));

        $monthlyBuckets = $rows
            ->groupBy(fn ($r) => Carbon::parse($r['release_date_raw'])->format('Y-m'))
            ->map(function ($group, $ym) {
                return [
                    'ym' => $ym,
                    'label' => Carbon::createFromFormat('Y-m', $ym)->format('M Y'),
                    'count' => $group->count(),
                    'unit_gross' => (float) $group->sum('gross_revenue'),
                    'sales' => (float) $group->sum('gross_revenue'),
                    'avg_days' => (float) ($group->pluck('days_to_sell')->filter(fn ($d) => $d !== null)->avg() ?? 0),
                ];
            });

        foreach ($miscByMonth as $ym => $miscAmount) {
            if (! $monthlyBuckets->has($ym)) {
                $monthlyBuckets->put($ym, [
                    'ym' => $ym,
                    'label' => Carbon::createFromFormat('Y-m', $ym)->format('M Y'),
                    'count' => 0,
                    'unit_gross' => 0.0,
                    'sales' => 0.0,
                    'avg_days' => 0.0,
                ]);
            }
            $entry = $monthlyBuckets->get($ym);
            $entry['miscellaneous'] = (float) $miscAmount;
            $entry['sales'] = (float) ($entry['unit_gross'] ?? 0) + (float) $miscAmount;
            $monthlyBuckets->put($ym, $entry);
        }

        $monthly = $monthlyBuckets
            ->map(function ($entry) {
                $entry['miscellaneous'] = (float) ($entry['miscellaneous'] ?? 0);
                $entry['sales'] = (float) ($entry['unit_gross'] ?? 0) + $entry['miscellaneous'];

                return $entry;
            })
            ->sortKeys()
            ->values();

        // Fill every month in the selected window so the chart shows a continuous series.
        $monthlyChart = collect();
        if (! empty($window['from']) && ! empty($window['to'])) {
            $cursor = Carbon::parse($window['from'])->startOfMonth();
            $endMonth = Carbon::parse($window['to'])->startOfMonth();
            $byYm = $monthly->keyBy('ym');
            while ($cursor->lte($endMonth)) {
                $ym = $cursor->format('Y-m');
                $existing = $byYm->get($ym);
                $monthlyChart->push([
                    'ym' => $ym,
                    'label' => $cursor->format('M Y'),
                    'count' => (int) ($existing['count'] ?? 0),
                    'sales' => round((float) ($existing['sales'] ?? 0), 2),
                    'miscellaneous' => round((float) ($existing['miscellaneous'] ?? 0), 2),
                    'avg_days' => (float) ($existing['avg_days'] ?? 0),
                ]);
                $cursor->addMonth();
            }
        } else {
            $monthlyChart = $monthly;
        }

        $miscByLocation = $miscellaneousRows
            ->groupBy(function ($row) {
                $name = trim((string) ($row['location'] ?? ''));

                return $name !== '' ? $name : 'No Location';
            })
            ->map(fn ($group) => (float) $group->sum('amount'));

        $financeBuckets = [];
        foreach ($rows as $r) {
            $slices = [];
            $branchFinancials = $r['branch_financials'] ?? [];
            if ($locationFilter === '' && is_array($branchFinancials) && $branchFinancials !== []) {
                foreach ($branchFinancials as $branchName => $slice) {
                    if (! is_array($slice)) {
                        continue;
                    }
                    $name = trim((string) $branchName);
                    if ($name === '') {
                        $name = 'No Location';
                    }
                    $slices[] = [
                        'label' => $name,
                        'unit_gross' => $this->numericOrNull($slice['total_revenue'] ?? null) ?? 0.0,
                        'costs' => $this->numericOrNull($slice['total_costs'] ?? null) ?? 0.0,
                    ];
                }
            }
            if ($slices === []) {
                $name = trim((string) ($r['location'] ?? ''));
                $slices[] = [
                    'label' => $name !== '' ? $name : 'No Location',
                    'unit_gross' => (float) ($r['gross_revenue'] ?? 0),
                    'costs' => (float) ($r['unit_cost'] ?? 0),
                ];
            }
            foreach ($slices as $slice) {
                $key = $slice['label'];
                if (! isset($financeBuckets[$key])) {
                    $financeBuckets[$key] = [
                        'label' => $key,
                        'units' => 0,
                        'unit_gross' => 0.0,
                        'costs' => 0.0,
                    ];
                }
                $financeBuckets[$key]['units']++;
                $financeBuckets[$key]['unit_gross'] += (float) $slice['unit_gross'];
                $financeBuckets[$key]['costs'] += (float) $slice['costs'];
            }
        }
        $locationFinance = collect($financeBuckets)
            ->map(function ($row) use ($miscByLocation) {
                $misc = (float) ($miscByLocation->get((string) $row['label']) ?? 0.0);
                $unitGross = (float) $row['unit_gross'];

                return [
                    'label' => (string) $row['label'],
                    'units' => (int) $row['units'],
                    'unit_gross' => $unitGross,
                    'miscellaneous' => $misc,
                    'gross' => $unitGross,
                    'costs' => (float) $row['costs'],
                    'net' => $unitGross + $misc - (float) $row['costs'],
                ];
            })
            ->sortBy(function ($row) use ($locationPriority) {
                $priority = $locationPriority[$row['label']] ?? 50;

                return sprintf('%02d-%s', $priority, $row['label']);
            })
            ->values();

        // Include misc-only locations (e.g. Flagship forfeit profit with no unit rows in edge cases).
        foreach ($miscByLocation as $locName => $miscAmount) {
            if ((float) $miscAmount == 0.0) {
                continue;
            }
            $exists = $locationFinance->contains(
                fn ($row) => strcasecmp((string) $row['label'], (string) $locName) === 0
            );
            if ($exists) {
                continue;
            }
            $locationFinance->push([
                'label' => (string) $locName,
                'units' => 0,
                'unit_gross' => 0.0,
                'miscellaneous' => (float) $miscAmount,
                'gross' => 0.0,
                'costs' => 0.0,
                'net' => (float) $miscAmount,
            ]);
        }
        $locationFinance = $locationFinance
            ->sortBy(function ($row) use ($locationPriority) {
                $priority = $locationPriority[$row['label']] ?? 50;

                return sprintf('%02d-%s', $priority, $row['label']);
            })
            ->values();

        // Prefer Unit Report sheet unit counts on location finance when available.
        if ($excelAligned && $excelLocationCounts !== [] && $locationFilter === '') {
            $countByName = collect($excelLocationCounts)->mapWithKeys(
                fn ($loc) => [strtolower((string) ($loc['name'] ?? '')) => (int) ($loc['count'] ?? 0)]
            );
            $locationFinance = $locationFinance->map(function ($row) use ($countByName) {
                $key = strtolower($row['label']);
                if ($countByName->has($key)) {
                    $row['units'] = (int) $countByName->get($key);
                }

                return $row;
            });
        }

        if ($locationFilter === '') {
            foreach (['Flagship', 'Annex'] as $requiredName) {
                $exists = $locationFinance->contains(
                    fn ($row) => strcasecmp((string) $row['label'], $requiredName) === 0
                );
                if ($exists) {
                    continue;
                }
                $misc = 0.0;
                foreach ($miscByLocation as $locName => $miscAmount) {
                    if (strcasecmp((string) $locName, $requiredName) === 0) {
                        $misc = (float) $miscAmount;
                        break;
                    }
                }
                $locationFinance->push([
                    'label' => $requiredName,
                    'units' => 0,
                    'unit_gross' => 0.0,
                    'miscellaneous' => $misc,
                    'gross' => 0.0,
                    'costs' => 0.0,
                    'net' => $misc,
                ]);
            }
            $locationFinance = $locationFinance
                ->sortBy(function ($row) use ($locationPriority) {
                    $priority = $locationPriority[$row['label']] ?? 50;

                    return sprintf('%02d-%s', $priority, $row['label']);
                })
                ->values();
        }

        $fromYmd = ! empty($window['from']) ? Carbon::parse($window['from'])->toDateString() : '';
        $toYmd = ! empty($window['to']) ? Carbon::parse($window['to'])->toDateString() : '';
        $sectionFin = ExcelUnitReconcile::sectionFinancialsForMonth($fromYmd, $toYmd);
        if (is_array($sectionFin) && $sectionFin !== []) {
            $locationFinance = $locationFinance->map(function ($row) use ($sectionFin, $locationFilter) {
                foreach ($sectionFin as $branch => $fin) {
                    if (strcasecmp((string) $row['label'], (string) $branch) !== 0) {
                        continue;
                    }
                    if ($locationFilter !== '' && strcasecmp($locationFilter, (string) $branch) !== 0) {
                        continue;
                    }
                    $row['unit_gross'] = (float) ($fin['total_revenue'] ?? 0);
                    $row['costs'] = (float) ($fin['total_costs'] ?? 0);
                    $row['gross'] = (float) $row['unit_gross'];
                    $row['net'] = (float) $row['unit_gross'] + (float) $row['miscellaneous'] - (float) $row['costs'];
                    break;
                }

                return $row;
            })->values();
            $vehicleGrossSales = (float) $locationFinance->sum('unit_gross');
            $totalCosts = (float) $locationFinance->sum('costs');
            $grossSales = $vehicleGrossSales + $miscellaneousTotal;
            $totalNet = $grossSales - $totalCosts;
        }

        $pnlLocationTable = [
            'columns' => ['Location', 'Units', 'Gross Sales', 'Miscellaneous', 'Total Costs', 'Total Net'],
            'rows' => $locationFinance->map(function ($locRow) {
                return [
                    'label' => (string) $locRow['label'],
                    'units' => (int) $locRow['units'],
                    'gross' => (float) $locRow['gross'],
                    'miscellaneous' => (float) $locRow['miscellaneous'],
                    'costs' => (float) $locRow['costs'],
                    'net' => (float) $locRow['net'],
                    'is_total' => false,
                ];
            })->values()->all(),
        ];
        if ($locationFinance->count() > 1) {
            $pnlLocationTable['rows'][] = [
                'label' => 'Combined',
                'units' => (int) $unitCount,
                'gross' => (float) $vehicleGrossSales,
                'miscellaneous' => (float) $miscellaneousTotal,
                'costs' => (float) $totalCosts,
                'net' => (float) $totalNet,
                'is_total' => true,
            ];
        }
        $pnlLocationLines = [];
        foreach ($locationFinance as $locRow) {
            $pnlLocationLines[] = [
                'label' => $locRow['label'],
                'value' => '',
                'is_section' => true,
            ];
            $pnlLocationLines[] = ['label' => $locRow['label'].' — Units', 'value' => number_format((int) $locRow['units'])];
            $pnlLocationLines[] = $moneyLines($locRow['label'].' — Gross Sales', (float) $locRow['gross']);
            $pnlLocationLines[] = $moneyLines($locRow['label'].' — Miscellaneous', (float) $locRow['miscellaneous']);
            $pnlLocationLines[] = $moneyLines($locRow['label'].' — Total Costs', (float) $locRow['costs']);
            $pnlLocationLines[] = $moneyLines($locRow['label'].' — Total Net', (float) $locRow['net']);
        }
        if ($locationFinance->count() > 1) {
            $pnlLocationLines[] = ['label' => 'Combined', 'value' => '', 'is_section' => true];
            $pnlLocationLines[] = ['label' => 'Combined — Units', 'value' => number_format($unitCount)];
            $pnlLocationLines[] = $moneyLines('Combined — Gross Sales', $vehicleGrossSales);
            $pnlLocationLines[] = $moneyLines('Combined — Miscellaneous', $miscellaneousTotal);
            $pnlLocationLines[] = $moneyLines('Combined — Total Costs', $totalCosts);
            $pnlLocationLines[] = $moneyLines('Combined — Total Net', $totalNet);
        }

        $allMakes = $rows
            ->groupBy('make')
            ->map(function ($group, $make) {
                return [
                    'label' => $make,
                    'count' => $group->count(),
                    'sales' => (float) $group->sum('gross_revenue'),
                ];
            })
            ->sortByDesc('count')
            ->values();

        $allModels = $rows
            ->groupBy('make_model')
            ->map(function ($group, $label) {
                return [
                    'label' => $label,
                    'count' => $group->count(),
                    'sales' => (float) $group->sum('gross_revenue'),
                    'avg_days' => (float) ($group->pluck('days_to_sell')->filter(fn ($d) => $d !== null)->avg() ?? 0),
                ];
            })
            ->sortByDesc('count')
            ->values();

        $chartMakes = $allMakes->take(10)->values();
        $chartModels = $allModels->take(10)->values();

        $salesMom = $this->monthlySalesComparison($window, $locationFilter);

        $fastestModels = $rows
            ->groupBy('make_model')
            ->map(function ($group, $label) {
                $days = $group->pluck('days_to_sell')->filter(fn ($d) => $d !== null);
                return [
                    'label' => $label,
                    'count' => $group->count(),
                    'sales' => (float) $group->sum('sold_price'),
                    'avg_days' => $days->count() > 0 ? (float) $days->avg() : null,
                ];
            })
            ->filter(fn ($r) => $r['count'] >= 3 && $r['avg_days'] !== null)
            ->sortBy('avg_days')
            ->take(10)
            ->values();

        return [
            'has_data' => $unitCount > 0 || $miscellaneousTotal > 0,
            'excel_aligned' => $excelAligned,
            'summary' => [
                [
                    'key' => 'units_released',
                    'label' => 'Units Released',
                    'value' => $unitCount,
                    'is_currency' => false,
                    'location_breakdown' => $locationBreakdown,
                    'detail' => [
                        'title' => 'Units Released',
                        'formula' => 'Count of released units in the selected date range',
                        'description' => $sourceNote.' Each unique plate in the filtered result counts as 1 unit.',
                        'lines' => array_merge(
                            [
                                ['label' => 'Total units', 'value' => number_format($unitCount)],
                            ],
                            collect($locationBreakdown)->map(fn ($loc) => [
                                'label' => (string) ($loc['label'] ?? 'Location'),
                                'value' => number_format((int) ($loc['count'] ?? 0)),
                            ])->all()
                        ),
                    ],
                ],
                [
                    'key' => 'avg_days',
                    'label' => 'Average Days to Sell',
                    'value' => round($avgDays, 1),
                    'is_currency' => false,
                    'suffix' => ' days',
                    'detail' => [
                        'title' => 'Average Days to Sell',
                        'formula' => 'Average of (release/sale date − purchase date)',
                        'description' => 'Only units with both a purchase date and a release/sale date are included. Days are calendar days between those dates.',
                        'lines' => [
                            ['label' => 'Units with both dates', 'value' => number_format($unitsWithDays)],
                            ['label' => 'Units missing a date', 'value' => number_format(max(0, $unitCount - $unitsWithDays))],
                            ['label' => 'Average Days to Sell', 'value' => number_format($avgDays, 1).' days'],
                        ],
                    ],
                ],
                [
                    'key' => 'pnl',
                    'label' => 'Gross Sales / Costs / Net',
                    'is_composite' => true,
                    'items' => array_values(array_filter([
                        ['label' => 'Gross Sales', 'value' => $vehicleGrossSales, 'is_currency' => true],
                        $miscellaneousTotal != 0.0
                            ? ['label' => 'Miscellaneous', 'value' => $miscellaneousTotal, 'is_currency' => true]
                            : null,
                        ['label' => 'Total Costs', 'value' => $totalCosts, 'is_currency' => true],
                        ['label' => 'Total Net', 'value' => $totalNet, 'is_currency' => true],
                    ])),
                    'detail' => [
                        'title' => 'Totals by location',
                        'formula' => 'Per location: Excel month-section TOTAL REVENUE = Gross Sales. Total Net = Gross Sales + Miscellaneous − Excel month-section TOTAL COSTS',
                        'description' => 'Flagship and Annex Gross Sales match the TOTAL REVENUE total in that month’s Released sheet block. Miscellaneous (including forfeit profit) is a separate column and is included in Total Net, not in Gross Sales.',
                        'location_table' => $pnlLocationTable,
                        'lines' => $pnlLocationLines !== []
                            ? $pnlLocationLines
                            : [
                                $moneyLines('Gross Sales', $vehicleGrossSales),
                                $moneyLines('Total Costs', $totalCosts),
                                $moneyLines('Total Net', $totalNet),
                            ],
                    ],
                ],
            ],
            'charts' => [
                'monthly_sales' => [
                    'title' => 'Monthly Gross Sales',
                    'labels' => $monthlyChart->pluck('label')->all(),
                    'data' => $monthlyChart->pluck('sales')->all(),
                    'type' => 'bar',
                    'is_currency' => true,
                    'dataset_label' => 'Gross Sales (₱)',
                ],
                'top_makes' => [
                    'title' => 'Top Makes by Units Sold',
                    'labels' => $chartMakes->pluck('label')->all(),
                    'data' => $chartMakes->pluck('count')->all(),
                    'type' => 'bar',
                    'index_axis' => 'y',
                    'dataset_label' => 'Units',
                ],
                'top_models' => [
                    'title' => 'Top Models by Units Sold',
                    'labels' => $chartModels->pluck('label')->all(),
                    'data' => $chartModels->pluck('count')->all(),
                    'type' => 'bar',
                    'index_axis' => 'y',
                    'dataset_label' => 'Units',
                ],
                'sales_mom' => [
                    'title' => $salesMom['title'],
                    'labels' => $salesMom['labels'],
                    'data' => $salesMom['current'],
                    'type' => 'line',
                    'is_currency' => true,
                    'dataset_label' => $salesMom['current_label'],
                    'datasets' => [
                        [
                            'label' => $salesMom['current_label'],
                            'data' => $salesMom['current'],
                            'borderColor' => 'rgba(13, 110, 253, 1)',
                            'backgroundColor' => 'rgba(13, 110, 253, 0.12)',
                        ],
                        [
                            'label' => $salesMom['prior_label'],
                            'data' => $salesMom['prior'],
                            'borderColor' => 'rgba(108, 117, 125, 1)',
                            'backgroundColor' => 'rgba(108, 117, 125, 0.08)',
                            'borderDash' => [6, 4],
                        ],
                    ],
                ],
                'fastest_models' => [
                    'title' => 'Fastest-Selling Models (Avg Days to Sell, min 3 units)',
                    'labels' => $fastestModels->pluck('label')->all(),
                    'data' => $fastestModels->map(fn ($r) => round((float) $r['avg_days'], 1))->all(),
                    'type' => 'bar',
                    'index_axis' => 'y',
                    'dataset_label' => 'Avg Days to Sell',
                ],
            ],
            'tables' => [
                'monthly' => $monthlyChart->all(),
                'top_makes' => $allMakes->all(),
                'top_models' => $allModels->all(),
                'fastest_models' => $fastestModels->all(),
                'miscellaneous' => $miscellaneousRows->all(),
                'miscellaneous_total' => $miscellaneousTotal,
            ],
        ];
    }

    /**
     * Last 12 months of Gross Sales vs the same months a year earlier (Excel revenue + miscellaneous).
     *
     * @return array{title:string,labels:array<int,string>,current:array<int,float>,prior:array<int,float>,current_label:string,prior_label:string}
     */
    protected function monthlySalesComparison(array $window, string $locationFilter = ''): array
    {
        $end = ! empty($window['to'])
            ? Carbon::parse($window['to'])->endOfMonth()
            : Carbon::now()->endOfMonth();
        $currentStart = $end->copy()->startOfMonth()->subMonths(11);
        $priorStart = $currentStart->copy()->subYear();
        $priorEnd = $end->copy()->subYear();

        $lookback = [
            'from' => $priorStart->copy()->startOfDay(),
            'to' => $end->copy()->endOfDay(),
        ];
        $vehicles = $this->soldVehiclesBase($lookback);
        $byMonth = [];
        foreach ($vehicles as $vehicle) {
            $gross = $this->vehicleGrossForLocation($vehicle, $locationFilter);
            if ($gross === null) {
                continue;
            }
            $event = $vehicle->getAttribute('excel_period_release_date')
                ?? $vehicle->statusDetail?->release_date
                ?? $vehicle->statusDetail?->sale_date;
            if (! $event) {
                continue;
            }
            $ym = Carbon::parse($event)->format('Y-m');
            $byMonth[$ym] = ($byMonth[$ym] ?? 0.0) + $gross;
        }

        $miscRows = $this->miscellaneousTransactionsInWindow($lookback, $locationFilter);
        foreach ($miscRows as $misc) {
            $date = $misc['transaction_date'] ?? null;
            if (! $date) {
                continue;
            }
            $ym = Carbon::parse($date)->format('Y-m');
            $byMonth[$ym] = ($byMonth[$ym] ?? 0.0) + (float) ($misc['amount'] ?? 0);
        }

        $labels = [];
        $current = [];
        $prior = [];
        $cursor = $currentStart->copy();
        while ($cursor->lte($end)) {
            $labels[] = $cursor->format('M');
            $current[] = round((float) ($byMonth[$cursor->format('Y-m')] ?? 0), 2);
            $prior[] = round((float) ($byMonth[$cursor->copy()->subYear()->format('Y-m')] ?? 0), 2);
            $cursor->addMonth();
        }

        $currentYearLabel = $currentStart->year === $end->year
            ? (string) $end->year
            : $currentStart->year.'–'.$end->format('y');
        $priorYearLabel = $priorStart->year === $priorEnd->year
            ? (string) $priorEnd->year
            : $priorStart->year.'–'.$priorEnd->format('y');

        $title = 'Monthly Sales Comparison';
        if ($locationFilter !== '') {
            $title .= ' — '.$locationFilter;
        }

        return [
            'title' => $title,
            'labels' => $labels,
            'current' => $current,
            'prior' => $prior,
            'current_label' => $currentYearLabel,
            'prior_label' => $priorYearLabel.' (prior year)',
        ];
    }

    /**
     * Gross (Excel TOTAL REVENUE when available) for a vehicle, or null if it does not belong to the location filter.
     */
    protected function vehicleGrossForLocation(Vehicle $v, string $locationFilter = ''): ?float
    {
        $locations = $v->getAttribute('excel_period_branches');
        if (! is_array($locations) || $locations === []) {
            $name = trim((string) ($v->getAttribute('excel_period_branch') ?? $v->branchLocation?->name ?? ''));
            $locations = $name !== '' ? [$name] : [];
        }
        if ($locationFilter !== '') {
            $match = false;
            foreach ($locations as $locName) {
                if (strcasecmp((string) $locName, $locationFilter) === 0) {
                    $match = true;
                    break;
                }
            }
            if (! $match) {
                return null;
            }
        }

        $soldPrice = (float) ($v->sold_price ?? 0);
        if ($soldPrice <= 0) {
            $soldPrice = (float) ($v->statusDetail?->sales_price ?? 0);
        }
        $excelSalesPrice = $v->getAttribute('excel_period_sales_price');
        if ($excelSalesPrice !== null && $excelSalesPrice !== '') {
            $soldPrice = (float) $excelSalesPrice;
        }
        $financeRevenue1 = (float) ($v->statusDetail?->finance_revenue_1 ?? 0);
        $financeRevenue2 = (float) ($v->statusDetail?->finance_revenue_2 ?? 0);
        $excelGross = $v->getAttribute('excel_period_total_revenue');
        $resolved = $this->resolveExcelFinancialsForLocation(
            is_array($v->getAttribute('excel_period_branch_financials'))
                ? $v->getAttribute('excel_period_branch_financials')
                : [],
            $locationFilter,
            $excelGross,
            null,
            null,
            $soldPrice,
            null
        );
        $excelGross = $resolved['total_revenue'];
        $soldPrice = $resolved['sales_price'];

        if ($excelGross !== null && $excelGross !== '') {
            return (float) $excelGross;
        }

        return $soldPrice + $financeRevenue1 + $financeRevenue2;
    }

    /**
     * Pick one sheet's Excel totals, or sum every sheet when All Locations is selected.
     *
     * @param  array<string, mixed>  $branchFinancials
     * @return array{total_revenue:mixed,total_costs:mixed,total_profit:mixed,sales_price:float,release_date:mixed}
     */
    protected function resolveExcelFinancialsForLocation(
        array $branchFinancials,
        string $locationFilter,
        mixed $excelGross,
        mixed $excelCosts,
        mixed $excelProfit,
        float $soldPrice,
        mixed $event
    ): array {
        if ($branchFinancials === []) {
            return [
                'total_revenue' => $excelGross,
                'total_costs' => $excelCosts,
                'total_profit' => $excelProfit,
                'sales_price' => $soldPrice,
                'release_date' => $event,
            ];
        }

        if ($locationFilter !== '') {
            foreach ($branchFinancials as $branchName => $slice) {
                if (strcasecmp((string) $branchName, $locationFilter) !== 0 || ! is_array($slice)) {
                    continue;
                }
                if (array_key_exists('total_revenue', $slice)) {
                    $excelGross = $slice['total_revenue'];
                }
                if (array_key_exists('total_costs', $slice)) {
                    $excelCosts = $slice['total_costs'];
                }
                if (array_key_exists('total_profit', $slice)) {
                    $excelProfit = $slice['total_profit'];
                }
                $sales = $this->numericOrNull($slice['sales_price'] ?? null);
                if ($sales !== null) {
                    $soldPrice = $sales;
                }
                if (! empty($slice['release_date'])) {
                    $event = $slice['release_date'];
                }
                break;
            }

            return [
                'total_revenue' => $excelGross,
                'total_costs' => $excelCosts,
                'total_profit' => $excelProfit,
                'sales_price' => $soldPrice,
                'release_date' => $event,
            ];
        }

        $rev = 0.0;
        $costs = 0.0;
        $profit = 0.0;
        $sales = 0.0;
        $hasRev = false;
        $hasCosts = false;
        $hasProfit = false;
        $hasSales = false;
        foreach ($branchFinancials as $slice) {
            if (! is_array($slice)) {
                continue;
            }
            $sliceRev = $this->numericOrNull($slice['total_revenue'] ?? null);
            if ($sliceRev !== null) {
                $rev += $sliceRev;
                $hasRev = true;
            }
            $sliceCosts = $this->numericOrNull($slice['total_costs'] ?? null);
            if ($sliceCosts !== null) {
                $costs += $sliceCosts;
                $hasCosts = true;
            }
            $sliceProfit = $this->numericOrNull($slice['total_profit'] ?? null);
            if ($sliceProfit !== null) {
                $profit += $sliceProfit;
                $hasProfit = true;
            }
            $sliceSales = $this->numericOrNull($slice['sales_price'] ?? null);
            if ($sliceSales !== null) {
                $sales += $sliceSales;
                $hasSales = true;
            }
        }

        return [
            'total_revenue' => $hasRev ? $rev : $excelGross,
            'total_costs' => $hasCosts ? $costs : $excelCosts,
            'total_profit' => $hasProfit ? $profit : $excelProfit,
            'sales_price' => $hasSales ? $sales : $soldPrice,
            'release_date' => $event,
        ];
    }

    protected function numericOrNull(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
    }

    /**
     * @return Collection<int, array{id:int,description:string,amount:float,transaction_date:string,location:?string}>
     */
    protected function miscellaneousTransactionsInWindow(array $window, string $locationFilter = ''): Collection
    {
        $query = MiscellaneousTransaction::query()
            ->orderByDesc('transaction_date')
            ->orderByDesc('id');

        $from = $window['from'] ?? null;
        $to = $window['to'] ?? null;
        if ($from) {
            $query->whereDate('transaction_date', '>=', Carbon::parse($from)->toDateString());
        }
        if ($to) {
            $query->whereDate('transaction_date', '<=', Carbon::parse($to)->toDateString());
        }
        if ($locationFilter !== '') {
            $query->where('location', $locationFilter);
        }

        return $query->get()->map(fn (MiscellaneousTransaction $row) => [
            'id' => (int) $row->id,
            'description' => (string) $row->description,
            'amount' => (float) $row->amount,
            'location' => $row->location ? (string) $row->location : null,
            'transaction_date' => optional($row->transaction_date)->format('Y-m-d'),
            'transaction_date_label' => optional($row->transaction_date)->format('M d, Y'),
        ])->values();
    }

    protected function resolveDateWindow(Request $request, string $period): array
    {
        $now = Carbon::now();
        $year = $this->resolveSelectedYear($request);
        $inYear = Carbon::create($year, $now->month, min($now->day, 28))->startOfDay();

        return match ($period) {
            'daily' => ['from' => $now->copy()->startOfDay(), 'to' => $now->copy()->endOfDay()],
            'weekly' => ['from' => $now->copy()->startOfWeek(), 'to' => $now->copy()->endOfWeek()],
            'monthly' => [
                'from' => Carbon::create($year, 1, 1)->startOfDay(),
                'to' => Carbon::create($year, 12, 31)->endOfDay(),
            ],
            'quarterly' => [
                'from' => $inYear->copy()->startOfQuarter(),
                'to' => $inYear->copy()->endOfQuarter(),
            ],
            'annually' => [
                'from' => Carbon::create($year, 1, 1)->startOfDay(),
                'to' => Carbon::create($year, 12, 31)->endOfDay(),
            ],
            'range' => [
                'from' => $request->filled('date_from') ? Carbon::parse((string) $request->get('date_from'))->startOfDay() : null,
                'to' => $request->filled('date_to') ? Carbon::parse((string) $request->get('date_to'))->endOfDay() : null,
            ],
            default => ['from' => $now->copy()->startOfMonth(), 'to' => $now->copy()->endOfMonth()],
        };
    }

    protected function attachMonthlyGrouping(array $result): array
    {
        if (! in_array($result['type'] ?? '', ['table', 'value_table'], true)) {
            return $result;
        }

        $rows = collect($result['rows'] ?? []);
        if ($rows->isEmpty()) {
            return $result;
        }

        $dateKey = null;
        foreach (['event_date_raw', 'reservation_date_raw', 'release_date_raw', 'sold_date_raw'] as $candidate) {
            if ($rows->first(fn ($r) => ! empty($r[$candidate])) !== null) {
                $dateKey = $candidate;
                break;
            }
        }
        if (! $dateKey) {
            return $result;
        }

        $hasStatus = ($result['table_kind'] ?? '') === 'reservations'
            || ($result['table_kind'] ?? '') === 'releases'
            || $rows->contains(fn ($r) => array_key_exists('status', $r) && ($r['status'] ?? '') !== '');

        $monthlyStatusConfig = $this->monthlyGroupedStatusConfig((string) ($result['table_kind'] ?? ''));

        $groups = $rows
            ->filter(fn ($r) => ! empty($r[$dateKey]))
            ->groupBy(fn ($r) => Carbon::parse($r[$dateKey])->format('Y-m'))
            ->map(function ($group, $ym) use ($result, $hasStatus, $monthlyStatusConfig) {
                $label = Carbon::createFromFormat('Y-m', $ym)->format('F Y');
                $entry = [
                    'month' => $label,
                    'month_key' => $ym,
                    'count' => $group->count(),
                ];
                if ($hasStatus) {
                    if ($monthlyStatusConfig !== null) {
                        $statusCounts = $this->filterStatusCounts(
                            $this->buildMonthlyFleetStatusCounts($ym),
                            $monthlyStatusConfig['keys']
                        );
                        $entry['count'] = $statusCounts[$monthlyStatusConfig['keys'][0]] ?? 0;
                    } else {
                        $statusCounts = $this->buildStatusCounts($group);
                    }
                    $entry['status_counts'] = $statusCounts;
                }
                if (($result['type'] ?? '') === 'value_table') {
                    $entry['total'] = (float) $group->sum('value');
                }
                return $entry;
            })
            ->values()
            ->all();

        if ($hasStatus && $monthlyStatusConfig !== null && $groups !== []) {
            $result['summary'] = $this->applyStatusSummaryToCards(
                $result['summary'] ?? [],
                $this->filterStatusCounts($this->sumStatusCounts(collect($groups)), $monthlyStatusConfig['keys']),
                $monthlyStatusConfig['summary_label']
            );
        }

        $result['monthly_groups'] = $groups;
        $result['monthly_groups_has_status'] = $hasStatus;
        if ($monthlyStatusConfig !== null) {
            $result['monthly_groups_status_keys'] = $monthlyStatusConfig['keys'];
        }
        return $result;
    }

    /**
     * @return array{keys: array<int, string>, summary_label: string}|null
     */
    protected function monthlyGroupedStatusConfig(string $tableKind): ?array
    {
        return match ($tableKind) {
            'reservations' => ['keys' => ['R'], 'summary_label' => 'Reservation Sales'],
            'releases' => ['keys' => ['RL'], 'summary_label' => 'Released Units'],
            default => null,
        };
    }

    protected function applyFinancialTablePresentation(Request $request, array $result, string $selectedReport): array
    {
        $type = $result['type'] ?? '';
        if (! in_array($type, ['table', 'value_table', 'age_table', 'age_sold_table', 'repairs_table'], true)) {
            return $result;
        }

        $defs = $this->financialTableSortDefinitions($selectedReport);
        if ($defs === []) {
            return $result;
        }

        $rows = $result['rows'] ?? [];
        if (! is_array($rows)) {
            return $result;
        }

        $defaultColumn = array_key_first($defs);
        $defaultSortDir = 'asc';
        $perPage = 20;

        if ($selectedReport === 'reservations') {
            $defaultColumn = 'age_days';
            $defaultSortDir = 'desc';
            $perPage = 5;
        }

        $sortColumn = (string) $request->get('sort_column', $defaultColumn);
        if (! isset($defs[$sortColumn])) {
            $sortColumn = $defaultColumn;
        }
        $sortDir = strtolower((string) $request->get('sort_dir', $defaultSortDir)) === 'desc' ? 'desc' : 'asc';

        usort($rows, function (array $a, array $b) use ($sortColumn, $sortDir, $defs): int {
            $cmp = $this->compareFinancialRowsForSort($a, $b, $sortColumn, $defs[$sortColumn]);

            return $sortDir === 'desc' ? -$cmp : $cmp;
        });

        $total = count($rows);
        $page = max(1, (int) $request->get('page', 1));
        $slice = array_slice($rows, ($page - 1) * $perPage, $perPage);

        $paginator = new LengthAwarePaginator(
            $slice,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'pageName' => 'page']
        );
        $paginator->appends($request->except('page'));

        $result['rows'] = $slice;
        $result['table_ui'] = [
            'sort_column' => $sortColumn,
            'sort_dir' => $sortDir,
            'column_keys' => array_keys($defs),
            'paginator' => $paginator,
        ];

        if (! empty($result['monthly_groups']) && is_array($result['monthly_groups'])) {
            $result = $this->applyMonthlyGroupsPresentation($request, $result, $type);
        }

        return $result;
    }

    protected function applyMonthlyGroupsPresentation(Request $request, array $result, string $resultType): array
    {
        $mgDefs = $this->financialMonthlyGroupsSortDefinitions($resultType);
        if ($mgDefs === []) {
            return $result;
        }

        $groups = $result['monthly_groups'];
        if (! is_array($groups) || $groups === []) {
            return $result;
        }

        $defaultColumn = array_key_first($mgDefs);
        $sortColumn = (string) $request->get('mg_sort', $defaultColumn);
        if (! isset($mgDefs[$sortColumn])) {
            $sortColumn = $defaultColumn;
        }
        $sortDir = strtolower((string) $request->get('mg_dir', 'asc')) === 'desc' ? 'desc' : 'asc';

        usort($groups, function (array $a, array $b) use ($sortColumn, $sortDir, $mgDefs): int {
            $cmp = $this->compareFinancialRowsForSort($a, $b, $sortColumn, $mgDefs[$sortColumn]);

            return $sortDir === 'desc' ? -$cmp : $cmp;
        });

        $total = count($groups);
        $perPage = 20;
        $page = max(1, (int) $request->get('mg_page', 1));
        $slice = array_slice($groups, ($page - 1) * $perPage, $perPage);

        $paginator = new LengthAwarePaginator(
            $slice,
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'pageName' => 'mg_page']
        );
        $paginator->appends($request->except('mg_page'));

        $result['monthly_groups'] = $slice;
        $result['monthly_groups_ui'] = [
            'sort_column' => $sortColumn,
            'sort_dir' => $sortDir,
            'column_keys' => array_keys($mgDefs),
            'paginator' => $paginator,
        ];

        return $result;
    }

    /**
     * @return array<string, array{type: string, raw?: string}>
     */
    protected function financialTableSortDefinitions(string $selectedReport): array
    {
        $valueTable = [
            'plate' => ['type' => 'string'],
            'unit' => ['type' => 'string'],
            'value' => ['type' => 'number'],
        ];

        return match ($selectedReport) {
            'reservations' => [
                'plate' => ['type' => 'string'],
                'unit' => ['type' => 'string'],
                'date_encoded' => ['type' => 'date', 'raw' => 'date_encoded_raw'],
                'purchase_price' => ['type' => 'number'],
                'reservation_amount' => ['type' => 'number'],
                'reservation_date' => ['type' => 'date', 'raw' => 'reservation_date_raw'],
                'age_days' => ['type' => 'number'],
                'status' => ['type' => 'string'],
            ],
            'releases' => [
                'plate' => ['type' => 'string'],
                'unit' => ['type' => 'string'],
                'date_encoded' => ['type' => 'date', 'raw' => 'date_encoded_raw'],
                'purchase_price' => ['type' => 'number'],
                'sold_price' => ['type' => 'number'],
                'sold_date' => ['type' => 'date', 'raw' => 'sold_date_raw'],
                'age_days' => ['type' => 'number'],
            ],
            'gross_per_unit', 'cost_per_unit', 'net_per_unit' => $valueTable,
            'inventory_age_unsold' => [
                'plate' => ['type' => 'string'],
                'unit' => ['type' => 'string'],
                'status' => ['type' => 'string'],
                'date_encoded' => ['type' => 'date', 'raw' => 'date_encoded_raw'],
                'age_days' => ['type' => 'number'],
                'purchase_price' => ['type' => 'number'],
                'posted_price' => ['type' => 'number'],
            ],
            'inventory_sales_age_sold' => [
                'plate' => ['type' => 'string'],
                'unit' => ['type' => 'string'],
                'date_encoded' => ['type' => 'date', 'raw' => 'date_encoded_raw'],
                'purchase_price' => ['type' => 'number'],
                'posted_price' => ['type' => 'number'],
                'sold_price' => ['type' => 'number'],
                'sold_date' => ['type' => 'date', 'raw' => 'sold_date_raw'],
                'age_days' => ['type' => 'number'],
            ],
            'unit_repairs' => [
                'plate' => ['type' => 'string'],
                'unit' => ['type' => 'string'],
                'repair_cost' => ['type' => 'number'],
                'post_reservation_repairs_cost' => ['type' => 'number'],
                'total_repairs' => ['type' => 'number'],
            ],
            default => [],
        };
    }

    /**
     * @return array<string, array{type: string, raw?: string}>
     */
    protected function financialMonthlyGroupsSortDefinitions(string $resultType): array
    {
        return match ($resultType) {
            'table' => [
                'month' => ['type' => 'month_key', 'raw' => 'month_key'],
                'count' => ['type' => 'number'],
            ],
            'value_table' => [
                'month' => ['type' => 'month_key', 'raw' => 'month_key'],
                'count' => ['type' => 'number'],
                'total' => ['type' => 'number'],
            ],
            default => [],
        };
    }

    /**
     * @param  array{type: string, raw?: string}  $def
     */
    protected function compareFinancialRowsForSort(array $a, array $b, string $column, array $def): int
    {
        $type = $def['type'] ?? 'string';

        if ($type === 'month_key') {
            $k = $def['raw'] ?? 'month_key';
            $va = (string) ($a[$k] ?? '');
            $vb = (string) ($b[$k] ?? '');

            return $va <=> $vb;
        }

        if ($type === 'number') {
            $va = (float) ($a[$column] ?? 0);
            $vb = (float) ($b[$column] ?? 0);

            return $va <=> $vb;
        }

        if ($type === 'date') {
            $rawKey = $def['raw'] ?? null;
            if ($rawKey && (isset($a[$rawKey]) || isset($b[$rawKey]))) {
                $ta = ! empty($a[$rawKey]) ? strtotime((string) $a[$rawKey]) : 0;
                $tb = ! empty($b[$rawKey]) ? strtotime((string) $b[$rawKey]) : 0;

                return ($ta <=> $tb) ?: strcmp((string) ($a[$column] ?? ''), (string) ($b[$column] ?? ''));
            }
            $da = (string) ($a[$column] ?? '');
            $db = (string) ($b[$column] ?? '');
            if ($da === '-' || $da === '') {
                $ta = 0;
            } else {
                $ts = strtotime($da);
                $ta = $ts !== false ? $ts : 0;
            }
            if ($db === '-' || $db === '') {
                $tb = 0;
            } else {
                $ts = strtotime($db);
                $tb = $ts !== false ? $ts : 0;
            }

            return $ta <=> $tb;
        }

        $va = mb_strtolower((string) ($a[$column] ?? ''));
        $vb = mb_strtolower((string) ($b[$column] ?? ''));

        return $va <=> $vb;
    }

    /**
     * @return array<int, array{key: string, label: string, pill_class: string, dot_class: string}>
     */
    protected function statusCountMeta(): array
    {
        return [
            ['key' => 'A', 'label' => 'Available', 'pill_class' => 'fin-status-pill--available', 'dot_class' => 'fin-status-dot--available'],
            ['key' => 'R', 'label' => 'Reserved', 'pill_class' => 'fin-status-pill--reserved', 'dot_class' => 'fin-status-dot--reserved'],
            ['key' => 'RL', 'label' => 'Released', 'pill_class' => 'fin-status-pill--released', 'dot_class' => 'fin-status-dot--released'],
            ['key' => 'F', 'label' => 'Forfeited', 'pill_class' => 'fin-status-pill--forfeited', 'dot_class' => 'fin-status-dot--forfeited'],
        ];
    }

    protected function fleetVehicles(): Collection
    {
        static $cache = null;
        if ($cache === null) {
            $cache = Vehicle::with(['statusDetail', 'forfeitDetails'])->get();
        }

        return $cache;
    }

    /**
     * Monthly fleet counts. Available = units encoded in month minus reserved/released same month.
     *
     * @return array{A: int, R: int, RL: int, F: int}
     */
    protected function buildMonthlyFleetStatusCounts(string $ym): array
    {
        $monthStart = Carbon::createFromFormat('Y-m', $ym)->startOfMonth()->startOfDay();
        $monthEnd = Carbon::createFromFormat('Y-m', $ym)->endOfMonth()->endOfDay();
        $vehicles = $this->fleetVehicles();

        $r = $vehicles->filter(function (Vehicle $v) use ($monthStart, $monthEnd) {
            $d = $v->statusDetail?->sale_date;

            return $d && Carbon::parse($d)->between($monthStart, $monthEnd);
        })->count();

        $rl = $vehicles->filter(function (Vehicle $v) use ($monthStart, $monthEnd) {
            $d = $v->statusDetail?->release_date;

            return $d && Carbon::parse($d)->between($monthStart, $monthEnd);
        })->count();

        $f = $vehicles->filter(function (Vehicle $v) use ($monthStart, $monthEnd) {
            if ($v->forfeitDetails->isNotEmpty()) {
                return $v->forfeitDetails->contains(
                    fn ($fd) => $fd->forfeit_date && Carbon::parse($fd->forfeit_date)->between($monthStart, $monthEnd)
                );
            }

            return $v->status === 'Forfeited'
                && $v->updated_at
                && Carbon::parse($v->updated_at)->between($monthStart, $monthEnd);
        })->count();

        $availableGross = $vehicles->filter(function (Vehicle $v) use ($monthStart, $monthEnd) {
            if ($v->status !== 'Available') {
                return false;
            }
            $encoded = $v->created_at ?? $v->purchase_date;

            return $encoded && Carbon::parse($encoded)->between($monthStart, $monthEnd);
        })->count();

        $a = max(0, $availableGross - $r - $rl);

        return ['A' => $a, 'R' => $r, 'RL' => $rl, 'F' => $f];
    }

    /**
     * @param  array{A: int, R: int, RL: int, F: int}  $counts
     */
    protected function sumStatusCounts(Collection $groups): array
    {
        $totals = ['A' => 0, 'R' => 0, 'RL' => 0, 'F' => 0];
        foreach ($groups as $group) {
            $counts = $group['status_counts'] ?? [];
            foreach ($totals as $key => $_) {
                $totals[$key] += (int) ($counts[$key] ?? 0);
            }
        }

        return $totals;
    }

    /**
     * @param  array<int, array<string, mixed>>  $summaryCards
     * @param  array{A: int, R: int, RL: int, F: int}  $counts
     * @return array<int, array<string, mixed>>
     */
    protected function applyStatusSummaryToCards(array $summaryCards, array $counts, string $targetLabel): array
    {
        foreach ($summaryCards as $index => $card) {
            if (($card['label'] ?? '') === $targetLabel) {
                $summaryCards[$index] = [
                    'label' => $targetLabel,
                    'status_counts' => $counts,
                    'is_status_summary' => true,
                ];
                break;
            }
        }

        return $summaryCards;
    }

    /**
     * @param  array{A?: int, R?: int, RL?: int, F?: int}  $counts
     * @param  array<int, string>  $keys
     * @return array<string, int>
     */
    protected function filterStatusCounts(array $counts, array $keys): array
    {
        $filtered = [];
        foreach ($keys as $key) {
            $filtered[$key] = (int) ($counts[$key] ?? 0);
        }

        return $filtered;
    }

    /**
     * @param  array<int, string>  $keys
     * @return array<int, array{key: string, label: string, pill_class: string, dot_class: string}>
     */
    protected function statusCountMetaForKeys(array $keys): array
    {
        return array_values(array_filter(
            $this->statusCountMeta(),
            fn (array $item) => in_array($item['key'], $keys, true)
        ));
    }

    /**
     * @return array{A: int, R: int, RL: int, F: int}
     */
    protected function buildStatusCounts(Collection $rows): array
    {
        $counts = ['A' => 0, 'R' => 0, 'RL' => 0, 'F' => 0];

        foreach ($rows as $row) {
            $abbrev = $this->statusToAbbrev(is_array($row) ? ($row['status'] ?? '') : ($row->status ?? ''));
            if ($abbrev !== null) {
                $counts[$abbrev]++;
            }
        }

        return $counts;
    }

    protected function statusToAbbrev(?string $status): ?string
    {
        return match ($status) {
            'Available' => 'A',
            'Reserved' => 'R',
            'Released' => 'RL',
            'Forfeited' => 'F',
            default => null,
        };
    }

    /**
     * @param  array{A: int, R: int, RL: int, F: int}  $counts
     */
    protected function formatStatusCountSummary(array $counts): string
    {
        return sprintf(
            'Available: %d | Reserved: %d | Released: %d | Forfeited: %d',
            $counts['A'],
            $counts['R'],
            $counts['RL'],
            $counts['F']
        );
    }

    protected function rangeLabel(array $window): string
    {
        $from = $window['from'] ?? null;
        $to = $window['to'] ?? null;
        if (! $from && ! $to) {
            return 'All dates';
        }
        if ($from && $to) {
            return $from->format('M d, Y') . ' to ' . $to->format('M d, Y');
        }
        if ($from) {
            return 'From ' . $from->format('M d, Y');
        }
        return 'Until ' . $to->format('M d, Y');
    }

    protected function inWindow($date, array $window): bool
    {
        if (! $date) {
            return false;
        }
        $d = Carbon::parse($date);
        $from = $window['from'] ?? null;
        $to = $window['to'] ?? null;
        if ($from && $d->lt($from)) {
            return false;
        }
        if ($to && $d->gt($to)) {
            return false;
        }
        return true;
    }

    protected function buildFinancialReport(string $type, array $window): array
    {
        return match ($type) {
            'reservations' => $this->reportReservations($window),
            'releases' => $this->reportReleases($window),
            'gross_per_unit' => $this->reportGrossPerUnit($window),
            'cost_per_unit' => $this->reportCostPerUnit($window),
            'net_per_unit' => $this->reportNetPerUnit($window),
            'avg_gross_per_unit' => $this->reportAverageGrossPerUnit($window),
            'avg_cost_per_unit' => $this->reportAverageCostPerUnit($window),
            'avg_net_per_unit' => $this->reportAverageNetPerUnit($window),
            'split_cash_financing' => $this->reportSplitCashFinancing($window),
            'total_discount_cash' => $this->reportTotalDiscountCash($window),
            'avg_discount_cash' => $this->reportAverageDiscountCash($window),
            'inventory_age_unsold' => $this->reportInventoryAgeUnsold($window),
            'inventory_sales_age_sold' => $this->reportInventorySalesAgeSold($window),
            'unit_repairs' => $this->reportUnitRepairs($window),
            default => $this->reportReservations($window),
        };
    }

    protected function soldVehiclesBase(array $window): Collection
    {
        $excelAligned = $this->excelAlignedReleasedVehicles($window);
        if ($excelAligned !== null) {
            return $excelAligned;
        }

        return Vehicle::with(['statusDetail', 'expense', 'branchLocation', 'make', 'vehicleModel'])
            ->where('status', 'Released')
            ->get()
            ->filter(function (Vehicle $v) use ($window) {
                $eventDate = $v->statusDetail?->release_date ?? $v->statusDetail?->sale_date ?? $v->updated_at;

                return $this->inWindow($eventDate, $window);
            })
            ->values();
    }

    /**
     * Match Unit Report Released date-filter counts using Excel release history
     * (includes plates later re-released / currently Forfeited that still appear in Excel for the period).
     */
    protected function excelAlignedReleasedVehicles(array $window): ?Collection
    {
        $from = ! empty($window['from']) ? Carbon::parse($window['from'])->format('Y-m-d') : '';
        $to = ! empty($window['to']) ? Carbon::parse($window['to'])->format('Y-m-d') : '';
        if ($from === '' && $to === '') {
            return null;
        }

        $context = ExcelUnitReconcile::releasedDateFilterContext(Request::create('/vehicles', 'GET', [
            'status' => 'Released',
            'release_date_from' => $from,
            'release_date_to' => $to,
        ]));
        if ($context === null) {
            return null;
        }

        $platesMeta = $context['plates_meta'] ?? [];
        if ($platesMeta === []) {
            return collect();
        }

        $vehiclesByPlate = Vehicle::with(['statusDetail', 'expense', 'branchLocation', 'forfeitDetails', 'make', 'vehicleModel'])
            ->get()
            ->groupBy(fn (Vehicle $v) => ExcelUnitReconcile::normalizePlate($v->plate_number));

        $ordered = collect();
        $usedVehicleIds = [];
        foreach ($platesMeta as $plateKey => $meta) {
            $plate = ExcelUnitReconcile::metaPlate($plateKey, $meta);
            $candidates = $vehiclesByPlate->get($plate, collect())
                ->reject(fn (Vehicle $candidate) => $candidate->status === 'Archived')
                ->values()
                ->all();
            $vehicle = ExcelUnitReconcile::pickVehicleForExcelRow($candidates, $meta, $usedVehicleIds);
            // Match Unit Report: include Excel-matched plates even if DB status is no longer Released.
            if (! $vehicle) {
                $vehicle = ExcelUnitReconcile::makeExcelOnlyVehicle($plate !== '' ? $plate : $plateKey, $meta);
            } else {
                $usedVehicleIds[$vehicle->id] = true;
                $vehicle->setAttribute('excel_period_release_date', $meta['release_date'] ?? null);
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
                if (isset($meta['purchase_price']) && $meta['purchase_price'] !== null && $meta['purchase_price'] !== '') {
                    $vehicle->setAttribute('purchase_price', $meta['purchase_price']);
                }
            }
            $ordered->push($vehicle);
        }

        return $ordered->values();
    }

    /**
     * @return array<int, array{id?: mixed, name?: string, count?: int}>
     */
    protected function excelAlignedLocationCounts(array $window): array
    {
        $from = ! empty($window['from']) ? Carbon::parse($window['from'])->format('Y-m-d') : '';
        $to = ! empty($window['to']) ? Carbon::parse($window['to'])->format('Y-m-d') : '';
        if ($from === '' && $to === '') {
            return [];
        }

        $context = ExcelUnitReconcile::releasedDateFilterContext(Request::create('/vehicles', 'GET', [
            'status' => 'Released',
            'release_date_from' => $from,
            'release_date_to' => $to,
        ]));

        return is_array($context['location_counts'] ?? null) ? $context['location_counts'] : [];
    }

    protected function reportReservations(array $window): array
    {
        $vehicles = Vehicle::with('statusDetail')
            ->where(function ($q) {
                $q->where('status', 'Reserved')
                    ->orWhereHas('statusDetail', function ($sq) {
                        $sq->where('sale_status', 'Reserved');
                    });
            })
            ->get();
        $vehicles = $vehicles->filter(function (Vehicle $v) use ($window) {
            return $this->inWindow($v->statusDetail?->sale_date ?? $v->updated_at, $window);
        })->values();

        $today = Carbon::today();
        $rows = $vehicles->map(function (Vehicle $v) use ($today) {
            $event = $v->statusDetail?->sale_date ?? $v->updated_at;
            $reference = $v->purchase_date ?? $v->created_at;
            $ageDays = $reference ? Carbon::parse($reference)->diffInDays($today) : 0;
            $encoded = $v->created_at;

            return [
                'plate' => $v->plate_number,
                'unit' => $v->full_name,
                'date_encoded' => $encoded ? Carbon::parse($encoded)->format('M d, Y') : '-',
                'date_encoded_raw' => $encoded ? Carbon::parse($encoded)->toDateString() : null,
                'purchase_price' => (float) ($v->purchase_price ?? 0),
                'reservation_amount' => (float) ($v->statusDetail?->sale_reservation_amount ?? 0),
                'reservation_date' => optional($v->statusDetail?->sale_date)?->format('M d, Y') ?? '-',
                'reservation_date_raw' => $event ? Carbon::parse($event)->toDateString() : null,
                'status' => $v->status,
                'age_days' => $ageDays,
            ];
        })->values()->all();

        $reservationAmount = (float) $vehicles->sum(function (Vehicle $v) {
            return (float) ($v->statusDetail?->sale_reservation_amount ?? 0);
        });
        $purchasePriceTotal = (float) $vehicles->sum(function (Vehicle $v) {
            return (float) ($v->purchase_price ?? 0);
        });

        $monthly = collect($rows)
            ->filter(fn ($r) => ! empty($r['reservation_date_raw']))
            ->groupBy(fn ($r) => Carbon::parse($r['reservation_date_raw'])->format('Y-m'))
            ->sortKeys()
            ->map(function ($group, $ym) {
                return [
                    'label' => Carbon::createFromFormat('Y-m', $ym)->format('M Y'),
                    'count' => $group->count(),
                ];
            })
            ->values();

        $cashCount = $vehicles->filter(function (Vehicle $v) {
            return str_contains(strtolower((string) $v->statusDetail?->cash_financing), 'cash');
        })->count();
        $financingCount = $vehicles->filter(function (Vehicle $v) {
            return str_contains(strtolower((string) $v->statusDetail?->cash_financing), 'financ');
        })->count();
        $unknownCount = max($vehicles->count() - $cashCount - $financingCount, 0);

        return [
            'title' => 'List of vehicle based on older age',
            'type' => 'table',
            'table_kind' => 'reservations',
            'count' => count($rows),
            'columns' => [
                'Plate',
                'Unit',
                'Date Encoded',
                'Purchase Price',
                'Reservation Amount',
                'Reserve Date',
                'Age (Days)',
                'Status',
            ],
            'rows' => $rows,
            'summary' => [
                ['label' => 'Reservation Sales', 'status_counts' => ['R' => 0], 'is_status_summary' => true],
                ['label' => 'Sales Amount', 'value' => $reservationAmount, 'is_currency' => true],
                ['label' => 'Purchase Price', 'value' => $purchasePriceTotal, 'is_currency' => true],
            ],
            'charts' => [
                'monthly_reservations' => [
                    'title' => 'Reservations by Month',
                    'labels' => $monthly->pluck('label')->all(),
                    'data' => $monthly->pluck('count')->all(),
                    'type' => 'bar',
                    'dataset_label' => 'Units',
                ],
                'cash_financing_split' => [
                    'title' => 'Cash vs Financing Split',
                    'labels' => ['Cash', 'Financing', 'Unknown'],
                    'data' => [$cashCount, $financingCount, $unknownCount],
                    'type' => 'pie',
                ],
            ],
        ];
    }

    protected function reportReleases(array $window): array
    {
        $vehicles = $this->soldVehiclesBase($window);

        $rows = $vehicles->map(function (Vehicle $v) {
            $event = $v->statusDetail?->release_date ?? $v->statusDetail?->sale_date ?? $v->updated_at;
            $start = $v->purchase_date ?? $v->created_at;
            $ageDays = ($start && $event) ? Carbon::parse($start)->diffInDays(Carbon::parse($event)) : 0;
            $encoded = $v->created_at;

            return [
                'plate' => $v->plate_number,
                'unit' => $v->full_name,
                'date_encoded' => $encoded ? Carbon::parse($encoded)->format('M d, Y') : '-',
                'date_encoded_raw' => $encoded ? Carbon::parse($encoded)->toDateString() : null,
                'purchase_price' => (float) ($v->purchase_price ?? 0),
                'sold_price' => (float) ($v->sold_price ?? 0),
                'release_date' => optional($v->statusDetail?->release_date)?->format('M d, Y') ?? '-',
                'release_date_raw' => $event ? Carbon::parse($event)->toDateString() : null,
                'sold_date' => $event ? Carbon::parse($event)->format('M d, Y') : '-',
                'sold_date_raw' => $event ? Carbon::parse($event)->toDateString() : null,
                'status' => 'Released',
                'age_days' => $ageDays,
            ];
        })->values()->all();

        $monthly = collect($rows)
            ->filter(fn ($r) => ! empty($r['release_date_raw']))
            ->groupBy(fn ($r) => Carbon::parse($r['release_date_raw'])->format('Y-m'))
            ->sortKeys()
            ->map(function ($group, $ym) {
                return [
                    'label' => Carbon::createFromFormat('Y-m', $ym)->format('M Y'),
                    'count' => $group->count(),
                    'sales' => (float) $group->sum('sold_price'),
                ];
            })
            ->values();

        return [
            'title' => 'List and Count of Releases',
            'type' => 'table',
            'table_kind' => 'releases',
            'count' => count($rows),
            'columns' => [
                'Plate',
                'Unit',
                'Date Encoded',
                'Purchase Price',
                'Sold Price',
                'Sold Date',
                'Age (Days)',
            ],
            'rows' => $rows,
            'summary' => [
                ['label' => 'Released Units', 'status_counts' => ['RL' => 0], 'is_status_summary' => true],
                ['label' => 'Sales Amount', 'value' => (float) collect($rows)->sum('sold_price'), 'is_currency' => true],
                ['label' => 'Purchase Price', 'value' => (float) collect($rows)->sum('purchase_price'), 'is_currency' => true],
            ],
            'charts' => [
                'monthly_release_count' => [
                    'title' => 'Releases by Month',
                    'labels' => $monthly->pluck('label')->all(),
                    'data' => $monthly->pluck('count')->all(),
                    'type' => 'bar',
                    'dataset_label' => 'Released Units',
                ],
                'monthly_release_sales' => [
                    'title' => 'Release Sales by Month',
                    'labels' => $monthly->pluck('label')->all(),
                    'data' => $monthly->pluck('sales')->all(),
                    'type' => 'line',
                    'dataset_label' => 'Sales Amount',
                ],
            ],
        ];
    }

    protected function reportGrossPerUnit(array $window): array
    {
        $rows = $this->soldVehiclesBase($window)->map(function (Vehicle $v) {
            $sold = (float) ($v->sold_price ?? 0);
            $cost = (float) ($v->purchase_price ?? 0);
            $event = $v->statusDetail?->release_date ?? $v->statusDetail?->sale_date ?? $v->updated_at;
            return [
                'plate' => $v->plate_number,
                'unit' => $v->full_name,
                'value' => $sold - $cost,
                'event_date_raw' => $event ? Carbon::parse($event)->toDateString() : null,
            ];
        })->values()->all();

        $monthly = collect($rows)
            ->filter(fn ($r) => ! empty($r['event_date_raw']))
            ->groupBy(fn ($r) => Carbon::parse($r['event_date_raw'])->format('Y-m'))
            ->sortKeys()
            ->map(function ($group, $ym) {
                return [
                    'label' => Carbon::createFromFormat('Y-m', $ym)->format('M Y'),
                    'total' => (float) $group->sum('value'),
                ];
            })
            ->values();

        return [
            'title' => 'Total Gross Per Unit',
            'type' => 'value_table',
            'count' => count($rows),
            'metric_label' => 'Gross',
            'columns' => ['Plate', 'Unit', 'Gross'],
            'rows' => $rows,
            'summary' => [
                ['label' => 'Total Gross', 'value' => (float) collect($rows)->sum('value'), 'is_currency' => true],
                ['label' => 'Average Gross', 'value' => (float) (collect($rows)->avg('value') ?? 0), 'is_currency' => true],
                ['label' => 'Units', 'value' => (float) count($rows), 'is_currency' => false],
            ],
            'charts' => [
                'gross_monthly' => [
                    'title' => 'Gross by Month',
                    'labels' => $monthly->pluck('label')->all(),
                    'data' => $monthly->pluck('total')->all(),
                    'type' => 'bar',
                    'dataset_label' => 'Gross',
                ],
            ],
        ];
    }

    protected function reportCostPerUnit(array $window): array
    {
        $rows = $this->soldVehiclesBase($window)->map(function (Vehicle $v) {
            $repairCost = (float) ($v->expense?->total_repair_cost ?? 0);
            $purchase = (float) ($v->purchase_price ?? 0);
            $event = $v->statusDetail?->release_date ?? $v->statusDetail?->sale_date ?? $v->updated_at;
            return [
                'plate' => $v->plate_number,
                'unit' => $v->full_name,
                'value' => $purchase + $repairCost,
                'event_date_raw' => $event ? Carbon::parse($event)->toDateString() : null,
            ];
        })->values()->all();

        $monthly = collect($rows)
            ->filter(fn ($r) => ! empty($r['event_date_raw']))
            ->groupBy(fn ($r) => Carbon::parse($r['event_date_raw'])->format('Y-m'))
            ->sortKeys()
            ->map(function ($group, $ym) {
                return [
                    'label' => Carbon::createFromFormat('Y-m', $ym)->format('M Y'),
                    'total' => (float) $group->sum('value'),
                ];
            })
            ->values();

        return [
            'title' => 'Total Cost Per Unit',
            'type' => 'value_table',
            'count' => count($rows),
            'metric_label' => 'Cost',
            'columns' => ['Plate', 'Unit', 'Cost'],
            'rows' => $rows,
            'summary' => [
                ['label' => 'Total Cost', 'value' => (float) collect($rows)->sum('value'), 'is_currency' => true],
                ['label' => 'Average Cost', 'value' => (float) (collect($rows)->avg('value') ?? 0), 'is_currency' => true],
                ['label' => 'Units', 'value' => (float) count($rows), 'is_currency' => false],
            ],
            'charts' => [
                'cost_monthly' => [
                    'title' => 'Cost by Month',
                    'labels' => $monthly->pluck('label')->all(),
                    'data' => $monthly->pluck('total')->all(),
                    'type' => 'bar',
                    'dataset_label' => 'Cost',
                ],
            ],
        ];
    }

    protected function reportNetPerUnit(array $window): array
    {
        $rows = $this->soldVehiclesBase($window)->map(function (Vehicle $v) {
            $sold = (float) ($v->sold_price ?? 0);
            $purchase = (float) ($v->purchase_price ?? 0);
            $repairCost = (float) ($v->expense?->total_repair_cost ?? 0);
            $event = $v->statusDetail?->release_date ?? $v->statusDetail?->sale_date ?? $v->updated_at;
            return [
                'plate' => $v->plate_number,
                'unit' => $v->full_name,
                'value' => $sold - ($purchase + $repairCost),
                'event_date_raw' => $event ? Carbon::parse($event)->toDateString() : null,
            ];
        })->values()->all();

        $monthly = collect($rows)
            ->filter(fn ($r) => ! empty($r['event_date_raw']))
            ->groupBy(fn ($r) => Carbon::parse($r['event_date_raw'])->format('Y-m'))
            ->sortKeys()
            ->map(function ($group, $ym) {
                return [
                    'label' => Carbon::createFromFormat('Y-m', $ym)->format('M Y'),
                    'total' => (float) $group->sum('value'),
                ];
            })
            ->values();

        return [
            'title' => 'Total Net Per Unit',
            'type' => 'value_table',
            'count' => count($rows),
            'metric_label' => 'Net',
            'columns' => ['Plate', 'Unit', 'Net'],
            'rows' => $rows,
            'summary' => [
                ['label' => 'Total Net', 'value' => (float) collect($rows)->sum('value'), 'is_currency' => true],
                ['label' => 'Average Net', 'value' => (float) (collect($rows)->avg('value') ?? 0), 'is_currency' => true],
                ['label' => 'Units', 'value' => (float) count($rows), 'is_currency' => false],
            ],
            'charts' => [
                'net_monthly' => [
                    'title' => 'Net by Month',
                    'labels' => $monthly->pluck('label')->all(),
                    'data' => $monthly->pluck('total')->all(),
                    'type' => 'line',
                    'dataset_label' => 'Net',
                ],
            ],
        ];
    }

    protected function reportAverageGrossPerUnit(array $window): array
    {
        $base = $this->reportGrossPerUnit($window);
        $rows = $base['rows'];
        $avg = collect($rows)->avg('value') ?? 0;

        return [
            'title' => 'Avg Gross per Unit',
            'type' => 'single_metric',
            'count' => count($rows),
            'metric_label' => 'Average Gross',
            'metric_value' => (float) $avg,
            'charts' => $base['charts'] ?? [],
        ];
    }

    protected function reportAverageCostPerUnit(array $window): array
    {
        $base = $this->reportCostPerUnit($window);
        $rows = $base['rows'];
        $avg = collect($rows)->avg('value') ?? 0;

        return [
            'title' => 'Avg Cost per Unit',
            'type' => 'single_metric',
            'count' => count($rows),
            'metric_label' => 'Average Cost',
            'metric_value' => (float) $avg,
            'charts' => $base['charts'] ?? [],
        ];
    }

    protected function reportAverageNetPerUnit(array $window): array
    {
        $base = $this->reportNetPerUnit($window);
        $rows = $base['rows'];
        $avg = collect($rows)->avg('value') ?? 0;

        return [
            'title' => 'Avg Net per Unit',
            'type' => 'single_metric',
            'count' => count($rows),
            'metric_label' => 'Average Net',
            'metric_value' => (float) $avg,
            'charts' => $base['charts'] ?? [],
        ];
    }

    protected function reportSplitCashFinancing(array $window): array
    {
        $base = Vehicle::with('statusDetail')
            ->whereHas('statusDetail')
            ->get()
            ->filter(fn (Vehicle $v) => $this->inWindow($v->statusDetail?->release_date ?? $v->statusDetail?->sale_date ?? $v->updated_at, $window))
            ->values();
        $cash = $base->filter(fn (Vehicle $v) => str_contains(strtolower((string) $v->statusDetail?->cash_financing), 'cash'))->count();
        $financing = $base->filter(fn (Vehicle $v) => str_contains(strtolower((string) $v->statusDetail?->cash_financing), 'financ'))->count();

        return [
            'title' => 'Split Of Cash/Financing',
            'type' => 'split_metric',
            'count' => $cash + $financing,
            'cash' => $cash,
            'financing' => $financing,
            'summary' => [
                ['label' => 'Cash', 'value' => (float) $cash, 'is_currency' => false],
                ['label' => 'Financing', 'value' => (float) $financing, 'is_currency' => false],
                ['label' => 'Total', 'value' => (float) ($cash + $financing), 'is_currency' => false],
            ],
            'charts' => [
                'cash_financing_split' => [
                    'title' => 'Cash vs Financing',
                    'labels' => ['Cash', 'Financing'],
                    'data' => [$cash, $financing],
                    'type' => 'pie',
                    'dataset_label' => 'Units',
                ],
            ],
        ];
    }

    protected function reportTotalDiscountCash(array $window): array
    {
        $discountRows = $this->cashDiscountRows($window);
        $total = collect($discountRows)->sum('discount');
        $monthly = collect($discountRows)
            ->filter(fn ($r) => ! empty($r['event_date_raw']))
            ->groupBy(fn ($r) => Carbon::parse($r['event_date_raw'])->format('Y-m'))
            ->sortKeys()
            ->map(function ($group, $ym) {
                return [
                    'label' => Carbon::createFromFormat('Y-m', $ym)->format('M Y'),
                    'total' => (float) $group->sum('discount'),
                ];
            })
            ->values();

        return [
            'title' => 'Total Discount on Cash',
            'type' => 'single_metric',
            'count' => count($discountRows),
            'metric_label' => 'Total Discount',
            'metric_value' => (float) $total,
            'charts' => [
                'discount_monthly' => [
                    'title' => 'Cash Discount by Month',
                    'labels' => $monthly->pluck('label')->all(),
                    'data' => $monthly->pluck('total')->all(),
                    'type' => 'bar',
                    'dataset_label' => 'Discount',
                ],
            ],
        ];
    }

    protected function reportAverageDiscountCash(array $window): array
    {
        $discountRows = $this->cashDiscountRows($window);
        $avg = collect($discountRows)->avg('discount') ?? 0;
        $monthly = collect($discountRows)
            ->filter(fn ($r) => ! empty($r['event_date_raw']))
            ->groupBy(fn ($r) => Carbon::parse($r['event_date_raw'])->format('Y-m'))
            ->sortKeys()
            ->map(function ($group, $ym) {
                return [
                    'label' => Carbon::createFromFormat('Y-m', $ym)->format('M Y'),
                    'avg' => (float) ($group->avg('discount') ?? 0),
                ];
            })
            ->values();

        return [
            'title' => 'Avg Discount on Cash',
            'type' => 'single_metric',
            'count' => count($discountRows),
            'metric_label' => 'Average Discount',
            'metric_value' => (float) $avg,
            'charts' => [
                'discount_avg_monthly' => [
                    'title' => 'Average Cash Discount by Month',
                    'labels' => $monthly->pluck('label')->all(),
                    'data' => $monthly->pluck('avg')->all(),
                    'type' => 'line',
                    'dataset_label' => 'Average Discount',
                ],
            ],
        ];
    }

    protected function cashDiscountRows(array $window): array
    {
        return Vehicle::with('statusDetail')
            ->where('status', 'Released')
            ->whereHas('statusDetail', function ($q) {
                $q->whereRaw('LOWER(cash_financing) LIKE ?', ['%cash%']);
            })
            ->get()
            ->filter(fn (Vehicle $v) => $this->inWindow($v->statusDetail?->release_date ?? $v->statusDetail?->sale_date ?? $v->updated_at, $window))
            ->map(function (Vehicle $v) {
                $posted = (float) ($v->posted_price ?? 0);
                $sold = (float) ($v->sold_price ?? 0);
                return [
                    'plate' => $v->plate_number,
                    'unit' => $v->full_name,
                    'discount' => max($posted - $sold, 0),
                    'event_date_raw' => $v->statusDetail?->release_date
                        ? Carbon::parse($v->statusDetail->release_date)->toDateString()
                        : ($v->statusDetail?->sale_date ? Carbon::parse($v->statusDetail->sale_date)->toDateString() : null),
                ];
            })
            ->values()
            ->all();
    }

    protected function reportInventoryAgeUnsold(array $window): array
    {
        $today = Carbon::today();
        $rows = Vehicle::where('status', '!=', 'Released')
            ->get()
            ->filter(function (Vehicle $v) use ($window) {
                return $this->inWindow($v->purchase_date ?? $v->created_at, $window);
            })
            ->map(function (Vehicle $v) use ($today) {
                $reference = $v->purchase_date ?? $v->created_at;
                return [
                    'plate' => $v->plate_number,
                    'unit' => $v->full_name,
                    'status' => $v->status,
                    'date_encoded' => $v->created_at ? Carbon::parse($v->created_at)->format('M d, Y') : '-',
                    'date_encoded_raw' => $v->created_at ? Carbon::parse($v->created_at)->toDateString() : null,
                    'age_days' => $reference ? Carbon::parse($reference)->diffInDays($today) : 0,
                    'purchase_price' => (float) ($v->purchase_price ?? 0),
                    'posted_price' => (float) ($v->posted_price ?? 0),
                    'purchase_date_raw' => $reference ? Carbon::parse($reference)->toDateString() : null,
                ];
            })
            ->values()
            ->all();

        $statusCounts = collect($rows)->groupBy('status')->map->count();
        $monthlyPurchaseTotals = collect($rows)
            ->filter(fn ($r) => ! empty($r['purchase_date_raw']))
            ->groupBy(fn ($r) => Carbon::parse($r['purchase_date_raw'])->format('Y-m'))
            ->sortKeys()
            ->map(function ($group, $ym) {
                return [
                    'label' => Carbon::createFromFormat('Y-m', $ym)->format('M Y'),
                    'total' => (float) $group->sum('purchase_price'),
                ];
            })
            ->values();

        return [
            'title' => 'Unit Inventory by Age (not sold)',
            'type' => 'age_table',
            'count' => count($rows),
            'columns' => ['Plate', 'Unit', 'Status', 'Date Encoded', 'Age (Days)', 'Purchase Price', 'Posted Price'],
            'rows' => $rows,
            'summary' => [
                ['label' => 'Units', 'value' => (float) count($rows), 'is_currency' => false],
                ['label' => 'Average Age (Days)', 'value' => (float) (collect($rows)->avg('age_days') ?? 0), 'is_currency' => false],
                ['label' => 'Max Age (Days)', 'value' => (float) (collect($rows)->max('age_days') ?? 0), 'is_currency' => false],
                ['label' => 'Total Purchase Price', 'value' => (float) collect($rows)->sum('purchase_price'), 'is_currency' => true],
            ],
            'charts' => [
                'unsold_status_split' => [
                    'title' => 'Unsold Units by Status',
                    'labels' => $statusCounts->keys()->all(),
                    'data' => $statusCounts->values()->all(),
                    'type' => 'pie',
                    'dataset_label' => 'Units',
                ],
                'unsold_purchase_monthly' => [
                    'title' => 'Unsold Inventory Value by Month',
                    'labels' => $monthlyPurchaseTotals->pluck('label')->all(),
                    'data' => $monthlyPurchaseTotals->pluck('total')->all(),
                    'type' => 'bar',
                    'dataset_label' => 'Purchase Value',
                ],
            ],
        ];
    }

    protected function reportInventorySalesAgeSold(array $window): array
    {
        $rows = Vehicle::with('statusDetail')
            ->where('status', 'Released')
            ->get()
            ->filter(fn (Vehicle $v) => $this->inWindow($v->statusDetail?->release_date ?? $v->statusDetail?->sale_date ?? $v->updated_at, $window))
            ->map(function (Vehicle $v) {
                $start = $v->purchase_date ?? $v->created_at;
                $end = $v->statusDetail?->release_date ?? $v->statusDetail?->sale_date ?? $v->updated_at;
                $ageDays = ($start && $end) ? Carbon::parse($start)->diffInDays(Carbon::parse($end)) : 0;

                return [
                    'plate' => $v->plate_number,
                    'unit' => $v->full_name,
                    'date_encoded' => $v->created_at ? Carbon::parse($v->created_at)->format('M d, Y') : '-',
                    'date_encoded_raw' => $v->created_at ? Carbon::parse($v->created_at)->toDateString() : null,
                    'purchase_price' => (float) ($v->purchase_price ?? 0),
                    'posted_price' => (float) ($v->posted_price ?? 0),
                    'sold_price' => (float) ($v->sold_price ?? 0),
                    'sold_date' => optional($end)?->format('M d, Y') ?? '-',
                    'sold_date_raw' => $end ? Carbon::parse($end)->toDateString() : null,
                    'age_days' => $ageDays,
                ];
            })
            ->values()
            ->all();

        $monthlyAvgAge = collect($rows)
            ->filter(fn ($r) => ! empty($r['sold_date_raw']))
            ->groupBy(fn ($r) => Carbon::parse($r['sold_date_raw'])->format('Y-m'))
            ->sortKeys()
            ->map(function ($group, $ym) {
                return [
                    'label' => Carbon::createFromFormat('Y-m', $ym)->format('M Y'),
                    'avg' => (float) ($group->avg('age_days') ?? 0),
                ];
            })
            ->values();

        $monthlySalesValue = collect($rows)
            ->filter(fn ($r) => ! empty($r['sold_date_raw']))
            ->groupBy(fn ($r) => Carbon::parse($r['sold_date_raw'])->format('Y-m'))
            ->sortKeys()
            ->map(function ($group, $ym) {
                return [
                    'label' => Carbon::createFromFormat('Y-m', $ym)->format('M Y'),
                    'sold_total' => (float) $group->sum('sold_price'),
                    'purchase_total' => (float) $group->sum('purchase_price'),
                ];
            })
            ->values();

        $ageBucketCounts = [
            '0-30 days' => 0,
            '31-60 days' => 0,
            '61-90 days' => 0,
            '91-180 days' => 0,
            '181+ days' => 0,
        ];
        foreach ($rows as $r) {
            $age = (int) ($r['age_days'] ?? 0);
            if ($age <= 30) {
                $ageBucketCounts['0-30 days']++;
            } elseif ($age <= 60) {
                $ageBucketCounts['31-60 days']++;
            } elseif ($age <= 90) {
                $ageBucketCounts['61-90 days']++;
            } elseif ($age <= 180) {
                $ageBucketCounts['91-180 days']++;
            } else {
                $ageBucketCounts['181+ days']++;
            }
        }

        return [
            'title' => 'Unit Inventory Sales by Age (sold)',
            'type' => 'age_sold_table',
            'count' => count($rows),
            'columns' => ['Plate', 'Unit', 'Date Encoded', 'Purchase Price', 'Posted Price', 'Sold Price', 'Sold Date', 'Age Before Sold (Days)'],
            'rows' => $rows,
            'summary' => [
                ['label' => 'Sold Units', 'value' => (float) count($rows), 'is_currency' => false],
                ['label' => 'Average Age Before Sold', 'value' => (float) (collect($rows)->avg('age_days') ?? 0), 'is_currency' => false],
                ['label' => 'Max Age Before Sold', 'value' => (float) (collect($rows)->max('age_days') ?? 0), 'is_currency' => false],
            ],
            'charts' => [
                'sold_age_avg_monthly' => [
                    'title' => 'Average Sold Age by Month',
                    'labels' => $monthlyAvgAge->pluck('label')->all(),
                    'data' => $monthlyAvgAge->pluck('avg')->all(),
                    'type' => 'line',
                    'dataset_label' => 'Avg Age (Days)',
                ],
                'sold_value_monthly' => [
                    'title' => 'Sold Value by Month',
                    'labels' => $monthlySalesValue->pluck('label')->all(),
                    'data' => $monthlySalesValue->pluck('sold_total')->all(),
                    'type' => 'bar',
                    'dataset_label' => 'Sold Value',
                ],
                'purchase_value_monthly' => [
                    'title' => 'Purchase Value by Sold Month',
                    'labels' => $monthlySalesValue->pluck('label')->all(),
                    'data' => $monthlySalesValue->pluck('purchase_total')->all(),
                    'type' => 'bar',
                    'dataset_label' => 'Purchase Value',
                ],
                'sold_age_bucket_split' => [
                    'title' => 'Sold Units Age Bucket Split',
                    'labels' => array_keys($ageBucketCounts),
                    'data' => array_values($ageBucketCounts),
                    'type' => 'pie',
                    'dataset_label' => 'Units',
                ],
            ],
        ];
    }

    protected function reportUnitRepairs(array $window): array
    {
        $rows = VehicleExpense::with('vehicle')
            ->get()
            ->filter(fn (VehicleExpense $e) => $this->inWindow($e->updated_at, $window))
            ->map(function (VehicleExpense $e) {
                return [
                    'plate' => $e->plate_number,
                    'unit' => $e->vehicle?->full_name ?? $e->plate_number,
                    'repair_cost' => (float) ($e->total_repair_cost ?? 0),
                    'post_reservation_repairs_cost' => (float) ($e->post_reservation_repairs_cost ?? 0),
                    'total_repairs' => (float) ($e->total_repair_cost ?? 0) + (float) ($e->post_reservation_repairs_cost ?? 0),
                    'event_date_raw' => $e->updated_at ? Carbon::parse($e->updated_at)->toDateString() : null,
                ];
            })
            ->values()
            ->all();

        $monthlyRepairs = collect($rows)
            ->filter(fn ($r) => ! empty($r['event_date_raw']))
            ->groupBy(fn ($r) => Carbon::parse($r['event_date_raw'])->format('Y-m'))
            ->sortKeys()
            ->map(function ($group, $ym) {
                return [
                    'label' => Carbon::createFromFormat('Y-m', $ym)->format('M Y'),
                    'total' => (float) $group->sum('total_repairs'),
                ];
            })
            ->values();

        return [
            'title' => 'Unit Repairs',
            'type' => 'repairs_table',
            'count' => count($rows),
            'columns' => ['Plate', 'Unit', 'Repair Cost', 'Post Reservation Repairs', 'Total Repairs'],
            'rows' => $rows,
            'grand_total' => collect($rows)->sum('total_repairs'),
            'summary' => [
                ['label' => 'Units with Repairs', 'value' => (float) count($rows), 'is_currency' => false],
                ['label' => 'Total Repairs', 'value' => (float) collect($rows)->sum('total_repairs'), 'is_currency' => true],
                ['label' => 'Average Repairs', 'value' => (float) (collect($rows)->avg('total_repairs') ?? 0), 'is_currency' => true],
            ],
            'charts' => [
                'repairs_monthly' => [
                    'title' => 'Repairs by Month',
                    'labels' => $monthlyRepairs->pluck('label')->all(),
                    'data' => $monthlyRepairs->pluck('total')->all(),
                    'type' => 'bar',
                    'dataset_label' => 'Repairs Total',
                ],
            ],
        ];
    }

    protected function streamFinancialCsv(array $data, string $baseName)
    {
        $result = $data['result'] ?? [];
        $type = (string) ($result['type'] ?? '');
        $filename = $baseName . '.csv';

        return response()->streamDownload(function () use ($data, $result, $type) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, ['Financial Report Export']);
            fputcsv($out, ['Report', (string) ($data['selectedReportLabel'] ?? '')]);
            fputcsv($out, ['Period', (string) ($data['selectedPeriod'] ?? '')]);
            fputcsv($out, ['Active Date Filter', (string) ($data['activeRangeLabel'] ?? '')]);
            fputcsv($out, []);

            if (! empty($result['summary']) && is_array($result['summary'])) {
                fputcsv($out, ['Summary']);
                fputcsv($out, ['Label', 'Value']);
                foreach ($result['summary'] as $s) {
                    $val = (float) ($s['value'] ?? 0);
                    $show = ! empty($s['is_currency']) ? number_format($val, 2, '.', '') : (string) $val;
                    fputcsv($out, [(string) ($s['label'] ?? ''), $show]);
                }
                fputcsv($out, []);
            }

            if ($type === 'single_metric') {
                fputcsv($out, [(string) ($result['metric_label'] ?? 'Value'), number_format((float) ($result['metric_value'] ?? 0), 2, '.', '')]);
                fclose($out);
                return;
            }
            if ($type === 'split_metric') {
                fputcsv($out, ['Cash', (string) ($result['cash'] ?? 0)]);
                fputcsv($out, ['Financing', (string) ($result['financing'] ?? 0)]);
                fclose($out);
                return;
            }

            $columns = $result['columns'] ?? [];
            $rows = $result['rows'] ?? [];
            if (is_array($columns) && is_array($rows) && ! empty($columns)) {
                fputcsv($out, $columns);
                $selectedReport = (string) ($data['selectedReport'] ?? '');
                foreach ($rows as $row) {
                    fputcsv($out, $this->csvRowValuesByType($type, (array) $row, $selectedReport));
                }
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    protected function csvRowValuesByType(string $type, array $row, ?string $selectedReport = null): array
    {
        return match ($type) {
            'table' => $selectedReport === 'releases'
                ? [
                    $row['plate'] ?? '',
                    $row['unit'] ?? '',
                    $row['date_encoded'] ?? '',
                    number_format((float) ($row['purchase_price'] ?? 0), 2, '.', ''),
                    number_format((float) ($row['sold_price'] ?? 0), 2, '.', ''),
                    $row['sold_date'] ?? '',
                    (string) ($row['age_days'] ?? 0),
                ]
                : [
                    $row['plate'] ?? '',
                    $row['unit'] ?? '',
                    $row['date_encoded'] ?? '',
                    number_format((float) ($row['purchase_price'] ?? 0), 2, '.', ''),
                    number_format((float) ($row['reservation_amount'] ?? 0), 2, '.', ''),
                    $row['reservation_date'] ?? '',
                    (string) ($row['age_days'] ?? 0),
                    $row['status'] ?? '',
                ],
            'value_table' => [
                $row['plate'] ?? '',
                $row['unit'] ?? '',
                number_format((float) ($row['value'] ?? 0), 2, '.', ''),
            ],
            'age_table' => [
                $row['plate'] ?? '',
                $row['unit'] ?? '',
                $row['status'] ?? '',
                $row['date_encoded'] ?? '',
                $row['age_days'] ?? 0,
                number_format((float) ($row['purchase_price'] ?? 0), 2, '.', ''),
                number_format((float) ($row['posted_price'] ?? 0), 2, '.', ''),
            ],
            'age_sold_table' => [
                $row['plate'] ?? '',
                $row['unit'] ?? '',
                $row['date_encoded'] ?? '',
                number_format((float) ($row['purchase_price'] ?? 0), 2, '.', ''),
                number_format((float) ($row['posted_price'] ?? 0), 2, '.', ''),
                number_format((float) ($row['sold_price'] ?? 0), 2, '.', ''),
                $row['sold_date'] ?? '',
                $row['age_days'] ?? 0,
            ],
            'repairs_table' => [
                $row['plate'] ?? '',
                $row['unit'] ?? '',
                number_format((float) ($row['repair_cost'] ?? 0), 2, '.', ''),
                number_format((float) ($row['post_reservation_repairs_cost'] ?? 0), 2, '.', ''),
                number_format((float) ($row['total_repairs'] ?? 0), 2, '.', ''),
            ],
            default => array_values($row),
        };
    }
}
