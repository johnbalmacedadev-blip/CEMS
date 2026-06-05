@php
    $record = $vehicle_registration ?? null;
    $isEdit = (bool) $record;
@endphp

@php
    $branches = $branches ?? \App\Models\BranchLocation::active()->ordered()->get();
@endphp

<div class="row g-3 vehicle-registration-form">
    <div class="col-md-3">
        <label for="branch_location_id" class="form-label">Branch / Store Location</label>
        <select class="form-select @error('branch_location_id') is-invalid @enderror" id="branch_location_id" name="branch_location_id">
            <option value="">— Select branch —</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}" {{ (string) old('branch_location_id', $record?->branch_location_id) === (string) $branch->id ? 'selected' : '' }}>
                    {{ $branch->name }}
                </option>
            @endforeach
        </select>
        @error('branch_location_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if($branches->isEmpty())
            <div class="form-text">Add branches in <a href="{{ route('settings.branch-locations.index') }}">Settings → Branch / Location</a>.</div>
        @endif
    </div>
    <div class="col-md-3">
        <label for="date" class="form-label">DATE <span class="text-danger">*</span></label>
        <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date"
            value="{{ old('date', $record ? $record->date->format('Y-m-d') : date('Y-m-d')) }}" required>
        @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        @php
            $selectedVehicleId = old('vehicle_id', $record?->vehicle_id ?? request('vehicle_id'));
            $vehicleDisplay = old('vehicle_display', '');
            $sv = $record?->vehicle ?? ($selectedVehicleId ? $vehicles->firstWhere('id', (int) $selectedVehicleId) : null);
            $svMake = '';
            $svSeries = '';
            if ($sv) {
                $svMake = $sv->make && is_object($sv->make) ? $sv->make->name : ($sv->make ?? '');
                $svSeries = $sv->vehicleModel && is_object($sv->vehicleModel) ? $sv->vehicleModel->name : ($sv->model ?? '');
                if (!$vehicleDisplay) {
                    $vehicleDisplay = ($sv->plate_number ?: 'No plate') . ' — ' . $sv->year . ' ' . $svMake . ' ' . $svSeries;
                }
            }
        @endphp
        <label for="plate_search" class="form-label">PLATE <span class="text-danger">*</span></label>
        <div class="plate-search-wrap position-relative">
            <input type="text" class="form-control @error('vehicle_id') is-invalid @enderror" id="plate_search"
                placeholder="Search plate number, make, model, or year..." autocomplete="off" value="{{ $vehicleDisplay }}">
            <input type="hidden" name="vehicle_id" id="vehicle_id" value="{{ $selectedVehicleId }}" required>
            <button type="button" class="btn btn-outline-secondary plate-search-clear" id="plate_search_clear" title="Clear"
                style="{{ $selectedVehicleId ? '' : 'display:none;' }}"><i class="fas fa-times"></i></button>
            <div id="plate_search_results" class="list-group plate-search-results shadow-sm"></div>
        </div>
        @error('vehicle_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        <div class="form-text">Type at least 2 characters to search by plate.</div>
    </div>

    <div class="col-md-2">
        <label class="form-label">YEAR</label>
        <input type="text" class="form-control bg-light" id="vehicle_year" value="{{ $sv->year ?? '' }}" readonly tabindex="-1">
    </div>
    <div class="col-md-5">
        <label class="form-label">MAKE</label>
        <input type="text" class="form-control bg-light" id="vehicle_make" value="{{ $svMake }}" readonly tabindex="-1">
    </div>
    <div class="col-md-5">
        <label class="form-label">SERIES</label>
        <input type="text" class="form-control bg-light" id="vehicle_series" value="{{ $svSeries }}" readonly tabindex="-1">
    </div>

    <div class="col-md-4">
        <label for="renewal_reg_or" class="form-label">RENEWAL REG. OR</label>
        <input type="number" class="form-control fee-input" id="renewal_reg_or" name="renewal_reg_or" step="0.01" min="0"
            value="{{ old('renewal_reg_or', $record?->renewal_reg_or) }}">
    </div>
    <div class="col-md-4">
        <label for="renewal_sop" class="form-label">RENEWAL SOP</label>
        <input type="number" class="form-control fee-input" id="renewal_sop" name="renewal_sop" step="0.01" min="0"
            value="{{ old('renewal_sop', $record?->renewal_sop) }}" placeholder="400.00">
    </div>
    <div class="col-md-4">
        <label for="smoke_na" class="form-label">SMOKE NA</label>
        <input type="number" class="form-control fee-input" id="smoke_na" name="smoke_na" step="0.01" min="0"
            value="{{ old('smoke_na', $record?->smoke_na) }}" placeholder="1000.00">
    </div>
    <div class="col-md-4">
        <label for="duplicate_plate" class="form-label">DUPLICATE PLATE</label>
        <input type="number" class="form-control fee-input" id="duplicate_plate" name="duplicate_plate" step="0.01" min="0"
            value="{{ old('duplicate_plate', $record?->duplicate_plate) }}">
    </div>
    <div class="col-md-4">
        <label for="migrate" class="form-label">MIGRATE</label>
        <input type="number" class="form-control fee-input" id="migrate" name="migrate" step="0.01" min="0"
            value="{{ old('migrate', $record?->migrate) }}">
    </div>
    <div class="col-md-4">
        <label for="duplicate_cr" class="form-label">DUPLICATE CR</label>
        <input type="number" class="form-control fee-input" id="duplicate_cr" name="duplicate_cr" step="0.01" min="0"
            value="{{ old('duplicate_cr', $record?->duplicate_cr) }}">
    </div>
    <div class="col-md-4">
        <label for="pnp_clearance" class="form-label">PNP CLEARANCE</label>
        <input type="number" class="form-control fee-input" id="pnp_clearance" name="pnp_clearance" step="0.01" min="0"
            value="{{ old('pnp_clearance', $record?->pnp_clearance) }}">
    </div>
    <div class="col-md-4">
        <label for="confirmation" class="form-label">CONFIRMATION</label>
        <input type="number" class="form-control fee-input" id="confirmation" name="confirmation" step="0.01" min="0"
            value="{{ old('confirmation', $record?->confirmation) }}">
    </div>
    <div class="col-md-4">
        <label for="remarks" class="form-label">REMARKS</label>
        <input type="text" class="form-control" id="remarks" name="remarks" value="{{ old('remarks', $record?->remarks) }}"
            placeholder="e.g. C/O ALYSSA">
    </div>
    <div class="col-md-4">
        <label for="coc_no" class="form-label">COC NO.</label>
        <input type="text" class="form-control" id="coc_no" name="coc_no" value="{{ old('coc_no', $record?->coc_no) }}"
            placeholder="e.g. COC 14380952">
    </div>
    <div class="col-md-4">
        <label for="status" class="form-label">STATUS</label>
        <input type="text" class="form-control" id="status" name="status" list="status_suggestions"
            value="{{ old('status', $record?->status) }}" placeholder="e.g. DONE NA FEB. 6">
        <datalist id="status_suggestions">
            @foreach(\App\Models\VehicleRegistration::statusSuggestions() as $opt)
                <option value="{{ $opt }}"></option>
            @endforeach
        </datalist>
    </div>
    <div class="col-md-4">
        <label class="form-label">TOTAL</label>
        <div class="form-control bg-light fw-bold text-danger fs-5" id="fee_total_display">0.00</div>
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i>{{ $isEdit ? 'Update' : 'Save' }}
        </button>
        <a href="{{ route('vehicle-registration.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</div>
