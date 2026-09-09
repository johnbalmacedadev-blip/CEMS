@extends('layouts.app')

@section('title', 'Chemical Inventory - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-flask me-2"></i>Chemical Inventory
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0 flex-wrap gap-2">
            @if($tab === 'stock')
                <button type="button" class="btn btn-primary" onclick="openStockModal()">
                    <i class="fas fa-plus me-1"></i>Add Stock
                </button>
            @else
                <button type="button" class="btn btn-primary" onclick="openMovementModal()">
                    <i class="fas fa-plus me-1"></i>Add Movement
                </button>
            @endif
            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'stock' ? 'active' : '' }}"
               href="{{ route('chemical-inventory.index', array_filter(['tab' => 'stock', 'location' => $location ?: null, 'q' => $search])) }}">
                <i class="fas fa-boxes me-1"></i>CURRENT STOCK
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'movements' ? 'active' : '' }}"
               href="{{ route('chemical-inventory.index', array_filter(['tab' => 'movements', 'location' => $location ?: null, 'q' => $search, 'period' => $period ?: null])) }}">
                <i class="fas fa-exchange-alt me-1"></i>INCOMING / OUTGOING
            </a>
        </li>
    </ul>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('chemical-inventory.index') }}" class="row g-2 align-items-end">
                <input type="hidden" name="tab" value="{{ $tab }}">
                <div class="col-md-3">
                    <label class="form-label small mb-1">Location</label>
                    <select name="location" class="form-select">
                        <option value="">All locations</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc }}" {{ $location === $loc ? 'selected' : '' }}>{{ strtoupper($loc) }}</option>
                        @endforeach
                    </select>
                </div>
                @if($tab === 'movements')
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Period</label>
                        <select name="period" class="form-select">
                            <option value="">All periods</option>
                            @foreach($periods as $p)
                                <option value="{{ $p }}" {{ $period === $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="col-md-4">
                    <label class="form-label small mb-1">Search</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="q" value="{{ $search }}" placeholder="Item, counted by, moved by…">
                    </div>
                </div>
                <div class="col-md-3 d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filter</button>
                    <a href="{{ route('chemical-inventory.index', ['tab' => $tab]) }}" class="btn btn-outline-secondary">Clear</a>
                </div>
            </form>
            <div class="mt-2 d-flex flex-wrap gap-2 small">
                <span class="text-muted">Quick location:</span>
                <a class="badge text-bg-light border" href="{{ route('chemical-inventory.index', array_filter(['tab' => $tab, 'q' => $search, 'period' => $period ?: null])) }}">All</a>
                <a class="badge text-bg-light border" href="{{ route('chemical-inventory.index', array_filter(['tab' => $tab, 'location' => 'Premium', 'q' => $search, 'period' => $period ?: null])) }}">Premium</a>
                <a class="badge text-bg-light border" href="{{ route('chemical-inventory.index', array_filter(['tab' => $tab, 'location' => 'Annex', 'q' => $search, 'period' => $period ?: null])) }}">Annex</a>
            </div>
        </div>
    </div>

    @if($tab === 'stock')
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Items</div>
                        <div class="fs-4 fw-bold">{{ number_format($stockCount) }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100 border-primary">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Total existing count</div>
                        <div class="fs-4 fw-bold text-primary">{{ number_format($stockTotalQty) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="fas fa-boxes me-2"></i>Current stock</h5>
                @if($stocks)
                    <span class="badge bg-primary">{{ $stocks->total() }} item(s)</span>
                @endif
            </div>
            <div class="card-body p-0">
                @if(!$stocks || $stocks->total() === 0)
                    <div class="text-center text-muted py-5">No stock records found.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width:4rem;">#</th>
                                    <th>Location</th>
                                    <th>Item</th>
                                    <th>Existing count</th>
                                    <th>Date of count</th>
                                    <th>Counted by</th>
                                    <th>Location stored</th>
                                    <th>Verified by photo?</th>
                                    <th class="text-end" style="width:7rem;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($stocks as $index => $row)
                                    <tr>
                                        <td>{{ $stocks->firstItem() + $index }}</td>
                                        <td><span class="badge bg-secondary">{{ strtoupper($row->location) }}</span></td>
                                        <td><strong>{{ $row->item }}</strong></td>
                                        <td><span class="badge bg-primary">{{ $row->existing_count }}</span></td>
                                        <td>{{ $row->count_date ? $row->count_date->format('M d, Y') : '—' }}</td>
                                        <td>{{ $row->counted_by ?: '—' }}</td>
                                        <td>{{ $row->location_stored ?: '—' }}</td>
                                        <td>{{ $row->verified_by_photo ?: '—' }}</td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                    onclick='openStockModal(@json($row))' title="Edit">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <form action="{{ route('chemical-inventory.stocks.destroy', $row) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this stock item?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($stocks->hasPages())
                        <div class="p-3">{{ $stocks->links() }}</div>
                    @endif
                @endif
            </div>
        </div>
    @else
        <div class="row g-3 mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase fw-semibold">Movement records</div>
                        <div class="fs-4 fw-bold">{{ number_format($movementCount) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0"><i class="fas fa-exchange-alt me-2"></i>Incoming / Outgoing</h5>
                @if($movements)
                    <span class="badge bg-primary">{{ $movements->total() }} item(s)</span>
                @endif
            </div>
            <div class="card-body p-0">
                @if(!$movements || $movements->total() === 0)
                    <div class="text-center text-muted py-5">No movement records found.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th style="width:4rem;">#</th>
                                    <th>Location</th>
                                    <th>Period</th>
                                    <th>Item</th>
                                    <th>Brand</th>
                                    <th>Size / pieces</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>By</th>
                                    <th>Remaining</th>
                                    <th class="text-end" style="width:7rem;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($movements as $index => $row)
                                    <tr>
                                        <td>{{ $movements->firstItem() + $index }}</td>
                                        <td><span class="badge bg-secondary">{{ strtoupper($row->location) }}</span></td>
                                        <td>{{ $row->period_label ?: '—' }}</td>
                                        <td><strong>{{ $row->item }}</strong></td>
                                        <td>{{ $row->brand ?: '—' }}</td>
                                        <td>{{ $row->size_or_pieces ?: '—' }}</td>
                                        <td>{{ $row->movement_date ? $row->movement_date->format('M d, Y') : '—' }}</td>
                                        <td>
                                            @if($row->movement_type === 'ADD')
                                                <span class="badge bg-success">ADD</span>
                                            @elseif($row->movement_type === 'REMOVE')
                                                <span class="badge bg-warning text-dark">REMOVE</span>
                                            @else
                                                {{ $row->movement_type ?: '—' }}
                                            @endif
                                        </td>
                                        <td>{{ $row->quantity_text ?: ($row->quantity !== null ? $row->quantity : '—') }}</td>
                                        <td>{{ $row->moved_by ?: '—' }}</td>
                                        <td>{{ $row->remaining_count !== null ? $row->remaining_count : '—' }}</td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                    onclick='openMovementModal(@json($row))' title="Edit">
                                                <i class="fas fa-pen"></i>
                                            </button>
                                            <form action="{{ route('chemical-inventory.movements.destroy', $row) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this movement?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($movements->hasPages())
                        <div class="p-3">{{ $movements->links() }}</div>
                    @endif
                @endif
            </div>
        </div>
    @endif
</div>

{{-- Stock modal --}}
<div class="modal fade" id="stockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="stockForm" class="modal-content">
            @csrf
            <input type="hidden" name="_method" id="stock_method" value="POST">
            <input type="hidden" name="filter_location" value="{{ $location }}">
            <input type="hidden" name="q" value="{{ $search }}">
            <div class="modal-header">
                <h5 class="modal-title" id="stockModalLabel">Add Stock</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Location <span class="text-danger">*</span></label>
                    <select name="location" id="stock_location" class="form-select" required>
                        @foreach($locations as $loc)
                            <option value="{{ $loc }}">{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Item <span class="text-danger">*</span></label>
                    <input type="text" name="item" id="stock_item" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Existing count <span class="text-danger">*</span></label>
                    <input type="number" name="existing_count" id="stock_existing_count" class="form-control" min="0" value="0" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Date of existing count</label>
                    <input type="date" name="count_date" id="stock_count_date" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Counted by</label>
                    <input type="text" name="counted_by" id="stock_counted_by" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Location stored</label>
                    <input type="text" name="location_stored" id="stock_location_stored" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Verified by photo?</label>
                    <input type="text" name="verified_by_photo" id="stock_verified_by_photo" class="form-control" placeholder="YES / N/A">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>

{{-- Movement modal --}}
<div class="modal fade" id="movementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="movementForm" class="modal-content">
            @csrf
            <input type="hidden" name="_method" id="movement_method" value="POST">
            <input type="hidden" name="filter_location" value="{{ $location }}">
            <input type="hidden" name="period" value="{{ $period }}">
            <input type="hidden" name="q" value="{{ $search }}">
            <div class="modal-header">
                <h5 class="modal-title" id="movementModalLabel">Add Movement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Location <span class="text-danger">*</span></label>
                        <select name="location" id="movement_location" class="form-select" required>
                            @foreach($locations as $loc)
                                <option value="{{ $loc }}">{{ $loc }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Period</label>
                        <input type="text" name="period_label" id="movement_period_label" class="form-control" placeholder="JULY / AUGUST">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date</label>
                        <input type="date" name="movement_date" id="movement_date" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Item <span class="text-danger">*</span></label>
                        <input type="text" name="item" id="movement_item" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Brand</label>
                        <input type="text" name="brand" id="movement_brand" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Size / pieces</label>
                        <input type="text" name="size_or_pieces" id="movement_size_or_pieces" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Type</label>
                        <select name="movement_type" id="movement_type" class="form-select">
                            <option value="">—</option>
                            <option value="ADD">ADD</option>
                            <option value="REMOVE">REMOVE</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Description</label>
                        <input type="text" name="quantity_text" id="movement_quantity_text" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Added / removed by</label>
                        <input type="text" name="moved_by" id="movement_moved_by" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Remaining count</label>
                        <input type="number" name="remaining_count" id="movement_remaining_count" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openStockModal(row = null) {
    const form = document.getElementById('stockForm');
    const method = document.getElementById('stock_method');
    document.getElementById('stockModalLabel').textContent = row ? 'Edit Stock' : 'Add Stock';
    if (row) {
        form.action = `/chemical-inventory/stocks/${row.id}`;
        method.value = 'PUT';
        document.getElementById('stock_location').value = row.location || 'Premium';
        document.getElementById('stock_item').value = row.item || '';
        document.getElementById('stock_existing_count').value = row.existing_count ?? 0;
        document.getElementById('stock_count_date').value = row.count_date ? String(row.count_date).substring(0, 10) : '';
        document.getElementById('stock_counted_by').value = row.counted_by || '';
        document.getElementById('stock_location_stored').value = row.location_stored || '';
        document.getElementById('stock_verified_by_photo').value = row.verified_by_photo || '';
    } else {
        form.action = @json(route('chemical-inventory.stocks.store'));
        method.value = 'POST';
        form.reset();
        document.getElementById('stock_location').value = @json($location ?: 'Premium');
    }
    new bootstrap.Modal(document.getElementById('stockModal')).show();
}

function openMovementModal(row = null) {
    const form = document.getElementById('movementForm');
    const method = document.getElementById('movement_method');
    document.getElementById('movementModalLabel').textContent = row ? 'Edit Movement' : 'Add Movement';
    if (row) {
        form.action = `/chemical-inventory/movements/${row.id}`;
        method.value = 'PUT';
        document.getElementById('movement_location').value = row.location || 'Premium';
        document.getElementById('movement_period_label').value = row.period_label || '';
        document.getElementById('movement_date').value = row.movement_date ? String(row.movement_date).substring(0, 10) : '';
        document.getElementById('movement_item').value = row.item || '';
        document.getElementById('movement_brand').value = row.brand || '';
        document.getElementById('movement_size_or_pieces').value = row.size_or_pieces || '';
        document.getElementById('movement_type').value = row.movement_type || '';
        document.getElementById('movement_quantity_text').value = row.quantity_text || '';
        document.getElementById('movement_moved_by').value = row.moved_by || '';
        document.getElementById('movement_remaining_count').value = row.remaining_count ?? '';
    } else {
        form.action = @json(route('chemical-inventory.movements.store'));
        method.value = 'POST';
        form.reset();
        document.getElementById('movement_location').value = @json($location ?: 'Premium');
        document.getElementById('movement_period_label').value = @json($period ?: '');
    }
    new bootstrap.Modal(document.getElementById('movementModal')).show();
}
</script>
@endsection
