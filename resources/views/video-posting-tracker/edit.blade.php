@extends('layouts.app')

@section('title', 'Edit Video/Posting Record - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-video me-2"></i>Edit Video / Posting Record
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('video-posting-tracker.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Tracker
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('video-posting-tracker.update', $video_posting_tracker) }}" method="POST">
                @csrf
                @method('PUT')
                @include('video-posting-tracker._form', ['video_posting_tracker' => $video_posting_tracker])
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Update</button>
                    <a href="{{ route('video-posting-tracker.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
