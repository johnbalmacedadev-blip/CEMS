@extends('layouts.app')

@section('title', 'Edit Client Follow Up - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-user-edit me-2"></i>Edit Client Follow Up
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('client-follow-up-list.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to List
            </a>
        </div>
    </div>

    <form action="{{ route('client-follow-up-list.update', $client_follow_up_list) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Initial Inquiry Details -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-inbox me-2"></i>Initial Inquiry Details
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="date_of_first_inquiry" class="form-label">Date of First Inquiry</label>
                        <input type="date" class="form-control @error('date_of_first_inquiry') is-invalid @enderror" id="date_of_first_inquiry" name="date_of_first_inquiry" value="{{ old('date_of_first_inquiry', $client_follow_up_list->date_of_first_inquiry?->format('Y-m-d')) }}">
                        @error('date_of_first_inquiry')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="application" class="form-label">Application</label>
                        <select class="form-select @error('application') is-invalid @enderror" id="application" name="application">
                            <option value="">— Select —</option>
                            @foreach(\App\Models\ClientFollowUp::applicationOptions() as $opt)
                                <option value="{{ $opt }}" {{ old('application', $client_follow_up_list->application) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                        @error('application')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="client_name" class="form-label">Customer Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('client_name') is-invalid @enderror" id="client_name" name="client_name" value="{{ old('client_name', $client_follow_up_list->client_name) }}" required>
                        @error('client_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="contact_number" class="form-label">Customer Phone</label>
                        <input type="text" class="form-control @error('contact_number') is-invalid @enderror" id="contact_number" name="contact_number" value="{{ old('contact_number', $client_follow_up_list->contact_number) }}">
                        @error('contact_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $client_follow_up_list->email) }}">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="vehicle_id" class="form-label">Link to Vehicle (optional)</label>
                        <select class="form-select @error('vehicle_id') is-invalid @enderror" id="vehicle_id" name="vehicle_id">
                            <option value="">— Optional —</option>
                            @foreach($vehicles as $v)
                                <option value="{{ $v->id }}" {{ old('vehicle_id', $client_follow_up_list->vehicle_id) == $v->id ? 'selected' : '' }}>{{ $v->full_name }} @if($v->plate_number)({{ $v->plate_number }})@endif</option>
                            @endforeach
                        </select>
                        @error('vehicle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="unit_inquired" class="form-label">Unit Inquired</label>
                        <input type="text" class="form-control @error('unit_inquired') is-invalid @enderror" id="unit_inquired" name="unit_inquired" value="{{ old('unit_inquired', $client_follow_up_list->unit_inquired) }}">
                        @error('unit_inquired')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="about_what" class="form-label">About What</label>
                        <input type="text" class="form-control @error('about_what') is-invalid @enderror" id="about_what" name="about_what" value="{{ old('about_what', $client_follow_up_list->about_what) }}">
                        @error('about_what')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes', $client_follow_up_list->notes) }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- 1st Follow Up -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <i class="fas fa-phone-alt me-2"></i>1st Follow Up
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="sales_exec_1" class="form-label">Sales Exec Who Followed Up</label>
                        <input type="text" class="form-control" id="sales_exec_1" name="sales_exec_1" value="{{ old('sales_exec_1', $client_follow_up_list->sales_exec_1) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_followed_up_1" class="form-label">Date Followed Up</label>
                        <input type="date" class="form-control" id="date_followed_up_1" name="date_followed_up_1" value="{{ old('date_followed_up_1', $client_follow_up_list->date_followed_up_1?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="outcome_1" class="form-label">Outcome of Follow Up</label>
                        <input type="text" class="form-control" id="outcome_1" name="outcome_1" value="{{ old('outcome_1', $client_follow_up_list->outcome_1) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="notes_1" class="form-label">Notes</label>
                        <input type="text" class="form-control" id="notes_1" name="notes_1" value="{{ old('notes_1', $client_follow_up_list->notes_1) }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- 2nd Follow Up -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <i class="fas fa-phone-alt me-2"></i>2nd Follow Up
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="sales_exec_2" class="form-label">Sales Exec Who Followed Up</label>
                        <input type="text" class="form-control" id="sales_exec_2" name="sales_exec_2" value="{{ old('sales_exec_2', $client_follow_up_list->sales_exec_2) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_followed_up_2" class="form-label">Date Followed Up</label>
                        <input type="date" class="form-control" id="date_followed_up_2" name="date_followed_up_2" value="{{ old('date_followed_up_2', $client_follow_up_list->date_followed_up_2?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="outcome_2" class="form-label">Outcome of Follow Up</label>
                        <input type="text" class="form-control" id="outcome_2" name="outcome_2" value="{{ old('outcome_2', $client_follow_up_list->outcome_2) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="notes_2" class="form-label">Notes</label>
                        <input type="text" class="form-control" id="notes_2" name="notes_2" value="{{ old('notes_2', $client_follow_up_list->notes_2) }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- 3rd Follow Up -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <i class="fas fa-phone-alt me-2"></i>3rd Follow Up
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="sales_exec_3" class="form-label">Sales Exec Who Followed Up</label>
                        <input type="text" class="form-control" id="sales_exec_3" name="sales_exec_3" value="{{ old('sales_exec_3', $client_follow_up_list->sales_exec_3) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_followed_up_3" class="form-label">Date Followed Up</label>
                        <input type="date" class="form-control" id="date_followed_up_3" name="date_followed_up_3" value="{{ old('date_followed_up_3', $client_follow_up_list->date_followed_up_3?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="outcome_3" class="form-label">Outcome of Follow Up</label>
                        <input type="text" class="form-control" id="outcome_3" name="outcome_3" value="{{ old('outcome_3', $client_follow_up_list->outcome_3) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="notes_3" class="form-label">Notes</label>
                        <input type="text" class="form-control" id="notes_3" name="notes_3" value="{{ old('notes_3', $client_follow_up_list->notes_3) }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- 4th Follow Up -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                <i class="fas fa-phone-alt me-2"></i>4th Follow Up
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="sales_exec_4" class="form-label">Sales Exec Who Followed Up</label>
                        <input type="text" class="form-control" id="sales_exec_4" name="sales_exec_4" value="{{ old('sales_exec_4', $client_follow_up_list->sales_exec_4) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_followed_up_4" class="form-label">Date Followed Up</label>
                        <input type="date" class="form-control" id="date_followed_up_4" name="date_followed_up_4" value="{{ old('date_followed_up_4', $client_follow_up_list->date_followed_up_4?->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="outcome_4" class="form-label">Outcome of Follow Up</label>
                        <input type="text" class="form-control" id="outcome_4" name="outcome_4" value="{{ old('outcome_4', $client_follow_up_list->outcome_4) }}">
                    </div>
                    <div class="col-md-3">
                        <label for="notes_4" class="form-label">Notes</label>
                        <input type="text" class="form-control" id="notes_4" name="notes_4" value="{{ old('notes_4', $client_follow_up_list->notes_4) }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Status & Actions -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            @foreach(\App\Models\ClientFollowUp::statusOptions() as $opt)
                                <option value="{{ $opt }}" {{ old('status', $client_follow_up_list->status) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-8 text-end">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Update</button>
                        <a href="{{ route('client-follow-up-list.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
