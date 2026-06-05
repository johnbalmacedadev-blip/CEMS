@extends('layouts.app')

@section('title', 'Transfer OR/CR - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-file-invoice me-2"></i>Transfer OR/CR
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
            <a href="{{ route('transfer-orcr.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add Record
            </a>
        </div>
    </div>


    <p class="text-muted mb-3">Track OR/CR transfer transactions: LTO file, transfer SOP/OR, PNP clearance, RD, and status. <span class="text-muted small"><i class="fas fa-arrows-alt-h me-1"></i>Click and drag the table left or right to scroll.</span></p>

    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('transfer-orcr.index') }}" class="row g-2 align-items-end">
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
                    <select class="form-select form-select-sm" name="status" style="width: auto;">
                        <option value="">All</option>
                        @foreach(\App\Models\TransferOrcr::statusOptions() as $opt)
                            <option value="{{ $opt }}" {{ request('status') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
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
                    <input type="text" class="form-control form-control-sm" name="search" placeholder="Plate, make, series..." value="{{ request('search') }}" style="width: 180px;">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search me-1"></i>Filter</button>
                    <a href="{{ route('transfer-orcr.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                    <a href="{{ route('transfer-orcr.export-pdf', request()->only(['branch_location_id', 'status', 'date_from', 'date_to', 'search'])) }}"
                        class="btn btn-outline-danger btn-sm" target="_blank" rel="noopener">
                        <i class="fas fa-file-pdf me-1"></i>Export PDF
                    </a>
                    <a href="{{ route('transfer-orcr.summary-report', request()->only(['branch_location_id'])) }}"
                        class="btn btn-outline-info btn-sm">
                        <i class="fas fa-chart-bar me-1"></i>View Summary Report
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($records->isNotEmpty())
                <div class="table-responsive transfer-orcr-drag-scroll" id="transferOrcrTableScroll">
                    <table class="table table-bordered align-middle mb-0 transfer-orcr-table">
                        <thead class="table-dark">
                            <tr>
                                <th>BRANCH</th>
                                <th>DATE</th>
                                <th>YEAR</th>
                                <th>MAKE</th>
                                <th>SERIES</th>
                                <th>PLATE</th>
                                <th>TRANSACTION TYPE</th>
                                <th>REMARK</th>
                                <th>LTO FILE NO</th>
                                <th>TRANSFER SOP</th>
                                <th>TRANSFER OR</th>
                                <th>OTHERS</th>
                                <th>PNP CLEARANCE</th>
                                <th>CONFIRMATION</th>
                                <th>HAND CARRY</th>
                                <th>RD</th>
                                <th>RD SOP</th>
                                <th>RD OR</th>
                                <th>REMARKS</th>
                                <th>STATUS</th>
                                <th>DATE</th>
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
                                    <td>{{ $v->year ?? '—' }}</td>
                                    <td>{{ $makeName ?: '—' }}</td>
                                    <td>{{ $seriesName ?: '—' }}</td>
                                    <td>
                                        @if($r->vehicle_id && $v)
                                            <a href="{{ route('vehicles.show', $v) }}">{{ $v->plate_number ?? '—' }}</a>
                                        @else
                                            {{ $v ? ($v->plate_number ?? '—') : '—' }}
                                        @endif
                                    </td>
                                    <td>{{ $r->transaction_type ?: '—' }}</td>
                                    <td>{{ $r->remark ?: '—' }}</td>
                                    <td>{{ $r->lto_file_no ?: '—' }}</td>
                                    <td>{{ $r->transfer_sop > 0 ? number_format((float)$r->transfer_sop, 2) : '—' }}</td>
                                    <td>{{ $r->transfer_or ? number_format((float)$r->transfer_or, 2) : '—' }}</td>
                                    <td>
                                        @if($r->others !== null && (float)$r->others > 0)
                                            {{ number_format((float)$r->others, 2) }}
                                        @elseif($r->others_note)
                                            {{ $r->others_note }}
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td>{{ $r->pnp_clearance ? number_format((float)$r->pnp_clearance, 2) : '—' }}</td>
                                    <td>{{ $r->confirmation !== null ? number_format((float)$r->confirmation, 2) : '—' }}</td>
                                    <td>{{ $r->notary !== null ? number_format((float)$r->notary, 2) : '—' }}</td>
                                    <td>{{ $r->rd ?: '—' }}</td>
                                    <td>{{ $r->rd_sop !== null ? number_format((float)$r->rd_sop, 2) : '—' }}</td>
                                    <td>{{ $r->rd_or !== null ? number_format((float)$r->rd_or, 2) : '—' }}</td>
                                    <td class="small">{{ Str::limit($r->remarks, 30) ?: '—' }}</td>
                                    <td><span class="fw-bold {{ $r->status === 'DONE' ? 'text-danger' : '' }}">{{ $r->status ?: '—' }}</span></td>
                                    <td>{{ $r->release_date ? $r->release_date->format('j M Y') : '—' }}</td>
                                    <td class="fw-bold text-danger">{{ number_format($r->feeTotal(), 2) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('transfer-orcr.edit', $r) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('transfer-orcr.destroy', $r) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this record?');">
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
                    <i class="fas fa-file-invoice fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No transfer OR/CR records yet</h5>
                    <p class="text-muted mb-3">Add records to track OR/CR transfers by vehicle.</p>
                    <a href="{{ route('transfer-orcr.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Record</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.transfer-orcr-table { font-size: 0.8rem; }
.transfer-orcr-table th { white-space: nowrap; padding: 0.5rem 0.4rem; }
.transfer-orcr-table td { padding: 0.4rem; vertical-align: middle; background-color: #fff; }
.transfer-orcr-table th:last-of-type,
.transfer-orcr-table td.text-danger { white-space: nowrap; }
.transfer-orcr-drag-scroll {
    overflow-x: auto;
    cursor: grab;
    -webkit-overflow-scrolling: touch;
}
.transfer-orcr-drag-scroll.is-dragging {
    cursor: grabbing;
    user-select: none;
}
.transfer-orcr-drag-scroll.is-dragging * {
    user-select: none;
}
.transfer-orcr-drag-scroll.is-dragging a,
.transfer-orcr-drag-scroll.is-dragging button {
    pointer-events: none;
}
</style>
@endsection

@section('scripts')
<script>
(function () {
    const scroller = document.getElementById('transferOrcrTableScroll');
    if (!scroller) return;

    let isDragging = false;
    let startX = 0;
    let scrollStart = 0;
    let moved = false;

    function isInteractiveTarget(el) {
        return el && el.closest('a, button, input, select, textarea, label, .btn');
    }

    function startDrag(clientX) {
        isDragging = true;
        moved = false;
        startX = clientX;
        scrollStart = scroller.scrollLeft;
        scroller.classList.add('is-dragging');
    }

    function moveDrag(clientX) {
        if (!isDragging) return;
        const delta = clientX - startX;
        if (Math.abs(delta) > 3) moved = true;
        scroller.scrollLeft = scrollStart - delta;
    }

    function endDrag() {
        if (!isDragging) return;
        isDragging = false;
        scroller.classList.remove('is-dragging');
    }

    scroller.addEventListener('mousedown', function (e) {
        if (e.button !== 0 || isInteractiveTarget(e.target)) return;
        startDrag(e.pageX);
        e.preventDefault();
    });

    window.addEventListener('mousemove', function (e) {
        if (!isDragging) return;
        e.preventDefault();
        moveDrag(e.pageX);
    });

    window.addEventListener('mouseup', endDrag);

    scroller.addEventListener('touchstart', function (e) {
        if (isInteractiveTarget(e.target) || e.touches.length !== 1) return;
        startDrag(e.touches[0].pageX);
    }, { passive: true });

    scroller.addEventListener('touchmove', function (e) {
        if (!isDragging) return;
        moveDrag(e.touches[0].pageX);
    }, { passive: true });

    scroller.addEventListener('touchend', endDrag);

    scroller.addEventListener('click', function (e) {
        if (moved && isInteractiveTarget(e.target)) {
            e.preventDefault();
            e.stopPropagation();
        }
        moved = false;
    }, true);
})();
</script>
@endsection
