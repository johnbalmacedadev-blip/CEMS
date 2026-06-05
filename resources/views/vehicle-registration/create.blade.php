@extends('layouts.app')

@section('title', 'Add Vehicle Registration - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-id-card me-2"></i>Add Vehicle Registration</h1>
        <a href="{{ route('vehicle-registration.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to List
        </a>
    </div>
    <div class="card">
        <div class="card-body">
            <form action="{{ route('vehicle-registration.store') }}" method="POST">
                @csrf
                @include('vehicle-registration._form')
            </form>
        </div>
    </div>
</div>
@include('vehicle-registration._form-scripts')
@endsection
