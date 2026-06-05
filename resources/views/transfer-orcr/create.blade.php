@extends('layouts.app')

@section('title', 'Add Transfer OR/CR - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-file-invoice me-2"></i>Add Transfer OR/CR Record
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('transfer-orcr.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to List
            </a>
        </div>
    </div>

    <p class="text-muted mb-3">Fields match the transfer OR/CR spreadsheet: vehicle details, fees, RD, status, and auto-calculated total.</p>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('transfer-orcr.store') }}" method="POST">
                @csrf
                @include('transfer-orcr._form')
            </form>
        </div>
    </div>
</div>
@include('transfer-orcr._form-scripts')
@endsection
