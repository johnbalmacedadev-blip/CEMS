@extends('layouts.app')

@section('title', 'Car Photos Folder - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-folder-open me-2"></i>Car Photos Folder
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
            <a href="{{ route('vehicles.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-car me-1"></i>Unit Report
            </a>
        </div>
    </div>

    <p class="text-muted mb-4">Browse all vehicle photos. Click a card to view the vehicle or manage its images.</p>

    @if($vehicles->isEmpty())
        <div class="card border-secondary">
            <div class="card-body text-center py-5">
                <i class="fas fa-images fa-3x text-muted mb-3"></i>
                <h5 class="card-title">No vehicles yet</h5>
                <p class="text-muted mb-3">Add vehicles from the Unit Report to see their photos here.</p>
                <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Add New Vehicle
                </a>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($vehicles as $vehicle)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <a href="{{ route('vehicles.show', $vehicle) }}" class="text-decoration-none text-dark">
                            <div class="position-relative" style="height: 180px; background: #f0f0f0; overflow: hidden;">
                                @if($vehicle->primaryImage)
                                    <img src="{{ $vehicle->primaryImage->thumbnail_url }}"
                                         alt="{{ $vehicle->full_name }}"
                                         class="w-100 h-100"
                                         style="object-fit: cover;">
                                @else
                                    <div class="d-flex align-items-center justify-content-center w-100 h-100 text-muted">
                                        <i class="fas fa-car-side fa-4x opacity-50"></i>
                                    </div>
                                @endif
                                <span class="position-absolute top-0 end-0 m-2 badge bg-{{ $vehicle->status === 'Available' ? 'success' : ($vehicle->status === 'Reserved' ? 'warning' : ($vehicle->status === 'Released' ? 'info' : 'secondary')) }}">
                                    {{ $vehicle->status }}
                                </span>
                            </div>
                            <div class="card-body">
                                <h6 class="card-title mb-1 text-truncate" title="{{ $vehicle->full_name }}">{{ $vehicle->full_name }}</h6>
                                <p class="card-text small text-muted mb-2">
                                    @if($vehicle->plate_number)
                                        <i class="fas fa-tag me-1"></i>{{ $vehicle->plate_number }}
                                    @else
                                        <span class="fst-italic">No plate</span>
                                    @endif
                                </p>
                                <p class="card-text small mb-0">
                                    <i class="fas fa-images me-1"></i>{{ $vehicle->images->count() }} photo(s)
                                </p>
                            </div>
                        </a>
                        <div class="card-footer bg-transparent border-top-0 pt-0">
                            <a href="{{ route('vehicles.images.index', $vehicle) }}" class="btn btn-sm btn-outline-primary w-100">
                                <i class="fas fa-camera me-1"></i>Manage Photos
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
