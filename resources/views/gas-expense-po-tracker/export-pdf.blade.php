<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $section === 'gas' ? 'Gas Expenses' : 'Purchase Orders' }} Export</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 8px; margin: 12px; color: #222; }
        h1 { font-size: 13px; border-bottom: 2px solid #333; padding-bottom: 6px; margin-bottom: 8px; }
        .meta { color: #666; margin-bottom: 10px; font-size: 8px; }
        .filters { margin-bottom: 8px; font-size: 8px; color: #444; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ccc; padding: 4px 5px; text-align: left; vertical-align: top; word-wrap: break-word; }
        th { background: #eee; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .nowrap { white-space: nowrap; }
        tfoot td { font-weight: bold; background: #f5f5f5; }
        .yes { color: #198754; }
        .no { color: #999; }
    </style>
</head>
<body>
    @if($section === 'gas')
        <h1>Gas Expenses Report</h1>
    @else
        <h1>Purchase Orders Report</h1>
    @endif

    <p class="meta">Exported {{ date('F j, Y g:i A') }} — {{ $items->count() }} record(s)</p>

    @if(!empty($filters))
        <p class="filters"><strong>Filters:</strong> {{ implode(' · ', $filters) }}</p>
    @else
        <p class="filters"><strong>Filters:</strong> None (all records)</p>
    @endif

    @if($section === 'gas')
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Plate No.</th>
                    <th>Vehicle</th>
                    <th>Driver</th>
                    <th>Model</th>
                    <th class="text-right">Amount</th>
                    <th>Sent By</th>
                    <th>Checked By</th>
                    <th class="text-center">Grp Chat</th>
                    <th class="text-center">Before</th>
                    <th class="text-center">After</th>
                    <th class="text-center">Plate/Gas</th>
                    <th class="text-center">Receipt</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $expense)
                    <tr>
                        <td class="nowrap">{{ $expense->date->format('M j, Y') }}</td>
                        <td>{{ $expense->plate_number ?: '—' }}</td>
                        <td>{{ $expense->vehicle?->full_name ?? '—' }}</td>
                        <td>{{ $expense->driver }}</td>
                        <td>{{ $expense->model }}</td>
                        <td class="text-right nowrap">₱{{ number_format($expense->gas_amount, 2) }}</td>
                        <td>{{ $expense->expense_sent_by }}</td>
                        <td>{{ $expense->checked_by }}</td>
                        <td class="text-center {{ $expense->has_photo_video_in_groupchat ? 'yes' : 'no' }}">{{ $expense->has_photo_video_in_groupchat ? '✓' : '—' }}</td>
                        <td class="text-center {{ $expense->photo_fuel_gauge_before ? 'yes' : 'no' }}">{{ $expense->photo_fuel_gauge_before ? '✓' : '—' }}</td>
                        <td class="text-center {{ $expense->photo_fuel_gauge_after ? 'yes' : 'no' }}">{{ $expense->photo_fuel_gauge_after ? '✓' : '—' }}</td>
                        <td class="text-center {{ $expense->photo_car_license_plate_gas_boy ? 'yes' : 'no' }}">{{ $expense->photo_car_license_plate_gas_boy ? '✓' : '—' }}</td>
                        <td class="text-center {{ $expense->photo_receipt_next_to_gas_pump ? 'yes' : 'no' }}">{{ $expense->photo_receipt_next_to_gas_pump ? '✓' : '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="13">No records match the current filters.</td></tr>
                @endforelse
            </tbody>
            @if($items->isNotEmpty())
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right">Total</td>
                        <td class="text-right nowrap">₱{{ number_format($totalAmount, 2) }}</td>
                        <td colspan="7"></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    @else
        <table>
            <thead>
                <tr>
                    <th>P.O. Number</th>
                    <th>Date</th>
                    <th>Vendor</th>
                    <th>Description</th>
                    <th class="text-right">Amount</th>
                    <th>Status</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $po)
                    <tr>
                        <td>{{ $po->po_number ?: '—' }}</td>
                        <td class="nowrap">{{ $po->po_date->format('M j, Y') }}</td>
                        <td>{{ $po->vendor ?: '—' }}</td>
                        <td>{{ $po->description ?: '—' }}</td>
                        <td class="text-right nowrap">₱{{ number_format($po->amount, 2) }}</td>
                        <td>{{ $po->status }}</td>
                        <td>{{ Str::limit($po->notes, 120) ?: '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7">No records match the current filters.</td></tr>
                @endforelse
            </tbody>
            @if($items->isNotEmpty())
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-right">Total</td>
                        <td class="text-right nowrap">₱{{ number_format($totalAmount, 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            @endif
        </table>
    @endif
</body>
</html>
