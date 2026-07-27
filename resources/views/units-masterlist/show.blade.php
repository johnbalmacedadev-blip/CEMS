@extends('layouts.app')

@section('title', 'Unit Details - Units Masterlist - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-eye me-2"></i>View Unit
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0 gap-2">
            <a href="{{ route('units-masterlist.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Masterlist
            </a>
            <a href="{{ route('units-masterlist.edit', $unit) }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i>Edit
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <i class="fas fa-car me-2"></i>{{ $unit->make_model }}
                    @if($unit->list_number)
                        <span class="badge bg-secondary ms-2">#{{ $unit->list_number }}</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="text-muted small">Plate Number</div>
                            <div class="fw-semibold">
                                @if($unit->vehicle_id && $unit->vehicle)
                                    <a href="{{ route('vehicles.show', $unit->vehicle) }}">
                                        {{ $unit->plate_number ?: $unit->vehicle->plate_number }}
                                    </a>
                                @else
                                    {{ $unit->plate_number ?: '—' }}
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Year</div>
                            <div>{{ $unit->year ?: '—' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Mileage</div>
                            <div>{{ $unit->mileage !== null ? number_format($unit->mileage).' km' : '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted small">Variant</div>
                            <div>{{ $unit->variant ?: '—' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Transmission</div>
                            <div>{{ $unit->transmission ?: '—' }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted small">Fuel Type</div>
                            <div>{{ $unit->fuel_type ?: '—' }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted small">Price</div>
                            <div class="fw-bold fs-5">{{ $unit->price !== null ? '₱'.number_format($unit->price, 2) : '—' }}</div>
                        </div>
                        <div class="col-md-8">
                            <div class="text-muted small">Linked Vehicle Profile</div>
                            <div>
                                @if($unit->vehicle)
                                    <a href="{{ route('vehicles.show', $unit->vehicle) }}">
                                        {{ $unit->vehicle->full_name }}
                                        @if($unit->vehicle->plate_number) ({{ $unit->vehicle->plate_number }}) @endif
                                    </a>
                                @else
                                    <span class="text-muted">Not linked</span>
                                @endif
                            </div>
                        </div>
                        @if($unit->notes)
                            <div class="col-12">
                                <div class="text-muted small">Notes</div>
                                <div>{{ $unit->notes }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header"><i class="fas fa-hand-holding-usd me-2"></i>Low Down Payment Option</div>
                        <div class="card-body">
                            <pre class="mb-0 small" style="white-space: pre-wrap; font-family: inherit;">{{ $unit->low_down_payment_option ?: '—' }}</pre>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header"><i class="fas fa-calendar-alt me-2"></i>Low Monthly Option</div>
                        <div class="card-body">
                            <pre class="mb-0 small" style="white-space: pre-wrap; font-family: inherit;">{{ $unit->low_monthly_option ?: '—' }}</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
