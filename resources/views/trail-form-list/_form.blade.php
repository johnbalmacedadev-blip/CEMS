@php
    $client = $client ?? null;
    $isEdit = isset($client);
@endphp

<div class="row g-3">
    <div class="col-md-4">
        <label for="inquiry_date" class="form-label">Inquiry date</label>
        <input type="date" class="form-control @error('inquiry_date') is-invalid @enderror" id="inquiry_date" name="inquiry_date"
            value="{{ old('inquiry_date', optional($client?->inquiry_date)->format('Y-m-d') ?? date('Y-m-d')) }}">
        @error('inquiry_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="status" class="form-label">Client status <span class="text-danger">*</span></label>
        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
            @foreach(\App\Models\TrailFormClient::statusOptions() as $opt)
                <option value="{{ $opt }}" {{ old('status', $client->status ?? 'Inquiring') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="inquiry_source" class="form-label">Where they inquired</label>
        <select class="form-select @error('inquiry_source') is-invalid @enderror" id="inquiry_source" name="inquiry_source">
            <option value="">— Select source —</option>
            @foreach(\App\Models\TrailFormClient::inquirySourceOptions() as $opt)
                <option value="{{ $opt }}" {{ old('inquiry_source', $client->inquiry_source ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
        </select>
        @error('inquiry_source')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-6">
        <label for="client_name" class="form-label">Client name <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('client_name') is-invalid @enderror" id="client_name" name="client_name"
            value="{{ old('client_name', $client->client_name ?? '') }}" required>
        @error('client_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="contact_number" class="form-label">Contact number</label>
        <input type="text" class="form-control @error('contact_number') is-invalid @enderror" id="contact_number" name="contact_number"
            value="{{ old('contact_number', $client->contact_number ?? '') }}" placeholder="09XXXXXXXXX">
        @error('contact_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
            value="{{ old('email', $client->email ?? '') }}">
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="vehicle_type" class="form-label">Vehicle type inquired</label>
        <select class="form-select @error('vehicle_type') is-invalid @enderror" id="vehicle_type" name="vehicle_type">
            <option value="">— Select type —</option>
            @foreach(\App\Models\TrailFormClient::vehicleTypeOptions() as $opt)
                <option value="{{ $opt }}" {{ old('vehicle_type', $client->vehicle_type ?? '') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
        </select>
        @error('vehicle_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-8">
        <label for="vehicle_interest" class="form-label">Specific unit / vehicle inquired</label>
        <input type="text" class="form-control @error('vehicle_interest') is-invalid @enderror" id="vehicle_interest" name="vehicle_interest"
            value="{{ old('vehicle_interest', $client->vehicle_interest ?? '') }}" placeholder="e.g. Toyota Fortuner 2020, White">
        @error('vehicle_interest')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="notes" class="form-label">Notes</label>
        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3"
            placeholder="Additional details about the inquiry or reservation...">{{ old('notes', $client->notes ?? '') }}</textarea>
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i>{{ $isEdit ? 'Update Client' : 'Save Client' }}
        </button>
        <a href="{{ route('trail-form-list.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</div>
