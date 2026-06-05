@extends('layouts.app')

@section('title', 'Agent BOLO - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-user-tie me-2"></i>Agent BOLO</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">Back to Main Menu</a>
            <a href="{{ route('agent-bolo.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Create Agent</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <p class="text-muted mb-4">Manage agents and their files/links. View or edit an agent to add files or links.</p>

    @if($agents->isNotEmpty())
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Sales Executive</th>
                                <th>Sales Agent</th>
                                <th>Phone Number</th>
                                <th>Email</th>
                                <th>Files/Links</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($agents as $a)
                                <tr>
                                    <td>{{ $a->sales_executive ?? '—' }}</td>
                                    <td><strong>{{ $a->name }}</strong></td>
                                    <td>{{ $a->contact_number ?? '—' }}</td>
                                    <td>{{ $a->email ?? '—' }}</td>
                                    <td>{{ $a->documents_count ?? 0 }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('agent-bolo.show', $a) }}" class="btn btn-sm btn-outline-primary">View</a>
                                        <a href="{{ route('agent-bolo.edit', $a) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                                        <form action="{{ route('agent-bolo.destroy', $a) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this agent and all their files/links?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <h5 class="text-muted mb-3">No agents yet</h5>
                <p class="text-muted mb-4">Create an agent to add their basic info and files/links.</p>
                <a href="{{ route('agent-bolo.create') }}" class="btn btn-primary">Create Agent</a>
            </div>
        </div>
    @endif
</div>
@endsection
