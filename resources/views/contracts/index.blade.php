@extends('layouts.app')

@section('title', 'Contracts - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-file-contract me-2"></i>Contracts
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
            <a href="{{ route('contracts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add Contract
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <p class="text-muted mb-4">Manage company contracts. Track type, party, dates, and attach documents.</p>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('contracts.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small">Linked to</label>
                    <select class="form-select form-select-sm" name="linked_to">
                        <option value="">All</option>
                        <option value="vehicle" {{ request('linked_to') === 'vehicle' ? 'selected' : '' }}>Vehicle</option>
                        <option value="employee" {{ request('linked_to') === 'employee' ? 'selected' : '' }}>Employee</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Type</label>
                    <select class="form-select form-select-sm" name="contract_type">
                        <option value="">All</option>
                        @foreach(\App\Models\Contract::typeOptions() as $opt)
                            <option value="{{ $opt }}" {{ request('contract_type') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Status</label>
                    <select class="form-select form-select-sm" name="status">
                        <option value="">All</option>
                        @foreach(\App\Models\Contract::statusOptions() as $opt)
                            <option value="{{ $opt }}" {{ request('status') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Start From</label>
                    <input type="date" class="form-control form-control-sm" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">End To</label>
                    <input type="date" class="form-control form-control-sm" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Search</label>
                    <input type="text" class="form-control form-control-sm" name="search" placeholder="Title, party, car, employee..." value="{{ request('search') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-sm me-2"><i class="fas fa-search me-1"></i>Filter</button>
                    <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($contracts->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Linked to</th>
                                <th>Type</th>
                                <th>Party</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>File</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contracts as $contract)
                                <tr>
                                    <td><strong>{{ $contract->title }}</strong></td>
                                    <td>
                                        @if($contract->vehicle_id && $contract->vehicle)
                                            <span class="badge bg-info">Vehicle</span>
                                            <a href="{{ route('vehicles.show', $contract->vehicle) }}">{{ $contract->vehicle->full_name }}@if($contract->vehicle->plate_number) ({{ $contract->vehicle->plate_number }})@endif</a>
                                        @elseif($contract->employee_id && $contract->employee)
                                            <span class="badge bg-primary">Employee</span>
                                            <a href="{{ route('employees.show', $contract->employee) }}">{{ $contract->employee->full_name }}</a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-secondary">{{ $contract->contract_type }}</span></td>
                                    <td>{{ $contract->party_name ?: '—' }}</td>
                                    <td>{{ $contract->start_date ? $contract->start_date->format('M d, Y') : '—' }}</td>
                                    <td>{{ $contract->end_date ? $contract->end_date->format('M d, Y') : '—' }}</td>
                                    <td>
                                        @if($contract->status === 'Active')
                                            <span class="badge bg-success">{{ $contract->status }}</span>
                                        @elseif($contract->status === 'Expired')
                                            <span class="badge bg-warning text-dark">{{ $contract->status }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ $contract->status }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($contract->has_file)
                                            <a href="{{ $contract->file_url }}" target="_blank" class="btn btn-sm btn-outline-primary" title="View file"><i class="fas fa-external-link-alt"></i></a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('contracts.edit', $contract) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('contracts.destroy', $contract) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this contract?');">
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
                    {{ $contracts->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-contract fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No contracts yet</h5>
                    <p class="text-muted mb-3">Add employment, vendor, lease or other contracts.</p>
                    <a href="{{ route('contracts.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Contract</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
