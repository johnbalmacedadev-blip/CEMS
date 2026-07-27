@php
    $r = $record;
@endphp
<div class="row g-3">
    <div class="col-md-3">
        <label for="job_date" class="form-label">Date <span class="text-danger">*</span></label>
        <input type="date" class="form-control @error('job_date') is-invalid @enderror" id="job_date" name="job_date"
               value="{{ old('job_date', optional($r?->job_date)->format('Y-m-d') ?? date('Y-m-d')) }}" required>
        @error('job_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label for="job_type" class="form-label">Job Type <span class="text-danger">*</span></label>
        <select class="form-select @error('job_type') is-invalid @enderror" id="job_type" name="job_type" required>
            @foreach(\App\Models\MechanicJob::jobTypeOptions() as $opt)
                <option value="{{ $opt }}" {{ old('job_type', $r->job_type ?? 'Internal') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
        </select>
        @error('job_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label for="mechanic" class="form-label">Mechanic</label>
        <input type="text" class="form-control @error('mechanic') is-invalid @enderror" id="mechanic" name="mechanic"
               value="{{ old('mechanic', $r->mechanic ?? '') }}" placeholder="e.g. Jefferson">
        @error('mechanic')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label for="category" class="form-label">Category (External)</label>
        <select class="form-select @error('category') is-invalid @enderror" id="category" name="category">
            <option value="">—</option>
            @foreach(\App\Models\MechanicJob::categoryOptions() as $opt)
                <option value="{{ $opt }}" {{ old('category', $r->category ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
        </select>
        @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="year_model" class="form-label">Year / Model</label>
        <input type="text" class="form-control @error('year_model') is-invalid @enderror" id="year_model" name="year_model"
               value="{{ old('year_model', $r->year_model ?? '') }}">
        @error('year_model')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="plate_number" class="form-label">Plate Number</label>
        <input type="text" class="form-control @error('plate_number') is-invalid @enderror" id="plate_number" name="plate_number"
               value="{{ old('plate_number', $r->plate_number ?? '') }}">
        @error('plate_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="status" class="form-label">Status</label>
        <input type="text" class="form-control @error('status') is-invalid @enderror" id="status" name="status" list="mech_status_options"
               value="{{ old('status', $r->status ?? 'Complete') }}">
        <datalist id="mech_status_options">
            @foreach(\App\Models\MechanicJob::statusOptions() as $opt)
                <option value="{{ $opt }}"></option>
            @endforeach
        </datalist>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="endorse" class="form-label">Endorse</label>
        <input type="text" class="form-control @error('endorse') is-invalid @enderror" id="endorse" name="endorse"
               value="{{ old('endorse', $r->endorse ?? '') }}">
        @error('endorse')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="parts_cost" class="form-label">Parts / Job Cost (₱)</label>
        <input type="number" step="0.01" min="0" class="form-control @error('parts_cost') is-invalid @enderror" id="parts_cost" name="parts_cost"
               value="{{ old('parts_cost', $r->parts_cost ?? '') }}">
        @error('parts_cost')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label for="description" class="form-label">Description / Item</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2">{{ old('description', $r->description ?? '') }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="labor" class="form-label">Labor / Work Done</label>
        <textarea class="form-control @error('labor') is-invalid @enderror" id="labor" name="labor" rows="2">{{ old('labor', $r->labor ?? '') }}</textarea>
        @error('labor')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="parts" class="form-label">Parts</label>
        <textarea class="form-control @error('parts') is-invalid @enderror" id="parts" name="parts" rows="2">{{ old('parts', $r->parts ?? '') }}</textarea>
        @error('parts')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label for="unit_label" class="form-label">Unit Label (optional)</label>
        <input type="text" class="form-control @error('unit_label') is-invalid @enderror" id="unit_label" name="unit_label"
               value="{{ old('unit_label', $r->unit_label ?? '') }}" placeholder="Raw unit text for external jobs">
        @error('unit_label')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Job</button>
        <a href="{{ route('mechanic-tracker.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</div>
