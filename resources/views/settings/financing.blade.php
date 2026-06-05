@extends('layouts.app')

@section('title', 'Car Financing Rules - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2"><i class="fas fa-calculator me-2"></i>Car Financing Rules</h1>
        <a href="{{ route('settings') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Settings
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Scheme tabs (ASIALINK 2nd Hand, JACCS, + Add rule) --}}
    <ul class="nav nav-tabs mb-3" id="schemeTabs" role="tablist">
        @foreach($schemes ?? [] as $scheme)
            <li class="nav-item" role="presentation">
                <a class="nav-link {{ ($currentScheme->id ?? null) == $scheme->id ? 'active' : '' }}" href="{{ route('settings.financing.index', ['scheme' => $scheme->id]) }}" role="tab">{{ $scheme->name }}</a>
            </li>
        @endforeach
        <li class="nav-item" role="presentation">
            <button type="button" class="nav-link text-success" data-bs-toggle="modal" data-bs-target="#addSchemeModal">+ Add rule</button>
        </li>
    </ul>

    @if($currentScheme ?? null)
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0"><i class="fas fa-calculator me-2"></i>{{ $currentScheme->name }} — Calculator &amp; Variables</h5>
            <div>
                <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#editSchemeModal" data-scheme-id="{{ $currentScheme->id }}" data-scheme-name="{{ $currentScheme->name }}" title="Edit rule name"><i class="fas fa-edit"></i></button>
                <form action="{{ route('settings.financing.schemes.destroy', $currentScheme) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this financing rule and all its year ranges?');">@csrf @method('DELETE')<button type="submit" class="btn btn-outline-light btn-sm" title="Delete rule"><i class="fas fa-trash"></i></button></form>
            </div>
        </div>
        <div class="card-body">
            {{-- Calculator: one row with Year Range, Price, DP %, DP --}}
            <div class="row align-items-end mb-3">
                <div class="col-md-2">
                    <label class="form-label fw-bold">Year Model</label>
                    <select class="form-select form-select-sm" id="calc_year_range">
                        @if($settings->isEmpty())
                            <option value="">— Add range below —</option>
                        @else
                            @foreach($settings as $s)
                                <option value="{{ $s->id }}" data-chattel="{{ $s->chattel_fee }}" data-chattel-fee-percent="{{ $s->chattel_fee_percent !== null ? $s->chattel_fee_percent : '' }}" data-insurance="{{ $s->insurance_initial }}" data-no-pdc="{{ $s->no_pdc_charge }}" data-pct12="{{ $s->term_pct_12 }}" data-pct24="{{ $s->term_pct_24 }}" data-pct36="{{ $s->term_pct_36 }}" data-pct48="{{ $s->term_pct_48 }}" data-pct60="{{ $s->term_pct_60 }}">{{ $s->year_model_range }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Price</label>
                    <input type="text" class="form-control form-control-sm" id="calc_unit_price" value="940,000.00" placeholder="Price" inputmode="decimal">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">DP %</label>
                    <div class="input-group input-group-sm">
                        <input type="number" class="form-control" id="calc_dp_percent" step="0.01" min="0" max="100" placeholder="%">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">DP</label>
                    <input type="text" class="form-control form-control-sm" id="calc_down_payment" value="440,000.00" placeholder="DP" inputmode="decimal">
                </div>
            </div>

            {{-- Results: AF, table, then CHMF %, CHMF, Insurance, NO PDC, All In DP --}}
            <div class="row small mb-3">
                <div class="col-auto"><strong>AF:</strong> <span id="calc_amt_finance">0.00</span></div>
                <div class="col-auto"><strong>CHMF %:</strong> <span id="calc_chattel_pct">—</span></div>
                <div class="col-auto"><strong>CHMF:</strong> <span id="calc_chattel">0.00</span></div>
                <div class="col-auto"><strong>Insurance:</strong> <span id="calc_insurance">0.00</span></div>
                <div class="col-auto"><strong>NO PDC:</strong> <span id="calc_no_pdc">0.00</span></div>
                <div class="col-auto"><strong>All In DP:</strong> <span id="calc_all_in_dp" class="text-primary fw-bold">0.00</span></div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table table-bordered table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Terms</th>
                            <th>Total Term %</th>
                            <th>Monthly</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach([12, 24, 36, 48, 60] as $months)
                            <tr>
                                <td>{{ $months }} mo</td>
                                <td id="term_pct_{{ $months }}">—</td>
                                <td id="monthly_{{ $months }}">—</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <hr>

            {{-- Variables per year range: tabs --}}
            <h6 class="mb-2 fw-bold">Variables per Year Model</h6>
            <ul class="nav nav-tabs mb-3" id="yearRangeTabs" role="tablist">
                @foreach($settings as $index => $setting)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" id="tab-{{ $setting->id }}" data-bs-toggle="tab" data-bs-target="#panel-{{ $setting->id }}" type="button" role="tab">{{ $setting->year_model_range }}</button>
                    </li>
                @endforeach
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $settings->isEmpty() ? 'active' : '' }}" id="tab-new" data-bs-toggle="tab" data-bs-target="#panel-new" type="button" role="tab">+ Add Range</button>
                </li>
            </ul>

            <div class="tab-content" id="yearRangeTabContent">
                @foreach($settings as $index => $setting)
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="panel-{{ $setting->id }}" role="tabpanel">
                        <form action="{{ route('settings.financing.update', $setting) }}" method="POST" class="financing-form">
                            @csrf
                            @method('PUT')
                            @include('settings.financing._form', ['setting' => $setting])
                            <div class="d-flex justify-content-between mt-3">
                                <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="if(confirm('Remove this year range?')) document.getElementById('delete-{{ $setting->id }}').submit();">Delete</button>
                            </div>
                        </form>
                        <form id="delete-{{ $setting->id }}" action="{{ route('settings.financing.destroy', $setting) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                    </div>
                @endforeach
                <div class="tab-pane fade {{ $settings->isEmpty() ? 'show active' : '' }}" id="panel-new" role="tabpanel">
                    <form action="{{ route('settings.financing.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="financing_scheme_id" value="{{ $currentScheme->id }}">
                        @include('settings.financing._form', ['setting' => null])
                        <button type="submit" class="btn btn-success btn-sm mt-3">Add Range</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @else
        <div class="card">
            <div class="card-body text-center text-muted py-4">
                <p class="mb-0">No financing rule selected. Use <strong>+ Add rule</strong> to create one (e.g. ASIALINK 2nd Hand, JACCS).</p>
            </div>
        </div>
    @endif
</div>

{{-- Add scheme modal --}}
<div class="modal fade" id="addSchemeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('settings.financing.schemes.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add financing rule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label fw-bold">Name</label>
                    <input type="text" class="form-control" name="name" required placeholder="e.g. JACCS, BDO Auto Loan" maxlength="100">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit scheme modal --}}
<div class="modal fade" id="editSchemeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editSchemeForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit financing rule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label fw-bold">Name</label>
                    <input type="text" class="form-control" name="name" id="editSchemeName" required maxlength="100">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sel = document.getElementById('calc_year_range');
    const unitPrice = document.getElementById('calc_unit_price');
    const downPayment = document.getElementById('calc_down_payment');
    const dpPercent = document.getElementById('calc_dp_percent');

    function onDpPercentInput() {
        const price = parseNum(unitPrice.value);
        const pct = parseFloat(dpPercent.value);
        if (pct >= 0 && pct <= 100 && price > 0) {
            downPayment.value = formatNum(price * pct / 100);
            updateCalculator();
        }
    }
    function onUnitPriceInput() {
        const pct = parseFloat(dpPercent.value);
        if (!isNaN(pct) && pct >= 0 && pct <= 100) {
            const price = parseNum(unitPrice.value);
            if (price > 0) downPayment.value = formatNum(price * pct / 100);
        }
        updateCalculator();
    }
    function onDownPaymentInput() {
        const price = parseNum(unitPrice.value);
        const dp = parseNum(downPayment.value);
        if (price > 0 && dp >= 0) {
            dpPercent.value = ((dp / price) * 100).toFixed(2);
        }
        updateCalculator();
    }

    if (dpPercent) dpPercent.addEventListener('input', onDpPercentInput);
    unitPrice.addEventListener('input', onUnitPriceInput);
    downPayment.addEventListener('input', onDownPaymentInput);
    unitPrice.addEventListener('blur', function() { var v = parseNum(unitPrice.value); if (!isNaN(v) && v >= 0) unitPrice.value = formatNum(v); });
    downPayment.addEventListener('blur', function() { var v = parseNum(downPayment.value); if (!isNaN(v) && v >= 0) downPayment.value = formatNum(v); });

    function getCurrentOption() {
        const opt = sel.options[sel.selectedIndex];
        if (!opt || !opt.value) return null;
        return {
            chattel: parseFloat(opt.dataset.chattel || 0),
            chattelFeePercent: opt.dataset.chattelFeePercent !== undefined && opt.dataset.chattelFeePercent !== '' ? parseFloat(opt.dataset.chattelFeePercent) : null,
            insurance: parseFloat(opt.dataset.insurance || 0),
            noPdc: parseFloat(opt.dataset.noPdc || 0),
            pct12: parseFloat(opt.dataset.pct12 || 0),
            pct24: parseFloat(opt.dataset.pct24 || 0),
            pct36: parseFloat(opt.dataset.pct36 || 0),
            pct48: parseFloat(opt.dataset.pct48 || 0),
            pct60: parseFloat(opt.dataset.pct60 || 0),
        };
    }

    function parseNum(str) { return parseFloat(String(str).replace(/,/g, '')) || 0; }
    function formatNum(n) {
        const num = Number(n);
        if (isNaN(num)) return '0.00';
        return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function updateCalculator() {
        const price = parseNum(unitPrice.value);
        const dp = parseNum(downPayment.value);
        const amtFinance = Math.max(0, price - dp);

        document.getElementById('calc_amt_finance').textContent = formatNum(amtFinance);

        const opt = getCurrentOption();
        if (!opt) {
            document.getElementById('calc_chattel_pct').textContent = '—';
            document.getElementById('calc_chattel').textContent = '—';
            document.getElementById('calc_insurance').textContent = '—';
            document.getElementById('calc_no_pdc').textContent = '—';
            document.getElementById('calc_all_in_dp').textContent = '—';
            [12,24,36,48,60].forEach(m => {
                document.getElementById('term_pct_' + m).textContent = '—';
                document.getElementById('monthly_' + m).textContent = '—';
            });
            return;
        }

        const chmfTotal = (opt.chattelFeePercent != null && !isNaN(opt.chattelFeePercent))
            ? amtFinance * (opt.chattelFeePercent / 100)
            : opt.chattel;
        document.getElementById('calc_chattel_pct').textContent = (opt.chattelFeePercent != null && !isNaN(opt.chattelFeePercent))
            ? (opt.chattelFeePercent).toFixed(2) + '%'
            : '—';
        document.getElementById('calc_chattel').textContent = formatNum(chmfTotal);
        document.getElementById('calc_insurance').textContent = formatNum(opt.insurance);
        document.getElementById('calc_no_pdc').textContent = formatNum(opt.noPdc);
        const allInDp = dp + chmfTotal + opt.insurance + opt.noPdc;
        document.getElementById('calc_all_in_dp').textContent = formatNum(allInDp);

        [[12, opt.pct12], [24, opt.pct24], [36, opt.pct36], [48, opt.pct48], [60, opt.pct60]].forEach(([months, pct]) => {
            document.getElementById('term_pct_' + months).textContent = (pct * 100).toFixed(2) + '%';
            const monthly = months > 0 ? (amtFinance * (1 + pct)) / months : 0;
            document.getElementById('monthly_' + months).textContent = formatNum(monthly);
        });
    }

    sel.addEventListener('change', updateCalculator);
    (function initDpPercent() {
        const price = parseNum(unitPrice.value);
        const dp = parseNum(downPayment.value);
        if (price > 0 && dp >= 0 && dpPercent) dpPercent.value = ((dp / price) * 100).toFixed(2);
    })();
    updateCalculator();

    var editSchemeModal = document.getElementById('editSchemeModal');
    if (editSchemeModal) {
        editSchemeModal.addEventListener('show.bs.modal', function(e) {
            var btn = e.relatedTarget;
            if (btn && btn.dataset.schemeId) {
                document.getElementById('editSchemeForm').action = '{{ url("/settings/financing/schemes") }}/' + btn.dataset.schemeId;
                document.getElementById('editSchemeName').value = btn.dataset.schemeName || '';
            }
        });
    }
});
</script>
@endsection
