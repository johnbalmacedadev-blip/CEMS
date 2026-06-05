@extends('layouts.app')

@section('title', 'Add Recommendation - Car Empire Management System')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-clipboard-list me-2"></i>Add Recommendation
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('recommendation-tracker.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Recommendation Tracker
            </a>
        </div>
    </div>

    <form action="{{ route('recommendation-tracker.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('recommendation-tracker._form')
        <div class="mb-4">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save Recommendation</button>
            <a href="{{ route('recommendation-tracker.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
(function() {
    var sel = document.getElementById('vehicle_id');
    var yearInp = document.getElementById('year');
    var makeInp = document.getElementById('make');
    var modelInp = document.getElementById('model');
    var customerInp = document.getElementById('customer');
    function fillFromVehicle(allowClear) {
        var opt = sel && sel.options[sel.selectedIndex];
        if (!opt || !opt.value) {
            if (allowClear) {
                if (yearInp) yearInp.value = '';
                if (makeInp) makeInp.value = '';
                if (modelInp) modelInp.value = '';
                if (customerInp) customerInp.value = '';
            }
            return;
        }
        if (yearInp) yearInp.value = opt.getAttribute('data-year') || '';
        if (makeInp) makeInp.value = opt.getAttribute('data-make') || '';
        if (modelInp) modelInp.value = opt.getAttribute('data-model') || '';
        if (customerInp) customerInp.value = opt.getAttribute('data-customer') || '';
    }
    if (sel) {
        sel.addEventListener('change', function() { fillFromVehicle(true); });
        fillFromVehicle(false);
    }
})();
document.getElementById('images')?.addEventListener('change', function(e) {
    var preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    var files = e.target.files;
    for (var i = 0; i < files.length; i++) {
        var file = files[i];
        if (!file.type.startsWith('image/')) continue;
        var reader = new FileReader();
        reader.onload = (function(f) {
            return function(ev) {
                var col = document.createElement('div');
                col.className = 'col-6 col-sm-4 col-md-3 col-lg-2';
                col.innerHTML = '<div class="border rounded overflow-hidden bg-light"><img src="' + ev.target.result + '" alt="" class="img-fluid w-100" style="object-fit: cover; height: 100px;"></div>';
                preview.appendChild(col);
            };
        })(file);
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
