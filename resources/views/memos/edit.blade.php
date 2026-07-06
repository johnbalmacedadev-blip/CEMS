@extends('layouts.app')

@section('title', 'Edit Memo - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-sticky-note me-2"></i>Edit Memo
        </h1>
        <a href="{{ route('memos.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Memos
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('memos.update', $memo) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('memos._form', ['memo' => $memo])
            </form>
        </div>
    </div>
</div>
@endsection
