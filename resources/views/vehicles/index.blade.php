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
                    @canPage('vehicles', 'create')
                    <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Add New Vehicle
                    </a>
                    @endcanPage
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Search & filters -->
            <div class="card mb-4">
                <div class="card-header py-2">
                    <span class="small text-muted"><i class="fas fa-sliders-h me-1"></i>Filters apply to the list and to exports</span>
                </div>
                <div class="card-body">
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
                        <div class="col-lg-3 col-md-6">
                            <label class="form-label small mb-0">Reservation date</label>
                            <input type="date" class="form-control" name="reservation_date"
                                   value="{{ $reservationDate ?? '' }}">
                        </div>
                        <div class="col-12 d-flex flex-wrap align-items-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-1"></i>Apply filters
                            </button>
                            <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Clear all
                            </a>
                            <span class="text-muted small ms-lg-2">Export current results:</span>
                            <a href="{{ route('vehicles.export-list') }}?{{ http_build_query(array_merge(request()->except('page'), ['format' => 'csv'])) }}" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-file-excel me-1"></i>Excel (CSV)
                            </a>
                            <a href="{{ route('vehicles.export-list') }}?{{ http_build_query(array_merge(request()->except('page'), ['format' => 'pdf'])) }}" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-file-pdf me-1"></i>PDF
                            </a>
                        </div>
                    </form>
                    @php
                        $hasExtraFilters = ($yearFrom ?? null) || ($yearTo ?? null) || ($transmission ?? null) || ($fuelType ?? null) || ($bodyType ?? null) || ($purchasedFrom ?? null) || ($reservationDate ?? null);
                    @endphp
                    @if($search || $status !== 'all' || $hasExtraFilters)
                        <div class="mt-3 d-flex flex-wrap gap-2">
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
                            @if($reservationDate)
                                <span class="badge badge-neutral">Reservation: {{ date('M d, Y', strtotime($reservationDate)) }} <a href="{{ route('vehicles.index', request()->except('reservation_date', 'page')) }}" class="text-white ms-1">&times;</a></span>
                            @endif
                        </div>
                    @endif
                </div>
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
                                <span class="badge badge-green ms-1">{{ $availableCount }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $status === 'Reserved' ? 'active' : '' }}" 
                               href="{{ route('vehicles.index', array_merge(request()->except('page', 'status'), ['status' => 'Reserved'])) }}" 
                               role="tab">
                                <i class="fas fa-clock me-1"></i>Reserved
                                <span class="badge badge-red ms-1">{{ $reservedCount }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $status === 'Released' ? 'active' : '' }}" 
                               href="{{ route('vehicles.index', array_merge(request()->except('page', 'status'), ['status' => 'Released'])) }}" 
                               role="tab">
                                <i class="fas fa-check-double me-1"></i>Released
                                <span class="badge badge-green ms-1">{{ $releasedCount }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $status === 'Forfeited' ? 'active' : '' }}" 
                               href="{{ route('vehicles.index', array_merge(request()->except('page', 'status'), ['status' => 'Forfeited'])) }}" 
                               role="tab">
                                <i class="fas fa-times-circle me-1"></i>Forfeited
                                <span class="badge badge-red ms-1">{{ $forfeitedCount }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $status === 'Under Maintenance' ? 'active' : '' }}" 
                               href="{{ route('vehicles.index', array_merge(request()->except('page', 'status'), ['status' => 'Under Maintenance'])) }}" 
                               role="tab">
                                <i class="fas fa-tools me-1"></i>Under Maintenance
                                <span class="badge badge-neutral ms-1">{{ $underMaintenanceCount }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $status === 'all' ? 'active' : '' }}" 
                               href="{{ route('vehicles.index', array_merge(request()->except('page', 'status'), ['status' => 'all'])) }}" 
                               role="tab">
                                <i class="fas fa-list me-1"></i>All Units
                                <span class="badge badge-all-units ms-1">{{ $availableCount + $reservedCount + $releasedCount + $underMaintenanceCount + $forfeitedCount }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <h5 class="card-title mb-3">
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
                                'Under Maintenance' => 'badge-neutral',
                                default => 'badge-all-units',
                            };
                        @endphp
                        <span class="badge {{ $totalBadgeClass }} ms-2">{{ $vehicles->total() }} total</span>
                        @if($search)
                            <span class="badge badge-neutral ms-1">for "{{ $search }}"</span>
                        @endif
                        @if($status !== 'all')
                            <span class="badge badge-neutral ms-1">{{ $status }}</span>
                        @endif
                    </h5>
                    @if($vehicles->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
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
                                <tbody>
                                    @foreach($vehicles as $vehicle)
                                    <tr>
                                        <td>{{ ($vehicles->currentPage() - 1) * $vehicles->perPage() + $loop->iteration }}</td>
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
                                            @if($vehicle->status === 'Forfeited' || $vehicle->forfeitDetails->count() > 0)
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
                                    <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i>Add New Vehicle
                                    </a>
                                </div>
                            @else
                                <i class="fas fa-car fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">No vehicles found</h4>
                                <p class="text-muted">Start by adding your first vehicle to the inventory.</p>
                                <a href="{{ route('vehicles.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i>Add New Vehicle
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>

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
#statusTabs .nav-link .badge {
    background-color: #6c757d !important;
    color: #fff !important;
}
#statusTabs .nav-link.active {
    color: #212529;
    border-color: #dee2e6 #dee2e6 #fff;
}
#statusTabs .nav-link.active .badge-green {
    background-color: #198754 !important;
}
#statusTabs .nav-link.active .badge-red {
    background-color: #dc3545 !important;
}
#statusTabs .nav-link.active .badge-neutral {
    background-color: #6c757d !important;
}
#statusTabs .nav-link.active .badge-all-units {
    background-color: #212529 !important;
    color: #fff !important;
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

</script>
@endsection
