@extends('layouts.app')

@section('title', 'Edit Document - Car Empire Management System')

@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <main class="col-12 px-md-4 main-content" id="mainContent">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-edit me-2"></i>
                    Edit Document: {{ str_replace('_', ' ', strtoupper($document->document_type)) }}
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
                            <h5 class="card-title mb-0">Document Information</h5>
                        </div>
                        <div class="card-body">
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            
                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            
                            <form method="POST" action="{{ route('vehicles.documents.update', [$vehicle, $document]) }}" enctype="multipart/form-data" id="documentForm">
                                @csrf
                                @method('PUT')
                                
                                @php
                                    // Check if document has storage data
                                    $hasStorageData = false;
                                    if ($document->storage_type) {
                                        if ($document->storage_type === 'link') {
                                            $hasStorageData = $document->files && $document->files->where('type', 'link')->count() > 0;
                                        } elseif ($document->storage_type === 'form') {
                                            $hasStorageData = $document->form_data && !empty($document->form_data) && (count($document->form_data) > 0 || (isset($document->form_data['_form_structure']) && !empty($document->form_data['_form_structure'])));
                                        } elseif ($document->storage_type === 'file') {
                                            $hasStorageData = $document->files && $document->files->where('type', 'file')->count() > 0;
                                        }
                                    }
                                @endphp

                                @if(!$document->is_completed)
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
                                    @error('storage_type')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                @endif

                                <!-- File Link Section -->
                                <div id="file_link_section" class="storage-section d-none" style="display: {{ old('storage_type', $document->storage_type ?? '') == 'link' ? 'block' : 'none' }};">
                                    <div class="mb-3">
                                        <label class="form-label">File Links</label>
                                        <div id="link_inputs_container">
                                            @if($document->storage_type === 'link' && $document->files && $document->files->where('type', 'link')->count() > 0)
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
                                    </div>
                                </div>

                                <!-- Custom Form Section -->
                                <div id="form_section" class="storage-section d-none" style="display: {{ old('storage_type', $document->storage_type ?? '') == 'form' ? 'block' : 'none' }};">
                                    @php
                                        // Initialize variables for form data
                                        $formData = old('form_data', $document->form_data ?? []);
                                        $formFields = old('form_fields', []);
                                        
                                        // Check if form structure exists in form_data
                                        if (empty($formFields) && !empty($formData) && isset($formData['_form_structure'])) {
                                            $formFields = $formData['_form_structure'];
                                            unset($formData['_form_structure']);
                                        }
                                        
                                        $hasFormFields = !empty($formFields) && is_array($formFields) && count($formFields) > 0;
                                    @endphp
                                    
                                    <!-- Form Builder Mode (Edit) -->
                                    <div id="form_builder_mode" class="mb-3" style="display: {{ $hasFormFields ? 'none' : 'block' }};">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label mb-0">Document Details</label>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" id="switch_to_view_mode" style="display: {{ $hasFormFields ? 'block' : 'none' }};">
                                                <i class="fas fa-eye me-1"></i>View Form
                                            </button>
                                        </div>
                                        <div id="form_fields_container">
                                            @if(!empty($formFields) && is_array($formFields))
                                                @foreach($formFields as $index => $field)
                                                    <div class="card mb-2 form-field-row" data-field-index="{{ $index }}">
                                                        <div class="card-body">
                                                            <div class="row align-items-center">
                                                                <div class="col-md-3">
                                                                    <input type="text" class="form-control form-field-label" 
                                                                           placeholder="Field Label" 
                                                                           value="{{ $field['label'] ?? $field['name'] ?? '' }}">
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <select class="form-select form-field-type">
                                                                        <option value="text" {{ ($field['type'] ?? 'text') == 'text' ? 'selected' : '' }}>Text</option>
                                                                        <option value="number" {{ ($field['type'] ?? '') == 'number' ? 'selected' : '' }}>Number</option>
                                                                        <option value="date" {{ ($field['type'] ?? '') == 'date' ? 'selected' : '' }}>Date</option>
                                                                        <option value="textarea" {{ ($field['type'] ?? '') == 'textarea' ? 'selected' : '' }}>Textarea</option>
                                                                        <option value="select" {{ ($field['type'] ?? '') == 'select' ? 'selected' : '' }}>Select</option>
                                                                        <option value="checkbox" {{ ($field['type'] ?? '') == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                                                                        <option value="radio" {{ ($field['type'] ?? '') == 'radio' ? 'selected' : '' }}>Radio</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <input type="text" class="form-control form-field-value" 
                                                                           placeholder="Field Value" 
                                                                           value="{{ $formData[$field['name']] ?? $field['value'] ?? '' }}">
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <button type="button" class="btn btn-sm btn-danger remove-field">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @elseif(isset($formData) && !empty($formData) && is_array($formData) && !isset($formData['_form_structure']))
                                                @foreach($formData as $key => $value)
                                                    <div class="card mb-2 form-field-row" data-field-index="{{ $loop->index }}">
                                                        <div class="card-body">
                                                            <div class="row align-items-center">
                                                                <div class="col-md-3">
                                                                    <input type="text" class="form-control form-field-label" 
                                                                           placeholder="Field Label" 
                                                                           value="{{ $key }}">
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
                                                                    <input type="text" class="form-control form-field-value" 
                                                                           placeholder="Field Value" 
                                                                           value="{{ $value }}">
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <button type="button" class="btn btn-sm btn-danger remove-field">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                        <div class="d-flex gap-2 mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" id="add_form_field">
                                                <i class="fas fa-plus me-1"></i>Add Field
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Check Date and Checked By Fields -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="check_date" class="form-label">Check Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" 
                                               id="check_date" name="check_date" 
                                               value="{{ old('check_date', $document->check_date ? $document->check_date->format('Y-m-d') : date('Y-m-d')) }}" 
                                               required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="checked_by" class="form-label">Checked By <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" 
                                               id="checked_by" name="checked_by" 
                                               value="{{ old('checked_by', $document->checked_by ?? Auth::user()->name) }}" 
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

                                <!-- Action Buttons -->
                                <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                                    <div>
                                        <button type="button" class="btn btn-danger" id="deleteDocumentBtn" data-document-id="{{ $document->id }}">
                                            <i class="fas fa-trash me-1"></i>Delete Document
                                        </button>
                                    </div>
                                    <div>
                                        <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-secondary me-md-2">Cancel</a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>Update Document
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this document? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
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
    
    // Initialize sections on page load
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
    
    // Delete document functionality
    document.getElementById('deleteDocumentBtn')?.addEventListener('click', function() {
        const documentId = this.getAttribute('data-document-id');
        const deleteForm = document.getElementById('deleteForm');
        deleteForm.action = '{{ route("vehicles.documents.destroy", [$vehicle, $document]) }}';
        
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    });
    
    // Handle form submission for form data
    document.getElementById('documentForm')?.addEventListener('submit', function(e) {
        const selectedStorageType = document.querySelector('input[name="storage_type"]:checked')?.value;
        
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



