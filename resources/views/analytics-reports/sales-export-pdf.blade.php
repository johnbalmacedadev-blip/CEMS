<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Car Sales Report Export</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; margin: 12px; color: #222; }
        h1 { font-size: 14px; margin: 0 0 8px 0; border-bottom: 2px solid #333; padding-bottom: 5px; }
        h2 { font-size: 11px; margin: 14px 0 6px 0; border-bottom: 1px solid #ccc; padding-bottom: 4px; }
        .meta { color: #555; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #d0d0d0; padding: 5px; vertical-align: top; }
        th { background: #f2f2f2; text-align: left; }
        .text-right { text-align: right; }
        .summary-grid td { width: 50%; }
        .muted { color: #666; }
    </style>
</head>
<body>
    <h1>Car Sales Report — Released Units</h1>
    <p class="meta">
        <strong>Period:</strong> {{ $periodOptions[$selectedPeriod] ?? $selectedPeriod }}<br>
        <strong>Date Filter:</strong> {{ $activeRangeLabel ?? '' }}<br>
        <strong>Location:</strong> {{ ($selectedLocation ?? '') !== '' ? $selectedLocation : 'All Locations' }}<br>
        <strong>Generated:</strong> {{ date('F j, Y g:i A') }}
    </p>

    @if(!empty($summary) && is_array($summary))
        <h2>Summary</h2>
        <table class="summary-grid">
            <tbody>
                @foreach(array_chunk($summary, 2) as $pair)
                    <tr>
                        @foreach($pair as $s)
                            <td>
                                <strong>{{ $s['label'] ?? '' }}</strong><br>
                                @if(!empty($s['is_composite']) && !empty($s['items']))
                                    @foreach($s['items'] as $item)
                                        {{ $item['label'] ?? '' }}:
                                        @if(!empty($item['is_currency']))
                                            ₱{{ number_format((float) ($item['value'] ?? 0), 2) }}
                                        @else
                                            {{ number_format((float) ($item['value'] ?? 0), 0) }}
                                        @endif
                                        @if(!$loop->last)<br>@endif
                                    @endforeach
                                @elseif(!empty($s['is_currency']))
                                    ₱{{ number_format((float) ($s['value'] ?? 0), 2) }}
                                @elseif(!empty($s['suffix']))
                                    {{ number_format((float) ($s['value'] ?? 0), 1) }}{{ $s['suffix'] }}
                                @else
                                    {{ number_format((float) ($s['value'] ?? 0), 0) }}
                                @endif
                                @if(!empty($s['location_breakdown']) && is_array($s['location_breakdown']))
                                    <div style="font-size: 10px; color: #555; margin-top: 4px;">
                                        @foreach($s['location_breakdown'] as $loc)
                                            {{ $loc['label'] ?? '' }}: {{ number_format((int) ($loc['count'] ?? 0)) }}@if(!$loop->last)<br>@endif
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                        @endforeach
                        @if(count($pair) === 1)
                            <td></td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if(!empty($tables['miscellaneous']))
        <h2>Miscellaneous</h2>
        <table>
            <thead>
                <tr>
                    <th>Transaction Date</th>
                    <th>Location</th>
                    <th>Description</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tables['miscellaneous'] as $row)
                    <tr>
                        <td>{{ $row['transaction_date_label'] ?? $row['transaction_date'] ?? '' }}</td>
                        <td>{{ $row['location'] ?? '—' }}</td>
                        <td>{{ $row['description'] ?? '' }}</td>
                        <td class="text-right">₱{{ number_format((float) ($row['amount'] ?? 0), 2) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="3"><strong>Total</strong></td>
                    <td class="text-right"><strong>₱{{ number_format((float) ($tables['miscellaneous_total'] ?? 0), 2) }}</strong></td>
                </tr>
            </tbody>
        </table>
    @endif

    @if(!empty($charts) && is_array($charts))
        <h2>Charts</h2>
        @foreach($charts as $chartConfig)
            @include('analytics-reports.partials.chart-svg-pdf', ['chart' => is_array($chartConfig) ? $chartConfig : []])
        @endforeach
    @endif

    @if(!empty($tables['top_makes']))
        <h2>All Makes</h2>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Make</th>
                    <th class="text-right">Units</th>
                    <th class="text-right">Gross Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tables['top_makes'] as $i => $row)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $row['label'] ?? '' }}</td>
                        <td class="text-right">{{ number_format((float) ($row['count'] ?? 0), 0) }}</td>
                        <td class="text-right">₱{{ number_format((float) ($row['sales'] ?? 0), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2"><strong>Total</strong></td>
                    <td class="text-right"><strong>{{ number_format((float) collect($tables['top_makes'])->sum('count'), 0) }}</strong></td>
                    <td class="text-right"><strong>₱{{ number_format((float) collect($tables['top_makes'])->sum('sales'), 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    @endif

    @if(!empty($tables['top_models']))
        <h2>All Models</h2>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Model</th>
                    <th class="text-right">Units</th>
                    <th class="text-right">Gross Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tables['top_models'] as $i => $row)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $row['label'] ?? '' }}</td>
                        <td class="text-right">{{ number_format((float) ($row['count'] ?? 0), 0) }}</td>
                        <td class="text-right">₱{{ number_format((float) ($row['sales'] ?? 0), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2"><strong>Total</strong></td>
                    <td class="text-right"><strong>{{ number_format((float) collect($tables['top_models'])->sum('count'), 0) }}</strong></td>
                    <td class="text-right"><strong>₱{{ number_format((float) collect($tables['top_models'])->sum('sales'), 2) }}</strong></td>
                </tr>
            </tfoot>
        </table>
    @endif

    @if(!empty($tables['fastest_models']))
        <h2>Fastest-Selling Models (min 3 units)</h2>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Model</th>
                    <th class="text-right">Avg Days</th>
                    <th class="text-right">Units</th>
                    <th class="text-right">Sales</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tables['fastest_models'] as $i => $row)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $row['label'] ?? '' }}</td>
                        <td class="text-right">{{ number_format((float) ($row['avg_days'] ?? 0), 1) }}</td>
                        <td class="text-right">{{ number_format((float) ($row['count'] ?? 0), 0) }}</td>
                        <td class="text-right">₱{{ number_format((float) ($row['sales'] ?? 0), 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if(empty($hasData))
        <p class="muted">No released units found for this date range.</p>
    @endif
</body>
</html>
