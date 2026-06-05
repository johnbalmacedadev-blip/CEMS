@extends('layouts.app')

@section('title', 'Sales Agents - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Main Content -->
        <main class="col-12 px-md-4 main-content" id="mainContent">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-user-tie me-2"></i>Sales Agents Management
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('sales-agents.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Add New Agent
                    </a>
                </div>
            </div>

            <!-- Status Tabs -->
            <div class="card mb-4">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="statusTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $status === 'all' ? 'active' : '' }}" 
                               href="{{ route('sales-agents.index', ['status' => 'all']) }}" 
                               role="tab">
                                <i class="fas fa-list me-1"></i>All Agents
                                <span class="badge bg-secondary ms-1">{{ $activeCount + $inactiveCount }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $status === 'active' ? 'active' : '' }}" 
                               href="{{ route('sales-agents.index', ['status' => 'active']) }}" 
                               role="tab">
                                <i class="fas fa-check-circle me-1"></i>Active
                                <span class="badge bg-success ms-1">{{ $activeCount }}</span>
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link {{ $status === 'inactive' ? 'active' : '' }}" 
                               href="{{ route('sales-agents.index', ['status' => 'inactive']) }}" 
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
                    <form method="GET" action="{{ route('sales-agents.index') }}" class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Search by name, email, sales agent ID..." 
                                       value="{{ $search ?? '' }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="status">
                                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>
                                    All Status
                                </option>
                                <option value="active" {{ $status === 'active' ? 'selected' : '' }}>
                                    <i class="fas fa-check-circle me-1"></i>Active
                                </option>
                                <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>
                                    <i class="fas fa-times-circle me-1"></i>Inactive
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i>Search
                            </button>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('sales-agents.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-times me-1"></i>Clear All
                            </a>
                        </div>
                    </form>
                    @if($search || $status !== 'all')
                        <div class="mt-3">
                            @if($search)
                                <span class="badge bg-info me-2">
                                    <i class="fas fa-search me-1"></i>
                                    Search: "{{ $search }}"
                                    <a href="{{ route('sales-agents.index', ['status' => $status]) }}" class="text-white ms-2">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                            @if($status !== 'all')
                                <span class="badge bg-primary me-2">
                                    <i class="fas fa-filter me-1"></i>
                                    Status: {{ ucfirst($status) }}
                                    <a href="{{ route('sales-agents.index', ['search' => $search]) }}" class="text-white ms-2">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sales Agents Table -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        @if($search && $status !== 'all')
                            Search Results - {{ ucfirst($status) }} Agents
                        @elseif($search)
                            Search Results
                        @elseif($status === 'all')
                            All Sales Agents
                        @else
                            {{ ucfirst($status) }} Agents
                        @endif
                        <span class="badge bg-secondary ms-2">{{ $agents->total() }} total</span>
                        @if($search)
                            <span class="badge bg-info ms-1">for "{{ $search }}"</span>
                        @endif
                        @if($status !== 'all')
                            <span class="badge bg-primary ms-1">{{ ucfirst($status) }}</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    @if($agents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Sales Agent ID</th>
                                        <th>Executive</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Department</th>
                                        <th>Position</th>
                                        <th>Commission Rate</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($agents as $index => $agent)
                                        <tr>
                                            <td>{{ $agents->firstItem() + $index }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                        {{ strtoupper(substr($agent->name, 0, 2)) }}
                                                    </div>
                                                    <div>
                                                        <strong>{{ $agent->name }}</strong>
                                                        @if($agent->hire_date)
                                                            <br><small class="text-muted">Hired: {{ $agent->formatted_hire_date }}</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-secondary">{{ $agent->sales_agent_id }}</span></td>
                                            <td>{{ $agent->executiveAgent?->name ?? '—' }}</td>
                                            <td>{{ $agent->email }}</td>
                                            <td>{{ $agent->phone ?: 'N/A' }}</td>
                                            <td>{{ $agent->department ?: 'N/A' }}</td>
                                            <td>{{ $agent->position ?: 'N/A' }}</td>
                                            <td>{{ $agent->formatted_commission_rate }}</td>
                                            <td>
                                                @if($agent->status === 'active')
                                                    <span class="badge bg-success">{{ ucfirst($agent->status) }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ ucfirst($agent->status) }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('sales-agents.show', $agent) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('sales-agents.edit', $agent) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="deleteAgent({{ $agent->id }}, '{{ $agent->name }}')" title="Delete">
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
                            {{ $agents->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-user-tie fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No sales agents found</h5>
                            <p class="text-muted">
                                @if($search || $status !== 'all')
                                    Try adjusting your search criteria or 
                                    <a href="{{ route('sales-agents.index') }}">view all agents</a>.
                                @else
                                    <a href="{{ route('sales-agents.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-1"></i>Add Your First Agent
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

// Delete agent function
function deleteAgent(agentId, agentName) {
    document.getElementById('agentName').textContent = agentName;
    document.getElementById('deleteForm').action = `/sales-agents/${agentId}`;
    
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
