@extends('layouts.app')

@section('title', 'Sales Agents Report - Car Empire Management System')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
<style>
    #addSalesAgentModal .modal-body,
    #editSalesAgentModal .modal-body {
        max-height: min(70vh, 640px);
        overflow-y: auto;
    }
    .ts-dropdown {
        z-index: 1065;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-user-tie me-2"></i>Sales Agents
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0 gap-2">
            @canPage('sales-agents', 'create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSalesAgentModal">
                <i class="fas fa-user-plus me-1"></i>Add sales agent
            </button>
            @endcanPage
            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
            <a href="{{ route('staff-reports.executive-agents') }}" class="btn btn-outline-primary">
                <i class="fas fa-user-shield me-1"></i>Executive Agents
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any() && (old('from_staff_report') || old('_staff_edit_submitted')))
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

    <p class="text-muted mb-3">Active sales agents with executive assignment. Earnings are summed from commission records: payment date when set, otherwise reservation date, otherwise the record date.</p>

    <div class="card mb-3">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('staff-reports.sales-agents') }}" id="staffSaReportFilter" class="row g-2 align-items-end flex-wrap">
                <div class="col-auto">
                    <label for="filter_period" class="form-label small mb-0">Period</label>
                    <select name="period" id="filter_period" class="form-select form-select-sm" style="min-width: 9rem;">
                        <option value="all" @selected(($period ?? 'all') === 'all')>All time</option>
                        <option value="day" @selected(($period ?? '') === 'day')>Day</option>
                        <option value="week" @selected(($period ?? '') === 'week')>Week</option>
                        <option value="month" @selected(($period ?? '') === 'month')>Month</option>
                        <option value="year" @selected(($period ?? '') === 'year')>Year</option>
                    </select>
                </div>
                <div class="col-auto" id="staffSaAnchorWrap">
                    <label for="filter_anchor" class="form-label small mb-0">Reference date</label>
                    <input type="date" name="anchor" id="filter_anchor" class="form-control form-control-sm" value="{{ ($anchor ?? now())->format('Y-m-d') }}">
                </div>
                <div class="col-auto">
                    <label for="filter_sort" class="form-label small mb-0">Sort</label>
                    <select name="sort" id="filter_sort" class="form-select form-select-sm" style="min-width: 12rem;">
                        <option value="earnings_desc" @selected(($sort ?? 'earnings_desc') === 'earnings_desc')>Earnings (high → low)</option>
                        <option value="earnings_asc" @selected(($sort ?? '') === 'earnings_asc')>Earnings (low → high)</option>
                        <option value="name_asc" @selected(($sort ?? '') === 'name_asc')>Name (A → Z)</option>
                        <option value="name_desc" @selected(($sort ?? '') === 'name_desc')>Name (Z → A)</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-filter me-1"></i>Apply</button>
                    <a href="{{ route('staff-reports.sales-agents') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>
            @if(($period ?? 'all') !== 'all')
                <p class="small text-muted mb-0 mt-2 mb-0">
                    <strong>View:</strong> {{ ucfirst($period ?? '') }} — <strong>{{ $periodLabel ?? '' }}</strong>
                </p>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th>Agent ID</th>
                            <th>Executive</th>
                            <th>Phone</th>
                            <th>Position</th>
                            <th class="text-end">
                                @if(($period ?? 'all') === 'all')
                                    Total earnings
                                @else
                                    <span class="d-inline-block text-end">Earnings<br><span class="small fw-normal" style="opacity: .85;">{{ $periodLabel ?? '' }}</span></span>
                                @endif
                            </th>
                            @canPage('sales-agents', 'update')
                            <th class="text-end" style="width: 1%;">Actions</th>
                            @endcanPage
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($agents as $agent)
                            <tr>
                                <td><strong>{{ $agent->name }}</strong></td>
                                <td><span class="badge bg-secondary">{{ $agent->sales_agent_id }}</span></td>
                                <td>{{ $agent->executiveAgent?->name ?? '—' }}</td>
                                <td>{{ $agent->phone ?: '—' }}</td>
                                <td>{{ $agent->position ?: '—' }}</td>
                                <td class="text-end fw-semibold">₱{{ number_format($agent->total_commission_earnings ?? 0, 2) }}</td>
                                @canPage('sales-agents', 'update')
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-staff-edit-sa" title="Edit"
                                        data-agent-id="{{ $agent->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                </td>
                                @endcanPage
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ auth()->check() && auth()->user()->canAccessPage('sales-agents', 'update') ? 7 : 6 }}" class="text-center text-muted py-4">No active sales agents found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@canPage('sales-agents', 'create')
{{-- Modal: add sales agent --}}
<div class="modal fade" id="addSalesAgentModal" tabindex="-1" aria-labelledby="addSalesAgentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('sales-agents.store') }}" id="staffAddSalesAgentForm" autocomplete="off">
                @csrf
                <input type="hidden" name="from_staff_report" value="1">
                <div class="modal-header">
                    <h5 class="modal-title" id="addSalesAgentModalLabel"><i class="fas fa-user-plus me-2"></i>Add sales agent</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @php
                        $modalStatus = old('from_staff_report') && !old('_staff_edit_submitted') ? old('status', 'active') : 'active';
                        $commissionTypeOld = old('from_staff_report') && !old('_staff_edit_submitted') ? old('commission_type', 'percentage') : 'percentage';
                    @endphp
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="modal_sa_name" class="form-label">Full name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="modal_sa_name" name="name" value="{{ old('from_staff_report') && !old('_staff_edit_submitted') ? old('name') : '' }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="modal_sa_phone" class="form-label">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="modal_sa_phone" name="phone" value="{{ old('from_staff_report') && !old('_staff_edit_submitted') ? old('phone') : '' }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="modal_executive_agent_id" class="form-label">Executive agent</label>
                            <select class="form-select @error('executive_agent_id') is-invalid @enderror" id="modal_executive_agent_id" name="executive_agent_id">
                                <option value="">— None —</option>
                                @foreach(($executives ?? collect()) as $exec)
                                    <option value="{{ $exec->id }}" @selected(old('from_staff_report') && !old('_staff_edit_submitted') && (string) old('executive_agent_id') === (string) $exec->id)>
                                        {{ $exec->name }} ({{ $exec->executive_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('executive_agent_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            <small class="text-muted">Type to search. Sales agent ID is assigned when you save.</small>
                        </div>
                        <div class="col-md-6">
                            <label for="modal_sa_position" class="form-label">Position</label>
                            <input type="text" class="form-control @error('position') is-invalid @enderror" id="modal_sa_position" name="position" value="{{ old('from_staff_report') && !old('_staff_edit_submitted') ? old('position', 'Sales Agent') : 'Sales Agent' }}">
                            @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="modal_sa_hire_date" class="form-label">Hire date</label>
                            <input type="date" class="form-control @error('hire_date') is-invalid @enderror" id="modal_sa_hire_date" name="hire_date" value="{{ old('from_staff_report') && !old('_staff_edit_submitted') ? old('hire_date') : '' }}">
                            @error('hire_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="modal_sa_status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="modal_sa_status" name="status" required>
                                <option value="active" @selected($modalStatus === 'active')>Active</option>
                                <option value="inactive" @selected($modalStatus === 'inactive')>Inactive</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="modal_commission_type" class="form-label">Commission <span class="text-danger">*</span></label>
                            <select class="form-select @error('commission_type') is-invalid @enderror" id="modal_commission_type" name="commission_type" required>
                                <option value="percentage" @selected($commissionTypeOld === 'percentage')>Percentage</option>
                                <option value="fixed_rate" @selected($commissionTypeOld === 'fixed_rate')>Fixed rate</option>
                                <option value="custom" @selected($commissionTypeOld === 'custom')>Custom</option>
                            </select>
                            @error('commission_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6" id="modal_commission_percentage_wrap">
                            <label for="modal_commission_rate_pct" class="form-label">Amount (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('commission_rate') is-invalid @enderror" id="modal_commission_rate_pct" name="commission_rate" value="{{ old('from_staff_report') && !old('_staff_edit_submitted') ? old('commission_rate') : '' }}" min="0" max="100" step="0.01" placeholder="e.g. 5.5">
                            @error('commission_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 d-none" id="modal_commission_fixed_wrap">
                            <label for="modal_commission_fixed_amt" class="form-label">Amount (₱) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('commission_fixed_amount') is-invalid @enderror" id="modal_commission_fixed_amt" name="commission_fixed_amount" value="{{ old('from_staff_report') && !old('_staff_edit_submitted') ? old('commission_fixed_amount') : '' }}" min="0" step="0.01" placeholder="Fixed amount in pesos">
                            @error('commission_fixed_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 d-none" id="modal_commission_custom_hint">
                            <p class="text-muted small mb-0">Custom: no commission amount is saved.</p>
                        </div>
                        <div class="col-12">
                            <label for="modal_sa_address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="modal_sa_address" name="address" rows="2">{{ old('from_staff_report') && !old('_staff_edit_submitted') ? old('address') : '' }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="modal_sa_emergency_name" class="form-label">Emergency contact name</label>
                            <input type="text" class="form-control @error('emergency_contact_name') is-invalid @enderror" id="modal_sa_emergency_name" name="emergency_contact_name" value="{{ old('from_staff_report') && !old('_staff_edit_submitted') ? old('emergency_contact_name') : '' }}">
                            @error('emergency_contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="modal_sa_emergency_phone" class="form-label">Emergency contact phone</label>
                            <input type="text" class="form-control @error('emergency_contact_phone') is-invalid @enderror" id="modal_sa_emergency_phone" name="emergency_contact_phone" value="{{ old('from_staff_report') && !old('_staff_edit_submitted') ? old('emergency_contact_phone') : '' }}">
                            @error('emergency_contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="modal_sa_notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="modal_sa_notes" name="notes" rows="2">{{ old('from_staff_report') && !old('_staff_edit_submitted') ? old('notes') : '' }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save agent</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcanPage

@canPage('sales-agents', 'update')
<div class="modal fade" id="editSalesAgentModal" tabindex="-1" aria-labelledby="editSalesAgentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="" id="editStaffSalesAgentForm" autocomplete="off">
                @csrf
                @method('PUT')
                <input type="hidden" name="from_staff_report" value="1">
                <input type="hidden" name="_staff_edit_submitted" value="1">
                <input type="hidden" name="staff_edit_sales_agent_id" id="editStaffSalesAgentIdHolder" value="{{ old('_staff_edit_submitted') ? old('staff_edit_sales_agent_id') : '' }}">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSalesAgentModalLabel"><i class="fas fa-user-edit me-2"></i>Edit sales agent</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @php
                        $editModalStatus = old('_staff_edit_submitted') ? old('status', 'active') : 'active';
                        $editCommissionTypeOld = old('_staff_edit_submitted') ? old('commission_type', 'percentage') : 'percentage';
                    @endphp
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_modal_sa_name" class="form-label">Full name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="edit_modal_sa_name" name="name" value="{{ old('_staff_edit_submitted') ? old('name') : '' }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="edit_modal_sa_phone" class="form-label">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="edit_modal_sa_phone" name="phone" value="{{ old('_staff_edit_submitted') ? old('phone') : '' }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="edit_modal_sa_sales_agent_id" class="form-label">Sales agent ID</label>
                            <input type="text" class="form-control bg-light" id="edit_modal_sa_sales_agent_id" name="sales_agent_id" value="{{ old('_staff_edit_submitted') ? old('sales_agent_id') : '' }}" readonly>
                        </div>
                        <div class="col-12">
                            <label for="edit_modal_executive_agent_id" class="form-label">Executive agent</label>
                            <select class="form-select @error('executive_agent_id') is-invalid @enderror" id="edit_modal_executive_agent_id" name="executive_agent_id">
                                <option value="">— None —</option>
                                @foreach(($executives ?? collect()) as $exec)
                                    <option value="{{ $exec->id }}" @selected(old('_staff_edit_submitted') && (string) old('executive_agent_id') === (string) $exec->id)>
                                        {{ $exec->name }} ({{ $exec->executive_code }})
                                    </option>
                                @endforeach
                            </select>
                            @error('executive_agent_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            <small class="text-muted">Type to search the list.</small>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_modal_sa_department" class="form-label">Department</label>
                            <input type="text" class="form-control @error('department') is-invalid @enderror" id="edit_modal_sa_department" name="department" value="{{ old('_staff_edit_submitted') ? old('department') : '' }}" placeholder="Optional">
                            @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="edit_modal_sa_position" class="form-label">Position</label>
                            <input type="text" class="form-control @error('position') is-invalid @enderror" id="edit_modal_sa_position" name="position" value="{{ old('_staff_edit_submitted') ? old('position', 'Sales Agent') : 'Sales Agent' }}">
                            @error('position')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="edit_modal_sa_hire_date" class="form-label">Hire date</label>
                            <input type="date" class="form-control @error('hire_date') is-invalid @enderror" id="edit_modal_sa_hire_date" name="hire_date" value="{{ old('_staff_edit_submitted') ? old('hire_date') : '' }}">
                            @error('hire_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="edit_modal_sa_status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="edit_modal_sa_status" name="status" required>
                                <option value="active" @selected($editModalStatus === 'active')>Active</option>
                                <option value="inactive" @selected($editModalStatus === 'inactive')>Inactive</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="edit_modal_commission_type" class="form-label">Commission <span class="text-danger">*</span></label>
                            <select class="form-select @error('commission_type') is-invalid @enderror" id="edit_modal_commission_type" name="commission_type" required>
                                <option value="percentage" @selected($editCommissionTypeOld === 'percentage')>Percentage</option>
                                <option value="fixed_rate" @selected($editCommissionTypeOld === 'fixed_rate')>Fixed rate</option>
                                <option value="custom" @selected($editCommissionTypeOld === 'custom')>Custom</option>
                            </select>
                            @error('commission_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6" id="edit_modal_commission_percentage_wrap">
                            <label for="edit_modal_commission_rate_pct" class="form-label">Amount (%) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('commission_rate') is-invalid @enderror" id="edit_modal_commission_rate_pct" name="commission_rate" value="{{ old('_staff_edit_submitted') ? old('commission_rate') : '' }}" min="0" max="100" step="0.01">
                            @error('commission_rate')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 d-none" id="edit_modal_commission_fixed_wrap">
                            <label for="edit_modal_commission_fixed_amt" class="form-label">Amount (₱) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('commission_fixed_amount') is-invalid @enderror" id="edit_modal_commission_fixed_amt" name="commission_fixed_amount" value="{{ old('_staff_edit_submitted') ? old('commission_fixed_amount') : '' }}" min="0" step="0.01">
                            @error('commission_fixed_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 d-none" id="edit_modal_commission_custom_hint">
                            <p class="text-muted small mb-0">Custom: no commission amount is saved.</p>
                        </div>
                        <div class="col-12">
                            <label for="edit_modal_sa_address" class="form-label">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" id="edit_modal_sa_address" name="address" rows="2">{{ old('_staff_edit_submitted') ? old('address') : '' }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="edit_modal_sa_emergency_name" class="form-label">Emergency contact name</label>
                            <input type="text" class="form-control @error('emergency_contact_name') is-invalid @enderror" id="edit_modal_sa_emergency_name" name="emergency_contact_name" value="{{ old('_staff_edit_submitted') ? old('emergency_contact_name') : '' }}">
                            @error('emergency_contact_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="edit_modal_sa_emergency_phone" class="form-label">Emergency contact phone</label>
                            <input type="text" class="form-control @error('emergency_contact_phone') is-invalid @enderror" id="edit_modal_sa_emergency_phone" name="emergency_contact_phone" value="{{ old('_staff_edit_submitted') ? old('emergency_contact_phone') : '' }}">
                            @error('emergency_contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="edit_modal_sa_notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="edit_modal_sa_notes" name="notes" rows="2">{{ old('_staff_edit_submitted') ? old('notes') : '' }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Update agent</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcanPage
@endsection

@section('scripts')
<script>
(function() {
    var form = document.getElementById('staffSaReportFilter');
    if (!form) return;
    var period = document.getElementById('filter_period');
    var anchor = document.getElementById('filter_anchor');
    var wrap = document.getElementById('staffSaAnchorWrap');
    function syncAnchorState() {
        if (!period || !anchor) return;
        var all = period.value === 'all';
        anchor.disabled = all;
        if (wrap) wrap.classList.toggle('opacity-50', all);
    }
    if (period) {
        period.addEventListener('change', syncAnchorState);
        syncAnchorState();
    }
})();
</script>
@php
    $staffSaFormScripts = auth()->check() && (
        auth()->user()->canAccessPage('sales-agents', 'create') || auth()->user()->canAccessPage('sales-agents', 'update')
    );
@endphp
@if ($staffSaFormScripts)
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
(function() {
    var STAFF_SALES_AGENTS = @json($agentsJson ?? []);
    var SALES_AGENT_UPDATE_BASE = @json(rtrim(url('sales-agents'), '/'));

    var modalAdd = document.getElementById('addSalesAgentModal');
    var modalEdit = document.getElementById('editSalesAgentModal');
    var execSelectAdd = document.getElementById('modal_executive_agent_id');
    var execSelectEdit = document.getElementById('edit_modal_executive_agent_id');
    var executiveTomSelectAdd = null;
    var executiveTomSelectEdit = null;

    function destroyTomSelect(ts) {
        if (ts) {
            try { ts.destroy(); } catch (e) {}
        }
    }

    function initExecutiveTomSelect(selectEl) {
        if (!selectEl || typeof TomSelect === 'undefined') return null;
        return new TomSelect(selectEl, {
            create: false,
            allowEmptyOption: true,
            placeholder: 'Search or select executive…',
            sortField: { field: 'text', direction: 'asc' },
            dropdownParent: 'body',
        });
    }

    function syncStaffCommissionFields(isEdit) {
        var typeEl = document.getElementById(isEdit ? 'edit_modal_commission_type' : 'modal_commission_type');
        var pfx = isEdit ? 'edit_modal_' : 'modal_';
        var pctWrap = document.getElementById(pfx + 'commission_percentage_wrap');
        var fixedWrap = document.getElementById(pfx + 'commission_fixed_wrap');
        var hint = document.getElementById(pfx + 'commission_custom_hint');
        var pctInput = document.getElementById(pfx + 'commission_rate_pct');
        var fixedInput = document.getElementById(pfx + 'commission_fixed_amt');
        if (!typeEl || !pctWrap || !fixedWrap || !hint || !pctInput || !fixedInput) return;
        var type = typeEl.value;
        if (type === 'percentage') {
            pctWrap.classList.remove('d-none');
            fixedWrap.classList.add('d-none');
            hint.classList.add('d-none');
            pctInput.disabled = false;
            fixedInput.disabled = true;
            fixedInput.value = '';
        } else if (type === 'fixed_rate') {
            pctWrap.classList.add('d-none');
            fixedWrap.classList.remove('d-none');
            hint.classList.add('d-none');
            pctInput.disabled = true;
            fixedInput.disabled = false;
            pctInput.value = '';
        } else {
            pctWrap.classList.add('d-none');
            fixedWrap.classList.add('d-none');
            hint.classList.remove('d-none');
            pctInput.disabled = true;
            fixedInput.disabled = true;
            pctInput.value = '';
            fixedInput.value = '';
        }
    }

    var commissionTypeEl = document.getElementById('modal_commission_type');
    if (commissionTypeEl) {
        commissionTypeEl.addEventListener('change', function() { syncStaffCommissionFields(false); });
    }
    var editCommissionTypeEl = document.getElementById('edit_modal_commission_type');
    if (editCommissionTypeEl) {
        editCommissionTypeEl.addEventListener('change', function() { syncStaffCommissionFields(true); });
    }

    function readNativeSelectValue(selectEl) {
        if (!selectEl) return '';
        if (selectEl.value) return String(selectEl.value);
        var opt = selectEl.querySelector('option:checked');
        return opt && opt.value ? String(opt.value) : '';
    }

    if (modalAdd && execSelectAdd) {
        modalAdd.addEventListener('shown.bs.modal', function() {
            destroyTomSelect(executiveTomSelectAdd);
            executiveTomSelectAdd = null;
            executiveTomSelectAdd = initExecutiveTomSelect(execSelectAdd);
            syncStaffCommissionFields(false);
        });
        modalAdd.addEventListener('hidden.bs.modal', function() {
            destroyTomSelect(executiveTomSelectAdd);
            executiveTomSelectAdd = null;
            var form = document.getElementById('staffAddSalesAgentForm');
            @if (!$errors->any() || !old('from_staff_report') || old('_staff_edit_submitted'))
            if (form) form.reset();
            syncStaffCommissionFields(false);
            @endif
        });
    }

    if (modalEdit && execSelectEdit) {
        modalEdit.addEventListener('shown.bs.modal', function() {
            destroyTomSelect(executiveTomSelectEdit);
            executiveTomSelectEdit = null;
            executiveTomSelectEdit = initExecutiveTomSelect(execSelectEdit);
            syncStaffCommissionFields(true);
            if (executiveTomSelectEdit && execSelectEdit) {
                var v = readNativeSelectValue(execSelectEdit);
                if (v) executiveTomSelectEdit.setValue(v, true);
                else executiveTomSelectEdit.clear(true);
            }
        });
        modalEdit.addEventListener('hidden.bs.modal', function() {
            destroyTomSelect(executiveTomSelectEdit);
            executiveTomSelectEdit = null;
            var form = document.getElementById('editStaffSalesAgentForm');
            @if (!$errors->any() || !old('_staff_edit_submitted'))
            if (form) {
                form.reset();
                form.setAttribute('action', '');
            }
            var hid = document.getElementById('editStaffSalesAgentIdHolder');
            if (hid) hid.value = '';
            syncStaffCommissionFields(true);
            @endif
        });
    }

    document.querySelectorAll('.btn-staff-edit-sa').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var id = this.getAttribute('data-agent-id');
            var ag = STAFF_SALES_AGENTS.find(function(a) { return String(a.id) === String(id); });
            if (!ag || !modalEdit) return;
            var form = document.getElementById('editStaffSalesAgentForm');
            if (!form) return;
            form.setAttribute('action', SALES_AGENT_UPDATE_BASE + '/' + ag.id);
            var hid = document.getElementById('editStaffSalesAgentIdHolder');
            if (hid) hid.value = ag.id;
            document.getElementById('edit_modal_sa_name').value = ag.name || '';
            document.getElementById('edit_modal_sa_phone').value = ag.phone || '';
            document.getElementById('edit_modal_sa_sales_agent_id').value = ag.sales_agent_id || '';
            document.getElementById('edit_modal_sa_department').value = ag.department || '';
            document.getElementById('edit_modal_sa_position').value = ag.position || 'Sales Agent';
            document.getElementById('edit_modal_sa_hire_date').value = ag.hire_date || '';
            document.getElementById('edit_modal_sa_status').value = ag.status || 'active';
            document.getElementById('edit_modal_commission_type').value = ag.commission_type || 'percentage';
            document.getElementById('edit_modal_commission_rate_pct').value = ag.commission_rate != null ? ag.commission_rate : '';
            document.getElementById('edit_modal_commission_fixed_amt').value = ag.commission_fixed_amount != null ? ag.commission_fixed_amount : '';
            document.getElementById('edit_modal_sa_address').value = ag.address || '';
            document.getElementById('edit_modal_sa_emergency_name').value = ag.emergency_contact_name || '';
            document.getElementById('edit_modal_sa_emergency_phone').value = ag.emergency_contact_phone || '';
            document.getElementById('edit_modal_sa_notes').value = ag.notes || '';
            var exSel = document.getElementById('edit_modal_executive_agent_id');
            if (exSel) exSel.value = ag.executive_agent_id ? String(ag.executive_agent_id) : '';
            bootstrap.Modal.getOrCreateInstance(modalEdit).show();
        });
    });

    @if ($errors->any() && old('from_staff_report') && !old('_staff_edit_submitted'))
    document.addEventListener('DOMContentLoaded', function() {
        syncStaffCommissionFields(false);
        if (modalAdd && execSelectAdd) {
            destroyTomSelect(executiveTomSelectAdd);
            executiveTomSelectAdd = initExecutiveTomSelect(execSelectAdd);
            if (executiveTomSelectAdd) {
                var v = @json(old('executive_agent_id'));
                if (v !== null && v !== '') executiveTomSelectAdd.setValue(String(v), true);
                else executiveTomSelectAdd.clear(true);
            }
            bootstrap.Modal.getOrCreateInstance(modalAdd).show();
        }
    });
    @endif

    @if ($errors->any() && old('_staff_edit_submitted'))
    document.addEventListener('DOMContentLoaded', function() {
        syncStaffCommissionFields(true);
        var form = document.getElementById('editStaffSalesAgentForm');
        var rid = @json(old('staff_edit_sales_agent_id'));
        if (form && rid) form.setAttribute('action', SALES_AGENT_UPDATE_BASE + '/' + rid);
        if (modalEdit && execSelectEdit) {
            destroyTomSelect(executiveTomSelectEdit);
            executiveTomSelectEdit = initExecutiveTomSelect(execSelectEdit);
            if (executiveTomSelectEdit) {
                var ev = @json(old('executive_agent_id'));
                if (ev !== null && ev !== '') executiveTomSelectEdit.setValue(String(ev), true);
                else executiveTomSelectEdit.clear(true);
            }
            bootstrap.Modal.getOrCreateInstance(modalEdit).show();
        }
    });
    @endif
})();
</script>
@endif
@endsection
