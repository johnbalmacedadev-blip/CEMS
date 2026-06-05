<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Financial Report Export</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; margin: 12px; color: #222; }
        h1 { font-size: 14px; margin: 0 0 8px 0; border-bottom: 2px solid #333; padding-bottom: 5px; }
        .meta { color: #555; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #d0d0d0; padding: 5px; vertical-align: top; }
        th { background: #f2f2f2; text-align: left; }
        .text-right { text-align: right; }
        .summary { margin-top: 10px; }
    </style>
</head>
<body>
    <h1>Financial Report Export</h1>
    <p class="meta">
        <strong>Report:</strong> {{ $selectedReportLabel ?? '' }}<br>
        <strong>Period:</strong> {{ $selectedPeriod ?? '' }}<br>
        <strong>Date Filter:</strong> {{ $activeRangeLabel ?? '' }}<br>
        <strong>Generated:</strong> {{ date('F j, Y g:i A') }}
    </p>

    @if(!empty($result['summary']) && is_array($result['summary']))
        <table class="summary">
            <thead><tr><th colspan="2">Summary</th></tr></thead>
            <tbody>
                @foreach($result['summary'] as $s)
                    <tr>
                        <td>{{ $s['label'] ?? '' }}</td>
                        <td class="text-right">
                            @if(!empty($s['is_currency']))
                                ₱{{ number_format((float) ($s['value'] ?? 0), 2) }}
                            @else
                                {{ number_format((float) ($s['value'] ?? 0), 0) }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if(!empty($result['charts']) && is_array($result['charts']))
        <h2 style="font-size: 11px; margin: 14px 0 6px 0; border-bottom: 1px solid #ccc; padding-bottom: 4px;">Charts</h2>
        @foreach($result['charts'] as $chartConfig)
            @include('analytics-reports.partials.chart-svg-pdf', ['chart' => is_array($chartConfig) ? $chartConfig : []])
        @endforeach
    @endif

    @if(($result['type'] ?? '') === 'single_metric')
        <table>
            <tbody>
                <tr>
                    <th>{{ $result['metric_label'] ?? 'Value' }}</th>
                    <td class="text-right">₱{{ number_format((float) ($result['metric_value'] ?? 0), 2) }}</td>
                </tr>
            </tbody>
        </table>
    @elseif(($result['type'] ?? '') === 'split_metric')
        <table>
            <thead><tr><th>Type</th><th>Count</th></tr></thead>
            <tbody>
                <tr><td>Cash</td><td class="text-right">{{ $result['cash'] ?? 0 }}</td></tr>
                <tr><td>Financing</td><td class="text-right">{{ $result['financing'] ?? 0 }}</td></tr>
            </tbody>
        </table>
    @elseif(!empty($result['columns']) && !empty($result['rows']))
        <table>
            <thead>
                <tr>
                    @foreach($result['columns'] as $column)
                        <th>{{ $column }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($result['rows'] as $row)
                    <tr>
                        @if(($result['type'] ?? '') === 'table')
                            @if(($selectedReport ?? '') === 'releases')
                                <td>{{ $row['plate'] ?? '-' }}</td>
                                <td>{{ $row['unit'] ?? '-' }}</td>
                                <td>{{ $row['date_encoded'] ?? '-' }}</td>
                                <td class="text-right">₱{{ number_format((float) ($row['purchase_price'] ?? 0), 2) }}</td>
                                <td class="text-right">₱{{ number_format((float) ($row['sold_price'] ?? 0), 2) }}</td>
                                <td>{{ $row['sold_date'] ?? '-' }}</td>
                                <td class="text-right">{{ $row['age_days'] ?? 0 }}</td>
                            @else
                                <td>{{ $row['plate'] ?? '-' }}</td>
                                <td>{{ $row['unit'] ?? '-' }}</td>
                                <td>{{ $row['date_encoded'] ?? '-' }}</td>
                                <td class="text-right">₱{{ number_format((float) ($row['purchase_price'] ?? 0), 2) }}</td>
                                <td class="text-right">₱{{ number_format((float) ($row['reservation_amount'] ?? 0), 2) }}</td>
                                <td>{{ $row['reservation_date'] ?? '-' }}</td>
                                <td class="text-right">{{ $row['age_days'] ?? 0 }}</td>
                                <td>{{ $row['status'] ?? '-' }}</td>
                            @endif
                        @elseif(($result['type'] ?? '') === 'value_table')
                            <td>{{ $row['plate'] ?? '-' }}</td>
                            <td>{{ $row['unit'] ?? '-' }}</td>
                            <td class="text-right">₱{{ number_format((float) ($row['value'] ?? 0), 2) }}</td>
                        @elseif(($result['type'] ?? '') === 'age_table')
                            <td>{{ $row['plate'] ?? '-' }}</td>
                            <td>{{ $row['unit'] ?? '-' }}</td>
                            <td>{{ $row['status'] ?? '-' }}</td>
                            <td>{{ $row['date_encoded'] ?? '-' }}</td>
                            <td class="text-right">{{ $row['age_days'] ?? 0 }}</td>
                            <td class="text-right">₱{{ number_format((float) ($row['purchase_price'] ?? 0), 2) }}</td>
                            <td class="text-right">₱{{ number_format((float) ($row['posted_price'] ?? 0), 2) }}</td>
                        @elseif(($result['type'] ?? '') === 'age_sold_table')
                            <td>{{ $row['plate'] ?? '-' }}</td>
                            <td>{{ $row['unit'] ?? '-' }}</td>
                            <td>{{ $row['date_encoded'] ?? '-' }}</td>
                            <td class="text-right">₱{{ number_format((float) ($row['purchase_price'] ?? 0), 2) }}</td>
                            <td class="text-right">₱{{ number_format((float) ($row['posted_price'] ?? 0), 2) }}</td>
                            <td class="text-right">₱{{ number_format((float) ($row['sold_price'] ?? 0), 2) }}</td>
                            <td>{{ $row['sold_date'] ?? '-' }}</td>
                            <td class="text-right">{{ $row['age_days'] ?? 0 }}</td>
                        @elseif(($result['type'] ?? '') === 'repairs_table')
                            <td>{{ $row['plate'] ?? '-' }}</td>
                            <td>{{ $row['unit'] ?? '-' }}</td>
                            <td class="text-right">₱{{ number_format((float) ($row['repair_cost'] ?? 0), 2) }}</td>
                            <td class="text-right">₱{{ number_format((float) ($row['post_reservation_repairs_cost'] ?? 0), 2) }}</td>
                            <td class="text-right">₱{{ number_format((float) ($row['total_repairs'] ?? 0), 2) }}</td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
