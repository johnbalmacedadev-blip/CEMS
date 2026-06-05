@extends('layouts.app')

@section('title', 'Source Screenshots - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-camera me-2"></i>Source Screenshots
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
            <a href="{{ route('source-screenshots.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add Screenshot
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <p class="text-muted mb-4">Track and store source screenshots for reference (expenses, transactions, etc.).</p>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('source-screenshots.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small">Category</label>
                    <input type="text" class="form-control form-control-sm" name="category" placeholder="Category" value="{{ request('category') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Date From</label>
                    <input type="date" class="form-control form-control-sm" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Date To</label>
                    <input type="date" class="form-control form-control-sm" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Search</label>
                    <input type="text" class="form-control form-control-sm" name="search" placeholder="Title, description..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm me-2"><i class="fas fa-search me-1"></i>Filter</button>
                    <a href="{{ route('source-screenshots.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    @if($screenshots->isNotEmpty())
        <div class="row g-4">
            @foreach($screenshots as $item)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card h-100 shadow-sm">
                        <div class="position-relative" style="height: 160px; background: #f0f0f0; overflow: hidden;">
                            @if($item->has_file)
                                <a href="{{ $item->file_url }}" target="_blank" rel="noopener" class="d-block h-100">
                                    <img src="{{ $item->file_url }}" alt="{{ $item->title }}" class="w-100 h-100" style="object-fit: cover;">
                                </a>
                            @elseif($item->link_url)
                                <a href="{{ $item->link_url }}" target="_blank" rel="noopener" class="d-flex align-items-center justify-content-center h-100 text-primary">
                                    <i class="fas fa-link fa-3x"></i>
                                </a>
                            @else
                                <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                    <i class="fas fa-image fa-3x opacity-50"></i>
                                </div>
                            @endif
                            @if($item->category)
                                <span class="position-absolute top-0 start-0 m-2 badge bg-dark">{{ $item->category }}</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <h6 class="card-title text-truncate" title="{{ $item->title }}">{{ $item->title }}</h6>
                            <p class="card-text small text-muted mb-1">{{ $item->screenshot_date->format('M d, Y') }}</p>
                            @if($item->description)
                                <p class="card-text small mb-0">{{ Str::limit($item->description, 50) }}</p>
                            @endif
                        </div>
                        <div class="card-footer bg-transparent border-top-0 pt-0 d-flex gap-1">
                            <a href="{{ route('source-screenshots.edit', $item) }}" class="btn btn-sm btn-outline-primary flex-grow-1"><i class="fas fa-edit me-1"></i>Edit</a>
                            <form action="{{ route('source-screenshots.destroy', $item) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this screenshot?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="d-flex justify-content-center mt-4">
            {{ $screenshots->links('pagination::bootstrap-4') }}
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No source screenshots yet</h5>
                <p class="text-muted mb-3">Add screenshots (upload image or add link) for expense or transaction reference.</p>
                <a href="{{ route('source-screenshots.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Screenshot</a>
            </div>
        </div>
    @endif
</div>
@endsection
