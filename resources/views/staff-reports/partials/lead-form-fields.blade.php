@php
    $prefix = $prefix ?? 'add';
    $useOld = (bool) ($useOld ?? false);
    $defaultDate = $prefix === 'add' ? now()->toDateString() : '';
    $val = function (string $field, $fallback = '') use ($useOld) {
        return $useOld ? old($field, $fallback) : $fallback;
    };
@endphp
<div class="row g-3">
    <div class="col-md-3">
        <label for="{{ $prefix }}_application_date" class="form-label">Application date</label>
        <input type="date" class="form-control @error('application_date') is-invalid @enderror" id="{{ $prefix }}_application_date" name="application_date" value="{{ $val('application_date', $defaultDate) }}">
        @error('application_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label for="{{ $prefix }}_application_source" class="form-label">Source</label>
        <input type="text" class="form-control @error('application_source') is-invalid @enderror" id="{{ $prefix }}_application_source" name="application_source" value="{{ $val('application_source') }}" placeholder="e.g. FB, Walk-in">
        @error('application_source')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label for="{{ $prefix }}_customer_name" class="form-label">Customer name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('customer_name') is-invalid @enderror" id="{{ $prefix }}_customer_name" name="customer_name" value="{{ $val('customer_name') }}" required>
        @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label for="{{ $prefix }}_customer_phone" class="form-label">Phone</label>
        <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" id="{{ $prefix }}_customer_phone" name="customer_phone" value="{{ $val('customer_phone') }}">
        @error('customer_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="{{ $prefix }}_unit_inquired" class="form-label">Unit inquired</label>
        <input type="text" class="form-control @error('unit_inquired') is-invalid @enderror" id="{{ $prefix }}_unit_inquired" name="unit_inquired" value="{{ $val('unit_inquired') }}">
        @error('unit_inquired')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="{{ $prefix }}_about_what" class="form-label">About / interest</label>
        <input type="text" class="form-control @error('about_what') is-invalid @enderror" id="{{ $prefix }}_about_what" name="about_what" value="{{ $val('about_what') }}">
        @error('about_what')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="{{ $prefix }}_notes" class="form-label">Notes</label>
        <input type="text" class="form-control @error('notes') is-invalid @enderror" id="{{ $prefix }}_notes" name="notes" value="{{ $val('notes') }}">
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="accordion mt-4" id="{{ $prefix }}LeadFollowupsAccordion">
    @foreach ([1 => '1st', 2 => '2nd', 3 => '3rd', 4 => '4th'] as $n => $label)
        <div class="accordion-item">
            <h2 class="accordion-header" id="{{ $prefix }}FuHeading{{ $n }}">
                <button class="accordion-button {{ $n === 1 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $prefix }}FuCollapse{{ $n }}" aria-expanded="{{ $n === 1 ? 'true' : 'false' }}" aria-controls="{{ $prefix }}FuCollapse{{ $n }}">
                    {{ $label }} follow-up (optional)
                </button>
            </h2>
            <div id="{{ $prefix }}FuCollapse{{ $n }}" class="accordion-collapse collapse {{ $n === 1 ? 'show' : '' }}" aria-labelledby="{{ $prefix }}FuHeading{{ $n }}" data-bs-parent="#{{ $prefix }}LeadFollowupsAccordion">
                <div class="accordion-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label" for="{{ $prefix }}_followup_{{ $n }}_exec">Executive / agent</label>
                            <input type="text" class="form-control" id="{{ $prefix }}_followup_{{ $n }}_exec" name="followup_{{ $n }}_exec" value="{{ $val('followup_'.$n.'_exec') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="{{ $prefix }}_followup_{{ $n }}_date">Date</label>
                            <input type="date" class="form-control" id="{{ $prefix }}_followup_{{ $n }}_date" name="followup_{{ $n }}_date" value="{{ $val('followup_'.$n.'_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="{{ $prefix }}_followup_{{ $n }}_outcome">Outcome</label>
                            <input type="text" class="form-control" id="{{ $prefix }}_followup_{{ $n }}_outcome" name="followup_{{ $n }}_outcome" value="{{ $val('followup_'.$n.'_outcome') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="{{ $prefix }}_followup_{{ $n }}_notes">Notes</label>
                            <input type="text" class="form-control" id="{{ $prefix }}_followup_{{ $n }}_notes" name="followup_{{ $n }}_notes" value="{{ $val('followup_'.$n.'_notes') }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
