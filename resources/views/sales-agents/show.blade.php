@extends('layouts.app')

@section('title', 'Sales Agent Details - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main content -->
        <main class="col-12 ms-sm-auto px-md-4" id="mainContent">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-user-tie me-2"></i>Sales Agent Details
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('sales-agents.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i>Back to Agents
                    </a>
                    <a href="{{ route('sales-agents.edit', $salesAgent) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i>Edit Agent
                    </a>
                </div>
            </div>

            <!-- Status Banner -->
            <div class="alert alert-{{ $salesAgent->status === 'active' ? 'success' : 'danger' }} d-flex justify-content-between align-items-center mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-{{ $salesAgent->status === 'active' ? 'check-circle' : 'times-circle' }} me-2"></i>
                    <strong>Status: {{ ucfirst($salesAgent->status) }}</strong>
                    <span class="ms-3">
                        <i class="fas fa-id-badge me-1"></i>{{ $salesAgent->sales_agent_id }}
                    </span>
                </div>
            </div>

            <div class="row">
                <!-- Agent Information -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-user me-2"></i>Personal Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Full Name</label>
                                    <p class="form-control-plaintext">{{ $salesAgent->name }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Email Address</label>
                                    <p class="form-control-plaintext">
                                        <a href="mailto:{{ $salesAgent->email }}">{{ $salesAgent->email }}</a>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Phone Number</label>
                                    <p class="form-control-plaintext">
                                        @if($salesAgent->phone)
                                            <a href="tel:{{ $salesAgent->phone }}">{{ $salesAgent->phone }}</a>
                                        @else
                                            N/A
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Sales Agent ID</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-secondary">{{ $salesAgent->sales_agent_id }}</span>
                                    </p>
                                </div>
                                @if($salesAgent->address)
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">Address</label>
                                    <p class="form-control-plaintext">{{ $salesAgent->address }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Work Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-briefcase me-2"></i>Work Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Department</label>
                                    <p class="form-control-plaintext">{{ $salesAgent->department ?: 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Position</label>
                                    <p class="form-control-plaintext">{{ $salesAgent->position ?: 'N/A' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Hire Date</label>
                                    <p class="form-control-plaintext">{{ $salesAgent->formatted_hire_date }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Status</label>
                                    <p class="form-control-plaintext">
                                        <span class="badge bg-{{ $salesAgent->status === 'active' ? 'success' : 'danger' }}">
                                            {{ ucfirst($salesAgent->status) }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Compensation Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-dollar-sign me-2"></i>Compensation
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Commission Rate</label>
                                    <p class="form-control-plaintext">{{ $salesAgent->formatted_commission_rate }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Base Salary</label>
                                    <p class="form-control-plaintext">{{ $salesAgent->formatted_base_salary }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    @if($salesAgent->emergency_contact_name || $salesAgent->emergency_contact_phone)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-phone-alt me-2"></i>Emergency Contact
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($salesAgent->emergency_contact_name)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Contact Name</label>
                                    <p class="form-control-plaintext">{{ $salesAgent->emergency_contact_name }}</p>
                                </div>
                                @endif
                                @if($salesAgent->emergency_contact_phone)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Contact Phone</label>
                                    <p class="form-control-plaintext">
                                        <a href="tel:{{ $salesAgent->emergency_contact_phone }}">{{ $salesAgent->emergency_contact_phone }}</a>
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Notes -->
                    @if($salesAgent->notes)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-sticky-note me-2"></i>Notes
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="form-control-plaintext">{{ $salesAgent->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Agent Summary -->
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-user me-2"></i>Agent Summary
                            </h6>
                        </div>
                        <div class="card-body text-center">
                            <div class="avatar-xl bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3">
                                {{ strtoupper(substr($salesAgent->name, 0, 2)) }}
                            </div>
                            <h4 class="mb-1">{{ $salesAgent->name }}</h4>
                            <p class="text-muted mb-3">{{ $salesAgent->sales_agent_id }}</p>

                            <div class="row text-center mb-3">
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

                            <div class="small text-start">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Department:</span>
                                    <span>{{ $salesAgent->department ?: 'N/A' }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Position:</span>
                                    <span>{{ $salesAgent->position ?: 'N/A' }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Hire Date:</span>
                                    <span>{{ $salesAgent->formatted_hire_date }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Base Salary:</span>
                                    <span>{{ $salesAgent->formatted_base_salary }}</span>
                                </div>
                            </div>

                            <hr>

                            <div class="d-grid gap-2">
                                <a href="{{ route('sales-agents.edit', $salesAgent) }}" class="btn btn-primary">
                                    <i class="fas fa-edit me-1"></i>Edit Agent
                                </a>
                                <button type="button" class="btn btn-outline-danger" 
                                        onclick="deleteAgent({{ $salesAgent->id }}, '{{ $salesAgent->name }}')">
                                    <i class="fas fa-trash me-1"></i>Delete Agent
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-chart-line me-2"></i>Quick Stats
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="border-end">
                                        <h5 class="text-primary mb-1">0</h5>
                                        <small class="text-muted">Sales</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <h5 class="text-success mb-1">₱0</h5>
                                    <small class="text-muted">Commission</small>
                                </div>
                            </div>
                        </div>
                    </div>
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
                <p>Are you sure you want to delete <strong id="agentName"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Agent</button>
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

// Delete agent function
function deleteAgent(agentId, agentName) {
    document.getElementById('agentName').textContent = agentName;
    document.getElementById('deleteForm').action = `/sales-agents/${agentId}`;
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>

<style>
.avatar-xl {
    width: 120px;
    height: 120px;
    font-size: 36px;
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
