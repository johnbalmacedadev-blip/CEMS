<div class="card mb-3 expense-item" data-item-id="{{ $item->id }}">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0">Item #{{ $index + 1 }}</h6>
            <button type="button" class="btn btn-sm btn-danger" onclick="deleteExpenseItem({{ $expenseTransaction->id }}, {{ $item->id }})" title="Delete this expense item">
                <i class="fas fa-trash me-1"></i>Delete Item
            </button>
        </div>
        
        <!-- 1. EXPENSE DATE -->
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Expense Date: <span class="text-danger">*</span></label>
                <input type="date" class="form-control" name="expense_date" value="{{ $item->expense_date ? $item->expense_date->format('Y-m-d') : date('Y-m-d') }}" required>
            </div>
        </div>
        
        <!-- 2. EXPENSE TYPE -->
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Expense Type: Operating or Vehicle <span class="text-danger">*</span></label>
                <select class="form-select payment-tag-select" name="payment_tag" required onchange="toggleExpenseType(this, {{ $item->id }})">
                    <option value="">Select</option>
                    <option value="Operating" {{ $item->payment_tag == 'Operating' ? 'selected' : '' }}>Operating</option>
                    <option value="Vehicle" {{ $item->payment_tag == 'Vehicle' ? 'selected' : '' }}>Vehicle</option>
                </select>
            </div>
        </div>
        
        <!-- 3. PAYMENT METHOD -->
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Payment Method: <span class="text-danger">*</span></label>
                <select class="form-select payment-method-select" name="payment_method_id" required id="payment_method_{{ $item->id }}">
                    <option value="">Select Payment Method</option>
                    @if($item->paymentMethod)
                        <option value="{{ $item->paymentMethod->id }}" selected>{{ $item->paymentMethod->name }}</option>
                    @endif
                </select>
            </div>
        </div>
        
        <!-- 4. REQUESTED BY -->
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Requested By:</label>
                <input type="text" class="form-control" name="requested_by" value="{{ $item->requested_by ?? '' }}">
            </div>
        </div>
        
        <!-- 5. APPROVED BY -->
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Approved By:</label>
                <input type="text" class="form-control" name="approved_by" value="{{ $item->approved_by ?? '' }}">
            </div>
        </div>
        
        <!-- 6. PAID BY -->
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Paid By:</label>
                <input type="text" class="form-control" name="care_of" value="{{ $item->care_of ?? '' }}">
            </div>
        </div>
        
        <!-- 7. STORE/SHOP -->
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Store / Shop:</label>
                <input type="text" class="form-control" name="store_shop" value="{{ $item->store_shop ?? '' }}">
            </div>
        </div>
        
        <!-- 8. NOTES -->
        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Notes:</label>
                <textarea class="form-control" name="description_details" rows="3" placeholder="Enter notes...">{{ $item->description_details ?? '' }}</textarea>
            </div>
        </div>
        
        <!-- Description field (for Operating/Vehicle - shown based on expense type) -->
        <div class="row description-field-row" style="display: {{ $item->payment_tag ? 'block' : 'none' }};">
            <div class="col-md-12 mb-3">
                <label class="form-label">Description <span class="text-danger">*</span></label>
                <!-- Text input for Operating Expense -->
                <textarea class="form-control description-text" id="description_text_{{ $item->id }}" name="description" rows="3" placeholder="Enter description..." {{ $item->payment_tag == 'Operating' ? 'required' : '' }} style="display: {{ $item->payment_tag == 'Operating' ? 'block' : 'none' }};">{{ $item->payment_tag == 'Operating' ? $item->description : '' }}</textarea>
                <!-- Checkbox list for Vehicle Expense -->
                <div class="description-checkbox-container" id="description_checkbox_container_{{ $item->id }}" style="display: {{ $item->payment_tag == 'Vehicle' ? 'block' : 'none' }};">
                    <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                        <div class="category-checkboxes" id="category_checkboxes_{{ $item->id }}">
                            @if($item->payment_tag == 'Vehicle')
                                @php
                                    $selectedCategories = explode(', ', $item->description);
                                @endphp
                                @foreach($selectedCategories as $category)
                                    @if($category)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="{{ trim($category) }}" id="category_{{ $item->id }}_{{ $loop->index }}" checked>
                                            <label class="form-check-label" for="category_{{ $item->id }}_{{ $loop->index }}">
                                                {{ trim($category) }}
                                            </label>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="mt-2">
                        <div class="input-group">
                            <input type="text" class="form-control new-category-input" id="new_category_input_{{ $item->id }}" placeholder="Add new category..." onkeypress="if(event.key === 'Enter') { event.preventDefault(); addNewCategory({{ $item->id }}); }">
                            <button type="button" class="btn btn-outline-primary" onclick="addNewCategory({{ $item->id }})">
                                <i class="fas fa-plus me-1"></i>Add
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Cost and Vehicle -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Cost or Amount: <span class="text-danger">*</span></label>
                <input type="number" class="form-control" name="cost" step="0.01" min="0" required value="{{ $item->cost }}">
            </div>
            <div class="col-md-6 mb-3 vehicle-search-container" style="display: {{ $item->payment_tag == 'Vehicle' ? 'block' : 'none' }};">
                <label class="form-label">Vehicle: <span class="text-danger">*</span></label>
                <input type="hidden" class="vehicle-id-input" name="vehicle_id" value="{{ $item->vehicle_id ?? '' }}">
                <div class="input-group">
                    <input type="text" class="form-control vehicle-search-input" placeholder="Search vehicle..." autocomplete="off" value="{{ $item->vehicle ? $item->vehicle->plate_number . ' - ' . $item->vehicle->full_name : '' }}" {{ $item->payment_tag == 'Vehicle' ? 'required' : '' }}>
                    <button type="button" class="btn btn-outline-secondary" onclick="clearVehicleSearch(this)">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="vehicle-search-results" style="display: none;"></div>
            </div>
        </div>
        
        <!-- 9. RECEIPT UPLOAD -->
        <div class="row">
            <div class="col-12 mb-3">
                <label class="form-label">Receipt Upload:</label>
                <div class="mb-2" id="receipt-existing-{{ $item->id }}">
                    @foreach($item->receipts as $receipt)
                        <div class="receipt-thumbnail d-inline-block me-2 mb-2" style="position: relative;">
                            <img src="{{ $receipt->url }}" alt="Receipt" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: cover; cursor: pointer;" onclick="viewReceipt('{{ $receipt->url }}', '{{ addslashes($receipt->original_name) }}')">
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteReceiptImage({{ $item->id }}, {{ $receipt->id }})" title="Delete" style="position: absolute; top: -5px; right: -5px; padding: 2px 6px;">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
                <input type="file" class="form-control receipt-files-input" name="receipts[]" multiple accept="image/*" onchange="uploadExpenseItemReceipts(this, {{ $item->id }}, {{ $expenseTransaction->id }})">
                <small class="text-muted" id="receipt-count-label-{{ $item->id }}">(up to 10 images, max 5MB each) - Current: {{ $item->receipts->count() }}/10</small>
                <div class="receipt-preview-container row g-2 mt-2" data-item-id="{{ $item->id }}"></div>
            </div>
        </div>
        
        <!-- 10. RECEIPT CHECK FIELDS -->
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" name="receipt_checked" id="receipt_checked_{{ $item->id }}" value="1" {{ $item->receipt_checked ? 'checked' : '' }}>
                    <label class="form-check-label" for="receipt_checked_{{ $item->id }}">
                        Receipt Checked:
                    </label>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Receipt Checker:</label>
                <input type="text" class="form-control" name="receipt_checker" value="{{ $item->receipt_checker ?? '' }}">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Receipt Check Date:</label>
                <input type="date" class="form-control" name="receipt_check_date" value="{{ $item->receipt_check_date ? $item->receipt_check_date->format('Y-m-d') : '' }}">
            </div>
        </div>
    </div>
</div>

