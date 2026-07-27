@extends('layouts.app')

@section('title', 'Appointment List - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-calendar-check me-2"></i>Appointment List
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
            @canPage('appointment-list', 'create')
            <a href="{{ route('appointment-list.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add Appointment
            </a>
            @endcanPage
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <p class="text-muted mb-4">Schedule and track showroom visits. Date added, customer details, preferred unit, and visit outcome.</p>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('appointment-list.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small">Date of Visit From</label>
                    <input type="date" class="form-control form-control-sm" name="date_visit_from" value="{{ request('date_visit_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Date of Visit To</label>
                    <input type="date" class="form-control form-control-sm" name="date_visit_to" value="{{ request('date_visit_to') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Showroom</label>
                    <input type="text" class="form-control form-control-sm" name="showroom" placeholder="Showroom" value="{{ request('showroom') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Search</label>
                    <input type="text" class="form-control form-control-sm" name="search" placeholder="Customer, phone, unit, outcome..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm me-2"><i class="fas fa-search me-1"></i>Filter</button>
                    <a href="{{ route('appointment-list.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($appointments->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date Added</th>
                                <th>Added By</th>
                                <th>Customer</th>
                                <th>Phone</th>
                                <th>Showroom</th>
                                <th>Date of Visit</th>
                                <th>Preferred Unit</th>
                                <th>Sales Exec</th>
                                <th>Outcome</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $apt)
                                <tr>
                                    <td>{{ $apt->date_added_to_schedule ? $apt->date_added_to_schedule->format('M d, Y') : '—' }}</td>
                                    <td>{{ $apt->added_by ?: '—' }}</td>
                                    <td><strong>{{ $apt->customer_full_name }}</strong></td>
                                    <td>{{ $apt->customer_phone_number ?: '—' }}</td>
                                    <td>
                                        @include('partials.showroom-badge', ['name' => $apt->showroom])
                                    </td>
                                    <td>{{ $apt->date_of_visit ? $apt->date_of_visit->format('M d, Y') : '—' }}</td>
                                    <td class="small">
                                        @if($apt->vehicle_id && $apt->vehicle)
                                            <a href="{{ route('vehicles.show', $apt->vehicle) }}">{{ $apt->vehicle->full_name }}</a>@if($apt->vehicle->plate_number) ({{ $apt->vehicle->plate_number }})@endif
                                        @else
                                            {{ Str::limit($apt->preferred_unit, 35) ?: '—' }}
                                        @endif
                                    </td>
                                    <td>{{ $apt->sales_exec_who_assisted ?: '—' }}</td>
                                    <td>{{ Str::limit($apt->outcome, 25) ?: '—' }}</td>
                                    <td class="text-center">
                                        @canPage('appointment-list', 'update')
                                        <a href="{{ route('appointment-list.edit', $apt) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                        @endcanPage
                                        @canPage('appointment-list', 'delete')
                                        <form action="{{ route('appointment-list.destroy', $apt) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this appointment?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                        @endcanPage
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3 pb-3">
                    {{ $appointments->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No appointments yet</h5>
                    <p class="text-muted mb-3">Add appointments to schedule and track showroom visits.</p>
                    @canPage('appointment-list', 'create')
                    <a href="{{ route('appointment-list.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Appointment</a>
                    @endcanPage
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
