@extends('layouts.app')

@section('title', 'Add Details - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <main class="col-12 px-md-4 main-content" id="mainContent">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-edit me-2"></i>
                    Add Details: {{ str_replace('_', ' ', strtoupper($documentType)) }}
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Vehicle
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Document Details</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('vehicles.documents.store', [$vehicle, $documentType]) }}" enctype="multipart/form-data" id="documentForm">
                                @csrf
                                @if($document)
                                    <input type="hidden" name="document_id" value="{{ $document->id }}">
                                @endif
                                
                                @if(isset($processType))
                                    <input type="hidden" name="process_type" value="{{ $processType }}">
                                @endif
                                
                            <!-- Storage Type Selection -->
                            <div class="mb-4" id="storage_type_section">
                                <label class="form-label fw-bold">Storage Type</label>
                                <div class="btn-group w-100" role="group" aria-label="Storage type selection">
                                    <input type="radio" class="btn-check" name="storage_type" id="storage_link" value="link" {{ old('storage_type', $document->storage_type ?? '') == 'link' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="storage_link">
                                        <i class="fas fa-link me-1"></i>Add File Link
                                    </label>

                                    <input type="radio" class="btn-check" name="storage_type" id="storage_form" value="form" {{ old('storage_type', $document->storage_type ?? '') == 'form' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-primary" for="storage_form">
                                        <i class="fas fa-edit me-1"></i>Custom Form
                                    </label>
                                </div>
                            </div>

                            <!-- File Link Section -->
                            <div id="file_link_section" class="storage-section d-none" style="display: {{ old('storage_type', $document->storage_type ?? '') == 'link' ? 'block' : 'none' }};">
                                <div class="mb-3">
                                    <label class="form-label">File Links</label>
                                    <div id="link_inputs_container">
                                        @if($document && $document->storage_type === 'link' && $document->files && $document->files->where('type', 'link')->count() > 0)
                                            @foreach($document->files->where('type', 'link') as $index => $link)
                                                <div class="link-input-group mb-2">
                                                    <div class="input-group">
                                                        <input type="url" class="form-control" 
                                                               name="file_links[]" 
                                                               value="{{ $link->file_link }}"
                                                               placeholder="https://example.com/document.pdf">
                                                        <button type="button" class="btn btn-outline-danger remove-link-input" style="display: {{ $document->files->where('type', 'link')->count() > 1 ? 'block' : 'none' }};">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <div class="link-input-group mb-2">
                                                <div class="input-group">
                                                    <input type="url" class="form-control" 
                                                           name="file_links[]" 
                                                           placeholder="https://example.com/document.pdf">
                                                    <button type="button" class="btn btn-outline-danger remove-link-input" style="display: none;">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="add_link_input">
                                        <i class="fas fa-plus me-1"></i>Add Another Link
                                    </button>
                                    
                                    @if($document && $document->storage_type === 'link' && $document->files && $document->files->where('type', 'link')->count() > 0)
                                        <div class="mt-3">
                                            <label class="form-label">Current Links:</label>
                                            @foreach($document->files->where('type', 'link') as $link)
                                                <div class="border rounded p-2 bg-light mb-2">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div>
                                                            <small class="text-muted d-block">
                                                                <i class="fas fa-link me-1"></i>
                                                                <a href="{{ $link->file_link }}" target="_blank" class="text-decoration-none">
                                                                    {{ $link->file_link }}
                                                                </a>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Custom Form Section -->
                            <div id="form_section" class="storage-section d-none" style="display: {{ old('storage_type', $document->storage_type ?? '') == 'form' ? 'block' : 'none' }};">
                                <!-- Template Selection -->
                                <div class="mb-3">
                                    <label class="form-label">Template Management</label>
                                    <div class="d-flex gap-2 align-items-end">
                                        <div>
                                            <label class="form-label small">&nbsp;</label>
                                            <button type="button" class="btn btn-outline-success d-block" id="create_new_template_btn">
                                                <i class="fas fa-plus me-1"></i>Create New Template
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Form Builder Mode (Edit) -->
                                <div id="form_builder_mode" class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label mb-0">Document Details</label>
                                    </div>
                                    <div id="form_fields_container">
                                        <!-- Form fields will be added here -->
                                    </div>
                                    <div class="d-flex gap-2 mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="add_form_field">
                                            <i class="fas fa-plus me-1"></i>Add Field
                                        </button>
                                    </div>
                                </div>

                                <!-- Check Date and Checked By Fields -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="check_date" class="form-label">Check Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" 
                                               id="check_date" name="check_date" 
                                               value="{{ old('check_date', $document && $document->check_date ? $document->check_date->format('Y-m-d') : date('Y-m-d')) }}" 
                                               required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="checked_by" class="form-label">Checked By <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" 
                                               id="checked_by" name="checked_by" 
                                               value="{{ old('checked_by', $document && $document->checked_by ? $document->checked_by : Auth::user()->name) }}" 
                                               readonly
                                               required>
                                    </div>
                                </div>

                                <!-- Notes Field -->
                                <div class="mb-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control" 
                                              id="notes" name="notes" 
                                              rows="3" 
                                              placeholder="Additional notes about this document...">{{ old('notes', $document->notes ?? '') }}</textarea>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-secondary me-md-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Save Document
                                </button>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const storageTypeInputs = document.querySelectorAll('input[name="storage_type"]');
    const linkSection = document.getElementById('file_link_section');
    const formSection = document.getElementById('form_section');
    
    // Function to toggle sections based on selected storage type
    function toggleSections() {
        const selectedType = document.querySelector('input[name="storage_type"]:checked')?.value;
        
        console.log('Selected storage type:', selectedType);
        
        // Hide all sections first
        if (linkSection) {
            linkSection.style.display = 'none';
            linkSection.classList.add('d-none');
        }
        if (formSection) {
            formSection.style.display = 'none';
            formSection.classList.add('d-none');
        }
        
        // Show the selected section
        if (selectedType === 'link') {
            if (linkSection) {
                linkSection.style.display = 'block';
                linkSection.classList.remove('d-none');
                linkSection.classList.add('d-block');
            }
        } else if (selectedType === 'form') {
            if (formSection) {
                formSection.style.display = 'block';
                formSection.classList.remove('d-none');
                formSection.classList.add('d-block');
            }
        }
    }
    
    // Add event listeners to radio buttons
    storageTypeInputs.forEach(input => {
        input.addEventListener('change', function() {
            toggleSections();
        });
        
        input.addEventListener('click', function() {
            this.checked = true;
            storageTypeInputs.forEach(radio => {
                if (radio.id !== this.id) {
                    radio.checked = false;
                }
            });
            toggleSections();
        });
    });
    
    // Use event delegation for the btn-group container
    const btnGroup = document.querySelector('.btn-group[role="group"]');
    if (btnGroup) {
        btnGroup.addEventListener('click', function(e) {
            const label = e.target.closest('label[for^="storage_"]');
            if (label) {
                const inputId = label.getAttribute('for');
                const input = document.getElementById(inputId);
                if (input) {
                    input.checked = true;
                    storageTypeInputs.forEach(radio => {
                        if (radio.id !== inputId) {
                            radio.checked = false;
                        }
                    });
                    toggleSections();
                }
            }
        });
    }
    
    // Add/Remove link inputs functionality
    document.getElementById('add_link_input')?.addEventListener('click', function() {
        const container = document.getElementById('link_inputs_container');
        const newInput = document.createElement('div');
        newInput.className = 'link-input-group mb-2';
        newInput.innerHTML = `
            <div class="input-group">
                <input type="url" class="form-control" name="file_links[]" placeholder="https://example.com/document.pdf">
                <button type="button" class="btn btn-outline-danger remove-link-input">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        container.appendChild(newInput);
        updateRemoveButtons();
    });
    
    // Update remove buttons visibility
    function updateRemoveButtons() {
        const linkGroups = document.querySelectorAll('.link-input-group');
        linkGroups.forEach((group, index) => {
            const removeBtn = group.querySelector('.remove-link-input');
            if (removeBtn) {
                removeBtn.style.display = linkGroups.length > 1 ? 'block' : 'none';
                removeBtn.onclick = function() {
                    group.remove();
                    updateRemoveButtons();
                };
            }
        });
    }
    
    // Add form field functionality
    document.getElementById('add_form_field')?.addEventListener('click', function() {
        const container = document.getElementById('form_fields_container');
        const fieldIndex = container.children.length;
        const newField = document.createElement('div');
        newField.className = 'card mb-2 form-field-row';
        newField.setAttribute('data-field-index', fieldIndex);
        newField.innerHTML = `
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <input type="text" class="form-control form-field-label" placeholder="Field Label">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select form-field-type">
                            <option value="text" selected>Text</option>
                            <option value="number">Number</option>
                            <option value="date">Date</option>
                            <option value="textarea">Textarea</option>
                            <option value="select">Select</option>
                            <option value="checkbox">Checkbox</option>
                            <option value="radio">Radio</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="text" class="form-control form-field-value" placeholder="Field Value">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-sm btn-danger remove-field">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
        container.appendChild(newField);
        
        // Add remove functionality
        newField.querySelector('.remove-field').addEventListener('click', function() {
            newField.remove();
        });
    });
    
    // Initialize remove buttons
    updateRemoveButtons();
    
    // Initialize sections on page load if document exists
    const initialStorageType = @json(old('storage_type', $document->storage_type ?? ''));
    if (initialStorageType) {
        const initialInput = document.querySelector(`input[name="storage_type"][value="${initialStorageType}"]`);
        if (initialInput) {
            initialInput.checked = true;
            storageTypeInputs.forEach(radio => {
                if (radio.value !== initialStorageType) {
                    radio.checked = false;
                }
            });
            toggleSections();
        }
    }
    
    // Handle form submission
    document.getElementById('documentForm')?.addEventListener('submit', function(e) {
        const selectedStorageType = document.querySelector('input[name="storage_type"]:checked')?.value;
        
        if (!selectedStorageType) {
            e.preventDefault();
            alert('Please select a storage type (Add File Link or Custom Form)');
            return false;
        }
        
        // Collect file links if storage type is 'link'
        if (selectedStorageType === 'link') {
            const linkInputs = document.querySelectorAll('input[name="file_links[]"]');
            let hasLinks = false;
            linkInputs.forEach(input => {
                if (input.value.trim() !== '') {
                    hasLinks = true;
                }
            });
            
            if (!hasLinks) {
                e.preventDefault();
                alert('Please add at least one file link');
                return false;
            }
        }
        
        // Collect form data if storage type is 'form'
        if (selectedStorageType === 'form') {
            const formFields = document.querySelectorAll('.form-field-row');
            if (formFields.length === 0) {
                e.preventDefault();
                alert('Please add at least one form field');
                return false;
            }
            
            // Build form_data structure
            const formData = {};
            const formStructure = [];
            
            formFields.forEach((field, index) => {
                const label = field.querySelector('.form-field-label')?.value || '';
                const type = field.querySelector('.form-field-type')?.value || 'text';
                const value = field.querySelector('.form-field-value')?.value || '';
                
                if (label) {
                    const fieldName = label.toLowerCase().replace(/\s+/g, '_');
                    formData[fieldName] = value;
                    formStructure.push({
                        name: fieldName,
                        label: label,
                        type: type,
                        value: value
                    });
                }
            });
            
            // Add form structure to form_data
            formData['_form_structure'] = formStructure;
            
            // Create hidden input for form_data
            let formDataInput = document.getElementById('form_data_input');
            if (!formDataInput) {
                formDataInput = document.createElement('input');
                formDataInput.type = 'hidden';
                formDataInput.id = 'form_data_input';
                formDataInput.name = 'form_data';
                this.appendChild(formDataInput);
            }
            formDataInput.value = JSON.stringify(formData);
        }
    });
});
</script>
@endsection

