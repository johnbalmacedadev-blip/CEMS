@extends('layouts.app')

@section('title', 'Page Permissions - ' . $user->name . ' - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-key me-2"></i>Page Permissions: {{ $user->name }}</h1>
        <div>
            <a href="{{ route('settings.users.edit', $user) }}" class="btn btn-outline-secondary me-2">Edit User</a>
            <a href="{{ route('settings.users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Users
            </a>
        </div>
    </div>

    @if($user->isAdmin())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>Admins have full access to all pages. All permissions below are shown as granted.
        </div>
    @endif

    {{-- Legend --}}
    <div class="card mb-4 border-secondary">
        <div class="card-header bg-light">
            <strong><i class="fas fa-info-circle me-2"></i>Legend</strong>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6 col-lg-3">
                    <div class="d-flex align-items-start">
                        <span class="perm-legend-icon perm-legend-icon--yes me-2"><i class="fas fa-check"></i></span>
                        <div>
                            <strong>Checked</strong>
                            <p class="text-muted small mb-0">User can perform this action on the page.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="d-flex align-items-start">
                        <span class="perm-legend-icon perm-legend-icon--no me-2"><i class="fas fa-times"></i></span>
                        <div>
                            <strong>Unchecked</strong>
                            <p class="text-muted small mb-0">User cannot access or perform this action.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="d-flex align-items-start">
                        <span class="badge bg-secondary me-2 mt-1">View</span>
                        <div>
                            <strong>View</strong>
                            <p class="text-muted small mb-0">Open and read the page.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="d-flex align-items-start">
                        <span class="badge bg-secondary me-2 mt-1">Create</span>
                        <div>
                            <strong>Create / Save</strong>
                            <p class="text-muted small mb-0">Add new records or save new entries.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="d-flex align-items-start">
                        <span class="badge bg-secondary me-2 mt-1">Edit</span>
                        <div>
                            <strong>Edit / Update</strong>
                            <p class="text-muted small mb-0">Modify existing records.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="d-flex align-items-start">
                        <span class="badge bg-secondary me-2 mt-1">Delete</span>
                        <div>
                            <strong>Delete</strong>
                            <p class="text-muted small mb-0">Remove records from the system.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="d-flex align-items-start">
                        <span class="perm-legend-icon perm-legend-icon--page-yes me-2"><i class="fas fa-check-circle"></i></span>
                        <div>
                            <strong>Page accessible</strong>
                            <p class="text-muted small mb-0">At least <em>View</em> is enabled — user can open this feature.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="d-flex align-items-start">
                        <span class="perm-legend-icon perm-legend-icon--page-no me-2"><i class="fas fa-ban"></i></span>
                        <div>
                            <strong>Page blocked</strong>
                            <p class="text-muted small mb-0">No permissions — user cannot open this feature.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <p class="text-muted mb-3">
                Features are grouped by home page category (e.g. Car Reports, Staff Reports). Check the boxes to grant access; leave unchecked to deny.
                @if(!$user->isAdmin())
                    Changes take effect after you click <strong>Save Permissions</strong>.
                @endif
            </p>
            <form action="{{ route('settings.users.permissions.update', $user) }}" method="POST" id="permissionsForm">
                @csrf
                @method('PUT')
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle permissions-table">
                        <thead class="table-dark">
                            <tr>
                                <th style="min-width: 220px;">Feature / Page</th>
                                <th class="text-center" style="width: 110px;">Accessible</th>
                                <th class="text-center" style="width: 90px;">View</th>
                                <th class="text-center" style="width: 110px;">Create / Save</th>
                                <th class="text-center" style="width: 110px;">Edit / Update</th>
                                <th class="text-center" style="width: 90px;">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permissionGroups as $group)
                                <tr class="perm-group-header">
                                    <td colspan="6">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="perm-group-icon"><i class="fas {{ $group['icon'] }}"></i></span>
                                            <div>
                                                <span class="perm-group-title">{{ $group['title'] }}</span>
                                                @if(!empty($group['description']))
                                                    <span class="perm-group-desc text-muted"> — {{ $group['description'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @foreach($group['pages'] as $slug => $label)
                                    @php
                                        $p = $permissions[$slug] ?? ['can_view' => false, 'can_create' => false, 'can_update' => false, 'can_delete' => false];
                                        $hasAccess = $p['can_view'];
                                        $rowClass = $hasAccess ? 'perm-row--granted' : 'perm-row--denied';
                                    @endphp
                                    <tr class="{{ $rowClass }} perm-group-row" data-page-row="{{ $slug }}">
                                        <td class="ps-4">
                                            <span class="fw-semibold">{{ $label }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="perm-page-status perm-page-status--{{ $hasAccess ? 'yes' : 'no' }}" data-page-status="{{ $slug }}" title="{{ $hasAccess ? 'Accessible' : 'Not accessible' }}">
                                                @if($hasAccess)
                                                    <i class="fas fa-check-circle"></i>
                                                @else
                                                    <i class="fas fa-ban"></i>
                                                @endif
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @include('settings.users._permission-checkbox', [
                                                'slug' => $slug,
                                                'field' => 'can_view',
                                                'checked' => $p['can_view'],
                                                'disabled' => $user->isAdmin(),
                                            ])
                                        </td>
                                        <td class="text-center">
                                            @include('settings.users._permission-checkbox', [
                                                'slug' => $slug,
                                                'field' => 'can_create',
                                                'checked' => $p['can_create'],
                                                'disabled' => $user->isAdmin(),
                                            ])
                                        </td>
                                        <td class="text-center">
                                            @include('settings.users._permission-checkbox', [
                                                'slug' => $slug,
                                                'field' => 'can_update',
                                                'checked' => $p['can_update'],
                                                'disabled' => $user->isAdmin(),
                                            ])
                                        </td>
                                        <td class="text-center">
                                            @include('settings.users._permission-checkbox', [
                                                'slug' => $slug,
                                                'field' => 'can_delete',
                                                'checked' => $p['can_delete'],
                                                'disabled' => $user->isAdmin(),
                                            ])
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if(!$user->isAdmin())
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Permissions</button>
                @endif
                <a href="{{ route('settings.users.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.perm-legend-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 0.85rem;
}
.perm-legend-icon--yes {
    background-color: #d1e7dd;
    color: #198754;
}
.perm-legend-icon--no {
    background-color: #f8d7da;
    color: #dc3545;
}
.perm-legend-icon--page-yes {
    background-color: transparent;
    color: #198754;
    font-size: 1.25rem;
    width: auto;
    height: auto;
}
.perm-legend-icon--page-no {
    background-color: transparent;
    color: #adb5bd;
    font-size: 1.1rem;
    width: auto;
    height: auto;
}

.permissions-table .perm-row--granted {
    background-color: rgba(25, 135, 84, 0.04);
}
.permissions-table .perm-row--denied {
    background-color: rgba(108, 117, 125, 0.06);
}

.perm-page-status {
    font-size: 1.25rem;
    line-height: 1;
}
.perm-page-status--yes {
    color: #198754;
}
.perm-page-status--no {
    color: #adb5bd;
}

.perm-check-wrap {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    margin: 0;
}
.perm-check-wrap.is-disabled {
    cursor: default;
    opacity: 0.85;
}
.perm-check-input {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
    pointer-events: none;
}
.perm-check-visual {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    border: 2px solid #dee2e6;
    background: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: transparent;
    transition: background-color 0.15s, border-color 0.15s, color 0.15s;
    font-size: 0.9rem;
}
.perm-check-input:checked + .perm-check-visual {
    background-color: #198754;
    border-color: #198754;
    color: #fff;
}
.perm-check-input:focus-visible + .perm-check-visual {
    box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
}
.perm-check-wrap:not(.is-disabled):hover .perm-check-visual {
    border-color: #198754;
}
.perm-check-wrap.is-disabled .perm-check-input:checked + .perm-check-visual {
    background-color: #6c757d;
    border-color: #6c757d;
}

.perm-group-header td {
    background-color: #212529;
    color: #fff;
    padding: 0.65rem 1rem;
    border-top: 2px solid #495057;
}
.perm-group-header:first-child td {
    border-top: none;
}
.perm-group-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.12);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.perm-group-title {
    font-weight: 700;
    letter-spacing: 0.03em;
    font-size: 0.9rem;
}
.perm-group-desc {
    font-size: 0.85rem;
    font-weight: 400;
}
.perm-group-row td:first-child {
    border-left: 3px solid rgba(13, 110, 253, 0.35);
}
</style>
@endsection

@section('scripts')
<script>
(function () {
    function updateRowState(row) {
        const viewCb = row.querySelector('.perm-check-input[name$="[can_view]"]');
        const hasAccess = viewCb && viewCb.checked;

        row.classList.toggle('perm-row--granted', hasAccess);
        row.classList.toggle('perm-row--denied', !hasAccess);

        const statusEl = row.querySelector('[data-page-status]');
        if (statusEl) {
            statusEl.classList.toggle('perm-page-status--yes', hasAccess);
            statusEl.classList.toggle('perm-page-status--no', !hasAccess);
            statusEl.innerHTML = hasAccess
                ? '<i class="fas fa-check-circle"></i>'
                : '<i class="fas fa-ban"></i>';
            statusEl.title = hasAccess ? 'Accessible' : 'Not accessible';
        }
    }

    document.querySelectorAll('[data-page-row]').forEach(function (row) {
        row.querySelectorAll('.perm-check-input').forEach(function (cb) {
            cb.addEventListener('change', function () {
                updateRowState(row);
            });
        });
    });
})();
</script>
@endsection
