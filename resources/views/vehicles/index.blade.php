@extends('layouts.app')

@section('title', 'Unit Report - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <main class="col-12 px-md-4 main-content" id="mainContent">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Unit Report</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-home me-1"></i>Back to Main Menu
                    </a>
                    @if($status === 'Archived')
                    @canPage('vehicles', 'update')
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#archiveVehicleModal">
                        <i class="fas fa-archive me-1"></i>Add to Archive
                    </button>
                    @endcanPage
                    @else
                    @canPage('vehicles', 'create')
                    <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Add New Vehicle
                    </a>
                    @endcanPage
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Search & filters -->
            @php
                $hasExtraFilters = ($yearFrom ?? null) || ($yearTo ?? null) || ($transmission ?? null) || ($fuelType ?? null) || ($bodyType ?? null) || ($purchasedFrom ?? null) || ($reservationDateFrom ?? null) || ($reservationDateTo ?? null) || ($releaseDateFrom ?? null) || ($releaseDateTo ?? null) || ($branchLocationId ?? null);
                $hasActiveFilters = $search || $status !== 'all' || $hasExtraFilters;
                $selectedBranchName = null;
                if (!empty($branchLocationId) && isset($branches)) {
                    $selectedBranchName = optional($branches->firstWhere('id', (int) $branchLocationId))->name;
                }
            @endphp
            <div class="card mb-4">
                <div class="accordion accordion-flush" id="vehicleFiltersAccordion">
                    <div class="accordion-item border-0">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed py-3 shadow-none" type="button"
                                data-bs-toggle="collapse" data-bs-target="#vehicleFiltersCollapse"
                                aria-expanded="false" aria-controls="vehicleFiltersCollapse">
                                <i class="fas fa-sliders-h me-2"></i>Search &amp; Filters
                                @if($hasActiveFilters)
                                    <span class="badge bg-primary ms-2">Active</span>
                                @endif
                                <span class="text-muted small fw-normal ms-2 d-none d-md-inline">Apply to list and exports</span>
                            </button>
                        </h2>
                        <div id="vehicleFiltersCollapse" class="accordion-collapse collapse" data-bs-parent="#vehicleFiltersAccordion">
                            <div class="accordion-body pt-0">
                                <form method="GET" action="{{ route('vehicles.index') }}" class="row g-3">
                        <div class="col-lg-4 col-md-6">
                            <label class="form-label small mb-0">Keywords</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" class="form-control" name="search"
                                       placeholder="Make, model, variant, plate…"
                                       value="{{ $search ?? '' }}">
                            </div>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label small mb-0">Status</label>
                            <select class="form-select" name="status">
                                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All status</option>
                                <option value="Available" {{ $status === 'Available' ? 'selected' : '' }}>Available</option>
                                <option value="Reserved" {{ $status === 'Reserved' ? 'selected' : '' }}>Reserved</option>
                                <option value="Released" {{ $status === 'Released' ? 'selected' : '' }}>Released</option>
                                <option value="Under Maintenance" {{ $status === 'Under Maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                                <option value="Forfeited" {{ $status === 'Forfeited' ? 'selected' : '' }}>Forfeited</option>
                                <option value="Archived" {{ $status === 'Archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label small mb-0">Showroom</label>
                            <select class="form-select" name="branch_location_id">
                                <option value="">All showrooms</option>
                                @foreach($branches ?? [] as $branch)
                                    <option value="{{ $branch->id }}" {{ (string) ($branchLocationId ?? '') === (string) $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <label class="form-label small mb-0">Year from</label>
                            <input type="number" class="form-control" name="year_from" min="1990" max="{{ date('Y') + 1 }}" placeholder="Min"
                                   value="{{ $yearFrom ?? '' }}">
                        </div>
                        <div class="col-lg-2 col-md-3">
                            <label class="form-label small mb-0">Year to</label>
                            <input type="number" class="form-control" name="year_to" min="1990" max="{{ date('Y') + 1 }}" placeholder="Max"
                                   value="{{ $yearTo ?? '' }}">
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label small mb-0">Transmission</label>
                            <select class="form-select" name="transmission">
                                <option value="">Any</option>
                                <option value="Manual" {{ ($transmission ?? '') === 'Manual' ? 'selected' : '' }}>Manual</option>
                                <option value="Automatic" {{ ($transmission ?? '') === 'Automatic' ? 'selected' : '' }}>Automatic</option>
                            </select>
                        </div>
                        <div class="col-lg-2 col-md-6">
                            <label class="form-label small mb-0">Fuel type</label>
                            <select class="form-select" name="fuel_type">
                                <option value="">Any</option>
                                <option value="Diesel" {{ ($fuelType ?? '') === 'Diesel' ? 'selected' : '' }}>Diesel</option>
                                <option value="Gasoline" {{ ($fuelType ?? '') === 'Gasoline' ? 'selected' : '' }}>Gasoline</option>
                                <option value="Hybrid" {{ ($fuelType ?? '') === 'Hybrid' ? 'selected' : '' }}>Hybrid</option>
                                <option value="Electric" {{ ($fuelType ?? '') === 'Electric' ? 'selected' : '' }}>Electric</option>
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label small mb-0">Body type (contains)</label>
                            <input type="text" class="form-control" name="body_type" placeholder="e.g. SUV, Sedan"
                                   value="{{ $bodyType ?? '' }}">
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label small mb-0">Purchased from (contains)</label>
                            <input type="text" class="form-control" name="purchased_from" placeholder="Seller / source"
                                   value="{{ $purchasedFrom ?? '' }}">
                        </div>
                        <div class="col-lg-3 col-md-6 reservation-date-filter-field" @if(($status ?? '') !== 'Reserved' && empty($reservationDateFrom) && empty($reservationDateTo)) style="display:none" @endif>
                            <label class="form-label small mb-0">Reservation date from</label>
                            <input type="date" class="form-control" name="reservation_date_from" id="reservation_date_from"
                                   value="{{ $reservationDateFrom ?? '' }}">
                        </div>
                        <div class="col-lg-3 col-md-6 reservation-date-filter-field" @if(($status ?? '') !== 'Reserved' && empty($reservationDateFrom) && empty($reservationDateTo)) style="display:none" @endif>
                            <label class="form-label small mb-0">Reservation date to</label>
                            <input type="date" class="form-control" name="reservation_date_to" id="reservation_date_to"
                                   value="{{ $reservationDateTo ?? '' }}">
                        </div>
                        <div class="col-lg-3 col-md-6 release-date-filter-field">
                            <label class="form-label small mb-0">Release date from</label>
                            <input type="date" class="form-control" name="release_date_from" id="release_date_from"
                                   value="{{ $releaseDateFrom ?? '' }}">
                        </div>
                        <div class="col-lg-3 col-md-6 release-date-filter-field">
                            <label class="form-label small mb-0">Release date to</label>
                            <input type="date" class="form-control" name="release_date_to" id="release_date_to"
                                   value="{{ $releaseDateTo ?? '' }}">
                        </div>
                        <div class="col-12 d-flex flex-wrap align-items-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Apply filters
                            </button>
                            <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Clear all
                            </a>
                        </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @if($hasActiveFilters)
                    <div class="card-body py-2 border-top">
                        <div class="d-flex flex-wrap gap-2">
                            @if($search)
                                <span class="badge badge-neutral">
                                    <i class="fas fa-search me-1"></i>{{ $search }}
                                    <a href="{{ route('vehicles.index', request()->except('search', 'page')) }}" class="text-white ms-1">&times;</a>
                                </span>
                            @endif
                            @if($status !== 'all')
                                <span class="badge badge-neutral">
                                    {{ $status }}
                                    <a href="{{ route('vehicles.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}" class="text-white ms-1">&times;</a>
                                </span>
                            @endif
                            @if($branchLocationId)
                                <span class="badge badge-neutral">
                                    Showroom: {{ $selectedBranchName ?? ('#'.$branchLocationId) }}
                                    <a href="{{ route('vehicles.index', request()->except('branch_location_id', 'page')) }}" class="text-white ms-1">&times;</a>
                                </span>
                            @endif
                            @if($yearFrom)
                                <span class="badge badge-neutral">Year ≥ {{ $yearFrom }} <a href="{{ route('vehicles.index', request()->except('year_from', 'page')) }}" class="text-white ms-1">&times;</a></span>
                            @endif
                            @if($yearTo)
                                <span class="badge badge-neutral">Year ≤ {{ $yearTo }} <a href="{{ route('vehicles.index', request()->except('year_to', 'page')) }}" class="text-white ms-1">&times;</a></span>
                            @endif
                            @if($transmission)
                                <span class="badge badge-neutral">{{ $transmission }} <a href="{{ route('vehicles.index', request()->except('transmission', 'page')) }}" class="text-white ms-1">&times;</a></span>
                            @endif
                            @if($fuelType)
                                <span class="badge badge-neutral">{{ $fuelType }} <a href="{{ route('vehicles.index', request()->except('fuel_type', 'page')) }}" class="text-white ms-1">&times;</a></span>
                            @endif
                            @if($bodyType)
                                <span class="badge badge-neutral">Body: {{ $bodyType }} <a href="{{ route('vehicles.index', request()->except('body_type', 'page')) }}" class="text-white ms-1">&times;</a></span>
                            @endif
                            @if($purchasedFrom)
                                <span class="badge badge-neutral">From: {{ Str::limit($purchasedFrom, 24) }} <a href="{{ route('vehicles.index', request()->except('purchased_from', 'page')) }}" class="text-white ms-1">&times;</a></span>
                            @endif
                            @if($reservationDateFrom)
                                <span class="badge badge-neutral">Reservation ≥ {{ date('M d, Y', strtotime($reservationDateFrom)) }} <a href="{{ route('vehicles.index', request()->except('reservation_date_from', 'page')) }}" class="text-white ms-1">&times;</a></span>
                            @endif
                            @if($reservationDateTo)
                                <span class="badge badge-neutral">Reservation ≤ {{ date('M d, Y', strtotime($reservationDateTo)) }} <a href="{{ route('vehicles.index', request()->except('reservation_date_to', 'page')) }}" class="text-white ms-1">&times;</a></span>
                            @endif
                            @if($releaseDateFrom)
                                <span class="badge badge-neutral">Release ≥ {{ date('M d, Y', strtotime($releaseDateFrom)) }} <a href="{{ route('vehicles.index', request()->except('release_date_from', 'page')) }}" class="text-white ms-1">&times;</a></span>
                            @endif
                            @if($releaseDateTo)
                                <span class="badge badge-neutral">Release ≤ {{ date('M d, Y', strtotime($releaseDateTo)) }} <a href="{{ route('vehicles.index', request()->except('release_date_to', 'page')) }}" class="text-white ms-1">&times;</a></span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Vehicles list -->
            <div class="card mb-4">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="statusTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $status === 'Available' ? 'active' : '' }}" 
                               href="{{ route('vehicles.index', array_merge(request()->except('page', 'status'), ['status' => 'Available'])) }}" 
                               role="tab">
                                <i class="fas fa-check-circle me-1"></i>Available
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $status === 'Reserved' ? 'active' : '' }}" 
                               href="{{ route('vehicles.index', array_merge(request()->except('page', 'status'), ['status' => 'Reserved'])) }}" 
                               role="tab">
                                <i class="fas fa-clock me-1"></i>Reserved
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $status === 'Released' ? 'active' : '' }}" 
                               href="{{ route('vehicles.index', array_merge(request()->except('page', 'status'), ['status' => 'Released'])) }}" 
                               role="tab">
                                <i class="fas fa-check-double me-1"></i>Released
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $status === 'Forfeited' ? 'active' : '' }}" 
                               href="{{ route('vehicles.index', array_merge(request()->except('page', 'status'), ['status' => 'Forfeited'])) }}" 
                               role="tab">
                                <i class="fas fa-times-circle me-1"></i>Forfeited
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $status === 'Under Maintenance' ? 'active' : '' }}" 
                               href="{{ route('vehicles.index', array_merge(request()->except('page', 'status'), ['status' => 'Under Maintenance'])) }}" 
                               role="tab">
                                <i class="fas fa-tools me-1"></i>Under Maintenance
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $status === 'Archived' ? 'active' : '' }}" 
                               href="{{ route('vehicles.index', array_merge(request()->except('page', 'status'), ['status' => 'Archived'])) }}" 
                               role="tab">
                                <i class="fas fa-archive me-1"></i>Archived
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $status === 'all' ? 'active' : '' }}" 
                               href="{{ route('vehicles.index', array_merge(request()->except('page', 'status'), ['status' => 'all'])) }}" 
                               role="tab">
                                <i class="fas fa-list me-1"></i>All Units
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center gap-2 mb-3">
                        <h5 class="card-title mb-0">
                            @if($search && $status !== 'all')
                                Search Results - {{ $status }} Vehicles
                            @elseif($search)
                                Search Results
                            @elseif($status === 'all')
                                All Vehicles
                            @else
                                {{ $status }} Vehicles
                            @endif
                            @php
                                $totalBadgeClass = match ($status) {
                                    'Available', 'Released' => 'badge-green',
                                    'Reserved', 'Forfeited' => 'badge-red',
                                    'Under Maintenance', 'Archived' => 'badge-neutral',
                                    default => 'badge-all-units',
                                };
                            @endphp
                            <span class="badge {{ $totalBadgeClass }} ms-2" data-vehicle-count-badge>{{ $vehicles->total() }} total</span>
                            @if($search)
                                <span class="badge badge-neutral ms-1">for "{{ $search }}"</span>
                            @endif
                            @if($status !== 'all')
                                <span class="badge badge-neutral ms-1">{{ $status }}</span>
                            @endif
                        </h5>

                        @if($status === 'Released' && $vehicles->count() > 0)
                            <div class="dropdown flex-shrink-0">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false"
                                        title="Export filtered Released results">
                                    <i class="fas fa-file-export me-1"></i>Export
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('vehicles.export-list') }}?{{ http_build_query(array_merge(request()->except('page'), ['status' => 'Released', 'format' => 'csv'])) }}">
                                            <i class="fas fa-file-excel text-success me-2"></i>Excel (CSV)
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('vehicles.export-list') }}?{{ http_build_query(array_merge(request()->except('page'), ['status' => 'Released', 'format' => 'pdf'])) }}">
                                            <i class="fas fa-file-pdf text-danger me-2"></i>PDF
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        @endif
                    </div>

                    <div class="row g-2 mb-3">
                        @foreach(($locationCounts ?? []) as $loc)
                            @php
                                $locKey = strtolower(trim($loc['name']));
                                $cardStyle = match (true) {
                                    $locKey === 'annex' => 'background-color: #fff3cd; border-color: #ffc107 !important;',
                                    $locKey === 'flagship' => 'background-color: #cff4fc; border-color: #0dcaf0 !important;',
                                    default => 'background-color: #f8f9fa; border-color: #ced4da !important;',
                                };
                                $badgeClass = match (true) {
                                    $locKey === 'annex' => 'bg-warning text-dark',
                                    $locKey === 'flagship' => 'bg-info text-white',
                                    default => 'bg-secondary text-white',
                                };
                            @endphp
                            <div class="col-sm-6 col-lg-3">
                                <div class="border rounded px-3 py-2 h-100 d-flex align-items-center justify-content-between" style="{{ $cardStyle }}">
                                    <div>
                                        <div class="small text-muted text-uppercase fw-semibold">Location {{ $loc['name'] }}</div>
                                        <div class="fs-5 fw-bold text-dark">{{ number_format($loc['count']) }}</div>
                                    </div>
                                    <span class="badge {{ $badgeClass }}">{{ $loc['name'] }}</span>
                                </div>
                            </div>
                        @endforeach
                        @if(($unassignedLocationCount ?? 0) > 0)
                            <div class="col-sm-6 col-lg-3">
                                <div class="border rounded px-3 py-2 h-100 d-flex align-items-center justify-content-between" style="background-color: #f8f9fa; border-color: #adb5bd !important;">
                                    <div>
                                        <div class="small text-muted text-uppercase fw-semibold">No Location</div>
                                        <div class="fs-5 fw-bold text-dark">{{ number_format($unassignedLocationCount) }}</div>
                                    </div>
                                    <span class="badge bg-light text-dark border">—</span>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if(!empty($excelReconcileNotes))
                        @foreach($excelReconcileNotes as $note)
                            <div class="alert alert-warning border-warning mb-3" role="alert">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="fas fa-exclamation-triangle mt-1"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold mb-1">
                                            Excel vs database count mismatch
                                            @if(!empty($note['label']))
                                                — {{ $note['label'] }}
                                            @endif
                                        </div>
                                        <div class="mb-2">{{ $note['summary'] }}</div>
                                        <div class="row g-2 mb-2 small">
                                            <div class="col-auto">
                                                <span class="badge bg-secondary">Excel rows: {{ number_format($note['excel_row_count'] ?? 0) }}</span>
                                            </div>
                                            <div class="col-auto">
                                                <span class="badge bg-secondary">Excel unique plates: {{ number_format($note['excel_unique_plates'] ?? 0) }}</span>
                                            </div>
                                            <div class="col-auto">
                                                <span class="badge bg-dark">Database: {{ number_format($note['db_count'] ?? 0) }}</span>
                                            </div>
                                            @if(!empty($note['tab']))
                                                <div class="col-auto">
                                                    <span class="badge bg-light text-dark border">Tab: {{ $note['tab'] }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        @if(!empty($note['reasons']))
                                            <div class="small mb-1 fw-semibold">Why it doesn’t match:</div>
                                            <ul class="mb-0 small ps-3">
                                                @foreach($note['reasons'] as $reason)
                                                    <li class="mb-1">{{ $reason['message'] }}</li>
                                                @endforeach
                                            </ul>
                                            @if(($note['hidden_reason_count'] ?? 0) > 0)
                                                <div class="small text-muted mt-1">…and {{ $note['hidden_reason_count'] }} more reason(s).</div>
                                            @endif
                                        @endif
                                        @if(!empty($note['source_name']))
                                            <div class="small text-muted mt-2">Source: {{ $note['source_name'] }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @if($vehicles->count() > 0)
                        <div class="table-responsive" id="vehiclesTableWrap">
                            <table class="table table-striped table-hover" id="vehiclesTable">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Location</th>
                                        <th>Vehicle</th>
                                        <th>Year</th>
                                        <th>Make</th>
                                        <th>Model</th>
                                        <th>Plate Number</th>
                                        <th>Colour</th>
                                        <th>Purchase Price</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="vehiclesTableBody">
                                    @foreach($vehicles as $vehicle)
                                    <tr data-vehicle-id="{{ $vehicle->id }}">
                                        <td>{{ ($vehicles->currentPage() - 1) * $vehicles->perPage() + $loop->iteration }}</td>
                                        <td>
                                            @if($vehicle->branchLocation)
                                                @include('partials.showroom-badge', ['name' => $vehicle->branchLocation->name])
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($vehicle->primaryImage)
                                                    <img src="{{ $vehicle->primaryImage->thumbnail_url }}" alt="Vehicle" class="me-3" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                                                @else
                                                    <div class="me-3 d-flex align-items-center justify-content-center bg-light" style="width: 60px; height: 40px; border-radius: 4px;">
                                                        <i class="fas fa-car text-muted"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <strong>{{ $vehicle->full_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $vehicle->transmission }} • {{ $vehicle->fuel_type }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $vehicle->year }}</td>
                                        <td>{{ $vehicle->make }}</td>
                                        <td>{{ $vehicle->model }}</td>
                                        <td><span class="badge bg-secondary">{{ $vehicle->plate_number }}</span></td>
                                        <td>{{ $vehicle->colour }}</td>
                                        <td>{{ $vehicle->formatted_purchase_price }}</td>
                                        <td>
                                            @if($vehicle->status === 'Archived')
                                                <span class="badge badge-neutral">Archived</span>
                                                @if($vehicle->archived_at)
                                                    <small class="text-muted d-block">{{ $vehicle->archived_at->format('M d, Y') }}</small>
                                                @endif
                                            @elseif($vehicle->status === 'Forfeited' || $vehicle->forfeitDetails->count() > 0)
                                                <span class="badge badge-red">Forfeited</span>
                                            @elseif($vehicle->status === 'Available')
                                                <span class="badge badge-green">{{ $vehicle->status }}</span>
                                            @elseif($vehicle->status === 'Under Maintenance')
                                                <span class="badge badge-neutral">{{ $vehicle->status }}</span>
                                            @elseif($vehicle->status === 'Reserved')
                                                <span class="badge badge-red">{{ $vehicle->status }}</span>
                                            @elseif($vehicle->status === 'Released')
                                                <span class="badge badge-green">{{ $vehicle->status }}</span>
                                            @else
                                                <span class="badge badge-neutral">{{ $vehicle->status }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                                    <i class="fas fa-eye me-1"></i>View Details
                                                </a>
                                                @canPage('vehicles', 'update')
                                                @if(in_array($status, ['Available', 'Released', 'Forfeited'], true) && $vehicle->isArchiveable())
                                                <form action="{{ route('vehicles.archive', $vehicle) }}" method="POST" class="d-inline archive-vehicle-form">
                                                    @csrf
                                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-1 archive-vehicle-btn" title="Archive this unit"
                                                            data-label="{{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->plate_number }})">
                                                        <i class="fas fa-archive me-1"></i>Archive
                                                    </button>
                                                </form>
                                                @endif
                                                @endcanPage
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $vehicles->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            @if($search || $status !== 'all' || ($hasExtraFilters ?? false))
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">No vehicles found</h4>
                                <p class="text-muted">
                                    @if($search && $status !== 'all')
                                        No vehicles match your search for "{{ $search }}" with status "{{ $status }}".
                                    @elseif($search)
                                        No vehicles match your search for "{{ $search }}".
                                    @elseif($status !== 'all')
                                        No vehicles with status "{{ $status }}" for the current filters.
                                    @else
                                        No vehicles match the filters you applied. Try clearing some filters or widening the year range.
                                    @endif
                                </p>
                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                    @if($search)
                                        <a href="{{ route('vehicles.index', request()->except('search', 'page')) }}" class="btn btn-outline-primary">
                                            <i class="fas fa-times me-1"></i>Clear search
                                        </a>
                                    @endif
                                    @if($status !== 'all')
                                        <a href="{{ route('vehicles.index', array_merge(request()->except('status', 'page'), ['status' => 'all'])) }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-filter me-1"></i>All statuses
                                        </a>
                                    @endif
                                    <a href="{{ route('vehicles.index') }}" class="btn btn-outline-info">
                                        <i class="fas fa-list me-1"></i>Reset all filters
                                    </a>
                                    @canPage('vehicles', 'update')
                                    @if($status === 'Archived')
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#archiveVehicleModal">
                                        <i class="fas fa-archive me-1"></i>Add to Archive
                                    </button>
                                    @else
                                    @canPage('vehicles', 'create')
                                    <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i>Add New Vehicle
                                    </a>
                                    @endcanPage
                                    @endif
                                    @endcanPage
                                </div>
                            @else
                                <i class="fas fa-{{ $status === 'Archived' ? 'archive' : 'car' }} fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">No vehicles found</h4>
                                @if($status === 'Archived')
                                <p class="text-muted">Search for Available, Released, or Forfeited units to add them to the archived list.</p>
                                @canPage('vehicles', 'update')
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#archiveVehicleModal">
                                    <i class="fas fa-archive me-1"></i>Add to Archive
                                </button>
                                @endcanPage
                                @else
                                <p class="text-muted">Start by adding your first vehicle to the inventory.</p>
                                @canPage('vehicles', 'create')
                                <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Add New Vehicle
                                </a>
                                @endcanPage
                                @endif
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>

@canPage('vehicles', 'update')
<div class="modal fade" id="archiveVehicleModal" tabindex="-1" aria-labelledby="archiveVehicleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="archiveVehicleModalLabel">
                    <i class="fas fa-archive me-2"></i>Add to Archive
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Search for units in <strong>Available</strong>, <strong>Released</strong>, or <strong>Forfeited</strong> to move them into the archived list. Archived units only appear on this tab.</p>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="search" class="form-control" id="archiveVehicleSearch" placeholder="Search by plate, make, model…" autocomplete="off">
                </div>
                <div id="archiveVehicleLoading" class="text-center py-4 text-muted d-none">
                    <i class="fas fa-spinner fa-spin me-2"></i>Searching…
                </div>
                <div id="archiveVehicleEmpty" class="text-center py-4 text-muted d-none">
                    <i class="fas fa-car-side fa-2x mb-2 d-block opacity-50"></i>
                    No matching units found. Only Available, Released, or Forfeited units can be archived.
                </div>
                <div id="archiveVehicleResults" class="list-group list-group-flush mt-3"></div>
            </div>
        </div>
    </div>
</div>
<form id="archiveVehicleModalForm" method="POST" class="d-none" aria-hidden="true">
    @csrf
</form>
@endcanPage

<style>
.stat-card {
    border: 1px solid #dee2e6;
}
.stat-card .icon-wrap {
    line-height: 1;
}
.stat-neutral { border-left: 3px solid #6c757d; }
.stat-green { border-left: 3px solid #198754; }
.stat-red { border-left: 3px solid #dc3545; }

.badge-green {
    background-color: #198754;
    color: #fff;
}
.badge-red {
    background-color: #dc3545;
    color: #fff;
}
.badge-neutral {
    background-color: #6c757d;
    color: #fff;
}
.badge-all-units {
    background-color: #212529;
    color: #fff;
}

#statusTabs .nav-link {
    color: #212529;
}
#statusTabs .nav-link.active {
    color: #212529;
    border-color: #dee2e6 #dee2e6 #fff;
}
.pagination .page-link {
    color: #212529;
    border: 1px solid #dee2e6;
    padding: 0.5rem 0.75rem;
    margin: 0 2px;
    border-radius: 0.375rem;
    transition: all 0.15s ease-in-out;
}

.pagination .page-link:hover {
    color: #212529;
    background-color: #e9ecef;
    border-color: #6c757d;
}

.pagination .page-item.active .page-link {
    background-color: #198754;
    border-color: #198754;
    color: white;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
}

.pagination .page-link:focus {
    box-shadow: 0 0 0 0.2rem rgba(33, 37, 41, 0.2);
}
</style>
@endsection

@section('scripts')
<script>
(function () {
    const currentStatus = @json($status);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Show release-date range when filtering Released (or when range values are already set)
    const statusSelect = document.querySelector('form[action="{{ route('vehicles.index') }}"] select[name="status"]');
    const releaseDateFields = document.querySelectorAll('.release-date-filter-field');
    const releaseFromInput = document.getElementById('release_date_from');
    const releaseToInput = document.getElementById('release_date_to');
    const reservationDateFields = document.querySelectorAll('.reservation-date-filter-field');
    const reservationFromInput = document.getElementById('reservation_date_from');
    const reservationToInput = document.getElementById('reservation_date_to');

    function toggleReleaseDateFilters() {
        const selected = statusSelect ? statusSelect.value : currentStatus;
        const hasRangeValues = Boolean((releaseFromInput && releaseFromInput.value) || (releaseToInput && releaseToInput.value));
        const show = selected === 'Released' || hasRangeValues;
        releaseDateFields.forEach(function (el) {
            el.style.display = show ? '' : 'none';
        });
    }

    function toggleReservationDateFilter() {
        const selected = statusSelect ? statusSelect.value : currentStatus;
        const hasValue = Boolean((reservationFromInput && reservationFromInput.value) || (reservationToInput && reservationToInput.value));
        const show = selected === 'Reserved' || hasValue;
        reservationDateFields.forEach(function (el) {
            el.style.display = show ? '' : 'none';
        });
        if (!show) {
            if (reservationFromInput) reservationFromInput.value = '';
            if (reservationToInput) reservationToInput.value = '';
        }
    }

    if (statusSelect) {
        statusSelect.addEventListener('change', function () {
            toggleReleaseDateFilters();
            toggleReservationDateFilter();
        });
    }
    toggleReleaseDateFilters();
    toggleReservationDateFilter();

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function showArchiveSuccess(data) {
        return Swal.fire({
            icon: 'success',
            title: data.swal_title || 'Archived',
            text: data.message || 'Vehicle moved to Archived successfully.',
            confirmButtonColor: '#198754',
            timer: 2500,
            timerProgressBar: true
        });
    }

    function showArchiveError(data) {
        return Swal.fire({
            icon: 'error',
            title: data.swal_title || 'Error',
            text: data.message || 'Could not archive this vehicle.',
            confirmButtonColor: '#dc3545'
        });
    }

    function updateVehicleCount(delta) {
        const badge = document.querySelector('[data-vehicle-count-badge]');
        if (!badge) {
            return;
        }

        const match = badge.textContent.match(/(\d+)/);
        if (match) {
            const next = Math.max(0, parseInt(match[1], 10) + delta);
            badge.textContent = next + ' total';
        }
    }

    function archiveVehicleRequest(url) {
        return fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).then(function (response) {
            return response.json().then(function (data) {
                if (!response.ok || !data.success) {
                    throw data;
                }
                return data;
            });
        });
    }

    function buildArchivedTableRow(vehicle, rowNumber) {
        const thumb = vehicle.thumbnail_url
            ? '<img src="' + escapeHtml(vehicle.thumbnail_url) + '" alt="Vehicle" class="me-3" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">'
            : '<div class="me-3 d-flex align-items-center justify-content-center bg-light" style="width: 60px; height: 40px; border-radius: 4px;"><i class="fas fa-car text-muted"></i></div>';

        const archivedDate = vehicle.archived_at
            ? '<small class="text-muted d-block">' + escapeHtml(vehicle.archived_at) + '</small>'
            : '';

        return '<tr data-vehicle-id="' + vehicle.id + '">' +
            '<td>' + rowNumber + '</td>' +
            '<td>' + window.showroomBadgeHtml(vehicle.location) + '</td>' +
            '<td><div class="d-flex align-items-center">' + thumb +
                '<div><strong>' + escapeHtml(vehicle.full_name) + '</strong><br>' +
                '<small class="text-muted">' + escapeHtml(vehicle.transmission) + ' • ' + escapeHtml(vehicle.fuel_type) + '</small></div></div></td>' +
            '<td>' + escapeHtml(vehicle.year) + '</td>' +
            '<td>' + escapeHtml(vehicle.make) + '</td>' +
            '<td>' + escapeHtml(vehicle.model) + '</td>' +
            '<td><span class="badge bg-secondary">' + escapeHtml(vehicle.plate_number) + '</span></td>' +
            '<td>' + escapeHtml(vehicle.colour) + '</td>' +
            '<td>' + escapeHtml(vehicle.purchase_price) + '</td>' +
            '<td><span class="badge badge-neutral">Archived</span>' + archivedDate + '</td>' +
            '<td><div class="btn-group" role="group">' +
                '<a href="' + escapeHtml(vehicle.show_url) + '" class="btn btn-sm btn-outline-primary" title="View Details">' +
                '<i class="fas fa-eye me-1"></i>View Details</a></div></td>' +
        '</tr>';
    }

    function prependArchivedVehicleRow(vehicle) {
        const tbody = document.getElementById('vehiclesTableBody');
        if (!tbody) {
            window.location.reload();
            return;
        }

        const existingRows = tbody.querySelectorAll('tr[data-vehicle-id]');
        tbody.insertAdjacentHTML('afterbegin', buildArchivedTableRow(vehicle, existingRows.length + 1));
        updateVehicleCount(1);
    }

    function removeVehicleRow(vehicleId) {
        const row = document.querySelector('tr[data-vehicle-id="' + vehicleId + '"]');
        if (!row) {
            return;
        }

        row.remove();
        updateVehicleCount(-1);

        const tbody = document.getElementById('vehiclesTableBody');
        if (tbody && tbody.querySelectorAll('tr[data-vehicle-id]').length === 0) {
            window.location.reload();
        }
    }

    function handleArchiveSuccess(data, options) {
        if (currentStatus === 'Archived') {
            if (document.getElementById('vehiclesTableBody')) {
                prependArchivedVehicleRow(data.vehicle);
                if (options && typeof options.onArchivedTab === 'function') {
                    options.onArchivedTab(data);
                }
                showArchiveSuccess(data);
            } else {
                showArchiveSuccess(data).then(function () {
                    window.location.reload();
                });
            }
            return;
        }

        if (['Available', 'Released', 'Forfeited'].includes(currentStatus)) {
            removeVehicleRow(data.vehicle_id);
            showArchiveSuccess(data);
            return;
        }

        showArchiveSuccess(data).then(function () {
            window.location.reload();
        });
    }

    function confirmAndArchive(url, label, options) {
        Swal.fire({
            title: 'Archive this unit?',
            text: 'Move ' + label + ' to Archived? It will no longer appear in Available, Reserved, Released, or Forfeited.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#6c757d',
            cancelButtonColor: '#adb5bd',
            confirmButtonText: 'Yes, archive',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then(function (result) {
            if (!result.isConfirmed) {
                return;
            }

            Swal.fire({
                title: 'Archiving…',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: function () {
                    Swal.showLoading();
                }
            });

            archiveVehicleRequest(url)
                .then(function (data) {
                    Swal.close();
                    handleArchiveSuccess(data, options);
                })
                .catch(function (error) {
                    Swal.close();
                    showArchiveError(error || {});
                });
        });
    }

    document.querySelectorAll('.archive-vehicle-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const form = btn.closest('form');
            const label = btn.getAttribute('data-label') || 'this vehicle';
            if (!form) {
                return;
            }

            confirmAndArchive(form.action, label);
        });
    });

@canPage('vehicles', 'update')
    const searchInput = document.getElementById('archiveVehicleSearch');
    const resultsEl = document.getElementById('archiveVehicleResults');
    const loadingEl = document.getElementById('archiveVehicleLoading');
    const emptyEl = document.getElementById('archiveVehicleEmpty');
    const modalEl = document.getElementById('archiveVehicleModal');
    const searchUrl = @json(route('vehicles.search-archiveable'));
    let searchTimer = null;

    if (searchInput && resultsEl) {
        function statusBadgeClass(status) {
            if (status === 'Available' || status === 'Released') return 'bg-success';
            if (status === 'Forfeited' || status === 'Reserved') return 'bg-danger';
            return 'bg-secondary';
        }

        function renderResults(items) {
            loadingEl.classList.add('d-none');

            if (!items.length) {
                resultsEl.innerHTML = '';
                emptyEl.classList.remove('d-none');
                return;
            }

            emptyEl.classList.add('d-none');
            resultsEl.innerHTML = items.map(function (item) {
                return '<div class="list-group-item d-flex justify-content-between align-items-center px-0" data-archive-result-id="' + item.id + '">' +
                    '<div class="me-3">' +
                        '<div class="fw-semibold">' + escapeHtml(item.label) + '</div>' +
                        '<span class="badge ' + statusBadgeClass(item.status) + '">' + escapeHtml(item.status) + '</span>' +
                    '</div>' +
                    '<button type="button" class="btn btn-sm btn-outline-secondary archive-from-modal-btn" ' +
                        'data-url="' + escapeHtml(item.archive_url) + '" data-label="' + escapeHtml(item.label) + '">' +
                        '<i class="fas fa-archive me-1"></i>Archive</button>' +
                '</div>';
            }).join('');
        }

        function searchArchiveable(query) {
            loadingEl.classList.remove('d-none');
            emptyEl.classList.add('d-none');
            resultsEl.innerHTML = '';

            fetch(searchUrl + '?q=' + encodeURIComponent(query), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(function (response) { return response.json(); })
                .then(renderResults)
                .catch(function () {
                    loadingEl.classList.add('d-none');
                    emptyEl.classList.remove('d-none');
                    emptyEl.innerHTML = '<i class="fas fa-car-side fa-2x mb-2 d-block opacity-50"></i>Could not load vehicles. Please try again.';
                });
        }

        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function () {
                searchArchiveable(searchInput.value.trim());
            }, 300);
        });

        if (modalEl) {
            modalEl.addEventListener('shown.bs.modal', function () {
                searchInput.value = '';
                searchArchiveable('');
                searchInput.focus();
            });
        }

        resultsEl.addEventListener('click', function (event) {
            const btn = event.target.closest('.archive-from-modal-btn');
            if (!btn) {
                return;
            }

            const label = btn.getAttribute('data-label') || 'this vehicle';
            const url = btn.getAttribute('data-url');
            const resultItem = btn.closest('[data-archive-result-id]');
            const resultId = resultItem ? resultItem.getAttribute('data-archive-result-id') : null;

            confirmAndArchive(url, label, {
                onArchivedTab: function () {
                    if (resultItem) {
                        resultItem.remove();
                    }
                    if (!resultsEl.querySelector('[data-archive-result-id]')) {
                        emptyEl.classList.remove('d-none');
                    }
                    searchArchiveable(searchInput.value.trim());
                }
            });
        });
    }
@endcanPage
})();
</script>
@endsection
