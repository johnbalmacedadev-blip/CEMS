@extends('layouts.app')

@section('title', 'View Document - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <main class="col-12 px-md-4 main-content" id="mainContent">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-eye me-2"></i>
                    View Document: {{ str_replace('_', ' ', strtoupper($document->document_type)) }}
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('vehicles.documents.edit', [$vehicle, $document]) }}" class="btn btn-warning me-2">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                    <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Vehicle
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-file-alt me-2"></i>Document Information
                            </h5>
                        </div>
                        <div class="card-body">
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <!-- Document Basic Information -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <table class="table table-bordered">
                                        <tbody>
                                            <tr>
                                                <th style="width: 40%; background-color: #f8f9fa;">Document Type</th>
                                                <td>{{ str_replace('_', ' ', strtoupper($document->document_type)) }}</td>
                                            </tr>
                                            <tr>
                                                <th style="background-color: #f8f9fa;">Process Type</th>
                                                <td>{{ $document->process_type ?? 'ACQUISITION' }}</td>
                                            </tr>
                                            <tr>
                                                <th style="background-color: #f8f9fa;">Status</th>
                                                <td>
                                                    @if($document->is_completed)
                                                        <span class="badge bg-success">Completed</span>
                                                    @else
                                                        <span class="badge bg-warning">Incomplete</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="background-color: #f8f9fa;">Storage Type</th>
                                                <td>
                                                    @if($document->storage_type === 'link')
                                                        <span class="badge bg-info"><i class="fas fa-link me-1"></i>File Link</span>
                                                    @elseif($document->storage_type === 'form')
                                                        <span class="badge bg-primary"><i class="fas fa-edit me-1"></i>Custom Form</span>
                                                    @elseif($document->storage_type === 'file')
                                                        <span class="badge bg-success"><i class="fas fa-file me-1"></i>File Upload</span>
                                                    @else
                                                        <span class="text-muted">Not Set</span>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="background-color: #f8f9fa;">Check Date</th>
                                                <td>{{ $document->check_date ? $document->check_date->format('M d, Y') : 'N/A' }}</td>
                                            </tr>
                                            <tr>
                                                <th style="background-color: #f8f9fa;">Checked By</th>
                                                <td>{{ $document->checked_by ?? 'N/A' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- File Links Section -->
                            @if($document->storage_type === 'link' && $document->files && $document->files->where('type', 'link')->count() > 0)
                                <div class="mb-4">
                                    <h5 class="mb-3">
                                        <i class="fas fa-link me-2"></i>File Links
                                    </h5>
                                    <div class="list-group">
                                        @foreach($document->files->where('type', 'link') as $index => $file)
                                            <div class="list-group-item">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong>Link {{ $index + 1 }}:</strong>
                                                        <a href="{{ $file->file_link }}" target="_blank" class="ms-2">
                                                            {{ $file->file_link }}
                                                            <i class="fas fa-external-link-alt ms-1"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- File Upload Section -->
                            @if($document->storage_type === 'file' && $document->files && $document->files->where('type', 'file')->count() > 0)
                                <div class="mb-4">
                                    <h5 class="mb-3">
                                        <i class="fas fa-file me-2"></i>Uploaded Files
                                    </h5>
                                    <div class="list-group">
                                        @foreach($document->files->where('type', 'file') as $index => $file)
                                            <div class="list-group-item">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="fas fa-file-pdf me-2 text-danger"></i>
                                                        <strong>{{ $file->file_name ?? 'File ' . ($index + 1) }}</strong>
                                                    </div>
                                                    <a href="{{ route('vehicles.documents.files.download', [$vehicle, $file->id]) }}" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       target="_blank">
                                                        <i class="fas fa-download me-1"></i>Download
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Form Data Section -->
                            @if($document->storage_type === 'form' && $document->form_data)
                                <div class="mb-4">
                                    <h5 class="mb-3">
                                        <i class="fas fa-edit me-2"></i>Form Data
                                    </h5>
                                    @php
                                        $formData = $document->form_data;
                                        // Remove internal structure data
                                        unset($formData['_form_structure']);
                                    @endphp
                                    @if(!empty($formData))
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 40%;">Field</th>
                                                        <th>Value</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($formData as $key => $value)
                                                        <tr>
                                                            <td class="fw-bold" style="background-color: #f8f9fa;">
                                                                {{ ucwords(str_replace('_', ' ', $key)) }}
                                                            </td>
                                                            <td>
                                                                @if(is_array($value))
                                                                    {{ json_encode($value) }}
                                                                @elseif(is_bool($value))
                                                                    {{ $value ? 'Yes' : 'No' }}
                                                                @else
                                                                    {{ $value ?? 'N/A' }}
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>No form data available.
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Notes Section -->
                            @if($document->notes)
                                <div class="mb-4">
                                    <h5 class="mb-3">
                                        <i class="fas fa-sticky-note me-2"></i>Notes
                                    </h5>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <p class="mb-0">{{ $document->notes }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- No Data Message -->
                            @if(!$document->storage_type || 
                                ($document->storage_type === 'link' && (!$document->files || $document->files->where('type', 'link')->count() == 0)) ||
                                ($document->storage_type === 'form' && (!$document->form_data || empty($document->form_data))) ||
                                ($document->storage_type === 'file' && (!$document->files || $document->files->where('type', 'file')->count() == 0)))
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    No document data has been added yet. Click "Edit" to add document details.
                                </div>
                            @endif

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                                <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>Back to Vehicle
                                </a>
                                <div>
                                    <a href="{{ route('vehicles.documents.edit', [$vehicle, $document]) }}" class="btn btn-warning">
                                        <i class="fas fa-edit me-1"></i>Edit Document
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
@endsection
