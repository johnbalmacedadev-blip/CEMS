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
                                        @php
                                            $stockCount = (int) ($row['stock_count'] ?? 0);
                                            $stockUnits = $row['stock_units'] ?? [];
                                            $canOpenStock = $stockCount > 0 && is_array($stockUnits) && count($stockUnits) > 0;
                                        @endphp
                                        <tr>
                                            <td>{{ $row['rank'] }}</td>
                                            <td class="fw-semibold">
                                                @if($canOpenStock)
                                                    <button type="button"
                                                            class="btn btn-link text-decoration-none p-0 fw-semibold inventory-stock-open"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#inventoryStockModal"
                                                            data-title="{{ $row['label'] }}"
                                                            data-dimension="{{ $report['dimension_label'] ?? 'Group' }}"
                                                            data-units='@json($stockUnits)'>
                                                        {{ $row['label'] }}
                                                    </button>
                                                @else
                                                    {{ $row['label'] }}
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if($canOpenStock)
                                                    <button type="button"
                                                            class="btn btn-link text-decoration-none p-0 inventory-stock-open"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#inventoryStockModal"
                                                            data-title="{{ $row['label'] }}"
                                                            data-dimension="{{ $report['dimension_label'] ?? 'Group' }}"
                                                            data-units='@json($stockUnits)'>
                                                        {{ number_format($stockCount) }}
                                                    </button>
                                                @else
                                                    {{ number_format($stockCount) }}
                                                @endif
                                            </td>
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

<div class="modal fade" id="inventoryStockModal" tabindex="-1" aria-labelledby="inventoryStockModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inventoryStockModalLabel">Current stock units</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted mb-3" id="inventoryStockModalSubtitle"></p>
                <div class="table-responsive">
                    <table class="table table-sm table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:3rem;">#</th>
                                <th>Plate</th>
                                <th>Unit</th>
                                <th>Year</th>
                                <th>Status</th>
                                <th>Location</th>
                                <th>Purchase</th>
                            </tr>
                        </thead>
                        <tbody id="inventoryStockModalBody">
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

    const modalEl = document.getElementById('inventoryStockModal');
    if (!modalEl) return;

    modalEl.addEventListener('show.bs.modal', function (event) {
        const trigger = event.relatedTarget;
        if (!trigger) return;

        const title = trigger.getAttribute('data-title') || 'Group';
        const dimension = trigger.getAttribute('data-dimension') || 'Group';
        let units = [];
        try {
            units = JSON.parse(trigger.getAttribute('data-units') || '[]');
        } catch (e) {
            units = [];
        }
        if (!Array.isArray(units)) units = [];

        const labelEl = document.getElementById('inventoryStockModalLabel');
        const subtitleEl = document.getElementById('inventoryStockModalSubtitle');
        const bodyEl = document.getElementById('inventoryStockModalBody');
        if (labelEl) {
            labelEl.textContent = title + ' — current stock';
        }
        if (subtitleEl) {
            subtitleEl.textContent = units.length + ' unit(s) in stock for this ' + dimension.toLowerCase() + '.';
        }
        if (!bodyEl) return;

        if (units.length === 0) {
            bodyEl.innerHTML = '<tr><td colspan="7" class="text-muted">No units found.</td></tr>';
            return;
        }

        const esc = function (value) {
            return String(value == null ? '' : value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        };

        bodyEl.innerHTML = units.map(function (unit, index) {
            const plate = esc(unit.plate || '—');
            const name = esc(unit.name || 'Unknown unit');
            const year = esc(unit.year || '—');
            const status = esc(unit.status || '—');
            const location = esc(unit.location || '—');
            const purchase = esc(unit.purchase_date || '—');
            const url = esc(unit.url || '#');
            const plateCell = unit.url
                ? '<a href="' + url + '" class="fw-semibold text-decoration-none">' + plate + '</a>'
                : plate;

            return '<tr>'
                + '<td>' + (index + 1) + '</td>'
                + '<td>' + plateCell + '</td>'
                + '<td>' + name + '</td>'
                + '<td>' + year + '</td>'
                + '<td>' + status + '</td>'
                + '<td>' + location + '</td>'
                + '<td>' + purchase + '</td>'
                + '</tr>';
        }).join('');
    });
});
</script>
@endsection
