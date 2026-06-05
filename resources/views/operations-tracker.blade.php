@extends('layouts.app')

@section('title', 'Operations Tracker - Tools Inventory')

@section('content')
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="fas fa-chevron-left me-2"></i>Home
        </a>
        <div class="navbar-nav ms-auto">
            <a href="{{ route('logout') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-sign-out-alt me-1"></i>Logout
            </a>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar bg-light">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#purchase-inventory">
                            <i class="fas fa-shopping-cart me-2"></i>Purchase Inventory
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#current-inventory">
                            <i class="fas fa-boxes me-2"></i>Current Tools Inventory
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-tools me-2"></i>Tools Inventory
                </h1>
            </div>

            <!-- Accordion for Sections -->
            <div class="accordion" id="inventoryAccordion">
                <!-- Purchase Inventory Section -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="purchaseInventoryHeading">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#purchaseInventoryCollapse" aria-expanded="true" aria-controls="purchaseInventoryCollapse">
                            <i class="fas fa-shopping-cart me-2 text-primary"></i>Purchase Inventory
                        </button>
                    </h2>
                    <div id="purchaseInventoryCollapse" class="accordion-collapse collapse show" aria-labelledby="purchaseInventoryHeading" data-bs-parent="#inventoryAccordion">
                        <div class="accordion-body">
                            <div class="d-flex justify-content-end mb-3">
                                <button type="button" class="btn btn-primary" onclick="openAddToolModal()">
                                    <i class="fas fa-plus me-1"></i>Add Purchase
                                </button>
                            </div>

                @if($groupedTools->isEmpty())
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No purchase records found. Click "Add Purchase" to start adding tool purchases.
                    </div>
                @else
                    @foreach($groupedTools->sortKeysDesc() as $date => $tools)
                    @php
                        $totalForDate = $dateTotals[$date] ?? $tools->sum('amount');
                        $formattedDate = \Carbon\Carbon::parse($date)->format('d-M-y');
                    @endphp
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-calendar me-2"></i>{{ \Carbon\Carbon::parse($date)->format('F j, Y') }}
                            </h5>
                            <span class="badge bg-light text-dark">
                                Total: ₱{{ number_format($totalForDate, 2) }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Name of Tools</th>
                                            <th>Quantity</th>
                                            <th>Amount</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tools as $index => $tool)
                                            <tr data-tool-id="{{ $tool->id }}">
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ $tool->name }}</td>
                                                <td>{{ $tool->quantity }}</td>
                                                <td><strong>₱{{ number_format($tool->amount, 2) }}</strong></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="openEditToolModal({{ $tool->id }})" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteTool({{ $tool->id }})" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
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
                        </div>
                    </div>
                </div>

                <!-- Current Tools Inventory Section -->
                <div class="accordion-item">
                    <h2 class="accordion-header" id="currentInventoryHeading">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#currentInventoryCollapse" aria-expanded="false" aria-controls="currentInventoryCollapse">
                            <i class="fas fa-boxes me-2 text-success"></i>Current Tools Inventory
                        </button>
                    </h2>
                    <div id="currentInventoryCollapse" class="accordion-collapse collapse" aria-labelledby="currentInventoryHeading" data-bs-parent="#inventoryAccordion">
                        <div class="accordion-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Current Tools Inventory section will be implemented soon.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Add/Edit Tool Modal -->
<div class="modal fade" id="toolModal" tabindex="-1" aria-labelledby="toolModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="toolModalLabel">
                    <i class="fas fa-shopping-cart me-2"></i>Add Purchase
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="toolForm">
                    @csrf
                    <input type="hidden" id="tool_id" name="tool_id">
                    <div class="mb-3">
                        <label for="tool_name" class="form-label">Name of Tool <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="tool_name" name="name" required>
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

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let isEditMode = false;

function openAddToolModal() {
    isEditMode = false;
    document.getElementById('toolModalLabel').innerHTML = '<i class="fas fa-shopping-cart me-2"></i>Add Purchase';
    document.getElementById('toolForm').reset();
    document.getElementById('tool_id').value = '';
    document.getElementById('tool_date_acquired').value = new Date().toISOString().split('T')[0];
    
    const modal = new bootstrap.Modal(document.getElementById('toolModal'));
    modal.show();
}

function openEditToolModal(toolId) {
    isEditMode = true;
    document.getElementById('toolModalLabel').innerHTML = '<i class="fas fa-edit me-2"></i>Edit Tool';
    
    // Find the tool row
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
    
    // Extract data from row
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
    
    // Set form values
    document.getElementById('tool_id').value = toolId;
    document.getElementById('tool_name').value = cells[1].textContent.trim();
    document.getElementById('tool_quantity').value = parseInt(cells[2].textContent.trim());
    
    // Extract amount (remove ₱ and commas)
    const amountText = cells[3].textContent.trim().replace('₱', '').replace(/,/g, '');
    document.getElementById('tool_amount').value = parseFloat(amountText).toFixed(2);
    
    // Get date from the card header
    const card = row.closest('.card');
    const dateHeader = card.querySelector('.card-header h5');
    const dateText = dateHeader.textContent.trim().replace('Total: ₱', '');
    
    // Try to extract date from header or use current date
    // We'll need to find a better way, but for now use a workaround
    const modal = new bootstrap.Modal(document.getElementById('toolModal'));
    modal.show();
    
    // Fetch full tool data via AJAX for date
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
        title: isEditMode ? 'Update Purchase?' : 'Add Purchase?',
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
