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
                Rankings use the same released vehicles as the <strong>Car Sales Report</strong>. Each car is credited to its sales agent when it has a commission in this range, otherwise to the sales person on the unit. Active date filter: <strong>{{ $activeRangeLabel }}</strong>.
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
            Try another month, switch view, or choose <em>All months in year</em>.
        </div>
    @else
        <div class="row g-3 mb-3">
            @foreach($summary as $card)
                @php
                    $opensTopModal = !empty($card['is_clickable']) && !empty($card['units']);
                    $opensDetail = !$opensTopModal && !empty($card['detail']);
                @endphp
                <div class="col-md-6 col-xl-3">
                    <div class="card border-warning h-100 {{ ($opensTopModal || $opensDetail) ? 'sales-exec-card' : '' }}"
                        @if($opensDetail)
                            role="button" tabindex="0"
                            data-bs-toggle="modal" data-bs-target="#salesExecCardModal"
                            data-detail-title="{{ $card['detail']['title'] ?? $card['label'] }}"
                            data-detail-body="salesExecCardBody{{ $loop->index }}"
                        @elseif($opensTopModal)
                            role="button" tabindex="0"
                            data-open-top-performer="1"
                        @endif
                    >
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="text-muted small mb-1">{{ $card['label'] }}</div>
                                @if($opensTopModal || $opensDetail)
                                    <span class="badge text-bg-light border text-muted small fw-normal"><i class="fas fa-info-circle me-1"></i>Details</span>
                                @endif
                            </div>
                            <div class="h5 mb-0">
                                @if($opensTopModal)
                                    {{ $card['value'] }}
                                    <span class="d-block small fw-normal text-muted mt-1">Click to view released units</span>
                                @elseif(!empty($card['is_text']))
                                    {{ $card['value'] }}
                                @elseif(!empty($card['is_currency']))
                                    ₱{{ number_format((float) $card['value'], 2) }}
                                @else
                                    {{ number_format((float) $card['value'], 0) }}
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($opensDetail)
                        @php $detail = $card['detail']; @endphp
                        <div id="salesExecCardBody{{ $loop->index }}" class="d-none">
                            @if(($detail['type'] ?? '') === 'units')
                                <div class="sales-exec-detail-list">
                                    <table class="table table-sm table-striped table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-end">#</th><th>Plate</th><th>Unit</th><th class="text-nowrap">Release Date</th>
                                                <th>Credited To</th><th>Role</th><th>Payment</th><th class="text-end text-nowrap">Sold Price</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($detail['units'] as $u)
                                                <tr class="sales-exec-detail-row">
                                                    <td class="text-end text-muted">{{ $loop->iteration }}</td>
                                                    <td class="text-nowrap">@if(!empty($u['url']))<a href="{{ $u['url'] }}" target="_blank" rel="noopener">{{ $u['plate'] }}</a>@else{{ $u['plate'] }}@endif</td>
                                                    <td>{{ $u['unit'] }}</td>
                                                    <td class="text-nowrap">{{ $u['release_date'] }}</td>
                                                    <td><a href="#" class="sales-exec-person-link text-decoration-none" data-person="{{ $u['person_ref'] ?? '' }}">{{ $u['credited_to'] }}</a></td>
                                                    <td><span class="badge bg-light text-dark border">{{ $u['role'] }}</span></td>
                                                    <td>{{ $u['payment'] }}</td>
                                                    <td class="text-end text-nowrap">₱{{ number_format((float) $u['sales'], 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-light fw-bold">
                                            <tr>
                                                <td colspan="7">Total ({{ number_format(count($detail['units'])) }} cars)</td>
                                                <td class="text-end text-nowrap">₱{{ number_format((float) collect($detail['units'])->sum('sales'), 2) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                @if(($detail['type'] ?? '') === 'sales')
                                    <div class="row g-2 mb-3">
                                        @foreach($detail['payment_split'] as $payment => $split)
                                            @continue($payment === '—' && $split['units'] === 0)
                                            <div class="col-sm-4">
                                                <div class="border rounded p-2 h-100">
                                                    <div class="small text-muted">{{ $payment === '—' ? 'Payment not recorded' : $payment }}</div>
                                                    <div class="fw-bold">₱{{ number_format($split['sales'], 2) }}</div>
                                                    <div class="small text-muted">{{ number_format($split['units']) }} cars</div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="sales-exec-detail-list">
                                    <table class="table table-sm table-striped align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-end">#</th><th>Name</th><th>Role</th>
                                                <th class="text-end">Units</th><th class="text-end">Units Share</th>
                                                <th class="text-end text-nowrap">Sales Amount</th><th class="text-end">Sales Share</th>
                                                <th class="text-end text-nowrap">Avg Sale</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($detail['ranking'] as $r)
                                                <tr class="sales-exec-detail-row sales-exec-person-row" title="Click to view this person's cars">
                                                    <td class="text-end text-muted">{{ $loop->iteration }}</td>
                                                    <td class="fw-semibold"><a href="#" class="sales-exec-person-link text-decoration-none" data-person="{{ $r['person_ref'] ?? '' }}">{{ $r['name'] }}</a></td>
                                                    <td><span class="badge bg-light text-dark border">{{ $r['role'] ?? '—' }}</span></td>
                                                    <td class="text-end">{{ number_format((int) $r['units']) }}</td>
                                                    <td class="text-end">{{ number_format((float) ($r['share_units'] ?? 0), 1) }}%</td>
                                                    <td class="text-end text-nowrap">₱{{ number_format((float) $r['sales'], 2) }}</td>
                                                    <td class="text-end">{{ number_format((float) ($r['share_sales'] ?? 0), 1) }}%</td>
                                                    <td class="text-end text-nowrap">₱{{ number_format((float) ($r['avg_sale'] ?? 0), 2) }}</td>
                                                </tr>
                                            @endforeach
                                            @if(($detail['unranked_units'] ?? 0) > 0)
                                                <tr class="text-muted fst-italic">
                                                    <td></td><td colspan="2">Not in this ranking (no linked {{ strtolower($viewLabel) }})</td>
                                                    <td class="text-end">{{ number_format($detail['unranked_units']) }}</td><td></td>
                                                    <td class="text-end text-nowrap">₱{{ number_format($detail['unranked_sales'], 2) }}</td><td></td><td></td>
                                                </tr>
                                            @endif
                                        </tbody>
                                        <tfoot class="table-light fw-bold">
                                            <tr>
                                                <td colspan="3">Total</td>
                                                <td class="text-end">{{ number_format(collect($detail['ranking'])->sum('units') + ($detail['unranked_units'] ?? 0)) }}</td><td></td>
                                                <td class="text-end text-nowrap">₱{{ number_format(collect($detail['ranking'])->sum('sales') + ($detail['unranked_sales'] ?? 0), 2) }}</td><td></td><td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @endif
                    @if($opensTopModal)
                        <button type="button" class="d-none sales-exec-top-performer-open"
                                data-bs-toggle="modal"
                                data-bs-target="#salesExecTopPerformerModal"
                                data-title="{{ $card['modal_title'] ?? ($card['value'] ?? 'Top Performer') }}"
                                data-units='@json($card['units'])'></button>
                    @endif
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
                                <tr class="sales-exec-person-row {{ $i < 3 ? 'table-warning' : '' }}" title="Click to view this person's cars">
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
                                    <td class="fw-semibold">
                                        <a href="#" class="sales-exec-person-link text-decoration-none" data-person="{{ $row['person_ref'] ?? '' }}">{{ $row['name'] }}</a>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $row['role'] ?? '—' }}</span>
                                        @if(!empty($row['executive_name']) && $viewMode !== 'executives')
                                            <div class="small text-muted">
                                                Exec:
                                                @foreach(array_filter(array_map('trim', explode(',', (string) $row['executive_name']))) as $execName)
                                                    <a href="#" class="sales-exec-person-link text-decoration-none" data-person="executives:{{ preg_replace('/\s+/', ' ', strtoupper($execName)) }}">{{ $execName }}</a>@if(!$loop->last), @endif
                                                @endforeach
                                            </div>
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
                                            <tr class="sales-exec-person-row" title="Click to view this person's cars">
                                                <td>
                                                    <span class="fw-semibold"><a href="#" class="sales-exec-person-link text-decoration-none" data-person="{{ $row['person_ref'] ?? '' }}">{{ $row['name'] }}</a></span>
                                                </td>
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
                                            <tr class="sales-exec-person-row" title="Click to view this person's cars">
                                                <td>
                                                    <span class="fw-semibold"><a href="#" class="sales-exec-person-link text-decoration-none" data-person="{{ $row['person_ref'] ?? '' }}">{{ $row['name'] }}</a></span>
                                                </td>
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
                                            <td class="fw-semibold">
                                                <a href="#" class="sales-exec-person-link text-decoration-none" data-person="{{ $deal['person_ref'] ?? '' }}">{{ $deal['name'] }}</a>
                                            </td>
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
                'report' => $salesReservation, 'context' => 'sales',
                'columns' => [
                    ['key' => 'sales_count', 'label' => 'Total Sales', 'format' => 'int', 'group' => 'sales'],
                    ['key' => 'sales_pct', 'label' => '% of Total Sales', 'format' => 'pct', 'group' => 'sales'],
                    ['key' => 'release_count', 'label' => 'Total Releases', 'format' => 'int', 'group' => 'releases'],
                    ['key' => 'release_pct', 'label' => '% of Total Releases', 'format' => 'pct', 'group' => 'releases'],
                    ['key' => 'avg_reservation', 'label' => 'Avg Reservation (excl. spot cash)', 'format' => 'money'],
                ],
                'totalKeys' => ['sales_count', 'release_count', 'avg_reservation'],
                'groups' => [
                    'sales' => ['label' => 'Sales', 'hint' => 'Sale is based on the date the car was initially reserved/sold.', 'tone' => 'blue'],
                    'releases' => ['label' => 'Releases', 'hint' => 'Release is based on the date the car was released.', 'tone' => 'green'],
                ],
            ])
        </div>

        <div class="tab-pane fade" id="cashSummaryPane" role="tabpanel">
            @include('analytics-reports.partials.sales-exec-metric-table', [
                'report' => $cashSummary, 'context' => 'cash',
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
                'report' => $financingSummary, 'context' => 'financing',
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
                'report' => $grossNet, 'context' => 'gross',
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

<div class="modal fade" id="salesExecPersonModal" tabindex="-1" aria-labelledby="salesExecPersonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="salesExecPersonModalLabel">Sales</h5>
                <a href="#" id="salesExecPersonProfile" class="btn btn-sm btn-outline-primary ms-auto me-2 d-none" target="_blank" rel="noopener">
                    <i class="fas fa-user me-1"></i>View profile
                </a>
                <button type="button" class="btn-close ms-0" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted mb-3">Range: <strong>{{ $activeRangeLabel }}</strong> · View: <strong>{{ $viewLabel }}</strong></p>
                <div id="salesExecPersonContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script type="application/json" id="salesExecPersonData">@json($personSales ?? ['released' => [], 'sold' => [], 'people' => []])</script>

<div class="modal fade" id="salesExecCardModal" tabindex="-1" aria-labelledby="salesExecCardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="salesExecCardModalLabel">Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted mb-3">Range: <strong>{{ $activeRangeLabel }}</strong> · View: <strong>{{ $viewLabel }}</strong></p>
                <div id="salesExecCardModalContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="salesExecTopPerformerModal" tabindex="-1" aria-labelledby="salesExecTopPerformerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="salesExecTopPerformerModalLabel">Top Performer — released units</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted mb-3" id="salesExecTopPerformerModalSubtitle"></p>
                <div class="table-responsive">
                    <table class="table table-sm table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:3rem;">#</th>
                                <th>Plate</th>
                                <th>Unit</th>
                                <th>Release Date</th>
                                <th>Credited To</th>
                                <th>Role</th>
                                <th class="text-end">Sales</th>
                            </tr>
                        </thead>
                        <tbody id="salesExecTopPerformerModalBody">
                            <tr><td colspan="7" class="text-muted">No units.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
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
.sales-exec-person-row {
    cursor: pointer;
}
.sales-exec-person-row:hover > td {
    --bs-table-bg-state: rgba(13, 110, 253, .08);
}
.sales-exec-card {
    cursor: pointer;
    transition: box-shadow .15s ease, transform .15s ease;
}
.sales-exec-card:hover,
.sales-exec-card:focus {
    box-shadow: 0 .25rem .75rem rgba(0, 0, 0, .12);
    transform: translateY(-1px);
    outline: none;
}
.sales-exec-detail-list {
    max-height: 60vh;
    overflow: auto;
    border: 1px solid #dee2e6;
    border-radius: .375rem;
}
.sales-exec-detail-list thead th { position: sticky; top: 0; z-index: 1; }
.sales-exec-detail-list tfoot td { position: sticky; bottom: 0; z-index: 1; }
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Person popup: every salesperson name opens the cars credited to them.
    let personData = { released: [], sold: [], people: {} };
    try {
        personData = JSON.parse(document.getElementById('salesExecPersonData').textContent || '{}');
    } catch (e) { /* keep empty */ }
    const personModalEl = document.getElementById('salesExecPersonModal');
    const pesc = v => String(v == null ? '' : v).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    const pmoney = v => '₱' + Number(v || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const plate = r => r.url ? '<a href="' + pesc(r.url) + '" target="_blank" rel="noopener">' + pesc(r.plate) + '</a>' : pesc(r.plate);
    function personTable(title, hint, tone, head, rows, cells, footCells) {
        let html = '<div class="mb-4"><div class="d-flex align-items-baseline gap-2 mb-1"><h6 class="mb-0 text-' + tone + '">' + title + ' (' + rows.length + ')</h6><span class="small text-muted">' + hint + '</span></div>';
        if (!rows.length) return html + '<p class="text-muted small mb-0">None in this range.</p></div>';
        html += '<div class="sales-exec-detail-list border-' + tone + '"><table class="table table-sm table-striped table-hover align-middle mb-0"><thead class="table-light"><tr><th class="text-end">#</th>' + head + '</tr></thead><tbody>';
        rows.forEach((r, i) => { html += '<tr><td class="text-end text-muted">' + (i + 1) + '</td>' + cells(r) + '</tr>'; });
        return html + '</tbody><tfoot class="table-light fw-bold"><tr>' + footCells + '</tr></tfoot></table></div></div>';
    }
    function showPerson(ref, fallbackName, context) {
        const p = (personData.people || {})[ref];
        const allReleased = p ? p.released.map(i => personData.released[i]).filter(Boolean) : [];
        const sold = p ? p.sold.map(i => personData.sold[i]).filter(Boolean) : [];
        const name = p ? p.name : fallbackName;
        const titles = { cash: 'cash releases', financing: 'financing releases', gross: 'gross & net' };
        document.getElementById('salesExecPersonModalLabel').textContent = name + ' — ' + (titles[context] || 'sales generated');
        const profile = document.getElementById('salesExecPersonProfile');
        if (p && p.profile_url) { profile.href = p.profile_url; profile.classList.remove('d-none'); } else { profile.classList.add('d-none'); }
        const sum = (list, key) => list.reduce((t, r) => t + Number(r[key] || 0), 0);
        const base = r => '<td class="text-nowrap">' + plate(r) + '</td><td>' + pesc(r.unit) + '</td><td class="text-nowrap">' + pesc(r.date) + '</td>';
        const m = v => '<td class="text-end text-nowrap">' + pmoney(v) + '</td>';
        let html = '';
        if (context === 'cash') {
            const list = allReleased.filter(r => r.is_cash);
            html = personTable('Cash releases', 'Released cars paid in cash. Discount = posted price − sold price.', 'success',
                '<th>Plate</th><th>Unit</th><th class="text-nowrap">Release Date</th><th class="text-end text-nowrap">Posted Price</th><th class="text-end text-nowrap">Sold Price</th><th class="text-end">Discount</th>',
                list, r => base(r) + m(r.posted) + m(r.sold) + m(r.discount),
                '<td colspan="4">Total</td>' + m(sum(list, 'posted')) + m(sum(list, 'sold')) + m(sum(list, 'discount')));
        } else if (context === 'financing') {
            const list = allReleased.filter(r => r.is_financing);
            html = personTable('Financing releases', 'Released cars on financing. Amount financed = sold price − reservation.', 'success',
                '<th>Plate</th><th>Unit</th><th class="text-nowrap">Release Date</th><th class="text-end text-nowrap">Sold Price</th><th class="text-end">Reservation</th><th class="text-end text-nowrap">Amount Financed</th>',
                list, r => base(r) + m(r.sold) + m(r.reservation) + m(r.financed),
                '<td colspan="4">Total</td>' + m(sum(list, 'sold')) + m(sum(list, 'reservation')) + m(sum(list, 'financed')));
        } else if (context === 'gross') {
            html = personTable('Released cars', 'Gross = sold price + finance revenue. Net = gross − costs.', 'success',
                '<th>Plate</th><th>Unit</th><th class="text-nowrap">Release Date</th><th class="text-end text-nowrap">Sold Price</th><th class="text-end">Gross</th><th class="text-end">Net</th>',
                allReleased, r => base(r) + m(r.sold) + m(r.gross) + '<td class="text-end text-nowrap' + (r.net < 0 ? ' text-danger' : '') + '">' + pmoney(r.net) + '</td>',
                '<td colspan="4">Total</td>' + m(sum(allReleased, 'sold')) + m(sum(allReleased, 'gross')) + m(sum(allReleased, 'net')));
        }
        if (context === 'cash' || context === 'financing' || context === 'gross') {
            document.getElementById('salesExecPersonContent').innerHTML = html;
            bootstrap.Modal.getOrCreateInstance(personModalEl).show();
            return;
        }
        const released = allReleased;
        html = personTable('Released cars', 'Release is based on the date the car was released.', 'success',
            '<th>Plate</th><th>Unit</th><th class="text-nowrap">Release Date</th><th>Payment</th><th class="text-end text-nowrap">Sold Price</th><th class="text-end">Gross</th><th class="text-end">Net</th>',
            released, r => '<td class="text-nowrap">' + plate(r) + '</td><td>' + pesc(r.unit) + '</td><td class="text-nowrap">' + pesc(r.date) + '</td><td>' + pesc(r.payment) + '</td><td class="text-end text-nowrap">' + pmoney(r.sold) + '</td><td class="text-end text-nowrap">' + pmoney(r.gross) + '</td><td class="text-end text-nowrap' + (r.net < 0 ? ' text-danger' : '') + '">' + pmoney(r.net) + '</td>',
            '<td colspan="5">Total</td><td class="text-end text-nowrap">' + pmoney(sum(released, 'sold')) + '</td><td class="text-end text-nowrap">' + pmoney(sum(released, 'gross')) + '</td><td class="text-end text-nowrap">' + pmoney(sum(released, 'net')) + '</td>');
        html += personTable('Sold / Reserved cars', 'Sale is based on the date the car was initially reserved/sold.', 'primary',
            '<th>Plate</th><th>Unit</th><th class="text-nowrap">Sale Date</th><th>Status</th><th>Payment</th><th class="text-end">Reservation</th><th class="text-end text-nowrap">Sold Price</th>',
            sold, r => '<td class="text-nowrap">' + plate(r) + '</td><td>' + pesc(r.unit) + '</td><td class="text-nowrap">' + pesc(r.date) + '</td><td><span class="badge bg-light text-dark border">' + pesc(r.status) + '</span></td><td>' + pesc(r.payment) + '</td><td class="text-end text-nowrap">' + pmoney(r.reservation) + '</td><td class="text-end text-nowrap">' + pmoney(r.sold) + '</td>',
            '<td colspan="6">Total</td><td class="text-end text-nowrap">' + pmoney(sum(sold, 'reservation')) + '</td><td class="text-end text-nowrap">' + pmoney(sum(sold, 'sold')) + '</td>');
        document.getElementById('salesExecPersonContent').innerHTML = html;
        bootstrap.Modal.getOrCreateInstance(personModalEl).show();
    }
    document.addEventListener('click', function (event) {
        let link = event.target.closest('.sales-exec-person-link');
        if (!link) {
            // Anywhere else on a person row opens that row's person, unless another link was clicked.
            if (event.target.closest('a, button, input, select, textarea')) return;
            const row = event.target.closest('.sales-exec-person-row');
            link = row ? row.querySelector('.sales-exec-person-link') : null;
            if (!link) return;
        }
        event.preventDefault();
        event.stopPropagation();
        const ref = link.getAttribute('data-person') || '';
        const context = link.getAttribute('data-context') || '';
        const name = link.textContent.trim();
        const open = document.querySelector('.modal.show');
        if (open && open !== personModalEl) {
            open.addEventListener('hidden.bs.modal', () => showPerson(ref, name, context), { once: true });
            bootstrap.Modal.getInstance(open).hide();
        } else {
            showPerson(ref, name, context);
        }
    });

    const cardModal = document.getElementById('salesExecCardModal');
    if (cardModal) {
        cardModal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget ? event.relatedTarget.closest('.sales-exec-card') : null;
            if (!trigger) return;
            const source = document.getElementById(trigger.getAttribute('data-detail-body') || '');
            document.getElementById('salesExecCardModalLabel').textContent = trigger.getAttribute('data-detail-title') || 'Details';
            document.getElementById('salesExecCardModalContent').innerHTML = source ? source.innerHTML : '<p class="text-muted mb-0">No details available.</p>';
        });
    }
    document.querySelectorAll('.sales-exec-card').forEach(function (card) {
        if (card.getAttribute('data-open-top-performer')) {
            card.addEventListener('click', function () {
                const hidden = card.parentElement.querySelector('.sales-exec-top-performer-open');
                if (hidden) hidden.click();
            });
        }
        card.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                card.click();
            }
        });
    });

    const topModal = document.getElementById('salesExecTopPerformerModal');
    if (topModal) {
        topModal.addEventListener('show.bs.modal', function (event) {
            const trigger = event.relatedTarget;
            if (!trigger) return;

            const title = trigger.getAttribute('data-title') || 'Top Performer — released units';
            let units = [];
            try {
                units = JSON.parse(trigger.getAttribute('data-units') || '[]');
            } catch (e) {
                units = [];
            }
            if (!Array.isArray(units)) units = [];

            const labelEl = document.getElementById('salesExecTopPerformerModalLabel');
            const subtitleEl = document.getElementById('salesExecTopPerformerModalSubtitle');
            const bodyEl = document.getElementById('salesExecTopPerformerModalBody');
            if (labelEl) labelEl.textContent = title;
            if (subtitleEl) {
                subtitleEl.textContent = units.length + ' released unit(s) credited to this top performer in the selected range.';
            }
            if (!bodyEl) return;

            const esc = function (value) {
                return String(value == null ? '' : value)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#39;');
            };
            const money = function (value) {
                const n = Number(value || 0);
                return '₱' + n.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            };

            if (units.length === 0) {
                bodyEl.innerHTML = '<tr><td colspan="7" class="text-muted">No units found.</td></tr>';
                return;
            }

            bodyEl.innerHTML = units.map(function (unit, index) {
                const plate = esc(unit.plate || '—');
                const unitName = esc(unit.unit || '—');
                const releaseDate = esc(unit.release_date || '—');
                const credited = esc(unit.credited_to || '—');
                const role = esc(unit.role || '—');
                const sales = money(unit.sales);
                const plateCell = unit.url
                    ? '<a href="' + esc(unit.url) + '" class="fw-semibold text-decoration-none">' + plate + '</a>'
                    : plate;

                return '<tr>'
                    + '<td>' + (index + 1) + '</td>'
                    + '<td>' + plateCell + '</td>'
                    + '<td>' + unitName + '</td>'
                    + '<td>' + releaseDate + '</td>'
                    + '<td>' + credited + '</td>'
                    + '<td>' + role + '</td>'
                    + '<td class="text-end">' + sales + '</td>'
                    + '</tr>';
            }).join('');
        });
    }

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
