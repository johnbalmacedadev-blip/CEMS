@extends('layouts.app')

@section('title', 'Sales Reports - Car Empire Management System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-2">
        <h1 class="h3 mb-0"><i class="fas fa-file-invoice-dollar me-2 text-success"></i>Sales Reports</h1>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
            <i class="fas fa-home me-1"></i>Back to Home
        </a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('analytics-report.sales-reports') }}" class="row g-2 align-items-end">
                <input type="hidden" name="run" value="1">
                <input type="hidden" name="excel_tab" id="excelTab" value="{{ $activeExcelTab ?? 'day_of_week' }}">
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
                        @foreach(($monthOptions ?? ['' => 'All months in year']) as $value => $label)
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
                Matches Excel <strong>(18-19) SALES REPORTS</strong>.
                Year: <strong>{{ $activeRangeLabel }}</strong>
                @if(($selectedLocation ?? '') !== '')
                    · Location: <strong>{{ $selectedLocation }}</strong>
                @else
                    · Location: <strong>All Locations</strong>
                @endif.
                Sales use sale dates; releases use release dates. Days to sell/release are purchase → sale/release averages. Zero rows are hidden.
            </p>
        </div>
    </div>

    @if(!($showResults ?? false))
        <div class="alert alert-light border mb-0">
            <i class="fas fa-filter me-2 text-muted"></i>
            Select a year and click <strong>Update Report</strong> to load Sales Reports tables.
        </div>
    @elseif(!($hasData ?? false))
        <div class="alert alert-info mb-0">
            <i class="fas fa-info-circle me-2"></i>
            No sales/release rows found for this year. Try another year or location.
        </div>
    @else
        @php
            $tabDefs = [
                'day_of_week' => 'Sales by Day of Week',
                'days_to_sell' => 'Days to Sell & Release',
            ];
            $activeTab = $activeExcelTab ?? 'day_of_week';
            if (! array_key_exists($activeTab, $tabDefs)) {
                $activeTab = 'day_of_week';
            }
            $dow = $dayOfWeekReport ?? null;
            $dts = $daysToSellReport ?? null;
        @endphp

        <ul class="nav nav-tabs" id="salesReportsTabs" role="tablist">
            @foreach($tabDefs as $tabKey => $tabLabel)
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === $tabKey ? 'active' : '' }}"
                            id="tab-{{ $tabKey }}-btn"
                            data-bs-toggle="tab"
                            data-bs-target="#tab-{{ $tabKey }}"
                            data-excel-tab="{{ $tabKey }}"
                            type="button"
                            role="tab">
                        {{ $tabLabel }}
                    </button>
                </li>
            @endforeach
        </ul>

        <div class="tab-content border border-top-0 bg-white" id="salesReportsTabContent">
            <div class="tab-pane fade {{ $activeTab === 'day_of_week' ? 'show active' : '' }}" id="tab-day_of_week" role="tabpanel">
                @if(empty($dow['has_data']))
                    <div class="p-4 text-muted">No day-of-week sales/release rows for this year.</div>
                @else
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 px-3 pt-3">
                        <div>
                            <strong>{{ $dow['title'] }}</strong>
                            <div class="small text-muted">{{ $activeRangeLabel }}</div>
                        </div>
                        <span class="badge text-bg-light border">{{ number_format(count($dow['rows'])) }} days</span>
                    </div>
                    <div class="table-responsive mt-2">
                        <table class="table table-sm table-striped table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:3rem;">Rank</th>
                                    <th>Day of Week</th>
                                    <th class="text-end">Total Sales</th>
                                    <th class="text-end">% of Total Sales</th>
                                    <th class="text-end">Total Releases</th>
                                    <th class="text-end">% of Total Releases</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dow['rows'] as $row)
                                    @php
                                        $salesUnits = $row['sales_units'] ?? [];
                                        $releaseUnits = $row['release_units'] ?? [];
                                        $canOpenSales = count($salesUnits) > 0;
                                        $canOpenReleases = count($releaseUnits) > 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $row['rank'] }}</td>
                                        <td class="fw-semibold">
                                            @if($canOpenSales)
                                                <button type="button"
                                                        class="btn btn-link text-decoration-none p-0 fw-semibold sales-reports-units-open"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#salesReportsUnitsModal"
                                                        data-title="{{ $row['label'] }}"
                                                        data-kind="sales"
                                                        data-units='@json($salesUnits)'>
                                                    {{ $row['label'] }}
                                                </button>
                                            @else
                                                {{ $row['label'] }}
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($canOpenSales)
                                                <button type="button"
                                                        class="btn btn-link text-decoration-none p-0 sales-reports-units-open"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#salesReportsUnitsModal"
                                                        data-title="{{ $row['label'] }}"
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
                                                        class="btn btn-link text-decoration-none p-0 sales-reports-units-open"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#salesReportsUnitsModal"
                                                        data-title="{{ $row['label'] }}"
                                                        data-kind="releases"
                                                        data-units='@json($releaseUnits)'>
                                                    {{ number_format((int) $row['release_count']) }}
                                                </button>
                                            @else
                                                {{ number_format((int) $row['release_count']) }}
                                            @endif
                                        </td>
                                        <td class="text-end">{{ number_format((float) $row['release_pct'], 1) }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                @php
                                    $totalSalesUnits = $dow['totals']['sales_units'] ?? [];
                                    $totalReleaseUnits = $dow['totals']['release_units'] ?? [];
                                    $canOpenTotalSales = count($totalSalesUnits) > 0;
                                    $canOpenTotalReleases = count($totalReleaseUnits) > 0;
                                @endphp
                                <tr>
                                    <th>—</th>
                                    <th>TOTAL</th>
                                    <th class="text-end">
                                        @if($canOpenTotalSales)
                                            <button type="button"
                                                    class="btn btn-link text-decoration-none p-0 fw-bold sales-reports-units-open"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#salesReportsUnitsModal"
                                                    data-title="TOTAL"
                                                    data-kind="sales"
                                                    data-units='@json($totalSalesUnits)'>
                                                {{ number_format((int) ($dow['totals']['sales_count'] ?? 0)) }}
                                            </button>
                                        @else
                                            {{ number_format((int) ($dow['totals']['sales_count'] ?? 0)) }}
                                        @endif
                                    </th>
                                    <th class="text-end">{{ number_format((float) ($dow['totals']['sales_pct'] ?? 0), 1) }}%</th>
                                    <th class="text-end">
                                        @if($canOpenTotalReleases)
                                            <button type="button"
                                                    class="btn btn-link text-decoration-none p-0 fw-bold sales-reports-units-open"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#salesReportsUnitsModal"
                                                    data-title="TOTAL"
                                                    data-kind="releases"
                                                    data-units='@json($totalReleaseUnits)'>
                                                {{ number_format((int) ($dow['totals']['release_count'] ?? 0)) }}
                                            </button>
                                        @else
                                            {{ number_format((int) ($dow['totals']['release_count'] ?? 0)) }}
                                        @endif
                                    </th>
                                    <th class="text-end">{{ number_format((float) ($dow['totals']['release_pct'] ?? 0), 1) }}%</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>

            <div class="tab-pane fade {{ $activeTab === 'days_to_sell' ? 'show active' : '' }}" id="tab-days_to_sell" role="tabpanel">
                @if(empty($dts['has_data']))
                    <div class="p-4 text-muted">No days-to-sell / days-to-release rows for this year.</div>
                @else
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 px-3 pt-3">
                        <div>
                            <strong>{{ $dts['title'] }}</strong>
                            <div class="small text-muted">{{ $activeRangeLabel }}</div>
                        </div>
                        <span class="badge text-bg-light border">{{ number_format(count($dts['rows'])) }} months</span>
                    </div>
                    <div class="table-responsive mt-2">
                        <table class="table table-sm table-striped table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:3rem;">#</th>
                                    <th>Month</th>
                                    <th class="text-end">Total Sales</th>
                                    <th class="text-end">Days to Sell</th>
                                    <th class="text-end">Total Releases</th>
                                    <th class="text-end">Days to Release</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($dts['rows'] as $i => $row)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td class="fw-semibold">{{ $row['label'] }}</td>
                                        <td class="text-end">{{ number_format((int) $row['sales_count']) }}</td>
                                        <td class="text-end">{{ number_format((float) $row['days_to_sell'], 1) }}</td>
                                        <td class="text-end">{{ number_format((int) $row['release_count']) }}</td>
                                        <td class="text-end">{{ number_format((float) $row['days_to_release'], 1) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th>—</th>
                                    <th>TOTAL</th>
                                    <th class="text-end">{{ number_format((int) ($dts['totals']['sales_count'] ?? 0)) }}</th>
                                    <th class="text-end">{{ number_format((float) ($dts['totals']['days_to_sell'] ?? 0), 1) }}</th>
                                    <th class="text-end">{{ number_format((int) ($dts['totals']['release_count'] ?? 0)) }}</th>
                                    <th class="text-end">{{ number_format((float) ($dts['totals']['days_to_release'] ?? 0), 1) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>

<div class="modal fade" id="salesReportsUnitsModal" tabindex="-1" aria-labelledby="salesReportsUnitsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="salesReportsUnitsModalLabel">Units</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted mb-3" id="salesReportsUnitsModalSubtitle"></p>
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
                                <th id="salesReportsUnitsEventHeader">Date</th>
                                <th class="text-end">Sold Price</th>
                            </tr>
                        </thead>
                        <tbody id="salesReportsUnitsModalBody">
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
    const excelTabInput = document.getElementById('excelTab');
    document.querySelectorAll('#salesReportsTabs [data-excel-tab]').forEach(btn => {
        btn.addEventListener('shown.bs.tab', function () {
            if (excelTabInput) {
                excelTabInput.value = btn.getAttribute('data-excel-tab') || 'day_of_week';
            }
        });
    });

    const modalEl = document.getElementById('salesReportsUnitsModal');
    if (!modalEl) return;

    modalEl.addEventListener('show.bs.modal', function (event) {
        const trigger = event.relatedTarget;
        if (!trigger) return;

        const title = trigger.getAttribute('data-title') || 'Day';
        const kind = trigger.getAttribute('data-kind') || 'sales';
        const isReleases = kind === 'releases';
        let units = [];
        try {
            units = JSON.parse(trigger.getAttribute('data-units') || '[]');
        } catch (e) {
            units = [];
        }
        if (!Array.isArray(units)) units = [];

        const labelEl = document.getElementById('salesReportsUnitsModalLabel');
        const subtitleEl = document.getElementById('salesReportsUnitsModalSubtitle');
        const eventHeaderEl = document.getElementById('salesReportsUnitsEventHeader');
        const bodyEl = document.getElementById('salesReportsUnitsModalBody');
        const kindLabel = isReleases ? 'releases' : 'sales';

        if (labelEl) {
            labelEl.textContent = title + ' — ' + kindLabel;
        }
        if (subtitleEl) {
            subtitleEl.textContent = units.length + ' unit(s) for this ' + kindLabel + ' summary.';
        }
        if (eventHeaderEl) {
            eventHeaderEl.textContent = isReleases ? 'Release Date' : 'Sale Date';
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
            bodyEl.innerHTML = '<tr><td colspan="8" class="text-muted">No units found.</td></tr>';
            return;
        }

        bodyEl.innerHTML = units.map(function (unit, index) {
            const plate = esc(unit.plate || '—');
            const name = esc(unit.name || 'Unknown unit');
            const year = esc(unit.year || '—');
            const status = esc(unit.status || '—');
            const location = esc(unit.location || '—');
            const eventDate = esc(unit.event_date_label || unit.event_date || '—');
            const soldPrice = money(unit.sold_price);
            const plateCell = unit.url
                ? '<a href="' + esc(unit.url) + '" class="fw-semibold text-decoration-none">' + plate + '</a>'
                : plate;

            return '<tr>'
                + '<td>' + (index + 1) + '</td>'
                + '<td>' + plateCell + '</td>'
                + '<td>' + name + '</td>'
                + '<td>' + year + '</td>'
                + '<td>' + status + '</td>'
                + '<td>' + location + '</td>'
                + '<td>' + eventDate + '</td>'
                + '<td class="text-end">' + soldPrice + '</td>'
                + '</tr>';
        }).join('');
    });
});
</script>
@endsection
