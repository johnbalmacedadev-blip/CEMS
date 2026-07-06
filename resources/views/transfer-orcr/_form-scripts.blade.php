@push('scripts')
<script>
(function () {
    const plateSearch = document.getElementById('plate_search');
    const vehicleIdInput = document.getElementById('vehicle_id');
    const plateResults = document.getElementById('plate_search_results');
    const plateClearBtn = document.getElementById('plate_search_clear');
    const yearEl = document.getElementById('vehicle_year');
    const makeEl = document.getElementById('vehicle_make');
    const seriesEl = document.getElementById('vehicle_series');
    const totalEl = document.getElementById('fee_total_display');
    const vehicleSearchUrl = @json(route('contracts.vehicles.search'));

    const feeIds = [
        'transfer_sop', 'transfer_or', 'notary',
        'pnp_clearance', 'confirmation', 'rd_sop', 'rd_or',
    ];

    let searchTimer = null;
    let selectedVehicle = null;

    function setVehicleDetails(v) {
        if (!v) {
            yearEl.value = makeEl.value = seriesEl.value = '';
            return;
        }
        yearEl.value = v.year || '';
        makeEl.value = v.make || '';
        seriesEl.value = v.series || '';
    }

    function applyVehicleSelection(v, displayText) {
        selectedVehicle = v;
        vehicleIdInput.value = v.id;
        plateSearch.value = displayText;
        plateClearBtn.style.display = '';
        plateResults.style.display = 'none';
        plateResults.innerHTML = '';
        setVehicleDetails(v);
    }

    function clearVehicleSelection() {
        selectedVehicle = null;
        vehicleIdInput.value = '';
        plateSearch.value = '';
        plateClearBtn.style.display = 'none';
        plateResults.style.display = 'none';
        plateResults.innerHTML = '';
        setVehicleDetails(null);
        plateSearch.focus();
    }

    function vehicleLabel(v) {
        return (v.plate_number ? v.plate_number + ' — ' : '') + (v.full_name || ('Vehicle #' + v.id));
    }

    function searchPlates(query) {
        fetch(vehicleSearchUrl + '?q=' + encodeURIComponent(query), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(r => r.json())
            .then(data => {
                plateResults.innerHTML = '';
                if (!data.length) {
                    plateResults.innerHTML = '<div class="list-group-item text-muted small">No vehicles found.</div>';
                } else {
                    data.forEach(v => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'list-group-item list-group-item-action';
                        item.textContent = vehicleLabel(v);
                        item.addEventListener('click', () => applyVehicleSelection(v, vehicleLabel(v)));
                        plateResults.appendChild(item);
                    });
                }
                plateResults.style.display = 'block';
            })
            .catch(() => {
                plateResults.innerHTML = '<div class="list-group-item text-danger small">Search failed.</div>';
                plateResults.style.display = 'block';
            });
    }

    plateSearch?.addEventListener('input', function () {
        clearTimeout(searchTimer);
        const q = this.value.trim();
        if (selectedVehicle && vehicleLabel(selectedVehicle) !== q) {
            vehicleIdInput.value = '';
            selectedVehicle = null;
            setVehicleDetails(null);
        }
        if (q.length < 2) {
            plateResults.style.display = 'none';
            plateResults.innerHTML = '';
            return;
        }
        searchTimer = setTimeout(() => searchPlates(q), 250);
    });

    plateSearch?.addEventListener('focus', function () {
        const q = this.value.trim();
        if (q.length >= 2 && !selectedVehicle) {
            searchPlates(q);
        }
    });

    plateClearBtn?.addEventListener('click', clearVehicleSelection);

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.plate-search-wrap')) {
            plateResults.style.display = 'none';
        }
    });

    if (vehicleIdInput.value && plateSearch.value) {
        const parts = plateSearch.value.split(' — ');
        selectedVehicle = {
            id: vehicleIdInput.value,
            plate_number: parts[0] || '',
            year: yearEl.value,
            make: makeEl.value,
            series: seriesEl.value,
        };
    }

    function parseFee(id) {
        const el = document.getElementById(id);
        if (!el || el.value === '') return 0;
        const n = parseFloat(el.value);
        return isNaN(n) ? 0 : n;
    }

    function updateTotal() {
        let sum = 0;
        feeIds.forEach(id => { sum += parseFee(id); });
        document.querySelectorAll('.other-transaction-amount').forEach(el => {
            if (el.value === '') return;
            const n = parseFloat(el.value);
            if (!isNaN(n)) sum += n;
        });
        totalEl.textContent = sum.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function bindPaidField(cb) {
        cb.addEventListener('change', function () { togglePaidHighlight(this); });
        const amount = cb.closest('.fee-field')?.querySelector('.fee-input');
        amount?.addEventListener('input', () => {
            togglePaidHighlight(cb);
            updateTotal();
        });
        togglePaidHighlight(cb);
    }

    function bindOtherTransactionRow(row) {
        row.querySelector('.other-transaction-amount')?.addEventListener('input', updateTotal);
        row.querySelectorAll('.fee-paid').forEach(bindPaidField);
    }

    function reindexOtherTransactions() {
        document.querySelectorAll('#other_transactions_container .other-transaction-row').forEach((row, index) => {
            row.querySelectorAll('[name^="other_transactions["]').forEach(input => {
                input.name = input.name.replace(/other_transactions\[\d+\]/, `other_transactions[${index}]`);
            });
        });
    }

    function otherTransactionRowHtml(index) {
        return `
            <div class="other-transaction-row row g-3 mb-3 pb-3 border-bottom">
                <div class="col-md-5">
                    <div class="form-field-cell">
                        <label class="form-label field-label-normal">Description</label>
                        <div class="form-field-control">
                            <input type="text" class="form-control" name="other_transactions[${index}][description]" placeholder="e.g. Notary fee">
                        </div>
                    </div>
                </div>
                <div class="col-md-5 fee-field-wrap">
                    <div class="form-field-cell">
                        <label class="form-label field-label-normal">Amount</label>
                        <div class="form-field-control">
                            <div class="input-group fee-field flex-nowrap">
                                <input type="number" class="form-control fee-input other-transaction-amount"
                                    name="other_transactions[${index}][amount]" step="0.01" min="0">
                                <span class="input-group-text fee-paid-addon">
                                    <input type="checkbox" name="other_transactions[${index}][paid]" value="1" class="fee-paid"> Paid
                                </span>
                                <span class="input-group-text fee-paid-date-wrap">
                                    <span class="fee-paid-date-label">Paid on</span>
                                    <input type="date" class="form-control form-control-sm fee-paid-date"
                                        name="other_transactions[${index}][paid_date]" title="Date paid">
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 other-transaction-action-col">
                    <div class="form-field-cell">
                        <label class="form-label field-label-normal d-block">&nbsp;</label>
                        <div class="form-field-control">
                            <button type="button" class="btn btn-outline-danger btn-sm remove-other-transaction" title="Remove row">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    const otherTxnContainer = document.getElementById('other_transactions_container');
    const addOtherTxnBtn = document.getElementById('add_other_transaction');

    addOtherTxnBtn?.addEventListener('click', function () {
        const index = otherTxnContainer.querySelectorAll('.other-transaction-row').length;
        otherTxnContainer.insertAdjacentHTML('beforeend', otherTransactionRowHtml(index));
        bindOtherTransactionRow(otherTxnContainer.lastElementChild);
    });

    otherTxnContainer?.addEventListener('click', function (e) {
        const removeBtn = e.target.closest('.remove-other-transaction');
        if (!removeBtn) return;
        const rows = otherTxnContainer.querySelectorAll('.other-transaction-row');
        if (rows.length <= 1) {
            const row = rows[0];
            row.querySelector('[name$="[description]"]').value = '';
            row.querySelector('.other-transaction-amount').value = '';
            const paidCb = row.querySelector('.fee-paid');
            paidCb.checked = false;
            togglePaidHighlight(paidCb);
            updateTotal();
            return;
        }
        removeBtn.closest('.other-transaction-row')?.remove();
        reindexOtherTransactions();
        updateTotal();
    });

    function togglePaidHighlight(input) {
        const wrap = input.closest('.fee-field-wrap');
        const group = input.closest('.fee-field');
        if (!group) return;
        const amount = group.querySelector('.fee-input');
        const paid = input.checked;
        const hasAmount = amount && amount.value !== '' && parseFloat(amount.value) > 0;
        group.classList.toggle('paid-active', paid && hasAmount);

        const dateWrap = wrap?.querySelector('.fee-paid-date-wrap');
        const dateInput = wrap?.querySelector('.fee-paid-date');
        if (dateWrap && dateInput) {
            dateWrap.classList.toggle('is-visible', paid);
            if (paid) {
                if (!dateInput.value) {
                    dateInput.value = new Date().toISOString().slice(0, 10);
                }
            } else {
                dateInput.value = '';
            }
        }
    }

    feeIds.forEach(id => {
        document.getElementById(id)?.addEventListener('input', updateTotal);
    });

    document.querySelectorAll('.transfer-orcr-section:not(.other-transactions-box) .fee-paid').forEach(bindPaidField);

    otherTxnContainer?.querySelectorAll('.other-transaction-row').forEach(bindOtherTransactionRow);

    if (vehicleIdInput.value && !yearEl.value) {
        fetch(vehicleSearchUrl + '?q=' + encodeURIComponent(plateSearch.value.split(' — ')[0] || plateSearch.value))
            .then(r => r.json())
            .then(data => {
                const match = data.find(v => String(v.id) === String(vehicleIdInput.value));
                if (match) {
                    selectedVehicle = match;
                    setVehicleDetails(match);
                }
            });
    }

    updateTotal();
})();
</script>
@endpush

@push('styles')
<style>
.transfer-orcr-form { display: flex; flex-direction: column; gap: 1.25rem; }

.transfer-orcr-section {
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    padding: 1rem 1.25rem 1.25rem;
    background: #fff;
}

.transfer-orcr-section-title {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #495057;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid #e9ecef;
}

.transfer-orcr-form .form-label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    margin-bottom: 0.35rem;
    color: #343a40;
}

.transfer-orcr-form .field-label-normal {
    text-transform: none;
    font-size: 0.85rem;
    letter-spacing: normal;
}

.form-field-cell {
    display: flex;
    flex-direction: column;
    height: 100%;
}

.form-field-control .form-control,
.form-field-control .form-select,
.form-field-control .input-group {
    width: 100%;
}

.form-field-extra {
    margin-top: 0.35rem;
}

.form-field-extra .form-text {
    margin-bottom: 0;
    font-size: 0.75rem;
}

.transfer-orcr-form .fee-field {
    flex-wrap: nowrap;
}

.transfer-orcr-form .fee-field .fee-input {
    min-width: 0;
    flex: 1 1 auto;
}

.transfer-orcr-form .fee-field .form-control {
    min-height: 38px;
}

.transfer-orcr-form .fee-paid-addon {
    white-space: nowrap;
    font-size: 0.85rem;
    user-select: none;
    flex: 0 0 auto;
}

.transfer-orcr-form .fee-paid-addon input[type="checkbox"] {
    margin-right: 0.35rem;
    vertical-align: middle;
}

.transfer-orcr-form .fee-field.paid-active .fee-input {
    background-color: rgba(25, 135, 84, 0.12);
    border-color: rgba(25, 135, 84, 0.35);
}

.transfer-orcr-form .fee-paid-date-wrap {
    display: none;
    align-items: center;
    gap: 0.4rem;
    padding: 0.2rem 0.5rem;
    white-space: nowrap;
    flex: 0 0 auto;
    background: rgba(25, 135, 84, 0.06);
}

.transfer-orcr-form .fee-paid-date-wrap.is-visible {
    display: flex;
}

.transfer-orcr-form .fee-paid-date-label {
    font-size: 0.68rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: #198754;
    white-space: nowrap;
}

.transfer-orcr-form .fee-paid-date-wrap .fee-paid-date {
    width: 8.75rem;
    min-width: 8.75rem;
    padding: 0.2rem 0.35rem;
    font-size: 0.8rem;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    background: #fff;
}

.transfer-orcr-form .others-note-input {
    background-color: rgba(255, 193, 7, 0.15);
    border-color: rgba(255, 193, 7, 0.45);
}

.transfer-orcr-total-row {
    display: flex;
    justify-content: flex-end;
    width: 100%;
}

.transfer-orcr-total-box {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.25rem;
    min-width: 280px;
    max-width: 420px;
    width: 100%;
    padding: 0.85rem 1.25rem;
    border: 2px solid #dc3545;
    border-radius: 0.5rem;
    background: linear-gradient(180deg, #fff 0%, #fff5f5 100%);
    box-shadow: 0 1px 3px rgba(220, 53, 69, 0.12);
}

.transfer-orcr-total-label {
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #495057;
}

.transfer-orcr-total-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #dc3545;
    font-variant-numeric: tabular-nums;
    line-height: 1.2;
}

.transfer-orcr-form-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    padding-top: 0.25rem;
}

.transfer-orcr-form .other-transaction-row:last-child {
    border-bottom: none !important;
    margin-bottom: 0 !important;
    padding-bottom: 0 !important;
}

.transfer-orcr-form .other-transaction-action-col .remove-other-transaction {
    height: 38px;
    width: 38px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.plate-search-wrap .form-control { padding-right: 2.5rem; }
.plate-search-clear {
    position: absolute;
    right: 4px;
    top: 4px;
    z-index: 4;
    padding: 0.25rem 0.5rem;
    line-height: 1;
}
.plate-search-results {
    display: none;
    position: absolute;
    left: 0;
    right: 0;
    top: 100%;
    z-index: 1050;
    max-height: 220px;
    overflow-y: auto;
    margin-top: 2px;
}
</style>
@endpush
