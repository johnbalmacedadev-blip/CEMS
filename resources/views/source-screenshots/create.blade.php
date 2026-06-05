@extends('layouts.app')

@section('title', 'Add Source Screenshot - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-camera me-2"></i>Add Source Screenshot
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('source-screenshots.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Source Screenshots
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('source-screenshots.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-3">
                    <div class="col-md-8">
                        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="screenshot_date" class="form-label">Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('screenshot_date') is-invalid @enderror" id="screenshot_date" name="screenshot_date" value="{{ old('screenshot_date', date('Y-m-d')) }}" required>
                        @error('screenshot_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="2" placeholder="Optional">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="category" class="form-label">Category</label>
                        <input type="text" class="form-control @error('category') is-invalid @enderror" id="category" name="category" value="{{ old('category') }}" placeholder="e.g. Expense, Transfer">
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-8">
                        <label for="link_url" class="form-label">Link URL</label>
                        <input type="url" class="form-control @error('link_url') is-invalid @enderror" id="link_url" name="link_url" value="{{ old('link_url') }}" placeholder="https://...">
                        @error('link_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <label for="screenshot_file" class="form-label">Upload screenshot (image)</label>
                        <input type="file" class="form-control @error('screenshot_file') is-invalid @enderror" id="screenshot_file" name="screenshot_file" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                        <small class="text-muted">Optional. Max 10MB. JPEG, PNG, GIF, WebP.</small>
                        @error('screenshot_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save</button>
                        <a href="{{ route('source-screenshots.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
