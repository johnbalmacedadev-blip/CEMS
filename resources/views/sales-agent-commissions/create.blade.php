@extends('layouts.app')

@section('title', 'Add Sales Agent Commission - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-hand-holding-usd me-2"></i>Add Commission Record
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('sales-agent-commissions.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to List
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('sales-agent-commissions.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    @php
                        $showroomSelected = old('showroom', 'FLAGSHIP');
                    @endphp
                    <div class="col-md-4 col-lg-3">
                        <label for="showroom" class="form-label">Showroom <span class="text-danger">*</span></label>
                        <select class="form-select @error('showroom') is-invalid @enderror" id="showroom" name="showroom" required>
                            @foreach($showroomNames as $name)
                                <option value="{{ $name }}" @selected($showroomSelected === $name)>{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('showroom')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-7">
                        <label for="agent_name" class="form-label">AGENT NAME <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control @error('agent_name') is-invalid @enderror" id="agent_name" name="agent_name" value="{{ old('agent_name') }}" required placeholder="Type to search sales agents…" autocomplete="off">
                            <button type="button" class="btn btn-outline-secondary" id="agent_clear_btn" title="Clear agent" style="display: none;"><i class="fas fa-times"></i></button>
                        </div>
                        <input type="hidden" name="sales_agent_id" id="sales_agent_id" value="{{ old('sales_agent_id') }}">
                        <div id="agent_results" class="list-group mt-2 shadow-sm" style="display: none; max-height: 220px; overflow-y: auto; z-index: 20; position: relative;"></div>
                        @error('agent_name')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        @error('sales_agent_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        <small class="text-muted">Choose a row from the list to link the master sales agent (Staff Reports). You can also type a name only if the agent is not in the directory.</small>
                    </div>
                    <div class="col-md-5">
                        <label for="client_name" class="form-label">CLIENT NAME</label>
                        <input type="text" class="form-control" id="client_name" name="client_name" value="{{ old('client_name') }}" placeholder="Optional">
                    </div>
                    <div class="col-12">
                        <label for="vehicle_search" class="form-label">Search & tag vehicle</label>
                        <div class="input-group">
                            <input type="text" class="form-control @error('vehicle_id') is-invalid @enderror" id="vehicle_search" placeholder="Type plate number, make, model or year to search..." autocomplete="off" value="{{ old('vehicle_display', $preselectedVehicle ? (($preselectedVehicle->plate_number ?? '') . ' — ' . $preselectedVehicle->full_name) : '') }}">
                            <button type="button" class="btn btn-outline-secondary" id="vehicle_clear_btn" title="Clear vehicle" style="display: none;"><i class="fas fa-times"></i></button>
                        </div>
                        <input type="hidden" name="vehicle_id" id="vehicle_id" value="{{ old('vehicle_id', $preselectedVehicle ? $preselectedVehicle->id : '') }}">
                        <div id="vehicle_results" class="list-group mt-2" style="display: none; max-height: 220px; overflow-y: auto;"></div>
                        @error('vehicle_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Select a vehicle to tag this commission and auto-fill UNIT and PLATE.</small>
                    </div>
                    <div class="col-md-6">
                        <label for="unit" class="form-label">UNIT (description)</label>
                        <input type="text" class="form-control" id="unit" name="unit" value="{{ old('unit', $preselectedVehicle ? $preselectedVehicle->full_name : '') }}" placeholder="e.g. 2013 FORD EXPLORER — or select vehicle above">
                    </div>
                    <div class="col-md-6">
                        <label for="plate_number" class="form-label">PLATE #</label>
                        <input type="text" class="form-control" id="plate_number" name="plate_number" value="{{ old('plate_number', $preselectedVehicle ? $preselectedVehicle->plate_number : '') }}" placeholder="Auto-filled when vehicle tagged">
                    </div>
                    <div class="col-md-4">
                        <label for="transaction_type" class="form-label">TRANSACTION TYPE <span class="text-danger">*</span></label>
                        <select class="form-select" id="transaction_type" name="transaction_type" required>
                            <option value="CASH" {{ old('transaction_type', 'CASH') === 'CASH' ? 'selected' : '' }}>CASH</option>
                            <option value="FINANCING" {{ old('transaction_type') === 'FINANCING' ? 'selected' : '' }}>FINANCING</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="release_date" class="form-label">RELEASE DATE</label>
                        <input type="date" class="form-control" id="release_date" name="release_date" value="{{ old('release_date') }}">
                    </div>
                    <div class="col-md-4">
                        <label for="amount" class="form-label">AMOUNT <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" id="amount" name="amount" step="0.01" min="0" value="{{ old('amount') }}" required placeholder="e.g. 15000 or 10000">
                        </div>
                        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="agents_folder_amount" class="form-label">Agents folder (₱)</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control @error('agents_folder_amount') is-invalid @enderror" id="agents_folder_amount" name="agents_folder_amount" step="0.01" min="0" value="{{ old('agents_folder_amount') }}" placeholder="Optional">
                        </div>
                        @error('agents_folder_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label for="sales_executive_commission" class="form-label">Sales executive commission (₱)</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="number" class="form-control @error('sales_executive_commission') is-invalid @enderror" id="sales_executive_commission" name="sales_executive_commission" step="0.01" min="0" value="{{ old('sales_executive_commission') }}" placeholder="Optional">
                        </div>
                        @error('sales_executive_commission')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label d-block">Proof of appointment</label>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="proof_of_appointment" value="1" id="proof_of_appointment" {{ old('proof_of_appointment') ? 'checked' : '' }}>
                            <label class="form-check-label" for="proof_of_appointment">Yes</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label d-block">Sign of client with agent name</label>
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="sign_client_with_agent" value="1" id="sign_client_with_agent" {{ old('sign_client_with_agent') ? 'checked' : '' }}>
                            <label class="form-check-label" for="sign_client_with_agent">Yes</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label for="date_sent" class="form-label">Reservation date (date sent)</label>
                        <input type="date" class="form-control" id="date_sent" name="date_sent" value="{{ old('date_sent') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_of_payment" class="form-label">Date of payment</label>
                        <input type="date" class="form-control" id="date_of_payment" name="date_of_payment" value="{{ old('date_of_payment') }}">
                    </div>
                    <div class="col-12">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2">{{ old('notes') }}</textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save</button>
                        <a href="{{ route('sales-agent-commissions.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var vehicleSearch = document.getElementById('vehicle_search');
    var vehicleId = document.getElementById('vehicle_id');
    var vehicleResults = document.getElementById('vehicle_results');
    var unitInput = document.getElementById('unit');
    var plateInput = document.getElementById('plate_number');
    var clearBtn = document.getElementById('vehicle_clear_btn');
    var searchTimeout;

    function updateClearBtn() {
        clearBtn.style.display = vehicleId.value ? 'inline-block' : 'none';
    }
    updateClearBtn();

    clearBtn.addEventListener('click', function() {
        vehicleId.value = '';
        vehicleSearch.value = '';
        unitInput.value = '';
        plateInput.value = '';
        vehicleResults.style.display = 'none';
        vehicleResults.innerHTML = '';
        updateClearBtn();
    });

    vehicleSearch.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        var q = this.value.trim();
        if (q.length < 2) {
            vehicleResults.style.display = 'none';
            vehicleResults.innerHTML = '';
            return;
        }
        searchTimeout = setTimeout(function() {
            fetch('{{ route("contracts.vehicles.search") }}?q=' + encodeURIComponent(q))
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    vehicleResults.innerHTML = '';
                    if (data.length === 0) {
                        vehicleResults.innerHTML = '<div class="list-group-item text-muted">No vehicles found.</div>';
                    } else {
                        data.forEach(function(v) {
                            var text = (v.plate_number ? v.plate_number + ' — ' : '') + (v.full_name || 'Vehicle #' + v.id);
                            var item = document.createElement('a');
                            item.href = '#';
                            item.className = 'list-group-item list-group-item-action';
                            item.textContent = text;
                            item.addEventListener('click', function(e) {
                                e.preventDefault();
                                vehicleId.value = v.id;
                                vehicleSearch.value = text;
                                unitInput.value = v.full_name || text;
                                plateInput.value = v.plate_number || '';
                                vehicleResults.style.display = 'none';
                                vehicleResults.innerHTML = '';
                                updateClearBtn();
                            });
                            vehicleResults.appendChild(item);
                        });
                    }
                    vehicleResults.style.display = 'block';
                })
                .catch(function() {
                    vehicleResults.innerHTML = '<div class="list-group-item text-muted">Search failed.</div>';
                    vehicleResults.style.display = 'block';
                });
        }, 300);
    });

    vehicleSearch.addEventListener('focus', function() {
        if (this.value.trim().length >= 2 && vehicleResults.children.length) vehicleResults.style.display = 'block';
    });
    var agentNameInput = document.getElementById('agent_name');
    var salesAgentIdInput = document.getElementById('sales_agent_id');
    var agentResults = document.getElementById('agent_results');
    var agentClearBtn = document.getElementById('agent_clear_btn');
    var agentSearchTimeout;
    var selectedAgentName = null;
    var selectedAgentId = null;

    function updateAgentClearBtn() {
        if (!agentClearBtn || !agentNameInput || !salesAgentIdInput) return;
        agentClearBtn.style.display = (salesAgentIdInput.value || agentNameInput.value.trim()) ? 'inline-block' : 'none';
    }
    updateAgentClearBtn();

    if (agentClearBtn && agentNameInput && salesAgentIdInput) {
        agentClearBtn.addEventListener('click', function() {
            agentNameInput.value = '';
            salesAgentIdInput.value = '';
            selectedAgentName = null;
            selectedAgentId = null;
            agentResults.style.display = 'none';
            agentResults.innerHTML = '';
            updateAgentClearBtn();
        });
    }

    if (agentNameInput && salesAgentIdInput && agentResults) {
        agentNameInput.addEventListener('input', function() {
            if (selectedAgentId && this.value.trim() !== selectedAgentName) {
                salesAgentIdInput.value = '';
                selectedAgentId = null;
                selectedAgentName = null;
            }
            updateAgentClearBtn();
            clearTimeout(agentSearchTimeout);
            var q = this.value.trim();
            if (q.length < 1) {
                agentResults.style.display = 'none';
                agentResults.innerHTML = '';
                return;
            }
            agentSearchTimeout = setTimeout(function() {
                fetch('{{ route("sales-agent-commissions.agents.search") }}?q=' + encodeURIComponent(q))
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        agentResults.innerHTML = '';
                        if (data.length === 0) {
                            agentResults.innerHTML = '<div class="list-group-item text-muted small">No matching sales agents. You can still save the name as typed.</div>';
                        } else {
                            data.forEach(function(a) {
                                var item = document.createElement('a');
                                item.href = '#';
                                item.className = 'list-group-item list-group-item-action';
                                item.textContent = a.display;
                                item.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    selectedAgentName = a.name;
                                    selectedAgentId = String(a.id);
                                    agentNameInput.value = a.name;
                                    salesAgentIdInput.value = a.id;
                                    agentResults.style.display = 'none';
                                    agentResults.innerHTML = '';
                                    updateAgentClearBtn();
                                });
                                agentResults.appendChild(item);
                            });
                        }
                        agentResults.style.display = 'block';
                    })
                    .catch(function() {
                        agentResults.innerHTML = '<div class="list-group-item text-muted">Search failed.</div>';
                        agentResults.style.display = 'block';
                    });
            }, 250);
        });

        agentNameInput.addEventListener('focus', function() {
            if (this.value.trim().length >= 1 && agentResults.children.length) agentResults.style.display = 'block';
        });
    }

    document.addEventListener('click', function(e) {
        if (!vehicleSearch.contains(e.target) && !clearBtn.contains(e.target) && !vehicleResults.contains(e.target)) vehicleResults.style.display = 'none';
        if (agentNameInput && agentClearBtn && agentResults) {
            if (!agentNameInput.contains(e.target) && !agentClearBtn.contains(e.target) && !agentResults.contains(e.target)) agentResults.style.display = 'none';
        }
    });

    @if(old('sales_agent_id'))
    selectedAgentId = @json((string) old('sales_agent_id'));
    selectedAgentName = @json(old('agent_name'));
    @endif
});
</script>
@endsection
