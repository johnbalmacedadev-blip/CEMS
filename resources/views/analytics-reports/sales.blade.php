@extends('layouts.app')

@section('title', 'Car Sales Report - Car Empire Management System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-2">
        <h1 class="h3 mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Car Sales Report</h1>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
            <i class="fas fa-home me-1"></i>Back to Home
        </a>
    </div>

    @php
        $filterMode = $filterMode ?? '';
        $showResults = (bool) ($showResults ?? false);
        $isPeriodMode = $filterMode === 'period';
        $isCarTypeMode = $filterMode === 'car_type';
    @endphp

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('analytics-report.sales') }}" class="row g-2 align-items-end" id="carSalesFilterForm">
                <div class="col-12">
                    <label class="form-label">Filter mode</label>
                    <div class="d-flex flex-wrap gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="filter_mode" id="filterModePeriod" value="period" {{ $isPeriodMode ? 'checked' : '' }}>
                            <label class="form-check-label" for="filterModePeriod">Filter by Period</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="filter_mode" id="filterModeCarType" value="car_type" {{ $isCarTypeMode ? 'checked' : '' }}>
                            <label class="form-check-label" for="filterModeCarType">Filter by Car Type</label>
                        </div>
                    </div>
                </div>

                <div class="col-12 period-filter-fields" style="{{ $isPeriodMode ? '' : 'display:none;' }}">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4 col-lg-3">
                            <label for="period" class="form-label">Filter period</label>
                            <select name="period" id="period" class="form-select" {{ $isPeriodMode ? '' : 'disabled' }}>
                                @foreach($periodOptions as $key => $label)
                                    <option value="{{ $key }}" {{ $selectedPeriod === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        @php
                            $showYearFilter = in_array($selectedPeriod, ['monthly', 'quarterly', 'annually'], true);
                        @endphp
                        <div class="col-md-3 col-lg-2 year-filter-field" style="{{ $showYearFilter ? '' : 'display:none;' }}">
                            <label for="year" class="form-label">Year</label>
                            <select name="year" id="year" class="form-select" {{ ($isPeriodMode && $showYearFilter) ? '' : 'disabled' }}>
                                @foreach(($yearOptions ?? []) as $value => $label)
                                    <option value="{{ $value }}" {{ (int) ($selectedYear ?? date('Y')) === (int) $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 col-lg-2 month-filter-field" style="{{ $selectedPeriod === 'monthly' ? '' : 'display:none;' }}">
                            <label for="month" class="form-label">Month</label>
                            <select name="month" id="month" class="form-select" {{ ($isPeriodMode && $selectedPeriod === 'monthly') ? '' : 'disabled' }}>
                                @foreach(($monthOptions ?? ['' => 'All months in year']) as $value => $label)
                                    <option value="{{ $value }}" {{ (string) ($selectedMonth ?? '') === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 col-lg-2">
                            <label for="location" class="form-label">Location</label>
                            <select name="location" id="location" class="form-select" {{ $isPeriodMode ? '' : 'disabled' }}>
                                @foreach(($locationOptions ?? ['' => 'All Locations']) as $value => $label)
                                    <option value="{{ $value }}" {{ ($selectedLocation ?? '') === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 col-lg-2 custom-range-only-field" style="{{ $selectedPeriod === 'range' ? '' : 'display:none;' }}">
                            <label for="date_from" class="form-label">Date from</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $selectedPeriod === 'range' ? $dateFrom : '' }}" {{ ($isPeriodMode && $selectedPeriod === 'range') ? '' : 'disabled' }}>
                        </div>
                        <div class="col-md-3 col-lg-2 custom-range-only-field" style="{{ $selectedPeriod === 'range' ? '' : 'display:none;' }}">
                            <label for="date_to" class="form-label">Date to</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $selectedPeriod === 'range' ? $dateTo : '' }}" {{ ($isPeriodMode && $selectedPeriod === 'range') ? '' : 'disabled' }}>
                        </div>
                    </div>
                </div>

                <div class="col-12 car-type-filter-fields" style="{{ $isCarTypeMode ? '' : 'display:none;' }}">
                    <input type="hidden" name="period" id="carTypePeriod" value="monthly" {{ $isCarTypeMode ? '' : 'disabled' }}>
                    <div class="row g-2 align-items-end">
                        <div class="col-md-4 col-lg-3">
                            <label for="car_type" class="form-label">Search car sales by</label>
                            <select name="car_type" id="car_type" class="form-select" {{ $isCarTypeMode ? '' : 'disabled' }}>
                                @foreach(($carTypeOptions ?? []) as $value => $label)
                                    <option value="{{ $value }}" {{ ($selectedCarType ?? 'make') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 col-lg-3">
                            <label for="car_search" class="form-label">Search value</label>
                            <input type="text" name="car_search" id="car_search" class="form-control"
                                   value="{{ $carSearch ?? '' }}"
                                   placeholder="e.g. Toyota, SUV, Vios, 2022"
                                   {{ $isCarTypeMode ? '' : 'disabled' }}>
                        </div>
                        <div class="col-md-3 col-lg-2">
                            <label for="car_year" class="form-label">Year</label>
                            <select name="year" id="car_year" class="form-select" {{ $isCarTypeMode ? '' : 'disabled' }}>
                                @foreach(($yearOptions ?? []) as $value => $label)
                                    <option value="{{ $value }}" {{ (int) ($selectedYear ?? date('Y')) === (int) $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 col-lg-2">
                            <label for="car_month" class="form-label">Month</label>
                            <select name="month" id="car_month" class="form-select" {{ $isCarTypeMode ? '' : 'disabled' }}>
                                @foreach(($monthOptions ?? ['' => 'All months in year']) as $value => $label)
                                    <option value="{{ $value }}" {{ (string) ($selectedMonth ?? '') === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 col-lg-2">
                            <label for="car_location" class="form-label">Location</label>
                            <select name="location" id="car_location" class="form-select" {{ $isCarTypeMode ? '' : 'disabled' }}>
                                @foreach(($locationOptions ?? ['' => 'All Locations']) as $value => $label)
                                    <option value="{{ $value }}" {{ ($selectedLocation ?? '') === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <p class="small text-muted mb-0 mt-2">
                        Tabular Excel format: Total Sales / % of Sales / Total Releases / % of Releases for the selected car dimension.
                    </p>
                </div>

                <div class="col-auto">
                    <label class="form-label d-none d-md-block">&nbsp;</label>
                    <div class="d-flex align-items-center gap-2">
                        <button type="submit" class="btn btn-primary" id="updateReportBtn" {{ $filterMode === '' ? 'disabled' : '' }}>
                            <i class="fas fa-sync-alt me-1"></i>Update Report
                        </button>
                        @if($showResults && $isPeriodMode)
                        <button type="button"
                                class="btn btn-outline-danger js-export-sales-pdf"
                                id="exportSalesPdfBtn"
                                data-format="pdf"
                                data-loaded-mode="period"
                                title="Export PDF"
                                aria-label="Export PDF">
                            <i class="fas fa-file-pdf"></i>
                        </button>
                        @endif
                    </div>
                </div>
                <div class="col-12">
                    <div id="salesExportStatus" class="small text-muted d-flex align-items-center gap-2" aria-live="polite"></div>
                </div>
            </form>
            <p class="small text-muted mt-2 mb-0" id="carSalesFilterHint" data-loaded-mode="{{ $filterMode }}">
                @if($showResults && $isPeriodMode)
                    Released units only for unit counts. Gross Sales is Excel unit TOTAL REVENUE. Miscellaneous (including Total Forfeit Profit) is shown in its own column and is included in Total Net: <strong>{{ $activeRangeLabel }}</strong>
                    @if(($selectedLocation ?? '') !== '')
                        · Location: <strong>{{ $selectedLocation }}</strong>
                    @else
                        · Location: <strong>All Locations</strong>
                    @endif.
                    Monthly and Annually use the selected calendar year.
                    @if(!empty($excelAlignedCounts))
                        Unit counts follow Unit Report Excel release history for the selected dates.
                    @endif
                @elseif($showResults && $isCarTypeMode)
                    Car type breakdown for <strong>{{ $activeRangeLabel }}</strong>
                    @if(($selectedLocation ?? '') !== '')
                        · Location: <strong>{{ $selectedLocation }}</strong>
                    @endif.
                    Sales use reservation/sale dates; Releases use release dates.
                @else
                    Choose <strong>Filter by Period</strong> or <strong>Filter by Car Type</strong>, set your options, then click <strong>Update Report</strong>.
                @endif
            </p>
            <p class="small text-muted mt-2 mb-0 d-none" id="carSalesModeMismatchHint">
                Filter mode changed. Click <strong>Update Report</strong> to load results for the selected mode.
            </p>
        </div>
    </div>

    <div id="carSalesIdleState" class="{{ $showResults ? 'd-none' : '' }}">
        <div class="alert alert-light border mb-0">
            <i class="fas fa-filter me-2 text-muted"></i>
            No report loaded yet. Select a filter mode above to view Car Sales results.
        </div>
    </div>

    <div id="carSalesResults" data-loaded-mode="{{ $filterMode }}" class="{{ $showResults ? '' : 'd-none' }}">
    @if($showResults && $isCarTypeMode)
        @php $report = $carTypeReport ?? null; @endphp
        @if(empty($report) || empty($report['has_data']))
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i>
                No car sales/release rows found for this car type filter. Try clearing search or widening the month/year.
            </div>
        @else
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <strong>{{ $report['title'] }}</strong>
                        <div class="small text-muted">{{ $activeRangeLabel }}</div>
                    </div>
                    <span class="badge text-bg-light border">{{ number_format(count($report['rows'])) }} groups</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:3rem;">Rank</th>
                                    <th>{{ $report['dimension_label'] }}</th>
                                    <th class="text-end">Total Sales</th>
                                    <th class="text-end">% of Total Sales</th>
                                    <th class="text-end">Total Releases</th>
                                    <th class="text-end">% of Total Releases</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($report['rows'] as $i => $row)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td class="fw-semibold">{{ $row['label'] }}</td>
                                        <td class="text-end">{{ number_format((int) $row['sales_count']) }}</td>
                                        <td class="text-end">{{ number_format((float) $row['sales_pct'], 1) }}%</td>
                                        <td class="text-end">{{ number_format((int) $row['release_count']) }}</td>
                                        <td class="text-end">{{ number_format((float) $row['release_pct'], 1) }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th>—</th>
                                    <th>TOTAL</th>
                                    <th class="text-end">{{ number_format((int) ($report['totals']['sales_count'] ?? 0)) }}</th>
                                    <th class="text-end">—</th>
                                    <th class="text-end">{{ number_format((int) ($report['totals']['release_count'] ?? 0)) }}</th>
                                    <th class="text-end">—</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @elseif($showResults && !$hasData)
        <div class="alert alert-info mb-0">
            <i class="fas fa-info-circle me-2"></i>
            No released units or miscellaneous entries found for this date range. Try expanding the period or selecting Custom Date Range.
        </div>
    @elseif($showResults)
        <div class="row g-3 mb-3 align-items-stretch sales-summary-row">
            @foreach($summary as $card)
                @php
                    $isComposite = !empty($card['is_composite']) && !empty($card['items']);
                    $cardKey = $card['key'] ?? '';
                    $colClass = match (true) {
                        $isComposite => 'col-12 col-md-8',
                        $cardKey === 'units_released' => 'col-6 col-md-2',
                        default => 'col-6 col-md-2',
                    };
                @endphp
                <div class="{{ $colClass }}">
                    <button
                        type="button"
                        class="card border-primary h-100 text-start w-100 sales-summary-card {{ $isComposite ? 'sales-summary-card--pnl' : 'sales-summary-card--compact' }}"
                        data-bs-toggle="modal"
                        data-bs-target="#salesSummaryDetailModal"
                        data-detail-title="{{ e($card['detail']['title'] ?? $card['label']) }}"
                        data-detail-formula="{{ e($card['detail']['formula'] ?? '') }}"
                        data-detail-description="{{ e($card['detail']['description'] ?? '') }}"
                        data-detail-body="salesDetailBody{{ $loop->index }}"
                    >
                        <div class="card-body py-3 text-start">
                            <div class="sales-summary-card__header mb-2">
                                <div class="sales-summary-card__title">{{ $card['label'] }}</div>
                                <span class="badge text-bg-light border text-muted small fw-normal sales-summary-card__details">
                                    <i class="fas fa-info-circle me-1"></i>Details
                                </span>
                            </div>
                            @if($isComposite)
                                <div class="sales-pnl-grid">
                                    @foreach($card['items'] as $item)
                                        <div class="sales-pnl-item">
                                            <div class="sales-pnl-label">{{ $item['label'] }}</div>
                                            <div class="sales-pnl-value {{ ($item['label'] ?? '') === 'Total Net' ? ((float)($item['value'] ?? 0) < 0 ? 'text-danger' : 'text-success') : 'text-dark' }}">
                                                @if(!empty($item['is_currency']))
                                                    ₱{{ number_format((float) $item['value'], 2) }}
                                                @else
                                                    {{ number_format((float) $item['value'], 0) }}
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="sales-kpi-value mb-0">
                                    @if(!empty($card['is_currency']))
                                        ₱{{ number_format((float) $card['value'], 2) }}
                                    @else
                                        {{ is_float($card['value']) || str_contains((string) $card['value'], '.')
                                            ? number_format((float) $card['value'], 1)
                                            : number_format((float) $card['value'], 0) }}<span class="sales-kpi-suffix">{{ $card['suffix'] ?? '' }}</span>
                                    @endif
                                </div>
                                @if(!empty($card['location_breakdown']) && is_array($card['location_breakdown']))
                                    <div class="sales-kpi-meta mt-2">
                                        @foreach($card['location_breakdown'] as $loc)
                                            <div>{{ $loc['label'] }}: <strong>{{ number_format((int) ($loc['count'] ?? 0)) }}</strong></div>
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                        </div>
                    </button>
                    <div id="salesDetailBody{{ $loop->index }}" class="d-none" aria-hidden="true">
                        @php
                            $locationTable = $card['detail']['location_table'] ?? null;
                            $detailLines = $card['detail']['lines'] ?? [];
                        @endphp
                        @if(!empty($locationTable['rows']))
                            <table class="table table-sm table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Location</th>
                                        <th class="text-end">Units</th>
                                        <th class="text-end">Gross Sales</th>
                                        <th class="text-end">Miscellaneous</th>
                                        <th class="text-end">Total Costs</th>
                                        <th class="text-end">Total Net</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($locationTable['rows'] as $locRow)
                                        <tr class="{{ !empty($locRow['is_total']) ? 'table-light fw-bold' : '' }}">
                                            <td>{{ $locRow['label'] }}</td>
                                            <td class="text-end">{{ number_format((int) ($locRow['units'] ?? 0)) }}</td>
                                            <td class="text-end text-nowrap">₱{{ number_format((float) ($locRow['gross'] ?? 0), 2) }}</td>
                                            <td class="text-end text-nowrap">₱{{ number_format((float) ($locRow['miscellaneous'] ?? 0), 2) }}</td>
                                            <td class="text-end text-nowrap">₱{{ number_format((float) ($locRow['costs'] ?? 0), 2) }}</td>
                                            <td class="text-end text-nowrap {{ ((float) ($locRow['net'] ?? 0)) < 0 ? 'text-danger' : '' }}">₱{{ number_format((float) ($locRow['net'] ?? 0), 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <table class="table table-sm mb-0">
                                <tbody>
                                    @foreach($detailLines as $line)
                                        @if(!empty($line['is_section']))
                                            <tr class="table-light">
                                                <td colspan="2" class="fw-bold text-uppercase small">{{ $line['label'] ?? '' }}</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td>{{ $line['label'] ?? '' }}</td>
                                                <td class="text-end fw-semibold text-nowrap">{{ $line['value'] ?? '' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="modal fade" id="salesSummaryDetailModal" tabindex="-1" aria-labelledby="salesSummaryDetailModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="salesSummaryDetailModalLabel">Card details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2"><strong>How it is calculated</strong></p>
                        <p class="font-monospace small bg-light border rounded p-2" id="salesSummaryDetailFormula"></p>
                        <p class="text-muted" id="salesSummaryDetailDescription"></p>
                        <div id="salesSummaryDetailContent" class="table-responsive"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-12">
                <div class="card h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <strong>{{ $charts['monthly_sales']['title'] ?? 'Monthly Gross Sales' }}</strong>
                        <span class="small text-muted">Units Gross Sales + Miscellaneous by month</span>
                    </div>
                    <div class="card-body">
                        <div class="sales-chart-wrap sales-chart-wrap--monthly">
                            <canvas class="sales-report-chart" data-chart-key="monthly_sales"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <strong>{{ $charts['top_makes']['title'] ?? 'Top Makes' }}</strong>
                    </div>
                    <div class="card-body">
                        <div class="sales-chart-wrap sales-chart-wrap--tall">
                            <canvas class="sales-report-chart" data-chart-key="top_makes"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <strong>{{ $charts['top_models']['title'] ?? 'Top Models' }}</strong>
                    </div>
                    <div class="card-body">
                        <div class="sales-chart-wrap sales-chart-wrap--tall">
                            <canvas class="sales-report-chart" data-chart-key="top_models"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <strong>{{ $charts['sales_mom']['title'] ?? 'Monthly Sales Comparison' }}</strong>
                    </div>
                    <div class="card-body">
                        <div class="sales-chart-wrap">
                            <canvas class="sales-report-chart" data-chart-key="sales_mom"></canvas>
                        </div>
                        <p class="small text-muted mb-0 mt-2">Gross Sales (units + miscellaneous) for the last 12 months vs the same months a year earlier.</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <strong>{{ $charts['fastest_models']['title'] ?? 'Fastest-Selling Models' }}</strong>
                    </div>
                    <div class="card-body">
                        <div class="sales-chart-wrap sales-chart-wrap--tall">
                            <canvas class="sales-report-chart" data-chart-key="fastest_models"></canvas>
                        </div>
                        <p class="small text-muted mb-0 mt-2">Lower average days = faster turnover. Models need at least 3 releases in the selected range.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-12">
                <div class="card sales-paged-card">
                    <div class="card-header bg-white"><strong>Monthly Breakdown</strong></div>
                    <div class="card-body p-0 d-flex flex-column">
                        <div class="sales-paged-table" data-page-size="12">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Month</th>
                                            <th class="text-end">Units</th>
                                            <th class="text-end">Gross Sales</th>
                                            <th class="text-end">Avg Days to Sell</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse(($tables['monthly'] ?? []) as $row)
                                            <tr>
                                                <td>{{ $row['label'] ?? '' }}</td>
                                                <td class="text-end">{{ number_format((int) ($row['count'] ?? 0)) }}</td>
                                                <td class="text-end">₱{{ number_format((float) ($row['sales'] ?? 0), 2) }}</td>
                                                <td class="text-end">{{ number_format((float) ($row['avg_days'] ?? 0), 1) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3">No monthly sales in this range.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if(!empty($tables['monthly']))
                                        <tfoot class="table-light">
                                            <tr>
                                                <th>Total</th>
                                                <th class="text-end">{{ number_format(collect($tables['monthly'])->sum('count')) }}</th>
                                                <th class="text-end">₱{{ number_format((float) collect($tables['monthly'])->sum('sales'), 2) }}</th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                            <div class="sales-paged-nav border-top px-3 py-2 d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-sm btn-outline-secondary sales-page-prev">Prev</button>
                                <span class="small text-muted sales-page-status"></span>
                                <button type="button" class="btn btn-sm btn-outline-secondary sales-page-next">Next</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-lg-6">
                <div class="card h-100 sales-paged-card">
                    <div class="card-header bg-white"><strong>All Makes</strong></div>
                    <div class="card-body p-0 d-flex flex-column">
                        <div class="sales-paged-table" data-page-size="10">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Make</th>
                                            <th class="text-end">Units</th>
                                            <th class="text-end">Gross Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($tables['top_makes'] as $row)
                                            <tr class="sales-paged-row">
                                                <td>{{ $row['label'] }}</td>
                                                <td class="text-end">{{ number_format($row['count']) }}</td>
                                                <td class="text-end">₱{{ number_format($row['sales'], 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="text-muted text-center py-3">No data</td></tr>
                                        @endforelse
                                    </tbody>
                                    @if(!empty($tables['top_makes']))
                                        <tfoot class="table-light fw-semibold">
                                            <tr>
                                                <td>Total</td>
                                                <td class="text-end">{{ number_format(collect($tables['top_makes'])->sum('count')) }}</td>
                                                <td class="text-end">₱{{ number_format((float) collect($tables['top_makes'])->sum('sales'), 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                            <div class="sales-paged-nav px-3 py-2 border-top d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-page-prev>&laquo; Prev</button>
                                <span class="small text-muted" data-page-status></span>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-page-next>Next &raquo;</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100 sales-paged-card">
                    <div class="card-header bg-white"><strong>All Models</strong></div>
                    <div class="card-body p-0 d-flex flex-column">
                        <div class="sales-paged-table" data-page-size="10">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Model</th>
                                            <th class="text-end">Units</th>
                                            <th class="text-end">Gross Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($tables['top_models'] as $row)
                                            <tr class="sales-paged-row">
                                                <td>{{ $row['label'] }}</td>
                                                <td class="text-end">{{ number_format($row['count']) }}</td>
                                                <td class="text-end">₱{{ number_format($row['sales'], 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="text-muted text-center py-3">No data</td></tr>
                                        @endforelse
                                    </tbody>
                                    @if(!empty($tables['top_models']))
                                        <tfoot class="table-light fw-semibold">
                                            <tr>
                                                <td>Total</td>
                                                <td class="text-end">{{ number_format(collect($tables['top_models'])->sum('count')) }}</td>
                                                <td class="text-end">₱{{ number_format((float) collect($tables['top_models'])->sum('sales'), 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                            <div class="sales-paged-nav px-3 py-2 border-top d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-page-prev>&laquo; Prev</button>
                                <span class="small text-muted" data-page-status></span>
                                <button type="button" class="btn btn-sm btn-outline-secondary" data-page-next>Next &raquo;</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card h-100">
                    <div class="card-header bg-white"><strong>Fastest Models</strong></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Model</th>
                                        <th class="text-end">Avg Days</th>
                                        <th class="text-end">Units</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tables['fastest_models'] as $row)
                                        <tr>
                                            <td>{{ $row['label'] }}</td>
                                            <td class="text-end">{{ number_format($row['avg_days'], 1) }}</td>
                                            <td class="text-end">{{ number_format($row['count']) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-muted text-center py-3">Need 3+ units per model</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(!empty($tables['miscellaneous']))
            <div class="card mb-0 mt-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong>Miscellaneous</strong>
                    <span class="text-muted small">{{ number_format(count($tables['miscellaneous'])) }} entr{{ count($tables['miscellaneous']) === 1 ? 'y' : 'ies' }} in this date range</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 160px;">Transaction Date</th>
                                    <th style="width: 120px;">Location</th>
                                    <th>Description</th>
                                    <th class="text-end" style="width: 160px;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tables['miscellaneous'] as $row)
                                    <tr>
                                        <td>{{ $row['transaction_date_label'] ?? $row['transaction_date'] }}</td>
                                        <td>{{ $row['location'] ?: '—' }}</td>
                                        <td>{{ $row['description'] }}</td>
                                        <td class="text-end">₱{{ number_format((float) $row['amount'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light fw-semibold">
                                <tr>
                                    <td colspan="3" class="text-end">Total</td>
                                    <td class="text-end">₱{{ number_format((float) ($tables['miscellaneous_total'] ?? 0), 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    @endif
    </div>
</div>
@endsection

@section('styles')
<style>
.sales-chart-wrap {
    position: relative;
    height: 280px;
}
.sales-chart-wrap--tall {
    height: 340px;
}
.sales-chart-wrap--monthly {
    height: 360px;
}
.sales-paged-table tr.is-pad td {
    border-color: transparent;
    color: transparent;
    user-select: none;
    height: 2rem;
}
.sales-paged-nav .btn:disabled {
    opacity: 0.45;
}
.sales-summary-card {
    background: #fff;
    cursor: pointer;
    transition: box-shadow .15s ease, transform .15s ease, border-color .15s ease;
    border-width: 1px !important;
    text-align: left !important;
}
.sales-summary-card:hover,
.sales-summary-card:focus {
    box-shadow: 0 0.35rem 0.85rem rgba(13, 110, 253, 0.14);
    transform: translateY(-1px);
    outline: none;
}
.sales-summary-card:focus-visible {
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.35);
}
.sales-summary-card .card-body,
.sales-summary-card__title,
.sales-kpi-value,
.sales-kpi-meta,
.sales-pnl-label,
.sales-pnl-value {
    text-align: left !important;
}
.sales-summary-card__header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 0.5rem;
}
.sales-summary-card__title {
    flex: 1 1 auto;
    min-width: 0;
    color: #6c757d;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    line-height: 1.25;
}
.sales-summary-card__details {
    flex: 0 0 auto;
}
.sales-summary-card--compact .card-body {
    padding-left: 1rem;
    padding-right: 1rem;
}
.sales-kpi-value {
    font-size: 1.75rem;
    font-weight: 700;
    line-height: 1.15;
    letter-spacing: -0.02em;
    color: #212529;
}
.sales-kpi-suffix {
    font-size: 0.95rem;
    font-weight: 600;
    color: #6c757d;
    margin-left: 0.15rem;
}
.sales-kpi-meta {
    font-size: 0.8rem;
    color: #6c757d;
    line-height: 1.35;
}
.sales-pnl-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0.85rem 1rem;
}
.sales-pnl-item {
    min-width: 0;
}
.sales-pnl-label {
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #6c757d;
    font-weight: 600;
    margin-bottom: 0.2rem;
    white-space: nowrap;
}
.sales-pnl-value {
    font-size: 0.98rem;
    font-weight: 700;
    line-height: 1.25;
    white-space: nowrap;
    font-variant-numeric: tabular-nums;
    letter-spacing: -0.01em;
}
@media (min-width: 768px) {
    .sales-summary-row {
        flex-wrap: nowrap;
    }
    .sales-pnl-grid {
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.65rem 0.85rem;
    }
    .sales-pnl-item:not(:last-child) {
        border-right: 1px solid #e9ecef;
        padding-right: 0.75rem;
    }
}
@media (min-width: 1200px) {
    .sales-pnl-value {
        font-size: 1.05rem;
    }
}
@media (max-width: 575.98px) {
    .sales-pnl-value {
        font-size: 0.95rem;
        white-space: normal;
        overflow-wrap: anywhere;
    }
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const period = document.getElementById('period');
    const customRangeOnlyFields = document.querySelectorAll('.custom-range-only-field');
    const yearFilterFields = document.querySelectorAll('.year-filter-field');
    const monthFilterFields = document.querySelectorAll('.month-filter-field');
    const periodFilterFields = document.querySelectorAll('.period-filter-fields');
    const carTypeFilterFields = document.querySelectorAll('.car-type-filter-fields');
    const filterModeInputs = document.querySelectorAll('input[name="filter_mode"]');
    const updateBtn = document.getElementById('updateReportBtn');
    const carTypePeriod = document.getElementById('carTypePeriod');

    function setSectionEnabled(sectionEls, enabled) {
        sectionEls.forEach(el => {
            el.style.display = enabled ? '' : 'none';
            el.querySelectorAll('input, select, textarea').forEach(input => {
                input.disabled = !enabled;
            });
        });
    }

    function syncResultsVisibility() {
        const selected = document.querySelector('input[name="filter_mode"]:checked');
        const mode = selected ? selected.value : '';
        const results = document.getElementById('carSalesResults');
        const idle = document.getElementById('carSalesIdleState');
        const filterHint = document.getElementById('carSalesFilterHint');
        const mismatchHint = document.getElementById('carSalesModeMismatchHint');
        const exportBtn = document.getElementById('exportSalesPdfBtn');
        const loadedMode = results ? (results.getAttribute('data-loaded-mode') || '') : '';
        const hasLoadedResults = loadedMode === 'period' || loadedMode === 'car_type';
        const modeMatches = hasLoadedResults && mode === loadedMode;

        if (results) {
            results.classList.toggle('d-none', !modeMatches);
        }

        if (idle) {
            idle.classList.toggle('d-none', modeMatches);
            const msg = idle.querySelector('.alert');
            if (msg && !modeMatches) {
                if (hasLoadedResults && mode !== '' && mode !== loadedMode) {
                    msg.innerHTML = '<i class="fas fa-filter me-2 text-muted"></i>Results are hidden because the filter mode changed. Click <strong>Update Report</strong> to load this mode.';
                } else if (mode !== '') {
                    msg.innerHTML = '<i class="fas fa-filter me-2 text-muted"></i>No report loaded yet. Set your options, then click <strong>Update Report</strong>.';
                } else {
                    msg.innerHTML = '<i class="fas fa-filter me-2 text-muted"></i>No report loaded yet. Select a filter mode above to view Car Sales results.';
                }
            }
        }

        if (filterHint) {
            // Keep the original loaded-mode hint only while that mode is still selected.
            filterHint.classList.toggle('d-none', hasLoadedResults && mode !== loadedMode);
        }
        if (mismatchHint) {
            mismatchHint.classList.toggle('d-none', !(hasLoadedResults && mode !== '' && mode !== loadedMode));
        }
        if (exportBtn) {
            exportBtn.classList.toggle('d-none', !(modeMatches && mode === 'period'));
        }
    }

    function toggleFilterMode() {
        const selected = document.querySelector('input[name="filter_mode"]:checked');
        const mode = selected ? selected.value : '';
        if (updateBtn) updateBtn.disabled = mode === '';

        setSectionEnabled(periodFilterFields, mode === 'period');
        setSectionEnabled(carTypeFilterFields, mode === 'car_type');

        if (mode === 'period') {
            toggleRangeFields();
        }
        if (carTypePeriod) {
            carTypePeriod.disabled = mode !== 'car_type';
        }

        syncResultsVisibility();
    }

    function toggleRangeFields() {
        const modeEl = document.querySelector('input[name="filter_mode"]:checked');
        if (!modeEl || modeEl.value !== 'period') return;

        const periodVal = period ? period.value : 'monthly';
        const isCustomRange = periodVal === 'range';
        const showYear = ['monthly', 'quarterly', 'annually'].indexOf(periodVal) !== -1;
        const showMonth = periodVal === 'monthly';

        customRangeOnlyFields.forEach(el => {
            el.style.display = isCustomRange ? '' : 'none';
            el.querySelectorAll('input').forEach(input => {
                input.disabled = !isCustomRange;
                if (!isCustomRange) input.value = '';
            });
        });

        yearFilterFields.forEach(el => {
            el.style.display = showYear ? '' : 'none';
            el.querySelectorAll('select').forEach(select => { select.disabled = !showYear; });
        });

        monthFilterFields.forEach(el => {
            el.style.display = showMonth ? '' : 'none';
            el.querySelectorAll('select').forEach(select => { select.disabled = !showMonth; });
        });

        // Location stays enabled in period mode.
        const location = document.getElementById('location');
        if (location) location.disabled = false;
        if (period) period.disabled = false;
    }

    filterModeInputs.forEach(input => input.addEventListener('change', toggleFilterMode));
    if (period) period.addEventListener('change', toggleRangeFields);
    toggleFilterMode();

    const detailModal = document.getElementById('salesSummaryDetailModal');
    if (detailModal) {
        detailModal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget
                ? event.relatedTarget.closest('.sales-summary-card')
                : null;
            if (!trigger) return;
            const title = trigger.getAttribute('data-detail-title') || 'Card details';
            const formula = trigger.getAttribute('data-detail-formula') || '';
            const description = trigger.getAttribute('data-detail-description') || '';
            const bodyId = trigger.getAttribute('data-detail-body') || '';
            const source = bodyId ? document.getElementById(bodyId) : null;

            const titleEl = detailModal.querySelector('#salesSummaryDetailModalLabel');
            const formulaEl = detailModal.querySelector('#salesSummaryDetailFormula');
            const descriptionEl = detailModal.querySelector('#salesSummaryDetailDescription');
            const contentEl = detailModal.querySelector('#salesSummaryDetailContent');

            if (titleEl) titleEl.textContent = title;
            if (formulaEl) formulaEl.textContent = formula;
            if (descriptionEl) descriptionEl.textContent = description;
            if (contentEl) {
                contentEl.innerHTML = source ? source.innerHTML : '<p class="text-muted mb-0">No breakdown is available for this card.</p>';
            }
        });
    }

    const exportButtons = document.querySelectorAll('.js-export-sales-pdf');
    const exportStatus = document.getElementById('salesExportStatus');
    const exportBaseUrl = @json(route('analytics-report.sales.export'));

    function setExportStatus(state, message) {
        if (!exportStatus) return;
        if (state === 'loading') {
            exportStatus.innerHTML = '<span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span><span>' + message + '</span>';
            return;
        }
        if (state === 'success') {
            exportStatus.innerHTML = '<i class="fas fa-check-circle text-success"></i><span class="text-success">' + message + '</span>';
            return;
        }
        if (state === 'error') {
            exportStatus.innerHTML = '<i class="fas fa-exclamation-circle text-danger"></i><span class="text-danger">' + message + '</span>';
            return;
        }
        exportStatus.textContent = message || '';
    }

    function setExportButtonsDisabled(disabled) {
        exportButtons.forEach(btn => {
            btn.disabled = disabled;
            btn.classList.toggle('disabled', disabled);
        });
    }

    function buildExportUrl() {
        const form = document.querySelector('form[action="{{ route('analytics-report.sales') }}"]');
        const params = new URLSearchParams(new FormData(form));
        return exportBaseUrl + '?' + params.toString();
    }

    function parseFilename(contentDisposition, fallback) {
        if (!contentDisposition) return fallback;
        const starMatch = contentDisposition.match(/filename\*=UTF-8''([^;]+)/i);
        if (starMatch && starMatch[1]) {
            return decodeURIComponent(starMatch[1].replace(/['"]/g, '').trim());
        }
        const match = contentDisposition.match(/filename="?([^";]+)"?/i);
        return match && match[1] ? match[1].trim() : fallback;
    }

    async function exportSalesPdf() {
        try {
            setExportButtonsDisabled(true);
            setExportStatus('loading', 'Exporting PDF... please wait.');
            const response = await fetch(buildExportUrl(), {
                method: 'GET',
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) {
                throw new Error('Export request failed');
            }
            const blob = await response.blob();
            const filename = parseFilename(response.headers.get('content-disposition'), 'sales-report.pdf');
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);
            setExportStatus('success', 'PDF exported successfully.');
        } catch (error) {
            setExportStatus('error', 'Export failed. Please try again.');
        } finally {
            setExportButtonsDisabled(false);
        }
    }

    exportButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            exportSalesPdf();
        });
    });

    const charts = @json($charts ?? []);
    if (!window.Chart || !charts || typeof charts !== 'object') return;

    const palette = [
        'rgba(13, 110, 253, 0.75)',
        'rgba(25, 135, 84, 0.75)',
        'rgba(255, 193, 7, 0.75)',
        'rgba(220, 53, 69, 0.75)',
        'rgba(111, 66, 193, 0.75)',
        'rgba(13, 202, 240, 0.75)',
        'rgba(108, 117, 125, 0.75)',
        'rgba(253, 126, 20, 0.75)',
        'rgba(32, 201, 151, 0.75)',
        'rgba(214, 51, 132, 0.75)'
    ];

    document.querySelectorAll('.sales-report-chart').forEach(function (canvasEl) {
        const key = canvasEl.getAttribute('data-chart-key');
        const chartCfg = charts[key];
        if (!chartCfg || !Array.isArray(chartCfg.labels) || chartCfg.labels.length === 0) return;

        const type = chartCfg.type || 'bar';
        const isPieType = type === 'pie' || type === 'doughnut' || type === 'polarArea';
        const data = chartCfg.data || [];
        const indexAxis = chartCfg.index_axis || 'x';
        const isCurrency = !!chartCfg.is_currency || /₱|sales amount/i.test(String(chartCfg.dataset_label || '') + ' ' + String(chartCfg.title || ''));
        const backgroundColor = isPieType
            ? data.map((_, i) => palette[i % palette.length])
            : (indexAxis === 'y' ? data.map((_, i) => palette[i % palette.length]) : 'rgba(13, 110, 253, 0.55)');
        const borderColor = isPieType || indexAxis === 'y'
            ? (Array.isArray(backgroundColor)
                ? backgroundColor.map(c => String(c).replace('0.75', '1'))
                : 'rgba(13, 110, 253, 1)')
            : (type === 'line' ? 'rgba(25, 135, 84, 1)' : 'rgba(13, 110, 253, 1)');

        const formatTick = function (value) {
            const n = Number(value);
            if (isCurrency) {
                if (Math.abs(n) >= 1000000) return '₱' + (n / 1000000).toFixed(1) + 'M';
                if (Math.abs(n) >= 1000) return '₱' + (n / 1000).toFixed(0) + 'K';
                return '₱' + n.toLocaleString();
            }
            return Number.isInteger(n) ? n : n.toFixed(1);
        };

        const multiDatasets = Array.isArray(chartCfg.datasets) && chartCfg.datasets.length > 0
            ? chartCfg.datasets.map(function (ds, i) {
                const color = ds.borderColor || palette[i % palette.length];
                return {
                    label: ds.label || chartCfg.dataset_label || 'Value',
                    data: ds.data || [],
                    backgroundColor: ds.backgroundColor || (type === 'line' ? String(color).replace('1)', '0.12)') : color),
                    borderColor: color,
                    borderWidth: 2,
                    borderDash: ds.borderDash || [],
                    fill: type === 'line',
                    tension: type === 'line' ? 0.25 : 0,
                    pointRadius: type === 'line' ? 3 : 0,
                    pointHoverRadius: 5
                };
            })
            : [{
                label: chartCfg.dataset_label || 'Value',
                data: data,
                backgroundColor: type === 'line' ? 'rgba(25, 135, 84, 0.15)' : backgroundColor,
                borderColor: borderColor,
                borderWidth: 1.5,
                fill: type === 'line',
                tension: type === 'line' ? 0.25 : 0
            }];

        const options = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: isPieType || multiDatasets.length > 1 },
                tooltip: {
                    callbacks: {
                        label: function (ctx) {
                            const n = Number(ctx.parsed.y ?? ctx.parsed ?? 0);
                            const name = ctx.dataset.label ? ctx.dataset.label + ': ' : '';
                            return name + (isCurrency ? ('₱' + n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })) : n.toLocaleString());
                        }
                    }
                }
            }
        };

        if (!isPieType) {
            options.indexAxis = indexAxis;
            options.scales = {
                x: { beginAtZero: true, ticks: { precision: 0 } },
                y: { beginAtZero: true, ticks: { callback: formatTick } }
            };
            if (indexAxis === 'y') {
                options.scales.x.ticks = { callback: formatTick };
                options.scales.y.ticks = { precision: 0 };
            } else if (!isCurrency) {
                options.scales.y.ticks.precision = 0;
            }
        }

        new Chart(canvasEl, {
            type: type,
            data: {
                labels: chartCfg.labels,
                datasets: multiDatasets
            },
            options: options
        });
    });

    document.querySelectorAll('.sales-paged-table').forEach(function (wrap) {
        const pageSize = Math.max(1, parseInt(wrap.getAttribute('data-page-size') || '10', 10));
        const tbody = wrap.querySelector('tbody');
        if (!tbody) return;
        const rows = Array.from(tbody.querySelectorAll('tr.sales-paged-row'));
        if (rows.length === 0) return;

        const prevBtn = wrap.querySelector('[data-page-prev]');
        const nextBtn = wrap.querySelector('[data-page-next]');
        const statusEl = wrap.querySelector('[data-page-status]');
        const colCount = (tbody.querySelector('tr') && tbody.querySelector('tr').children.length) || 3;
        const pageCount = Math.max(1, Math.ceil(rows.length / pageSize));
        let page = 1;

        function ensurePads() {
            tbody.querySelectorAll('tr.is-pad').forEach(function (el) { el.remove(); });
            const visible = rows.filter(function (row) { return row.style.display !== 'none'; }).length;
            for (let i = visible; i < pageSize; i++) {
                const pad = document.createElement('tr');
                pad.className = 'is-pad';
                pad.innerHTML = Array.from({ length: colCount }, function () {
                    return '<td>&nbsp;</td>';
                }).join('');
                tbody.appendChild(pad);
            }
        }

        function render() {
            const start = (page - 1) * pageSize;
            const end = start + pageSize;
            rows.forEach(function (row, i) {
                row.style.display = (i >= start && i < end) ? '' : 'none';
            });
            ensurePads();
            if (statusEl) statusEl.textContent = 'Page ' + page + ' of ' + pageCount;
            if (prevBtn) prevBtn.disabled = page <= 1;
            if (nextBtn) nextBtn.disabled = page >= pageCount;
        }

        if (prevBtn) prevBtn.addEventListener('click', function () {
            if (page > 1) { page -= 1; render(); }
        });
        if (nextBtn) nextBtn.addEventListener('click', function () {
            if (page < pageCount) { page += 1; render(); }
        });
        render();
    });
});
</script>
@endsection
