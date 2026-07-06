@php
    $counts = $counts ?? ['A' => 0, 'R' => 0, 'RL' => 0, 'F' => 0];
    $meta = $statusMeta ?? [];
    $compact = !empty($compact);
@endphp
<div class="fin-status-badges {{ $compact ? 'fin-status-badges--compact' : '' }}">
    @foreach($meta as $item)
        @php $value = (int) ($counts[$item['key']] ?? 0); @endphp
        <span class="fin-status-pill {{ $item['pill_class'] ?? '' }}" title="{{ ($item['label'] ?? $item['key']) . ': ' . number_format($value) }}">
            <span class="fin-status-dot {{ $item['dot_class'] ?? '' }}" aria-hidden="true"></span>
            <span class="fin-status-pill__label">{{ $item['label'] ?? $item['key'] }}</span>
            <span class="fin-status-pill__count">{{ number_format($value) }}</span>
        </span>
    @endforeach
</div>
