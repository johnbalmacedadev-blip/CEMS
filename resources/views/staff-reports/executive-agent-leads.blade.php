@extends('layouts.app')

@section('title', $executive->name . ' — Leads Master List - Car Empire Management System')

@section('content')
@php
    $canEditLead = auth()->user()?->canAccessPage('staff-reports.executive-agents', 'update')
        || auth()->user()?->canAccessPage('staff-reports.executive-agents', 'create');
    $canDeleteLead = auth()->user()?->canAccessPage('staff-reports.executive-agents', 'delete')
        || auth()->user()?->canAccessPage('staff-reports.executive-agents', 'create');
@endphp
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-address-book me-2"></i>{{ $executive->name }} — Leads Master List
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0 gap-2">
            @canPage('staff-reports.executive-agents', 'create')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLeadModal">
                <i class="fas fa-plus me-1"></i>Add Lead
            </button>
            @endcanPage
            <a href="{{ route('staff-reports.executive-agents.show', $executive) }}" class="btn btn-outline-secondary">
                <i class="fas fa-user-shield me-1"></i>Agent profile
            </a>
            <a href="{{ route('staff-reports.executive-agents') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Executive Agents
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any() && (session('open_add_lead_modal') || session('open_edit_lead_modal')))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Could not save lead:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('staff-reports.executive-agents.leads', $executive) }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="leadsSearch" class="form-label mb-1">Search leads</label>
                    <input type="search" class="form-control" id="leadsSearch" name="q"
                           value="{{ $search }}" placeholder="Name, phone, unit, source, notes…">
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i>Search</button>
                    @if($search !== '')
                        <a href="{{ route('staff-reports.executive-agents.leads', $executive) }}" class="btn btn-outline-secondary">Clear</a>
                    @endif
                    <div class="ms-auto align-self-center text-muted small">
                        {{ number_format($leads->total()) }} lead{{ $leads->total() === 1 ? '' : 's' }}
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Source</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Unit inquired</th>
                            <th>About</th>
                            <th>1st follow-up</th>
                            <th>2nd follow-up</th>
                            <th>3rd follow-up</th>
                            <th>4th follow-up</th>
                            <th>Notes</th>
                            @if($canEditLead || $canDeleteLead)
                                <th class="text-end" style="width: 6rem;">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leads as $lead)
                            @php
                                $leadPayload = [
                                    'id' => $lead->id,
                                    'update_url' => route('staff-reports.executive-agents.leads.update', [$executive, $lead]),
                                    'application_date' => optional($lead->application_date)->format('Y-m-d'),
                                    'application_source' => $lead->application_source,
                                    'customer_name' => $lead->customer_name,
                                    'customer_phone' => $lead->customer_phone,
                                    'unit_inquired' => $lead->unit_inquired,
                                    'about_what' => $lead->about_what,
                                    'notes' => $lead->notes,
                                    'followup_1_exec' => $lead->followup_1_exec,
                                    'followup_1_date' => optional($lead->followup_1_date)->format('Y-m-d'),
                                    'followup_1_outcome' => $lead->followup_1_outcome,
                                    'followup_1_notes' => $lead->followup_1_notes,
                                    'followup_2_exec' => $lead->followup_2_exec,
                                    'followup_2_date' => optional($lead->followup_2_date)->format('Y-m-d'),
                                    'followup_2_outcome' => $lead->followup_2_outcome,
                                    'followup_2_notes' => $lead->followup_2_notes,
                                    'followup_3_exec' => $lead->followup_3_exec,
                                    'followup_3_date' => optional($lead->followup_3_date)->format('Y-m-d'),
                                    'followup_3_outcome' => $lead->followup_3_outcome,
                                    'followup_3_notes' => $lead->followup_3_notes,
                                    'followup_4_exec' => $lead->followup_4_exec,
                                    'followup_4_date' => optional($lead->followup_4_date)->format('Y-m-d'),
                                    'followup_4_outcome' => $lead->followup_4_outcome,
                                    'followup_4_notes' => $lead->followup_4_notes,
                                ];
                            @endphp
                            <tr>
                                <td class="text-nowrap">{{ optional($lead->application_date)->format('Y-m-d') ?: '—' }}</td>
                                <td>{{ $lead->application_source ?: '—' }}</td>
                                <td class="fw-semibold">{{ $lead->customer_name }}</td>
                                <td class="text-nowrap">{{ $lead->customer_phone ?: '—' }}</td>
                                <td>{{ $lead->unit_inquired ?: '—' }}</td>
                                <td>{{ $lead->about_what ?: '—' }}</td>
                                <td class="small">
                                    @if($lead->followup_1_exec || $lead->followup_1_date || $lead->followup_1_outcome)
                                        <div><strong>{{ $lead->followup_1_exec ?: '—' }}</strong></div>
                                        <div class="text-muted">{{ optional($lead->followup_1_date)->format('Y-m-d') }}</div>
                                        <div>{{ $lead->followup_1_outcome }}</div>
                                        @if($lead->followup_1_notes)
                                            <div class="text-muted">{{ \Illuminate\Support\Str::limit($lead->followup_1_notes, 80) }}</div>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="small">
                                    @if($lead->followup_2_exec || $lead->followup_2_date || $lead->followup_2_outcome)
                                        <div><strong>{{ $lead->followup_2_exec ?: '—' }}</strong></div>
                                        <div class="text-muted">{{ optional($lead->followup_2_date)->format('Y-m-d') }}</div>
                                        <div>{{ $lead->followup_2_outcome }}</div>
                                        @if($lead->followup_2_notes)
                                            <div class="text-muted">{{ \Illuminate\Support\Str::limit($lead->followup_2_notes, 80) }}</div>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="small">
                                    @if($lead->followup_3_exec || $lead->followup_3_date || $lead->followup_3_outcome)
                                        <div><strong>{{ $lead->followup_3_exec ?: '—' }}</strong></div>
                                        <div class="text-muted">{{ optional($lead->followup_3_date)->format('Y-m-d') }}</div>
                                        <div>{{ $lead->followup_3_outcome }}</div>
                                        @if($lead->followup_3_notes)
                                            <div class="text-muted">{{ \Illuminate\Support\Str::limit($lead->followup_3_notes, 80) }}</div>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="small">
                                    @if($lead->followup_4_exec || $lead->followup_4_date || $lead->followup_4_outcome)
                                        <div><strong>{{ $lead->followup_4_exec ?: '—' }}</strong></div>
                                        <div class="text-muted">{{ optional($lead->followup_4_date)->format('Y-m-d') }}</div>
                                        <div>{{ $lead->followup_4_outcome }}</div>
                                        @if($lead->followup_4_notes)
                                            <div class="text-muted">{{ \Illuminate\Support\Str::limit($lead->followup_4_notes, 80) }}</div>
                                        @endif
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="small" style="max-width: 180px;">
                                    {{ $lead->notes ? \Illuminate\Support\Str::limit($lead->notes, 100) : '—' }}
                                </td>
                                @if($canEditLead || $canDeleteLead)
                                    <td class="text-end text-nowrap">
                                        @if($canEditLead)
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-warning js-edit-lead"
                                                title="Edit lead"
                                                data-lead='@json($leadPayload)'
                                            >
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                        @if($canDeleteLead)
                                            <form
                                                method="POST"
                                                action="{{ route('staff-reports.executive-agents.leads.destroy', [$executive, $lead]) }}"
                                                class="d-inline"
                                                onsubmit="return confirm(@json('Delete lead '.$lead->customer_name.'? This cannot be undone.'));"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete lead">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ ($canEditLead || $canDeleteLead) ? 12 : 11 }}" class="text-center text-muted py-4">
                                    No leads yet. Use <strong>Add Lead</strong> or import Excel from the Executive Agents page.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($leads->hasPages())
            <div class="card-footer">
                {{ $leads->links() }}
            </div>
        @endif
    </div>
</div>

@canPage('staff-reports.executive-agents', 'create')
<div class="modal fade" id="addLeadModal" tabindex="-1" aria-labelledby="addLeadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" action="{{ route('staff-reports.executive-agents.leads.store', $executive) }}" autocomplete="off">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addLeadModalLabel">
                        <i class="fas fa-plus me-2"></i>Add lead — {{ $executive->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('staff-reports.partials.lead-form-fields', ['prefix' => 'add', 'useOld' => session('open_add_lead_modal')])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save lead</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcanPage

@if($canEditLead)
<div class="modal fade" id="editLeadModal" tabindex="-1" aria-labelledby="editLeadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <form method="POST" id="editLeadForm" autocomplete="off">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editLeadModalLabel">
                        <i class="fas fa-edit me-2"></i>Edit lead — {{ $executive->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('staff-reports.partials.lead-form-fields', ['prefix' => 'edit', 'useOld' => session('open_edit_lead_modal')])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save me-1"></i>Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if (session('open_add_lead_modal'))
    var addModal = document.getElementById('addLeadModal');
    if (addModal) bootstrap.Modal.getOrCreateInstance(addModal).show();
    @endif

    var editModal = document.getElementById('editLeadModal');
    var editForm = document.getElementById('editLeadForm');
    if (!editModal || !editForm) return;

    function setVal(id, value) {
        var el = document.getElementById(id);
        if (el) el.value = value == null ? '' : value;
    }

    function fillEditForm(data) {
        editForm.action = data.update_url || '';
        setVal('edit_application_date', data.application_date);
        setVal('edit_application_source', data.application_source);
        setVal('edit_customer_name', data.customer_name);
        setVal('edit_customer_phone', data.customer_phone);
        setVal('edit_unit_inquired', data.unit_inquired);
        setVal('edit_about_what', data.about_what);
        setVal('edit_notes', data.notes);
        for (var n = 1; n <= 4; n++) {
            setVal('edit_followup_' + n + '_exec', data['followup_' + n + '_exec']);
            setVal('edit_followup_' + n + '_date', data['followup_' + n + '_date']);
            setVal('edit_followup_' + n + '_outcome', data['followup_' + n + '_outcome']);
            setVal('edit_followup_' + n + '_notes', data['followup_' + n + '_notes']);
        }
    }

    document.querySelectorAll('.js-edit-lead').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var data = {};
            try { data = JSON.parse(btn.getAttribute('data-lead') || '{}'); } catch (e) { data = {}; }
            fillEditForm(data);
            bootstrap.Modal.getOrCreateInstance(editModal).show();
        });
    });

    @if (session('open_edit_lead_modal') && session('edit_lead_id'))
    (function() {
        var id = @json((string) session('edit_lead_id'));
        var btn = document.querySelector('.js-edit-lead[data-lead*="\\"id\\":' + id + '"], .js-edit-lead[data-lead*="\\"id\\": ' + id + '"]');
        if (!btn) {
            btn = Array.prototype.find.call(document.querySelectorAll('.js-edit-lead'), function(b) {
                try {
                    var d = JSON.parse(b.getAttribute('data-lead') || '{}');
                    return String(d.id) === id;
                } catch (e) { return false; }
            });
        }
        if (btn) {
            try { fillEditForm(JSON.parse(btn.getAttribute('data-lead') || '{}')); } catch (e) {}
        }
        @if(old('customer_name'))
        setVal('edit_application_date', @json(old('application_date')));
        setVal('edit_application_source', @json(old('application_source')));
        setVal('edit_customer_name', @json(old('customer_name')));
        setVal('edit_customer_phone', @json(old('customer_phone')));
        setVal('edit_unit_inquired', @json(old('unit_inquired')));
        setVal('edit_about_what', @json(old('about_what')));
        setVal('edit_notes', @json(old('notes')));
        @for ($n = 1; $n <= 4; $n++)
        setVal('edit_followup_{{ $n }}_exec', @json(old('followup_'.$n.'_exec')));
        setVal('edit_followup_{{ $n }}_date', @json(old('followup_'.$n.'_date')));
        setVal('edit_followup_{{ $n }}_outcome', @json(old('followup_'.$n.'_outcome')));
        setVal('edit_followup_{{ $n }}_notes', @json(old('followup_'.$n.'_notes')));
        @endfor
        @endif
        if (btn) {
            try {
                var d = JSON.parse(btn.getAttribute('data-lead') || '{}');
                editForm.action = d.update_url || '';
            } catch (e) {}
        }
        bootstrap.Modal.getOrCreateInstance(editModal).show();
    })();
    @endif
});
</script>
@endsection
