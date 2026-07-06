<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleExpense;
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
        $data = $this->buildFinancialPageData($request);
        $data['result'] = $this->applyFinancialTablePresentation($request, $data['result'], $data['selectedReport']);

        return view('analytics-reports.financial', $data);
    }

    public function exportFinancial(Request $request)
    {
        $format = strtolower((string) $request->get('format', 'csv'));
        if (! in_array($format, ['csv', 'pdf'], true)) {
            abort(422, 'Invalid export format.');
        }

        $data = $this->buildFinancialPageData($request);
        $baseName = 'financial-report-' . str_replace('_', '-', (string) $data['selectedReport']) . '-' . date('Y-m-d');

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('analytics-reports.financial-export-pdf', $data)->setPaper('a4', 'landscape');
            return $pdf->download($baseName . '.pdf');
        }

        return $this->streamFinancialCsv($data, $baseName);
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
        return view('analytics-reports.sales');
    }

    public function salesExecutive(Request $request)
    {
        return view('analytics-reports.sales-executive');
    }

    protected function resolveDateWindow(Request $request, string $period): array
    {
        $now = Carbon::now();
        return match ($period) {
            'daily' => ['from' => $now->copy()->startOfDay(), 'to' => $now->copy()->endOfDay()],
            'weekly' => ['from' => $now->copy()->startOfWeek(), 'to' => $now->copy()->endOfWeek()],
            'monthly' => [
                'from' => $request->filled('date_from')
                    ? Carbon::parse((string) $request->get('date_from'))->startOfDay()
                    : $now->copy()->startOfYear(),
                'to' => $request->filled('date_to')
                    ? Carbon::parse((string) $request->get('date_to'))->endOfDay()
                    : $now->copy()->endOfYear(),
            ],
            'quarterly' => ['from' => $now->copy()->startOfQuarter(), 'to' => $now->copy()->endOfQuarter()],
            'annually' => ['from' => $now->copy()->startOfYear(), 'to' => $now->copy()->endOfYear()],
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
        return Vehicle::with(['statusDetail', 'expense'])
            ->where('status', 'Released')
            ->get()
            ->filter(function (Vehicle $v) use ($window) {
                $eventDate = $v->statusDetail?->release_date ?? $v->statusDetail?->sale_date ?? $v->updated_at;
                return $this->inWindow($eventDate, $window);
            })
            ->values();
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
