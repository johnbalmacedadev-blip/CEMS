@extends('layouts.app')

@section('title', 'Create Agent - Agent BOLO')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Create Agent</h1>
        <a href="{{ route('agent-bolo.index') }}" class="btn btn-outline-secondary">Back to Agent BOLO</a>
    </div>

    <div class="card">
        <div class="card-header">Agent information</div>
        <div class="card-body">
            <form action="{{ route('agent-bolo.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="sales_executive" class="form-label">Sales Executive</label>
                        <input type="text" class="form-control" id="sales_executive" name="sales_executive" value="{{ old('sales_executive') }}" placeholder="e.g. Evel, Chess">
                    </div>
                    <div class="col-md-6">
                        <label for="name" class="form-label">Sales Agent <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="contact_number" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="contact_number" name="contact_number" value="{{ old('contact_number') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                    </div>
                    <div class="col-12">
                        <label for="facebook_profile_link" class="form-label">Facebook Profile Link</label>
                        <input type="url" class="form-control @error('facebook_profile_link') is-invalid @enderror" id="facebook_profile_link" name="facebook_profile_link" value="{{ old('facebook_profile_link') }}" placeholder="https://www.facebook.com/...">
                        @error('facebook_profile_link')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="facebook_page_link" class="form-label">Facebook Page Link</label>
                        <input type="url" class="form-control @error('facebook_page_link') is-invalid @enderror" id="facebook_page_link" name="facebook_page_link" value="{{ old('facebook_page_link') }}" placeholder="https://www.facebook.com/profile.php?id=...">
                        @error('facebook_page_link')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="signed_bolo" class="form-label">Signed BOLO</label>
                        <input type="text" class="form-control" id="signed_bolo" name="signed_bolo" value="{{ old('signed_bolo') }}" placeholder="e.g. Saved in G Drive Folder, Saved in C Drive folder, Quit">
                    </div>
                    <div class="col-12">
                        <label for="one_valid_id" class="form-label">1 Valid ID</label>
                        <input type="text" class="form-control" id="one_valid_id" name="one_valid_id" value="{{ old('one_valid_id') }}" placeholder="e.g. Saved in G Drive Folder, Saved in C Drive Folder">
                    </div>
                    <div class="col-md-6">
                        <label for="joined_sales_associate_gc" class="form-label">Joined Sales Associate GC</label>
                        <input type="date" class="form-control" id="joined_sales_associate_gc" name="joined_sales_associate_gc" value="{{ old('joined_sales_associate_gc') }}">
                    </div>
                    <div class="col-12">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Save Agent</button>
                        <a href="{{ route('agent-bolo.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
