@extends('layouts.app')

@section('title', 'Pricelist - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-tags me-2"></i>Pricelist
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
            <a href="{{ route('vehicles.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-car me-1"></i>Unit Report
            </a>
        </div>
    </div>

    <p class="text-muted mb-4">Car pricelist with variant, specs, price, and financing options.</p>

    <!-- Search & Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('pricelist') }}" class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" placeholder="Search by make, model, or plate..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-0">Year From</label>
                    <input type="number" class="form-control" name="year_from" placeholder="e.g. 2018" min="1990" max="2030" value="{{ request('year_from', $yearFrom ?? '') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-0">Year To</label>
                    <input type="number" class="form-control" name="year_to" placeholder="e.g. 2024" min="1990" max="2030" value="{{ request('year_to', $yearTo ?? '') }}">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="status">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="Available" {{ $status === 'Available' ? 'selected' : '' }}>Available</option>
                        <option value="Reserved" {{ $status === 'Reserved' ? 'selected' : '' }}>Reserved</option>
                        <option value="Released" {{ $status === 'Released' ? 'selected' : '' }}>Released</option>
                        <option value="Under Maintenance" {{ $status === 'Under Maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                        <option value="Forfeited" {{ $status === 'Forfeited' ? 'selected' : '' }}>Forfeited</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <label class="form-label small text-muted mb-0 d-block">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i>Search</button>
                </div>
                <div class="col-md-1">
                    <label class="form-label small text-muted mb-0 d-block">&nbsp;</label>
                    <a href="{{ route('pricelist') }}" class="btn btn-outline-secondary w-100"><i class="fas fa-times me-1"></i>Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Pricelist Table -->
    <div class="card">
        <div class="card-body p-0">
            @if($vehicles->isNotEmpty())
                {{-- Bulk price & financing actions --}}
                <div class="d-flex align-items-center gap-3 px-3 py-2 border-bottom bg-light flex-wrap">
                    <a href="{{ route('pricelist.exportPdf', request()->query()) }}" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-file-pdf me-1"></i>Export PDF
                    </a>
                    <form action="{{ route('pricelist.financing.updateAll') }}" method="POST" class="mb-0 d-flex align-items-center gap-2" onsubmit="return confirm('Update OPTION 1 and OPTION 2 for all vehicles with a posted price, using each car\'s YEAR to match the financing rule?');">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-sync-alt me-1"></i>Update all
                        </button>
                    </form>
                    <span class="small text-muted">Recomputes financing options by each vehicle's year</span>
                </div>
                {{-- Bulk actions bar (shown when rows selected) --}}
                <div id="pricelistBulkBar" class="d-none d-flex align-items-center gap-2 px-3 py-2 bg-light border-bottom">
                    <span id="pricelistBulkCount" class="fw-bold">0</span> selected
                    <button type="button" class="btn btn-sm btn-success" id="pricelistBulkAddDetailsBtn">
                        <i class="fas fa-plus me-1"></i>Add details
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 pricelist-table">
                        <thead>
                            <tr class="table-success">
                                <th class="text-center" style="width: 2.5rem;">
                                    <input type="checkbox" class="form-check-input" id="pricelistSelectAll" title="Select all on this page">
                                </th>
                                <th>MAKE MODEL</th>
                                <th>PLATE</th>
                                <th>VARIANT</th>
                                <th>TRANSMISSION</th>
                                <th>FUEL TYPE</th>
                                <th class="text-center">YEAR</th>
                                <th class="text-end">MILEAGE</th>
                                <th class="text-end">PRICE</th>
                                <th>OPTION 1 (LOW DOWN PAYMENT)</th>
                                <th class="pricelist-option2-header">OPTION 2 (LOW MONTHLY PAYMENT)</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vehicles as $vehicle)
                                @php
                                    $t = $vehicle->transmission ?? '';
                                    $transDisplay = $t === 'Automatic' ? 'A/T' : ($t === 'Manual' ? 'M/T' : ($t ? $t : '—'));
                                    $fuelDisplay = $vehicle->fuel_type ? (stripos($vehicle->fuel_type, 'gas') !== false ? 'Gas' : (stripos($vehicle->fuel_type, 'diesel') !== false ? 'Diesel' : $vehicle->fuel_type)) : '—';
                                @endphp
                                <tr>
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input pricelist-row-checkbox" value="{{ $vehicle->id }}" aria-label="Select row">
                                    </td>
                                    <td><strong>{{ $vehicle->full_name }}</strong></td>
                                    <td><span class="badge bg-secondary">{{ $vehicle->plate_number ?: '—' }}</span></td>
                                    <td>{{ $vehicle->variant ?: '—' }}</td>
                                    <td>{{ $transDisplay }}</td>
                                    <td>{{ $fuelDisplay }}</td>
                                    <td class="text-center">{{ $vehicle->year ?? '—' }}</td>
                                    <td class="text-end">{{ $vehicle->kilometers !== null && $vehicle->kilometers !== '' ? number_format($vehicle->kilometers) : '—' }}</td>
                                    <td class="text-end">{{ $vehicle->posted_price != null ? '₱' . number_format($vehicle->posted_price, 2) : '—' }}</td>
                                    <td>
                                        @if($vehicle->option1_cash_out != null || $vehicle->option1_12mos != null)
                                            <small><strong>ALL IN CASH OUT:</strong> {{ $vehicle->option1_cash_out != null ? number_format($vehicle->option1_cash_out) : '—' }}</small><br>
                                            <small>12 Mos: {{ $vehicle->option1_12mos != null ? number_format($vehicle->option1_12mos) : '—' }}</small><br>
                                            <small>24 Mos: {{ $vehicle->option1_24mos != null ? number_format($vehicle->option1_24mos) : '—' }}</small><br>
                                            <small>36 Mos: {{ $vehicle->option1_36mos != null ? number_format($vehicle->option1_36mos) : '—' }}</small><br>
                                            <small>48 Mos: {{ $vehicle->option1_48mos != null ? number_format($vehicle->option1_48mos) : '—' }}</small>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="pricelist-option2-cell">
                                        @if($vehicle->option2_cash_out != null || $vehicle->option2_12mos != null)
                                            <small><strong>ALL IN CASH OUT:</strong> {{ $vehicle->option2_cash_out != null ? number_format($vehicle->option2_cash_out) : '—' }}</small><br>
                                            <small>12 Mos: {{ $vehicle->option2_12mos != null ? number_format($vehicle->option2_12mos) : '—' }}</small><br>
                                            <small>24 Mos: {{ $vehicle->option2_24mos != null ? number_format($vehicle->option2_24mos) : '—' }}</small><br>
                                            <small>36 Mos: {{ $vehicle->option2_36mos != null ? number_format($vehicle->option2_36mos) : '—' }}</small><br>
                                            <small>48 Mos: {{ $vehicle->option2_48mos != null ? number_format($vehicle->option2_48mos) : '—' }}</small>
                                        @else
                                            —
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $optionsEmpty = $vehicle->option1_cash_out == null && $vehicle->option1_12mos == null && $vehicle->option2_cash_out == null && $vehicle->option2_12mos == null;
                                        @endphp
                                        @if($optionsEmpty)
                                            <button type="button" class="btn btn-sm btn-success me-1 pricelist-add-details-btn" title="Add financing details" data-vehicle-id="{{ $vehicle->id }}" data-vehicle-name="{{ $vehicle->full_name }}">
                                                <i class="fas fa-plus me-1"></i>ADD Details
                                            </button>
                                        @endif
                                        <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-sm btn-outline-primary" title="View vehicle">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <style>
                    .pricelist-table thead .table-success th { font-weight: 600; }
                    .pricelist-option2-header { background-color: #fff3cd !important; color: #856404; }
                    .pricelist-option2-cell { background-color: #fffde7; }
                </style>
                <div class="d-flex justify-content-center mt-3 pb-3">
                    {{ $vehicles->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No vehicles found</h5>
                    <p class="text-muted mb-3">
                        @if(request('search') || ($status ?? 'all') !== 'all' || request('year_from') || request('year_to'))
                            Try adjusting your search or year/status filter.
                        @else
                            Add vehicles in Unit Report to see them on the pricelist.
                        @endif
                    </p>
                    <a href="{{ route('vehicles.index') }}" class="btn btn-primary"><i class="fas fa-car me-1"></i>Unit Report</a>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal: Add financing details (select Year Model variables) --}}
<div class="modal fade" id="pricelistFinancingModal" tabindex="-1" aria-labelledby="pricelistFinancingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pricelistFinancingModalLabel">Add financing details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="pricelistFinancingForm" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small mb-2">Select the <strong>Variables per Year Model</strong> from ASIALINK 2nd Hand Car Financing to compute Option 1 (Low Down Payment) and Option 2 (Low Monthly Payment).</p>
                    <input type="hidden" id="pricelistFinancingVehicleId" name="vehicle_id" value="">
                    <div class="mb-0">
                        <label for="pricelistYearModelSelect" class="form-label fw-bold">Year Model Range</label>
                        <select class="form-select" id="pricelistYearModelSelect" name="year_model_setting_id" required>
                            <option value="">— Select —</option>
                            @foreach($financingSettings ?? [] as $fs)
                                <option value="{{ $fs->id }}">{{ $fs->financingScheme->name ?? 'Financing' }}: {{ $fs->year_model_range }}</option>
                            @endforeach
                        </select>
                        @if(empty($financingSettings) || $financingSettings->isEmpty())
                            <p class="small text-warning mt-2 mb-0">No year model ranges defined. Add them in <a href="{{ route('settings.financing.index') }}">Settings → Car Financing</a>.</p>
                        @endif
                    </div>
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
    var modalEl = document.getElementById('pricelistFinancingModal');
    var form = document.getElementById('pricelistFinancingForm');
    var select = document.getElementById('pricelistYearModelSelect');
    var baseUrl = '{{ url("/pricelist/vehicles") }}';
    var bulkUrl = '{{ route("pricelist.financing.storeBulk") }}';
    var bulkBar = document.getElementById('pricelistBulkBar');
    var bulkCountEl = document.getElementById('pricelistBulkCount');
    var selectAll = document.getElementById('pricelistSelectAll');
    var bulkAddBtn = document.getElementById('pricelistBulkAddDetailsBtn');

    function updateBulkBar() {
        var checkboxes = document.querySelectorAll('.pricelist-row-checkbox:checked');
        var n = checkboxes.length;
        if (bulkCountEl) bulkCountEl.textContent = n;
        if (bulkBar) {
            if (n > 0) bulkBar.classList.remove('d-none'); else bulkBar.classList.add('d-none');
        }
        if (selectAll) selectAll.checked = n > 0 && document.querySelectorAll('.pricelist-row-checkbox').length === n;
    }

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            document.querySelectorAll('.pricelist-row-checkbox').forEach(function(cb) { cb.checked = selectAll.checked; });
            updateBulkBar();
        });
    }
    document.querySelectorAll('.pricelist-row-checkbox').forEach(function(cb) {
        cb.addEventListener('change', updateBulkBar);
    });

    if (bulkAddBtn) {
        bulkAddBtn.addEventListener('click', function() {
            var checked = document.querySelectorAll('.pricelist-row-checkbox:checked');
            if (checked.length === 0) return;
            form.action = bulkUrl;
            form.dataset.bulk = '1';
            document.getElementById('pricelistFinancingModalLabel').textContent = 'Add financing details (' + checked.length + ' vehicle' + (checked.length !== 1 ? 's' : '') + ')';
            if (select) select.value = '';
            new bootstrap.Modal(modalEl).show();
        });
    }

    document.querySelectorAll('.pricelist-add-details-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var vehicleId = this.getAttribute('data-vehicle-id');
            var vehicleName = this.getAttribute('data-vehicle-name') || ('Vehicle #' + vehicleId);
            form.action = baseUrl + '/' + vehicleId + '/financing-details';
            delete form.dataset.bulk;
            document.getElementById('pricelistFinancingModalLabel').textContent = 'Add financing details — ' + vehicleName;
            if (select) select.value = '';
            new bootstrap.Modal(modalEl).show();
        });
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        var submitBtn = form.querySelector('button[type="submit"]');
        var origText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Saving…';
        var body;
        if (form.dataset.bulk === '1') {
            var yearModelId = select && select.value;
            if (!yearModelId) {
                submitBtn.disabled = false;
                submitBtn.textContent = origText;
                return;
            }
            body = new FormData();
            body.append('_token', form.querySelector('input[name="_token"]').value);
            body.append('year_model_setting_id', yearModelId);
            document.querySelectorAll('.pricelist-row-checkbox:checked').forEach(function(cb) {
                body.append('vehicle_ids[]', cb.value);
            });
        } else {
            body = new FormData(form);
        }
        fetch(form.action, {
            method: 'POST',
            body: body,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).then(function(r) {
            return r.json().then(function(data) {
                if (!r.ok) throw data;
                return data;
            });
        }).then(function() {
            bootstrap.Modal.getInstance(modalEl).hide();
            window.location.reload();
        }).catch(function(err) {
            submitBtn.disabled = false;
            submitBtn.textContent = origText;
            alert(err && err.message ? err.message : 'Failed to save. Please try again.');
        });
    });
});
</script>
@endsection
