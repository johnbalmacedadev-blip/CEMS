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
                    <button type="submit" class="btn btn-primary w-100" id="compareSubmitBtn" title="Search">
                        <i class="fas fa-search" id="compareSubmitIcon"></i>
                    </button>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-secondary w-100" id="compareResetBtn" title="Reset">
                        <i class="fas fa-undo"></i>
                    </button>
                </div>
                <div class="col-12">
                    <p class="small text-muted mb-0">
                        Search returns competitor links for the same year / brand / model, plus average prices scraped from each competitor results page.
                    </p>
                    <div id="compareLoading" class="small text-primary d-none align-items-center gap-2 mt-2">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        <span>Fetching competitor links and average prices…</span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($searched)
        <div id="compareResults">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <h2 class="h5 mb-0">Competitor links & prices</h2>
                    <div class="small text-muted">Matching: <strong>{{ $queryLabel }}</strong></div>
                </div>
                <span class="badge text-bg-light border">{{ number_format(count($links)) }} links</span>
            </div>

            @if(!empty($priceSummary))
                <div class="card mb-3 border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <strong>Average price by competitor</strong>
                        <span class="small text-muted ms-1">(from their search results page)</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Competitor</th>
                                    <th class="text-end">Average Price</th>
                                    <th class="text-end">Min</th>
                                    <th class="text-end">Max</th>
                                    <th class="text-end">Samples</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($priceSummary as $row)
                                    <tr>
                                        <td class="fw-semibold">{{ $row['label'] }}</td>
                                        <td class="text-end">
                                            @if(!empty($row['avg_price_label']))
                                                <span class="text-success fw-semibold">{{ $row['avg_price_label'] }}</span>
                                            @else
                                                <span class="text-muted">{{ $row['price_note'] ?? 'N/A' }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">{{ $row['min_price_label'] ?? '—' }}</td>
                                        <td class="text-end">{{ $row['max_price_label'] ?? '—' }}</td>
                                        <td class="text-end">{{ number_format((int) ($row['price_count'] ?? 0)) }}</td>
                                        <td class="text-end">
                                            <a href="{{ $row['url'] }}" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary">
                                                Open <i class="fas fa-external-link-alt ms-1"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if(empty($links))
                <div class="alert alert-info mb-0">No links could be built for this search.</div>
            @else
                @php
                    $groups = [
                        'competitor' => 'Competitor sites',
                        'marketplace' => 'Marketplaces',
                        'search' => 'Search engine shortcuts',
                    ];
                @endphp

                @foreach($groups as $type => $groupLabel)
                    @php
                        $groupLinks = array_values(array_filter($links, fn ($l) => ($l['type'] ?? '') === $type));
                    @endphp
                    @if(!empty($groupLinks))
                        <h3 class="h6 text-uppercase text-muted mt-3 mb-2">{{ $groupLabel }}</h3>
                        <div class="list-group mb-3">
                            @foreach($groupLinks as $link)
                                <a href="{{ $link['url'] }}"
                                   target="_blank"
                                   rel="noopener noreferrer"
                                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-start gap-3">
                                    <div>
                                        <div class="fw-semibold">
                                            {{ $link['label'] }}
                                            @if(($link['type'] ?? '') === 'competitor' && !empty($link['avg_price_label']))
                                                <span class="badge text-bg-success ms-1">Avg {{ $link['avg_price_label'] }}</span>
                                            @elseif(($link['type'] ?? '') === 'competitor')
                                                <span class="badge text-bg-light border ms-1">Avg N/A</span>
                                            @endif
                                        </div>
                                        <div class="small text-muted">{{ $link['description'] }}</div>
                                        @if(($link['type'] ?? '') === 'competitor' && !empty($link['price_count']))
                                            <div class="small text-muted mt-1">
                                                Based on {{ number_format((int) $link['price_count']) }} price(s)
                                                @if(!empty($link['min_price_label']) && !empty($link['max_price_label']))
                                                    · {{ $link['min_price_label'] }} – {{ $link['max_price_label'] }}
                                                @endif
                                            </div>
                                        @elseif(($link['type'] ?? '') === 'competitor' && !empty($link['price_note']))
                                            <div class="small text-warning mt-1">{{ $link['price_note'] }}</div>
                                        @endif
                                        <div class="small text-break mt-1" style="opacity:.75;">{{ $link['url'] }}</div>
                                    </div>
                                    <span class="badge text-bg-primary align-self-center text-nowrap">
                                        Open <i class="fas fa-external-link-alt ms-1"></i>
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    @endif
                @endforeach

                <div class="alert alert-light border small mb-0">
                    Average prices are estimated from public numbers on each competitor’s search results page. Sites that block scraping or show no prices will show N/A.
                </div>
            @endif
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('compareForm');
    const submitBtn = document.getElementById('compareSubmitBtn');
    const iconEl = document.getElementById('compareSubmitIcon');
    const loadingEl = document.getElementById('compareLoading');
    const variantEl = document.getElementById('variant');
    const resetBtn = document.getElementById('compareResetBtn');

    if (!form || !submitBtn) return;

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
        if (iconEl) {
            iconEl.classList.remove('fa-search');
            iconEl.classList.add('fa-spinner', 'fa-spin');
        }
        if (loadingEl) {
            loadingEl.classList.remove('d-none');
            loadingEl.classList.add('d-flex');
        }
        submitBtn.disabled = true;
    });

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            window.location.href = @json(route('compare.index'));
        });
    }
});
</script>
@endsection
