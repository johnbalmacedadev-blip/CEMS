@extends('layouts.app')

@section('title', 'Vehicle Expenses - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main content -->
        <main class="col-12 px-md-4 main-content" id="mainContent">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Vehicle Expenses - {{ $vehicle->full_name }}</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Vehicle Details
                    </a>
                </div>
            </div>

            <!-- Add New Expense Transaction Form -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-plus me-2"></i>Add New Expense Transaction
                    </h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('expenses.store') }}" enctype="multipart/form-data" id="newExpenseForm" novalidate>
                        @csrf
                        
                        <!-- Hidden Transaction Details -->
                        <input type="hidden" name="transaction_date" value="{{ date('Y-m-d') }}">
                        <input type="hidden" name="starting_cash" value="0">
                        <input type="hidden" name="added_cash" value="0">
                        
                        <!-- Expense Items Container -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label fw-bold mb-0">Expense Item</label>
                            </div>
                            <div id="expenseItemsContainer">
                                <!-- Expense Item Form -->
                                <div class="card mb-3 expense-item" data-item-id="1">
                                    <div class="card-body">
                                        <!-- Expense Date -->
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Expense Date <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control" name="items[1][expense_date]" required value="{{ date('Y-m-d') }}">
                                            </div>
                                        </div>
                                        
                                        <!-- Payment Tag (pre-set to Vehicle) -->
                                        <input type="hidden" name="items[1][payment_tag]" value="Vehicle">
                                        <input type="hidden" name="items[1][vehicle_id]" value="{{ $vehicle->id }}">
                                        
                                        <!-- Payment Method -->
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                                                <select class="form-select payment-method-select" name="items[1][payment_method_id]" required id="payment_method_1">
                                                    <option value="">Select Payment Method</option>
                                                    @if(isset($paymentMethods) && $paymentMethods->count() > 0)
                                                        @foreach($paymentMethods as $method)
                                                            <option value="{{ $method->id }}">{{ $method->name }}</option>
                                                        @endforeach
                                                    @else
                                                        <option value="1">COST CENTER (FLAGSHIP BUDGET)</option>
                                                        <option value="2">COST CENTER (WAREHOUSE BUDGET)</option>
                                                        <option value="3">COST CENTER (ANNEX BUDGET)</option>
                                                        <option value="4">GCASH</option>
                                                        <option value="5">CREDIT CARD #1</option>
                                                        <option value="6">CREDIT CARD #2</option>
                                                        <option value="7">CREDIT CARD #3</option>
                                                        <option value="8">CREDIT CARD #4</option>
                                                        <option value="9">CREDIT CARD #5</option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <!-- Requested By -->
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Requested By</label>
                                                <input type="text" class="form-control" name="items[1][requested_by]">
                                            </div>
                                        </div>
                                        
                                        <!-- Approved By -->
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Approved By</label>
                                                <input type="text" class="form-control" name="items[1][approved_by]">
                                            </div>
                                        </div>
                                        
                                        <!-- Paid By (Care Of) -->
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Paid By (C/o)</label>
                                                <input type="text" class="form-control" name="items[1][care_of]">
                                            </div>
                                        </div>
                                        
                                        <!-- Store/Shop -->
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Store / Shop</label>
                                                <input type="text" class="form-control" name="items[1][store_shop]">
                                            </div>
                                        </div>
                                        
                                        <!-- Description -->
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Description <span class="text-danger">*</span></label>
                                                <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                                                    <div class="category-checkboxes" id="category_checkboxes_1">
                                                        @if(isset($vehicleExpenseCategories) && $vehicleExpenseCategories->count() > 0)
                                                            @foreach($vehicleExpenseCategories as $category)
                                                                <div class="form-check">
                                                                    <input class="form-check-input category-checkbox" type="checkbox" value="{{ $category->name }}" id="category_1_{{ $category->id }}" name="categories_1[]" onchange="updateDescription(1)">
                                                                    <label class="form-check-label" for="category_1_{{ $category->id }}">
                                                                        {{ $category->name }}
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    <div class="input-group">
                                                        <input type="text" class="form-control new-category-input" id="new_category_input_1" placeholder="Add new category..." onkeypress="if(event.key === 'Enter') { event.preventDefault(); addNewCategory(1); }">
                                                        <button type="button" class="btn btn-outline-primary" onclick="addNewCategory(1)">
                                                            <i class="fas fa-plus me-1"></i>Add
                                                        </button>
                                                    </div>
                                                </div>
                                                <input type="hidden" class="description-input" name="items[1][description]" required>
                                            </div>
                                        </div>
                                        
                                        <!-- Notes -->
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label notes-label">Notes</label>
                                                <textarea class="form-control notes-input" name="items[1][description_details]" rows="2" placeholder="Enter notes..."></textarea>
                                            </div>
                                        </div>
                                        
                                        <!-- Cost -->
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Cost or Amount <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" name="items[1][cost]" step="0.01" min="0" required>
                                            </div>
                                        </div>
                                        
                                        <!-- Receipt Upload -->
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Receipt Upload</label>
                                                <input type="file" class="form-control receipt-files-input" name="items[1][receipts][]" multiple accept="image/*" onchange="previewReceipts(this, 1)">
                                                <small class="text-muted">(up to 10 images, max 5MB each)</small>
                                                <div class="receipt-preview-container row g-2 mt-2" data-item-id="1"></div>
                                            </div>
                                        </div>
                                        
                                        <!-- Receipt Check Fields -->
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <div class="form-check mt-4">
                                                    <input class="form-check-input" type="checkbox" name="items[1][receipt_checked]" id="receipt_checked_1" value="1">
                                                    <label class="form-check-label" for="receipt_checked_1">
                                                        Receipt Checked
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Receipt Checker</label>
                                                <input type="text" class="form-control" name="items[1][receipt_checker]">
                                            </div>
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Receipt Check Date</label>
                                                <input type="date" class="form-control" name="items[1][receipt_check_date]" value="{{ date('Y-m-d') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-secondary" onclick="resetExpenseForm()">Reset</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Save Expense Transaction
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <hr class="my-4">

            <!-- Existing Expense Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Existing Expense Items
                    </h5>
                </div>
                <div class="card-body">
            @if($groupedExpenseItems->isEmpty())
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No expense items found for this vehicle.
                </div>
            @else
                <form method="POST" action="{{ route('vehicles.expenses.store', $vehicle) }}" id="expenseForm">
                    @csrf
                    
                    @php
                        // Category field mapping
                        $categoryFieldMap = [
                            'Paint' => ['items' => 'paint_items', 'costs' => 'paint_costs'],
                            'Mechanical / Electrical' => ['items' => 'mechanical_electrical_items', 'costs' => 'mechanical_electrical_costs'],
                            'Cluster' => ['items' => 'cluster_items', 'costs' => 'cluster_costs'],
                            'Aircon' => ['items' => 'aircon_items', 'costs' => 'aircon_cost'],
                            'Interior' => ['items' => 'interior_items', 'costs' => 'interior_costs'],
                            'Paper' => ['items' => 'papers_items', 'costs' => 'papers_costs'],
                            'Tyers' => ['items' => 'tyres_battery_items', 'costs' => 'tyres_battery_cost'],
                            'Battery' => ['items' => 'tyres_battery_items', 'costs' => 'tyres_battery_cost'],
                            'Miscellaneous' => ['items' => 'misc_items', 'costs' => 'misc_costs'],
                            'Repair' => ['items' => 'total_repair_items', 'costs' => 'total_repair_cost'],
                            'Post Reservation Repairs' => ['items' => 'post_reservation_repairs', 'costs' => 'post_reservation_repairs_cost'],
                        ];
                        
                        // Aggregate expenses by category and date
                        $categoryData = [];
                        foreach ($groupedExpenseItems as $date => $items) {
                            foreach ($items as $item) {
                                if ($item->description) {
                                    $itemCategories = array_map('trim', explode(',', $item->description));
                                    foreach ($itemCategories as $category) {
                                        if (isset($categoryFieldMap[$category])) {
                                            $fieldKey = $categoryFieldMap[$category]['items'];
                                            $costKey = $categoryFieldMap[$category]['costs'];
                                            
                                            if (!isset($categoryData[$category])) {
                                                $categoryData[$category] = [
                                                    'items' => [],
                                                    'costs' => [],
                                                    'field_items' => $fieldKey,
                                                    'field_costs' => $costKey,
                                                ];
                                            }
                                            
                                            $categoryData[$category]['items'][] = [
                                                'date' => $date,
                                                'description' => $item->care_of ? "C/o: {$item->care_of} - {$item->description}" : $item->description,
                                                'cost' => $item->cost,
                                                'item_id' => $item->id,
                                            ];
                                            $categoryData[$category]['costs'][] = $item->cost;
                                        }
                                    }
                                }
                            }
                        }
                    @endphp

                    @foreach($groupedExpenseItems->sortKeys() as $date => $items)
                        @php
                            $transaction = $items->first()->expenseTransaction;
                            $formattedDate = $transaction ? $transaction->transaction_date->format('F j, Y') : 'Unknown Date';
                        @endphp
                        
                        <div class="card mb-4">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-calendar me-2"></i>{{ $formattedDate }}
                                    @if($transaction)
                                        <a href="{{ route('expenses.show', $transaction) }}" class="text-white text-decoration-none ms-2" target="_blank">
                                            <small>(View Transaction)</small>
                                        </a>
                                    @endif
                                </h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Description</th>
                                            <th>C/o</th>
                                            <th>Cost</th>
                                            <th>Receipts</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($items as $item)
                                            <tr>
                                                <td>
                                                    <div>{{ $item->description }}</div>
                                                    @if($item->expense_category === 'Post Reservation')
                                                        <small class="text-muted d-block" style="font-style: italic; margin-top: 2px;">post reservation</small>
                                                    @elseif($item->expense_category === 'Post Release')
                                                        <small class="text-muted d-block" style="font-style: italic; margin-top: 2px;">post release</small>
                                                    @endif
                                                </td>
                                                <td>{{ $item->care_of ?: 'N/A' }}</td>
                                                <td><strong>₱{{ number_format($item->cost, 2) }}</strong></td>
                                                <td class="text-nowrap">
                                                    @if($item->receipts->count() > 0)
                                                        <span class="badge bg-info">{{ $item->receipts->count() }}</span>
                                                        <button type="button" class="btn btn-sm btn-outline-primary ms-1" onclick="openVehicleReceiptsModal(@json($item->receipts->map(function ($r) { return ['url' => $r->url, 'name' => $r->original_name ?? 'Receipt']; })->values()))">
                                                            <i class="fas fa-eye me-1"></i>View
                                                        </button>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="2" class="text-end">Total for {{ $formattedDate }}:</th>
                                            <th class="text-danger">₱{{ number_format($items->sum('cost'), 2) }}</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @endforeach

                    <hr class="my-4">
                    
                    <!-- Category-based expense summary -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-bar me-2"></i>Expense Summary by Category
                            </h5>
                        </div>
                        <div class="card-body">
                            @foreach($categoryData as $category => $data)
                                @php
                                    $categoryTotal = array_sum($data['costs']);
                                    $itemDescriptions = array_column($data['items'], 'description');
                                    $itemList = implode("\n", $itemDescriptions);
                                @endphp
                                <div class="row mb-3 border-bottom pb-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">{{ $category }} Items</label>
                                        <div class="bg-light p-3 rounded border" style="min-height: 60px; white-space: pre-wrap;">
                                            @if(!empty($itemList))
                                                {{ $itemList }}
                                            @else
                                                <span class="text-muted">No items for this category</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold">{{ $category }} Cost</label>
                                        <div class="bg-light p-3 rounded border">
                                            <h5 class="mb-0 text-primary">
                                                ₱{{ number_format($categoryTotal, 2) }}
                                            </h5>
                                            <small class="text-muted">
                                                Total from {{ count($data['items']) }} expense item(s)
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            @if(empty($categoryData))
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    No categories found. Please add expense items with categories from the Expenses page.
                                </div>
                            @endif
                                    </div>
                                </div>
                </div>
            </div>
            @endif

            <!-- View uploaded receipts (existing expense items) -->
            <div class="modal fade" id="vehicleExpenseReceiptsModal" tabindex="-1" aria-labelledby="vehicleExpenseReceiptsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="vehicleExpenseReceiptsModalLabel"><i class="fas fa-receipt me-2"></i>Receipts</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body" id="vehicleExpenseReceiptsModalBody"></div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('scripts')
<script>
let expenseItemCount = 0;
let paymentMethods = [];
let vehicleCategories = [];

function openVehicleReceiptsModal(receipts) {
    if (!receipts || !receipts.length) {
        return;
    }
    const body = document.getElementById('vehicleExpenseReceiptsModalBody');
    if (!body) {
        return;
    }
    body.innerHTML = '';
    receipts.forEach(function (r) {
        const wrap = document.createElement('div');
        wrap.className = 'mb-4 text-center';
        const cap = document.createElement('p');
        cap.className = 'small text-muted mb-2';
        cap.textContent = r.name || 'Receipt';
        const img = document.createElement('img');
        img.src = r.url;
        img.className = 'img-fluid rounded border';
        img.style.maxHeight = '70vh';
        img.style.objectFit = 'contain';
        img.alt = '';
        img.onerror = function () {
            this.style.display = 'none';
            const err = document.createElement('p');
            err.className = 'text-danger small';
            err.textContent = 'Could not load this image.';
            wrap.appendChild(err);
        };
        wrap.appendChild(cap);
        wrap.appendChild(img);
        body.appendChild(wrap);
    });
    const el = document.getElementById('vehicleExpenseReceiptsModal');
    if (el && typeof bootstrap !== 'undefined') {
        bootstrap.Modal.getOrCreateInstance(el).show();
    }
}

/** Local thumbnails before submit (files still post with the form) */
function previewReceipts(input, itemId) {
    const previewContainer = document.querySelector('.receipt-preview-container[data-item-id="' + itemId + '"]');
    if (!previewContainer) {
        return;
    }
    previewContainer.innerHTML = '';
    if (!input.files || input.files.length === 0) {
        return;
    }
    previewContainer.classList.add('row', 'g-2');
    Array.from(input.files).forEach(function (file) {
        if (!file.type.startsWith('image/')) {
            return;
        }
        const reader = new FileReader();
        reader.onload = function (e) {
            const col = document.createElement('div');
            col.className = 'col-auto';
            col.innerHTML = '<div class="border rounded overflow-hidden bg-light" style="width:72px;height:72px;">' +
                '<img src="' + e.target.result + '" class="w-100 h-100" style="object-fit:cover;" alt=""></div>';
            previewContainer.appendChild(col);
        };
        reader.readAsDataURL(file);
    });
}

// Load payment methods
function loadPaymentMethods() {
    console.log('Loading payment methods...');
    return fetch('/api/expenses/payment-methods', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to load payment methods');
        }
        return response.json();
    })
    .then(methods => {
        console.log('Payment methods loaded:', methods);
        paymentMethods = methods || [];
        // Populate all existing dropdowns
        document.querySelectorAll('.payment-method-select').forEach(select => {
            const itemId = select.id.replace('payment_method_', '');
            if (itemId) {
                populatePaymentMethodDropdown(itemId);
            }
        });
        return paymentMethods;
    })
    .catch(error => {
        console.error('Error loading payment methods:', error);
        paymentMethods = [];
        return [];
    });
}

// Load vehicle categories
function loadVehicleCategories() {
    console.log('Loading vehicle categories...');
    return fetch('/api/expenses/vehicle-categories', {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to load categories');
        }
        return response.json();
    })
    .then(categories => {
        console.log('Vehicle categories loaded:', categories);
        // Ensure categories is an array
        if (Array.isArray(categories)) {
            vehicleCategories = categories;
        } else {
            vehicleCategories = [];
        }
        console.log('Vehicle categories array:', vehicleCategories);
        console.log('Number of categories:', vehicleCategories.length);
        
        // Populate all existing category checkboxes
        document.querySelectorAll('.category-checkboxes').forEach(container => {
            const itemId = container.id.replace('category_checkboxes_', '');
            console.log('Found category container for item:', itemId);
            if (itemId) {
                populateCategoryCheckboxes(itemId);
            }
        });
        
        // Also explicitly populate item 1
        populateCategoryCheckboxes(1);
        
        return vehicleCategories;
    })
    .catch(error => {
        console.error('Error loading vehicle categories:', error);
        vehicleCategories = [];
        return [];
    });
}

// Add expense item - make it globally accessible
window.addExpenseItem = function() {
    console.log('addExpenseItem called, current count:', expenseItemCount);
    expenseItemCount++;
    const container = document.getElementById('expenseItemsContainer');
    
    if (!container) {
        console.error('expenseItemsContainer not found!');
        alert('Error: Expense items container not found. Please refresh the page.');
        return;
    }
    
    const itemDiv = document.createElement('div');
    itemDiv.className = 'card mb-3 expense-item';
    itemDiv.setAttribute('data-item-id', expenseItemCount);
    itemDiv.innerHTML = `
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Item #${expenseItemCount}</h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeExpenseItem(this)">
                    <i class="fas fa-times"></i> Remove
                </button>
            </div>
            
            <!-- Expense Date -->
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Expense Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="items[${expenseItemCount}][expense_date]" required value="{{ date('Y-m-d') }}">
                </div>
            </div>
            
            <!-- Payment Tag (pre-set to Vehicle) -->
            <input type="hidden" name="items[${expenseItemCount}][payment_tag]" value="Vehicle">
            <input type="hidden" name="items[${expenseItemCount}][vehicle_id]" value="{{ $vehicle->id }}">
            
            <!-- Payment Method -->
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Payment Method <span class="text-danger">*</span></label>
                    <select class="form-select payment-method-select" name="items[${expenseItemCount}][payment_method_id]" required id="payment_method_${expenseItemCount}">
                        <option value="">Select Payment Method</option>
                    </select>
                </div>
            </div>
            
            <!-- Requested By -->
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Requested By</label>
                    <input type="text" class="form-control" name="items[${expenseItemCount}][requested_by]">
                </div>
            </div>
            
            <!-- Approved By -->
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Approved By</label>
                    <input type="text" class="form-control" name="items[${expenseItemCount}][approved_by]">
                </div>
            </div>
            
            <!-- Paid By (Care Of) -->
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Paid By (C/o)</label>
                    <input type="text" class="form-control" name="items[${expenseItemCount}][care_of]">
                </div>
            </div>
            
            <!-- Store/Shop -->
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Store / Shop</label>
                    <input type="text" class="form-control" name="items[${expenseItemCount}][store_shop]">
                </div>
            </div>
            
            <!-- Description -->
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Description <span class="text-danger">*</span></label>
                    <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                        <div class="category-checkboxes" id="category_checkboxes_${expenseItemCount}"></div>
                    </div>
                    <div class="mt-2">
                        <div class="input-group">
                            <input type="text" class="form-control new-category-input" id="new_category_input_${expenseItemCount}" placeholder="Add new category..." onkeypress="if(event.key === 'Enter') { event.preventDefault(); addNewCategory(${expenseItemCount}); }">
                            <button type="button" class="btn btn-outline-primary" onclick="addNewCategory(${expenseItemCount})">
                                <i class="fas fa-plus me-1"></i>Add
                            </button>
                        </div>
                    </div>
                    <input type="hidden" class="description-input" name="items[${expenseItemCount}][description]" required>
                </div>
            </div>
            
            <!-- Notes -->
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label notes-label">Notes</label>
                    <textarea class="form-control notes-input" name="items[${expenseItemCount}][description_details]" rows="2" placeholder="Enter notes..."></textarea>
                </div>
            </div>
            
            <!-- Cost -->
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Cost or Amount <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="items[${expenseItemCount}][cost]" step="0.01" min="0" required>
                </div>
            </div>
            
            <!-- Receipt Upload -->
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label class="form-label">Receipt Upload</label>
                    <input type="file" class="form-control receipt-files-input" name="items[${expenseItemCount}][receipts][]" multiple accept="image/*" onchange="previewReceipts(this, ${expenseItemCount})">
                    <small class="text-muted">(up to 10 images, max 5MB each)</small>
                    <div class="receipt-preview-container row g-2 mt-2" data-item-id="${expenseItemCount}"></div>
                </div>
            </div>
            
            <!-- Receipt Check Fields -->
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="items[${expenseItemCount}][receipt_checked]" id="receipt_checked_${expenseItemCount}" value="1">
                        <label class="form-check-label" for="receipt_checked_${expenseItemCount}">
                            Receipt Checked
                        </label>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Receipt Checker</label>
                    <input type="text" class="form-control" name="items[${expenseItemCount}][receipt_checker]">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Receipt Check Date</label>
                    <input type="date" class="form-control" name="items[${expenseItemCount}][receipt_check_date]" value="${(() => { const d = new Date(); const p = n => String(n).padStart(2, '0'); return d.getFullYear() + '-' + p(d.getMonth() + 1) + '-' + p(d.getDate()); })()}">
                </div>
            </div>
        </div>
    `;
    
    container.appendChild(itemDiv);
    
    // Populate payment method dropdown
    populatePaymentMethodDropdown(expenseItemCount);
    
    // Populate category checkboxes
    populateCategoryCheckboxes(expenseItemCount);
    
    console.log('Expense item added successfully, new count:', expenseItemCount);
}

// Also create a regular function for easier access
function addExpenseItem() {
    return window.addExpenseItem();
}

// Remove expense item
function removeExpenseItem(button) {
    const itemDiv = button.closest('.expense-item');
    itemDiv.remove();
    
    // Show no items message if empty
    const container = document.getElementById('expenseItemsContainer');
    if (container.children.length === 0) {
        document.getElementById('noItemsMessage').style.display = 'block';
    }
}

// Populate payment method dropdown
function populatePaymentMethodDropdown(itemId) {
    const select = document.getElementById(`payment_method_${itemId}`);
    if (!select) {
        console.warn(`Payment method select not found for item ${itemId}`);
        return;
    }
    
    // If payment methods haven't loaded yet, wait and try again
    if (paymentMethods.length === 0) {
        console.log(`Payment methods not loaded yet for item ${itemId}, waiting...`);
        setTimeout(() => {
            if (paymentMethods.length > 0) {
                populatePaymentMethodDropdown(itemId);
            } else {
                // Try loading again
                loadPaymentMethods().then(() => {
                    populatePaymentMethodDropdown(itemId);
                });
            }
        }, 500);
        return;
    }
    
    // Clear existing options except the first one
    const firstOption = select.querySelector('option[value=""]');
    select.innerHTML = '';
    if (firstOption) {
        select.appendChild(firstOption);
    } else {
        select.innerHTML = '<option value="">Select Payment Method</option>';
    }
    
    // Add payment methods
    paymentMethods.forEach(method => {
        const option = document.createElement('option');
        option.value = method.id;
        option.textContent = method.name;
        select.appendChild(option);
    });
    
    console.log(`Populated payment method dropdown for item ${itemId} with ${paymentMethods.length} methods`);
}

// Populate category checkboxes (same as expenses create page)
function populateCategoryCheckboxes(itemId) {
    const container = document.getElementById(`category_checkboxes_${itemId}`);
    if (!container) {
        console.warn(`Category checkboxes container not found for item ${itemId}`);
        return;
    }
    
    container.innerHTML = '';
    
    // If categories haven't loaded yet, wait and try again
    if (vehicleCategories.length === 0) {
        console.log(`Vehicle categories not loaded yet for item ${itemId}, waiting...`);
        setTimeout(() => {
            if (vehicleCategories.length > 0) {
                populateCategoryCheckboxes(itemId);
            } else {
                // Try loading again
                loadVehicleCategories().then(() => {
                    populateCategoryCheckboxes(itemId);
                });
            }
        }, 500);
        return;
    }
    
    // Render checkboxes exactly like expenses create page
    vehicleCategories.forEach(category => {
        const categoryName = typeof category === 'object' ? category.name : category;
        const categoryId = typeof category === 'object' ? category.id : category.replace(/\s+/g, '_');
        const div = document.createElement('div');
        div.className = 'form-check';
        div.innerHTML = `
            <input class="form-check-input category-checkbox" type="checkbox" value="${categoryName}" id="category_${itemId}_${categoryId}" name="categories_${itemId}[]" onchange="updateDescription(${itemId})">
            <label class="form-check-label" for="category_${itemId}_${categoryId}">
                ${categoryName}
            </label>
        `;
        container.appendChild(div);
    });
    
    if (vehicleCategories.length === 0) {
        container.innerHTML = '<p class="text-muted">No categories available. Add a new category below.</p>';
    }
    
    console.log(`Populated category checkboxes for item ${itemId} with ${vehicleCategories.length} categories`);
}

// Update description from selected categories and toggle Notes required when Miscellaneous is selected
function updateDescription(itemId) {
    const checkboxes = document.querySelectorAll(`#category_checkboxes_${itemId} .category-checkbox:checked`);
    const selectedCategories = Array.from(checkboxes).map(cb => cb.value);
    const descriptionInput = document.querySelector(`.expense-item[data-item-id="${itemId}"] .description-input`);
    const itemCard = document.querySelector(`.expense-item[data-item-id="${itemId}"]`);
    
    if (descriptionInput) {
        descriptionInput.value = selectedCategories.join(', ');
    }
    
    // When Miscellaneous is selected, make Notes required and change label
    const notesLabel = itemCard?.querySelector('.notes-label');
    const notesInput = itemCard?.querySelector('.notes-input');
    if (notesLabel && notesInput) {
        const hasMiscellaneous = selectedCategories.some(c => c.trim().toLowerCase() === 'miscellaneous');
        if (hasMiscellaneous) {
            notesLabel.innerHTML = 'Miscellaneous Notes for misc and others: <span class="text-danger">*</span>';
            notesInput.required = true;
            notesInput.setAttribute('required', 'required');
        } else {
            notesLabel.textContent = 'Notes';
            notesInput.required = false;
            notesInput.removeAttribute('required');
        }
    }
}

// Add new category
function addNewCategory(itemId) {
    const input = document.getElementById(`new_category_input_${itemId}`);
    const categoryName = input.value.trim();
    
    if (!categoryName) {
        Swal.fire({
            icon: 'warning',
            title: 'Validation Error',
            text: 'Please enter a category name.',
            confirmButtonColor: '#dc3545'
        });
        return;
    }
    
    // Check if category already exists
    const exists = vehicleCategories.some(cat => {
        const name = typeof cat === 'object' ? cat.name : cat;
        return name.toLowerCase() === categoryName.toLowerCase();
    });
    
    if (exists) {
        Swal.fire({
            icon: 'warning',
            title: 'Category Exists',
            text: 'This category already exists.',
            confirmButtonColor: '#dc3545'
        });
        return;
    }
    
    // Show loading
    Swal.fire({
        title: 'Adding Category...',
        text: 'Please wait...',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Save to API
    fetch('/api/expenses/vehicle-categories', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ name: categoryName })
    })
    .then(response => response.json())
    .then(data => {
        Swal.close();
        if (data.success) {
            // Add to local array
            vehicleCategories.push({ id: data.category.id, name: data.category.name });
            
            // Add checkbox to all items (same as expenses create page)
            document.querySelectorAll('.expense-item').forEach(item => {
                const currentItemId = item.getAttribute('data-item-id');
                const categoryContainer = document.getElementById(`category_checkboxes_${currentItemId}`);
                if (categoryContainer) {
                    const div = document.createElement('div');
                    div.className = 'form-check';
                    div.innerHTML = `
                        <input class="form-check-input category-checkbox" type="checkbox" value="${data.category.name}" id="category_${currentItemId}_${data.category.id}" name="categories_${currentItemId}[]" onchange="updateDescription(${currentItemId})" checked>
                        <label class="form-check-label" for="category_${currentItemId}_${data.category.id}">
                            ${data.category.name}
                        </label>
                    `;
                    categoryContainer.appendChild(div);
                    // Update description for this item
                    updateDescription(currentItemId);
                }
            });
            
            // Clear input
            input.value = '';
            
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Category added successfully!',
                confirmButtonColor: '#28a745',
                timer: 1500,
                timerProgressBar: true
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: data.message || 'Failed to add category',
                confirmButtonColor: '#dc3545'
            });
        }
    })
    .catch(error => {
        Swal.close();
        console.error('Error adding category:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'An error occurred while adding the category',
            confirmButtonColor: '#dc3545'
        });
    });
}

// Reset form
function resetExpenseForm() {
    document.getElementById('expenseItemsContainer').innerHTML = '';
    document.getElementById('noItemsMessage').style.display = 'block';
    expenseItemCount = 0;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Vehicle expenses page loaded');
    
    // Set initial expense item count since form is already rendered in template
    expenseItemCount = 1;
    
    // Initialize categories from server-side data if available
    @if(isset($vehicleExpenseCategories) && $vehicleExpenseCategories->count() > 0)
        vehicleCategories = @json($vehicleExpenseCategories);
        console.log('Vehicle categories initialized from server:', vehicleCategories);
    @endif
    
    // Load payment methods and categories, then populate the form
    Promise.all([loadPaymentMethods(), loadVehicleCategories()]).then(() => {
        console.log('Payment methods and categories loaded');
        console.log('Vehicle categories:', vehicleCategories);
        // Populate dropdowns for the existing form
        populatePaymentMethodDropdown(1);
        // Only populate if categories weren't already loaded from server
        const container = document.getElementById('category_checkboxes_1');
        if (container && container.children.length === 0 && vehicleCategories.length > 0) {
            populateCategoryCheckboxes(1);
        }
    }).catch(error => {
        console.error('Error loading data:', error);
        // Still try to populate with what we have
        populatePaymentMethodDropdown(1);
        // If categories were loaded from server, they should already be displayed
        // Otherwise try to load from API
        const container = document.getElementById('category_checkboxes_1');
        if (container && container.children.length === 0) {
            loadVehicleCategories().then(() => {
                populateCategoryCheckboxes(1);
            });
        }
    });
    
    // Form submission
    const expenseForm = document.getElementById('newExpenseForm');
    if (expenseForm) {
        console.log('Expense form found, attaching submit listener');
        expenseForm.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();
        
            // Find all expense items within the form
            const items = this.querySelectorAll('.expense-item');
            
            if (items.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    text: 'Please add at least one expense item.',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }
        
            // Update description from selected categories before validation (also toggles Notes required when Miscellaneous is selected)
            items.forEach((item) => {
                const itemId = item.getAttribute('data-item-id');
                if (itemId) {
                    updateDescription(itemId);
                }
            });
        
            // Validate all required fields and collect error messages
            let isValid = true;
            const errors = [];
            items.forEach((item, index) => {
                const itemNum = index + 1;
                const expenseDate = item.querySelector('input[name*="[expense_date]"]');
                const paymentMethod = item.querySelector('select[name*="[payment_method_id]"]');
                const description = item.querySelector('.description-input');
                const cost = item.querySelector('input[name*="[cost]"]');
                const descriptionDetails = item.querySelector('.notes-input');
                
                const itemErrors = [];
                if (!expenseDate?.value) itemErrors.push('Expense Date');
                if (!paymentMethod?.value) itemErrors.push('Payment Method');
                if (!description?.value || !description.value.trim()) itemErrors.push('Description (select at least one category)');
                if (cost?.value === '' || cost?.value === null || cost?.value === undefined) {
                    itemErrors.push('Cost or Amount');
                } else if (Number.isNaN(parseFloat(cost.value)) || parseFloat(cost.value) < 0) {
                    itemErrors.push('Cost or Amount (enter a valid number ≥ 0)');
                }
                const descHasMisc = description?.value && description.value.toLowerCase().includes('miscellaneous');
                if (descHasMisc && (!descriptionDetails?.value || !descriptionDetails.value.trim())) {
                    itemErrors.push('Miscellaneous Notes for misc and others');
                }
                
                if (itemErrors.length > 0) {
                    isValid = false;
                    errors.push(`Item #${itemNum}: ${itemErrors.join(', ')}`);
                }
            });
        
            if (!isValid) {
                const errorText = errors.length > 0 ? 'Please complete the following required fields:\n\n' + errors.join('\n') : 'Please fill in all required fields for all expense items.';
                Swal.fire({
                    icon: 'warning',
                    title: 'Validation Error',
                    text: errorText,
                    confirmButtonColor: '#dc3545',
                    width: '500px'
                });
                return;
            }
        
        // Show loading
        Swal.fire({
            title: 'Saving...',
            text: 'Please wait while we save your expense transaction',
            icon: 'info',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Submit form - collect all form data
        const formData = new FormData(this);
        
        // Log form data for debugging
        console.log('Form data being submitted:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        fetch('{{ route("expenses.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {})
            },
            credentials: 'same-origin'
        })
        .then(async response => {
            const contentType = response.headers.get('content-type') || '';
            let data;
            if (contentType.includes('application/json')) {
                data = await response.json();
            } else {
                const text = await response.text();
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    throw new Error(text ? text.substring(0, 200) : ('HTTP ' + response.status));
                }
            }

            if (typeof Swal !== 'undefined') {
                Swal.close();
            }

            if (response.status === 422 && data.errors) {
                const lines = [];
                Object.entries(data.errors).forEach(([key, msgs]) => {
                    const arr = Array.isArray(msgs) ? msgs : [msgs];
                    arr.forEach(m => lines.push(m));
                });
                Swal.fire({
                    icon: 'warning',
                    title: 'Please fix the form',
                    html: lines.length ? lines.join('<br>') : 'Validation failed. Check required fields.',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }

            if (!response.ok) {
                const msg = data.message || data.error || ('Request failed (' + response.status + ')');
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: msg,
                    confirmButtonColor: '#dc3545'
                });
                return;
            }

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Saved',
                    text: data.message || 'Expense transaction created successfully!',
                    confirmButtonColor: '#28a745',
                    timer: 2200,
                    timerProgressBar: true
                }).then(() => {
                    window.location.href = '{{ route("vehicles.expenses.create", $vehicle) }}';
                });
            } else {
                let errorMsg = 'Failed to create expense transaction';
                if (data.errors) {
                    errorMsg = Object.values(data.errors).flat().join('<br>');
                } else if (data.message) {
                    errorMsg = data.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: errorMsg,
                    confirmButtonColor: '#dc3545'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (typeof Swal !== 'undefined') {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while saving: ' + (error.message || 'Unknown error.'),
                    confirmButtonColor: '#dc3545'
                });
            } else {
                alert('An error occurred while saving: ' + error.message);
            }
        });
        });
    } else {
        console.error('Expense form not found!');
    }
});

// Show success message if redirected from form submission
@if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: '{{ session('success') }}',
        confirmButtonColor: '#28a745',
        timer: 3000,
        timerProgressBar: true
    });
@endif

// Show error message if there was an error
@if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: '{{ session('error') }}',
        confirmButtonColor: '#dc3545'
    });
@endif

</script>
@endsection
