@extends('layouts.app')

@section('title', 'Client Follow Up List - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-user-clock me-2"></i>Client Follow Up List
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
            <a href="{{ route('client-follow-up-list.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add Client
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <p class="text-muted mb-4">Track clients that need follow-up. Optionally link to a unit (vehicle) of interest.</p>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('client-follow-up-list.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small">Status</label>
                    <select class="form-select form-select-sm" name="status">
                        <option value="">All</option>
                        @foreach(\App\Models\ClientFollowUp::statusOptions() as $opt)
                            <option value="{{ $opt }}" {{ request('status') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Follow-up From</label>
                    <input type="date" class="form-control form-control-sm" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Follow-up To</label>
                    <input type="date" class="form-control form-control-sm" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Search</label>
                    <input type="text" class="form-control form-control-sm" name="search" placeholder="Name, contact, email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm me-2"><i class="fas fa-search me-1"></i>Filter</button>
                    <a href="{{ route('client-follow-up-list.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card">
        <div class="card-body p-0">
            @if($clients->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date of First Inquiry</th>
                                <th>Application</th>
                                <th>Customer Name</th>
                                <th>Customer Phone</th>
                                <th>Unit Inquired</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clients as $client)
                                <tr>
                                    <td>{{ $client->date_of_first_inquiry ? $client->date_of_first_inquiry->format('m/d/Y') : '—' }}</td>
                                    <td>{{ $client->application ?: '—' }}</td>
                                    <td><strong>{{ $client->client_name }}</strong></td>
                                    <td>
                                        @if($client->contact_number)
                                            <span class="d-block">{{ $client->contact_number }}</span>
                                        @endif
                                        @if($client->email)
                                            <span class="small text-muted">{{ $client->email }}</span>
                                        @endif
                                        @if(!$client->contact_number && !$client->email)
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="small">
                                        @if($client->vehicle_id && $client->vehicle)
                                            <a href="{{ route('vehicles.show', $client->vehicle) }}">{{ Str::limit($client->unit_inquired, 30) ?: $client->vehicle->full_name }}</a>
                                        @else
                                            {{ Str::limit($client->unit_inquired, 30) ?: '—' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($client->status === 'Closed')
                                            <span class="badge bg-success">{{ $client->status }}</span>
                                        @elseif($client->status === 'In Progress')
                                            <span class="badge bg-info">{{ $client->status }}</span>
                                        @elseif($client->status === 'Contacted')
                                            <span class="badge bg-primary">{{ $client->status }}</span>
                                        @else
                                            <span class="badge bg-warning text-dark">{{ $client->status }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('client-follow-up-list.edit', $client) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('client-follow-up-list.destroy', $client) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this client from the follow-up list?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3 pb-3">
                    {{ $clients->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-user-clock fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No clients in follow-up list yet</h5>
                    <p class="text-muted mb-3">Add clients that need follow-up (inquiries, leads, etc.).</p>
                    <a href="{{ route('client-follow-up-list.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Client</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
