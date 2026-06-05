<?php

namespace App\Http\Controllers;

use App\Models\BranchLocation;
use App\Models\TransferOrcr;
use App\Models\Vehicle;
use App\Traits\LogsActivity;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferOrcrController extends Controller
{
    use LogsActivity;
    public function index(Request $request)
    {
        $records = $this->filteredQuery($request)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $branches = BranchLocation::ordered()->get();

        return view('transfer-orcr.index', compact('records', 'branches'));
    }

    public function exportPdf(Request $request)
    {
        $records = $this->filteredQuery($request)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $filters = $this->filterLabels($request);
        $grandTotal = $records->sum(fn (TransferOrcr $r) => $r->feeTotal());

        $pdf = Pdf::loadView('transfer-orcr.export-pdf', compact('records', 'filters', 'grandTotal'));
        $pdf->setPaper('a4', 'landscape');

        $statusSlug = $request->filled('status')
            ? strtolower(preg_replace('/[^a-z0-9]+/i', '-', $request->status))
            : 'all';
        $fileName = 'transfer-orcr-' . $statusSlug . '-' . date('Y-m-d') . '.pdf';

        return $pdf->download($fileName);
    }

    public function summaryReport(Request $request)
    {
        $asOf = Carbon::today();
        $monthInput = $request->input('month', $asOf->format('Y-m'));
        try {
            $reportMonth = Carbon::createFromFormat('Y-m', $monthInput)->startOfMonth();
        } catch (\Exception $e) {
            $reportMonth = $asOf->copy()->startOfMonth();
        }

        $baseQuery = $this->summaryBaseQuery($request);
        $branches = BranchLocation::ordered()->get();
        $branchName = null;
        if ($request->filled('branch_location_id')) {
            $branchName = BranchLocation::find($request->branch_location_id)?->name;
        }

        $pendingStatuses = [TransferOrcr::STATUS_PENDING, TransferOrcr::STATUS_IN_PROGRESS];

        $pendingByMonth = $this->countsByYearMonth(
            (clone $baseQuery)->whereIn('status', $pendingStatuses)
        );
        $doneByMonth = $this->countsByYearMonth(
            (clone $baseQuery)->where('status', TransferOrcr::STATUS_DONE),
            fillGaps: true
        );

        $monthStart = $reportMonth->copy()->startOfMonth();
        $monthEnd = $reportMonth->copy()->endOfMonth();

        $dailyCounts = $this->dailyCountsInRange(
            $baseQuery,
            $monthStart->toDateString(),
            $monthEnd->toDateString()
        );

        $doneDailyCounts = $this->dailyCountsInRange(
            (clone $baseQuery)->where('status', TransferOrcr::STATUS_DONE),
            $monthStart->toDateString(),
            $monthEnd->toDateString()
        );

        $calendarWeeks = $this->buildCalendarWeeks($reportMonth, $dailyCounts);
        $doneWeeklyInMonth = $this->buildCalendarWeeks($reportMonth, $doneDailyCounts);

        $currentPending = (clone $baseQuery)->whereIn('status', $pendingStatuses)->count();
        $totalDone = (clone $baseQuery)->where('status', TransferOrcr::STATUS_DONE)->count();
        $newInMonth = (clone $baseQuery)
            ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->count();
        $doneInMonth = (clone $baseQuery)
            ->where('status', TransferOrcr::STATUS_DONE)
            ->whereBetween('date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->count();
        $openingPending = max(0, $currentPending - $newInMonth + $doneInMonth);

        return view('transfer-orcr.summary-report', [
            'asOf' => $asOf,
            'reportMonth' => $reportMonth,
            'monthInput' => $reportMonth->format('Y-m'),
            'branches' => $branches,
            'branchName' => $branchName,
            'pendingByMonth' => $pendingByMonth,
            'doneByMonth' => $doneByMonth,
            'calendarWeeks' => $calendarWeeks,
            'doneWeeklyInMonth' => $doneWeeklyInMonth,
            'openingPending' => $openingPending,
            'newInMonth' => $newInMonth,
            'doneInMonth' => $doneInMonth,
            'currentPending' => $currentPending,
            'totalDone' => $totalDone,
        ]);
    }

    public function create()
    {
        $vehicles = Vehicle::with(['make', 'vehicleModel'])->orderBy('created_at', 'desc')->get();
        $branches = BranchLocation::active()->ordered()->get();

        return view('transfer-orcr.create', compact('vehicles', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateTransferOrcr($request);
        $otherTransactions = $this->validateOtherTransactions($request);

        $record = TransferOrcr::create($validated);
        $this->syncOtherTransactions($record, $otherTransactions);
        $record->load('vehicle');
        $this->logCreate(
            $record,
            'Created Transfer OR/CR: ' . $this->transferOrcrLogLabel($record),
            'Transfer OR/CR'
        );

        return redirect()
            ->route('transfer-orcr.index')
            ->with('success', 'Transfer OR/CR record saved successfully.')
            ->with('swal_title', 'Saved');
    }

    public function show(TransferOrcr $transfer_orcr)
    {
        return redirect()->route('transfer-orcr.edit', $transfer_orcr);
    }

    public function edit(TransferOrcr $transfer_orcr)
    {
        $transfer_orcr->load('vehicle.make', 'vehicle.vehicleModel', 'otherTransactions');
        $vehicles = collect([$transfer_orcr->vehicle])->filter();
        $branches = BranchLocation::active()->ordered()->get();

        return view('transfer-orcr.edit', compact('transfer_orcr', 'vehicles', 'branches'));
    }

    public function update(Request $request, TransferOrcr $transfer_orcr)
    {
        $validated = $this->validateTransferOrcr($request);
        $otherTransactions = $this->validateOtherTransactions($request);
        $original = $transfer_orcr->getOriginal();
        $transfer_orcr->update($validated);
        $this->syncOtherTransactions($transfer_orcr, $otherTransactions);
        $transfer_orcr->load('vehicle');

        $changes = [];
        foreach ($validated as $key => $value) {
            if (array_key_exists($key, $original) && $original[$key] != $value) {
                $changes[$key] = ['old' => $original[$key], 'new' => $value];
            }
        }

        $this->logUpdate(
            $transfer_orcr,
            !empty($changes) ? $changes : null,
            'Updated Transfer OR/CR: ' . $this->transferOrcrLogLabel($transfer_orcr),
            'Transfer OR/CR'
        );

        return redirect()
            ->route('transfer-orcr.index')
            ->with('success', 'Transfer OR/CR record saved successfully.')
            ->with('swal_title', 'Saved');
    }

    public function destroy(TransferOrcr $transfer_orcr)
    {
        $transfer_orcr->load('vehicle');
        $label = $this->transferOrcrLogLabel($transfer_orcr);
        $this->logDelete($transfer_orcr, 'Deleted Transfer OR/CR: ' . $label, 'Transfer OR/CR');
        $transfer_orcr->delete();

        return redirect()
            ->route('transfer-orcr.index')
            ->with('success', 'Transfer OR/CR record removed.')
            ->with('swal_title', 'Deleted');
    }

    private function summaryBaseQuery(Request $request)
    {
        $query = TransferOrcr::query();

        if ($request->filled('branch_location_id')) {
            $query->where('branch_location_id', $request->branch_location_id);
        }

        return $query;
    }

    private function countsByYearMonth($query, bool $fillGaps = false): array
    {
        $rows = (clone $query)
            ->selectRaw('YEAR(date) as y, MONTH(date) as m, MIN(date) as sort_date, COUNT(*) as total')
            ->groupByRaw('YEAR(date), MONTH(date)')
            ->orderByRaw('MIN(date)')
            ->get();

        $items = $rows->map(fn ($row) => [
            'label' => Carbon::create((int) $row->y, (int) $row->m, 1)->format('Y-M'),
            'count' => (int) $row->total,
            'sort' => Carbon::create((int) $row->y, (int) $row->m, 1),
        ])->values();

        if ($fillGaps && $items->isNotEmpty()) {
            $filled = collect();
            $cursor = $items->first()['sort']->copy()->startOfMonth();
            $end = $items->last()['sort']->copy()->startOfMonth();
            $byKey = $items->keyBy(fn ($item) => $item['sort']->format('Y-m'));

            while ($cursor->lte($end)) {
                $key = $cursor->format('Y-m');
                $filled->push([
                    'label' => $cursor->format('Y-M'),
                    'count' => $byKey->has($key) ? $byKey[$key]['count'] : 0,
                    'sort' => $cursor->copy(),
                ]);
                $cursor->addMonth();
            }

            $items = $filled;
        }

        $list = $items->map(fn ($item) => [
            'label' => $item['label'],
            'count' => $item['count'],
        ])->all();

        return [
            'items' => $list,
            'grandTotal' => array_sum(array_column($list, 'count')),
        ];
    }

    private function dailyCountsInRange($query, string $from, string $to): array
    {
        return (clone $query)
            ->whereBetween('date', [$from, $to])
            ->selectRaw('date, COUNT(*) as total')
            ->groupByRaw('date')
            ->pluck('total', 'date')
            ->mapWithKeys(fn ($count, $date) => [
                Carbon::parse($date)->format('Y-m-d') => (int) $count,
            ])
            ->all();
    }

    private function buildCalendarWeeks(Carbon $month, array $dailyCounts): array
    {
        $start = $month->copy()->startOfMonth();
        $end = $month->copy()->endOfMonth();
        $cursor = $start->copy()->startOfWeek(Carbon::SUNDAY);
        $weeks = [];

        while ($cursor->lte($end) || $cursor->copy()->endOfWeek(Carbon::SATURDAY)->gte($start)) {
            $week = ['days' => [], 'total' => 0];
            $hasMonthDay = false;

            for ($i = 0; $i < 7; $i++) {
                $day = $cursor->copy()->addDays($i);
                if ($day->month === $month->month && $day->year === $month->year) {
                    $hasMonthDay = true;
                    $key = $day->format('Y-m-d');
                    $count = $dailyCounts[$key] ?? 0;
                    $week['days'][] = [
                        'date' => $day,
                        'count' => $count,
                    ];
                    $week['total'] += $count;
                } else {
                    $week['days'][] = [
                        'date' => null,
                        'count' => null,
                    ];
                }
            }

            if ($hasMonthDay) {
                $weeks[] = [
                    'days' => $week['days'],
                    'total' => $week['total'],
                    'label' => $this->weekRangeLabel($week['days']),
                ];
            }

            $cursor->addWeek();
            if ($cursor->gt($end->copy()->endOfWeek(Carbon::SATURDAY))) {
                break;
            }
        }

        return $weeks;
    }

    private function weekRangeLabel(array $days): string
    {
        $dates = collect($days)
            ->pluck('date')
            ->filter()
            ->values();

        if ($dates->isEmpty()) {
            return '';
        }

        return $dates->first()->format('M d') . ' – ' . $dates->last()->format('M d, Y');
    }

    private function filteredQuery(Request $request)
    {
        $query = TransferOrcr::with('vehicle.make', 'vehicle.vehicleModel', 'branchLocation');

        if ($request->filled('branch_location_id')) {
            $query->where('branch_location_id', $request->branch_location_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('vehicle', function ($q) use ($search) {
                $q->where('plate_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('make', fn ($mq) => $mq->where('name', 'LIKE', "%{$search}%"))
                    ->orWhereHas('vehicleModel', fn ($mq) => $mq->where('name', 'LIKE', "%{$search}%"))
                    ->orWhere('year', 'LIKE', "%{$search}%");
            });
        }

        return $query;
    }

    private function filterLabels(Request $request): array
    {
        $labels = [];

        if ($request->filled('branch_location_id')) {
            $branch = BranchLocation::find($request->branch_location_id);
            $labels[] = 'Branch: ' . ($branch?->name ?? $request->branch_location_id);
        }
        if ($request->filled('status')) {
            $labels[] = 'Status: ' . $request->status;
        }
        if ($request->filled('date_from')) {
            $labels[] = 'Date from: ' . $request->date_from;
        }
        if ($request->filled('date_to')) {
            $labels[] = 'Date to: ' . $request->date_to;
        }
        if ($request->filled('search')) {
            $labels[] = 'Search: ' . $request->search;
        }

        return $labels;
    }

    private function transferOrcrLogLabel(TransferOrcr $record): string
    {
        $plate = $record->vehicle?->plate_number ?: 'No plate';
        $date = $record->date?->format('j M Y') ?? '';

        return trim("{$plate}" . ($date ? " ({$date})" : ''));
    }

    private function validateTransferOrcr(Request $request): array
    {
        $validated = $request->validate([
            'branch_location_id' => 'nullable|exists:branch_locations,id',
            'date' => 'required|date',
            'vehicle_id' => 'required|exists:vehicles,id',
            'transaction_type' => 'nullable|string|max:50',
            'remark' => 'nullable|string|max:255',
            'release_date' => 'nullable|date',
            'lto_file_no' => 'nullable|string|max:100',
            'transfer_sop' => 'nullable|numeric|min:0',
            'transfer_or' => 'nullable|numeric|min:0',
            'others' => 'nullable|numeric|min:0',
            'others_note' => 'nullable|string|max:100',
            'notary' => 'nullable|numeric|min:0',
            'pnp_clearance' => 'nullable|numeric|min:0',
            'confirmation' => 'nullable|numeric|min:0',
            'rd' => 'nullable|string|max:100',
            'rd_sop' => 'nullable|numeric|min:0',
            'rd_or' => 'nullable|numeric|min:0',
            'renewal_reg_or' => 'nullable|numeric|min:0',
            'renewal_sop' => 'nullable|numeric|min:0',
            'smoke_na' => 'nullable|string|max:50',
            'remarks' => 'nullable|string|max:2000',
            'status' => 'nullable|in:Pending,In Progress,DONE',
            'transfer_sop_paid' => 'nullable|boolean',
            'transfer_sop_paid_date' => 'nullable|date',
            'transfer_or_paid' => 'nullable|boolean',
            'transfer_or_paid_date' => 'nullable|date',
            'pnp_clearance_paid' => 'nullable|boolean',
            'pnp_clearance_paid_date' => 'nullable|date',
            'rd_sop_paid' => 'nullable|boolean',
            'rd_sop_paid_date' => 'nullable|date',
            'rd_or_paid' => 'nullable|boolean',
            'rd_or_paid_date' => 'nullable|date',
        ]);

        $validated['transfer_sop'] = $validated['transfer_sop'] ?? 0;
        $validated['transfer_or'] = $validated['transfer_or'] ?? 0;
        $validated['pnp_clearance'] = $validated['pnp_clearance'] ?? 0;
        $validated['transaction_type'] = trim($validated['transaction_type'] ?? '');
        $validated['status'] = $validated['status'] ?? TransferOrcr::STATUS_PENDING;

        $paidPairs = [
            'transfer_sop_paid' => 'transfer_sop_paid_date',
            'transfer_or_paid' => 'transfer_or_paid_date',
            'pnp_clearance_paid' => 'pnp_clearance_paid_date',
            'rd_sop_paid' => 'rd_sop_paid_date',
            'rd_or_paid' => 'rd_or_paid_date',
        ];

        foreach ($paidPairs as $paidKey => $dateKey) {
            $validated[$paidKey] = (bool) ($validated[$paidKey] ?? false);
            if ($validated[$paidKey]) {
                $validated[$dateKey] = $validated[$dateKey] ?? now()->toDateString();
            } else {
                $validated[$dateKey] = null;
            }
        }

        return $validated;
    }

    private function validateOtherTransactions(Request $request): array
    {
        $validated = $request->validate([
            'other_transactions' => 'nullable|array',
            'other_transactions.*.description' => 'nullable|string|max:255',
            'other_transactions.*.amount' => 'nullable|numeric|min:0',
            'other_transactions.*.paid' => 'nullable|boolean',
            'other_transactions.*.paid_date' => 'nullable|date',
        ]);

        $items = [];
        foreach ($validated['other_transactions'] ?? [] as $item) {
            $description = trim($item['description'] ?? '');
            $amount = $item['amount'] ?? null;
            $hasAmount = $amount !== null && $amount !== '' && (float) $amount > 0;

            if ($description === '' && ! $hasAmount) {
                continue;
            }

            $paid = (bool) ($item['paid'] ?? false);
            $items[] = [
                'description' => $description ?: null,
                'amount' => $hasAmount ? (float) $amount : 0,
                'paid' => $paid,
                'paid_date' => $paid ? ($item['paid_date'] ?? now()->toDateString()) : null,
            ];
        }

        return $items;
    }

    private function syncOtherTransactions(TransferOrcr $record, array $items): void
    {
        $record->otherTransactions()->delete();

        foreach ($items as $index => $item) {
            $record->otherTransactions()->create([
                'description' => $item['description'],
                'amount' => $item['amount'],
                'paid' => $item['paid'],
                'paid_date' => $item['paid_date'],
                'sort_order' => $index,
            ]);
        }
    }
}
