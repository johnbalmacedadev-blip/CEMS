@extends('layouts.app')

@section('title', 'WooCommerce Products - Car Empire Management System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-2">
        <h1 class="h3 mb-0"><i class="fas fa-box-open me-2" style="color:#96588a;"></i>WooCommerce Products</h1>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('integration.woocommerce.settings') }}" class="btn btn-outline-secondary">
                <i class="fas fa-link me-1"></i>Connection
            </a>
            @canPage('integration.woocommerce', 'create')
            <a href="{{ route('integration.woocommerce.products.create') }}" class="btn btn-primary {{ $settings->isConfigured() ? '' : 'disabled' }}">
                <i class="fas fa-plus me-1"></i>Add Product
            </a>
            @endcanPage
            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                <i class="fas fa-home me-1"></i>Home
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if($error)
        <div class="alert alert-warning">
            <i class="fas fa-info-circle me-1"></i>{{ $error }}
            @unless($settings->isConfigured())
                <a href="{{ route('integration.woocommerce.settings') }}" class="alert-link ms-1">Open Connection settings</a>
            @endunless
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('integration.woocommerce.products.index') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small mb-0">Search</label>
                    <input type="text" name="search" class="form-control form-control-sm" value="{{ $search }}" placeholder="Name, SKU…">
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-0">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All</option>
                        @foreach(['publish' => 'Published', 'draft' => 'Draft', 'pending' => 'Pending', 'private' => 'Private'] as $value => $label)
                            <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-sync-alt me-1"></i>Fetch / Filter</button>
                    <a href="{{ route('integration.woocommerce.products.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
            </form>
            <p class="small text-muted mb-0 mt-2">
                Live data from
                <strong>{{ $settings->store_url ?: 'your WooCommerce store' }}</strong>.
                Add / Edit / Delete updates the WordPress site directly.
            </p>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:70px;">ID</th>
                            <th style="width:72px;">Image</th>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th class="text-end">Price</th>
                            <th>Stock</th>
                            <th class="text-center" style="width:140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            @php
                                $image = data_get($product, 'images.0.src');
                                $price = $product['price'] ?? $product['regular_price'] ?? '';
                                $stockLabel = !empty($product['manage_stock'])
                                    ? (($product['stock_quantity'] ?? 0).' qty')
                                    : ($product['stock_status'] ?? '—');
                            @endphp
                            <tr>
                                <td class="text-muted">#{{ $product['id'] ?? '—' }}</td>
                                <td>
                                    @if($image)
                                        <img src="{{ $image }}" alt="" class="rounded border" style="width:48px;height:48px;object-fit:cover;">
                                    @else
                                        <span class="text-muted small">No image</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $product['name'] ?? '—' }}</div>
                                    @if(!empty($product['permalink']))
                                        <a href="{{ $product['permalink'] }}" target="_blank" rel="noopener" class="small">View on site</a>
                                    @endif
                                </td>
                                <td>{{ $product['sku'] ?: '—' }}</td>
                                <td><span class="badge text-bg-light border">{{ $product['type'] ?? '—' }}</span></td>
                                <td>
                                    @php $st = $product['status'] ?? ''; @endphp
                                    <span class="badge {{ $st === 'publish' ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $st ?: '—' }}</span>
                                </td>
                                <td class="text-end">{{ $price !== '' ? number_format((float) $price, 2) : '—' }}</td>
                                <td>{{ $stockLabel }}</td>
                                <td class="text-center">
                                    @canPage('integration.woocommerce', 'update')
                                    <a href="{{ route('integration.woocommerce.products.edit', $product['id']) }}" class="btn btn-sm btn-outline-primary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcanPage
                                    @canPage('integration.woocommerce', 'delete')
                                    <form action="{{ route('integration.woocommerce.products.destroy', $product['id']) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Delete this product from WooCommerce permanently?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    @endcanPage
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    {{ $error ? 'Unable to load products.' : 'No products found.' }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($paginator)
            <div class="card-footer bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="small text-muted">
                    Showing page {{ $paginator->currentPage() }} of {{ max(1, $paginator->lastPage()) }}
                    · {{ number_format($paginator->total()) }} product(s)
                </div>
                {{ $paginator->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</div>
@endsection
