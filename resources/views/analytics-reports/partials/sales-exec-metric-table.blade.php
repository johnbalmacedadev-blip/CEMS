@php
    $report = $report ?? null;
    $columns = $columns ?? [];
    $totalKeys = $totalKeys ?? [];
    // Optional column groups: ['sales' => ['label' => 'Sales', 'tone' => 'blue'], ...]; each column may name a 'group'.
    $groups = $groups ?? [];
    $hasGroups = $groups !== [] && collect($columns)->contains(fn ($c) => ! empty($c['group']));

    // Contiguous header segments and per-column edge flags for the group borders.
    $segments = [];
    foreach ($columns as $i => $col) {
        $g = $col['group'] ?? null;
        if ($segments !== [] && $segments[count($segments) - 1]['group'] === $g) {
            $segments[count($segments) - 1]['span']++;
        } else {
            $segments[] = ['group' => $g, 'span' => 1];
        }
    }
    $groupClass = function (int $i) use ($columns, $groups) {
        $g = $columns[$i]['group'] ?? null;
        if (! $g || ! isset($groups[$g])) {
            return '';
        }
        $classes = ['exec-group', 'exec-group--'.($groups[$g]['tone'] ?? 'blue')];
        if (($columns[$i - 1]['group'] ?? null) !== $g) {
            $classes[] = 'exec-group--start';
        }
        if (($columns[$i + 1]['group'] ?? null) !== $g) {
            $classes[] = 'exec-group--end';
        }

        return implode(' ', $classes);
    };
@endphp

@once
    <style>
        .exec-group--blue { --bs-table-bg: #e7f1ff; }
        .exec-group--green { --bs-table-bg: #e6f4ea; }
        .exec-group--blue.exec-group--start { border-left: 2px solid #0d6efd !important; }
        .exec-group--blue.exec-group--end { border-right: 2px solid #0d6efd !important; }
        .exec-group--green.exec-group--start { border-left: 2px solid #198754 !important; }
        .exec-group--green.exec-group--end { border-right: 2px solid #198754 !important; }
        .exec-group-head th { text-transform: uppercase; letter-spacing: .04em; font-size: .78rem; }
        .exec-group-head .exec-group-hint { text-transform: none; letter-spacing: normal; font-size: .78rem; color: #495057; margin-top: .15rem; }
        .exec-group-head .exec-group--blue { color: #084298; border-top: 2px solid #0d6efd !important; }
        .exec-group-head .exec-group--green { color: #0f5132; border-top: 2px solid #198754 !important; }
        tfoot .exec-group--blue { border-bottom: 2px solid #0d6efd !important; }
        tfoot .exec-group--green { border-bottom: 2px solid #198754 !important; }
    </style>
@endonce

@if(!$report)
    <div class="alert alert-info mb-0">No metric report data available.</div>
@else
    <div class="card">
        <div class="card-header bg-white">
            <div class="fw-semibold">{{ $report['title'] ?? 'Report' }}</div>
            @if(!empty($report['subtitle']))
                <div class="small text-muted">{{ $report['subtitle'] }}</div>
            @endif
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover mb-0 align-middle">
                    <thead class="table-light">
                        @if($hasGroups)
                            <tr class="exec-group-head">
                                <th colspan="2"></th>
                                @foreach($segments as $seg)
                                    @if($seg['group'] && isset($groups[$seg['group']]))
                                        @php $tone = $groups[$seg['group']]['tone'] ?? 'blue'; @endphp
                                        <th colspan="{{ $seg['span'] }}" class="text-center exec-group exec-group--{{ $tone }} exec-group--start exec-group--end">
                                            {{ $groups[$seg['group']]['label'] }}
                                            @if(!empty($groups[$seg['group']]['hint']))
                                                <span class="exec-group-hint d-block fw-normal">{{ $groups[$seg['group']]['hint'] }}</span>
                                            @endif
                                        </th>
                                    @else
                                        <th colspan="{{ $seg['span'] }}"></th>
                                    @endif
                                @endforeach
                            </tr>
                        @endif
                        <tr>
                            <th style="width:3rem;">Rank</th>
                            <th>{{ $report['person_label'] ?? 'Name' }}</th>
                            @foreach($columns as $ci => $col)
                                <th class="text-end {{ $groupClass($ci) }}">{{ $col['label'] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($report['rows'] ?? []) as $i => $row)
                            <tr class="sales-exec-person-row" title="Click to view this person's cars">
                                <td>{{ $i + 1 }}</td>
                                <td class="fw-semibold">
                                    <a href="#" class="sales-exec-person-link text-decoration-none" data-person="{{ $row['person_ref'] ?? '' }}" data-context="{{ $context ?? '' }}">{{ $row['name'] ?? '—' }}</a>
                                </td>
                                @foreach($columns as $ci => $col)
                                    @php
                                        $value = $row[$col['key']] ?? 0;
                                        $format = $col['format'] ?? 'int';
                                    @endphp
                                    <td class="text-end {{ $groupClass($ci) }}">
                                        @if($format === 'money')
                                            ₱{{ number_format((float) $value, 2) }}
                                        @elseif($format === 'pct')
                                            {{ number_format((float) $value, 1) }}%
                                        @else
                                            {{ number_format((float) $value, 0) }}
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 2 + count($columns) }}" class="text-center text-muted py-4">
                                    No rows for this metric in the selected range.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if(!empty($report['rows']) && !empty($report['totals']))
                        <tfoot class="table-light">
                            <tr>
                                <th>—</th>
                                <th>TOTAL</th>
                                @foreach($columns as $ci => $col)
                                    @php
                                        $key = $col['key'];
                                        $format = $col['format'] ?? 'int';
                                        $showTotal = in_array($key, $totalKeys, true);
                                        $value = $report['totals'][$key] ?? null;
                                    @endphp
                                    <th class="text-end {{ $groupClass($ci) }}">
                                        @if($showTotal && $value !== null)
                                            @if($format === 'money')
                                                ₱{{ number_format((float) $value, 2) }}
                                            @elseif($format === 'pct')
                                                —
                                            @else
                                                {{ number_format((float) $value, 0) }}
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </th>
                                @endforeach
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
@endif
