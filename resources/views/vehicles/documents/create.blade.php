@extends('layouts.app')

@section('title', 'Add Document - Car Empire Management System')

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
                    <i class="fas fa-file-alt me-2"></i>
                    {{ $document ? 'Edit' : 'Add' }} Document: {{ str_replace('_', ' ', strtoupper($documentType)) }}
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Vehicle
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
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
                            
                            <form method="POST" action="{{ route('vehicles.documents.store', [$vehicle, $documentType]) }}" enctype="multipart/form-data" id="documentForm">
                                @csrf
                                
                                @if(isset($processType))
                                    <input type="hidden" name="process_type" value="{{ $processType }}">
                                @endif
                                
                                @if($document)
                                    <input type="hidden" name="document_id" value="{{ $document->id }}">
                                    @php
                                        // Check if document has storage data
                                        $hasStorageData = false;
                                        if ($document->storage_type) {
                                            if ($document->storage_type === 'link') {
                                                $hasStorageData = $document->file_link || ($document->files && $document->files->where('type', 'link')->count() > 0);
                                            } elseif ($document->storage_type === 'form') {
                                                $hasStorageData = $document->form_data && !empty($document->form_data) && (count($document->form_data) > 0 || (isset($document->form_data['_form_structure']) && !empty($document->form_data['_form_structure'])));
                                            } elseif ($document->storage_type === 'file') {
                                                $hasStorageData = $document->file_path || ($document->files && $document->files->where('type', 'file')->count() > 0);
                                            }
                                        }
                                    @endphp
                                @else
                                    @php
                                        $hasStorageData = false;
                                    @endphp
                                @endif

                                @if(!$document || !$document->is_completed)
                                <div class="mb-4" id="storage_type_section">
                                    <label class="form-label fw-bold">Storage Type <small class="text-muted">(Optional - You can mark as completed without selecting)</small></label>
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

                                <!-- File Upload Section -->
                                <div id="file_upload_section" class="storage-section" style="display: none;">
                                    <div class="mb-3">
                                        <label class="form-label">Upload Files</label>
                                        <div id="file_inputs_container">
                                            <div class="file-input-group mb-2">
                                                <div class="input-group">
                                                    <input type="file" class="form-control @error('files.*') is-invalid @enderror" 
                                                           name="files[]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                    <button type="button" class="btn btn-outline-danger remove-file-input" style="display: none;">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="add_file_input">
                                            <i class="fas fa-plus me-1"></i>Add Another File
                                        </button>
                                        <small class="form-text text-muted d-block mt-2">Maximum file size: 10MB per file. Allowed formats: PDF, DOC, DOCX, JPG, PNG</small>
                                        @error('files.*')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                        
                                        @if(isset($document) && $document && $document->files && $document->files->where('type', 'file')->count() > 0)
                                            <div class="mt-3">
                                                <label class="form-label">Current Files:</label>
                                                @foreach($document->files->where('type', 'file') as $file)
                                                    <div class="border rounded p-2 bg-light mb-2">
                                                        @php
                                                            $fileExtension = strtolower(pathinfo($file->file_name, PATHINFO_EXTENSION));
                                                            $isImage = in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                        @endphp
                                                        
                                                        @if($isImage)
                                                            <div class="mb-2">
                                                                <img src="{{ $file->url }}" 
                                                                     alt="{{ $file->file_name }}" 
                                                                     class="img-thumbnail" 
                                                                     style="max-height: 150px; max-width: 100%; cursor: pointer;"
                                                                     onclick="window.open('{{ $file->url }}', '_blank')">
                                                            </div>
                                                        @endif
                                                        
                                                        <div class="d-flex align-items-center justify-content-between">
                                                            <div>
                                                                <small class="text-muted d-block">
                                                                    <i class="fas fa-file me-1"></i>{{ $file->file_name }}
                                                                </small>
                                                                <small class="text-muted">
                                                                    <a href="{{ $file->url }}" 
                                                                       class="text-decoration-none" 
                                                                       target="_blank">
                                                                        <i class="fas fa-download me-1"></i>Download
                                                                    </a>
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        
                                        <!-- Preview for newly selected files -->
                                        <div id="file_preview" class="mt-3" style="display: none;">
                                            <label class="form-label">New File Preview:</label>
                                            <div class="border rounded p-2 bg-light">
                                                <div id="file_preview_content"></div>
                                                <small class="text-muted d-block mt-2" id="file_preview_name"></small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- File Link Section -->
                                <div id="file_link_section" class="storage-section d-none" style="display: none;">
                                    <div class="mb-3">
                                        <label class="form-label">File Links</label>
                                        <div id="link_inputs_container">
                                            <div class="link-input-group mb-2">
                                                <div class="input-group">
                                                    <input type="url" class="form-control @error('file_links.*') is-invalid @enderror" 
                                                           name="file_links[]" 
                                                           placeholder="https://example.com/document.pdf">
                                                    <button type="button" class="btn btn-outline-danger remove-link-input" style="display: none;">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="add_link_input">
                                            <i class="fas fa-plus me-1"></i>Add Another Link
                                        </button>
                                        @error('file_links.*')
                                            <div class="text-danger mt-1">{{ $message }}</div>
                                        @enderror
                                        
                                        @if(isset($document) && $document && $document->files && $document->files->where('type', 'link')->count() > 0)
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
                                <div id="form_section" class="storage-section d-none" style="display: none;">
                                    <!-- Template Selection -->
                                    <div class="mb-3">
                                        <label class="form-label">Template Management</label>
                                        <div class="d-flex gap-2 align-items-end">
                                            @if(isset($templates) && $templates->count() > 0)
                                            <div class="flex-grow-1">
                                                <label class="form-label small">Load Existing Template</label>
                                                <select class="form-select" id="template_select">
                                                    <option value="">-- Select a template --</option>
                                                    @foreach($templates as $template)
                                                        <option value="{{ $template->id }}">{{ $template->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="form-label small">&nbsp;</label>
                                                <button type="button" class="btn btn-outline-primary d-block" id="load_template_btn">
                                                    <i class="fas fa-download me-1"></i>Load Template
                                                </button>
                                            </div>
                                            @endif
                                            <div>
                                                <label class="form-label small">&nbsp;</label>
                                                <button type="button" class="btn btn-outline-success d-block" id="create_new_template_btn">
                                                    <i class="fas fa-plus me-1"></i>Create New Template
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @php
                                        // Initialize variables
                                        $formData = old('form_data', $document->form_data ?? []);
                                        $formFields = old('form_fields', []);
                                        
                                        // Check if form structure exists in form_data
                                        if (empty($formFields) && !empty($formData) && isset($formData['_form_structure'])) {
                                            $formFields = $formData['_form_structure'];
                                            unset($formData['_form_structure']); // Remove from formData for display
                                        }
                                        
                                        // If we have form fields, start in view mode
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
                                                    @include('vehicles.documents.partials.form-field', ['field' => $field, 'index' => $index, 'formData' => $formData])
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
                                            <button type="button" class="btn btn-sm btn-outline-success" id="save_template_btn" data-bs-toggle="modal" data-bs-target="#saveTemplateModal">
                                                <i class="fas fa-save me-1"></i>Save as Template
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Form View Mode (Submit) -->
                                    <div id="form_view_mode" class="mb-3" style="display: {{ $hasFormFields ? 'block' : 'none' }};">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label mb-0">Document Details</label>
                                            <button type="button" class="btn btn-sm btn-outline-warning" id="switch_to_edit_mode" style="display: {{ $hasFormFields ? 'block' : 'none' }};">
                                                <i class="fas fa-edit me-1"></i>Edit Form
                                            </button>
                                        </div>
                                        <div id="form_inputs_container">
                                            @if($hasFormFields && isset($formFields))
                                                @foreach($formFields as $index => $field)
                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            {{ $field['label'] ?? $field['name'] }}
                                                            @if($field['required'] ?? false)
                                                                <span class="text-danger">*</span>
                                                            @endif
                                                        </label>
                                                        @if(($field['type'] ?? 'text') == 'textarea')
                                                            <textarea class="form-control" 
                                                                      name="form_data[{{ $field['name'] }}]"
                                                                      placeholder="{{ $field['placeholder'] ?? '' }}"
                                                                      {{ ($field['required'] ?? false) ? 'required' : '' }}>{{ $formData[$field['name']] ?? '' }}</textarea>
                                                        @elseif(($field['type'] ?? 'text') == 'select')
                                                            <select class="form-select" 
                                                                    name="form_data[{{ $field['name'] }}]"
                                                                    {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                                                <option value="">-- Select --</option>
                                                                @if(isset($field['options']) && is_array($field['options']))
                                                                    @foreach($field['options'] as $option)
                                                                        <option value="{{ $option }}" {{ (isset($formData[$field['name']]) && $formData[$field['name']] == $option) ? 'selected' : '' }}>{{ $option }}</option>
                                                                    @endforeach
                                                                @endif
                                                            </select>
                                                        @elseif(($field['type'] ?? 'text') == 'checkbox')
                                                            <div class="form-check">
                                                                <input type="checkbox" 
                                                                       class="form-check-input" 
                                                                       name="form_data[{{ $field['name'] }}]"
                                                                       value="1"
                                                                       {{ (isset($formData[$field['name']]) && $formData[$field['name']]) ? 'checked' : '' }}>
                                                                <label class="form-check-label">{{ $field['label'] ?? $field['name'] }}</label>
                                                            </div>
                                                        @elseif(($field['type'] ?? 'text') == 'radio')
                                                            @if(isset($field['options']) && is_array($field['options']))
                                                                @foreach($field['options'] as $option)
                                                                    <div class="form-check">
                                                                        <input type="radio" 
                                                                               class="form-check-input" 
                                                                               name="form_data[{{ $field['name'] }}]"
                                                                               value="{{ $option }}"
                                                                               id="radio_{{ $field['name'] }}_{{ $loop->index }}"
                                                                               {{ (isset($formData[$field['name']]) && $formData[$field['name']] == $option) ? 'checked' : '' }}>
                                                                        <label class="form-check-label" for="radio_{{ $field['name'] }}_{{ $loop->index }}">{{ $option }}</label>
                                                                    </div>
                                                                @endforeach
                                                            @endif
                                                        @else
                                                            <input type="{{ $field['type'] ?? 'text' }}" 
                                                                   class="form-control" 
                                                                   name="form_data[{{ $field['name'] }}]"
                                                                   value="{{ $formData[$field['name']] ?? '' }}"
                                                                   placeholder="{{ $field['placeholder'] ?? '' }}"
                                                                   {{ ($field['required'] ?? false) ? 'required' : '' }}>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="check_date" class="form-label">Check Date <span class="text-danger">*</span></label>
                                        <input type="date" class="form-control @error('check_date') is-invalid @enderror" 
                                               id="check_date" name="check_date" 
                                               value="{{ old('check_date', $document && $document->check_date ? $document->check_date->format('Y-m-d') : date('Y-m-d')) }}" 
                                               required>
                                        @error('check_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="checked_by" class="form-label">Checked By <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('checked_by') is-invalid @enderror" 
                                               id="checked_by" name="checked_by" 
                                               value="{{ old('checked_by', $document && $document->checked_by ? $document->checked_by : Auth::user()->name) }}" 
                                               readonly
                                               required>
                                        @error('checked_by')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3" id="notes_section" style="display: {{ ($document && $document->is_completed) ? 'none' : 'block' }};">
                                    <label for="notes" class="form-label">Notes</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3" 
                                              placeholder="Additional notes about this document...">{{ old('notes', $document->notes ?? '') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    @if($document)
                                        @if($document->is_completed)
                                            <button type="button" class="btn btn-success me-md-2" onclick="markDocumentIncomplete({{ $document->id }})">
                                                <i class="fas fa-check-circle me-1"></i>Marked as Completed
                                            </button>
                                        @elseif(!$hasStorageData)
                                            <button type="button" class="btn btn-outline-success me-md-2" id="markCompletedBtn" onclick="markDocumentCompleted({{ $document->id }})">
                                                <i class="fas fa-check me-1"></i>Mark Completed
                                            </button>
                                        @endif
                                    @else
                                        <button type="button" class="btn btn-outline-success me-md-2" id="markNewCompletedBtn" onclick="markNewDocumentCompleted()">
                                            <i class="fas fa-check me-1"></i>Mark Completed
                                        </button>
                                    @endif
                                    <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-secondary me-md-2">Cancel</a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>{{ $document ? 'Update' : 'Save' }} Document
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const storageTypeInputs = document.querySelectorAll('input[name="storage_type"]');
    const sections = {
        'file': document.getElementById('file_upload_section'),
        'link': document.getElementById('file_link_section'),
        'form': document.getElementById('form_section')
    };
    
    // Verify sections are found
    console.log('=== INITIAL SECTION CHECK ===');
    console.log('Sections found:', {
        file: !!sections.file,
        link: !!sections.link,
        form: !!sections.form
    });
    
    // Log actual elements
    if (sections.link) {
        console.log('Link section element:', sections.link);
        console.log('Link section parent:', sections.link.parentElement);
        console.log('Link section computed style:', window.getComputedStyle(sections.link));
    }
    if (sections.form) {
        console.log('Form section element:', sections.form);
        console.log('Form section parent:', sections.form.parentElement);
        console.log('Form section computed style:', window.getComputedStyle(sections.form));
    }

    // Show/hide sections based on selected storage type
    function toggleSections() {
        // Re-fetch sections to ensure they exist
        const fileSection = document.getElementById('file_upload_section');
        const linkSection = document.getElementById('file_link_section');
        const formSection = document.getElementById('form_section');
        
        const selectedType = document.querySelector('input[name="storage_type"]:checked')?.value;
        
        console.log('=== TOGGLE SECTIONS ===');
        console.log('Selected storage type:', selectedType);
        console.log('Sections found:', {
            file: !!fileSection,
            link: !!linkSection,
            form: !!formSection
        });
        
        // Check if document is completed
        const isCompleted = {{ ($document && $document->is_completed) ? 'true' : 'false' }};
        
        // If document is completed, hide all storage sections
        if (isCompleted) {
            console.log('Document is completed, hiding all sections');
            if (fileSection) fileSection.style.display = 'none';
            if (linkSection) linkSection.style.display = 'none';
            if (formSection) formSection.style.display = 'none';
            return;
        }
        
        // Hide all sections first
        if (fileSection) {
            fileSection.style.display = 'none';
            fileSection.classList.remove('d-block');
            fileSection.classList.add('d-none');
        }
        if (linkSection) {
            linkSection.style.display = 'none';
            linkSection.classList.remove('d-block');
            linkSection.classList.add('d-none');
        }
        if (formSection) {
            formSection.style.display = 'none';
            formSection.classList.remove('d-block');
            formSection.classList.add('d-none');
        }
        
        console.log('All sections hidden');
        
        // Show the selected section (never show file upload)
        if (selectedType) {
            if (selectedType === 'file') {
                // Don't show file upload section
                if (fileSection) {
                    fileSection.style.display = 'none';
                    fileSection.classList.add('d-none');
                }
                console.log('File upload section hidden (not supported)');
            } else if (selectedType === 'link') {
                if (linkSection) {
                    linkSection.style.display = 'block';
                    linkSection.classList.remove('d-none');
                    linkSection.classList.add('d-block');
                    console.log('Showing file link section - display set to block');
                    // Force a reflow to ensure the change takes effect
                    void linkSection.offsetHeight;
                } else {
                    console.error('File link section not found!');
                }
            } else if (selectedType === 'form') {
                if (formSection) {
                    formSection.style.display = 'block';
                    formSection.classList.remove('d-none');
                    formSection.classList.add('d-block');
                    console.log('Showing form section - display set to block');
                    // Force a reflow to ensure the change takes effect
                    void formSection.offsetHeight;
                } else {
                    console.error('Form section not found!');
                }
            }
        } else {
            console.log('No storage type selected, all sections hidden');
        }
        
        // Update button visibility
        if (typeof checkStorageDataAndToggleButton === 'function') {
            checkStorageDataAndToggleButton();
        }
    }

    // File preview function
    function previewFile(input) {
        const previewDiv = document.getElementById('file_preview');
        const previewContent = document.getElementById('file_preview_content');
        const previewName = document.getElementById('file_preview_name');
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const fileExtension = file.name.split('.').pop().toLowerCase();
            const isImage = ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExtension);
            
            previewName.textContent = file.name;
            
            if (isImage) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContent.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" style="max-height: 300px; max-width: 100%;">`;
                    previewDiv.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                previewContent.innerHTML = `<i class="fas fa-file fa-3x text-muted"></i><p class="mb-0 mt-2">${file.name}</p>`;
                previewDiv.style.display = 'block';
            }
        } else {
            previewDiv.style.display = 'none';
        }
    }

    // Function to handle storage type selection
    function handleStorageTypeChange() {
        // Get the currently checked radio
        const selectedType = document.querySelector('input[name="storage_type"]:checked')?.value;
        console.log('Handling storage type change:', selectedType);
        
        // Ensure all other radios are unchecked
        storageTypeInputs.forEach(radio => {
            if (radio.value !== selectedType) {
                radio.checked = false;
            }
        });
        
        // Toggle sections immediately
        toggleSections();
        
        // Update Mark Completed button visibility
        if (typeof checkStorageDataAndToggleButton === 'function') {
            checkStorageDataAndToggleButton();
        }
    }

    // Add event listeners to radio buttons
    storageTypeInputs.forEach(input => {
        // Listen for change event (most reliable)
        input.addEventListener('change', function(e) {
            console.log('Radio change event:', this.value, this.checked);
            if (this.checked) {
                handleStorageTypeChange();
            }
        });
        
        // Also listen for click on the input itself
        input.addEventListener('click', function(e) {
            console.log('Radio input clicked:', this.value);
            this.checked = true;
            // Uncheck others
            storageTypeInputs.forEach(radio => {
                if (radio.id !== this.id) {
                    radio.checked = false;
                }
            });
            // Immediately toggle sections
            toggleSections();
            
            // Update Mark Completed button visibility
            if (typeof checkStorageDataAndToggleButton === 'function') {
                checkStorageDataAndToggleButton();
            }
        });
    });
    
    // Use event delegation for the btn-group container (catches all clicks)
    const btnGroup = document.querySelector('.btn-group[role="group"]');
    if (btnGroup) {
        console.log('Button group found, adding click listener');
        btnGroup.addEventListener('click', function(e) {
            // Check if clicking on a label or the label's children (icon, text)
            const label = e.target.closest('label[for^="storage_"]');
        if (label) {
                e.stopPropagation();
                const inputId = label.getAttribute('for');
                const input = document.getElementById(inputId);
                if (input) {
                    console.log('=== BUTTON GROUP CLICKED ===');
                    console.log('Input ID:', inputId, 'Value:', input.value);
                    
                    // Immediately check the input
                    input.checked = true;
                    // Uncheck others
                    storageTypeInputs.forEach(radio => {
                        if (radio.id !== inputId) {
                            radio.checked = false;
                        }
                    });
                    
                    // Immediately toggle sections
                    console.log('Calling toggleSections directly...');
                    toggleSections();
                    
                    // Update Mark Completed button visibility
                    if (typeof checkStorageDataAndToggleButton === 'function') {
                        checkStorageDataAndToggleButton();
                    }
                }
            }
        });
    } else {
        console.error('Button group not found!');
    }
    
    // Also listen directly on labels as backup
    document.querySelectorAll('label[for^="storage_"]').forEach(label => {
        label.addEventListener('click', function(e) {
            const inputId = this.getAttribute('for');
            const input = document.getElementById(inputId);
            if (input) {
                console.log('=== LABEL DIRECT CLICK ===');
                console.log('Input ID:', inputId, 'Value:', input.value);
                
                // Wait a tiny bit for Bootstrap to handle the click, then force check
                setTimeout(() => {
                    // Force check
                    input.checked = true;
                    storageTypeInputs.forEach(radio => {
                        if (radio.id !== inputId) {
                            radio.checked = false;
                        }
                    });
                    
                    // Immediately toggle sections - call toggleSections directly
                    console.log('Calling toggleSections directly...');
                    toggleSections();
                    
                    // Update Mark Completed button visibility
                    if (typeof checkStorageDataAndToggleButton === 'function') {
                        checkStorageDataAndToggleButton();
                    }
                }, 50);
            }
        });
    });
    
    // Use MutationObserver to watch for radio button state changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'checked') {
                const target = mutation.target;
                if (target.name === 'storage_type' && target.checked) {
                    console.log('MutationObserver detected radio change:', target.value);
                    handleStorageTypeChange();
                    
                    // Update Mark Completed button visibility
                    if (typeof checkStorageDataAndToggleButton === 'function') {
                        checkStorageDataAndToggleButton();
                    }
                }
            }
        });
    });
    
    // Observe all storage type radio buttons
    storageTypeInputs.forEach(input => {
        observer.observe(input, {
            attributes: true,
            attributeFilter: ['checked']
        });
    });
    
    // Ensure storage type section is visible on page load (unless document is completed)
    const isCompleted = {{ ($document && $document->is_completed) ? 'true' : 'false' }};
    const storageTypeSection = document.getElementById('storage_type_section');
    if (storageTypeSection && !isCompleted) {
        storageTypeSection.style.display = 'block';
        console.log('Storage type section made visible on page load');
    }
    
    // Initial toggle on page load - check for existing storage type
    setTimeout(() => {
        console.log('Initial toggle on page load');
        // Check if there's a pre-selected storage type from PHP
        const initialStorageType = @json(old('storage_type', $document->storage_type ?? ''));
        console.log('Initial storage type from PHP:', initialStorageType);
        
        if (initialStorageType) {
            const initialInput = document.querySelector(`input[name="storage_type"][value="${initialStorageType}"]`);
            if (initialInput) {
                initialInput.checked = true;
                // Uncheck others
                storageTypeInputs.forEach(radio => {
                    if (radio.value !== initialStorageType) {
                        radio.checked = false;
                    }
                });
                console.log('Setting initial storage type:', initialStorageType);
            }
        }
        
        // Always call toggleSections to set initial state
    toggleSections();
        
        // Also check if any radio is checked and toggle accordingly
        const checkedRadio = document.querySelector('input[name="storage_type"]:checked');
        if (checkedRadio) {
            console.log('Found checked radio on load:', checkedRadio.value);
            toggleSections();
        }
        
        // Update Mark Completed button visibility on page load
        if (typeof checkStorageDataAndToggleButton === 'function') {
            checkStorageDataAndToggleButton();
        }
    }, 100);

    // Add event listeners for existing form field type changes
    document.querySelectorAll('.form-field-type').forEach(select => {
        select.addEventListener('change', function() {
            updateFieldInput(this.closest('.form-field-row'), this.value);
        });
    });

    // Add event listeners for existing options inputs
    document.querySelectorAll('.form-field-options').forEach(input => {
        input.addEventListener('input', function() {
            const container = this.closest('.form-field-input-container');
            if (container) {
                updateSelectOptions(container, this.value);
            }
        });
    });

    // Add form field
    const addFormFieldBtn = document.getElementById('add_form_field');
    if (addFormFieldBtn) {
        addFormFieldBtn.addEventListener('click', function() {
            addFormField();
        });
    }

    function addFormField(fieldData = null) {
        const container = document.getElementById('form_fields_container');
        const index = container.querySelectorAll('.form-field-row').length;
        
        const fieldRow = document.createElement('div');
        fieldRow.className = 'card mb-2 form-field-row';
        fieldRow.setAttribute('data-field-index', index);
        
        const field = fieldData || {
            label: '',
            type: 'text',
            name: '',
            value: '',
            options: [],
            required: false,
            placeholder: ''
        };
        
        fieldRow.innerHTML = `
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <label class="form-label small">Field Label</label>
                        <input type="text" class="form-control form-field-label" 
                               placeholder="Field Label" 
                               value="${field.label || ''}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Input Type</label>
                        <select class="form-select form-field-type">
                            <option value="text" ${field.type === 'text' ? 'selected' : ''}>Text</option>
                            <option value="number" ${field.type === 'number' ? 'selected' : ''}>Number</option>
                            <option value="date" ${field.type === 'date' ? 'selected' : ''}>Date</option>
                            <option value="textarea" ${field.type === 'textarea' ? 'selected' : ''}>Textarea</option>
                            <option value="select" ${field.type === 'select' ? 'selected' : ''}>Select</option>
                            <option value="checkbox" ${field.type === 'checkbox' ? 'selected' : ''}>Checkbox</option>
                            <option value="radio" ${field.type === 'radio' ? 'selected' : ''}>Radio</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">Field Name</label>
                        <input type="text" class="form-control form-field-name" 
                               placeholder="field_name" 
                               value="${field.name || ''}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small">Value/Options</label>
                        <div class="form-field-input-container">
                            ${getFieldInputHTML(field)}
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small">&nbsp;</label>
                        <div>
                            <button type="button" class="btn btn-sm btn-danger remove-field">
                                <i class="fas fa-times"></i> Remove
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input form-field-required" 
                                   ${field.required ? 'checked' : ''}>
                            <label class="form-check-label small">Required</label>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <input type="text" class="form-control form-control-sm form-field-placeholder" 
                               placeholder="Placeholder text (optional)" 
                               value="${field.placeholder || ''}">
                    </div>
                </div>
            </div>
        `;
        
        container.appendChild(fieldRow);
        
        // Add event listener for type change
        const typeSelect = fieldRow.querySelector('.form-field-type');
        typeSelect.addEventListener('change', function() {
            updateFieldInput(fieldRow, this.value);
        });
    }

    function getFieldInputHTML(field) {
        const type = field.type || 'text';
        const value = field.value || '';
        const options = field.options || [];
        
        if (type === 'textarea') {
            return `<textarea class="form-control form-field-value" placeholder="Field Value">${value}</textarea>`;
        } else if (type === 'select' || type === 'radio') {
            const optionsStr = options.join(', ');
            return `
                <input type="text" class="form-control form-field-options mb-1" 
                       placeholder="Option1, Option2, Option3" 
                       value="${optionsStr}">
                <select class="form-control form-field-value" style="display: ${type === 'select' ? 'block' : 'none'};">
                    ${options.map(opt => `<option value="${opt}" ${value === opt ? 'selected' : ''}>${opt}</option>`).join('')}
                </select>
            `;
        } else if (type === 'checkbox') {
            return `
                <div class="form-check">
                    <input type="checkbox" class="form-check-input form-field-value" 
                           ${value ? 'checked' : ''}>
                    <label class="form-check-label">Checked</label>
                </div>
            `;
        } else {
            return `<input type="${type}" class="form-control form-field-value" 
                           placeholder="Field Value" 
                           value="${value}">`;
        }
    }

    function updateFieldInput(fieldRow, type) {
        const container = fieldRow.querySelector('.form-field-input-container');
        const currentValue = fieldRow.querySelector('.form-field-value')?.value || '';
        
        if (type === 'textarea') {
            container.innerHTML = `<textarea class="form-control form-field-value" placeholder="Field Value">${currentValue}</textarea>`;
        } else if (type === 'select' || type === 'radio') {
            container.innerHTML = `
                <input type="text" class="form-control form-field-options mb-1" 
                       placeholder="Option1, Option2, Option3">
                <select class="form-control form-field-value mt-1" style="display: ${type === 'select' ? 'block' : 'none'};">
                </select>
            `;
            
            // Add event listener for options input
            const optionsInput = container.querySelector('.form-field-options');
            if (optionsInput) {
                optionsInput.addEventListener('input', function() {
                    updateSelectOptions(container, this.value);
                });
            }
        } else if (type === 'checkbox') {
            container.innerHTML = `
                <div class="form-check">
                    <input type="checkbox" class="form-check-input form-field-value" ${currentValue ? 'checked' : ''}>
                    <label class="form-check-label">Checked</label>
                </div>
            `;
        } else {
            container.innerHTML = `<input type="${type}" class="form-control form-field-value" 
                           placeholder="Field Value" 
                           value="${currentValue}">`;
        }
    }

    function updateSelectOptions(container, optionsStr) {
        const select = container.querySelector('.form-field-value');
        const options = optionsStr.split(',').map(opt => opt.trim()).filter(opt => opt);
        select.innerHTML = options.map(opt => `<option value="${opt}">${opt}</option>`).join('');
    }

    // Remove form field
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-field')) {
            e.target.closest('.form-field-row').remove();
        }
    });

    // Add/Remove file inputs
    document.getElementById('add_file_input')?.addEventListener('click', function() {
        const container = document.getElementById('file_inputs_container');
        const newInput = document.createElement('div');
        newInput.className = 'file-input-group mb-2';
        newInput.innerHTML = `
            <div class="input-group">
                <input type="file" class="form-control" name="files[]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                <button type="button" class="btn btn-outline-danger remove-file-input">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        container.appendChild(newInput);
        updateRemoveButtons();
    });

    // Add/Remove link inputs
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
        checkStorageDataAndToggleButton();
    });
    
    // Function to check if storage data exists and hide/show Mark Completed button
    function checkStorageDataAndToggleButton() {
        const markCompletedBtn = document.getElementById('markCompletedBtn');
        const markNewCompletedBtn = document.getElementById('markNewCompletedBtn');
        const buttonToHide = markCompletedBtn || markNewCompletedBtn;
        
        console.log('=== CHECK STORAGE DATA AND TOGGLE BUTTON ===');
        console.log('Mark Completed Btn:', !!markCompletedBtn);
        console.log('Mark New Completed Btn:', !!markNewCompletedBtn);
        console.log('Button to hide:', !!buttonToHide);
        
        if (!buttonToHide) {
            console.log('No button found to hide/show');
            return;
        }
        
        // Check if storage type is selected
        const selectedStorageType = document.querySelector('input[name="storage_type"]:checked')?.value;
        console.log('Selected storage type:', selectedStorageType);
        
        // If a storage type is selected (Add File Link or Custom Form), hide the Mark Completed button
        if (selectedStorageType) {
            // Storage type selected means user is adding details, so hide Mark Completed button
            console.log('Storage type selected, hiding Mark Completed button');
            buttonToHide.style.display = 'none';
            return;
        }
        
        // No storage type selected, show the Mark Completed button
        console.log('No storage type selected, showing Mark Completed button');
        buttonToHide.style.display = 'inline-block';
    }
    
    // Check on page load
    setTimeout(checkStorageDataAndToggleButton, 200);
    
    // Monitor file link inputs for changes
    document.addEventListener('input', function(e) {
        if (e.target.matches('input[name="file_links[]"]')) {
            checkStorageDataAndToggleButton();
        }
    });
    
    // Monitor form field changes
    const formBuilder = document.getElementById('form_builder_mode');
    if (formBuilder) {
        const observer = new MutationObserver(function() {
            checkStorageDataAndToggleButton();
        });
        observer.observe(formBuilder, { childList: true, subtree: true });
    }
    
    // Check when form view mode is shown
    const switchToViewBtn = document.getElementById('switch_to_view_mode');
    if (switchToViewBtn) {
        switchToViewBtn.addEventListener('click', function() {
            setTimeout(checkStorageDataAndToggleButton, 100);
        });
    }

    // Remove file/link inputs
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-file-input')) {
            const inputGroup = e.target.closest('.file-input-group');
            if (document.querySelectorAll('.file-input-group').length > 1) {
                inputGroup.remove();
            }
            updateRemoveButtons();
        }
        if (e.target.closest('.remove-link-input')) {
            const inputGroup = e.target.closest('.link-input-group');
            if (document.querySelectorAll('.link-input-group').length > 1) {
                inputGroup.remove();
            }
            updateRemoveButtons();
            checkStorageDataAndToggleButton();
        }
    });

    // Update remove buttons visibility
    function updateRemoveButtons() {
        const fileGroups = document.querySelectorAll('.file-input-group');
        const linkGroups = document.querySelectorAll('.link-input-group');
        
        fileGroups.forEach((group, index) => {
            const removeBtn = group.querySelector('.remove-file-input');
            if (removeBtn) {
                removeBtn.style.display = fileGroups.length > 1 ? 'block' : 'none';
            }
        });
        
        linkGroups.forEach((group, index) => {
            const removeBtn = group.querySelector('.remove-link-input');
            if (removeBtn) {
                removeBtn.style.display = linkGroups.length > 1 ? 'block' : 'none';
            }
        });
    }

    // Initialize remove buttons on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateRemoveButtons();
    });

    // Switch to view mode (show rendered form inputs)
    function switchToViewMode(fields) {
        const builderMode = document.getElementById('form_builder_mode');
        const viewMode = document.getElementById('form_view_mode');
        const container = document.getElementById('form_inputs_container');
        
        // Hide builder, show view
        builderMode.style.display = 'none';
        viewMode.style.display = 'block';
        const switchToViewBtn = document.getElementById('switch_to_view_mode');
        const switchToEditBtn = document.getElementById('switch_to_edit_mode');
        if (switchToViewBtn) switchToViewBtn.style.display = 'none';
        if (switchToEditBtn) switchToEditBtn.style.display = 'block';
        
        // Clear and render form inputs
        container.innerHTML = '';
        
        fields.forEach((field, index) => {
            const fieldHtml = renderFormInput(field, index);
            container.appendChild(fieldHtml);
        });
    }

    // Switch to edit mode (show form builder)
    function switchToEditMode() {
        const builderMode = document.getElementById('form_builder_mode');
        const viewMode = document.getElementById('form_view_mode');
        const container = document.getElementById('form_fields_container');
        
        // Hide view, show builder
        viewMode.style.display = 'none';
        builderMode.style.display = 'block';
        const switchToViewBtn = document.getElementById('switch_to_view_mode');
        const switchToEditBtn = document.getElementById('switch_to_edit_mode');
        if (switchToEditBtn) switchToEditBtn.style.display = 'none';
        if (switchToViewBtn) switchToViewBtn.style.display = 'block';
        
        // If we have a template loaded, populate the builder
        if (window.currentTemplate && window.currentTemplate.form_fields) {
            container.innerHTML = '';
            window.currentTemplate.form_fields.forEach(field => {
                addFormField(field);
            });
        }
    }

    // Render a single form input based on field definition
    function renderFormInput(field, index) {
        const div = document.createElement('div');
        div.className = 'mb-3';
        
        const label = document.createElement('label');
        label.className = 'form-label';
        label.textContent = field.label || field.name;
        if (field.required) {
            label.innerHTML += ' <span class="text-danger">*</span>';
        }
        label.setAttribute('for', `form_input_${index}`);
        
        div.appendChild(label);
        
        let input;
        const fieldName = field.name || `field_${index}`;
        
        if (field.type === 'textarea') {
            input = document.createElement('textarea');
            input.className = 'form-control';
            input.name = `form_data[${fieldName}]`;
            input.id = `form_input_${index}`;
            input.placeholder = field.placeholder || '';
            if (field.required) input.required = true;
            input.value = field.value || '';
        } else if (field.type === 'select') {
            input = document.createElement('select');
            input.className = 'form-select';
            input.name = `form_data[${fieldName}]`;
            input.id = `form_input_${index}`;
            if (field.required) input.required = true;
            
            // Add empty option
            const emptyOption = document.createElement('option');
            emptyOption.value = '';
            emptyOption.textContent = '-- Select --';
            input.appendChild(emptyOption);
            
            // Add options
            if (field.options && Array.isArray(field.options)) {
                field.options.forEach(option => {
                    const optionEl = document.createElement('option');
                    optionEl.value = option;
                    optionEl.textContent = option;
                    if (field.value === option) optionEl.selected = true;
                    input.appendChild(optionEl);
                });
            }
        } else if (field.type === 'checkbox') {
            const checkDiv = document.createElement('div');
            checkDiv.className = 'form-check';
            
            input = document.createElement('input');
            input.type = 'checkbox';
            input.className = 'form-check-input';
            input.name = `form_data[${fieldName}]`;
            input.id = `form_input_${index}`;
            input.value = '1';
            if (field.value) input.checked = true;
            
            const checkLabel = document.createElement('label');
            checkLabel.className = 'form-check-label';
            checkLabel.setAttribute('for', `form_input_${index}`);
            checkLabel.textContent = field.label || field.name;
            
            checkDiv.appendChild(input);
            checkDiv.appendChild(checkLabel);
            div.appendChild(checkDiv);
            return div;
        } else if (field.type === 'radio') {
            // Radio buttons need special handling
            if (field.options && Array.isArray(field.options)) {
                field.options.forEach((option, optIndex) => {
                    const radioDiv = document.createElement('div');
                    radioDiv.className = 'form-check';
                    
                    const radioInput = document.createElement('input');
                    radioInput.type = 'radio';
                    radioInput.className = 'form-check-input';
                    radioInput.name = `form_data[${fieldName}]`;
                    radioInput.id = `form_input_${index}_${optIndex}`;
                    radioInput.value = option;
                    if (field.value === option) radioInput.checked = true;
                    
                    const radioLabel = document.createElement('label');
                    radioLabel.className = 'form-check-label';
                    radioLabel.setAttribute('for', `form_input_${index}_${optIndex}`);
                    radioLabel.textContent = option;
                    
                    radioDiv.appendChild(radioInput);
                    radioDiv.appendChild(radioLabel);
                    div.appendChild(radioDiv);
                });
            }
            return div;
        } else {
            input = document.createElement('input');
            input.type = field.type || 'text';
            input.className = 'form-control';
            input.name = `form_data[${fieldName}]`;
            input.id = `form_input_${index}`;
            input.placeholder = field.placeholder || '';
            if (field.required) input.required = true;
            input.value = field.value || '';
        }
        
        if (input) {
            div.appendChild(input);
        }
        
        return div;
    }

    // Switch mode buttons
    const switchToViewBtn = document.getElementById('switch_to_view_mode');
    if (switchToViewBtn) {
        switchToViewBtn.addEventListener('click', function() {
            const fields = collectFormFields();
            if (fields.length === 0) {
                alert('Please add at least one field before viewing the form.');
                return;
            }
            switchToViewMode(fields);
        });
    }

    const switchToEditBtn = document.getElementById('switch_to_edit_mode');
    if (switchToEditBtn) {
        switchToEditBtn.addEventListener('click', function() {
            switchToEditMode();
        });
    }

    // Load template
    const loadTemplateBtn = document.getElementById('load_template_btn');
    if (loadTemplateBtn) {
        loadTemplateBtn.addEventListener('click', function() {
            const templateId = document.getElementById('template_select').value;
            if (!templateId) {
                alert('Please select a template first.');
                return;
            }
            
            fetch(`/document-templates/${templateId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Store template data
                        window.currentTemplate = data.template;
                        
                        // Switch to view mode and render the form
                        switchToViewMode(data.template.form_fields);
                        
                        alert('Template loaded successfully! You can now fill in the form details.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load template.');
                });
        });
    }

    // Create new template
    const createNewTemplateBtn = document.getElementById('create_new_template_btn');
    if (createNewTemplateBtn) {
        createNewTemplateBtn.addEventListener('click', function() {
            // Clear any existing template
            window.currentTemplate = null;
            
            // Clear template selection
            const templateSelect = document.getElementById('template_select');
            if (templateSelect) {
                templateSelect.value = '';
            }
            
            // Switch to edit mode
            switchToEditMode();
            
            // Clear form fields container
            const container = document.getElementById('form_fields_container');
            if (container) {
                container.innerHTML = '';
            }
            
            // Add one empty field to start with
            addFormField();
            
            alert('Ready to create a new template! Add your form fields and click "Save as Template" when done.');
        });
    }

    // Save template modal
    const saveTemplateBtn = document.getElementById('save_template_btn');
    if (saveTemplateBtn) {
        saveTemplateBtn.addEventListener('click', function() {
            const fields = collectFormFields();
            if (fields.length === 0) {
                alert('Please add at least one field before saving as template.');
                return;
            }
            
            // Store fields in modal for saving
            document.getElementById('template_fields_data').value = JSON.stringify(fields);
        });
    }

    // Save template form submission
    const saveTemplateForm = document.getElementById('saveTemplateForm');
    if (saveTemplateForm) {
        saveTemplateForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const name = document.getElementById('template_name').value;
            const documentType = '{{ $documentType }}';
            const fields = JSON.parse(document.getElementById('template_fields_data').value);
            
            fetch('/document-templates', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    name: name,
                    document_type: documentType,
                    form_fields: fields
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Template saved successfully!');
                    location.reload(); // Reload to show new template in dropdown
                } else {
                    alert('Failed to save template.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to save template.');
            });
        });
    }

    function collectFormFields() {
        const fields = [];
        document.querySelectorAll('.form-field-row').forEach(row => {
            const label = row.querySelector('.form-field-label')?.value || '';
            const type = row.querySelector('.form-field-type')?.value || 'text';
            const name = row.querySelector('.form-field-name')?.value || '';
            const required = row.querySelector('.form-field-required')?.checked || false;
            const placeholder = row.querySelector('.form-field-placeholder')?.value || '';
            
            let value = '';
            let options = [];
            
            if (type === 'select' || type === 'radio') {
                const optionsInput = row.querySelector('.form-field-options');
                if (optionsInput) {
                    options = optionsInput.value.split(',').map(opt => opt.trim()).filter(opt => opt);
                }
                const select = row.querySelector('.form-field-value');
                if (select) value = select.value;
            } else if (type === 'checkbox') {
                const checkbox = row.querySelector('.form-field-value');
                if (checkbox) value = checkbox.checked;
            } else {
                const input = row.querySelector('.form-field-value');
                if (input) value = input.value;
            }
            
            if (label && name) {
                fields.push({
                    label: label,
                    type: type,
                    name: name,
                    value: value,
                    options: options,
                    required: required,
                    placeholder: placeholder
                });
            }
        });
        
        return fields;
    }

    // Form submission - convert form fields to JSON
    document.getElementById('documentForm').addEventListener('submit', function(e) {
        const storageType = document.querySelector('input[name="storage_type"]:checked')?.value;
        
        // Validate file upload if storage type is file
        if (storageType === 'file') {
            const fileInput = document.getElementById('file');
            if (!fileInput.files || fileInput.files.length === 0) {
                e.preventDefault();
                alert('Please select a file to upload.');
                return false;
            }
        }
        
        if (storageType === 'form') {
            // Check if we're in view mode (rendered form) or edit mode (builder)
            const viewMode = document.getElementById('form_view_mode');
            const isViewMode = viewMode.style.display !== 'none';
            
            let formData = {};
            let fields = [];
            
            if (isViewMode) {
                // Collect data from rendered form inputs
                const inputs = viewMode.querySelectorAll('input, select, textarea');
                inputs.forEach(input => {
                    if (input.type === 'checkbox') {
                        if (input.checked) {
                            const name = input.name.match(/\[([^\]]+)\]/);
                            if (name) {
                                formData[name[1]] = input.value || true;
                            }
                        }
                    } else if (input.type === 'radio') {
                        if (input.checked) {
                            const name = input.name.match(/\[([^\]]+)\]/);
                            if (name) {
                                formData[name[1]] = input.value;
                            }
                        }
                    } else {
                        const name = input.name.match(/\[([^\]]+)\]/);
                        if (name && input.value) {
                            formData[name[1]] = input.value;
                        }
                    }
                });
                
                // Use stored template fields if available
                if (window.currentTemplate && window.currentTemplate.form_fields) {
                    fields = window.currentTemplate.form_fields;
                }
            } else {
                // Collect from builder
                fields = collectFormFields();
                fields.forEach(field => {
                    formData[field.name] = field.value;
                });
            }

            // Create hidden input for form_data
            let hiddenInput = document.querySelector('input[name="form_data"]');
            if (!hiddenInput) {
                hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'form_data';
                this.appendChild(hiddenInput);
            }
            hiddenInput.value = JSON.stringify(formData);
            
            // Also store form_fields structure
            let hiddenFieldsInput = document.querySelector('input[name="form_fields"]');
            if (!hiddenFieldsInput) {
                hiddenFieldsInput = document.createElement('input');
                hiddenFieldsInput.type = 'hidden';
                hiddenFieldsInput.name = 'form_fields';
                this.appendChild(hiddenFieldsInput);
            }
            hiddenFieldsInput.value = JSON.stringify(fields);
        }
    });
});

// Mark document as completed
function markDocumentCompleted(documentId) {
    const btn = document.querySelector(`button[onclick*="markDocumentCompleted(${documentId})"]`) || 
                document.querySelector(`button[onclick*="markDocumentIncomplete(${documentId})"]`);
    
    fetch(`/vehicles/{{ $vehicle->id }}/documents/${documentId}/mark-completed`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update button to show completed state
            if (btn) {
                btn.className = 'btn btn-success me-md-2';
                btn.innerHTML = '<i class="fas fa-check-circle me-1"></i>Marked as Completed';
                btn.setAttribute('onclick', `markDocumentIncomplete(${documentId})`);
            }
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Document Marked as Completed',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                alert('Document marked as completed!');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to mark document as completed.');
    });
}

// Mark new document as completed (when no document exists yet)
function markNewDocumentCompleted() {
    const btn = document.querySelector('button[onclick="markNewDocumentCompleted()"]');
    const documentType = '{{ $documentType }}';
    const processType = '{{ $processType ?? "ACQUISITION" }}';
    
    // Get CSRF token
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfInput = document.querySelector('input[name="_token"]');
    const csrfToken = (csrfMeta && csrfMeta.content) || 
                     (csrfInput && csrfInput.value) ||
                     '{{ csrf_token() }}';
    
    if (!csrfToken) {
        alert('CSRF token not found. Please refresh the page and try again.');
        return;
    }
    
    console.log('Marking document as completed:', { documentType, processType, vehicleId: {{ $vehicle->id }} });
    
    // Use route helper to generate correct URL
    const vehicleId = {{ $vehicle->id }};
    const url = '{{ route("vehicles.documents.mark-new-completed", $vehicle) }}';
    console.log('Request URL:', url);
    console.log('Vehicle ID:', vehicleId);
    console.log('CSRF Token:', csrfToken);
    console.log('Request data:', { document_type: documentType, process_type: processType });
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            document_type: documentType,
            process_type: processType
        })
    })
    .then(async response => {
        console.log('Response status:', response.status);
        console.log('Response URL:', response.url);
        console.log('Request URL was:', url);
        console.log('Response headers:', Object.fromEntries(response.headers.entries()));
        
        // If 404, provide more specific error
        if (response.status === 404) {
            const text = await response.text();
            console.error('404 Error - Response text:', text.substring(0, 500));
            throw new Error(`Route not found (404). URL: ${url}. Please check if the route is registered correctly.`);
        }
        
        const contentType = response.headers.get('content-type') || '';
        let data;
        
        if (contentType.includes('application/json')) {
            try {
                data = await response.json();
            } catch (e) {
                console.error('Failed to parse JSON:', e);
                const text = await response.text();
                console.error('Response text:', text.substring(0, 500));
                throw new Error('Invalid JSON response from server');
            }
        } else {
            // If not JSON, get text and show helpful error
            const text = await response.text();
            console.error('Non-JSON response received (Status: ' + response.status + '):', text.substring(0, 500));
            
            // Extract error message from HTML if possible
            let errorMessage = `Server returned an error (Status: ${response.status}). Please check the browser console.`;
            if (text.includes('CSRF token') || text.includes('419')) {
                errorMessage = 'CSRF token mismatch. Please refresh the page and try again.';
            } else if (text.includes('404') || text.includes('Not Found')) {
                errorMessage = `Route not found (404). URL: ${url}. Please verify the route exists.`;
            } else if (text.includes('500') || text.includes('Internal Server Error')) {
                errorMessage = 'Server error occurred. Please try again later.';
            } else if (text.includes('MethodNotAllowed')) {
                errorMessage = 'Invalid request method. Please refresh and try again.';
            }
            
            throw new Error(errorMessage);
        }
        
        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Failed to mark document as completed');
        }
        
        return data;
    })
    .then(data => {
        if (data && data.success) {
            // Hide storage type section
            const storageTypeSection = document.getElementById('storage_type_section');
            if (storageTypeSection) {
                storageTypeSection.style.display = 'none';
            }
            
            // Hide all storage type sections (file upload, link, form)
            document.querySelectorAll('.storage-section').forEach(section => {
                section.style.display = 'none';
            });
            
            // Update button to show completed state
            if (btn) {
                btn.className = 'btn btn-success me-md-2';
                btn.innerHTML = '<i class="fas fa-check-circle me-1"></i>Marked as Completed';
                btn.setAttribute('onclick', `markDocumentIncomplete(${data.document_id})`);
            }
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Document Marked as Completed',
                    text: 'The document has been created and marked as completed.',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                alert('Document marked as completed!');
            }
        } else {
            throw new Error(data.message || 'Failed to mark document as completed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const errorMessage = error.message || 'Failed to mark document as completed.';
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage
            });
        } else {
            alert(errorMessage);
        }
    });
}

// Mark document as incomplete
function markDocumentIncomplete(documentId) {
    const btn = document.querySelector(`button[onclick*="markDocumentCompleted(${documentId})"]`) || 
                document.querySelector(`button[onclick*="markDocumentIncomplete(${documentId})"]`);
    
    fetch(`/vehicles/{{ $vehicle->id }}/documents/${documentId}/mark-incomplete`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show storage type section
            const storageTypeSection = document.getElementById('storage_type_section');
            if (storageTypeSection) {
                storageTypeSection.style.display = 'block';
            }
            
            // Update button to show incomplete state
            if (btn) {
                btn.className = 'btn btn-outline-success me-md-2';
                btn.innerHTML = '<i class="fas fa-check me-1"></i>Mark Completed';
                btn.setAttribute('onclick', `markDocumentCompleted(${documentId})`);
            }
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'info',
                    title: 'Document Marked as Incomplete',
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                alert('Document marked as incomplete!');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to mark document as incomplete.');
    });
}
</script>

<!-- Save Template Modal -->
<div class="modal fade" id="saveTemplateModal" tabindex="-1" aria-labelledby="saveTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="saveTemplateModalLabel">Save Form as Template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="saveTemplateForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="template_name" class="form-label">Template Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="template_name" name="template_name" required placeholder="e.g., Standard OR Form">
                    </div>
                    <input type="hidden" id="template_fields_data" name="template_fields_data">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Save Template
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

