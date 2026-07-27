@extends('layouts.app')

@section('title', 'Payroll - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-money-check-alt me-2"></i>Payroll
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
            <a href="{{ route('employees.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-users me-1"></i>Employee Management
            </a>
        </div>
    </div>

    <p class="text-muted mb-4">View employees for payroll reference. Manage staff profiles in Employee Management.</p>

    <!-- Summary cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card stat-card border-primary">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">{{ $activeCount }}</h5>
                        <p class="mb-0 text-muted small">Active Employees</p>
                    </div>
                    <i class="fas fa-user-check fa-2x text-primary"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card border-success">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">{{ $regularCount }}</h5>
                        <p class="mb-0 text-muted small">Regular</p>
                    </div>
                    <i class="fas fa-id-card fa-2x text-success"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card border-warning">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">{{ $probationaryCount }}</h5>
                        <p class="mb-0 text-muted small">Probationary</p>
                    </div>
                    <i class="fas fa-clock fa-2x text-warning"></i>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card border-secondary">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">{{ $inactiveCount }}</h5>
                        <p class="mb-0 text-muted small">Inactive</p>
                    </div>
                    <i class="fas fa-user-times fa-2x text-secondary"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('payroll.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small">Status</label>
                    <select class="form-select form-select-sm" name="status">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Contract Type</label>
                    <select class="form-select form-select-sm" name="contract_type">
                        <option value="all" {{ $contractType === 'all' ? 'selected' : '' }}>All</option>
                        <option value="REGULAR" {{ $contractType === 'REGULAR' ? 'selected' : '' }}>Regular</option>
                        <option value="PROBATIONARY" {{ $contractType === 'PROBATIONARY' ? 'selected' : '' }}>Probationary</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Search</label>
                    <input type="text" class="form-control form-control-sm" name="search" placeholder="Name, role, location..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm me-2"><i class="fas fa-search me-1"></i>Filter</button>
                    <a href="{{ route('payroll.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Employee list -->
    <div class="card">
        <div class="card-body p-0">
            @if($employees->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Employee</th>
                                <th>Role</th>
                                <th>Location</th>
                                <th>Contract Type</th>
                                <th>Contract Start</th>
                                <th>Status</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($employees as $employee)
                                <tr>
                                    <td>
                                        <strong>{{ $employee->full_name }}</strong>
                                    </td>
                                    <td>{{ $employee->role ?: '—' }}</td>
                                    <td>
                                        @include('partials.showroom-badge', ['name' => $employee->location])
                                    </td>
                                    <td>
                                        @if($employee->contract_type === 'REGULAR')
                                            <span class="badge bg-primary">{{ $employee->contract_type }}</span>
                                        @elseif($employee->contract_type === 'PROBATIONARY')
                                            <span class="badge bg-warning text-dark">{{ $employee->contract_type }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $employee->contract_start ? $employee->contract_start->format('M d, Y') : '—' }}</td>
                                    <td>
                                        @if($employee->status === 'active')
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-outline-primary" title="View profile">
                                            <i class="fas fa-user me-1"></i>View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3 pb-3">
                    {{ $employees->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-money-check-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No employees found</h5>
                    <p class="text-muted mb-3">
                        @if(request('search') || request('status') !== 'active' || request('contract_type') !== 'all')
                            Try adjusting your filters.
                        @else
                            Add employees in Employee Management to see them here.
                        @endif
                    </p>
                    <a href="{{ route('employees.index') }}" class="btn btn-primary"><i class="fas fa-users me-1"></i>Employee Management</a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.stat-card { border-left-width: 4px; }
</style>
@endsection
