<label class="perm-check-wrap {{ ($disabled ?? false) ? 'is-disabled' : '' }}">
    <input type="checkbox"
           class="perm-check-input"
           name="permissions[{{ $slug }}][{{ $field }}]"
           value="1"
           {{ ($checked ?? false) ? 'checked' : '' }}
           {{ ($disabled ?? false) ? 'disabled' : '' }}>
    <span class="perm-check-visual" aria-hidden="true"><i class="fas fa-check"></i></span>
    <span class="visually-hidden">{{ ucfirst(str_replace('can_', '', $field)) }}</span>
</label>
