@extends('layouts.app')

@section('title', 'Customer Reports - Car Empire Management System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-2">
        <h1 class="h3 mb-0"><i class="fas fa-users me-2 text-success"></i>Customer Reports</h1>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
            <i class="fas fa-home me-1"></i>Back to Home
        </a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('analytics-report.customers') }}" class="row g-2 align-items-end">
                <input type="hidden" name="run" value="1">
                <input type="hidden" name="excel_tab" id="excelTab" value="{{ $activeExcelTab ?? 'by_age' }}">
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
                    <label for="location" class="form-label">Branch Location</label>
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
                Matches Excel <strong>(28-29) CUSTOMER REPORTS</strong> (Age, Gender) and catalog #30 (Location).
                Period: <strong>{{ $activeRangeLabel }}</strong>
                @if(($selectedLocation ?? '') !== '')
                    · Branch: <strong>{{ $selectedLocation }}</strong>
                @else
                    · Branch: <strong>All Locations</strong>
                @endif.
                Sales use sale dates; releases use release dates. Age is from customer DOB at the sale/release date. Zero rows are hidden.
            </p>
        </div>
    </div>

    @if(!($showResults ?? false))
        <div class="alert alert-light border mb-0">
            <i class="fas fa-filter me-2 text-muted"></i>
            Select filters and click <strong>Update Report</strong> to load Customer tables.
        </div>
    @elseif(!($hasData ?? false))
        <div class="alert alert-info mb-0">
            <i class="fas fa-info-circle me-2"></i>
            No customer sales/release rows found for these filters.
        </div>
    @else
        @php
            $tabDefs = [
                'by_age' => ['label' => 'By Age', 'report' => $byAgeReport ?? null],
                'by_gender' => ['label' => 'By Gender', 'report' => $byGenderReport ?? null],
                'by_location' => ['label' => 'By Customer Location', 'report' => $byLocationReport ?? null],
            ];
            $activeTab = $activeExcelTab ?? 'by_age';
            if (! array_key_exists($activeTab, $tabDefs)) {
                $activeTab = 'by_age';
            }
        @endphp

        <ul class="nav nav-tabs" id="customerTabs" role="tablist">
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

        <div class="tab-content border border-top-0 bg-white" id="customerTabContent">
            @foreach($tabDefs as $tabKey => $tabMeta)
                @php $report = $tabMeta['report']; @endphp
                <div class="tab-pane fade {{ $activeTab === $tabKey ? 'show active' : '' }}" id="tab-{{ $tabKey }}" role="tabpanel">
                    @if(empty($report['has_data']))
                        <div class="p-4 text-muted">No rows for this customer table.</div>
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
                                        <th class="text-end">Total Sales</th>
                                        <th class="text-end">% of Total Sales</th>
                                        <th class="text-end">Total Releases</th>
                                        <th class="text-end">% of Total Releases</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($report['rows'] as $row)
                                        <tr>
                                            <td>{{ $row['rank'] }}</td>
                                            <td class="fw-semibold">{{ $row['label'] }}</td>
                                            <td class="text-end">{{ number_format((int) $row['sales_count']) }}</td>
                                            <td class="text-end">{{ number_format((float) $row['sales_pct'], 1) }}%</td>
                                            <td class="text-end">{{ number_format((int) $row['release_count']) }}</td>
                                            <td class="text-end">{{ number_format((float) $row['release_pct'], 1) }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th>—</th>
                                        <th>TOTAL</th>
                                        <th class="text-end">{{ number_format((int) ($report['totals']['sales_count'] ?? 0)) }}</th>
                                        <th class="text-end">{{ number_format((float) ($report['totals']['sales_pct'] ?? 0), 1) }}%</th>
                                        <th class="text-end">{{ number_format((int) ($report['totals']['release_count'] ?? 0)) }}</th>
                                        <th class="text-end">{{ number_format((float) ($report['totals']['release_pct'] ?? 0), 1) }}%</th>
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
    document.querySelectorAll('#customerTabs [data-excel-tab]').forEach(btn => {
        btn.addEventListener('shown.bs.tab', function () {
            if (excelTabInput) {
                excelTabInput.value = btn.getAttribute('data-excel-tab') || 'by_age';
            }
        });
    });
});
</script>
@endsection
