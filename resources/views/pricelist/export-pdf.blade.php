<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pricelist</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 8px; margin: 14px; color: #222; }
        h1 { font-size: 12px; margin: 0 0 4px; padding: 0 0 6px; border-bottom: 2px solid #333; }
        .meta { color: #666; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 4px; vertical-align: top; }
        th { background: #f5f5f5; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .nowrap { white-space: nowrap; }
    </style>
</head>
<body>
    <h1>Pricelist</h1>
    <div class="meta">
        Exported on {{ date('F j, Y g:i A') }}
        @php
            $filterParts = [];
            if (($filters['status'] ?? 'all') !== 'all') $filterParts[] = 'Status: ' . ($filters['status'] ?? '');
            if (!empty($filters['year_from'])) $filterParts[] = 'Year From: ' . $filters['year_from'];
            if (!empty($filters['year_to'])) $filterParts[] = 'Year To: ' . $filters['year_to'];
            if (!empty($filters['search'])) $filterParts[] = 'Search: ' . $filters['search'];
        @endphp
        @if(!empty($filterParts))
            <span class="nowrap"> — Filters: {{ implode(', ', $filterParts) }}</span>
        @endif
        <span class="nowrap"> — Rows: {{ $vehicles->count() }}</span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 16%;">MAKE MODEL</th>
                <th style="width: 8%;">PLATE</th>
                <th style="width: 10%;">VARIANT</th>
                <th style="width: 6%;">TRANS</th>
                <th style="width: 7%;">FUEL</th>
                <th style="width: 4%;" class="text-center">YEAR</th>
                <th style="width: 7%;" class="text-right">MILEAGE</th>
                <th style="width: 7%;" class="text-right">PRICE</th>
                <th style="width: 17%;">OPTION 1</th>
                <th style="width: 17%;">OPTION 2</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vehicles as $v)
                @php
                    $t = $v->transmission ?? '';
                    $transDisplay = $t === 'Automatic' ? 'A/T' : ($t === 'Manual' ? 'M/T' : ($t ? $t : '—'));
                    $fuelDisplay = $v->fuel_type ? (stripos($v->fuel_type, 'gas') !== false ? 'Gas' : (stripos($v->fuel_type, 'diesel') !== false ? 'Diesel' : $v->fuel_type)) : '—';
                @endphp
                <tr>
                    <td><strong>{{ $v->full_name }}</strong></td>
                    <td>{{ $v->plate_number ?: '—' }}</td>
                    <td>{{ $v->variant ?: '—' }}</td>
                    <td>{{ $transDisplay }}</td>
                    <td>{{ $fuelDisplay }}</td>
                    <td class="text-center">{{ $v->year ?? '—' }}</td>
                    <td class="text-right">{{ $v->kilometers !== null && $v->kilometers !== '' ? number_format($v->kilometers) : '—' }}</td>
                    <td class="text-right">{{ $v->posted_price != null ? '₱' . number_format($v->posted_price, 2) : '—' }}</td>
                    <td>
                        @if($v->option1_cash_out != null || $v->option1_12mos != null)
                            <strong>Cash out:</strong> {{ $v->option1_cash_out != null ? number_format($v->option1_cash_out) : '—' }}<br>
                            12: {{ $v->option1_12mos != null ? number_format($v->option1_12mos) : '—' }} /
                            24: {{ $v->option1_24mos != null ? number_format($v->option1_24mos) : '—' }} /
                            36: {{ $v->option1_36mos != null ? number_format($v->option1_36mos) : '—' }} /
                            48: {{ $v->option1_48mos != null ? number_format($v->option1_48mos) : '—' }}
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @if($v->option2_cash_out != null || $v->option2_12mos != null)
                            <strong>Cash out:</strong> {{ $v->option2_cash_out != null ? number_format($v->option2_cash_out) : '—' }}<br>
                            12: {{ $v->option2_12mos != null ? number_format($v->option2_12mos) : '—' }} /
                            24: {{ $v->option2_24mos != null ? number_format($v->option2_24mos) : '—' }} /
                            36: {{ $v->option2_36mos != null ? number_format($v->option2_36mos) : '—' }} /
                            48: {{ $v->option2_48mos != null ? number_format($v->option2_48mos) : '—' }}
                        @else
                            —
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

