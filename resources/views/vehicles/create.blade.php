@extends('layouts.app')

@section('title', 'Add New Vehicle - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <main class="col-12 main-content px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Add New Vehicle</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Unit Report
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Vehicle Information</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('vehicles.store') }}">
                                @csrf
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="year" class="form-label">Year <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('year') is-invalid @enderror" 
                                               id="year" name="year" value="{{ old('year') }}" 
                                               min="1900" max="{{ date('Y') + 1 }}" required>
                                        @error('year')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="make_search" class="form-label">Make <span class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <input type="text" class="form-control @error('make_id') is-invalid @enderror" 
                                                   id="make_search" placeholder="Type to search makes..." autocomplete="off">
                                            <input type="hidden" id="make_id" name="make_id" value="{{ old('make_id') }}">
                                            <div id="make_suggestions" class="suggestions-dropdown"></div>
                                        </div>
                                        @error('make_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="model_search" class="form-label">Model <span class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <input type="text" class="form-control @error('model_id') is-invalid @enderror" 
                                                   id="model_search" placeholder="Type to search models..." autocomplete="off">
                                            <input type="hidden" id="model_id" name="model_id" value="{{ old('model_id') }}">
                                            <div id="model_suggestions" class="suggestions-dropdown"></div>
                                        </div>
                                        @error('model_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="variant" class="form-label">Variant</label>
                                        <input type="text" class="form-control @error('variant') is-invalid @enderror" 
                                               id="variant" name="variant" value="{{ old('variant') }}">
                                        @error('variant')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="body_type_search" class="form-label">Body Type</label>
                                        <div class="position-relative">
                                            <input type="text" class="form-control" 
                                                   id="body_type_search" placeholder="Type to search body types..." autocomplete="off" value="{{ old('body_type') }}">
                                            <input type="hidden" id="body_type" name="body_type" value="{{ old('body_type') }}">
                                            <div id="body_type_suggestions" class="suggestions-dropdown"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="transmission" class="form-label">Transmission <span class="text-danger">*</span></label>
                                        <select class="form-select @error('transmission') is-invalid @enderror" 
                                                id="transmission" name="transmission" required>
                                            <option value="">Select Transmission</option>
                                            <option value="Manual" {{ old('transmission') == 'Manual' ? 'selected' : '' }}>Manual</option>
                                            <option value="Automatic" {{ old('transmission') == 'Automatic' ? 'selected' : '' }}>Automatic</option>
                                        </select>
                                        @error('transmission')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="fuel_type" class="form-label">Fuel Type <span class="text-danger">*</span></label>
                                        <select class="form-select @error('fuel_type') is-invalid @enderror" 
                                                id="fuel_type" name="fuel_type" required>
                                            <option value="">Select Fuel Type</option>
                                            <option value="Diesel" {{ old('fuel_type') == 'Diesel' ? 'selected' : '' }}>Diesel</option>
                                            <option value="Gasoline" {{ old('fuel_type') == 'Gasoline' ? 'selected' : '' }}>Gasoline</option>
                                            <option value="Hybrid" {{ old('fuel_type') == 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                                            <option value="Electric" {{ old('fuel_type') == 'Electric' ? 'selected' : '' }}>Electric</option>
                                        </select>
                                        @error('fuel_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="kilometers" class="form-label">Kilometers <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('kilometers') is-invalid @enderror" 
                                               id="kilometers" name="kilometers" value="{{ old('kilometers') }}" 
                                               min="0" required>
                                        @error('kilometers')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="plate_number" class="form-label">Plate Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('plate_number') is-invalid @enderror" 
                                               id="plate_number" name="plate_number" value="{{ old('plate_number') }}" 
                                               placeholder="e.g., ABC-1234" required>
                                        @error('plate_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="colour" class="form-label">Colour <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('colour') is-invalid @enderror" 
                                               id="colour" name="colour" value="{{ old('colour') }}" required>
                                        @error('colour')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" 
                                                id="status" name="status" required>
                                            <option value="">Select Status</option>
                                            <option value="Available" {{ old('status') == 'Available' ? 'selected' : '' }}>Available</option>
                                            <option value="Under Maintenance" {{ old('status') == 'Under Maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                                            <option value="Reserved" {{ old('status') == 'Reserved' ? 'selected' : '' }}>Reserved</option>
                                            <option value="Released" {{ old('status') == 'Released' ? 'selected' : '' }}>Released</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="purchase_price" class="form-label">Purchase Price <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">₱</span>
                                            <input type="text" class="form-control @error('purchase_price') is-invalid @enderror" 
                                                   id="purchase_price" 
                                                   value="{{ old('purchase_price') ? number_format(old('purchase_price'), 2, '.', ',') : '' }}" 
                                                   placeholder="0.00" required>
                                            <input type="hidden" name="purchase_price" id="purchase_price_hidden">
                                        </div>
                                        @error('purchase_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="purchase_date" class="form-label">Purchase Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" 
                                               id="purchase_date" name="purchase_date" value="{{ old('purchase_date') }}" required>
                                        @error('purchase_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="purchased_from" class="form-label">Purchased From <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('purchased_from') is-invalid @enderror" 
                                           id="purchased_from" name="purchased_from" value="{{ old('purchased_from') }}" required>
                                    @error('purchased_from')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="with_tools" name="with_tools" value="1" {{ old('with_tools') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="with_tools">
                                                With Tools
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="with_matting" name="with_matting" value="1" {{ old('with_matting') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="with_matting">
                                                With Matting
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="with_spare_tire" name="with_spare_tire" value="1" {{ old('with_spare_tire') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="with_spare_tire">
                                                With Spare Tire
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="spare_key" name="spare_key" value="1" {{ old('spare_key') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="spare_key">
                                                Spare Key
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3" placeholder="Additional notes about the vehicle...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <a href="{{ route('vehicles.index') }}" class="btn btn-secondary me-md-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Save Details
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Quick Tips</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-info-circle text-primary me-2"></i>
                                    <strong>Year:</strong> Enter the manufacturing year
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-info-circle text-primary me-2"></i>
                                    <strong>Plate Number:</strong> Must be unique
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-info-circle text-primary me-2"></i>
                                    <strong>Purchase Price:</strong> Enter the amount paid
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-info-circle text-primary me-2"></i>
                                    <strong>Status:</strong> Set current availability
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

@section('styles')
<style>
/* Sidebar Styles - Hidden by default */
#sidebarCollapse {
    display: none !important;
}

#sidebarCollapse.show {
    display: block !important;
}

@media (min-width: 768px) {
    #sidebarCollapse {
        display: none !important;
    }
    
    #sidebarCollapse.show {
        display: block !important;
    }
}

.sidebar {
    width: 250px;
    max-width: 250px;
    margin-right: 0;
    padding: 0;
    flex: 0 0 250px;
}

.main-content {
    margin-left: 0 !important;
    width: 100%;
    flex: 1;
    padding-left: 2rem;
    padding-right: 2rem;
}

@media (min-width: 768px) {
    .sidebar.show {
        display: block !important;
    }
    
    .main-content {
        margin-left: 0 !important;
        max-width: 100%;
    }
}

.suggestions-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ced4da;
    border-top: none;
    border-radius: 0 0 0.375rem 0.375rem;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    display: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.suggestion-item {
    padding: 0.5rem 0.75rem;
    cursor: pointer;
    border-bottom: 1px solid #f8f9fa;
    transition: background-color 0.15s ease-in-out;
}

.suggestion-item:hover {
    background-color: #f8f9fa;
}

.suggestion-item:last-child {
    border-bottom: none;
}

.suggestion-item.highlighted {
    background-color: #e9ecef;
}

.position-relative {
    position: relative;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const makeSearch = document.getElementById('make_search');
    const makeId = document.getElementById('make_id');
    const makeSuggestions = document.getElementById('make_suggestions');
    
    const modelSearch = document.getElementById('model_search');
    const modelId = document.getElementById('model_id');
    const modelSuggestions = document.getElementById('model_suggestions');
    
    const bodyTypeSearch = document.getElementById('body_type_search');
    const bodyType = document.getElementById('body_type');
    const bodyTypeSuggestions = document.getElementById('body_type_suggestions');
    
    // Static body types list
    const bodyTypes = [
        'Sedan',
        'Hatchback',
        'SUV',
        'Crossover',
        'Coupe',
        'Convertible',
        'Wagon / Estate',
        'Pickup Truck',
        'Van',
        'Minivan / MPV',
        'Roadster',
        'Sports Car',
        'Supercar',
        'Hypercar',
        'Limousine',
        'Microcar',
        'Off-road / 4x4',
        'Fastback',
        'Liftback',
        'Targa Top',
        'Ute',
        'Coupé SUV',
        'Cabrio SUV',
        'Shooting Brake',
        'Crossover Coupe',
        'Panel Van'
    ];
    
    let makeTimeout;
    let modelTimeout;
    let bodyTypeTimeout;
    let selectedMakeId = null;
    
    // Make autocomplete functionality
    makeSearch.addEventListener('input', function() {
        const query = this.value.trim();
        
        clearTimeout(makeTimeout);
        makeTimeout = setTimeout(() => {
            if (query.length >= 1) {
                searchMakes(query);
            } else {
                hideSuggestions(makeSuggestions);
            }
        }, 300);
    });
    
    // Model autocomplete functionality
    modelSearch.addEventListener('input', function() {
        const query = this.value.trim();
        console.log('Model input event:', { query, selectedMakeId });
        
        clearTimeout(modelTimeout);
        modelTimeout = setTimeout(() => {
            if (query.length >= 1 && selectedMakeId) {
                console.log('Calling searchModels with:', { query, selectedMakeId });
                searchModels(query, selectedMakeId);
            } else {
                console.log('Hiding suggestions - query length:', query.length, 'selectedMakeId:', selectedMakeId);
                hideSuggestions(modelSuggestions);
            }
        }, 300);
    });
    
    // Search makes function
    function searchMakes(query) {
        console.log('Searching makes:', query);
        
        // Use Laravel's base URL helper to ensure correct path including subdirectory
        const baseUrl = '{{ url("/") }}';
        const makesUrl = `${baseUrl}/api/makes/search?q=${encodeURIComponent(query)}`;
        
        console.log('Makes URL:', makesUrl);
        
        fetch(makesUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin'
        })
            .then(response => {
                console.log('Makes response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(makes => {
                console.log('Makes received:', makes);
                if (Array.isArray(makes) && makes.length > 0) {
                    showSuggestions(makeSuggestions, makes, function(make) {
                        console.log('Make selected:', make);
                        makeSearch.value = make.name;
                        makeId.value = make.id;
                        selectedMakeId = make.id;
                        hideSuggestions(makeSuggestions);
                        
                        // Enable model search
                        modelSearch.disabled = false;
                        modelSearch.placeholder = 'Type to search models...';
                        modelSearch.value = '';
                        modelId.value = '';
                        console.log('Model search enabled, selectedMakeId:', selectedMakeId);
                    });
                } else {
                    hideSuggestions(makeSuggestions);
                }
            })
            .catch(error => {
                console.error('Error searching makes:', error);
                hideSuggestions(makeSuggestions);
                // Show user-friendly error
                console.error('Failed to search makes. Please check your connection and try again.');
            });
    }
    
    // Search models function
    function searchModels(query, makeId) {
        console.log('Searching models:', { query, makeId });
        
        if (!makeId) {
            console.warn('Cannot search models without make_id');
            hideSuggestions(modelSuggestions);
            return;
        }
        
        // Use Laravel's base URL helper to ensure correct path including subdirectory
        const baseUrl = '{{ url("/") }}';
        const modelsUrl = `${baseUrl}/api/models/search?q=${encodeURIComponent(query)}&make_id=${makeId}`;
        
        console.log('Models URL:', modelsUrl);
        
        fetch(modelsUrl, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin'
        })
            .then(response => {
                console.log('Models response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(models => {
                console.log('Models received:', models);
                if (Array.isArray(models) && models.length > 0) {
                    showSuggestions(modelSuggestions, models, function(model) {
                        modelSearch.value = model.name;
                        modelId.value = model.id;
                        hideSuggestions(modelSuggestions);
                    });
                } else {
                    hideSuggestions(modelSuggestions);
                }
            })
            .catch(error => {
                console.error('Error searching models:', error);
                hideSuggestions(modelSuggestions);
                // Show user-friendly error
                console.error('Failed to search models. Please check your connection and try again.');
            });
    }
    
    // Show suggestions dropdown
    function showSuggestions(container, items, onSelect) {
        container.innerHTML = '';
        
        if (items.length === 0) {
            container.innerHTML = '<div class="suggestion-item">No results found</div>';
        } else {
            items.forEach(item => {
                const div = document.createElement('div');
                div.className = 'suggestion-item';
                div.textContent = item.name;
                div.addEventListener('click', () => onSelect(item));
                container.appendChild(div);
            });
        }
        
        container.style.display = 'block';
    }
    
    // Hide suggestions dropdown
    function hideSuggestions(container) {
        container.style.display = 'none';
    }
    
    // Body type autocomplete functionality
    bodyTypeSearch.addEventListener('input', function() {
        const query = this.value.trim().toLowerCase();
        
        clearTimeout(bodyTypeTimeout);
        bodyTypeTimeout = setTimeout(() => {
            if (query.length >= 0) {
                filterBodyTypes(query);
            } else {
                hideSuggestions(bodyTypeSuggestions);
            }
        }, 200);
    });
    
    // Filter body types function
    function filterBodyTypes(query) {
        const filtered = bodyTypes.filter(type => 
            type.toLowerCase().includes(query)
        );
        
        if (filtered.length > 0 || query.length > 0) {
            showSuggestions(bodyTypeSuggestions, filtered.map(type => ({ name: type })), function(item) {
                bodyTypeSearch.value = item.name;
                bodyType.value = item.name;
                hideSuggestions(bodyTypeSuggestions);
            });
        } else {
            hideSuggestions(bodyTypeSuggestions);
        }
    }
    
    // Show all body types when input is focused and empty
    bodyTypeSearch.addEventListener('focus', function() {
        if (this.value.trim() === '') {
            showSuggestions(bodyTypeSuggestions, bodyTypes.map(type => ({ name: type })), function(item) {
                bodyTypeSearch.value = item.name;
                bodyType.value = item.name;
                hideSuggestions(bodyTypeSuggestions);
            });
        }
    });
    
    // Clear body type when search input is cleared
    bodyTypeSearch.addEventListener('input', function() {
        if (this.value.trim() === '') {
            bodyType.value = '';
        }
    });
    
    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.position-relative')) {
            hideSuggestions(makeSuggestions);
            hideSuggestions(modelSuggestions);
            hideSuggestions(bodyTypeSuggestions);
        }
    });
    
    // Price formatting function
    function formatPrice(value) {
        // Remove all non-numeric characters except decimal point
        let numericValue = value.replace(/[^\d.]/g, '');
        
        // Remove multiple decimal points, keep only the first one
        const parts = numericValue.split('.');
        if (parts.length > 2) {
            numericValue = parts[0] + '.' + parts.slice(1).join('');
        }
        
        // Limit to 2 decimal places
        if (parts.length === 2) {
            numericValue = parts[0] + '.' + parts[1].substring(0, 2);
        }
        
        // Format with commas
        const num = parseFloat(numericValue);
        if (isNaN(num) || numericValue === '') {
            return '';
        }
        
        // Split integer and decimal parts
        const integerPart = Math.floor(num).toString();
        const decimalPart = parts.length === 2 ? parts[1].substring(0, 2) : '';
        
        // Add commas to integer part
        const formattedInteger = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        
        return decimalPart ? `${formattedInteger}.${decimalPart}` : formattedInteger;
    }
    
    function unformatPrice(value) {
        // Remove commas and return numeric value
        return value.replace(/,/g, '');
    }
    
    // Apply price formatting to purchase_price input
    const purchasePriceInput = document.getElementById('purchase_price');
    const purchasePriceHidden = document.getElementById('purchase_price_hidden');
    
    if (purchasePriceInput) {
        // Format on input
        purchasePriceInput.addEventListener('input', function(e) {
            const cursorPosition = this.selectionStart;
            const oldValue = this.value;
            const unformattedValue = unformatPrice(oldValue);
            const formatted = formatPrice(unformattedValue);
            
            this.value = formatted;
            
            // Calculate new cursor position
            // Count commas before cursor in old value
            const commasBeforeCursor = (oldValue.substring(0, cursorPosition).match(/,/g) || []).length;
            
            // Count digits before cursor in unformatted value
            const digitsBeforeCursor = unformattedValue.substring(0, cursorPosition - commasBeforeCursor).length;
            
            // Find equivalent position in formatted value
            let digitCount = 0;
            let newPosition = formatted.length;
            for (let i = 0; i < formatted.length; i++) {
                if (/\d/.test(formatted[i])) {
                    digitCount++;
                    if (digitCount >= digitsBeforeCursor) {
                        newPosition = i + 1;
                        break;
                    }
                }
            }
            
            this.setSelectionRange(newPosition, newPosition);
            
            // Update hidden field with unformatted value
            if (purchasePriceHidden) {
                const num = parseFloat(unformattedValue);
                purchasePriceHidden.value = isNaN(num) ? '' : num.toString();
            }
        });
        
        // Format on blur (when user leaves the field)
        purchasePriceInput.addEventListener('blur', function() {
            const unformatted = unformatPrice(this.value);
            const num = parseFloat(unformatted);
            if (!isNaN(num) && num >= 0) {
                this.value = formatPrice(num.toFixed(2));
                if (purchasePriceHidden) {
                    purchasePriceHidden.value = num.toFixed(2);
                }
            } else if (this.value === '') {
                if (purchasePriceHidden) {
                    purchasePriceHidden.value = '';
                }
            }
        });
        
        // Initialize hidden field
        if (purchasePriceHidden && purchasePriceInput.value) {
            const unformatted = unformatPrice(purchasePriceInput.value);
            const num = parseFloat(unformatted);
            purchasePriceHidden.value = isNaN(num) ? '' : num.toString();
        }
    }
    
    // Handle form validation and confirmation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Always prevent default first
        
        // Update hidden field with unformatted value before submission
        if (purchasePriceInput && purchasePriceHidden) {
            const unformatted = unformatPrice(purchasePriceInput.value);
            const num = parseFloat(unformatted);
            
            if (isNaN(num) || num < 0 || unformatted === '') {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please enter a valid purchase price',
                    confirmButtonColor: '#dc3545'
                });
                purchasePriceInput.focus();
                return;
            }
            
            purchasePriceHidden.value = num.toFixed(2);
        }
        
        if (!makeId.value || !modelId.value) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please select both Make and Model',
                confirmButtonColor: '#dc3545'
            });
            return;
        }
        
        // Show confirmation dialog
        Swal.fire({
            title: 'Save Vehicle Details?',
            text: 'Are you sure you want to save this vehicle information? You will be redirected to continue adding other details.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Save Details!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Saving Vehicle Details...',
                    text: 'Please wait while we save the vehicle information.',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Submit the form
                form.submit();
            }
        });
    });
    
    // Keyboard navigation for suggestions
    makeSearch.addEventListener('keydown', function(e) {
        handleKeyNavigation(e, makeSuggestions);
    });
    
    modelSearch.addEventListener('keydown', function(e) {
        handleKeyNavigation(e, modelSuggestions);
    });
    
    bodyTypeSearch.addEventListener('keydown', function(e) {
        handleKeyNavigation(e, bodyTypeSuggestions);
    });
    
    function handleKeyNavigation(e, container) {
        const items = container.querySelectorAll('.suggestion-item');
        const highlighted = container.querySelector('.suggestion-item.highlighted');
        let currentIndex = -1;
        
        if (highlighted) {
            currentIndex = Array.from(items).indexOf(highlighted);
        }
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            currentIndex = Math.min(currentIndex + 1, items.length - 1);
            updateHighlight(items, currentIndex);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            currentIndex = Math.max(currentIndex - 1, 0);
            updateHighlight(items, currentIndex);
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (highlighted) {
                highlighted.click();
            }
        } else if (e.key === 'Escape') {
            hideSuggestions(container);
        }
    }
    
    function updateHighlight(items, index) {
        items.forEach(item => item.classList.remove('highlighted'));
        if (items[index]) {
            items[index].classList.add('highlighted');
        }
    }
});
</script>
@endsection
