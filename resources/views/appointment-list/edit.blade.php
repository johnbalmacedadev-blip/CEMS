@extends('layouts.app')

@section('title', 'Edit Appointment - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-calendar-alt me-2"></i>Edit Appointment
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('appointment-list.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to List
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('appointment-list.update', $appointment_list) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="date_added_to_schedule" class="form-label">Date Added to Schedule</label>
                        <input type="date" class="form-control @error('date_added_to_schedule') is-invalid @enderror" id="date_added_to_schedule" name="date_added_to_schedule" value="{{ old('date_added_to_schedule', $appointment_list->date_added_to_schedule?->format('Y-m-d')) }}">
                        @error('date_added_to_schedule')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="added_by" class="form-label">Added By?</label>
                        <input type="text" class="form-control @error('added_by') is-invalid @enderror" id="added_by" name="added_by" value="{{ old('added_by', $appointment_list->added_by) }}">
                        @error('added_by')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4"></div>
                    <div class="col-md-4">
                        <label for="customer_first_name" class="form-label">Customer First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('customer_first_name') is-invalid @enderror" id="customer_first_name" name="customer_first_name" value="{{ old('customer_first_name', $appointment_list->customer_first_name) }}" required>
                        @error('customer_first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="customer_last_name" class="form-label">Customer Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('customer_last_name') is-invalid @enderror" id="customer_last_name" name="customer_last_name" value="{{ old('customer_last_name', $appointment_list->customer_last_name) }}" required>
                        @error('customer_last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="customer_phone_number" class="form-label">Customer Phone Number</label>
                        <input type="text" class="form-control @error('customer_phone_number') is-invalid @enderror" id="customer_phone_number" name="customer_phone_number" value="{{ old('customer_phone_number', $appointment_list->customer_phone_number) }}">
                        @error('customer_phone_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="showroom" class="form-label">Showroom</label>
                        <input type="text" class="form-control @error('showroom') is-invalid @enderror" id="showroom" name="showroom" value="{{ old('showroom', $appointment_list->showroom) }}">
                        @error('showroom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="date_of_visit" class="form-label">Date of Visit</label>
                        <input type="date" class="form-control @error('date_of_visit') is-invalid @enderror" id="date_of_visit" name="date_of_visit" value="{{ old('date_of_visit', $appointment_list->date_of_visit?->format('Y-m-d')) }}">
                        @error('date_of_visit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="vehicle_id" class="form-label">Link to Vehicle (optional)</label>
                        <select class="form-select @error('vehicle_id') is-invalid @enderror" id="vehicle_id" name="vehicle_id">
                            <option value="">— Optional —</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}" {{ old('vehicle_id', $appointment_list->vehicle_id) == $v->id ? 'selected' : '' }}>{{ $v->full_name }} @if($v->plate_number)({{ $v->plate_number }})@endif</option>
                            @endforeach
                        </select>
                        @error('vehicle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="preferred_unit" class="form-label">Preferred Unit <small class="text-muted">(Be as specific as possible; include plate number if possible)</small></label>
                        <input type="text" class="form-control @error('preferred_unit') is-invalid @enderror" id="preferred_unit" name="preferred_unit" value="{{ old('preferred_unit', $appointment_list->preferred_unit) }}">
                        @error('preferred_unit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes', $appointment_list->notes) }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="sales_exec_who_assisted" class="form-label">Sales Exec Who Assisted</label>
                        <input type="text" class="form-control @error('sales_exec_who_assisted') is-invalid @enderror" id="sales_exec_who_assisted" name="sales_exec_who_assisted" value="{{ old('sales_exec_who_assisted', $appointment_list->sales_exec_who_assisted) }}">
                        @error('sales_exec_who_assisted')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="outcome" class="form-label">Outcome</label>
                        <input type="text" class="form-control @error('outcome') is-invalid @enderror" id="outcome" name="outcome" value="{{ old('outcome', $appointment_list->outcome) }}">
                        @error('outcome')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="notes_of_visit" class="form-label">Notes of Visit</label>
                        <textarea class="form-control @error('notes_of_visit') is-invalid @enderror" id="notes_of_visit" name="notes_of_visit" rows="3">{{ old('notes_of_visit', $appointment_list->notes_of_visit) }}</textarea>
                        @error('notes_of_visit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Update</button>
                        <a href="{{ route('appointment-list.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
