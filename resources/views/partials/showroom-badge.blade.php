@php
    $label = isset($name) ? trim((string) $name) : '';
    $emptyText = $empty ?? '—';
@endphp

@if($label === '' || $label === '—')
    <span class="text-muted">{{ $emptyText }}</span>
@elseif(\App\Support\ShowroomBadge::isKnownShowroom($label))
    <span class="badge rounded-pill {{ \App\Support\ShowroomBadge::badgeClass($label) }}">{{ $label }}</span>
@else
    {{ $label }}
@endif
