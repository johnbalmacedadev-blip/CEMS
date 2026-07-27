@extends('layouts.app')

@section('title', 'Executive Agents Report - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-user-shield me-2"></i>Executive Agents
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0 gap-2">
            @canPage('staff-reports.executive-agents', 'create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addExecutiveAgentModal">
                <i class="fas fa-user-plus me-1"></i>Add executive agent
            </button>
            @endcanPage
            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
            <a href="{{ route('staff-reports.sales-agents') }}" class="btn btn-outline-primary">
                <i class="fas fa-user-tie me-1"></i>Sales Agents
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any() && old('from_executive_staff_report'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Please fix the following:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <p class="text-muted mb-4">Executive agents and their teams. Per agent, <strong>Executive commission</strong> is the sum of the “sales executive commission” field on that agent’s payout rows; <strong>Sales agent earnings</strong> is the agent’s commission total. The header totals match the sum of each column.</p>

    @forelse($executives as $executive)
        <div class="card mb-4">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="mb-0">
                        <a href="{{ route('staff-reports.executive-agents.show', $executive) }}" class="text-decoration-none text-dark">
                            <i class="fas fa-user-shield me-2 text-primary"></i>{{ $executive->name }}
                        </a>
                    </h5>
                    <small class="text-muted">{{ $executive->executive_code }} · {{ $executive->email ?: 'No email' }} · {{ $executive->phone ?: '—' }}</small>
                </div>
                <div class="text-end">
                    <div class="small text-muted">Executive commission (own)</div>
                    <div class="h5 mb-1 text-primary">₱{{ number_format($executive->executive_own_earnings ?? 0, 2) }}</div>
                    <div class="small text-muted">Team total earnings</div>
                    <div class="h5 mb-0 text-success">₱{{ number_format($executive->team_total_earnings ?? 0, 2) }}</div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Sales agent</th>
                                <th>Agent ID</th>
                                <th class="text-end">Executive commission</th>
                                <th class="text-end">Sales agent earnings</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($executive->salesAgents as $sa)
                                <tr>
                                    <td>{{ $sa->name }}</td>
                                    <td><span class="badge bg-secondary">{{ $sa->sales_agent_id }}</span></td>
                                    <td class="text-end fw-semibold text-primary">₱{{ number_format($sa->staff_report_executive_commission ?? 0, 2) }}</td>
                                    <td class="text-end fw-semibold">₱{{ number_format($sa->staff_report_sales_agent_earnings ?? 0, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-muted text-center py-3">No sales agents assigned to this executive yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>No executive agents found yet. Use <strong>Add executive agent</strong> above (if you have permission) or run seeders.
        </div>
    @endforelse
</div>

@canPage('staff-reports.executive-agents', 'create')
<div class="modal fade" id="addExecutiveAgentModal" tabindex="-1" aria-labelledby="addExecutiveAgentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('staff-reports.executive-agents.store') }}" id="staffAddExecutiveAgentForm" autocomplete="off">
                @csrf
                <input type="hidden" name="from_executive_staff_report" value="1">
                <div class="modal-header">
                    <h5 class="modal-title" id="addExecutiveAgentModalLabel"><i class="fas fa-user-plus me-2"></i>Add executive agent</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @php
                        $execModalStatus = old('from_executive_staff_report') ? old('status', 'active') : 'active';
                    @endphp
                    <p class="text-muted small">Executive code (e.g. EA001) is assigned automatically when you save.</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="modal_ea_name" class="form-label">Full name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="modal_ea_name" name="name" value="{{ old('from_executive_staff_report') ? old('name') : '' }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="modal_ea_phone" class="form-label">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="modal_ea_phone" name="phone" value="{{ old('from_executive_staff_report') ? old('phone') : '' }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="modal_ea_email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="modal_ea_email" name="email" value="{{ old('from_executive_staff_report') ? old('email') : '' }}" placeholder="Optional">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="modal_ea_department" class="form-label">Department</label>
                            <input type="text" class="form-control @error('department') is-invalid @enderror" id="modal_ea_department" name="department" value="{{ old('from_executive_staff_report') ? old('department') : '' }}" placeholder="Optional">
                            @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="modal_ea_status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="modal_ea_status" name="status" required>
                                <option value="active" @selected($execModalStatus === 'active')>Active</option>
                                <option value="inactive" @selected($execModalStatus === 'inactive')>Inactive</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="modal_ea_notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="modal_ea_notes" name="notes" rows="3" placeholder="Optional">{{ old('from_executive_staff_report') ? old('notes') : '' }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save executive</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcanPage
@endsection

@section('scripts')
@canPage('staff-reports.executive-agents', 'create')
<script>
(function() {
    var modalEl = document.getElementById('addExecutiveAgentModal');
    if (!modalEl) return;

    modalEl.addEventListener('hidden.bs.modal', function() {
        var form = document.getElementById('staffAddExecutiveAgentForm');
        @if (!$errors->any() || !old('from_executive_staff_report'))
        if (form) form.reset();
        @endif
    });

    @if ($errors->any() && old('from_executive_staff_report'))
    document.addEventListener('DOMContentLoaded', function() {
        var m = bootstrap.Modal.getOrCreateInstance(modalEl);
        m.show();
    });
    @endif
})();
</script>
@endcanPage
@endsection
