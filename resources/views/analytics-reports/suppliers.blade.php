@extends('layouts.app')

@section('title', 'Suppliers Report - Car Empire Management System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-2">
        <h1 class="h3 mb-0"><i class="fas fa-truck me-2 text-success"></i>Suppliers Report</h1>
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
            <form method="GET" action="{{ route('analytics-report.suppliers') }}" class="row g-2 align-items-end" id="suppliersFilterForm">
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
                    <label for="location" class="form-label">Location</label>
                    <select name="location" id="location" class="form-select">
                        @foreach(($locationOptions ?? ['' => 'All Locations']) as $value => $label)
                            <option value="{{ $value }}" {{ ($selectedLocation ?? '') === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-lg-2">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-sync-alt me-1"></i>Update Report
                    </button>
                </div>
            </form>
            <p class="small text-muted mt-2 mb-0">
                Excel tabular format for supplier sales/releases and release gross/net.
                Active date filter: <strong>{{ $activeRangeLabel }}</strong>
                @if(($selectedLocation ?? '') !== '')
                    · Location: <strong>{{ $selectedLocation }}</strong>
                @else
                    · Location: <strong>All Locations</strong>
                @endif.
                Trade-in sources are grouped as <strong>TRADE-IN</strong>.
            </p>
        </div>
    </div>

    @php
        $speed = $speedReport ?? null;
        $grossNet = $grossNetReport ?? null;
    @endphp

    @if(!$hasData)
        <div class="alert alert-info mb-0">
            <i class="fas fa-info-circle me-2"></i>
            No supplier sales or releases found for this date range. Try another month or widen the period.
        </div>
    @else
        <ul class="nav nav-tabs mb-3" id="supplierTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="speed-tab" data-bs-toggle="tab" data-bs-target="#speedPane" type="button" role="tab">
                    Sales &amp; Releases / Speed
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="gross-net-tab" data-bs-toggle="tab" data-bs-target="#grossNetPane" type="button" role="tab">
                    Gross &amp; Net of Releases
                </button>
            </li>
        </ul>

        <div class="tab-content" id="supplierTabContent">
            <div class="tab-pane fade show active" id="speedPane" role="tabpanel">
                @if(empty($speed) || empty($speed['has_data']))
                    <div class="alert alert-info mb-0">No supplier sales/release rows for this period.</div>
                @else
                    <div class="card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <strong>{{ $speed['title'] }}</strong>
                                <div class="small text-muted">{{ $activeRangeLabel }}</div>
                            </div>
                            <span class="badge text-bg-light border">{{ number_format(count($speed['rows'])) }} suppliers</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:3rem;">Rank</th>
                                            <th>Supplier</th>
                                            <th class="text-end">Total Sales</th>
                                            <th class="text-end">% of Total Sales</th>
                                            <th class="text-end">Total Releases</th>
                                            <th class="text-end">% of Total Releases</th>
                                            <th class="text-end">Average Days to Sell</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($speed['rows'] as $i => $row)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td class="fw-semibold">{{ $row['supplier'] }}</td>
                                                <td class="text-end">{{ number_format((int) $row['sales_count']) }}</td>
                                                <td class="text-end">{{ number_format((float) $row['sales_pct'], 1) }}%</td>
                                                <td class="text-end">{{ number_format((int) $row['release_count']) }}</td>
                                                <td class="text-end">{{ number_format((float) $row['release_pct'], 1) }}%</td>
                                                <td class="text-end">{{ number_format((float) $row['avg_days_to_sell'], 1) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th>—</th>
                                            <th>TOTAL</th>
                                            <th class="text-end">{{ number_format((int) ($speed['totals']['sales_count'] ?? 0)) }}</th>
                                            <th class="text-end">—</th>
                                            <th class="text-end">{{ number_format((int) ($speed['totals']['release_count'] ?? 0)) }}</th>
                                            <th class="text-end">—</th>
                                            <th class="text-end">{{ number_format((float) ($speed['totals']['avg_days_to_sell'] ?? 0), 1) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="tab-pane fade" id="grossNetPane" role="tabpanel">
                @if(empty($grossNet) || empty($grossNet['has_data']))
                    <div class="alert alert-info mb-0">No supplier release gross/net rows for this period.</div>
                @else
                    <div class="card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <strong>{{ $grossNet['title'] }}</strong>
                                <div class="small text-muted">{{ $activeRangeLabel }}</div>
                            </div>
                            <span class="badge text-bg-light border">{{ number_format(count($grossNet['rows'])) }} suppliers</span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-hover mb-0 align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:3rem;">Rank</th>
                                            <th>Supplier</th>
                                            <th class="text-end">Total Gross Sales Release (PHP)</th>
                                            <th class="text-end">Average Gross Per Release (PHP)</th>
                                            <th class="text-end">Total Net Profit Releases (PHP)</th>
                                            <th class="text-end">Average Net Profit Per Release (PHP)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($grossNet['rows'] as $i => $row)
                                            <tr>
                                                <td>{{ $i + 1 }}</td>
                                                <td class="fw-semibold">{{ $row['supplier'] }}</td>
                                                <td class="text-end">₱{{ number_format((float) $row['total_gross'], 2) }}</td>
                                                <td class="text-end">₱{{ number_format((float) $row['avg_gross'], 2) }}</td>
                                                <td class="text-end {{ (float) $row['total_net'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                    ₱{{ number_format((float) $row['total_net'], 2) }}
                                                </td>
                                                <td class="text-end {{ (float) $row['avg_net'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                    ₱{{ number_format((float) $row['avg_net'], 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th>—</th>
                                            <th>TOTAL</th>
                                            <th class="text-end">₱{{ number_format((float) ($grossNet['totals']['total_gross'] ?? 0), 2) }}</th>
                                            <th class="text-end">₱{{ number_format((float) ($grossNet['totals']['avg_gross'] ?? 0), 2) }}</th>
                                            <th class="text-end">₱{{ number_format((float) ($grossNet['totals']['total_net'] ?? 0), 2) }}</th>
                                            <th class="text-end">₱{{ number_format((float) ($grossNet['totals']['avg_net'] ?? 0), 2) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
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
});
</script>
@endsection
