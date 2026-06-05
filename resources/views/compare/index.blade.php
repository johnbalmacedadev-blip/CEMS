@extends('layouts.app')

@section('title', 'Compare Cars - Car Empire Management System')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 border-bottom pb-2">
        <h1 class="h3 mb-0"><i class="fas fa-balance-scale me-2 text-primary"></i>Compare Cars</h1>
        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
            <i class="fas fa-home me-1"></i>Back to Home
        </a>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('compare.index') }}" class="row g-2 align-items-end" id="compareForm">
                <div class="col-md-2">
                    <label for="year" class="form-label">Year</label>
                    <input type="text" class="form-control" id="year" name="year" value="{{ $year }}" placeholder="e.g. 2017" required>
                </div>
                <div class="col-md-3">
                    <label for="vehicle_brand" class="form-label">Vehicle Brand</label>
                    <input type="text" class="form-control" id="vehicle_brand" name="vehicle_brand" value="{{ $vehicleBrand }}" placeholder="e.g. Toyota" required>
                </div>
                <div class="col-md-3">
                    <label for="model" class="form-label">Model</label>
                    <input type="text" class="form-control" id="model" name="model" value="{{ $model }}" placeholder="e.g. Fortuner" required>
                </div>
                <div class="col-md-3">
                    <label for="variant" class="form-label">Variant</label>
                    <input type="text" class="form-control" id="variant" name="variant" value="{{ $variant !== '' ? $variant : 'Any Variant' }}" placeholder="Any Variant" data-default-any-variant="1">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100" id="compareSubmitBtn">
                        <i class="fas fa-search" id="compareSubmitIcon"></i>
                    </button>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-secondary w-100" id="compareResetBtn" title="Reset">
                        <i class="fas fa-undo"></i>
                    </button>
                </div>
                <div class="col-12">
                    <div id="compareLoading" class="small text-primary d-none align-items-center gap-2">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span>Fetching data...</span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(!empty($marketTable))
        <div id="compareResults">
        @php
            $siteHeaders = array_keys($marketTable);
            $attributes = ['Availability', 'Model Variant', 'Price Range', 'Mileage', 'Transmission'];
        @endphp
        <div class="card border-0 shadow-sm" style="background:#1f1f1f; color:#f8f9fa;">
            <div class="card-body p-4">
                <h2 class="h3 mb-4 text-white">
                    <i class="fas fa-chart-bar me-2 text-info"></i>MARKET COMPARISON TABLE
                </h2>
                <div class="table-responsive">
                    <table class="table table-borderless align-middle mb-0" style="color:#f8f9fa;">
                        <thead>
                            <tr style="border-bottom:1px solid rgba(255,255,255,0.12);">
                                <th class="pb-3">Attribute</th>
                                @foreach($siteHeaders as $siteHeader)
                                    <th class="pb-3">{{ $siteHeader }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attributes as $attribute)
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.06);">
                                    <td class="py-3">{{ $attribute }}</td>
                                    @foreach($siteHeaders as $siteHeader)
                                        @php $value = $marketTable[$siteHeader][$attribute] ?? 'N/A'; @endphp
                                        <td class="py-3">
                                            @if($attribute === 'Availability')
                                                @if($value === 'Available')
                                                    <span class="text-success">✔ {{ $value }}</span>
                                                @elseif($value === 'None')
                                                    <span class="text-danger">✖ {{ $value }}</span>
                                                @else
                                                    <span class="text-warning">{{ $value }}</span>
                                                @endif
                                            @else
                                                {{ $value }}
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row g-3 mt-1">
            @foreach($results as $site => $siteResult)
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <strong>{{ $site }}</strong>
                            @if(!empty($siteResult['used_url']))
                                <a href="{{ $siteResult['used_url'] }}" target="_blank" class="small">open source</a>
                            @endif
                        </div>
                        <div class="card-body">
                            @if(!empty($siteResult['error']))
                                <p class="text-danger small mb-0">{{ $siteResult['error'] }}</p>
                            @elseif(empty($siteResult['matches']))
                                <p class="text-muted mb-0">No matching listings found.</p>
                            @else
                                @foreach(array_slice($siteResult['matches'], 0, 3) as $match)
                                    <div class="d-flex gap-2 mb-2">
                                        @if(!empty($match['image_url']))
                                            <img src="{{ $match['image_url'] }}" alt="" style="width:64px;height:48px;object-fit:cover;border-radius:6px;border:1px solid #ddd;">
                                        @else
                                            <div style="width:64px;height:48px;border-radius:6px;border:1px solid #ddd;background:#f3f3f3;"></div>
                                        @endif
                                        <div class="flex-grow-1">
                                            <div class="small fw-semibold">
                                                <a href="{{ $match['url'] ?? '#' }}" target="_blank" rel="noopener">
                                                    {{ $match['short_title'] ?? ($match['title'] ?? '-') }}
                                                </a>
                                            </div>
                                            <div class="small text-muted">
                                                @if(!empty($match['price'])) {{ $match['price'] }} @endif
                                                @if(!empty($match['mileage'])) <span class="ms-2">{{ $match['mileage'] }}</span> @endif
                                                @if(!empty($match['transmission'])) <span class="ms-2">{{ $match['transmission'] }}</span> @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="alert alert-info mt-3 mb-0">
            This table is built from public website results. When a site does not expose enough structured data, unavailable fields are shown as `N/A`.
        </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('compareForm');
    const submitBtn = document.getElementById('compareSubmitBtn');
    const loadingEl = document.getElementById('compareLoading');
    const iconEl = document.getElementById('compareSubmitIcon');
    const variantEl = document.getElementById('variant');
    const resetBtn = document.getElementById('compareResetBtn');
    const resultsEl = document.getElementById('compareResults');

    if (!form || !submitBtn || !loadingEl) return;

    if (variantEl) {
        const anyVariantLabel = 'Any Variant';
        variantEl.addEventListener('focus', function () {
            if ((variantEl.value || '').trim().toLowerCase() === anyVariantLabel.toLowerCase()) {
                variantEl.value = '';
            }
        });
        variantEl.addEventListener('blur', function () {
            if ((variantEl.value || '').trim() === '') {
                variantEl.value = anyVariantLabel;
            }
        });
    }

    form.addEventListener('submit', function () {
        if (variantEl && (variantEl.value || '').trim().toLowerCase() === 'any variant') {
            variantEl.value = '';
        }
        submitBtn.disabled = true;
        loadingEl.classList.remove('d-none');
        loadingEl.classList.add('d-flex');
        if (iconEl) {
            iconEl.classList.remove('fa-search');
            iconEl.classList.add('fa-spinner', 'fa-spin');
        }
    });

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            // Hide current results immediately (UI feedback)
            if (resultsEl) {
                resultsEl.style.display = 'none';
            }

            // Clear inputs
            const yearEl = document.getElementById('year');
            const brandEl = document.getElementById('vehicle_brand');
            const modelEl = document.getElementById('model');
            if (yearEl) yearEl.value = '';
            if (brandEl) brandEl.value = '';
            if (modelEl) modelEl.value = '';
            if (variantEl) variantEl.value = 'Any Variant';

            // Reset loading + submit state
            submitBtn.disabled = false;
            loadingEl.classList.add('d-none');
            loadingEl.classList.remove('d-flex');
            if (iconEl) {
                iconEl.classList.add('fa-search');
                iconEl.classList.remove('fa-spinner', 'fa-spin');
            }

            // Navigate to clean URL (no query params) so server-side results are cleared.
            window.location.href = @json(route('compare.index'));
        });
    }
});
</script>
@endsection

