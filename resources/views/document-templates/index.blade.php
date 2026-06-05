@extends('layouts.app')

@section('title', 'Form Templates - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="row">
        <main class="col-12 px-md-4 main-content" id="mainContent">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-file-alt me-2"></i>Form Templates
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('document-templates.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Create Template
                    </a>
                    <a href="{{ route('settings') }}" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-arrow-left me-1"></i>Back to Settings
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($templates->count() > 0)
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Document Type</th>
                                        <th>Fields Count</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($templates as $template)
                                        <tr>
                                            <td><strong>{{ $template->name }}</strong></td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ str_replace('_', ' ', strtoupper($template->document_type)) }}
                                                </span>
                                            </td>
                                            <td>{{ count($template->form_fields ?? []) }} field(s)</td>
                                            <td>
                                                @if($template->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-secondary">Inactive</span>
                                                @endif
                                            </td>
                                            <td>{{ $template->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('document-templates.edit', $template) }}" 
                                                   class="btn btn-sm btn-outline-warning" 
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <form action="{{ route('document-templates.destroy', $template) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this template?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </button>
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
                        <h5 class="text-muted mb-3">No form templates found</h5>
                        <p class="text-muted mb-4">Create your first form template to get started.</p>
                        <a href="{{ route('document-templates.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Create Template
                        </a>
                    </div>
                </div>
            @endif
        </main>
    </div>
</div>
@endsection
