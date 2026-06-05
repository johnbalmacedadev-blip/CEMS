@extends('layouts.app')

@section('title', 'Edit Sales Agent - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main content -->
        <main class="col-12 ms-sm-auto px-md-4" id="mainContent">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-user-edit me-2"></i>Edit Sales Agent
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('sales-agents.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Agents
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-user-tie me-2"></i>Agent Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('sales-agents.update', $salesAgent) }}">
                                @csrf
                                @method('PUT')
                                
                                <div class="row">
                                    <!-- Basic Information -->
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $salesAgent->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email', $salesAgent->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" value="{{ old('phone', $salesAgent->phone) }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="sales_agent_id" class="form-label">Sales Agent ID <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('sales_agent_id') is-invalid @enderror" 
                                               id="sales_agent_id" name="sales_agent_id" value="{{ old('sales_agent_id', $salesAgent->sales_agent_id) }}" required>
                                        @error('sales_agent_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="executive_agent_id" class="form-label">Executive Agent</label>
                                        <select class="form-select @error('executive_agent_id') is-invalid @enderror" id="executive_agent_id" name="executive_agent_id">
                                            <option value="">— None —</option>
                                            @foreach(($executives ?? collect()) as $exec)
                                                <option value="{{ $exec->id }}" {{ (string) old('executive_agent_id', $salesAgent->executive_agent_id) === (string) $exec->id ? 'selected' : '' }}>{{ $exec->name }} ({{ $exec->executive_code }})</option>
                                            @endforeach
                                        </select>
                                        @error('executive_agent_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Work Information -->
                                    <div class="col-md-6 mb-3">
                                        <label for="department" class="form-label">Department</label>
                                        <select class="form-select @error('department') is-invalid @enderror" id="department" name="department">
                                            <option value="">Select Department</option>
                                            <option value="Sales" {{ old('department', $salesAgent->department) == 'Sales' ? 'selected' : '' }}>Sales</option>
                                            <option value="Marketing" {{ old('department', $salesAgent->department) == 'Marketing' ? 'selected' : '' }}>Marketing</option>
                                            <option value="Finance" {{ old('department', $salesAgent->department) == 'Finance' ? 'selected' : '' }}>Finance</option>
                                            <option value="Operations" {{ old('department', $salesAgent->department) == 'Operations' ? 'selected' : '' }}>Operations</option>
                                            <option value="Management" {{ old('department', $salesAgent->department) == 'Management' ? 'selected' : '' }}>Management</option>
                                        </select>
                                        @error('department')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="position" class="form-label">Position</label>
                                        <input type="text" class="form-control @error('position') is-invalid @enderror" 
                                               id="position" name="position" value="{{ old('position', $salesAgent->position) }}">
                                        @error('position')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="hire_date" class="form-label">Hire Date</label>
                                        <input type="date" class="form-control @error('hire_date') is-invalid @enderror" 
                                               id="hire_date" name="hire_date" value="{{ old('hire_date', $salesAgent->hire_date?->format('Y-m-d')) }}">
                                        @error('hire_date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                            <option value="active" {{ old('status', $salesAgent->status) == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status', $salesAgent->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Compensation -->
                                    <div class="col-md-6 mb-3">
                                        <label for="commission_rate" class="form-label">Commission Rate (%)</label>
                                        <input type="number" class="form-control @error('commission_rate') is-invalid @enderror" 
                                               id="commission_rate" name="commission_rate" value="{{ old('commission_rate', $salesAgent->commission_rate) }}" 
                                               min="0" max="100" step="0.01">
                                        @error('commission_rate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="base_salary" class="form-label">Base Salary (₱)</label>
                                        <input type="number" class="form-control @error('base_salary') is-invalid @enderror" 
                                               id="base_salary" name="base_salary" value="{{ old('base_salary', $salesAgent->base_salary) }}" 
                                               min="0" step="0.01">
                                        @error('base_salary')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Address -->
                                    <div class="col-12 mb-3">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                                  id="address" name="address" rows="3">{{ old('address', $salesAgent->address) }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Emergency Contact -->
                                    <div class="col-md-6 mb-3">
                                        <label for="emergency_contact_name" class="form-label">Emergency Contact Name</label>
                                        <input type="text" class="form-control @error('emergency_contact_name') is-invalid @enderror" 
                                               id="emergency_contact_name" name="emergency_contact_name" value="{{ old('emergency_contact_name', $salesAgent->emergency_contact_name) }}">
                                        @error('emergency_contact_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="emergency_contact_phone" class="form-label">Emergency Contact Phone</label>
                                        <input type="text" class="form-control @error('emergency_contact_phone') is-invalid @enderror" 
                                               id="emergency_contact_phone" name="emergency_contact_phone" value="{{ old('emergency_contact_phone', $salesAgent->emergency_contact_phone) }}">
                                        @error('emergency_contact_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Notes -->
                                    <div class="col-12 mb-3">
                                        <label for="notes" class="form-label">Notes</label>
                                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                  id="notes" name="notes" rows="4" placeholder="Additional notes about the agent...">{{ old('notes', $salesAgent->notes) }}</textarea>
                                        @error('notes')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('sales-agents.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-1"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i>Update Agent
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Agent Summary Card -->
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-user me-2"></i>Agent Summary
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <div class="avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
                                    {{ strtoupper(substr($salesAgent->name, 0, 2)) }}
                                </div>
                                <h5 class="mb-1">{{ $salesAgent->name }}</h5>
                                <p class="text-muted mb-0">{{ $salesAgent->sales_agent_id }}</p>
                            </div>

                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h6 class="text-muted mb-1">Status</h6>
                                        <span class="badge bg-{{ $salesAgent->status === 'active' ? 'success' : 'danger' }}">
                                            {{ ucfirst($salesAgent->status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h6 class="text-muted mb-1">Commission</h6>
                                    <span class="fw-bold">{{ $salesAgent->formatted_commission_rate }}</span>
                                </div>
                            </div>

                            <hr>

                            <div class="small">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Department:</span>
                                    <span>{{ $salesAgent->department ?: 'N/A' }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Position:</span>
                                    <span>{{ $salesAgent->position ?: 'N/A' }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Hire Date:</span>
                                    <span>{{ $salesAgent->formatted_hire_date }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Base Salary:</span>
                                    <span>{{ $salesAgent->formatted_base_salary }}</span>
                                </div>
                            </div>
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
.avatar-lg {
    width: 80px;
    height: 80px;
    font-size: 24px;
    font-weight: bold;
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
