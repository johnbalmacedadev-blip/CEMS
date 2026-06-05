@extends('layouts.app')

@section('title', 'Edit Expense Transaction - Car Empire Management System')

@section('content')
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid" style="padding-left: 0.5rem; padding-right: 0.5rem;">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('images/CAREMPIRE_LOGO.png') }}" alt="CAR EMPIRE Logo" onerror="this.style.display='none';">
        </a>
        <div class="navbar-nav ms-auto">
            <a href="{{ route('logout') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-sign-out-alt me-1"></i>Logout
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid" style="padding-left: 2rem; padding-right: 2rem; padding-top: 1rem;">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="pt-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('expenses-inventory') }}">Expenses & Inventory</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Transaction</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-edit me-2"></i>Edit Expense Transaction #{{ $expenseTransaction->id }}
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('expenses-inventory') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Expenses
                    </a>
                </div>
            </div>

            <!-- Transaction Info -->
            <div class="card mb-4">
                <div class="card-body">
                    @if(isset($vehicle) && $vehicle)
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Vehicle Plate Number:</label>
                            <p class="mb-0">{{ $vehicle->plate_number }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Vehicle Model:</label>
                            <p class="mb-0">
                                @if(is_string($vehicle->model))
                                    {{ $vehicle->model }}
                                @elseif($vehicle->vehicleModel)
                                    {{ $vehicle->vehicleModel->name }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Vehicle Make:</label>
                            <p class="mb-0">
                                @if(is_string($vehicle->make))
                                    {{ $vehicle->make }}
                                @elseif($vehicle->make)
                                    {{ $vehicle->make->name }}
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>
                    <hr>
                    @endif
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Transaction Date:</label>
                            <p class="mb-0">{{ $expenseTransaction->transaction_date->format('M d, Y') }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Total Expense:</label>
                            <p class="mb-0">₱{{ number_format(isset($filteredTotalExpense) ? $filteredTotalExpense : $expenseTransaction->total_expense, 2) }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Total Items:</label>
                            <p class="mb-0">{{ $expenseTransaction->expenseItems->count() }}</p>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">Cash Remaining:</label>
                            <p class="mb-0">₱{{ number_format($expenseTransaction->cash_remaining, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <form id="expenseForm">
                @csrf

            <!-- Expense Items -->
            <div class="card mb-4">
                    <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-list me-2"></i>Expense {{ $expenseTransaction->expenseItems->count() == 1 ? 'Item' : 'Items' }} ({{ $expenseTransaction->expenseItems->count() }})
                    </h5>
                </div>
                <div class="card-body">
                        <div id="expenseItemsContainer">
                                    @foreach($expenseTransaction->expenseItems as $index => $item)
                                @include('expenses.partials.expense-item-form', ['item' => $item, 'index' => $index, 'expenseTransaction' => $expenseTransaction])
                                                            @endforeach
                                                        </div>
                        @if($expenseTransaction->expenseItems->count() == 0)
                            <div id="noItemsMessage" class="text-center text-muted py-3">
                                <i class="fas fa-info-circle me-2"></i>No expense items found for this transaction.
                        </div>
                    @else
                            <div id="noItemsMessage" class="text-center text-muted py-3" style="display: none;">
                            <i class="fas fa-info-circle me-2"></i>No expense items found for this transaction.
                        </div>
                    @endif
                </div>
            </div>

                <!-- Actions -->
                <div class="d-flex justify-content-end gap-2 mb-4">
                    <a href="{{ route('expenses-inventory') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                    <button type="button" class="btn btn-primary" onclick="updateAllExpenseItems()">
                    <i class="fas fa-save me-1"></i>Update Transaction
                </button>
            </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let expenseItemCount = {{ $expenseTransaction->expenseItems->count() }};
let vehicleCategories = [];
let paymentMethods = [];

// Load payment methods on page load
document.addEventListener('DOMContentLoaded', function() {
    loadPaymentMethods().then(() => {
        // After payment methods are loaded, ensure all dropdowns are properly populated
        document.querySelectorAll('.payment-method-select').forEach(select => {
            const itemId = select.id.replace('payment_method_', '');
            populatePaymentMethodDropdown(itemId);
        });
    });
    
        loadVehicleCategories();
        
    // Initialize all existing items
    document.querySelectorAll('.expense-item').forEach(item => {
        const itemId = item.getAttribute('data-item-id');
        const paymentTag = item.querySelector('.payment-tag-select').value;
        if (paymentTag === 'Vehicle') {
            initVehicleSearch(itemId);
        }
    });
});

// Load payment methods
function loadPaymentMethods() {
    return fetch('/api/expenses/payment-methods', {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            console.error('Payment methods API response not OK:', response.status);
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
            populatePaymentMethodDropdown(itemId);
        });
        return methods;
    })
    .catch(error => {
        console.error('Error loading payment methods:', error);
        paymentMethods = [];
        return [];
    });
}

// Populate payment method dropdown
function populatePaymentMethodDropdown(itemId) {
    const select = document.getElementById(`payment_method_${itemId}`);
    if (!select) {
        console.warn(`Payment method select not found for item ${itemId}`);
        return;
    }
    
    // Get the currently selected value before clearing
    const currentValue = select.value;
    
    // Clear existing options except the first one
    while (select.options.length > 1) {
        select.remove(1);
    }
    
    if (!paymentMethods || paymentMethods.length === 0) {
        console.warn('Payment methods array is empty');
        return;
    }
    
    paymentMethods.forEach(method => {
        const option = document.createElement('option');
        option.value = method.id;
        option.textContent = method.name;
        // Set selected if this was the original value
        if (currentValue && method.id == currentValue) {
            option.selected = true;
        }
        select.appendChild(option);
    });
    
    // If no match found but we had a value, try to set it again
    if (currentValue && select.value !== currentValue) {
        select.value = currentValue;
    }
}

// Load vehicle categories
function loadVehicleCategories() {
    fetch('/api/expenses/vehicle-categories', {
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
        vehicleCategories = categories;
        })
        .catch(error => {
        console.error('Error loading categories:', error);
    });
}

// Toggle expense type
function toggleExpenseType(select, itemId) {
    const itemDiv = select.closest('.expense-item');
    const descriptionRow = itemDiv.querySelector('.description-field-row');
    const descriptionText = itemDiv.querySelector('.description-text');
    const descriptionCheckbox = itemDiv.querySelector('.description-checkbox-container');
    const container = itemDiv.querySelector('.vehicle-search-container');
    const vehicleSearchInput = container.querySelector('.vehicle-search-input');
    const vehicleIdInput = container.querySelector('.vehicle-id-input');
    
    if (select.value === 'Vehicle') {
        if (descriptionRow) {
            descriptionRow.style.display = 'block';
        }
        if (descriptionCheckbox) {
            descriptionCheckbox.style.display = 'block';
            renderCategoryCheckboxes(itemId);
        }
        if (descriptionText) {
            descriptionText.style.display = 'none';
            descriptionText.removeAttribute('required');
        }
        
        container.style.display = 'block';
        vehicleSearchInput.setAttribute('required', 'required');
        initVehicleSearch(itemId);
    } else if (select.value === 'Operating') {
        if (descriptionRow) {
            descriptionRow.style.display = 'block';
        }
        if (descriptionText) {
            descriptionText.style.display = 'block';
            descriptionText.setAttribute('required', 'required');
        }
        if (descriptionCheckbox) {
            descriptionCheckbox.style.display = 'none';
        }
        
        container.style.display = 'none';
        vehicleSearchInput.removeAttribute('required');
        vehicleIdInput.value = '';
        vehicleSearchInput.value = '';
        container.querySelector('.vehicle-search-results').style.display = 'none';
    } else {
        if (descriptionRow) {
            descriptionRow.style.display = 'none';
        }
        if (descriptionCheckbox) {
            descriptionCheckbox.style.display = 'none';
        }
        
        container.style.display = 'none';
        vehicleSearchInput.removeAttribute('required');
    }
}

// Render category checkboxes
function renderCategoryCheckboxes(itemId) {
    const container = document.getElementById(`category_checkboxes_${itemId}`);
    if (!container) return;
    
    container.innerHTML = '';
    
    vehicleCategories.forEach(category => {
        const div = document.createElement('div');
        div.className = 'form-check';
        div.innerHTML = `
            <input class="form-check-input" type="checkbox" value="${category.name}" id="category_${itemId}_${category.id}" name="categories_${itemId}[]">
            <label class="form-check-label" for="category_${itemId}_${category.id}">
                ${category.name}
            </label>
        `;
        container.appendChild(div);
    });
}

// Initialize vehicle search
function initVehicleSearch(itemId) {
    const vehicleSearchInput = document.querySelector(`.expense-item[data-item-id="${itemId}"] .vehicle-search-input`);
    if (!vehicleSearchInput) return;
    
    let searchTimeout;
    
    vehicleSearchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            const resultsDiv = this.closest('.vehicle-search-container').querySelector('.vehicle-search-results');
            if (resultsDiv) resultsDiv.style.display = 'none';
            return;
        }
        
        searchTimeout = setTimeout(() => {
            performVehicleSearch(query, itemId);
        }, 300);
    });
}

// Perform vehicle search
function performVehicleSearch(query, itemId) {
    fetch(`/api/expenses/vehicles/search?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(vehicles => {
            displayVehicleResults(vehicles, itemId);
        })
        .catch(error => {
            console.error('Error searching vehicles:', error);
        });
}

// Display vehicle search results
function displayVehicleResults(vehicles, itemId) {
    const resultsDiv = document.querySelector(`.expense-item[data-item-id="${itemId}"] .vehicle-search-results`);
    if (!resultsDiv) return;
    
    if (vehicles.length === 0) {
        resultsDiv.innerHTML = '<div class="list-group-item text-muted">No vehicles found</div>';
        resultsDiv.style.display = 'block';
        return;
    }
    
    let html = '<div class="list-group">';
    vehicles.forEach(vehicle => {
        const vehicleName = vehicle.full_name || `${vehicle.make} ${vehicle.model}`;
        html += `<a href="#" class="list-group-item list-group-item-action" onclick="selectVehicle(${vehicle.id}, '${vehicleName.replace(/'/g, "\\'")}', ${itemId}); return false;">
                    <strong>${vehicle.plate_number}</strong> - ${vehicleName}
                 </a>`;
    });
    html += '</div>';
    
    resultsDiv.innerHTML = html;
    resultsDiv.style.display = 'block';
}

// Select vehicle
function selectVehicle(vehicleId, vehicleName, itemId) {
    const itemDiv = document.querySelector(`.expense-item[data-item-id="${itemId}"]`);
    itemDiv.querySelector('.vehicle-id-input').value = vehicleId;
    itemDiv.querySelector('.vehicle-search-input').value = vehicleName;
    itemDiv.querySelector('.vehicle-search-results').style.display = 'none';
}

// Clear vehicle search
function clearVehicleSearch(button) {
    const itemDiv = button.closest('.expense-item');
    itemDiv.querySelector('.vehicle-id-input').value = '';
    itemDiv.querySelector('.vehicle-search-input').value = '';
    itemDiv.querySelector('.vehicle-search-results').style.display = 'none';
}

// Add new expense item (for adding new items to existing transaction)
function addExpenseItem() {
    expenseItemCount++;
    const container = document.getElementById('expenseItemsContainer');
    const noItemsMessage = document.getElementById('noItemsMessage');
    
    if (noItemsMessage) {
        noItemsMessage.style.display = 'none';
    }
    
    // Create new item HTML (similar to create page)
    const itemDiv = document.createElement('div');
    itemDiv.className = 'card mb-3 expense-item';
    itemDiv.setAttribute('data-item-id', expenseItemCount);
    itemDiv.setAttribute('data-is-new', 'true');
    itemDiv.innerHTML = `
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Item #${expenseItemCount} <span class="badge bg-success">New</span></h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeExpenseItem(this)">
                    <i class="fas fa-trash me-1"></i>Remove
                </button>
            </div>
            <!-- Form fields will be added here - same structure as create page -->
        </div>
    `;
    
    container.appendChild(itemDiv);
    // TODO: Add full form structure here or use a template
}

// Remove expense item (for UI only, marks for deletion on update)
function removeExpenseItem(button) {
    const itemDiv = button.closest('.expense-item');
    const isNew = itemDiv.getAttribute('data-is-new') === 'true';
    
    if (isNew) {
        itemDiv.remove();
        } else {
        // For existing items, mark for deletion
        itemDiv.style.display = 'none';
        itemDiv.setAttribute('data-deleted', 'true');
    }
    
    const container = document.getElementById('expenseItemsContainer');
    const visibleItems = container.querySelectorAll('.expense-item:not([style*="display: none"])');
    if (visibleItems.length === 0) {
        document.getElementById('noItemsMessage').style.display = 'block';
    }
}

// Delete expense item immediately via API
function deleteExpenseItem(transactionId, itemId) {
    Swal.fire({
        title: 'Delete Expense Item?',
        text: 'Are you sure you want to delete this expense item? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait while we delete the expense item.',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Delete the item
            fetch(`/expenses/${transactionId}/items/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: data.message || 'Expense item deleted successfully!',
                        confirmButtonColor: '#28a745',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        // Reload the page to refresh the transaction totals
                        location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Failed to delete expense item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.message || 'Failed to delete expense item',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
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
    
    const container = document.getElementById(`category_checkboxes_${itemId}`);
    const existingCheckbox = container.querySelector(`input[value="${categoryName}"]`);
    if (existingCheckbox) {
            Swal.fire({
            icon: 'warning',
            title: 'Category Exists',
            text: 'This category already exists.',
            confirmButtonColor: '#dc3545'
        });
        return;
    }
    
    // Add checkbox directly
    const div = document.createElement('div');
    div.className = 'form-check';
    div.innerHTML = `
        <input class="form-check-input" type="checkbox" value="${categoryName}" id="category_${itemId}_new_${Date.now()}" checked>
        <label class="form-check-label" for="category_${itemId}_new_${Date.now()}">
            ${categoryName}
        </label>
    `;
    container.appendChild(div);
    input.value = '';
}

// Preview receipts (used on other expense forms)
function previewReceipts(input, itemId) {
    const previewContainer = document.querySelector(`.receipt-preview-container[data-item-id="${itemId}"]`);
    if (!previewContainer) return;

    previewContainer.innerHTML = '';

    if (!input.files || input.files.length === 0) return;

    previewContainer.classList.add('row', 'g-2');

    Array.from(input.files).forEach((file) => {
        if (!file.type.startsWith('image/')) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const col = document.createElement('div');
            col.className = 'col-auto';
            col.innerHTML = `
                <div class="position-relative border rounded overflow-hidden" style="width:72px;height:72px;">
                    <img src="${e.target.result}" class="w-100 h-100" style="object-fit:cover;" alt="">
                    <small class="d-block text-truncate px-1" style="max-width:72px;font-size:0.65rem;" title="${file.name}">${file.name}</small>
                </div>
            `;
            previewContainer.appendChild(col);
        };
        reader.readAsDataURL(file);
    });
}

/**
 * Upload receipt images to the server (expense edit page) and show thumbnails after success.
 */
function uploadExpenseItemReceipts(input, itemId, transactionId) {
    const previewContainer = document.querySelector('.receipt-preview-container[data-item-id="' + itemId + '"]');
    const existingWrap = document.getElementById('receipt-existing-' + itemId);
    const countLabel = document.getElementById('receipt-count-label-' + itemId);

    if (!input.files || input.files.length === 0) {
        return;
    }

    const files = Array.from(input.files).filter(function (f) { return f.type.startsWith('image/'); });
    if (files.length === 0) {
        Swal.fire({ icon: 'warning', title: 'Invalid file', text: 'Please choose image files only (JPEG, PNG, GIF, WebP).' });
        input.value = '';
        return;
    }

    let currentCount = 0;
    if (countLabel) {
        const m = countLabel.textContent.match(/Current:\s*(\d+)\/10/);
        if (m) {
            currentCount = parseInt(m[1], 10);
        }
    }
    const remaining = Math.max(0, 10 - currentCount);
    const toUpload = files.slice(0, remaining);

    if (remaining === 0) {
        Swal.fire({ icon: 'info', title: 'Limit reached', text: 'Maximum of 10 receipt images per line item.' });
        input.value = '';
        return;
    }

    if (toUpload.length < files.length) {
        Swal.fire({ icon: 'info', title: 'Partial upload', text: 'Only ' + remaining + ' more receipt(s) allowed. Extra files were not uploaded.' });
    }

    if (toUpload.length === 0) {
        input.value = '';
        return;
    }

    if (previewContainer) {
        previewContainer.innerHTML = '';
        previewContainer.classList.add('row', 'g-2');
        toUpload.forEach(function (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const col = document.createElement('div');
                col.className = 'col-auto';
                col.innerHTML = '<div class="position-relative border rounded overflow-hidden bg-light" style="width:72px;height:72px;">' +
                    '<img src="' + e.target.result + '" class="w-100 h-100" style="object-fit:cover;" alt="">' +
                    '<span class="position-absolute bottom-0 start-0 end-0 text-center text-white py-1" style="background:rgba(0,0,0,0.55);font-size:0.6rem;">Uploading…</span></div>';
                previewContainer.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    }

    const formData = new FormData();
    toUpload.forEach(function (f) {
        formData.append('receipts[]', f);
    });

    fetch('/expenses/' + transactionId + '/items/' + itemId + '/receipts', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData,
        credentials: 'same-origin'
    })
    .then(function (response) {
        return response.json().then(function (data) {
            if (!response.ok) {
                let msg = data.message || 'Upload failed';
                if (data.errors) {
                    msg = Object.values(data.errors).flat().join(' ');
                }
                throw new Error(msg);
            }
            return data;
        });
    })
    .then(function (data) {
        input.value = '';
        if (previewContainer) {
            previewContainer.innerHTML = '';
        }

        if (!data.success || !data.receipts || !data.receipts.length) {
            Swal.fire({ icon: 'success', title: 'Done', text: data.message || 'Upload complete.', timer: 1800, showConfirmButton: false });
            return;
        }

        if (!existingWrap) {
            location.reload();
            return;
        }

        data.receipts.forEach(function (rec) {
            const wrap = document.createElement('div');
            wrap.className = 'receipt-thumbnail d-inline-block me-2 mb-2';
            wrap.style.position = 'relative';

            const img = document.createElement('img');
            img.src = rec.url;
            img.alt = 'Receipt';
            img.className = 'img-thumbnail';
            img.style.width = '80px';
            img.style.height = '80px';
            img.style.objectFit = 'cover';
            img.style.cursor = 'pointer';
            img.addEventListener('click', function () {
                viewReceipt(rec.url, rec.original_name || 'Receipt');
            });

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline-danger';
            btn.title = 'Delete';
            btn.style.cssText = 'position:absolute;top:-5px;right:-5px;padding:2px 6px;';
            btn.innerHTML = '<i class="fas fa-times"></i>';
            btn.addEventListener('click', function () {
                deleteReceiptImage(itemId, rec.id);
            });

            wrap.appendChild(img);
            wrap.appendChild(btn);
            existingWrap.appendChild(wrap);
        });

        const newCount = currentCount + data.receipts.length;
        if (countLabel) {
            countLabel.textContent = '(up to 10 images, max 5MB each) - Current: ' + newCount + '/10';
        }

        Swal.fire({
            icon: 'success',
            title: 'Receipts uploaded',
            text: data.message || 'Upload complete.',
            timer: 2200,
            showConfirmButton: false
        });
    })
    .catch(function (err) {
        console.error(err);
        if (previewContainer) {
            previewContainer.innerHTML = '';
        }
        Swal.fire({ icon: 'error', title: 'Upload failed', text: err.message || 'Could not upload receipts.' });
    });
}

// View receipt
function viewReceipt(imageUrl, imageName) {
    // Create modal dynamically
    const modalHtml = `
        <div class="modal fade" id="viewReceiptModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Receipt Image</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="${imageUrl}" alt="Receipt" class="img-fluid">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <a href="${imageUrl}" download class="btn btn-primary">
                            <i class="fas fa-download me-1"></i>Download
                        </a>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('viewReceiptModal');
    if (existingModal) existingModal.remove();
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById('viewReceiptModal'));
    modal.show();
}

// Delete receipt image
function deleteReceiptImage(itemId, receiptId) {
    Swal.fire({
        title: 'Delete Receipt?',
        text: 'Are you sure you want to delete this receipt image?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/expenses/{{ $expenseTransaction->id }}/items/${itemId}/receipts/${receiptId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: data.message,
                        confirmButtonColor: '#28a745',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Failed to delete receipt');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.message || 'Failed to delete receipt',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
}

// Update all expense items
function updateAllExpenseItems() {
    const form = document.getElementById('expenseForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const items = [];
    const itemsToDelete = [];
    
    document.querySelectorAll('.expense-item').forEach(itemDiv => {
        const itemId = itemDiv.getAttribute('data-item-id');
        const isDeleted = itemDiv.getAttribute('data-deleted') === 'true';
        const isNew = itemDiv.getAttribute('data-is-new') === 'true';
        
        if (isDeleted) {
            if (!isNew && itemId) {
                itemsToDelete.push(itemId);
            }
        return;
    }
    
        const paymentTag = itemDiv.querySelector('.payment-tag-select').value;
        const descriptionText = itemDiv.querySelector('.description-text');
        const descriptionCheckbox = itemDiv.querySelector('.description-checkbox-container');
        
        let description = '';
        if (paymentTag === 'Vehicle') {
            const selectedCategories = Array.from(itemDiv.querySelectorAll('.category-checkboxes input[type="checkbox"]:checked'))
                .map(cb => cb.value);
            description = selectedCategories.join(', ');
        } else {
            description = descriptionText ? descriptionText.value.trim() : '';
        }
        
        const itemData = {
            expense_date: itemDiv.querySelector('input[name="expense_date"]')?.value,
            payment_tag: paymentTag,
            payment_method_id: itemDiv.querySelector('.payment-method-select').value,
            requested_by: itemDiv.querySelector('input[name="requested_by"]')?.value || '',
            approved_by: itemDiv.querySelector('input[name="approved_by"]')?.value || '',
            care_of: itemDiv.querySelector('input[name="care_of"]')?.value || '',
            store_shop: itemDiv.querySelector('input[name="store_shop"]')?.value || '',
            description_details: itemDiv.querySelector('textarea[name="description_details"]')?.value || '',
            description: description,
            cost: parseFloat(itemDiv.querySelector('input[name="cost"]')?.value || 0),
            vehicle_id: paymentTag === 'Vehicle' ? (itemDiv.querySelector('.vehicle-id-input').value || null) : null,
            receipt_checked: itemDiv.querySelector('input[name="receipt_checked"]')?.checked || false,
            receipt_checker: itemDiv.querySelector('input[name="receipt_checker"]')?.value || '',
            receipt_check_date: itemDiv.querySelector('input[name="receipt_check_date"]')?.value || '',
        };
        
        items.push({ id: isNew ? null : itemId, data: itemData });
    });
    
    Swal.fire({
        title: 'Updating Transaction...',
        text: 'Please wait while we update the expense transaction.',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Update all items
    Promise.all(items.map(item => {
        if (item.id) {
            // Update existing item
            return fetch(`/expenses/{{ $expenseTransaction->id }}/items/${item.id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(item.data)
            }).then(response => response.json());
        } else {
            // Add new item
            return fetch(`/expenses/{{ $expenseTransaction->id }}/items`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(item.data)
            }).then(response => response.json());
        }
    })).then(() => {
        // Delete items marked for deletion
        return Promise.all(itemsToDelete.map(itemId => {
            return fetch(`/expenses/{{ $expenseTransaction->id }}/items/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            }).then(response => response.json());
        }));
    }).then(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
            text: 'Expense transaction updated successfully!',
                        confirmButtonColor: '#28a745',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
            location.reload();
        });
    }).catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
            text: error.message || 'Failed to update expense transaction',
                    confirmButtonColor: '#dc3545'
                });
    });
}
</script>
@endsection
