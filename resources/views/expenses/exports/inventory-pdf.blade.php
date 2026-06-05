<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Expenses Inventory Export</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 8px; margin: 12px; color: #222; }
        h1 { font-size: 13px; border-bottom: 2px solid #333; padding-bottom: 6px; margin-bottom: 8px; }
        .meta { color: #666; margin-bottom: 10px; font-size: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ccc; padding: 4px 5px; text-align: left; vertical-align: top; word-wrap: break-word; }
        th { background: #eee; font-weight: bold; }
        .text-right { text-align: right; }
        .nowrap { white-space: nowrap; }
    </style>
</head>
<body>
    @if($section === 'expenses')
        <h1>Expense Transactions</h1>
    @else
        <h1>External Expenses</h1>
    @endif
    <p class="meta">Exported {{ date('F j, Y g:i A') }} — {{ $items->count() }} record(s)</p>

    @if($section === 'expenses')
        <table>
            <thead>
                <tr>
                    <th>Txn Date</th>
                    <th>Expense Date</th>
                    <th>Description</th>
                    <th>Type</th>
                    <th>Payment</th>
                    <th>Requested</th>
                    <th>Approved</th>
                    <th>Paid By</th>
                    <th>Store</th>
                    <th class="text-right">Cost</th>
                    <th>Plate</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td class="nowrap">{{ optional($item->expenseTransaction)->transaction_date?->format('M j, Y') ?? '—' }}</td>
                        <td class="nowrap">{{ $item->expense_date?->format('M j, Y') ?? '—' }}</td>
                        <td>{{ Str::limit($item->description, 120) }}</td>
                        <td>{{ $item->payment_tag }}</td>
                        <td>{{ $item->paymentMethod?->name ?? '—' }}</td>
                        <td>{{ $item->requested_by ?: '—' }}</td>
                        <td>{{ $item->approved_by ?: '—' }}</td>
                        <td>{{ $item->care_of ?: '—' }}</td>
                        <td>{{ $item->store_shop ?: '—' }}</td>
                        <td class="text-right nowrap">₱{{ number_format($item->cost, 2) }}</td>
                        <td>{{ $item->vehicle?->plate_number ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="11">No records match the current filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    @else
        <table>
            <thead>
                <tr>
                    <th>Expense</th>
                    <th class="text-right">Amount</th>
                    <th>Repaired By</th>
                    <th>Unit</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>{{ $item->description }}{{ $item->description_details ? ' — ' . Str::limit($item->description_details, 200) : '' }}</td>
                        <td class="text-right nowrap">₱{{ number_format($item->cost, 2) }}</td>
                        <td>{{ $item->store_shop }}</td>
                        <td>{{ $item->vehicle ? trim($item->vehicle->full_name . ' ' . $item->vehicle->plate_number) : '—' }}</td>
                        <td class="nowrap">{{ $item->expense_date?->format('M j, Y') ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">No records match the current filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    @endif
</body>
</html>
