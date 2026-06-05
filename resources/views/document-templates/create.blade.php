@extends('layouts.app')

@section('title', 'Create Form Template - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="row">
        <main class="col-12 px-md-4 main-content" id="mainContent">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-plus me-2"></i>Create Form Template
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('document-templates.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Templates
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">Template Information</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('document-templates.store') }}" id="templateForm">
                                @csrf
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Template Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" 
                                               value="{{ old('name') }}" 
                                               required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="document_type" class="form-label">Document Type <span class="text-danger">*</span></label>
                                        <select class="form-select @error('document_type') is-invalid @enderror" 
                                                id="document_type" name="document_type" 
                                                required>
                                            <option value="">-- Select Document Type --</option>
                                            @foreach($documentTypes as $key => $label)
                                                <option value="{{ $key }}" {{ old('document_type') == $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('document_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            Active (Template will be available for use)
                                        </label>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="form-label mb-0"><strong>Form Fields</strong></label>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="add_field_btn">
                                            <i class="fas fa-plus me-1"></i>Add Field
                                        </button>
                                    </div>
                                    <div id="form_fields_container">
                                        <!-- Fields will be added here dynamically -->
                                    </div>
                                    <small class="text-muted">Add at least one field to create the template.</small>
                                </div>

                                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                                    <a href="{{ route('document-templates.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Create Template
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
    let fieldIndex = 0;
    const container = document.getElementById('form_fields_container');
    const addBtn = document.getElementById('add_field_btn');

    // Add field function
    function addField(fieldData = {}) {
        const index = fieldIndex++;
        const field = document.createElement('div');
        field.className = 'card mb-3 form-field-row';
        field.dataset.index = index;
        
        field.innerHTML = `
            <div class="card-body">
                <div class="row align-items-start">
                    <div class="col-md-3 mb-2">
                        <label class="form-label small">Field Label <span class="text-danger">*</span></label>
                        <input type="text" class="form-control form-field-label" 
                               name="form_fields[${index}][label]" 
                               placeholder="e.g., Owner Name" 
                               value="${fieldData.label || ''}" 
                               required>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label small">Field Type <span class="text-danger">*</span></label>
                        <select class="form-select form-field-type" 
                                name="form_fields[${index}][type]" 
                                required>
                            <option value="text" ${fieldData.type === 'text' ? 'selected' : ''}>Text</option>
                            <option value="number" ${fieldData.type === 'number' ? 'selected' : ''}>Number</option>
                            <option value="date" ${fieldData.type === 'date' ? 'selected' : ''}>Date</option>
                            <option value="textarea" ${fieldData.type === 'textarea' ? 'selected' : ''}>Textarea</option>
                            <option value="select" ${fieldData.type === 'select' ? 'selected' : ''}>Select</option>
                            <option value="checkbox" ${fieldData.type === 'checkbox' ? 'selected' : ''}>Checkbox</option>
                            <option value="radio" ${fieldData.type === 'radio' ? 'selected' : ''}>Radio</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label small">Field Name</label>
                        <input type="text" class="form-control form-field-name" 
                               name="form_fields[${index}][name]" 
                               placeholder="field_name" 
                               value="${fieldData.name || ''}" 
                               required>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label small">Placeholder</label>
                        <input type="text" class="form-control" 
                               name="form_fields[${index}][placeholder]" 
                               placeholder="Optional placeholder text" 
                               value="${fieldData.placeholder || ''}">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label small">Options</label>
                        <div class="d-flex gap-1">
                            <input type="text" class="form-control form-field-options" 
                                   name="form_fields[${index}][options]" 
                                   placeholder="Option1,Option2" 
                                   value=""
                                   title="For select/radio: comma-separated options">
                            <button type="button" class="btn btn-sm btn-danger remove-field-btn">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                   name="form_fields[${index}][required]" 
                                   value="1" 
                                   id="required_${index}"
                                   ${fieldData.required ? 'checked' : ''}>
                            <label class="form-check-label" for="required_${index}">
                                Required Field
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        container.appendChild(field);
        
        // Auto-generate field name from label
        const labelInput = field.querySelector('.form-field-label');
        const nameInput = field.querySelector('.form-field-name');
        labelInput.addEventListener('blur', function() {
            if (!nameInput.value) {
                const name = this.value.toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_+|_+$/g, '');
                nameInput.value = name;
            }
        });
        
        // Remove field button
        field.querySelector('.remove-field-btn').addEventListener('click', function() {
            field.remove();
        });
    }

    // Add first field by default
    addField();

    // Add field button
    addBtn.addEventListener('click', function() {
        addField();
    });

    // Form submission validation
    document.getElementById('templateForm').addEventListener('submit', function(e) {
        const fields = container.querySelectorAll('.form-field-row');
        if (fields.length === 0) {
            e.preventDefault();
            alert('Please add at least one field to the template.');
            return false;
        }
    });
});
</script>
@endsection
