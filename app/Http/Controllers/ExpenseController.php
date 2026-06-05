<?php

namespace App\Http\Controllers;

use App\Models\ExpenseTransaction;
use App\Models\ExpenseItem;
use App\Models\ExpenseItemReceipt;
use App\Models\Vehicle;
use App\Models\VehicleExpenseCategory;
use App\Models\PaymentMethod;
use App\Models\SoaManualEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Traits\LogsActivity;
use Illuminate\Http\UploadedFile;

class ExpenseController extends Controller
{
    use LogsActivity;

    /**
     * Files from items[i][receipts][] — $request->hasFile('items.{i}.receipts') is unreliable for array uploads.
     *
     * @return array<int, UploadedFile>
     */
    protected function receiptFilesForItem(Request $request, int|string $index): array
    {
        $itemsFiles = $request->file('items');
        if (!is_array($itemsFiles)) {
            return [];
        }
        $row = $itemsFiles[$index] ?? $itemsFiles[(string) $index] ?? null;
        if (!is_array($row) || empty($row['receipts'])) {
            return [];
        }
        $receipts = $row['receipts'];
        if (!is_array($receipts)) {
            $receipts = [$receipts];
        }

        return array_values(array_filter($receipts, function ($f) {
            return $f instanceof UploadedFile && $f->isValid();
        }));
    }

    /**
     * Keyword search for expenses-inventory (plate, vehicle type, or requested by).
     */
    protected function applyExpenseInventorySearch($query, Request $request): void
    {
        $search = $request->get('search');
        if (! is_string($search) || trim($search) === '') {
            return;
        }

        $like = '%' . trim($search) . '%';
        $mode = $request->get('search_by', 'all');

        switch ($mode) {
            case 'plate':
                $query->whereHas('vehicle', function ($q) use ($like) {
                    $q->where('plate_number', 'like', $like);
                });
                break;
            case 'vehicle_type':
                $query->whereHas('vehicle', function ($q) use ($like) {
                    $q->where(function ($q2) use ($like) {
                        $q2->where('body_type', 'like', $like)
                            ->orWhere('variant', 'like', $like)
                            ->orWhere('make', 'like', $like)
                            ->orWhere('model', 'like', $like);
                    });
                });
                break;
            case 'requested_by':
                $query->where('requested_by', 'like', $like);
                break;
            case 'all':
            default:
                $query->where(function ($q) use ($like) {
                    $q->where('requested_by', 'like', $like)
                        ->orWhereHas('vehicle', function ($vq) use ($like) {
                            $vq->where('plate_number', 'like', $like)
                                ->orWhere('body_type', 'like', $like)
                                ->orWhere('variant', 'like', $like)
                                ->orWhere('make', 'like', $like)
                                ->orWhere('model', 'like', $like);
                        });
                });
                break;
        }
    }

    /**
     * Same filters as expenses-inventory list (expenses or external-expenses section).
     */
    protected function expenseInventoryItemsQuery(Request $request, string $section): ?Builder
    {
        if ($section === 'expenses') {
            $query = ExpenseItem::with('expenseTransaction', 'vehicle', 'paymentMethod')
                ->orderBy('expense_date', 'desc')
                ->orderBy('created_at', 'desc');

            if ($request->filled('date_from')) {
                $query->where('expense_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->where('expense_date', '<=', $request->date_to);
            }
            if ($request->filled('expense_type')) {
                $query->where('payment_tag', $request->expense_type);
            }
            if ($request->filled('payment_method_id')) {
                $query->where('payment_method_id', $request->payment_method_id);
            }

            $this->applyExpenseInventorySearch($query, $request);

            return $query;
        }

        if ($section === 'external-expenses') {
            $query = ExpenseItem::with('expenseTransaction', 'vehicle', 'paymentMethod')
                ->whereNotNull('store_shop')
                ->where('store_shop', '!=', '')
                ->orderBy('expense_date', 'desc')
                ->orderBy('created_at', 'desc');

            if ($request->filled('date_from')) {
                $query->where('expense_date', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->where('expense_date', '<=', $request->date_to);
            }

            $this->applyExpenseInventorySearch($query, $request);

            return $query;
        }

        return null;
    }

    /**
     * SOA "Expense budget" lines for a single calendar day: each tier row uses the sister payment method
     * (e.g. COST CENTER (WAREHOUSE BUDGET)) for display and for which expense items are summed.
     * Used on expenses-inventory when Date From = Date To.
     *
     * @return array{applicable: bool, reason?: string, date?: string, date_formatted?: string, rows?: array<int, array<string, mixed>>, has_any_budget?: bool}
     */
    protected function buildExpenseBudgetSummary(Request $request): array
    {
        if (! $request->filled('date_from') || ! $request->filled('date_to')) {
            return ['applicable' => false, 'reason' => 'incomplete_dates'];
        }

        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        if ($dateFrom !== $dateTo) {
            return ['applicable' => false, 'reason' => 'multi_day'];
        }

        $day = $dateFrom;

        $budgetEntries = SoaManualEntry::query()
            ->where('is_expense_budget', true)
            ->whereDate('entry_date', $day)
            ->with('paymentMethod')
            ->orderBy('payment_method_id')
            ->orderByRaw("FIELD(COALESCE(expense_budget_tier, ''), 'flagship', 'annex', 'warehouse')")
            ->get();

        if ($request->filled('payment_method_id')) {
            $filterId = (int) $request->payment_method_id;
            $budgetEntries = $budgetEntries->filter(function (SoaManualEntry $entry) use ($filterId) {
                $pm = $entry->paymentMethod;
                if (! $pm) {
                    return (int) $entry->payment_method_id === $filterId;
                }
                $tier = $entry->expense_budget_tier ?: SoaManualEntry::EXPENSE_BUDGET_TIER_FLAGSHIP;
                $resolved = $this->paymentMethodIdForExpenseBudgetTier($pm, $tier);

                return $resolved === $filterId || (int) $entry->payment_method_id === $filterId;
            })->values();
        }

        $tierLabel = static function (?string $tier): string {
            return match ($tier) {
                SoaManualEntry::EXPENSE_BUDGET_TIER_FLAGSHIP => 'Flagship',
                SoaManualEntry::EXPENSE_BUDGET_TIER_WAREHOUSE => 'Warehouse',
                SoaManualEntry::EXPENSE_BUDGET_TIER_ANNEX => 'Annex',
                default => 'Budget',
            };
        };

        $rows = [];
        foreach ($budgetEntries as $entry) {
            $pm = $entry->paymentMethod;
            $tier = $entry->expense_budget_tier ?: SoaManualEntry::EXPENSE_BUDGET_TIER_FLAGSHIP;

            $line = (float) ($entry->debit ?? 0);
            if ($line <= 0) {
                $line = (float) ($entry->credit ?? 0);
            }

            $displayName = $pm
                ? $this->paymentMethodDisplayNameForBudgetTier($pm->name, $tier)
                : 'N/A';
            $expensePmId = $pm
                ? $this->paymentMethodIdForExpenseBudgetTier($pm, $tier)
                : (int) $entry->payment_method_id;

            $totalExpenses = (float) ExpenseItem::query()
                ->where('payment_method_id', $expensePmId)
                ->whereDate('expense_date', $day)
                ->sum('cost');

            $rows[] = [
                'payment_method_id' => $expensePmId,
                'payment_method_name' => $displayName,
                'tier_label' => $tierLabel($entry->expense_budget_tier),
                'entry_description' => $entry->description ?: '—',
                'budget_amount' => round($line, 2),
                'total_expenses' => $totalExpenses,
                'remaining' => round($line - $totalExpenses, 2),
            ];
        }

        return [
            'applicable' => true,
            'date' => $day,
            'date_formatted' => Carbon::parse($day)->format('F j, Y'),
            'rows' => $rows,
            'has_any_budget' => count($rows) > 0,
        ];
    }

    /**
     * Align payment method label with expense-budget tier (…FLAGSHIP BUDGET) → (…ANNEX BUDGET), etc.
     */
    protected function paymentMethodDisplayNameForBudgetTier(string $currentName, string $tier): string
    {
        $suffix = match ($tier) {
            SoaManualEntry::EXPENSE_BUDGET_TIER_ANNEX => '(ANNEX BUDGET)',
            SoaManualEntry::EXPENSE_BUDGET_TIER_WAREHOUSE => '(WAREHOUSE BUDGET)',
            default => '(FLAGSHIP BUDGET)',
        };

        if (preg_match('/\([^)]*BUDGET\)/i', $currentName)) {
            $replaced = preg_replace('/\([^)]*BUDGET\)/i', $suffix, $currentName, 1);

            return $replaced !== null && $replaced !== '' ? $replaced : trim($currentName).' '.$suffix;
        }

        return trim($currentName).' '.$suffix;
    }

    /**
     * Payment method used on expense lines for this tier (sister row in payment_methods), else SOA-linked id.
     */
    protected function paymentMethodIdForExpenseBudgetTier(PaymentMethod $soaLinkedPaymentMethod, string $tier): int
    {
        $targetName = $this->paymentMethodDisplayNameForBudgetTier($soaLinkedPaymentMethod->name, $tier);

        $found = PaymentMethod::query()
            ->where('is_active', true)
            ->where(function ($q) use ($targetName) {
                $q->where('name', $targetName)
                    ->orWhereRaw('LOWER(TRIM(name)) = LOWER(?)', [trim($targetName)]);
            })
            ->first();

        return $found ? (int) $found->id : (int) $soaLinkedPaymentMethod->id;
    }

    /**
     * Export expenses-inventory (current filters) as CSV (opens in Excel) or PDF.
     */
    public function exportInventory(Request $request)
    {
        $section = $request->get('section', 'expenses');
        $format = strtolower((string) $request->get('format', 'csv'));
        if (! in_array($format, ['csv', 'pdf'], true)) {
            abort(422, 'Invalid export format.');
        }

        $query = $this->expenseInventoryItemsQuery($request, $section);
        if ($query === null) {
            abort(404, 'Export is only available for Expense Transactions or External Expenses.');
        }

        $items = $query->get();
        $slug = preg_replace('/[^a-z0-9\-]+/i', '-', $section);
        $baseName = 'expenses-inventory-' . $slug . '-' . date('Y-m-d');

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('expenses.exports.inventory-pdf', [
                'items' => $items,
                'section' => $section,
            ]);
            $pdf->setPaper('a4', 'landscape');

            return $pdf->download($baseName . '.pdf');
        }

        return $this->streamExpenseInventoryCsv($items, $section, $baseName);
    }

    /**
     * UTF-8 CSV with BOM for Excel; filename uses .csv (xlsx label in UI maps here).
     */
    protected function streamExpenseInventoryCsv($items, string $section, string $baseName)
    {
        $filename = $baseName . '.csv';

        return response()->streamDownload(function () use ($items, $section) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");

            if ($section === 'expenses') {
                fputcsv($out, [
                    'Transaction Date',
                    'Expense Date',
                    'Description',
                    'Expense Type',
                    'Payment Method',
                    'Requested By',
                    'Approved By',
                    'Paid By',
                    'Store / Shop',
                    'Cost',
                    'Vehicle Plate',
                ]);
                foreach ($items as $item) {
                    fputcsv($out, [
                        optional($item->expenseTransaction)->transaction_date
                            ? $item->expenseTransaction->transaction_date->format('Y-m-d') : '',
                        $item->expense_date ? $item->expense_date->format('Y-m-d') : '',
                        $item->description,
                        $item->payment_tag,
                        $item->paymentMethod ? $item->paymentMethod->name : '',
                        $item->requested_by,
                        $item->approved_by,
                        $item->care_of,
                        $item->store_shop,
                        number_format((float) $item->cost, 2, '.', ''),
                        $item->vehicle ? $item->vehicle->plate_number : '',
                    ]);
                }
            } else {
                fputcsv($out, ['Expense', 'Amount', 'Repaired By', 'Unit (Vehicle)', 'Date']);
                foreach ($items as $item) {
                    $desc = $item->description;
                    if ($item->description_details) {
                        $desc .= ' — ' . $item->description_details;
                    }
                    $unit = '';
                    if ($item->vehicle) {
                        $unit = trim($item->vehicle->full_name . ' ' . $item->vehicle->plate_number);
                    }
                    fputcsv($out, [
                        $desc,
                        number_format((float) $item->cost, 2, '.', ''),
                        $item->store_shop,
                        $unit,
                        $item->expense_date ? $item->expense_date->format('Y-m-d') : '',
                    ]);
                }
            }

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Display a listing of expense transactions.
     */
    public function index(Request $request)
    {
        $section = $request->get('section', 'expenses'); // expenses, tools-purchase, tools-current, external-expenses

        if ($section === 'expenses' && ! $request->filled('date_from') && ! $request->filled('date_to')) {
            $today = now()->toDateString();
            $request->merge([
                'date_from' => $today,
                'date_to' => $today,
            ]);
        }

        // For expenses section, query expense items directly with filters
        if ($section == 'expenses') {
            $query = $this->expenseInventoryItemsQuery($request, 'expenses');
            $expenseItems = $query->paginate(15)->withQueryString();
            $paymentMethods = PaymentMethod::where('is_active', true)->orderBy('name')->get();
            $externalExpenseItems = null;
            $expenseBudgetSummary = $this->buildExpenseBudgetSummary($request);
        } elseif ($section == 'external-expenses') {
            $query = $this->expenseInventoryItemsQuery($request, 'external-expenses');
            $externalExpenseItems = $query->paginate(15)->withQueryString();
            $expenseItems = null;
            $paymentMethods = null;
            $expenseBudgetSummary = null;
        } else {
            $expenseItems = null;
            $paymentMethods = null;
            $externalExpenseItems = null;
            $expenseBudgetSummary = null;
        }
        
        // Keep old transactions query for backward compatibility (if needed)
        $transactions = ExpenseTransaction::with('expenseItems.vehicle', 'expenseItems.paymentMethod')
            ->orderBy('transaction_date', 'desc')
            ->get();
        
        // Get tools data for tools sections
        $tools = \App\Models\Tool::orderBy('date_acquired', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Group tools by date_acquired for purchase inventory
        $groupedTools = $tools->groupBy(function($tool) {
            return $tool->date_acquired->format('Y-m-d');
        });
        
        // Calculate totals for each date
        $dateTotals = [];
        foreach ($groupedTools as $date => $dateTools) {
            $dateTotals[$date] = $dateTools->sum('amount');
        }
        
        // Calculate current inventory (aggregate by tool name)
        $currentInventory = $tools->groupBy('name')->map(function($group) {
            return [
                'name' => $group->first()->name,
                'total_quantity' => $group->sum('quantity'),
                'total_amount' => $group->sum('amount'),
                'entries' => $group->count(),
                'first_acquired' => $group->min('date_acquired'),
                'last_acquired' => $group->max('date_acquired'),
            ];
        })->sortBy('name')->values();
        
        return view('expenses.index', compact('transactions', 'expenseItems', 'paymentMethods', 'externalExpenseItems', 'groupedTools', 'dateTotals', 'currentInventory', 'section', 'expenseBudgetSummary'));
    }

    /**
     * Show the form for creating a new expense transaction.
     */
    public function create()
    {
        return view('expenses.create');
    }

    /**
     * Store a newly created expense transaction.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_date' => 'required|date',
            'starting_cash' => 'required|numeric|min:0',
            'added_cash' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.expense_date' => 'required|date',
            'items.*.payment_method_id' => 'required|exists:payment_methods,id',
            'items.*.description' => 'required|string|max:255',
            'items.*.description_details' => 'nullable|string',
            'items.*.care_of' => 'nullable|string|max:255',
            'items.*.requested_by' => 'nullable|string|max:255',
            'items.*.approved_by' => 'nullable|string|max:255',
            'items.*.store_shop' => 'nullable|string|max:255',
            'items.*.receipt_checked' => 'nullable|boolean',
            'items.*.receipt_checker' => 'nullable|string|max:255',
            'items.*.receipt_check_date' => 'nullable|date',
            'items.*.cost' => 'required|numeric|min:0',
            'items.*.payment_tag' => 'required|in:Operating,Vehicle',
            'items.*.vehicle_id' => 'nullable|exists:vehicles,id',
            'items.*.receipts.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // When description contains "Miscellaneous", description_details (notes) is required
        foreach ($request->items as $index => $item) {
            $description = $item['description'] ?? '';
            $details = trim($item['description_details'] ?? '');
            if (stripos($description, 'Miscellaneous') !== false && $details === '') {
                return response()->json([
                    'success' => false,
                    'errors' => [
                        "items.{$index}.description_details" => ['Notes for misc and others is required when Miscellaneous is selected in Description.']
                    ]
                ], 422);
            }
        }

        // Calculate totals
        $totalCash = $request->starting_cash + $request->added_cash;
        $totalExpense = collect($request->items)->sum('cost');
        $cashRemaining = $totalCash - $totalExpense;

        // Create transaction
        $transaction = ExpenseTransaction::create([
            'transaction_date' => $request->transaction_date,
            'starting_cash' => $request->starting_cash,
            'added_cash' => $request->added_cash,
            'total_cash' => $totalCash,
            'total_expense' => $totalExpense,
            'cash_remaining' => $cashRemaining,
        ]);

        // Log activity
        $this->logCreate($transaction, null, "Expense Transactions");

            // Create expense items with receipts
        foreach ($request->items as $index => $item) {
            $expenseItem = ExpenseItem::create([
                'expense_transaction_id' => $transaction->id,
                'expense_date' => $item['expense_date'],
                'payment_method_id' => $item['payment_method_id'],
                'description' => $item['description'],
                'description_details' => $item['description_details'] ?? null,
                'care_of' => $item['care_of'] ?? null,
                'requested_by' => $item['requested_by'] ?? null,
                'approved_by' => $item['approved_by'] ?? null,
                'store_shop' => $item['store_shop'] ?? null,
                'receipt_checked' => isset($item['receipt_checked']) && $item['receipt_checked'] == '1',
                'receipt_checker' => $item['receipt_checker'] ?? null,
                'receipt_check_date' => $item['receipt_check_date'] ?? null,
                'cost' => $item['cost'],
                'payment_tag' => $item['payment_tag'],
                'expense_category' => $item['expense_category'] ?? null,
                'vehicle_id' => $item['payment_tag'] === 'Vehicle' && isset($item['vehicle_id']) ? $item['vehicle_id'] : null,
            ]);

            // Log activity for expense item creation
            $this->logCreate($expenseItem, "Created expense item: {$expenseItem->description} (₱" . number_format($expenseItem->cost, 2) . ")", "Expense Items");

            // Handle receipt uploads for this item (nested items[n][receipts][] — see receiptFilesForItem)
            $receipts = $this->receiptFilesForItem($request, $index);
            $receipts = array_slice($receipts, 0, 10);

            foreach ($receipts as $receiptIndex => $receipt) {
                $originalName = $receipt->getClientOriginalName();
                $extension = $receipt->getClientOriginalExtension();
                $fileName = Str::uuid() . '.' . $extension;
                $path = 'expenses/receipts/' . $fileName;

                $receipt->storeAs('public/expenses/receipts', $fileName);

                ExpenseItemReceipt::create([
                    'expense_item_id' => $expenseItem->id,
                    'image_path' => $path,
                    'original_name' => $originalName,
                    'mime_type' => $receipt->getMimeType(),
                    'file_size' => $receipt->getSize(),
                    'sort_order' => $receiptIndex,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Expense transaction created successfully!',
            'transaction' => $transaction->load('expenseItems.vehicle', 'expenseItems.receipts')
        ]);
    }

    /**
     * Search vehicles for the autocomplete.
     */
    public function searchVehicles(Request $request)
    {
        $query = $request->get('q', '');
        
        $vehicles = Vehicle::where(function($q) use ($query) {
            $q->where('plate_number', 'LIKE', "%{$query}%")
              ->orWhere('make', 'LIKE', "%{$query}%")
              ->orWhere('model', 'LIKE', "%{$query}%");
        })
        ->limit(10)
        ->get(['id', 'plate_number', 'make', 'model', 'year']);

        // Add full_name to each vehicle
        $vehicles->map(function($vehicle) {
            $vehicle->full_name = $vehicle->year . ' ' . $vehicle->make . ' ' . $vehicle->model;
            return $vehicle;
        });

        return response()->json($vehicles);
    }

    /**
     * Display the specified expense transaction.
     */
    public function show(ExpenseTransaction $expenseTransaction, Request $request)
    {
        $expenseTransaction->load('expenseItems.vehicle', 'expenseItems.receipts', 'expenseItems.paymentMethod');
        
        $vehicle = null;
        $filteredTotalExpense = $expenseTransaction->total_expense;
        
        // If an item ID is provided, filter to show only that specific item
        $itemId = $request->get('item');
        if ($itemId) {
            $selectedItem = $expenseTransaction->expenseItems->firstWhere('id', $itemId);
            if ($selectedItem) {
                // Show only the selected item
                $expenseTransaction->expenseItems = collect([$selectedItem]);
                
                // Get vehicle info if the item has a vehicle
                if ($selectedItem->vehicle_id) {
                    $vehicle = \App\Models\Vehicle::with(['make', 'vehicleModel'])->find($selectedItem->vehicle_id);
                }
                
                // Recalculate total expense based on the single item
                $filteredTotalExpense = $selectedItem->cost;
            }
        } else {
            // If a vehicle ID is provided, filter to show only items for that vehicle
            $vehicleId = $request->get('vehicle_id');
            if ($vehicleId) {
                $vehicle = \App\Models\Vehicle::with(['make', 'vehicleModel'])->find($vehicleId);
                $expenseTransaction->expenseItems = $expenseTransaction->expenseItems->filter(function($item) use ($vehicleId) {
                    return $item->vehicle_id == $vehicleId && $item->payment_tag == 'Vehicle';
                })->values(); // Reset keys after filtering
                
                // Recalculate total expense based on filtered items
                $filteredTotalExpense = $expenseTransaction->expenseItems->sum('cost');
            }
        }
        
        // If request wants JSON, return JSON
        if (request()->wantsJson() || request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'transaction' => [
                    'id' => $expenseTransaction->id,
                    'transaction_date' => $expenseTransaction->transaction_date->format('Y-m-d'),
                    'starting_cash' => $expenseTransaction->starting_cash,
                    'added_cash' => $expenseTransaction->added_cash,
                    'total_cash' => $expenseTransaction->total_cash,
                    'total_expense' => $expenseTransaction->total_expense,
                    'cash_remaining' => $expenseTransaction->cash_remaining,
                ]
            ]);
        }
        
        return view('expenses.show', compact('expenseTransaction', 'vehicle', 'filteredTotalExpense'));
    }

    /**
     * Update an expense transaction.
     */
    public function update(Request $request, ExpenseTransaction $expenseTransaction)
    {
        $validator = Validator::make($request->all(), [
            'transaction_date' => 'required|date',
            'starting_cash' => 'required|numeric|min:0',
            'added_cash' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Get original values for logging
        $original = $expenseTransaction->getOriginal();

        // Calculate totals
        $totalCash = $request->starting_cash + $request->added_cash;
        $totalExpense = $expenseTransaction->expenseItems()->sum('cost');
        $cashRemaining = $totalCash - $totalExpense;

        // Update transaction
        $expenseTransaction->update([
            'transaction_date' => $request->transaction_date,
            'starting_cash' => $request->starting_cash,
            'added_cash' => $request->added_cash,
            'total_cash' => $totalCash,
            'total_expense' => $totalExpense,
            'cash_remaining' => $cashRemaining,
        ]);

        // Track changes for logging
        $changes = [];
        $updatedData = [
            'transaction_date' => $request->transaction_date,
            'starting_cash' => $request->starting_cash,
            'added_cash' => $request->added_cash,
            'total_cash' => $totalCash,
            'total_expense' => $totalExpense,
            'cash_remaining' => $cashRemaining,
        ];
        foreach ($updatedData as $key => $value) {
            if (isset($original[$key]) && $original[$key] != $value) {
                $changes[$key] = [
                    'old' => $original[$key],
                    'new' => $value
                ];
            }
        }

        // Log activity
        $this->logUpdate($expenseTransaction, !empty($changes) ? $changes : null, null, "Expense Transactions");

        return response()->json([
            'success' => true,
            'message' => 'Expense transaction updated successfully!',
            'transaction' => $expenseTransaction->load('expenseItems.vehicle', 'expenseItems.receipts')
        ]);
    }

    /**
     * Delete an expense transaction.
     */
    public function destroy(ExpenseTransaction $expenseTransaction)
    {
        // Log activity before deleting
        $this->logDelete($expenseTransaction);

        // Delete all associated expense items (this will cascade delete receipts via foreign key)
        $expenseTransaction->expenseItems()->each(function ($item) {
            // Delete receipt images
            foreach ($item->receipts as $receipt) {
                if (Storage::exists('public/' . $receipt->image_path)) {
                    Storage::delete('public/' . $receipt->image_path);
                }
            }
            $item->delete();
        });

        // Delete the transaction
        $expenseTransaction->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense transaction deleted successfully!'
        ]);
    }

    /**
     * Add expense item to existing transaction.
     */
    public function addExpenseItem(Request $request, ExpenseTransaction $expenseTransaction)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:255',
            'description_details' => 'nullable|string',
            'care_of' => 'nullable|string|max:255',
            'cost' => 'required|numeric|min:0',
            'payment_tag' => 'required|in:Operating,Vehicle',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'expense_date' => 'required|date',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'requested_by' => 'nullable|string|max:255',
            'approved_by' => 'nullable|string|max:255',
            'store_shop' => 'nullable|string|max:255',
            'receipt_checked' => 'nullable|boolean',
            'receipt_checker' => 'nullable|string|max:255',
            'receipt_check_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Create new expense item
        $expenseItem = ExpenseItem::create([
            'expense_transaction_id' => $expenseTransaction->id,
            'description' => $request->description,
            'description_details' => $request->description_details ?? null,
            'care_of' => $request->care_of ?? null,
            'cost' => $request->cost,
            'payment_tag' => $request->payment_tag,
            'expense_category' => $request->expense_category ?? null,
            'vehicle_id' => $request->payment_tag === 'Vehicle' && $request->vehicle_id ? $request->vehicle_id : null,
            'expense_date' => $request->expense_date,
            'payment_method_id' => $request->payment_method_id,
            'requested_by' => $request->requested_by ?? null,
            'approved_by' => $request->approved_by ?? null,
            'store_shop' => $request->store_shop ?? null,
            'receipt_checked' => isset($request->receipt_checked) && $request->receipt_checked == '1',
            'receipt_checker' => $request->receipt_checker ?? null,
            'receipt_check_date' => $request->receipt_check_date ?? null,
        ]);

        // Recalculate totals
        $totalExpense = $expenseTransaction->expenseItems()->sum('cost');
        $cashRemaining = $expenseTransaction->total_cash - $totalExpense;

        // Update transaction
        $expenseTransaction->update([
            'total_expense' => $totalExpense,
            'cash_remaining' => $cashRemaining,
        ]);

        $expenseTransaction->load('expenseItems.vehicle');

        return response()->json([
            'success' => true,
            'message' => 'Expense item added successfully!',
            'transaction' => $expenseTransaction,
            'item' => $expenseItem->load('vehicle')
        ]);
    }

    /**
     * Update an expense item.
     */
    public function updateExpenseItem(Request $request, ExpenseTransaction $expenseTransaction, ExpenseItem $expenseItem)
    {
        // Verify the expense item belongs to this transaction
        if ($expenseItem->expense_transaction_id !== $expenseTransaction->id) {
            return response()->json([
                'success' => false,
                'message' => 'Expense item does not belong to this transaction.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:255',
            'description_details' => 'nullable|string',
            'care_of' => 'nullable|string|max:255',
            'cost' => 'required|numeric|min:0',
            'payment_tag' => 'required|in:Operating,Vehicle',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'expense_date' => 'required|date',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'requested_by' => 'nullable|string|max:255',
            'approved_by' => 'nullable|string|max:255',
            'store_shop' => 'nullable|string|max:255',
            'receipt_checked' => 'nullable|boolean',
            'receipt_checker' => 'nullable|string|max:255',
            'receipt_check_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Update expense item
        $expenseItem->update([
            'description' => $request->description,
            'description_details' => $request->description_details ?? null,
            'care_of' => $request->care_of ?? null,
            'cost' => $request->cost,
            'payment_tag' => $request->payment_tag,
            'expense_category' => $request->expense_category ?? null,
            'vehicle_id' => $request->payment_tag === 'Vehicle' && $request->vehicle_id ? $request->vehicle_id : null,
            'expense_date' => $request->expense_date,
            'payment_method_id' => $request->payment_method_id,
            'requested_by' => $request->requested_by ?? null,
            'approved_by' => $request->approved_by ?? null,
            'store_shop' => $request->store_shop ?? null,
            'receipt_checked' => isset($request->receipt_checked) && $request->receipt_checked == '1',
            'receipt_checker' => $request->receipt_checker ?? null,
            'receipt_check_date' => $request->receipt_check_date ?? null,
        ]);

        // Recalculate totals
        $totalExpense = $expenseTransaction->expenseItems()->sum('cost');
        $cashRemaining = $expenseTransaction->total_cash - $totalExpense;

        // Get original values for logging
        $original = $expenseItem->getOriginal();
        
        // Track changes for logging
        $changes = [];
        $updatedData = [
            'description' => $request->description,
            'cost' => $request->cost,
            'payment_tag' => $request->payment_tag,
            'expense_date' => $request->expense_date,
            'payment_method_id' => $request->payment_method_id,
        ];
        foreach ($updatedData as $key => $value) {
            if (isset($original[$key]) && $original[$key] != $value) {
                $changes[$key] = [
                    'old' => $original[$key],
                    'new' => $value
                ];
            }
        }

        // Update transaction
        $expenseTransaction->update([
            'total_expense' => $totalExpense,
            'cash_remaining' => $cashRemaining,
        ]);

        // Log activity for expense item update
        $this->logUpdate($expenseItem, !empty($changes) ? $changes : null, "Updated expense item: {$expenseItem->description}", "Expense Items");

        $expenseTransaction->load('expenseItems.vehicle');

        return response()->json([
            'success' => true,
            'message' => 'Expense item updated successfully!',
            'transaction' => $expenseTransaction,
            'item' => $expenseItem->load('vehicle')
        ]);
    }

    /**
     * Delete an expense item.
     */
    public function deleteExpenseItem(ExpenseTransaction $expenseTransaction, ExpenseItem $expenseItem)
    {
        // Verify the expense item belongs to this transaction
        if ($expenseItem->expense_transaction_id !== $expenseTransaction->id) {
            return response()->json([
                'success' => false,
                'message' => 'Expense item does not belong to this transaction.'
            ], 403);
        }

        // Log activity before deleting
        $this->logDelete($expenseItem, "Deleted expense item: {$expenseItem->description}", "Expense Items");

        // Delete the expense item
        $expenseItem->delete();

        // Recalculate totals
        $totalExpense = $expenseTransaction->expenseItems()->sum('cost');
        $cashRemaining = $expenseTransaction->total_cash - $totalExpense;

        // Update transaction
        $expenseTransaction->update([
            'total_expense' => $totalExpense,
            'cash_remaining' => $cashRemaining,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Expense item deleted successfully!',
            'transaction' => $expenseTransaction->load('expenseItems.vehicle')
        ]);
    }

    /**
     * Upload receipt images for an expense item.
     */
    public function uploadReceipts(Request $request, ExpenseTransaction $expenseTransaction, ExpenseItem $expenseItem)
    {
        // Verify the expense item belongs to this transaction
        if ($expenseItem->expense_transaction_id !== $expenseTransaction->id) {
            return response()->json([
                'success' => false,
                'message' => 'Expense item does not belong to this transaction.'
            ], 403);
        }

        $request->validate([
            'receipts.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max per image
        ]);

        // Check current receipt count
        $currentCount = $expenseItem->receipts()->count();
        $maxReceipts = 10;

        if ($currentCount >= $maxReceipts) {
            return response()->json([
                'success' => false,
                'message' => "Maximum of {$maxReceipts} receipt images allowed per expense item."
            ], 422);
        }

        $uploadedReceipts = [];
        $files = $request->file('receipts');
        $remainingSlots = $maxReceipts - $currentCount;

        foreach (array_slice($files, 0, $remainingSlots) as $receipt) {
            $originalName = $receipt->getClientOriginalName();
            $extension = $receipt->getClientOriginalExtension();
            $fileName = Str::uuid() . '.' . $extension;
            $path = 'expenses/receipts/' . $fileName;

            // Store the image
            $receipt->storeAs('public/expenses/receipts', $fileName);

            // Save receipt record
            $expenseReceipt = ExpenseItemReceipt::create([
                'expense_item_id' => $expenseItem->id,
                'image_path' => $path,
                'original_name' => $originalName,
                'mime_type' => $receipt->getMimeType(),
                'file_size' => $receipt->getSize(),
                'sort_order' => $currentCount,
            ]);

            // Log activity for receipt upload
            $this->logCreate($expenseReceipt, "Uploaded receipt: {$originalName} for expense item: {$expenseItem->description}", "Receipts");

            $uploadedReceipts[] = $expenseReceipt;
            $currentCount++;
        }

        return response()->json([
            'success' => true,
            'message' => count($uploadedReceipts) . ' receipt image(s) uploaded successfully!',
            'receipts' => collect($uploadedReceipts)->map(function (ExpenseItemReceipt $r) {
                return [
                    'id' => $r->id,
                    'url' => $r->url,
                    'original_name' => $r->original_name,
                ];
            })->values()->all(),
        ]);
    }

    /**
     * Delete a receipt image.
     */
    public function deleteReceipt(ExpenseTransaction $expenseTransaction, ExpenseItem $expenseItem, ExpenseItemReceipt $expenseItemReceipt)
    {
        // Verify the receipt belongs to the expense item
        if ($expenseItemReceipt->expense_item_id !== $expenseItem->id) {
            return response()->json([
                'success' => false,
                'message' => 'Receipt does not belong to this expense item.'
            ], 403);
        }

        // Verify the expense item belongs to this transaction
        if ($expenseItem->expense_transaction_id !== $expenseTransaction->id) {
            return response()->json([
                'success' => false,
                'message' => 'Expense item does not belong to this transaction.'
            ], 403);
        }

        // Log activity before deleting
        $this->logDelete($expenseItemReceipt, "Deleted receipt: {$expenseItemReceipt->original_name} for expense item: {$expenseItem->description}", "Receipts");

        // Delete the file from storage
        if (Storage::exists('public/' . $expenseItemReceipt->image_path)) {
            Storage::delete('public/' . $expenseItemReceipt->image_path);
        }

        // Delete the receipt record
        $expenseItemReceipt->delete();

        return response()->json([
            'success' => true,
            'message' => 'Receipt image deleted successfully!'
        ]);
    }

    /**
     * Get all vehicle expense categories.
     */
    public function getVehicleCategories()
    {
        try {
            // Check if table exists first
            if (!Schema::hasTable('vehicle_expense_categories')) {
                return response()->json([]);
            }
            
            $categories = VehicleExpenseCategory::orderBy('name')->get(['id', 'name']);
            return response()->json($categories);
        } catch (\Exception $e) {
            // If table doesn't exist or any other error, return empty array
            return response()->json([]);
        }
    }

    /**
     * Add a new vehicle expense category.
     */
    public function addVehicleCategory(Request $request)
    {
        try {
            // Check if table exists
            if (!Schema::hasTable('vehicle_expense_categories')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Database table does not exist. Please run: php artisan migrate',
                    'error' => 'Table vehicle_expense_categories not found'
                ], 500);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|unique:vehicle_expense_categories,name',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors();
                // Provide a more user-friendly message for duplicate entries
                if ($errors->has('name') && str_contains($errors->first('name'), 'already been taken')) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This category already exists. Please select it from the list above.',
                        'errors' => $errors
                    ], 422);
                }
                
                return response()->json([
                    'success' => false,
                    'errors' => $errors
                ], 422);
            }

            $category = VehicleExpenseCategory::create([
                'name' => $request->name,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Category added successfully!',
                'category' => $category
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Handle database connection errors
            return response()->json([
                'success' => false,
                'message' => 'Database error. Please check your database connection.',
                'error' => config('app.debug') ? $e->getMessage() : 'Database connection error'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add category: ' . $e->getMessage(),
                'error' => config('app.debug') ? $e->getMessage() : 'Unknown error'
            ], 500);
        }
    }

    /**
     * Get all active payment methods.
     */
    public function getPaymentMethods()
    {
        try {
            // Check if table exists first
            if (!Schema::hasTable('payment_methods')) {
                return response()->json([]);
            }
            
            $methods = PaymentMethod::active()->get(['id', 'name']);
            return response()->json($methods);
        } catch (\Exception $e) {
            // If table doesn't exist or any other error, return empty array
            return response()->json([]);
        }
    }
}
