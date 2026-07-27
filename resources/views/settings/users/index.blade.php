@extends('layouts.app')

@section('title', 'User Management - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-users-cog me-2"></i>User Management</h1>
        <div>
            <a href="{{ route('settings') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i>Back to Settings
            </a>
            <a href="{{ route('settings.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add User
            </a>
        </div>
    </div>

    <div class="alert alert-light border mb-4">
        <div class="row g-3 align-items-start">
            <div class="col-md-4">
                <strong class="d-block"><span class="badge bg-danger me-1">Super Admin</span></strong>
                <span class="small text-muted">Full access to every page, settings, and user management.</span>
            </div>
            <div class="col-md-4">
                <strong class="d-block"><span class="badge bg-primary me-1">Contributor</span></strong>
                <span class="small text-muted">View, add, and edit records across modules. Limited delete; no user admin logs.</span>
            </div>
            <div class="col-md-4">
                <strong class="d-block"><span class="badge bg-secondary me-1">Spectator</span></strong>
                <span class="small text-muted">View-only access to available pages. Cannot create, edit, or delete.</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th width="220">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                            <tr>
                                <td>{{ $u->name }}</td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    @if($u->isAdmin())
                                        <span class="badge bg-danger">Super Admin</span>
                                    @else
                                        <span class="badge bg-primary">User</span>
                                        <a href="{{ route('settings.users.permissions', $u) }}" class="small ms-1">Permissions</a>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('settings.users.permissions', $u) }}" class="btn btn-sm btn-outline-info" title="Page permissions">
                                        <i class="fas fa-key"></i>
                                    </a>
                                    <a href="{{ route('settings.users.edit', $u) }}" class="btn btn-sm btn-outline-secondary" title="Edit user">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($u->id !== auth()->id())
                                        <form action="{{ route('settings.users.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this user? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete user">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
