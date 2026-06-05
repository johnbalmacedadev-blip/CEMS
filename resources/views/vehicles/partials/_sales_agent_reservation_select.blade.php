@php
    $currentValue = $currentValue ?? '';
    $required = $required ?? false;
@endphp
<select id="{{ $selectId }}" name="{{ $name }}" class="form-select ts-sales-agent-select" autocomplete="off" data-placeholder="Search sales agent…" @if($required) required @endif>
    <option value="">— Select sales agent —</option>
    @if($currentValue !== '' && ! ($salesAgentsList ?? collect())->contains('name', $currentValue))
        <option value="{{ $currentValue }}" selected>{{ $currentValue }} (legacy)</option>
    @endif
    @foreach($salesAgentsList ?? [] as $sa)
        <option value="{{ $sa->name }}" @selected($currentValue === $sa->name)>{{ $sa->name }}@if($sa->sales_agent_id) ({{ $sa->sales_agent_id }})@endif</option>
    @endforeach
</select>
