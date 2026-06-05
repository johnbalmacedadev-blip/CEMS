@extends('layouts.app')

@section('title', 'View All Documents - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <main class="col-12 px-md-4 main-content" id="mainContent">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-file-alt me-2"></i>
                    All Documents - {{ $vehicle->full_name }}
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Vehicle
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Acquisition Documents Section -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-folder-open me-2"></i>Acquisition Documents
                    </h5>
                </div>
                <div class="card-body">
                    @if($acquisitionDocuments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Document Type</th>
                                        <th>Status</th>
                                        <th>Storage Type</th>
                                        <th>Check Date</th>
                                        <th>Checked By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($acquisitionDocuments as $document)
                                        <tr>
                                            <td>
                                                <strong>{{ str_replace('_', ' ', strtoupper($document->document_type)) }}</strong>
                                            </td>
                                            <td>
                                                @if($document->is_completed)
                                                    <span class="badge bg-success">Completed</span>
                                                @else
                                                    <span class="badge bg-warning">Incomplete</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($document->storage_type === 'link')
                                                    <span class="badge bg-info"><i class="fas fa-link me-1"></i>Link</span>
                                                @elseif($document->storage_type === 'form')
                                                    <span class="badge bg-primary"><i class="fas fa-edit me-1"></i>Form</span>
                                                @elseif($document->storage_type === 'file')
                                                    <span class="badge bg-success"><i class="fas fa-file me-1"></i>File</span>
                                                @else
                                                    <span class="text-muted">Not Set</span>
                                                @endif
                                            </td>
                                            <td>{{ $document->check_date ? $document->check_date->format('M d, Y') : 'N/A' }}</td>
                                            <td>{{ $document->checked_by ?? 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('vehicles.documents.show', [$vehicle, $document]) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('vehicles.documents.edit', [$vehicle, $document]) }}" 
                                                   class="btn btn-sm btn-outline-warning" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>No acquisition documents found.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Reservation Documents Section -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-check me-2"></i>Reservation Documents
                    </h5>
                </div>
                <div class="card-body">
                    @if($reservationDocuments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Document Type</th>
                                        <th>Status</th>
                                        <th>Storage Type</th>
                                        <th>Check Date</th>
                                        <th>Checked By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reservationDocuments as $document)
                                        <tr>
                                            <td>
                                                <strong>{{ str_replace('_', ' ', strtoupper($document->document_type)) }}</strong>
                                            </td>
                                            <td>
                                                @if($document->is_completed)
                                                    <span class="badge bg-success">Completed</span>
                                                @else
                                                    <span class="badge bg-warning">Incomplete</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($document->storage_type === 'link')
                                                    <span class="badge bg-info"><i class="fas fa-link me-1"></i>Link</span>
                                                @elseif($document->storage_type === 'form')
                                                    <span class="badge bg-primary"><i class="fas fa-edit me-1"></i>Form</span>
                                                @elseif($document->storage_type === 'file')
                                                    <span class="badge bg-success"><i class="fas fa-file me-1"></i>File</span>
                                                @else
                                                    <span class="text-muted">Not Set</span>
                                                @endif
                                            </td>
                                            <td>{{ $document->check_date ? $document->check_date->format('M d, Y') : 'N/A' }}</td>
                                            <td>{{ $document->checked_by ?? 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('vehicles.documents.show', [$vehicle, $document]) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('vehicles.documents.edit', [$vehicle, $document]) }}" 
                                                   class="btn btn-sm btn-outline-warning" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>No reservation documents found.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Release Documents Section -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-check-circle me-2"></i>Release Documents
                    </h5>
                </div>
                <div class="card-body">
                    @if($releaseDocuments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Document Type</th>
                                        <th>Status</th>
                                        <th>Storage Type</th>
                                        <th>Check Date</th>
                                        <th>Checked By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($releaseDocuments as $document)
                                        <tr>
                                            <td>
                                                <strong>{{ str_replace('_', ' ', strtoupper($document->document_type)) }}</strong>
                                            </td>
                                            <td>
                                                @if($document->is_completed)
                                                    <span class="badge bg-success">Completed</span>
                                                @else
                                                    <span class="badge bg-warning">Incomplete</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($document->storage_type === 'link')
                                                    <span class="badge bg-info"><i class="fas fa-link me-1"></i>Link</span>
                                                @elseif($document->storage_type === 'form')
                                                    <span class="badge bg-primary"><i class="fas fa-edit me-1"></i>Form</span>
                                                @elseif($document->storage_type === 'file')
                                                    <span class="badge bg-success"><i class="fas fa-file me-1"></i>File</span>
                                                @else
                                                    <span class="text-muted">Not Set</span>
                                                @endif
                                            </td>
                                            <td>{{ $document->check_date ? $document->check_date->format('M d, Y') : 'N/A' }}</td>
                                            <td>{{ $document->checked_by ?? 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('vehicles.documents.show', [$vehicle, $document]) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="View Details">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                                <a href="{{ route('vehicles.documents.edit', [$vehicle, $document]) }}" 
                                                   class="btn btn-sm btn-outline-warning" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>No release documents found.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Summary Card -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Document Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-primary text-white mb-3">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ $acquisitionDocuments->count() }}</h3>
                                    <p class="mb-0">Acquisition Documents</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white mb-3">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ $reservationDocuments->count() }}</h3>
                                    <p class="mb-0">Reservation Documents</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white mb-3">
                                <div class="card-body text-center">
                                    <h3 class="mb-0">{{ $releaseDocuments->count() }}</h3>
                                    <p class="mb-0">Release Documents</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <strong>Total Documents: {{ $acquisitionDocuments->count() + $reservationDocuments->count() + $releaseDocuments->count() }}</strong>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="mt-4">
                <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Back to Vehicle
                </a>
            </div>
        </main>
    </div>
</div>
@endsection
