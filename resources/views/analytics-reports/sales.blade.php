@extends('layouts.app')

@section('title', 'Sales Report - Car Empire Management System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-2">
        <h1 class="h3 mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Sales Report</h1>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
            <i class="fas fa-home me-1"></i>Back to Home
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <p class="mb-2">This page is ready for your sales report content.</p>
            <p class="text-muted mb-0">You can add sales trends, conversion stats, and monthly summaries here.</p>
        </div>
    </div>
</div>
@endsection
