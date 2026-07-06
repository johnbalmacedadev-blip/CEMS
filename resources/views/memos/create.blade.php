@extends('layouts.app')

@section('title', 'Add Memo - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-sticky-note me-2"></i>Add Memo
        </h1>
        <a href="{{ route('memos.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Memos
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('memos.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('memos._form')
            </form>
        </div>
    </div>
</div>
@endsection
