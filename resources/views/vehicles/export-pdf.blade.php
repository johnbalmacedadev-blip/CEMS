<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vehicle Details - {{ $vehicle->plate_number ?? $vehicle->id }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; margin: 15px; color: #333; }
        h1 { font-size: 14px; border-bottom: 2px solid #333; padding-bottom: 5px; margin-bottom: 10px; }
        h2 { font-size: 11px; margin-top: 12px; margin-bottom: 6px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th, td { border: 1px solid #ddd; padding: 5px; text-align: left; }
        th { background: #f5f5f5; font-weight: bold; }
        .text-right { text-align: right; }
        .text-muted { color: #666; }
        .section { margin-bottom: 15px; }
    </style>
</head>
<body>
    <h1>Vehicle Details – {{ $vehicle->full_name }} @if($vehicle->plate_number)({{ $vehicle->plate_number }})@endif</h1>
    <p class="text-muted">Exported on {{ date('F j, Y g:i A') }}</p>

    {{-- Car Details --}}
    <div class="section">
        <h2>Car Details</h2>
        <table>
            <tr><th width="25%">Year</th><td>{{ $vehicle->year ?? '—' }}</td><th width="25%">Make</th><td>{{ is_object($vehicle->make) ? $vehicle->make->name : ($vehicle->make ?? '—') }}</td></tr>
            <tr><th>Model</th><td>{{ is_object($vehicle->vehicleModel) ? $vehicle->vehicleModel->name : ($vehicle->model ?? '—') }}</td><th>Plate</th><td>{{ $vehicle->plate_number ?? '—' }}</td></tr>
            <tr><th>Variant</th><td>{{ $vehicle->variant ?? '—' }}</td><th>Body Type</th><td>{{ $vehicle->body_type ?? '—' }}</td></tr>
            <tr><th>Transmission</th><td>{{ $vehicle->transmission ?? '—' }}</td><th>Fuel Type</th><td>{{ $vehicle->fuel_type ?? '—' }}</td></tr>
            <tr><th>Kilometers</th><td>{{ $vehicle->kilometers !== null ? number_format($vehicle->kilometers) . ' km' : '—' }}</td><th>Colour</th><td>{{ $vehicle->colour ?? '—' }}</td></tr>
            <tr><th>Status</th><td>{{ $vehicle->status ?? '—' }}</td><th>Purchased From</th><td>{{ $vehicle->purchased_from ?? '—' }}</td></tr>
            <tr><th>With Tools</th><td>{{ $vehicle->with_tools ? 'Yes' : 'No' }}</td><th>With Matting</th><td>{{ $vehicle->with_matting ? 'Yes' : 'No' }}</td></tr>
            <tr><th>With Spare Tire</th><td>{{ $vehicle->with_spare_tire ? 'Yes' : 'No' }}</td><th>Spare Key</th><td>{{ $vehicle->spare_key ? 'Yes' : 'No' }}</td></tr>
            <tr><th>Purchase Price</th><td>{{ $vehicle->purchase_price !== null ? '₱' . number_format($vehicle->purchase_price, 2) : '—' }}</td><th>Purchase Date</th><td>{{ $vehicle->purchase_date ? $vehicle->purchase_date->format('M j, Y') : '—' }}</td></tr>
            <tr><th>Posted Price</th><td>{{ $vehicle->posted_price !== null ? '₱' . number_format($vehicle->posted_price, 2) : '—' }}</td><th>Sold Price</th><td>{{ $vehicle->sold_price !== null ? '₱' . number_format($vehicle->sold_price, 2) : '—' }}</td></tr>
            @if($vehicle->gasExpenses && $vehicle->gasExpenses->count() > 0)
            <tr><th>Total Gas Expenses</th><td colspan="3">₱{{ number_format($vehicle->gasExpenses->sum('gas_amount'), 2) }} ({{ $vehicle->gasExpenses->count() }} transaction(s))</td></tr>
            @endif
            @if($vehicle->notes)
            <tr><th>Notes</th><td colspan="3">{{ Str::limit($vehicle->notes, 200) }}</td></tr>
            @endif
        </table>
    </div>

    {{-- Documents --}}
    @php
        $documentTypeLabels = [
            'OR' => 'OR', 'CR' => 'CR', 'AR' => 'AR', 'IDS' => 'IDS', 'PROMISSORY' => 'PROMISSORY',
            'CHATTEL' => 'CHATTEL', 'REGISTRY_OF_DEEDS' => 'REGISTRY OF DEEDS', 'SEC_CERT' => 'SEC CERT',
            'DEED_OF_SALE' => 'DEED OF SALE', 'VOLUNTARY_SURRENDER' => 'VOLUNTARY SURRENDER',
            'SHERRIF_LETTER' => 'SHERRIF LETTER', 'DEED_OF_SALE_BANK' => 'DEED OF SALE (BANK)',
        ];
        $acqDocs = $vehicle->acquisitionDocuments ?? collect();
        $resDocs = $vehicle->reservationDocuments ?? collect();
        $relDocs = $vehicle->releaseDocuments ?? collect();
    @endphp
    @if($acqDocs->isNotEmpty() || $resDocs->isNotEmpty() || $relDocs->isNotEmpty())
    <div class="section">
        <h2>Documents</h2>
        <table>
            @if($acqDocs->isNotEmpty())
            <tr><th width="30%">Acquisition Documents</th><td>{{ $acqDocs->pluck('document_type')->map(function ($t) use ($documentTypeLabels) { return $documentTypeLabels[$t] ?? $t; })->unique()->sort()->implode(', ') }}</td></tr>
            @endif
            @if($resDocs->isNotEmpty())
            <tr><th>Reservation Documents</th><td>{{ $resDocs->pluck('document_type')->map(function ($t) use ($documentTypeLabels) { return $documentTypeLabels[$t] ?? $t; })->unique()->sort()->implode(', ') }}</td></tr>
            @endif
            @if($relDocs->isNotEmpty())
            <tr><th>Release Documents</th><td>{{ $relDocs->pluck('document_type')->map(function ($t) use ($documentTypeLabels) { return $documentTypeLabels[$t] ?? $t; })->unique()->sort()->implode(', ') }}</td></tr>
            @endif
        </table>
    </div>
    @endif

    {{-- Vehicle Expenses --}}
    @if($vehicle->expenseItems->isNotEmpty() || ($vehicle->gasExpenses && $vehicle->gasExpenses->count() > 0))
    <div class="section">
        <h2>Vehicle Expenses</h2>
        @if($vehicle->expenseItems->isNotEmpty())
        <table>
            <thead><tr><th>#</th><th>Date</th><th>Description</th><th>Category</th><th class="text-right">Cost</th></tr></thead>
            <tbody>
                @foreach($vehicle->expenseItems as $i => $item)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $item->expense_date ? $item->expense_date->format('Y-m-d') : '—' }}</td>
                    <td>{{ Str::limit($item->description ?? '', 40) }}</td>
                    <td>{{ $item->expense_category ?? '—' }}</td>
                    <td class="text-right">{{ $item->cost !== null ? '₱' . number_format($item->cost, 2) : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <p><strong>Total Vehicle Expenses:</strong> ₱{{ number_format($vehicle->expenseItems->sum('cost'), 2) }}</p>
        @endif
        @if($vehicle->gasExpenses && $vehicle->gasExpenses->count() > 0)
        <p><strong>Total Gas Expenses:</strong> ₱{{ number_format($vehicle->gasExpenses->sum('gas_amount'), 2) }}</p>
        @endif
    </div>
    @endif

    {{-- Vehicle Reservation Details --}}
    @if($vehicle->statusDetail && ($vehicle->statusDetail->sale_date || $vehicle->statusDetail->sales_person_reserved || $vehicle->statusDetail->sale_reservation_amount || $vehicle->statusDetail->sales_price || ($vehicle->statusDetail->sale_status && $vehicle->statusDetail->sale_status !== 'Available')))
    <div class="section">
        <h2>Vehicle Reservation Details</h2>
        <table>
            <tr><th width="35%">Showroom</th><td>{{ $vehicle->statusDetail->showroom ?? '—' }}</td></tr>
            <tr><th>Sale Date</th><td>{{ $vehicle->statusDetail->sale_date ? $vehicle->statusDetail->sale_date->format('M j, Y') : '—' }}</td></tr>
            <tr><th>Sales Price</th><td>{{ $vehicle->statusDetail->sales_price !== null ? '₱' . number_format($vehicle->statusDetail->sales_price, 2) : '—' }}</td></tr>
            <tr><th>Sale Reservation Amount</th><td>{{ $vehicle->statusDetail->sale_reservation_amount !== null ? '₱' . number_format($vehicle->statusDetail->sale_reservation_amount, 2) : '—' }}</td></tr>
            <tr><th>Sales Person Reserved ( S.E )</th><td>{{ $vehicle->statusDetail->sales_person_reserved ?? '—' }}</td></tr>
            <tr><th>Sales Person Released (S.E)</th><td>{{ $vehicle->statusDetail->sales_person_release ?? '—' }}</td></tr>
            <tr><th>Good Sales Review</th><td>{{ $vehicle->statusDetail->good_sales_review === true ? 'Yes' : ($vehicle->statusDetail->good_sales_review === false ? 'No' : '—') }}</td></tr>
            <tr><th>Cash/Financing</th><td>{{ $vehicle->statusDetail->cash_financing ?? '—' }}</td></tr>
            @if(($vehicle->statusDetail->cash_financing ?? '') === 'Financing')
            <tr><th>Financing Company</th><td>{{ $vehicle->statusDetail->financing_company ?? '—' }}</td></tr>
            <tr><th>Finance Revenue 1</th><td>{{ $vehicle->statusDetail->finance_revenue_1 !== null ? '₱' . number_format($vehicle->statusDetail->finance_revenue_1, 2) : '—' }}</td></tr>
            <tr><th>Finance Revenue 2</th><td>{{ $vehicle->statusDetail->finance_revenue_2 !== null ? '₱' . number_format($vehicle->statusDetail->finance_revenue_2, 2) : '—' }}</td></tr>
            @endif
            <tr><th>Sale Origin</th><td>{{ $vehicle->statusDetail->sale_origin ?? '—' }}</td></tr>
            <tr><th>Sales agent name</th><td>{{ $vehicle->statusDetail->sales_agent_name ?? '—' }}</td></tr>
            <tr><th>Agent Cost</th><td>{{ $vehicle->statusDetail->agent_cost !== null ? '₱' . number_format($vehicle->statusDetail->agent_cost, 2) : '—' }}</td></tr>
            <tr><th>Sale Status</th><td>{{ $vehicle->statusDetail->sale_status ?? '—' }}</td></tr>
            <tr><th>Release Date</th><td>{{ $vehicle->statusDetail->release_date ? $vehicle->statusDetail->release_date->format('M j, Y') : '—' }}</td></tr>
            <tr><th>Days from Acquisition to Reservation</th><td>{{ $vehicle->statusDetail->days_from_acquisition_to_reservation ?? '—' }}</td></tr>
            <tr><th>Days from Reservation to Release</th><td>{{ $vehicle->statusDetail->days_from_reservation_to_release ?? '—' }}</td></tr>
            @if($vehicle->statusDetail->customer_first_name || $vehicle->statusDetail->customer_last_name)
            <tr><th>Customer Name</th><td>{{ trim(($vehicle->statusDetail->customer_first_name ?? '') . ' ' . ($vehicle->statusDetail->customer_last_name ?? '')) ?: '—' }}</td></tr>
            @endif
            @if($vehicle->statusDetail->customer_contact_number)
            <tr><th>Customer Contact</th><td>{{ $vehicle->statusDetail->customer_contact_number }}</td></tr>
            @endif
        </table>
    </div>
    @endif

    {{-- Forfeit Details --}}
    @if($vehicle->forfeitDetails->isNotEmpty())
    <div class="section">
        <h2>Forfeit Details</h2>
        <table>
            <thead><tr><th>Previous Forfeit</th><th>Forfeit Amount</th><th>Forfeit Date</th></tr></thead>
            <tbody>
                @foreach($vehicle->forfeitDetails as $fd)
                <tr>
                    <td>{{ $fd->previous_forfeit_date ? $fd->previous_forfeit_date->format('Y-m-d') : '—' }}</td>
                    <td class="text-right">{{ $fd->forfeit_amount !== null ? '₱' . number_format($fd->forfeit_amount, 2) : '—' }}</td>
                    <td>{{ $fd->forfeit_date ? $fd->forfeit_date->format('Y-m-d') : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($vehicle->transferOrcrs->isNotEmpty())
    <div class="section">
        <h2>Transfer Details</h2>
        <table>
            <thead><tr><th>Date</th><th>Transaction Type</th><th>Release Date</th><th>LTO File/No</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($vehicle->transferOrcrs as $r)
                <tr>
                    <td>{{ $r->date ? $r->date->format('Y-m-d') : '—' }}</td>
                    <td>{{ $r->transaction_type ?? '—' }}</td>
                    <td>{{ $r->release_date ? $r->release_date->format('Y-m-d') : '—' }}</td>
                    <td>{{ $r->lto_file_no ?? '—' }}</td>
                    <td>{{ $r->status ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($vehicle->salesAgentCommissions->isNotEmpty())
    <div class="section">
        <h2>Sales Agent Commissions</h2>
        <table>
            <thead><tr><th>Agent</th><th>Client</th><th>Type</th><th class="text-right">Amount</th><th>Date Sent</th></tr></thead>
            <tbody>
                @foreach($vehicle->salesAgentCommissions as $c)
                <tr>
                    <td>{{ $c->agent_name ?? '—' }}</td>
                    <td>{{ $c->client_name ?? '—' }}</td>
                    <td>{{ $c->transaction_type ?? '—' }}</td>
                    <td class="text-right">{{ $c->amount !== null ? '₱' . number_format($c->amount, 2) : '—' }}</td>
                    <td>{{ $c->date_sent ? $c->date_sent->format('Y-m-d') : '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($vehicle->buffingRecords->isNotEmpty())
    <div class="section">
        <h2>Buffing Records</h2>
        <table>
            <thead><tr><th>Date</th><th>Staff</th><th>Status</th><th>Notes</th></tr></thead>
            <tbody>
                @foreach($vehicle->buffingRecords as $b)
                <tr>
                    <td>{{ $b->buffing_date ? $b->buffing_date->format('Y-m-d') : '—' }}</td>
                    <td>{{ $b->employee ? $b->employee->full_name : '—' }}</td>
                    <td>{{ $b->status ?? '—' }}</td>
                    <td>{{ Str::limit($b->notes ?? '', 50) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($vehicle->videoPostingRecords->isNotEmpty())
    <div class="section">
        <h2>Video / Posting Records</h2>
        <table>
            <thead><tr><th>Date</th><th>Title</th><th>Type</th><th>Platform</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($vehicle->videoPostingRecords as $v)
                <tr>
                    <td>{{ $v->record_date ? $v->record_date->format('Y-m-d') : '—' }}</td>
                    <td>{{ Str::limit($v->title ?? '', 30) }}</td>
                    <td>{{ $v->type ?? '—' }}</td>
                    <td>{{ $v->platform ?? '—' }}</td>
                    <td>{{ $v->status ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    @if($vehicle->followUpDocuments->isNotEmpty())
    <div class="section">
        <h2>Follow Up Docs Details</h2>
        <table>
            <thead><tr><th>Title/Description</th><th>Due Date</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($vehicle->followUpDocuments as $f)
                <tr>
                    <td>{{ Str::limit($f->title ?? $f->description ?? '—', 40) }}</td>
                    <td>{{ $f->due_date ? $f->due_date->format('Y-m-d') : '—' }}</td>
                    <td>{{ $f->status ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Ads/Boosting Details --}}
    @if($vehicle->ads->isNotEmpty())
    <div class="section">
        <h2>Ads/Boosting Details</h2>
        <table>
            <thead><tr><th>Posted Date</th><th>Video Link</th><th>Social Media Post Link</th><th>Ads/Boost Link</th><th>Campaign ID</th><th>Ad ID</th></tr></thead>
            <tbody>
                @foreach($vehicle->ads as $ad)
                <tr>
                    <td>{{ $ad->posted_date ? $ad->posted_date->format('Y-m-d') : '—' }}</td>
                    <td>{{ Str::limit(implode('; ', $ad->video_links_list) ?: '—', 50) }}</td>
                    <td>{{ Str::limit(collect($ad->social_media_links_list)->map(fn ($i) => ($i['channel'] ?? '') . ': ' . ($i['link'] ?? ''))->implode('; ') ?: '—', 50) }}</td>
                    <td>{{ Str::limit($ad->ads_boost_link ?? '—', 35) }}</td>
                    <td>{{ $ad->campaign_id ?? '—' }}</td>
                    <td>{{ $ad->ad_id ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Summary of Profit/Loss Details --}}
    <div class="section">
        <h2>Summary of Profit/Loss Details</h2>
        @php
            $purchasePricePl = $vehicle->purchase_price ?? 0;
            $totalVehicleExpensesPl = $vehicle->expenseItems->sum('cost');
            $grandTotalPl = $purchasePricePl + $totalVehicleExpensesPl;
            $soldPricePl = $vehicle->sold_price ?? 0;
            $postedPricePl = $vehicle->posted_price ?? 0;
            if ($vehicle->sold_price) {
                $profitPl = $soldPricePl - $grandTotalPl;
                $profitLabelPl = 'Total Profit';
                $profitFormulaPl = 'Sold Price - Expense Totals';
            } else {
                $profitPl = $postedPricePl - $grandTotalPl;
                $profitLabelPl = 'Potential Profit';
                $profitFormulaPl = 'Posted Price - Expense Totals';
            }
        @endphp
        <table>
            <tr><th width="45%">Expense Totals (Purchase + Vehicle Expenses)</th><td>₱{{ number_format($grandTotalPl, 2) }}</td></tr>
            <tr><th>Posted Price</th><td>{{ $vehicle->posted_price !== null ? '₱' . number_format($vehicle->posted_price, 2) : '—' }}</td></tr>
            <tr><th>Sold Price</th><td>{{ $vehicle->sold_price !== null ? '₱' . number_format($vehicle->sold_price, 2) : '—' }}</td></tr>
            <tr><th>{{ $profitLabelPl }} ({{ $profitFormulaPl }})</th><td>{{ $profitPl >= 0 ? '+' : '' }}₱{{ number_format($profitPl, 2) }}</td></tr>
            @if($vehicle->gasExpenses && $vehicle->gasExpenses->count() > 0)
            <tr><th>Vehicle Gas Expense Total</th><td>₱{{ number_format($vehicle->gasExpenses->sum('gas_amount'), 2) }}</td></tr>
            @endif
        </table>
    </div>
</body>
</html>
