@extends('layouts.app')

@section('title', 'Admin & Documents - Activity Logs')

@section('content')
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <button class="btn btn-outline-light me-3" type="button" onclick="window.history.back()">
            <i class="fas fa-chevron-left"></i>
        </button>
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('images/CAREMPIRE_LOGO.png') }}" alt="CAR EMPIRE Logo" onerror="this.style.display='none';">
        </a>
    </div>
</nav>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('settings') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Back to Settings
            </a>
            <h2 class="mb-0"><i class="fas fa-history me-2"></i>Activity Logs</h2>
        </div>
        <div class="d-flex gap-2">
            <form method="GET" action="{{ route('admin-docs') }}" class="d-flex gap-2">
                <!-- Action Filter -->
                <select name="action" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all" {{ request('action') == 'all' || !request('action') ? 'selected' : '' }}>All Actions</option>
                    <option value="login" {{ request('action') == 'login' ? 'selected' : '' }}>Login</option>
                    <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                    <option value="view" {{ request('action') == 'view' ? 'selected' : '' }}>View</option>
                    <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Create</option>
                    <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Edit / Update</option>
                    <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Delete</option>
                </select>

                <!-- Model Type Filter -->
                <select name="model_type" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all" {{ request('model_type') == 'all' || !request('model_type') ? 'selected' : '' }}>All Types</option>
                    @foreach($modelTypes ?? [] as $modelType)
                        <option value="{{ $modelType }}" {{ request('model_type') == $modelType ? 'selected' : '' }}>
                            {{ class_basename($modelType) }}
                        </option>
                    @endforeach
                </select>

                <!-- User Filter -->
                <select name="user_filter" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="all" {{ request('user_filter') == 'all' || !request('user_filter') ? 'selected' : '' }}>All Users</option>
                    <option value="current" {{ request('user_filter') == 'current' ? 'selected' : '' }}>My Activity</option>
                </select>

                @if(request()->hasAny(['action', 'model_type', 'user_filter']))
                    <a href="{{ route('admin-docs') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-times"></i> Clear
                    </a>
                @endif
            </form>
        </div>
    </div>

    @if(isset($logs) && $logs->count() > 0)
        <div class="card border-dark">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date & Time</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Page / Section</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td>
                                        <small class="text-muted">
                                            {{ $log->created_at->format('M d, Y') }}<br>
                                            {{ $log->created_at->format('h:i A') }}
                                        </small>
                                    </td>
                                    <td>
                                        <strong>{{ $log->user->name ?? 'Unknown' }}</strong><br>
                                        <small class="text-muted">{{ $log->user->email ?? '' }}</small>
                                    </td>
                                    <td>
                                        @if($log->action == 'login')
                                            <span class="badge bg-info">
                                                <i class="fas fa-sign-in-alt"></i> Login
                                            </span>
                                        @elseif($log->action == 'logout')
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-sign-out-alt"></i> Logout
                                            </span>
                                        @elseif($log->action == 'view')
                                            <span class="badge bg-primary">
                                                <i class="fas fa-eye"></i> View
                                            </span>
                                        @elseif($log->action == 'create')
                                            <span class="badge bg-success">
                                                <i class="fas fa-plus"></i> Create
                                            </span>
                                        @elseif($log->action == 'update')
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-edit"></i> Edit / Update
                                            </span>
                                        @elseif($log->action == 'delete')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-trash"></i> Delete
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($log->action) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->model_type === 'Auth' || empty($log->model_type))
                                            <small class="text-muted">—</small>
                                        @elseif($log->model_type === 'Page')
                                            <small>Page</small>
                                        @else
                                            <small>{{ class_basename($log->model_type) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $log->description }}
                                        @if($log->changes && $log->action == 'update')
                                            <button class="btn btn-sm btn-link p-0 ms-1" type="button" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#changes-{{ $log->id }}" 
                                                    aria-expanded="false">
                                                <small><i class="fas fa-chevron-down"></i> View Changes</small>
                                            </button>
                                            <div class="collapse mt-2" id="changes-{{ $log->id }}">
                                                <div class="card card-body bg-light">
                                                    @foreach($log->changes as $field => $change)
                                                        <small>
                                                            <strong>{{ $field }}:</strong>
                                                            @if(is_array($change))
                                                                {{ isset($change['old']) ? 'from "' . $change['old'] . '" to "' . ($change['new'] ?? 'N/A') . '"' : json_encode($change) }}
                                                            @else
                                                                {{ $change }}
                                                            @endif
                                                        </small><br>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log->page || $log->section)
                                            @if($log->page)
                                                <small class="text-primary">
                                                    <i class="fas fa-file-alt me-1"></i>{{ $log->page }}
                                                </small>
                                            @endif
                                            @if($log->section)
                                                <br><small class="text-info">
                                                    <i class="fas fa-tag me-1"></i>{{ $log->section }}
                                                </small>
                                            @endif
                                        @else
                                            <small class="text-muted">N/A</small>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $log->ip_address ?? 'N/A' }}</small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @if($logs->hasPages())
                <div class="card-footer">
                    <div class="d-flex justify-content-center">
                        {{ $logs->links() }}
                    </div>
                </div>
            @endif
        </div>
    @else
        <div class="card border-dark">
            <div class="card-body py-5 text-center">
                <div class="mb-3">
                    <span class="icon-circle text-dark"><i class="fas fa-history"></i></span>
                </div>
                <h5 class="mb-2">No Activity Logs Found</h5>
                <p class="text-muted mb-0">Activity logs will appear here once actions are performed.</p>
            </div>
        </div>
    @endif
</div>
@endsection

@section('styles')
<style>
.icon-circle { 
    width: 64px; 
    height: 64px; 
    border: 1px solid currentColor; 
    border-radius: 50%; 
    display: inline-flex; 
    align-items: center; 
    justify-content: center; 
    font-size: 1.5rem; 
}
.table th {
    border-top: none;
    font-weight: 600;
}
.badge {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
}
</style>
@endsection


