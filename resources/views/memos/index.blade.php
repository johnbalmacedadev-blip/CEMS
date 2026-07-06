@extends('layouts.app')

@section('title', 'Memos - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-sticky-note me-2"></i>Memos
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
            <a href="{{ route('memos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add Memo
            </a>
        </div>
    </div>

    @include('partials.flash-alert')

    <p class="text-muted mb-4">Create memos with text, attach files, or add links for your team.</p>

    @if($memos->isNotEmpty())
        <div class="row g-3">
            @foreach($memos as $memo)
                <div class="col-12 col-lg-6 col-xl-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0">{{ $memo->title ?: 'Untitled memo' }}</h5>
                                <small class="text-muted text-nowrap ms-2">{{ $memo->created_at->format('M j, Y') }}</small>
                            </div>

                            <div class="mb-2">
                                @if($memo->hasBody())
                                    <span class="badge bg-primary me-1">Text</span>
                                @endif
                                @if($memo->isFile())
                                    <span class="badge bg-secondary me-1">File</span>
                                @endif
                                @if($memo->isLink())
                                    <span class="badge bg-info">Link</span>
                                @endif
                            </div>

                            @if($memo->hasBody())
                                <p class="card-text text-muted flex-grow-1" style="white-space: pre-wrap;">{{ Str::limit($memo->body, 200) }}</p>
                            @endif

                            <div class="d-flex flex-wrap gap-2 mt-auto pt-2">
                                @if($memo->isFile())
                                    <a href="{{ $memo->display_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-file me-1"></i>Open file
                                    </a>
                                @endif
                                @if($memo->isLink())
                                    <a href="{{ $memo->link_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-external-link-alt me-1"></i>Open link
                                    </a>
                                @endif
                                <a href="{{ route('memos.edit', $memo) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('memos.destroy', $memo) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this memo?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-sticky-note fa-4x text-muted mb-3"></i>
                <h5 class="text-muted mb-3">No memos yet</h5>
                <p class="text-muted mb-4">Add a memo with text, a file upload, or a link.</p>
                <a href="{{ route('memos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Add Memo
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
