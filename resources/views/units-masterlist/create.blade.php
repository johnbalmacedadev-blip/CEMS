@extends('layouts.app')

@section('title', 'Add Unit - Units Masterlist - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-plus me-2"></i>Add Unit
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('units-masterlist.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Masterlist
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('units-masterlist.store') }}" method="POST">
                @csrf
                @include('units-masterlist._form')
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Unit</button>
                    <a href="{{ route('units-masterlist.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

@include('units-masterlist._financing-recompute')
@endsection
