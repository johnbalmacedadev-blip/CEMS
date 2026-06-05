@php
    $currentValue = $currentValue ?? '';
    $required = $required ?? false;
@endphp
<select id="{{ $selectId }}" name="{{ $name }}" class="form-select ts-executive-select" autocomplete="off" data-placeholder="Search sales executive…" @if($required) required @endif>
    <option value="">— Select sales executive —</option>
    @if($currentValue !== '' && ! ($executiveAgents ?? collect())->contains('name', $currentValue))
        <option value="{{ $currentValue }}" selected>{{ $currentValue }} (legacy)</option>
    @endif
    @foreach($executiveAgents ?? [] as $exec)
        <option value="{{ $exec->name }}" @selected($currentValue === $exec->name)>{{ $exec->name }} ({{ $exec->executive_code }})</option>
    @endforeach
</select>
