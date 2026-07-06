@extends('layouts.app')

@section('title', 'Add Client - Trail Form List')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-user-plus me-2"></i>Add Client
        </h1>
        <a href="{{ route('trail-form-list.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Trail Form List
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('trail-form-list.store') }}" method="POST">
                @csrf
                @include('trail-form-list._form')
            </form>
        </div>
    </div>
</div>
@endsection
