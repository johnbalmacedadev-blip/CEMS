@php
    $unit = $unit ?? null;
@endphp
<div class="row g-3">
    <div class="col-md-2">
        <label for="list_number" class="form-label">#</label>
        <input type="number" class="form-control @error('list_number') is-invalid @enderror" id="list_number" name="list_number" value="{{ old('list_number', $unit->list_number ?? '') }}" min="0">
        @error('list_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-5">
        <label for="make_model" class="form-label">Make / Model <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('make_model') is-invalid @enderror" id="make_model" name="make_model" value="{{ old('make_model', $unit->make_model ?? '') }}" required>
        @error('make_model')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-5">
        <label for="plate_number" class="form-label">Plate Number</label>
        <input type="text" class="form-control @error('plate_number') is-invalid @enderror" id="plate_number" name="plate_number" value="{{ old('plate_number', $unit->plate_number ?? '') }}" placeholder="Links to vehicle profile when matched">
        @error('plate_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="variant" class="form-label">Variant</label>
        <input type="text" class="form-control @error('variant') is-invalid @enderror" id="variant" name="variant" value="{{ old('variant', $unit->variant ?? '') }}">
        @error('variant')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label for="transmission" class="form-label">Transmission</label>
        <input type="text" class="form-control @error('transmission') is-invalid @enderror" id="transmission" name="transmission" value="{{ old('transmission', $unit->transmission ?? '') }}" list="transmission_options" placeholder="A/T or M/T">
        <datalist id="transmission_options">
            <option value="A/T"></option>
            <option value="M/T"></option>
        </datalist>
        @error('transmission')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label for="fuel_type" class="form-label">Fuel Type</label>
        <input type="text" class="form-control @error('fuel_type') is-invalid @enderror" id="fuel_type" name="fuel_type" value="{{ old('fuel_type', $unit->fuel_type ?? '') }}" list="fuel_options">
        <datalist id="fuel_options">
            <option value="Gas"></option>
            <option value="Diesel"></option>
            <option value="Hybrid"></option>
        </datalist>
        @error('fuel_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label for="year" class="form-label">Year</label>
        <input type="text" class="form-control @error('year') is-invalid @enderror" id="year" name="year" value="{{ old('year', $unit->year ?? '') }}" placeholder="e.g. 2023">
        @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label for="mileage" class="form-label">Mileage</label>
        <input type="number" class="form-control @error('mileage') is-invalid @enderror" id="mileage" name="mileage" value="{{ old('mileage', $unit->mileage ?? '') }}" min="0">
        @error('mileage')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label for="price" class="form-label">Price</label>
        <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $unit->price ?? '') }}" min="0">
        @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label for="vehicle_id" class="form-label">Link Vehicle Profile</label>
        <select class="form-select @error('vehicle_id') is-invalid @enderror" id="vehicle_id" name="vehicle_id">
            <option value="">— Auto by plate / none —</option>
            @foreach($vehicles as $v)
                <option value="{{ $v->id }}" {{ (string) old('vehicle_id', $unit->vehicle_id ?? '') === (string) $v->id ? 'selected' : '' }}>
                    {{ $v->full_name }} @if($v->plate_number)({{ $v->plate_number }})@endif
                </option>
            @endforeach
        </select>
        @error('vehicle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="low_down_payment_option" class="form-label">Low Down Payment Option</label>
        <textarea class="form-control @error('low_down_payment_option') is-invalid @enderror" id="low_down_payment_option" name="low_down_payment_option" rows="5">{{ old('low_down_payment_option', $unit->low_down_payment_option ?? '') }}</textarea>
        @error('low_down_payment_option')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <button type="button" class="btn btn-sm btn-outline-primary mt-2 recompute-financing-btn"
            data-target="low_down_payment_option"
            data-label="Low Down Payment Option"
            data-default-dp="5">
            <i class="fas fa-calculator me-1"></i>Re-compute
        </button>
    </div>
    <div class="col-md-6">
        <label for="low_monthly_option" class="form-label">Low Monthly Option</label>
        <textarea class="form-control @error('low_monthly_option') is-invalid @enderror" id="low_monthly_option" name="low_monthly_option" rows="5">{{ old('low_monthly_option', $unit->low_monthly_option ?? '') }}</textarea>
        @error('low_monthly_option')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <button type="button" class="btn btn-sm btn-outline-primary mt-2 recompute-financing-btn"
            data-target="low_monthly_option"
            data-label="Low Monthly Option"
            data-default-dp="10">
            <i class="fas fa-calculator me-1"></i>Re-compute
        </button>
    </div>
    <div class="col-12">
        <label for="notes" class="form-label">Notes</label>
        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes', $unit->notes ?? '') }}</textarea>
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
