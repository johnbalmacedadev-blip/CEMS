@extends('layouts.app')

@section('title', 'Edit User - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-user-edit me-2"></i>Edit User: {{ $user->name }}</h1>
        <div>
            <a href="{{ route('settings.users.permissions', $user) }}" class="btn btn-outline-info me-2"><i class="fas fa-key me-1"></i>Page Permissions</a>
            <a href="{{ route('settings.users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Users
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-light">
            <strong><i class="fas fa-layer-group me-2"></i>Apply Access Template</strong>
        </div>
        <div class="card-body">
            <p class="text-muted small mb-3">Optionally apply a template when saving. Leave blank to keep the current role and permissions unchanged.</p>
            <div class="row g-3">
                @foreach($templates as $key => $tpl)
                    <div class="col-md-4">
                        <div class="border rounded p-3 h-100 template-card" data-template="{{ $key }}">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-{{ $tpl['badge'] ?? 'secondary' }} me-2">
                                    <i class="fas {{ $tpl['icon'] ?? 'fa-user' }}"></i>
                                </span>
                                <strong>{{ $tpl['label'] }}</strong>
                            </div>
                            <p class="text-muted small mb-0">{{ $tpl['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('settings.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">New Password <span class="text-muted">(leave blank to keep current)</span></label>
                        @include('partials.password-input', ['id' => 'password', 'name' => 'password', 'autocomplete' => 'new-password'])
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        @include('partials.password-input', ['id' => 'password_confirmation', 'name' => 'password_confirmation', 'autocomplete' => 'new-password'])
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="permission_template" class="form-label">Access Template <span class="text-muted">(optional)</span></label>
                        <select class="form-select @error('permission_template') is-invalid @enderror" id="permission_template" name="permission_template">
                            <option value="">— Keep current permissions —</option>
                            @foreach($templates as $key => $tpl)
                                <option value="{{ $key }}" {{ old('permission_template') === $key ? 'selected' : '' }}>
                                    {{ $tpl['label'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('permission_template')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text" id="templateHelp">Applying a template overwrites page permissions for this user.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="role" class="form-label">System Role</label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Super Admin</option>
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Update User</button>
                <a href="{{ route('settings.users.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@include('partials.password-toggle-script')
@endsection

@section('scripts')
<script>
(function () {
    const templates = @json(collect($templates)->mapWithKeys(fn ($t, $k) => [$k => ['sets_role' => $t['sets_role'] ?? 'user', 'description' => $t['description'] ?? '']]));
    const templateSelect = document.getElementById('permission_template');
    const roleSelect = document.getElementById('role');
    const help = document.getElementById('templateHelp');

    function syncFromTemplate() {
        const key = templateSelect.value;
        document.querySelectorAll('.template-card').forEach(function (card) {
            card.classList.toggle('border-primary', card.dataset.template === key);
            card.classList.toggle('bg-light', card.dataset.template === key);
        });
        if (!key) {
            if (help) help.textContent = 'Leave blank to keep current permissions.';
            return;
        }
        const tpl = templates[key];
        if (!tpl) return;
        roleSelect.value = tpl.sets_role === 'admin' ? 'admin' : 'user';
        if (help) help.textContent = tpl.description;
    }

    templateSelect.addEventListener('change', syncFromTemplate);
    document.querySelectorAll('.template-card').forEach(function (card) {
        card.style.cursor = 'pointer';
        card.addEventListener('click', function () {
            templateSelect.value = card.dataset.template;
            syncFromTemplate();
        });
    });
    syncFromTemplate();
})();
</script>
@endsection
