@extends('layouts.app')

@section('title', 'Transfer OR/CR Summary Report - Car Empire Management System')

@section('content')
<div class="container-fluid transfer-orcr-summary">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-chart-bar me-2"></i>Transfer OR/CR — Summary Report</h1>
        <a href="{{ route('transfer-orcr.index', request()->only(['branch_location_id'])) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to List
        </a>
    </div>

    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('transfer-orcr.summary-report') }}" class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label small mb-0">Branch / Store Location</label>
                    <select class="form-select form-select-sm" name="branch_location_id" style="min-width: 160px;">
                        <option value="">All branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ (string) request('branch_location_id') === (string) $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label small mb-0">Calendar month (weekly view)</label>
                    <input type="month" class="form-control form-control-sm" name="month" value="{{ $monthInput }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-sync me-1"></i>Update Report</button>
                </div>
            </form>
            @if($branchName)
                <p class="small text-muted mb-0 mt-2">Filtered by branch: <strong>{{ $branchName }}</strong></p>
            @endif
        </div>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-md-3">
            <div class="card border-danger h-100">
                <div class="card-body py-2 text-center">
                    <div class="small text-muted">Pending now</div>
                    <div class="fs-4 fw-bold text-danger">{{ $currentPending }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success h-100">
                <div class="card-body py-2 text-center">
                    <div class="small text-muted">Done (all time)</div>
                    <div class="fs-4 fw-bold text-success">{{ $totalDone }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body py-2 text-center">
                    <div class="small text-muted">New in {{ $reportMonth->format('F Y') }}</div>
                    <div class="fs-4 fw-bold">{{ $newInMonth }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card h-100">
                <div class="card-body py-2 text-center">
                    <div class="small text-muted">Done in {{ $reportMonth->format('F Y') }}</div>
                    <div class="fs-4 fw-bold">{{ $doneInMonth }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <h2 class="h6 text-danger mb-2">Pending transfer by month</h2>
            <p class="small text-muted mb-2">Count of records still <strong>Pending</strong> or <strong>In Progress</strong>, grouped by transaction date (year-month).</p>
            <div class="summary-table-wrap mb-4">
                <table class="summary-table summary-pending">
                    <thead>
                        <tr>
                            <th>DATE - Year-Month</th>
                            <th class="text-end">Pending Transfer as of {{ $asOf->format('F d, Y') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingByMonth['items'] as $row)
                            <tr>
                                <td>{{ $row['label'] }}</td>
                                <td class="text-end">{{ $row['count'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted">No pending records</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Grand Total</th>
                            <th class="text-end">{{ $pendingByMonth['grandTotal'] }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <h2 class="h6 text-success mb-2">Done transfer by month</h2>
            <p class="small text-muted mb-2">Count of <strong>DONE</strong> records grouped by transaction date (year-month).</p>
            <div class="summary-table-wrap">
                <table class="summary-table summary-done">
                    <thead>
                        <tr>
                            <th>DATE - Year-Month</th>
                            <th class="text-end">Done Transfer as of {{ $asOf->format('F d, Y') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($doneByMonth['items'] as $row)
                            <tr>
                                <td>{{ $row['label'] }}</td>
                                <td class="text-end">{{ $row['count'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted">No done records</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>Grand Total</th>
                            <th class="text-end">{{ $doneByMonth['grandTotal'] }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="col-lg-7">
            <h2 class="h6 mb-2">{{ $reportMonth->format('F Y') }} — Transfers per day &amp; per week</h2>
            <p class="small text-muted mb-2">All new transfer records by transaction date. Weekly column shows the total for that week.</p>
            <div class="summary-calendar-wrap mb-4">
                <table class="summary-calendar">
                    <thead>
                        <tr>
                            <th class="day-sun">SUNDAY</th>
                            <th class="day-week">MONDAY</th>
                            <th class="day-week">TUESDAY</th>
                            <th class="day-week">WEDNESDAY</th>
                            <th class="day-week">THURSDAY</th>
                            <th class="day-week">FRIDAY</th>
                            <th class="day-sat">SATURDAY</th>
                            <th class="day-total">TOTAL TRANSFER PER WEEK</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($calendarWeeks as $week)
                            <tr class="calendar-dates">
                                @foreach($week['days'] as $day)
                                    <td>
                                        @if($day['date'])
                                            {{ $day['date']->format('F d, Y') }}
                                        @endif
                                    </td>
                                @endforeach
                                <td rowspan="2" class="cell-week-total align-middle">{{ $week['total'] }}</td>
                            </tr>
                            <tr class="calendar-counts">
                                @foreach($week['days'] as $day)
                                    <td>
                                        @if($day['date'])
                                            {{ $day['count'] > 0 ? $day['count'] : '—' }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <h2 class="h6 text-success mb-2">Done transfers per week — {{ $reportMonth->format('F Y') }}</h2>
            <div class="table-responsive mb-3">
                <table class="table table-sm table-bordered summary-weekly-done mb-0">
                    <thead class="table-success">
                        <tr>
                            <th>Week</th>
                            <th class="text-end">Done count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($doneWeeklyInMonth as $week)
                            <tr>
                                <td>{{ $week['label'] }}</td>
                                <td class="text-end fw-semibold">{{ $week['total'] }}</td>
                            </tr>
                        @endforeach
                        <tr class="table-light fw-bold">
                            <td>Month total</td>
                            <td class="text-end">{{ $doneInMonth }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="summary-narrative mt-2">
        From {{ $openingPending }} pending, plus {{ $newInMonth }} new for transfer,
        @if($doneInMonth > 0)
            − {{ $doneInMonth }} done transfer,
        @else
            − done transfer,
        @endif
        {{ $currentPending }} pendings as of {{ $asOf->format('F d, Y') }}
    </div>
</div>
@endsection

@section('styles')
<style>
.transfer-orcr-summary .summary-table-wrap {
    overflow-x: auto;
    max-height: 420px;
    overflow-y: auto;
}
.transfer-orcr-summary .summary-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.9rem;
}
.transfer-orcr-summary .summary-table th,
.transfer-orcr-summary .summary-table td {
    border: 1px solid #ccc;
    padding: 0.45rem 0.65rem;
}
.transfer-orcr-summary .summary-pending thead th {
    background: #c00000;
    color: #fff;
    font-weight: bold;
}
.transfer-orcr-summary .summary-pending thead th:first-child {
    font-style: italic;
}
.transfer-orcr-summary .summary-pending tbody td {
    background: #fce4d6;
}
.transfer-orcr-summary .summary-pending tfoot th {
    background: #c00000;
    color: #fff;
    font-weight: bold;
}
.transfer-orcr-summary .summary-done thead th:first-child {
    background: #375623;
    color: #fff;
    font-style: italic;
    font-weight: bold;
}
.transfer-orcr-summary .summary-done thead th:last-child {
    background: #92d050;
    color: #000;
    font-weight: bold;
}
.transfer-orcr-summary .summary-done tbody td {
    background: #e2efda;
}
.transfer-orcr-summary .summary-done tfoot th {
    background: #92d050;
    color: #000;
    font-weight: bold;
}
.transfer-orcr-summary .summary-calendar {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.75rem;
    text-align: center;
}
.transfer-orcr-summary .summary-calendar th,
.transfer-orcr-summary .summary-calendar td {
    border: 1px solid #999;
    padding: 0.35rem 0.2rem;
    vertical-align: middle;
}
.transfer-orcr-summary .summary-calendar .day-sun,
.transfer-orcr-summary .summary-calendar .day-sat {
    background: #ed7d31;
    color: #fff;
    font-weight: bold;
}
.transfer-orcr-summary .summary-calendar .day-week {
    background: #70ad47;
    color: #fff;
    font-weight: bold;
}
.transfer-orcr-summary .summary-calendar .day-total {
    background: #203864;
    color: #fff;
    font-weight: bold;
    font-size: 0.65rem;
    min-width: 72px;
}
.transfer-orcr-summary .calendar-dates td {
    background: #ffff00;
    font-weight: 500;
    font-size: 0.7rem;
}
.transfer-orcr-summary .calendar-counts td {
    background: #fff2cc;
}
.transfer-orcr-summary .calendar-counts .cell-week-total,
.transfer-orcr-summary .summary-calendar .cell-week-total {
    background: #bdd7ee;
    font-weight: bold;
    font-size: 1rem;
}
.transfer-orcr-summary .summary-narrative {
    background: #ed7d31;
    color: #000;
    font-weight: bold;
    font-size: 1.1rem;
    padding: 1rem 1.25rem;
    text-align: center;
    border: 1px solid #c55a11;
}
</style>
@endsection
