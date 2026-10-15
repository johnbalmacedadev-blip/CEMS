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
            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#importLeadsModal">
                <i class="fas fa-file-excel me-1"></i>Import Leads Excel
            </button>
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

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any() && (old('from_executive_staff_report') || old('from_leads_import')))
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

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Executive Agent</th>
                            <th class="text-center">Sales Agents</th>
                            <th class="text-end">Total Earnings</th>
                            <th class="text-end">Total Leads</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($executives as $executive)
                            @php
                                $teamPayload = $executive->salesAgents->map(function ($sa) {
                                    return [
                                        'name' => $sa->name,
                                        'sales_agent_id' => $sa->sales_agent_id,
                                        'exec_commission' => (float) ($sa->staff_report_executive_commission ?? 0),
                                        'agent_earnings' => (float) ($sa->staff_report_sales_agent_earnings ?? 0),
                                    ];
                                })->values();
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $executive->name }}</div>
                                    <div class="small text-muted">
                                        {{ $executive->executive_code ?: '—' }}
                                        @if($executive->email) · {{ $executive->email }} @endif
                                        @if($executive->phone) · {{ $executive->phone }} @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary js-view-team"
                                        data-name="{{ $executive->name }}"
                                        data-team='@json($teamPayload)'
                                        data-exec-own="{{ number_format($executive->executive_own_earnings ?? 0, 2) }}"
                                        data-team-total="{{ number_format($executive->team_total_earnings ?? 0, 2) }}"
                                    >
                                        {{ number_format($executive->sales_agents_count ?? $executive->salesAgents->count()) }}
                                        <span class="ms-1">View</span>
                                    </button>
                                </td>
                                <td class="text-end fw-semibold text-success">
                                    ₱{{ number_format($executive->total_earnings ?? 0, 2) }}
                                    <div class="small text-muted fw-normal">
                                        Exec ₱{{ number_format($executive->executive_own_earnings ?? 0, 2) }}
                                        · Team ₱{{ number_format($executive->team_total_earnings ?? 0, 2) }}
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('staff-reports.executive-agents.leads', $executive) }}" class="text-decoration-none fw-semibold">
                                        {{ number_format($executive->masterlist_leads_count ?? 0) }}
                                    </a>
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('staff-reports.executive-agents.leads', $executive) }}" class="btn btn-sm btn-outline-primary" title="View Leads">
                                        <i class="fas fa-address-book"></i>
                                    </a>
                                    <a href="{{ route('staff-reports.executive-agents.show', $executive) }}" class="btn btn-sm btn-outline-secondary" title="Profile">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @canPage('staff-reports.executive-agents', 'update')
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-warning js-edit-executive"
                                        title="Edit account"
                                        data-id="{{ $executive->id }}"
                                        data-update-url="{{ route('staff-reports.executive-agents.update', $executive) }}"
                                        data-name="{{ $executive->name }}"
                                        data-email="{{ $executive->email }}"
                                        data-phone="{{ $executive->phone }}"
                                        data-code="{{ $executive->executive_code }}"
                                        data-department="{{ $executive->department }}"
                                        data-status="{{ $executive->status }}"
                                        data-notes="{{ $executive->notes }}"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @endcanPage
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-muted text-center py-4">
                                    No executive agents found yet. Use <strong>Add executive agent</strong> above (if you have permission) or run seeders.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Sales agents under executive --}}
<div class="modal fade" id="teamModal" tabindex="-1" aria-labelledby="teamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="teamModalLabel">
                    <i class="fas fa-users me-2"></i>Sales Agents — <span id="teamModalExecName"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex flex-wrap gap-3 mb-3 small text-muted">
                    <div>Executive commission: <strong class="text-primary" id="teamModalExecOwn">₱0.00</strong></div>
                    <div>Team earnings: <strong class="text-success" id="teamModalTeamTotal">₱0.00</strong></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-sm table-striped align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Sales agent</th>
                                <th>Agent ID</th>
                                <th class="text-end">Executive commission</th>
                                <th class="text-end">Sales agent earnings</th>
                            </tr>
                        </thead>
                        <tbody id="teamModalBody">
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

@canPage('staff-reports.executive-agents', 'update')
<div class="modal fade" id="editExecutiveAgentModal" tabindex="-1" aria-labelledby="editExecutiveAgentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" id="editExecutiveAgentForm" autocomplete="off">
                @csrf
                @method('PUT')
                <input type="hidden" name="from_executive_staff_report" value="1">
                <div class="modal-header">
                    <h5 class="modal-title" id="editExecutiveAgentModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit executive agent
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_ea_name" class="form-label">Full name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="edit_ea_name" name="name" value="{{ old('from_executive_staff_report') && session('edit_executive_id') ? old('name') : '' }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="edit_ea_code" class="form-label">Executive code</label>
                            <input type="text" class="form-control @error('executive_code') is-invalid @enderror" id="edit_ea_code" name="executive_code" value="{{ old('from_executive_staff_report') && session('edit_executive_id') ? old('executive_code') : '' }}">
                            @error('executive_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="edit_ea_phone" class="form-label">Phone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="edit_ea_phone" name="phone" value="{{ old('from_executive_staff_report') && session('edit_executive_id') ? old('phone') : '' }}">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="edit_ea_email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="edit_ea_email" name="email" value="{{ old('from_executive_staff_report') && session('edit_executive_id') ? old('email') : '' }}" placeholder="Optional">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="edit_ea_department" class="form-label">Department</label>
                            <input type="text" class="form-control @error('department') is-invalid @enderror" id="edit_ea_department" name="department" value="{{ old('from_executive_staff_report') && session('edit_executive_id') ? old('department') : '' }}" placeholder="Optional">
                            @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="edit_ea_status" class="form-label">Status <span class="text-danger">*</span></label>
                            @php $editStatus = old('from_executive_staff_report') && session('edit_executive_id') ? old('status', 'active') : 'active'; @endphp
                            <select class="form-select @error('status') is-invalid @enderror" id="edit_ea_status" name="status" required>
                                <option value="active" @selected($editStatus === 'active')>Active</option>
                                <option value="inactive" @selected($editStatus === 'inactive')>Inactive</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="edit_ea_notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="edit_ea_notes" name="notes" rows="3" placeholder="Optional">{{ old('from_executive_staff_report') && session('edit_executive_id') ? old('notes') : '' }}</textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1"></i>Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcanPage

@canPage('staff-reports.executive-agents', 'create')
<div class="modal fade" id="importLeadsModal" tabindex="-1" aria-labelledby="importLeadsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('staff-reports.executive-agents.import-leads') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="from_leads_import" value="1">
                <div class="modal-header">
                    <h5 class="modal-title" id="importLeadsModalLabel">
                        <i class="fas fa-file-excel me-2"></i>Import Leads Master List
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted">
                        Upload a workbook like <strong>JM SALES TEAM LEADS MASTERLIST.xlsx</strong>.
                        Existing leads for the selected executive are skipped; only missing rows are added.
                    </p>
                    <div class="mb-3">
                        <label for="import_executive_agent_id" class="form-label">Executive agent <span class="text-danger">*</span></label>
                        <select class="form-select @error('executive_agent_id') is-invalid @enderror" id="import_executive_agent_id" name="executive_agent_id" required>
                            <option value="">— Select executive —</option>
                            @foreach($executives as $exec)
                                <option value="{{ $exec->id }}" @selected((string) old('executive_agent_id') === (string) $exec->id)>
                                    {{ $exec->name }} ({{ $exec->executive_code }})
                                </option>
                            @endforeach
                        </select>
                        @error('executive_agent_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-0">
                        <label for="import_leads_file" class="form-label">Excel file (.xlsx) <span class="text-danger">*</span></label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" id="import_leads_file" name="file" accept=".xlsx,.xls" required>
                        @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-upload me-1"></i>Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
                        $execModalStatus = old('from_executive_staff_report') && !session('edit_executive_id') ? old('status', 'active') : 'active';
                    @endphp
                    <p class="text-muted small">Executive code (e.g. EA001) is assigned automatically when you save.</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="modal_ea_name" class="form-label">Full name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="modal_ea_name" name="name" value="{{ old('from_executive_staff_report') && !session('edit_executive_id') ? old('name') : '' }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="modal_ea_phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="modal_ea_phone" name="phone" value="{{ old('from_executive_staff_report') && !session('edit_executive_id') ? old('phone') : '' }}">
                        </div>
                        <div class="col-md-6">
                            <label for="modal_ea_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="modal_ea_email" name="email" value="{{ old('from_executive_staff_report') && !session('edit_executive_id') ? old('email') : '' }}" placeholder="Optional">
                        </div>
                        <div class="col-md-6">
                            <label for="modal_ea_department" class="form-label">Department</label>
                            <input type="text" class="form-control" id="modal_ea_department" name="department" value="{{ old('from_executive_staff_report') && !session('edit_executive_id') ? old('department') : '' }}" placeholder="Optional">
                        </div>
                        <div class="col-md-6">
                            <label for="modal_ea_status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="modal_ea_status" name="status" required>
                                <option value="active" @selected($execModalStatus === 'active')>Active</option>
                                <option value="inactive" @selected($execModalStatus === 'inactive')>Inactive</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label for="modal_ea_notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="modal_ea_notes" name="notes" rows="3" placeholder="Optional">{{ old('from_executive_staff_report') && !session('edit_executive_id') ? old('notes') : '' }}</textarea>
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
<script>
(function() {
    function peso(n) {
        var num = Number(n) || 0;
        return '₱' + num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    function esc(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    document.querySelectorAll('.js-view-team').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var name = btn.getAttribute('data-name') || '';
            var team = [];
            try { team = JSON.parse(btn.getAttribute('data-team') || '[]'); } catch (e) { team = []; }

            document.getElementById('teamModalExecName').textContent = name;
            document.getElementById('teamModalExecOwn').textContent = '₱' + (btn.getAttribute('data-exec-own') || '0.00');
            document.getElementById('teamModalTeamTotal').textContent = '₱' + (btn.getAttribute('data-team-total') || '0.00');

            var tbody = document.getElementById('teamModalBody');
            tbody.innerHTML = '';
            if (!team.length) {
                tbody.innerHTML = '<tr><td colspan="4" class="text-muted text-center py-3">No sales agents assigned to this executive yet.</td></tr>';
            } else {
                team.forEach(function(sa) {
                    var tr = document.createElement('tr');
                    tr.innerHTML =
                        '<td>' + esc(sa.name || '') + '</td>' +
                        '<td><span class="badge bg-secondary">' + esc(sa.sales_agent_id || '—') + '</span></td>' +
                        '<td class="text-end fw-semibold text-primary">' + peso(sa.exec_commission) + '</td>' +
                        '<td class="text-end fw-semibold">' + peso(sa.agent_earnings) + '</td>';
                    tbody.appendChild(tr);
                });
            }
            bootstrap.Modal.getOrCreateInstance(document.getElementById('teamModal')).show();
        });
    });

    var editModal = document.getElementById('editExecutiveAgentModal');
    var editForm = document.getElementById('editExecutiveAgentForm');
    if (editModal && editForm) {
        document.querySelectorAll('.js-edit-executive').forEach(function(btn) {
            btn.addEventListener('click', function() {
                editForm.action = btn.getAttribute('data-update-url') || '';
                document.getElementById('edit_ea_name').value = btn.getAttribute('data-name') || '';
                document.getElementById('edit_ea_email').value = btn.getAttribute('data-email') || '';
                document.getElementById('edit_ea_phone').value = btn.getAttribute('data-phone') || '';
                document.getElementById('edit_ea_code').value = btn.getAttribute('data-code') || '';
                document.getElementById('edit_ea_department').value = btn.getAttribute('data-department') || '';
                document.getElementById('edit_ea_status').value = btn.getAttribute('data-status') || 'active';
                document.getElementById('edit_ea_notes').value = btn.getAttribute('data-notes') || '';
                bootstrap.Modal.getOrCreateInstance(editModal).show();
            });
        });

        @if ($errors->any() && old('from_executive_staff_report') && session('edit_executive_id'))
        document.addEventListener('DOMContentLoaded', function() {
            var id = @json((string) session('edit_executive_id'));
            var btn = document.querySelector('.js-edit-executive[data-id="' + id + '"]');
            if (btn) {
                editForm.action = btn.getAttribute('data-update-url') || '';
            }
            bootstrap.Modal.getOrCreateInstance(editModal).show();
        });
        @endif
    }

    var addModal = document.getElementById('addExecutiveAgentModal');
    if (addModal) {
        addModal.addEventListener('hidden.bs.modal', function() {
            var form = document.getElementById('staffAddExecutiveAgentForm');
            @if (!$errors->any() || session('edit_executive_id') || !old('from_executive_staff_report'))
            if (form) form.reset();
            @endif
        });

        @if ($errors->any() && old('from_executive_staff_report') && !session('edit_executive_id'))
        document.addEventListener('DOMContentLoaded', function() {
            bootstrap.Modal.getOrCreateInstance(addModal).show();
        });
        @endif
    }

    @if ($errors->any() && old('from_leads_import'))
    document.addEventListener('DOMContentLoaded', function() {
        var importModal = document.getElementById('importLeadsModal');
        if (importModal) {
            bootstrap.Modal.getOrCreateInstance(importModal).show();
        }
    });
    @endif
})();
</script>
@endsection
