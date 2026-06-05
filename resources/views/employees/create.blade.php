@extends('layouts.app')

@section('title', 'Add New Employee - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main content -->
        <main class="col-12 ms-sm-auto px-md-4" id="mainContent">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-user-plus me-2"></i>Add New Employee
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Employees
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-user me-2"></i>Employee Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('employees.store') }}">
                                @csrf
                                
                                <div class="row">
                                    <!-- Personal Information -->
                                    <div class="col-md-4 mb-3">
                                        <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                               id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="middle_name" class="form-label">Middle Name</label>
                                        <input type="text" class="form-control @error('middle_name') is-invalid @enderror" 
                                               id="middle_name" name="middle_name" value="{{ old('middle_name') }}">
                                        @error('middle_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                               id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Contract Information -->
                                    <div class="col-md-6 mb-3">
                                        <label for="contract_start" class="form-label">Contract Start Date</label>
                                        <input type="date" class="form-control @error('contract_start') is-invalid @enderror" 
                                               id="contract_start" name="contract_start" value="{{ old('contract_start') }}">
                                        @error('contract_start')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="contract_type" class="form-label">Contract Type</label>
                                        <select class="form-select @error('contract_type') is-invalid @enderror" id="contract_type" name="contract_type">
                                            <option value="">Select Contract Type</option>
                                            <option value="PROBATIONARY" {{ old('contract_type') == 'PROBATIONARY' ? 'selected' : '' }}>Probationary</option>
                                            <option value="REGULAR" {{ old('contract_type') == 'REGULAR' ? 'selected' : '' }}>Regular</option>
                                        </select>
                                        @error('contract_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Work Information -->
                                    <div class="col-md-6 mb-3">
                                        <label for="role" class="form-label">Role/Position</label>
                                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role">
                                            <option value="">Select Role</option>
                                            <option value="ADMIN" {{ old('role') == 'ADMIN' ? 'selected' : '' }}>Admin</option>
                                            <option value="ADMIN SALES" {{ old('role') == 'ADMIN SALES' ? 'selected' : '' }}>Admin Sales</option>
                                            <option value="BUFFER - GEN. STAFF" {{ old('role') == 'BUFFER - GEN. STAFF' ? 'selected' : '' }}>Buffer - Gen. Staff</option>
                                            <option value="DATA ENCODER" {{ old('role') == 'DATA ENCODER' ? 'selected' : '' }}>Data Encoder</option>
                                            <option value="DRIVER" {{ old('role') == 'DRIVER' ? 'selected' : '' }}>Driver</option>
                                            <option value="MECHANIC" {{ old('role') == 'MECHANIC' ? 'selected' : '' }}>Mechanic</option>
                                            <option value="SALES ASST. - GEN. STAFF" {{ old('role') == 'SALES ASST. - GEN. STAFF' ? 'selected' : '' }}>Sales Asst. - Gen. Staff</option>
                                        </select>
                                        @error('role')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="location" class="form-label">Location</label>
                                        <select class="form-select @error('location') is-invalid @enderror" id="location" name="location">
                                            <option value="">Select Location</option>
                                            <option value="MAIN" {{ old('location') == 'MAIN' ? 'selected' : '' }}>Main</option>
                                            <option value="EXTENSION" {{ old('location') == 'EXTENSION' ? 'selected' : '' }}>Extension</option>
                                            <option value="ANNEX" {{ old('location') == 'ANNEX' ? 'selected' : '' }}>Annex</option>
                                            <option value="PREMIUM" {{ old('location') == 'PREMIUM' ? 'selected' : '' }}>Premium</option>
                                            <option value="WAREHOUSE" {{ old('location') == 'WAREHOUSE' ? 'selected' : '' }}>Warehouse</option>
                                        </select>
                                        @error('location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Government IDs -->
                                    <div class="col-md-4 mb-3">
                                        <label for="sss" class="form-label">SSS Number</label>
                                        <input type="text" class="form-control @error('sss') is-invalid @enderror" 
                                               id="sss" name="sss" value="{{ old('sss') }}" placeholder="XX-XXXXXXX-X">
                                        @error('sss')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="philhealth" class="form-label">PhilHealth Number</label>
                                        <input type="text" class="form-control @error('philhealth') is-invalid @enderror" 
                                               id="philhealth" name="philhealth" value="{{ old('philhealth') }}" placeholder="XX-XXXXXXXXX-X">
                                        @error('philhealth')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label for="pagibig" class="form-label">Pag-IBIG Number</label>
                                        <input type="text" class="form-control @error('pagibig') is-invalid @enderror" 
                                               id="pagibig" name="pagibig" value="{{ old('pagibig') }}" placeholder="XXXX-XXXXXX-XX">
                                        @error('pagibig')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Personal Details -->
                                    <div class="col-md-6 mb-3">
                                        <label for="birthdate" class="form-label">Birthdate</label>
                                        <input type="date" class="form-control @error('birthdate') is-invalid @enderror" 
                                               id="birthdate" name="birthdate" value="{{ old('birthdate') }}">
                                        @error('birthdate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Notes -->
                                    <div class="col-12 mb-3">
                                        <label for="notes" class="form-label">Notes</label>
                                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                  id="notes" name="notes" rows="4" placeholder="Additional notes about the employee...">{{ old('notes') }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Create Employee
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Help Card -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i>Help & Tips
                            </h6>
                        </div>
                        <div class="card-body">
                            <h6>Required Fields</h6>
                            <ul class="list-unstyled small text-muted">
                                <li>• First Name</li>
                                <li>• Last Name</li>
                                <li>• Status</li>
                            </ul>

                            <h6 class="mt-3">Government ID Formats</h6>
                            <ul class="list-unstyled small text-muted">
                                <li>• SSS: XX-XXXXXXX-X</li>
                                <li>• PhilHealth: XX-XXXXXXXXX-X</li>
                                <li>• Pag-IBIG: XXXX-XXXXXX-XX</li>
                            </ul>

                            <h6 class="mt-3">Contract Types</h6>
                            <ul class="list-unstyled small text-muted">
                                <li>• <strong>Probationary:</strong> New employees on trial period</li>
                                <li>• <strong>Regular:</strong> Permanent employees</li>
                            </ul>

                            <h6 class="mt-3">Status</h6>
                            <p class="small text-muted">Set to "Active" for currently working employees, "Inactive" for former employees.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
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
</script>

<style>
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



