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
                                    <span class="badge bg-{{ $u->role === 'admin' ? 'danger' : 'primary' }}">{{ ucfirst($u->role) }}</span>
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
