@php
    $record = $transfer_orcr ?? null;
    $isEdit = (bool) $record;
    $branches = $branches ?? \App\Models\BranchLocation::active()->ordered()->get();

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

<div class="transfer-orcr-form">
    {{-- Vehicle & record info --}}
    <section class="transfer-orcr-section">
        <h6 class="transfer-orcr-section-title">Record &amp; Vehicle</h6>
        <div class="row g-3">
            @component('transfer-orcr._simple-field', ['label' => 'Branch / Store Location', 'id' => 'branch_location_id', 'cols' => 3])
                <select class="form-select @error('branch_location_id') is-invalid @enderror" id="branch_location_id" name="branch_location_id">
                    <option value="">— Select branch —</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ (string) old('branch_location_id', $record?->branch_location_id) === (string) $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
                @error('branch_location_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                @if($branches->isEmpty())
                    <div class="form-text">Add branches in <a href="{{ route('settings.branch-locations.index') }}">Settings → Branch / Location</a>.</div>
                @endif
            @endcomponent

            @component('transfer-orcr._simple-field', ['label' => 'Date <span class="text-danger">*</span>', 'id' => 'date', 'cols' => 3])
                <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date"
                    value="{{ old('date', $record ? $record->date->format('Y-m-d') : date('Y-m-d')) }}" required>
                @error('date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            @endcomponent

            <div class="col-md-6">
                <div class="form-field-cell">
                    <label for="plate_search" class="form-label">Plate No. <span class="text-danger">*</span></label>
                    <div class="form-field-control">
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
                    </div>
                    <div class="form-field-extra">
                        <div class="form-text">Type at least 2 characters to search by plate.</div>
                    </div>
                </div>
            </div>

            @component('transfer-orcr._simple-field', ['label' => 'Year Model', 'cols' => 2])
                <input type="text" class="form-control bg-light" id="vehicle_year" value="{{ $sv->year ?? '' }}" readonly tabindex="-1">
            @endcomponent

            @component('transfer-orcr._simple-field', ['label' => 'Make', 'cols' => 5])
                <input type="text" class="form-control bg-light" id="vehicle_make" value="{{ $svMake }}" readonly tabindex="-1">
            @endcomponent

            @component('transfer-orcr._simple-field', ['label' => 'Series', 'cols' => 5])
                <input type="text" class="form-control bg-light" id="vehicle_series" value="{{ $svSeries }}" readonly tabindex="-1">
            @endcomponent
        </div>
    </section>

    {{-- Transaction details --}}
    <section class="transfer-orcr-section">
        <h6 class="transfer-orcr-section-title">Transaction Details</h6>
        <div class="row g-3">
            @component('transfer-orcr._simple-field', ['label' => 'Transaction Type', 'id' => 'transaction_type'])
                <select class="form-select" id="transaction_type" name="transaction_type">
                    <option value="" {{ old('transaction_type', $record?->transaction_type) === '' ? 'selected' : '' }}>—</option>
                    @foreach(\App\Models\TransferOrcr::transactionTypeOptions() as $opt)
                        <option value="{{ $opt }}" {{ old('transaction_type', $record?->transaction_type) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
            @endcomponent

            @component('transfer-orcr._simple-field', ['label' => 'Remark', 'id' => 'remark'])
                <input type="text" class="form-control" id="remark" name="remark" value="{{ old('remark', $record?->remark) }}" placeholder="e.g. VALID ID">
            @endcomponent

            @component('transfer-orcr._simple-field', ['label' => 'LTO File No', 'id' => 'lto_file_no'])
                <input type="text" class="form-control" id="lto_file_no" name="lto_file_no" value="{{ old('lto_file_no', $record?->lto_file_no) }}" placeholder="e.g. LTO MUNTINLUPA, NCR">
            @endcomponent
        </div>
    </section>

    {{-- Fees --}}
    <section class="transfer-orcr-section">
        <h6 class="transfer-orcr-section-title">Fees &amp; Payments</h6>
        <div class="row g-3">
            @include('transfer-orcr._fee-field', [
                'id' => 'transfer_sop',
                'label' => 'Transfer SOP',
                'name' => 'transfer_sop',
                'value' => old('transfer_sop', $record?->transfer_sop ?? ''),
                'paidName' => 'transfer_sop_paid',
                'paid' => old('transfer_sop_paid', $record?->transfer_sop_paid),
                'paidDateName' => 'transfer_sop_paid_date',
                'paidDate' => old('transfer_sop_paid_date', $record?->transfer_sop_paid_date?->format('Y-m-d')),
            ])

            @include('transfer-orcr._fee-field', [
                'id' => 'transfer_or',
                'label' => 'Transfer OR',
                'name' => 'transfer_or',
                'value' => old('transfer_or', $record?->transfer_or),
                'paidName' => 'transfer_or_paid',
                'paid' => old('transfer_or_paid', $record?->transfer_or_paid),
                'paidDateName' => 'transfer_or_paid_date',
                'paidDate' => old('transfer_or_paid_date', $record?->transfer_or_paid_date?->format('Y-m-d')),
            ])

            @component('transfer-orcr._simple-field', ['label' => 'Others <span class="text-muted fw-normal small">(location / note)</span>', 'id' => 'others_note'])
                <input type="text" class="form-control others-note-input" id="others_note" name="others_note"
                    value="{{ old('others_note', $record?->others_note) }}" placeholder="e.g. PARAÑAQUE">
            @endcomponent

            @include('transfer-orcr._fee-field', [
                'id' => 'pnp_clearance',
                'label' => 'PNP Clearance',
                'name' => 'pnp_clearance',
                'value' => old('pnp_clearance', $record?->pnp_clearance),
                'paidName' => 'pnp_clearance_paid',
                'paid' => old('pnp_clearance_paid', $record?->pnp_clearance_paid),
                'paidDateName' => 'pnp_clearance_paid_date',
                'paidDate' => old('pnp_clearance_paid_date', $record?->pnp_clearance_paid_date?->format('Y-m-d')),
            ])

            @component('transfer-orcr._simple-field', ['label' => 'Confirmation', 'id' => 'confirmation'])
                <input type="number" class="form-control fee-input" id="confirmation" name="confirmation" step="0.01" min="0"
                    value="{{ old('confirmation', $record?->confirmation) }}" placeholder="500.00">
            @endcomponent

            @component('transfer-orcr._simple-field', ['label' => 'Hand Carry', 'id' => 'notary'])
                <input type="number" class="form-control fee-input" id="notary" name="notary" step="0.01" min="0"
                    value="{{ old('notary', $record?->notary) }}" placeholder="500.00">
            @endcomponent

            @component('transfer-orcr._simple-field', ['label' => 'RD', 'id' => 'rd'])
                <input type="text" class="form-control" id="rd" name="rd" value="{{ old('rd', $record?->rd) }}" placeholder="e.g. RD QUEZON CITY, PAID">
            @endcomponent

            @include('transfer-orcr._fee-field', [
                'id' => 'rd_sop',
                'label' => 'RD SOP',
                'name' => 'rd_sop',
                'value' => old('rd_sop', $record?->rd_sop),
                'paidName' => 'rd_sop_paid',
                'paid' => old('rd_sop_paid', $record?->rd_sop_paid),
                'paidDateName' => 'rd_sop_paid_date',
                'paidDate' => old('rd_sop_paid_date', $record?->rd_sop_paid_date?->format('Y-m-d')),
            ])

            @include('transfer-orcr._fee-field', [
                'id' => 'rd_or',
                'label' => 'RD OR',
                'name' => 'rd_or',
                'value' => old('rd_or', $record?->rd_or),
                'paidName' => 'rd_or_paid',
                'paid' => old('rd_or_paid', $record?->rd_or_paid),
                'paidDateName' => 'rd_or_paid_date',
                'paidDate' => old('rd_or_paid_date', $record?->rd_or_paid_date?->format('Y-m-d')),
            ])
        </div>
    </section>

    {{-- Status --}}
    <section class="transfer-orcr-section">
        <h6 class="transfer-orcr-section-title">Status &amp; Completion</h6>
        <div class="row g-3">
            @component('transfer-orcr._simple-field', ['label' => 'Remarks', 'id' => 'remarks'])
                <input type="text" class="form-control" id="remarks" name="remarks" value="{{ old('remarks', $record?->remarks) }}">
            @endcomponent

            @component('transfer-orcr._simple-field', ['label' => 'Status', 'id' => 'status'])
                <select class="form-select" id="status" name="status">
                    <option value="" {{ old('status', $record?->status ?? '') === '' ? 'selected' : '' }}>—</option>
                    @foreach(\App\Models\TransferOrcr::statusOptions() as $opt)
                        <option value="{{ $opt }}" {{ old('status', $record?->status ?? 'Pending') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                    @endforeach
                </select>
            @endcomponent

            @component('transfer-orcr._simple-field', ['label' => 'Completion Date', 'id' => 'release_date', 'hint' => 'Release date (optional)'])
                <input type="date" class="form-control" id="release_date" name="release_date"
                    value="{{ old('release_date', $record?->release_date?->format('Y-m-d')) }}">
            @endcomponent
        </div>
    </section>

    {{-- Other transactions --}}
    <section class="transfer-orcr-section other-transactions-box">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="transfer-orcr-section-title mb-0">Other Transactions</h6>
            <button type="button" class="btn btn-sm btn-outline-primary" id="add_other_transaction">
                <i class="fas fa-plus me-1"></i>Add row
            </button>
        </div>
        <div id="other_transactions_container">
            @foreach($otherTransactions as $index => $txn)
                <div class="other-transaction-row row g-3 mb-3 pb-3 border-bottom">
                    <div class="col-md-5">
                        <div class="form-field-cell">
                            <label class="form-label field-label-normal">Description</label>
                            <div class="form-field-control">
                                <input type="text" class="form-control" name="other_transactions[{{ $index }}][description]"
                                    value="{{ $txn['description'] ?? '' }}" placeholder="e.g. Notary fee">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5 fee-field-wrap">
                        <div class="form-field-cell">
                            <label class="form-label field-label-normal">Amount</label>
                            <div class="form-field-control">
                                <div class="input-group fee-field flex-nowrap">
                                    <input type="number" class="form-control fee-input other-transaction-amount"
                                        name="other_transactions[{{ $index }}][amount]" step="0.01" min="0"
                                        value="{{ $txn['amount'] ?? '' }}">
                                    <span class="input-group-text fee-paid-addon">
                                        <input type="checkbox" name="other_transactions[{{ $index }}][paid]" value="1" class="fee-paid"
                                            {{ !empty($txn['paid']) ? 'checked' : '' }}> Paid
                                    </span>
                                    <span class="input-group-text fee-paid-date-wrap">
                                        <span class="fee-paid-date-label">Paid on</span>
                                        <input type="date" class="form-control form-control-sm fee-paid-date"
                                            name="other_transactions[{{ $index }}][paid_date]"
                                            value="{{ $txn['paid_date'] ?? '' }}" title="Date paid">
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
            @endforeach
        </div>
    </section>

    <div class="transfer-orcr-total-row">
        <div class="transfer-orcr-total-box">
            <span class="transfer-orcr-total-label">Total Amount</span>
            <span class="transfer-orcr-total-value" id="fee_total_display">0.00</span>
        </div>
    </div>

    <div class="transfer-orcr-form-actions">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i>{{ $isEdit ? 'Update' : 'Save' }}
        </button>
        <a href="{{ route('transfer-orcr.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</div>
