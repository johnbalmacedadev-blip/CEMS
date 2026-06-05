<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transfer OR/CR Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 6px; margin: 10px; color: #222; }
        h1 { font-size: 12px; border-bottom: 2px solid #333; padding-bottom: 5px; margin-bottom: 6px; }
        .meta { color: #666; margin-bottom: 8px; font-size: 7px; }
        .filters { margin-bottom: 8px; font-size: 7px; color: #444; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th, td { border: 1px solid #ccc; padding: 3px 4px; text-align: left; vertical-align: top; word-wrap: break-word; }
        th { background: #eee; font-weight: bold; }
        .text-right { text-align: right; }
        .nowrap { white-space: nowrap; }
        tfoot td { font-weight: bold; background: #f5f5f5; }
        .status-done { color: #dc3545; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Transfer OR/CR Report</h1>
    <p class="meta">Exported {{ date('F j, Y g:i A') }} — {{ $records->count() }} record(s)</p>

    @if(!empty($filters))
        <p class="filters"><strong>Filters:</strong> {{ implode(' · ', $filters) }}</p>
    @else
        <p class="filters"><strong>Filters:</strong> None (all records)</p>
    @endif

    <table>
        <thead>
            <tr>
                <th>Branch</th>
                <th>Date</th>
                <th>Year</th>
                <th>Make</th>
                <th>Series</th>
                <th>Plate</th>
                <th>Trans. Type</th>
                <th>Remark</th>
                <th>LTO File</th>
                <th class="text-right">Trans. SOP</th>
                <th class="text-right">Trans. OR</th>
                <th>Others</th>
                <th class="text-right">PNP</th>
                <th class="text-right">Confirm.</th>
                <th class="text-right">Hand Carry</th>
                <th>RD</th>
                <th class="text-right">RD SOP</th>
                <th class="text-right">RD OR</th>
                <th>Remarks</th>
                <th>Status</th>
                <th>Release</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $r)
                @php
                    $v = $r->vehicle;
                    $makeName = '';
                    $seriesName = '';
                    if ($v) {
                        $makeName = $v->make && is_object($v->make) ? $v->make->name : ($v->make ?? '');
                        $seriesName = $v->vehicleModel && is_object($v->vehicleModel) ? $v->vehicleModel->name : ($v->model ?? '');
                    }
                    $othersDisplay = ($r->others !== null && (float) $r->others > 0)
                        ? number_format((float) $r->others, 2)
                        : ($r->others_note ?: '—');
                @endphp
                <tr>
                    <td>{{ $r->branchLocation?->name ?: '—' }}</td>
                    <td class="nowrap">{{ $r->date->format('j M Y') }}</td>
                    <td>{{ $v?->year ?? '—' }}</td>
                    <td>{{ $makeName ?: '—' }}</td>
                    <td>{{ $seriesName ?: '—' }}</td>
                    <td class="nowrap">{{ $v?->plate_number ?? '—' }}</td>
                    <td>{{ $r->transaction_type ?: '—' }}</td>
                    <td>{{ $r->remark ?: '—' }}</td>
                    <td>{{ $r->lto_file_no ?: '—' }}</td>
                    <td class="text-right">{{ $r->transfer_sop > 0 ? number_format((float) $r->transfer_sop, 2) : '—' }}</td>
                    <td class="text-right">{{ $r->transfer_or ? number_format((float) $r->transfer_or, 2) : '—' }}</td>
                    <td>{{ $othersDisplay }}</td>
                    <td class="text-right">{{ $r->pnp_clearance ? number_format((float) $r->pnp_clearance, 2) : '—' }}</td>
                    <td class="text-right">{{ $r->confirmation !== null ? number_format((float) $r->confirmation, 2) : '—' }}</td>
                    <td class="text-right">{{ $r->notary !== null ? number_format((float) $r->notary, 2) : '—' }}</td>
                    <td>{{ $r->rd ?: '—' }}</td>
                    <td class="text-right">{{ $r->rd_sop !== null ? number_format((float) $r->rd_sop, 2) : '—' }}</td>
                    <td class="text-right">{{ $r->rd_or !== null ? number_format((float) $r->rd_or, 2) : '—' }}</td>
                    <td>{{ Str::limit($r->remarks, 40) ?: '—' }}</td>
                    <td class="{{ $r->status === 'DONE' ? 'status-done' : '' }}">{{ $r->status ?: '—' }}</td>
                    <td class="nowrap">{{ $r->release_date ? $r->release_date->format('j M Y') : '—' }}</td>
                    <td class="text-right nowrap">{{ number_format($r->feeTotal(), 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="22">No records match the current filters.</td>
                </tr>
            @endforelse
        </tbody>
        @if($records->isNotEmpty())
            <tfoot>
                <tr>
                    <td colspan="21" class="text-right">Grand total</td>
                    <td class="text-right nowrap">{{ number_format($grandTotal, 2) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>
</body>
</html>
