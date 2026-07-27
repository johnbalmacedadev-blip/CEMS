@php
    $r = $video_posting_tracker ?? null;
@endphp
<div class="row g-3">
    <div class="col-md-8">
        <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $r->title ?? '') }}" required>
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="record_date" class="form-label">Record Date <span class="text-danger">*</span></label>
        <input type="date" class="form-control @error('record_date') is-invalid @enderror" id="record_date" name="record_date" value="{{ old('record_date', isset($r) ? $r->record_date->format('Y-m-d') : date('Y-m-d')) }}" required>
        @error('record_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label for="vlogger" class="form-label">Vlogger</label>
        <input type="text" class="form-control @error('vlogger') is-invalid @enderror" id="vlogger" name="vlogger" value="{{ old('vlogger', $r->vlogger ?? '') }}">
        @error('vlogger')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label for="category" class="form-label">Category</label>
        @php
            $categories = ['SOLO', 'DUO', 'FEATURED', 'FEEDBACK'];
            $currentCategory = old('category', $r->category ?? '');
        @endphp
        <select class="form-select @error('category') is-invalid @enderror" id="category" name="category">
            <option value="">— Select —</option>
            @foreach($categories as $opt)
                <option value="{{ $opt }}" {{ strcasecmp((string) $currentCategory, $opt) === 0 ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
            @if($currentCategory !== '' && ! collect($categories)->contains(fn ($c) => strcasecmp($c, (string) $currentCategory) === 0))
                <option value="{{ $currentCategory }}" selected>{{ $currentCategory }}</option>
            @endif
        </select>
        @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label for="showroom" class="form-label">Showroom</label>
        @php
            $showroomOptions = $showrooms ?? \App\Models\BranchLocation::ordered()->pluck('name');
            $currentShowroom = old('showroom', $r->showroom ?? '');
        @endphp
        <select class="form-select @error('showroom') is-invalid @enderror" id="showroom" name="showroom">
            <option value="">— Select —</option>
            @foreach($showroomOptions as $opt)
                <option value="{{ $opt }}" {{ strcasecmp((string) $currentShowroom, (string) $opt) === 0 ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
            @if($currentShowroom !== '' && ! $showroomOptions->contains(fn ($s) => strcasecmp((string) $s, (string) $currentShowroom) === 0))
                <option value="{{ $currentShowroom }}" selected>{{ $currentShowroom }}</option>
            @endif
        </select>
        @error('showroom')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label for="plate_number" class="form-label">Plate No.</label>
        <input type="text" class="form-control @error('plate_number') is-invalid @enderror" id="plate_number" name="plate_number" value="{{ old('plate_number', $r->plate_number ?? '') }}" placeholder="Links to vehicle when matched">
        @error('plate_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-6">
        <label for="featured_car_or_client" class="form-label">Featured Car / Client</label>
        <input type="text" class="form-control @error('featured_car_or_client') is-invalid @enderror" id="featured_car_or_client" name="featured_car_or_client" value="{{ old('featured_car_or_client', $r->featured_car_or_client ?? '') }}">
        @error('featured_car_or_client')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label for="date_uploaded_gdrive" class="form-label">Date Uploaded (GDrive)</label>
        <input type="date" class="form-control @error('date_uploaded_gdrive') is-invalid @enderror" id="date_uploaded_gdrive" name="date_uploaded_gdrive" value="{{ old('date_uploaded_gdrive', isset($r) && $r->date_uploaded_gdrive ? $r->date_uploaded_gdrive->format('Y-m-d') : '') }}">
        @error('date_uploaded_gdrive')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-3">
        <label for="date_posted_social" class="form-label">Date Posted (Social)</label>
        <input type="date" class="form-control @error('date_posted_social') is-invalid @enderror" id="date_posted_social" name="date_posted_social" value="{{ old('date_posted_social', isset($r) && $r->date_posted_social ? $r->date_posted_social->format('Y-m-d') : '') }}">
        @error('date_posted_social')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="gdrive_file_name" class="form-label">GDrive File Name</label>
        <input type="text" class="form-control @error('gdrive_file_name') is-invalid @enderror" id="gdrive_file_name" name="gdrive_file_name" value="{{ old('gdrive_file_name', $r->gdrive_file_name ?? '') }}">
        @error('gdrive_file_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
        <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
            @foreach(\App\Models\VideoPostingRecord::typeOptions() as $opt)
                <option value="{{ $opt }}" {{ old('type', $r->type ?? 'Video') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
        </select>
        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
            @foreach(\App\Models\VideoPostingRecord::statusOptions() as $opt)
                <option value="{{ $opt }}" {{ old('status', $r->status ?? 'Posted') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
            @endforeach
        </select>
        @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="platform" class="form-label">Platform</label>
        <input type="text" class="form-control @error('platform') is-invalid @enderror" id="platform" name="platform" value="{{ old('platform', $r->platform ?? '') }}" placeholder="e.g. Facebook">
        @error('platform')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="vehicle_id" class="form-label">Unit (Vehicle)</label>
        <select class="form-select @error('vehicle_id') is-invalid @enderror" id="vehicle_id" name="vehicle_id">
            <option value="">— Optional / auto by plate —</option>
            @foreach($vehicles as $v)
                <option value="{{ $v->id }}" {{ (string) old('vehicle_id', $r->vehicle_id ?? request('vehicle_id')) === (string) $v->id ? 'selected' : '' }}>
                    {{ $v->full_name }} @if($v->plate_number)({{ $v->plate_number }})@endif
                </option>
            @endforeach
        </select>
        @error('vehicle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-4">
        <label for="link_url" class="form-label">Link to Post</label>
        <input type="url" class="form-control @error('link_url') is-invalid @enderror" id="link_url" name="link_url" value="{{ old('link_url', $r->link_url ?? '') }}" placeholder="https://...">
        @error('link_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label for="notes" class="form-label">Notes</label>
        <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="2">{{ old('notes', $r->notes ?? '') }}</textarea>
        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
