@extends('layouts.app')

@section('title', 'Sales Executive Report - Car Empire Management System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-2">
        <h1 class="h3 mb-0"><i class="fas fa-user-tie me-2 text-warning"></i>Sales Executive Report</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('analytics-report.sales') }}" class="btn btn-outline-primary">
                <i class="fas fa-chart-bar me-1"></i>Car Sales Report
            </a>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                <i class="fas fa-home me-1"></i>Back to Home
            </a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('analytics-report.sales-executive') }}" class="row g-2 align-items-end">
                <div class="col-md-4 col-lg-3">
                    <label for="view" class="form-label">Show ranking for</label>
                    <select name="view" id="view" class="form-select">
                        <option value="executives" {{ $viewMode === 'executives' ? 'selected' : '' }}>Sales Executives</option>
                        <option value="agents" {{ $viewMode === 'agents' ? 'selected' : '' }}>Sales Agents</option>
                        <option value="team" {{ $viewMode === 'team' ? 'selected' : '' }}>Sales Team (all credited names)</option>
                    </select>
                </div>
                <div class="col-md-4 col-lg-2">
                    <label for="period" class="form-label">Filter period</label>
                    <select name="period" id="period" class="form-select">
                        @foreach($periodOptions as $key => $label)
                            <option value="{{ $key }}" {{ $selectedPeriod === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                @php
                    $showYearMonth = in_array($selectedPeriod, ['monthly', 'quarterly', 'annually'], true);
                @endphp
                <div class="col-md-3 col-lg-2 year-filter-field" style="{{ $showYearMonth ? '' : 'display:none;' }}">
                    <label for="year" class="form-label">Year</label>
                    <select name="year" id="year" class="form-select" {{ $showYearMonth ? '' : 'disabled' }}>
                        @foreach(($yearOptions ?? []) as $value => $label)
                            <option value="{{ $value }}" {{ (int) ($selectedYear ?? date('Y')) === (int) $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-lg-2 month-filter-field" style="{{ $selectedPeriod === 'monthly' ? '' : 'display:none;' }}">
                    <label for="month" class="form-label">Month</label>
                    <select name="month" id="month" class="form-select" {{ $selectedPeriod === 'monthly' ? '' : 'disabled' }}>
                        @foreach(($monthOptions ?? ['' => 'All months in year']) as $value => $label)
                            <option value="{{ $value }}" {{ (string) ($selectedMonth ?? '') === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-lg-2 custom-range-only-field" style="{{ $selectedPeriod === 'range' ? '' : 'display:none;' }}">
                    <label for="date_from" class="form-label">Date from</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $selectedPeriod === 'range' ? $dateFrom : '' }}" {{ $selectedPeriod === 'range' ? '' : 'disabled' }}>
                </div>
                <div class="col-md-3 col-lg-2 custom-range-only-field" style="{{ $selectedPeriod === 'range' ? '' : 'display:none;' }}">
                    <label for="date_to" class="form-label">Date to</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $selectedPeriod === 'range' ? $dateTo : '' }}" {{ $selectedPeriod === 'range' ? '' : 'disabled' }}>
                </div>
                <div class="col-md-4 col-lg-2">
                    <button type="submit" class="btn btn-warning w-100">
                        <i class="fas fa-sync-alt me-1"></i>Update Report
                    </button>
                </div>
            </form>
            <p class="small text-muted mt-2 mb-0">
                Rankings follow <strong>Sales Agent Commission release dates</strong> (same as
                <a href="{{ route('sales-agent-commissions.index') }}">Sales Agent Commissions</a>),
                plus any Released units in the range that have no commission row yet.
                Credit uses the commission agent / linked executive when available.
                Active date filter: <strong>{{ $activeRangeLabel }}</strong>.
            </p>
        </div>
    </div>

    @php $metricReports = $metricReports ?? []; @endphp

    <ul class="nav nav-tabs mb-3" id="salesExecTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overviewPane" type="button" role="tab">Overview</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="sales-reservation-tab" data-bs-toggle="tab" data-bs-target="#salesReservationPane" type="button" role="tab">Sales &amp; Reservation</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="cash-summary-tab" data-bs-toggle="tab" data-bs-target="#cashSummaryPane" type="button" role="tab">Cash Only</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="financing-summary-tab" data-bs-toggle="tab" data-bs-target="#financingSummaryPane" type="button" role="tab">Financing Only</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="gross-net-tab" data-bs-toggle="tab" data-bs-target="#grossNetPane" type="button" role="tab">Gross &amp; Net</button>
        </li>
    </ul>

    <div class="tab-content" id="salesExecTabContent">
        <div class="tab-pane fade show active" id="overviewPane" role="tabpanel">
    @if(!$hasData)
        <div class="alert alert-info mb-0">
            <i class="fas fa-info-circle me-2"></i>
            No performance data found for <strong>{{ $viewLabel }}</strong> in this date range.
            Try another month (commissions currently peak earlier in the year), switch view, or choose <em>All months in year</em>.
        </div>
    @else
        <div class="row g-3 mb-3">
            @foreach($summary as $card)
                <div class="col-md-6 col-xl-3">
                    <div class="card border-warning h-100">
                        <div class="card-body py-3">
                            <div class="text-muted small mb-1">{{ $card['label'] }}</div>
                            <div class="h5 mb-0">
                                @if(!empty($card['is_text']))
                                    {{ $card['value'] }}
                                @elseif(!empty($card['is_currency']))
                                    ₱{{ number_format((float) $card['value'], 2) }}
                                @else
                                    {{ number_format((float) $card['value'], 0) }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-3 mb-3">
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <strong>{{ $charts['top_units']['title'] ?? 'Top by Units' }}</strong>
                    </div>
                    <div class="card-body">
                        <div class="exec-chart-wrap exec-chart-wrap--tall">
                            <canvas class="sales-exec-chart" data-chart-key="top_units"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <strong>{{ $charts['top_sales']['title'] ?? 'Top by Sales' }}</strong>
                    </div>
                    <div class="card-body">
                        <div class="exec-chart-wrap exec-chart-wrap--tall">
                            <canvas class="sales-exec-chart" data-chart-key="top_sales"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white">
                        <strong>{{ $charts['leader_monthly']['title'] ?? 'Monthly Sales — Top Performer' }}</strong>
                    </div>
                    <div class="card-body">
                        <div class="exec-chart-wrap">
                            <canvas class="sales-exec-chart" data-chart-key="leader_monthly"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                <strong>{{ $viewLabel }} Performance Ranking</strong>
                <span class="badge bg-warning text-dark">{{ count($tables['ranking']) }} ranked</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width:3rem;">#</th>
                                <th>Name</th>
                                <th>Role / Team</th>
                                @if($viewMode === 'executives')
                                    <th class="text-end">Team Size</th>
                                @endif
                                <th class="text-end">Units</th>
                                <th class="text-end">Unit Share</th>
                                <th class="text-end">Sales Amount</th>
                                <th class="text-end">Sales Share</th>
                                <th class="text-end">Avg Sale</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tables['ranking'] as $i => $row)
                                <tr class="{{ $i < 3 ? 'table-warning' : '' }}">
                                    <td>
                                        @if($i === 0)
                                            <span class="badge bg-warning text-dark">1</span>
                                        @elseif($i === 1)
                                            <span class="badge bg-secondary">2</span>
                                        @elseif($i === 2)
                                            <span class="badge bg-dark">3</span>
                                        @else
                                            {{ $i + 1 }}
                                        @endif
                                    </td>
                                    <td class="fw-semibold">{{ $row['name'] }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $row['role'] ?? '—' }}</span>
                                        @if(!empty($row['executive_name']) && $viewMode !== 'executives')
                                            <div class="small text-muted">Exec: {{ $row['executive_name'] }}</div>
                                        @endif
                                    </td>
                                    @if($viewMode === 'executives')
                                        <td class="text-end">{{ $row['team_size'] !== null ? number_format($row['team_size']) : '—' }}</td>
                                    @endif
                                    <td class="text-end">{{ number_format($row['units']) }}</td>
                                    <td class="text-end">{{ number_format($row['share_units'], 1) }}%</td>
                                    <td class="text-end">₱{{ number_format($row['sales'], 2) }}</td>
                                    <td class="text-end">{{ number_format($row['share_sales'], 1) }}%</td>
                                    <td class="text-end">₱{{ number_format($row['avg_sale'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row g-3">
            @if($viewMode === 'team')
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header bg-white"><strong>Sales Agents Snapshot</strong></div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 320px;">
                                <table class="table table-sm table-striped mb-0">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th>Agent</th>
                                            <th class="text-end">Units</th>
                                            <th class="text-end">Sales</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($tables['agents'] as $row)
                                            <tr>
                                                <td>{{ $row['name'] }}</td>
                                                <td class="text-end">{{ number_format($row['units']) }}</td>
                                                <td class="text-end">₱{{ number_format($row['sales'], 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="text-muted text-center py-3">No agent-tagged sales in range</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-header bg-white"><strong>Executives Snapshot</strong></div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 320px;">
                                <table class="table table-sm table-striped mb-0">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th>Executive</th>
                                            <th class="text-end">Units</th>
                                            <th class="text-end">Sales</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($tables['executives'] as $row)
                                            <tr>
                                                <td>{{ $row['name'] }}</td>
                                                <td class="text-end">{{ number_format($row['units']) }}</td>
                                                <td class="text-end">₱{{ number_format($row['sales'], 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="3" class="text-muted text-center py-3">No executive matches in range</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white"><strong>Recent Released Deals</strong></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Plate</th>
                                        <th>Unit</th>
                                        <th>Credited To</th>
                                        <th>Role</th>
                                        <th class="text-end">Sold Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tables['recent_deals'] as $deal)
                                        <tr>
                                            <td>{{ $deal['date'] }}</td>
                                            <td>{{ $deal['plate'] }}</td>
                                            <td>{{ $deal['unit'] }}</td>
                                            <td class="fw-semibold">{{ $deal['name'] }}</td>
                                            <td><span class="badge bg-light text-dark border">{{ $deal['role'] }}</span></td>
                                            <td class="text-end">₱{{ number_format($deal['sales'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-muted text-center py-3">No deals</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
        </div>{{-- overview pane --}}

        @php
            $salesReservation = $metricReports['sales_reservation'] ?? null;
            $cashSummary = $metricReports['cash_summary'] ?? null;
            $financingSummary = $metricReports['financing_summary'] ?? null;
            $grossNet = $metricReports['gross_net'] ?? null;
        @endphp

        <div class="tab-pane fade" id="salesReservationPane" role="tabpanel">
            @include('analytics-reports.partials.sales-exec-metric-table', [
                'report' => $salesReservation,
                'columns' => [
                    ['key' => 'sales_count', 'label' => 'Total Sales', 'format' => 'int'],
                    ['key' => 'sales_pct', 'label' => '% of Total Sales', 'format' => 'pct'],
                    ['key' => 'release_count', 'label' => 'Total Releases', 'format' => 'int'],
                    ['key' => 'release_pct', 'label' => '% of Total Releases', 'format' => 'pct'],
                    ['key' => 'avg_reservation', 'label' => 'Avg Reservation (excl. spot cash)', 'format' => 'money'],
                ],
                'totalKeys' => ['sales_count', 'release_count', 'avg_reservation'],
            ])
        </div>

        <div class="tab-pane fade" id="cashSummaryPane" role="tabpanel">
            @include('analytics-reports.partials.sales-exec-metric-table', [
                'report' => $cashSummary,
                'columns' => [
                    ['key' => 'cash_releases', 'label' => '# of Cash Releases', 'format' => 'int'],
                    ['key' => 'cash_pct', 'label' => '% of Cash Releases', 'format' => 'pct'],
                    ['key' => 'discount_total', 'label' => 'Total Discount Given (₱)', 'format' => 'money'],
                    ['key' => 'discount_avg', 'label' => 'Average Discount Given (₱)', 'format' => 'money'],
                ],
                'totalKeys' => ['cash_releases', 'discount_total', 'discount_avg'],
            ])
        </div>

        <div class="tab-pane fade" id="financingSummaryPane" role="tabpanel">
            @include('analytics-reports.partials.sales-exec-metric-table', [
                'report' => $financingSummary,
                'columns' => [
                    ['key' => 'financing_releases', 'label' => '# of Financing Releases', 'format' => 'int'],
                    ['key' => 'financing_pct', 'label' => '% of Financing Releases', 'format' => 'pct'],
                    ['key' => 'financed_total', 'label' => 'Total Amount Financed (₱)', 'format' => 'money'],
                    ['key' => 'financed_avg', 'label' => 'Average Amount Financed (₱)', 'format' => 'money'],
                ],
                'totalKeys' => ['financing_releases', 'financed_total', 'financed_avg'],
            ])
        </div>

        <div class="tab-pane fade" id="grossNetPane" role="tabpanel">
            @include('analytics-reports.partials.sales-exec-metric-table', [
                'report' => $grossNet,
                'columns' => [
                    ['key' => 'gross_total', 'label' => 'Total Gross Sales (₱)', 'format' => 'money'],
                    ['key' => 'gross_avg', 'label' => 'Average Gross Per Sale (₱)', 'format' => 'money'],
                    ['key' => 'net_total', 'label' => 'Total Net Profit (₱)', 'format' => 'money'],
                    ['key' => 'net_avg', 'label' => 'Average Net Profit Per Sale (₱)', 'format' => 'money'],
                ],
                'totalKeys' => ['gross_total', 'gross_avg', 'net_total', 'net_avg'],
            ])
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.exec-chart-wrap {
    position: relative;
    height: 280px;
}
.exec-chart-wrap--tall {
    height: 360px;
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

    function toggleRangeFields() {
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
    }

    if (period) period.addEventListener('change', toggleRangeFields);
    toggleRangeFields();

    const charts = @json($charts ?? []);
    if (!window.Chart || !charts || typeof charts !== 'object') return;

    const palette = [
        'rgba(255, 193, 7, 0.8)',
        'rgba(13, 110, 253, 0.75)',
        'rgba(25, 135, 84, 0.75)',
        'rgba(220, 53, 69, 0.75)',
        'rgba(111, 66, 193, 0.75)',
        'rgba(13, 202, 240, 0.75)',
        'rgba(108, 117, 125, 0.75)',
        'rgba(253, 126, 20, 0.75)',
        'rgba(32, 201, 151, 0.75)',
        'rgba(214, 51, 132, 0.75)'
    ];

    document.querySelectorAll('.sales-exec-chart').forEach(function (canvasEl) {
        const key = canvasEl.getAttribute('data-chart-key');
        const chartCfg = charts[key];
        if (!chartCfg || !Array.isArray(chartCfg.labels) || chartCfg.labels.length === 0) return;

        const type = chartCfg.type || 'bar';
        const data = chartCfg.data || [];
        const indexAxis = chartCfg.index_axis || 'x';
        const isLine = type === 'line';

        const options = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } }
        };

        if (!isLine) {
            options.indexAxis = indexAxis;
            options.scales = {
                x: { beginAtZero: true, ticks: { precision: 0 } },
                y: { beginAtZero: true, ticks: { precision: 0 } }
            };
        } else {
            options.scales = {
                y: { beginAtZero: true }
            };
        }

        new Chart(canvasEl, {
            type: type,
            data: {
                labels: chartCfg.labels,
                datasets: [{
                    label: chartCfg.dataset_label || 'Value',
                    data: data,
                    backgroundColor: isLine
                        ? 'rgba(255, 193, 7, 0.2)'
                        : data.map((_, i) => palette[i % palette.length]),
                    borderColor: isLine
                        ? 'rgba(255, 193, 7, 1)'
                        : data.map((_, i) => String(palette[i % palette.length]).replace('0.75', '1').replace('0.8', '1')),
                    borderWidth: 1.5,
                    fill: isLine,
                    tension: isLine ? 0.25 : 0
                }]
            },
            options: options
        });
    });
});
</script>
@endsection
