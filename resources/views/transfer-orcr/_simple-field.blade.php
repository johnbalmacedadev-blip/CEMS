<div class="col-md-{{ $cols ?? 4 }}">
    <div class="form-field-cell">
        <label @if(!empty($id)) for="{{ $id }}" @endif class="form-label">{!! $label !!}</label>
        <div class="form-field-control">
            {{ $slot }}
        </div>
        <div class="form-field-extra" @if(empty($hint)) aria-hidden="true" style="display:none;" @endif>
            @if(!empty($hint))
                <div class="form-text">{{ $hint }}</div>
            @endif
        </div>
    </div>
</div>
