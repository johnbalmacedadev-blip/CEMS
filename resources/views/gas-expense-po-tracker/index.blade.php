@extends('layouts.app')

@section('title', 'Gas Expenses / P.O. Tracker - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-gas-pump me-2"></i>Gas Expenses / P.O. Tracker
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
            <a href="{{ route('expenses-inventory') }}" class="btn btn-outline-primary">
                <i class="fas fa-file-invoice-dollar me-1"></i>Expenses & Inventory
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <p class="text-muted mb-4">Track gas expenses by vehicle and manage purchase orders (P.O.).</p>

    <!-- ========== GAS EXPENSES SECTION ========== -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-gas-pump me-2"></i>Gas Expenses</h5>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#gasExpenseModal" id="addGasExpenseBtn">
                <i class="fas fa-plus me-1"></i>Add New
            </button>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('gas-expense-po-tracker.index') }}" class="row g-3 mb-3">
                <input type="hidden" name="po_page" value="{{ request('po_page', 1) }}">
                <div class="col-md-2">
                    <label class="form-label small">Date From</label>
                    <input type="date" class="form-control form-control-sm" name="gas_date_from" value="{{ request('gas_date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Date To</label>
                    <input type="date" class="form-control form-control-sm" name="gas_date_to" value="{{ request('gas_date_to') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Plate No.</label>
                    <input type="text" class="form-control form-control-sm" name="gas_plate" placeholder="Plate..." value="{{ request('gas_plate') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search me-1"></i>Filter</button>
                    <a href="{{ route('gas-expense-po-tracker.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                    <a href="{{ route('gas-expense-po-tracker.export-pdf') }}?{{ http_build_query(array_merge(request()->only(['gas_date_from', 'gas_date_to', 'gas_plate', 'po_status', 'po_date_from', 'po_date_to']), ['section' => 'gas'])) }}" class="btn btn-outline-danger btn-sm" target="_blank" rel="noopener">
                        <i class="fas fa-file-pdf me-1"></i>PDF
                    </a>
                </div>
            </form>

            @if($gasExpenses->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Vehicle / Plate</th>
                                <th>Driver</th>
                                <th>Model</th>
                                <th class="text-end">Amount</th>
                                <th>Sent By</th>
                                <th>Checked By</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gasExpenses as $expense)
                                <tr>
                                    <td>{{ $expense->date->format('M d, Y') }}</td>
                                    <td>
                                        @if($expense->vehicle)
                                            <a href="{{ route('vehicles.show', $expense->vehicle) }}">{{ $expense->vehicle->full_name }}</a>
                                            <br><small class="text-muted">{{ $expense->plate_number }}</small>
                                        @else
                                            <span class="text-muted">{{ $expense->plate_number }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $expense->driver }}</td>
                                    <td>{{ $expense->model }}</td>
                                    <td class="text-end">₱{{ number_format($expense->gas_amount, 2) }}</td>
                                    <td>{{ $expense->expense_sent_by }}</td>
                                    <td>{{ $expense->checked_by }}</td>
                                    <td class="text-center">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary edit-gas-expense-btn"
                                                title="Edit"
                                                data-bs-toggle="modal"
                                                data-bs-target="#gasExpenseModal"
                                                data-expense-id="{{ $expense->id }}"
                                                data-vehicle-id="{{ $expense->vehicle?->id ?? '' }}"
                                                data-vehicle-label="{{ $expense->vehicle ? $expense->plate_number . ' — ' . $expense->vehicle->full_name : $expense->plate_number }}"
                                                data-date="{{ $expense->date->format('Y-m-d') }}"
                                                data-driver="{{ $expense->driver }}"
                                                data-model="{{ $expense->model }}"
                                                data-gas-amount="{{ $expense->gas_amount }}"
                                                data-expense-sent-by="{{ $expense->expense_sent_by }}"
                                                data-checked-by="{{ $expense->checked_by }}"
                                                data-has-photo-video="{{ $expense->has_photo_video_in_groupchat ? '1' : '0' }}"
                                                data-photo-before="{{ $expense->photo_fuel_gauge_before ? '1' : '0' }}"
                                                data-photo-after="{{ $expense->photo_fuel_gauge_after ? '1' : '0' }}"
                                                data-photo-plate="{{ $expense->photo_car_license_plate_gas_boy ? '1' : '0' }}"
                                                data-photo-receipt="{{ $expense->photo_receipt_next_to_gas_pump ? '1' : '0' }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger delete-gas-expense-btn"
                                                title="Delete"
                                                data-expense-id="{{ $expense->id }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        @if($expense->vehicle)
                                            <a href="{{ route('vehicles.show', $expense->vehicle) }}#gas-expense" class="btn btn-sm btn-outline-secondary" title="View on vehicle"><i class="fas fa-external-link-alt"></i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $gasExpenses->withQueryString()->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-gas-pump fa-2x mb-2"></i>
                    <p class="mb-0">No gas expenses found. Click <strong>Add New</strong> to record one.</p>
                    <button type="button" class="btn btn-sm btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#gasExpenseModal">
                        <i class="fas fa-plus me-1"></i>Add New
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- ========== P.O. TRACKER SECTION ========== -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>P.O. Tracker</h5>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addPoModal">
                <i class="fas fa-plus me-1"></i>Add P.O.
            </button>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('gas-expense-po-tracker.index') }}" class="row g-3 mb-3">
                <input type="hidden" name="gas_page" value="{{ request('gas_page', 1) }}">
                <div class="col-md-2">
                    <label class="form-label small">Status</label>
                    <select class="form-select form-select-sm" name="po_status">
                        <option value="">All</option>
                        @foreach(\App\Models\PurchaseOrder::statusOptions() as $opt)
                            <option value="{{ $opt }}" {{ request('po_status') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Date From</label>
                    <input type="date" class="form-control form-control-sm" name="po_date_from" value="{{ request('po_date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Date To</label>
                    <input type="date" class="form-control form-control-sm" name="po_date_to" value="{{ request('po_date_to') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search me-1"></i>Filter</button>
                    <a href="{{ route('gas-expense-po-tracker.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                    <a href="{{ route('gas-expense-po-tracker.export-pdf') }}?{{ http_build_query(array_merge(request()->only(['gas_date_from', 'gas_date_to', 'gas_plate', 'po_status', 'po_date_from', 'po_date_to']), ['section' => 'po'])) }}" class="btn btn-outline-danger btn-sm" target="_blank" rel="noopener">
                        <i class="fas fa-file-pdf me-1"></i>PDF
                    </a>
                </div>
            </form>

            @if($purchaseOrders->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>P.O. Number</th>
                                <th>Date</th>
                                <th>Vendor</th>
                                <th>Description</th>
                                <th class="text-end">Amount</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($purchaseOrders as $po)
                                <tr>
                                    <td>{{ $po->po_number ?: '—' }}</td>
                                    <td>{{ $po->po_date->format('M d, Y') }}</td>
                                    <td>{{ $po->vendor ?: '—' }}</td>
                                    <td><span class="text-muted small">{{ Str::limit($po->description, 30) ?: '—' }}</span></td>
                                    <td class="text-end">₱{{ number_format($po->amount, 2) }}</td>
                                    <td>
                                        @if($po->status === 'Received')
                                            <span class="badge bg-success">{{ $po->status }}</span>
                                        @elseif($po->status === 'Ordered')
                                            <span class="badge bg-info">{{ $po->status }}</span>
                                        @elseif($po->status === 'Cancelled')
                                            <span class="badge bg-secondary">{{ $po->status }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ $po->status }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editPoModal" data-po-id="{{ $po->id }}" data-po-number="{{ $po->po_number }}" data-po-date="{{ $po->po_date->format('Y-m-d') }}" data-po-vendor="{{ $po->vendor }}" data-po-description="{{ $po->description }}" data-po-amount="{{ $po->amount }}" data-po-status="{{ $po->status }}" data-po-notes="{{ $po->notes }}" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('gas-expense-po-tracker.destroy-po', $po) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this P.O.?');">
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
                <div class="d-flex justify-content-center mt-3">
                    {{ $purchaseOrders->withQueryString()->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-file-invoice fa-2x mb-2"></i>
                    <p class="mb-0">No purchase orders yet. Click "Add P.O." to add one.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add / Edit Gas Expense Modal -->
<div class="modal fade" id="gasExpenseModal" tabindex="-1" aria-labelledby="gasExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="gasExpenseModalLabel">Add Gas Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="gas_expense_id" value="">
                <input type="hidden" id="gas_vehicle_id" value="">

                <div class="mb-3">
                    <label for="gas_vehicle_search" class="form-label">Search vehicle <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="gas_vehicle_search" placeholder="Plate number, make, model, or year..." autocomplete="off">
                    <div id="gas_vehicle_search_results" class="list-group mt-2 shadow-sm" style="display:none; max-height: 220px; overflow-y: auto;"></div>
                    <div id="gas_vehicle_selected" class="alert alert-success py-2 mt-2 mb-0" style="display:none;">
                        <i class="fas fa-check-circle me-1"></i><span id="gas_vehicle_selected_label"></span>
                        <button type="button" class="btn btn-link btn-sm p-0 ms-2" id="gas_vehicle_clear">Change</button>
                    </div>
                </div>

                <hr>

                <form id="gasExpenseForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="gas_date" class="form-label">Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" id="gas_date" name="date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gas_driver" class="form-label">Driver <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="gas_driver" name="driver" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="gas_model" class="form-label">Model <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="gas_model" name="model" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gas_amount" class="form-label">Gas Amount (₱) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control form-control-sm" id="gas_amount" name="gas_amount" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="gas_expense_sent_by" class="form-label">Expense Sent By <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="gas_expense_sent_by" name="expense_sent_by" required>
                                <option value="">Select</option>
                                <option value="MERLIN">MERLIN</option>
                                <option value="ALYSSA">ALYSSA</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="gas_checked_by" class="form-label">Checked By <span class="text-danger">*</span></label>
                            <input type="text" class="form-control form-control-sm" id="gas_checked_by" name="checked_by" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="gas_has_photo_video" value="1">
                                <label class="form-check-label" for="gas_has_photo_video">Photo/Video in Group Chat</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="gas_photo_before" value="1">
                                <label class="form-check-label" for="gas_photo_before">Photo of Fuel Gauge Before</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="gas_photo_after" value="1">
                                <label class="form-check-label" for="gas_photo_after">Photo of Fuel Gauge After</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="gas_photo_plate" value="1">
                                <label class="form-check-label" for="gas_photo_plate">Photo of Car with License Plate &amp; Gas Boy</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-2">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="gas_photo_receipt" value="1">
                                <label class="form-check-label" for="gas_photo_receipt">Photo of Receipt Next to Gas Pump</label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" id="saveGasExpenseBtn">
                    <i class="fas fa-save me-1"></i>Save Gas Expense
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add P.O. Modal -->
<div class="modal fade" id="addPoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('gas-expense-po-tracker.store-po') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add P.O.</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">P.O. Number</label>
                        <input type="text" class="form-control form-control-sm" name="po_number" placeholder="Optional">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" name="po_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Vendor</label>
                        <input type="text" class="form-control form-control-sm" name="vendor" placeholder="Optional">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Description</label>
                        <input type="text" class="form-control form-control-sm" name="description" placeholder="Optional">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Amount (₱) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-sm" name="amount" step="0.01" min="0" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Status</label>
                        <select class="form-select form-select-sm" name="status" required>
                            @foreach(\App\Models\PurchaseOrder::statusOptions() as $opt)
                                <option value="{{ $opt }}" {{ $opt === 'Pending' ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control form-control-sm" name="notes" rows="2" placeholder="Optional"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i>Save P.O.</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit P.O. Modal -->
<div class="modal fade" id="editPoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editPoForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit P.O.</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">P.O. Number</label>
                        <input type="text" class="form-control form-control-sm" name="po_number" id="edit_po_number" placeholder="Optional">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-sm" name="po_date" id="edit_po_date" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Vendor</label>
                        <input type="text" class="form-control form-control-sm" name="vendor" id="edit_po_vendor" placeholder="Optional">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Description</label>
                        <input type="text" class="form-control form-control-sm" name="description" id="edit_po_description" placeholder="Optional">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Amount (₱) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-sm" name="amount" id="edit_po_amount" step="0.01" min="0" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Status</label>
                        <select class="form-select form-select-sm" name="status" id="edit_po_status" required>
                            @foreach(\App\Models\PurchaseOrder::statusOptions() as $opt)
                                <option value="{{ $opt }}">{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Notes</label>
                        <textarea class="form-control form-control-sm" name="notes" id="edit_po_notes" rows="2" placeholder="Optional"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i>Update P.O.</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const vehicleSearchUrl = @json(route('expenses.vehicles.search'));
    const storeGasUrl = @json(route('gas-expense-po-tracker.store-gas'));
    const updateGasUrlTemplate = @json(route('gas-expense-po-tracker.update-gas', ['gasExpense' => 0]));
    const deleteGasUrlTemplate = @json(route('gas-expense-po-tracker.destroy-gas', ['gasExpense' => 0]));
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const gasModal = document.getElementById('gasExpenseModal');
    const gasModalLabel = document.getElementById('gasExpenseModalLabel');
    const gasExpenseIdInput = document.getElementById('gas_expense_id');
    const gasVehicleIdInput = document.getElementById('gas_vehicle_id');
    const gasVehicleSearch = document.getElementById('gas_vehicle_search');
    const gasVehicleResults = document.getElementById('gas_vehicle_search_results');
    const gasVehicleSelected = document.getElementById('gas_vehicle_selected');
    const gasVehicleSelectedLabel = document.getElementById('gas_vehicle_selected_label');
    const gasVehicleClear = document.getElementById('gas_vehicle_clear');
    const saveGasExpenseBtn = document.getElementById('saveGasExpenseBtn');
    const addGasExpenseBtn = document.getElementById('addGasExpenseBtn');

    let vehicleSearchTimer = null;

    function resetGasExpenseModal() {
        gasExpenseIdInput.value = '';
        gasVehicleIdInput.value = '';
        gasVehicleSearch.value = '';
        gasVehicleSearch.style.display = 'block';
        gasVehicleResults.style.display = 'none';
        gasVehicleResults.innerHTML = '';
        gasVehicleSelected.style.display = 'none';
        gasVehicleSelectedLabel.textContent = '';
        document.getElementById('gasExpenseForm').reset();
        document.getElementById('gas_date').value = new Date().toISOString().split('T')[0];
        gasModalLabel.textContent = 'Add Gas Expense';
        saveGasExpenseBtn.innerHTML = '<i class="fas fa-save me-1"></i>Save Gas Expense';
    }

    function selectVehicle(vehicle) {
        gasVehicleIdInput.value = vehicle.id;
        const label = (vehicle.plate_number || '') + ' — ' + (vehicle.full_name || ((vehicle.year || '') + ' ' + (vehicle.make || '') + ' ' + (vehicle.model || '')).trim());
        gasVehicleSelectedLabel.textContent = label.trim();
        gasVehicleSelected.style.display = 'block';
        gasVehicleSearch.style.display = 'none';
        gasVehicleResults.style.display = 'none';
        gasVehicleResults.innerHTML = '';
        if (!document.getElementById('gas_model').value) {
            document.getElementById('gas_model').value = vehicle.full_name || label;
        }
    }

    function clearSelectedVehicle() {
        gasVehicleIdInput.value = '';
        gasVehicleSelected.style.display = 'none';
        gasVehicleSelectedLabel.textContent = '';
        gasVehicleSearch.style.display = 'block';
        gasVehicleSearch.value = '';
        gasVehicleSearch.focus();
    }

    function searchVehicles(query) {
        if (!query || query.length < 1) {
            gasVehicleResults.style.display = 'none';
            gasVehicleResults.innerHTML = '';
            return;
        }
        fetch(vehicleSearchUrl + '?q=' + encodeURIComponent(query), {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        })
            .then(r => r.json())
            .then(vehicles => {
                gasVehicleResults.innerHTML = '';
                if (!vehicles.length) {
                    gasVehicleResults.innerHTML = '<div class="list-group-item text-muted small">No vehicles found.</div>';
                } else {
                    vehicles.forEach(v => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'list-group-item list-group-item-action py-2';
                        item.textContent = (v.plate_number || '') + ' — ' + (v.full_name || '');
                        item.addEventListener('click', () => selectVehicle(v));
                        gasVehicleResults.appendChild(item);
                    });
                }
                gasVehicleResults.style.display = 'block';
            })
            .catch(() => {
                gasVehicleResults.innerHTML = '<div class="list-group-item text-danger small">Search failed.</div>';
                gasVehicleResults.style.display = 'block';
            });
    }

    function fillGasExpenseFormFromButton(btn) {
        gasExpenseIdInput.value = btn.getAttribute('data-expense-id') || '';
        const vehicleId = btn.getAttribute('data-vehicle-id') || '';
        const vehicleLabel = btn.getAttribute('data-vehicle-label') || '';
        if (vehicleId) {
            gasVehicleIdInput.value = vehicleId;
            gasVehicleSelectedLabel.textContent = vehicleLabel;
            gasVehicleSelected.style.display = 'block';
            gasVehicleSearch.style.display = 'none';
        } else {
            clearSelectedVehicle();
        }
        document.getElementById('gas_date').value = btn.getAttribute('data-date') || '';
        document.getElementById('gas_driver').value = btn.getAttribute('data-driver') || '';
        document.getElementById('gas_model').value = btn.getAttribute('data-model') || '';
        document.getElementById('gas_amount').value = btn.getAttribute('data-gas-amount') || '';
        document.getElementById('gas_expense_sent_by').value = btn.getAttribute('data-expense-sent-by') || '';
        document.getElementById('gas_checked_by').value = btn.getAttribute('data-checked-by') || '';
        document.getElementById('gas_has_photo_video').checked = btn.getAttribute('data-has-photo-video') === '1';
        document.getElementById('gas_photo_before').checked = btn.getAttribute('data-photo-before') === '1';
        document.getElementById('gas_photo_after').checked = btn.getAttribute('data-photo-after') === '1';
        document.getElementById('gas_photo_plate').checked = btn.getAttribute('data-photo-plate') === '1';
        document.getElementById('gas_photo_receipt').checked = btn.getAttribute('data-photo-receipt') === '1';
    }

    function collectGasExpensePayload() {
        return {
            vehicle_id: gasVehicleIdInput.value,
            date: document.getElementById('gas_date').value,
            driver: document.getElementById('gas_driver').value,
            model: document.getElementById('gas_model').value,
            gas_amount: document.getElementById('gas_amount').value,
            expense_sent_by: document.getElementById('gas_expense_sent_by').value,
            checked_by: document.getElementById('gas_checked_by').value,
            has_photo_video_in_groupchat: document.getElementById('gas_has_photo_video').checked ? 1 : 0,
            photo_fuel_gauge_before: document.getElementById('gas_photo_before').checked ? 1 : 0,
            photo_fuel_gauge_after: document.getElementById('gas_photo_after').checked ? 1 : 0,
            photo_car_license_plate_gas_boy: document.getElementById('gas_photo_plate').checked ? 1 : 0,
            photo_receipt_next_to_gas_pump: document.getElementById('gas_photo_receipt').checked ? 1 : 0,
        };
    }

    if (gasVehicleSearch) {
        gasVehicleSearch.addEventListener('input', function() {
            clearTimeout(vehicleSearchTimer);
            vehicleSearchTimer = setTimeout(() => searchVehicles(gasVehicleSearch.value.trim()), 250);
        });
    }

    if (gasVehicleClear) {
        gasVehicleClear.addEventListener('click', clearSelectedVehicle);
    }

    if (addGasExpenseBtn) {
        addGasExpenseBtn.addEventListener('click', resetGasExpenseModal);
    }

    if (gasModal) {
        gasModal.addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            if (btn && btn.classList.contains('edit-gas-expense-btn')) {
                gasModalLabel.textContent = 'Edit Gas Expense';
                saveGasExpenseBtn.innerHTML = '<i class="fas fa-save me-1"></i>Update Gas Expense';
                fillGasExpenseFormFromButton(btn);
            } else if (btn && btn.id === 'addGasExpenseBtn') {
                resetGasExpenseModal();
            }
        });
    }

    if (saveGasExpenseBtn) {
        saveGasExpenseBtn.addEventListener('click', function() {
            const payload = collectGasExpensePayload();
            if (!payload.vehicle_id) {
                Swal.fire({ icon: 'warning', title: 'Vehicle required', text: 'Please search and select a vehicle.' });
                return;
            }
            const form = document.getElementById('gasExpenseForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const expenseId = gasExpenseIdInput.value;
            const isEdit = !!expenseId;
            const url = isEdit
                ? updateGasUrlTemplate.replace(/\/0(\/|$)/, '/' + expenseId + '$1')
                : storeGasUrl;
            const method = isEdit ? 'PUT' : 'POST';

            saveGasExpenseBtn.disabled = true;
            fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(payload),
            })
                .then(async r => {
                    const data = await r.json();
                    if (!r.ok) {
                        const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Save failed');
                        throw new Error(msg);
                    }
                    return data;
                })
                .then(data => {
                    bootstrap.Modal.getInstance(gasModal).hide();
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved',
                        text: data.message || 'Gas expense saved.',
                        timer: 1800,
                        showConfirmButton: false,
                    }).then(() => location.reload());
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'Save failed' });
                })
                .finally(() => {
                    saveGasExpenseBtn.disabled = false;
                });
        });
    }

    document.querySelectorAll('.delete-gas-expense-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const expenseId = this.getAttribute('data-expense-id');
            Swal.fire({
                title: 'Delete gas expense?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel',
            }).then(result => {
                if (!result.isConfirmed) return;
                const url = deleteGasUrlTemplate.replace(/\/0(\/|$)/, '/' + expenseId + '$1');
                fetch(url, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                })
                    .then(async r => {
                        const data = await r.json();
                        if (!r.ok) throw new Error(data.message || 'Delete failed');
                        return data;
                    })
                    .then(data => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted',
                            text: data.message || 'Gas expense deleted.',
                            timer: 1500,
                            showConfirmButton: false,
                        }).then(() => location.reload());
                    })
                    .catch(err => {
                        Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'Delete failed' });
                    });
            });
        });
    });

    const editModal = document.getElementById('editPoModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            const id = btn.getAttribute('data-po-id');
            document.getElementById('editPoForm').action = '{{ route("gas-expense-po-tracker.update-po", ["purchase_order" => 0]) }}'.replace(/\/0\//, '/' + id + '/');
            document.getElementById('edit_po_number').value = btn.getAttribute('data-po-number') || '';
            document.getElementById('edit_po_date').value = btn.getAttribute('data-po-date') || '';
            document.getElementById('edit_po_vendor').value = btn.getAttribute('data-po-vendor') || '';
            document.getElementById('edit_po_description').value = btn.getAttribute('data-po-description') || '';
            document.getElementById('edit_po_amount').value = btn.getAttribute('data-po-amount') || '';
            document.getElementById('edit_po_status').value = btn.getAttribute('data-po-status') || 'Pending';
            document.getElementById('edit_po_notes').value = btn.getAttribute('data-po-notes') || '';
        });
    }
});
</script>
@endsection
