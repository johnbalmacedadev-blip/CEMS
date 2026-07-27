@extends('layouts.app')

@section('title', 'Expenses & Inventory - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="row">
        <main class="col-12 px-md-4 main-content" id="mainContent">
            @if($section == 'expenses')
                <!-- Expense Transactions Section -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Expense Transactions
                </h1>
                    <div class="btn-toolbar mb-2 mb-md-0 flex-wrap">
                        <a href="{{ route('expenses.create') }}" class="btn btn-primary mb-2 mb-md-0">
                        <i class="fas fa-plus me-1"></i>Add New Transaction
                    </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary ms-md-2 mb-2 mb-md-0">
                        <i class="fas fa-home me-1"></i>Back to Main Menu
                    </a>
                </div>
            </div>

            <!-- Filter Section (collapsed by default) -->
            <div class="accordion mb-4" id="expenseFilterAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="expenseFilterHeading">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#expenseFilterCollapse" aria-expanded="false" aria-controls="expenseFilterCollapse">
                            <i class="fas fa-filter me-2"></i>Filter Expenses
                        </button>
                    </h2>
                    <div id="expenseFilterCollapse" class="accordion-collapse collapse" aria-labelledby="expenseFilterHeading" data-bs-parent="#expenseFilterAccordion">
                        <div class="accordion-body">
                    <form method="GET" action="{{ route('expenses-inventory') }}" id="filterForm">
                        <input type="hidden" name="section" value="expenses">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="date_from" class="form-label">Date From:</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="date_to" class="form-label">Date To:</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-3">
                                <label for="expense_type" class="form-label">Expense Type:</label>
                                <select class="form-select" id="expense_type" name="expense_type">
                                    <option value="">All Types</option>
                                    <option value="Vehicle" {{ request('expense_type') == 'Vehicle' ? 'selected' : '' }}>Vehicle</option>
                                    <option value="Operating" {{ request('expense_type') == 'Operating' ? 'selected' : '' }}>Operating</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="payment_method_id" class="form-label">Payment Method:</label>
                                <select class="form-select" id="payment_method_id" name="payment_method_id">
                                    <option value="">All Methods</option>
                                    @if($paymentMethods)
                                        @foreach($paymentMethods as $method)
                                            <option value="{{ $method->id }}" {{ request('payment_method_id') == $method->id ? 'selected' : '' }}>
                                                {{ $method->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                        <div class="row g-3 mt-1">
                            <div class="col-md-4 col-lg-3">
                                <label for="search_by" class="form-label">Search by</label>
                                <select class="form-select" id="search_by" name="search_by">
                                    <option value="all" {{ request('search_by', 'all') === 'all' ? 'selected' : '' }}>All (plate, type, requested by)</option>
                                    <option value="plate" {{ request('search_by') === 'plate' ? 'selected' : '' }}>Vehicle plate number</option>
                                    <option value="vehicle_type" {{ request('search_by') === 'vehicle_type' ? 'selected' : '' }}>Vehicle type (body, make, model)</option>
                                    <option value="requested_by" {{ request('search_by') === 'requested_by' ? 'selected' : '' }}>Requested by</option>
                                </select>
                            </div>
                            <div class="col-md-8 col-lg-6">
                                <label for="search" class="form-label">Search keywords</label>
                                <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Plate, body type, make, model, or requester name" autocomplete="off">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12 d-flex flex-wrap align-items-center gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i>Apply Filters
                                </button>
                                <a href="{{ route('expenses-inventory', ['section' => 'expenses']) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i>Clear Filters
                                </a>
                                <span class="text-muted small ms-md-2">Export (uses current filters):</span>
                                <a href="{{ route('expenses-inventory.export') }}?{{ http_build_query(array_merge(request()->only(['date_from', 'date_to', 'expense_type', 'payment_method_id', 'search', 'search_by']), ['section' => 'expenses', 'format' => 'csv'])) }}" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-file-excel me-1"></i>Excel (CSV)
                                </a>
                                <a href="{{ route('expenses-inventory.export') }}?{{ http_build_query(array_merge(request()->only(['date_from', 'date_to', 'expense_type', 'payment_method_id', 'search', 'search_by']), ['section' => 'expenses', 'format' => 'pdf'])) }}" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-file-pdf me-1"></i>PDF
                                </a>
                            </div>
                        </div>
                    </form>
                        </div>
                    </div>
                </div>
            </div>

            @if($section == 'expenses' && isset($expenseBudgetSummary))
                @if(!empty($expenseBudgetSummary['applicable']))
                    @if(!empty($expenseBudgetSummary['has_any_budget']))
                        <div class="card mb-4 border-primary shadow-sm">
                            <div class="card-header bg-primary text-white d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <h5 class="card-title mb-0 text-white">
                                    <i class="fas fa-piggy-bank me-2"></i>Daily expense budget (from SOA)
                                </h5>
                                <span class="small opacity-90">{{ $expenseBudgetSummary['date_formatted'] ?? '' }}</span>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-3 mb-md-2">
                                    Each row matches an SOA <strong>Expense budget</strong> tier. <strong>Payment method</strong> is the cost-center line used on expenses (flagship / annex / warehouse).
                                    <strong>Total expenses</strong> and <strong>Remaining</strong> use only expense lines for that same payment method on this date (not limited by expense type or search filters above).
                                </p>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Payment method</th>
                                                <th>Budget location</th>
                                                <th>Description</th>
                                                <th class="text-end">Starting budget</th>
                                                <th class="text-end">Total expenses</th>
                                                <th class="text-end">Remaining balance</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($expenseBudgetSummary['rows'] as $bud)
                                                <tr>
                                                    <td>{{ $bud['payment_method_name'] }}</td>
                                                    <td>{{ $bud['tier_label'] ?? '—' }}</td>
                                                    <td>{{ $bud['entry_description'] ?: '—' }}</td>
                                                    <td class="text-end fw-semibold">₱{{ number_format($bud['budget_amount'], 2) }}</td>
                                                    <td class="text-end text-danger fw-semibold">₱{{ number_format($bud['total_expenses'], 2) }}</td>
                                                    <td class="text-end fw-bold {{ ($bud['remaining'] ?? 0) < 0 ? 'text-danger' : 'text-success' }}">
                                                        ₱{{ number_format($bud['remaining'], 2) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('soa.create') }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-file-invoice me-1"></i>Open SOA
                                    </a>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-light border mb-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                            <div>
                                <i class="fas fa-info-circle me-2 text-primary"></i>
                                No <strong>Expense budget</strong> line was found in SOA for
                                <strong>{{ $expenseBudgetSummary['date_formatted'] ?? '' }}</strong>
                                @if(request()->filled('payment_method_id'))
                                    for the selected payment method.
                                @else
                                    (any payment method).
                                @endif
                            </div>
                            <a href="{{ route('soa.create') }}" class="btn btn-sm btn-primary">Set up in SOA</a>
                        </div>
                    @endif
                @elseif(($expenseBudgetSummary['reason'] ?? '') === 'multi_day')
                    <div class="alert alert-secondary py-2 small mb-4">
                        <i class="fas fa-calendar-alt me-1"></i>
                        Daily expense budget (from SOA) is shown when <strong>Date From</strong> and <strong>Date To</strong> are the same day.
                    </div>
                @endif
            @endif

            @if($expenseItems && $expenseItems->count() > 0)
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">All Expense Transactions</h5>
                        <span class="badge bg-primary">{{ $expenseItems->total() }} item(s) found</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" style="width: 100%; table-layout: auto;">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Transaction Date</th>
                                        <th>Expense Date</th>
                                        <th>Description</th>
                                        <th>Expense Type</th>
                                        <th>Payment Method</th>
                                        <th>Requested By</th>
                                        <th>Approved By</th>
                                        <th>Paid By</th>
                                        <th>Store / Shop</th>
                                        <th>Cost</th>
                                        <th>Vehicle</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expenseItems as $item)
                                        <tr>
                                            <td>{{ $item->expenseTransaction->transaction_date->format('M d, Y') }}</td>
                                            <td>{{ $item->expense_date ? $item->expense_date->format('M d, Y') : 'N/A' }}</td>
                                            <td>{{ strlen($item->description) > 50 ? substr($item->description, 0, 50) . '...' : $item->description }}</td>
                                            <td>
                                                <span class="badge bg-{{ $item->payment_tag == 'Vehicle' ? 'info' : 'secondary' }}">
                                                    {{ $item->payment_tag }}
                                                </span>
                                            </td>
                                            <td>{{ $item->paymentMethod ? $item->paymentMethod->name : 'N/A' }}</td>
                                            <td>{{ $item->requested_by ?: 'N/A' }}</td>
                                            <td>{{ $item->approved_by ?: 'N/A' }}</td>
                                            <td>{{ $item->care_of ?: 'N/A' }}</td>
                                            <td>{{ $item->store_shop ?: 'N/A' }}</td>
                                            <td><strong class="text-danger">₱{{ number_format($item->cost, 2) }}</strong></td>
                                            <td>
                                                @if($item->vehicle)
                                                    {{ $item->vehicle->plate_number }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('expenses.show', $item->expenseTransaction) }}?item={{ $item->id }}" class="btn btn-sm btn-outline-primary" title="View Transaction">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $expenseItems->links() }}
                        </div>
                    </div>
                </div>
            @elseif($expenseItems && $expenseItems->count() == 0)
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No expense transactions found</h4>
                        <p class="text-muted">Try adjusting your filters or start by creating your first expense transaction.</p>
                        <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Add New Transaction
                        </a>
                    </div>
                </div>
            @elseif(!$expenseItems)
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-file-invoice-dollar fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No expense transactions found</h4>
                        <p class="text-muted">Start by creating your first expense transaction.</p>
                        <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>Add New Transaction
                        </a>
                    </div>
                </div>
            @endif

            @elseif($section == 'external-expenses')
                <!-- External Expenses Section -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="fas fa-store me-2"></i>External Expenses
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0 flex-wrap">
                        <a href="{{ route('expenses-inventory', ['section' => 'expenses']) }}" class="btn btn-outline-primary mb-2 mb-md-0">
                            <i class="fas fa-file-invoice-dollar me-1"></i>Expense Transactions
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary ms-md-2 mb-2 mb-md-0">
                            <i class="fas fa-home me-1"></i>Back to Main Menu
                        </a>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-filter me-2"></i>Filter by date
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="{{ route('expenses-inventory') }}" id="externalFilterForm">
                            <input type="hidden" name="section" value="external-expenses">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="ext_date_from" class="form-label">Date From:</label>
                                    <input type="date" class="form-control" id="ext_date_from" name="date_from" value="{{ request('date_from') }}">
                                </div>
                                <div class="col-md-4">
                                    <label for="ext_date_to" class="form-label">Date To:</label>
                                    <input type="date" class="form-control" id="ext_date_to" name="date_to" value="{{ request('date_to') }}">
                                </div>
                            </div>
                            <div class="row g-3 mt-1">
                                <div class="col-md-4 col-lg-3">
                                    <label for="ext_search_by" class="form-label">Search by</label>
                                    <select class="form-select" id="ext_search_by" name="search_by">
                                        <option value="all" {{ request('search_by', 'all') === 'all' ? 'selected' : '' }}>All (plate, type, requested by)</option>
                                        <option value="plate" {{ request('search_by') === 'plate' ? 'selected' : '' }}>Vehicle plate number</option>
                                        <option value="vehicle_type" {{ request('search_by') === 'vehicle_type' ? 'selected' : '' }}>Vehicle type (body, make, model)</option>
                                        <option value="requested_by" {{ request('search_by') === 'requested_by' ? 'selected' : '' }}>Requested by</option>
                                    </select>
                                </div>
                                <div class="col-md-8 col-lg-6">
                                    <label for="ext_search" class="form-label">Search keywords</label>
                                    <input type="text" class="form-control" id="ext_search" name="search" value="{{ request('search') }}" placeholder="Plate, body type, make, model, or requester name" autocomplete="off">
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 d-flex flex-wrap align-items-center gap-2">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="fas fa-search me-1"></i>Apply
                                    </button>
                                    <a href="{{ route('expenses-inventory', ['section' => 'external-expenses']) }}" class="btn btn-outline-secondary">Clear</a>
                                    <span class="text-muted small ms-md-2">Export (uses current filters):</span>
                                    <a href="{{ route('expenses-inventory.export') }}?{{ http_build_query(array_merge(request()->only(['date_from', 'date_to', 'search', 'search_by']), ['section' => 'external-expenses', 'format' => 'csv'])) }}" class="btn btn-outline-success btn-sm">
                                        <i class="fas fa-file-excel me-1"></i>Excel (CSV)
                                    </a>
                                    <a href="{{ route('expenses-inventory.export') }}?{{ http_build_query(array_merge(request()->only(['date_from', 'date_to', 'search', 'search_by']), ['section' => 'external-expenses', 'format' => 'pdf'])) }}" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-file-pdf me-1"></i>PDF
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                @if($externalExpenseItems && $externalExpenseItems->count() > 0)
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Expenses repaired by external shops</h5>
                            <span class="badge bg-primary">{{ $externalExpenseItems->total() }} item(s)</span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Expenses</th>
                                            <th>Amount</th>
                                            <th>Repaired By</th>
                                            <th>Unit</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($externalExpenseItems as $item)
                                            <tr>
                                                <td>{{ $item->description }}{{ $item->description_details ? ' — ' . (strlen($item->description_details) > 80 ? substr($item->description_details, 0, 80) . '...' : $item->description_details) : '' }}</td>
                                                <td><strong class="text-danger">₱{{ number_format($item->cost, 2) }}</strong></td>
                                                <td>{{ $item->store_shop }}</td>
                                                <td>{{ $item->vehicle ? trim($item->vehicle->full_name . ' ' . $item->vehicle->plate_number) : '—' }}</td>
                                                <td>{{ $item->expense_date ? $item->expense_date->format('j-M-y') : '—' }}</td>
                                                <td>
                                                    <a href="{{ route('expenses.show', $item->expenseTransaction) }}?item={{ $item->id }}" class="btn btn-sm btn-outline-primary" title="View Transaction">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                {{ $externalExpenseItems->links() }}
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-store fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No external expenses found</h4>
                            <p class="text-muted">Expenses with a Store / Shop (Repaired by) value appear here. Add expenses and set the Store / Shop field to see them in this list.</p>
                            <a href="{{ route('expenses-inventory', ['section' => 'expenses']) }}" class="btn btn-primary">
                                <i class="fas fa-file-invoice-dollar me-1"></i>Expense Transactions
                            </a>
                        </div>
                    </div>
                @endif

            @elseif($section == 'tools-purchase')
                <!-- Purchase Inventory Section (Mechanic Tools / Expenses) -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="fas fa-tools me-2"></i>Mechanic Tools / Purchase Inventory
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0 flex-wrap">
                        <button type="button" class="btn btn-primary mb-2 mb-md-0" onclick="openAddToolModal()">
                            <i class="fas fa-plus me-1"></i>Add Inventory
                        </button>
                        <a href="{{ route('expenses-inventory', ['section' => 'tools-current']) }}" class="btn btn-outline-primary ms-md-2 mb-2 mb-md-0">
                            <i class="fas fa-boxes me-1"></i>Current Inventory
                        </a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary ms-md-2 mb-2 mb-md-0">
                            <i class="fas fa-home me-1"></i>Back to Main Menu
                        </a>
                    </div>
                </div>

                @if(!$groupedTools->isEmpty())
                    @php
                        $grandTotal = $groupedTools->sum(fn($tools) => $tools->sum('amount'));
                        $totalEntries = $groupedTools->sum(fn($tools) => $tools->count());
                    @endphp
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="text-muted small text-uppercase fw-semibold">Purchase dates</div>
                                    <div class="fs-4 fw-bold">{{ $groupedTools->count() }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100">
                                <div class="card-body">
                                    <div class="text-muted small text-uppercase fw-semibold">Total items</div>
                                    <div class="fs-4 fw-bold">{{ $totalEntries }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card h-100 border-primary">
                                <div class="card-body">
                                    <div class="text-muted small text-uppercase fw-semibold">Total amount</div>
                                    <div class="fs-4 fw-bold text-primary">₱{{ number_format($grandTotal, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($groupedTools->isEmpty())
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No purchases yet</h4>
                            <p class="text-muted mb-4">Start recording mechanic tool purchases by date. Each entry can include tool name, quantity, and amount.</p>
                            <button type="button" class="btn btn-primary" onclick="openAddToolModal()">
                                <i class="fas fa-plus me-1"></i>Add First Purchase
                            </button>
                        </div>
                    </div>
                @else
                    @foreach($groupedTools->sortKeysDesc() as $date => $tools)
                        @php
                            $totalForDate = $dateTotals[$date] ?? $tools->sum('amount');
                            $dayName = \Carbon\Carbon::parse($date)->format('l');
                        @endphp
                        <div class="card mb-4">
                            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div>
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-calendar-day me-2"></i>{{ \Carbon\Carbon::parse($date)->format('F j, Y') }}
                                    </h5>
                                    <small class="text-muted">{{ $dayName }} · {{ $tools->count() }} item(s)</small>
                                </div>
                                <span class="badge bg-primary fs-6">
                                    Day total: ₱{{ number_format($totalForDate, 2) }}
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 4rem;">#</th>
                                                <th>Tool name</th>
                                                <th style="width: 6rem;">Qty</th>
                                                <th style="width: 10rem;">Amount</th>
                                                <th class="text-end" style="width: 8rem;">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($tools as $index => $tool)
                                                <tr data-tool-id="{{ $tool->id }}">
                                                    <td>{{ $index + 1 }}</td>
                                                    <td><strong>{{ $tool->name }}</strong></td>
                                                    <td>{{ $tool->quantity }}</td>
                                                    <td><strong>₱{{ number_format($tool->amount, 2) }}</strong></td>
                                                    <td class="text-end">
                                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openEditToolModal({{ $tool->id }})" title="Edit">
                                                            <i class="fas fa-pen"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteTool({{ $tool->id }})" title="Delete">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif

            @elseif($section == 'tools-current')
                <!-- Current Tools Inventory Section -->
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">
                        <i class="fas fa-boxes me-2"></i>Current Tools Inventory
                    </h1>
                    <div class="btn-toolbar mb-2 mb-md-0 flex-wrap">
                        <button type="button" class="btn btn-success mb-2 mb-md-0" onclick="openAddToolModal()">
                            <i class="fas fa-plus me-1"></i>Add Item to Inventory
                        </button>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary ms-md-2 mb-2 mb-md-0">
                            <i class="fas fa-home me-1"></i>Back to Main Menu
                        </a>
                    </div>
                </div>

                @if($currentInventory->count() > 0)
                    <div class="card">
                        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-boxes me-2"></i>Current Stock Summary
                            </h5>
                            <span class="badge bg-light text-dark">
                                <span id="current_inventory_count">{{ $currentInventory->count() }}</span> Item(s)
                            </span>
                        </div>
                        <div class="card-body">
                            <!-- Search Box -->
                            <div class="mb-3">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="current_tools_search" placeholder="Search tools by name..." autocomplete="off">
                                    <button type="button" class="btn btn-outline-secondary" id="clear_current_search" style="display: none;">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Type to filter tools in the list below</small>
                            </div>
                            
                            <div class="table-responsive">
                                <table class="table table-striped table-hover" id="current_tools_table" style="width: 100%; table-layout: auto;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Tool Name</th>
                                            <th>Total Quantity</th>
                                            <th>First Acquired</th>
                                            <th>Last Acquired</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="current_tools_tbody">
                                        @foreach($currentInventory as $index => $item)
                                            <tr data-item-name="{{ strtolower($item['name']) }}" class="tool-row">
                                                <td class="row-number">{{ $index + 1 }}</td>
                                                <td><strong>{{ $item['name'] }}</strong></td>
                                                <td><span class="badge bg-primary">{{ $item['total_quantity'] }}</span></td>
                                                <td>{{ \Carbon\Carbon::parse($item['first_acquired'])->format('M d, Y') }}</td>
                                                <td>{{ \Carbon\Carbon::parse($item['last_acquired'])->format('M d, Y') }}</td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-success" onclick="openAddMoreModal('{{ addslashes($item['name']) }}')" title="Add More Quantity">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-info" onclick="viewItemHistory('{{ addslashes($item['name']) }}')" title="View Purchase History">
                                                        <i class="fas fa-history"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <th colspan="2">Total</th>
                                            <th><span class="badge bg-success">{{ $currentInventory->sum('total_quantity') }}</span></th>
                                            <th colspan="3"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                            <h4 class="text-muted">No tools in inventory</h4>
                            <p class="text-muted mb-4">Add items to start building your inventory.</p>
                            <button type="button" class="btn btn-success" onclick="openAddToolModal()">
                                <i class="fas fa-plus me-1"></i>Add First Item
                            </button>
                        </div>
                    </div>
                @endif
            @endif
        </main>
    </div>
</div>

<!-- Edit Transaction Modal -->
<div class="modal fade" id="editTransactionModal" tabindex="-1" aria-labelledby="editTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editTransactionModalLabel">
                    <i class="fas fa-edit me-2"></i>Edit Transaction Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editTransactionForm">
                    @csrf
                    <input type="hidden" id="edit_transaction_id" name="transaction_id">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="edit_transaction_date" class="form-label">Transaction Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="edit_transaction_date" name="transaction_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_starting_cash" class="form-label">Starting Cash Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control" id="edit_starting_cash" name="starting_cash" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_added_cash" class="form-label">Added Cash <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" class="form-control" id="edit_added_cash" name="added_cash" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" onclick="saveTransactionChanges()">
                    <i class="fas fa-save me-1"></i>Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Tool Modal -->
<div class="modal fade" id="toolModal" tabindex="-1" aria-labelledby="toolModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="toolModalLabel">
                    <i class="fas fa-shopping-cart me-2"></i>Add Inventory
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="toolForm">
                    @csrf
                    <input type="hidden" id="tool_id" name="tool_id">
                    <div class="mb-3">
                        <label for="tool_name" class="form-label">Name of Tool <span class="text-danger">*</span></label>
                        
                        <!-- Search box for filtering tools -->
                        <div class="mb-2">
                            <input type="text" class="form-control form-control-sm" id="tool_search_input" placeholder="Search tools..." autocomplete="off">
                        </div>
                        
                        <!-- Checkbox list container (same style as expense description) -->
                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                            <div id="tool_list_container" class="tool-checkboxes">
                                <!-- Tools will be loaded here -->
                                <div class="p-3 text-center text-muted">
                                    <i class="fas fa-spinner fa-spin me-2"></i>Loading tools...
                                </div>
                            </div>
                        </div>
                        
                        <!-- Add new tool input -->
                        <div class="mt-2">
                            <div class="input-group">
                                <input type="text" class="form-control" id="tool_name_new" placeholder="Add new tool..." onkeypress="if(event.key === 'Enter') { event.preventDefault(); addNewTool(); }">
                                <button type="button" class="btn btn-outline-primary" onclick="addNewTool()">
                                    <i class="fas fa-plus me-1"></i>Add
                                </button>
                            </div>
                        </div>
                        
                        <!-- Hidden input for selected tool -->
                        <input type="hidden" id="tool_name" name="name" required>
                        
                        <small class="form-text text-muted d-block mt-2">Select an existing tool from the list above or add a new one</small>
                    </div>
                    <div class="mb-3">
                        <label for="tool_quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="tool_quantity" name="quantity" min="1" value="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="tool_amount" class="form-label">Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control" id="tool_amount" name="amount" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="tool_date_acquired" class="form-label">Date Acquired <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="tool_date_acquired" name="date_acquired" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" onclick="saveTool()">
                    <i class="fas fa-save me-1"></i>Save Tool
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add More Quantity Modal (for existing items) -->
<div class="modal fade" id="addMoreModal" tabindex="-1" aria-labelledby="addMoreModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addMoreModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>Add More Quantity
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addMoreForm">
                    @csrf
                    <input type="hidden" id="add_more_tool_name" name="tool_name">
                    <div class="mb-3">
                        <label class="form-label">Tool Name</label>
                        <input type="text" class="form-control" id="add_more_display_name" readonly style="background-color: #f8f9fa;">
                    </div>
                    <div class="mb-3">
                        <label for="add_more_quantity" class="form-label">Additional Quantity <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="add_more_quantity" name="quantity" min="1" value="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="add_more_amount" class="form-label">Amount <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control" id="add_more_amount" name="amount" step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="add_more_date_acquired" class="form-label">Date Acquired <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="add_more_date_acquired" name="date_acquired" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-success" onclick="saveAddMore()">
                    <i class="fas fa-save me-1"></i>Add to Inventory
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Item History Modal -->
<div class="modal fade" id="itemHistoryModal" tabindex="-1" aria-labelledby="itemHistoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="itemHistoryModalLabel">
                    <i class="fas fa-history me-2"></i>Purchase History
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="itemHistoryContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('styles')
<style>
/* Ensure navbar is above sidebar */
.navbar {
    z-index: 1030 !important;
    position: sticky !important;
    top: 0;
}

.sidebar {
    position: fixed;
    top: 56px;
    bottom: 0;
    left: 0;
    z-index: 1000;
    padding: 48px 0 0;
    box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
    overflow-y: auto;
    background-color: #f8f9fa;
    width: 240px;
    transition: transform 0.3s ease-in-out;
    transform: translateX(-100%);
}

.sidebar.collapsed {
    transform: translateX(-100%);
}

.sidebar .nav-link {
    color: #333;
    padding: 0.75rem 1rem;
    border-radius: 0.25rem;
    margin: 0.125rem 0.5rem;
    transition: all 0.2s;
}

.sidebar .nav-link:hover {
    background-color: rgba(0, 0, 0, 0.05);
    color: #007bff;
}

.sidebar .nav-link.active {
    color: #fff;
    font-weight: 600;
    background-color: #007bff;
}

.sidebar .nav-link.active:hover {
    background-color: #0056b3;
}

.sidebar-heading {
    font-size: .75rem;
    text-transform: uppercase;
    font-weight: 600;
    padding: 0.5rem 1rem;
}

main {
    margin-left: 0;
    /* Padding is now controlled globally in layouts/app.blade.php - 30px left/right */
    padding-top: 1rem;
    padding-bottom: 1rem;
    transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out;
    width: 100%;
    min-height: calc(100vh - 56px);
    box-sizing: border-box;
    max-width: 100%;
}

.container-fluid {
    /* Padding is now controlled globally in layouts/app.blade.php - 30px */
    margin-left: 0;
    margin-right: 0;
    max-width: 100%;
    overflow-x: hidden;
}

body {
    overflow-x: hidden;
    max-width: 100vw;
}

html {
    overflow-x: hidden;
    max-width: 100vw;
}

.card {
    max-width: 100%;
    overflow-x: hidden;
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

@media (min-width: 768px) {
    main {
        margin-left: 0;
        width: 100%;
        /* Padding is now controlled globally in layouts/app.blade.php - 30px left/right */
        padding-top: 1rem;
        padding-bottom: 1rem;
        max-width: 100vw;
        box-sizing: border-box;
    }
    
    main.sidebar-open {
        margin-left: 240px;
        width: calc(100% - 240px);
        max-width: calc(100vw - 240px);
    }
    
    .sidebar:not(.collapsed) {
        transform: translateX(0);
    }
    
    .sidebar.collapsed {
        transform: translateX(-100%);
    }
    
    .container-fluid {
        padding-left: 0;
        padding-right: 0;
        margin-left: 0;
        margin-right: 0;
        max-width: 100vw;
    }
}

@media (max-width: 767.98px) {
    .navbar {
        z-index: 1030 !important;
    }
    
    .sidebar {
        position: fixed;
        top: 56px;
        width: 280px;
        max-width: 80vw;
        z-index: 1020;
    }
    
    .sidebar.collapsed {
        transform: translateX(-100%);
    }
    
    main {
        margin-left: 0;
        width: 100%;
        padding: 1rem;
        transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out;
    }
    
    .container-fluid {
        padding-left: 0;
        padding-right: 0;
    }
    
    /* Overlay for mobile when sidebar is open */
    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 56px;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1040;
    }
    
    .sidebar-overlay.show {
        display: block;
    }
}

/* Autocomplete styles */
.position-relative {
    position: relative;
}

.suggestions-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    max-height: 200px;
    overflow-y: auto;
    z-index: 9999;
    margin-top: 2px;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    width: 100%;
    box-sizing: border-box;
}

.suggestion-item {
    padding: 0.5rem 0.75rem;
    cursor: pointer;
    border-bottom: 1px solid #f8f9fa;
    transition: background-color 0.15s ease-in-out;
}

.suggestion-item:hover {
    background-color: #f8f9fa;
}

.suggestion-item:last-child {
    border-bottom: none;
}

.suggestion-item.highlighted {
    background-color: #e9ecef;
}

.suggestion-item-new:hover {
    background-color: #cfe2ff !important;
    cursor: pointer;
}

/* Tool Select Dropdown Styles */
.tool-select-trigger {
    background-color: #fff;
    border: 1px solid #ced4da;
    padding: 0.375rem 0.75rem;
    cursor: pointer;
    min-height: 38px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.tool-select-trigger:hover {
    border-color: #86b7fe;
}

.tool-select-trigger:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.tool-select-dropdown {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border: 1px solid #dee2e6;
}

.tool-item-checkbox {
    cursor: pointer;
    padding: 0.5rem 1rem;
    border: none;
    border-bottom: 1px solid #f8f9fa;
    transition: background-color 0.15s ease;
}

.tool-item-checkbox:hover {
    background-color: #f8f9fa;
}

.tool-item-checkbox.selected {
    background-color: #e7f3ff;
}

.tool-item-checkbox input[type="checkbox"] {
    margin-right: 0.75rem;
    cursor: pointer;
}

.tool-item-checkbox label {
    margin: 0;
    cursor: pointer;
    width: 100%;
    display: flex;
    align-items: center;
}

.tool-item-checkbox.hidden {
    display: none;
}

.tool-listbox-container {
    border: 1px solid #ced4da;
    background-color: #fff;
}

.tool-listbox-container:focus-within {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Sidebar toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    if (!sidebar || !mainContent) {
        console.error('Sidebar or mainContent element not found');
        return;
    }
    
    // ALWAYS start with sidebar closed by default
    // The sidebar HTML already has 'collapsed' class, so it should be hidden
    // Force it closed to ensure it's hidden
    sidebar.classList.add('collapsed');
    mainContent.classList.remove('sidebar-open');
    
    // Don't restore from localStorage - always start closed
    // User can toggle it open if they want, and that preference will be saved
    
    // Mobile: always start collapsed
    if (window.innerWidth < 768) {
        if (sidebarOverlay) sidebarOverlay.classList.remove('show');
    }
    
    // Toggle sidebar
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            toggleSidebar();
        });
    }
    
    // Close sidebar when clicking overlay (mobile)
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            if (window.innerWidth < 768) {
                toggleSidebar();
            }
        });
    }
    
    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            // On resize, maintain current sidebar state (don't restore from localStorage)
            const isCurrentlyCollapsed = sidebar.classList.contains('collapsed');
            
            if (window.innerWidth >= 768) {
                // Desktop: maintain current state
                if (isCurrentlyCollapsed) {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.remove('sidebar-open');
                } else {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.add('sidebar-open');
                }
                if (sidebarOverlay) sidebarOverlay.classList.remove('show');
            } else {
                // Mobile: always collapsed
                sidebar.classList.add('collapsed');
                mainContent.classList.remove('sidebar-open');
                if (sidebarOverlay) sidebarOverlay.classList.remove('show');
            }
        }, 250);
    });
    
    function toggleSidebar() {
        const isCollapsed = sidebar.classList.contains('collapsed');
        
        if (isCollapsed) {
            // Open sidebar
            sidebar.classList.remove('collapsed');
            if (window.innerWidth >= 768) {
                mainContent.classList.add('sidebar-open');
            }
            localStorage.setItem('expensesSidebarOpen', 'true');
            
            // Show overlay on mobile
            if (window.innerWidth < 768 && sidebarOverlay) {
                sidebarOverlay.classList.add('show');
            }
        } else {
            // Close sidebar
            sidebar.classList.add('collapsed');
            if (window.innerWidth >= 768) {
                mainContent.classList.remove('sidebar-open');
            }
            localStorage.setItem('expensesSidebarOpen', 'false');
            
            // Hide overlay on mobile
            if (window.innerWidth < 768 && sidebarOverlay) {
                sidebarOverlay.classList.remove('show');
            }
        }
    }
    
    // Current Tools Inventory Search Functionality
    const currentToolsSearch = document.getElementById('current_tools_search');
    const clearCurrentSearch = document.getElementById('clear_current_search');
    
    if (currentToolsSearch && clearCurrentSearch) {
        let searchTimeout;
        
        currentToolsSearch.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                filterCurrentTools(query);
                
                // Show/hide clear button
                if (query.length > 0) {
                    clearCurrentSearch.style.display = 'block';
                } else {
                    clearCurrentSearch.style.display = 'none';
                }
            }, 200);
        });
        
        clearCurrentSearch.addEventListener('click', function() {
            currentToolsSearch.value = '';
            currentToolsSearch.focus();
            filterCurrentTools('');
            clearCurrentSearch.style.display = 'none';
        });
        
        function filterCurrentTools(query) {
            const rows = document.querySelectorAll('#current_tools_tbody .tool-row');
            let visibleCount = 0;
            
            rows.forEach((row, index) => {
                const toolName = row.getAttribute('data-item-name');
                const matches = !query || toolName.includes(query);
                
                if (matches) {
                    row.style.display = '';
                    visibleCount++;
                    // Update row number
                    const rowNumber = row.querySelector('.row-number');
                    if (rowNumber) {
                        rowNumber.textContent = visibleCount;
                    }
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Update count badge
            const countBadge = document.getElementById('current_inventory_count');
            if (countBadge) {
                countBadge.textContent = visibleCount;
            }
            
            // Show no results message if needed
            let noResultsMsg = document.getElementById('current_tools_no_results');
            if (visibleCount === 0 && query.length > 0) {
                if (!noResultsMsg) {
                    noResultsMsg = document.createElement('tr');
                    noResultsMsg.id = 'current_tools_no_results';
                    noResultsMsg.className = 'table-warning';
                    noResultsMsg.innerHTML = `
                        <td colspan="6" class="text-center py-4">
                            <i class="fas fa-search me-2"></i>No tools found matching "<strong>${query}</strong>"
                        </td>
                    `;
                    const tbody = document.getElementById('current_tools_tbody');
                    if (tbody) {
                        tbody.appendChild(noResultsMsg);
                    }
                }
            } else {
                if (noResultsMsg) {
                    noResultsMsg.remove();
                }
            }
        }
    }
});

// Transaction editing functions
function openEditTransactionModal(transactionId) {
    // Show loading
    Swal.fire({
        title: 'Loading...',
        text: 'Please wait while we load the transaction data.',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Fetch transaction data from server
    fetch(`/expenses/${transactionId}`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Failed to load transaction');
        }
        return response.json();
    })
    .then(data => {
        Swal.close();
        if (data.success && data.transaction) {
            const transaction = data.transaction;
            document.getElementById('edit_transaction_id').value = transactionId;
            document.getElementById('edit_transaction_date').value = transaction.transaction_date;
            document.getElementById('edit_starting_cash').value = parseFloat(transaction.starting_cash).toFixed(2);
            document.getElementById('edit_added_cash').value = parseFloat(transaction.added_cash).toFixed(2);
            
            const modal = new bootstrap.Modal(document.getElementById('editTransactionModal'));
            modal.show();
        } else {
            throw new Error('Invalid transaction data received');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.close();
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to load transaction data. Please refresh the page and try again.',
            confirmButtonColor: '#dc3545'
        });
    });
}

function parseDateFromDisplay(dateStr) {
    const months = {
        'Jan': '01', 'Feb': '02', 'Mar': '03', 'Apr': '04', 'May': '05', 'Jun': '06',
        'Jul': '07', 'Aug': '08', 'Sep': '09', 'Oct': '10', 'Nov': '11', 'Dec': '12'
    };
    
    const parts = dateStr.split(' ');
    if (parts.length === 3) {
        const month = months[parts[0]];
        const day = parts[1].replace(',', '').padStart(2, '0');
        const year = parts[2];
        return `${year}-${month}-${day}`;
    }
    return '';
}

function saveTransactionChanges() {
    const form = document.getElementById('editTransactionForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const transactionId = document.getElementById('edit_transaction_id').value;
    const formData = {
        transaction_date: document.getElementById('edit_transaction_date').value,
        starting_cash: parseFloat(document.getElementById('edit_starting_cash').value),
        added_cash: parseFloat(document.getElementById('edit_added_cash').value),
    };
    
    Swal.fire({
        title: 'Update Transaction?',
        text: 'Are you sure you want to update this transaction?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Update!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Updating Transaction...',
                text: 'Please wait while we update the transaction.',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(`/expenses/${transactionId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Failed to update transaction');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        confirmButtonColor: '#28a745',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('editTransactionModal'));
                        if (modal) {
                            modal.hide();
                        }
                        location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Failed to update transaction');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.message || 'Failed to update transaction',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
}

// Tool management functions
let isEditMode = false;
let allTools = []; // Array to store all tools, similar to vehicleCategories

function openAddToolModal() {
    isEditMode = false;
    document.getElementById('toolModalLabel').innerHTML = '<i class="fas fa-shopping-cart me-2"></i>Add Inventory';
    document.getElementById('toolForm').reset();
    document.getElementById('tool_id').value = '';
    document.getElementById('tool_date_acquired').value = new Date().toISOString().split('T')[0];
    
    // Reset tool selection
    resetToolSelection();
    
    const modal = new bootstrap.Modal(document.getElementById('toolModal'));
    
    // Initialize tool list after modal is shown
    modal._element.addEventListener('shown.bs.modal', function() {
        setTimeout(() => {
            loadAllTools();
            initToolListbox();
        }, 100);
    }, { once: true });
    
    modal.show();
}

function resetToolSelection() {
    const hiddenInput = document.getElementById('tool_name');
    const newToolInput = document.getElementById('tool_name_new');
    
    // Uncheck all checkboxes
    document.querySelectorAll('#tool_list_container input[type="checkbox"]').forEach(cb => {
        cb.checked = false;
    });
    
    if (hiddenInput) hiddenInput.value = '';
    if (newToolInput) {
        newToolInput.value = '';
    }
}

function initToolListbox() {
    const toolSearchInput = document.getElementById('tool_search_input');
    
    if (!toolSearchInput) {
        console.error('Tool search input not found');
        return;
    }
    
    // Search functionality
    let searchTimeout;
    toolSearchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            filterTools(query);
        }, 200);
    });
}

// Add new tool function (similar to addNewCategory)
function addNewTool() {
    const input = document.getElementById('tool_name_new');
    const toolName = input.value.trim();
    
    if (!toolName) {
        Swal.fire({
            icon: 'warning',
            title: 'Validation Error',
            text: 'Please enter a tool name.',
            confirmButtonColor: '#dc3545'
        });
        return;
    }
    
    // Check if tool already exists
    if (allTools.some(tool => tool.toLowerCase() === toolName.toLowerCase())) {
        Swal.fire({
            icon: 'warning',
            title: 'Tool Exists',
            text: 'This tool already exists in the list.',
            confirmButtonColor: '#dc3545'
        });
        return;
    }
    
    // Add to local array
    allTools.push(toolName);
    
    // Re-render tool checkboxes
    renderToolList(allTools);
    
    // Clear input
    input.value = '';
    
    // Select the newly added tool
    const hiddenInput = document.getElementById('tool_name');
    if (hiddenInput) {
        hiddenInput.value = toolName;
    }
    
    // Check the checkbox for the new tool
    const checkboxes = document.querySelectorAll('#tool_list_container input[type="checkbox"]');
    checkboxes.forEach(cb => {
        if (cb.value === toolName) {
            cb.checked = true;
        } else {
            cb.checked = false;
        }
    });
}

function loadAllTools() {
    const toolListContainer = document.getElementById('tool_list_container');
    
    // Show loading state
    if (toolListContainer) {
        toolListContainer.innerHTML = '<div class="p-3 text-center text-muted"><i class="fas fa-spinner fa-spin me-2"></i>Loading tools...</div>';
    }
    
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const headers = {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    };
    
    if (csrfToken) {
        headers['X-CSRF-TOKEN'] = csrfToken;
    }
    
    // Fetch all tools (using search with empty query or get all)
    const searchUrl = '/api/tools/search?q=';
    console.log('Loading tools from:', searchUrl);
    
    fetch(searchUrl, {
        method: 'GET',
        headers: headers,
        credentials: 'same-origin'
    })
        .then(response => {
            console.log('Load tools response status:', response.status, response.statusText);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                // Try to get error message from response
                return response.text().then(text => {
                    console.error('Error response text:', text);
                    try {
                        const errorJson = JSON.parse(text);
                        throw new Error(errorJson.message || `HTTP error! status: ${response.status}`);
                    } catch (parseError) {
                        throw new Error(`HTTP error! status: ${response.status} - ${text.substring(0, 100)}`);
                    }
                });
            }
            return response.json();
        })
        .then(tools => {
            console.log('Tools received from API:', tools);
            console.log('Tools type:', typeof tools);
            console.log('Is array?', Array.isArray(tools));
            console.log('Tools length:', Array.isArray(tools) ? tools.length : 'N/A');
            
            if (Array.isArray(tools)) {
                // Extract tool names and store in allTools array
                allTools = tools.map(tool => {
                    if (typeof tool === 'string') {
                        return tool;
                    } else if (tool && tool.name) {
                        return tool.name;
                    }
                    return String(tool);
                }).filter(name => name && name.trim()); // Filter out empty names
                
                console.log('Extracted tool names:', allTools);
                
                // Remove duplicates and sort
                allTools = [...new Set(allTools)].sort();
                
                console.log('Final tools list:', allTools);
                
                // Render tools
                renderToolList(allTools);
            } else {
                console.error('Unexpected tools format:', tools);
                console.error('Tools type:', typeof tools);
                allTools = [];
                if (toolListContainer) {
                    toolListContainer.innerHTML = '<div class="p-3 text-center text-danger">Error: Invalid response format from server.</div>';
                }
            }
        })
        .catch(error => {
            console.error('Error loading tools:', error);
            console.error('Error stack:', error.stack);
            allTools = [];
            // Show error message but still allow adding new tools
            if (toolListContainer) {
                toolListContainer.innerHTML = `<div class="p-3 text-center text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Error loading tools: ${error.message}
                    <br><small>Check browser console for details. You can still add new tools below.</small>
                </div>`;
            }
        });
}


function renderToolList(tools) {
    const toolListContainer = document.getElementById('tool_list_container');
    
    if (!toolListContainer) {
        console.error('tool_list_container not found');
        return;
    }
    
    toolListContainer.innerHTML = '';
    
    if (!tools || !Array.isArray(tools) || tools.length === 0) {
        toolListContainer.innerHTML = '<p class="text-muted">No tools available. Add a new tool below.</p>';
        return;
    }
    
    // Render tools using same style as expense categories (form-check)
    tools.forEach((toolName, index) => {
        const toolItem = document.createElement('div');
        toolItem.className = 'form-check';
        toolItem.setAttribute('data-tool-name', toolName);
        
        const checkboxId = `tool_checkbox_${index}`;
        toolItem.innerHTML = `
            <input class="form-check-input" type="checkbox" value="${toolName}" id="${checkboxId}" name="tool_selection">
            <label class="form-check-label" for="${checkboxId}">
                ${toolName}
            </label>
        `;
        
        // Handle checkbox change - single selection (like radio but using checkbox)
        const checkbox = toolItem.querySelector('input[type="checkbox"]');
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                // Uncheck all other checkboxes
                document.querySelectorAll('#tool_list_container input[type="checkbox"]').forEach(cb => {
                    if (cb !== checkbox) {
                        cb.checked = false;
                    }
                });
                
                // Set hidden input value
                const hiddenInput = document.getElementById('tool_name');
                if (hiddenInput) {
                    hiddenInput.value = toolName;
                }
            } else {
                // If unchecked, clear hidden input
                const hiddenInput = document.getElementById('tool_name');
                if (hiddenInput) {
                    hiddenInput.value = '';
                }
            }
        });
        
        toolListContainer.appendChild(toolItem);
    });
    
    console.log('Successfully rendered', tools.length, 'tools');
}

function filterTools(query) {
    const toolItems = document.querySelectorAll('#tool_list_container .form-check');
    
    toolItems.forEach(item => {
        const toolName = item.getAttribute('data-tool-name').toLowerCase();
        if (toolName.includes(query)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
    
    // Show message if no results
    const visibleItems = document.querySelectorAll('#tool_list_container .form-check:not([style*="display: none"])');
    const toolListContainer = document.getElementById('tool_list_container');
    
    // Remove existing no-results message
    const existingMsg = toolListContainer.querySelector('.no-results-message');
    if (existingMsg) existingMsg.remove();
    
    if (visibleItems.length === 0 && query) {
        const noResults = document.createElement('div');
        noResults.className = 'p-3 text-muted text-center no-results-message';
        noResults.textContent = 'No tools found matching your search';
        toolListContainer.appendChild(noResults);
    }
}


function initToolNameAutocomplete() {
    const toolNameInput = document.getElementById('tool_name');
    const suggestionsContainer = document.getElementById('tool_name_suggestions');
    
    if (!toolNameInput || !suggestionsContainer) {
        console.error('Tool name autocomplete: Input or container not found');
        return;
    }
    
    if (toolNameAutocompleteInitialized) {
        console.log('Tool name autocomplete already initialized');
        return;
    }
    
    console.log('Initializing tool name autocomplete');
    toolNameAutocompleteInitialized = true;
    
    // Use the input directly without cloning to preserve event listeners
    const newInput = toolNameInput;
    
    // Search on input
    const inputHandler = function(e) {
        const query = this.value.trim();
        toolHighlightedIndex = -1;
        
        clearTimeout(toolSearchTimeout);
        toolSearchTimeout = setTimeout(() => {
            if (query.length >= 1) {
                console.log('Searching for:', query);
                searchToolNames(query, newInput);
            } else {
                hideToolSuggestions();
            }
        }, 300);
    };
    
    // Remove existing listener if any, then add new one
    newInput.removeEventListener('input', inputHandler);
    newInput.addEventListener('input', inputHandler, false);
    
    // Handle keyboard navigation
    newInput.addEventListener('keydown', function(e) {
        const items = suggestionsContainer.querySelectorAll('.suggestion-item:not(.text-muted)');
        
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            toolHighlightedIndex = Math.min(toolHighlightedIndex + 1, items.length - 1);
            updateHighlight(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            toolHighlightedIndex = Math.max(toolHighlightedIndex - 1, -1);
            updateHighlight(items);
        } else if (e.key === 'Enter' && toolHighlightedIndex >= 0 && items[toolHighlightedIndex]) {
            e.preventDefault();
            items[toolHighlightedIndex].click();
        } else if (e.key === 'Escape') {
            hideToolSuggestions();
        }
    }, false);
    
    // Close suggestions when clicking outside (only once per initialization)
    if (!window.toolAutocompleteClickHandler) {
        window.toolAutocompleteClickHandler = function(e) {
            const toolNameInput = document.getElementById('tool_name');
            const suggestionsContainer = document.getElementById('tool_name_suggestions');
            if (toolNameInput && suggestionsContainer && 
                !e.target.closest('.position-relative') && 
                !e.target.closest('#tool_name_suggestions')) {
                if (suggestionsContainer) {
                    suggestionsContainer.style.display = 'none';
                }
            }
        };
        document.addEventListener('click', window.toolAutocompleteClickHandler);
    }
    
    function searchToolNames(query, inputElement) {
        if (!query || query.length < 1) {
            hideToolSuggestions();
            return;
        }
        
        console.log('Fetching tools for query:', query);
        const searchUrl = `/api/tools/search?q=${encodeURIComponent(query)}`;
        console.log('Search URL:', searchUrl);
        
        // Get CSRF token if available
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const headers = {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        };
        
        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken;
        }
        
        fetch(searchUrl, {
            method: 'GET',
            headers: headers,
            credentials: 'same-origin',
            cache: 'no-cache'
        })
            .then(response => {
                console.log('Search response status:', response.status, response.statusText);
                if (!response.ok) {
                    // If 401/403, might be auth issue, but still show "add as new" option
                    if (response.status === 401 || response.status === 403) {
                        console.warn('Auth issue, but allowing to add as new');
                        showToolSuggestions([], inputElement);
                        return null;
                    }
                    return response.text().then(text => {
                        console.error('Search failed response:', text);
                        // Still show "add as new" option even on error
                        showToolSuggestions([], inputElement);
                        return null;
                    });
                }
                return response.json();
            })
            .then(tools => {
                if (tools === null) return; // Already handled in error case
                console.log('Search results received:', tools);
                if (Array.isArray(tools)) {
                    showToolSuggestions(tools, inputElement);
                } else {
                    console.error('Unexpected response format:', tools);
                    showToolSuggestions([], inputElement);
                }
            })
            .catch(error => {
                console.error('Error searching tools:', error);
                // Instead of showing error, show "add as new" option
                showToolSuggestions([], inputElement);
            });
    }
    
    function showToolSuggestions(tools, inputElement) {
        if (!suggestionsContainer) return;
        
        suggestionsContainer.innerHTML = '';
        
        if (!tools || !Array.isArray(tools) || tools.length === 0) {
            // Show message and add button
            const noResult = document.createElement('div');
            noResult.className = 'suggestion-item text-muted';
            noResult.innerHTML = '<i class="fas fa-info-circle me-2"></i>No existing tools found.';
            suggestionsContainer.appendChild(noResult);
            
            // Add "Add as new tool" option
            const addNewDiv = document.createElement('div');
            addNewDiv.className = 'suggestion-item suggestion-item-new';
            addNewDiv.style.backgroundColor = '#e7f3ff';
            addNewDiv.style.fontWeight = 'bold';
            const currentValue = inputElement.value.trim();
            addNewDiv.innerHTML = `<i class="fas fa-plus-circle me-2 text-success"></i>Add "${currentValue}" as new tool`;
            addNewDiv.addEventListener('click', () => {
                // Keep the current value, just close suggestions
                hideToolSuggestions();
                inputElement.focus();
            });
            suggestionsContainer.appendChild(addNewDiv);
        } else {
            tools.forEach((tool, index) => {
                const div = document.createElement('div');
                div.className = 'suggestion-item';
                const toolName = (tool && tool.name) ? tool.name : (typeof tool === 'string' ? tool : String(tool));
                div.innerHTML = `<i class="fas fa-toolbox me-2 text-muted"></i>${toolName}`;
                div.addEventListener('click', () => {
                    inputElement.value = toolName;
                    hideToolSuggestions();
                    inputElement.focus();
                });
                div.addEventListener('mouseenter', () => {
                    toolHighlightedIndex = index;
                    updateHighlight(suggestionsContainer.querySelectorAll('.suggestion-item:not(.text-muted):not(.suggestion-item-new)'));
                });
                suggestionsContainer.appendChild(div);
            });
            
            // Add "Add as new tool" option even when results exist
            const currentValue = inputElement.value.trim();
            if (currentValue && !tools.some(t => {
                const toolName = (t && t.name) ? t.name : (typeof t === 'string' ? t : String(t));
                return toolName.toLowerCase() === currentValue.toLowerCase();
            })) {
                const addNewDiv = document.createElement('div');
                addNewDiv.className = 'suggestion-item suggestion-item-new';
                addNewDiv.style.backgroundColor = '#e7f3ff';
                addNewDiv.style.borderTop = '1px solid #dee2e6';
                addNewDiv.style.fontWeight = 'bold';
                addNewDiv.innerHTML = `<i class="fas fa-plus-circle me-2 text-success"></i>Add "${currentValue}" as new tool`;
                addNewDiv.addEventListener('click', () => {
                    // Keep the current value, just close suggestions
                    hideToolSuggestions();
                    inputElement.focus();
                });
                suggestionsContainer.appendChild(addNewDiv);
            }
        }
        
        suggestionsContainer.style.display = 'block';
        toolHighlightedIndex = -1;
    }
    
    function updateHighlight(items) {
        items.forEach((item, index) => {
            if (index === toolHighlightedIndex) {
                item.classList.add('highlighted');
            } else {
                item.classList.remove('highlighted');
            }
        });
    }
    
    function hideToolSuggestions() {
        if (suggestionsContainer) {
            suggestionsContainer.style.display = 'none';
        }
        toolHighlightedIndex = -1;
    }
}

function openAddMoreModal(toolName) {
    document.getElementById('add_more_tool_name').value = toolName;
    document.getElementById('add_more_display_name').value = toolName;
    document.getElementById('addMoreForm').reset();
    document.getElementById('add_more_quantity').value = 1;
    document.getElementById('add_more_date_acquired').value = new Date().toISOString().split('T')[0];
    document.getElementById('add_more_tool_name').value = toolName;
    
    const modal = new bootstrap.Modal(document.getElementById('addMoreModal'));
    modal.show();
}

function saveAddMore() {
    const form = document.getElementById('addMoreForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const formData = {
        name: document.getElementById('add_more_tool_name').value.trim(),
        quantity: parseInt(document.getElementById('add_more_quantity').value),
        amount: parseFloat(document.getElementById('add_more_amount').value),
        date_acquired: document.getElementById('add_more_date_acquired').value,
    };
    
    Swal.fire({
        title: 'Add to Inventory?',
        text: 'This will create a new purchase entry for this item.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Add!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Adding Item...',
                text: 'Please wait...',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('/api/tools', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Failed to add item');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        confirmButtonColor: '#28a745',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        location.reload();
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.message || 'Failed to add item',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
}

function viewItemHistory(toolName) {
    document.getElementById('itemHistoryModalLabel').innerHTML = `<i class="fas fa-history me-2"></i>Purchase History: ${toolName}`;
    document.getElementById('itemHistoryContent').innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    
    const modal = new bootstrap.Modal(document.getElementById('itemHistoryModal'));
    modal.show();
    
    // Fetch tool history
    fetch(`/api/tools/history?name=${encodeURIComponent(toolName)}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.tools && data.tools.length > 0) {
            let html = '<div class="table-responsive"><table class="table table-sm table-hover table-striped"><thead class="table-light"><tr><th>Date Acquired</th><th>Quantity</th><th>Amount</th><th>Total</th></tr></thead><tbody>';
            let grandTotal = 0;
            data.tools.forEach(tool => {
                const date = new Date(tool.date_acquired);
                const formattedDate = date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                const total = parseFloat(tool.amount) * parseInt(tool.quantity);
                grandTotal += total;
                html += `<tr>
                    <td>${formattedDate}</td>
                    <td>${tool.quantity}</td>
                    <td>₱${parseFloat(tool.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    <td><strong>₱${total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong></td>
                </tr>`;
            });
            html += '</tbody><tfoot class="table-light"><tr><th colspan="3">Grand Total</th><th><strong>₱' + grandTotal.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</strong></th></tr></tfoot></table></div>';
            document.getElementById('itemHistoryContent').innerHTML = html;
        } else {
            document.getElementById('itemHistoryContent').innerHTML = '<div class="alert alert-info">No purchase history found for this item.</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('itemHistoryContent').innerHTML = '<div class="alert alert-danger">Error loading purchase history.</div>';
    });
}

function openEditToolModal(toolId) {
    isEditMode = true;
    document.getElementById('toolModalLabel').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Tool';
    
    const row = document.querySelector(`tr[data-tool-id="${toolId}"]`);
    if (!row) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Tool not found',
            confirmButtonColor: '#dc3545'
        });
        return;
    }
    
    const cells = row.querySelectorAll('td');
    if (cells.length < 4) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Could not extract tool data',
            confirmButtonColor: '#dc3545'
        });
        return;
    }
    
    document.getElementById('tool_id').value = toolId;
    document.getElementById('tool_name').value = cells[1].textContent.trim();
    document.getElementById('tool_quantity').value = parseInt(cells[2].textContent.trim());
    
    const amountText = cells[3].textContent.trim().replace('₱', '').replace(/,/g, '');
    document.getElementById('tool_amount').value = parseFloat(amountText).toFixed(2);
    
    const modal = new bootstrap.Modal(document.getElementById('toolModal'));
    modal.show();
    
    fetch(`/api/tools/${toolId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.tool) {
                document.getElementById('tool_date_acquired').value = data.tool.date_acquired;
            }
        })
        .catch(error => {
            console.error('Error fetching tool data:', error);
        });
}

function saveTool() {
    const form = document.getElementById('toolForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const formData = {
        name: document.getElementById('tool_name').value.trim(),
        quantity: parseInt(document.getElementById('tool_quantity').value),
        amount: parseFloat(document.getElementById('tool_amount').value),
        date_acquired: document.getElementById('tool_date_acquired').value,
    };
    
    const toolId = document.getElementById('tool_id').value;
    const url = toolId ? `/api/tools/${toolId}` : '/api/tools';
    const method = toolId ? 'PUT' : 'POST';
    
    Swal.fire({
        title: isEditMode ? 'Update Inventory?' : 'Add Inventory?',
        text: isEditMode ? 'Are you sure you want to update this purchase record?' : 'Are you sure you want to add this purchase?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, ' + (isEditMode ? 'Update' : 'Add') + '!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: isEditMode ? 'Updating Tool...' : 'Adding Tool...',
                text: 'Please wait...',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Failed to save tool');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        confirmButtonColor: '#28a745',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        location.reload();
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.message || 'Failed to save tool',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
}

function deleteTool(toolId) {
    Swal.fire({
        title: 'Delete Tool?',
        text: 'Are you sure you want to delete this tool? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Deleting Tool...',
                text: 'Please wait...',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch(`/api/tools/${toolId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'Failed to delete tool');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: data.message,
                        confirmButtonColor: '#28a745',
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        location.reload();
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: error.message || 'Failed to delete tool',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
}
</script>
@endsection
