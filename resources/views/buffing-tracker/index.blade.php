@extends('layouts.app')

@section('title', 'Buffing Tracker - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-spray-can me-2"></i>Buffing Tracker
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
            <a href="{{ route('buffing-tracker.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add Buffing Record
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <p class="text-muted mb-4">Track buffing assignments and completion status by vehicle and staff.</p>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('buffing-tracker.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small">Status</label>
                    <select class="form-select form-select-sm" name="status">
                        <option value="">All</option>
                        @foreach(\App\Models\BuffingRecord::statusOptions() as $opt)
                            <option value="{{ $opt }}" {{ request('status') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
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
                    <a href="{{ route('buffing-tracker.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
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
                                <th>Vehicle</th>
                                <th>Staff</th>
                                <th>Status</th>
                                <th>Notes</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $record)
                                <tr>
                                    <td>{{ $record->buffing_date->format('M d, Y') }}</td>
                                    <td>
                                        @if($record->vehicle)
                                            <a href="{{ route('vehicles.show', $record->vehicle) }}">{{ $record->vehicle->full_name }}</a>
                                            @if($record->vehicle->plate_number)
                                                <br><small class="text-muted">{{ $record->vehicle->plate_number }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $record->employee ? $record->employee->full_name : '—' }}</td>
                                    <td>
                                        @if($record->status === 'Completed')
                                            <span class="badge bg-success">{{ $record->status }}</span>
                                        @elseif($record->status === 'In Progress')
                                            <span class="badge bg-info">{{ $record->status }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ $record->status }}</span>
                                        @endif
                                    </td>
                                    <td><span class="text-muted small">{{ Str::limit($record->notes, 40) ?: '—' }}</span></td>
                                    <td class="text-center">
                                        <a href="{{ route('buffing-tracker.edit', $record) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('buffing-tracker.destroy', $record) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this buffing record?');">
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
                    <i class="fas fa-spray-can fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No buffing records yet</h5>
                    <p class="text-muted mb-3">Add a record to start tracking buffing tasks.</p>
                    <a href="{{ route('buffing-tracker.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Buffing Record</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
