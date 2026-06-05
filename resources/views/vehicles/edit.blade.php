@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <main class="col-md-12 ms-sm-auto px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Edit Vehicle</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Back to Unit Report
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
                            <form action="{{ route('vehicles.update', $vehicle) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="year" class="form-label">Year <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('year') is-invalid @enderror" 
                                               id="year" name="year" value="{{ old('year', $vehicle->year) }}" 
                                               min="1900" max="{{ date('Y') + 1 }}" required>
                                        @error('year')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="make_search" class="form-label">Make <span class="text-danger">*</span></label>
                                        <div class="position-relative">
                                            <input type="text" class="form-control @error('make_id') is-invalid @enderror" 
                                                   id="make_search" placeholder="Type to search makes..." autocomplete="off"
                                                   value="{{ old('make_search', is_object($vehicle->make) ? $vehicle->make->name : (is_string($vehicle->make) ? $vehicle->make : '')) }}">
                                            <input type="hidden" id="make_id" name="make_id" value="{{ old('make_id', $vehicle->make_id) }}">
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
                                                   id="model_search" placeholder="Select a Make first..." autocomplete="off"
                                                   value="{{ old('model_search', $vehicle->vehicleModel ? $vehicle->vehicleModel->name : '') }}">
                                            <input type="hidden" id="model_id" name="model_id" value="{{ old('model_id', $vehicle->model_id) }}">
                                            <div id="model_suggestions" class="suggestions-dropdown"></div>
                                        </div>
                                        @error('model_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="variant" class="form-label">Variant</label>
                                        <input type="text" class="form-control @error('variant') is-invalid @enderror" 
                                               id="variant" name="variant" value="{{ old('variant', $vehicle->variant) }}">
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
                                                   id="body_type_search" placeholder="Type to search body types..." autocomplete="off" value="{{ old('body_type', $vehicle->body_type) }}">
                                            <input type="hidden" id="body_type" name="body_type" value="{{ old('body_type', $vehicle->body_type) }}">
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
                                            <option value="Manual" {{ old('transmission', $vehicle->transmission) == 'Manual' ? 'selected' : '' }}>Manual</option>
                                            <option value="Automatic" {{ old('transmission', $vehicle->transmission) == 'Automatic' ? 'selected' : '' }}>Automatic</option>
                                            <option value="CVT" {{ old('transmission', $vehicle->transmission) == 'CVT' ? 'selected' : '' }}>CVT</option>
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
                                            <option value="Gasoline" {{ old('fuel_type', $vehicle->fuel_type) == 'Gasoline' ? 'selected' : '' }}>Gasoline</option>
                                            <option value="Diesel" {{ old('fuel_type', $vehicle->fuel_type) == 'Diesel' ? 'selected' : '' }}>Diesel</option>
                                            <option value="Hybrid" {{ old('fuel_type', $vehicle->fuel_type) == 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                                            <option value="Electric" {{ old('fuel_type', $vehicle->fuel_type) == 'Electric' ? 'selected' : '' }}>Electric</option>
                                        </select>
                                        @error('fuel_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="kilometers" class="form-label">Kilometers</label>
                                        <input type="number" class="form-control @error('kilometers') is-invalid @enderror" 
                                               id="kilometers" name="kilometers" value="{{ old('kilometers', $vehicle->kilometers) }}" 
                                               min="0">
                                        @error('kilometers')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="plate_number" class="form-label">Plate Number <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('plate_number') is-invalid @enderror" 
                                               id="plate_number" name="plate_number" value="{{ old('plate_number', $vehicle->plate_number) }}" 
                                               required>
                                        @error('plate_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="colour" class="form-label">Colour</label>
                                        <input type="text" class="form-control @error('colour') is-invalid @enderror" 
                                               id="colour" name="colour" value="{{ old('colour', $vehicle->colour) }}">
                                        @error('colour')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" 
                                                id="status" name="status" required>
                                            <option value="">Select Status</option>
                                            <option value="Available" {{ old('status', $vehicle->status) == 'Available' ? 'selected' : '' }}>Available</option>
                                            <option value="Under Maintenance" {{ old('status', $vehicle->status) == 'Under Maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                                            <option value="Reserved" {{ old('status', $vehicle->status) == 'Reserved' ? 'selected' : '' }}>Reserved</option>
                                            <option value="Released" {{ old('status', $vehicle->status) == 'Released' ? 'selected' : '' }}>Released</option>
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
                                                   value="{{ old('purchase_price', $vehicle->purchase_price ? number_format($vehicle->purchase_price, 2, '.', ',') : '') }}" 
                                                   placeholder="0.00" required>
                                            <input type="hidden" name="purchase_price" id="purchase_price_hidden">
                                        </div>
                                        @error('purchase_price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="purchase_date" class="form-label">Purchase Date</label>
                                        <input type="date" class="form-control @error('purchase_date') is-invalid @enderror" 
                                               id="purchase_date" name="purchase_date" value="{{ old('purchase_date', $vehicle->purchase_date ? $vehicle->purchase_date->format('Y-m-d') : '') }}">
                                        @error('purchase_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="purchased_from" class="form-label">Purchased From</label>
                                        <input type="text" class="form-control @error('purchased_from') is-invalid @enderror" 
                                               id="purchased_from" name="purchased_from" value="{{ old('purchased_from', $vehicle->purchased_from) }}">
                                        @error('purchased_from')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row border-top pt-3 mt-2">
                                    <div class="col-12">
                                        <h6 class="text-muted mb-3"><i class="fas fa-tags me-1"></i>Pricelist &amp; Financing</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Option 1 (Low Down Payment)</label>
                                        <div class="row g-2">
                                            <div class="col-12"><input type="number" step="0.01" min="0" class="form-control form-control-sm" name="option1_cash_out" placeholder="All in cash out" value="{{ old('option1_cash_out', $vehicle->option1_cash_out) }}"></div>
                                            <div class="col-6"><input type="number" step="0.01" min="0" class="form-control form-control-sm" name="option1_12mos" placeholder="12 Mos" value="{{ old('option1_12mos', $vehicle->option1_12mos) }}"></div>
                                            <div class="col-6"><input type="number" step="0.01" min="0" class="form-control form-control-sm" name="option1_24mos" placeholder="24 Mos" value="{{ old('option1_24mos', $vehicle->option1_24mos) }}"></div>
                                            <div class="col-6"><input type="number" step="0.01" min="0" class="form-control form-control-sm" name="option1_36mos" placeholder="36 Mos" value="{{ old('option1_36mos', $vehicle->option1_36mos) }}"></div>
                                            <div class="col-6"><input type="number" step="0.01" min="0" class="form-control form-control-sm" name="option1_48mos" placeholder="48 Mos" value="{{ old('option1_48mos', $vehicle->option1_48mos) }}"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Option 2 (Low Monthly Payment)</label>
                                        <div class="row g-2">
                                            <div class="col-12"><input type="number" step="0.01" min="0" class="form-control form-control-sm" name="option2_cash_out" placeholder="All in cash out" value="{{ old('option2_cash_out', $vehicle->option2_cash_out) }}"></div>
                                            <div class="col-6"><input type="number" step="0.01" min="0" class="form-control form-control-sm" name="option2_12mos" placeholder="12 Mos" value="{{ old('option2_12mos', $vehicle->option2_12mos) }}"></div>
                                            <div class="col-6"><input type="number" step="0.01" min="0" class="form-control form-control-sm" name="option2_24mos" placeholder="24 Mos" value="{{ old('option2_24mos', $vehicle->option2_24mos) }}"></div>
                                            <div class="col-6"><input type="number" step="0.01" min="0" class="form-control form-control-sm" name="option2_36mos" placeholder="36 Mos" value="{{ old('option2_36mos', $vehicle->option2_36mos) }}"></div>
                                            <div class="col-6"><input type="number" step="0.01" min="0" class="form-control form-control-sm" name="option2_48mos" placeholder="48 Mos" value="{{ old('option2_48mos', $vehicle->option2_48mos) }}"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Vehicle Features</label>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="with_tools" name="with_tools" value="1" 
                                                           {{ old('with_tools', $vehicle->with_tools) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="with_tools">
                                                        With Tools
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="with_matting" name="with_matting" value="1" 
                                                           {{ old('with_matting', $vehicle->with_matting) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="with_matting">
                                                        With Matting
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="with_spare_tire" name="with_spare_tire" value="1" 
                                                           {{ old('with_spare_tire', $vehicle->with_spare_tire) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="with_spare_tire">
                                                        With Spare Tire
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="spare_key" name="spare_key" value="1" 
                                                           {{ old('spare_key', $vehicle->spare_key) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="spare_key">
                                                        Spare Key
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="notes" class="form-label">Notes</label>
                                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                  id="notes" name="notes" rows="3">{{ old('notes', $vehicle->notes) }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('vehicles.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>
                                        Update Vehicle
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Custom Fields Section -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-plus-circle me-2"></i>Custom Fields
                            </h5>
                            <div class="card-actions">
                                <button type="button" class="btn btn-sm btn-primary" onclick="openCustomFieldModal('vehicle_information')">
                                    <i class="fas fa-plus me-1"></i>Add Field
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="custom-fields-container">
                                @foreach($vehicle->customFieldsForSection('vehicle_information')->get() as $field)
                                    <div class="row mb-3 field-row" data-field-id="{{ $field->id }}">
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">{{ $field->field_label }}</label>
                                        </div>
                                        <div class="col-md-6">
                                            @if($field->field_type == 'checkbox')
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           id="field_{{ $field->id }}" 
                                                           {{ $field->field_value ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="field_{{ $field->id }}">
                                                        {{ $field->field_label }}
                                                    </label>
                                                </div>
                                            @elseif($field->field_type == 'select')
                                                <select class="form-select" id="field_{{ $field->id }}">
                                                    <option value="">Select {{ $field->field_label }}</option>
                                                    @foreach($field->field_options as $option)
                                                        <option value="{{ $option }}" {{ $field->field_value == $option ? 'selected' : '' }}>
                                                            {{ $option }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @elseif($field->field_type == 'textarea')
                                                <textarea class="form-control" id="field_{{ $field->id }}" rows="3">{{ $field->field_value }}</textarea>
                                            @else
                                                <input type="{{ $field->field_type == 'number' ? 'number' : ($field->field_type == 'date' ? 'date' : 'text') }}" 
                                                       class="form-control" id="field_{{ $field->id }}" 
                                                       value="{{ $field->field_value }}">
                                            @endif
                                        </div>
                                        <div class="col-md-2">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editCustomField({{ $field->id }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteCustomField({{ $field->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                
                                @if($vehicle->customFieldsForSection('vehicle_information')->count() == 0)
                                    <div class="text-center py-4">
                                        <i class="fas fa-plus-circle fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No custom fields added yet.</p>
                                        <button type="button" class="btn btn-primary" onclick="openCustomFieldModal('vehicle_information')">
                                            <i class="fas fa-plus me-1"></i>Add Your First Field
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<style>
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

.field-row {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 0.375rem;
    margin-bottom: 1rem;
}

.field-row:hover {
    background-color: #e9ecef;
}

.btn-group .btn {
    margin-right: 0.25rem;
}

.btn-group .btn:last-child {
    margin-right: 0;
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
    let selectedMakeId = makeId.value;
    
    // Enable model search if make is already selected
    if (selectedMakeId) {
        modelSearch.disabled = false;
        modelSearch.placeholder = 'Type to search models...';
    }
    
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
        
        clearTimeout(modelTimeout);
        modelTimeout = setTimeout(() => {
            if (query.length >= 1 && selectedMakeId) {
                searchModels(query, selectedMakeId);
            } else {
                hideSuggestions(modelSuggestions);
            }
        }, 300);
    });
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
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
            // Count commas before cursor position in new value
            const commasInFormatted = (formatted.substring(0, cursorPosition + (formatted.length - oldValue.length)).match(/,/g) || []).length;
            
            // Adjust cursor position based on comma changes
            let newPosition = cursorPosition;
            if (formatted.length !== oldValue.length) {
                // Count digits before cursor in unformatted value
                const digitsBeforeCursor = unformattedValue.substring(0, cursorPosition - commasBeforeCursor).length;
                // Find equivalent position in formatted value
                let digitCount = 0;
                for (let i = 0; i < formatted.length; i++) {
                    if (/\d/.test(formatted[i])) {
                        digitCount++;
                        if (digitCount >= digitsBeforeCursor) {
                            newPosition = i + 1;
                            break;
                        }
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
            title: 'Update Vehicle?',
            text: 'Are you sure you want to update this vehicle information?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, Update Vehicle!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Updating Vehicle...',
                    text: 'Please wait while we update the vehicle information.',
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
<!-- Custom Field Modal -->
<div class="modal fade" id="customFieldModal" tabindex="-1" aria-labelledby="customFieldModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customFieldModalLabel">Add Custom Field</h5>
                <button type="button" class="btn-close" onclick="closeCustomFieldModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="customFieldForm">
                    @csrf
                    <input type="hidden" id="fieldSectionName" name="section_name">
                    <input type="hidden" id="fieldId" name="field_id">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="fieldName" class="form-label">Field Name</label>
                            <input type="text" class="form-control" id="fieldName" name="field_name" required>
                        </div>
                        <div class="col-md-6">
                            <label for="fieldLabel" class="form-label">Field Label</label>
                            <input type="text" class="form-control" id="fieldLabel" name="field_label" required>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="fieldType" class="form-label">Field Type</label>
                            <select class="form-select" id="fieldType" name="field_type" onchange="toggleFieldOptions()" required>
                                <option value="">Select Type</option>
                                <option value="text">Text</option>
                                <option value="textarea">Textarea</option>
                                <option value="number">Number</option>
                                <option value="date">Date</option>
                                <option value="email">Email</option>
                                <option value="url">URL</option>
                                <option value="select">Select</option>
                                <option value="checkbox">Checkbox</option>
                                <option value="radio">Radio</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="fieldRequired" class="form-label">Required</label>
                            <select class="form-select" id="fieldRequired" name="is_required">
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row mb-3" id="fieldValueRow" style="display: none;">
                        <div class="col-md-12">
                            <label for="fieldValue" class="form-label">Field Value</label>
                            <input type="text" class="form-control" id="fieldValue" name="field_value">
                        </div>
                    </div>
                    
                    <div class="row mb-3" id="fieldOptionsRow" style="display: none;">
                        <div class="col-md-12">
                            <label for="fieldOptions" class="form-label">Options (one per line)</label>
                            <textarea class="form-control" id="fieldOptions" name="field_options" rows="3" placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeCustomFieldModal()">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveCustomField()" id="saveFieldBtn">Save Field</button>
            </div>
        </div>
    </div>
</div>

<script>
// Custom Field Functions
let currentEditingFieldId = null;

function openCustomFieldModal(sectionName) {
    currentEditingFieldId = null;
    document.getElementById('customFieldModalLabel').textContent = 'Add Custom Field';
    document.getElementById('saveFieldBtn').textContent = 'Save Field';
    document.getElementById('customFieldForm').reset();
    document.getElementById('fieldSectionName').value = sectionName;
    document.getElementById('fieldId').value = '';
    document.getElementById('fieldValueRow').style.display = 'none';
    document.getElementById('fieldOptionsRow').style.display = 'none';
    document.getElementById('customFieldModal').style.display = 'block';
    document.getElementById('customFieldModal').classList.add('show');
    document.body.classList.add('modal-open');
}

function closeCustomFieldModal() {
    currentEditingFieldId = null;
    document.getElementById('customFieldModal').style.display = 'none';
    document.getElementById('customFieldModal').classList.remove('show');
    document.body.classList.remove('modal-open');
}

function toggleFieldOptions() {
    const fieldType = document.getElementById('fieldType').value;
    const optionsRow = document.getElementById('fieldOptionsRow');
    const valueRow = document.getElementById('fieldValueRow');
    
    if (fieldType === 'select' || fieldType === 'radio') {
        optionsRow.style.display = 'block';
        valueRow.style.display = 'none';
    } else if (fieldType === 'checkbox') {
        optionsRow.style.display = 'none';
        valueRow.style.display = 'block';
        document.getElementById('fieldValue').value = '0';
    } else {
        optionsRow.style.display = 'none';
        valueRow.style.display = 'block';
    }
}

function saveCustomField() {
    const form = document.getElementById('customFieldForm');
    const formData = new FormData(form);
    
    // Validate required fields
    const fieldName = formData.get('field_name');
    const fieldLabel = formData.get('field_label');
    const fieldType = formData.get('field_type');
    
    if (!fieldName || !fieldLabel || !fieldType) {
        alert('Please fill in all required fields');
        return;
    }
    
    // Prepare data
    const data = {
        section_name: formData.get('section_name'),
        field_name: fieldName.trim(),
        field_label: fieldLabel.trim(),
        field_type: fieldType,
        field_value: formData.get('field_value') || '',
        field_options: [],
        is_required: formData.get('is_required') === '1'
    };
    
    // Handle options for select and radio fields
    if (fieldType === 'select' || fieldType === 'radio') {
        const optionsText = formData.get('field_options');
        data.field_options = optionsText ? optionsText.split('\n').filter(option => option.trim() !== '') : [];
        
        if (data.field_options.length === 0) {
            alert('Please add options for ' + fieldType + ' field');
            return;
        }
    }
    
    // Determine if we're creating or updating
    const isEditing = currentEditingFieldId !== null;
    const url = isEditing 
        ? `/vehicles/{{ $vehicle->id }}/custom-fields/${currentEditingFieldId}`
        : `/vehicles/{{ $vehicle->id }}/custom-fields`;
    const method = isEditing ? 'PUT' : 'POST';
    
    // Send AJAX request
    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const action = isEditing ? 'updated' : 'created';
            alert(`Custom field ${action} successfully!`);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the custom field: ' + error.message);
    });
}

function editCustomField(fieldId) {
    currentEditingFieldId = fieldId;
    
    // Update modal title and button
    document.getElementById('customFieldModalLabel').textContent = 'Edit Custom Field';
    document.getElementById('saveFieldBtn').textContent = 'Update Field';
    
    // Get field data from the server
    fetch(`/vehicles/{{ $vehicle->id }}/custom-fields/${fieldId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const field = data.field;
                
                // Populate form
                document.getElementById('fieldSectionName').value = field.section_name;
                document.getElementById('fieldId').value = field.id;
                document.getElementById('fieldName').value = field.field_name;
                document.getElementById('fieldLabel').value = field.field_label;
                document.getElementById('fieldType').value = field.field_type;
                document.getElementById('fieldValue').value = field.field_value || '';
                document.getElementById('fieldRequired').value = field.is_required ? '1' : '0';
                
                // Handle options
                if (field.field_options && field.field_options.length > 0) {
                    document.getElementById('fieldOptions').value = field.field_options.join('\n');
                }
                
                // Toggle options display
                toggleFieldOptions();
                
                // Open modal
                document.getElementById('customFieldModal').style.display = 'block';
                document.getElementById('customFieldModal').classList.add('show');
                document.body.classList.add('modal-open');
            } else {
                alert('Error loading field: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading the field');
        });
}

function deleteCustomField(fieldId) {
    if (confirm('Are you sure you want to delete this custom field?')) {
        fetch(`/vehicles/{{ $vehicle->id }}/custom-fields/${fieldId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the custom field');
        });
    }
}
</script>

@endsection
