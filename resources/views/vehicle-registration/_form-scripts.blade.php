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
        'renewal_reg_or', 'renewal_sop', 'smoke_na', 'duplicate_plate',
        'migrate', 'duplicate_cr', 'pnp_clearance', 'confirmation'
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

    plateClearBtn?.addEventListener('click', clearVehicleSelection);

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.plate-search-wrap')) {
            plateResults.style.display = 'none';
        }
    });

    function parseFee(id) {
        const el = document.getElementById(id);
        if (!el || el.value === '') return 0;
        const n = parseFloat(el.value);
        return isNaN(n) ? 0 : n;
    }

    function updateTotal() {
        let sum = 0;
        feeIds.forEach(id => { sum += parseFee(id); });
        totalEl.textContent = sum.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    feeIds.forEach(id => {
        document.getElementById(id)?.addEventListener('input', updateTotal);
    });

    updateTotal();
})();
</script>
@endpush

@push('styles')
<style>
.vehicle-registration-form .form-label { font-size: 0.8rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.02em; }
.plate-search-wrap .form-control { padding-right: 2.5rem; }
.plate-search-clear { position: absolute; right: 4px; top: 4px; z-index: 4; padding: 0.25rem 0.5rem; line-height: 1; }
.plate-search-results { display: none; position: absolute; left: 0; right: 0; top: 100%; z-index: 1050; max-height: 220px; overflow-y: auto; margin-top: 2px; }
#fee_total_display { min-height: 38px; line-height: 1.5; }
</style>
@endpush
