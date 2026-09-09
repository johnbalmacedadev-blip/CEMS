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
                    <button type="button" class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#vehicleExcelImportModal">
                        <i class="fas fa-file-import me-1"></i>Import Excel
                    </button>
                    @endcanPage
                    @if($status === 'Archived')
                    @canPage('vehicles', 'update')
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#archiveVehicleModal">
                        <i class="fas fa-archive me-1"></i>Add to Archive
                    </button>
                    @endcanPage
                    @elseif($status === 'Miscellaneous')
                    @canPage('vehicles', 'create')
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMiscellaneousModal">
                        <i class="fas fa-plus me-1"></i>Add New
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
            @if(($status ?? '') !== 'Miscellaneous')
            @php
                $hasExtraFilters = ($yearFrom ?? null) || ($yearTo ?? null) || ($transmission ?? null) || ($fuelType ?? null) || ($bodyType ?? null) || ($purchasedFrom ?? null) || ($branchLocationId ?? null)
                    || (($status ?? '') === 'Reserved' && (($reservationDateFrom ?? null) || ($reservationDateTo ?? null)))
                    || (($status ?? '') === 'Released' && (($releaseDateFrom ?? null) || ($releaseDateTo ?? null)));
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
                        <div class="col-lg-3 col-md-6 reservation-date-filter-field" @if(($status ?? '') !== 'Reserved') style="display:none" @endif>
                            <label class="form-label small mb-0">Reservation date from</label>
                            <input type="date" class="form-control" name="reservation_date_from" id="reservation_date_from"
                                   value="{{ ($status ?? '') === 'Reserved' ? ($reservationDateFrom ?? '') : '' }}">
                        </div>
                        <div class="col-lg-3 col-md-6 reservation-date-filter-field" @if(($status ?? '') !== 'Reserved') style="display:none" @endif>
                            <label class="form-label small mb-0">Reservation date to</label>
                            <input type="date" class="form-control" name="reservation_date_to" id="reservation_date_to"
                                   value="{{ ($status ?? '') === 'Reserved' ? ($reservationDateTo ?? '') : '' }}">
                        </div>
                        <div class="col-lg-3 col-md-6 release-date-filter-field" @if(($status ?? '') !== 'Released') style="display:none" @endif>
                            <label class="form-label small mb-0">Release date from</label>
                            <input type="date" class="form-control" name="release_date_from" id="release_date_from"
                                   value="{{ ($status ?? '') === 'Released' ? ($releaseDateFrom ?? '') : '' }}">
                        </div>
                        <div class="col-lg-3 col-md-6 release-date-filter-field" @if(($status ?? '') !== 'Released') style="display:none" @endif>
                            <label class="form-label small mb-0">Release date to</label>
                            <input type="date" class="form-control" name="release_date_to" id="release_date_to"
                                   value="{{ ($status ?? '') === 'Released' ? ($releaseDateTo ?? '') : '' }}">
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
                            @if(($status ?? '') === 'Reserved' && $reservationDateFrom)
                                <span class="badge badge-neutral">Reservation ≥ {{ date('M d, Y', strtotime($reservationDateFrom)) }} <a href="{{ route('vehicles.index', request()->except('reservation_date_from', 'page')) }}" class="text-white ms-1">&times;</a></span>
                            @endif
                            @if(($status ?? '') === 'Reserved' && $reservationDateTo)
                                <span class="badge badge-neutral">Reservation ≤ {{ date('M d, Y', strtotime($reservationDateTo)) }} <a href="{{ route('vehicles.index', request()->except('reservation_date_to', 'page')) }}" class="text-white ms-1">&times;</a></span>
                            @endif
                            @if(($status ?? '') === 'Released' && $releaseDateFrom)
                                <span class="badge badge-neutral">Release ≥ {{ date('M d, Y', strtotime($releaseDateFrom)) }} <a href="{{ route('vehicles.index', request()->except('release_date_from', 'page')) }}" class="text-white ms-1">&times;</a></span>
                            @endif
                            @if(($status ?? '') === 'Released' && $releaseDateTo)
                                <span class="badge badge-neutral">Release ≤ {{ date('M d, Y', strtotime($releaseDateTo)) }} <a href="{{ route('vehicles.index', request()->except('release_date_to', 'page')) }}" class="text-white ms-1">&times;</a></span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
            @endif

            <!-- Vehicles list -->
            @php
                $unitReportTabParams = function (string $targetStatus) {
                    $drop = ['page', 'status'];
                    if ($targetStatus !== 'Released') {
                        array_push($drop, 'release_date_from', 'release_date_to');
                    }
                    if ($targetStatus !== 'Reserved') {
                        array_push($drop, 'reservation_date_from', 'reservation_date_to', 'reservation_date');
                    }
                    if ($targetStatus !== 'Miscellaneous') {
                        array_push($drop, 'misc_location', 'misc_year', 'misc_month');
                    }

                    return array_merge(request()->except($drop), ['status' => $targetStatus]);
                };
                $locationVisibility = $locationVisibility ?? ['ids' => null, 'include_none' => true];
            @endphp
            <div class="card mb-4">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="statusTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $status === 'Available' ? 'active' : '' }}" 
                               href="{{ route('vehicles.index', $unitReportTabParams('Available')) }}" 
                               role="tab">
                                <i class="fas fa-check-circle me-1"></i>Available
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $status === 'Reserved' ? 'active' : '' }}" 
                               href="{{ route('vehicles.index', $unitReportTabParams('Reserved')) }}" 
                               role="tab">
                                <i class="fas fa-clock me-1"></i>Reserved
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $status === 'Released' ? 'active' : '' }}" 
                               href="{{ route('vehicles.index', $unitReportTabParams('Released')) }}" 
                               role="tab">
                                <i class="fas fa-check-double me-1"></i>Released
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $status === 'Forfeited' ? 'active' : '' }}" 
                               href="{{ route('vehicles.index', $unitReportTabParams('Forfeited')) }}" 
                               role="tab">
                                <i class="fas fa-times-circle me-1"></i>Forfeited
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $status === 'Under Maintenance' ? 'active' : '' }}" 
                               href="{{ route('vehicles.index', $unitReportTabParams('Under Maintenance')) }}" 
                               role="tab">
                                <i class="fas fa-tools me-1"></i>Under Maintenance
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $status === 'Archived' ? 'active' : '' }}" 
                               href="{{ route('vehicles.index', $unitReportTabParams('Archived')) }}" 
                               role="tab">
                                <i class="fas fa-archive me-1"></i>Archived
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $status === 'Miscellaneous' ? 'active' : '' }}"
                               href="{{ route('vehicles.index', $unitReportTabParams('Miscellaneous')) }}"
                               role="tab">
                                <i class="fas fa-ellipsis-h me-1"></i>Miscellaneous
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    @if($status === 'Miscellaneous')
                        @php
                            $miscItems = $miscellaneousTransactions ?? collect();
                        @endphp
                        <div class="d-flex justify-content-between align-items-center gap-2 mb-3 flex-wrap">
                            <h5 class="card-title mb-0">
                                Miscellaneous
                                <span class="badge badge-neutral ms-2">{{ $miscItems->total() }} total</span>
                                @if(($miscYear ?? 0) || ($miscMonth ?? 0) || ($miscLocation ?? '') !== '')
                                    <span class="badge badge-green ms-1">₱{{ number_format((float) ($miscFilteredTotal ?? 0), 2) }} filtered</span>
                                @endif
                            </h5>
                            <div class="d-flex align-items-end gap-2 flex-nowrap">
                                <form method="GET" action="{{ route('vehicles.index') }}" class="d-flex align-items-end gap-2 flex-nowrap">
                                    <input type="hidden" name="status" value="Miscellaneous">
                                    @if(!empty($search))
                                        <input type="hidden" name="search" value="{{ $search }}">
                                    @endif
                                    <div style="width: 118px;">
                                        <label for="misc_month" class="form-label mb-1 small text-muted">Month</label>
                                        <select name="misc_month" id="misc_month" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="">All Months</option>
                                            @foreach([
                                                1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                                                5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                                                9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
                                            ] as $monthNum => $monthLabel)
                                                <option value="{{ $monthNum }}" {{ (int) ($miscMonth ?? 0) === $monthNum ? 'selected' : '' }}>{{ $monthLabel }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="width: 118px;">
                                        <label for="misc_year" class="form-label mb-1 small text-muted">Year</label>
                                        <select name="misc_year" id="misc_year" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="">All Years</option>
                                            @foreach(($miscYearOptions ?? collect()) as $yearOpt)
                                                <option value="{{ $yearOpt }}" {{ (int) ($miscYear ?? 0) === (int) $yearOpt ? 'selected' : '' }}>{{ $yearOpt }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="width: 118px;">
                                        <label for="misc_location" class="form-label mb-1 small text-muted">Location</label>
                                        <select name="misc_location" id="misc_location" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="">All Locations</option>
                                            @foreach(\App\Models\MiscellaneousTransaction::locationOptions() as $locOpt)
                                                <option value="{{ $locOpt }}" {{ ($miscLocation ?? '') === $locOpt ? 'selected' : '' }}>{{ $locOpt }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </form>
                                @canPage('vehicles', 'create')
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addMiscellaneousModal">
                                    <i class="fas fa-plus me-1"></i>Add New
                                </button>
                                @endcanPage
                            </div>
                        </div>

                        @if($miscItems->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 160px;">Transaction Date</th>
                                            <th style="width: 140px;">Location</th>
                                            <th>Description</th>
                                            <th class="text-end" style="width: 160px;">Amount</th>
                                            <th class="text-end" style="width: 100px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($miscItems as $entry)
                                            <tr>
                                                <td>{{ optional($entry->transaction_date)->format('M d, Y') }}</td>
                                                <td>
                                                    @if($entry->location)
                                                        @include('partials.showroom-badge', ['name' => $entry->location])
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>{{ $entry->description }}</td>
                                                <td class="text-end">₱{{ number_format((float) $entry->amount, 2) }}</td>
                                                <td class="text-end">
                                                    @canPage('vehicles', 'delete')
                                                    <form action="{{ route('vehicles.miscellaneous.destroy', $entry) }}" method="POST" class="d-inline"
                                                          onsubmit="return confirm('Delete this miscellaneous entry?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                    @endcanPage
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="3" class="text-end">Page total</th>
                                            <th class="text-end">₱{{ number_format((float) $miscItems->sum('amount'), 2) }}</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $miscItems->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-ellipsis-h fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">No miscellaneous entries yet</h4>
                                <p class="text-muted">Add description, amount, and transaction date to start the list.</p>
                                @canPage('vehicles', 'create')
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMiscellaneousModal">
                                    <i class="fas fa-plus me-1"></i>Add New
                                </button>
                                @endcanPage
                            </div>
                        @endif
                    @else
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
                            @if(!empty($excelReleaseWarning))
                                <button type="button"
                                        class="btn btn-sm btn-outline-warning ms-2 align-middle"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#excelReleaseWarningBanner"
                                        aria-expanded="false"
                                        aria-controls="excelReleaseWarningBanner"
                                        title="Show or hide release data issues">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span class="badge bg-dark ms-1">{{ number_format($excelReleaseWarning['issue_count'] ?? 0) }}</span>
                                </button>
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

                    <div class="row g-2 mb-3" id="locationVisibilityCards">
                        @foreach(($locationCounts ?? []) as $loc)
                            @php
                                $locKey = strtolower(trim($loc['name']));
                                $locId = $loc['id'] ?? null;
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
                                $allLocationsEnabled = ($locationVisibility['ids'] ?? null) === null;
                                $locChecked = $allLocationsEnabled
                                    || (is_array($locationVisibility['ids'] ?? null) && $locId && in_array((int) $locId, $locationVisibility['ids'], true));
                            @endphp
                            <div class="col-sm-6 col-lg-3">
                                <div class="border rounded px-3 py-2 h-100 position-relative {{ $locChecked ? '' : 'opacity-50' }}" style="{{ $cardStyle }}">
                                    <div class="form-check position-absolute top-0 end-0 m-2">
                                        <input class="form-check-input location-visibility-toggle"
                                               type="checkbox"
                                               role="switch"
                                               id="locToggle{{ $locId }}"
                                               data-location-id="{{ $locId }}"
                                               @checked($locChecked)
                                               title="Show {{ $loc['name'] }} units in the list">
                                        <label class="form-check-label visually-hidden" for="locToggle{{ $locId }}">Show {{ $loc['name'] }}</label>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between pe-4">
                                        <div>
                                            <div class="small text-muted text-uppercase fw-semibold">Location {{ $loc['name'] }}</div>
                                            <div class="fs-5 fw-bold text-dark">{{ number_format($loc['count']) }}</div>
                                        </div>
                                        <span class="badge {{ $badgeClass }}">{{ $loc['name'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        @if(($unassignedLocationCount ?? 0) > 0)
                            @php
                                $allLocationsEnabled = ($locationVisibility['ids'] ?? null) === null;
                                $noneChecked = $allLocationsEnabled || !empty($locationVisibility['include_none']);
                            @endphp
                            <div class="col-sm-6 col-lg-3">
                                <div class="border rounded px-3 py-2 h-100 position-relative {{ $noneChecked ? '' : 'opacity-50' }}" style="background-color: #f8f9fa; border-color: #adb5bd !important;">
                                    <div class="form-check position-absolute top-0 end-0 m-2">
                                        <input class="form-check-input location-visibility-toggle"
                                               type="checkbox"
                                               role="switch"
                                               id="locToggleNone"
                                               data-location-none="1"
                                               @checked($noneChecked)
                                               title="Show units with no location">
                                        <label class="form-check-label visually-hidden" for="locToggleNone">Show no location</label>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between pe-4">
                                        <div>
                                            <div class="small text-muted text-uppercase fw-semibold">No Location</div>
                                            <div class="fs-5 fw-bold text-dark">{{ number_format($unassignedLocationCount) }}</div>
                                        </div>
                                        <span class="badge bg-light text-dark border">—</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if(!empty($excelReleaseWarning))
                        <div class="collapse" id="excelReleaseWarningBanner">
                            <button type="button"
                                    class="alert alert-warning border-warning mb-3 w-100 text-start"
                                    data-bs-toggle="modal"
                                    data-bs-target="#excelReleaseIssuesModal"
                                    style="cursor: pointer;">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="fas fa-exclamation-triangle mt-1"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold mb-1">
                                            {{ $excelReleaseWarning['title'] ?? 'Release data issues' }}
                                            <span class="badge bg-dark ms-1">{{ number_format($excelReleaseWarning['issue_count'] ?? 0) }}</span>
                                        </div>
                                        <div class="mb-1">{{ $excelReleaseWarning['summary'] ?? '' }}</div>
                                        <div class="small text-decoration-underline">Click to view issue details</div>
                                    </div>
                                    <i class="fas fa-external-link-alt mt-1 opacity-75"></i>
                                </div>
                            </button>
                        </div>

                        <div class="modal fade" id="excelReleaseIssuesModal" tabindex="-1" aria-labelledby="excelReleaseIssuesModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="excelReleaseIssuesModalLabel">
                                            <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                            Release data issues
                                            @if(!empty($excelReleaseWarning['date_label']))
                                                <span class="text-muted small fw-normal">— {{ $excelReleaseWarning['date_label'] }}</span>
                                            @endif
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p class="small text-muted mb-3">
                                            Monthly totals match Excel sales: section rows with a later release date are forced into this month’s table (with a Forced add note). Re-release units stay in this notice only.
                                            ({{ number_format($excelReleaseWarning['excel_unique_plates'] ?? 0) }} unique plate(s) in the table).
                                        </p>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Plate</th>
                                                        <th>Location</th>
                                                        <th>Type</th>
                                                        <th>Vehicle</th>
                                                        <th>Excel release</th>
                                                        <th>DB release</th>
                                                        <th>Issue</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach(($excelReleaseWarning['issues'] ?? []) as $issue)
                                                        <tr>
                                                            <td class="fw-semibold">{{ $issue['plate'] ?? '—' }}</td>
                                                            <td class="small">{{ $issue['branch'] ?? '—' }}</td>
                                                            <td class="small">
                                                                @php
                                                                    $typeLabel = match($issue['type'] ?? '') {
                                                                        'duplicate_rerelease' => 'Re-release',
                                                                        'release_date_mismatch' => 'Date mismatch',
                                                                        'missing_in_database' => 'Missing in DB',
                                                                        'extra_in_database' => 'Extra in DB',
                                                                        'status_not_released' => 'Not Released in DB',
                                                                        'excel_section_outside_filter' => 'Forced add',
                                                                        default => $issue['type'] ?? '—',
                                                                    };
                                                                @endphp
                                                                <span class="badge bg-secondary">{{ $typeLabel }}</span>
                                                            </td>
                                                            <td class="small">
                                                                {{ trim(($issue['year'] ?? '').' '.($issue['make'] ?? '').' '.($issue['model'] ?? '')) ?: '—' }}
                                                            </td>
                                                            <td class="small">
                                                                {{ $issue['excel_release_date'] ?? '—' }}
                                                                @if(!empty($issue['excel_row']))
                                                                    <span class="text-muted">(row {{ $issue['excel_row'] }})</span>
                                                                @endif
                                                            </td>
                                                            <td class="small">
                                                                {{ $issue['db_release_date'] ?? '—' }}
                                                                @if(!empty($issue['newer_excel_row']))
                                                                    <span class="text-muted">(Excel row {{ $issue['newer_excel_row'] }})</span>
                                                                @endif
                                                            </td>
                                                            <td class="small">{{ $issue['message'] ?? '' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @if(!empty($excelReleaseWarning['source_name']))
                                            <div class="small text-muted mt-3">Source: {{ $excelReleaseWarning['source_name'] }}</div>
                                        @endif
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

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
                                        @canViewPurchasePrice
                                        <th>Purchase Price</th>
                                        @endcanViewPurchasePrice
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="vehiclesTableBody">
                                    @foreach($vehicles as $vehicle)
                                    <tr data-vehicle-id="{{ $vehicle->id }}">
                                        <td>{{ ($vehicles->currentPage() - 1) * $vehicles->perPage() + $loop->iteration }}</td>
                                        <td>
                                            @if(!empty($vehicle->excel_period_branch))
                                                @include('partials.showroom-badge', ['name' => $vehicle->excel_period_branch])
                                            @elseif($vehicle->branchLocation)
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
                                        <td>
                                            @php
                                                $plateIssues = $vehicle->getAttribute('excel_release_issues') ?? [];
                                                $isExcelOnly = !empty($vehicle->getAttribute('excel_only'));
                                                $missingPlate = !empty($vehicle->getAttribute('excel_missing_plate'));
                                                $hasForfeitHistory = $isExcelOnly
                                                    ? false
                                                    : ($vehicle->relationLoaded('forfeitDetails')
                                                        ? $vehicle->forfeitDetails->count() > 0
                                                        : $vehicle->forfeitDetails()->exists());
                                                $issueNote = collect($plateIssues)->pluck('message')->filter()->first();
                                            @endphp
                                            <div>
                                                @if($missingPlate || trim((string) ($vehicle->plate_number ?? '')) === '')
                                                    <span class="badge bg-warning text-dark">No plate</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $vehicle->plate_number }}</span>
                                                @endif
                                                @if(!empty($vehicle->excel_forced_section_add))
                                                    <span class="badge bg-warning text-dark ms-1" title="Listed under this Excel month section even though the release date is outside the filter">Forced add</span>
                                                @endif
                                                @if(!empty($vehicle->has_excel_rerelease_issue))
                                                    <span class="badge bg-warning text-dark ms-1" title="Re-released later; included to match Excel period count">Re-release</span>
                                                @endif
                                                @if(($vehicle->status === 'Forfeited' || $hasForfeitHistory) && ($status ?? '') === 'Released')
                                                    <span class="badge badge-red ms-1" title="Also listed under Forfeited">Forfeited</span>
                                                @endif
                                                @if($isExcelOnly)
                                                    <span class="badge bg-warning text-dark ms-1" title="Listed from Excel to match period totals">Excel only</span>
                                                @endif
                                            </div>
                                            @if($issueNote)
                                                <small class="text-warning d-block mt-1" style="max-width: 240px; line-height: 1.3;">
                                                    {{ \Illuminate\Support\Str::limit($issueNote, 160) }}
                                                </small>
                                            @endif
                                            @if(!empty($plateIssues))
                                                <button type="button"
                                                        class="btn btn-link btn-sm p-0 align-baseline mt-1"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#vehicleExcelIssueModal{{ $vehicle->id }}">
                                                    View details
                                                </button>
                                            @endif
                                            @if($hasForfeitHistory)
                                                <button type="button"
                                                        class="btn btn-link btn-sm p-0 align-baseline mt-1 {{ !empty($plateIssues) ? 'ms-2' : '' }}"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#vehicleForfeitHistoryModal{{ $vehicle->id }}">
                                                    View history
                                                </button>
                                            @endif
                                        </td>
                                        <td>{{ $vehicle->colour }}</td>
                                        @canViewPurchasePrice
                                        <td>{{ $vehicle->formatted_purchase_price }}</td>
                                        @endcanViewPurchasePrice
                                        <td>
                                            @if(!empty($vehicle->getAttribute('excel_only')))
                                                <span class="badge bg-warning text-dark">Excel only</span>
                                            @elseif($vehicle->status === 'Archived')
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
                                                @if(empty($vehicle->getAttribute('excel_only')))
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-primary vehicle-quick-view-btn"
                                                        title="View Details"
                                                        aria-label="View Details"
                                                        data-url="/vehicles/{{ $vehicle->id }}/quick-view">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                @canPage('vehicles', 'update')
                                                @if(in_array($status, ['Available', 'Released', 'Forfeited'], true) && $vehicle->isArchiveable())
                                                <form action="{{ route('vehicles.archive', $vehicle) }}" method="POST" class="d-inline archive-vehicle-form">
                                                    @csrf
                                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-1 archive-vehicle-btn" title="Archive this unit" aria-label="Archive this unit"
                                                            data-label="{{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->plate_number }})">
                                                        <i class="fas fa-archive"></i>
                                                    </button>
                                                </form>
                                                @endif
                                                @endcanPage
                                                @else
                                                <span class="text-muted small">Not in database</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @foreach($vehicles as $vehicle)
                            @php $plateIssues = $vehicle->getAttribute('excel_release_issues') ?? []; @endphp
                            @if(!empty($plateIssues))
                            <div class="modal fade" id="vehicleExcelIssueModal{{ $vehicle->id }}" tabindex="-1" aria-labelledby="vehicleExcelIssueModalLabel{{ $vehicle->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="vehicleExcelIssueModalLabel{{ $vehicle->id }}">
                                                Release issues — {{ $vehicle->plate_number ?: 'No plate' }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="small text-muted mb-3">
                                                {{ $vehicle->full_name }} · Current status:
                                                <strong>{{ $vehicle->status }}</strong>
                                                @if(!empty($vehicle->excel_period_release_date))
                                                    · Excel release in this filter: <strong>{{ $vehicle->excel_period_release_date }}</strong>
                                                @endif
                                            </p>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-striped mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Type</th>
                                                            <th>Excel release</th>
                                                            <th>DB release</th>
                                                            <th>Issue</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($plateIssues as $issue)
                                                            <tr>
                                                                <td>
                                                                    @php
                                                                        $typeLabel = match($issue['type'] ?? '') {
                                                                            'duplicate_rerelease' => 'Re-release',
                                                                            'release_date_mismatch' => 'Date mismatch',
                                                                            'missing_in_database' => 'Missing in DB',
                                                                            'extra_in_database' => 'Extra in DB',
                                                                            'status_not_released' => 'Not Released in DB',
                                                                            'excel_section_outside_filter' => 'Forced add',
                                                                            default => $issue['type'] ?? '—',
                                                                        };
                                                                    @endphp
                                                                    <span class="badge bg-secondary">{{ $typeLabel }}</span>
                                                                </td>
                                                                <td class="small">
                                                                    {{ $issue['excel_release_date'] ?? '—' }}
                                                                    @if(!empty($issue['excel_row']))
                                                                        <span class="text-muted">(row {{ $issue['excel_row'] }})</span>
                                                                    @endif
                                                                </td>
                                                                <td class="small">{{ $issue['db_release_date'] ?? '—' }}</td>
                                                                <td class="small">{{ $issue['message'] ?? '' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-outline-primary">Open vehicle</a>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if($vehicle->forfeitDetails->count() > 0)
                            <div class="modal fade" id="vehicleForfeitHistoryModal{{ $vehicle->id }}" tabindex="-1" aria-labelledby="vehicleForfeitHistoryModalLabel{{ $vehicle->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="vehicleForfeitHistoryModalLabel{{ $vehicle->id }}">
                                                Forfeit history — {{ $vehicle->plate_number }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p class="small text-muted mb-3">{{ $vehicle->full_name }}</p>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-striped mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Previous forfeit</th>
                                                            <th class="text-end">Amount</th>
                                                            <th>Forfeit date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($vehicle->forfeitDetails as $fd)
                                                            <tr>
                                                                <td>{{ $fd->previous_forfeit_date ? $fd->previous_forfeit_date->format('M d, Y') : '—' }}</td>
                                                                <td class="text-end">₱{{ number_format((float) $fd->forfeit_amount, 2) }}</td>
                                                                <td>{{ $fd->forfeit_date ? $fd->forfeit_date->format('M d, Y') : '—' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <a href="{{ route('vehicles.show', $vehicle) }}#forfeit-details" class="btn btn-outline-primary">Open vehicle</a>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                        
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
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>

@canPage('vehicles', 'create')
<div class="modal fade" id="addMiscellaneousModal" tabindex="-1" aria-labelledby="addMiscellaneousModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('vehicles.miscellaneous.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addMiscellaneousModalLabel">
                        <i class="fas fa-plus me-2"></i>Add Miscellaneous Entry
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="misc_description" class="form-label">Description <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('description') is-invalid @enderror"
                               id="misc_description" name="description" value="{{ old('description') }}"
                               maxlength="500" required>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="misc_amount" class="form-label">Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" step="0.01" min="0"
                                   class="form-control @error('amount') is-invalid @enderror"
                                   id="misc_amount" name="amount" value="{{ old('amount') }}" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="misc_location_field" class="form-label">Location <span class="text-danger">*</span></label>
                        <select class="form-select @error('location') is-invalid @enderror"
                                id="misc_location_field" name="location" required>
                            <option value="">Select location</option>
                            @foreach(\App\Models\MiscellaneousTransaction::locationOptions() as $locOpt)
                                <option value="{{ $locOpt }}" {{ old('location') === $locOpt ? 'selected' : '' }}>{{ $locOpt }}</option>
                            @endforeach
                        </select>
                        @error('location')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-0">
                        <label for="misc_transaction_date" class="form-label">Transaction Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('transaction_date') is-invalid @enderror"
                               id="misc_transaction_date" name="transaction_date"
                               value="{{ old('transaction_date', now()->toDateString()) }}" required>
                        @error('transaction_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcanPage

@canPage('vehicles', 'create')
<div class="modal fade" id="vehicleExcelImportModal" tabindex="-1" aria-labelledby="vehicleExcelImportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vehicleExcelImportModalLabel">
                    <i class="fas fa-file-import me-2"></i>Import Units from Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="small text-muted mb-3">
                    Upload the <strong>(PRIVATE) AVAILABLE-RESERVED-RELEASED UNITS.xlsx</strong> workbook,
                    choose which tab(s) to import, review the affected tables, then confirm.
                </p>

                <div id="importStepUpload">
                    <label class="form-label">Excel file (.xlsx)</label>
                    <input type="file" class="form-control" id="vehicleImportFile" accept=".xlsx,.xls">
                    <div class="form-text">Max ~50MB. Large Released sheets may take a minute to analyze.</div>
                </div>

                <div id="importStepSheets" class="d-none mt-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="fw-semibold">Select tabs to import</div>
                        <div class="small text-muted" id="importFileName"></div>
                    </div>
                    <div id="importSheetList" class="border rounded p-2" style="max-height: 260px; overflow:auto;"></div>
                </div>

                <div id="importStepSummary" class="d-none mt-3">
                    <div class="fw-semibold mb-2">Import impact summary</div>
                    <div class="row g-2 mb-3" id="importTotals"></div>
                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Excel tab</th>
                                    <th>Status</th>
                                    <th>Showroom</th>
                                    <th>Rows</th>
                                    <th>Create</th>
                                    <th>Update</th>
                                </tr>
                            </thead>
                            <tbody id="importTabsBody"></tbody>
                        </table>
                    </div>
                    <div class="fw-semibold mb-2">Affected database tables</div>
                    <div class="table-responsive mb-2">
                        <table class="table table-sm table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Table</th>
                                    <th>Action</th>
                                    <th>~Rows touched</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody id="importTablesBody"></tbody>
                        </table>
                    </div>
                    <ul class="small text-muted mb-0" id="importNotes"></ul>
                </div>

                <div id="importStepResult" class="d-none mt-3"></div>
                <div id="importError" class="alert alert-danger d-none mt-3 mb-0"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="importAnalyzeBtn" disabled>
                    <i class="fas fa-search me-1"></i>Analyze selected tabs
                </button>
                <button type="button" class="btn btn-success d-none" id="importConfirmBtn">
                    <i class="fas fa-check me-1"></i>Confirm &amp; Import
                </button>
            </div>
        </div>
    </div>
</div>
@endcanPage

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

{{-- Unit Report quick view (Excel-style fields) --}}
<div class="modal fade" id="vehicleQuickViewModal" tabindex="-1" aria-labelledby="vehicleQuickViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vehicleQuickViewModalLabel">
                    <i class="fas fa-car me-2"></i><span id="vehicleQuickViewTitle">Vehicle details</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="vehicleQuickViewLoading" class="text-center py-5 text-muted">
                    <i class="fas fa-spinner fa-spin fa-2x mb-3 d-block"></i>
                    Loading vehicle data…
                </div>
                <div id="vehicleQuickViewError" class="alert alert-danger d-none mb-0"></div>
                <div id="vehicleQuickViewFields" class="vehicle-quick-view-grid d-none"></div>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <a href="#" id="vehicleQuickViewDetailsBtn" class="btn btn-primary disabled" aria-disabled="true">
                    <i class="fas fa-external-link-alt me-1"></i>View Car Details
                </a>
            </div>
        </div>
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

.vehicle-quick-view-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 0;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    overflow: hidden;
}
.vehicle-quick-view-row {
    display: grid;
    grid-template-columns: minmax(140px, 42%) 1fr;
    border-bottom: 1px solid #eee;
    background: #fff;
}
.vehicle-quick-view-row:nth-child(4n+1),
.vehicle-quick-view-row:nth-child(4n+2) {
    background: #f8f9fa;
}
.vehicle-quick-view-label {
    padding: 0.45rem 0.65rem;
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.02em;
    color: #495057;
    border-right: 1px solid #eee;
    display: flex;
    align-items: flex-start;
}
.vehicle-quick-view-value {
    padding: 0.45rem 0.65rem;
    font-size: 0.85rem;
    color: #212529;
    white-space: pre-wrap;
    word-break: break-word;
    min-height: 1.75rem;
}
@media (max-width: 767.98px) {
    .vehicle-quick-view-grid {
        grid-template-columns: 1fr;
    }
    .vehicle-quick-view-row:nth-child(4n+1),
    .vehicle-quick-view-row:nth-child(4n+2) {
        background: #fff;
    }
    .vehicle-quick-view-row:nth-child(odd) {
        background: #f8f9fa;
    }
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
    window.canViewPurchasePrice = @json(auth()->user()?->canViewPurchasePrice() ?? false);
(function () {
    const currentStatus = @json($status);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    (function initVehicleQuickView() {
        const modalEl = document.getElementById('vehicleQuickViewModal');
        if (!modalEl) return;

        const titleEl = document.getElementById('vehicleQuickViewTitle');
        const loadingEl = document.getElementById('vehicleQuickViewLoading');
        const errorEl = document.getElementById('vehicleQuickViewError');
        const fieldsEl = document.getElementById('vehicleQuickViewFields');
        const detailsBtn = document.getElementById('vehicleQuickViewDetailsBtn');

        function getModal() {
            if (typeof bootstrap === 'undefined' || !bootstrap.Modal) return null;
            return bootstrap.Modal.getOrCreateInstance(modalEl);
        }

        function escapeHtml(str) {
            return String(str ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function setLoading() {
            if (loadingEl) loadingEl.classList.remove('d-none');
            if (errorEl) {
                errorEl.classList.add('d-none');
                errorEl.textContent = '';
            }
            if (fieldsEl) {
                fieldsEl.classList.add('d-none');
                fieldsEl.innerHTML = '';
            }
            if (titleEl) titleEl.textContent = 'Vehicle details';
            if (detailsBtn) {
                detailsBtn.href = '#';
                detailsBtn.classList.add('disabled');
                detailsBtn.setAttribute('aria-disabled', 'true');
            }
        }

        function renderFields(fields) {
            fieldsEl.innerHTML = (fields || []).map(function (row) {
                const label = escapeHtml(row.label || '');
                const value = escapeHtml(row.value || '');
                return '<div class="vehicle-quick-view-row">' +
                    '<div class="vehicle-quick-view-label">' + label + '</div>' +
                    '<div class="vehicle-quick-view-value">' + (value || '<span class="text-muted">—</span>') + '</div>' +
                    '</div>';
            }).join('');
            fieldsEl.classList.remove('d-none');
        }

        async function openQuickView(url) {
            setLoading();
            const modal = getModal();
            if (modal) modal.show();
            const controller = new AbortController();
            const timer = setTimeout(function () { controller.abort(); }, 20000);
            try {
                let fetchUrl = url;
                try {
                    const parsed = new URL(url, window.location.origin);
                    fetchUrl = parsed.pathname + parsed.search;
                } catch (e) {}
                const res = await fetch(fetchUrl, {
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    signal: controller.signal
                });
                const raw = await res.text();
                let data = {};
                try {
                    data = raw ? JSON.parse(raw) : {};
                } catch (e) {
                    throw new Error('Could not load vehicle details.');
                }
                if (!res.ok || !data.ok) {
                    throw new Error(data.message || 'Could not load vehicle details.');
                }
                if (titleEl) titleEl.textContent = data.title || 'Vehicle details';
                renderFields(data.fields || []);
                if (data.show_url && detailsBtn) {
                    detailsBtn.href = data.show_url;
                    detailsBtn.classList.remove('disabled');
                    detailsBtn.removeAttribute('aria-disabled');
                }
            } catch (err) {
                if (errorEl) {
                    errorEl.textContent = (err && err.name === 'AbortError')
                        ? 'Loading timed out. Please try again.'
                        : (err.message || 'Could not load vehicle details.');
                    errorEl.classList.remove('d-none');
                }
            } finally {
                clearTimeout(timer);
                if (loadingEl) loadingEl.classList.add('d-none');
            }
        }

        document.addEventListener('click', function (event) {
            const btn = event.target.closest('.vehicle-quick-view-btn');
            if (!btn) return;
            event.preventDefault();
            const url = btn.getAttribute('data-url');
            if (!url) return;
            openQuickView(url);
        });
    })();

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
        const show = selected === 'Released';
        releaseDateFields.forEach(function (el) {
            el.style.display = show ? '' : 'none';
        });
        if (!show) {
            if (releaseFromInput) releaseFromInput.value = '';
            if (releaseToInput) releaseToInput.value = '';
        }
    }

    function toggleReservationDateFilter() {
        const selected = statusSelect ? statusSelect.value : currentStatus;
        const show = selected === 'Reserved';
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

    // Location card checkboxes: reload with only checked locations in the result list.
    (function () {
        const toggles = document.querySelectorAll('.location-visibility-toggle');
        if (!toggles.length) {
            return;
        }

        const allLocationIds = Array.from(toggles)
            .map(function (el) { return el.getAttribute('data-location-id'); })
            .filter(function (id) { return id !== null && id !== ''; });
        const hasNoneToggle = Array.from(toggles).some(function (el) {
            return el.getAttribute('data-location-none') === '1';
        });

        function navigateWithLocationVisibility() {
            const params = new URLSearchParams(window.location.search);
            params.delete('page');
            params.delete('locations');
            params.delete('locations[]');
            params.delete('locations_none');

            // Clear old array-style keys
            Array.from(params.keys()).forEach(function (key) {
                if (key === 'locations' || key.indexOf('locations[') === 0) {
                    params.delete(key);
                }
            });

            const checkedIds = [];
            let includeNone = false;
            let checkedCount = 0;
            toggles.forEach(function (el) {
                if (!el.checked) {
                    return;
                }
                checkedCount += 1;
                if (el.getAttribute('data-location-none') === '1') {
                    includeNone = true;
                    return;
                }
                const id = el.getAttribute('data-location-id');
                if (id) {
                    checkedIds.push(id);
                }
            });

            const allChecked = checkedCount === toggles.length
                && checkedIds.length === allLocationIds.length
                && (!hasNoneToggle || includeNone);

            if (!allChecked) {
                checkedIds.forEach(function (id) {
                    params.append('locations[]', id);
                });
                if (includeNone) {
                    params.set('locations_none', '1');
                } else if (hasNoneToggle) {
                    params.set('locations_none', '0');
                }
            }

            const query = params.toString();
            window.location.href = window.location.pathname + (query ? '?' + query : '');
        }

        toggles.forEach(function (el) {
            el.addEventListener('change', navigateWithLocationVisibility);
        });
    })();

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
            (window.canViewPurchasePrice ? ('<td>' + escapeHtml(vehicle.purchase_price || '') + '</td>') : '') +
            '<td><span class="badge badge-neutral">Archived</span>' + archivedDate + '</td>' +
            '<td><div class="btn-group" role="group">' +
                '<button type="button" class="btn btn-sm btn-outline-primary vehicle-quick-view-btn" title="View Details" aria-label="View Details" ' +
                'data-url="' + escapeHtml(vehicle.quick_view_url || ('/vehicles/' + vehicle.id + '/quick-view')) + '"><i class="fas fa-eye"></i></button></div></td>' +
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

    // ---- Excel import (upload → select tabs → analyze → confirm) ----
    (function initVehicleExcelImport() {
        const modal = document.getElementById('vehicleExcelImportModal');
        if (!modal) return;

        const fileInput = document.getElementById('vehicleImportFile');
        const sheetStep = document.getElementById('importStepSheets');
        const summaryStep = document.getElementById('importStepSummary');
        const resultStep = document.getElementById('importStepResult');
        const sheetList = document.getElementById('importSheetList');
        const fileNameEl = document.getElementById('importFileName');
        const analyzeBtn = document.getElementById('importAnalyzeBtn');
        const confirmBtn = document.getElementById('importConfirmBtn');
        const errorEl = document.getElementById('importError');
        const totalsEl = document.getElementById('importTotals');
        const tabsBody = document.getElementById('importTabsBody');
        const tablesBody = document.getElementById('importTablesBody');
        const notesEl = document.getElementById('importNotes');

        let importToken = null;
        let selectedSheets = [];

        function showError(msg) {
            errorEl.textContent = msg || 'Something went wrong.';
            errorEl.classList.remove('d-none');
        }
        function clearError() {
            errorEl.classList.add('d-none');
            errorEl.textContent = '';
        }
        function resetUi() {
            importToken = null;
            selectedSheets = [];
            fileInput.value = '';
            sheetStep.classList.add('d-none');
            summaryStep.classList.add('d-none');
            resultStep.classList.add('d-none');
            resultStep.innerHTML = '';
            sheetList.innerHTML = '';
            analyzeBtn.disabled = true;
            confirmBtn.classList.add('d-none');
            confirmBtn.disabled = false;
            clearError();
        }

        modal.addEventListener('hidden.bs.modal', resetUi);

        fileInput.addEventListener('change', async function () {
            clearError();
            summaryStep.classList.add('d-none');
            resultStep.classList.add('d-none');
            confirmBtn.classList.add('d-none');
            const file = fileInput.files && fileInput.files[0];
            if (!file) return;

            const form = new FormData();
            form.append('file', file);
            analyzeBtn.disabled = true;
            analyzeBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Reading Excel…';

            try {
                const res = await fetch(@json(route('vehicles.import.upload')), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: form
                });
                const data = await res.json();
                if (!res.ok || !data.ok) {
                    throw new Error(data.message || 'Upload failed');
                }
                importToken = data.token;
                fileNameEl.textContent = data.original_name || file.name;
                sheetList.innerHTML = '';
                (data.sheets || []).forEach(function (sheet) {
                    const id = 'sheet_' + btoa(unescape(encodeURIComponent(sheet.name))).replace(/=+/g, '');
                    const disabled = !sheet.supported;
                    const wrap = document.createElement('div');
                    wrap.className = 'form-check py-1 border-bottom';
                    wrap.innerHTML =
                        '<input class="form-check-input import-sheet-check" type="checkbox" value="' + escapeHtml(sheet.name) + '" id="' + id + '" ' + (disabled ? 'disabled' : '') + '>' +
                        '<label class="form-check-label w-100" for="' + id + '">' +
                        '<div class="d-flex justify-content-between gap-2">' +
                        '<span><strong>' + escapeHtml(sheet.name) + '</strong>' +
                        (sheet.supported
                            ? ' <span class="badge bg-primary ms-1">' + escapeHtml(sheet.status) + '</span> <span class="badge bg-info text-dark">' + escapeHtml(sheet.branch) + '</span>'
                            : ' <span class="badge bg-secondary ms-1">Unsupported</span>') +
                        '</span>' +
                        '<span class="text-muted small">' + Number(sheet.excel_rows || 0).toLocaleString() + ' rows</span>' +
                        '</div>' +
                        (sheet.note ? '<div class="small text-muted">' + escapeHtml(sheet.note) + '</div>' : '') +
                        (sheet.supported ? '<div class="small text-muted">Tables: ' + escapeHtml((sheet.tables || []).join(', ')) + '</div>' : '') +
                        '</label>';
                    sheetList.appendChild(wrap);
                });
                sheetStep.classList.remove('d-none');
                analyzeBtn.disabled = false;
            } catch (err) {
                showError(err.message || String(err));
            } finally {
                analyzeBtn.innerHTML = '<i class="fas fa-search me-1"></i>Analyze selected tabs';
            }
        });

        analyzeBtn.addEventListener('click', async function () {
            clearError();
            selectedSheets = Array.from(sheetList.querySelectorAll('.import-sheet-check:checked')).map(function (el) {
                return el.value;
            });
            if (!importToken || selectedSheets.length === 0) {
                showError('Select at least one supported Excel tab.');
                return;
            }
            analyzeBtn.disabled = true;
            analyzeBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Analyzing…';
            try {
                const res = await fetch(@json(route('vehicles.import.analyze')), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ token: importToken, sheets: selectedSheets })
                });
                const data = await res.json();
                if (!res.ok || !data.ok) {
                    throw new Error(data.message || 'Analyze failed');
                }
                const summary = data.summary || {};
                const totals = summary.totals || {};
                totalsEl.innerHTML =
                    '<div class="col-md-3"><div class="border rounded p-2"><div class="small text-muted">Excel rows</div><div class="fs-5 fw-bold">' + Number(totals.excel_rows || 0).toLocaleString() + '</div></div></div>' +
                    '<div class="col-md-3"><div class="border rounded p-2"><div class="small text-muted">Unique plates</div><div class="fs-5 fw-bold">' + Number(totals.unique_plates || 0).toLocaleString() + '</div></div></div>' +
                    '<div class="col-md-3"><div class="border rounded p-2"><div class="small text-muted">Will create</div><div class="fs-5 fw-bold text-success">' + Number(totals.will_create_vehicles || 0).toLocaleString() + '</div></div></div>' +
                    '<div class="col-md-3"><div class="border rounded p-2"><div class="small text-muted">Will update</div><div class="fs-5 fw-bold text-primary">' + Number(totals.will_update_vehicles || 0).toLocaleString() + '</div></div></div>';

                tabsBody.innerHTML = (summary.tabs || []).map(function (t) {
                    return '<tr>' +
                        '<td>' + escapeHtml(t.name) + '</td>' +
                        '<td>' + escapeHtml(t.status) + '</td>' +
                        '<td>' + escapeHtml(t.branch) + '</td>' +
                        '<td>' + Number(t.excel_rows || 0).toLocaleString() + '</td>' +
                        '<td>' + Number(t.will_create_vehicles || 0).toLocaleString() + '</td>' +
                        '<td>' + Number(t.will_update_vehicles || 0).toLocaleString() + '</td>' +
                        '</tr>';
                }).join('');

                tablesBody.innerHTML = (summary.tables || []).map(function (t) {
                    return '<tr>' +
                        '<td><code>' + escapeHtml(t.table) + '</code></td>' +
                        '<td>' + escapeHtml(t.action) + '</td>' +
                        '<td>' + Number(t.approx_rows_touched || 0).toLocaleString() + '</td>' +
                        '<td class="small">' + escapeHtml(t.description || '') + '</td>' +
                        '</tr>';
                }).join('');

                notesEl.innerHTML = (summary.notes || []).map(function (n) {
                    return '<li>' + escapeHtml(n) + '</li>';
                }).join('');

                summaryStep.classList.remove('d-none');
                confirmBtn.classList.remove('d-none');
            } catch (err) {
                showError(err.message || String(err));
            } finally {
                analyzeBtn.disabled = false;
                analyzeBtn.innerHTML = '<i class="fas fa-search me-1"></i>Analyze selected tabs';
            }
        });

        confirmBtn.addEventListener('click', async function () {
            clearError();
            if (!importToken || selectedSheets.length === 0) {
                showError('Nothing to import.');
                return;
            }
            const ok = window.confirm('Proceed with importing the selected Excel tabs? Existing plates will be updated.');
            if (!ok) return;

            confirmBtn.disabled = true;
            analyzeBtn.disabled = true;
            confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Importing…';
            try {
                const res = await fetch(@json(route('vehicles.import.confirm')), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        token: importToken,
                        sheets: selectedSheets,
                        confirm: 1
                    })
                });
                const data = await res.json();
                if (!res.ok || !data.ok) {
                    throw new Error(data.message || 'Import failed');
                }
                resultStep.classList.remove('d-none');
                resultStep.innerHTML = '<div class="alert alert-success mb-2">' + escapeHtml(data.message || 'Import completed.') + '</div>' +
                    '<ul class="small mb-0">' + (data.results || []).map(function (r) {
                        return '<li><strong>' + escapeHtml(r.sheet) + '</strong>: ' + Number(r.rows || 0).toLocaleString() + ' rows (' + escapeHtml(r.command) + ')</li>';
                    }).join('') + '</ul>';
                confirmBtn.classList.add('d-none');
                setTimeout(function () { window.location.reload(); }, 1500);
            } catch (err) {
                showError(err.message || String(err));
                confirmBtn.disabled = false;
            } finally {
                analyzeBtn.disabled = false;
                confirmBtn.innerHTML = '<i class="fas fa-check me-1"></i>Confirm &amp; Import';
            }
        });
    })();
})();
</script>
@if($errors->any() && (old('description') !== null || old('amount') !== null || old('transaction_date') !== null))
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalEl = document.getElementById('addMiscellaneousModal');
    if (modalEl && window.bootstrap) {
        new bootstrap.Modal(modalEl).show();
    }
});
</script>
@endif
@endsection
