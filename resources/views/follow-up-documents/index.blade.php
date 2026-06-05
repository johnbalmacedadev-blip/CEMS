@extends('layouts.app')

@section('title', 'Follow Up Documents - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-clipboard-check me-2"></i>Follow Up Documents
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
            <a href="{{ route('follow-up-documents.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add Document
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <p class="text-muted mb-4">Track documents that need follow-up. Optionally link to a unit (vehicle).</p>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('follow-up-documents.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small">Status</label>
                    <select class="form-select form-select-sm" name="status">
                        <option value="">All</option>
                        @foreach(\App\Models\FollowUpDocument::statusOptions() as $opt)
                            <option value="{{ $opt }}" {{ request('status') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Priority</label>
                    <select class="form-select form-select-sm" name="priority">
                        <option value="">All</option>
                        @foreach(\App\Models\FollowUpDocument::priorityOptions() as $opt)
                            <option value="{{ $opt }}" {{ request('priority') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Due From</label>
                    <input type="date" class="form-control form-control-sm" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Due To</label>
                    <input type="date" class="form-control form-control-sm" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Search</label>
                    <input type="text" class="form-control form-control-sm" name="search" placeholder="Title, description..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm me-2"><i class="fas fa-search me-1"></i>Filter</button>
                    <a href="{{ route('follow-up-documents.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body p-0">
            @if($documents->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Unit</th>
                                <th>Due Date</th>
                                <th>Priority</th>
                                <th>Status</th>
                                <th>Description</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $doc)
                                <tr>
                                    <td><strong>{{ $doc->title }}</strong></td>
                                    <td>
                                        @if($doc->vehicle)
                                            <a href="{{ route('vehicles.show', $doc->vehicle) }}">{{ $doc->vehicle->full_name }}</a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $doc->due_date ? $doc->due_date->format('M d, Y') : '—' }}</td>
                                    <td>
                                        @if($doc->priority === 'High')
                                            <span class="badge bg-danger">{{ $doc->priority }}</span>
                                        @elseif($doc->priority === 'Medium')
                                            <span class="badge bg-warning text-dark">{{ $doc->priority }}</span>
                                        @elseif($doc->priority === 'Low')
                                            <span class="badge bg-secondary">{{ $doc->priority }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($doc->status === 'Completed')
                                            <span class="badge bg-success">{{ $doc->status }}</span>
                                        @elseif($doc->status === 'In Progress')
                                            <span class="badge bg-info">{{ $doc->status }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ $doc->status }}</span>
                                        @endif
                                    </td>
                                    <td><span class="text-muted small">{{ Str::limit($doc->description, 40) ?: '—' }}</span></td>
                                    <td class="text-center">
                                        <a href="{{ route('follow-up-documents.edit', $doc) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('follow-up-documents.destroy', $doc) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this follow up document?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3 pb-3">
                    {{ $documents->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-check fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No follow up documents yet</h5>
                    <p class="text-muted mb-3">Add documents that need follow-up (e.g. transfer papers, OR/CR).</p>
                    <a href="{{ route('follow-up-documents.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Document</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
