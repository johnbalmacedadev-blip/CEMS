@extends('layouts.app')

@section('title', 'Sales Report - Car Empire Management System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-2">
        <h1 class="h3 mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Sales Report</h1>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
            <i class="fas fa-home me-1"></i>Back to Home
        </a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('analytics-report.sales') }}" class="row g-2 align-items-end">
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
                <div class="col-md-4 col-lg-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-sync-alt me-1"></i>Update Report
                    </button>
                </div>
                <div class="col-md-4 col-lg-2">
                    <button type="button" class="btn btn-outline-danger w-100 js-export-sales-pdf" data-format="pdf">
                        <i class="fas fa-file-pdf me-1"></i>Export PDF
                    </button>
                </div>
                <div class="col-12">
                    <div id="salesExportStatus" class="small text-muted d-flex align-items-center gap-2" aria-live="polite"></div>
                </div>
            </form>
            <p class="small text-muted mt-2 mb-0">
                Released units only. Active date filter: <strong>{{ $activeRangeLabel }}</strong>.
                Monthly default uses the current calendar year.
            </p>
        </div>
    </div>

    @if(!$hasData)
        <div class="alert alert-info mb-0">
            <i class="fas fa-info-circle me-2"></i>
            No released units found for this date range. Try expanding the period or selecting Custom Date Range.
        </div>
    @else
        <div class="row g-3 mb-3">
            @foreach($summary as $card)
                <div class="col-md-6 col-xl-3">
                    <div class="card border-primary h-100">
                        <div class="card-body py-3">
                            <div class="text-muted small mb-1">{{ $card['label'] }}</div>
                            <div class="h4 mb-0">
                                @if(!empty($card['is_currency']))
                                    ₱{{ number_format((float) $card['value'], 2) }}
                                @else
                                    {{ is_float($card['value']) || str_contains((string) $card['value'], '.')
                                        ? number_format((float) $card['value'], 1)
                                        : number_format((float) $card['value'], 0) }}{{ $card['suffix'] ?? '' }}
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
                        <strong>{{ $charts['monthly_units']['title'] ?? 'Monthly Units Sold' }}</strong>
                    </div>
                    <div class="card-body">
                        <div class="sales-chart-wrap">
                            <canvas class="sales-report-chart" data-chart-key="monthly_units"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-header bg-white">
                        <strong>{{ $charts['monthly_sales']['title'] ?? 'Monthly Sales Amount' }}</strong>
                    </div>
                    <div class="card-body">
                        <div class="sales-chart-wrap">
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
                        <strong>{{ $charts['body_type_mix']['title'] ?? 'Body Type Mix' }}</strong>
                    </div>
                    <div class="card-body">
                        <div class="sales-chart-wrap">
                            <canvas class="sales-report-chart" data-chart-key="body_type_mix"></canvas>
                        </div>
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

        <div class="row g-3">
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header bg-white"><strong>Top Makes</strong></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Make</th>
                                        <th class="text-end">Units</th>
                                        <th class="text-end">Sales</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tables['top_makes'] as $row)
                                        <tr>
                                            <td>{{ $row['label'] }}</td>
                                            <td class="text-end">{{ number_format($row['count']) }}</td>
                                            <td class="text-end">₱{{ number_format($row['sales'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-muted text-center py-3">No data</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card h-100">
                    <div class="card-header bg-white"><strong>Top Models</strong></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Model</th>
                                        <th class="text-end">Units</th>
                                        <th class="text-end">Sales</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tables['top_models'] as $row)
                                        <tr>
                                            <td>{{ $row['label'] }}</td>
                                            <td class="text-end">{{ number_format($row['count']) }}</td>
                                            <td class="text-end">₱{{ number_format($row['sales'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="3" class="text-muted text-center py-3">No data</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
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
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-white"><strong>Monthly Breakdown</strong></div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Month</th>
                                        <th class="text-end">Units Released</th>
                                        <th class="text-end">Sales Amount</th>
                                        <th class="text-end">Avg Days to Sell</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tables['monthly'] as $row)
                                        <tr>
                                            <td>{{ $row['label'] }}</td>
                                            <td class="text-end">{{ number_format($row['count']) }}</td>
                                            <td class="text-end">₱{{ number_format($row['sales'], 2) }}</td>
                                            <td class="text-end">{{ number_format($row['avg_days'], 1) }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-muted text-center py-3">No monthly data</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
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
</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const period = document.getElementById('period');
    const customRangeOnlyFields = document.querySelectorAll('.custom-range-only-field');

    function toggleRangeFields() {
        const isCustomRange = period && period.value === 'range';
        customRangeOnlyFields.forEach(el => { el.style.display = isCustomRange ? '' : 'none'; });
    }

    if (period) period.addEventListener('change', toggleRangeFields);
    toggleRangeFields();

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
        const backgroundColor = isPieType
            ? data.map((_, i) => palette[i % palette.length])
            : (indexAxis === 'y' ? data.map((_, i) => palette[i % palette.length]) : 'rgba(13, 110, 253, 0.55)');
        const borderColor = isPieType || indexAxis === 'y'
            ? (Array.isArray(backgroundColor)
                ? backgroundColor.map(c => String(c).replace('0.75', '1'))
                : 'rgba(13, 110, 253, 1)')
            : (type === 'line' ? 'rgba(25, 135, 84, 1)' : 'rgba(13, 110, 253, 1)');

        const options = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: isPieType }
            }
        };

        if (!isPieType) {
            options.indexAxis = indexAxis;
            options.scales = {
                x: { beginAtZero: true, ticks: { precision: 0 } },
                y: { beginAtZero: true, ticks: { precision: 0 } }
            };
            if (indexAxis === 'y') {
                options.scales.x.ticks.precision = 0;
            }
        }

        new Chart(canvasEl, {
            type: type,
            data: {
                labels: chartCfg.labels,
                datasets: [{
                    label: chartCfg.dataset_label || 'Value',
                    data: data,
                    backgroundColor: type === 'line' ? 'rgba(25, 135, 84, 0.15)' : backgroundColor,
                    borderColor: borderColor,
                    borderWidth: 1.5,
                    fill: type === 'line',
                    tension: type === 'line' ? 0.25 : 0
                }]
            },
            options: options
        });
    });
});
</script>
@endsection
