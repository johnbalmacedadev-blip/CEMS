@extends('layouts.app')

@section('title', 'Sales Agent Commission - Car Empire Management System')

@section('styles')
<style>
    .commission-sheet-table thead th {
        background-color: #4472c4;
        color: #fff;
        font-weight: 600;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.02em;
        vertical-align: middle;
        border-color: #3a5fa8;
        white-space: nowrap;
    }
    .commission-sheet-table tbody td {
        background-color: #e2efda;
        font-size: 0.8rem;
        vertical-align: middle;
        border-color: #b7d5b0;
    }
    .commission-sheet-table tbody tr:nth-child(even) td {
        background-color: #d4e8cc;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-hand-holding-usd me-2"></i>Online Sales Agent Commission
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
            <a href="{{ route('sales-agent-commissions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>Add Commission
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <p class="text-muted mb-3">Sales agent (e.g. Sarah Yao) and sales executive (e.g. THYRA) when the commission is linked to a sales agent with an executive. Vehicle columns use the tagged unit when present.</p>

    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('sales-agent-commissions.index') }}" class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label small mb-0">Transaction</label>
                    <select class="form-select form-select-sm" name="transaction_type" style="width: auto;">
                        <option value="">All</option>
                        <option value="CASH" {{ request('transaction_type') === 'CASH' ? 'selected' : '' }}>CASH</option>
                        <option value="FINANCING" {{ request('transaction_type') === 'FINANCING' ? 'selected' : '' }}>FINANCING</option>
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label small mb-0">Sales agent</label>
                    <input type="text" class="form-control form-control-sm" name="agent" placeholder="Name" value="{{ request('agent') }}" style="width: 160px;">
                </div>
                <div class="col-auto">
                    <label class="form-label small mb-0">Date sent from</label>
                    <input type="date" class="form-control form-control-sm" name="date_from" value="{{ request('date_from') }}" style="width: auto;">
                </div>
                <div class="col-auto">
                    <label class="form-label small mb-0">Date sent to</label>
                    <input type="date" class="form-control form-control-sm" name="date_to" value="{{ request('date_to') }}" style="width: auto;">
                </div>
                <div class="col-auto">
                    <label class="form-label small mb-0">Search</label>
                    <input type="text" class="form-control form-control-sm" name="search" placeholder="Agent, executive, client, plate…" value="{{ request('search') }}" style="width: 200px;">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search me-1"></i>Filter</button>
                    <a href="{{ route('sales-agent-commissions.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if($commissions->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-bordered table-hover commission-sheet-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Showroom</th>
                                <th>Year</th>
                                <th>Make</th>
                                <th>Model</th>
                                <th>Plate #</th>
                                <th>Transaction</th>
                                <th>Status</th>
                                <th>Sales agent</th>
                                <th>Sales executive</th>
                                <th>Client</th>
                                <th>Reservation date / by</th>
                                <th>Release date / by</th>
                                <th class="text-end">Coms amount</th>
                                <th class="text-end">Agents folder</th>
                                <th class="text-end">Sales executive commission</th>
                                <th>Proof of appt.</th>
                                <th>Sign client w/ agent name</th>
                                <th>Date of payment</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($commissions as $c)
                                @php
                                    $v = $c->vehicle;
                                    $rowNum = $loop->iteration + ($commissions->currentPage() - 1) * $commissions->perPage();
                                    $makeName = '—';
                                    $modelName = '—';
                                    if ($v) {
                                        if (is_object($v->make) && isset($v->make->name)) {
                                            $makeName = $v->make->name;
                                        } elseif (is_string($v->make) && $v->make !== '') {
                                            $makeName = $v->make;
                                        }
                                        if (is_object($v->vehicleModel) && isset($v->vehicleModel->name)) {
                                            $modelName = $v->vehicleModel->name;
                                        } elseif (is_string($v->model) && $v->model !== '') {
                                            $modelName = $v->model;
                                        }
                                    }
                                    $yearDisp = $v && $v->year ? $v->year : '—';
                                    $showroom = $c->showroom ?: ($v && $v->purchased_from ? $v->purchased_from : '—');
                                    $plate = $c->plate_number ?: ($v?->plate_number) ?: '—';
                                    $byLine = $c->sales_executive_display;
                                    $bySuffix = ($byLine !== '—') ? $byLine : '—';
                                    $resLine = $c->date_sent ? $c->date_sent->format('j F Y') . ' / ' . $bySuffix : '—';
                                    $relLine = $c->release_date ? $c->release_date->format('j F Y') . ' / ' . $bySuffix : '—';
                                @endphp
                                <tr>
                                    <td class="text-muted">{{ $rowNum }}</td>
                                    <td>{{ $showroom }}</td>
                                    <td>{{ $yearDisp }}</td>
                                    <td>{{ $makeName }}</td>
                                    <td>{{ $modelName }}</td>
                                    <td>
                                        @if($v)
                                            <a href="{{ route('vehicles.show', $v) }}">{{ $plate }}</a>
                                        @else
                                            {{ $plate }}
                                        @endif
                                    </td>
                                    <td><span class="badge bg-{{ $c->transaction_type === 'FINANCING' ? 'primary' : 'secondary' }}">{{ $c->transaction_type }}</span></td>
                                    <td>
                                        @php $commissionStatus = $c->commission_status ?: \App\Models\SalesAgentCommission::STATUS_PENDING; @endphp
                                        <span class="badge bg-{{ $commissionStatus === \App\Models\SalesAgentCommission::STATUS_POSTED ? 'success' : 'warning text-dark' }}">
                                            {{ $commissionStatus }}
                                        </span>
                                    </td>
                                    <td><strong>{{ $c->sales_agent_display }}</strong></td>
                                    <td>{{ $c->sales_executive_display }}</td>
                                    <td>{{ $c->client_name ?: '—' }}</td>
                                    <td class="small">{{ $resLine }}</td>
                                    <td class="small">{{ $relLine }}</td>
                                    <td class="text-end fw-semibold">₱{{ number_format($c->amount, 2) }}</td>
                                    <td class="text-end small">{{ $c->agents_folder_amount !== null ? '₱' . number_format($c->agents_folder_amount, 2) : '—' }}</td>
                                    <td class="text-end small">{{ $c->sales_executive_commission !== null ? '₱' . number_format($c->sales_executive_commission, 2) : '—' }}</td>
                                    <td class="text-center fw-semibold">{{ $c->proof_of_appointment_label }}</td>
                                    <td class="text-center fw-semibold">{{ $c->sign_client_with_agent_label }}</td>
                                    <td class="small">{{ $c->date_of_payment_display ?: '—' }}</td>
                                    <td class="text-center text-nowrap">
                                        <a href="{{ route('sales-agent-commissions.edit', $c) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="fas fa-edit"></i></a>
                                        <form action="{{ route('sales-agent-commissions.destroy', $c) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this commission record?');">
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
                    {{ $commissions->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-hand-holding-usd fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No commission records yet</h5>
                    <p class="text-muted mb-3">Add records to track sales agent commissions (Cash/Financing).</p>
                    <a href="{{ route('sales-agent-commissions.create') }}" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Commission</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
