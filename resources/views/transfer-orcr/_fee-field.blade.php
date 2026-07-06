@php
    $fieldId = $id ?? $name;
    $paidChecked = !empty($paid);
@endphp
<div class="col-md-4">
    <div class="form-field-cell fee-field-wrap">
        <label for="{{ $fieldId }}" class="form-label">{{ $label }}</label>
        <div class="form-field-control">
            <div class="input-group fee-field flex-nowrap">
                <input type="number" class="form-control fee-input" id="{{ $fieldId }}" name="{{ $name }}" step="0.01" min="0"
                    value="{{ $value ?? '' }}">
                <span class="input-group-text fee-paid-addon">
                    <input type="checkbox" name="{{ $paidName }}" value="1" class="fee-paid" id="{{ $paidName }}"
                        {{ $paidChecked ? 'checked' : '' }}> Paid
                </span>
                <span class="input-group-text fee-paid-date-wrap">
                    <span class="fee-paid-date-label">Paid on</span>
                    <input type="date" class="form-control form-control-sm fee-paid-date" name="{{ $paidDateName }}"
                        value="{{ $paidDate ?? '' }}" title="Date paid">
                </span>
            </div>
        </div>
    </div>
</div>
