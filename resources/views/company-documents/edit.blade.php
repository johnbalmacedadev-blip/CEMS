@extends('layouts.app')

@section('title', 'Edit – ' . $typeLabel . ' - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-file-alt me-2"></i>Edit {{ $typeLabel }}
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route($routePrefix . '.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to {{ $typeLabel }}
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route($routePrefix . '.update', $document) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-12">
                        <label for="title" class="form-label">Title (optional)</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $document->title) }}" placeholder="e.g. January 2026 BOLO">
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="file" class="form-label">Upload file</label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif">
                        @if($document->isFile())
                            <div class="form-text">Current: <a href="{{ $document->display_url }}" target="_blank" rel="noopener">View file</a>. Upload a new file to replace.</div>
                        @else
                            <div class="form-text">Max 20MB. Leave empty to keep the existing link.</div>
                        @endif
                        @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="link_url" class="form-label">Or paste a link</label>
                        <input type="url" class="form-control @error('link_url') is-invalid @enderror" id="link_url" name="link_url" value="{{ old('link_url', $document->link_url) }}" placeholder="https://...">
                        @if($document->isLink())
                            <div class="form-text">Current link. Change to update.</div>
                        @endif
                        @error('link_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Update</button>
                        <a href="{{ route($routePrefix . '.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
