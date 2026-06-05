@extends('layouts.app')

@section('title', 'Add New Expense Transaction - Car Empire Management System')

@section('content')
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <button class="btn btn-outline-light me-3" type="button" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
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

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-plus me-2"></i>Add New Expense Transaction
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('expenses-inventory') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Expenses
                    </a>
                </div>
            </div>

            <form id="expenseForm">
                @csrf
                
                <!-- Hidden Transaction Details (required by backend) -->
                <input type="hidden" id="transaction_date" name="transaction_date" value="{{ date('Y-m-d') }}">
                <input type="hidden" id="starting_cash" name="starting_cash" value="0">
                <input type="hidden" id="added_cash" name="added_cash" value="0">

                <!-- Expense Items -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list me-2"></i>Expense Items
                        </h5>
                        <button type="button" class="btn btn-sm btn-primary" onclick="addExpenseItem()">
                            <i class="fas fa-plus me-1"></i>Add Item
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="expenseItemsContainer">
                            <!-- Expense items will be added here dynamically -->
                        </div>
                        <div id="noItemsMessage" class="text-center text-muted py-3">
                            <i class="fas fa-info-circle me-2"></i>No expense items added yet. Click "Add Item" to start.
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-end gap-2 mb-4">
                    <a href="{{ route('expenses-inventory') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Save Transaction
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let expenseItemCount = 0;

// Format number with commas and 2 decimal places
function formatPrice(amount) {
    return amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// Calculate summary (function kept for compatibility but no longer displays anything)
function calculateSummary() {
    // Summary section removed - function kept to prevent errors if called
}

// Add expense item
function addExpenseItem() {
    expenseItemCount++;
    const container = document.getElementById('expenseItemsContainer');
    const noItemsMessage = document.getElementById('noItemsMessage');
    
    if (noItemsMessage) {
        noItemsMessage.style.display = 'none';
    }
    
    const itemDiv = document.createElement('div');
    itemDiv.className = 'card mb-3 expense-item';
    itemDiv.setAttribute('data-item-id', expenseItemCount);
    itemDiv.innerHTML = `
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Item #${expenseItemCount}</h6>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeExpenseItem(this)">
                    <i class="fas fa-trash me-1"></i>Remove
                </button>
            </div>
            <!-- 1–2: Expense Date | Expense Type -->
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Expense Date: <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="items[${expenseItemCount}][expense_date]" required value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Expense Type: Operating or Vehicle <span class="text-danger">*</span></label>
                    <select class="form-select payment-tag-select" name="items[${expenseItemCount}][payment_tag]" required onchange="toggleExpenseType(this, ${expenseItemCount})">
                        <option value="">Select</option>
                        <option value="Operating">Operating</option>
                        <option value="Vehicle">Vehicle</option>
                    </select>
                </div>
            </div>
            
            <!-- 3–4: Payment Method | Requested By -->
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Payment Method: <span class="text-danger">*</span></label>
                    <select class="form-select payment-method-select" name="items[${expenseItemCount}][payment_method_id]" required id="payment_method_${expenseItemCount}">
                        <option value="">Select Payment Method</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Requested By:</label>
                    <input type="text" class="form-control" name="items[${expenseItemCount}][requested_by]">
                </div>
            </div>
            
            <!-- 5–6: Approved By | Paid By -->
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Approved By:</label>
                    <input type="text" class="form-control" name="items[${expenseItemCount}][approved_by]">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Paid By:</label>
                    <input type="text" class="form-control" name="items[${expenseItemCount}][care_of]">
                </div>
            </div>
            
            <!-- 7–8: Store/Shop | Notes -->
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Store / Shop:</label>
                    <input type="text" class="form-control" name="items[${expenseItemCount}][store_shop]">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Notes:</label>
                    <textarea class="form-control" name="items[${expenseItemCount}][description_details]" rows="3" placeholder="Enter notes..."></textarea>
                </div>
            </div>
            
            <!-- Description field (for Operating/Vehicle - shown based on expense type) -->
            <div class="row description-field-row g-3" style="display: none;">
                <div class="col-12">
                    <label class="form-label">Description <span class="text-danger">*</span></label>
                    <!-- Text input for Operating Expense -->
                    <textarea class="form-control description-text" id="description_text_${expenseItemCount}" name="items[${expenseItemCount}][description]" rows="3" placeholder="Enter description..."></textarea>
                    <!-- Checkbox list for Vehicle Expense -->
                    <div class="description-checkbox-container" id="description_checkbox_container_${expenseItemCount}" style="display: none;">
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
                    </div>
                </div>
            </div>
            
            <!-- Cost and Vehicle -->
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Cost or Amount: <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="items[${expenseItemCount}][cost]" step="0.01" min="0" required data-item-cost onchange="calculateSummary()">
                </div>
                <div class="col-md-6 vehicle-search-container" style="display: none;">
                    <label class="form-label">Vehicle: <span class="text-danger">*</span></label>
                    <input type="hidden" class="vehicle-id-input" name="items[${expenseItemCount}][vehicle_id]">
                    <div class="input-group">
                        <input type="text" class="form-control vehicle-search-input" placeholder="Search vehicle..." autocomplete="off">
                        <button type="button" class="btn btn-outline-secondary" onclick="clearVehicleSearch(this)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="vehicle-search-results" style="display: none;"></div>
                </div>
            </div>
            <!-- 9. RECEIPT UPLOAD -->
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Receipt Upload:</label>
                    <input type="file" class="form-control receipt-files-input" name="items[${expenseItemCount}][receipts][]" multiple accept="image/*" onchange="previewReceipts(this, ${expenseItemCount})">
                    <small class="text-muted">(up to 10 images, max 5MB each)</small>
                    <div class="receipt-preview-container row g-2 mt-2" data-item-id="${expenseItemCount}"></div>
                </div>
            </div>
            <!-- 10. RECEIPT CHECK FIELDS (2 per row) -->
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="form-check pt-4">
                        <input class="form-check-input" type="checkbox" name="items[${expenseItemCount}][receipt_checked]" id="receipt_checked_${expenseItemCount}" value="1">
                        <label class="form-check-label" for="receipt_checked_${expenseItemCount}">
                            Receipt Checked
                        </label>
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Receipt Checker:</label>
                    <input type="text" class="form-control" name="items[${expenseItemCount}][receipt_checker]">
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Receipt Check Date:</label>
                    <input type="date" class="form-control" name="items[${expenseItemCount}][receipt_check_date]">
                </div>
            </div>
        </div>
    `;
    
    container.appendChild(itemDiv);
    
    // Initialize vehicle search for this item
    initVehicleSearch(expenseItemCount);
    
    // Populate payment method dropdown (will wait if methods not loaded yet)
    if (paymentMethods && paymentMethods.length > 0) {
        populatePaymentMethodDropdown(expenseItemCount);
    } else {
        // Wait for payment methods to load
        loadPaymentMethods().then(() => {
            populatePaymentMethodDropdown(expenseItemCount);
        });
    }
}

// Remove expense item
function removeExpenseItem(button) {
    const itemDiv = button.closest('.expense-item');
    itemDiv.remove();
    calculateSummary();
    
    // Show no items message if empty
    const container = document.getElementById('expenseItemsContainer');
    if (container.children.length === 0) {
        document.getElementById('noItemsMessage').style.display = 'block';
    }
}

let vehicleCategories = [];
let paymentMethods = [];

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
    
    // Clear existing options except the first one
    while (select.options.length > 1) {
        select.remove(1);
    }
    
    // Add payment methods
    if (paymentMethods && paymentMethods.length > 0) {
        paymentMethods.forEach(method => {
            const option = document.createElement('option');
            option.value = method.id;
            option.textContent = method.name;
            select.appendChild(option);
        });
        console.log(`Populated payment methods for item ${itemId}:`, paymentMethods.length, 'methods');
    } else {
        console.warn(`Payment methods not loaded yet for item ${itemId}, will retry...`);
        // If payment methods not loaded yet, wait a bit and try again
        setTimeout(() => {
            if (paymentMethods && paymentMethods.length > 0) {
                populatePaymentMethodDropdown(itemId);
            } else {
                // Try loading again
                loadPaymentMethods().then(() => {
                    populatePaymentMethodDropdown(itemId);
                });
            }
        }, 200);
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

// Toggle expense type (Operating/Vehicle)
function toggleExpenseType(select, itemId) {
    const itemDiv = select.closest('.expense-item');
    const descriptionRow = itemDiv.querySelector('.description-field-row');
    const descriptionText = itemDiv.querySelector('.description-text');
    const descriptionCheckbox = itemDiv.querySelector('.description-checkbox-container');
    const container = itemDiv.querySelector('.vehicle-search-container');
    const vehicleSearchInput = container.querySelector('.vehicle-search-input');
    const vehicleIdInput = container.querySelector('.vehicle-id-input');
    
    if (select.value === 'Vehicle') {
        // Show description row and checkbox container for Vehicle Expense
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
        
        // Show vehicle search
        container.style.display = 'block';
        vehicleSearchInput.setAttribute('required', 'required');
        initVehicleSearch(itemId);
    } else if (select.value === 'Operating') {
        // Show description row and text input for Operating Expense
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
        
        // Hide vehicle search
        container.style.display = 'none';
        vehicleSearchInput.removeAttribute('required');
        vehicleIdInput.value = '';
        vehicleSearchInput.value = '';
        container.querySelector('.vehicle-search-results').style.display = 'none';
    } else {
        // No selection - hide description row
        if (descriptionRow) {
            descriptionRow.style.display = 'none';
        }
        if (descriptionCheckbox) {
            descriptionCheckbox.style.display = 'none';
        }
        
        // Hide vehicle search
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
    
    if (vehicleCategories.length === 0) {
        container.innerHTML = '<p class="text-muted">No categories available. Add a new category below.</p>';
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
    if (vehicleCategories.some(cat => cat.name.toLowerCase() === categoryName.toLowerCase())) {
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
    
    // Add category via API
    fetch('/api/expenses/vehicle-categories', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ name: categoryName })
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                try {
                    const json = JSON.parse(text);
                    let errorMessage = json.message || 'Failed to add category';
                    if (json.errors && json.errors.name) {
                        errorMessage = Array.isArray(json.errors.name) ? json.errors.name[0] : json.errors.name;
                    }
                    throw new Error(errorMessage);
                } catch (parseError) {
                    if (text.includes('<!DOCTYPE') || text.includes('<html')) {
                        throw new Error(`Database table does not exist. Please run: php artisan migrate`);
                    }
                    throw new Error(`Server error (${response.status})`);
                }
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Add to local array
            vehicleCategories.push(data.category);
            // Re-render checkboxes for all items
            document.querySelectorAll('.expense-item').forEach(item => {
                const itemIdAttr = item.getAttribute('data-item-id');
                const paymentTag = item.querySelector('.payment-tag-select').value;
                if (paymentTag === 'Vehicle') {
                    renderCategoryCheckboxes(itemIdAttr);
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
            throw new Error(data.message || 'Failed to add category');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.message || 'Failed to add category',
            confirmButtonColor: '#dc3545'
        });
    });
}

// Toggle vehicle search (kept for compatibility)
function toggleVehicleSearch(select) {
    // This is now handled by toggleExpenseType
    const itemDiv = select.closest('.expense-item');
    const itemId = itemDiv.getAttribute('data-item-id');
    toggleExpenseType(select, itemId);
}

// Initialize vehicle search
function initVehicleSearch(itemId) {
    const itemDiv = document.querySelector(`[data-item-id="${itemId}"]`);
    if (!itemDiv) return;
    
    const searchInput = itemDiv.querySelector('.vehicle-search-input');
    if (!searchInput) return;
    
    // Remove any existing listeners
    const newSearchInput = searchInput.cloneNode(true);
    searchInput.parentNode.replaceChild(newSearchInput, searchInput);
    
    let searchTimeout;
    
    newSearchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();
        
        if (query.length < 2) {
            itemDiv.querySelector('.vehicle-search-results').style.display = 'none';
            return;
        }
        
        searchTimeout = setTimeout(() => {
            fetch(`/api/expenses/vehicles/search?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(vehicles => {
                    displayVehicleResults(this, vehicles);
                })
                .catch(error => {
                    console.error('Error searching vehicles:', error);
                });
        }, 300);
    });
}

// Display vehicle search results
function displayVehicleResults(input, vehicles) {
    const resultsDiv = input.closest('.expense-item').querySelector('.vehicle-search-results');
    const vehicleIdInput = input.closest('.expense-item').querySelector('.vehicle-id-input');
    
    if (vehicles.length === 0) {
        resultsDiv.innerHTML = '<div class="list-group-item">No vehicles found</div>';
        resultsDiv.style.display = 'block';
        return;
    }
    
    let html = '<div class="list-group">';
    vehicles.forEach(vehicle => {
        html += `<a href="#" class="list-group-item list-group-item-action" onclick="selectVehicle(this, ${vehicle.id}, '${vehicle.full_name || vehicle.plate_number}'); return false;">
                    <strong>${vehicle.plate_number}</strong> - ${vehicle.full_name || vehicle.make} ${vehicle.model} (${vehicle.year})
                 </a>`;
    });
    html += '</div>';
    
    resultsDiv.innerHTML = html;
    resultsDiv.style.display = 'block';
}

// Select vehicle
function selectVehicle(element, vehicleId, vehicleName) {
    const itemDiv = element.closest('.expense-item');
    const vehicleIdInput = itemDiv.querySelector('.vehicle-id-input');
    const searchInput = itemDiv.querySelector('.vehicle-search-input');
    const resultsDiv = itemDiv.querySelector('.vehicle-search-results');
    
    vehicleIdInput.value = vehicleId;
    searchInput.value = vehicleName;
    resultsDiv.style.display = 'none';
}

// Clear vehicle search
function clearVehicleSearch(button) {
    const itemDiv = button.closest('.expense-item');
    const vehicleIdInput = itemDiv.querySelector('.vehicle-id-input');
    const searchInput = itemDiv.querySelector('.vehicle-search-input');
    const resultsDiv = itemDiv.querySelector('.vehicle-search-results');
    
    vehicleIdInput.value = '';
    searchInput.value = '';
    resultsDiv.style.display = 'none';
}

// Preview receipts
function previewReceipts(input, itemId) {
    const previewContainer = document.querySelector(`.receipt-preview-container[data-item-id="${itemId}"]`);
    if (!previewContainer) return;
    
    previewContainer.innerHTML = '';
    
    if (!input.files || input.files.length === 0) return;
    
    const maxFiles = 10;
    const files = Array.from(input.files).slice(0, maxFiles);
    
    if (input.files.length > maxFiles) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-warning alert-dismissible fade show w-100';
        alert.innerHTML = `<strong>Note:</strong> Only the first ${maxFiles} images will be uploaded.`;
        previewContainer.appendChild(alert);
    }
    
    files.forEach((file, index) => {
        if (!file.type.startsWith('image/')) {
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewDiv = document.createElement('div');
            previewDiv.style.cssText = 'position: relative; display: inline-block; margin: 5px;';
            previewDiv.innerHTML = `
                <img src="${e.target.result}" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; cursor: pointer;" onclick="window.open('${e.target.result}', '_blank')">
                <div class="mt-1">
                    <small class="d-block text-truncate" style="max-width: 100px;" title="${file.name}">${file.name}</small>
                    <small class="text-muted">${(file.size / 1024).toFixed(2)} KB</small>
                </div>
            `;
            previewContainer.appendChild(previewDiv);
        };
        reader.readAsDataURL(file);
    });
}

// Form submission
document.getElementById('expenseForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate form
    if (!this.checkValidity()) {
        this.reportValidity();
        return;
    }
    
    // Check if at least one item is added
    const itemCount = document.querySelectorAll('.expense-item').length;
    if (itemCount === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Please add at least one expense item.',
            confirmButtonColor: '#dc3545'
        });
        return;
    }
    
    // Collect form data using FormData to handle file uploads
    const formData = new FormData();
    formData.append('transaction_date', document.getElementById('transaction_date').value);
    formData.append('starting_cash', document.getElementById('starting_cash').value);
    formData.append('added_cash', document.getElementById('added_cash').value);
    
    // Validate and collect items with receipts
    try {
        document.querySelectorAll('.expense-item').forEach((itemDiv, index) => {
            const paymentTag = itemDiv.querySelector('[name*="[payment_tag]"]').value;
            
            if (!paymentTag) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: `Item #${index + 1}: Please select Expense Type.`,
                    confirmButtonColor: '#dc3545'
                });
                throw new Error('Validation failed');
            }
            
            // Get description based on expense type
            let description = '';
            if (paymentTag === 'Vehicle') {
                // Get selected categories and join with comma
                const itemId = itemDiv.getAttribute('data-item-id');
                const selectedCategories = Array.from(itemDiv.querySelectorAll(`#category_checkboxes_${itemId} input[type="checkbox"]:checked`))
                    .map(cb => cb.value);
                if (selectedCategories.length === 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: `Item #${index + 1}: Please select at least one category for Vehicle Expense.`,
                        confirmButtonColor: '#dc3545'
                    });
                    throw new Error('Validation failed');
                }
                description = selectedCategories.join(', ');
            } else {
                // Get text description for Operating Expense
                const descriptionText = itemDiv.querySelector('.description-text');
                description = descriptionText ? descriptionText.value.trim() : '';
                if (!description) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: `Item #${index + 1}: Please enter a description for Operating Expense.`,
                        confirmButtonColor: '#dc3545'
                    });
                    throw new Error('Validation failed');
                }
            }
            
            formData.append(`items[${index}][expense_date]`, itemDiv.querySelector('[name*="[expense_date]"]').value);
            formData.append(`items[${index}][payment_method_id]`, itemDiv.querySelector('[name*="[payment_method_id]"]').value);
            formData.append(`items[${index}][description]`, description);
            formData.append(`items[${index}][care_of]`, itemDiv.querySelector('[name*="[care_of]"]').value || '');
            formData.append(`items[${index}][requested_by]`, itemDiv.querySelector('[name*="[requested_by]"]').value || '');
            formData.append(`items[${index}][approved_by]`, itemDiv.querySelector('[name*="[approved_by]"]').value || '');
            formData.append(`items[${index}][store_shop]`, itemDiv.querySelector('[name*="[store_shop]"]').value || '');
            const receiptChecked = itemDiv.querySelector('[name*="[receipt_checked]"]');
            if (receiptChecked && receiptChecked.checked) {
                formData.append(`items[${index}][receipt_checked]`, '1');
            }
            formData.append(`items[${index}][receipt_checker]`, itemDiv.querySelector('[name*="[receipt_checker]"]').value || '');
            formData.append(`items[${index}][receipt_check_date]`, itemDiv.querySelector('[name*="[receipt_check_date]"]').value || '');
            formData.append(`items[${index}][cost]`, itemDiv.querySelector('[name*="[cost]"]').value);
            formData.append(`items[${index}][payment_tag]`, paymentTag);
            
            const vehicleIdInput = itemDiv.querySelector('.vehicle-id-input');
            if (paymentTag === 'Vehicle') {
                if (!vehicleIdInput || !vehicleIdInput.value) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: `Item #${index + 1}: Please select a vehicle for Vehicle Expense.`,
                        confirmButtonColor: '#dc3545'
                    });
                    throw new Error('Validation failed');
                }
                formData.append(`items[${index}][vehicle_id]`, vehicleIdInput.value);
            }
        
            // Add receipt files for this item
            const receiptInput = itemDiv.querySelector('.receipt-files-input');
            if (receiptInput && receiptInput.files && receiptInput.files.length > 0) {
                const maxFiles = Math.min(10, receiptInput.files.length);
                for (let i = 0; i < maxFiles; i++) {
                    formData.append(`items[${index}][receipts][]`, receiptInput.files[i]);
                }
            }
        });
    } catch (error) {
        if (error.message !== 'Validation failed') {
            throw error;
        }
        return; // Stop form submission
    }
    
    // Show loading
    Swal.fire({
        title: 'Saving Transaction...',
        text: 'Please wait while we save the expense transaction.',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Send request
    fetch('{{ route("expenses.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                confirmButtonColor: '#28a745',
                timer: 2000,
                timerProgressBar: true
            }).then(() => {
                window.location.href = '{{ route("expenses-inventory") }}';
            });
        } else {
            throw new Error(data.message || 'Failed to save transaction');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.message || 'Failed to save expense transaction',
            confirmButtonColor: '#dc3545'
        });
    });
});

// No event listeners needed for transaction details since they're hidden

// Load vehicle categories and payment methods on page load
loadVehicleCategories();
loadPaymentMethods();

// Close search results when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.vehicle-search-container')) {
        document.querySelectorAll('.vehicle-search-results').forEach(results => {
            results.style.display = 'none';
        });
    }
});
</script>
@endsection

@section('styles')
<style>
.vehicle-search-results {
    position: absolute;
    z-index: 1000;
    width: 100%;
    max-height: 300px;
    overflow-y: auto;
    background-color: var(--bg-primary);
    border: 1px solid var(--border-color);
    border-radius: 0.375rem;
    margin-top: 5px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.vehicle-search-container {
    position: relative;
}

.receipt-preview-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.receipt-preview-container img {
    border: 2px solid var(--border-color);
    transition: border-color 0.2s ease;
}

.receipt-preview-container img:hover {
    border-color: var(--border-hover);
}
</style>
@endsection

