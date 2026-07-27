@extends('layouts.app')

@section('title', 'Add Insurance Record - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-shield-alt me-2"></i>Add Insurance Record
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('insurance-tracker.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to List
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('insurance-tracker.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="sales" class="form-label">Sales</label>
                        <input type="text" class="form-control @error('sales') is-invalid @enderror" id="sales" name="sales" value="{{ old('sales') }}" placeholder="e.g. THYRA">
                        @error('sales')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="vehicle_id" class="form-label">Link to Vehicle (optional)</label>
                        <select class="form-select @error('vehicle_id') is-invalid @enderror" id="vehicle_id" name="vehicle_id">
                            <option value="">— Optional —</option>
                            @foreach($vehicles as $v)
                                @php
                                    $makeName = is_string($v->make ?? null) ? $v->make : (isset($v->make->name) ? $v->make->name : '');
                                    $modelName = is_string($v->model ?? null) ? $v->model : (isset($v->vehicleModel->name) ? $v->vehicleModel->name : '');
                                @endphp
                                <option value="{{ $v->id }}"
                                    data-year="{{ $v->year ?? '' }}"
                                    data-make="{{ e($makeName) }}"
                                    data-model="{{ e($modelName) }}"
                                    data-plate="{{ $v->plate_number ?? '' }}"
                                    {{ old('vehicle_id') == $v->id ? 'selected' : '' }}>{{ $v->full_name }} @if($v->plate_number)({{ $v->plate_number }})@endif</option>
                            @endforeach
                        </select>
                        @error('vehicle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label for="year" class="form-label">Year</label>
                        <input type="text" class="form-control @error('year') is-invalid @enderror" id="year" name="year" value="{{ old('year') }}" placeholder="e.g. 2023">
                        @error('year')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label for="make" class="form-label">Make</label>
                        <input type="text" class="form-control @error('make') is-invalid @enderror" id="make" name="make" value="{{ old('make') }}" placeholder="e.g. TOYOTA">
                        @error('make')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label for="model" class="form-label">Model</label>
                        <input type="text" class="form-control @error('model') is-invalid @enderror" id="model" name="model" value="{{ old('model') }}" placeholder="e.g. VELOZ">
                        @error('model')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label for="number" class="form-label">Number</label>
                        <input type="text" class="form-control @error('number') is-invalid @enderror" id="number" name="number" value="{{ old('number') }}" placeholder="e.g. NIF2150">
                        @error('number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="transaction" class="form-label">Transaction</label>
                        <input type="text" class="form-control @error('transaction') is-invalid @enderror" id="transaction" name="transaction" value="{{ old('transaction') }}" placeholder="e.g. CASH">
                        @error('transaction')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-8">
                        <label for="source" class="form-label">Source</label>
                        <input type="text" class="form-control @error('source') is-invalid @enderror" id="source" name="source" value="{{ old('source') }}" placeholder="e.g. CAR EMPIRE FB PAGE FLAGSHIP">
                        @error('source')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="reservation_date" class="form-label">Reservation</label>
                        <input type="date" class="form-control @error('reservation_date') is-invalid @enderror" id="reservation_date" name="reservation_date" value="{{ old('reservation_date') }}">
                        @error('reservation_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="release_date" class="form-label">Release Date</label>
                        <input type="date" class="form-control @error('release_date') is-invalid @enderror" id="release_date" name="release_date" value="{{ old('release_date') }}">
                        @error('release_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="amount" class="form-label">Amount (P)</label>
                        <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" step="0.01" min="0" value="{{ old('amount') }}" placeholder="e.g. 28386">
                        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save</button>
                        <a href="{{ route('insurance-tracker.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var vehicleSelect = document.getElementById('vehicle_id');
    var yearInput = document.getElementById('year');
    var makeInput = document.getElementById('make');
    var modelInput = document.getElementById('model');
    var numberInput = document.getElementById('number');

    function fillFromVehicle() {
        var opt = vehicleSelect.options[vehicleSelect.selectedIndex];
        if (opt && opt.value) {
            yearInput.value = opt.getAttribute('data-year') || '';
            makeInput.value = opt.getAttribute('data-make') || '';
            modelInput.value = opt.getAttribute('data-model') || '';
            numberInput.value = opt.getAttribute('data-plate') || '';
        } else {
            yearInput.value = '';
            makeInput.value = '';
            modelInput.value = '';
            numberInput.value = '';
        }
    }

    vehicleSelect.addEventListener('change', fillFromVehicle);
    if (vehicleSelect.value) fillFromVehicle();
});
</script>
@endsection
