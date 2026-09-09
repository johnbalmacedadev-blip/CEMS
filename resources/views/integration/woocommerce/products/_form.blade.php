@php
    $isEdit = isset($productId);
    $action = $isEdit
        ? route('integration.woocommerce.products.update', $productId)
        : route('integration.woocommerce.products.store');
    $product = old() ? array_merge($product, old()) : $product;
@endphp

<div class="row g-3">
    <div class="col-md-8">
        <label for="name" class="form-label">Product name <span class="text-danger">*</span></label>
        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ $product['name'] ?? '' }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="sku" class="form-label">SKU</label>
        <input type="text" name="sku" id="sku" class="form-control @error('sku') is-invalid @enderror"
               value="{{ $product['sku'] ?? '' }}">
        @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
            @foreach(['simple' => 'Simple', 'variable' => 'Variable', 'grouped' => 'Grouped', 'external' => 'External'] as $value => $label)
                <option value="{{ $value }}" {{ ($product['type'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
            @foreach(['publish' => 'Published', 'draft' => 'Draft', 'pending' => 'Pending', 'private' => 'Private'] as $value => $label)
                <option value="{{ $value }}" {{ ($product['status'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="stock_status" class="form-label">Stock status <span class="text-danger">*</span></label>
        <select name="stock_status" id="stock_status" class="form-select @error('stock_status') is-invalid @enderror" required>
            @foreach(['instock' => 'In stock', 'outofstock' => 'Out of stock', 'onbackorder' => 'On backorder'] as $value => $label)
                <option value="{{ $value }}" {{ ($product['stock_status'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('stock_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="regular_price" class="form-label">Regular price</label>
        <input type="number" step="0.01" min="0" name="regular_price" id="regular_price"
               class="form-control @error('regular_price') is-invalid @enderror"
               value="{{ $product['regular_price'] ?? '' }}">
        @error('regular_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="sale_price" class="form-label">Sale price</label>
        <input type="number" step="0.01" min="0" name="sale_price" id="sale_price"
               class="form-control @error('sale_price') is-invalid @enderror"
               value="{{ $product['sale_price'] ?? '' }}">
        @error('sale_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="stock_quantity" class="form-label">Stock quantity</label>
        <input type="number" name="stock_quantity" id="stock_quantity"
               class="form-control @error('stock_quantity') is-invalid @enderror"
               value="{{ $product['stock_quantity'] ?? '' }}">
        @error('stock_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-check mt-2">
            <input class="form-check-input" type="checkbox" value="1" id="manage_stock" name="manage_stock"
                   {{ !empty($product['manage_stock']) ? 'checked' : '' }}>
            <label class="form-check-label" for="manage_stock">Manage stock</label>
        </div>
    </div>

    <div class="col-12">
        <label for="short_description" class="form-label">Short description</label>
        <textarea name="short_description" id="short_description" rows="2"
                  class="form-control @error('short_description') is-invalid @enderror">{{ $product['short_description'] ?? '' }}</textarea>
        @error('short_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" id="description" rows="6"
                  class="form-control @error('description') is-invalid @enderror">{{ $product['description'] ?? '' }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
