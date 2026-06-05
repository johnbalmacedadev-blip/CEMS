@extends('layouts.app')

@section('title', $agent->name . ' - Agent BOLO - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-user-tie me-2"></i>{{ $agent->name }}
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('agent-bolo.edit', $agent) }}" class="btn btn-outline-warning me-2"><i class="fas fa-edit me-1"></i>Edit Agent</a>
            <a href="{{ route('agent-bolo.index') }}" class="btn btn-outline-secondary">Back to Agent BOLO</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">Agent information</div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label text-muted small mb-0">Sales Executive</label>
                    <p class="mb-0">{{ $agent->sales_executive ?? '—' }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small mb-0">Sales Agent</label>
                    <p class="mb-0">{{ $agent->name }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small mb-0">Phone Number</label>
                    <p class="mb-0">{{ $agent->contact_number ?? '—' }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small mb-0">Email</label>
                    <p class="mb-0">{{ $agent->email ?? '—' }}</p>
                </div>
                @if($agent->facebook_profile_link)
                <div class="col-12">
                    <label class="form-label text-muted small mb-0">Facebook Profile Link</label>
                    <p class="mb-0"><a href="{{ $agent->facebook_profile_link }}" target="_blank" rel="noopener">{{ $agent->facebook_profile_link }}</a></p>
                </div>
                @endif
                @if($agent->facebook_page_link)
                <div class="col-12">
                    <label class="form-label text-muted small mb-0">Facebook Page Link</label>
                    <p class="mb-0"><a href="{{ $agent->facebook_page_link }}" target="_blank" rel="noopener">{{ $agent->facebook_page_link }}</a></p>
                </div>
                @endif
                <div class="col-md-6">
                    <label class="form-label text-muted small mb-0">Signed BOLO</label>
                    <p class="mb-0">{{ $agent->signed_bolo ?? '—' }}</p>
                </div>
                <div class="col-md-6">
                    <label class="form-label text-muted small mb-0">1 Valid ID</label>
                    <p class="mb-0">{{ $agent->one_valid_id ?? '—' }}</p>
                </div>
                @if($agent->joined_sales_associate_gc)
                <div class="col-md-6">
                    <label class="form-label text-muted small mb-0">Joined Sales Associate GC</label>
                    <p class="mb-0">{{ $agent->joined_sales_associate_gc->format('M j, Y') }}</p>
                </div>
                @endif
                @if($agent->notes)
                <div class="col-12">
                    <label class="form-label text-muted small mb-0">Notes</label>
                    <p class="mb-0">{{ $agent->notes }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <span>Files &amp; links</span>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="collapse" data-bs-target="#addDocumentForm">
                <i class="fas fa-plus me-1"></i>Add file or link
            </button>
        </div>
        <div class="card-body">
            <div class="collapse mb-4" id="addDocumentForm">
                <form action="{{ route('agent-bolo.documents.store', $agent) }}" method="POST" enctype="multipart/form-data" class="border rounded p-3 bg-light">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="doc_title" class="form-label">Title (optional)</label>
                            <input type="text" class="form-control" id="doc_title" name="title" value="{{ old('title') }}" placeholder="e.g. Contract PDF">
                        </div>
                        <div class="col-12">
                            <label for="doc_file" class="form-label">Upload file</label>
                            <input type="file" class="form-control @error('file') is-invalid @enderror" id="doc_file" name="file" accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif">
                            <div class="form-text">Max 20MB. Leave empty if you provide a link.</div>
                            @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="doc_link_url" class="form-label">Or paste a link</label>
                            <input type="url" class="form-control @error('link_url') is-invalid @enderror" id="doc_link_url" name="link_url" value="{{ old('link_url') }}" placeholder="https://...">
                            @error('link_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-save me-1"></i>Add</button>
                        </div>
                    </div>
                </form>
            </div>

            @if($agent->documents->isNotEmpty())
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
                            @foreach($agent->documents as $doc)
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
                                        <a href="{{ $doc->display_url }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-primary">Open</a>
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('agent-bolo.documents.destroy', [$agent, $doc]) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this file/link?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted mb-0">No files or links yet. Click &quot;Add file or link&quot; above to add one.</p>
            @endif
        </div>
    </div>
</div>
@endsection
