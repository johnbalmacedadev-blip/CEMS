@extends('layouts.app')

@section('title', 'Recommendation Tracker - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-clipboard-list me-2"></i>Recommendation Tracker
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
            <a href="{{ route('recommendation-tracker.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add Recommendation
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <p class="text-muted mb-4">CAR EMPIRE RECOMMENDATIONS – vehicle condition and accessories checklist.</p>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('recommendation-tracker.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small">Customer</label>
                    <input type="text" class="form-control form-control-sm" name="customer" value="{{ request('customer') }}" placeholder="Search customer">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Make</label>
                    <input type="text" class="form-control form-control-sm" name="make" value="{{ request('make') }}" placeholder="Make">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Date From</label>
                    <input type="date" class="form-control form-control-sm" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Date To</label>
                    <input type="date" class="form-control form-control-sm" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm me-2"><i class="fas fa-search me-1"></i>Filter</button>
                    <a href="{{ route('recommendation-tracker.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body p-0">
            @if($records->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Year</th>
                                <th>Customer</th>
                                <th>Make / Model</th>
                                <th>Paint</th>
                                <th>Vehicle</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $record)
                                <tr>
                                    <td>{{ $record->date->format('M d, Y') }}</td>
                                    <td>{{ $record->year ?? '—' }}</td>
                                    <td>{{ $record->customer ?? '—' }}</td>
                                    <td>{{ $record->make ?? '—' }} / {{ $record->model ?? '—' }}</td>
                                    <td>{{ $record->paint ?? '—' }}</td>
                                    <td>
                                        @if($record->vehicle)
                                            <a href="{{ route('vehicles.show', $record->vehicle) }}">{{ $record->vehicle->full_name }}</a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('recommendation-tracker.show', $record) }}" class="btn btn-sm btn-outline-secondary" title="View"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('recommendation-tracker.edit', $record) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('recommendation-tracker.destroy', $record) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this recommendation record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3 pb-3">
                    {{ $records->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No recommendation records yet</h5>
                    <p class="text-muted mb-3">Add a record to start tracking vehicle recommendations.</p>
                    <a href="{{ route('recommendation-tracker.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Recommendation</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
