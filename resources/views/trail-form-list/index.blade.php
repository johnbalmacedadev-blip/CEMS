@extends('layouts.app')

@section('title', 'Trail Form List - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-clipboard-list me-2"></i>Trail Form List
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
            <a href="{{ route('trail-form-list.create') }}" class="btn btn-primary">
                <i class="fas fa-user-plus me-1"></i>Add Client
            </a>
        </div>
    </div>

    @include('partials.flash-alert')

    <p class="text-muted mb-4">Track client inquiries and reservations — where they came from and what vehicle they are interested in.</p>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('trail-form-list.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small">Status</label>
                    <select class="form-select form-select-sm" name="status">
                        <option value="">All</option>
                        @foreach(\App\Models\TrailFormClient::statusOptions() as $opt)
                            <option value="{{ $opt }}" {{ request('status') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Inquiry source</label>
                    <select class="form-select form-select-sm" name="inquiry_source">
                        <option value="">All</option>
                        @foreach(\App\Models\TrailFormClient::inquirySourceOptions() as $opt)
                            <option value="{{ $opt }}" {{ request('inquiry_source') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Search</label>
                    <input type="text" class="form-control form-control-sm" name="search" placeholder="Name, contact, vehicle..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm me-2"><i class="fas fa-search me-1"></i>Filter</button>
                    <a href="{{ route('trail-form-list.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($clients->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Inquiry Date</th>
                                <th>Client Name</th>
                                <th>Contact</th>
                                <th>Status</th>
                                <th>Inquired From</th>
                                <th>Vehicle Type</th>
                                <th>Unit Inquired</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($clients as $client)
                                <tr>
                                    <td>{{ $client->inquiry_date?->format('M j, Y') ?? '—' }}</td>
                                    <td>
                                        <strong>{{ $client->client_name }}</strong>
                                        @if($client->email)
                                            <br><small class="text-muted">{{ $client->email }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $client->contact_number ?? '—' }}</td>
                                    <td>
                                        @if($client->status === 'Reservation')
                                            <span class="badge bg-success">{{ $client->status }}</span>
                                        @else
                                            <span class="badge bg-info text-dark">{{ $client->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $client->inquiry_source ?? '—' }}</td>
                                    <td>{{ $client->vehicle_type ?? '—' }}</td>
                                    <td>{{ $client->vehicle_interest ?? '—' }}</td>
                                    <td class="text-center text-nowrap">
                                        <a href="{{ route('trail-form-list.edit', $client) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('trail-form-list.destroy', $client) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this client?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($clients->hasPages())
                    <div class="card-footer">{{ $clients->links() }}</div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted mb-3">No clients yet</h5>
                    <p class="text-muted mb-4">Add a client inquiry or reservation to get started.</p>
                    <a href="{{ route('trail-form-list.create') }}" class="btn btn-primary">
                        <i class="fas fa-user-plus me-1"></i>Add Client
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
