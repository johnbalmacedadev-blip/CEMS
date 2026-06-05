@extends('layouts.app')

@section('title', 'Vehicle Reservation Details - Car Empire Management System')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
<style>.ts-dropdown { z-index: 1065 !important; }</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <main class="col-12 px-md-4 main-content" id="mainContent">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-chart-line me-2"></i>Vehicle Reservation Details - {{ $vehicle->full_name }}
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <button type="button" class="btn btn-primary me-2" onclick="openStatusDetailsModal()">
                        <i class="fas fa-edit me-1"></i>Edit Status Details
                    </button>
                    @php
                        $hasReservationData = $vehicle->statusDetail && (
                            $vehicle->statusDetail->sale_date ||
                            $vehicle->statusDetail->sales_person_reserved ||
                            $vehicle->statusDetail->sale_reservation_amount ||
                            $vehicle->statusDetail->sales_price ||
                            ($vehicle->statusDetail->sale_status && $vehicle->statusDetail->sale_status !== 'Available')
                        );
                    @endphp
                    @if($hasReservationData)
                    <button type="button" class="btn btn-danger me-2" onclick="deleteReservationDetails()">
                        <i class="fas fa-trash me-1"></i>Delete Reservation Details
                    </button>
                    @endif
                    <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Vehicle
                    </a>
                </div>
            </div>

            @if($vehicle->statusDetail)
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>Reservation Information
                    </h5>
                </div>
                <div class="card-body">
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
                            <label class="form-label fw-bold">Agent Cost</label>
                            <p class="form-control-plaintext">{{ $vehicle->statusDetail->formatted_agent_cost }}</p>
                        </div>
                    </div>
                    @endif

                    @if(($vehicle->statusDetail->cash_financing ?? '') === 'Financing')
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
                    </div>
                </div>
            </div>

            <!-- Customer Information Section -->
            @if($vehicle->status !== 'Released')
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-user me-2"></i>Customer Information
                        </h5>
                        @php
                            $hasCustomerInfo = $vehicle->statusDetail && (
                                $vehicle->statusDetail->customer_first_name ||
                                $vehicle->statusDetail->customer_last_name ||
                                $vehicle->statusDetail->customer_contact_number
                            );
                        @endphp
                        @if(!$hasCustomerInfo)
                            <button type="button" class="btn btn-sm btn-light" onclick="openCustomerInfoModal()">
                                <i class="fas fa-plus me-1"></i>Add Customer Information
                            </button>
                        @else
                            <button type="button" class="btn btn-sm btn-light" onclick="openCustomerInfoModal()">
                                <i class="fas fa-edit me-1"></i>Edit Customer Information
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
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
                </div>
            </div>
            @endif

            <!-- Reservation Documents Section -->
            <div class="card mt-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-file-alt me-2"></i>Reservation Documents
                    </h5>
                </div>
                <div class="card-body">
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
                                $document = $reservationDocuments->where('document_type', $type)->first();
                                if (!$document) {
                                    $document = $acquisitionDocuments->where('document_type', $type)->first();
                                }
                            @endphp
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-{{ $document ? ($document->is_completed ? 'check-circle text-success' : 'check-square text-primary') : 'square text-muted' }} me-3" style="font-size: 1.1rem; min-width: 24px;"></i>
                                    <span class="fw-semibold text-dark me-auto">{{ $label }}</span>
                                    <div class="ms-4 d-flex gap-2">
                                        @if($document)
                                            @php
                                                $isFromAcquisition = $reservationDocuments->where('document_type', $type)->first() === null;
                                            @endphp
                                            <a href="{{ route('vehicles.documents.show', [$vehicle, $document]) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="View Document">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                            <a href="{{ route('vehicles.documents.create', [$vehicle, $type]) }}?process_type=RESERVATION" 
                                               class="btn btn-sm btn-outline-warning" 
                                               title="Edit Document">
                                                <i class="fas fa-edit"></i> Edit
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
                </div>
            </div>

            <!-- Release Vehicle Button -->
            @if($vehicle->statusDetail && $vehicle->statusDetail->sale_status === 'Reserved')
            <div class="card mt-4">
                <div class="card-body text-center">
                    <button type="button" class="btn btn-lg btn-success" onclick="releaseVehicle()">
                        <i class="fas fa-check-circle me-2"></i>Release this Vehicle
                    </button>
                </div>
            </div>
            @endif

            @else
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-info-circle fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted mb-3">No reservation details found</h5>
                    <p class="text-muted">This vehicle does not have reservation details yet.</p>
                </div>
            </div>
            @endif

            <!-- Back Button -->
            <div class="mt-4">
                <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Vehicle
                </a>
            </div>
        </main>
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
                    
                    <input type="hidden" id="sale_status" name="sale_status" value="{{ $vehicle->statusDetail->sale_status ?? 'Reserved' }}">
                    
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

                    @php $reservationDetailsFinancing = ($vehicle->statusDetail->cash_financing ?? '') === 'Financing'; @endphp
                    <div id="financing_company_wrap" class="row g-1 mb-3 {{ $reservationDetailsFinancing ? '' : 'd-none' }}">
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

                    @php $reservationDetailsSaleAgentOrigin = ($vehicle->statusDetail->sale_origin ?? '') === 'Agent'; @endphp
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
                    <div id="sale_origin_agent_fields_wrap" class="{{ $reservationDetailsSaleAgentOrigin ? '' : 'd-none' }}">
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

                    <div id="finance_revenue_fields_wrap" class="row g-1 mb-3 {{ $reservationDetailsFinancing ? '' : 'd-none' }}">
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

<!-- Customer Info Modal -->
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

@push('scripts')
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
// Status Details Modal Functions
function openStatusDetailsModal() {
    const modal = new bootstrap.Modal(document.getElementById('statusDetailsModal'));
    modal.show();
}

function saveStatusDetails() {
    const form = document.getElementById('statusDetailsForm');
    const formData = new FormData(form);
    
    // Get sale_status from hidden input or use current status
    const saleStatus = document.getElementById('sale_status').value || '{{ $vehicle->statusDetail->sale_status ?? $vehicle->status }}';
    
    // Convert FormData to JSON
    const data = {
        sale_status: saleStatus
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

// Customer Info Modal Functions
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
        data.release_date = existingStatusDetail.release_date || '';
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

// Delete Reservation Details Function
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
                        window.location.href = '{{ route('vehicles.show', $vehicle) }}';
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

// Release Vehicle Function
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
            // Redirect to vehicle show page where the release modal is available
            window.location.href = '{{ route('vehicles.show', $vehicle) }}';
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
});
</script>
@endpush
@endsection
