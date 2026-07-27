@extends('layouts.app')

@section('title', 'Employees - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <main class="col-12 px-md-4 main-content" id="mainContent">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-users me-2"></i>Employee Management
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('employees.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Add New Employee
                    </a>
                </div>
            </div>

            <!-- Status Tabs -->
            <div class="card mb-4">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="statusTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $status === 'all' ? 'active' : '' }}" 
                               href="{{ route('employees.index', ['status' => 'all']) }}" 
                               role="tab">
                                <i class="fas fa-list me-1"></i>All Employees
                                <span class="badge bg-secondary ms-1">{{ $activeCount + $inactiveCount }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $status === 'active' ? 'active' : '' }}" 
                               href="{{ route('employees.index', ['status' => 'active']) }}" 
                               role="tab">
                                <i class="fas fa-check-circle me-1"></i>Active
                                <span class="badge bg-success ms-1">{{ $activeCount }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $status === 'inactive' ? 'active' : '' }}" 
                               href="{{ route('employees.index', ['status' => 'inactive']) }}" 
                               role="tab">
                                <i class="fas fa-times-circle me-1"></i>Inactive
                                <span class="badge bg-danger ms-1">{{ $inactiveCount }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Search Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('employees.index') }}" class="row g-3">
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Search by name, role, location, SSS, PhilHealth, Pag-IBIG..." 
                                       value="{{ $search ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="status">
                                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>
                                    All Status
                                </option>
                                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>
                                    Active
                                </option>
                                <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>
                                    Inactive
                                </option>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i>Search
                            </button>
                        </div>
                        <div class="col-md-1">
                            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-times me-1"></i>Clear
                            </a>
                        </div>
                    </form>
                    @if($search || $status !== 'all')
                        <div class="mt-3">
                            @if($search)
                                <span class="badge bg-info me-2">
                                    <i class="fas fa-search me-1"></i>
                                    Search: "{{ $search }}"
                                    <a href="{{ route('employees.index', ['status' => $status]) }}" class="text-white ms-2">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                            @if($status !== 'all')
                                <span class="badge bg-primary me-2">
                                    <i class="fas fa-filter me-1"></i>
                                    Status: {{ ucfirst($status) }}
                                    <a href="{{ route('employees.index', ['search' => $search]) }}" class="text-white ms-2">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Employees Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        @if($search && $status !== 'all')
                            Search Results - {{ ucfirst($status) }} Employees
                        @elseif($search)
                            Search Results
                        @elseif($status === 'all')
                            All Employees
                        @else
                            {{ ucfirst($status) }} Employees
                        @endif
                        <span class="badge bg-secondary ms-2">{{ $employees->total() }} total</span>
                        @if($search)
                            <span class="badge bg-info ms-1">for "{{ $search }}"</span>
                        @endif
                        @if($status !== 'all')
                            <span class="badge bg-primary ms-1">{{ ucfirst($status) }}</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    @if($employees->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Contract Start</th>
                                        <th>Contract Type</th>
                                        <th>Role</th>
                                        <th>Location</th>
                                        <th>SSS</th>
                                        <th>PhilHealth</th>
                                        <th>Pag-IBIG</th>
                                        <th>Birthdate</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employees as $index => $employee)
                                        <tr>
                                            <td>{{ $employees->firstItem() + $index }}</td>
                                            <td>
                                                <div>
                                                    <strong>{{ $employee->full_name }}</strong>
                                                    @if($employee->age)
                                                        <br><small class="text-muted">Age: {{ $employee->age }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($employee->contract_start)
                                                    {{ $employee->formatted_contract_start }}
                                                    <br><small class="text-muted">{{ $employee->contract_duration }}</small>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                @if($employee->contract_type)
                                                    <span class="badge bg-{{ $employee->contract_type === 'REGULAR' ? 'primary' : 'warning' }}">
                                                        {{ $employee->contract_type }}
                                                    </span>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>{{ $employee->role ?: 'N/A' }}</td>
                                            <td>
                                                @include('partials.showroom-badge', ['name' => $employee->location, 'empty' => 'N/A'])
                                            </td>
                                            <td>{{ $employee->sss ?: 'N/A' }}</td>
                                            <td>{{ $employee->philhealth ?: 'N/A' }}</td>
                                            <td>{{ $employee->pagibig ?: 'N/A' }}</td>
                                            <td>{{ $employee->formatted_birthdate }}</td>
                                            <td>
                                                @if($employee->status === 'active')
                                                    <span class="badge bg-success">{{ ucfirst($employee->status) }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ ucfirst($employee->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('employees.show', $employee) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="deleteEmployee({{ $employee->id }}, '{{ $employee->full_name }}')" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center">
                            {{ $employees->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No employees found</h5>
                            <p class="text-muted">
                                @if($search || $status !== 'all')
                                    Try adjusting your search criteria or 
                                    <a href="{{ route('employees.index') }}">view all employees</a>.
                                @else
                                    <a href="{{ route('employees.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i>Add Your First Employee
                                    </a>
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <strong id="employeeName"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Employee</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>

// Delete employee function
function deleteEmployee(employeeId, employeeName) {
    document.getElementById('employeeName').textContent = employeeName;
    document.getElementById('deleteForm').action = `/employees/${employeeId}`;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Clear cache function
function clearAllCache() {
    fetch('/clear-cache', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Cache Cleared!',
                text: 'All cache has been cleared successfully.',
                confirmButtonColor: '#28a745',
                timer: 2000,
                timerProgressBar: true
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'Failed to clear cache.',
                confirmButtonColor: '#dc3545'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'An error occurred while clearing cache.',
            confirmButtonColor: '#dc3545'
        });
    });
}

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

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 14px;
    font-weight: bold;
}

</style>
@endsection



