@extends('layouts.app')

@section('title', 'Client List - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-user-friends me-2"></i>Client List
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
            <a href="{{ route('client-follow-up-list.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add Client
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <p class="text-muted mb-4">Sales team leads and follow-ups, grouped by sales executive.</p>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('client-follow-up-list.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small">Sales Executive</label>
                    <select class="form-select form-select-sm" name="executive_agent_id">
                        <option value="">All</option>
                        @foreach($executives as $exec)
                            <option value="{{ $exec->id }}" {{ (string) request('executive_agent_id') === (string) $exec->id ? 'selected' : '' }}>{{ $exec->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Status</label>
                    <select class="form-select form-select-sm" name="status">
                        <option value="">All</option>
                        @foreach(\App\Models\ClientFollowUp::statusOptions() as $opt)
                            <option value="{{ $opt }}" {{ request('status') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Inquiry From</label>
                    <input type="date" class="form-control form-control-sm" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Inquiry To</label>
                    <input type="date" class="form-control form-control-sm" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Search</label>
                    <input type="text" class="form-control form-control-sm" name="search" placeholder="Name, phone, unit..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm me-2"><i class="fas fa-search me-1"></i>Filter</button>
                    <a href="{{ route('client-follow-up-list.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($clients->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Inquiry Date</th>
                                <th>Sales Executive</th>
                                <th>Application</th>
                                <th>Customer Name</th>
                                <th>Phone</th>
                                <th>Unit Inquired</th>
                                <th>About What</th>
                                <th>Latest Follow-up</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clients as $client)
                                <tr>
                                    <td class="text-nowrap">{{ $client->date_of_first_inquiry ? $client->date_of_first_inquiry->format('m/d/Y') : '—' }}</td>
                                    <td>
                                        @if($client->executiveAgent)
                                            <a href="{{ route('staff-reports.executive-agents.show', $client->executiveAgent) }}" class="fw-bold text-decoration-none">
                                                {{ $client->executiveAgent->name }}
                                            </a>
                                        @else
                                            {{ $client->team_lead ?: '—' }}
                                        @endif
                                    </td>
                                    <td class="small">{{ Str::limit($client->application, 24) ?: '—' }}</td>
                                    <td>
                                        <strong>{{ $client->client_name }}</strong>
                                        @if($client->notes)
                                            <br><span class="small text-muted">{{ Str::limit($client->notes, 40) }}</span>
                                        @endif
                                    </td>
                                    <td class="text-nowrap small">{{ $client->contact_number ?: '—' }}</td>
                                    <td class="small">
                                        @if($client->vehicle_id && $client->vehicle)
                                            <a href="{{ route('vehicles.show', $client->vehicle) }}">{{ Str::limit($client->unit_inquired, 28) ?: $client->vehicle->full_name }}</a>
                                        @else
                                            {{ Str::limit($client->unit_inquired, 28) ?: '—' }}
                                        @endif
                                    </td>
                                    <td class="small text-muted">{{ Str::limit($client->about_what, 30) ?: '—' }}</td>
                                    <td class="small">{{ Str::limit($client->latestFollowUpSummary(), 42) }}</td>
                                    <td>
                                        @if($client->status === 'Closed')
                                            <span class="badge bg-success">{{ $client->status }}</span>
                                        @elseif($client->status === 'In Progress')
                                            <span class="badge bg-info">{{ $client->status }}</span>
                                        @elseif($client->status === 'Contacted')
                                            <span class="badge bg-primary">{{ $client->status }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ $client->status }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center text-nowrap">
                                        @php
                                            $viewPayload = [
                                                'id' => $client->id,
                                                'inquiry_date' => $client->date_of_first_inquiry?->format('F j, Y'),
                                                'executive' => $client->executiveAgent->name ?? ($client->team_lead ?: null),
                                                'executive_url' => $client->executiveAgent
                                                    ? route('staff-reports.executive-agents.show', $client->executiveAgent)
                                                    : null,
                                                'application' => $client->application,
                                                'client_name' => $client->client_name,
                                                'contact_number' => $client->contact_number,
                                                'email' => $client->email,
                                                'unit_inquired' => $client->unit_inquired,
                                                'vehicle' => $client->vehicle?->full_name,
                                                'vehicle_url' => ($client->vehicle_id && $client->vehicle) ? route('vehicles.show', $client->vehicle) : null,
                                                'about_what' => $client->about_what,
                                                'notes' => $client->notes,
                                                'status' => $client->status,
                                                'latest_followup' => $client->latestFollowUpSummary(),
                                                'edit_url' => route('client-follow-up-list.edit', $client),
                                                'followups' => collect(range(1, 5))->map(function ($i) use ($client) {
                                                    $date = $client->{"date_followed_up_{$i}"};
                                                    $exec = $client->{"sales_exec_{$i}"};
                                                    $outcome = $client->{"outcome_{$i}"};
                                                    $notes = $client->{"notes_{$i}"};
                                                    if (!$date && !$exec && !$outcome && !$notes) {
                                                        return null;
                                                    }
                                                    return [
                                                        'round' => $i,
                                                        'exec' => $exec,
                                                        'date' => $date ? $date->format('F j, Y') : null,
                                                        'outcome' => $outcome,
                                                        'notes' => $notes,
                                                    ];
                                                })->filter()->values()->all(),
                                            ];
                                        @endphp
                                        <button type="button"
                                                class="btn btn-sm btn-outline-info view-client-btn"
                                                title="View"
                                                data-bs-toggle="modal"
                                                data-bs-target="#viewClientModal"
                                                data-payload="{{ base64_encode(json_encode($viewPayload, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE)) }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('client-follow-up-list.edit', $client) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('client-follow-up-list.destroy', $client) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this client?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3 pb-3">
                    {{ $clients->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-user-friends fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No clients yet</h5>
                    <p class="text-muted mb-3">Import sales team leads or add a client manually.</p>
                    <a href="{{ route('client-follow-up-list.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Client</a>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- View Client Details Modal --}}
<div class="modal fade" id="viewClientModal" tabindex="-1" aria-labelledby="viewClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 rounded-0">
                <h5 class="modal-title" id="viewClientModalLabel">
                    <i class="fas fa-user me-2"></i><span id="viewClientName">Client Details</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="text-muted small">Sales Executive</div>
                        <div class="fw-semibold" id="viewExecutive">—</div>
                    </div>
                    <span class="badge" id="viewStatusBadge">—</span>
                </div>

                <div class="alert alert-secondary py-2 mb-4" role="status">
                    <div class="text-muted small mb-1">Latest Follow-up Summary</div>
                    <div class="fw-semibold mb-0" id="viewLatestFollowup">—</div>
                </div>

                <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-inbox me-1"></i> Inquiry Details</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="text-muted small">Inquiry Date</div>
                        <div id="viewInquiryDate">—</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Application</div>
                        <div id="viewApplication">—</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Phone</div>
                        <div id="viewPhone">—</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Email</div>
                        <div id="viewEmail">—</div>
                    </div>
                    <div class="col-md-8">
                        <div class="text-muted small">Unit Inquired</div>
                        <div id="viewUnit">—</div>
                    </div>
                    <div class="col-md-12">
                        <div class="text-muted small">About What</div>
                        <div id="viewAbout">—</div>
                    </div>
                    <div class="col-md-12">
                        <div class="text-muted small">Notes</div>
                        <div id="viewNotes">—</div>
                    </div>
                </div>

                <h6 class="border-bottom pb-2 mb-3"><i class="fas fa-phone-alt me-1"></i> Follow-ups</h6>
                <div id="viewFollowupsEmpty" class="text-muted small mb-2 d-none">No follow-up records yet.</div>
                <div id="viewFollowups" class="vstack gap-3"></div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-primary btn-sm" id="viewEditLink">
                    <i class="fas fa-edit me-1"></i>Edit
                </a>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    const modal = document.getElementById('viewClientModal');
    if (!modal) return;

    const dash = (v) => (v && String(v).trim() !== '' && String(v).trim() !== '—' ? String(v) : '—');
    const esc = (v) => {
        const d = document.createElement('div');
        d.textContent = dash(v);
        return d.innerHTML;
    };

    function statusClass(status) {
        switch (status) {
            case 'Closed': return 'bg-success';
            case 'In Progress': return 'bg-info';
            case 'Contacted': return 'bg-primary';
            default: return 'bg-warning text-dark';
        }
    }

    function ordinal(n) {
        if (n === 1) return '1st';
        if (n === 2) return '2nd';
        if (n === 3) return '3rd';
        return n + 'th';
    }

    function parsePayload(raw) {
        if (!raw) return {};
        try {
            const binary = atob(raw);
            const bytes = Uint8Array.from(binary, (c) => c.charCodeAt(0));
            return JSON.parse(new TextDecoder('utf-8').decode(bytes));
        } catch (e) {
            try {
                return JSON.parse(raw);
            } catch (e2) {
                return {};
            }
        }
    }

    function fillModal(data) {
        document.getElementById('viewClientName').textContent = data.client_name || 'Client Details';
        const execEl = document.getElementById('viewExecutive');
        execEl.textContent = '';
        const execName = dash(data.executive);
        if (data.executive_url && execName !== '—') {
            const a = document.createElement('a');
            a.href = data.executive_url;
            a.className = 'text-decoration-none';
            a.textContent = execName;
            execEl.appendChild(a);
        } else {
            execEl.textContent = execName;
        }
        document.getElementById('viewLatestFollowup').textContent = dash(data.latest_followup);
        document.getElementById('viewInquiryDate').textContent = dash(data.inquiry_date);
        document.getElementById('viewApplication').textContent = dash(data.application);
        document.getElementById('viewPhone').textContent = dash(data.contact_number);
        document.getElementById('viewEmail').textContent = dash(data.email);
        document.getElementById('viewAbout').textContent = dash(data.about_what);
        document.getElementById('viewNotes').textContent = dash(data.notes);

        const unitEl = document.getElementById('viewUnit');
        unitEl.textContent = '';
        const unitLabel = dash(data.unit_inquired || data.vehicle);
        if (data.vehicle_url && unitLabel !== '—') {
            const a = document.createElement('a');
            a.href = data.vehicle_url;
            a.textContent = unitLabel;
            unitEl.appendChild(a);
        } else {
            unitEl.textContent = unitLabel;
        }

        const badge = document.getElementById('viewStatusBadge');
        badge.textContent = data.status || '—';
        badge.className = 'badge ' + statusClass(data.status);

        document.getElementById('viewEditLink').href = data.edit_url || '#';

        const wrap = document.getElementById('viewFollowups');
        const empty = document.getElementById('viewFollowupsEmpty');
        wrap.innerHTML = '';
        const followups = Array.isArray(data.followups) ? data.followups : [];
        if (!followups.length) {
            empty.classList.remove('d-none');
        } else {
            empty.classList.add('d-none');
            followups.forEach((fu) => {
                const card = document.createElement('div');
                card.className = 'border rounded p-3 bg-light';
                card.innerHTML = `
                    <div class="fw-semibold mb-2">${esc(ordinal(fu.round))} Follow Up</div>
                    <div class="row g-2 small">
                        <div class="col-md-4"><span class="text-muted">Sales Exec:</span> ${esc(fu.exec)}</div>
                        <div class="col-md-4"><span class="text-muted">Date:</span> ${esc(fu.date)}</div>
                        <div class="col-md-4"><span class="text-muted">Outcome:</span> ${esc(fu.outcome)}</div>
                        <div class="col-12"><span class="text-muted">Notes:</span> ${esc(fu.notes)}</div>
                    </div>
                `;
                wrap.appendChild(card);
            });
        }
    }

    modal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        if (!btn || !btn.classList.contains('view-client-btn')) return;
        fillModal(parsePayload(btn.getAttribute('data-payload')));
    });
})();
</script>
@endsection
