@php
    $report = $report ?? null;
    $columns = $columns ?? [];
    $totalKeys = $totalKeys ?? [];
@endphp

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
                        <tr>
                            <th style="width:3rem;">Rank</th>
                            <th>{{ $report['person_label'] ?? 'Name' }}</th>
                            @foreach($columns as $col)
                                <th class="text-end">{{ $col['label'] }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($report['rows'] ?? []) as $i => $row)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td class="fw-semibold">{{ $row['name'] ?? '—' }}</td>
                                @foreach($columns as $col)
                                    @php
                                        $value = $row[$col['key']] ?? 0;
                                        $format = $col['format'] ?? 'int';
                                    @endphp
                                    <td class="text-end">
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
                                @foreach($columns as $col)
                                    @php
                                        $key = $col['key'];
                                        $format = $col['format'] ?? 'int';
                                        $showTotal = in_array($key, $totalKeys, true);
                                        $value = $report['totals'][$key] ?? null;
                                    @endphp
                                    <th class="text-end">
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
