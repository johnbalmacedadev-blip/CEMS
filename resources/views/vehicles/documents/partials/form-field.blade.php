<div class="card mb-2 form-field-row" data-field-index="{{ $index }}">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-3">
                <label class="form-label small">Field Label</label>
                <input type="text" class="form-control form-field-label" 
                       placeholder="Field Label" 
                       value="{{ $field['label'] ?? '' }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Input Type</label>
                <select class="form-select form-field-type">
                    <option value="text" {{ ($field['type'] ?? 'text') == 'text' ? 'selected' : '' }}>Text</option>
                    <option value="number" {{ ($field['type'] ?? '') == 'number' ? 'selected' : '' }}>Number</option>
                    <option value="date" {{ ($field['type'] ?? '') == 'date' ? 'selected' : '' }}>Date</option>
                    <option value="textarea" {{ ($field['type'] ?? '') == 'textarea' ? 'selected' : '' }}>Textarea</option>
                    <option value="select" {{ ($field['type'] ?? '') == 'select' ? 'selected' : '' }}>Select</option>
                    <option value="checkbox" {{ ($field['type'] ?? '') == 'checkbox' ? 'selected' : '' }}>Checkbox</option>
                    <option value="radio" {{ ($field['type'] ?? '') == 'radio' ? 'selected' : '' }}>Radio</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Field Name</label>
                <input type="text" class="form-control form-field-name" 
                       placeholder="field_name" 
                       value="{{ $field['name'] ?? '' }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small">Value/Options</label>
                <div class="form-field-input-container">
                    @if(($field['type'] ?? 'text') == 'textarea')
                        <textarea class="form-control form-field-value" placeholder="Field Value">{{ $formData[$field['name'] ?? ''] ?? '' }}</textarea>
                    @elseif(($field['type'] ?? 'text') == 'select' || ($field['type'] ?? 'text') == 'radio')
                        <input type="text" class="form-control form-field-options mb-1" 
                               placeholder="Option1, Option2, Option3" 
                               value="{{ isset($field['options']) && is_array($field['options']) ? implode(', ', $field['options']) : '' }}">
                        <select class="form-control form-field-value mt-1" style="display: {{ ($field['type'] ?? 'text') == 'select' ? 'block' : 'none' }};">
                            @if(isset($field['options']) && is_array($field['options']))
                                @foreach($field['options'] as $option)
                                    <option value="{{ $option }}" {{ (isset($formData[$field['name'] ?? '']) && $formData[$field['name'] ?? ''] == $option) ? 'selected' : '' }}>{{ $option }}</option>
                                @endforeach
                            @endif
                        </select>
                    @elseif(($field['type'] ?? 'text') == 'checkbox')
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input form-field-value" 
                                   {{ ($formData[$field['name'] ?? ''] ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label">Checked</label>
                        </div>
                    @else
                        <input type="{{ $field['type'] ?? 'text' }}" class="form-control form-field-value" 
                               placeholder="Field Value" 
                               value="{{ $formData[$field['name'] ?? ''] ?? '' }}">
                    @endif
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label small">&nbsp;</label>
                <div>
                    <button type="button" class="btn btn-sm btn-danger remove-field">
                        <i class="fas fa-times"></i> Remove
                    </button>
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input form-field-required" 
                           {{ ($field['required'] ?? false) ? 'checked' : '' }}>
                    <label class="form-check-label small">Required</label>
                </div>
            </div>
            <div class="col-md-9">
                <input type="text" class="form-control form-control-sm form-field-placeholder" 
                       placeholder="Placeholder text (optional)" 
                       value="{{ $field['placeholder'] ?? '' }}">
            </div>
        </div>
    </div>
</div>

