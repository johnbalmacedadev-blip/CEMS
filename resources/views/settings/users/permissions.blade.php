@extends('layouts.app')

@section('title', 'Page Permissions - ' . $user->name . ' - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-key me-2"></i>Page Permissions: {{ $user->name }}</h1>
        <div>
            <a href="{{ route('settings.users.edit', $user) }}" class="btn btn-outline-secondary me-2">Edit User</a>
            <a href="{{ route('settings.users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Users
            </a>
        </div>
    </div>

    @if($user->isAdmin())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>Admins have full access to all pages. Permission checkboxes below do not apply to this user.
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <p class="text-muted mb-3">Select which pages this user can access and whether they can view, create, update, or delete.</p>
            <form action="{{ route('settings.users.permissions.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Page</th>
                                <th class="text-center">View</th>
                                <th class="text-center">Create / Save</th>
                                <th class="text-center">Edit / Update</th>
                                <th class="text-center">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pages as $slug => $label)
                                @php $p = $permissions[$slug] ?? ['can_view' => false, 'can_create' => false, 'can_update' => false, 'can_delete' => false]; @endphp
                                <tr>
                                    <td>{{ $label }}</td>
                                    <td class="text-center">
                                        <input type="checkbox" name="permissions[{{ $slug }}][can_view]" value="1" {{ $p['can_view'] ? 'checked' : '' }} {{ $user->isAdmin() ? 'disabled' : '' }}>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" name="permissions[{{ $slug }}][can_create]" value="1" {{ $p['can_create'] ? 'checked' : '' }} {{ $user->isAdmin() ? 'disabled' : '' }}>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" name="permissions[{{ $slug }}][can_update]" value="1" {{ $p['can_update'] ? 'checked' : '' }} {{ $user->isAdmin() ? 'disabled' : '' }}>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" name="permissions[{{ $slug }}][can_delete]" value="1" {{ $p['can_delete'] ? 'checked' : '' }} {{ $user->isAdmin() ? 'disabled' : '' }}>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if(!$user->isAdmin())
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Permissions</button>
                @endif
                <a href="{{ route('settings.users.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection
