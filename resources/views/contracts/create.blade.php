@extends('layouts.app')

@section('title', 'Add Contract - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-file-contract me-2"></i>Add Contract
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to List
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('contracts.store') }}" method="POST" enctype="multipart/form-data" id="contractForm">
                @csrf
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Contract for <span class="text-danger">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="linked_to" id="linked_vehicle" value="vehicle" {{ old('linked_to', 'vehicle') === 'vehicle' ? 'checked' : '' }}>
                                <label class="form-check-label" for="linked_vehicle">Vehicle</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="linked_to" id="linked_employee" value="employee" {{ old('linked_to') === 'employee' ? 'checked' : '' }}>
                                <label class="form-check-label" for="linked_employee">Employee</label>
                            </div>
                        </div>
                    </div>

                    <div class="col-12" id="vehicle_block">
                        <label for="vehicle_search" class="form-label">Search & select vehicle <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('vehicle_id') is-invalid @enderror" id="vehicle_search" placeholder="Type plate number, make, model or year..." autocomplete="off" value="{{ old('vehicle_display') }}">
                        <input type="hidden" name="vehicle_id" id="vehicle_id" value="{{ old('vehicle_id') }}">
                        <div id="vehicle_results" class="list-group mt-2" style="display: none; max-height: 220px; overflow-y: auto;"></div>
                        @error('vehicle_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Start typing to search cars.</small>
                    </div>

                    <div class="col-12" id="employee_block" style="display: none;">
                        <label for="employee_id" class="form-label">Select employee <span class="text-danger">*</span></label>
                        <select class="form-select @error('employee_id') is-invalid @enderror" id="employee_id" name="employee_id">
                            <option value="">— Select employee —</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->full_name }}</option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-8">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required placeholder="e.g. Employment Contract - Juan Dela Cruz">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="contract_type" class="form-label">Type <span class="text-danger">*</span></label>
                        <select class="form-select @error('contract_type') is-invalid @enderror" id="contract_type" name="contract_type" required>
                            @foreach(\App\Models\Contract::typeOptions() as $opt)
                                <option value="{{ $opt }}" {{ old('contract_type', 'Other') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                        @error('contract_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="party_name" class="form-label">Party / Counterparty</label>
                        <input type="text" class="form-control @error('party_name') is-invalid @enderror" id="party_name" name="party_name" value="{{ old('party_name') }}" placeholder="Name or company">
                        @error('party_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Start Date</label>
                        <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date') }}">
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">End Date</label>
                        <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}">
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2" placeholder="Brief description">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="file" class="form-label">Attach File</label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <small class="text-muted">PDF, DOC, DOCX, JPG, PNG. Max 10MB.</small>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            @foreach(\App\Models\Contract::statusOptions() as $opt)
                                <option value="{{ $opt }}" {{ old('status', 'Active') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2" placeholder="Optional">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save</button>
                        <a href="{{ route('contracts.index') }}" class="btn btn-outline-secondary">Cancel</a>
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
    const linkedVehicle = document.getElementById('linked_vehicle');
    const linkedEmployee = document.getElementById('linked_employee');
    const vehicleBlock = document.getElementById('vehicle_block');
    const employeeBlock = document.getElementById('employee_block');
    const vehicleSearch = document.getElementById('vehicle_search');
    const vehicleId = document.getElementById('vehicle_id');
    const vehicleResults = document.getElementById('vehicle_results');
    const employeeSelect = document.getElementById('employee_id');

    function toggleBlocks() {
        const isVehicle = linkedVehicle.checked;
        vehicleBlock.style.display = isVehicle ? 'block' : 'none';
        employeeBlock.style.display = isVehicle ? 'none' : 'block';
        if (isVehicle) {
            employeeSelect.removeAttribute('required');
            employeeSelect.value = '';
        } else {
            vehicleId.value = '';
            vehicleSearch.value = '';
            vehicleResults.style.display = 'none';
            employeeSelect.setAttribute('required', 'required');
        }
    }
    linkedVehicle.addEventListener('change', toggleBlocks);
    linkedEmployee.addEventListener('change', toggleBlocks);
    toggleBlocks();

    let searchTimeout;
    vehicleSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const q = this.value.trim();
        if (q.length < 2) {
            vehicleResults.style.display = 'none';
            vehicleResults.innerHTML = '';
            return;
        }
        searchTimeout = setTimeout(function() {
            fetch('{{ route("contracts.vehicles.search") }}?q=' + encodeURIComponent(q))
                .then(r => r.json())
                .then(data => {
                    vehicleResults.innerHTML = '';
                    if (data.length === 0) {
                        vehicleResults.innerHTML = '<div class="list-group-item text-muted">No vehicles found.</div>';
                    } else {
                        data.forEach(function(v) {
                            const text = (v.plate_number ? v.plate_number + ' — ' : '') + (v.full_name || 'Vehicle #' + v.id);
                            const item = document.createElement('a');
                            item.href = '#';
                            item.className = 'list-group-item list-group-item-action';
                            item.textContent = text;
                            item.addEventListener('click', function(e) {
                                e.preventDefault();
                                vehicleId.value = v.id;
                                vehicleSearch.value = text;
                                vehicleResults.style.display = 'none';
                                vehicleResults.innerHTML = '';
                            });
                            vehicleResults.appendChild(item);
                        });
                    }
                    vehicleResults.style.display = 'block';
                })
                .catch(() => {
                    vehicleResults.innerHTML = '<div class="list-group-item text-muted">Search failed.</div>';
                    vehicleResults.style.display = 'block';
                });
        }, 300);
    });

    vehicleSearch.addEventListener('focus', function() {
        if (this.value.trim().length >= 2 && vehicleResults.children.length) vehicleResults.style.display = 'block';
    });
    document.addEventListener('click', function(e) {
        if (!vehicleSearch.contains(e.target) && !vehicleResults.contains(e.target)) vehicleResults.style.display = 'none';
    });
});
</script>
@endsection
