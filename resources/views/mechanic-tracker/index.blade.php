@extends('layouts.app')

@section('title', 'Mechanic Tracker - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-wrench me-2"></i>Mechanic Tracker
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
            <a href="{{ route('mechanic-tracker.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add Job
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <p class="text-muted mb-4">Track internal mechanic jobs and external shop work by date, unit, and status.</p>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('mechanic-tracker.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small">Job Type</label>
                    <select class="form-select form-select-sm" name="job_type">
                        <option value="">All</option>
                        @foreach(\App\Models\MechanicJob::jobTypeOptions() as $opt)
                            <option value="{{ $opt }}" {{ request('job_type') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Status</label>
                    <select class="form-select form-select-sm" name="status">
                        <option value="">All</option>
                        @foreach(\App\Models\MechanicJob::statusOptions() as $opt)
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
                <div class="col-md-2">
                    <label class="form-label small">Search</label>
                    <input type="text" class="form-control form-control-sm" name="search" value="{{ request('search') }}" placeholder="Mechanic, plate, description...">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm me-2"><i class="fas fa-search me-1"></i>Filter</button>
                    <a href="{{ route('mechanic-tracker.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($records->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Mechanic / Category</th>
                                <th>Unit</th>
                                <th>Description</th>
                                <th>Labor / Work</th>
                                <th class="text-end">Cost</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $record)
                                <tr>
                                    <td>{{ $record->job_date->format('M d, Y') }}</td>
                                    <td>
                                        @if($record->job_type === 'External')
                                            <span class="badge bg-info">External</span>
                                        @else
                                            <span class="badge bg-secondary">Internal</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->job_type === 'External')
                                            {{ $record->category ?: '—' }}
                                        @else
                                            {{ $record->mechanic ?: '—' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($record->vehicle)
                                            <a href="{{ route('vehicles.show', $record->vehicle) }}">{{ $record->year_model ?: $record->vehicle->full_name }}</a>
                                            @if($record->plate_number)
                                                <br><small class="text-muted">{{ $record->plate_number }}</small>
                                            @endif
                                        @elseif($record->plate_number || $record->year_model)
                                            {{ $record->year_model ?: '—' }}
                                            @if($record->plate_number)
                                                <br><small class="text-muted">{{ $record->plate_number }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">{{ $record->unit_label ?: '—' }}</span>
                                        @endif
                                    </td>
                                    <td><span class="small">{{ Str::limit($record->description, 60) ?: '—' }}</span></td>
                                    <td><span class="small text-muted">{{ Str::limit($record->labor, 50) ?: '—' }}</span></td>
                                    <td class="text-end">{{ $record->parts_cost !== null ? '₱'.number_format($record->parts_cost, 2) : '—' }}</td>
                                    <td>
                                        @php $st = $record->status; @endphp
                                        @if($st === 'Complete')
                                            <span class="badge bg-success">{{ $st }}</span>
                                        @elseif($st === 'Ongoing')
                                            <span class="badge bg-info">{{ $st }}</span>
                                        @elseif($st === 'Transferred')
                                            <span class="badge bg-warning text-dark">{{ $st }}</span>
                                        @elseif($st)
                                            <span class="badge bg-secondary">{{ $st }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('mechanic-tracker.edit', $record) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('mechanic-tracker.destroy', $record) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this mechanic job?');">
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
                    <i class="fas fa-wrench fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No mechanic jobs yet</h5>
                    <p class="text-muted mb-3">Add a job to start tracking mechanic work.</p>
                    <a href="{{ route('mechanic-tracker.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Job</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
