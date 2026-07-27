@extends('layouts.app')

@section('title', 'Recommendation Tracker - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-clipboard-list me-2"></i>Recommendation Tracker
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
            <a href="{{ route('recommendation-tracker.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add Recommendation
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <p class="text-muted mb-4">Vehicle recommendations and completion notes. Plate numbers link to unit profiles when matched.</p>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('recommendation-tracker.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small">Make</label>
                    <input type="text" class="form-control form-control-sm" name="make" value="{{ request('make') }}" placeholder="Make">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Status</label>
                    <input type="text" class="form-control form-control-sm" name="final_status" value="{{ request('final_status') }}" placeholder="e.g. COMPLETE">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Vehicle Link</label>
                    <select class="form-select form-select-sm" name="linked">
                        <option value="">All</option>
                        <option value="yes" {{ request('linked') === 'yes' ? 'selected' : '' }}>Linked</option>
                        <option value="no" {{ request('linked') === 'no' ? 'selected' : '' }}>Not linked</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Date From</label>
                    <input type="date" class="form-control form-control-sm" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Search</label>
                    <input type="text" class="form-control form-control-sm" name="search" value="{{ request('search') }}" placeholder="Plate, make, model...">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm me-2"><i class="fas fa-search me-1"></i>Filter</button>
                    <a href="{{ route('recommendation-tracker.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($records->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Year</th>
                                <th>Make / Model</th>
                                <th>Plate</th>
                                <th>Status</th>
                                <th>Paint Rec.</th>
                                <th class="text-center" style="width:9rem;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $record)
                                @php
                                    $categories = [];
                                    foreach (\App\Models\RecommendationTracker::recommendationCategories() as $key => $label) {
                                        $rec = $record->{$key.'_recommendation'};
                                        $comp = $record->{$key.'_completion'};
                                        if ($rec || $comp) {
                                            $categories[] = [
                                                'label' => $label,
                                                'recommendation' => $rec,
                                                'completion' => $comp,
                                            ];
                                        }
                                    }
                                    $viewPayload = [
                                        'title' => $record->display_title,
                                        'date' => $record->date?->format('F j, Y'),
                                        'year' => $record->year,
                                        'make' => $record->make,
                                        'model' => $record->model,
                                        'variant' => $record->variant,
                                        'plate_number' => $record->plate_number,
                                        'transmission' => $record->transmission,
                                        'fuel_type' => $record->fuel_type,
                                        'color' => $record->color,
                                        'odometers' => $record->odometers,
                                        'final_status' => $record->final_status,
                                        'purchase_price' => $record->purchase_price !== null ? '₱'.number_format($record->purchase_price, 2) : null,
                                        'purchased_from' => $record->purchased_from ?: $record->customer,
                                        'purchase_date' => $record->purchase_date?->format('F j, Y'),
                                        'categories' => $categories,
                                        'with_tools' => (bool) $record->with_tools,
                                        'with_matting' => (bool) $record->with_matting_complete,
                                        'with_spare_tire' => (bool) $record->with_spare_tire,
                                        'with_spare_key' => (bool) $record->with_spare_key,
                                        'vehicle_label' => $record->vehicle
                                            ? trim($record->vehicle->full_name.($record->vehicle->plate_number ? ' ('.$record->vehicle->plate_number.')' : ''))
                                            : null,
                                        'vehicle_url' => ($record->vehicle_id && $record->vehicle)
                                            ? route('vehicles.show', $record->vehicle)
                                            : null,
                                        'edit_url' => route('recommendation-tracker.edit', $record),
                                    ];
                                @endphp
                                <tr>
                                    <td class="text-nowrap">{{ $record->date?->format('M d, Y') ?? '—' }}</td>
                                    <td>{{ $record->year ?? '—' }}</td>
                                    <td>
                                        <strong>{{ $record->make ?? '—' }}</strong>
                                        @if($record->model)<br><span class="small text-muted">{{ $record->model }}@if($record->variant) · {{ Str::limit($record->variant, 24) }}@endif</span>@endif
                                    </td>
                                    <td class="text-nowrap">
                                        @if($record->vehicle_id && $record->vehicle)
                                            <a href="{{ route('vehicles.show', $record->vehicle) }}" class="fw-semibold text-decoration-none" title="Open vehicle profile">
                                                {{ $record->plate_number ?: $record->vehicle->plate_number }}
                                                <i class="fas fa-external-link-alt small ms-1"></i>
                                            </a>
                                        @elseif($record->plate_number)
                                            <span title="No matching vehicle profile">{{ $record->plate_number }}</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->final_status)
                                            <span class="badge {{ strtoupper($record->final_status) === 'COMPLETE' ? 'bg-success' : 'bg-warning text-dark' }}">{{ $record->final_status }}</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="small text-muted">{{ Str::limit($record->paint_recommendation ?: $record->paint, 40) ?: '—' }}</td>
                                    <td class="text-center text-nowrap">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-info view-reco-btn"
                                                title="View"
                                                data-bs-toggle="modal"
                                                data-bs-target="#viewRecoModal"
                                                data-payload="{{ base64_encode(json_encode($viewPayload, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE)) }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="{{ route('recommendation-tracker.edit', $record) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('recommendation-tracker.destroy', $record) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this recommendation record?');">
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
                    {{ $records->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No recommendation records yet</h5>
                    <p class="text-muted mb-3">Import the recommendations tracker or add a record manually.</p>
                    <a href="{{ route('recommendation-tracker.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Recommendation</a>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="viewRecoModal" tabindex="-1" aria-labelledby="viewRecoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 rounded-0">
                <h5 class="modal-title" id="viewRecoModalLabel">
                    <i class="fas fa-clipboard-list me-2"></i><span id="viewRecoTitle">Recommendation Details</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <div class="text-muted small">Final Status</div>
                        <span class="badge" id="viewRecoStatus">—</span>
                    </div>
                    <div class="text-end">
                        <div class="text-muted small">Date</div>
                        <div id="viewRecoDate">—</div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-3"><div class="text-muted small">Year</div><div id="viewRecoYear">—</div></div>
                    <div class="col-md-3"><div class="text-muted small">Make</div><div id="viewRecoMake">—</div></div>
                    <div class="col-md-3"><div class="text-muted small">Model</div><div id="viewRecoModel">—</div></div>
                    <div class="col-md-3"><div class="text-muted small">Plate</div><div class="fw-semibold" id="viewRecoPlate">—</div></div>
                    <div class="col-md-4"><div class="text-muted small">Variant</div><div id="viewRecoVariant">—</div></div>
                    <div class="col-md-4"><div class="text-muted small">Transmission / Fuel</div><div id="viewRecoTransFuel">—</div></div>
                    <div class="col-md-4"><div class="text-muted small">Color</div><div id="viewRecoColor">—</div></div>
                    <div class="col-md-4"><div class="text-muted small">Odometer</div><div id="viewRecoOdo">—</div></div>
                    <div class="col-md-4"><div class="text-muted small">Purchase Price</div><div id="viewRecoPrice">—</div></div>
                    <div class="col-md-4"><div class="text-muted small">Purchased From</div><div id="viewRecoFrom">—</div></div>
                    <div class="col-md-12"><div class="text-muted small">Linked Vehicle Profile</div><div id="viewRecoVehicle">—</div></div>
                    <div class="col-md-12"><div class="text-muted small">Accessories</div><div id="viewRecoAccessories">—</div></div>
                </div>

                <h6 class="border-bottom pb-2 mb-3">Recommendations</h6>
                <div id="viewRecoCatsEmpty" class="text-muted small d-none">No recommendation notes.</div>
                <div id="viewRecoCats" class="vstack gap-3"></div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-primary btn-sm" id="viewRecoEditLink"><i class="fas fa-edit me-1"></i>Edit</a>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function () {
    const modal = document.getElementById('viewRecoModal');
    if (!modal) return;

    const dash = (v) => (v && String(v).trim() !== '' ? String(v) : '—');
    const esc = (v) => {
        const d = document.createElement('div');
        d.textContent = dash(v);
        return d.innerHTML;
    };

    function setText(id, value) {
        document.getElementById(id).textContent = dash(value);
    }

    function setLinkOrText(el, label, url) {
        el.textContent = '';
        const text = dash(label);
        if (url && text !== '—') {
            const a = document.createElement('a');
            a.href = url;
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
        if (!btn || !btn.classList.contains('view-reco-btn')) return;
        const data = parsePayload(btn.getAttribute('data-payload'));

        document.getElementById('viewRecoTitle').textContent = data.title || 'Recommendation Details';
        setText('viewRecoDate', data.date);
        setText('viewRecoYear', data.year);
        setText('viewRecoMake', data.make);
        setText('viewRecoModel', data.model);
        setText('viewRecoVariant', data.variant);
        setText('viewRecoColor', data.color);
        setText('viewRecoOdo', data.odometers);
        setText('viewRecoPrice', data.purchase_price);
        setText('viewRecoFrom', data.purchased_from);

        const tf = [data.transmission, data.fuel_type].filter(Boolean).join(' / ');
        setText('viewRecoTransFuel', tf);

        const status = document.getElementById('viewRecoStatus');
        status.textContent = data.final_status || '—';
        status.className = 'badge ' + (String(data.final_status || '').toUpperCase() === 'COMPLETE' ? 'bg-success' : 'bg-warning text-dark');

        setLinkOrText(document.getElementById('viewRecoPlate'), data.plate_number, data.vehicle_url || null);
        setLinkOrText(document.getElementById('viewRecoVehicle'), data.vehicle_label || 'Not linked', data.vehicle_url || null);

        const accessories = [];
        if (data.with_tools) accessories.push('Tools');
        if (data.with_matting) accessories.push('Matting');
        if (data.with_spare_tire) accessories.push('Spare tire');
        if (data.with_spare_key) accessories.push('Spare key');
        setText('viewRecoAccessories', accessories.join(', ') || '—');

        const wrap = document.getElementById('viewRecoCats');
        const empty = document.getElementById('viewRecoCatsEmpty');
        wrap.innerHTML = '';
        const cats = Array.isArray(data.categories) ? data.categories : [];
        if (!cats.length) {
            empty.classList.remove('d-none');
        } else {
            empty.classList.add('d-none');
            cats.forEach((cat) => {
                const card = document.createElement('div');
                card.className = 'border rounded p-3 bg-light';
                card.innerHTML = `
                    <div class="fw-semibold mb-2">${esc(cat.label)}</div>
                    <div class="row g-2 small">
                        <div class="col-md-6"><span class="text-muted">Recommendation:</span><br><pre class="mb-0 mt-1" style="white-space:pre-wrap;font-family:inherit;">${esc(cat.recommendation)}</pre></div>
                        <div class="col-md-6"><span class="text-muted">Completion:</span><br><pre class="mb-0 mt-1" style="white-space:pre-wrap;font-family:inherit;">${esc(cat.completion)}</pre></div>
                    </div>
                `;
                wrap.appendChild(card);
            });
        }

        document.getElementById('viewRecoEditLink').href = data.edit_url || '#';
    });
})();
</script>
@endsection
