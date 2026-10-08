@extends('layouts.app')

@section('title', 'Current Inventory - Car Empire Management System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-2">
        <h1 class="h3 mb-0"><i class="fas fa-warehouse me-2 text-success"></i>Current Inventory</h1>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
            <i class="fas fa-home me-1"></i>Back to Home
        </a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('analytics-report.current-inventory') }}" class="row g-2 align-items-end">
                <input type="hidden" name="run" value="1">
                <input type="hidden" name="excel_tab" id="excelTab" value="{{ $activeExcelTab ?? 'by_model' }}">
                <div class="col-md-3 col-lg-2">
                    <label for="year" class="form-label">Year</label>
                    <select name="year" id="year" class="form-select">
                        @foreach(($yearOptions ?? []) as $value => $label)
                            <option value="{{ $value }}" {{ (int) ($selectedYear ?? date('Y')) === (int) $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-lg-2">
                    <label for="month" class="form-label">Month</label>
                    <select name="month" id="month" class="form-select">
                        @foreach(($monthOptions ?? ['' => 'All months (as of today)']) as $value => $label)
                            <option value="{{ $value }}" {{ (string) ($selectedMonth ?? '') === (string) $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
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
                Matches Excel <strong>(20-27) INVENTORY REPORTS</strong>.
                As of: <strong>{{ $activeRangeLabel }}</strong>
                · Sold window (last 3 months ending as-of): <strong>{{ $soldWindowLabel }}</strong>
                @if(($selectedLocation ?? '') !== '')
                    · Location: <strong>{{ $selectedLocation }}</strong>
                @else
                    · Location: <strong>All Locations</strong>
                @endif.
                Stock = units on hand as of that date (acquired on/before, not yet released/sold). Potential profit = posted − (purchase + repairs + agent + transfer). Zero rows are hidden.
            </p>
        </div>
    </div>

    @if(!($showResults ?? false))
        <div class="alert alert-light border mb-0">
            <i class="fas fa-filter me-2 text-muted"></i>
            Select filters and click <strong>Update Report</strong> to load inventory tables.
        </div>
    @elseif(!($hasData ?? false))
        <div class="alert alert-info mb-0">
            <i class="fas fa-info-circle me-2"></i>
            No inventory or recent sales rows found for these filters.
        </div>
    @else
        @php
            $tabDefs = [
                'by_model' => ['label' => 'By Model', 'report' => $byModelReport ?? null],
                'by_make' => ['label' => 'By Make', 'report' => $byMakeReport ?? null],
                'by_body_type' => ['label' => 'By Body Type', 'report' => $byBodyTypeReport ?? null],
                'by_year_model' => ['label' => 'By Year Model', 'report' => $byYearModelReport ?? null],
                'by_fuel_type' => ['label' => 'By Fuel Type', 'report' => $byFuelTypeReport ?? null],
                'by_transmission' => ['label' => 'By Transmission', 'report' => $byTransmissionReport ?? null],
                'by_supplier' => ['label' => 'By Supplier', 'report' => $bySupplierReport ?? null],
                'by_age' => ['label' => 'By Car Age', 'report' => $byAgeReport ?? null],
            ];
            $activeTab = $activeExcelTab ?? 'by_model';
            if (! array_key_exists($activeTab, $tabDefs)) {
                $activeTab = 'by_model';
            }
        @endphp

        <ul class="nav nav-tabs flex-wrap" id="inventoryTabs" role="tablist">
            @foreach($tabDefs as $tabKey => $tabMeta)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === $tabKey ? 'active' : '' }}"
                            id="tab-{{ $tabKey }}-btn"
                            data-bs-toggle="tab"
                            data-bs-target="#tab-{{ $tabKey }}"
                            data-excel-tab="{{ $tabKey }}"
                            type="button"
                            role="tab">
                        {{ $tabMeta['label'] }}
                    </button>
                </li>
            @endforeach
        </ul>

        <div class="tab-content border border-top-0 bg-white" id="inventoryTabContent">
            @foreach($tabDefs as $tabKey => $tabMeta)
                @php $report = $tabMeta['report']; @endphp
                <div class="tab-pane fade {{ $activeTab === $tabKey ? 'show active' : '' }}" id="tab-{{ $tabKey }}" role="tabpanel">
                    @if(empty($report['has_data']))
                        <div class="p-4 text-muted">No rows for this inventory table.</div>
                    @else
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 px-3 pt-3">
                            <div>
                                <strong>{{ $report['title'] }}</strong>
                                <div class="small text-muted">{{ $activeRangeLabel }}</div>
                            </div>
                            <span class="badge text-bg-light border">{{ number_format(count($report['rows'])) }} groups</span>
                        </div>
                        <div class="table-responsive mt-2">
                            <table class="table table-sm table-striped table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:3rem;">Rank</th>
                                        <th>{{ $report['dimension_label'] ?? 'Label' }}</th>
                                        <th class="text-end">Current Stock</th>
                                        <th class="text-end">% of Total Stock</th>
                                        <th class="text-end"># Sold Last 3 Months</th>
                                        <th class="text-end">Avg Speed to Sell (Days)</th>
                                        <th class="text-end">Avg Potential Profit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($report['rows'] as $row)
                                        <tr>
                                            <td>{{ $row['rank'] }}</td>
                                            <td class="fw-semibold">{{ $row['label'] }}</td>
                                            <td class="text-end">{{ number_format((int) $row['stock_count']) }}</td>
                                            <td class="text-end">{{ number_format((float) $row['stock_pct'], 1) }}%</td>
                                            <td class="text-end">{{ number_format((int) $row['sold_last_3_months']) }}</td>
                                            <td class="text-end">{{ number_format((float) $row['avg_speed_days'], 1) }}</td>
                                            <td class="text-end {{ (float) $row['avg_potential_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                                ₱{{ number_format((float) $row['avg_potential_profit'], 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th>—</th>
                                        <th>TOTAL</th>
                                        <th class="text-end">{{ number_format((int) ($report['totals']['stock_count'] ?? 0)) }}</th>
                                        <th class="text-end">{{ number_format((float) ($report['totals']['stock_pct'] ?? 0), 1) }}%</th>
                                        <th class="text-end">{{ number_format((int) ($report['totals']['sold_last_3_months'] ?? 0)) }}</th>
                                        <th class="text-end">{{ number_format((float) ($report['totals']['avg_speed_days'] ?? 0), 1) }}</th>
                                        <th class="text-end">₱{{ number_format((float) ($report['totals']['avg_potential_profit'] ?? 0), 2) }}</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const excelTabInput = document.getElementById('excelTab');
    document.querySelectorAll('#inventoryTabs [data-excel-tab]').forEach(btn => {
        btn.addEventListener('shown.bs.tab', function () {
            if (excelTabInput) {
                excelTabInput.value = btn.getAttribute('data-excel-tab') || 'by_model';
            }
        });
    });
});
</script>
@endsection
