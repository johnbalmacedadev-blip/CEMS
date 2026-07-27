{{-- Financing re-compute modal (same calculator logic as Settings → Financing) --}}
<div class="modal fade" id="recomputeFinancingModal" tabindex="-1" aria-labelledby="recomputeFinancingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="recomputeFinancingModalLabel">
                    <i class="fas fa-calculator me-2"></i>Re-compute Financing
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">
                    Select a financing rule, adjust price / down payment, then update
                    <strong id="umTargetLabel">the option box</strong>.
                </p>

                <div class="row g-3 align-items-end mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold" for="um_calc_scheme">Financing Option</label>
                        <select class="form-select form-select-sm" id="um_calc_scheme">
                            <option value="">— Select rule —</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" for="um_calc_year_range">Year Model</label>
                        <select class="form-select form-select-sm" id="um_calc_year_range">
                            <option value="">— Select scheme first —</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" for="um_calc_unit_price">Price</label>
                        <input type="text" class="form-control form-control-sm" id="um_calc_unit_price" placeholder="Price" inputmode="decimal">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" for="um_calc_dp_percent">DP %</label>
                        <div class="input-group input-group-sm">
                            <input type="number" class="form-control" id="um_calc_dp_percent" step="0.01" min="0" max="100" placeholder="%">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold" for="um_calc_down_payment">DP</label>
                        <input type="text" class="form-control form-control-sm" id="um_calc_down_payment" placeholder="DP" inputmode="decimal">
                    </div>
                </div>

                <div class="row small mb-3">
                    <div class="col-auto"><strong>AF:</strong> <span id="um_calc_amt_finance">0.00</span></div>
                    <div class="col-auto"><strong>CHMF %:</strong> <span id="um_calc_chattel_pct">—</span></div>
                    <div class="col-auto"><strong>CHMF:</strong> <span id="um_calc_chattel">0.00</span></div>
                    <div class="col-auto"><strong>Insurance:</strong> <span id="um_calc_insurance">0.00</span></div>
                    <div class="col-auto"><strong>NO PDC:</strong> <span id="um_calc_no_pdc">0.00</span></div>
                    <div class="col-auto"><strong>All In DP:</strong> <span id="um_calc_all_in_dp" class="text-primary fw-bold">0.00</span></div>
                </div>

                <div class="table-responsive mb-3">
                    <table class="table table-bordered table-sm mb-0">
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
                                    <td id="um_term_pct_{{ $months }}">—</td>
                                    <td id="um_monthly_{{ $months }}">—</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-light border mb-0">
                    <div class="small text-muted mb-1">Preview (will be written to the box)</div>
                    <pre class="mb-0 small" id="um_calc_preview" style="white-space: pre-wrap; font-family: inherit;">—</pre>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="um_calc_apply_btn">
                    <i class="fas fa-sync-alt me-1"></i>Update option details
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const schemes = @json($financingSchemes ?? []);
    const modalEl = document.getElementById('recomputeFinancingModal');
    if (!modalEl || typeof bootstrap === 'undefined') return;

    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    const schemeSel = document.getElementById('um_calc_scheme');
    const yearSel = document.getElementById('um_calc_year_range');
    const unitPrice = document.getElementById('um_calc_unit_price');
    const downPayment = document.getElementById('um_calc_down_payment');
    const dpPercent = document.getElementById('um_calc_dp_percent');
    const previewEl = document.getElementById('um_calc_preview');
    const applyBtn = document.getElementById('um_calc_apply_btn');
    const targetLabel = document.getElementById('umTargetLabel');

    let targetFieldId = null;
    let optionKind = 'down'; // down | monthly
    let lastResult = null;

    function parseNum(str) {
        return parseFloat(String(str || '').replace(/,/g, '')) || 0;
    }
    function formatNum(n) {
        const num = Number(n);
        if (isNaN(num)) return '0.00';
        return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    function formatPeso(n) {
        return '₱' + Math.round(Number(n) || 0).toLocaleString('en-US');
    }
    function formatPctDisplay(pct) {
        const v = Number(pct);
        if (isNaN(v)) return '0';
        return Number.isInteger(v) ? String(v) : v.toFixed(2).replace(/\.?0+$/, '');
    }

    function populateSchemes() {
        schemeSel.innerHTML = '<option value="">— Select rule —</option>';
        schemes.forEach(function (s) {
            const opt = document.createElement('option');
            opt.value = String(s.id);
            opt.textContent = s.name;
            schemeSel.appendChild(opt);
        });
    }

    function currentScheme() {
        const id = schemeSel.value;
        return schemes.find(function (s) { return String(s.id) === String(id); }) || null;
    }

    function currentSetting() {
        const scheme = currentScheme();
        if (!scheme) return null;
        const id = yearSel.value;
        return (scheme.settings || []).find(function (s) { return String(s.id) === String(id); }) || null;
    }

    function populateYearRanges(preferredYear) {
        const scheme = currentScheme();
        yearSel.innerHTML = '';
        if (!scheme || !(scheme.settings || []).length) {
            yearSel.innerHTML = '<option value="">— No year ranges —</option>';
            return;
        }

        let selectedId = null;
        const year = parseInt(preferredYear, 10) || 0;

        scheme.settings.forEach(function (s, idx) {
            const opt = document.createElement('option');
            opt.value = String(s.id);
            opt.textContent = s.year_model_range;
            yearSel.appendChild(opt);
            if (year > 0 && s.year_min != null && s.year_max != null && year >= s.year_min && year <= s.year_max) {
                selectedId = s.id;
            }
            if (idx === 0 && selectedId == null) selectedId = s.id;
        });

        if (selectedId != null) yearSel.value = String(selectedId);
    }

    function buildPreviewText(allIn, monthlies, dpPct) {
        const pctLabel = formatPctDisplay(dpPct);
        const title = optionKind === 'monthly'
            ? (pctLabel + '% LOW MONTHLY OPTION')
            : (pctLabel + '% LOW DOWN PAYMENT OPTION');

        const lines = [
            title,
            'ALL-IN CASH OUT: ' + formatPeso(allIn),
        ];
        [24, 36, 48].forEach(function (m) {
            if (monthlies[m] != null) {
                lines.push(m + ' Mos: ' + formatPeso(monthlies[m]));
            }
        });
        return lines.join('\n');
    }

    function updateCalculator() {
        const price = parseNum(unitPrice.value);
        const dp = parseNum(downPayment.value);
        const amtFinance = Math.max(0, price - dp);
        document.getElementById('um_calc_amt_finance').textContent = formatNum(amtFinance);

        const opt = currentSetting();
        if (!opt) {
            document.getElementById('um_calc_chattel_pct').textContent = '—';
            document.getElementById('um_calc_chattel').textContent = '—';
            document.getElementById('um_calc_insurance').textContent = '—';
            document.getElementById('um_calc_no_pdc').textContent = '—';
            document.getElementById('um_calc_all_in_dp').textContent = '—';
            [12, 24, 36, 48, 60].forEach(function (m) {
                document.getElementById('um_term_pct_' + m).textContent = '—';
                document.getElementById('um_monthly_' + m).textContent = '—';
            });
            previewEl.textContent = '—';
            lastResult = null;
            return;
        }

        const chmfTotal = (opt.chattel_fee_percent != null && !isNaN(opt.chattel_fee_percent))
            ? amtFinance * (opt.chattel_fee_percent / 100)
            : opt.chattel;

        document.getElementById('um_calc_chattel_pct').textContent =
            (opt.chattel_fee_percent != null && !isNaN(opt.chattel_fee_percent))
                ? Number(opt.chattel_fee_percent).toFixed(2) + '%'
                : '—';
        document.getElementById('um_calc_chattel').textContent = formatNum(chmfTotal);
        document.getElementById('um_calc_insurance').textContent = formatNum(opt.insurance);
        document.getElementById('um_calc_no_pdc').textContent = formatNum(opt.no_pdc);

        const allInDp = dp + chmfTotal + opt.insurance + opt.no_pdc;
        document.getElementById('um_calc_all_in_dp').textContent = formatNum(allInDp);

        const monthlies = {};
        [
            [12, opt.pct12],
            [24, opt.pct24],
            [36, opt.pct36],
            [48, opt.pct48],
            [60, opt.pct60],
        ].forEach(function (pair) {
            const months = pair[0];
            const pct = pair[1];
            document.getElementById('um_term_pct_' + months).textContent = (pct * 100).toFixed(2) + '%';
            const monthly = months > 0 ? (amtFinance * (1 + pct)) / months : 0;
            document.getElementById('um_monthly_' + months).textContent = formatNum(monthly);
            monthlies[months] = monthly;
        });

        const pct = parseFloat(dpPercent.value);
        const text = buildPreviewText(allInDp, monthlies, isNaN(pct) ? 0 : pct);
        previewEl.textContent = text;
        lastResult = { text: text };
    }

    function onDpPercentInput() {
        const price = parseNum(unitPrice.value);
        const pct = parseFloat(dpPercent.value);
        if (pct >= 0 && pct <= 100 && price > 0) {
            downPayment.value = formatNum(price * pct / 100);
        }
        updateCalculator();
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

    function openFor(btn) {
        targetFieldId = btn.getAttribute('data-target');
        const label = btn.getAttribute('data-label') || 'option';
        const defaultDp = parseFloat(btn.getAttribute('data-default-dp') || '5');
        optionKind = targetFieldId === 'low_monthly_option' ? 'monthly' : 'down';
        targetLabel.textContent = label;

        populateSchemes();
        if (schemes.length) {
            schemeSel.value = String(schemes[0].id);
        }

        const yearInput = document.getElementById('year');
        const priceInput = document.getElementById('price');
        const preferredYear = yearInput ? yearInput.value : '';
        populateYearRanges(preferredYear);

        const priceVal = priceInput ? parseNum(priceInput.value) : 0;
        unitPrice.value = priceVal > 0 ? formatNum(priceVal) : '';
        dpPercent.value = isNaN(defaultDp) ? '5' : String(defaultDp);
        if (priceVal > 0) {
            downPayment.value = formatNum(priceVal * (parseFloat(dpPercent.value) || 0) / 100);
        } else {
            downPayment.value = '';
        }

        updateCalculator();
        modal.show();
    }

    document.querySelectorAll('.recompute-financing-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            openFor(btn);
        });
    });

    schemeSel.addEventListener('change', function () {
        const yearInput = document.getElementById('year');
        populateYearRanges(yearInput ? yearInput.value : '');
        updateCalculator();
    });
    yearSel.addEventListener('change', updateCalculator);
    unitPrice.addEventListener('input', onUnitPriceInput);
    downPayment.addEventListener('input', onDownPaymentInput);
    dpPercent.addEventListener('input', onDpPercentInput);
    unitPrice.addEventListener('blur', function () {
        const v = parseNum(unitPrice.value);
        if (!isNaN(v) && v >= 0 && unitPrice.value !== '') unitPrice.value = formatNum(v);
    });
    downPayment.addEventListener('blur', function () {
        const v = parseNum(downPayment.value);
        if (!isNaN(v) && v >= 0 && downPayment.value !== '') downPayment.value = formatNum(v);
    });

    applyBtn.addEventListener('click', function () {
        if (!lastResult || !targetFieldId) {
            alert('Select a financing rule and year model first.');
            return;
        }
        const field = document.getElementById(targetFieldId);
        if (!field) return;
        field.value = lastResult.text;
        modal.hide();
    });
})();
</script>
@endpush
