@extends('layouts.app')

@section('title', 'Operations Tracker - Coming Soon')

@section('content')
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('home') }}"><i class="fas fa-chevron-left me-2"></i>Home</a>
    </div>
</nav>
<div class="container py-5">
    <div class="card border-secondary">
        <div class="card-body py-5 text-center">
            <div class="mb-3">
                <span class="icon-circle text-secondary"><i class="fas fa-tasks"></i></span>
            </div>
            <h3 class="mb-2">Operations Tracker</h3>
            <p class="text-muted mb-0">Mechanic, buffing, cleaning, and driver tracking — coming soon.</p>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.icon-circle { width: 64px; height: 64px; border: 1px solid currentColor; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 1.5rem; }
</style>
@endsection















