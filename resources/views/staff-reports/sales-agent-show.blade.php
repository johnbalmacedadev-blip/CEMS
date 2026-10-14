@extends('layouts.app')

@section('title', $agent->name . ' - Sales Agent - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-user-tie me-2"></i>{{ $agent->name }}
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0 gap-2">
            <a href="{{ route('staff-reports.sales-agents', $listQuery ?? []) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Sales Agents
            </a>
        </div>
    </div>

    <div class="alert alert-{{ $agent->status === 'active' ? 'success' : 'secondary' }} d-flex flex-wrap align-items-center gap-3 mb-4" role="alert">
        <span>
            <i class="fas fa-{{ $agent->status === 'active' ? 'check-circle' : 'pause-circle' }} me-1"></i>
            <strong>Status: {{ ucfirst($agent->status) }}</strong>
        </span>
        @if($agent->sales_agent_id)
            <span><i class="fas fa-id-badge me-1"></i>{{ $agent->sales_agent_id }}</span>
        @endif
        @if($agent->executiveAgent)
            <span><i class="fas fa-user-shield me-1"></i>{{ $agent->executiveAgent->name }}</span>
        @endif
    </div>

    <div class="row mb-4">
        <div class="col-lg-4 mb-3 mb-lg-0">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-user me-2"></i>Agent details</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-muted small">Phone</div>
                            <div>{{ $agent->phone ?: '—' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Position</div>
                            <div>{{ $agent->position ?: '—' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Department</div>
                            <div>{{ $agent->department ?: '—' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="text-muted small">Hire date</div>
                            <div>{{ $agent->hire_date?->format('M j, Y') ?: '—' }}</div>
                        </div>
                        <div class="col-12">
                            <div class="text-muted small">Commission type</div>
                            <div>{{ $agent->formatted_commission_rate }}</div>
                        </div>
                        @if($agent->notes)
                            <div class="col-12">
                                <div class="text-muted small">Notes</div>
                                <div>{{ $agent->notes }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="card-title mb-0"><i class="fas fa-coins me-2"></i>Earnings</h5>
                    <div class="fs-5 fw-semibold text-success">₱{{ number_format($earningsTotal ?? 0, 2) }}</div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('staff-reports.sales-agents.show', $agent) }}" class="row g-2 align-items-end">
                        @foreach(($listQuery ?? []) as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <div class="col-sm-4 col-md-3">
                            <label for="date_from" class="form-label small mb-0">From</label>
                            <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ $dateFrom ?? '' }}">
                        </div>
                        <div class="col-sm-4 col-md-3">
                            <label for="date_to" class="form-label small mb-0">To</label>
                            <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ $dateTo ?? '' }}">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i>Apply</button>
                            <a href="{{ route('staff-reports.sales-agents.show', $agent) }}" class="btn btn-outline-secondary btn-sm">All time</a>
                        </div>
                    </form>
                    <p class="small text-muted mb-0 mt-2">
                        @if($hasDateFilter && $dateFrom && $dateTo)
                            Showing commissions dated <strong>{{ \Carbon\Carbon::parse($dateFrom)->format('M j, Y') }}</strong>
                            – <strong>{{ \Carbon\Carbon::parse($dateTo)->format('M j, Y') }}</strong>
                            (payment date, else date sent, else record date).
                        @else
                            Showing all commission earnings for this agent.
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-list me-2"></i>Commission records</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Showroom</th>
                            <th>Plate</th>
                            <th>Unit</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commissions as $row)
                            @php
                                $earnDate = $row->date_of_payment ?? $row->date_sent ?? $row->created_at;
                            @endphp
                            <tr>
                                <td>{{ $earnDate ? \Carbon\Carbon::parse($earnDate)->format('M j, Y') : '—' }}</td>
                                <td>{{ $row->showroom ?: '—' }}</td>
                                <td>
                                    @if($row->plate_number)
                                        <span class="badge bg-secondary">{{ $row->plate_number }}</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ $row->unit ?: '—' }}</td>
                                <td>{{ $row->transaction_type ?: '—' }}</td>
                                <td>
                                    @if($row->commission_status)
                                        <span class="badge bg-{{ strtolower($row->commission_status) === 'posted' ? 'success' : 'warning' }}">
                                            {{ $row->commission_status }}
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="text-end fw-semibold">₱{{ number_format((float) $row->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">No commission records for this date range.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($commissions->hasPages())
            <div class="card-footer">
                {{ $commissions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
