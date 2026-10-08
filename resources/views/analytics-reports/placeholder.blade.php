@extends('layouts.app')

@section('title', $title.' - Car Empire Management System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-2">
        <h1 class="h3 mb-0"><i class="fas {{ $icon }} me-2 text-success"></i>{{ $title }}</h1>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
            <i class="fas fa-home me-1"></i>Back to Home
        </a>
    </div>

    <div class="card">
        <div class="card-body py-5 text-center">
            <i class="fas {{ $icon }} fa-3x text-muted mb-3"></i>
            <h2 class="h5 mb-2">{{ $title }}</h2>
            <p class="text-muted mb-0">{{ $message }}</p>
        </div>
    </div>
</div>
@endsection
