@extends('layouts.app')

@section('title', 'Car Video Boost Report - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-bullhorn me-2"></i>Car Video Boost Report
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('home') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-home me-1"></i>Back to Main Menu
            </a>
            <a href="{{ route('vehicles.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-car me-1"></i>Unit Report
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <p class="text-muted mb-4">All video ad and boosting details across units. Add or manage records here or per unit in Unit Report → Ads/Boosting.</p>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('car-video-boost-report.index') }}" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small">Posted From</label>
                    <input type="date" class="form-control form-control-sm" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small">Posted To</label>
                    <input type="date" class="form-control form-control-sm" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small">Search unit</label>
                    <input type="text" class="form-control form-control-sm" name="search" placeholder="Plate, make, model..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm me-2"><i class="fas fa-search me-1"></i>Filter</button>
                    <a href="{{ route('car-video-boost-report.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Report table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-video me-2"></i>Video Ad / Boost Records</h5>
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#vehicleAdReportModal" id="addVehicleAdBtn">
                <i class="fas fa-plus me-1"></i>Add New
            </button>
        </div>
        <div class="card-body p-0">
            @if($ads->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Unit (Vehicle)</th>
                                <th>Posted Date</th>
                                <th>Video Link</th>
                                <th>Social Media Post</th>
                                <th>Ads/Boost Link</th>
                                <th>Campaign ID</th>
                                <th>Ad ID</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ads as $ad)
                                <tr>
                                    <td>
                                        @if($ad->vehicle)
                                            <a href="{{ route('vehicles.show', $ad->vehicle) }}">{{ $ad->vehicle->full_name }}</a>
                                            @if($ad->vehicle->plate_number)
                                                <br><small class="text-muted">{{ $ad->vehicle->plate_number }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $ad->posted_date->format('M d, Y') }}</td>
                                    <td>
                                        @forelse($ad->video_links_list as $index => $url)
                                            <a href="{{ $url }}" target="_blank" rel="noopener" class="text-primary d-inline-block mb-1">
                                                <i class="fas fa-external-link-alt me-1"></i>Video{{ count($ad->video_links_list) > 1 ? ' ' . ($index + 1) : '' }}
                                            </a>@if(!$loop->last)<br>@endif
                                        @empty
                                            <span class="text-muted">—</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        @forelse($ad->social_media_links_list as $item)
                                            <a href="{{ $item['link'] }}" target="_blank" rel="noopener" class="text-primary d-inline-block mb-1">
                                                <i class="fas fa-external-link-alt me-1"></i>{{ $item['channel'] }}
                                            </a>@if(!$loop->last)<br>@endif
                                        @empty
                                            <span class="text-muted">—</span>
                                        @endforelse
                                    </td>
                                    <td>
                                        @if($ad->ads_boost_link)
                                            <a href="{{ $ad->ads_boost_link }}" target="_blank" rel="noopener" class="text-primary"><i class="fas fa-external-link-alt me-1"></i>View</a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $ad->campaign_id ?: '—' }}</td>
                                    <td>{{ $ad->ad_id ?: '—' }}</td>
                                    <td class="text-center">
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary edit-vehicle-ad-btn"
                                                title="Edit"
                                                data-bs-toggle="modal"
                                                data-bs-target="#vehicleAdReportModal"
                                                data-ad-id="{{ $ad->id }}"
                                                data-vehicle-id="{{ $ad->vehicle_id ?? '' }}"
                                                data-vehicle-label="{{ $ad->vehicle ? ($ad->vehicle->plate_number . ' — ' . $ad->vehicle->full_name) : '' }}"
                                                data-posted-date="{{ $ad->posted_date->format('Y-m-d') }}"
                                                data-video-links="{{ e(json_encode($ad->video_links_list)) }}"
                                                data-social-media-links="{{ e(json_encode($ad->social_media_links_list)) }}"
                                                data-ads-boost-link="{{ $ad->ads_boost_link ?? '' }}"
                                                data-campaign-id="{{ $ad->campaign_id ?? '' }}"
                                                data-ad-id-value="{{ $ad->ad_id ?? '' }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger delete-vehicle-ad-btn"
                                                title="Delete"
                                                data-ad-id="{{ $ad->id }}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        @if($ad->vehicle)
                                            <a href="{{ route('vehicles.show', $ad->vehicle) }}#adsBoostingDetailsCollapse" class="btn btn-sm btn-outline-secondary" title="View on unit">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3 pb-3">
                    {{ $ads->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No video ad / boost records yet</h5>
                    <p class="text-muted mb-3">Click <strong>Add New</strong> to record video ad and boosting details for a unit.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#vehicleAdReportModal">
                        <i class="fas fa-plus me-1"></i>Add New
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Add / Edit Vehicle Ad Modal -->
<div class="modal fade" id="vehicleAdReportModal" tabindex="-1" aria-labelledby="vehicleAdReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="vehicleAdReportModalLabel">Add Video Ad Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="report_ad_id" value="">
                <input type="hidden" id="report_vehicle_id" value="">

                <div class="mb-3">
                    <label for="report_vehicle_search" class="form-label">Search vehicle <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="report_vehicle_search" placeholder="Plate number, make, model, or year..." autocomplete="off">
                    <div id="report_vehicle_search_results" class="list-group mt-2 shadow-sm" style="display:none; max-height: 220px; overflow-y: auto;"></div>
                    <div id="report_vehicle_selected" class="alert alert-success py-2 mt-2 mb-0" style="display:none;">
                        <i class="fas fa-check-circle me-1"></i><span id="report_vehicle_selected_label"></span>
                        <button type="button" class="btn btn-link btn-sm p-0 ms-2" id="report_vehicle_clear">Change</button>
                    </div>
                </div>

                <hr>

                <form id="vehicleAdReportForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="report_posted_date" class="form-label">Posted Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" id="report_posted_date" name="posted_date" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        @include('partials.vehicle-ad-multi-links-fields', ['prefix' => 'report_', 'inputSize' => 'sm'])
                    </div>
                    <div class="mb-3">
                        <label for="report_ads_boost_link" class="form-label">Link to Ads or Boost</label>
                        <input type="url" class="form-control form-control-sm" id="report_ads_boost_link" name="ads_boost_link" placeholder="https://...">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="report_campaign_id" class="form-label">Campaign ID</label>
                            <input type="text" class="form-control form-control-sm" id="report_campaign_id" name="campaign_id" placeholder="Enter campaign ID">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="report_ad_id_value" class="form-label">Ad ID</label>
                            <input type="text" class="form-control form-control-sm" id="report_ad_id_value" name="ad_id" placeholder="Enter ad ID">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" id="saveVehicleAdReportBtn">
                    <i class="fas fa-save me-1"></i>Save
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const vehicleSearchUrl = @json(route('expenses.vehicles.search'));
    const storeAdUrl = @json(route('car-video-boost-report.store-ad'));
    const updateAdUrlTemplate = @json(route('car-video-boost-report.update-ad', ['vehicleAd' => 0]));
    const deleteAdUrlTemplate = @json(route('car-video-boost-report.destroy-ad', ['vehicleAd' => 0]));
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const adModal = document.getElementById('vehicleAdReportModal');
    const adModalLabel = document.getElementById('vehicleAdReportModalLabel');
    const reportAdIdInput = document.getElementById('report_ad_id');
    const reportVehicleIdInput = document.getElementById('report_vehicle_id');
    const reportVehicleSearch = document.getElementById('report_vehicle_search');
    const reportVehicleResults = document.getElementById('report_vehicle_search_results');
    const reportVehicleSelected = document.getElementById('report_vehicle_selected');
    const reportVehicleSelectedLabel = document.getElementById('report_vehicle_selected_label');
    const reportVehicleClear = document.getElementById('report_vehicle_clear');
    const saveVehicleAdReportBtn = document.getElementById('saveVehicleAdReportBtn');
    const addVehicleAdBtn = document.getElementById('addVehicleAdBtn');

    let vehicleSearchTimer = null;

    function resetVehicleAdModal() {
        reportAdIdInput.value = '';
        reportVehicleIdInput.value = '';
        reportVehicleSearch.value = '';
        reportVehicleSearch.style.display = 'block';
        reportVehicleResults.style.display = 'none';
        reportVehicleResults.innerHTML = '';
        reportVehicleSelected.style.display = 'none';
        reportVehicleSelectedLabel.textContent = '';
        document.getElementById('vehicleAdReportForm').reset();
        document.getElementById('report_posted_date').value = new Date().toISOString().split('T')[0];
        if (window.VehicleAdLinkFields) {
            VehicleAdLinkFields.reset('report_', [''], [{ channel: 'Facebook', link: '' }]);
        }
        adModalLabel.textContent = 'Add Video Ad Details';
        saveVehicleAdReportBtn.innerHTML = '<i class="fas fa-save me-1"></i>Save';
    }

    function selectVehicle(vehicle) {
        reportVehicleIdInput.value = vehicle.id;
        const label = (vehicle.plate_number || '') + ' — ' + (vehicle.full_name || ((vehicle.year || '') + ' ' + (vehicle.make || '') + ' ' + (vehicle.model || '')).trim());
        reportVehicleSelectedLabel.textContent = label.trim();
        reportVehicleSelected.style.display = 'block';
        reportVehicleSearch.style.display = 'none';
        reportVehicleResults.style.display = 'none';
        reportVehicleResults.innerHTML = '';
    }

    function clearSelectedVehicle() {
        reportVehicleIdInput.value = '';
        reportVehicleSelected.style.display = 'none';
        reportVehicleSelectedLabel.textContent = '';
        reportVehicleSearch.style.display = 'block';
        reportVehicleSearch.value = '';
        reportVehicleSearch.focus();
    }

    function searchVehicles(query) {
        if (!query || query.length < 1) {
            reportVehicleResults.style.display = 'none';
            reportVehicleResults.innerHTML = '';
            return;
        }
        fetch(vehicleSearchUrl + '?q=' + encodeURIComponent(query), {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        })
            .then(r => r.json())
            .then(vehicles => {
                reportVehicleResults.innerHTML = '';
                if (!vehicles.length) {
                    reportVehicleResults.innerHTML = '<div class="list-group-item text-muted small">No vehicles found.</div>';
                } else {
                    vehicles.forEach(v => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'list-group-item list-group-item-action py-2';
                        item.textContent = (v.plate_number || '') + ' — ' + (v.full_name || '');
                        item.addEventListener('click', () => selectVehicle(v));
                        reportVehicleResults.appendChild(item);
                    });
                }
                reportVehicleResults.style.display = 'block';
            })
            .catch(() => {
                reportVehicleResults.innerHTML = '<div class="list-group-item text-danger small">Search failed.</div>';
                reportVehicleResults.style.display = 'block';
            });
    }

    function fillFormFromButton(btn) {
        reportAdIdInput.value = btn.getAttribute('data-ad-id') || '';
        const vehicleId = btn.getAttribute('data-vehicle-id') || '';
        const vehicleLabel = btn.getAttribute('data-vehicle-label') || '';
        if (vehicleId) {
            reportVehicleIdInput.value = vehicleId;
            reportVehicleSelectedLabel.textContent = vehicleLabel;
            reportVehicleSelected.style.display = 'block';
            reportVehicleSearch.style.display = 'none';
        } else {
            clearSelectedVehicle();
        }
        document.getElementById('report_posted_date').value = btn.getAttribute('data-posted-date') || '';
        let videoLinks = [];
        let socialLinks = [];
        try { videoLinks = JSON.parse(btn.getAttribute('data-video-links') || '[]'); } catch (e) {}
        try { socialLinks = JSON.parse(btn.getAttribute('data-social-media-links') || '[]'); } catch (e) {}
        if (window.VehicleAdLinkFields) {
            VehicleAdLinkFields.reset('report_', videoLinks.length ? videoLinks : [''], socialLinks.length ? socialLinks : [{ channel: 'Facebook', link: '' }]);
        }
        document.getElementById('report_ads_boost_link').value = btn.getAttribute('data-ads-boost-link') || '';
        document.getElementById('report_campaign_id').value = btn.getAttribute('data-campaign-id') || '';
        document.getElementById('report_ad_id_value').value = btn.getAttribute('data-ad-id-value') || '';
    }

    function collectPayload() {
        const payload = {
            vehicle_id: reportVehicleIdInput.value,
            posted_date: document.getElementById('report_posted_date').value,
        };
        if (window.VehicleAdLinkFields) {
            Object.assign(payload, VehicleAdLinkFields.collect('report_'));
        }
        const optionalFields = [
            ['ads_boost_link', 'report_ads_boost_link'],
            ['campaign_id', 'report_campaign_id'],
            ['ad_id', 'report_ad_id_value'],
        ];
        optionalFields.forEach(([key, id]) => {
            const val = document.getElementById(id).value.trim();
            if (val) payload[key] = val;
        });
        return payload;
    }

    if (reportVehicleSearch) {
        reportVehicleSearch.addEventListener('input', function() {
            clearTimeout(vehicleSearchTimer);
            vehicleSearchTimer = setTimeout(() => searchVehicles(reportVehicleSearch.value.trim()), 250);
        });
    }

    if (reportVehicleClear) {
        reportVehicleClear.addEventListener('click', clearSelectedVehicle);
    }

    if (addVehicleAdBtn) {
        addVehicleAdBtn.addEventListener('click', resetVehicleAdModal);
    }

    if (window.VehicleAdLinkFields) {
        VehicleAdLinkFields.reset('report_', [''], [{ channel: 'Facebook', link: '' }]);
    }

    if (adModal) {
        adModal.addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            if (btn && btn.classList.contains('edit-vehicle-ad-btn')) {
                adModalLabel.textContent = 'Edit Video Ad Details';
                saveVehicleAdReportBtn.innerHTML = '<i class="fas fa-save me-1"></i>Update';
                fillFormFromButton(btn);
            } else {
                resetVehicleAdModal();
            }
        });
    }

    if (saveVehicleAdReportBtn) {
        saveVehicleAdReportBtn.addEventListener('click', function() {
            if (!reportVehicleIdInput.value) {
                Swal.fire({ icon: 'warning', title: 'Vehicle required', text: 'Please search and select a vehicle.' });
                return;
            }
            const form = document.getElementById('vehicleAdReportForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            const adId = reportAdIdInput.value;
            const isEdit = !!adId;
            const url = isEdit
                ? updateAdUrlTemplate.replace(/\/0(\/|$)/, '/' + adId + '$1')
                : storeAdUrl;
            const method = isEdit ? 'PUT' : 'POST';

            saveVehicleAdReportBtn.disabled = true;
            fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(collectPayload()),
            })
                .then(async r => {
                    const data = await r.json();
                    if (!r.ok) {
                        const msg = data.message || (data.errors ? Object.values(data.errors).flat().join(' ') : 'Save failed');
                        throw new Error(msg);
                    }
                    return data;
                })
                .then(data => {
                    bootstrap.Modal.getInstance(adModal).hide();
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved',
                        text: data.message || 'Video ad details saved.',
                        timer: 1800,
                        showConfirmButton: false,
                    }).then(() => location.reload());
                })
                .catch(err => {
                    Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'Save failed' });
                })
                .finally(() => {
                    saveVehicleAdReportBtn.disabled = false;
                });
        });
    }

    document.querySelectorAll('.delete-vehicle-ad-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const adId = this.getAttribute('data-ad-id');
            Swal.fire({
                title: 'Delete video ad details?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel',
            }).then(result => {
                if (!result.isConfirmed) return;
                const url = deleteAdUrlTemplate.replace(/\/0(\/|$)/, '/' + adId + '$1');
                fetch(url, {
                    method: 'DELETE',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                })
                    .then(async r => {
                        const data = await r.json();
                        if (!r.ok) throw new Error(data.message || 'Delete failed');
                        return data;
                    })
                    .then(data => {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted',
                            text: data.message || 'Video ad details deleted.',
                            timer: 1500,
                            showConfirmButton: false,
                        }).then(() => location.reload());
                    })
                    .catch(err => {
                        Swal.fire({ icon: 'error', title: 'Error', text: err.message || 'Delete failed' });
                    });
            });
        });
    });
});
</script>
@endsection
