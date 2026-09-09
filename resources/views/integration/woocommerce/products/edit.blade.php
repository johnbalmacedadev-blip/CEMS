@extends('layouts.app')

@section('title', 'Edit WooCommerce Product - Car Empire Management System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-2">
        <h1 class="h3 mb-0"><i class="fas fa-edit me-2 text-primary"></i>Edit WooCommerce Product #{{ $productId }}</h1>
        <div class="d-flex gap-2">
            @if(!empty($product['permalink']))
                <a href="{{ $product['permalink'] }}" target="_blank" rel="noopener" class="btn btn-outline-primary">
                    <i class="fas fa-external-link-alt me-1"></i>View on site
                </a>
            @endif
            <a href="{{ route('integration.woocommerce.products.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Products
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('integration.woocommerce.products.update', $productId) }}">
                @csrf
                @method('PUT')
                @include('integration.woocommerce.products._form', ['product' => $product, 'productId' => $productId])
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Update on WooCommerce</button>
                    <a href="{{ route('integration.woocommerce.products.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
