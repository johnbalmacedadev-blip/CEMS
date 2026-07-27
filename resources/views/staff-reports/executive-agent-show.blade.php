@extends('layouts.app')

@section('title', $executive->name . ' - Executive Agent - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-user-shield me-2"></i>{{ $executive->name }}
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0 gap-2">
            <a href="{{ route('staff-reports.executive-agents') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Executive Agents
            </a>
            <a href="{{ route('client-follow-up-list.index', ['executive_agent_id' => $executive->id]) }}" class="btn btn-outline-primary">
                <i class="fas fa-user-friends me-1"></i>View Clients ({{ number_format($clientLeadCount) }})
            </a>
        </div>
    </div>

    <div class="alert alert-{{ $executive->status === 'active' ? 'success' : 'secondary' }} d-flex align-items-center mb-4" role="alert">
        <i class="fas fa-{{ $executive->status === 'active' ? 'check-circle' : 'pause-circle' }} me-2"></i>
        <strong>Status: {{ ucfirst($executive->status) }}</strong>
        @if($executive->executive_code)
            <span class="ms-3"><i class="fas fa-id-badge me-1"></i>{{ $executive->executive_code }}</span>
        @endif
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-user me-2"></i>Account Details</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted small">Full Name</div>
                            <div class="fw-semibold">{{ $executive->name }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Executive Code</div>
                            <div class="fw-semibold">{{ $executive->executive_code ?: '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Email</div>
                            <div>{{ $executive->email ?: '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Phone</div>
                            <div>{{ $executive->phone ?: '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Department</div>
                            <div>{{ $executive->department ?: '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Client Leads</div>
                            <div>
                                <a href="{{ route('client-follow-up-list.index', ['executive_agent_id' => $executive->id]) }}">
                                    {{ number_format($clientLeadCount) }} clients
                                </a>
                            </div>
                        </div>
                        @if($executive->notes)
                            <div class="col-12">
                                <div class="text-muted small">Notes</div>
                                <div>{{ $executive->notes }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="fas fa-users me-2"></i>Sales Team</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Sales Agent</th>
                                    <th>Agent ID</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($executive->salesAgents as $agent)
                                    <tr>
                                        <td>
                                            <a href="{{ route('sales-agents.show', $agent) }}">{{ $agent->name }}</a>
                                        </td>
                                        <td><span class="badge bg-secondary">{{ $agent->sales_agent_id }}</span></td>
                                        <td>
                                            <span class="badge bg-{{ $agent->status === 'active' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($agent->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-muted text-center py-3">No sales agents assigned yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
