@extends('layouts.app')

@section('title', $typeLabel . ' - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-file-alt me-2"></i>{{ $typeLabel }}
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
            <a href="{{ route($routePrefix . '.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add File or Link
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <p class="text-muted mb-4">Upload a file or add a link. Each entry can be either a file or a URL.</p>

    @if($documents->isNotEmpty())
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>View</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $doc)
                                <tr>
                                    <td>{{ $doc->title ?: ($doc->isFile() ? 'Uploaded file' : 'Link') }}</td>
                                    <td>
                                        @if($doc->isFile())
                                            <span class="badge bg-secondary">File</span>
                                        @else
                                            <span class="badge bg-info">Link</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ $doc->display_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-external-link-alt me-1"></i>Open
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route($routePrefix . '.edit', $doc) }}" class="btn btn-sm btn-outline-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route($routePrefix . '.destroy', $doc) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this item?');">
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
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
                <h5 class="text-muted mb-3">No items yet</h5>
                <p class="text-muted mb-4">Add a file upload or a link to get started.</p>
                <a href="{{ route($routePrefix . '.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add File or Link</a>
            </div>
        </div>
    @endif
</div>
@endsection
