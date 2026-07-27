@extends('layouts.app')

@section('title', 'Add User - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-user-plus me-2"></i>Add User</h1>
        <a href="{{ route('settings.users.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Users
        </a>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-light">
            <strong><i class="fas fa-layer-group me-2"></i>Permission Templates</strong>
        </div>
        <div class="card-body">
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
            <form action="{{ route('settings.users.store') }}" method="POST" id="createUserForm">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password</label>
                        @include('partials.password-input', ['id' => 'password', 'name' => 'password', 'required' => true, 'autocomplete' => 'new-password'])
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        @include('partials.password-input', ['id' => 'password_confirmation', 'name' => 'password_confirmation', 'required' => true, 'autocomplete' => 'new-password'])
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="permission_template" class="form-label">Access Template</label>
                        <select class="form-select @error('permission_template') is-invalid @enderror" id="permission_template" name="permission_template" required>
                            @foreach($templates as $key => $tpl)
                                <option value="{{ $key }}" {{ old('permission_template', 'spectator') === $key ? 'selected' : '' }}>
                                    {{ $tpl['label'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('permission_template')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text" id="templateHelp">Choose a starting access level. You can fine-tune page permissions after creating the user.</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="role" class="form-label">System Role</label>
                        <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="user" {{ old('role', 'user') === 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Super Admin</option>
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="form-text">Super Admin bypasses page permissions. Contributor and Spectator use the User role with a permission matrix.</div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Create User</button>
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
        const tpl = templates[key];
        if (!tpl) return;
        roleSelect.value = tpl.sets_role === 'admin' ? 'admin' : 'user';
        if (help) help.textContent = tpl.description;
        document.querySelectorAll('.template-card').forEach(function (card) {
            card.classList.toggle('border-primary', card.dataset.template === key);
            card.classList.toggle('bg-light', card.dataset.template === key);
        });
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
