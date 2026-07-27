@extends('layouts.app')

@section('title', 'Edit Mechanic Job - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-wrench me-2"></i>Edit Mechanic Job
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('mechanic-tracker.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Mechanic Tracker
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('mechanic-tracker.update', $mechanic_tracker) }}" method="POST">
                @csrf
                @method('PUT')
                @include('mechanic-tracker._form', ['record' => $mechanic_tracker])
            </form>
        </div>
    </div>
</div>
@endsection
