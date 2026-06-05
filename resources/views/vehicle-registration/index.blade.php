@extends('layouts.app')

@section('title', 'Vehicle Registration - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-id-card me-2"></i>Vehicle Registration
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
            <a href="{{ route('vehicle-registration.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add Record
            </a>
        </div>
    </div>

    <p class="text-muted mb-3">Track vehicle registration renewals, smoke test, duplicate plates, PNP clearance, and COC. <span class="text-muted small"><i class="fas fa-arrows-alt-h me-1"></i>Click and drag the table left or right to scroll.</span></p>

    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('vehicle-registration.index') }}" class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label small mb-0">Branch / Store Location</label>
                    <select class="form-select form-select-sm" name="branch_location_id" style="width: auto; min-width: 160px;">
                        <option value="">All</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ (string) request('branch_location_id') === (string) $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label small mb-0">Status</label>
                    <input type="text" class="form-control form-control-sm" name="status" value="{{ request('status') }}" placeholder="Status..." style="width: 140px;">
                </div>
                <div class="col-auto">
                    <label class="form-label small mb-0">Date From</label>
                    <input type="date" class="form-control form-control-sm" name="date_from" value="{{ request('date_from') }}" style="width: auto;">
                </div>
                <div class="col-auto">
                    <label class="form-label small mb-0">Date To</label>
                    <input type="date" class="form-control form-control-sm" name="date_to" value="{{ request('date_to') }}" style="width: auto;">
                </div>
                <div class="col-auto">
                    <label class="form-label small mb-0">Search</label>
                    <input type="text" class="form-control form-control-sm" name="search" placeholder="Plate, make, COC..." value="{{ request('search') }}" style="width: 180px;">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search me-1"></i>Filter</button>
                    <a href="{{ route('vehicle-registration.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($records->isNotEmpty())
                <div class="table-responsive vehicle-reg-drag-scroll" id="vehicleRegTableScroll">
                    <table class="table table-bordered align-middle mb-0 vehicle-reg-table">
                        <thead class="table-dark">
                            <tr>
                                <th>BRANCH</th>
                                <th>DATE</th>
                                <th>PLATE</th>
                                <th>MAKE</th>
                                <th>SERIES</th>
                                <th>YEAR</th>
                                <th>RENEWAL REG. OR</th>
                                <th>RENEWAL SOP</th>
                                <th>SMOKE NA</th>
                                <th>DUPLICATE PLATE</th>
                                <th>MIGRATE</th>
                                <th>DUPLICATE CR</th>
                                <th>PNP CLEARANCE</th>
                                <th>CONFIRMATION</th>
                                <th>REMARKS</th>
                                <th>COC NO.</th>
                                <th>STATUS</th>
                                <th>TOTAL</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $r)
                                @php
                                    $v = $r->vehicle;
                                    $makeName = $v->make && is_object($v->make) ? $v->make->name : ($v->make ?? '');
                                    $seriesName = $v->vehicleModel && is_object($v->vehicleModel) ? $v->vehicleModel->name : ($v->model ?? '');
                                @endphp
                                <tr>
                                    <td>{{ $r->branchLocation?->name ?: '—' }}</td>
                                    <td>{{ $r->date->format('j M Y') }}</td>
                                    <td>
                                        @if($r->vehicle_id && $v)
                                            <a href="{{ route('vehicles.show', $v) }}">{{ $v->plate_number ?? '—' }}</a>
                                        @else
                                            {{ $v ? ($v->plate_number ?? '—') : '—' }}
                                        @endif
                                    </td>
                                    <td>{{ $makeName ?: '—' }}</td>
                                    <td>{{ $seriesName ?: '—' }}</td>
                                    <td>{{ $v->year ?? '—' }}</td>
                                    <td>{{ $r->renewal_reg_or !== null ? number_format((float)$r->renewal_reg_or, 2) : '—' }}</td>
                                    <td>{{ $r->renewal_sop !== null ? number_format((float)$r->renewal_sop, 2) : '—' }}</td>
                                    <td>{{ $r->smoke_na !== null ? number_format((float)$r->smoke_na, 2) : '—' }}</td>
                                    <td>{{ $r->duplicate_plate !== null ? number_format((float)$r->duplicate_plate, 2) : '—' }}</td>
                                    <td>{{ $r->migrate !== null ? number_format((float)$r->migrate, 2) : '—' }}</td>
                                    <td>{{ $r->duplicate_cr !== null ? number_format((float)$r->duplicate_cr, 2) : '—' }}</td>
                                    <td>{{ $r->pnp_clearance !== null ? number_format((float)$r->pnp_clearance, 2) : '—' }}</td>
                                    <td>{{ $r->confirmation !== null ? number_format((float)$r->confirmation, 2) : '—' }}</td>
                                    <td class="small">{{ Str::limit($r->remarks, 30) ?: '—' }}</td>
                                    <td>{{ $r->coc_no ?: '—' }}</td>
                                    <td><span class="fw-bold {{ str_contains(strtoupper($r->status ?? ''), 'DONE') ? 'text-danger' : '' }}">{{ $r->status ?: '—' }}</span></td>
                                    <td class="fw-bold text-danger">{{ number_format($r->feeTotal(), 2) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('vehicle-registration.edit', $r) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('vehicle-registration.destroy', $r) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this record?');">
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
                    <i class="fas fa-id-card fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No vehicle registration records yet</h5>
                    <p class="text-muted mb-3">Add records to track registration renewals by vehicle.</p>
                    <a href="{{ route('vehicle-registration.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Record</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.vehicle-reg-table { font-size: 0.8rem; }
.vehicle-reg-table th { white-space: nowrap; padding: 0.5rem 0.4rem; }
.vehicle-reg-table td { padding: 0.4rem; vertical-align: middle; background-color: #fff; }
.vehicle-reg-drag-scroll { overflow-x: auto; cursor: grab; -webkit-overflow-scrolling: touch; }
.vehicle-reg-drag-scroll.is-dragging { cursor: grabbing; user-select: none; }
.vehicle-reg-drag-scroll.is-dragging a,
.vehicle-reg-drag-scroll.is-dragging button { pointer-events: none; }
</style>
@endsection

@section('scripts')
<script>
(function () {
    const scroller = document.getElementById('vehicleRegTableScroll');
    if (!scroller) return;
    let isDragging = false, startX = 0, scrollStart = 0, moved = false;
    function isInteractiveTarget(el) { return el && el.closest('a, button, input, select, textarea, label, .btn'); }
    function startDrag(x) { isDragging = true; moved = false; startX = x; scrollStart = scroller.scrollLeft; scroller.classList.add('is-dragging'); }
    function moveDrag(x) { if (!isDragging) return; const d = x - startX; if (Math.abs(d) > 3) moved = true; scroller.scrollLeft = scrollStart - d; }
    function endDrag() { if (!isDragging) return; isDragging = false; scroller.classList.remove('is-dragging'); }
    scroller.addEventListener('mousedown', e => { if (e.button !== 0 || isInteractiveTarget(e.target)) return; startDrag(e.pageX); e.preventDefault(); });
    window.addEventListener('mousemove', e => { if (!isDragging) return; e.preventDefault(); moveDrag(e.pageX); });
    window.addEventListener('mouseup', endDrag);
    scroller.addEventListener('touchstart', e => { if (isInteractiveTarget(e.target) || e.touches.length !== 1) return; startDrag(e.touches[0].pageX); }, { passive: true });
    scroller.addEventListener('touchmove', e => { if (!isDragging) return; moveDrag(e.touches[0].pageX); }, { passive: true });
    scroller.addEventListener('touchend', endDrag);
    scroller.addEventListener('click', e => { if (moved && isInteractiveTarget(e.target)) { e.preventDefault(); e.stopPropagation(); } moved = false; }, true);
})();
</script>
@endsection
