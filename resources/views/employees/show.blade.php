@extends('layouts.app')

@section('title', 'Employee Details - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main content -->
        <main class="col-12 ms-sm-auto px-md-4" id="mainContent">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="pt-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employees</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $employee->full_name }}</li>
                </ol>
            </nav>

            <!-- Header Section -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <div class="d-flex align-items-center">
                    <div class="position-relative me-3">
                        @if($employee->primary_photo_url)
                            <img src="{{ $employee->primary_photo_url }}" alt="{{ $employee->full_name }}" 
                                 class="avatar-lg rounded-circle" style="object-fit: cover; border: 3px solid #007bff;">
                        @else
                            <div class="avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                            </div>
                        @endif
                        <button type="button" class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle" 
                                style="width: 32px; height: 32px; padding: 0;" 
                                data-bs-toggle="modal" data-bs-target="#uploadPhotoModal"
                                title="Upload/Change Photo">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                    <div>
                        <h1 class="h2 mb-0">{{ $employee->full_name }}</h1>
                        <p class="text-muted mb-0">
                            @if($employee->role)
                                {{ $employee->role }}
                            @else
                                Employee
                            @endif
                            @if($employee->location)
                                • {{ $employee->location }}
                            @endif
                        </p>
                    </div>
                </div>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2" role="group">
                        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i>Edit Employee
                        </a>
                        <button type="button" class="btn btn-danger" onclick="deleteEmployee({{ $employee->id }}, '{{ $employee->full_name }}')">
                            <i class="fas fa-trash me-1"></i>Delete
                        </button>
                    </div>
                    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Employees
                    </a>
                </div>
            </div>

            <!-- Employee Details Cards -->
            <div class="row">
                <!-- Personal Information -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-user me-2"></i>Personal Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>First Name:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $employee->first_name }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Middle Name:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $employee->middle_name ?: 'N/A' }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Last Name:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $employee->last_name }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Birthdate:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $employee->formatted_birthdate }}
                                    @if($employee->age)
                                        <br><small class="text-muted">Age: {{ $employee->age }}</small>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Status:</strong>
                                </div>
                                <div class="col-sm-8">
                                    @if($employee->status === 'active')
                                        <span class="badge bg-success fs-6">{{ ucfirst($employee->status) }}</span>
                                    @else
                                        <span class="badge bg-danger fs-6">{{ ucfirst($employee->status) }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Employment Information -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-briefcase me-2"></i>Employment Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Role:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $employee->role ?: 'N/A' }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Location:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $employee->location ?: 'N/A' }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Contract Type:</strong>
                                </div>
                                <div class="col-sm-8">
                                    @if($employee->contract_type)
                                        <span class="badge bg-{{ $employee->contract_type === 'REGULAR' ? 'primary' : 'warning' }} fs-6">
                                            {{ $employee->contract_type }}
                                        </span>
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Contract Start:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $employee->formatted_contract_start }}
                                    @if($employee->contract_duration)
                                        <br><small class="text-muted">Duration: {{ $employee->contract_duration }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Government IDs -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-id-card me-2"></i>Government IDs
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>SSS:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $employee->sss ?: 'N/A' }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>PhilHealth:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $employee->philhealth ?: 'N/A' }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Pag-IBIG:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $employee->pagibig ?: 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-sticky-note me-2"></i>Additional Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Notes:</strong>
                                </div>
                                <div class="col-sm-8">
                                    @if($employee->notes)
                                        <div class="bg-light p-3 rounded">
                                            {{ $employee->notes }}
                                        </div>
                                    @else
                                        <span class="text-muted">No notes available</span>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Created:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $employee->created_at->format('F j, Y \a\t g:i A') }}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-4">
                                    <strong>Last Updated:</strong>
                                </div>
                                <div class="col-sm-8">
                                    {{ $employee->updated_at->format('F j, Y \a\t g:i A') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning w-100">
                                <i class="fas fa-edit me-1"></i>Edit Employee
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('employees.index') }}" class="btn btn-secondary w-100">
                                <i class="fas fa-list me-1"></i>View All Employees
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('employees.create') }}" class="btn btn-success w-100">
                                <i class="fas fa-plus me-1"></i>Add New Employee
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button type="button" class="btn btn-danger w-100" onclick="deleteEmployee({{ $employee->id }}, '{{ $employee->full_name }}')">
                                <i class="fas fa-trash me-1"></i>Delete Employee
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Upload Photo Modal -->
<div class="modal fade" id="uploadPhotoModal" tabindex="-1" aria-labelledby="uploadPhotoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadPhotoModalLabel">Upload Primary Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uploadPhotoForm" method="POST" action="{{ route('employees.update', $employee) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="primary_photo" class="form-label">Select Photo</label>
                        <input type="file" class="form-control" id="primary_photo" name="primary_photo" accept="image/*" required>
                        <small class="text-muted">Accepted formats: JPEG, PNG, JPG, GIF, WEBP (Max: 5MB)</small>
                    </div>
                    @if($employee->primary_photo_url)
                        <div class="mb-3">
                            <label class="form-label">Current Photo</label>
                            <div>
                                <img src="{{ $employee->primary_photo_url }}" alt="Current Photo" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-1"></i>Upload Photo
                    </button>
                </div>
            </form>
        </div>
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
// Sidebar toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    
    // Check if sidebar state is stored in localStorage
    const sidebarState = localStorage.getItem('sidebarOpen');
    
    // Set initial state (closed by default)
    if (sidebarState === 'true') {
        sidebar.classList.remove('collapse');
        mainContent.classList.remove('col-12');
        mainContent.classList.add('col-md-9', 'col-lg-10');
    } else {
        sidebar.classList.add('collapse');
        mainContent.classList.add('col-12');
        mainContent.classList.remove('col-md-9', 'col-lg-10');
    }
    
    // Toggle sidebar
    sidebarToggle.addEventListener('click', function() {
        if (sidebar.classList.contains('collapse')) {
            // Open sidebar
            sidebar.classList.remove('collapse');
            mainContent.classList.remove('col-12');
            mainContent.classList.add('col-md-9', 'col-lg-10');
            localStorage.setItem('sidebarOpen', 'true');
        } else {
            // Close sidebar
            sidebar.classList.add('collapse');
            mainContent.classList.add('col-12');
            mainContent.classList.remove('col-md-9', 'col-lg-10');
            localStorage.setItem('sidebarOpen', 'false');
        }
    });
});

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
.avatar-lg {
    width: 80px;
    height: 80px;
    font-size: 32px;
    font-weight: bold;
}

.avatar-lg img {
    width: 80px;
    height: 80px;
}

.sidebar {
    background-color: #f8f9fa;
    border-right: 1px solid #dee2e6;
    min-height: calc(100vh - 56px);
}

.sidebar .nav-link {
    color: #495057;
    padding: 0.75rem 1rem;
    border-radius: 0.375rem;
    margin: 0.125rem 0.5rem;
}

.sidebar .nav-link:hover {
    background-color: #e9ecef;
    color: #212529;
}

.sidebar .nav-link.active {
    background-color: #007bff;
    color: white;
}

.sidebar .nav-link i {
    width: 20px;
    text-align: center;
}

@media (max-width: 767.98px) {
    .sidebar {
        position: fixed;
        top: 56px;
        left: -100%;
        width: 100%;
        height: calc(100vh - 56px);
        z-index: 1000;
        transition: left 0.3s ease;
    }
    
    .sidebar.show {
        left: 0;
    }
    
    .main-content {
        margin-left: 0;
    }
}
</style>
@endsection


