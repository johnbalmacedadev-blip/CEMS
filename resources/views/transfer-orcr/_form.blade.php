@php
    $record = $transfer_orcr ?? null;
    $isEdit = (bool) $record;
@endphp

@php
    $branches = $branches ?? \App\Models\BranchLocation::active()->ordered()->get();
@endphp

<div class="row g-3 transfer-orcr-form">
    {{-- BRANCH, DATE, PLATE NO. --}}
    <div class="col-md-3">
        <label for="branch_location_id" class="form-label">Branch / Store Location</label>
        <select class="form-select @error('branch_location_id') is-invalid @enderror" id="branch_location_id" name="branch_location_id">
            <option value="">— Select branch —</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}" {{ (string) old('branch_location_id', $record?->branch_location_id) === (string) $branch->id ? 'selected' : '' }}>
                    {{ $branch->name }}
                </option>
            @endforeach
        </select>
        @error('branch_location_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @if($branches->isEmpty())
            <div class="form-text">Add branches in <a href="{{ route('settings.branch-locations.index') }}">Settings → Branch / Location</a>.</div>
        @endif
    </div>
    <div class="col-md-3">
        <label for="date" class="form-label">DATE <span class="text-danger">*</span></label>
        <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date"
            value="{{ old('date', $record ? $record->date->format('Y-m-d') : date('Y-m-d')) }}" required>
        @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        @php
            $selectedVehicleId = old('vehicle_id', $record?->vehicle_id ?? request('vehicle_id'));
            $vehicleDisplay = old('vehicle_display', '');
            $sv = $record?->vehicle ?? ($selectedVehicleId ? $vehicles->firstWhere('id', (int) $selectedVehicleId) : null);
            $svMake = '';
            $svSeries = '';
            if ($sv) {
                $svMake = $sv->make && is_object($sv->make) ? $sv->make->name : ($sv->make ?? '');
                $svSeries = $sv->vehicleModel && is_object($sv->vehicleModel) ? $sv->vehicleModel->name : ($sv->model ?? '');
                if (!$vehicleDisplay) {
                    $vehicleDisplay = ($sv->plate_number ?: 'No plate') . ' — ' . $sv->year . ' ' . $svMake . ' ' . $svSeries;
                }
            }
        @endphp
        <label for="plate_search" class="form-label">PLATE NO. <span class="text-danger">*</span></label>
        <div class="plate-search-wrap position-relative">
            <input type="text"
                class="form-control @error('vehicle_id') is-invalid @enderror"
                id="plate_search"
                placeholder="Search plate number, make, model, or year..."
                autocomplete="off"
                value="{{ $vehicleDisplay }}">
            <input type="hidden" name="vehicle_id" id="vehicle_id" value="{{ $selectedVehicleId }}" required>
            <button type="button" class="btn btn-outline-secondary plate-search-clear" id="plate_search_clear" title="Clear" style="{{ $selectedVehicleId ? '' : 'display:none;' }}">
                <i class="fas fa-times"></i>
            </button>
            <div id="plate_search_results" class="list-group plate-search-results shadow-sm"></div>
        </div>
        @error('vehicle_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
        <div class="form-text">Type at least 2 characters to search by plate.</div>
    </div>

    {{-- YEAR MODEL, MAKE, SERIES (read-only from vehicle) --}}
    <div class="col-md-2">
        <label class="form-label">YEAR MODEL</label>
        <input type="text" class="form-control bg-light" id="vehicle_year" value="{{ $sv->year ?? '' }}" readonly tabindex="-1">
    </div>
    <div class="col-md-5">
        <label class="form-label">MAKE</label>
        <input type="text" class="form-control bg-light" id="vehicle_make" value="{{ $svMake }}" readonly tabindex="-1">
    </div>
    <div class="col-md-5">
        <label class="form-label">SERIES</label>
        <input type="text" class="form-control bg-light" id="vehicle_series" value="{{ $svSeries }}" readonly tabindex="-1">
    </div>

    {{-- TRANSACTION TYPE, REMARK, LTO FILE NO --}}
    <div class="col-md-4">
        <label for="transaction_type" class="form-label">TRANSACTION TYPE</label>
        <select class="form-select" id="transaction_type" name="transaction_type">
            <option value="" {{ old('transaction_type', $record?->transaction_type) === '' ? 'selected' : '' }}>—</option>
            @foreach(\App\Models\TransferOrcr::transactionTypeOptions() as $opt)
                <option value="{{ $opt }}" {{ old('transaction_type', $record?->transaction_type) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label for="remark" class="form-label">REMARK</label>
        <input type="text" class="form-control" id="remark" name="remark" value="{{ old('remark', $record?->remark) }}" placeholder="e.g. VALID ID">
    </div>
    <div class="col-md-4">
        <label for="lto_file_no" class="form-label">LTO FILE NO</label>
        <input type="text" class="form-control" id="lto_file_no" name="lto_file_no" value="{{ old('lto_file_no', $record?->lto_file_no) }}" placeholder="e.g. LTO MUNTINLUPA, NCR">
    </div>

    {{-- TRANSFER SOP, TRANSFER OR, OTHERS --}}
    <div class="col-md-4 fee-field-wrap">
        <label for="transfer_sop" class="form-label">TRANSFER SOP</label>
        <div class="input-group fee-field">
            <input type="number" class="form-control fee-input" id="transfer_sop" name="transfer_sop" step="0.01" min="0"
                value="{{ old('transfer_sop', $record?->transfer_sop ?? '') }}">
            <span class="input-group-text"><input type="checkbox" name="transfer_sop_paid" value="1" class="fee-paid"
                {{ old('transfer_sop_paid', $record?->transfer_sop_paid) ? 'checked' : '' }}> Paid</span>
        </div>
        <div class="fee-paid-date-wrap mt-1">
            <input type="date" class="form-control form-control-sm fee-paid-date" name="transfer_sop_paid_date"
                value="{{ old('transfer_sop_paid_date', $record?->transfer_sop_paid_date?->format('Y-m-d')) }}"
                title="Date paid">
        </div>
    </div>
    <div class="col-md-4 fee-field-wrap">
        <label for="transfer_or" class="form-label">TRANSFER OR</label>
        <div class="input-group fee-field">
            <input type="number" class="form-control fee-input" id="transfer_or" name="transfer_or" step="0.01" min="0"
                value="{{ old('transfer_or', $record?->transfer_or) }}">
            <span class="input-group-text"><input type="checkbox" name="transfer_or_paid" value="1" class="fee-paid"
                {{ old('transfer_or_paid', $record?->transfer_or_paid) ? 'checked' : '' }}> Paid</span>
        </div>
        <div class="fee-paid-date-wrap mt-1">
            <input type="date" class="form-control form-control-sm fee-paid-date" name="transfer_or_paid_date"
                value="{{ old('transfer_or_paid_date', $record?->transfer_or_paid_date?->format('Y-m-d')) }}"
                title="Date paid">
        </div>
    </div>
    <div class="col-md-4">
        <label for="others_note" class="form-label">OTHERS <span class="text-muted small">(location / note)</span></label>
        <input type="text" class="form-control others-note-input" id="others_note" name="others_note"
            value="{{ old('others_note', $record?->others_note) }}" placeholder="e.g. PARAÑAQUE">
    </div>

    {{-- PNP CLEARANCE, CONFIRMATION, HAND CARRY --}}
    <div class="col-md-4 fee-field-wrap">
        <label for="pnp_clearance" class="form-label">PNP CLEARANCE</label>
        <div class="input-group fee-field">
            <input type="number" class="form-control fee-input" id="pnp_clearance" name="pnp_clearance" step="0.01" min="0"
                value="{{ old('pnp_clearance', $record?->pnp_clearance) }}">
            <span class="input-group-text"><input type="checkbox" name="pnp_clearance_paid" value="1" class="fee-paid"
                {{ old('pnp_clearance_paid', $record?->pnp_clearance_paid) ? 'checked' : '' }}> Paid</span>
        </div>
        <div class="fee-paid-date-wrap mt-1">
            <input type="date" class="form-control form-control-sm fee-paid-date" name="pnp_clearance_paid_date"
                value="{{ old('pnp_clearance_paid_date', $record?->pnp_clearance_paid_date?->format('Y-m-d')) }}"
                title="Date paid">
        </div>
    </div>
    <div class="col-md-4">
        <label for="confirmation" class="form-label">CONFIRMATION</label>
        <input type="number" class="form-control fee-input" id="confirmation" name="confirmation" step="0.01" min="0"
            value="{{ old('confirmation', $record?->confirmation) }}" placeholder="500.00">
    </div>
    <div class="col-md-4">
        <label for="notary" class="form-label">HAND CARRY</label>
        <input type="number" class="form-control fee-input" id="notary" name="notary" step="0.01" min="0"
            value="{{ old('notary', $record?->notary) }}" placeholder="500.00">
    </div>

    {{-- RD, RD SOP, RD OR --}}
    <div class="col-md-4">
        <label for="rd" class="form-label">RD</label>
        <input type="text" class="form-control" id="rd" name="rd" value="{{ old('rd', $record?->rd) }}" placeholder="e.g. RD QUEZON CITY, PAID">
    </div>
    <div class="col-md-4 fee-field-wrap">
        <label for="rd_sop" class="form-label">RD SOP</label>
        <div class="input-group fee-field">
            <input type="number" class="form-control fee-input" id="rd_sop" name="rd_sop" step="0.01" min="0"
                value="{{ old('rd_sop', $record?->rd_sop) }}">
            <span class="input-group-text"><input type="checkbox" name="rd_sop_paid" value="1" class="fee-paid"
                {{ old('rd_sop_paid', $record?->rd_sop_paid) ? 'checked' : '' }}> Paid</span>
        </div>
        <div class="fee-paid-date-wrap mt-1">
            <input type="date" class="form-control form-control-sm fee-paid-date" name="rd_sop_paid_date"
                value="{{ old('rd_sop_paid_date', $record?->rd_sop_paid_date?->format('Y-m-d')) }}"
                title="Date paid">
        </div>
    </div>
    <div class="col-md-4 fee-field-wrap">
        <label for="rd_or" class="form-label">RD OR</label>
        <div class="input-group fee-field">
            <input type="number" class="form-control fee-input" id="rd_or" name="rd_or" step="0.01" min="0"
                value="{{ old('rd_or', $record?->rd_or) }}">
            <span class="input-group-text"><input type="checkbox" name="rd_or_paid" value="1" class="fee-paid"
                {{ old('rd_or_paid', $record?->rd_or_paid) ? 'checked' : '' }}> Paid</span>
        </div>
        <div class="fee-paid-date-wrap mt-1">
            <input type="date" class="form-control form-control-sm fee-paid-date" name="rd_or_paid_date"
                value="{{ old('rd_or_paid_date', $record?->rd_or_paid_date?->format('Y-m-d')) }}"
                title="Date paid">
        </div>
    </div>

    {{-- REMARKS, STATUS, DATE (completion) --}}
    <div class="col-md-4">
        <label for="remarks" class="form-label">REMARKS</label>
        <input type="text" class="form-control" id="remarks" name="remarks" value="{{ old('remarks', $record?->remarks) }}">
    </div>
    <div class="col-md-4">
        <label for="status" class="form-label">STATUS</label>
        <select class="form-select" id="status" name="status">
            <option value="" {{ old('status', $record?->status ?? '') === '' ? 'selected' : '' }}>—</option>
            @foreach(\App\Models\TransferOrcr::statusOptions() as $opt)
                <option value="{{ $opt }}" {{ old('status', $record?->status ?? 'Pending') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label for="release_date" class="form-label">DATE</label>
        <input type="date" class="form-control" id="release_date" name="release_date"
            value="{{ old('release_date', $record?->release_date?->format('Y-m-d')) }}">
        <div class="form-text">Completion / release date (optional)</div>
    </div>

    {{-- TOTAL --}}
    <div class="col-md-4">
        <label class="form-label">TOTAL</label>
        <div class="form-control bg-light fw-bold text-danger fs-5" id="fee_total_display">0.00</div>
    </div>

    {{-- OTHER TRANSACTIONS --}}
    @php
        $otherTransactions = old('other_transactions');
        if ($otherTransactions === null) {
            $otherTransactions = $record
                ? $record->otherTransactions->map(fn ($t) => [
                    'description' => $t->description,
                    'amount' => $t->amount,
                    'paid' => $t->paid,
                    'paid_date' => $t->paid_date?->format('Y-m-d'),
                ])->all()
                : [];
        }
        if (empty($otherTransactions)) {
            $otherTransactions = [['description' => '', 'amount' => '', 'paid' => false, 'paid_date' => '']];
        }
    @endphp
    <div class="col-12">
        <div class="other-transactions-box border rounded p-3 bg-light">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0 fw-bold text-uppercase">Other Transactions</h6>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add_other_transaction">
                    <i class="fas fa-plus me-1"></i>Add row
                </button>
            </div>
            <div id="other_transactions_container">
                @foreach($otherTransactions as $index => $txn)
                    <div class="other-transaction-row row g-2 align-items-start mb-3 pb-3 border-bottom">
                        <div class="col-md-5">
                            <label class="form-label">Description</label>
                            <input type="text" class="form-control" name="other_transactions[{{ $index }}][description]"
                                value="{{ $txn['description'] ?? '' }}" placeholder="e.g. Notary fee">
                        </div>
                        <div class="col-md-5 fee-field-wrap">
                            <label class="form-label">Amount</label>
                            <div class="input-group fee-field">
                                <input type="number" class="form-control fee-input other-transaction-amount"
                                    name="other_transactions[{{ $index }}][amount]" step="0.01" min="0"
                                    value="{{ $txn['amount'] ?? '' }}">
                                <span class="input-group-text">
                                    <input type="checkbox" name="other_transactions[{{ $index }}][paid]" value="1" class="fee-paid"
                                        {{ !empty($txn['paid']) ? 'checked' : '' }}> Paid
                                </span>
                            </div>
                            <div class="fee-paid-date-wrap mt-1">
                                <input type="date" class="form-control form-control-sm fee-paid-date"
                                    name="other_transactions[{{ $index }}][paid_date]"
                                    value="{{ $txn['paid_date'] ?? '' }}" title="Date paid">
                            </div>
                        </div>
                        <div class="col-md-2 other-transaction-action-col">
                            <label class="form-label d-block">&nbsp;</label>
                            <button type="button" class="btn btn-outline-danger btn-sm remove-other-transaction" title="Remove row">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i>{{ $isEdit ? 'Update' : 'Save' }}
        </button>
        <a href="{{ route('transfer-orcr.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</div>
