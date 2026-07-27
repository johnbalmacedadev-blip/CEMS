<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales Report Export</title>
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
    <h1>Sales Report — Released Units</h1>
    <p class="meta">
        <strong>Period:</strong> {{ $periodOptions[$selectedPeriod] ?? $selectedPeriod }}<br>
        <strong>Date Filter:</strong> {{ $activeRangeLabel ?? '' }}<br>
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
                                @if(!empty($s['is_currency']))
                                    ₱{{ number_format((float) ($s['value'] ?? 0), 2) }}
                                @elseif(!empty($s['suffix']))
                                    {{ number_format((float) ($s['value'] ?? 0), 1) }}{{ $s['suffix'] }}
                                @else
                                    {{ number_format((float) ($s['value'] ?? 0), 0) }}
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

    @if(!empty($charts) && is_array($charts))
        <h2>Charts</h2>
        @foreach($charts as $chartConfig)
            @include('analytics-reports.partials.chart-svg-pdf', ['chart' => is_array($chartConfig) ? $chartConfig : []])
        @endforeach
    @endif

    @if(!empty($tables['top_makes']))
        <h2>Top Makes</h2>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Make</th>
                    <th class="text-right">Units</th>
                    <th class="text-right">Sales</th>
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
        </table>
    @endif

    @if(!empty($tables['top_models']))
        <h2>Top Models</h2>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Model</th>
                    <th class="text-right">Units</th>
                    <th class="text-right">Sales</th>
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

    @if(!empty($tables['monthly']))
        <h2>Monthly Breakdown</h2>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th class="text-right">Units Released</th>
                    <th class="text-right">Sales Amount</th>
                    <th class="text-right">Avg Days to Sell</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tables['monthly'] as $row)
                    <tr>
                        <td>{{ $row['label'] ?? '' }}</td>
                        <td class="text-right">{{ number_format((float) ($row['count'] ?? 0), 0) }}</td>
                        <td class="text-right">₱{{ number_format((float) ($row['sales'] ?? 0), 2) }}</td>
                        <td class="text-right">{{ number_format((float) ($row['avg_days'] ?? 0), 1) }}</td>
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
