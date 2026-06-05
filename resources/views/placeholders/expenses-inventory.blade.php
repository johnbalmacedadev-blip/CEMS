@extends('layouts.app')

@section('title', 'Expenses & Inventory - Coming Soon')

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
    <div class="card border-warning">
        <div class="card-body py-5 text-center">
            <div class="mb-3">
                <span class="icon-circle text-warning"><i class="fas fa-file-invoice-dollar"></i></span>
            </div>
            <h3 class="mb-2">Expenses & Inventory</h3>
            <p class="text-muted mb-0">Car, transfer, operating expenses, and inventory — coming soon.</p>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.icon-circle { width: 64px; height: 64px; border: 1px solid currentColor; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; }
</style>
@endsection


