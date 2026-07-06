@extends('layouts.app')

@section('title', 'Settings - Car Empire Management System')

@section('content')
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <button class="btn btn-outline-light me-3" type="button" onclick="window.history.back()">
            <i class="fas fa-chevron-left"></i>
        </button>
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('images/CAREMPIRE_LOGO.png') }}" alt="CAR EMPIRE Logo" onerror="this.style.display='none';">
        </a>
    </div>
</nav>
<div class="container py-5">
    <div class="row">
        <div class="col-md-12 mb-4">
            <h2 class="mb-4">
                <i class="fas fa-cog me-2"></i>Settings
            </h2>
        </div>
    </div>
    
    <div class="row">
        <!-- Form Templates Card -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100" style="border-color: #6c757d;">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <span class="icon-circle" style="color: #6c757d; border-color: #6c757d;">
                            <i class="fas fa-file-alt"></i>
                        </span>
                    </div>
                    <h5 class="card-title mb-3">Form Templates</h5>
                    <p class="text-muted mb-4">Create and manage form templates that can be assigned to specific document types.</p>
                    <a href="{{ route('document-templates.index') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create Form Template
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Car Price List / Financing -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100" style="border-color: #198754;">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <span class="icon-circle" style="color: #198754; border-color: #198754;">
                            <i class="fas fa-calculator"></i>
                        </span>
                    </div>
                    <h5 class="card-title mb-3">Car Price List / Financing</h5>
                    <p class="text-muted mb-4">ASIALINK 2nd Hand Car Financing variables and calculator (year model ranges, chattel fee, insurance, term %, monthly payment).</p>
                    <a href="{{ route('settings.financing.index') }}" class="btn btn-success">
                        <i class="fas fa-cog me-2"></i>Manage Variables
                    </a>
                </div>
            </div>
        </div>
        
        <!-- User Management (admin only) -->
        @if(Auth::user()->isAdmin())
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100" style="border-color: #0d6efd;">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <span class="icon-circle" style="color: #0d6efd; border-color: #0d6efd;">
                            <i class="fas fa-users-cog"></i>
                        </span>
                    </div>
                    <h5 class="card-title mb-3">User Management</h5>
                    <p class="text-muted mb-4">Create user logins and set page-level access. Control who can view, create, update, or delete per page.</p>
                    <a href="{{ route('settings.users.index') }}" class="btn btn-primary">
                        <i class="fas fa-users me-2"></i>Manage Users
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- User Activity Logs -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100" style="border-color: #6f42c1;">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <span class="icon-circle" style="color: #6f42c1; border-color: #6f42c1;">
                            <i class="fas fa-history"></i>
                        </span>
                    </div>
                    <h5 class="card-title mb-3">User Activity Logs</h5>
                    <p class="text-muted mb-4">View all user activity: login, logout, create, edit, update, and delete actions across the system.</p>
                    <a href="{{ route('admin-docs') }}" class="btn btn-settings-purple">
                        <i class="fas fa-list me-2"></i>View Activity Logs
                    </a>
                </div>
            </div>
        </div>

        <!-- Branch / Location -->
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100" style="border-color: #fd7e14;">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <span class="icon-circle" style="color: #fd7e14; border-color: #fd7e14;">
                            <i class="fas fa-map-marker-alt"></i>
                        </span>
                    </div>
                    <h5 class="card-title mb-3">Branch / Location</h5>
                    <p class="text-muted mb-4">Create and manage a list of branch or office locations (e.g. Muntinlupa, NCR, Laguna).</p>
                    <a href="{{ route('settings.branch-locations.index') }}" class="btn btn-settings-orange">
                        <i class="fas fa-list me-2"></i>Manage Locations
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.icon-circle { 
    width: 64px; 
    height: 64px; 
    border: 1px solid currentColor; 
    border-radius: 50%; 
    display: inline-flex; 
    align-items: center; 
    justify-content: center; 
    font-size: 1.5rem; 
}
.btn-settings-purple {
    color: #6f42c1;
    background-color: transparent;
    border-color: #6f42c1;
}
.btn-settings-purple:hover {
    background-color: #6f42c1;
    color: #fff;
    border-color: #6f42c1;
}
.btn-settings-orange {
    color: #fd7e14;
    background-color: transparent;
    border-color: #fd7e14;
}
.btn-settings-orange:hover {
    background-color: #fd7e14;
    color: #fff;
    border-color: #fd7e14;
}
</style>
@endsection



