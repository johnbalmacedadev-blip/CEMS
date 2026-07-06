@extends('layouts.app')

@section('title', 'Vehicle Details - Car Empire Management System')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
<style>
    .ts-dropdown { z-index: 1065 !important; }
    .image-card {
        transition: transform 0.2s ease-in-out;
        cursor: pointer;
        position: relative;
    }
    .image-card:hover {
        transform: translateY(-5px);
    }
    .image-card.border-primary {
        border: 2px solid #007bff !important;
    }
    .image-card .position-absolute .btn-group-vertical {
        opacity: 0;
        transition: opacity 0.2s ease-in-out;
    }
    .image-card:hover .position-absolute .btn-group-vertical {
        opacity: 1;
    }
    .image-card img {
        filter: brightness(0.9);
        transition: filter 0.2s ease-in-out;
    }
    .image-card:hover img {
        filter: brightness(0.7);
    }
    
    /* Two Column Layout */
    .col-lg-4 {
        position: sticky;
        top: 20px;
        height: fit-content;
    }
    
    /* Vehicle Stats Card Styling */
    .card .border-end:last-child {
        border-end: none !important;
    }
    
    
    /* Responsive adjustments */
    @media (max-width: 991px) {
        .col-lg-4 {
            position: static;
            margin-top: 2rem;
        }
    }
    
    @media (max-width: 768px) {
        .card .border-end {
            border-end: none !important;
            border-bottom: 1px solid #dee2e6 !important;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
        }
    }
    
    .custom-section .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .card-actions {
        display: flex;
        gap: 0.5rem;
    }
    
    .field-row {
        background-color: #f8f9fa;
    }
    
    .field-row:hover {
        background-color: #e9ecef;
    }
    
    .field-actions {
        margin-top: 0.5rem;
        display: flex;
        gap: 0.5rem;
    }
    
    .field-actions .btn {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    /* Primary Photo Square Holder */
    .primary-photo-holder {
        aspect-ratio: 1;
        border: 2px solid #dee2e6;
        cursor: pointer;
        transition: all 0.3s ease;
        border-radius: 8px;
        overflow: hidden;
    }
    
    .primary-photo-holder:hover {
        border-color: #007bff;
        transform: scale(1.02);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .primary-photo-holder img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>
@endsection

@section('content')
<style>
    /* Fix gap and ensure full width */
    .container-fluid {
        padding-left: 0 !important;
        padding-right: 0 !important;
        margin-left: 0 !important;
        margin-right: 0 !important;
        max-width: 100% !important;
        width: 100% !important;
    }
    
    .row {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
    
    /* Main content - full width */
    #mainContent {
        width: 100% !important;
        max-width: 100% !important;
        margin-left: 0 !important;
        transition: margin-left 0.3s ease-in-out;
    }
    
    /* When sidebar is open, add margin to main content */
    #sidebar.show ~ #mainContent,
    #sidebar:not(.collapse):not(.show) ~ #mainContent {
        margin-left: 250px !important;
        width: calc(100% - 250px) !important;
    }
    
    /* Ensure row doesn't add unwanted margins */
    .row {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
    
    .row > * {
        padding-left: 0;
        padding-right: 0;
    }
</style>
<div class="container-fluid" style="padding-left: 0; padding-right: 0;">
    <div class="row" style="margin-left: 0; margin-right: 0;">
        <!-- Main content -->
        <main class="col-12 ms-sm-auto px-md-4" id="mainContent">
            <!-- Status Banner with Image (show FORFEITED in red when vehicle has forfeit details) -->
            @php $showAsForfeited = $vehicle->status == 'Forfeited' || $vehicle->forfeitDetails->count() > 0; @endphp
            <div class="alert alert-{{ 
                $showAsForfeited ? 'danger' : 
                ($vehicle->status == 'Available' ? 'success' : 
                ($vehicle->status == 'Under Maintenance' ? 'warning' : 
                ($vehicle->status == 'Reserved' ? 'info' : 'primary'))) 
            }} d-flex align-items-center mb-3 mt-4" role="alert" style="margin-top: 2rem !important;">
                <!-- Round Image on Left -->
                <div class="me-3" style="flex-shrink: 0;">
                    <a href="{{ route('vehicles.images.index', $vehicle) }}" class="text-decoration-none">
                        <div style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            @if($vehicle->primaryImage)
                                <img src="{{ $vehicle->primaryImage->thumbnail_url }}" 
                                     alt="Primary Vehicle Image" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <div class="d-flex align-items-center justify-content-center w-100 h-100 bg-light" style="background-color: #f8f9fa;">
                                    <i class="fas fa-camera text-muted"></i>
                                </div>
                            @endif
                        </div>
                    </a>
                </div>
                <!-- Status Text -->
                <div class="d-flex align-items-center flex-grow-1">
                    <i class="fas fa-{{ $showAsForfeited ? 'times-circle' : ($vehicle->status == 'Available' ? 'check-circle' : ($vehicle->status == 'Under Maintenance' ? 'tools' : ($vehicle->status == 'Reserved' ? 'clock' : 'check-double'))) }} me-2"></i>
                    <strong>Current Status: @if($showAsForfeited)<span class="text-danger fw-bold">Forfeited</span>@else{{ $vehicle->status }}@endif</strong>
                    @if($vehicle->statusDetail)
                        <span class="ms-3">
                            @if($vehicle->statusDetail->showroom)
                                <i class="fas fa-building me-1"></i>{{ $vehicle->statusDetail->showroom }}
                            @endif
                            @if($vehicle->statusDetail->sale_date)
                                <i class="fas fa-calendar me-1 ms-2"></i>{{ $vehicle->statusDetail->formatted_sale_date }}
                            @endif
                        </span>
                    @endif
                </div>
            </div>

            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Acquisition</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i>Back to Unit Report
                    </a>
                    <a href="{{ route('vehicles.export', ['vehicle' => $vehicle, 'format' => 'pdf']) }}" class="btn btn-outline-primary me-2" target="_blank" rel="noopener">
                        <i class="fas fa-file-pdf me-1"></i>Export PDF
                    </a>
                    <a href="{{ route('vehicles.export', ['vehicle' => $vehicle, 'format' => 'csv']) }}" class="btn btn-outline-success me-2">
                        <i class="fas fa-file-excel me-1"></i>Export Excel (CSV)
                    </a>
                    @canPage('vehicles', 'update')
                    @if($vehicle->isArchiveable())
                    <form action="{{ route('vehicles.archive', $vehicle) }}" method="POST" class="d-inline me-2" id="archiveVehicleForm">
                        @csrf
                        <button type="button" class="btn btn-outline-secondary" onclick="archiveVehicle()">
                            <i class="fas fa-archive me-1"></i>Archive This Unit
                        </button>
                    </form>
                    @endif
                    @endcanPage
                    @canPage('vehicles', 'delete')
                    @if($vehicle->status !== 'Reserved' && $vehicle->status !== 'Released')
                    <button type="button" class="btn btn-outline-danger" onclick="deleteVehicle()">
                        <i class="fas fa-trash me-1"></i>Delete Vehicle
                    </button>
                    @endif
                    @endcanPage
                </div>
            </div>

            <!-- Posted price -->
            <div class="mb-4">
                <span class="fw-bold">POSTED PRICE :</span>
                @if($vehicle->posted_price != null)
                    <span class="ms-1">₱{{ number_format($vehicle->posted_price, 2) }}</span>
                    @canPage('vehicles', 'update')
                    <a href="#" class="ms-2" onclick="openPostedPriceModal({{ $vehicle->posted_price }}); return false;">edit</a>
                    @endcanPage
                @else
                    <span class="ms-1">—</span>
                    @canPage('vehicles', 'update')
                    <button type="button" class="btn btn-sm btn-primary ms-2" onclick="openPostedPriceModal()">Add Value</button>
                    @endcanPage
                @endif
            </div>

                    <!-- Vehicle Details Accordion (Car Details, Documents, Expenses, Reservation, Released, Transfer, Follow Up Docs, Buffing, Ads/Boosting, Vlog, Agent, Profit/Loss) -->
                    <hr class="my-3">
                    <div class="accordion mb-4" id="acquisitionDetailsAccordion">
                        <!-- Car Details Section -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="carDetailsHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#carDetailsCollapse" aria-expanded="false" aria-controls="carDetailsCollapse">
                                    <i class="fas fa-car me-2"></i>Car Details
                                </button>
                            </h2>
                            <div id="carDetailsCollapse" class="accordion-collapse collapse" aria-labelledby="carDetailsHeading" data-bs-parent="#acquisitionDetailsAccordion">
                                <div class="accordion-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered mb-0" style="font-size: 0.9rem;">
                                            <tbody>
                                                <tr>
                                                    <td class="fw-bold" style="width: 30%; background-color: #f8f9fa;">Vehicle Name</td>
                                                    <td>{{ $vehicle->full_name }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold" style="background-color: #f8f9fa;">Year</td>
                                                    <td>{{ $vehicle->year }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold" style="background-color: #f8f9fa;">Make</td>
                                                    <td>
                                                        @if(is_string($vehicle->make))
                                                            {{ $vehicle->make }}
                                                        @elseif($vehicle->make)
                                                            {{ $vehicle->make->name }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold" style="background-color: #f8f9fa;">Model</td>
                                                    <td>
                                                        @if(is_string($vehicle->model))
                                                            {{ $vehicle->model }}
                                                        @elseif($vehicle->vehicleModel)
                                                            {{ $vehicle->vehicleModel->name }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold" style="background-color: #f8f9fa;">Variant</td>
                                                    <td>{{ $vehicle->variant ?: 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold" style="background-color: #f8f9fa;">Body Type</td>
                                                    <td>{{ $vehicle->body_type ?: 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold" style="background-color: #f8f9fa;">Transmission</td>
                                                    <td>{{ $vehicle->transmission }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold" style="background-color: #f8f9fa;">Fuel Type</td>
                                                    <td>{{ $vehicle->fuel_type }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold" style="background-color: #f8f9fa;">Kilometers</td>
                                                    <td>{{ number_format($vehicle->kilometers) }} km</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold" style="background-color: #f8f9fa;">Plate Number</td>
                                                    <td>{{ $vehicle->plate_number }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold" style="background-color: #f8f9fa;">Colour</td>
                                                    <td>{{ $vehicle->colour }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-bold" style="background-color: #f8f9fa;">Status</td>
                                                    <td>
                                                        @if($vehicle->status == 'Available')
                                                            <span class="badge bg-success" style="font-size: 0.85rem;">{{ $vehicle->status }}</span>
                                                        @elseif($vehicle->status == 'Under Maintenance')
                                                            <span class="badge bg-warning" style="font-size: 0.85rem;">{{ $vehicle->status }}</span>
                                                        @elseif($vehicle->status == 'Reserved')
                                                            <span class="badge bg-info" style="font-size: 0.85rem;">{{ $vehicle->status }}</span>
                                                        @elseif($vehicle->status == 'Released')
                                                            <span class="badge bg-primary" style="font-size: 0.85rem;">{{ $vehicle->status }}</span>
                                                        @elseif($vehicle->status == 'Forfeited')
                                                            <span class="badge bg-danger" style="font-size: 0.85rem;">{{ $vehicle->status }}</span>
                                                        @else
                                                            <span class="badge bg-secondary" style="font-size: 0.85rem;">{{ $vehicle->status }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <hr class="my-2">

                                    <!-- Vehicle Features -->
                                    <div class="row">
                                        <div class="col-md-12 mb-2">
                                            <label class="form-label fw-bold mb-1" style="font-size: 0.9rem;">Vehicle Features</label>
                                        </div>
                                        <div class="col-md-3 col-lg-2 mb-2">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-{{ $vehicle->with_tools ? 'check-circle text-success' : 'times-circle text-danger' }} me-2" style="font-size: 0.9rem;"></i>
                                                <span style="font-size: 0.9rem;">With Tools</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-2 mb-2">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-{{ $vehicle->with_matting ? 'check-circle text-success' : 'times-circle text-danger' }} me-2" style="font-size: 0.9rem;"></i>
                                                <span style="font-size: 0.9rem;">With Matting</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-2 mb-2">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-{{ $vehicle->with_spare_tire ? 'check-circle text-success' : 'times-circle text-danger' }} me-2" style="font-size: 0.9rem;"></i>
                                                <span style="font-size: 0.9rem;">With Spare Tire</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-lg-2 mb-2">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-{{ $vehicle->spare_key ? 'check-circle text-success' : 'times-circle text-danger' }} me-2" style="font-size: 0.9rem;"></i>
                                                <span style="font-size: 0.9rem;">Spare Key</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Purchase Information -->
                                    <hr class="my-2">
                                    <div class="row">
                                        <div class="col-md-3 col-lg-2 mb-2">
                                            <label class="form-label fw-bold mb-0" style="font-size: 0.85rem; margin-bottom: 0.1rem !important;">Purchase Price</label>
                                            <p class="form-control-plaintext mb-0" style="font-size: 0.9rem;">{{ $vehicle->formatted_purchase_price }}</p>
                                        </div>
                                        <div class="col-md-3 col-lg-2 mb-2">
                                            <label class="form-label fw-bold mb-0" style="font-size: 0.85rem; margin-bottom: 0.1rem !important;">Purchase Date</label>
                                            <p class="form-control-plaintext mb-0" style="font-size: 0.9rem;">{{ $vehicle->formatted_purchase_date }}</p>
                                        </div>
                                        <div class="col-md-3 col-lg-2 mb-2">
                                            <label class="form-label fw-bold mb-0" style="font-size: 0.85rem; margin-bottom: 0.1rem !important;">Purchased From</label>
                                            <p class="form-control-plaintext mb-0" style="font-size: 0.9rem;">{{ $vehicle->purchased_from ?: 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-3 col-lg-2 mb-2">
                                            <label class="form-label fw-bold mb-0" style="font-size: 0.85rem; margin-bottom: 0.1rem !important;">Total Gas Expenses</label>
                                            <p class="form-control-plaintext mb-0" style="font-size: 0.9rem;">₱{{ number_format($vehicle->gasExpenses->sum('gas_amount'), 2) }}</p>
                                        </div>
                                    </div>

                                    <!-- Notes Section -->
                                    @if($vehicle->notes)
                                    <hr class="my-2">
                                    <div class="row">
                                        <div class="col-md-12 mb-2">
                                            <label class="form-label fw-bold mb-1" style="font-size: 0.9rem;">Notes</label>
                                            <p class="form-control-plaintext mb-0" style="font-size: 0.9rem;">{{ $vehicle->notes }}</p>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <!-- Custom Fields for Vehicle Information -->
                                    @if($vehicle->customFieldsForSection('vehicle_information')->count() > 0)
                                    <hr class="my-3">
                                    <div class="row">
                                        @foreach($vehicle->customFieldsForSection('vehicle_information')->get() as $field)
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">{{ $field->field_label }}</label>
                                                <p class="form-control-plaintext">
                                                    @if($field->field_type == 'checkbox')
                                                        <i class="fas fa-{{ $field->field_value ? 'check-circle text-success' : 'times-circle text-danger' }} me-1"></i>
                                                        {{ $field->field_value ? 'Yes' : 'No' }}
                                                    @elseif($field->field_type == 'select' || $field->field_type == 'radio')
                                                        {{ $field->field_value ?: 'N/A' }}
                                                    @else
                                                        {{ $field->field_value ?: 'N/A' }}
                                                    @endif
                                                </p>
                                            </div>
                                        @endforeach
                                    </div>
                                    @endif

                                    @canPage('vehicles', 'update')
                                    <!-- Edit Vehicle Button -->
                                    <div class="text-end mt-4 pt-3" style="border-top: 1px solid #dee2e6;">
                                        <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-warning" style="background-color: #ffc107; border-color: #ffc107; color: #000; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='transparent'; this.style.color='#ffc107';" onmouseout="this.style.backgroundColor='#ffc107'; this.style.color='#000';">
                                            <i class="fas fa-edit me-1"></i>Edit Details
                                        </a>
                                    </div>
                                    @endcanPage
                                </div>
                            </div>
                        </div>

                        <!-- Documents Section -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="acquisitionDetailsHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#acquisitionDetailsCollapse" aria-expanded="false" aria-controls="acquisitionDetailsCollapse">
                                    <i class="fas fa-file-alt me-2"></i>Documents
                                </button>
                            </h2>
                            <div id="acquisitionDetailsCollapse" class="accordion-collapse collapse" aria-labelledby="acquisitionDetailsHeading" data-bs-parent="#acquisitionDetailsAccordion">
                                <div class="accordion-body">
                                    <!-- Acquisition Documents Section -->
                                    <div class="mb-4">
                                        @php
                                            $acquisitionDocuments = $vehicle->acquisitionDocuments;
                                            $documentTypes = [
                                                'OR' => 'OR',
                                                'CR' => 'CR',
                                                'AR' => 'AR',
                                                'IDS' => 'IDS',
                                                'PROMISSORY' => 'PROMISSORY',
                                                'CHATTEL' => 'CHATTEL',
                                                'REGISTRY_OF_DEEDS' => 'REGISTRY OF DEEDS',
                                                'SEC_CERT' => 'SEC CERT',
                                                'DEED_OF_SALE' => 'DEED OF SALE',
                                                'VOLUNTARY_SURRENDER' => 'VOLUNTARY SURRENDER',
                                                'SHERRIF_LETTER' => 'SHERRIF LETTER',
                                                'DEED_OF_SALE_BANK' => 'DEED OF SALE (BANK)',
                                            ];
                                        @endphp
                            <div class="row">
                                            @foreach($documentTypes as $type => $label)
                                                @php
                                                    $document = $acquisitionDocuments->where('document_type', $type)->first();
                                                    // Check if document has data (storage type with content, files, links, or form data)
                                                    $hasData = false;
                                                    if ($document) {
                                                        // Load files if not already loaded
                                                        if (!$document->relationLoaded('files')) {
                                                            $document->load('files');
                                                        }
                                                        
                                                        $hasData = $document->is_completed || 
                                                                   ($document->storage_type && (
                                                                       ($document->storage_type === 'link' && ($document->file_link || ($document->files && $document->files->where('type', 'link')->count() > 0))) ||
                                                                       ($document->storage_type === 'form' && $document->form_data && !empty($document->form_data) && (count($document->form_data) > 0 || (isset($document->form_data['_form_structure']) && !empty($document->form_data['_form_structure'])))) ||
                                                                       ($document->storage_type === 'file' && ($document->file_path || ($document->files && $document->files->where('type', 'file')->count() > 0)))
                                                                   ));
                                                    }
                                                @endphp
                                        <div class="col-md-6 mb-1" style="padding-left: 0; padding-right: 10px;">
                                    <div class="d-flex align-items-center">
                                                <input type="checkbox" 
                                                       class="form-check-input document-checkbox me-3" 
                                                       data-document-type="{{ $type }}"
                                                       data-document-label="{{ $label }}"
                                                       data-vehicle-id="{{ $vehicle->id }}"
                                                       data-document-id="{{ $document ? $document->id : '' }}"
                                                       data-is-completed="{{ $document && $document->is_completed ? '1' : '0' }}"
                                                       {{ $hasData ? 'checked' : '' }}
                                                       style="width: 18px; height: 18px; cursor: pointer; min-width: 24px;">
                                                        <span class="fw-semibold text-dark me-auto" style="color: #212529 !important; min-width: 0; flex: 1; margin-right: 1rem !important;">{{ $label }}</span>
                                                        <div class="ms-4 d-flex gap-2 flex-shrink-0">
                                                            @if($hasData && $document)
                                                                <a href="{{ route('vehicles.documents.show', [$vehicle, $document]) }}" 
                                                                   class="btn btn-sm btn-outline-primary" 
                                                                   title="View Document">
                                                                    <i class="fas fa-eye"></i> View
                                                                </a>
                                                                @if($document && $document->storage_type === 'file')
                                                                    @if($document->files && $document->files->where('type', 'file')->count() > 0)
                                                                        @foreach($document->files->where('type', 'file') as $file)
                                                                            <a href="{{ route('vehicles.documents.files.download', [$vehicle, $file->id]) }}" 
                                                                               class="btn btn-sm btn-outline-success" 
                                                                               title="Download {{ $file->file_name }}"
                                                                               target="_blank">
                                                                                <i class="fas fa-download"></i> Download {{ $loop->iteration }}
                                                                            </a>
                                                                        @endforeach
                                                                    @else
                                                                        <a href="{{ route('vehicles.documents.download', [$vehicle, $document]) }}" 
                                                                           class="btn btn-sm btn-outline-success" 
                                                                           title="Download File"
                                                                           target="_blank">
                                                                            <i class="fas fa-download"></i> Download
                                                                        </a>
                                                                    @endif
                                                                @endif
                                                            @endif
                                    </div>
                                </div>
                                    </div>
                                            @endforeach
                                </div>
                                    </div>

                                    <!-- View Documents Button -->
                                    <div class="text-end mt-4 pt-3" style="border-top: 1px solid #dee2e6;">
                                        <a href="{{ route('vehicles.documents.index', $vehicle) }}" class="btn btn-warning" style="background-color: #ffc107; border-color: #ffc107; color: #000; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='transparent'; this.style.color='#ffc107';" onmouseout="this.style.backgroundColor='#ffc107'; this.style.color='#000';">
                                            <i class="fas fa-eye me-1"></i>View Documents
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Vehicle Expenses Section -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="vehicleExpensesHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#vehicleExpensesCollapse" aria-expanded="false" aria-controls="vehicleExpensesCollapse">
                                    <i class="fas fa-list me-2"></i>Vehicle Expenses
                                    @if($vehicle->expenseItems->count() > 0)
                                        <span class="badge bg-primary ms-2">{{ $vehicle->expenseItems->count() }}</span>
                                    @endif
                                </button>
                            </h2>
                            <div id="vehicleExpensesCollapse" class="accordion-collapse collapse" aria-labelledby="vehicleExpensesHeading" data-bs-parent="#acquisitionDetailsAccordion">
                                <div class="accordion-body">
                            @php
                                $totalExpenseItems = $vehicle->expenseItems->sum('cost');
                                $totalGasExpenses = $vehicle->gasExpenses->sum('gas_amount');
                                $totalVehicleExpensesWithGas = $totalExpenseItems + $totalGasExpenses;
                                $hasExpenseItemsOrGas = $vehicle->expenseItems->count() > 0 || $vehicle->gasExpenses->count() > 0;
                            @endphp
                            @if($hasExpenseItemsOrGas)
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>#</th>
                                                <th>Description</th>
                                                <th>C/o</th>
                                                <th>Cost</th>
                                                <th>Transaction Date</th>
                                                <th>Receipts</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($vehicle->expenseItems as $index => $item)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
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
                                                    <td>
                                                        @if($item->expenseTransaction)
                                                            <a href="{{ route('expenses.show', ['expenseTransaction' => $item->expenseTransaction->id, 'item' => $item->id]) }}" class="text-decoration-none">
                                                                {{ $item->expenseTransaction->transaction_date->format('M j, Y') }}
                                                            </a>
                            @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($item->receipts->count() > 0)
                                                            <span class="badge bg-info">{{ $item->receipts->count() }} receipt(s)</span>
                                                        @else
                                                            <span class="text-muted">No receipts</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($item->expenseTransaction)
                                                            <a href="{{ route('expenses.show', ['expenseTransaction' => $item->expenseTransaction->id, 'item' => $item->id]) }}" class="btn btn-sm btn-outline-primary" title="View Transaction">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            @if($vehicle->gasExpenses->count() > 0)
                                                <tr>
                                                    <td>{{ $vehicle->expenseItems->count() + 1 }}</td>
                                                    <td><i class="fas fa-gas-pump me-1"></i>Vehicle Gas Expense</td>
                                                    <td>—</td>
                                                    <td><strong>₱{{ number_format($totalGasExpenses, 2) }}</strong></td>
                                                    <td><span class="text-muted">{{ $vehicle->gasExpenses->count() }} transaction(s)</span></td>
                                                    <td><span class="text-muted">—</span></td>
                                                    <td>
                                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openGasExpenseModal()" title="View Gas Expenses">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                        <tfoot class="table-light">
                                            <tr>
                                                <th colspan="3" class="text-end">Total Vehicle Expenses:</th>
                                                <th class="text-danger">₱{{ number_format($totalVehicleExpensesWithGas, 2) }}</th>
                                                <th colspan="3"></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <div class="text-center text-muted py-4">
                                    <i class="fas fa-info-circle fa-3x mb-3" style="color: #6c757d;"></i>
                                    <h5 class="text-muted mb-3">No expense items found for this vehicle.</h5>
                                    <a href="{{ route('vehicles.expenses.create', $vehicle) }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i>Add Expenses
                                    </a>
                                </div>
                            @endif

                                    <!-- View Details Button -->
                                    <div class="text-end mt-4 pt-3" style="border-top: 1px solid #dee2e6;">
                                        <a href="{{ route('vehicles.expenses.create', $vehicle) }}" class="btn btn-warning" style="background-color: #ffc107; border-color: #ffc107; color: #000; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='transparent'; this.style.color='#ffc107';" onmouseout="this.style.backgroundColor='#ffc107'; this.style.color='#000';">
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Vehicle Reservation Details Section -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="reservationDetailsHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#reservationDetailsCollapse" aria-expanded="false" aria-controls="reservationDetailsCollapse">
                                <i class="fas fa-chart-line me-2"></i>Vehicle Reservation Details
                                </button>
                            </h2>
                            <div id="reservationDetailsCollapse" class="accordion-collapse collapse" aria-labelledby="reservationDetailsHeading" data-bs-parent="#acquisitionDetailsAccordion">
                                <div class="accordion-body">
                                    @php
                                        // Check if vehicle is reserved
                                        $isReserved = $vehicle->status === 'Reserved' || ($vehicle->statusDetail && (
                                            $vehicle->statusDetail->sale_date ||
                                            $vehicle->statusDetail->sales_person_reserved ||
                                            $vehicle->statusDetail->sale_reservation_amount ||
                                            $vehicle->statusDetail->sales_price ||
                                            ($vehicle->statusDetail->sale_status && $vehicle->statusDetail->sale_status !== 'Available')
                                        ));
                                    @endphp

                                    @if(!$isReserved)
                                    <!-- Reserve Vehicle Button (shown when vehicle is not reserved) -->
                                    <div class="text-center py-5">
                                        <i class="fas fa-handshake fa-4x text-muted mb-4"></i>
                                        <h5 class="mb-4">This vehicle is available for reservation</h5>
                                        <button type="button" class="btn btn-primary btn-lg" onclick="openReserveVehicleModal()">
                                            <i class="fas fa-calendar-check me-2"></i>Reserve This Vehicle
                                        </button>
                                    </div>
                                    @else
                                    <!-- Reservation Details (shown when vehicle is reserved) -->
                                    <div class="d-flex justify-content-end mb-3">
                                        @canPage('vehicles', 'create')
                                        <button type="button" class="btn btn-sm btn-success me-2" onclick="openPostReservationExpenseModal()">
                                            <i class="fas fa-plus me-1"></i>Add Post Reservation Expense
                                        </button>
                                        @endcanPage
                                        @canPage('vehicles', 'update')
                                        <button type="button" class="btn btn-sm btn-primary" onclick="openStatusDetailsModal()">
                                            <i class="fas fa-edit me-1"></i>Edit Status Details
                                        </button>
                                        @endcanPage
                                        @php
                                            // Check if statusDetail has actual reservation data
                                            $hasReservationData = $vehicle->statusDetail && (
                                                $vehicle->statusDetail->sale_date ||
                                                $vehicle->statusDetail->sales_person_reserved ||
                                                $vehicle->statusDetail->sale_reservation_amount ||
                                                $vehicle->statusDetail->sales_price ||
                                                ($vehicle->statusDetail->sale_status && $vehicle->statusDetail->sale_status !== 'Available')
                                            );
                                        @endphp
                                        @canPage('vehicles', 'delete')
                                        @if($hasReservationData)
                                        <button type="button" class="btn btn-sm btn-danger ms-2" onclick="deleteReservationDetails()">
                                            <i class="fas fa-trash me-1"></i>Delete Reservation Details
                                        </button>
                                        @endif
                                        @endcanPage
                                    </div>
                                    @if($vehicle->statusDetail)
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Showroom</label>
                                        <p class="form-control-plaintext">{{ $vehicle->statusDetail->showroom ?: 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Sale Date</label>
                                        <p class="form-control-plaintext">{{ $vehicle->statusDetail->formatted_sale_date ?: 'N/A' }}</p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Sales Price</label>
                                        <p class="form-control-plaintext">{{ $vehicle->statusDetail->formatted_sales_price ?: 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Sale Reservation Amount</label>
                                        <p class="form-control-plaintext">{{ $vehicle->statusDetail->formatted_sale_reservation_amount ?: 'N/A' }}</p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Sales Person Reserved ( S.E )</label>
                                        <p class="form-control-plaintext">{{ $vehicle->statusDetail->sales_person_reserved ?: 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Sales Person Released (S.E)</label>
                                        <p class="form-control-plaintext">{{ $vehicle->statusDetail->sales_person_release ?: 'N/A' }}</p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Good Sales Review</label>
                                        <p class="form-control-plaintext">
                                            @if($vehicle->statusDetail->good_sales_review === true)
                                                <i class="fas fa-check-circle text-success me-1"></i>Yes
                                            @elseif($vehicle->statusDetail->good_sales_review === false)
                                                <i class="fas fa-times-circle text-danger me-1"></i>No
                                            @else
                                                N/A
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Cash/Financing</label>
                                        <p class="form-control-plaintext">{{ $vehicle->statusDetail->cash_financing ?: 'N/A' }}</p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Sale Origin</label>
                                        <p class="form-control-plaintext">{{ $vehicle->statusDetail->sale_origin ?: 'N/A' }}</p>
                                    </div>
                                </div>
                                @if(($vehicle->statusDetail->sale_origin ?? '') === 'Agent')
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Sales agent name</label>
                                        <p class="form-control-plaintext">{{ $vehicle->statusDetail->sales_agent_name ?: 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">
                                            <a href="#agentDetailsCollapse" class="text-decoration-none" title="View agent details">Agent Cost</a>
                                        </label>
                                        <p class="form-control-plaintext">{{ $vehicle->statusDetail->formatted_agent_cost }}</p>
                                    </div>
                                </div>
                                @endif

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Sale Status</label>
                                        <p class="form-control-plaintext">
                                            @if($vehicle->statusDetail->sale_status == 'Available')
                                                <span class="badge bg-success">{{ $vehicle->statusDetail->sale_status }}</span>
                                            @elseif($vehicle->statusDetail->sale_status == 'Under Maintenance')
                                                <span class="badge bg-warning">{{ $vehicle->statusDetail->sale_status }}</span>
                                            @elseif($vehicle->statusDetail->sale_status == 'Reserved')
                                                <span class="badge bg-info">{{ $vehicle->statusDetail->sale_status }}</span>
                                            @elseif($vehicle->statusDetail->sale_status == 'Released')
                                                <span class="badge bg-primary">{{ $vehicle->statusDetail->sale_status }}</span>
                                            @elseif($vehicle->statusDetail->sale_status == 'Forfeited')
                                                <span class="badge bg-danger">{{ $vehicle->statusDetail->sale_status }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $vehicle->statusDetail->sale_status }}</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Transfer Cost</label>
                                        <p class="form-control-plaintext">{{ $vehicle->statusDetail->formatted_transfer_cost ?: 'N/A' }}</p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Days from Acquisition to Reservation</label>
                                        <p class="form-control-plaintext">
                                            @if($vehicle->statusDetail->days_from_acquisition_to_reservation !== null)
                                                {{ $vehicle->statusDetail->days_from_acquisition_to_reservation }} day{{ $vehicle->statusDetail->days_from_acquisition_to_reservation != 1 ? 's' : '' }}
                                            @elseif($vehicle->statusDetail->sale_date && $vehicle->purchase_date)
                                                @php
                                                    $days = $vehicle->purchase_date->diffInDays($vehicle->statusDetail->sale_date);
                                                @endphp
                                                {{ $days }} day{{ $days != 1 ? 's' : '' }}
                                            @else
                                                N/A
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Insurance</label>
                                        <p class="form-control-plaintext">
                                            @if($vehicle->statusDetail->has_insurance)
                                                Yes - {{ $vehicle->statusDetail->insurance_value ? '₱' . number_format($vehicle->statusDetail->insurance_value, 2) : 'N/A' }}
                                            @else
                                                No
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Trade-in</label>
                                        <p class="form-control-plaintext">
                                            @if($vehicle->statusDetail->has_trade_in)
                                                Yes - {{ $vehicle->statusDetail->trade_in_value ? '₱' . number_format($vehicle->statusDetail->trade_in_value, 2) : 'N/A' }}
                                            @else
                                                No
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Total Post Reservation Expenses</label>
                                        <p class="form-control-plaintext">
                                            @php
                                                $postReservationExpenses = \App\Models\ExpenseItem::where('vehicle_id', $vehicle->id)
                                                    ->where('expense_category', 'Post Reservation')
                                                    ->sum('cost');
                                            @endphp
                                            <strong class="text-danger">₱{{ number_format($postReservationExpenses, 2) }}</strong>
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Customer Information Section (Hidden when vehicle is Released) -->
                                @if($vehicle->status !== 'Released')
                                <hr class="my-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0">
                                        <i class="fas fa-user me-2"></i>Customer Information
                                    </h6>
                                    @php
                                        $hasCustomerInfo = $vehicle->statusDetail && (
                                            $vehicle->statusDetail->customer_first_name ||
                                            $vehicle->statusDetail->customer_last_name ||
                                            $vehicle->statusDetail->customer_contact_number
                                        );
                                    @endphp
                                    @canPage('vehicles', 'update')
                                    @if(!$hasCustomerInfo)
                                        <button type="button" class="btn btn-sm btn-primary" onclick="openCustomerInfoModal()">
                                            <i class="fas fa-plus me-1"></i>Add Customer Information
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openCustomerInfoModal()">
                                            <i class="fas fa-edit me-1"></i>Edit Customer Information
                                        </button>
                                    @endif
                                    @endcanPage
                                </div>
                                
                                @if($hasCustomerInfo)
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">First Name</label>
                                            <p class="form-control-plaintext">{{ $vehicle->statusDetail->customer_first_name ?: 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Last Name</label>
                                            <p class="form-control-plaintext">{{ $vehicle->statusDetail->customer_last_name ?: 'N/A' }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Middle Name</label>
                                            <p class="form-control-plaintext">{{ $vehicle->statusDetail->customer_middle_name ?: 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Contact Number</label>
                                            <p class="form-control-plaintext">{{ $vehicle->statusDetail->customer_contact_number ?: 'N/A' }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Date of Birth</label>
                                            <p class="form-control-plaintext">
                                                {{ $vehicle->statusDetail->customer_date_of_birth ? $vehicle->statusDetail->customer_date_of_birth->format('M d, Y') : 'N/A' }}
                                            </p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Gender</label>
                                            <p class="form-control-plaintext">{{ $vehicle->statusDetail->customer_gender ?: 'N/A' }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Location</label>
                                            <p class="form-control-plaintext">{{ $vehicle->statusDetail->customer_location ?: 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Purpose</label>
                                            <p class="form-control-plaintext">{{ $vehicle->statusDetail->customer_purpose ?: 'N/A' }}</p>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle me-2"></i>No customer information added yet. Click "Add Customer Information" to add details.
                                    </div>
                                @endif
                                @endif
                                
                                @if(($vehicle->statusDetail->cash_financing ?? '') === 'Financing')
                                <!-- Financing Details Section -->
                                <hr class="my-4">
                                <h6 class="mb-3">
                                    <i class="fas fa-money-bill-wave me-2"></i>Financing Details
                                </h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Financing Company</label>
                                        <p class="form-control-plaintext">{{ $vehicle->statusDetail->financing_company ?: 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Finance Revenue 1</label>
                                        <p class="form-control-plaintext">{{ $vehicle->statusDetail->formatted_finance_revenue_1 ?: 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Finance Revenue 2</label>
                                        <p class="form-control-plaintext">{{ $vehicle->statusDetail->formatted_finance_revenue_2 ?: 'N/A' }}</p>
                                    </div>
                                </div>
                                @endif

                                <!-- Reservation Documents Section -->
                                <hr class="my-4">
                                <h6 class="mb-3">
                                    <i class="fas fa-file-alt me-2"></i>Reservation Documents
                                </h6>
                                @php
                                    $reservationDocuments = $vehicle->reservationDocuments;
                                    $acquisitionDocuments = $vehicle->acquisitionDocuments ?? collect();
                                    $reservationDocumentTypes = [
                                        'IDS' => 'IDS',
                                        'AR' => 'AR',
                                        'SOURCE_OF_SALE' => 'SOURCE OF SALE',
                                        'POSTING_OF_GRAPHICS' => 'POSTING OF GRAPHICS',
                                    ];
                                @endphp
                                <div class="row">
                                    @foreach($reservationDocumentTypes as $type => $label)
                                        @php
                                            // First check for reservation document, then fallback to acquisition document
                                            $document = $reservationDocuments->where('document_type', $type)->first();
                                            if (!$document) {
                                                $document = $acquisitionDocuments->where('document_type', $type)->first();
                                            }
                                        @endphp
                                        <div class="col-md-6 mb-1" style="padding-left: 0; padding-right: 10px;">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-{{ $document ? ($document->is_completed ? 'check-circle text-success' : 'check-square text-primary') : 'square text-muted' }} me-3" style="font-size: 1.1rem; min-width: 24px;"></i>
                                                <span class="fw-semibold text-dark me-auto" style="color: #212529 !important; min-width: 0; flex: 1; margin-right: 1rem !important;">{{ $label }}</span>
                                                <div class="ms-4 d-flex gap-2 flex-shrink-0">
                                                    @if($document)
                                                        @php
                                                            // Check if document is from acquisition (to show different link)
                                                            $isFromAcquisition = $reservationDocuments->where('document_type', $type)->first() === null;
                                                        @endphp
                                                        <a href="{{ route('vehicles.documents.create', [$vehicle, $type]) }}?process_type=RESERVATION" 
                                                           class="btn btn-sm btn-outline-primary" 
                                                           title="{{ $isFromAcquisition ? 'Add Reservation Document (Acquisition document exists)' : 'Edit Document' }}">
                                                            <i class="fas fa-edit"></i> {{ $isFromAcquisition ? 'Add' : 'Edit' }}
                                                        </a>
                                                        @if($document->storage_type === 'file')
                                                            @if($document->files && $document->files->where('type', 'file')->count() > 0)
                                                                @foreach($document->files->where('type', 'file') as $file)
                                                                    <a href="{{ route('vehicles.documents.files.download', [$vehicle, $file->id]) }}" 
                                                                       class="btn btn-sm btn-outline-success" 
                                                                       title="Download {{ $file->file_name }}"
                                                                       target="_blank">
                                                                        <i class="fas fa-download"></i> Download {{ $loop->iteration }}
                                                                    </a>
                                                                @endforeach
                                                            @else
                                                                <a href="{{ route('vehicles.documents.download', [$vehicle, $document]) }}" 
                                                                   class="btn btn-sm btn-outline-success" 
                                                                   title="Download File"
                                                                   target="_blank">
                                                                    <i class="fas fa-download"></i> Download
                                                                </a>
                                                            @endif
                                                        @endif
                                                    @else
                                                        <a href="{{ route('vehicles.documents.create', [$vehicle, $type]) }}?process_type=RESERVATION" 
                                                           class="btn btn-sm btn-primary" 
                                                           title="Add Document">
                                                            <i class="fas fa-plus"></i> Add
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <!-- Release Vehicle Button -->
                                @if($vehicle->statusDetail && $vehicle->statusDetail->sale_status === 'Reserved')
                                <div class="d-flex justify-content-center mt-4 pt-3 border-top">
                                    <button type="button" class="btn btn-lg btn-success" onclick="releaseVehicle()">
                                        <i class="fas fa-check-circle me-2"></i>Release this Vehicle
                                    </button>
                                </div>
                                @endif

                                    <!-- View Details Button -->
                                    <div class="text-end mt-4 pt-3" style="border-top: 1px solid #dee2e6;">
                                        <a href="{{ route('vehicles.reservation-details.show', $vehicle) }}" class="btn btn-warning" style="background-color: #ffc107; border-color: #ffc107; color: #000; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='transparent'; this.style.color='#ffc107';" onmouseout="this.style.backgroundColor='#ffc107'; this.style.color='#000';">
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </a>
                                    </div>
                                    @endif
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Vehicle Released Details Section -->
                        @if($vehicle->status === 'Released' && $vehicle->statusDetail)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="releasedDetailsHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#releasedDetailsCollapse" aria-expanded="false" aria-controls="releasedDetailsCollapse">
                                    <i class="fas fa-check-circle me-2"></i>Vehicle Released Details
                                </button>
                            </h2>
                            <div id="releasedDetailsCollapse" class="accordion-collapse collapse" aria-labelledby="releasedDetailsHeading" data-bs-parent="#acquisitionDetailsAccordion">
                                <div class="accordion-body">
                                    @canPage('vehicles', 'update')
                                    <div class="d-flex justify-content-end mb-3">
                                        <button type="button" class="btn btn-sm btn-success me-2" onclick="openPostReleaseExpenseModal()">
                                            <i class="fas fa-plus me-1"></i>Add Post Release Expense
                                        </button>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="openStatusDetailsModal()">
                                            <i class="fas fa-edit me-1"></i>Edit Release Details
                                        </button>
                                    </div>
                                    @endcanPage
                                    
                                    <!-- Release Information -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Sales Person Released (S.E)</label>
                                            <p class="form-control-plaintext">{{ $vehicle->statusDetail->sales_person_release ?: 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Good Sales Review</label>
                                            <p class="form-control-plaintext">
                                                @if($vehicle->statusDetail->good_sales_review === true)
                                                    <i class="fas fa-check-circle text-success me-1"></i>Yes
                                                @elseif($vehicle->statusDetail->good_sales_review === false)
                                                    <i class="fas fa-times-circle text-danger me-1"></i>No
                                                @else
                                                    N/A
                                                @endif
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Customer Information Section -->
                                    <hr class="my-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="mb-0">
                                            <i class="fas fa-user me-2"></i>Customer Information
                                        </h6>
                                        @php
                                            $hasCustomerInfo = $vehicle->statusDetail && (
                                                $vehicle->statusDetail->customer_first_name ||
                                                $vehicle->statusDetail->customer_last_name ||
                                                $vehicle->statusDetail->customer_contact_number
                                            );
                                        @endphp
                                        @canPage('vehicles', 'update')
                                        @if($hasCustomerInfo)
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="openCustomerInfoModal()">
                                                <i class="fas fa-edit me-1"></i>Edit Customer Information
                                            </button>
                                        @endif
                                        @endcanPage
                                    </div>
                                    
                                    @if($hasCustomerInfo)
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">First Name</label>
                                                <p class="form-control-plaintext">{{ $vehicle->statusDetail->customer_first_name ?: 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Last Name</label>
                                                <p class="form-control-plaintext">{{ $vehicle->statusDetail->customer_last_name ?: 'N/A' }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Middle Name</label>
                                                <p class="form-control-plaintext">{{ $vehicle->statusDetail->customer_middle_name ?: 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Contact Number</label>
                                                <p class="form-control-plaintext">{{ $vehicle->statusDetail->customer_contact_number ?: 'N/A' }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Date of Birth</label>
                                                <p class="form-control-plaintext">{{ $vehicle->statusDetail->customer_date_of_birth ? $vehicle->statusDetail->customer_date_of_birth->format('M d, Y') : 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Gender</label>
                                                <p class="form-control-plaintext">{{ $vehicle->statusDetail->customer_gender ?: 'N/A' }}</p>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Location</label>
                                                <p class="form-control-plaintext">{{ $vehicle->statusDetail->customer_location ?: 'N/A' }}</p>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold">Purpose</label>
                                                <p class="form-control-plaintext">{{ $vehicle->statusDetail->customer_purpose ?: 'N/A' }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-2"></i>No customer information available.
                                        </div>
                                    @endif
                                    
                                    <!-- Post Release Expenses Total -->
                                    <hr class="my-4">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-bold">Total Post Release Expenses</label>
                                            <p class="form-control-plaintext">
                                                @php
                                                    $postReleaseExpenses = \App\Models\ExpenseItem::where('vehicle_id', $vehicle->id)
                                                        ->where('expense_category', 'Post Release')
                                                        ->sum('cost');
                                                @endphp
                                                <strong class="text-danger">₱{{ number_format($postReleaseExpenses, 2) }}</strong>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Release Documents Section -->
                                    <hr class="my-4">
                                    <h6 class="mb-3">
                                        <i class="fas fa-file-alt me-2"></i>Release Documents
                                    </h6>
                                    @php
                                        $releaseDocuments = $vehicle->releaseDocuments ?? collect();
                                        $acquisitionDocuments = $vehicle->acquisitionDocuments ?? collect();
                                        $releaseDocumentTypes = [
                                            'OR' => 'OR',
                                            'CR' => 'CR',
                                            'AR' => 'AR',
                                            'IDS' => 'IDS',
                                            'PROMISSORY' => 'PROMISSORY',
                                            'CHATTEL' => 'CHATTEL',
                                            'REGISTRY_OF_DEEDS' => 'REGISTRY OF DEEDS',
                                            'SEC_CERT' => 'SEC CERT',
                                            'DEED_OF_SALE' => 'DEED OF SALE',
                                            'CONSENT_FORM' => 'CONSENT FORM',
                                        ];
                                    @endphp
                                    <div class="row gx-2">
                                        @foreach($releaseDocumentTypes as $type => $label)
                                            @php
                                                // First check for release document, then fallback to acquisition document
                                                $document = $releaseDocuments->where('document_type', $type)->first();
                                                if (!$document) {
                                                    $document = $acquisitionDocuments->where('document_type', $type)->first();
                                                }
                                                $hasDocument = !is_null($document);
                                                $isFromAcquisition = $hasDocument && $releaseDocuments->where('document_type', $type)->first() === null;
                                            @endphp
                                            <div class="col-md-6 mb-2" style="padding-left: 0; padding-right: 10px;">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-{{ $hasDocument ? ($document->is_completed ? 'check-circle text-success' : 'check-square text-primary') : 'square text-muted' }} me-2" style="font-size: 1rem; min-width: 20px;"></i>
                                                    <span class="fw-semibold text-dark me-auto" style="color: #212529 !important; min-width: 0; flex: 1; margin-right: 0.5rem !important; font-size: 0.9rem;">{{ $label }}</span>
                                                    <div class="ms-2 d-flex gap-1 flex-shrink-0">
                                                        @if($hasDocument)
                                                            <a href="{{ route('vehicles.documents.create', [$vehicle, $type]) }}?process_type=RELEASE" 
                                                               class="btn btn-sm btn-outline-primary" 
                                                               title="{{ $isFromAcquisition ? 'Add Release Document (Acquisition document exists)' : 'Edit Document' }}">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            @if($document->storage_type === 'file')
                                                                @if($document->files && $document->files->where('type', 'file')->count() > 0)
                                                                    @foreach($document->files->where('type', 'file') as $file)
                                                                        <a href="{{ route('vehicles.documents.files.download', [$vehicle, $file->id]) }}" 
                                                                           class="btn btn-sm btn-outline-success" 
                                                                           title="Download {{ $file->file_name }}"
                                                                           target="_blank">
                                                                            <i class="fas fa-download"></i>
                                                                        </a>
                                                                    @endforeach
                                                                @else
                                                                    <a href="{{ route('vehicles.documents.download', [$vehicle, $document]) }}" 
                                                                       class="btn btn-sm btn-outline-success" 
                                                                       title="Download File"
                                                                       target="_blank">
                                                                        <i class="fas fa-download"></i>
                                                                    </a>
                                                                @endif
                                                            @endif
                                                        @else
                                                            <a href="{{ route('vehicles.documents.create', [$vehicle, $type]) }}?process_type=RELEASE" 
                                                               class="btn btn-sm btn-primary" 
                                                               title="Add Document">
                                                                <i class="fas fa-plus"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Edit Details Button -->
                                    <div class="text-end mt-4 pt-3" style="border-top: 1px solid #dee2e6;">
                                        <button type="button" class="btn btn-warning" style="background-color: #ffc107; border-color: #ffc107; color: #000; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='transparent'; this.style.color='#ffc107';" onmouseout="this.style.backgroundColor='#ffc107'; this.style.color='#000';" onclick="openStatusDetailsModal()">
                                            <i class="fas fa-edit me-1"></i>Edit Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Incentive Section -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="incentiveDetailsHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#incentiveDetailsCollapse" aria-expanded="false" aria-controls="incentiveDetailsCollapse">
                                    <i class="fas fa-gift me-2"></i>Incentive
                                </button>
                            </h2>
                            <div id="incentiveDetailsCollapse" class="accordion-collapse collapse" aria-labelledby="incentiveDetailsHeading" data-bs-parent="#acquisitionDetailsAccordion">
                                <div class="accordion-body">
                                    @php $inc = $vehicle->incentive; @endphp
                                    <form id="incentiveForm">
                                        @csrf
                                        <input type="hidden" name="_method" value="PUT">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">S.A Origin</label>
                                                <input type="text" class="form-control" name="sa_origin" value="{{ optional($inc)->sa_origin ?? '' }}" placeholder="S.A Origin">
                                                <div class="mt-1">
                                                    <input type="url" class="form-control form-control-sm" name="sa_origin_link" value="{{ optional($inc)->sa_origin_link ?? '' }}" placeholder="Add link or attach image">
                                                    <input type="file" class="form-control form-control-sm mt-1" name="sa_origin_file" accept="image/*">
                                                    @if($inc && $inc->sa_origin_file_path)
                                                        <a href="{{ asset('storage/' . $inc->sa_origin_file_path) }}" target="_blank" class="small">View attached</a>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Reserved By or Employee</label>
                                                <input type="text" class="form-control" name="reserved_by" value="{{ optional($inc)->reserved_by ?? '' }}" placeholder="Reserved by / employee">
                                            </div>
                                        </div>
                                        <hr class="my-3">
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="no_look" value="1" id="inc_no_look" {{ ($inc && $inc->no_look) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold" for="inc_no_look">NO LOOK</label>
                                                </div>
                                                <input type="url" class="form-control form-control-sm mt-1" name="no_look_link" value="{{ optional($inc)->no_look_link ?? '' }}" placeholder="Add link or attach image">
                                                <input type="file" class="form-control form-control-sm mt-1" name="no_look_file" accept="image/*">
                                                @if($inc && $inc->no_look_file_path)
                                                    <a href="{{ asset('storage/' . $inc->no_look_file_path) }}" target="_blank" class="small">View attached</a>
                                                @endif
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="insurance" value="1" id="inc_insurance" {{ ($inc && $inc->insurance) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold" for="inc_insurance">INSURANCE</label>
                                                </div>
                                                <input type="url" class="form-control form-control-sm mt-1" name="insurance_link" value="{{ optional($inc)->insurance_link ?? '' }}" placeholder="Add link or attach image">
                                                <input type="file" class="form-control form-control-sm mt-1" name="insurance_file" accept="image/*">
                                                @if($inc && $inc->insurance_file_path)
                                                    <a href="{{ asset('storage/' . $inc->insurance_file_path) }}" target="_blank" class="small">View attached</a>
                                                @endif
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="testimonial" value="1" id="inc_testimonial" {{ ($inc && $inc->testimonial) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold" for="inc_testimonial">Testimonial</label>
                                                </div>
                                                <input type="url" class="form-control form-control-sm mt-1" name="testimonial_link" value="{{ optional($inc)->testimonial_link ?? '' }}" placeholder="Add link or attach image">
                                                <input type="file" class="form-control form-control-sm mt-1" name="testimonial_file" accept="image/*">
                                                @if($inc && $inc->testimonial_file_path)
                                                    <a href="{{ asset('storage/' . $inc->testimonial_file_path) }}" target="_blank" class="small">View attached</a>
                                                @endif
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="review" value="1" id="inc_review" {{ ($inc && $inc->review) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-bold" for="inc_review">Review</label>
                                                </div>
                                                <input type="url" class="form-control form-control-sm mt-1" name="review_link" value="{{ optional($inc)->review_link ?? '' }}" placeholder="Add link or attach image">
                                                <input type="file" class="form-control form-control-sm mt-1" name="review_file" accept="image/*">
                                                @if($inc && $inc->review_file_path)
                                                    <a href="{{ asset('storage/' . $inc->review_file_path) }}" target="_blank" class="small">View attached</a>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-end mt-3 pt-3" style="border-top: 1px solid #dee2e6;">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save me-1"></i>Save Incentive
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        @php
                            $hasReservationDetails = $vehicle->status === 'Reserved' || ($vehicle->statusDetail && (
                                $vehicle->statusDetail->sale_date ||
                                $vehicle->statusDetail->sales_person_reserved ||
                                $vehicle->statusDetail->sale_reservation_amount ||
                                $vehicle->statusDetail->sales_price ||
                                ($vehicle->statusDetail->sale_status && $vehicle->statusDetail->sale_status !== 'Available')
                            ));
                        @endphp
                        @if($hasReservationDetails)
                        <!-- Forfeit Details Section -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="forfetDetailsHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#forfetDetailsCollapse" aria-expanded="false" aria-controls="forfetDetailsCollapse">
                                    <i class="fas fa-gavel me-2"></i>Forfeit Details
                                </button>
                            </h2>
                            <div id="forfetDetailsCollapse" class="accordion-collapse collapse" aria-labelledby="forfetDetailsHeading" data-bs-parent="#acquisitionDetailsAccordion">
                                <div class="accordion-body">
                                    @if($vehicle->forfeitDetails->count() > 0)
                                    <div class="table-responsive mb-4">
                                        <table class="table table-bordered table-hover align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Previous Forfeit</th>
                                                    <th>Forfeit Amount</th>
                                                    <th>Forfeit Date</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($vehicle->forfeitDetails as $fd)
                                                <tr>
                                                    <td>{{ $fd->previous_forfeit_date ? $fd->previous_forfeit_date->format('M d, Y') : '—' }}</td>
                                                    <td>₱{{ number_format($fd->forfeit_amount, 2) }}</td>
                                                    <td>{{ $fd->forfeit_date->format('M d, Y') }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="text-center text-muted py-4">
                                        <i class="fas fa-info-circle fa-3x mb-3" style="color: #6c757d;"></i>
                                        <h5 class="text-muted mb-3">Forfeit details will be displayed here</h5>
                                        <p class="text-muted">Click "Add Details" to add forfeit information.</p>
                                    </div>
                                    @endif

                                    <!-- Add / Edit Details Button -->
                                    <div class="text-end mt-4 pt-3" style="border-top: 1px solid #dee2e6;">
                                        <button type="button" class="btn btn-warning" style="background-color: #ffc107; border-color: #ffc107; color: #000; transition: all 0.3s ease;" onmouseover="this.style.backgroundColor='transparent'; this.style.color='#ffc107';" onmouseout="this.style.backgroundColor='#ffc107'; this.style.color='#000';" data-bs-toggle="modal" data-bs-target="#forfeitDetailsModal">
                                            @if($vehicle->forfeitDetails->count() > 0)
                                            <i class="fas fa-edit me-1"></i>Edit Details
                                            @else
                                            <i class="fas fa-plus me-1"></i>Add Details
                                            @endif
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Transfer Details Section -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="transferDetailsHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#transferDetailsCollapse" aria-expanded="false" aria-controls="transferDetailsCollapse">
                                    <i class="fas fa-exchange-alt me-2"></i>Transfer Details
                                    @if($vehicle->transferOrcrs->count() > 0)
                                        <span class="badge bg-primary ms-2">{{ $vehicle->transferOrcrs->count() }}</span>
                                    @endif
                                </button>
                            </h2>
                            <div id="transferDetailsCollapse" class="accordion-collapse collapse" aria-labelledby="transferDetailsHeading" data-bs-parent="#acquisitionDetailsAccordion">
                                <div class="accordion-body">
                                    @if($vehicle->transferOrcrs->isNotEmpty())
                                        <p class="text-muted small mb-2">OR/CR transfer records linked to this vehicle.</p>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Transaction Type</th>
                                                        <th>Release Date</th>
                                                        <th>LTO File/No</th>
                                                        <th>Transfer SOP</th>
                                                        <th>Transfer OR</th>
                                                        <th>PNP Clearance</th>
                                                        <th>Status</th>
                                                        <th class="text-center">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($vehicle->transferOrcrs as $tr)
                                                        <tr>
                                                            <td>{{ $tr->date ? $tr->date->format('j M Y') : '—' }}</td>
                                                            <td>{{ $tr->transaction_type ?: '—' }}</td>
                                                            <td>{{ $tr->release_date ? $tr->release_date->format('j M Y') : '—' }}</td>
                                                            <td>{{ $tr->lto_file_no ?: '—' }}</td>
                                                            <td>{{ $tr->transfer_sop !== null ? number_format((float)$tr->transfer_sop, 2) : '—' }}</td>
                                                            <td>{{ $tr->transfer_or !== null ? number_format((float)$tr->transfer_or, 2) : '—' }}</td>
                                                            <td>{{ $tr->pnp_clearance !== null ? number_format((float)$tr->pnp_clearance, 2) : '—' }}</td>
                                                            <td>
                                                                @if($tr->status === 'DONE')
                                                                    <span class="badge bg-success">{{ $tr->status }}</span>
                                                                @elseif($tr->status === 'In Progress')
                                                                    <span class="badge bg-info">{{ $tr->status }}</span>
                                                                @else
                                                                    <span class="badge bg-warning text-dark">{{ $tr->status ?? '—' }}</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                @canPage('vehicles', 'update')
                                                                <a href="{{ route('transfer-orcr.edit', $tr) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                                                @endcanPage
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center text-muted py-3">
                                            <i class="fas fa-exchange-alt fa-2x mb-2" style="color: #6c757d;"></i>
                                            <p class="mb-2">No transfer OR/CR records linked to this vehicle yet.</p>
                                            <p class="small mb-0">Add a record from the Transfer OR/CR list and link this vehicle to see it here.</p>
                                        </div>
                                    @endif

                                    <div class="d-flex justify-content-end gap-2 mt-3 pt-3" style="border-top: 1px solid #dee2e6;">
                                        <a href="{{ route('transfer-orcr.create') }}?vehicle_id={{ $vehicle->id }}" class="btn btn-warning btn-sm" style="background-color: #ffc107; border-color: #ffc107; color: #000;">
                                            <i class="fas fa-plus me-1"></i>Add Transfer OR/CR
                                        </a>
                                        <a href="{{ route('transfer-orcr.index') }}" class="btn btn-outline-secondary btn-sm">View all transfer OR/CR</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Follow Up Docs Details Section -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="followUpDocsDetailsHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#followUpDocsDetailsCollapse" aria-expanded="false" aria-controls="followUpDocsDetailsCollapse">
                                    <i class="fas fa-clipboard-check me-2"></i>Follow Up Docs Details
                                    @if($vehicle->followUpDocuments->count() > 0)
                                        <span class="badge bg-primary ms-2">{{ $vehicle->followUpDocuments->count() }}</span>
                                    @endif
                                </button>
                            </h2>
                            <div id="followUpDocsDetailsCollapse" class="accordion-collapse collapse" aria-labelledby="followUpDocsDetailsHeading" data-bs-parent="#acquisitionDetailsAccordion">
                                <div class="accordion-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="text-muted small">Follow-up documents linked to this unit. Manage all in <a href="{{ route('follow-up-documents.index') }}">Follow Up Documents</a>.</span>
                                        <a href="{{ route('follow-up-documents.create', ['vehicle_id' => $vehicle->id]) }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-plus me-1"></i>Add Follow Up Doc
                                        </a>
                                    </div>
                                    @if($vehicle->followUpDocuments->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover table-sm mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Title</th>
                                                        <th>Due Date</th>
                                                        <th>Priority</th>
                                                        <th>Status</th>
                                                        <th class="text-center">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($vehicle->followUpDocuments as $doc)
                                                        <tr>
                                                            <td><strong>{{ $doc->title }}</strong></td>
                                                            <td>{{ $doc->due_date ? $doc->due_date->format('M d, Y') : '—' }}</td>
                                                            <td>
                                                                @if($doc->priority === 'High')
                                                                    <span class="badge bg-danger">{{ $doc->priority }}</span>
                                                                @elseif($doc->priority === 'Medium')
                                                                    <span class="badge bg-warning text-dark">{{ $doc->priority }}</span>
                                                                @elseif($doc->priority === 'Low')
                                                                    <span class="badge bg-secondary">{{ $doc->priority }}</span>
                                                                @else
                                                                    —
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($doc->status === 'Completed')
                                                                    <span class="badge bg-success">{{ $doc->status }}</span>
                                                                @elseif($doc->status === 'In Progress')
                                                                    <span class="badge bg-info">{{ $doc->status }}</span>
                                                                @else
                                                                    <span class="badge bg-warning text-dark">{{ $doc->status }}</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                @canPage('vehicles', 'update')
                                                                <a href="{{ route('follow-up-documents.edit', $doc) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                                                @endcanPage
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="alert alert-info mb-0">
                                            <i class="fas fa-info-circle me-2"></i>No follow-up documents for this unit. <a href="{{ route('follow-up-documents.create', ['vehicle_id' => $vehicle->id]) }}" class="alert-link">Add one</a> or view all in <a href="{{ route('follow-up-documents.index') }}" class="alert-link">Follow Up Documents</a>.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Buffing Details Section -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="buffingDetailsHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#buffingDetailsCollapse" aria-expanded="false" aria-controls="buffingDetailsCollapse">
                                    <i class="fas fa-spray-can me-2"></i>Buffing Details
                                    @if($vehicle->buffingRecords->count() > 0)
                                        <span class="badge bg-primary ms-2">{{ $vehicle->buffingRecords->count() }}</span>
                                    @endif
                                </button>
                            </h2>
                            <div id="buffingDetailsCollapse" class="accordion-collapse collapse" aria-labelledby="buffingDetailsHeading" data-bs-parent="#acquisitionDetailsAccordion">
                                <div class="accordion-body">
                                    @if($vehicle->buffingRecords->isNotEmpty())
                                        <p class="text-muted small mb-2">Buffing tracker records linked to this vehicle.</p>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Staff</th>
                                                        <th>Status</th>
                                                        <th>Notes</th>
                                                        <th class="text-center">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($vehicle->buffingRecords as $rec)
                                                        <tr>
                                                            <td>{{ $rec->buffing_date ? $rec->buffing_date->format('M d, Y') : '—' }}</td>
                                                            <td>{{ $rec->employee ? $rec->employee->full_name : '—' }}</td>
                                                            <td>
                                                                @if($rec->status === 'Completed')
                                                                    <span class="badge bg-success">{{ $rec->status }}</span>
                                                                @elseif($rec->status === 'In Progress')
                                                                    <span class="badge bg-info">{{ $rec->status }}</span>
                                                                @else
                                                                    <span class="badge bg-warning text-dark">{{ $rec->status ?? '—' }}</span>
                                                                @endif
                                                            </td>
                                                            <td class="small">{{ Str::limit($rec->notes, 40) ?: '—' }}</td>
                                                            <td class="text-center">
                                                                @canPage('vehicles', 'update')
                                                                <a href="{{ route('buffing-tracker.edit', $rec) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                                                @endcanPage
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center text-muted py-3">
                                            <i class="fas fa-spray-can fa-2x mb-2" style="color: #6c757d;"></i>
                                            <p class="mb-2">No buffing records linked to this vehicle yet.</p>
                                            <p class="small mb-0">Add a record from the Buffing Tracker and link this unit to see it here.</p>
                                        </div>
                                    @endif

                                    <div class="d-flex justify-content-end gap-2 mt-3 pt-3" style="border-top: 1px solid #dee2e6;">
                                        <a href="{{ route('buffing-tracker.create') }}?vehicle_id={{ $vehicle->id }}" class="btn btn-warning btn-sm" style="background-color: #ffc107; border-color: #ffc107; color: #000;">
                                            <i class="fas fa-plus me-1"></i>Add Buffing Record
                                        </a>
                                        <a href="{{ route('buffing-tracker.index') }}" class="btn btn-outline-secondary btn-sm">View Buffing Tracker</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ads/Boosting Details Section -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="adsBoostingDetailsHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#adsBoostingDetailsCollapse" aria-expanded="false" aria-controls="adsBoostingDetailsCollapse">
                                    <i class="fas fa-bullhorn me-2"></i>Ads/Boosting Details
                                </button>
                            </h2>
                            <div id="adsBoostingDetailsCollapse" class="accordion-collapse collapse" aria-labelledby="adsBoostingDetailsHeading" data-bs-parent="#acquisitionDetailsAccordion">
                                <div class="accordion-body">
                                    @canPage('vehicles', 'create')
                                    <div class="d-flex justify-content-end mb-3">
                                        <button type="button" class="btn btn-sm btn-success" onclick="openAddVehicleAdModal()">
                                            <i class="fas fa-plus me-1"></i>Add Video Ad Details
                                        </button>
                                    </div>
                                    @endcanPage

                                    @if($vehicle->ads->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Posted Date</th>
                                                    <th>Video Link</th>
                                                    <th>Social Media Post Link</th>
                                                    <th>Ads/Boost Link</th>
                                                    <th>Campaign ID</th>
                                                    <th>Ad ID</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($vehicle->ads as $ad)
                                                <tr data-ad-id="{{ $ad->id }}" 
                                                    data-posted-date="{{ $ad->posted_date->format('Y-m-d') }}"
                                                    data-video-links="{{ e(json_encode($ad->video_links_list)) }}"
                                                    data-social-media-links="{{ e(json_encode($ad->social_media_links_list)) }}"
                                                    data-ads-boost-link="{{ $ad->ads_boost_link ?? '' }}"
                                                    data-campaign-id="{{ $ad->campaign_id ?? '' }}"
                                                    data-ad-id-value="{{ $ad->ad_id ?? '' }}">
                                                    <td>{{ $ad->posted_date->format('M d, Y') }}</td>
                                                    <td>
                                                        @forelse($ad->video_links_list as $index => $url)
                                                            <a href="{{ $url }}" target="_blank" class="text-primary d-inline-block mb-1">
                                                                <i class="fas fa-external-link-alt me-1"></i>Video{{ count($ad->video_links_list) > 1 ? ' ' . ($index + 1) : '' }}
                                                            </a>@if(!$loop->last)<br>@endif
                                                        @empty
                                                            <span class="text-muted">N/A</span>
                                                        @endforelse
                                                    </td>
                                                    <td>
                                                        @forelse($ad->social_media_links_list as $item)
                                                            <a href="{{ $item['link'] }}" target="_blank" class="text-primary d-inline-block mb-1">
                                                                <i class="fas fa-external-link-alt me-1"></i>{{ $item['channel'] }}
                                                            </a>@if(!$loop->last)<br>@endif
                                                        @empty
                                                            <span class="text-muted">N/A</span>
                                                        @endforelse
                                                    </td>
                                                    <td>
                                                        @if($ad->ads_boost_link)
                                                            <a href="{{ $ad->ads_boost_link }}" target="_blank" class="text-primary">
                                                                <i class="fas fa-external-link-alt me-1"></i>View Ad/Boost
                                                            </a>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $ad->campaign_id ?: 'N/A' }}</td>
                                                    <td>{{ $ad->ad_id ?: 'N/A' }}</td>
                                                    <td>
                                                        @canPage('vehicles', 'update')
                                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="editVehicleAd({{ $ad->id }})" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        @endcanPage
                                                        @canPage('vehicles', 'delete')
                                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteVehicleAd({{ $ad->id }})" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        @endcanPage
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle me-2"></i>No video ad details recorded yet. Click "Add Video Ad Details" to add one.
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Vlog Details Section -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="vlogDetailsHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#vlogDetailsCollapse" aria-expanded="false" aria-controls="vlogDetailsCollapse">
                                    <i class="fas fa-video me-2"></i>Vlog Details
                                    @if($vehicle->videoPostingRecords->count() > 0)
                                        <span class="badge bg-primary ms-2">{{ $vehicle->videoPostingRecords->count() }}</span>
                                    @endif
                                </button>
                            </h2>
                            <div id="vlogDetailsCollapse" class="accordion-collapse collapse" aria-labelledby="vlogDetailsHeading" data-bs-parent="#acquisitionDetailsAccordion">
                                <div class="accordion-body">
                                    @if($vehicle->videoPostingRecords->isNotEmpty())
                                        <p class="text-muted small mb-2">Video and posting tracker records linked to this vehicle.</p>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Title</th>
                                                        <th>Type</th>
                                                        <th>Platform</th>
                                                        <th>Status</th>
                                                        <th>Link</th>
                                                        <th class="text-center">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($vehicle->videoPostingRecords as $rec)
                                                        <tr>
                                                            <td>{{ $rec->record_date ? $rec->record_date->format('M d, Y') : '—' }}</td>
                                                            <td><strong>{{ $rec->title ?: '—' }}</strong></td>
                                                            <td>
                                                                @if($rec->type === 'Video')
                                                                    <span class="badge bg-info">{{ $rec->type }}</span>
                                                                @elseif($rec->type === 'Post')
                                                                    <span class="badge bg-primary">{{ $rec->type }}</span>
                                                                @else
                                                                    <span class="badge bg-warning text-dark">{{ $rec->type ?? '—' }}</span>
                                                                @endif
                                                            </td>
                                                            <td>{{ $rec->platform ?: '—' }}</td>
                                                            <td>
                                                                @if($rec->status === 'Posted')
                                                                    <span class="badge bg-success">{{ $rec->status }}</span>
                                                                @elseif($rec->status === 'Scheduled')
                                                                    <span class="badge bg-secondary">{{ $rec->status }}</span>
                                                                @else
                                                                    <span class="badge bg-warning text-dark">{{ $rec->status ?? '—' }}</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($rec->link_url)
                                                                    <a href="{{ $rec->link_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary"><i class="fas fa-external-link-alt"></i></a>
                                                                @else
                                                                    <span class="text-muted">—</span>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">
                                                                @canPage('vehicles', 'update')
                                                                <a href="{{ route('video-posting-tracker.edit', $rec) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                                                @endcanPage
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center text-muted py-3">
                                            <i class="fas fa-video fa-2x mb-2" style="color: #6c757d;"></i>
                                            <p class="mb-2">No video or posting records linked to this vehicle yet.</p>
                                            <p class="small mb-0">Add a record from the Video and Posting Tracker and link this unit to see it here.</p>
                                        </div>
                                    @endif

                                    <div class="d-flex justify-content-end gap-2 mt-3 pt-3" style="border-top: 1px solid #dee2e6;">
                                        <a href="{{ route('video-posting-tracker.create') }}?vehicle_id={{ $vehicle->id }}" class="btn btn-warning btn-sm" style="background-color: #ffc107; border-color: #ffc107; color: #000;">
                                            <i class="fas fa-plus me-1"></i>Add Record
                                        </a>
                                        <a href="{{ route('video-posting-tracker.index') }}" class="btn btn-outline-secondary btn-sm">View Video and Posting Tracker</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Agent Details Section -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="agentDetailsHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#agentDetailsCollapse" aria-expanded="false" aria-controls="agentDetailsCollapse">
                                    <i class="fas fa-user-tie me-2"></i>Agent Details
                                </button>
                            </h2>
                            <div id="agentDetailsCollapse" class="accordion-collapse collapse" aria-labelledby="agentDetailsHeading" data-bs-parent="#acquisitionDetailsAccordion">
                                <div class="accordion-body">
                                    @if($vehicle->salesAgentCommissions->isNotEmpty())
                                        <p class="text-muted small mb-2">Commission records linked to this vehicle.</p>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Agent</th>
                                                        <th>Client</th>
                                                        <th>Type</th>
                                                        <th class="text-end">Amount</th>
                                                        <th class="text-end">Agents folder</th>
                                                        <th class="text-end">SE commission</th>
                                                        <th>Proof appt.</th>
                                                        <th>Sign w/ agent</th>
                                                        <th>Date of payment</th>
                                                        <th class="text-center">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($vehicle->salesAgentCommissions as $com)
                                                        <tr>
                                                            <td><strong>{{ $com->agent_name }}</strong></td>
                                                            <td>{{ $com->client_name ?: '—' }}</td>
                                                            <td><span class="badge bg-{{ $com->transaction_type === 'FINANCING' ? 'primary' : 'secondary' }}">{{ $com->transaction_type }}</span></td>
                                                            <td class="text-end"><strong>₱{{ number_format($com->amount, 2) }}</strong></td>
                                                            <td class="text-end small">{{ $com->agents_folder_amount !== null ? '₱' . number_format($com->agents_folder_amount, 2) : '—' }}</td>
                                                            <td class="text-end small">{{ $com->sales_executive_commission !== null ? '₱' . number_format($com->sales_executive_commission, 2) : '—' }}</td>
                                                            <td class="text-center small">{{ $com->proof_of_appointment_label }}</td>
                                                            <td class="text-center small">{{ $com->sign_client_with_agent_label }}</td>
                                                            <td class="small">{{ $com->date_of_payment_display ?: '—' }}</td>
                                                            <td class="text-center">
                                                                @canPage('vehicles', 'update')
                                                                <a href="{{ route('sales-agent-commissions.edit', $com) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                                                @endcanPage
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center text-muted py-3">
                                            <i class="fas fa-hand-holding-usd fa-2x mb-2" style="color: #6c757d;"></i>
                                            <p class="mb-2">No commission records linked to this vehicle yet.</p>
                                            <p class="small mb-0">Add a commission from the list and tag this vehicle to see it here.</p>
                                        </div>
                                    @endif

                                    <div class="d-flex justify-content-end gap-2 mt-3 pt-3" style="border-top: 1px solid #dee2e6;">
                                        <a href="{{ route('sales-agent-commissions.create') }}?vehicle_id={{ $vehicle->id }}" class="btn btn-warning btn-sm" style="background-color: #ffc107; border-color: #ffc107; color: #000;">
                                            <i class="fas fa-plus me-1"></i>Add Commission
                                        </a>
                                        <a href="{{ route('sales-agent-commissions.index') }}" class="btn btn-outline-secondary btn-sm">View all commissions</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Summary of Profit/Loss Details Section -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="profitLossDetailsHeading">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#profitLossDetailsCollapse" aria-expanded="false" aria-controls="profitLossDetailsCollapse">
                                    <i class="fas fa-chart-line me-2"></i>Summary of Profit/Loss Details
                                </button>
                            </h2>
                            <div id="profitLossDetailsCollapse" class="accordion-collapse collapse" aria-labelledby="profitLossDetailsHeading" data-bs-parent="#acquisitionDetailsAccordion">
                                <div class="accordion-body">
                                    @php
                                        $purchasePricePl = $vehicle->purchase_price ?? 0;
                                        $totalVehicleExpensesPl = $vehicle->expenseItems->sum('cost');
                                        $grandTotalPl = $purchasePricePl + $totalVehicleExpensesPl;
                                        $soldPricePl = $vehicle->sold_price ?? 0;
                                        $postedPricePl = $vehicle->posted_price ?? 0;
                                        if ($vehicle->sold_price) {
                                            $profitPl = $soldPricePl - $grandTotalPl;
                                            $profitLabelPl = 'Total Profit';
                                            $profitFormulaPl = 'Sold Price - Expense Totals';
                                            $profitDisplayValuePl = $soldPricePl;
                                        } else {
                                            $profitPl = $postedPricePl - $grandTotalPl;
                                            $profitLabelPl = 'Potential Profit';
                                            $profitFormulaPl = 'Posted Price - Expense Totals';
                                            $profitDisplayValuePl = $postedPricePl;
                                        }
                                    @endphp
                                    <h6 class="text-center mb-3">
                                        <i class="fas fa-receipt me-1"></i>Expense Totals
                                    </h6>
                                    <div class="row text-center mb-3">
                                        <div class="col-12">
                                            <div class="p-3 rounded" style="background-color: #fff3cd; border: 2px solid #ffc107;">
                                                <h5 class="text-dark fw-bold mb-1">₱{{ number_format($grandTotalPl, 2) }}</h5>
                                                <small class="text-muted">Purchase Price + Total Vehicle Expenses</small>
                                                <small class="text-muted d-block">(₱{{ number_format($purchasePricePl, 2) }} + ₱{{ number_format($totalVehicleExpensesPl, 2) }})</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <div class="col-6">
                                            <h6 class="mb-1"><i class="fas fa-tag me-1"></i>Posted Price</h6>
                                            <div class="p-2 rounded small" style="background-color: #e7f3ff; border: 1px solid #0d6efd;">
                                                {{ $vehicle->posted_price ? '₱' . number_format($vehicle->posted_price, 2) : '—' }}
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <h6 class="mb-1"><i class="fas fa-money-bill-wave me-1"></i>Sold Price</h6>
                                            <div class="p-2 rounded small" style="background-color: #d4edda; border: 1px solid #28a745;">
                                                {{ $vehicle->sold_price ? '₱' . number_format($vehicle->sold_price, 2) : '—' }}
                                            </div>
                                        </div>
                                    </div>
                                    <h6 class="text-center mt-3 mb-2">
                                        <i class="fas fa-chart-line me-1"></i>{{ $profitLabelPl }}
                                    </h6>
                                    <div class="row text-center mb-3">
                                        <div class="col-12">
                                            <div class="p-3 rounded" style="background-color: {{ $profitPl >= 0 ? '#d4edda' : '#f8d7da' }}; border: 2px solid {{ $profitPl >= 0 ? '#28a745' : '#dc3545' }};">
                                                <h5 class="fw-bold mb-1 {{ $profitPl >= 0 ? 'text-success' : 'text-danger' }}">
                                                    {{ $profitPl >= 0 ? '+' : '' }}₱{{ number_format($profitPl, 2) }}
                                                </h5>
                                                <small class="text-muted">{{ $profitFormulaPl }}</small>
                                                @if($vehicle->sold_price && $grandTotalPl > 0)
                                                    <small class="text-muted d-block">(₱{{ number_format($soldPricePl, 2) }} - ₱{{ number_format($grandTotalPl, 2) }})</small>
                                                @elseif(!$vehicle->sold_price && $vehicle->posted_price && $grandTotalPl > 0)
                                                    <small class="text-muted d-block">(₱{{ number_format($postedPricePl, 2) }} - ₱{{ number_format($grandTotalPl, 2) }})</small>
                                                @elseif(!$vehicle->sold_price && !$vehicle->posted_price)
                                                    <small class="text-danger d-block">Posted price not set</small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @if($vehicle->gasExpenses->count() > 0)
                                        <hr class="my-2">
                                        <h6 class="text-center mb-2"><i class="fas fa-gas-pump me-1"></i>Vehicle Gas Expense</h6>
                                        <div class="text-center">
                                            <span class="text-danger fw-bold">₱{{ number_format($vehicle->gasExpenses->sum('gas_amount'), 2) }}</span>
                                            <span class="text-muted small"> ({{ $vehicle->gasExpenses->count() }} transaction(s))</span>
                                        </div>
                                    @endif
                                    <div class="mt-3 pt-3" style="border-top: 1px solid #dee2e6;">
                                        <span class="fw-bold">POSTED PRICE :</span>
                                        <span class="ms-1">{{ $vehicle->posted_price != null ? '₱' . number_format($vehicle->posted_price, 2) : '—' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Custom Sections (Dynamically Created) -->
                        <div id="custom-sections-container">
                            @foreach($vehicle->customSections as $section)
                                <div class="accordion-item custom-section" data-section-id="{{ $section->id }}">
                                    <h2 class="accordion-header" id="customSectionHeading{{ $section->id }}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#customSectionCollapse{{ $section->id }}" aria-expanded="false" aria-controls="customSectionCollapse{{ $section->id }}">
                                            <i class="fas fa-folder me-2"></i>{{ $section->title }}
                                            <div class="ms-auto me-3" onclick="event.stopPropagation();">
                                                @canPage('vehicles', 'update')
                                                <button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="editCustomSection({{ $section->id }})">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                @endcanPage
                                                @canPage('vehicles', 'delete')
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteCustomSection({{ $section->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                @endcanPage
                                            </div>
                                        </button>
                                    </h2>
                                    <div id="customSectionCollapse{{ $section->id }}" class="accordion-collapse collapse" aria-labelledby="customSectionHeading{{ $section->id }}" data-bs-parent="#acquisitionDetailsAccordion">
                                        <div class="accordion-body">
                                            @if($section->description)
                                                <p class="text-muted mb-3">{{ $section->description }}</p>
                                            @endif
                                            <div class="row">
                                                @foreach($section->fields as $field)
                                                    <div class="col-md-6 mb-3">
                                                        <label class="form-label fw-bold">{{ $field->field_label }}</label>
                                                        <p class="form-control-plaintext">
                                                            @if($field->field_type == 'checkbox')
                                                                <i class="fas fa-{{ $field->field_value ? 'check-circle text-success' : 'times-circle text-danger' }} me-1"></i>
                                                                {{ $field->field_value ? 'Yes' : 'No' }}
                                                            @elseif($field->field_type == 'select' || $field->field_type == 'radio')
                                                                {{ $field->field_value ?: 'N/A' }}
                                                            @else
                                                                {{ $field->field_value ?: 'N/A' }}
                                                            @endif
                                                        </p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Add New Section Button -->
                        <div class="text-center py-3 px-3" style="border-top: 1px solid #dee2e6;">
                            @canPage('vehicles', 'create')
                            <button type="button" class="btn btn-primary btn-sm" onclick="openCustomSectionModal()">
                                <i class="fas fa-plus me-2"></i>Add New Section
                            </button>
                            @endcanPage
                        </div>
                    </div>
                        </div>
                    </div>
                </div>

            <div class="row">
                <!-- Left Column - Vehicle Images and Information -->
                <div class="col-lg-12">
                </div>
                        </div>
        </main>
                    </div>
                </div>

<!-- Hidden Delete Form -->
<form id="deleteForm" method="POST" action="{{ route('vehicles.destroy', $vehicle) }}" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Image Upload Modal -->
<div class="modal fade" id="imageUploadModal" tabindex="-1" aria-labelledby="imageUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageUploadModalLabel">Upload Vehicle Images</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
            <div class="modal-body">
                <form id="imageUploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="images" class="form-label">Select Images (Max 5 images, 5MB each)</label>
                        <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*" required>
                        <div class="form-text">Supported formats: JPEG, PNG, JPG, GIF, WebP. Maximum file size: 5MB per image.</div>
                                    </div>
                    <div id="imagePreview" class="row"></div>
                </form>
                                </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="uploadImages()">
                    <i class="fas fa-upload me-1"></i>Upload Images
                                                </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
<!-- Status Change Modal -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusModalLabel">Change Vehicle Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="statusForm">
                    @csrf
                    <div class="mb-3">
                        <label for="newStatus" class="form-label">New Status</label>
                        <select class="form-select" id="newStatus" name="status" required>
                            <option value="">Select Status</option>
                            <option value="Available" {{ $vehicle->status == 'Available' ? 'selected' : '' }}>Available</option>
                            <option value="Under Maintenance" {{ $vehicle->status == 'Under Maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                            <option value="Reserved" {{ $vehicle->status == 'Reserved' ? 'selected' : '' }}>Reserved</option>
                            <option value="Released" {{ $vehicle->status == 'Released' ? 'selected' : '' }}>Released</option>
                            <option value="Forfeited" {{ $vehicle->status == 'Forfeited' ? 'selected' : '' }}>Forfeited</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                @canPage('vehicles', 'update')
                <button type="button" class="btn btn-primary" onclick="updateStatus()">Update Status</button>
                @endcanPage
            </div>
        </div>
                                </div>
                            </div>
                            
<!-- Release Vehicle Form Modal -->
<div class="modal fade" id="releaseVehicleModal" tabindex="-1" aria-labelledby="releaseVehicleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="releaseVehicleModalLabel">Release Vehicle - Complete Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
            <div class="modal-body">
                <form id="releaseVehicleForm">
                    @csrf
                    @php
                        // Ensure acquisition documents are available in the modal
                        $acquisitionDocuments = $vehicle->acquisitionDocuments ?? collect();
                    @endphp
                    <h6 class="mb-3"><i class="fas fa-user-tie me-2"></i>Release Information</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="release_sales_person_release" class="form-label">Sales Person Released (S.E) <span class="text-danger">*</span></label>
                            @include('vehicles.partials._executive_sales_select', [
                                'executiveAgents' => $executiveAgents ?? collect(),
                                'selectId' => 'release_sales_person_release',
                                'name' => 'sales_person_release',
                                'required' => true,
                                'currentValue' => $vehicle->statusDetail->sales_person_release ?? '',
                            ])
                                    </div>
                        <div class="col-md-6 mb-3">
                            <label for="release_good_sales_review" class="form-label">Good Sales Review <span class="text-danger">*</span></label>
                            <select class="form-select" id="release_good_sales_review" name="good_sales_review" required>
                                <option value="">Select</option>
                                <option value="1" {{ ($vehicle->statusDetail->good_sales_review ?? null) === true ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ ($vehicle->statusDetail->good_sales_review ?? null) === false ? 'selected' : '' }}>No</option>
                            </select>
                                        </div>
                                        </div>
                    
                    <hr class="my-4">
                    <h6 class="mb-3"><i class="fas fa-user me-2"></i>Customer Information</h6>
                    
                    @php
                        $hasCustomerInfoInRelease = $vehicle->statusDetail && (
                            $vehicle->statusDetail->customer_first_name ||
                            $vehicle->statusDetail->customer_last_name ||
                            $vehicle->statusDetail->customer_contact_number
                        );
                            @endphp
                            
                    @if($hasCustomerInfoInRelease)
                        <!-- Display existing customer information -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">First Name</label>
                                <p class="form-control-plaintext">{{ $vehicle->statusDetail->customer_first_name ?: 'N/A' }}</p>
                                    </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Last Name</label>
                                <p class="form-control-plaintext">{{ $vehicle->statusDetail->customer_last_name ?: 'N/A' }}</p>
                                </div>
                            </div>
                            
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Middle Name</label>
                                <p class="form-control-plaintext">{{ $vehicle->statusDetail->customer_middle_name ?: 'N/A' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Contact Number</label>
                                <p class="form-control-plaintext">{{ $vehicle->statusDetail->customer_contact_number ?: 'N/A' }}</p>
                                    </div>
                                </div>
                                
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Date of Birth</label>
                                <p class="form-control-plaintext">
                                    {{ $vehicle->statusDetail->customer_date_of_birth ? $vehicle->statusDetail->customer_date_of_birth->format('M d, Y') : 'N/A' }}
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Gender</label>
                                <p class="form-control-plaintext">{{ $vehicle->statusDetail->customer_gender ?: 'N/A' }}</p>
                                    </div>
                                </div>
                                
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Location</label>
                                <p class="form-control-plaintext">{{ $vehicle->statusDetail->customer_location ?: 'N/A' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Purpose</label>
                                <p class="form-control-plaintext">{{ $vehicle->statusDetail->customer_purpose ?: 'N/A' }}</p>
                                    </div>
                                </div>
                            @else
                        <!-- Show customer information form -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="release_customer_first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="release_customer_first_name" name="customer_first_name">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="release_customer_last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="release_customer_last_name" name="customer_last_name">
                                    </div>
                                </div>
                                
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="release_customer_middle_name" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="release_customer_middle_name" name="customer_middle_name">
                                    </div>
                            <div class="col-md-6 mb-3">
                                <label for="release_customer_contact_number" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" id="release_customer_contact_number" name="customer_contact_number">
                                </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="release_customer_date_of_birth" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="release_customer_date_of_birth" name="customer_date_of_birth">
                    </div>
                            <div class="col-md-6 mb-3">
                                <label for="release_customer_gender" class="form-label">Gender</label>
                                <select class="form-select" id="release_customer_gender" name="customer_gender">
                                    <option value="">Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Other">Other</option>
                                </select>
                </div>
            </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="release_customer_location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="release_customer_location" name="customer_location">
    </div>
                            <div class="col-md-6 mb-3">
                                <label for="release_customer_purpose" class="form-label">Purpose</label>
                                <textarea class="form-control" id="release_customer_purpose" name="customer_purpose" rows="3"></textarea>
</div>
                        </div>
                    @endif
                    
                    <!-- Release Documents Section -->
                    <hr class="my-4">
                    <h6 class="mb-3">
                        <i class="fas fa-file-alt me-2"></i>Release Documents
                    </h6>
                    <p class="text-muted small mb-3">Complete the following documents for vehicle release:</p>
                    @php
                        $releaseDocumentTypes = [
                            'OR' => 'OR',
                            'CR' => 'CR',
                            'AR' => 'AR',
                            'IDS' => 'IDS',
                            'PROMISSORY' => 'PROMISSORY',
                            'CHATTEL' => 'CHATTEL',
                            'REGISTRY_OF_DEEDS' => 'REGISTRY OF DEEDS',
                            'SEC_CERT' => 'SEC CERT',
                            'DEED_OF_SALE' => 'DEED OF SALE',
                            'CONSENT_FORM' => 'CONSENT FORM',
                        ];
                        $releaseDocuments = $vehicle->releaseDocuments ?? collect();
                        // Get acquisition documents for fallback (already defined at top of modal)
                    @endphp
                    <div class="row gx-2">
                        @foreach($releaseDocumentTypes as $type => $label)
                            @php
                                // First check for release document, then fallback to acquisition document
                                $document = $releaseDocuments->where('document_type', $type)->first();
                                if (!$document) {
                                    $document = $acquisitionDocuments->where('document_type', $type)->first();
                                }
                                $hasDocument = !is_null($document);
                                $isFromAcquisition = $hasDocument && $releaseDocuments->where('document_type', $type)->first() === null;
                            @endphp
                            <div class="col-md-6 mb-2" style="padding-left: 0; padding-right: 5px;">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-{{ $hasDocument ? ($document->is_completed ? 'check-circle text-success' : 'check-square text-primary') : 'square text-muted' }} me-2" style="font-size: 1rem; min-width: 20px;"></i>
                                    <span class="fw-semibold text-dark me-auto" style="color: #212529 !important; min-width: 0; flex: 1; margin-right: 0.5rem !important; font-size: 0.9rem;">{{ $label }}</span>
                                    <div class="ms-2 d-flex gap-1 flex-shrink-0">
                                        @if($hasDocument)
                                            <a href="{{ route('vehicles.documents.create', [$vehicle, $type]) }}?process_type=RELEASE" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="{{ $isFromAcquisition ? 'Add Release Document (Acquisition document exists)' : 'Edit Document' }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($document->storage_type === 'file')
                                                @if($document->files && $document->files->where('type', 'file')->count() > 0)
                                                    @foreach($document->files->where('type', 'file') as $file)
                                                        <a href="{{ route('vehicles.documents.files.download', [$vehicle, $file->id]) }}" 
                                                           class="btn btn-sm btn-outline-success" 
                                                           title="Download {{ $file->file_name }}"
                                                           target="_blank">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    @endforeach
                                                @else
                                                    <a href="{{ route('vehicles.documents.download', [$vehicle, $document]) }}" 
                                                       class="btn btn-sm btn-outline-success" 
                                                       title="Download File"
                                                       target="_blank">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                @endif
                                            @endif
                                        @else
                                            <a href="{{ route('vehicles.documents.create', [$vehicle, $type]) }}?process_type=RELEASE" 
                                               class="btn btn-sm btn-primary" 
                                               title="Add Document">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                        @endif
            </div>
                    </div>
                            </div>
                        @endforeach
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="saveReleaseVehicle()">
                    <i class="fas fa-check-circle me-1"></i>Save & Release Vehicle
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Sale Summary Modal -->
<div class="modal fade" id="saleSummaryModal" tabindex="-1" aria-labelledby="saleSummaryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="saleSummaryModalLabel">
                    <i class="fas fa-chart-bar me-2"></i>Vehicle Sale Summary
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                            @php
                                $purchasePrice = $vehicle->purchase_price ?? 0;
                                $totalVehicleExpenses = $vehicle->expenseItems->sum('cost');
                                $grandTotal = $purchasePrice + $totalVehicleExpenses;
                            @endphp
                            <h6 class="text-center mb-3">
                                <i class="fas fa-receipt me-1"></i>Expense Totals
                            </h6>
                            
                            <div class="row text-center">
                                <div class="col-12">
                                    <div class="p-3 rounded" style="background-color: #fff3cd; border: 2px solid #ffc107;">
                                        <h3 class="text-dark fw-bold mb-2">₱{{ number_format($grandTotal, 2) }}</h3>
                                        <small class="text-muted d-block">Purchase Price + Total Vehicle Expenses</small>
                                        <small class="text-muted">
                                            (₱{{ number_format($purchasePrice, 2) }} + ₱{{ number_format($totalVehicleExpenses, 2) }})
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Posted Price Section -->
                            <div class="row text-center mt-3">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">
                                            <i class="fas fa-tag me-1"></i>Posted Price
                                        </h6>
                                        <div>
                                            @canPage('vehicles', 'update')
                                            @if($vehicle->posted_price)
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="openPostedPriceModal({{ $vehicle->posted_price }})">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-primary" onclick="openPostedPriceModal()">
                                                    <i class="fas fa-plus me-1"></i>Add
                                                </button>
                                            @endif
                                            @endcanPage
                                            @canPage('vehicles', 'delete')
                                            @if($vehicle->posted_price)
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deletePostedPrice()">
                                                    <i class="fas fa-trash me-1"></i>Delete
                                                </button>
                                            @endif
                                            @endcanPage
                                        </div>
                                    </div>
                                    @if($vehicle->posted_price)
                                        <div class="p-2 rounded" style="background-color: #e7f3ff; border: 1px solid #0d6efd;">
                                            <h5 class="text-primary mb-0">₱{{ number_format($vehicle->posted_price, 2) }}</h5>
                                        </div>
                                    @else
                                        <div class="p-2 rounded" style="background-color: #f8f9fa; border: 1px solid #dee2e6;">
                                            <small class="text-muted">No posted price set</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Sold Price Section -->
                            <div class="row text-center mt-3">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0">
                                            <i class="fas fa-money-bill-wave me-1"></i>Sold Price
                                        </h6>
                                        <div>
                                            @canPage('vehicles', 'update')
                                            @if($vehicle->sold_price)
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="openSoldPriceModal({{ $vehicle->sold_price }})">
                                                    <i class="fas fa-edit me-1"></i>Edit
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-primary" onclick="openSoldPriceModal()">
                                                    <i class="fas fa-plus me-1"></i>Add
                                                </button>
                                            @endif
                                            @endcanPage
                                            @canPage('vehicles', 'delete')
                                            @if($vehicle->sold_price)
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteSoldPrice()">
                                                    <i class="fas fa-trash me-1"></i>Delete
                                                </button>
                                            @endif
                                            @endcanPage
                                        </div>
                                    </div>
                                    @if($vehicle->sold_price)
                                        <div class="p-2 rounded" style="background-color: #d4edda; border: 1px solid #28a745;">
                                            <h5 class="text-success mb-0">₱{{ number_format($vehicle->sold_price, 2) }}</h5>
                                        </div>
                                    @else
                                        <div class="p-2 rounded" style="background-color: #f8f9fa; border: 1px solid #dee2e6;">
                                            <small class="text-muted">No sold price set</small>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            @php
                                $soldPrice = $vehicle->sold_price ?? 0;
                                $postedPrice = $vehicle->posted_price ?? 0;
                                
                                // Calculate profit based on whether sold price exists
                                if ($vehicle->sold_price) {
                                    // Total Profit: Sold Price - Expense Totals
                                    $profit = $soldPrice - $grandTotal;
                                    $profitLabel = 'Total Profit';
                                    $profitFormula = 'Sold Price - Expense Totals';
                                    $profitDisplayValue = $soldPrice;
                                } else {
                                    // Potential Profit: Posted Price - Expense Totals
                                    $profit = $postedPrice - $grandTotal;
                                    $profitLabel = 'Potential Profit';
                                    $profitFormula = 'Posted Price - Expense Totals';
                                    $profitDisplayValue = $postedPrice;
                                }
                            @endphp
                            
                            <!-- Profit Section -->
                            <div class="row text-center mt-3">
                                <div class="col-12">
                                    <h6 class="mb-3">
                                        <i class="fas fa-chart-line me-1"></i>{{ $profitLabel }}
                                    </h6>
                                    <div class="p-3 rounded" style="background-color: {{ $profit >= 0 ? '#d4edda' : '#f8d7da' }}; border: 2px solid {{ $profit >= 0 ? '#28a745' : '#dc3545' }};">
                                        <h3 class="fw-bold mb-2 {{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $profit >= 0 ? '+' : '' }}₱{{ number_format($profit, 2) }}
                                        </h3>
                                        <small class="text-muted d-block">{{ $profitFormula }}</small>
                                        @if($vehicle->sold_price && $grandTotal > 0)
                                            <small class="text-muted">
                                                (₱{{ number_format($soldPrice, 2) }} - ₱{{ number_format($grandTotal, 2) }})
                                            </small>
                                        @elseif(!$vehicle->sold_price && $vehicle->posted_price && $grandTotal > 0)
                                            <small class="text-muted">
                                                (₱{{ number_format($postedPrice, 2) }} - ₱{{ number_format($grandTotal, 2) }})
                                            </small>
                                        @elseif(!$vehicle->sold_price && !$vehicle->posted_price)
                                            <small class="text-danger">Posted price not set</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-3">
                            <h6 class="text-center mb-3">
                                <i class="fas fa-gas-pump me-1"></i>Vehicle Gas Expense
                            </h6>
                            
                            @if($vehicle->gasExpenses->count() > 0)
                                <div class="row text-center">
                                    <div class="col-12 mb-2">
                                        <h5 class="text-danger">₱{{ number_format($vehicle->gasExpenses->sum('gas_amount'), 2) }}</h5>
                                        <small class="text-muted">Total Gas Expenses</small>
                                    </div>
                                </div>
                                
                                <div class="row text-center">
                                    <div class="col-12 mb-2">
                                        <h6 class="text-muted">{{ $vehicle->gasExpenses->count() }} transaction(s)</h6>
                                        <small class="text-muted">Gas expense records</small>
                                    </div>
                                </div>
                                
                                <div class="row text-center">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openGasExpenseModal()">
                                            <i class="fas fa-eye me-1"></i>View Details
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="row text-center">
                                    <div class="col-12 mb-2">
                                        <h5 class="text-muted">₱0.00</h5>
                                        <small class="text-muted">Total Gas Expenses</small>
                                    </div>
                                </div>
                                
                                <div class="row text-center">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-sm btn-primary" onclick="openGasExpenseModal()">
                                            <i class="fas fa-plus me-1"></i>Add Gas Expense
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
    </div>
</div>

<!-- Customer Information Modal -->
<div class="modal fade" id="customerInfoModal" tabindex="-1" aria-labelledby="customerInfoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customerInfoModalLabel">Customer Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="customerInfoForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="customer_first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="customer_first_name" name="customer_first_name" 
                                   value="{{ $vehicle->statusDetail->customer_first_name ?? '' }}">
                    </div>
                        <div class="col-md-6 mb-3">
                            <label for="customer_last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="customer_last_name" name="customer_last_name" 
                                   value="{{ $vehicle->statusDetail->customer_last_name ?? '' }}">
            </div>
            </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="customer_middle_name" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="customer_middle_name" name="customer_middle_name" 
                                   value="{{ $vehicle->statusDetail->customer_middle_name ?? '' }}">
        </div>
                        <div class="col-md-6 mb-3">
                            <label for="customer_contact_number" class="form-label">Contact Number</label>
                            <input type="text" class="form-control" id="customer_contact_number" name="customer_contact_number" 
                                   value="{{ $vehicle->statusDetail->customer_contact_number ?? '' }}">
    </div>
</div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="customer_date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="customer_date_of_birth" name="customer_date_of_birth" 
                                   value="{{ $vehicle->statusDetail->customer_date_of_birth ? $vehicle->statusDetail->customer_date_of_birth->format('Y-m-d') : '' }}">
            </div>
                        <div class="col-md-6 mb-3">
                            <label for="customer_gender" class="form-label">Gender</label>
                            <select class="form-select" id="customer_gender" name="customer_gender">
                                <option value="">Select Gender</option>
                                <option value="Male" {{ ($vehicle->statusDetail->customer_gender ?? '') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ ($vehicle->statusDetail->customer_gender ?? '') == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ ($vehicle->statusDetail->customer_gender ?? '') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="customer_location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="customer_location" name="customer_location" 
                                   value="{{ $vehicle->statusDetail->customer_location ?? '' }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="customer_purpose" class="form-label">Purpose</label>
                            <textarea class="form-control" id="customer_purpose" name="customer_purpose" rows="3">{{ $vehicle->statusDetail->customer_purpose ?? '' }}</textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveCustomerInfo()">
                    <i class="fas fa-save me-1"></i>Save Customer Information
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Status Details Modal -->
<div class="modal fade" id="statusDetailsModal" tabindex="-1" aria-labelledby="statusDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusDetailsModalLabel">Vehicle Reservation Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="statusDetailsForm">
                    @csrf
                    <div class="alert alert-info" id="reservationRequiredAlert" style="display: none;">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Required for Reserved Status:</strong> Please fill in Sale Date, Sales Person Reserved ( S.E ), and Sale Reservation Amount to complete the reservation.
                    </div>
                    
                    <input type="hidden" id="sale_status" name="sale_status" value="Reserved">
                    
                    <div class="row g-1 mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="showroom" class="form-label d-block mb-2">Showroom</label>
                                <select class="form-select" id="showroom" name="showroom">
                                    <option value="">Select Showroom</option>
                                    @foreach($showrooms ?? [] as $showroom)
                                        <option value="{{ $showroom->name }}" {{ ($vehicle->statusDetail->showroom ?? '') == $showroom->name ? 'selected' : '' }}>
                                            {{ $showroom->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sale_date" class="form-label d-block mb-2">Sale Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="sale_date" name="sale_date" value="{{ $vehicle->statusDetail && $vehicle->statusDetail->sale_date ? $vehicle->statusDetail->sale_date->format('Y-m-d') : '' }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row g-1 mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sales_price" class="form-label d-block mb-2">Sales Price</label>
                                <input type="number" class="form-control" id="sales_price" name="sales_price" step="0.01" value="{{ $vehicle->statusDetail->sales_price ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sale_reservation_amount" class="form-label d-block mb-2">Sale Reservation Amount <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="sale_reservation_amount" name="sale_reservation_amount" step="0.01" value="{{ $vehicle->statusDetail->sale_reservation_amount ?? '' }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row g-1 mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sales_person_reserved" class="form-label d-block mb-2">Sales Person Reserved ( S.E ) <span class="text-danger">*</span></label>
                                @include('vehicles.partials._executive_sales_select', [
                                    'executiveAgents' => $executiveAgents ?? collect(),
                                    'selectId' => 'sales_person_reserved',
                                    'name' => 'sales_person_reserved',
                                    'required' => true,
                                    'currentValue' => $vehicle->statusDetail->sales_person_reserved ?? '',
                                ])
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sales_person_release" class="form-label d-block mb-2">Sales Person Released (S.E)</label>
                                @include('vehicles.partials._executive_sales_select', [
                                    'executiveAgents' => $executiveAgents ?? collect(),
                                    'selectId' => 'sales_person_release',
                                    'name' => 'sales_person_release',
                                    'required' => false,
                                    'currentValue' => $vehicle->statusDetail->sales_person_release ?? '',
                                ])
                            </div>
                        </div>
                    </div>

                    <div class="row g-1 mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="good_sales_review" class="form-label d-block mb-2">Good Sales Review</label>
                                <select class="form-select" id="good_sales_review" name="good_sales_review">
                                    <option value="">Select</option>
                                    <option value="1" {{ ($vehicle->statusDetail->good_sales_review ?? null) === true ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ ($vehicle->statusDetail->good_sales_review ?? null) === false ? 'selected' : '' }}>No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cash_financing" class="form-label d-block mb-2">Cash/Financing</label>
                                <select class="form-select" id="cash_financing" name="cash_financing">
                                    <option value="">Select</option>
                                    <option value="Cash" {{ ($vehicle->statusDetail->cash_financing ?? '') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                    <option value="Financing" {{ ($vehicle->statusDetail->cash_financing ?? '') == 'Financing' ? 'selected' : '' }}>Financing</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    @php $statusDetailsFinancing = ($vehicle->statusDetail->cash_financing ?? '') === 'Financing'; @endphp
                    <div id="financing_company_wrap" class="row g-1 mb-3 {{ $statusDetailsFinancing ? '' : 'd-none' }}">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="financing_company" class="form-label d-block mb-2">Financing Company</label>
                                <select class="form-select" id="financing_company" name="financing_company">
                                    <option value="">Select</option>
                                    @foreach(\App\Models\VehicleStatusDetail::financingCompanyOptions() as $finCo)
                                        <option value="{{ $finCo }}" {{ ($vehicle->statusDetail->financing_company ?? '') === $finCo ? 'selected' : '' }}>{{ $finCo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    @php $statusDetailsSaleAgentOrigin = ($vehicle->statusDetail->sale_origin ?? '') === 'Agent'; @endphp
                    <div class="row g-1 mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sale_origin" class="form-label d-block mb-2">Sale Origin</label>
                                @include('vehicles.partials._sale_origin_select', [
                                    'currentValue' => $vehicle->statusDetail->sale_origin ?? '',
                                ])
                            </div>
                        </div>
                    </div>
                    <div id="sale_origin_agent_fields_wrap" class="{{ $statusDetailsSaleAgentOrigin ? '' : 'd-none' }}">
                        <div class="row g-1 mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sales_agent_name" class="form-label d-block mb-2">Sales agent name</label>
                                    @include('vehicles.partials._sales_agent_reservation_select', [
                                        'salesAgentsList' => $salesAgentsList ?? collect(),
                                        'selectId' => 'sales_agent_name',
                                        'name' => 'sales_agent_name',
                                        'required' => false,
                                        'currentValue' => $vehicle->statusDetail->sales_agent_name ?? '',
                                    ])
                                </div>
                            </div>
                        </div>
                        <div class="row g-1 mb-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="agent_cost" class="form-label d-block mb-2">Agent Cost</label>
                                    <input type="number" class="form-control" id="agent_cost" name="agent_cost" step="0.01" value="{{ $vehicle->statusDetail->agent_cost ?? 0 }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="finance_revenue_fields_wrap" class="row g-1 mb-3 {{ $statusDetailsFinancing ? '' : 'd-none' }}">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="finance_revenue_1" class="form-label d-block mb-2">Finance Revenue 1</label>
                                <input type="number" class="form-control" id="finance_revenue_1" name="finance_revenue_1" step="0.01" value="{{ $vehicle->statusDetail->finance_revenue_1 ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="finance_revenue_2" class="form-label d-block mb-2">Finance Revenue 2</label>
                                <input type="number" class="form-control" id="finance_revenue_2" name="finance_revenue_2" step="0.01" value="{{ $vehicle->statusDetail->finance_revenue_2 ?? '' }}">
                            </div>
                        </div>
                    </div>

                    <div class="row g-1 mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="transfer_cost" class="form-label d-block mb-2">Transfer Cost</label>
                                <input type="number" class="form-control" id="transfer_cost" name="transfer_cost" step="0.01" value="{{ $vehicle->statusDetail->transfer_cost ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="days_from_acquisition" class="form-label d-block mb-2">Days from Acquisition to Reservation</label>
                                <input type="text" class="form-control" id="days_from_acquisition" name="days_from_acquisition_display" readonly style="background-color: #e9ecef;" value="@if($vehicle->statusDetail && $vehicle->statusDetail->days_from_acquisition_to_reservation !== null){{ $vehicle->statusDetail->days_from_acquisition_to_reservation }} day{{ $vehicle->statusDetail->days_from_acquisition_to_reservation != 1 ? 's' : '' }}@elseif($vehicle->statusDetail && $vehicle->statusDetail->sale_date && $vehicle->purchase_date)@php $days = $vehicle->purchase_date->diffInDays($vehicle->statusDetail->sale_date); @endphp{{ $days }} day{{ $days != 1 ? 's' : '' }}@endif">
                                <input type="hidden" id="days_from_acquisition_to_reservation" name="days_from_acquisition_to_reservation" value="{{ $vehicle->statusDetail->days_from_acquisition_to_reservation ?? ($vehicle->statusDetail && $vehicle->statusDetail->sale_date && $vehicle->purchase_date ? $vehicle->purchase_date->diffInDays($vehicle->statusDetail->sale_date) : '') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row g-1 mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="has_insurance" name="has_insurance" value="1" {{ ($vehicle->statusDetail->has_insurance ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_insurance">
                                        Insurance
                                    </label>
                                </div>
                                <input type="number" class="form-control" id="insurance_value" name="insurance_value" step="0.01" value="{{ $vehicle->statusDetail->insurance_value ?? '' }}" placeholder="Insurance Value" {{ ($vehicle->statusDetail->has_insurance ?? false) ? '' : 'disabled' }}>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="has_trade_in" name="has_trade_in" value="1" {{ ($vehicle->statusDetail->has_trade_in ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="has_trade_in">
                                        Trade-in
                                    </label>
                                </div>
                                <input type="number" class="form-control" id="trade_in_value" name="trade_in_value" step="0.01" value="{{ $vehicle->statusDetail->trade_in_value ?? '' }}" placeholder="Trade-in Value" {{ ($vehicle->statusDetail->has_trade_in ?? false) ? '' : 'disabled' }}>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveStatusDetails()">Save Status Details</button>
            </div>
        </div>
    </div>
</div>

<!-- Gas Expense Modal -->
<div class="modal fade" id="gasExpenseModal" tabindex="-1" aria-labelledby="gasExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="gasExpenseModalLabel">Vehicle Gas Expenses</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Add New Gas Expense Form -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-plus me-2"></i>Add New Gas Expense
                        </h6>
                    </div>
                    <div class="card-body">
                        <form id="gasExpenseForm">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="date" class="form-label">Date</label>
                                    <input type="date" class="form-control" id="date" name="date" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="driver" class="form-label">Driver</label>
                                    <input type="text" class="form-control" id="driver" name="driver" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="model" class="form-label">Model</label>
                                    <input type="text" class="form-control" id="model" name="model" value="{{ $vehicle->full_name }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="gas_amount" class="form-label">Gas Amount (₱)</label>
                                    <input type="number" class="form-control" id="gas_amount" name="gas_amount" step="0.01" min="0" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="expense_sent_by" class="form-label">Expense Sent By</label>
                                    <select class="form-select" id="expense_sent_by" name="expense_sent_by" required>
                                        <option value="">Select</option>
                                        <option value="MERLIN">MERLIN</option>
                                        <option value="ALYSSA">ALYSSA</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="checked_by" class="form-label">Checked By</label>
                                    <input type="text" class="form-control" id="checked_by" name="checked_by" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="has_photo_video_in_groupchat" name="has_photo_video_in_groupchat" value="1">
                                        <label class="form-check-label" for="has_photo_video_in_groupchat">
                                            Photo/Video in Group Chat
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="photo_fuel_gauge_before" name="photo_fuel_gauge_before" value="1">
                                        <label class="form-check-label" for="photo_fuel_gauge_before">
                                            Photo of Fuel Gauge Before
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="photo_fuel_gauge_after" name="photo_fuel_gauge_after" value="1">
                                        <label class="form-check-label" for="photo_fuel_gauge_after">
                                            Photo of Fuel Gauge After
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="photo_car_license_plate_gas_boy" name="photo_car_license_plate_gas_boy" value="1">
                                        <label class="form-check-label" for="photo_car_license_plate_gas_boy">
                                            Photo of Car with License Plate & Gas Boy
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="photo_receipt_next_to_gas_pump" name="photo_receipt_next_to_gas_pump" value="1">
                                        <label class="form-check-label" for="photo_receipt_next_to_gas_pump">
                                            Photo of Receipt Next to Gas Pump
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <button type="button" class="btn btn-secondary me-2" onclick="resetGasExpenseForm()">Reset</button>
                                <button type="button" class="btn btn-primary" onclick="saveGasExpense()">
                                    <i class="fas fa-save me-1"></i>Save Gas Expense
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Existing Gas Expenses List -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-list me-2"></i>Existing Gas Expenses
                        </h6>
                    </div>
                    <div class="card-body">
                        <div id="gasExpensesList">
                            <!-- Gas expenses will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Post Reservation Expense Modal -->
<div class="modal fade" id="postReservationExpenseModal" tabindex="-1" aria-labelledby="postReservationExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="postReservationExpenseModalLabel">Add Post Reservation Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="postReservationExpenseForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="post_expense_date" class="form-label">Expense Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="post_expense_date" name="expense_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="post_payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select" id="post_payment_method" name="payment_method_id" required>
                                <option value="">Select Payment Method</option>
                                @foreach(\App\Models\PaymentMethod::active()->orderBy('sort_order')->get() as $method)
                                    <option value="{{ $method->id }}">{{ $method->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="post_description" class="form-label">Description <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="post_description" name="description" required placeholder="Enter expense description">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="post_description_details" class="form-label">Notes</label>
                            <textarea class="form-control" id="post_description_details" name="description_details" rows="2" placeholder="Enter additional notes..."></textarea>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="post_cost" class="form-label">Amount (₱) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="post_cost" name="cost" step="0.01" min="0" required placeholder="0.00">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="post_requested_by" class="form-label">Requested By</label>
                            <input type="text" class="form-control" id="post_requested_by" name="requested_by" placeholder="Enter name">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="post_approved_by" class="form-label">Approved By</label>
                            <input type="text" class="form-control" id="post_approved_by" name="approved_by" placeholder="Enter name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="post_store_shop" class="form-label">Store/Shop</label>
                            <input type="text" class="form-control" id="post_store_shop" name="store_shop" placeholder="Enter store/shop name">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="savePostReservationExpense()">
                    <i class="fas fa-save me-1"></i>Save Expense
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Post Release Expense Modal -->
<div class="modal fade" id="postReleaseExpenseModal" tabindex="-1" aria-labelledby="postReleaseExpenseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="postReleaseExpenseModalLabel">Add Post Release Expense</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="postReleaseExpenseForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="release_expense_date" class="form-label">Expense Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="release_expense_date" name="expense_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="release_payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select" id="release_payment_method" name="payment_method_id" required>
                                <option value="">Select Payment Method</option>
                                @foreach(\App\Models\PaymentMethod::active()->orderBy('sort_order')->get() as $method)
                                    <option value="{{ $method->id }}">{{ $method->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="release_description" class="form-label">Description <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="release_description" name="description" required placeholder="Enter expense description">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="release_description_details" class="form-label">Notes</label>
                            <textarea class="form-control" id="release_description_details" name="description_details" rows="2" placeholder="Enter additional notes..."></textarea>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="release_cost" class="form-label">Amount (₱) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="release_cost" name="cost" step="0.01" min="0" required placeholder="0.00">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="release_requested_by" class="form-label">Requested By</label>
                            <input type="text" class="form-control" id="release_requested_by" name="requested_by" placeholder="Enter name">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="release_approved_by" class="form-label">Approved By</label>
                            <input type="text" class="form-control" id="release_approved_by" name="approved_by" placeholder="Enter name">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="release_store_shop" class="form-label">Store/Shop</label>
                            <input type="text" class="form-control" id="release_store_shop" name="store_shop" placeholder="Enter store/shop name">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="savePostReleaseExpense()">
                    <i class="fas fa-save me-1"></i>Save Expense
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Vehicle Ad Modal -->
<div class="modal fade" id="vehicleAdModal" tabindex="-1" aria-labelledby="vehicleAdModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vehicleAdModalLabel">Add Video Ad Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="vehicleAdForm">
                    @csrf
                    <input type="hidden" id="vehicle_ad_id" name="vehicle_ad_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ad_posted_date" class="form-label">Posted Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="ad_posted_date" name="posted_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    
                    @include('partials.vehicle-ad-multi-links-fields', ['prefix' => 'ad_'])
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="ad_ads_boost_link" class="form-label">Link to Ads or Boost</label>
                            <input type="url" class="form-control" id="ad_ads_boost_link" name="ads_boost_link" placeholder="https://...">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ad_campaign_id" class="form-label">Campaign ID</label>
                            <input type="text" class="form-control" id="ad_campaign_id" name="campaign_id" placeholder="Enter campaign ID">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ad_ad_id" class="form-label">Ad ID</label>
                            <input type="text" class="form-control" id="ad_ad_id" name="ad_id" placeholder="Enter ad ID">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveVehicleAd()">
                    <i class="fas fa-save me-1"></i>Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Forfeit Details Modal -->
<div class="modal fade" id="forfeitDetailsModal" tabindex="-1" aria-labelledby="forfeitDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="forfeitDetailsModalLabel">Add Forfeit Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="forfeitDetailsForm">
                    @csrf
                    <div class="mb-3">
                        <label for="previous_forfeit_date" class="form-label">Previous Forfeit</label>
                        <input type="date" class="form-control" id="previous_forfeit_date" name="previous_forfeit_date">
                    </div>
                    <div class="mb-3">
                        <label for="forfeit_amount" class="form-label">Forfeit Amount <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="forfeit_amount" name="forfeit_amount" step="0.01" min="0" placeholder="0.00" required>
                    </div>
                    <div class="mb-3">
                        <label for="forfeit_date" class="form-label">Forfeit Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="forfeit_date" name="forfeit_date" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveForfeitDetail()">
                    <i class="fas fa-save me-1"></i>Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Posted Price Modal -->
<div class="modal fade" id="postedPriceModal" tabindex="-1" aria-labelledby="postedPriceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="postedPriceModalLabel">Add/Edit Posted Price</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="postedPriceForm">
                    @csrf
                    <div class="mb-3">
                        <label for="posted_price_input" class="form-label">Posted Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control" id="posted_price_input" name="posted_price" step="0.01" min="0" required placeholder="Enter posted price">
                        </div>
                        <small class="form-text text-muted">Enter the posted price for this vehicle</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="savePostedPrice()">
                    <i class="fas fa-save me-1"></i>Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Sold Price Modal -->
<div class="modal fade" id="soldPriceModal" tabindex="-1" aria-labelledby="soldPriceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="soldPriceModalLabel">Add/Edit Sold Price</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="soldPriceForm">
                    @csrf
                    <div class="mb-3">
                        <label for="sold_price_input" class="form-label">Sold Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control" id="sold_price_input" name="sold_price" step="0.01" min="0" required placeholder="Enter sold price">
                        </div>
                        <small class="form-text text-muted">Enter the sold price for this vehicle</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveSoldPrice()">
                    <i class="fas fa-save me-1"></i>Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Document Action Modal -->
<div class="modal fade" id="documentActionModal" tabindex="-1" aria-labelledby="documentActionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentActionModalLabel">Document Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">What would you like to do with <strong id="modalDocumentLabel"></strong>?</p>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-success btn-lg" id="markCompletedBtn">
                        <i class="fas fa-check-circle me-2"></i>Mark as Completed
                    </button>
                    <button type="button" class="btn btn-primary btn-lg" id="addDetailsBtn">
                        <i class="fas fa-edit me-2"></i>Add Details
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Document checkbox functionality
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.document-checkbox');
        const modalElement = document.getElementById('documentActionModal');
        if (!modalElement) return;
        
        const modal = new bootstrap.Modal(modalElement);
        let currentCheckbox = null;
        let currentDocumentType = null;
        let currentDocumentId = null;
        let currentVehicleId = null;

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // Only show modal when checking (not unchecking)
                if (this.checked) {
                    currentCheckbox = this;
                    currentDocumentType = this.getAttribute('data-document-type');
                    currentDocumentId = this.getAttribute('data-document-id');
                    currentVehicleId = this.getAttribute('data-vehicle-id');
                    const documentLabel = this.getAttribute('data-document-label');
                    
                    // Set modal label
                    document.getElementById('modalDocumentLabel').textContent = documentLabel;
                    
                    // Show modal
                    modal.show();
                } else {
                    // If unchecking, mark as incomplete if document exists
                    const documentId = this.getAttribute('data-document-id');
                    if (documentId) {
                        markDocumentIncomplete(documentId, this);
                    } else {
                        // If no document exists, just uncheck
                        this.checked = false;
                    }
                }
            });
        });

        // Mark as Completed button
        const markCompletedBtn = document.getElementById('markCompletedBtn');
        if (markCompletedBtn) {
            markCompletedBtn.addEventListener('click', function() {
                modal.hide();
                
                if (currentDocumentId) {
                    // Document exists, mark as completed
                    markDocumentCompleted(currentDocumentId, currentCheckbox);
                } else {
                    // Document doesn't exist, create and mark as completed
                    markNewDocumentCompleted(currentVehicleId, currentDocumentType, currentCheckbox);
                }
            });
        }

        // Add Details button
        const addDetailsBtn = document.getElementById('addDetailsBtn');
        if (addDetailsBtn) {
            addDetailsBtn.addEventListener('click', function() {
                modal.hide();
                
                // Redirect to add details page
                const url = `/vehicles/${currentVehicleId}/documents/${currentDocumentType}/add-details`;
                window.location.href = url;
            });
        }

        // Function to clean up modal backdrop
        function cleanupModalBackdrop() {
            setTimeout(() => {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }, 100);
        }
        
        // Reset checkbox state when modal is closed without action
        modalElement.addEventListener('hidden.bs.modal', function() {
            cleanupModalBackdrop();
            
            if (currentCheckbox && !currentCheckbox.hasAttribute('data-completed')) {
                currentCheckbox.checked = false;
            }
        });
        
        // Handle when modal starts to hide
        modalElement.addEventListener('hide.bs.modal', function() {
            cleanupModalBackdrop();
        });
        
        // Handle cancel and close buttons explicitly
        const cancelBtn = modalElement.querySelector('[data-bs-dismiss="modal"]');
        const closeBtn = modalElement.querySelector('.btn-close');
        
        if (cancelBtn) {
            cancelBtn.addEventListener('click', cleanupModalBackdrop);
        }
        
        if (closeBtn) {
            closeBtn.addEventListener('click', cleanupModalBackdrop);
        }
    });

    // Mark document as completed
    function markDocumentCompleted(documentId, checkbox) {
        fetch(`/vehicles/{{ $vehicle->id }}/documents/${documentId}/mark-completed`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (checkbox) {
                    checkbox.setAttribute('data-is-completed', '1');
                    checkbox.setAttribute('data-document-id', documentId);
                }
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Document marked as completed!',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to mark document as completed.'
            });
            if (checkbox) {
                checkbox.checked = false;
            }
        });
    }

    // Mark new document as completed
    function markNewDocumentCompleted(vehicleId, documentType, checkbox) {
        fetch(`/vehicles/${vehicleId}/documents/mark-completed`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                document_type: documentType,
                process_type: 'ACQUISITION'
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Failed to mark document as completed');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (checkbox) {
                    checkbox.setAttribute('data-is-completed', '1');
                    checkbox.setAttribute('data-document-id', data.document_id);
                }
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Document marked as completed!',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                throw new Error(data.message || 'Failed to mark document as completed');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Failed to mark document as completed.'
            });
            if (checkbox) {
                checkbox.checked = false;
            }
        });
    }

    // Mark document as incomplete
    function markDocumentIncomplete(documentId, checkbox) {
        fetch(`/vehicles/{{ $vehicle->id }}/documents/${documentId}/mark-incomplete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (checkbox) {
                    checkbox.setAttribute('data-is-completed', '0');
                }
                Swal.fire({
                    icon: 'info',
                    title: 'Success!',
                    text: 'Document marked as incomplete!',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to mark document as incomplete.'
            });
            if (checkbox) {
                checkbox.checked = true;
            }
        });
    }
</script>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
function destroyVehicleExecutiveTomSelect(selectEl) {
    if (selectEl && selectEl.tomselect) {
        try { selectEl.tomselect.destroy(); } catch (e) {}
    }
}
function initVehicleExecutiveTomSelects(container) {
    if (typeof TomSelect === 'undefined' || !container) return;
    container.querySelectorAll('select.ts-executive-select, select.ts-sales-agent-select').forEach(function(sel) {
        destroyVehicleExecutiveTomSelect(sel);
        new TomSelect(sel, {
            create: false,
            allowEmptyOption: true,
            placeholder: sel.getAttribute('data-placeholder') || 'Search…',
            sortField: { field: 'text', direction: 'asc' },
            dropdownParent: 'body',
        });
    });
}
// Page permission flags for edit/delete buttons (e.g. gas expenses table)
window.vehiclesCanUpdate = {{ auth()->user()->canAccessPage('vehicles', 'update') ? 'true' : 'false' }};
window.vehiclesCanDelete = {{ auth()->user()->canAccessPage('vehicles', 'delete') ? 'true' : 'false' }};
// Sidebar Toggle Functionality

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

// Open accordion when URL hash matches (e.g. from Follow Up Documents, Car Video Boost Report, or Agent Details)
function openCollapseFromHash() {
    const hash = window.location.hash;
    if (hash && typeof bootstrap !== 'undefined') {
        const collapseEl = document.getElementById(hash.replace('#', ''));
        if (collapseEl) {
            const collapse = bootstrap.Collapse.getOrCreateInstance(collapseEl, { toggle: false });
            collapse.show();
        }
    }
}
document.addEventListener('DOMContentLoaded', openCollapseFromHash);
window.addEventListener('hashchange', openCollapseFromHash);

// Delete vehicle function
function archiveVehicle() {
    const form = document.getElementById('archiveVehicleForm');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    Swal.fire({
        title: 'Archive this unit?',
        text: 'Move "{{ $vehicle->full_name }}" to Archived? It will no longer appear in active status tabs.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#6c757d',
        cancelButtonColor: '#adb5bd',
        confirmButtonText: 'Yes, archive',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (!result.isConfirmed || !form) {
            return;
        }

        Swal.fire({
            title: 'Archiving…',
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => { Swal.showLoading(); }
        });

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.json().then(data => {
                if (!response.ok || !data.success) {
                    throw data;
                }
                return data;
            }))
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: data.swal_title || 'Archived',
                    text: data.message,
                    confirmButtonColor: '#198754',
                    timer: 2500,
                    timerProgressBar: true
                }).then(() => {
                    window.location.href = @json(route('vehicles.index', ['status' => 'Archived']));
                });
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: error.swal_title || 'Error',
                    text: error.message || 'Could not archive this vehicle.',
                    confirmButtonColor: '#dc3545'
                });
            });
    });
}

function deleteVehicle() {
    Swal.fire({
        title: 'Delete Vehicle?',
        text: 'Are you sure you want to delete "{{ $vehicle->full_name }}"? This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Deleting Vehicle...',
                text: 'Please wait while we delete the vehicle.',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Submit the delete form
            document.getElementById('deleteForm').submit();
        }
    });
}

// Status management functions
function openSaleSummaryModal() {
    const modal = new bootstrap.Modal(document.getElementById('saleSummaryModal'));
    modal.show();
}

function openStatusModal() {
    const modal = new bootstrap.Modal(document.getElementById('statusModal'));
    modal.show();
}

function updateStatus() {
    const status = document.getElementById('newStatus').value;
    
    if (!status) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Please select a status',
            confirmButtonColor: '#dc3545'
        });
        return;
    }

    // If status is Reserved, require reservation details first
    if (status === 'Reserved') {
        // Close status modal
        const statusModal = bootstrap.Modal.getInstance(document.getElementById('statusModal'));
        if (statusModal) {
            statusModal.hide();
        }
        
        // Set sale_status to Reserved in the reservation details form
        document.getElementById('sale_status').value = 'Reserved';
        
        // Show reservation details modal with required alert
        const reservationAlert = document.getElementById('reservationRequiredAlert');
        if (reservationAlert) {
            reservationAlert.style.display = 'block';
        }
        
        // Open reservation details modal
        const reservationModal = new bootstrap.Modal(document.getElementById('statusDetailsModal'));
        reservationModal.show();
        
        // Focus on first required field
        setTimeout(() => {
            const saleDateField = document.getElementById('sale_date');
            if (saleDateField) {
                saleDateField.focus();
            }
        }, 300);
        
        return;
    }

    Swal.fire({
        title: 'Update Status?',
        text: `Are you sure you want to change the status to "${status}"?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Update!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Updating Status...',
                text: 'Please wait while we update the status.',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send AJAX request
            fetch(`{{ route('vehicles.status.update', $vehicle) }}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: status })
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
                        location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Failed to update status');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.message || 'Failed to update status',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
}

function openCustomerInfoModal() {
    const modal = new bootstrap.Modal(document.getElementById('customerInfoModal'));
    modal.show();
}

function saveCustomerInfo() {
    const form = document.getElementById('customerInfoForm');
    const formData = new FormData(form);
    
    // Show loading
    Swal.fire({
        title: 'Saving...',
        text: 'Please wait while we save the customer information.',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Get existing status detail to preserve other fields
    const existingStatusDetail = @json($vehicle->statusDetail);
    const data = {
        sale_status: existingStatusDetail?.sale_status || '{{ $vehicle->status }}',
        customer_first_name: formData.get('customer_first_name'),
        customer_last_name: formData.get('customer_last_name'),
        customer_middle_name: formData.get('customer_middle_name'),
        customer_contact_number: formData.get('customer_contact_number'),
        customer_date_of_birth: formData.get('customer_date_of_birth'),
        customer_gender: formData.get('customer_gender'),
        customer_location: formData.get('customer_location'),
        customer_purpose: formData.get('customer_purpose'),
    };
    
    // Preserve existing status detail fields if they exist
    if (existingStatusDetail) {
        data.showroom = existingStatusDetail.showroom || '';
        data.sale_date = existingStatusDetail.sale_date || '';
        data.sales_price = existingStatusDetail.sales_price || '';
        data.sale_reservation_amount = existingStatusDetail.sale_reservation_amount || '';
        data.sales_person_reserved = existingStatusDetail.sales_person_reserved || '';
        data.sales_person_release = existingStatusDetail.sales_person_release || '';
        data.good_sales_review = existingStatusDetail.good_sales_review;
        data.cash_financing = existingStatusDetail.cash_financing || '';
        data.financing_company = existingStatusDetail.financing_company || '';
        data.sale_origin = existingStatusDetail.sale_origin || '';
        data.sales_agent_name = existingStatusDetail.sales_agent_name || '';
        data.agent_cost = existingStatusDetail.agent_cost || '';
        data.finance_revenue_1 = existingStatusDetail.finance_revenue_1 || '';
        data.finance_revenue_2 = existingStatusDetail.finance_revenue_2 || '';
        data.transfer_cost = existingStatusDetail.transfer_cost || '';
    }
    
    // Send AJAX request
    fetch(`{{ route('vehicles.status-details.store', $vehicle) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Customer information saved successfully!',
                confirmButtonColor: '#28a745',
                timer: 2000,
                timerProgressBar: true
            }).then(() => {
                location.reload();
            });
        } else {
            throw new Error(data.message || 'Failed to save customer information');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.message || 'Failed to save customer information',
            confirmButtonColor: '#dc3545'
        });
    });
}

function openStatusDetailsModal() {
    // Reset form state when opening normally (not from reserve button)
    const alert = document.getElementById('reservationRequiredAlert');
    if (alert) {
        alert.style.display = 'none';
    }
    const modal = new bootstrap.Modal(document.getElementById('statusDetailsModal'));
    modal.show();
}

function openReserveVehicleModal() {
    // First open the modal
    const modalElement = document.getElementById('statusDetailsModal');
    if (!modalElement) {
        console.error('Status details modal not found');
        return;
    }
    
    const modal = new bootstrap.Modal(modalElement);
    modal.show();
    
    // Wait for modal to be fully shown before manipulating elements
    modalElement.addEventListener('shown.bs.modal', function onModalShown() {
        // Show the required alert
        const alert = document.getElementById('reservationRequiredAlert');
        if (alert) {
            alert.style.display = 'block';
        }
        
        // Make required fields visible and add asterisks
        const requiredFields = ['sale_date', 'sale_reservation_amount', 'sales_person_reserved'];
        requiredFields.forEach(fieldId => {
            const field = document.getElementById(fieldId);
            if (field) {
                field.required = true;
                // Find the label
                const row = field.closest('.row');
                if (row) {
                    const col = field.closest('.col-md-6');
                    if (col) {
                        const label = col.querySelector('label');
                        if (label && !label.innerHTML.includes('*')) {
                            label.innerHTML += ' <span class="text-danger">*</span>';
                        }
                    }
                }
            }
        });
        
        // Remove the event listener to avoid multiple calls
        modalElement.removeEventListener('shown.bs.modal', onModalShown);
    }, { once: true });
}

function saveStatusDetails() {
    const form = document.getElementById('statusDetailsForm');
    const formData = new FormData(form);
    
    // Sale status is always Reserved (fixed)
    const saleStatus = 'Reserved';
    
    // Validate required fields for Reserved status
    const saleDate = formData.get('sale_date');
    const salesPersonReserved = formData.get('sales_person_reserved');
    const saleReservationAmount = formData.get('sale_reservation_amount');
    
    if (!saleDate || !salesPersonReserved || !saleReservationAmount) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            html: 'Please fill in all required fields:<br>' +
                  (!saleDate ? '• Sale Date<br>' : '') +
                  (!salesPersonReserved ? '• Sales Person Reserved ( S.E )<br>' : '') +
                  (!saleReservationAmount ? '• Sale Reservation Amount' : ''),
            confirmButtonColor: '#dc3545'
        });
        return;
    }
    
    // Validate form
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Convert FormData to JSON
    const data = {
        sale_status: saleStatus // Always set to Reserved
    };
    for (let [key, value] of formData.entries()) {
        if (key === 'good_sales_review') {
            data[key] = value === '1' ? true : (value === '0' ? false : null);
        } else if (key === 'has_insurance') {
            data[key] = document.getElementById('has_insurance').checked;
        } else if (key === 'has_trade_in') {
            data[key] = document.getElementById('has_trade_in').checked;
        } else if (key === 'days_from_acquisition_display') {
            // Skip the display field, use the hidden field instead
            continue;
        } else if (key === 'sales_agent_name' || key === 'financing_company') {
            data[key] = value;
        } else if (key === 'sale_origin') {
            data[key] = value === '' ? null : value;
        } else if (key !== 'sale_status' && value !== '') {
            data[key] = value;
        }
    }
    
    // Add days_from_acquisition_to_reservation if computed
    const daysFromAcquisition = document.getElementById('days_from_acquisition_to_reservation').value;
    if (daysFromAcquisition) {
        data['days_from_acquisition_to_reservation'] = parseInt(daysFromAcquisition);
    }

    Swal.fire({
        title: 'Save Status Details?',
        text: 'Are you sure you want to save these status details?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Save!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Saving Status Details...',
                text: 'Please wait while we save the details.',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send AJAX request
            fetch(`{{ route('vehicles.status-details.store', $vehicle) }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Failed to save status details');
                    });
                }
                return response.json();
            })
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
                        location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Failed to save status details');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.message || 'Failed to save status details',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
}

// Auto-compute days from acquisition to reservation
function calculateDaysFromAcquisition() {
    const purchaseDateStr = '{{ $vehicle->purchase_date ? $vehicle->purchase_date->format("Y-m-d") : "" }}';
    const saleDateInput = document.getElementById('sale_date');
    const daysDisplay = document.getElementById('days_from_acquisition');
    const daysHidden = document.getElementById('days_from_acquisition_to_reservation');
    
    if (!purchaseDateStr || !saleDateInput.value) {
        daysDisplay.value = '';
        daysHidden.value = '';
        return;
    }
    
    const purchaseDate = new Date(purchaseDateStr);
    const saleDate = new Date(saleDateInput.value);
    
    if (isNaN(purchaseDate.getTime()) || isNaN(saleDate.getTime())) {
        daysDisplay.value = '';
        daysHidden.value = '';
        return;
    }
    
    // Calculate difference in days
    const diffTime = saleDate - purchaseDate;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays >= 0) {
        daysDisplay.value = diffDays + ' day' + (diffDays !== 1 ? 's' : '');
        daysHidden.value = diffDays;
    } else {
        daysDisplay.value = 'Invalid date range';
        daysHidden.value = '';
    }
}

// Handle insurance checkbox
function toggleInsuranceInput() {
    const checkbox = document.getElementById('has_insurance');
    const input = document.getElementById('insurance_value');
    input.disabled = !checkbox.checked;
    if (!checkbox.checked) {
        input.value = '';
    }
}

// Handle trade-in checkbox
function toggleTradeInInput() {
    const checkbox = document.getElementById('has_trade_in');
    const input = document.getElementById('trade_in_value');
    input.disabled = !checkbox.checked;
    if (!checkbox.checked) {
        input.value = '';
    }
}

function syncCashFinancingFields(root) {
    if (!root) return;
    const sel = root.querySelector('#cash_financing');
    const wrapCompany = root.querySelector('#financing_company_wrap');
    const wrapRevenue = root.querySelector('#finance_revenue_fields_wrap');
    if (!sel || !wrapCompany || !wrapRevenue) return;
    const isFinancing = sel.value === 'Financing';
    wrapCompany.classList.toggle('d-none', !isFinancing);
    wrapRevenue.classList.toggle('d-none', !isFinancing);
}

function syncSaleOriginAgentFields(root) {
    if (!root) return;
    const sel = root.querySelector('#sale_origin');
    const wrap = root.querySelector('#sale_origin_agent_fields_wrap');
    if (!sel || !wrap) return;
    const isAgent = sel.value === 'Agent';
    wrap.classList.toggle('d-none', !isAgent);
    if (!isAgent) {
        const agentNameSelect = root.querySelector('#sales_agent_name');
        if (agentNameSelect) {
            if (agentNameSelect.tomselect) {
                try { agentNameSelect.tomselect.clear(); } catch (e) {}
            }
            agentNameSelect.value = '';
        }
        const agentCost = root.querySelector('#agent_cost');
        if (agentCost) {
            agentCost.value = '0';
        }
    }
}

// Initialize event listeners when modal is shown
document.addEventListener('DOMContentLoaded', function() {
    const statusDetailsModal = document.getElementById('statusDetailsModal');
    if (statusDetailsModal) {
        syncCashFinancingFields(statusDetailsModal);
        syncSaleOriginAgentFields(statusDetailsModal);
        statusDetailsModal.addEventListener('shown.bs.modal', function() {
            initVehicleExecutiveTomSelects(statusDetailsModal);
            syncCashFinancingFields(statusDetailsModal);
            syncSaleOriginAgentFields(statusDetailsModal);
            const cashFinancingSelect = statusDetailsModal.querySelector('#cash_financing');
            if (cashFinancingSelect && !cashFinancingSelect.dataset.financingToggleBound) {
                cashFinancingSelect.dataset.financingToggleBound = '1';
                cashFinancingSelect.addEventListener('change', function() {
                    syncCashFinancingFields(statusDetailsModal);
                    if (this.value !== 'Financing') {
                        const fc = statusDetailsModal.querySelector('#financing_company');
                        if (fc) fc.value = '';
                    }
                });
            }
            const saleOriginSelect = statusDetailsModal.querySelector('#sale_origin');
            if (saleOriginSelect && !saleOriginSelect.dataset.saleOriginToggleBound) {
                saleOriginSelect.dataset.saleOriginToggleBound = '1';
                saleOriginSelect.addEventListener('change', function() {
                    syncSaleOriginAgentFields(statusDetailsModal);
                });
            }
            // Set up sale date change listener
            const saleDateInput = document.getElementById('sale_date');
            if (saleDateInput) {
                saleDateInput.addEventListener('change', calculateDaysFromAcquisition);
                saleDateInput.addEventListener('input', calculateDaysFromAcquisition);
                // Calculate on modal open if sale date already has a value
                calculateDaysFromAcquisition();
            }
            
            // Set up checkbox listeners
            const insuranceCheckbox = document.getElementById('has_insurance');
            if (insuranceCheckbox) {
                insuranceCheckbox.addEventListener('change', toggleInsuranceInput);
                toggleInsuranceInput(); // Initialize state
            }
            
            const tradeInCheckbox = document.getElementById('has_trade_in');
            if (tradeInCheckbox) {
                tradeInCheckbox.addEventListener('change', toggleTradeInInput);
                toggleTradeInInput(); // Initialize state
            }
        });
        statusDetailsModal.addEventListener('hidden.bs.modal', function() {
            statusDetailsModal.querySelectorAll('select.ts-executive-select, select.ts-sales-agent-select').forEach(destroyVehicleExecutiveTomSelect);
        });
    }

    const releaseVehicleModalEl = document.getElementById('releaseVehicleModal');
    if (releaseVehicleModalEl) {
        releaseVehicleModalEl.addEventListener('shown.bs.modal', function() {
            initVehicleExecutiveTomSelects(releaseVehicleModalEl);
        });
        releaseVehicleModalEl.addEventListener('hidden.bs.modal', function() {
            releaseVehicleModalEl.querySelectorAll('select.ts-executive-select, select.ts-sales-agent-select').forEach(destroyVehicleExecutiveTomSelect);
        });
    }
});

function releaseVehicle() {
    Swal.fire({
        title: 'Release Vehicle?',
        html: 'Are you sure you want to release this vehicle?<br><br>' +
              'This will change the vehicle status from <strong>Reserved</strong> to <strong>Released</strong>.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Release!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Open the release vehicle form modal
            const modal = new bootstrap.Modal(document.getElementById('releaseVehicleModal'));
            modal.show();
            
            // Calculate days from reservation when modal is shown
            const modalElement = document.getElementById('releaseVehicleModal');
            modalElement.addEventListener('shown.bs.modal', function onModalShown() {
                calculateDaysFromReservation();
                // Reload release documents if needed
                loadReleaseDocuments();
                modalElement.removeEventListener('shown.bs.modal', onModalShown);
            }, { once: true });
        }
    });
}

function loadReleaseDocuments() {
    // Fetch release documents via AJAX to ensure they're up to date
    fetch(`{{ route('vehicles.show', $vehicle) }}?load_release_docs=1`)
        .then(response => response.text())
        .then(html => {
            // The documents are already loaded in the modal, no need to update
            // This function can be used for future dynamic loading if needed
        })
        .catch(error => {
            console.error('Error loading release documents:', error);
        });
}

function saveReleaseVehicle() {
    const form = document.getElementById('releaseVehicleForm');
    const formData = new FormData(form);
    
    // Validate required fields
    if (!formData.get('sales_person_release') || !formData.get('good_sales_review')) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Please fill in all required fields: Sales Person Released (S.E) and Good Sales Review.',
            confirmButtonColor: '#dc3545'
        });
        return;
    }
    
    // Get existing status detail to preserve other fields
    const existingStatusDetail = @json($vehicle->statusDetail);
    const data = {
        sale_status: 'Released',
        sales_person_release: formData.get('sales_person_release'),
        good_sales_review: formData.get('good_sales_review') === '1' ? true : (formData.get('good_sales_review') === '0' ? false : null),
    };
    
    // Check if customer info exists
    const hasCustomerInfo = {{ ($vehicle->statusDetail && ($vehicle->statusDetail->customer_first_name || $vehicle->statusDetail->customer_last_name || $vehicle->statusDetail->customer_contact_number)) ? 'true' : 'false' }};
    
    // Add customer information if form fields exist (only if customer info doesn't exist)
    if (!hasCustomerInfo) {
        data.customer_first_name = formData.get('customer_first_name') || '';
        data.customer_last_name = formData.get('customer_last_name') || '';
        data.customer_middle_name = formData.get('customer_middle_name') || '';
        data.customer_contact_number = formData.get('customer_contact_number') || '';
        data.customer_date_of_birth = formData.get('customer_date_of_birth') || '';
        data.customer_gender = formData.get('customer_gender') || '';
        data.customer_location = formData.get('customer_location') || '';
        data.customer_purpose = formData.get('customer_purpose') || '';
    }
    
    // Preserve existing status detail fields
    if (existingStatusDetail) {
        data.showroom = existingStatusDetail.showroom || '';
        data.sale_date = existingStatusDetail.sale_date || '';
        data.sales_price = existingStatusDetail.sales_price || '';
        data.sale_reservation_amount = existingStatusDetail.sale_reservation_amount || '';
        data.sales_person_reserved = existingStatusDetail.sales_person_reserved || '';
        data.cash_financing = existingStatusDetail.cash_financing || '';
        data.financing_company = existingStatusDetail.financing_company || '';
        data.sale_origin = existingStatusDetail.sale_origin || '';
        data.sales_agent_name = existingStatusDetail.sales_agent_name || '';
        data.agent_cost = existingStatusDetail.agent_cost || '';
        data.finance_revenue_1 = existingStatusDetail.finance_revenue_1 || '';
        data.finance_revenue_2 = existingStatusDetail.finance_revenue_2 || '';
        data.transfer_cost = existingStatusDetail.transfer_cost || '';
        
        // Preserve customer info if it exists
        if (hasCustomerInfo) {
            data.customer_first_name = existingStatusDetail.customer_first_name || '';
            data.customer_last_name = existingStatusDetail.customer_last_name || '';
            data.customer_middle_name = existingStatusDetail.customer_middle_name || '';
            data.customer_contact_number = existingStatusDetail.customer_contact_number || '';
            data.customer_date_of_birth = existingStatusDetail.customer_date_of_birth || '';
            data.customer_gender = existingStatusDetail.customer_gender || '';
            data.customer_location = existingStatusDetail.customer_location || '';
            data.customer_purpose = existingStatusDetail.customer_purpose || '';
        }
    }
    
    // Show loading
    Swal.fire({
        title: 'Releasing Vehicle...',
        text: 'Please wait while we save the release details.',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Send AJAX request
    fetch(`{{ route('vehicles.status-details.store', $vehicle) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(async response => {
        console.log('Response status:', response.status);
        console.log('Response URL:', response.url);
        
        if (!response.ok) {
            let errorMessage = `HTTP ${response.status}: Failed to release vehicle.`;
            try {
                const errorData = await response.json();
                errorMessage = errorData.message || errorMessage;
                console.error('Error response:', errorData);
            } catch (parseError) {
                const text = await response.text();
                console.error('Error response text:', text.substring(0, 500));
            }
            throw new Error(errorMessage);
        }
        
        const contentType = response.headers.get('content-type') || '';
        if (contentType.includes('application/json')) {
            return response.json();
        } else {
            const text = await response.text();
            console.error('Non-JSON response:', text.substring(0, 500));
            throw new Error('Server returned an invalid response. Please try again.');
        }
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Vehicle has been released successfully!',
                confirmButtonColor: '#28a745',
                timer: 2000,
                timerProgressBar: true
            }).then(() => {
                location.reload();
            });
        } else {
            throw new Error(data.message || 'Failed to release vehicle');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.message || 'Failed to release vehicle. Please try again.',
            confirmButtonColor: '#dc3545'
        });
    });
}

// Old releaseVehicle function (kept for reference but replaced above)
function releaseVehicleOld() {
    Swal.fire({
        title: 'Release Vehicle?',
        html: 'Are you sure you want to release this vehicle?<br><br>' +
              'This will change the vehicle status from <strong>Reserved</strong> to <strong>Released</strong>.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Release!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Releasing...',
                text: 'Please wait while we release the vehicle.',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Update vehicle status to Released
            fetch(`{{ route('vehicles.status.update', $vehicle) }}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: 'Released' })
            })
            .then(async response => {
                if (!response.ok) {
                    try {
                        const data = await response.json();
                        throw new Error(data.message || 'Failed to release vehicle');
                    } catch (parseError) {
                        throw new Error(`HTTP ${response.status}: Failed to release vehicle. Please try again.`);
                    }
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Also update status details
                    return fetch(`{{ route('vehicles.status-details.store', $vehicle) }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ sale_status: 'Released' })
                    });
                } else {
                    throw new Error(data.message || 'Failed to release vehicle');
                }
            })
            .then(async response => {
                if (!response.ok) {
                    const data = await response.json();
                    throw new Error(data.message || 'Failed to update status details');
                }
                return response.json();
            })
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Released!',
                    text: 'Vehicle has been released successfully!',
                    confirmButtonColor: '#28a745',
                    timer: 2000,
                    timerProgressBar: true
                }).then(() => {
                    location.reload();
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.message || 'Failed to release vehicle. Please try again.',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
}

function deleteReservationDetails() {
    const statusMessage = '{{ $vehicle->status }}' === 'Reserved' 
        ? '<br><br>This will also change the vehicle status from <strong>Reserved</strong> to <strong>Available</strong>.' 
        : '';
    
    Swal.fire({
        title: 'Delete Reservation Details?',
        html: 'Are you sure you want to delete the vehicle reservation details?<br><br>' +
              '<span class="text-danger"><strong>This action cannot be undone!</strong></span>' +
              statusMessage,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait while we delete the reservation details.',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(`{{ route('vehicles.status-details.delete', $vehicle) }}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(async response => {
                if (!response.ok) {
                    try {
                        const data = await response.json();
                        throw new Error(data.message || 'Failed to delete reservation details');
                    } catch (parseError) {
                        throw new Error(`HTTP ${response.status}: Failed to delete reservation details. Please try again.`);
                    }
                }
                return response.json();
            })
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
                    throw new Error(data.message || 'Failed to delete reservation details');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.message || 'Failed to delete reservation details. Please try again.',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
}

// Image management functions
function openImageUploadModal() {
    const modal = new bootstrap.Modal(document.getElementById('imageUploadModal'));
    modal.show();
    
    // Reset form
    document.getElementById('imageUploadForm').reset();
    document.getElementById('imagePreview').innerHTML = '';
}

function uploadImages() {
    const formData = new FormData();
    const images = document.getElementById('images').files;
    
    if (images.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'No Images Selected',
            text: 'Please select at least one image to upload.',
            confirmButtonColor: '#dc3545'
        });
        return;
    }
    
    // Add images to form data
    for (let i = 0; i < images.length; i++) {
        formData.append('images[]', images[i]);
    }
    
    // Show loading
    Swal.fire({
        title: 'Uploading Images...',
        text: 'Please wait while we upload your images.',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Upload images
    fetch('{{ route("vehicles.images.store", $vehicle) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                confirmButtonColor: '#28a745'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Upload Failed',
                text: data.message || 'Unknown error occurred',
                confirmButtonColor: '#dc3545'
            });
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Upload Failed',
            text: 'An error occurred while uploading images. Check console for details.',
            confirmButtonColor: '#dc3545'
        });
    });
}

function setPrimaryImage(imageId) {
    Swal.fire({
        title: 'Set as Primary?',
        text: 'This image will be set as the primary image for this vehicle.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Set Primary!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ route('vehicles.images.primary', [$vehicle, '']) }}/${imageId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: data.message,
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
}

function deleteImage(imageId) {
    Swal.fire({
        title: 'Delete Image?',
        text: 'Are you sure you want to delete this image? This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ route('vehicles.images.destroy', [$vehicle, '']) }}/${imageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: data.message,
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
}

// Image preview functionality
document.getElementById('images').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    for (let i = 0; i < e.target.files.length; i++) {
        const file = e.target.files[i];
        const reader = new FileReader();
        
        reader.onload = function(e) {
            const col = document.createElement('div');
            col.className = 'col-md-4 mb-2';
            col.innerHTML = `
                <div class="card">
                    <img src="${e.target.result}" class="card-img-top" style="height: 100px; object-fit: cover;">
                    <div class="card-body p-2">
                        <small class="text-muted">${file.name}</small>
                    </div>
                </div>
            `;
            preview.appendChild(col);
        };
        
        reader.readAsDataURL(file);
    }
});

// Gas Expense Management Functions
function openGasExpenseModal() {
    const modal = new bootstrap.Modal(document.getElementById('gasExpenseModal'));
    modal.show();
    
    // Load existing gas expenses
    loadGasExpenses();
    
    // Set today's date as default
    document.getElementById('date').value = new Date().toISOString().split('T')[0];
    
}

function loadGasExpenses() {
    fetch(`{{ route('vehicles.gas-expenses.index', $vehicle) }}`, {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Response text:', text);
                throw new Error('Invalid JSON response from server');
            }
        });
    })
    .then(data => {
        if (data.success) {
            displayGasExpenses(data.gasExpenses);
        } else {
            console.error('Error from server:', data.message);
            document.getElementById('gasExpensesList').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error loading gas expenses: ${data.message}
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error loading gas expenses:', error);
        document.getElementById('gasExpensesList').innerHTML = `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Error loading gas expenses: ${error.message}
            </div>
        `;
    });
}

function displayGasExpenses(gasExpenses) {
    const container = document.getElementById('gasExpensesList');
    
    if (gasExpenses.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-gas-pump fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No gas expenses recorded</h5>
                <p class="text-muted">Add your first gas expense using the form above.</p>
            </div>
        `;
        return;
    }
    
    let html = '<div class="table-responsive"><table class="table table-striped">';
    html += `
        <thead>
            <tr>
                <th>Date</th>
                <th>Driver</th>
                <th>Model</th>
                <th>Amount</th>
                <th>Sent By</th>
                <th>Checked By</th>
                <th>Photos</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
    `;
    
    gasExpenses.forEach(expense => {
        const photos = [];
        if (expense.has_photo_video_in_groupchat) photos.push('Group Chat');
        if (expense.photo_fuel_gauge_before) photos.push('Gauge Before');
        if (expense.photo_fuel_gauge_after) photos.push('Gauge After');
        if (expense.photo_car_license_plate_gas_boy) photos.push('Car & Gas Boy');
        if (expense.photo_receipt_next_to_gas_pump) photos.push('Receipt');
        
        html += `
            <tr>
                <td>${new Date(expense.date).toLocaleDateString()}</td>
                <td>${expense.driver}</td>
                <td>${expense.model}</td>
                <td>₱${parseFloat(expense.gas_amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')}</td>
                <td>${expense.expense_sent_by}</td>
                <td>${expense.checked_by}</td>
                <td>
                    ${photos.length > 0 ? 
                        `<span class="badge bg-success">${photos.length} photos</span>` : 
                        '<span class="badge bg-secondary">No photos</span>'
                    }
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        ${window.vehiclesCanUpdate ? `<button type="button" class="btn btn-outline-primary" onclick="editGasExpense(${expense.id})" title="Edit"><i class="fas fa-edit"></i></button>` : ''}
                        ${window.vehiclesCanDelete ? `<button type="button" class="btn btn-outline-danger" onclick="deleteGasExpense(${expense.id})" title="Delete"><i class="fas fa-trash"></i></button>` : ''}
                    </div>
                </td>
            </tr>
        `;
    });
    
    html += '</tbody></table></div>';
    container.innerHTML = html;
}

function saveGasExpense() {
    const form = document.getElementById('gasExpenseForm');
    const formData = new FormData(form);
    
    // Convert FormData to JSON
    const data = {};
    for (let [key, value] of formData.entries()) {
        if (key.includes('photo') || key === 'has_photo_video_in_groupchat') {
            data[key] = value === '1';
        } else {
            data[key] = value;
        }
    }
    
    // Debug: Log the data being sent
    console.log('Data being sent:', data);
    console.log('Store route URL:', `{{ route('vehicles.gas-expenses.store', $vehicle) }}`);
    
    // Show loading
    Swal.fire({
        title: 'Saving Gas Expense...',
        text: 'Please wait while we save the gas expense.',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Send AJAX request
    fetch(`{{ route('vehicles.gas-expenses.store', $vehicle) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.text().then(text => {
            try {
                return JSON.parse(text);
            } catch (e) {
                console.error('Response text:', text);
                throw new Error('Invalid JSON response from server');
            }
        });
    })
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
                // Reset form and reload expenses
                resetGasExpenseForm();
                loadGasExpenses();
                // Reload page to update stats
                location.reload();
            });
        } else {
            throw new Error(data.message || 'Failed to save gas expense');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.message || 'Failed to save gas expense',
            confirmButtonColor: '#dc3545'
        });
    });
}

function resetGasExpenseForm() {
    document.getElementById('gasExpenseForm').reset();
    document.getElementById('date').value = new Date().toISOString().split('T')[0];
    document.getElementById('model').value = '{{ $vehicle->full_name }}';
}

function editGasExpense(expenseId) {
    // TODO: Implement edit functionality
    Swal.fire({
        icon: 'info',
        title: 'Edit Functionality',
        text: 'Edit functionality will be implemented in the next update.',
        confirmButtonColor: '#007bff'
    });
}

function deleteGasExpense(expenseId) {
    Swal.fire({
        title: 'Delete Gas Expense?',
        text: 'Are you sure you want to delete this gas expense? This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Deleting Gas Expense...',
                text: 'Please wait while we delete the gas expense.',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Send delete request
            fetch(`{{ route('vehicles.gas-expenses.destroy', [$vehicle, '']) }}/${expenseId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
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
                        loadGasExpenses();
                        location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Failed to delete gas expense');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.message || 'Failed to delete gas expense',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
}

// Post Reservation Expense Functions
function openPostReservationExpenseModal() {
    const modal = new bootstrap.Modal(document.getElementById('postReservationExpenseModal'));
    modal.show();
}

function savePostReservationExpense() {
    const form = document.getElementById('postReservationExpenseForm');
    const formData = new FormData(form);
    
    // Validate form
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Show loading
    Swal.fire({
        title: 'Saving Expense...',
        text: 'Please wait while we save the post reservation expense.',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Convert FormData to JSON
    const data = {};
    for (let [key, value] of formData.entries()) {
        if (value !== '') {
            data[key] = value;
        }
    }
    
    fetch(`{{ route('vehicles.post-reservation-expenses.store', $vehicle) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || 'Failed to save expense');
            });
        }
        return response.json();
    })
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
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('postReservationExpenseModal'));
                modal.hide();
                // Reset form
                form.reset();
                // Reload page to show updated total
                location.reload();
            });
        } else {
            throw new Error(data.message || 'Failed to save expense');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.message || 'Failed to save post reservation expense',
            confirmButtonColor: '#dc3545'
        });
    });
}

// Post Release Expense Functions
function openPostReleaseExpenseModal() {
    const modal = new bootstrap.Modal(document.getElementById('postReleaseExpenseModal'));
    modal.show();
}

function savePostReleaseExpense() {
    const form = document.getElementById('postReleaseExpenseForm');
    const formData = new FormData(form);
    
    // Validate form
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Show loading
    Swal.fire({
        title: 'Saving Expense...',
        text: 'Please wait while we save the post release expense.',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Convert FormData to JSON
    const data = {};
    for (let [key, value] of formData.entries()) {
        if (value !== '') {
            data[key] = value;
        }
    }
    
    fetch(`{{ route('vehicles.post-release-expenses.store', $vehicle) }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(data => {
                throw new Error(data.message || 'Failed to save expense');
            });
        }
        return response.json();
    })
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
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('postReleaseExpenseModal'));
                modal.hide();
                // Reset form
                form.reset();
                // Reload page to show updated total
                location.reload();
            });
        } else {
            throw new Error(data.message || 'Failed to save expense');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.message || 'Failed to save post release expense',
            confirmButtonColor: '#dc3545'
        });
    });
}

// Incentive form submit
document.addEventListener('DOMContentLoaded', function() {
    const incentiveForm = document.getElementById('incentiveForm');
    if (incentiveForm) {
        incentiveForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.set('no_look', document.getElementById('inc_no_look').checked ? '1' : '0');
            formData.set('insurance', document.getElementById('inc_insurance').checked ? '1' : '0');
            formData.set('testimonial', document.getElementById('inc_testimonial').checked ? '1' : '0');
            formData.set('review', document.getElementById('inc_review').checked ? '1' : '0');
            Swal.fire({
                title: 'Saving...',
                text: 'Please wait.',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => { Swal.showLoading(); }
            });
            fetch('{{ route('vehicles.incentive.update', $vehicle) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json().then(json => ({ ok: response.ok, json })).catch(() => ({ ok: response.ok, json: {} })))
            .then(({ ok, json }) => {
                if (ok && json.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: json.message || 'Incentive details saved.',
                        confirmButtonColor: '#28a745'
                    }).then(() => location.reload());
                } else {
                    throw new Error(json.message || 'Failed to save incentive details');
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: err.message || 'Failed to save incentive details',
                    confirmButtonColor: '#dc3545'
                });
            });
        });
    }
});

// Forfeit Details Functions
function saveForfeitDetail() {
    const form = document.getElementById('forfeitDetailsForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    const formData = new FormData(form);
    const data = {
        _token: formData.get('_token'),
        previous_forfeit_date: formData.get('previous_forfeit_date') || null,
        forfeit_amount: formData.get('forfeit_amount'),
        forfeit_date: formData.get('forfeit_date')
    };
    if (!data.forfeit_date) {
        Swal.fire({ icon: 'error', title: 'Error', text: 'Forfeit Date is required.', confirmButtonColor: '#dc3545' });
        return;
    }
    Swal.fire({
        title: 'Saving...',
        text: 'Please wait.',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => { Swal.showLoading(); }
    });
    fetch('{{ route('vehicles.forfeit-details.store', $vehicle) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json().then(json => ({ ok: response.ok, json })))
    .then(({ ok, json }) => {
        if (ok && json.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: json.message,
                confirmButtonColor: '#28a745',
                timer: 2000,
                timerProgressBar: true
            }).then(() => {
                bootstrap.Modal.getInstance(document.getElementById('forfeitDetailsModal')).hide();
                form.reset();
                location.reload();
            });
        } else {
            throw new Error(json.message || 'Failed to save forfeit details');
        }
    })
    .catch(err => {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: err.message || 'Failed to save forfeit details',
            confirmButtonColor: '#dc3545'
        });
    });
}

// Vehicle Ad Functions
let editingAdId = null;

function openAddVehicleAdModal() {
    editingAdId = null;
    document.getElementById('vehicleAdModalLabel').textContent = 'Add Video Ad Details';
    document.getElementById('vehicleAdForm').reset();
    document.getElementById('vehicle_ad_id').value = '';
    document.getElementById('ad_posted_date').value = '{{ date('Y-m-d') }}';
    if (window.VehicleAdLinkFields) {
        VehicleAdLinkFields.reset('ad_', [''], [{ channel: 'Facebook', link: '' }]);
    }
    const modal = new bootstrap.Modal(document.getElementById('vehicleAdModal'));
    modal.show();
}

function editVehicleAd(adId) {
    editingAdId = adId;
    document.getElementById('vehicleAdModalLabel').textContent = 'Edit Video Ad Details';
    
    const row = document.querySelector(`tr[data-ad-id="${adId}"]`);
    if (row) {
        document.getElementById('vehicle_ad_id').value = adId;
        document.getElementById('ad_posted_date').value = row.getAttribute('data-posted-date') || '';
        let videoLinks = [];
        let socialLinks = [];
        try { videoLinks = JSON.parse(row.getAttribute('data-video-links') || '[]'); } catch (e) {}
        try { socialLinks = JSON.parse(row.getAttribute('data-social-media-links') || '[]'); } catch (e) {}
        if (window.VehicleAdLinkFields) {
            VehicleAdLinkFields.reset('ad_', videoLinks.length ? videoLinks : [''], socialLinks.length ? socialLinks : [{ channel: 'Facebook', link: '' }]);
        }
        document.getElementById('ad_ads_boost_link').value = row.getAttribute('data-ads-boost-link') || '';
        document.getElementById('ad_campaign_id').value = row.getAttribute('data-campaign-id') || '';
        document.getElementById('ad_ad_id').value = row.getAttribute('data-ad-id-value') || '';
    }
    
    const modal = new bootstrap.Modal(document.getElementById('vehicleAdModal'));
    modal.show();
}

function saveVehicleAd() {
    const form = document.getElementById('vehicleAdForm');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    Swal.fire({
        title: 'Saving...',
        text: 'Please wait while we save the video ad details.',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    const data = {
        posted_date: document.getElementById('ad_posted_date').value,
    };
    if (window.VehicleAdLinkFields) {
        Object.assign(data, VehicleAdLinkFields.collect('ad_'));
    }
    const adsBoost = document.getElementById('ad_ads_boost_link').value.trim();
    const campaignId = document.getElementById('ad_campaign_id').value.trim();
    const adIdVal = document.getElementById('ad_ad_id').value.trim();
    if (adsBoost) data.ads_boost_link = adsBoost;
    if (campaignId) data.campaign_id = campaignId;
    if (adIdVal) data.ad_id = adIdVal;
    
    const adId = document.getElementById('vehicle_ad_id').value;
    const url = adId 
        ? `{{ route('vehicles.ads.update', [$vehicle, '']) }}/${adId}`
        : `{{ route('vehicles.ads.store', $vehicle) }}`;
    const method = adId ? 'PUT' : 'POST';
    
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
            return response.json().then(data => {
                throw new Error(data.message || 'Failed to save video ad details');
            });
        }
        return response.json();
    })
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
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('vehicleAdModal'));
                modal.hide();
                // Reset form
                form.reset();
                // Reload page to show updated data
                location.reload();
            });
        } else {
            throw new Error(data.message || 'Failed to save video ad details');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.message || 'Failed to save video ad details',
            confirmButtonColor: '#dc3545'
        });
    });
}

function deleteVehicleAd(adId) {
    Swal.fire({
        title: 'Delete Video Ad Details?',
        text: 'Are you sure you want to delete this video ad details? This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait while we delete the video ad details.',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(`{{ route('vehicles.ads.destroy', [$vehicle, '']) }}/${adId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Failed to delete video ad details');
                    });
                }
                return response.json();
            })
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
                    throw new Error(data.message || 'Failed to delete video ad details');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.message || 'Failed to delete video ad details',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
}

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
    // Find the field data from the page
    const fieldElement = document.querySelector(`[data-field-id="${fieldId}"]`);
    if (!fieldElement) {
        alert('Field not found');
        return;
    }
    
    currentEditingFieldId = fieldId;
    
    // Update modal title and button
    document.getElementById('customFieldModalLabel').textContent = 'Edit Custom Field';
    document.getElementById('saveFieldBtn').textContent = 'Update Field';
    
    // Get field data from the page (you might need to store this data in data attributes)
    // For now, we'll need to fetch it from the server
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

// Custom Section Functions
let currentEditingSectionId = null;

function openCustomSectionModal() {
    currentEditingSectionId = null;
    document.getElementById('customSectionModalLabel').textContent = 'Add Custom Section';
    document.getElementById('saveSectionBtn').textContent = 'Save Section';
    document.getElementById('customSectionForm').reset();
    document.getElementById('fieldsContainer').innerHTML = '';
    document.getElementById('customSectionModal').style.display = 'block';
    document.getElementById('customSectionModal').classList.add('show');
    document.body.classList.add('modal-open');
}

function closeCustomSectionModal() {
    currentEditingSectionId = null;
    document.getElementById('customSectionModal').style.display = 'none';
    document.getElementById('customSectionModal').classList.remove('show');
    document.body.classList.remove('modal-open');
}

function addField() {
    const fieldsContainer = document.getElementById('fieldsContainer');
    const fieldIndex = fieldsContainer.children.length;
    
    const fieldDiv = document.createElement('div');
    fieldDiv.className = 'field-row border p-3 mb-3 rounded';
    fieldDiv.innerHTML = `
        <div class="row">
            <div class="col-md-3">
                <label class="form-label">Field Name</label>
                <input type="text" class="form-control" name="fields[${fieldIndex}][field_name]" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Field Label</label>
                <input type="text" class="form-control" name="fields[${fieldIndex}][field_label]" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Field Type</label>
                <select class="form-select" name="fields[${fieldIndex}][field_type]" onchange="toggleOptions(${fieldIndex})" required>
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
            <div class="col-md-2">
                <label class="form-label">Required</label>
                <select class="form-select" name="fields[${fieldIndex}][is_required]">
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-danger btn-sm d-block" onclick="removeField(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="row mt-2" id="options-${fieldIndex}" style="display: none;">
            <div class="col-md-12">
                <label class="form-label">Options (one per line)</label>
                <textarea class="form-control" name="fields[${fieldIndex}][field_options]" rows="3" placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
            </div>
        </div>
    `;
    
    fieldsContainer.appendChild(fieldDiv);
}

function removeField(button) {
    button.closest('.field-row').remove();
}

function toggleOptions(fieldIndex) {
    const select = document.querySelector(`select[name="fields[${fieldIndex}][field_type]"]`);
    const optionsDiv = document.getElementById(`options-${fieldIndex}`);
    
    if (select.value === 'select' || select.value === 'radio') {
        optionsDiv.style.display = 'block';
    } else {
        optionsDiv.style.display = 'none';
    }
}

function saveCustomSection() {
    const form = document.getElementById('customSectionForm');
    const formData = new FormData(form);
    
    // Validate required fields
    const title = formData.get('title');
    if (!title || title.trim() === '') {
        alert('Please enter a section title');
        return;
    }
    
    // Convert form data to JSON
    const data = {
        title: title.trim(),
        description: formData.get('description') || '',
        fields: []
    };
    
    // Collect field data
    const fieldRows = document.querySelectorAll('.field-row');
    if (fieldRows.length === 0) {
        alert('Please add at least one field to the section');
        return;
    }
    
    fieldRows.forEach((row, index) => {
        const fieldName = row.querySelector(`input[name="fields[${index}][field_name]"]`).value.trim();
        const fieldLabel = row.querySelector(`input[name="fields[${index}][field_label]"]`).value.trim();
        const fieldType = row.querySelector(`select[name="fields[${index}][field_type]"]`).value;
        
        // Validate field data
        if (!fieldName || !fieldLabel || !fieldType) {
            alert(`Please fill in all required fields for field ${index + 1}`);
            return;
        }
        
        const fieldData = {
            field_name: fieldName,
            field_label: fieldLabel,
            field_type: fieldType,
            is_required: row.querySelector(`select[name="fields[${index}][is_required]"]`).value === '1',
            field_value: '',
            field_options: []
        };
        
        // Handle options for select and radio fields
        if (fieldData.field_type === 'select' || fieldData.field_type === 'radio') {
            const optionsText = row.querySelector(`textarea[name="fields[${index}][field_options]"]`).value;
            fieldData.field_options = optionsText.split('\n').filter(option => option.trim() !== '');
            
            if (fieldData.field_options.length === 0) {
                alert(`Please add options for ${fieldData.field_type} field: ${fieldLabel}`);
                return;
            }
        }
        
        data.fields.push(fieldData);
    });
    
    // Debug: Log the data being sent
    console.log('Sending data:', data);
    
    // Determine if we're creating or updating
    const isEditing = currentEditingSectionId !== null;
    const url = isEditing 
        ? `/vehicles/{{ $vehicle->id }}/custom-sections/${currentEditingSectionId}`
        : `/vehicles/{{ $vehicle->id }}/custom-sections`;
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
            alert(`Custom section ${action} successfully!`);
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the custom section: ' + error.message);
    });
}

function editCustomSection(sectionId) {
    currentEditingSectionId = sectionId;
    
    // Fetch the section data
    fetch(`/vehicles/{{ $vehicle->id }}/custom-sections/${sectionId}/edit`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const section = data.section;
                
                // Update modal title and button
                document.getElementById('customSectionModalLabel').textContent = 'Edit Custom Section';
                document.getElementById('saveSectionBtn').textContent = 'Update Section';
                
                // Populate section data
                document.getElementById('sectionTitle').value = section.title;
                document.getElementById('sectionDescription').value = section.description || '';
                
                // Clear existing fields
                document.getElementById('fieldsContainer').innerHTML = '';
                
                // Add fields
                section.fields.forEach((field, index) => {
                    addField();
                    const fieldRow = document.querySelectorAll('.field-row')[index];
                    
                    // Populate field data
                    fieldRow.querySelector(`input[name="fields[${index}][field_name]"]`).value = field.field_name;
                    fieldRow.querySelector(`input[name="fields[${index}][field_label]"]`).value = field.field_label;
                    fieldRow.querySelector(`select[name="fields[${index}][field_type]"]`).value = field.field_type;
                    fieldRow.querySelector(`select[name="fields[${index}][is_required]"]`).value = field.is_required ? '1' : '0';
                    
                    // Handle options for select and radio fields
                    if (field.field_type === 'select' || field.field_type === 'radio') {
                        const optionsText = field.field_options ? field.field_options.join('\n') : '';
                        fieldRow.querySelector(`textarea[name="fields[${index}][field_options]"]`).value = optionsText;
                        toggleOptions(index);
                    }
                });
                
                // Open modal
                document.getElementById('customSectionModal').style.display = 'block';
                document.getElementById('customSectionModal').classList.add('show');
                document.body.classList.add('modal-open');
            } else {
                alert('Error loading section: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading the section');
        });
}

function deleteCustomSection(sectionId) {
    if (confirm('Are you sure you want to delete this custom section?')) {
        fetch(`/vehicles/{{ $vehicle->id }}/custom-sections/${sectionId}`, {
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
            alert('An error occurred while deleting the custom section');
        });
    }
}

// Posted Price Functions
function openPostedPriceModal(currentPrice = null) {
    const modal = new bootstrap.Modal(document.getElementById('postedPriceModal'));
    const input = document.getElementById('posted_price_input');
    const title = document.getElementById('postedPriceModalLabel');
    
    if (currentPrice !== null && currentPrice !== undefined) {
        title.textContent = 'Edit Posted Price';
        input.value = currentPrice;
    } else {
        title.textContent = 'Add Posted Price';
        input.value = '';
    }
    
    modal.show();
}

function savePostedPrice() {
    const postedPrice = parseFloat(document.getElementById('posted_price_input').value);
    const form = document.getElementById('postedPriceForm');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    if (isNaN(postedPrice) || postedPrice < 0) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Please enter a valid posted price.',
            confirmButtonColor: '#dc3545'
        });
        return;
    }
    
    Swal.fire({
        title: 'Saving...',
        text: 'Please wait while we save the posted price.',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch(`{{ route('vehicles.posted-price.update', $vehicle) }}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            posted_price: postedPrice
        })
    })
    .then(async response => {
        if (!response.ok) {
            try {
                const data = await response.json();
                throw new Error(data.message || (data.errors && data.errors.posted_price ? data.errors.posted_price[0] : 'Failed to save posted price'));
            } catch (parseError) {
                throw new Error(`HTTP ${response.status}: Failed to save posted price. Please try again.`);
            }
        }
        return response.json();
    })
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
                location.reload();
            });
        } else {
            throw new Error(data.message || 'Failed to save posted price');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.message || 'Failed to save posted price. Please try again.',
            confirmButtonColor: '#dc3545'
        });
    });
}

function deletePostedPrice() {
    Swal.fire({
        title: 'Delete Posted Price?',
        text: 'Are you sure you want to delete the posted price? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait while we delete the posted price.',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(`{{ route('vehicles.posted-price.delete', $vehicle) }}`, {
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
                    throw new Error(data.message || 'Failed to delete posted price');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.message || 'Failed to delete posted price. Please try again.',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
}

// Sold Price Functions
function openSoldPriceModal(currentPrice = null) {
    const modal = new bootstrap.Modal(document.getElementById('soldPriceModal'));
    const input = document.getElementById('sold_price_input');
    const title = document.getElementById('soldPriceModalLabel');
    
    if (currentPrice !== null && currentPrice !== undefined) {
        title.textContent = 'Edit Sold Price';
        input.value = currentPrice;
    } else {
        title.textContent = 'Add Sold Price';
        input.value = '';
    }
    
    modal.show();
}

function saveSoldPrice() {
    const soldPrice = parseFloat(document.getElementById('sold_price_input').value);
    const form = document.getElementById('soldPriceForm');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    if (isNaN(soldPrice) || soldPrice < 0) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Please enter a valid sold price.',
            confirmButtonColor: '#dc3545'
        });
        return;
    }
    
    Swal.fire({
        title: 'Saving...',
        text: 'Please wait while we save the sold price.',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch(`{{ route('vehicles.sold-price.update', $vehicle) }}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            sold_price: soldPrice
        })
    })
    .then(async response => {
        if (!response.ok) {
            try {
                const data = await response.json();
                throw new Error(data.message || (data.errors && data.errors.sold_price ? data.errors.sold_price[0] : 'Failed to save sold price'));
            } catch (parseError) {
                throw new Error(`HTTP ${response.status}: Failed to save sold price. Please try again.`);
            }
        }
        return response.json();
    })
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
                location.reload();
            });
        } else {
            throw new Error(data.message || 'Failed to save sold price');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.message || 'Failed to save sold price. Please try again.',
            confirmButtonColor: '#dc3545'
        });
    });
}

function deleteSoldPrice() {
    Swal.fire({
        title: 'Delete Sold Price?',
        text: 'Are you sure you want to delete the sold price? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait while we delete the sold price.',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(`{{ route('vehicles.sold-price.delete', $vehicle) }}`, {
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
                    throw new Error(data.message || 'Failed to delete sold price');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.message || 'Failed to delete sold price. Please try again.',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
}
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

<!-- Custom Section Modal -->
<div class="modal fade" id="customSectionModal" tabindex="-1" aria-labelledby="customSectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customSectionModalLabel">Add Custom Section</h5>
                <button type="button" class="btn-close" onclick="closeCustomSectionModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="customSectionForm">
                    @csrf
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="sectionTitle" class="form-label">Section Title</label>
                            <input type="text" class="form-control" id="sectionTitle" name="title" required>
                        </div>
                        <div class="col-md-6">
                            <label for="sectionDescription" class="form-label">Description (Optional)</label>
                            <input type="text" class="form-control" id="sectionDescription" name="description">
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6>Fields</h6>
                        <button type="button" class="btn btn-primary btn-sm" onclick="addField()">
                            <i class="fas fa-plus me-1"></i>Add Field
                        </button>
                    </div>
                    
                    <div id="fieldsContainer">
                        <!-- Fields will be added here dynamically -->
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeCustomSectionModal()">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveCustomSection()" id="saveSectionBtn">Save Section</button>
            </div>
        </div>
    </div>
</div>

<!-- Posted Price Modal -->
<div class="modal fade" id="postedPriceModal" tabindex="-1" aria-labelledby="postedPriceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="postedPriceModalLabel">Add/Edit Posted Price</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="postedPriceForm">
                    @csrf
                    <div class="mb-3">
                        <label for="posted_price_input" class="form-label">Posted Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control" id="posted_price_input" name="posted_price" step="0.01" min="0" required placeholder="Enter posted price">
                        </div>
                        <small class="form-text text-muted">Enter the posted price for this vehicle</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="savePostedPrice()">
                    <i class="fas fa-save me-1"></i>Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Sold Price Modal -->
<div class="modal fade" id="soldPriceModal" tabindex="-1" aria-labelledby="soldPriceModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="soldPriceModalLabel">Add/Edit Sold Price</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="soldPriceForm">
                    @csrf
                    <div class="mb-3">
                        <label for="sold_price_input" class="form-label">Sold Price <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control" id="sold_price_input" name="sold_price" step="0.01" min="0" required placeholder="Enter sold price">
                        </div>
                        <small class="form-text text-muted">Enter the sold price for this vehicle</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveSoldPrice()">
                    <i class="fas fa-save me-1"></i>Save
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Document Action Modal -->
<div class="modal fade" id="documentActionModal" tabindex="-1" aria-labelledby="documentActionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentActionModalLabel">Document Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">What would you like to do with <strong id="modalDocumentLabel"></strong>?</p>
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-success btn-lg" id="markCompletedBtn">
                        <i class="fas fa-check-circle me-2"></i>Mark as Completed
                    </button>
                    <button type="button" class="btn btn-primary btn-lg" id="addDetailsBtn">
                        <i class="fas fa-edit me-2"></i>Add Details
                    </button>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Document checkbox functionality
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.document-checkbox');
        const modalElement = document.getElementById('documentActionModal');
        if (!modalElement) return;
        
        const modal = new bootstrap.Modal(modalElement);
        let currentCheckbox = null;
        let currentDocumentType = null;
        let currentDocumentId = null;
        let currentVehicleId = null;

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // Only show modal when checking (not unchecking)
                if (this.checked) {
                    currentCheckbox = this;
                    currentDocumentType = this.getAttribute('data-document-type');
                    currentDocumentId = this.getAttribute('data-document-id');
                    currentVehicleId = this.getAttribute('data-vehicle-id');
                    const documentLabel = this.getAttribute('data-document-label');
                    
                    // Set modal label
                    document.getElementById('modalDocumentLabel').textContent = documentLabel;
                    
                    // Show modal
                    modal.show();
                } else {
                    // If unchecking, mark as incomplete if document exists
                    const documentId = this.getAttribute('data-document-id');
                    if (documentId) {
                        markDocumentIncomplete(documentId, this);
                    } else {
                        // If no document exists, just uncheck
                        this.checked = false;
                    }
                }
            });
        });

        // Mark as Completed button
        const markCompletedBtn = document.getElementById('markCompletedBtn');
        if (markCompletedBtn) {
            markCompletedBtn.addEventListener('click', function() {
                modal.hide();
                
                if (currentDocumentId) {
                    // Document exists, mark as completed
                    markDocumentCompleted(currentDocumentId, currentCheckbox);
                } else {
                    // Document doesn't exist, create and mark as completed
                    markNewDocumentCompleted(currentVehicleId, currentDocumentType, currentCheckbox);
                }
            });
        }

        // Add Details button
        const addDetailsBtn = document.getElementById('addDetailsBtn');
        if (addDetailsBtn) {
            addDetailsBtn.addEventListener('click', function() {
                modal.hide();
                
                // Redirect to add details page
                const url = `/vehicles/${currentVehicleId}/documents/${currentDocumentType}/add-details`;
                window.location.href = url;
            });
        }

        // Function to clean up modal backdrop
        function cleanupModalBackdrop() {
            setTimeout(() => {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }, 100);
        }
        
        // Reset checkbox state when modal is closed without action
        modalElement.addEventListener('hidden.bs.modal', function() {
            cleanupModalBackdrop();
            
            if (currentCheckbox && !currentCheckbox.hasAttribute('data-completed')) {
                currentCheckbox.checked = false;
            }
        });
        
        // Handle when modal starts to hide
        modalElement.addEventListener('hide.bs.modal', function() {
            cleanupModalBackdrop();
        });
        
        // Handle cancel and close buttons explicitly
        const cancelBtn = modalElement.querySelector('[data-bs-dismiss="modal"]');
        const closeBtn = modalElement.querySelector('.btn-close');
        
        if (cancelBtn) {
            cancelBtn.addEventListener('click', cleanupModalBackdrop);
        }
        
        if (closeBtn) {
            closeBtn.addEventListener('click', cleanupModalBackdrop);
        }
    });

    // Mark document as completed
    function markDocumentCompleted(documentId, checkbox) {
        fetch(`/vehicles/{{ $vehicle->id }}/documents/${documentId}/mark-completed`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (checkbox) {
                    checkbox.setAttribute('data-is-completed', '1');
                    checkbox.setAttribute('data-document-id', documentId);
                }
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Document marked as completed!',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to mark document as completed.'
            });
            if (checkbox) {
                checkbox.checked = false;
            }
        });
    }

    // Mark new document as completed
    function markNewDocumentCompleted(vehicleId, documentType, checkbox) {
        fetch(`/vehicles/${vehicleId}/documents/mark-completed`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                document_type: documentType,
                process_type: 'ACQUISITION'
            })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    throw new Error(err.message || 'Failed to mark document as completed');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                if (checkbox) {
                    checkbox.setAttribute('data-is-completed', '1');
                    checkbox.setAttribute('data-document-id', data.document_id);
                }
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Document marked as completed!',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                throw new Error(data.message || 'Failed to mark document as completed');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'Failed to mark document as completed.'
            });
            if (checkbox) {
                checkbox.checked = false;
            }
        });
    }

    // Mark document as incomplete
    function markDocumentIncomplete(documentId, checkbox) {
        fetch(`/vehicles/{{ $vehicle->id }}/documents/${documentId}/mark-incomplete`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (checkbox) {
                    checkbox.setAttribute('data-is-completed', '0');
                }
                Swal.fire({
                    icon: 'info',
                    title: 'Success!',
                    text: 'Document marked as incomplete!',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to mark document as incomplete.'
            });
            if (checkbox) {
                checkbox.checked = true;
            }
        });
    }
</script>
@endsection
