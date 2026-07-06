@extends('layouts.app')

@section('title', 'Financial Report - Car Empire Management System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-2">
        <h1 class="h3 mb-0"><i class="fas fa-chart-line me-2 text-success"></i>Financial Report</h1>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
            <i class="fas fa-home me-1"></i>Back to Home
        </a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('analytics-report.financial') }}" class="row g-2 align-items-end">
                <div class="col-md-8 col-lg-6">
                    <label for="report_type" class="form-label">Select report to generate</label>
                    <select name="report_type" id="report_type" class="form-select">
                        @foreach($reportOptions as $key => $label)
                            <option value="{{ $key }}" {{ $selectedReport === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-lg-3">
                    <label for="period" class="form-label">Filter period</label>
                    <select name="period" id="period" class="form-select">
                        @foreach($periodOptions as $key => $label)
                            <option value="{{ $key }}" {{ $selectedPeriod === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 col-lg-2 custom-range-only-field" style="{{ $selectedPeriod === 'range' ? '' : 'display:none;' }}">
                    <label for="date_from" class="form-label">Date from</label>
                    <input type="date" name="date_from" id="date_from" class="form-control" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-3 col-lg-2 custom-range-only-field" style="{{ $selectedPeriod === 'range' ? '' : 'display:none;' }}">
                    <label for="date_to" class="form-label">Date to</label>
                    <input type="date" name="date_to" id="date_to" class="form-control" value="{{ $dateTo }}">
                </div>
                <div class="col-md-4 col-lg-3 monthly-only-field" style="{{ $selectedPeriod === 'monthly' ? '' : 'display:none;' }}">
                    <label for="monthly_view" class="form-label">Monthly display</label>
                    <select name="monthly_view" id="monthly_view" class="form-select">
                        <option value="grouped" {{ ($monthlyView ?? 'grouped') === 'grouped' ? 'selected' : '' }}>Group by month (ALL default)</option>
                        <option value="specific" {{ ($monthlyView ?? 'grouped') === 'specific' ? 'selected' : '' }}>Specific month</option>
                    </select>
                </div>
                <div class="col-md-3 col-lg-2 monthly-specific-field" style="{{ $selectedPeriod === 'monthly' && ($monthlyView ?? 'grouped') === 'specific' ? '' : 'display:none;' }}">
                    <label for="month_pick" class="form-label">Choose month</label>
                    <input type="month" name="month_pick" id="month_pick" class="form-control" value="{{ $selectedMonth ?? '' }}">
                </div>
                <div class="col-md-4 col-lg-2">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-file-alt me-1"></i>Generate Report
                    </button>
                </div>
                <div class="col-md-4 col-lg-2">
                    <button type="button" class="btn btn-outline-success w-100 js-export-report" data-format="csv">
                        <i class="fas fa-file-excel me-1"></i>Excel (CSV)
                    </button>
                </div>
                <div class="col-md-4 col-lg-2">
                    <button type="button" class="btn btn-outline-danger w-100 js-export-report" data-format="pdf">
                        <i class="fas fa-file-pdf me-1"></i>PDF
                    </button>
                </div>
                <div class="col-12">
                    <div id="exportStatus" class="small text-muted d-flex align-items-center gap-2" aria-live="polite"></div>
                </div>
            </form>
            <p class="small text-muted mt-2 mb-0">
                Active date filter: <strong>{{ $activeRangeLabel }}</strong>. Monthly grouped mode uses ALL dates by default.
            </p>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center mb-3">
                <h5 class="mb-0">{{ $result['title'] ?? 'Report' }}</h5>
                <span class="badge bg-primary">{{ $result['count'] ?? 0 }} record(s)</span>
            </div>

            @if(!empty($result['summary']) && is_array($result['summary']))
                <div class="row g-3 mb-3">
                    @foreach($result['summary'] as $card)
                        <div class="col-md-4">
                            <div class="card border-success h-100">
                                <div class="card-body py-3">
                                    <div class="text-muted small mb-1">{{ $card['label'] ?? 'Summary' }}</div>
                                    <div class="h5 mb-0">
                                        @if(!empty($card['is_status_summary']) && !empty($card['status_counts']))
                                            @php
                                                $summaryStatusKeys = match ($selectedReport ?? '') {
                                                    'reservations' => ['R'],
                                                    'releases' => ['RL'],
                                                    default => null,
                                                };
                                                $summaryStatusMeta = $summaryStatusKeys
                                                    ? collect($statusCountMeta ?? [])->filter(fn ($item) => in_array($item['key'] ?? '', $summaryStatusKeys, true))->values()->all()
                                                    : ($statusCountMeta ?? []);
                                            @endphp
                                            @include('analytics-reports.partials.status-count-badges', [
                                                'counts' => $card['status_counts'],
                                                'statusMeta' => $summaryStatusMeta,
                                            ])
                                        @elseif(!empty($card['is_text']))
                                            {{ $card['value'] ?? '' }}
                                        @elseif(!empty($card['is_currency']))
                                            ₱{{ number_format((float) ($card['value'] ?? 0), 2) }}
                                        @else
                                            {{ number_format((float) ($card['value'] ?? 0), 0) }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if(!empty($result['charts']) && is_array($result['charts']))
                <div class="row g-3 mb-3">
                    @foreach($result['charts'] as $chartKey => $chart)
                        <div class="{{ (($chart['type'] ?? 'bar') === 'pie' || ($chart['type'] ?? 'bar') === 'doughnut') ? 'col-lg-4' : 'col-lg-8' }}">
                            <div class="card h-100">
                                <div class="card-header py-2">{{ $chart['title'] ?? 'Chart' }}</div>
                                <div class="card-body">
                                    <canvas id="financialChart_{{ $chartKey }}" class="financial-report-chart" data-chart-key="{{ $chartKey }}" height="110"></canvas>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($selectedPeriod === 'monthly' && !empty($result['monthly_groups']))
                @php
                    $mgUi = $result['monthly_groups_ui'] ?? null;
                    $mgType = $result['type'] ?? '';
                    $mgHasStatus = !empty($result['monthly_groups_has_status']);
                    $mgStatusKeys = $result['monthly_groups_status_keys'] ?? null;
                    $mgStatusMeta = $mgStatusKeys
                        ? collect($statusCountMeta ?? [])->filter(fn ($item) => in_array($item['key'], $mgStatusKeys, true))->values()->all()
                        : ($statusCountMeta ?? []);
                    $mgCountLabel = match ($selectedReport ?? '') {
                        'reservations' => 'Reserved Count',
                        'releases' => 'Released Count',
                        default => $mgHasStatus ? 'Status Count' : 'Count',
                    };
                @endphp
                <div class="card border-info mb-3">
                    <div class="card-body py-2">
                        <div class="d-flex flex-wrap gap-3 align-items-center justify-content-between mb-2">
                            <strong class="text-info">Monthly Grouped Result</strong>
                            @if($mgHasStatus && !empty($mgStatusMeta))
                                <div class="fin-status-legend d-flex flex-wrap align-items-center gap-2">
                                    <span class="small text-muted me-1">Legend:</span>
                                    @foreach($mgStatusMeta as $item)
                                        <span class="fin-status-legend-item">
                                            <span class="fin-status-dot {{ $item['dot_class'] ?? '' }}" aria-hidden="true"></span>
                                            <span class="small">{{ $item['label'] ?? '' }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        @if($mgHasStatus && ($selectedReport ?? '') === 'reservations')
                            <p class="small text-muted mb-2 mb-md-3">
                                Count of units reserved in each month (by reserve date).
                            </p>
                        @elseif($mgHasStatus && ($selectedReport ?? '') === 'releases')
                            <p class="small text-muted mb-2 mb-md-3">
                                Count of units released in each month (by release date).
                            </p>
                        @elseif($mgHasStatus && empty($mgStatusKeys))
                            <p class="small text-muted mb-2 mb-md-3">
                                Available = units encoded that month with Available status, minus Reserved and Released in the same month.
                            </p>
                        @endif
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        @if($mgUi && !empty($mgUi['column_keys']))
                                            @foreach($mgUi['column_keys'] as $mgKey)
                                                @php
                                                    $mgLabels = ['month' => 'Month', 'count' => $mgCountLabel, 'total' => 'Total'];
                                                    $mgLabel = $mgLabels[$mgKey] ?? $mgKey;
                                                    $mgActive = ($mgUi['sort_column'] ?? '') === $mgKey;
                                                    $mgNewDir = $mgActive && ($mgUi['sort_dir'] ?? 'asc') === 'asc' ? 'desc' : 'asc';
                                                    $mgUrl = request()->fullUrlWithQuery(['mg_sort' => $mgKey, 'mg_dir' => $mgNewDir, 'mg_page' => 1]);
                                                @endphp
                                                <th>
                                                    <a href="{{ $mgUrl }}" class="text-dark text-decoration-none d-inline-flex align-items-center gap-1">
                                                        {{ $mgLabel }}
                                                        @if($mgActive)
                                                            <i class="fas fa-sort-{{ ($mgUi['sort_dir'] ?? '') === 'desc' ? 'down' : 'up' }}"></i>
                                                        @else
                                                            <i class="fas fa-sort text-muted"></i>
                                                        @endif
                                                    </a>
                                                </th>
                                            @endforeach
                                        @else
                                            <th>Month</th>
                                            <th>{{ $mgCountLabel }}</th>
                                            @if($mgType === 'value_table')
                                                <th>Total</th>
                                            @endif
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($result['monthly_groups'] as $g)
                                        <tr>
                                            <td>{{ $g['month'] }}</td>
                                            <td>
                                                @if(!empty($g['status_counts']))
                                                    @include('analytics-reports.partials.status-count-badges', [
                                                        'counts' => $g['status_counts'],
                                                        'statusMeta' => $mgStatusMeta,
                                                    ])
                                                @else
                                                    {{ $g['count'] }}
                                                @endif
                                            </td>
                                            @if($mgType === 'value_table')
                                                <td>₱{{ number_format((float) ($g['total'] ?? 0), 2) }}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @if(!empty($mgUi['paginator']) && $mgUi['paginator']->hasPages())
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-2 small text-muted">
                                <div>
                                    Showing {{ $mgUi['paginator']->firstItem() }}–{{ $mgUi['paginator']->lastItem() }} of {{ $mgUi['paginator']->total() }}
                                </div>
                                <div>{{ $mgUi['paginator']->links() }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if(($result['type'] ?? '') === 'single_metric')
                <div class="alert alert-success mb-0">
                    <strong>{{ $result['metric_label'] ?? 'Value' }}:</strong>
                    ₱{{ number_format((float) ($result['metric_value'] ?? 0), 2) }}
                </div>
            @elseif(($result['type'] ?? '') === 'split_metric')
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="alert alert-info mb-0">
                            <strong>Cash:</strong> {{ $result['cash'] ?? 0 }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-warning mb-0">
                            <strong>Financing:</strong> {{ $result['financing'] ?? 0 }}
                        </div>
                    </div>
                </div>
            @elseif(in_array(($result['type'] ?? ''), ['table', 'value_table', 'age_table', 'age_sold_table', 'repairs_table'], true))
                @php
                    $tableUi = $result['table_ui'] ?? null;
                    $dataColumns = $result['columns'] ?? [];
                @endphp
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                @if($tableUi && !empty($tableUi['column_keys']))
                                    @foreach($tableUi['column_keys'] as $idx => $colKey)
                                        @php
                                            $colLabel = $dataColumns[$idx] ?? $colKey;
                                            $isSortActive = ($tableUi['sort_column'] ?? '') === $colKey;
                                            $newSortDir = $isSortActive && ($tableUi['sort_dir'] ?? 'asc') === 'asc' ? 'desc' : 'asc';
                                            $sortUrl = request()->fullUrlWithQuery(['sort_column' => $colKey, 'sort_dir' => $newSortDir, 'page' => 1]);
                                        @endphp
                                        <th scope="col">
                                            <a href="{{ $sortUrl }}" class="text-white text-decoration-none d-inline-flex align-items-center gap-1">
                                                {{ $colLabel }}
                                                @if($isSortActive)
                                                    <i class="fas fa-sort-{{ ($tableUi['sort_dir'] ?? '') === 'desc' ? 'down' : 'up' }}"></i>
                                                @else
                                                    <i class="fas fa-sort text-white-50"></i>
                                                @endif
                                            </a>
                                        </th>
                                    @endforeach
                                @else
                                    @foreach($dataColumns as $column)
                                        <th scope="col">{{ $column }}</th>
                                    @endforeach
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($result['rows'] ?? []) as $row)
                                <tr>
                                    @if(($result['type'] ?? '') === 'table')
                                        @if(($selectedReport ?? '') === 'releases')
                                            <td>{{ $row['plate'] ?? '-' }}</td>
                                            <td>{{ $row['unit'] ?? '-' }}</td>
                                            <td>{{ $row['date_encoded'] ?? '-' }}</td>
                                            <td class="text-end">₱{{ number_format((float) ($row['purchase_price'] ?? 0), 2) }}</td>
                                            <td class="text-end">₱{{ number_format((float) ($row['sold_price'] ?? 0), 2) }}</td>
                                            <td>{{ $row['sold_date'] ?? '-' }}</td>
                                            <td class="text-end">{{ $row['age_days'] ?? 0 }}</td>
                                        @else
                                            <td>{{ $row['plate'] ?? '-' }}</td>
                                            <td>{{ $row['unit'] ?? '-' }}</td>
                                            <td>{{ $row['date_encoded'] ?? '-' }}</td>
                                            <td class="text-end">₱{{ number_format((float) ($row['purchase_price'] ?? 0), 2) }}</td>
                                            <td class="text-end">₱{{ number_format((float) ($row['reservation_amount'] ?? 0), 2) }}</td>
                                            <td>{{ $row['reservation_date'] ?? '-' }}</td>
                                            <td class="text-end">{{ $row['age_days'] ?? 0 }}</td>
                                            <td>{{ $row['status'] ?? '-' }}</td>
                                        @endif
                                    @elseif(($result['type'] ?? '') === 'value_table')
                                        <td>{{ $row['plate'] ?? '-' }}</td>
                                        <td>{{ $row['unit'] ?? '-' }}</td>
                                        <td>₱{{ number_format((float) ($row['value'] ?? 0), 2) }}</td>
                                    @elseif(($result['type'] ?? '') === 'age_table')
                                        <td>{{ $row['plate'] ?? '-' }}</td>
                                        <td>{{ $row['unit'] ?? '-' }}</td>
                                        <td>{{ $row['status'] ?? '-' }}</td>
                                        <td>{{ $row['date_encoded'] ?? '-' }}</td>
                                        <td>{{ $row['age_days'] ?? 0 }}</td>
                                        <td>₱{{ number_format((float) ($row['purchase_price'] ?? 0), 2) }}</td>
                                        <td>₱{{ number_format((float) ($row['posted_price'] ?? 0), 2) }}</td>
                                    @elseif(($result['type'] ?? '') === 'age_sold_table')
                                        <td>{{ $row['plate'] ?? '-' }}</td>
                                        <td>{{ $row['unit'] ?? '-' }}</td>
                                        <td>{{ $row['date_encoded'] ?? '-' }}</td>
                                        <td>₱{{ number_format((float) ($row['purchase_price'] ?? 0), 2) }}</td>
                                        <td>₱{{ number_format((float) ($row['posted_price'] ?? 0), 2) }}</td>
                                        <td>₱{{ number_format((float) ($row['sold_price'] ?? 0), 2) }}</td>
                                        <td>{{ $row['sold_date'] ?? '-' }}</td>
                                        <td>{{ $row['age_days'] ?? 0 }}</td>
                                    @elseif(($result['type'] ?? '') === 'repairs_table')
                                        <td>{{ $row['plate'] ?? '-' }}</td>
                                        <td>{{ $row['unit'] ?? '-' }}</td>
                                        <td>₱{{ number_format((float) ($row['repair_cost'] ?? 0), 2) }}</td>
                                        <td>₱{{ number_format((float) ($row['post_reservation_repairs_cost'] ?? 0), 2) }}</td>
                                        <td>₱{{ number_format((float) ($row['total_repairs'] ?? 0), 2) }}</td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ count($result['columns'] ?? []) }}" class="text-center text-muted py-4">
                                        No records found for this report.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if(($result['type'] ?? '') === 'repairs_table' && isset($result['grand_total']))
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Grand Total</th>
                                    <th>₱{{ number_format((float) $result['grand_total'], 2) }}</th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
                @if(!empty($tableUi['paginator']) && $tableUi['paginator']->hasPages())
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-2 small text-muted">
                        <div>
                            Showing {{ $tableUi['paginator']->firstItem() }}–{{ $tableUi['paginator']->lastItem() }} of {{ $tableUi['paginator']->total() }}
                        </div>
                        <div>{{ $tableUi['paginator']->links() }}</div>
                    </div>
                @endif
            @else
                <p class="text-muted mb-0">No report output available.</p>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.fin-status-dot {
    display: inline-block;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
}
.fin-status-dot--available { background-color: #198754; }
.fin-status-dot--reserved { background-color: #ffc107; }
.fin-status-dot--released { background-color: #0d6efd; }
.fin-status-dot--forfeited { background-color: #dc3545; }

.fin-status-legend-item {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
}

.fin-status-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.fin-status-badges--compact {
    gap: 0.35rem;
}

.fin-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.35rem 0.65rem;
    border-radius: 999px;
    font-size: 0.82rem;
    font-weight: 600;
    line-height: 1.2;
    border: 1px solid transparent;
    white-space: nowrap;
}
.fin-status-pill__label {
    font-weight: 500;
}
.fin-status-pill__count {
    font-weight: 700;
    min-width: 1.25rem;
    text-align: right;
}

.fin-status-pill--available {
    background: rgba(25, 135, 84, 0.12);
    border-color: rgba(25, 135, 84, 0.35);
    color: #146c43;
}
.fin-status-pill--reserved {
    background: rgba(255, 193, 7, 0.18);
    border-color: rgba(255, 193, 7, 0.45);
    color: #856404;
}
.fin-status-pill--released {
    background: rgba(13, 110, 253, 0.12);
    border-color: rgba(13, 110, 253, 0.35);
    color: #084298;
}
.fin-status-pill--forfeited {
    background: rgba(220, 53, 69, 0.12);
    border-color: rgba(220, 53, 69, 0.35);
    color: #b02a37;
}

.fin-status-badges--compact .fin-status-pill {
    padding: 0.25rem 0.5rem;
    font-size: 0.78rem;
}
.fin-status-badges--compact .fin-status-pill__label {
    display: none;
}
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const period = document.getElementById('period');
    const monthlyView = document.getElementById('monthly_view');
    const monthlyOnlyFields = document.querySelectorAll('.monthly-only-field');
    const monthlySpecificFields = document.querySelectorAll('.monthly-specific-field');
    const customRangeOnlyFields = document.querySelectorAll('.custom-range-only-field');

    function toggleMonthlyFields() {
        const isCustomRange = period && period.value === 'range';
        customRangeOnlyFields.forEach(el => { el.style.display = isCustomRange ? '' : 'none'; });

        const isMonthly = period && period.value === 'monthly';
        monthlyOnlyFields.forEach(el => { el.style.display = isMonthly ? '' : 'none'; });
        const isSpecific = isMonthly && monthlyView && monthlyView.value === 'specific';
        monthlySpecificFields.forEach(el => { el.style.display = isSpecific ? '' : 'none'; });
    }

    if (period) period.addEventListener('change', toggleMonthlyFields);
    if (monthlyView) monthlyView.addEventListener('change', toggleMonthlyFields);
    toggleMonthlyFields();

    const exportButtons = document.querySelectorAll('.js-export-report');
    const exportStatus = document.getElementById('exportStatus');
    const exportBaseUrl = @json(route('analytics-report.financial.export'));

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

    function buildExportUrl(format) {
        const params = new URLSearchParams(new FormData(document.querySelector('form[action="{{ route('analytics-report.financial') }}"]')));
        params.set('format', format);
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

    async function exportReport(format) {
        try {
            setExportButtonsDisabled(true);
            setExportStatus('loading', 'Exporting ' + format.toUpperCase() + '... please wait.');
            const response = await fetch(buildExportUrl(format), {
                method: 'GET',
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) {
                throw new Error('Export request failed');
            }
            const blob = await response.blob();
            const filename = parseFilename(response.headers.get('content-disposition'), 'financial-report.' + format);
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);
            if (format.toLowerCase() === 'pdf') {
                setExportStatus('success', 'Successfully process PDF. it will automatically download in a moment');
            } else {
                setExportStatus('success', 'Successfully exported ' + format.toUpperCase() + '.');
            }
        } catch (error) {
            setExportStatus('error', 'Export failed. Please try again.');
        } finally {
            setExportButtonsDisabled(false);
        }
    }

    exportButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const format = this.getAttribute('data-format');
            if (format) {
                exportReport(format);
            }
        });
    });

    const charts = @json($result['charts'] ?? []);
    if (window.Chart && charts && typeof charts === 'object') {
        const palette = [
            'rgba(13, 110, 253, 0.75)',
            'rgba(25, 135, 84, 0.75)',
            'rgba(255, 193, 7, 0.75)',
            'rgba(220, 53, 69, 0.75)',
            'rgba(111, 66, 193, 0.75)',
            'rgba(13, 202, 240, 0.75)',
            'rgba(108, 117, 125, 0.75)'
        ];
        document.querySelectorAll('.financial-report-chart').forEach(function (canvasEl) {
            const key = canvasEl.getAttribute('data-chart-key');
            const chartCfg = charts[key];
            if (!chartCfg || !Array.isArray(chartCfg.labels)) return;

            const type = chartCfg.type || 'bar';
            const isPieType = type === 'pie' || type === 'doughnut' || type === 'polarArea';
            const data = chartCfg.data || [];
            const backgroundColor = isPieType
                ? data.map((_, i) => palette[i % palette.length])
                : 'rgba(13, 110, 253, 0.5)';
            const borderColor = isPieType
                ? data.map((_, i) => palette[i % palette.length].replace('0.75', '1'))
                : 'rgba(13, 110, 253, 1)';

            new Chart(canvasEl, {
                type: type,
                data: {
                    labels: chartCfg.labels,
                    datasets: [{
                        label: chartCfg.dataset_label || 'Value',
                        data: data,
                        backgroundColor: backgroundColor,
                        borderColor: borderColor,
                        borderWidth: 1.5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: isPieType ? {} : { y: { beginAtZero: true, ticks: { precision: 0 } } }
                }
            });
        });
    }
});
</script>
@endsection
