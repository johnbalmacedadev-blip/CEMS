@extends('layouts.app')

@section('title', 'Units Masterlist - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-list me-2"></i>Units Masterlist
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
            @canPage('units-masterlist', 'create')
            <a href="{{ route('units-masterlist.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add Unit
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

    <p class="text-muted mb-4">Pricelist masterlist units. Plate numbers link to the vehicle profile when matched.</p>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('units-masterlist.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small">Year</label>
                    <input type="text" class="form-control form-control-sm" name="year" value="{{ request('year') }}" placeholder="e.g. 2023">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Transmission</label>
                    <select class="form-select form-select-sm" name="transmission">
                        <option value="">All</option>
                        <option value="A/T" {{ request('transmission') === 'A/T' ? 'selected' : '' }}>A/T</option>
                        <option value="M/T" {{ request('transmission') === 'M/T' ? 'selected' : '' }}>M/T</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Vehicle Link</label>
                    <select class="form-select form-select-sm" name="linked">
                        <option value="">All</option>
                        <option value="yes" {{ request('linked') === 'yes' ? 'selected' : '' }}>Linked</option>
                        <option value="no" {{ request('linked') === 'no' ? 'selected' : '' }}>Not linked</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Search</label>
                    <input type="text" class="form-control form-control-sm" name="search" value="{{ request('search') }}" placeholder="Make, plate, variant...">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm me-2"><i class="fas fa-search me-1"></i>Filter</button>
                    <a href="{{ route('units-masterlist.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($units->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:3rem;">#</th>
                                <th>Make / Model</th>
                                <th>Plate</th>
                                <th>Variant</th>
                                <th>Trans.</th>
                                <th>Fuel</th>
                                <th>Year</th>
                                <th class="text-end">Mileage</th>
                                <th class="text-end">Price</th>
                                <th class="text-center" style="width:9rem;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($units as $unit)
                                @php
                                    $viewPayload = [
                                        'list_number' => $unit->list_number,
                                        'make_model' => $unit->make_model,
                                        'plate_number' => $unit->plate_number,
                                        'variant' => $unit->variant,
                                        'transmission' => $unit->transmission,
                                        'fuel_type' => $unit->fuel_type,
                                        'year' => $unit->year,
                                        'mileage' => $unit->mileage !== null ? number_format($unit->mileage).' km' : null,
                                        'price' => $unit->price !== null ? '₱'.number_format($unit->price, 2) : null,
                                        'low_down_payment_option' => $unit->low_down_payment_option,
                                        'low_monthly_option' => $unit->low_monthly_option,
                                        'notes' => $unit->notes,
                                        'vehicle_label' => $unit->vehicle
                                            ? trim($unit->vehicle->full_name.($unit->vehicle->plate_number ? ' ('.$unit->vehicle->plate_number.')' : ''))
                                            : null,
                                        'vehicle_url' => ($unit->vehicle_id && $unit->vehicle)
                                            ? route('vehicles.show', $unit->vehicle)
                                            : null,
                                        'edit_url' => route('units-masterlist.edit', $unit),
                                    ];
                                @endphp
                                <tr>
                                    <td class="text-muted">{{ $unit->list_number ?? '—' }}</td>
                                    <td><strong>{{ $unit->make_model }}</strong></td>
                                    <td class="text-nowrap">
                                        @if($unit->vehicle_id && $unit->vehicle)
                                            <a href="{{ route('vehicles.show', $unit->vehicle) }}" class="fw-semibold text-decoration-none" title="Open vehicle profile">
                                                {{ $unit->plate_number ?: $unit->vehicle->plate_number }}
                                                <i class="fas fa-external-link-alt small ms-1"></i>
                                            </a>
                                        @elseif($unit->plate_number)
                                            <span title="No matching vehicle profile">{{ $unit->plate_number }}</span>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="small">{{ Str::limit($unit->variant, 36) ?: '—' }}</td>
                                    <td>{{ $unit->transmission ?: '—' }}</td>
                                    <td>{{ $unit->fuel_type ?: '—' }}</td>
                                    <td>{{ $unit->year ?: '—' }}</td>
                                    <td class="text-end text-nowrap">{{ $unit->mileage !== null ? number_format($unit->mileage) : '—' }}</td>
                                    <td class="text-end text-nowrap fw-semibold">{{ $unit->price !== null ? '₱'.number_format($unit->price, 0) : '—' }}</td>
                                    <td class="text-center text-nowrap">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-info view-unit-btn"
                                                title="View"
                                                data-bs-toggle="modal"
                                                data-bs-target="#viewUnitModal"
                                                data-payload="{{ base64_encode(json_encode($viewPayload, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE)) }}">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @canPage('units-masterlist', 'update')
                                        <a href="{{ route('units-masterlist.edit', $unit) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                        @endcanPage
                                        @canPage('units-masterlist', 'delete')
                                        <form action="{{ route('units-masterlist.destroy', $unit) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this unit from the masterlist?');">
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
                    {{ $units->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-list fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No units in masterlist yet</h5>
                    <p class="text-muted mb-3">Import the pricelist masterlist or add a unit manually.</p>
                    @canPage('units-masterlist', 'create')
                    <a href="{{ route('units-masterlist.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Unit</a>
                    @endcanPage
                </div>
            @endif
        </div>
    </div>
</div>

{{-- View Unit Modal --}}
<div class="modal fade" id="viewUnitModal" tabindex="-1" aria-labelledby="viewUnitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content overflow-hidden">
            <div class="modal-header bg-dark text-white border-0 rounded-0">
                <h5 class="modal-title" id="viewUnitModalLabel">
                    <i class="fas fa-car me-2"></i><span id="viewUnitTitle">Unit Details</span>
                    <span class="badge bg-secondary ms-2 d-none" id="viewUnitNumber"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="text-muted small">Plate Number</div>
                        <div class="fw-semibold" id="viewUnitPlate">—</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Year</div>
                        <div id="viewUnitYear">—</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Mileage</div>
                        <div id="viewUnitMileage">—</div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-muted small">Variant</div>
                        <div id="viewUnitVariant">—</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Transmission</div>
                        <div id="viewUnitTransmission">—</div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-muted small">Fuel Type</div>
                        <div id="viewUnitFuel">—</div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-muted small">Price</div>
                        <div class="fw-bold fs-5" id="viewUnitPrice">—</div>
                    </div>
                    <div class="col-md-8">
                        <div class="text-muted small">Linked Vehicle Profile</div>
                        <div id="viewUnitVehicle">—</div>
                    </div>
                    <div class="col-12 d-none" id="viewUnitNotesWrap">
                        <div class="text-muted small">Notes</div>
                        <div id="viewUnitNotes">—</div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100 bg-light">
                            <div class="fw-semibold mb-2"><i class="fas fa-hand-holding-usd me-1"></i>Low Down Payment Option</div>
                            <pre class="mb-0 small" id="viewUnitDown" style="white-space: pre-wrap; font-family: inherit;">—</pre>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="border rounded p-3 h-100 bg-light">
                            <div class="fw-semibold mb-2"><i class="fas fa-calendar-alt me-1"></i>Low Monthly Option</div>
                            <pre class="mb-0 small" id="viewUnitMonthly" style="white-space: pre-wrap; font-family: inherit;">—</pre>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                @canPage('units-masterlist', 'update')
                <a href="#" class="btn btn-primary btn-sm" id="viewUnitEditLink">
                    <i class="fas fa-edit me-1"></i>Edit
                </a>
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
    const modal = document.getElementById('viewUnitModal');
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
        if (!btn || !btn.classList.contains('view-unit-btn')) return;
        const data = parsePayload(btn.getAttribute('data-payload'));

        document.getElementById('viewUnitTitle').textContent = data.make_model || 'Unit Details';

        const numBadge = document.getElementById('viewUnitNumber');
        if (data.list_number) {
            numBadge.textContent = '#' + data.list_number;
            numBadge.classList.remove('d-none');
        } else {
            numBadge.classList.add('d-none');
        }

        setLinkOrText(
            document.getElementById('viewUnitPlate'),
            data.plate_number,
            data.vehicle_url || null
        );
        setText('viewUnitYear', data.year);
        setText('viewUnitMileage', data.mileage);
        setText('viewUnitVariant', data.variant);
        setText('viewUnitTransmission', data.transmission);
        setText('viewUnitFuel', data.fuel_type);
        setText('viewUnitPrice', data.price);
        setLinkOrText(
            document.getElementById('viewUnitVehicle'),
            data.vehicle_label || 'Not linked',
            data.vehicle_url || null
        );

        const notesWrap = document.getElementById('viewUnitNotesWrap');
        if (data.notes && String(data.notes).trim() !== '') {
            notesWrap.classList.remove('d-none');
            setText('viewUnitNotes', data.notes);
        } else {
            notesWrap.classList.add('d-none');
        }

        setText('viewUnitDown', data.low_down_payment_option);
        setText('viewUnitMonthly', data.low_monthly_option);

        document.getElementById('viewUnitEditLink').href = data.edit_url || '#';
    });
})();
</script>
@endsection
