@extends('layouts.app')

@section('title', 'SOA - Statement of Account - Car Empire Management System')

@section('content')
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <button class="btn btn-outline-light me-3" type="button" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('images/CAREMPIRE_LOGO.png') }}" alt="CAR EMPIRE Logo" onerror="this.style.display='none';">
        </a>
        <div class="navbar-nav ms-auto">
            <a href="{{ route('logout') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-sign-out-alt me-1"></i>Logout
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid" style="overflow-x: hidden;">
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Sidebar Navigation -->
    <nav class="sidebar collapsed" id="sidebar">
        <div class="position-sticky pt-3">
            <h6 class="sidebar-heading">
                <span>Navigation</span>
            </h6>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">
                        <i class="fas fa-home me-2"></i>Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('soa.create') }}">
                        <i class="fas fa-file-invoice me-2"></i>SOA - Statement of Account
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('expenses-inventory', ['section' => 'expenses']) }}">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Expense Transactions
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main id="mainContent">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-file-invoice me-2"></i>SOA - Statement of Account
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Home
                    </a>
                </div>
            </div>

            <div id="floatedFundsAlertWrap" class="mb-3" style="display:none;">
                <div class="alert alert-warning d-flex flex-wrap align-items-center justify-content-between gap-2 mb-0 shadow-sm">
                    <div class="me-2">
                        <i class="fas fa-water me-2"></i>
                        <strong>Floated funds</strong> — total amount: <strong id="floatedFundsTotalDisplay">₱0.00</strong>
                        <span class="text-muted small ms-1 d-inline-block">(declared starting balance was below the prior day's SOA closing)</span>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-dark" id="floatedFundsViewBtn">
                        <i class="fas fa-eye me-1"></i>View
                    </button>
                </div>
            </div>

            <!-- Payment Method Selection Accordion -->
            <div class="accordion mb-4" id="paymentMethodAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="paymentMethodHeading">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#paymentMethodCollapse" aria-expanded="true" aria-controls="paymentMethodCollapse">
                            <i class="fas fa-calendar-alt me-2"></i>Generate SOA
                        </button>
                    </h2>
                    <div id="paymentMethodCollapse" class="accordion-collapse collapse show" aria-labelledby="paymentMethodHeading" data-bs-parent="#paymentMethodAccordion">
                        <div class="accordion-body">
                    <div class="row">
                        <div class="d-none">
                            <label for="payment_method_id" class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select" id="payment_method_id" name="payment_method_id" required>
                                <option value="">-- Select Payment Method --</option>
                                @foreach($paymentMethods as $method)
                                    <option value="{{ $method->id }}">{{ $method->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3" id="dateSelectorContainer">
                            <label for="selected_date" class="form-label">Select Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="selected_date" name="selected_date" required>
                        </div>
                        <div class="col-md-3 mb-3" id="viewButtonContainer">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-primary w-100" id="viewTransactionBtn">
                                <i class="fas fa-bolt me-1"></i>Generate
                            </button>
                        </div>
                    </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Daily Budget Form -->
            <div class="card mb-4" id="dailyBudgetCard" style="display: none;">
                <div class="card-header bg-warning">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-coins me-2"></i>Starting Balance Required
                    </h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">No starting balance found for this date. Enter the starting balance, then click save to generate the SOA table.</p>
                    <div id="yesterdayBalanceSuggestionWrap" class="d-none"></div>
                    <form id="dailyBudgetForm">
                        @csrf
                        <input type="hidden" id="budget_payment_method_id" name="payment_method_id">
                        <input type="hidden" id="budget_date" name="budget_date">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="starting_balance" class="form-label">Starting Balance <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="starting_balance" name="starting_balance" step="0.01" min="0" required placeholder="0.00" value="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="added_cash" class="form-label">Initial Credit (Optional)</label>
                                <input type="number" class="form-control" id="added_cash" name="added_cash" step="0.01" min="0" placeholder="0.00" value="0">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="display_total_cash" class="form-label">Opening Total</label>
                                <input type="text" class="form-control bg-light" id="display_total_cash" readonly value="0.00">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="budget_notes" class="form-label">Notes (Optional)</label>
                                <input type="text" class="form-control" id="budget_notes" name="notes" placeholder="Additional notes">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Save Starting Balance
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Edit Starting Cash Section Accordion -->
            <div class="accordion mb-4" id="cashManagementAccordion">
                <div class="accordion-item" id="cashActionsCard" style="display: none;">
                    <h2 class="accordion-header" id="cashManagementHeading">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#cashManagementCollapse" aria-expanded="true" aria-controls="cashManagementCollapse">
                            <i class="fas fa-money-bill-wave me-2"></i>Cash Management
                        </button>
                    </h2>
                    <div id="cashManagementCollapse" class="accordion-collapse collapse show" aria-labelledby="cashManagementHeading" data-bs-parent="#cashManagementAccordion">
                        <div class="accordion-body">
                    <div class="row">
                        <div class="col-md-8 col-lg-6 mb-3">
                            <label for="edit_starting_cash" class="form-label">Edit Starting Cash</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="edit_starting_cash" step="0.01" min="0" placeholder="0.00">
                                <button type="button" class="btn btn-primary" id="updateStartingCashBtn">
                                    <i class="fas fa-save me-1"></i>Update
                                </button>
                            </div>
                        </div>
                    </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction Table -->
            <div class="card mb-4" id="transactionsCard" style="display: none;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-table me-2"></i>SOA Details
                    </h5>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-danger btn-sm" id="deleteSoaPeriodBtn" style="display: none;">
                            <i class="fas fa-trash-alt me-1"></i>Delete SOA
                        </button>
                        <button type="button" class="btn btn-success btn-sm" id="addSoaDetailBtn">
                            <i class="fas fa-plus me-1"></i>Add Detail
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div id="priorSoaBalanceBannerWrap" class="px-3 pt-3" style="display: none;"></div>
                    <div id="soaTableContainer">
                        <!-- Table will be inserted here by JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Summary Section -->
            <div class="card mb-4" id="summaryCard" style="display: none;">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calculator me-2"></i>Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="display_total_expenses" class="form-label">Total debits (SOA)</label>
                            <input type="text" class="form-control bg-light" id="display_total_expenses" readonly value="0.00">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="display_total_remaining_cash" class="form-label">Total Cash</label>
                            <input type="text" class="form-control bg-light" id="display_total_remaining_cash" readonly value="0.00">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="floatedFundsModal" tabindex="-1" aria-labelledby="floatedFundsModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="floatedFundsModalLabel"><i class="fas fa-water me-2"></i>Floated funds — where it came from</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted small mb-3">Each row is the difference saved when the starting balance for an SOA date was lower than the previous calendar day's closing balance (after totals).</p>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>SOA date (opening)</th>
                                            <th>Closing from (previous day)</th>
                                            <th class="text-end">Prior day closing</th>
                                            <th class="text-end">Declared starting</th>
                                            <th class="text-end">Floated amount</th>
                                        </tr>
                                    </thead>
                                    <tbody id="floatedFundsModalBody"></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </main>
</div>
@endsection

@section('styles')
<style>
.soa-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
    table-layout: fixed;
}

.soa-table th {
    background-color: #1f5fbf;
    color: #fff;
    font-weight: bold;
    padding: 10px;
    text-align: left;
    border: 1px solid #000;
}

.soa-table td {
    padding: 10px;
    border: 1px solid #000;
    text-align: left;
}

.soa-table .text-right {
    text-align: right;
}

.soa-table .debit {
    color: #FF0000;
    font-weight: bold;
}

.soa-table .balance {
    font-weight: bold;
}

.soa-table .starting-balance-row {
    background-color: #1f5fbf;
    color: #fff;
}

.soa-table .starting-balance-row .balance {
    color: #ffffff;
}

.soa-table tbody tr:nth-child(even) {
    background-color: #FFFFFF;
}

.soa-table tbody tr:nth-child(odd) {
    background-color: #F0F0F0;
}

.soa-table tbody tr.highlighted {
    background-color: #90EE90;
}

.soa-header-row {
    background-color: #FFFFFF;
}

.soa-header-row td {
    border: 1px solid #000;
    padding: 10px;
    font-weight: bold;
}

.soa-table .total-row td {
    background-color: #1f5fbf;
    color: #fff;
    font-weight: 700;
}

#soaTableContainer {
    overflow-x: auto;
}

/* Sidebar Styles */
.sidebar {
    position: fixed;
    top: 56px;
    left: 0;
    bottom: 0;
    z-index: 1000;
    padding: 48px 0 0;
    box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
    overflow-y: auto;
    background-color: #f8f9fa;
    width: 240px;
    transition: transform 0.3s ease-in-out;
    transform: translateX(-100%);
}

/* Ensure navbar is above sidebar */
.navbar {
    z-index: 1030 !important;
    position: sticky !important;
    top: 0;
}

.sidebar.collapsed {
    transform: translateX(-100%);
}

.sidebar:not(.collapsed) {
    transform: translateX(0);
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

#mainContent {
    margin-left: 0;
    padding-top: 1rem;
    padding-bottom: 1rem;
    transition: margin-left 0.3s ease-in-out, width 0.3s ease-in-out;
    width: 100%;
    min-height: calc(100vh - 56px);
    box-sizing: border-box;
    max-width: 100%;
}

#mainContent.sidebar-open {
    margin-left: 240px;
    width: calc(100% - 240px);
}

/* Sidebar Overlay for Mobile */
.sidebar-overlay {
    display: none;
    position: fixed;
    top: 56px;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 999;
    transition: opacity 0.3s ease-in-out;
}

.sidebar-overlay.show {
    display: block;
}

@media (max-width: 767.98px) {
    .sidebar {
        z-index: 1020;
    }
    
    .sidebar-overlay {
        z-index: 1019;
    }
    
    .navbar {
        z-index: 1030 !important;
    }
    
    #mainContent.sidebar-open {
        margin-left: 0;
        width: 100%;
    }
}

/* Clickable expense descriptions */
.soa-table td a {
    color: #0d6efd !important;
    text-decoration: none;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
}

.soa-table td a:hover {
    color: #0a58ca !important;
    text-decoration: underline;
    font-weight: 500;
}

.soa-table td a i {
    opacity: 0.7;
    transition: opacity 0.2s ease;
}

.soa-table td a:hover i {
    opacity: 1;
}

.soa-manual-edit {
    vertical-align: middle;
    line-height: 1;
}

.soa-manual-edit:hover {
    color: #0d6efd !important;
}

.soa-manual-delete {
    vertical-align: middle;
    line-height: 1;
}

.soa-manual-delete:hover {
    color: #b02a37 !important;
}

</style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// Format number with commas and 2 decimal places
function formatPrice(amount) {
    if (amount === null || amount === undefined) return '';
    return '₱' + parseFloat(amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// Format date from d-M-y to d-M-y format (already formatted from backend)
function formatDate(dateString) {
    return dateString || '';
}

function formatLongDate(dateString) {
    if (!dateString) return '';
    const dt = new Date(`${dateString}T00:00:00`);
    if (Number.isNaN(dt.getTime())) return dateString;
    return dt.toLocaleDateString('en-US', { day: 'numeric', month: 'long', year: 'numeric' });
}

function applyYesterdayStartingSuggestion(data) {
    const wrap = document.getElementById('yesterdayBalanceSuggestionWrap');
    const startingInput = document.getElementById('starting_balance');
    if (!wrap || !startingInput) {
        return;
    }

    wrap.innerHTML = '';
    wrap.classList.add('d-none');

    const hasPrior = !!(data.has_prior_soa_activity || data.has_yesterday_soa_activity);
    const priorDate = data.prior_soa_date_formatted || data.yesterday_date_formatted || formatLongDate(data.prior_soa_date || data.yesterday_date) || '';
    const priorClose = parseFloat(data.prior_soa_closing_balance ?? data.yesterday_closing_balance);
    const safe = Number.isFinite(priorClose) ? priorClose : 0;

    if (!hasPrior) {
        startingInput.value = data.starting_balance != null ? data.starting_balance : 0;
        updateCashSummary();
        wrap.classList.remove('d-none');
        wrap.innerHTML = '<div class="alert alert-secondary mb-0"><i class="fas fa-info-circle me-2"></i>No previous SOA record was found. Enter your starting balance below.</div>';
        return;
    }

    startingInput.value = safe.toFixed(2);
    updateCashSummary();

    const priceStr = formatPrice(safe);
    wrap.classList.remove('d-none');
    wrap.innerHTML = `<div class="alert alert-info mb-0"><i class="fas fa-lightbulb me-2"></i><strong>Remaining balance from last SOA (${priorDate}):</strong> <strong>${priceStr}</strong>. It is filled in as starting balance below—change it if needed, then click <strong>Save Starting Balance</strong>.</div>`;
}

function updatePriorSoaBalanceBanner(data) {
    const wrap = document.getElementById('priorSoaBalanceBannerWrap');
    if (!wrap) return;

    const hasPrior = !!(data && (data.has_prior_soa_activity || data.has_yesterday_soa_activity));
    if (!hasPrior) {
        wrap.style.display = 'none';
        wrap.innerHTML = '';
        return;
    }

    const priorDate = data.prior_soa_date_formatted || data.yesterday_date_formatted || formatLongDate(data.prior_soa_date || data.yesterday_date) || '';
    const priorClose = parseFloat(data.prior_soa_closing_balance ?? data.yesterday_closing_balance);
    const safe = Number.isFinite(priorClose) ? priorClose : 0;
    const openingToday = parseFloat(data.starting_balance || 0);
    const priceStr = formatPrice(safe);

    let extra = '';
    if (data.has_starting_balance && Math.abs(openingToday - safe) > 0.009) {
        extra = ` · Opening today: <strong>${formatPrice(openingToday)}</strong>`;
    }

    wrap.style.display = 'block';
    const debitAmount = getCarryOverDebitAmount(data);
    const showDebitAction = data.has_starting_balance && debitAmount > 0.009 && !hasCarryOverDebitEntry(data);
    const debitBtn = showDebitAction
        ? `<button type="button" class="btn btn-sm btn-danger ms-2" id="addPriorSoaDebitBtn"><i class="fas fa-plus-circle me-1"></i>Add as Debit</button>`
        : '';

    wrap.innerHTML = `<div class="alert alert-info mb-0 py-2 d-flex flex-wrap align-items-center justify-content-between gap-2"><div><i class="fas fa-wallet me-2"></i><strong>Remaining balance from last SOA (${priorDate}):</strong> <strong>${priceStr}</strong>${extra}</div>${debitBtn}</div>`;

    const btn = document.getElementById('addPriorSoaDebitBtn');
    if (btn) {
        btn.addEventListener('click', function () {
            promptCarryOverDebitIfNeeded(data, true);
        });
    }
}

function carryOverDebitPromptKey(paymentMethodId, date) {
    return `soa_carry_debit_dismissed_${paymentMethodId}_${date}`;
}

function hasCarryOverDebitEntry(data) {
    return (data.transactions || []).some((t) => {
        const desc = (t.description || '').toLowerCase();
        return desc.includes('carry-over from last soa') || desc.includes('remaining balance from last soa');
    });
}

function getCarryOverDebitAmount(data) {
    const priorClose = parseFloat(data.prior_soa_closing_balance ?? data.yesterday_closing_balance);
    if (!Number.isFinite(priorClose)) return 0;
    return Math.round(priorClose * 100) / 100;
}

function getCarryOverTotalBalance(data) {
    const openingToday = parseFloat(data.starting_balance || 0);
    const debitAmount = getCarryOverDebitAmount(data);
    if (!Number.isFinite(openingToday)) return debitAmount;
    return Math.round((openingToday + debitAmount) * 100) / 100;
}

function promptCarryOverDebitIfNeeded(data, forcePrompt = false) {
    if (!data || !data.has_starting_balance) return;

    const hasPrior = !!(data.has_prior_soa_activity || data.has_yesterday_soa_activity);
    if (!hasPrior) return;

    const priorClose = parseFloat(data.prior_soa_closing_balance ?? data.yesterday_closing_balance);
    const openingToday = parseFloat(data.starting_balance || 0);
    if (!Number.isFinite(priorClose) || priorClose <= 0) return;

    const debitAmount = getCarryOverDebitAmount(data);
    if (debitAmount <= 0) return;

    if (hasCarryOverDebitEntry(data)) return;

    const paymentMethodId = document.getElementById('payment_method_id').value;
    const selectedDate = document.getElementById('selected_date').value;
    if (!paymentMethodId || !selectedDate) return;

    const dismissKey = carryOverDebitPromptKey(paymentMethodId, selectedDate);
    if (!forcePrompt && sessionStorage.getItem(dismissKey) === '1') return;

    const priorDate = data.prior_soa_date_formatted || formatLongDate(data.prior_soa_date || data.yesterday_date) || '';
    const debitStr = formatPrice(debitAmount);
    const priorStr = formatPrice(priorClose);
    const openingStr = formatPrice(openingToday);
    const totalStr = formatPrice(getCarryOverTotalBalance(data));

    Swal.fire({
        title: 'Add as Debit?',
        html: `Last SOA (${priorDate}) closed with <strong>${priorStr}</strong>. Your opening today is <strong>${openingStr}</strong>.<br><br>Would you like to record <strong>${debitStr}</strong> as a <strong>Debit</strong> line (added to balance)? Your total cash will become <strong>${openingStr} + ${debitStr} = ${totalStr}</strong>.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, add debit',
        cancelButtonText: 'Not now',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
    }).then((result) => {
        if (!result.isConfirmed) {
            if (!forcePrompt) {
                sessionStorage.setItem(dismissKey, '1');
            }
            return;
        }
        addCarryOverDebit(paymentMethodId, selectedDate, debitAmount, priorDate);
    });
}

async function addCarryOverDebit(paymentMethodId, selectedDate, debitAmount, priorDateLabel) {
    Swal.fire({ title: 'Saving debit...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const description = `Carry-over from last SOA (${priorDateLabel})`;

    try {
        const debitRes = await fetch(soaManualEntryStoreUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                payment_method_id: paymentMethodId,
                entry_date: selectedDate,
                description,
                type: 'debit',
                amount: debitAmount,
                is_carry_over: true,
            }),
        });
        const debitData = await debitRes.json();
        if (!debitRes.ok || !debitData.success) {
            const msg = debitData.errors ? Object.values(debitData.errors).flat().join(' ') : (debitData.message || 'Failed to add debit');
            throw new Error(msg);
        }

        sessionStorage.removeItem(carryOverDebitPromptKey(paymentMethodId, selectedDate));
        Swal.close();
        Swal.fire({
            icon: 'success',
            title: 'Debit added',
            text: `${formatPrice(debitAmount)} recorded as debit.`,
            timer: 2200,
            showConfirmButton: false,
        });
        loadTransactions();
    } catch (err) {
        Swal.close();
        Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'Failed to save debit' });
    }
}

function clearYesterdayStartingSuggestion() {
    const wrap = document.getElementById('yesterdayBalanceSuggestionWrap');
    if (wrap) {
        wrap.innerHTML = '';
        wrap.classList.add('d-none');
    }
}

let currentSoaData = null;

const EXPENSE_BUDGET_TIER_OPTIONS = [
    { value: 'flagship', label: 'Flagship budget' },
    { value: 'warehouse', label: 'Warehouse budget' },
    { value: 'annex', label: 'Annex budget' },
];

function expenseBudgetTierLabel(value) {
    const o = EXPENSE_BUDGET_TIER_OPTIONS.find((x) => x.value === value);
    return o ? o.label : (value || '');
}

/** Tiers already used by other manual expense-budget rows (excluding excludeId). */
function getUsedExpenseBudgetTiers(excludeId) {
    const used = new Set();
    if (!currentSoaData || !currentSoaData.transactions) return used;
    currentSoaData.transactions.forEach((t) => {
        if (t.type !== 'soa_manual' || !t.is_expense_budget) return;
        if (excludeId != null && parseInt(t.id, 10) === parseInt(excludeId, 10)) return;
        if (t.expense_budget_tier) used.add(t.expense_budget_tier);
    });
    return used;
}

function allExpenseBudgetTiersUsed(excludeId) {
    const used = getUsedExpenseBudgetTiers(excludeId);
    return EXPENSE_BUDGET_TIER_OPTIONS.every((o) => used.has(o.value));
}

/** Options for dropdown: unused tiers plus currentSelection if set. */
function getAvailableExpenseBudgetTiers(excludeId, currentTier) {
    const used = getUsedExpenseBudgetTiers(excludeId);
    return EXPENSE_BUDGET_TIER_OPTIONS.filter((o) => !used.has(o.value) || o.value === currentTier);
}

function buildTierSelectOptionsHtml(excludeId, row) {
    const current = row && row.expense_budget_tier ? row.expense_budget_tier : '';
    const available = getAvailableExpenseBudgetTiers(excludeId, current);
    return available.map((o) => `<option value="${o.value}"${o.value === current ? ' selected' : ''}>${o.label}</option>`).join('');
}

const soaManualEntryStoreUrl = @json(route('soa.manual-entries.store'));
const soaManualEntriesBaseUrl = @json(url('/api/soa/manual-entries'));
const deleteSoaPeriodUrl = @json(route('soa.daily-record.destroy'));
const floatedFundsApiUrl = @json(route('soa.floated-funds'));

function updateDeleteSoaPeriodButton(data) {
    const deleteBtn = document.getElementById('deleteSoaPeriodBtn');
    if (!deleteBtn) return;

    const hasActivity = !!(data && (data.has_soa_activity || data.has_starting_balance || (data.transactions && data.transactions.length > 0)));
    deleteBtn.style.display = hasActivity ? 'inline-block' : 'none';
}

function resetSoaViewAfterPeriodDelete() {
    currentSoaData = null;
    document.getElementById('deleteSoaPeriodBtn').style.display = 'none';
    document.getElementById('transactionsCard').style.display = 'none';
    document.getElementById('summaryCard').style.display = 'none';
    document.getElementById('cashActionsCard').style.display = 'none';
    document.getElementById('dailyBudgetCard').style.display = 'none';
    document.getElementById('soaTableContainer').innerHTML = '';
    document.getElementById('display_total_expenses').value = '0.00';
    document.getElementById('display_total_remaining_cash').value = '0.00';
    document.getElementById('edit_starting_cash').value = '';
    const priorBanner = document.getElementById('priorSoaBalanceBannerWrap');
    if (priorBanner) {
        priorBanner.style.display = 'none';
        priorBanner.innerHTML = '';
    }
}

function deleteSoaForSelectedDate() {
    const paymentMethodId = document.getElementById('payment_method_id').value;
    const selectedDate = document.getElementById('selected_date').value;

    if (!paymentMethodId || !selectedDate) {
        Swal.fire({
            icon: 'warning',
            title: 'Selection Required',
            text: 'Please select a date first.',
            confirmButtonColor: '#dc3545'
        });
        return;
    }

    const dateLabel = new Date(selectedDate + 'T00:00:00').toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });

    Swal.fire({
        title: 'Delete entire SOA?',
        html: `This will permanently remove <strong>all SOA data</strong> for <strong>${dateLabel}</strong>, including the starting balance, cash additions, and detail lines.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete all',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (!result.isConfirmed) return;

        Swal.fire({ title: 'Deleting...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        fetch(deleteSoaPeriodUrl, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                payment_method_id: paymentMethodId,
                date: selectedDate,
            }),
        })
            .then(async (response) => {
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || 'Delete failed');
                }
                return data;
            })
            .then((data) => {
                Swal.close();
                resetSoaViewAfterPeriodDelete();
                loadFloatedFundsBanner();
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted',
                    text: data.message || 'SOA deleted successfully.',
                    confirmButtonColor: '#198754',
                    timer: 2500,
                    timerProgressBar: true,
                });
            })
            .catch((err) => {
                Swal.close();
                Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'Delete failed' });
            });
    });
}

function loadFloatedFundsBanner() {
    const wrap = document.getElementById('floatedFundsAlertWrap');
    const totalEl = document.getElementById('floatedFundsTotalDisplay');
    const pm = document.getElementById('payment_method_id').value;
    if (!wrap || !totalEl || !pm) {
        if (wrap) wrap.style.display = 'none';
        return;
    }
    const ts = new Date().getTime();
    fetch(`${floatedFundsApiUrl}?payment_method_id=${encodeURIComponent(pm)}&_t=${ts}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Cache-Control': 'no-cache'
        },
        cache: 'no-store'
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) {
            wrap.style.display = 'none';
            return;
        }
        const total = parseFloat(data.total) || 0;
        const entries = data.entries || [];
        if (total <= 0 || entries.length === 0) {
            wrap.style.display = 'none';
            return;
        }
        totalEl.textContent = formatPrice(total);
        wrap.style.display = 'block';
    })
    .catch(() => {
        wrap.style.display = 'none';
    });
}

function openFloatedFundsModal() {
    const pm = document.getElementById('payment_method_id').value;
    const tbody = document.getElementById('floatedFundsModalBody');
    if (!pm || !tbody) return;
    tbody.innerHTML = '<tr><td colspan="5" class="text-center p-3"><div class="spinner-border spinner-border-sm"></div></td></tr>';
    const ts = new Date().getTime();
    fetch(`${floatedFundsApiUrl}?payment_method_id=${encodeURIComponent(pm)}&_t=${ts}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Cache-Control': 'no-cache'
        },
        cache: 'no-store'
    })
    .then(r => r.json())
    .then(data => {
        tbody.innerHTML = '';
        if (!data.success || !data.entries || data.entries.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-muted text-center p-3">No floated fund records.</td></tr>';
        } else {
            data.entries.forEach((row) => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td><strong>${row.budget_date_formatted}</strong><br><span class="small text-muted">${row.budget_date}</span></td>
                    <td><strong>${row.reference_date_formatted}</strong><br><span class="small text-muted">${row.reference_date}</span></td>
                    <td class="text-end">${formatPrice(row.yesterday_closing_balance)}</td>
                    <td class="text-end">${formatPrice(row.declared_starting_balance)}</td>
                    <td class="text-end fw-bold">${formatPrice(row.difference_amount)}</td>
                `;
                tbody.appendChild(tr);
            });
        }
        const modalEl = document.getElementById('floatedFundsModal');
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
    })
    .catch(() => {
        tbody.innerHTML = '<tr><td colspan="5" class="text-danger text-center p-3">Could not load floated funds.</td></tr>';
        const modalEl = document.getElementById('floatedFundsModal');
        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        modal.show();
    });
}

// Initialize default date and default payment method
document.addEventListener('DOMContentLoaded', function() {
    const paymentMethodSelect = document.getElementById('payment_method_id');
    if (paymentMethodSelect && !paymentMethodSelect.value) {
        const firstRealOption = Array.from(paymentMethodSelect.options).find(opt => opt.value);
        if (firstRealOption) {
            paymentMethodSelect.value = firstRealOption.value;
        }
    }

    const dateInput = document.getElementById('selected_date');
    if (dateInput && !dateInput.value) {
        dateInput.value = new Date().toISOString().split('T')[0];
    }

    document.getElementById('transactionsCard').style.display = 'none';
    document.getElementById('dailyBudgetCard').style.display = 'none';
    document.getElementById('cashActionsCard').style.display = 'none';
    document.getElementById('summaryCard').style.display = 'none';

    loadFloatedFundsBanner();

    document.getElementById('floatedFundsViewBtn').addEventListener('click', function() {
        openFloatedFundsModal();
    });

    document.getElementById('deleteSoaPeriodBtn').addEventListener('click', function() {
        deleteSoaForSelectedDate();
    });

    document.getElementById('transactionsCard').addEventListener('click', function(e) {
        const editBtn = e.target.closest('.soa-manual-edit');
        if (editBtn && editBtn.dataset.id) {
            e.preventDefault();
            editSoaManualEntryById(parseInt(editBtn.dataset.id, 10));
            return;
        }
        const delBtn = e.target.closest('.soa-manual-delete');
        if (delBtn && delBtn.dataset.id) {
            e.preventDefault();
            deleteSoaManualEntryById(parseInt(delBtn.dataset.id, 10));
        }
    });
});

function editSoaManualEntryById(id) {
    if (!currentSoaData || !currentSoaData.transactions) return;
    const row = currentSoaData.transactions.find(t => t.type === 'soa_manual' && parseInt(t.id, 10) === id);
    if (!row) return;

    const isDebit = row.debit != null && parseFloat(row.debit) > 0;
    const amount = isDebit ? parseFloat(row.debit) : parseFloat(row.credit || 0);
    const type = isDebit ? 'debit' : 'credit';
    const description = row.description || '';
    const descAttr = String(description)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');

    const showExpenseBudgetEdit = !!row.is_expense_budget || !allExpenseBudgetTiersUsed(id);
    const expenseBudgetChecked = row.is_expense_budget ? ' checked' : '';
    const expenseBudgetBlockEdit = showExpenseBudgetEdit
        ? `<div class="form-check mb-2 text-start" id="soa_edit_expense_budget_wrap">
                <input class="form-check-input" type="checkbox" id="soa_edit_expense_budget"${expenseBudgetChecked}>
                <label class="form-check-label" for="soa_edit_expense_budget">Expense budget</label>
                <div class="form-text">Up to three lines per day: flagship, warehouse, and annex. Uncheck to remove this flag.</div>
           </div>
           <div class="mb-3 text-start" id="soa_edit_expense_budget_tier_wrap" style="display:none;">
                <label for="soa_edit_expense_budget_tier" class="form-label">Budget location</label>
                <select id="soa_edit_expense_budget_tier" class="form-select">${buildTierSelectOptionsHtml(id, row)}</select>
           </div>`
        : '';

    Swal.fire({
        title: 'Edit SOA Detail',
        html: `
            ${expenseBudgetBlockEdit}
            <div class="mb-3 text-start">
                <label for="soa_edit_description" class="form-label">Description</label>
                <input type="text" id="soa_edit_description" class="form-control" value="${descAttr}">
            </div>
            <div class="mb-3 text-start">
                <label for="soa_edit_amount" class="form-label">Amount</label>
                <input type="number" id="soa_edit_amount" class="form-control" step="0.01" min="0.01" value="${amount}">
            </div>
            <div class="mb-3 text-start">
                <label for="soa_edit_type" class="form-label">Type</label>
                <select id="soa_edit_type" class="form-select">
                    <option value="debit"${type === 'debit' ? ' selected' : ''}>Debit</option>
                    <option value="credit"${type === 'credit' ? ' selected' : ''}>Credit</option>
                </select>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Save',
        confirmButtonColor: '#28a745',
        didOpen: () => {
            const cb = document.getElementById('soa_edit_expense_budget');
            const tierWrap = document.getElementById('soa_edit_expense_budget_tier_wrap');
            if (!cb || !tierWrap) return;
            const sync = () => {
                tierWrap.style.display = cb.checked ? 'block' : 'none';
            };
            cb.addEventListener('change', sync);
            sync();
        },
        preConfirm: () => {
            const d = document.getElementById('soa_edit_description').value.trim();
            const amt = parseFloat(document.getElementById('soa_edit_amount').value || 0);
            const t = document.getElementById('soa_edit_type').value;
            if (!d) {
                Swal.showValidationMessage('Description is required.');
                return false;
            }
            if (amt <= 0) {
                Swal.showValidationMessage('Amount must be greater than 0.');
                return false;
            }
            const wrap = document.getElementById('soa_edit_expense_budget_wrap');
            if (!wrap) {
                return {
                    description: d,
                    amount: amt,
                    type: t,
                    expense_budget: !!row.is_expense_budget,
                    expense_budget_tier: row.is_expense_budget ? (row.expense_budget_tier || null) : null,
                };
            }
            const cb = document.getElementById('soa_edit_expense_budget');
            const expense_budget = !!(cb && cb.checked);
            let expense_budget_tier = row.expense_budget_tier || null;
            if (expense_budget) {
                const ts = document.getElementById('soa_edit_expense_budget_tier');
                expense_budget_tier = ts && ts.value ? ts.value : null;
                if (!expense_budget_tier) {
                    Swal.showValidationMessage('Select a budget location (flagship, warehouse, or annex).');
                    return false;
                }
            } else {
                expense_budget_tier = null;
            }
            return { description: d, amount: amt, type: t, expense_budget, expense_budget_tier: expense_budget ? expense_budget_tier : null };
        }
    }).then((result) => {
        if (!result.isConfirmed) return;
        const url = `${soaManualEntriesBaseUrl}/${id}`;
        Swal.fire({ title: 'Saving...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        fetch(url, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(result.value)
        })
        .then(r => r.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                loadTransactions();
            } else {
                const fromErrUpd = data.errors ? Object.values(data.errors).flat().filter(Boolean).join(' ') : '';
                const msg = fromErrUpd || data.message || 'Update failed';
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
            }
        })
        .catch(err => {
            Swal.close();
            Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'Update failed' });
        });
    });
}

function deleteSoaManualEntryById(id) {
    if (!currentSoaData || !currentSoaData.transactions) return;
    const row = currentSoaData.transactions.find(t => t.type === 'soa_manual' && parseInt(t.id, 10) === id);
    if (!row) return;

    const desc = row.description || 'this entry';
    Swal.fire({
        title: 'Delete SOA detail?',
        text: `Remove "${desc}" from this statement?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (!result.isConfirmed) return;
        const url = `${soaManualEntriesBaseUrl}/${id}`;
        Swal.fire({ title: 'Deleting...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
        })
            .then((r) => r.json())
            .then((data) => {
                Swal.close();
                if (data.success) {
                    loadTransactions();
                } else {
                    const msg = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Delete failed');
                    Swal.fire({ icon: 'error', title: 'Error', text: msg });
                }
            })
            .catch((err) => {
                Swal.close();
                Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'Delete failed' });
            });
    });
}

document.getElementById('addSoaDetailBtn').addEventListener('click', function() {
    const paymentMethodId = document.getElementById('payment_method_id').value;
    const selectedDate = document.getElementById('selected_date').value;

    if (!paymentMethodId || !selectedDate) {
        Swal.fire({
            icon: 'warning',
            title: 'Selection Required',
            text: 'Please select a date first.',
            confirmButtonColor: '#dc3545'
        });
        return;
    }

    const showExpenseBudgetAdd = !allExpenseBudgetTiersUsed(null);
    const expenseBudgetBlockAdd = showExpenseBudgetAdd
        ? `<div class="form-check mb-2 text-start">
                <input class="form-check-input" type="checkbox" id="soa_detail_expense_budget">
                <label class="form-check-label" for="soa_detail_expense_budget">Expense budget</label>
                <div class="form-text">Choose flagship, warehouse, or annex. Each can be added once per day.</div>
           </div>
           <div class="mb-3 text-start" id="soa_detail_expense_budget_tier_wrap" style="display:none;">
                <label for="soa_detail_expense_budget_tier" class="form-label">Budget location</label>
                <select id="soa_detail_expense_budget_tier" class="form-select">${buildTierSelectOptionsHtml(null, {})}</select>
           </div>`
        : '';

    Swal.fire({
        title: 'Add SOA Detail',
        html: `
            ${expenseBudgetBlockAdd}
            <div class="mb-3 text-start">
                <label for="soa_detail_description" class="form-label">Description</label>
                <input type="text" id="soa_detail_description" class="form-control" placeholder="Enter description">
            </div>
            <div class="mb-3 text-start">
                <label for="soa_detail_amount" class="form-label">Amount</label>
                <input type="number" id="soa_detail_amount" class="form-control" step="0.01" min="0.01" placeholder="0.00">
            </div>
            <div class="mb-3 text-start">
                <label for="soa_detail_type" class="form-label">Type</label>
                <select id="soa_detail_type" class="form-select">
                    <option value="debit">Debit</option>
                    <option value="credit">Credit</option>
                </select>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Add',
        confirmButtonColor: '#28a745',
        didOpen: () => {
            const cb = document.getElementById('soa_detail_expense_budget');
            const tierWrap = document.getElementById('soa_detail_expense_budget_tier_wrap');
            if (!cb || !tierWrap) return;
            const sync = () => {
                tierWrap.style.display = cb.checked ? 'block' : 'none';
            };
            cb.addEventListener('change', sync);
            sync();
        },
        preConfirm: () => {
            const description = document.getElementById('soa_detail_description').value.trim();
            const amount = parseFloat(document.getElementById('soa_detail_amount').value || 0);
            const type = document.getElementById('soa_detail_type').value;

            if (!description) {
                Swal.showValidationMessage('Description is required.');
                return false;
            }
            if (amount <= 0) {
                Swal.showValidationMessage('Amount must be greater than 0.');
                return false;
            }

            const eb = document.getElementById('soa_detail_expense_budget');
            const expense_budget = !!(eb && eb.checked);
            let expense_budget_tier = null;
            if (expense_budget) {
                const ts = document.getElementById('soa_detail_expense_budget_tier');
                expense_budget_tier = ts && ts.value ? ts.value : null;
                if (!expense_budget_tier) {
                    Swal.showValidationMessage('Select a budget location (flagship, warehouse, or annex).');
                    return false;
                }
            }

            return { description, amount, type, expense_budget, expense_budget_tier: expense_budget ? expense_budget_tier : null };
        }
    }).then((result) => {
        if (!result.isConfirmed) return;

        Swal.fire({ title: 'Saving...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        fetch(soaManualEntryStoreUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                payment_method_id: paymentMethodId,
                entry_date: selectedDate,
                description: result.value.description,
                type: result.value.type,
                amount: result.value.amount,
                expense_budget: result.value.expense_budget,
                expense_budget_tier: result.value.expense_budget_tier
            })
        })
        .then(r => r.json())
        .then(data => {
            Swal.close();
            if (data.success) {
                loadTransactions();
            } else {
                const fromErr = data.errors ? Object.values(data.errors).flat().filter(Boolean).join(' ') : '';
                const msg = fromErr || data.message || 'Save failed';
                Swal.fire({ icon: 'error', title: 'Error', text: msg });
            }
        })
        .catch(err => {
            Swal.close();
            Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'Save failed' });
        });
    });
});

// Load transactions when View Transaction button is clicked
document.getElementById('viewTransactionBtn').addEventListener('click', function() {
    // Collapse both accordions when viewing transactions
    const paymentMethodCollapse = document.getElementById('paymentMethodCollapse');
    const cashManagementCollapse = document.getElementById('cashManagementCollapse');
    
    if (paymentMethodCollapse) {
        const bsCollapse = new bootstrap.Collapse(paymentMethodCollapse, {
            toggle: false
        });
        bsCollapse.hide();
    }
    
    if (cashManagementCollapse) {
        const bsCollapse = new bootstrap.Collapse(cashManagementCollapse, {
            toggle: false
        });
        bsCollapse.hide();
    }
    
    // Load transactions
    loadTransactions();
});

// Optional: Also load when date changes (remove if you only want button click)
// document.getElementById('selected_date').addEventListener('change', function() {
//     loadTransactions();
// });

// Load transactions function
function loadTransactions() {
    const paymentMethodId = document.getElementById('payment_method_id').value;
    const selectedDate = document.getElementById('selected_date').value;
    
    if (!selectedDate) {
        return;
    }

    if (!paymentMethodId) {
        Swal.fire({
            icon: 'error',
            title: 'No Payment Method Found',
            text: 'Please configure at least one active payment method in settings.',
            confirmButtonColor: '#dc3545'
        });
        return;
    }
    
    document.getElementById('dailyBudgetCard').style.display = 'none';
    
    // Show loading
    document.getElementById('transactionsCard').style.display = 'block';
    document.getElementById('soaTableContainer').innerHTML = '<div class="text-center p-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
    
    // Fetch transactions with cache busting to ensure fresh data
    const timestamp = new Date().getTime();
    fetch(`{{ route('soa.transactions') }}?payment_method_id=${paymentMethodId}&date=${selectedDate}&_t=${timestamp}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Cache-Control': 'no-cache'
        },
        cache: 'no-store'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            currentSoaData = data;
            updateDeleteSoaPeriodButton(data);
            updatePriorSoaBalanceBanner(data);
            // Always populate budget form fields (for editing)
            document.getElementById('budget_payment_method_id').value = paymentMethodId;
            document.getElementById('budget_date').value = selectedDate;
            document.getElementById('starting_balance').value = data.starting_balance || 0;
            document.getElementById('added_cash').value = data.added_cash || 0;
            updateCashSummary();
            
            // Check if starting balance exists
            if (!data.has_starting_balance) {
                // Show add daily budget form
                document.getElementById('dailyBudgetCard').style.display = 'block';
                document.getElementById('cashActionsCard').style.display = 'none';
                applyYesterdayStartingSuggestion(data);
                // Still show transactions if any exist
                if (data.transactions && data.transactions.length > 0) {
                    document.getElementById('transactionsCard').style.display = 'block';
                    renderSOATable(data);
                } else {
                    document.getElementById('transactionsCard').style.display = 'none';
                    document.getElementById('summaryCard').style.display = 'none';
                }
            } else {
                clearYesterdayStartingSuggestion();
                // Show transactions table and cash management
                document.getElementById('dailyBudgetCard').style.display = 'none';
                document.getElementById('cashActionsCard').style.display = 'block';
                document.getElementById('transactionsCard').style.display = 'block';
                // Set current starting cash value
                document.getElementById('edit_starting_cash').value = data.starting_balance || 0;
                // Render table which will also show summary
                renderSOATable(data);
                promptCarryOverDebitIfNeeded(data);
            }
            loadFloatedFundsBanner();
        } else {
            throw new Error(data.message || 'Failed to load transactions');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('soaTableContainer').innerHTML = `
            <div class="alert alert-danger m-3">
                <i class="fas fa-exclamation-triangle me-2"></i>Error loading transactions: ${error.message}
            </div>
        `;
    });
}

// Calculate and update total cash display
function updateCashSummary() {
    const startingCash = parseFloat(document.getElementById('starting_balance').value) || 0;
    const addedCash = parseFloat(document.getElementById('added_cash').value) || 0;
    const totalCash = startingCash + addedCash;
    
    document.getElementById('display_total_cash').value = formatPrice(totalCash).replace('₱', '');
}

// Add event listeners for cash calculation
document.addEventListener('DOMContentLoaded', function() {
    const startingBalanceInput = document.getElementById('starting_balance');
    const addedCashInput = document.getElementById('added_cash');
    
    if (startingBalanceInput) {
        startingBalanceInput.addEventListener('input', updateCashSummary);
    }
    if (addedCashInput) {
        addedCashInput.addEventListener('input', updateCashSummary);
    }
    
    // Initial calculation
    updateCashSummary();
});

// Handle update starting cash button
document.getElementById('updateStartingCashBtn').addEventListener('click', function() {
    const paymentMethodId = document.getElementById('payment_method_id').value;
    const selectedDate = document.getElementById('selected_date').value;
    const startingCash = document.getElementById('edit_starting_cash').value;
    
    if (!selectedDate) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Please select a date first.',
            confirmButtonColor: '#dc3545'
        });
        return;
    }

    if (!paymentMethodId) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'No payment method is configured.',
            confirmButtonColor: '#dc3545'
        });
        return;
    }
    
    if (!startingCash || parseFloat(startingCash) < 0) {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Please enter a valid starting cash amount.',
            confirmButtonColor: '#dc3545'
        });
        return;
    }
    
    Swal.fire({
        title: 'Updating Starting Cash...',
        text: 'Please wait...',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    fetch('{{ route("soa.update-starting-cash") }}', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            payment_method_id: paymentMethodId,
            budget_date: selectedDate,
            starting_balance: startingCash
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                confirmButtonColor: '#28a745',
                timer: 1000,
                timerProgressBar: true,
                showConfirmButton: false
            });
            
            // Immediately reload transactions without waiting for Swal to close
            setTimeout(() => {
                loadTransactions();
                loadFloatedFundsBanner();
            }, 100);
        } else {
            let errorMessage = data.message || 'Failed to update starting cash';
            if (data.errors) {
                const errorMessages = Object.values(data.errors).flat();
                errorMessage = errorMessages.join('\n');
            }
            throw new Error(errorMessage);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.message || 'Failed to update starting cash',
            confirmButtonColor: '#dc3545'
        });
    });
});

// Handle daily budget form submission
document.getElementById('dailyBudgetForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = {
        payment_method_id: document.getElementById('budget_payment_method_id').value,
        budget_date: document.getElementById('budget_date').value,
        starting_balance: document.getElementById('starting_balance').value,
        added_cash: document.getElementById('added_cash').value || 0,
        notes: document.getElementById('budget_notes').value || null,
    };
    
    // Show loading
    Swal.fire({
        title: 'Saving Daily Budget...',
        text: 'Please wait...',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Send request
    fetch('{{ route("soa.daily-budget.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reset form immediately
            document.getElementById('starting_balance').value = '0';
            document.getElementById('added_cash').value = '0';
            document.getElementById('budget_notes').value = '';
            updateCashSummary();
            
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                confirmButtonColor: '#28a745',
                timer: 1000,
                timerProgressBar: true,
                showConfirmButton: false
            });
            
            // Immediately reload transactions without waiting for Swal to close
            setTimeout(() => {
                loadTransactions();
                loadFloatedFundsBanner();
            }, 100);
        } else {
            let errorMessage = data.message || 'Failed to save daily budget';
            if (data.errors) {
                const errorMessages = Object.values(data.errors).flat();
                errorMessage = errorMessages.join('\n');
            }
            throw new Error(errorMessage);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: error.message || 'Failed to save daily budget',
            confirmButtonColor: '#dc3545'
        });
    });
});

// Render SOA table
function renderSOATable(data) {
    const container = document.getElementById('soaTableContainer');
    
    if (!container) {
        return;
    }
    
    const transactions = data.transactions || [];
    if (!transactions.length && !data.has_starting_balance) {
        document.getElementById('summaryCard').style.display = 'none';
        container.innerHTML = `
            <div class="alert alert-info m-3">
                <i class="fas fa-info-circle me-2"></i>No SOA entries found for this date.
            </div>
        `;
        return;
    }
    
    let html = '<table class="soa-table">';

    html += '<tr class="soa-header-row">';
    html += '<td style="width: 38%;"><strong>DATE</strong></td>';
    html += `<td style="width: 22%;"><strong>${formatLongDate(data.selected_date)}</strong></td>`;
    html += '<td style="width: 20%;"><strong>STARTING BALANCE</strong></td>';
    html += `<td style="width: 20%;" class="text-right"><strong>${formatPrice(data.starting_balance || 0)}</strong></td>`;
    html += '</tr>';

    html += '<tr class="starting-balance-row">';
    html += '<th>DESCRIPTION</th>';
    html += '<th class="text-right">DEBIT</th>';
    html += '<th class="text-right">CREDIT</th>';
    html += '<th class="text-right">BALANCE</th>';
    html += '</tr>';

    let runningBalance = parseFloat(data.starting_balance || 0);
    let totalDebit = 0;
    let totalExpenseDebits = 0;
    let totalCredit = 0;

    transactions.forEach((transaction) => {
        if (transaction.type === 'starting_balance') return;

        const debit = parseFloat(transaction.debit || 0);
        const credit = parseFloat(transaction.credit || 0);
        totalDebit += debit;
        if (!(transaction.is_carry_over && debit)) {
            totalExpenseDebits += debit;
        }
        totalCredit += credit;
        runningBalance += credit;
        if (transaction.is_carry_over && debit) {
            runningBalance += debit;
        } else {
            runningBalance -= debit;
        }

        const isManual = transaction.type === 'soa_manual';
        const manualRowActions = (cellAmount) => {
            if (!isManual || !transaction.id || !cellAmount) return '';
            return `<button type="button" class="btn btn-link btn-sm p-0 ms-1 soa-manual-edit text-secondary" title="Edit" aria-label="Edit" data-id="${transaction.id}"><i class="fas fa-edit"></i></button>` +
                `<button type="button" class="btn btn-link btn-sm p-0 ms-1 soa-manual-delete text-danger" title="Delete" aria-label="Delete" data-id="${transaction.id}"><i class="fas fa-trash-alt"></i></button>`;
        };

        html += '<tr>';
        html += '<td>';
        if (transaction.type === 'expense' && transaction.id && transaction.expense_transaction_id) {
            const expenseUrl = `{{ url('/expenses') }}/${transaction.expense_transaction_id}?item=${transaction.id}`;
            html += `<a href="${expenseUrl}" class="text-decoration-none text-primary" style="cursor: pointer;" title="Click to view expense details">`;
            html += `<i class="fas fa-external-link-alt me-1" style="font-size: 0.8em;"></i>`;
            html += transaction.description || '';
            html += '</a>';
        } else {
            html += transaction.description || '';
            if (isManual && transaction.is_expense_budget) {
                const tl = expenseBudgetTierLabel(transaction.expense_budget_tier);
                html += ` <span class="badge bg-secondary ms-1">${tl || 'Expense budget'}</span>`;
            }
        }
        html += '</td>';

        html += `<td class="text-right ${transaction.debit ? 'debit' : ''}">`;
        if (transaction.debit) {
            html += formatPrice(transaction.debit) + manualRowActions(true);
        }
        html += '</td>';

        html += '<td class="text-right">';
        if (transaction.credit) {
            html += formatPrice(transaction.credit) + manualRowActions(true);
        }
        html += '</td>';

        html += `<td class="text-right balance">${formatPrice(runningBalance)}</td>`;
        html += '</tr>';
    });

    html += '<tr class="total-row">';
    html += '<td>TOTAL AMOUNT</td>';
    html += `<td class="text-right">${formatPrice(totalDebit)}</td>`;
    html += `<td class="text-right">${formatPrice(totalCredit)}</td>`;
    html += `<td class="text-right">${formatPrice(runningBalance)}</td>`;
    html += '</tr>';

    html += '</table>';
    container.innerHTML = html;

    const totalExpensesEl = document.getElementById('display_total_expenses');
    const totalCashEl = document.getElementById('display_total_remaining_cash');
    if (totalExpensesEl) totalExpensesEl.value = formatPrice(totalExpenseDebits).replace('₱', '');
    if (totalCashEl) totalCashEl.value = formatPrice(runningBalance).replace('₱', '');
    document.getElementById('summaryCard').style.display = 'block';
}

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
    sidebar.classList.add('collapsed');
    mainContent.classList.remove('sidebar-open');
    
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
            if (window.innerWidth >= 768) {
                // Desktop: remove overlay
                if (sidebarOverlay) sidebarOverlay.classList.remove('show');
            } else {
                // Mobile: close sidebar if open
                if (sidebar && !sidebar.classList.contains('collapsed')) {
                    sidebar.classList.add('collapsed');
                    if (sidebarOverlay) sidebarOverlay.classList.remove('show');
                }
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
            localStorage.setItem('soaSidebarOpen', 'true');
            
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
            localStorage.setItem('soaSidebarOpen', 'false');
            
            // Hide overlay on mobile
            if (window.innerWidth < 768 && sidebarOverlay) {
                sidebarOverlay.classList.remove('show');
            }
        }
    }
});
</script>
@endsection
