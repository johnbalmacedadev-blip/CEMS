@extends('layouts.app')

@section('title', 'Financing Reports - Car Empire Management System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-2">
        <h1 class="h3 mb-0"><i class="fas fa-hand-holding-usd me-2 text-success"></i>Financing Reports</h1>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
            <i class="fas fa-home me-1"></i>Back to Home
        </a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('analytics-report.financing') }}" class="row g-2 align-items-end" id="financingFilterForm">
                <input type="hidden" name="run" value="1">
                <input type="hidden" name="excel_tab" id="excelTab" value="{{ $activeExcelTab ?? 'summary' }}">
                <div class="col-md-3 col-lg-2">
                    <label for="year" class="form-label">Year</label>
                    <select name="year" id="year" class="form-select">
                        @foreach(($yearOptions ?? []) as $value => $label)
                            <option value="{{ $value }}" {{ (int) ($selectedYear ?? date('Y')) === (int) $value ? 'selected' : '' }}>{{ $label }}</option>
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
                Matches Excel <strong>(12-15) FINANCING REPORTS</strong>.
                Year: <strong>{{ $activeRangeLabel }}</strong>
                @if(($selectedLocation ?? '') !== '')
                    · Location: <strong>{{ $selectedLocation }}</strong>
                @else
                    · Location: <strong>All Locations</strong>
                @endif.
                Financing releases use <code>cash_financing = Financing</code>. Amount financed = sales price − reservation (fallback: sales price). Zero rows are hidden.
            </p>
        </div>
    </div>

    @if(!($showResults ?? false))
        <div class="alert alert-light border mb-0">
            <i class="fas fa-filter me-2 text-muted"></i>
            Select a year and click <strong>Update Report</strong> to load Financing tables.
        </div>
    @elseif(!($hasData ?? false))
        <div class="alert alert-info mb-0">
            <i class="fas fa-info-circle me-2"></i>
            No financing releases found for this year. Try another year or location.
        </div>
    @else
        @php
            $tabDefs = [
                'summary' => 'Financing Summary',
                'term_length' => 'Term Length Summary',
                'by_company' => 'By Company',
                'finishing' => 'Loans Finishing 3/2/1 Months',
            ];
            $activeTab = $activeExcelTab ?? 'summary';
            if (! array_key_exists($activeTab, $tabDefs)) {
                $activeTab = 'summary';
            }
            $summary = $summaryReport ?? null;
            $termLength = $termLengthReport ?? null;
            $company = $companyReport ?? null;
            $finishing = $finishingReport ?? null;
        @endphp

        <ul class="nav nav-tabs" id="financingTabs" role="tablist">
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

        <div class="tab-content border border-top-0 bg-white" id="financingTabContent">
            <div class="tab-pane fade {{ $activeTab === 'summary' ? 'show active' : '' }}" id="tab-summary" role="tabpanel">
                @if(empty($summary['has_data']))
                    <div class="p-4 text-muted">No financing summary rows for this year.</div>
                @else
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 px-3 pt-3">
                        <div>
                            <strong>{{ $summary['title'] }}</strong>
                            <div class="small text-muted">{{ $activeRangeLabel }}</div>
                        </div>
                        <span class="badge text-bg-light border">{{ number_format(count($summary['rows'])) }} months</span>
                    </div>
                    <div class="table-responsive mt-2">
                        <table class="table table-sm table-striped table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:3rem;">#</th>
                                    <th>Month</th>
                                    <th class="text-end"># of Financing Releases</th>
                                    <th class="text-end">% of Financing Releases vs Total Releases</th>
                                    <th class="text-end">Total Amount Financed (PHP)</th>
                                    <th class="text-end">Average Amount Financed (PHP)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($summary['rows'] as $i => $row)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td class="fw-semibold">{{ $row['label'] }}</td>
                                        <td class="text-end">{{ number_format((int) $row['financing_releases']) }}</td>
                                        <td class="text-end">{{ number_format((float) $row['financing_pct'], 1) }}%</td>
                                        <td class="text-end">₱{{ number_format((float) $row['amount_financed'], 2) }}</td>
                                        <td class="text-end">₱{{ number_format((float) $row['avg_amount_financed'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th>—</th>
                                    <th>TOTAL</th>
                                    <th class="text-end">{{ number_format((int) ($summary['totals']['financing_releases'] ?? 0)) }}</th>
                                    <th class="text-end">{{ number_format((float) ($summary['totals']['financing_pct'] ?? 0), 1) }}%</th>
                                    <th class="text-end">₱{{ number_format((float) ($summary['totals']['amount_financed'] ?? 0), 2) }}</th>
                                    <th class="text-end">₱{{ number_format((float) ($summary['totals']['avg_amount_financed'] ?? 0), 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>

            <div class="tab-pane fade {{ $activeTab === 'term_length' ? 'show active' : '' }}" id="tab-term_length" role="tabpanel">
                <div class="p-4">
                    <strong>{{ $termLength['title'] ?? 'Financing Term Length Summary' }}</strong>
                    <p class="text-muted mb-0 mt-2">{{ $termLength['message'] ?? 'No term length data available.' }}</p>
                </div>
            </div>

            <div class="tab-pane fade {{ $activeTab === 'by_company' ? 'show active' : '' }}" id="tab-by_company" role="tabpanel">
                @if(empty($company['has_data']))
                    <div class="p-4 text-muted">No financing company rows for this year.</div>
                @else
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 px-3 pt-3">
                        <div>
                            <strong>{{ $company['title'] }}</strong>
                            <div class="small text-muted">{{ $activeRangeLabel }}</div>
                        </div>
                        <span class="badge text-bg-light border">{{ number_format(count($company['rows'])) }} companies</span>
                    </div>
                    <div class="table-responsive mt-2">
                        <table class="table table-sm table-striped table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:3rem;">#</th>
                                    <th>Financing Company</th>
                                    <th class="text-end"># of Financing Releases</th>
                                    <th class="text-end">% of Financing Releases vs Total Releases</th>
                                    <th class="text-end">Total Amount Financed (PHP)</th>
                                    <th class="text-end">Average Amount Financed (PHP)</th>
                                    <th class="text-end">Average Days to Release</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($company['rows'] as $i => $row)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td class="fw-semibold">{{ $row['label'] }}</td>
                                        <td class="text-end">{{ number_format((int) $row['financing_releases']) }}</td>
                                        <td class="text-end">{{ number_format((float) $row['financing_pct'], 1) }}%</td>
                                        <td class="text-end">₱{{ number_format((float) $row['amount_financed'], 2) }}</td>
                                        <td class="text-end">₱{{ number_format((float) $row['avg_amount_financed'], 2) }}</td>
                                        <td class="text-end">{{ number_format((float) $row['avg_days_to_release'], 1) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th>—</th>
                                    <th>TOTAL</th>
                                    <th class="text-end">{{ number_format((int) ($company['totals']['financing_releases'] ?? 0)) }}</th>
                                    <th class="text-end">—</th>
                                    <th class="text-end">₱{{ number_format((float) ($company['totals']['amount_financed'] ?? 0), 2) }}</th>
                                    <th class="text-end">₱{{ number_format((float) ($company['totals']['avg_amount_financed'] ?? 0), 2) }}</th>
                                    <th class="text-end">{{ number_format((float) ($company['totals']['avg_days_to_release'] ?? 0), 1) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>

            <div class="tab-pane fade {{ $activeTab === 'finishing' ? 'show active' : '' }}" id="tab-finishing" role="tabpanel">
                <div class="p-4">
                    <strong>{{ $finishing['title'] ?? 'Financing Loans Finishing in 3/2/1 Months' }}</strong>
                    <p class="text-muted mb-0 mt-2">{{ $finishing['message'] ?? 'No finishing loan data available.' }}</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const excelTabInput = document.getElementById('excelTab');
    document.querySelectorAll('#financingTabs [data-excel-tab]').forEach(btn => {
        btn.addEventListener('shown.bs.tab', function () {
            if (excelTabInput) {
                excelTabInput.value = btn.getAttribute('data-excel-tab') || 'summary';
            }
        });
    });
});
</script>
@endsection
