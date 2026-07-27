@extends('layouts.app')

@section('title', 'Add Driver Activity - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-id-card me-2"></i>Add Driver Activity
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('driver-activity-tracker.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Driver Activity Tracker
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('driver-activity-tracker.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="employee_id" class="form-label">Driver</label>
                        <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id">
                            <option value="">— Select driver (optional) —</option>
                            @foreach($drivers as $e)
                                <option value="{{ $e->id }}" {{ old('employee_id') == $e->id ? 'selected' : '' }}>{{ $e->full_name }}@if($e->role) — {{ $e->role }}@endif</option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="vehicle_id" class="form-label">Vehicle</label>
                        <select class="form-select @error('vehicle_id') is-invalid @enderror" id="vehicle_id" name="vehicle_id">
                            <option value="">— Select vehicle (optional) —</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}" {{ old('vehicle_id', request('vehicle_id')) == $v->id ? 'selected' : '' }}>{{ $v->full_name }} @if($v->plate_number)({{ $v->plate_number }})@endif</option>
                            @endforeach
                        </select>
                        @error('vehicle_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="activity_date" class="form-label">Activity Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('activity_date') is-invalid @enderror" id="activity_date" name="activity_date" value="{{ old('activity_date', date('Y-m-d')) }}" required>
                        @error('activity_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="activity_type" class="form-label">Activity Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('activity_type') is-invalid @enderror" id="activity_type" name="activity_type" required>
                            @foreach(\App\Models\DriverActivity::activityTypeOptions() as $opt)
                                <option value="{{ $opt }}" {{ old('activity_type', 'Delivery') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                        @error('activity_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            @foreach(\App\Models\DriverActivity::statusOptions() as $opt)
                                <option value="{{ $opt }}" {{ old('status', 'Pending') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="destination" class="form-label">Destination</label>
                        <input type="text" class="form-control @error('destination') is-invalid @enderror" id="destination" name="destination" value="{{ old('destination') }}" placeholder="e.g. LTO, client address...">
                        @error('destination')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Optional notes...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Activity</button>
                        <a href="{{ route('driver-activity-tracker.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
