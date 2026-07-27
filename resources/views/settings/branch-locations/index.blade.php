@extends('layouts.app')

@section('title', 'Showroom - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-map-marker-alt me-2"></i>Showroom</h1>
        <a href="{{ route('settings') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Settings
        </a>
    </div>

    <p class="text-muted mb-4">Create and manage your list of showrooms used across the system.</p>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Add Showroom</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('settings.branch-locations.store') }}" method="POST" class="row g-3 align-items-end">
                @csrf
                <div class="col-md-4">
                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                        value="{{ old('name') }}" placeholder="e.g. Annex, Flagship" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label for="description" class="form-label">Description / Notes</label>
                    <input type="text" class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                        value="{{ old('description') }}" placeholder="Optional details">
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-2">
                    <label for="sort_order" class="form-label">Sort order</label>
                    <input type="number" class="form-control" id="sort_order" name="sort_order" min="0"
                        value="{{ old('sort_order', ($branches->max('sort_order') ?? 0) + 1) }}">
                </div>
                <div class="col-md-2">
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active_new" checked>
                        <label class="form-check-label" for="is_active_new">Active</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-save me-1"></i>Add
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Showroom List ({{ $branches->count() }})</h5>
        </div>
        <div class="card-body p-0">
            @if($branches->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th style="width: 90px;">Order</th>
                                <th style="width: 90px;">Status</th>
                                <th style="width: 140px;" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($branches as $index => $branch)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td class="fw-semibold">{{ $branch->name }}</td>
                                    <td class="text-muted">{{ $branch->description ?: '—' }}</td>
                                    <td>{{ $branch->sort_order }}</td>
                                    <td>
                                        @if($branch->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary" title="Edit"
                                            data-bs-toggle="modal" data-bs-target="#editBranchModal"
                                            data-id="{{ $branch->id }}"
                                            data-name="{{ $branch->name }}"
                                            data-description="{{ $branch->description }}"
                                            data-sort-order="{{ $branch->sort_order }}"
                                            data-is-active="{{ $branch->is_active ? '1' : '0' }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('settings.branch-locations.destroy', $branch) }}" method="POST" class="d-inline"
                                            onsubmit="return confirm('Delete this showroom?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-map-marker-alt fa-3x mb-3"></i>
                    <p class="mb-0">No showrooms yet. Add one using the form above.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="editBranchModal" tabindex="-1" aria-labelledby="editBranchModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editBranchForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editBranchModalLabel">Edit Showroom</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description / Notes</label>
                        <input type="text" class="form-control" id="edit_description" name="description">
                    </div>
                    <div class="mb-3">
                        <label for="edit_sort_order" class="form-label">Sort order</label>
                        <input type="number" class="form-control" id="edit_sort_order" name="sort_order" min="0">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="edit_is_active">
                        <label class="form-check-label" for="edit_is_active">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('editBranchModal')?.addEventListener('show.bs.modal', function (event) {
    const btn = event.relatedTarget;
    const form = document.getElementById('editBranchForm');
    const updateUrlTemplate = @json(route('settings.branch-locations.update', ['branch_location' => '__ID__']));
    form.action = updateUrlTemplate.replace('__ID__', btn.getAttribute('data-id'));
    document.getElementById('edit_name').value = btn.getAttribute('data-name') || '';
    document.getElementById('edit_description').value = btn.getAttribute('data-description') || '';
    document.getElementById('edit_sort_order').value = btn.getAttribute('data-sort-order') || '0';
    document.getElementById('edit_is_active').checked = btn.getAttribute('data-is-active') === '1';
});
</script>
@endsection
