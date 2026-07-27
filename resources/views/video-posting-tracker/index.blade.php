@extends('layouts.app')

@section('title', 'Video and Posting Tracker - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-video me-2"></i>Video and Posting Tracker
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
            @canPage('video-posting-tracker', 'create')
            <a href="{{ route('video-posting-tracker.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add Record
            </a>
            @endcanPage
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <p class="text-muted mb-4">Vlog and social posting tracker — matching the Excel masterlist columns.</p>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('video-posting-tracker.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small">Vlogger</label>
                    <input type="text" class="form-control form-control-sm" name="vlogger" value="{{ request('vlogger') }}" placeholder="e.g. JOBERT">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Showroom</label>
                    <select class="form-select form-select-sm" name="showroom">
                        <option value="">All</option>
                        @foreach($showrooms as $showroom)
                            <option value="{{ $showroom }}" {{ strcasecmp((string) request('showroom'), (string) $showroom) === 0 ? 'selected' : '' }}>
                                {{ $showroom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Category</label>
                    <select class="form-select form-select-sm" name="category">
                        <option value="">All</option>
                        @foreach(['SOLO','DUO','FEATURED','FEEDBACK'] as $opt)
                            <option value="{{ $opt }}" {{ request('category') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Date From</label>
                    <input type="date" class="form-control form-control-sm" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Search</label>
                    <input type="text" class="form-control form-control-sm" name="search" placeholder="Plate, car, file..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm me-2"><i class="fas fa-search me-1"></i>Filter</button>
                    <a href="{{ route('video-posting-tracker.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($records->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-sm">
                        <thead class="table-light">
                            <tr>
                                <th style="width:3rem;">#</th>
                                <th>Vlogger</th>
                                <th>Category</th>
                                <th>Showroom</th>
                                <th>Featured Car/s or Client</th>
                                <th>Plate No.</th>
                                <th>Date Uploaded to G Drive</th>
                                <th>Date Posted on Social Media</th>
                                <th>Name of File in G Drive</th>
                                <th class="text-center">Link to Post</th>
                                <th class="text-center text-nowrap">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $record)
                                @php
                                    $viewPayload = [
                                        'vlogger' => $record->vlogger,
                                        'category' => $record->category,
                                        'showroom' => $record->showroom,
                                        'featured_car_or_client' => $record->featured_car_or_client ?: $record->title,
                                        'plate_number' => $record->plate_number,
                                        'active_unit' => $record->active_unit,
                                        'date_uploaded_gdrive' => $record->date_uploaded_gdrive?->format('F j, Y'),
                                        'date_posted_social' => $record->date_posted_social?->format('F j, Y'),
                                        'gdrive_file_name' => $record->gdrive_file_name,
                                        'link_url' => $record->link_url,
                                        'platform' => $record->platform,
                                        'status' => $record->status,
                                        'notes' => $record->notes,
                                        'source_sheet' => $record->source_sheet,
                                        'vehicle_label' => $record->vehicle
                                            ? trim($record->vehicle->full_name.($record->vehicle->plate_number ? ' ('.$record->vehicle->plate_number.')' : ''))
                                            : null,
                                        'vehicle_url' => ($record->vehicle_id && $record->vehicle)
                                            ? route('vehicles.show', $record->vehicle)
                                            : null,
                                        'edit_url' => route('video-posting-tracker.edit', $record),
                                    ];
                                @endphp
                                <tr>
                                    <td class="text-muted">{{ $records->firstItem() + $loop->index }}</td>
                                    <td class="text-nowrap">{{ $record->vlogger ?: '—' }}</td>
                                    <td class="text-nowrap">
                                        @if($record->category)
                                            <span class="badge bg-secondary">{{ $record->category }}</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="text-nowrap">
                                        @include('partials.showroom-badge', ['name' => $record->showroom])
                                    </td>
                                    <td>{{ $record->featured_car_or_client ?: ($record->title ?: '—') }}</td>
                                    <td class="text-nowrap">
                                        @if($record->vehicle_id && $record->vehicle)
                                            <a href="{{ route('vehicles.show', $record->vehicle) }}" class="fw-semibold text-decoration-none" title="Open vehicle profile">
                                                {{ $record->plate_number ?: $record->vehicle->plate_number }}
                                            </a>
                                        @else
                                            {{ $record->plate_number ?: '—' }}
                                        @endif
                                    </td>
                                    <td class="text-nowrap small">{{ $record->date_uploaded_gdrive?->format('d F, Y') ?? '—' }}</td>
                                    <td class="text-nowrap small">{{ $record->date_posted_social?->format('d F, Y') ?? '—' }}</td>
                                    <td class="small">{{ $record->gdrive_file_name ?: '—' }}</td>
                                    <td class="text-center">
                                        @if($record->link_url)
                                            <a href="{{ $record->link_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary" title="Open post">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center text-nowrap">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-info view-post-btn"
                                                title="View"
                                                data-bs-toggle="modal"
                                                data-bs-target="#viewPostModal"
                                                data-payload="{{ base64_encode(json_encode($viewPayload, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE)) }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @canPage('video-posting-tracker', 'update')
                                        <a href="{{ route('video-posting-tracker.edit', $record) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                        @endcanPage
                                        @canPage('video-posting-tracker', 'delete')
                                        <form action="{{ route('video-posting-tracker.destroy', $record) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                        @endcanPage
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3 pb-3">
                    {{ $records->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-video fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No records yet</h5>
                    <p class="text-muted mb-3">Import the vlog/posting tracker or add a record manually.</p>
                    @canPage('video-posting-tracker', 'create')
                    <a href="{{ route('video-posting-tracker.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Record</a>
                    @endcanPage
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="viewPostModal" tabindex="-1" aria-labelledby="viewPostModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 rounded-0">
                <h5 class="modal-title" id="viewPostModalLabel">
                    <i class="fas fa-video me-2"></i><span id="viewPostTitle">Posting Details</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-4"><div class="text-muted small">Vlogger</div><div class="fw-semibold" id="viewPostVlogger">—</div></div>
                    <div class="col-md-4"><div class="text-muted small">Category</div><div id="viewPostCategory">—</div></div>
                    <div class="col-md-4"><div class="text-muted small">Showroom</div><div id="viewPostShowroom">—</div></div>
                    <div class="col-md-8"><div class="text-muted small">Featured Car/s or Client</div><div id="viewPostFeatured">—</div></div>
                    <div class="col-md-4"><div class="text-muted small">Plate No.</div><div class="fw-semibold" id="viewPostPlate">—</div></div>
                    <div class="col-md-6"><div class="text-muted small">Date Uploaded to G Drive</div><div id="viewPostUploaded">—</div></div>
                    <div class="col-md-6"><div class="text-muted small">Date Posted on Social Media</div><div id="viewPostPosted">—</div></div>
                    <div class="col-md-6"><div class="text-muted small">Name of File in G Drive</div><div id="viewPostFile">—</div></div>
                    <div class="col-md-6"><div class="text-muted small">Platform / Status</div><div id="viewPostPlatform">—</div></div>
                    <div class="col-12"><div class="text-muted small">Link to Post</div><div id="viewPostLink">—</div></div>
                    <div class="col-12"><div class="text-muted small">Linked Vehicle Profile</div><div id="viewPostVehicle">—</div></div>
                    <div class="col-12 d-none" id="viewPostNotesWrap"><div class="text-muted small">Notes</div><div id="viewPostNotes">—</div></div>
                </div>
            </div>
            <div class="modal-footer">
                @canPage('video-posting-tracker', 'update')
                <a href="#" class="btn btn-primary btn-sm" id="viewPostEditLink"><i class="fas fa-edit me-1"></i>Edit</a>
                @endcanPage
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    const modal = document.getElementById('viewPostModal');
    if (!modal) return;

    const dash = (v) => (v && String(v).trim() !== '' ? String(v) : '—');

    function setText(id, value) {
        document.getElementById(id).textContent = dash(value);
    }

    function setLinkOrText(el, label, url) {
        el.textContent = '';
        const text = dash(label);
        if (url && text !== '—') {
            const a = document.createElement('a');
            a.href = url;
            a.target = '_blank';
            a.rel = 'noopener';
            a.textContent = text;
            el.appendChild(a);
        } else {
            el.textContent = text;
        }
    }

    function parsePayload(raw) {
        if (!raw) return {};
        try {
            const binary = atob(raw);
            const bytes = Uint8Array.from(binary, (c) => c.charCodeAt(0));
            return JSON.parse(new TextDecoder('utf-8').decode(bytes));
        } catch (e) {
            return {};
        }
    }

    modal.addEventListener('show.bs.modal', function (event) {
        const btn = event.relatedTarget;
        if (!btn || !btn.classList.contains('view-post-btn')) return;
        const data = parsePayload(btn.getAttribute('data-payload'));

        document.getElementById('viewPostTitle').textContent = data.featured_car_or_client || data.vlogger || 'Posting Details';
        setText('viewPostVlogger', data.vlogger);
        setText('viewPostCategory', data.category);
        setShowroomBadge(document.getElementById('viewPostShowroom'), data.showroom);
        setText('viewPostFeatured', data.featured_car_or_client);
        setText('viewPostUploaded', data.date_uploaded_gdrive);
        setText('viewPostPosted', data.date_posted_social);
        setText('viewPostFile', data.gdrive_file_name);
        setText('viewPostPlatform', [data.platform, data.status].filter(Boolean).join(' / '));

        setLinkOrText(document.getElementById('viewPostPlate'), data.plate_number, data.vehicle_url || null);
        setLinkOrText(document.getElementById('viewPostLink'), data.link_url, data.link_url || null);
        setLinkOrText(document.getElementById('viewPostVehicle'), data.vehicle_label || 'Not linked', data.vehicle_url || null);

        const notesWrap = document.getElementById('viewPostNotesWrap');
        if (data.notes && String(data.notes).trim() !== '') {
            notesWrap.classList.remove('d-none');
            setText('viewPostNotes', data.notes);
        } else {
            notesWrap.classList.add('d-none');
        }

        document.getElementById('viewPostEditLink').href = data.edit_url || '#';
    });
})();
</script>
@endsection
