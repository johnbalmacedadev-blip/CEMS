<div class="input-group password-toggle-group">
    <input type="password"
        class="form-control @error($name) is-invalid @enderror"
        id="{{ $id }}"
        name="{{ $name }}"
        @if(!empty($required)) required @endif
        @if(!empty($autocomplete)) autocomplete="{{ $autocomplete }}" @endif>
    <button type="button" class="btn btn-outline-secondary toggle-password" data-target="{{ $id }}" aria-label="Show password" title="Show password">
        <i class="fas fa-eye"></i>
    </button>
    @error($name)<div class="invalid-feedback d-block w-100">{{ $message }}</div>@enderror
</div>
