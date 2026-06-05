@extends('layouts.app')

@section('title', 'Manage Vehicle Images - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <img src="{{ asset('images/CAREMPIRE_LOGO.png') }}" alt="CAR EMPIRE Logo" onerror="this.style.display='none';">
            </a>
            
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                        <span class="badge bg-{{ Auth::user()->isAdmin() ? 'danger' : 'primary' }} ms-1">
                            {{ ucfirst(Auth::user()->role) }}
                        </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
            <div class="position-sticky pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('vehicles.index') }}">
                            <i class="fas fa-car me-2"></i>Unit Report
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-images me-2"></i>Manage Vehicle Images
                </h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Vehicle
                    </a>
                </div>
            </div>

            <!-- Vehicle Info Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">{{ $vehicle->full_name }}</h5>
                    <p class="text-muted mb-0">Plate Number: {{ $vehicle->plate_number }}</p>
                </div>
            </div>

            <!-- Primary Image Section -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-star me-2"></i>Primary Image
                    </h5>
                </div>
                <div class="card-body">
                    @if($vehicle->primaryImage)
                        <div class="text-center">
                            <div class="d-inline-block" style="width: 300px; height: 300px; border: 2px solid #007bff; border-radius: 8px; overflow: hidden;">
                                <img src="{{ $vehicle->primaryImage->thumbnail_url }}" 
                                     alt="Primary Vehicle Image" 
                                     class="w-100 h-100" 
                                     style="object-fit: cover;">
                            </div>
                            <p class="mt-3 mb-0">
                                <span class="badge bg-primary">{{ $vehicle->primaryImage->original_name }}</span>
                            </p>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-image fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No primary image set. Upload images below to set one.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- All Images Section -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-images me-2"></i>All Vehicle Images ({{ $vehicle->images->count() }}/5)
                    </h5>
                    @if($vehicle->images->count() < 5)
                        <button type="button" class="btn btn-sm btn-primary" onclick="openImageUploadModal()">
                            <i class="fas fa-plus me-1"></i>Add Images
                        </button>
                    @else
                        <span class="badge bg-warning">Maximum images reached</span>
                    @endif
                </div>
                <div class="card-body">
                    @if($vehicle->images->count() > 0)
                        <div class="row" id="vehicleImages">
                            @foreach($vehicle->images as $image)
                                <div class="col-md-4 mb-3" data-image-id="{{ $image->id }}">
                                    <div class="card image-card {{ $image->is_primary ? 'border-primary' : '' }}">
                                        <div class="position-relative">
                                            <img src="{{ $image->thumbnail_url }}" class="card-img-top" alt="Vehicle Image" style="height: 200px; object-fit: cover;">
                                            @if($image->is_primary)
                                                <span class="badge bg-primary position-absolute top-0 start-0 m-2">Primary</span>
                                            @endif
                                            <div class="position-absolute top-0 end-0 m-2">
                                                <div class="btn-group-vertical">
                                                    @if(!$image->is_primary)
                                                        <button type="button" class="btn btn-sm btn-success" onclick="setPrimaryImage({{ $image->id }})" title="Set as Primary">
                                                            <i class="fas fa-star"></i>
                                                        </button>
                                                    @endif
                                                    <button type="button" class="btn btn-sm btn-danger" onclick="deleteImage({{ $image->id }})" title="Delete Image">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-2">
                                            <small class="text-muted">{{ $image->original_name }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-images fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No images uploaded yet.</p>
                            <button type="button" class="btn btn-primary" onclick="openImageUploadModal()">
                                <i class="fas fa-plus me-1"></i>Upload Images
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>

<style>
    .image-card {
        transition: transform 0.2s ease-in-out;
        cursor: pointer;
        position: relative;
    }
    .image-card:hover {
        transform: translateY(-5px);
    }
    .image-card.border-primary {
        border: 2px solid #007bff !important;
    }
    .image-card .position-absolute .btn-group-vertical {
        opacity: 0;
        transition: opacity 0.2s ease-in-out;
    }
    .image-card:hover .position-absolute .btn-group-vertical {
        opacity: 1;
    }
    .image-card img {
        filter: brightness(0.9);
        transition: filter 0.2s ease-in-out;
    }
    .image-card:hover img {
        filter: brightness(0.7);
    }
</style>

<!-- Image Upload Modal -->
<div class="modal fade" id="imageUploadModal" tabindex="-1" aria-labelledby="imageUploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageUploadModalLabel">Upload Vehicle Images</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="imageUploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="images" class="form-label">Select Images (Max {{ 5 - $vehicle->images->count() }} images remaining, 5MB each)</label>
                        <input type="file" class="form-control" id="images" name="images[]" multiple accept="image/*" required>
                        <div class="form-text">Supported formats: JPEG, PNG, JPG, GIF, WebP. Maximum file size: 5MB per image.</div>
                    </div>
                    <div id="imagePreview" class="row"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="uploadImages()">
                    <i class="fas fa-upload me-1"></i>Upload Images
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openImageUploadModal() {
    const modal = new bootstrap.Modal(document.getElementById('imageUploadModal'));
    modal.show();
}

function uploadImages() {
    const form = document.getElementById('imageUploadForm');
    const formData = new FormData(form);
    const fileInput = document.getElementById('images');
    
    if (!fileInput.files || fileInput.files.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'No Files Selected',
            text: 'Please select at least one image to upload.',
            confirmButtonColor: '#dc3545'
        });
        return;
    }

    Swal.fire({
        title: 'Uploading Images...',
        text: 'Please wait while we upload your images.',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    fetch(`{{ route('vehicles.images.store', $vehicle) }}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                confirmButtonColor: '#28a745'
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Upload Failed',
                text: data.message || 'Unknown error occurred',
                confirmButtonColor: '#dc3545'
            });
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Upload Failed',
            text: 'An error occurred while uploading images.',
            confirmButtonColor: '#dc3545'
        });
    });
}

function setPrimaryImage(imageId) {
    Swal.fire({
        title: 'Set as Primary?',
        text: 'This image will be set as the primary image for this vehicle.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Set Primary!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ route('vehicles.images.primary', [$vehicle, '']) }}/${imageId}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: data.message,
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
}

function deleteImage(imageId) {
    Swal.fire({
        title: 'Delete Image?',
        text: 'Are you sure you want to delete this image? This action cannot be undone!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete!',
        cancelButtonText: 'Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ route('vehicles.images.destroy', [$vehicle, '']) }}/${imageId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: data.message,
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed',
                        text: data.message,
                        confirmButtonColor: '#dc3545'
                    });
                }
            });
        }
    });
}
</script>
@endsection





