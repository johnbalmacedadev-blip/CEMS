@php
    $memo = $memo ?? null;
    $isEdit = isset($memo);
    $formAction = $isEdit ? route('memos.update', $memo) : route('memos.store');
@endphp

<div class="row g-3">
    <div class="col-12">
        <label for="title" class="form-label">Title <span class="text-muted">(optional)</span></label>
        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
            value="{{ old('title', $memo->title ?? '') }}" placeholder="e.g. Shop policy update">
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="body" class="form-label">Memo text</label>
        <textarea class="form-control @error('body') is-invalid @enderror" id="body" name="body" rows="6"
            placeholder="Write your memo here...">{{ old('body', $memo->body ?? '') }}</textarea>
        <div class="form-text">Provide memo text, a file, or a link — at least one is required.</div>
        @error('body')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="file" class="form-label">Upload file</label>
        <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file"
            accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.webp,.txt,.zip">
        @if($isEdit && $memo->isFile())
            <div class="form-text">Current: <a href="{{ $memo->display_url }}" target="_blank" rel="noopener">View file</a>. Upload to replace.</div>
        @else
            <div class="form-text">Max 20MB.</div>
        @endif
        @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <label for="link_url" class="form-label">Or add a link</label>
        <input type="url" class="form-control @error('link_url') is-invalid @enderror" id="link_url" name="link_url"
            value="{{ old('link_url', $memo->link_url ?? '') }}" placeholder="https://...">
        @error('link_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-1"></i>{{ $isEdit ? 'Update Memo' : 'Save Memo' }}
        </button>
        <a href="{{ route('memos.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </div>
</div>
