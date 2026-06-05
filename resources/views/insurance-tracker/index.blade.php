@extends('layouts.app')

@section('title', 'Insurance List - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-shield-alt me-2"></i>Insurance List
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
            <a href="{{ route('insurance-tracker.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add Record
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <p class="text-muted mb-4">Track insurance by showroom, sales, unit, reservation and release dates.</p>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('insurance-tracker.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small">Month</label>
                    <select class="form-select form-select-sm" name="month">
                        <option value="">All</option>
                        @foreach(['1'=>'January','2'=>'February','3'=>'March','4'=>'April','5'=>'May','6'=>'June','7'=>'July','8'=>'August','9'=>'September','10'=>'October','11'=>'November','12'=>'December'] as $num => $label)
                            <option value="{{ $num }}" {{ request('month') === $num ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Year</label>
                    <select class="form-select form-select-sm" name="year">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Showroom</label>
                    <input type="text" class="form-control form-control-sm" name="showroom" placeholder="e.g. FLAGSHIP" value="{{ request('showroom') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Search</label>
                    <input type="text" class="form-control form-control-sm" name="search" placeholder="Sales, make, model, number..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm me-2"><i class="fas fa-search me-1"></i>Filter</button>
                    <a href="{{ route('insurance-tracker.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($records->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-bordered align-middle mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th class="text-center">#</th>
                                <th>SHOWROOM</th>
                                <th>SALES</th>
                                <th>YEAR</th>
                                <th>MAKE</th>
                                <th>MODEL</th>
                                <th>NUMBER</th>
                                <th>TRANSACTION</th>
                                <th>SOURCE</th>
                                <th>RESERVATION</th>
                                <th>RELEASE DATE</th>
                                <th class="text-end">AMOUNT</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($records as $index => $r)
                                <tr>
                                    <td class="text-center">{{ $records->firstItem() + $index }}</td>
                                    <td>{{ $r->showroom ?: '—' }}</td>
                                    <td>{{ $r->sales ?: '—' }}</td>
                                    <td>{{ $r->display_year }}</td>
                                    <td>{{ $r->display_make }}</td>
                                    <td>{{ $r->display_model }}</td>
                                    <td>
                                        @if($r->vehicle_id && $r->vehicle)
                                            <a href="{{ route('vehicles.show', $r->vehicle) }}">{{ $r->display_number }}</a>
                                        @else
                                            {{ $r->display_number }}
                                        @endif
                                    </td>
                                    <td>{{ $r->transaction ?: '—' }}</td>
                                    <td class="small">{{ Str::limit($r->source, 30) ?: '—' }}</td>
                                    <td>{{ $r->reservation_date ? $r->reservation_date->format('d F Y') : '—' }}</td>
                                    <td>{{ $r->release_date ? $r->release_date->format('d F Y') : '—' }}</td>
                                    <td class="text-end fw-bold">{{ $r->amount !== null ? '₱' . number_format($r->amount, 2) : '—' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('insurance-tracker.edit', $r) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('insurance-tracker.destroy', $r) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this record?');">
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
                    {{ $records->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-shield-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No insurance records yet</h5>
                    <p class="text-muted mb-3">Add records to track insurance by showroom, unit, and dates.</p>
                    <a href="{{ route('insurance-tracker.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Record</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
