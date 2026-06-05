<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Unit Report — Vehicles</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 8px; margin: 12px; color: #222; }
        h1 { font-size: 13px; border-bottom: 2px solid #333; padding-bottom: 6px; margin-bottom: 8px; }
        .meta { color: #666; margin-bottom: 10px; font-size: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ccc; padding: 4px 5px; text-align: left; vertical-align: top; }
        th { background: #eee; font-weight: bold; }
        .text-right { text-align: right; }
        .nowrap { white-space: nowrap; }
    </style>
</head>
<body>
    <h1>Unit Report — All Units</h1>
    <p class="meta">Exported {{ date('F j, Y g:i A') }} — {{ $vehicles->count() }} vehicle(s)</p>

    <table>
        <thead>
            <tr>
                <th>Year</th>
                <th>Make</th>
                <th>Model</th>
                <th>Variant</th>
                <th>Plate</th>
                <th>Colour</th>
                <th>Trans.</th>
                <th>Fuel</th>
                <th class="text-right">KM</th>
                <th class="text-right">Purchase</th>
                <th>Status</th>
                <th>Purchased From</th>
            </tr>
        </thead>
        <tbody>
            @forelse($vehicles as $v)
                @php
                    $makeLabel = is_object($v->make) && isset($v->make->name)
                        ? $v->make->name
                        : (is_string($v->make) ? $v->make : ($v->getAttributes()['make'] ?? ''));
                    $modelLabel = is_object($v->vehicleModel) && isset($v->vehicleModel->name)
                        ? $v->vehicleModel->name
                        : (is_string($v->model) ? $v->model : ($v->getAttributes()['model'] ?? ''));
                    $displayStatus = ($v->status === 'Forfeited' || $v->forfeitDetails->count() > 0) ? 'Forfeited' : $v->status;
                @endphp
                <tr>
                    <td class="nowrap">{{ $v->year }}</td>
                    <td>{{ $makeLabel }}</td>
                    <td>{{ $modelLabel }}</td>
                    <td>{{ $v->variant ?: '—' }}</td>
                    <td class="nowrap">{{ $v->plate_number }}</td>
                    <td>{{ $v->colour }}</td>
                    <td>{{ $v->transmission }}</td>
                    <td>{{ $v->fuel_type }}</td>
                    <td class="text-right">{{ $v->kilometers !== null ? number_format($v->kilometers) : '—' }}</td>
                    <td class="text-right nowrap">{{ $v->formatted_purchase_price }}</td>
                    <td>{{ $displayStatus }}</td>
                    <td>{{ Str::limit($v->purchased_from ?? '', 40) ?: '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="12">No vehicles match the current filters.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
