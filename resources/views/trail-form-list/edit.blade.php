@extends('layouts.app')

@section('title', 'Edit Client - Trail Form List')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-edit me-2"></i>Edit Client
        </h1>
        <a href="{{ route('trail-form-list.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Trail Form List
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('trail-form-list.update', $client) }}" method="POST">
                @csrf
                @method('PUT')
                @include('trail-form-list._form', ['client' => $client])
            </form>
        </div>
    </div>
</div>
@endsection
