@extends('layouts.app')

@section('title', 'WooCommerce Connection - Car Empire Management System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-2">
        <h1 class="h3 mb-0"><i class="fas fa-link me-2" style="color:#96588a;"></i>WooCommerce Connection</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('integration.woocommerce.products.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-box-open me-1"></i>Products
            </a>
            <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                <i class="fas fa-home me-1"></i>Back to Home
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

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header bg-white">
                    <strong>Store credentials</strong>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('integration.woocommerce.settings.update') }}" class="row g-3">
                        @csrf
                        @method('PUT')
                        <div class="col-12">
                            <label for="store_url" class="form-label">Store URL <span class="text-danger">*</span></label>
                            <input type="url" name="store_url" id="store_url"
                                   class="form-control @error('store_url') is-invalid @enderror"
                                   value="{{ old('store_url', $settings->store_url) }}"
                                   placeholder="https://your-store.com" required>
                            @error('store_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Use the site root URL (no trailing path). Example: https://example.com</div>
                        </div>
                        <div class="col-md-6">
                            <label for="consumer_key" class="form-label">Consumer Key</label>
                            <input type="text" name="consumer_key" id="consumer_key"
                                   class="form-control @error('consumer_key') is-invalid @enderror"
                                   value="{{ old('consumer_key') }}"
                                   placeholder="{{ $settings->consumer_key ? '•••• saved (leave blank to keep)' : 'ck_xxxxxxxx' }}"
                                   autocomplete="off">
                            @error('consumer_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="consumer_secret" class="form-label">Consumer Secret</label>
                            <input type="password" name="consumer_secret" id="consumer_secret"
                                   class="form-control @error('consumer_secret') is-invalid @enderror"
                                   value="{{ old('consumer_secret') }}"
                                   placeholder="{{ $settings->consumer_secret ? '•••• saved (leave blank to keep)' : 'cs_xxxxxxxx' }}"
                                   autocomplete="new-password">
                            @error('consumer_secret')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       id="is_enabled" name="is_enabled" value="1"
                                       {{ old('is_enabled', $settings->is_enabled) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_enabled">Enable WooCommerce connection</label>
                            </div>
                        </div>
                        <div class="col-12 d-flex flex-wrap gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Save Connection
                            </button>
                        </div>
                    </form>
                    <form method="POST" action="{{ route('integration.woocommerce.settings.test') }}" class="mt-3">
                        @csrf
                        <button type="submit" class="btn btn-outline-success" {{ $settings->isConfigured() ? '' : 'disabled' }}>
                            <i class="fas fa-plug me-1"></i>Test Connection
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header bg-white">
                    <strong>How to get API keys</strong>
                </div>
                <div class="card-body">
                    <ol class="mb-3 ps-3">
                        <li class="mb-2">Log in to WordPress admin.</li>
                        <li class="mb-2">Go to <strong>WooCommerce → Settings → Advanced → REST API</strong>.</li>
                        <li class="mb-2">Click <strong>Add key</strong>.</li>
                        <li class="mb-2">Set permissions to <strong>Read/Write</strong> and generate.</li>
                        <li class="mb-2">Copy the Consumer Key and Consumer Secret here.</li>
                    </ol>
                    <div class="border rounded p-3 bg-light">
                        <div class="small text-muted text-uppercase fw-semibold mb-1">Status</div>
                        @if($settings->isConfigured())
                            <div class="text-success fw-semibold"><i class="fas fa-check-circle me-1"></i>Configured & enabled</div>
                        @else
                            <div class="text-warning fw-semibold"><i class="fas fa-exclamation-triangle me-1"></i>Not ready</div>
                        @endif
                        @if($settings->last_tested_at)
                            <div class="small text-muted mt-2">
                                Last test: {{ $settings->last_tested_at->format('M d, Y g:i A') }}
                                ·
                                <span class="{{ $settings->last_test_status === 'ok' ? 'text-success' : 'text-danger' }}">
                                    {{ $settings->last_test_message }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
