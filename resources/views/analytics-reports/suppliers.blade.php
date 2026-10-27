@extends('layouts.app')

@section('title', 'Supplier Reports - Car Empire Management System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-2">
        <h1 class="h3 mb-0"><i class="fas fa-truck me-2 text-success"></i>Supplier Reports</h1>
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
                Matches Excel <strong>(10-11) SUPPLIER REPORTS</strong>.
                Active date filter: <strong>{{ $activeRangeLabel }}</strong>
                @if(($selectedLocation ?? '') !== '')
                    · Location: <strong>{{ $selectedLocation }}</strong>
                @else
                    · Location: <strong>All Locations</strong>
                @endif.
                Trade-in sources are grouped as <strong>TRADE-IN</strong>. Zero-value suppliers are hidden.
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
            No supplier supplied/sales/release rows found for this date range. Try another month or widen the period.
        </div>
    @else
        <ul class="nav nav-tabs" id="supplierTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="speed-tab" data-bs-toggle="tab" data-bs-target="#speedPane" type="button" role="tab">
                    Units Supplied / Sales / Releases / Speed
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="gross-net-tab" data-bs-toggle="tab" data-bs-target="#grossNetPane" type="button" role="tab">
                    Gross &amp; Net of Releases
                </button>
            </li>
        </ul>

        <div class="tab-content border border-top-0 bg-white" id="supplierTabContent">
            <div class="tab-pane fade show active" id="speedPane" role="tabpanel">
                @if(empty($speed) || empty($speed['has_data']))
                    <div class="p-4 text-muted mb-0">No supplier sales/release rows for this period.</div>
                @else
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 px-3 pt-3">
                        <div>
                            <strong>{{ $speed['title'] }}</strong>
                            <div class="small text-muted">{{ $activeRangeLabel }}</div>
                        </div>
                        <span class="badge text-bg-light border">{{ number_format(count($speed['rows'])) }} suppliers</span>
                    </div>
                    <div class="table-responsive mt-2">
                        <table class="table table-sm table-striped table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:3rem;">Rank</th>
                                    <th>Supplier</th>
                                    <th class="text-end">Units Supplied</th>
                                    <th class="text-end">% of Total Units Supplied</th>
                                    <th class="text-end">Total Sales</th>
                                    <th class="text-end">% of Total Sales</th>
                                    <th class="text-end">Total Releases</th>
                                    <th class="text-end">% of Total Releases</th>
                                    <th class="text-end">Average Days to Sell</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($speed['rows'] as $i => $row)
                                    @php
                                        $suppliedUnits = $row['supplied_units'] ?? [];
                                        $salesUnits = $row['sales_units'] ?? [];
                                        $releaseUnits = $row['release_units'] ?? [];
                                        $canOpenSupplied = count($suppliedUnits) > 0;
                                        $canOpenSales = count($salesUnits) > 0;
                                        $canOpenReleases = count($releaseUnits) > 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td class="fw-semibold">{{ $row['supplier'] }}</td>
                                        <td class="text-end">
                                            @if($canOpenSupplied)
                                                <button type="button"
                                                        class="btn btn-link text-decoration-none p-0 supplier-units-open"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#supplierUnitsModal"
                                                        data-title="{{ $row['supplier'] }}"
                                                        data-kind="supplied"
                                                        data-units='@json($suppliedUnits)'>
                                                    {{ number_format((int) ($row['supplied_count'] ?? 0)) }}
                                                </button>
                                            @else
                                                {{ number_format((int) ($row['supplied_count'] ?? 0)) }}
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format((float) ($row['supplied_pct'] ?? 0), 1) }}%</td>
                                        <td class="text-end">
                                            @if($canOpenSales)
                                                <button type="button"
                                                        class="btn btn-link text-decoration-none p-0 supplier-units-open"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#supplierUnitsModal"
                                                        data-title="{{ $row['supplier'] }}"
                                                        data-kind="sales"
                                                        data-units='@json($salesUnits)'>
                                                    {{ number_format((int) $row['sales_count']) }}
                                                </button>
                                            @else
                                                {{ number_format((int) $row['sales_count']) }}
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format((float) $row['sales_pct'], 1) }}%</td>
                                        <td class="text-end">
                                            @if($canOpenReleases)
                                                <button type="button"
                                                        class="btn btn-link text-decoration-none p-0 supplier-units-open"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#supplierUnitsModal"
                                                        data-title="{{ $row['supplier'] }}"
                                                        data-kind="releases"
                                                        data-units='@json($releaseUnits)'>
                                                    {{ number_format((int) $row['release_count']) }}
                                                </button>
                                            @else
                                                {{ number_format((int) $row['release_count']) }}
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format((float) $row['release_pct'], 1) }}%</td>
                                        <td class="text-end">{{ number_format((float) $row['avg_days_to_sell'], 1) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                @php
                                    $totalSuppliedUnits = $speed['totals']['supplied_units'] ?? [];
                                    $totalSalesUnits = $speed['totals']['sales_units'] ?? [];
                                    $totalReleaseUnits = $speed['totals']['release_units'] ?? [];
                                    $canOpenTotalSupplied = count($totalSuppliedUnits) > 0;
                                    $canOpenTotalSales = count($totalSalesUnits) > 0;
                                    $canOpenTotalReleases = count($totalReleaseUnits) > 0;
                                @endphp
                                <tr>
                                    <th>—</th>
                                    <th>TOTAL</th>
                                    <th class="text-end">
                                        @if($canOpenTotalSupplied)
                                            <button type="button"
                                                    class="btn btn-link text-decoration-none p-0 fw-bold supplier-units-open"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#supplierUnitsModal"
                                                    data-title="TOTAL"
                                                    data-kind="supplied"
                                                    data-units='@json($totalSuppliedUnits)'>
                                                {{ number_format((int) ($speed['totals']['supplied_count'] ?? 0)) }}
                                            </button>
                                        @else
                                            {{ number_format((int) ($speed['totals']['supplied_count'] ?? 0)) }}
                                        @endif
                                    </th>
                                    <th class="text-end">—</th>
                                    <th class="text-end">
                                        @if($canOpenTotalSales)
                                            <button type="button"
                                                    class="btn btn-link text-decoration-none p-0 fw-bold supplier-units-open"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#supplierUnitsModal"
                                                    data-title="TOTAL"
                                                    data-kind="sales"
                                                    data-units='@json($totalSalesUnits)'>
                                                {{ number_format((int) ($speed['totals']['sales_count'] ?? 0)) }}
                                            </button>
                                        @else
                                            {{ number_format((int) ($speed['totals']['sales_count'] ?? 0)) }}
                                        @endif
                                    </th>
                                    <th class="text-end">—</th>
                                    <th class="text-end">
                                        @if($canOpenTotalReleases)
                                            <button type="button"
                                                    class="btn btn-link text-decoration-none p-0 fw-bold supplier-units-open"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#supplierUnitsModal"
                                                    data-title="TOTAL"
                                                    data-kind="releases"
                                                    data-units='@json($totalReleaseUnits)'>
                                                {{ number_format((int) ($speed['totals']['release_count'] ?? 0)) }}
                                            </button>
                                        @else
                                            {{ number_format((int) ($speed['totals']['release_count'] ?? 0)) }}
                                        @endif
                                    </th>
                                    <th class="text-end">—</th>
                                    <th class="text-end">{{ number_format((float) ($speed['totals']['avg_days_to_sell'] ?? 0), 1) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>

            <div class="tab-pane fade" id="grossNetPane" role="tabpanel">
                @if(empty($grossNet) || empty($grossNet['has_data']))
                    <div class="p-4 text-muted mb-0">No supplier release gross/net rows for this period.</div>
                @else
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 px-3 pt-3">
                        <div>
                            <strong>{{ $grossNet['title'] }}</strong>
                            <div class="small text-muted">{{ $activeRangeLabel }}</div>
                        </div>
                        <span class="badge text-bg-light border">{{ number_format(count($grossNet['rows'])) }} suppliers</span>
                    </div>
                    <div class="table-responsive mt-2">
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
                @endif
            </div>
        </div>
    @endif
</div>

<div class="modal fade" id="supplierUnitsModal" tabindex="-1" aria-labelledby="supplierUnitsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supplierUnitsModalLabel">Supplier units</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted mb-3" id="supplierUnitsModalSubtitle"></p>
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
                                <th id="supplierUnitsEventHeader">Date</th>
                                <th class="text-end" id="supplierUnitsPriceHeader">Sold Price</th>
                            </tr>
                        </thead>
                        <tbody id="supplierUnitsModalBody">
                            <tr><td colspan="8" class="text-muted">No units.</td></tr>
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

    const modalEl = document.getElementById('supplierUnitsModal');
    if (!modalEl) return;

    modalEl.addEventListener('show.bs.modal', function (event) {
        const trigger = event.relatedTarget;
        if (!trigger) return;

        const title = trigger.getAttribute('data-title') || 'Supplier';
        const kind = trigger.getAttribute('data-kind') || 'supplied';
        let units = [];
        try {
            units = JSON.parse(trigger.getAttribute('data-units') || '[]');
        } catch (e) {
            units = [];
        }
        if (!Array.isArray(units)) units = [];

        const labelEl = document.getElementById('supplierUnitsModalLabel');
        const subtitleEl = document.getElementById('supplierUnitsModalSubtitle');
        const eventHeaderEl = document.getElementById('supplierUnitsEventHeader');
        const priceHeaderEl = document.getElementById('supplierUnitsPriceHeader');
        const bodyEl = document.getElementById('supplierUnitsModalBody');

        const kindMeta = {
            supplied: {
                label: 'units supplied',
                subtitle: 'unit(s) purchased/acquired from this supplier in the selected range.',
                eventHeader: 'Purchase',
                showPrice: false
            },
            sales: {
                label: 'sales',
                subtitle: 'sale(s) from this supplier in the selected range.',
                eventHeader: 'Sale Date',
                showPrice: true
            },
            releases: {
                label: 'releases',
                subtitle: 'release(s) from this supplier in the selected range.',
                eventHeader: 'Release Date',
                showPrice: true
            }
        };
        const meta = kindMeta[kind] || kindMeta.supplied;

        if (labelEl) labelEl.textContent = title + ' — ' + meta.label;
        if (subtitleEl) subtitleEl.textContent = units.length + ' ' + meta.subtitle;
        if (eventHeaderEl) eventHeaderEl.textContent = meta.eventHeader;
        if (priceHeaderEl) priceHeaderEl.style.display = meta.showPrice ? '' : 'none';
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
            bodyEl.innerHTML = '<tr><td colspan="8" class="text-muted">No units found.</td></tr>';
            return;
        }

        bodyEl.innerHTML = units.map(function (unit, index) {
            const plate = esc(unit.plate || '—');
            const name = esc(unit.name || 'Unknown unit');
            const year = esc(unit.year || '—');
            const status = esc(unit.status || '—');
            const location = esc(unit.location || '—');
            const eventDate = esc(
                unit.event_date_label
                || unit.event_date
                || unit.purchase_date
                || '—'
            );
            const plateCell = unit.url
                ? '<a href="' + esc(unit.url) + '" class="fw-semibold text-decoration-none">' + plate + '</a>'
                : plate;
            const priceCell = meta.showPrice
                ? '<td class="text-end">' + money(unit.sold_price) + '</td>'
                : '';

            return '<tr>'
                + '<td>' + (index + 1) + '</td>'
                + '<td>' + plateCell + '</td>'
                + '<td>' + name + '</td>'
                + '<td>' + year + '</td>'
                + '<td>' + status + '</td>'
                + '<td>' + location + '</td>'
                + '<td>' + eventDate + '</td>'
                + priceCell
                + '</tr>';
        }).join('');
    });
});
</script>
@endsection
