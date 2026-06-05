<?php

namespace App\Http\Controllers;

use App\Models\ExpenseTransaction;
use App\Models\PaymentMethod;
use App\Models\DailyBudget;
use App\Models\CashAddition;
use App\Models\SoaManualEntry;
use App\Models\SoaFloatedFund;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Traits\LogsActivity;
use Carbon\Carbon;

class SOAController extends Controller
{
    use LogsActivity;

    /**
     * Show the form for creating a new SOA transaction.
     */
    public function create()
    {
        $paymentMethods = PaymentMethod::active()->orderBy('sort_order')->get();
        return view('soa.create', compact('paymentMethods'));
    }

    /**
     * Get transactions for a specific payment method and date.
     */
    public function getTransactions(Request $request)
    {
        $paymentMethodId = $request->input('payment_method_id');
        $selectedDate = $request->input('date');
        
        if (!$paymentMethodId) {
            return response()->json([
                'success' => false,
                'message' => 'Payment method is required'
            ], 400);
        }

        if (!$selectedDate) {
            return response()->json([
                'success' => false,
                'message' => 'Date is required'
            ], 400);
        }

        // Get starting balance from daily_budgets table for this payment method and date
        $dailyBudget = DailyBudget::where('payment_method_id', $paymentMethodId)
            ->where('budget_date', $selectedDate)
            ->first();

        $startingBalance = $dailyBudget ? $dailyBudget->starting_balance : 0;
        $addedCash = $dailyBudget ? $dailyBudget->added_cash : 0;
        $totalCash = $startingBalance + $addedCash;
        $hasStartingBalance = $dailyBudget !== null;

        // Get cash additions (credits) for this payment method and date
        $cashAdditions = CashAddition::where('payment_method_id', $paymentMethodId)
            ->whereDate('addition_date', $selectedDate)
            ->orderBy('addition_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $manualEntries = SoaManualEntry::where('payment_method_id', $paymentMethodId)
            ->whereDate('entry_date', $selectedDate)
            ->orderBy('id', 'asc')
            ->get();

        // Build transactions array
        $transactions = [];
        // Use starting balance as the initial balance (not total cash)
        $runningBalance = $startingBalance;

        // Add starting balance row if we have transactions or starting balance
        if ($cashAdditions->count() > 0 || $hasStartingBalance || $manualEntries->count() > 0) {
            $dateFormatted = Carbon::parse($selectedDate)->format('d-M-y');
            $transactions[] = [
                'type' => 'starting_balance',
                'date' => $dateFormatted,
                'description' => 'STARTING BALANCE',
                'debit' => null,
                'credit' => null,
                'balance' => $startingBalance,
            ];
        }

        // Combine and sort cash additions and manual SOA lines by time
        $allTransactions = [];
        
        // Add cash additions as credits
        foreach ($cashAdditions as $addition) {
            $runningBalance += $addition->amount; // Credit increases balance
            $allTransactions[] = [
                'type' => 'credit',
                'date' => $addition->addition_date ? $addition->addition_date->format('d-M-y') : '',
                'time' => $addition->created_at ? $addition->created_at->format('H:i:s') : '00:00:00',
                'description' => $addition->description ?: 'ADD CASH',
                'debit' => null,
                'credit' => $addition->amount,
                'balance' => $runningBalance,
                'id' => $addition->id,
            ];
        }
        
        // Total SOA debits (manual lines only; expense transactions are tracked separately, not on SOA)
        $totalExpenses = 0;

        foreach ($manualEntries as $entry) {
            $debitAmt = $entry->debit !== null ? (float) $entry->debit : 0;
            $creditAmt = $entry->credit !== null ? (float) $entry->credit : 0;
            $runningBalance = $this->applyManualEntryToBalance($runningBalance, $entry);
            if ($debitAmt > 0 && ! $this->isCarryOverEntry($entry)) {
                $totalExpenses += $debitAmt;
            }
            $allTransactions[] = [
                'type' => 'soa_manual',
                'date' => $entry->entry_date ? $entry->entry_date->format('d-M-y') : '',
                'time' => $entry->created_at ? $entry->created_at->format('H:i:s') : '00:00:00',
                'description' => $entry->description,
                'debit' => $debitAmt > 0 ? $debitAmt : null,
                'credit' => $creditAmt > 0 ? $creditAmt : null,
                'balance' => $runningBalance,
                'id' => $entry->id,
                'is_expense_budget' => (bool) $entry->is_expense_budget,
                'expense_budget_tier' => $entry->expense_budget_tier,
                'is_carry_over' => $this->isCarryOverEntry($entry),
            ];
        }

        // Sort all transactions by date and time
        usort($allTransactions, function($a, $b) {
            $dateCompare = strcmp($a['date'], $b['date']);
            if ($dateCompare !== 0) {
                return $dateCompare;
            }
            return strcmp($a['time'], $b['time']);
        });
        
        // Add sorted transactions
        foreach ($allTransactions as $transaction) {
            unset($transaction['time']); // Remove time from final output
            // Keep id for credit rows (edit/delete); manual rows carry id for edit/delete
            $transactions[] = $transaction;
        }

        $paymentMethod = PaymentMethod::find($paymentMethodId);

        $priorSoaDate = $this->findMostRecentPriorSoaDate($paymentMethodId, $selectedDate);
        $hasPriorSoaActivity = $priorSoaDate !== null;
        $priorSoaClosingBalance = $hasPriorSoaActivity
            ? round($this->computeSoaClosingBalanceForDate($paymentMethodId, $priorSoaDate), 2)
            : 0.0;

        $yesterday = Carbon::parse($selectedDate)->subDay()->format('Y-m-d');

        return response()->json([
            'success' => true,
            'payment_method' => $paymentMethod ? $paymentMethod->name : '',
            'selected_date' => $selectedDate,
            'starting_balance' => $startingBalance,
            'added_cash' => $addedCash,
            'total_cash' => $totalCash,
            'has_starting_balance' => $hasStartingBalance,
            'total_expenses' => $totalExpenses,
            'ending_balance' => $runningBalance,
            'transactions' => $transactions,
            'prior_soa_date' => $priorSoaDate,
            'prior_soa_date_formatted' => $priorSoaDate ? Carbon::parse($priorSoaDate)->format('F j, Y') : null,
            'prior_soa_closing_balance' => $priorSoaClosingBalance,
            'has_prior_soa_activity' => $hasPriorSoaActivity,
            'yesterday_date' => $yesterday,
            'yesterday_date_formatted' => Carbon::parse($yesterday)->format('F j, Y'),
            'yesterday_closing_balance' => $priorSoaClosingBalance,
            'has_yesterday_soa_activity' => $hasPriorSoaActivity,
            'has_soa_activity' => $this->hasSoaActivityForDate($paymentMethodId, $selectedDate),
        ]);
    }

    /**
     * Whether the SOA has any saved data for that calendar day (budget, cash additions, manual lines).
     */
    private function hasSoaActivityForDate($paymentMethodId, string $dateYmd): bool
    {
        if (DailyBudget::where('payment_method_id', $paymentMethodId)->where('budget_date', $dateYmd)->exists()) {
            return true;
        }
        if (CashAddition::where('payment_method_id', $paymentMethodId)->whereDate('addition_date', $dateYmd)->exists()) {
            return true;
        }
        if (SoaManualEntry::where('payment_method_id', $paymentMethodId)->whereDate('entry_date', $dateYmd)->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Most recent calendar date before $beforeDateYmd that has any SOA activity for this payment method.
     */
    private function findMostRecentPriorSoaDate(int $paymentMethodId, string $beforeDateYmd): ?string
    {
        $dates = collect();

        DailyBudget::where('payment_method_id', $paymentMethodId)
            ->whereDate('budget_date', '<', $beforeDateYmd)
            ->pluck('budget_date')
            ->each(function ($date) use ($dates) {
                $dates->push(Carbon::parse($date)->format('Y-m-d'));
            });

        CashAddition::where('payment_method_id', $paymentMethodId)
            ->whereDate('addition_date', '<', $beforeDateYmd)
            ->selectRaw('DATE(addition_date) as activity_date')
            ->distinct()
            ->pluck('activity_date')
            ->each(function ($date) use ($dates) {
                $dates->push(Carbon::parse($date)->format('Y-m-d'));
            });

        SoaManualEntry::where('payment_method_id', $paymentMethodId)
            ->whereDate('entry_date', '<', $beforeDateYmd)
            ->selectRaw('DATE(entry_date) as activity_date')
            ->distinct()
            ->pluck('activity_date')
            ->each(function ($date) use ($dates) {
                $dates->push(Carbon::parse($date)->format('Y-m-d'));
            });

        if ($dates->isEmpty()) {
            return null;
        }

        return $dates->max();
    }

    /**
     * Whether the given expense budget tier is already used for this payment method and date (excluding $exceptId).
     */
    private function isExpenseBudgetTierTaken(int $paymentMethodId, string $entryDateYmd, string $tier, ?int $exceptId = null): bool
    {
        $query = SoaManualEntry::where('payment_method_id', $paymentMethodId)
            ->whereDate('entry_date', $entryDateYmd)
            ->where('is_expense_budget', true)
            ->where('expense_budget_tier', $tier);

        if ($exceptId !== null) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->exists();
    }

    /**
     * Closing balance for a date: daily starting balance plus credits minus debits in date/time order (matches SOA table logic).
     */
    private function computeSoaClosingBalanceForDate($paymentMethodId, string $dateYmd): float
    {
        $dailyBudget = DailyBudget::where('payment_method_id', $paymentMethodId)
            ->where('budget_date', $dateYmd)
            ->first();

        $startingBalance = $dailyBudget ? (float) $dailyBudget->starting_balance : 0.0;

        $allRows = [];

        $cashAdditionsForDay = CashAddition::where('payment_method_id', $paymentMethodId)
            ->whereDate('addition_date', $dateYmd)
            ->orderBy('addition_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        foreach ($cashAdditionsForDay as $addition) {
            $allRows[] = [
                'debit' => 0.0,
                'credit' => (float) $addition->amount,
                'date' => $addition->addition_date ? $addition->addition_date->format('d-M-y') : '',
                'time' => $addition->created_at ? $addition->created_at->format('H:i:s') : '00:00:00',
            ];
        }

        $manualForDay = SoaManualEntry::where('payment_method_id', $paymentMethodId)
            ->whereDate('entry_date', $dateYmd)
            ->orderBy('id', 'asc')
            ->get();

        foreach ($manualForDay as $entry) {
            $debitAmt = $entry->debit !== null ? (float) $entry->debit : 0.0;
            $creditAmt = $entry->credit !== null ? (float) $entry->credit : 0.0;
            $allRows[] = [
                'debit' => $debitAmt,
                'credit' => $creditAmt,
                'is_carry_over' => $this->isCarryOverEntry($entry),
                'date' => $entry->entry_date ? $entry->entry_date->format('d-M-y') : '',
                'time' => $entry->created_at ? $entry->created_at->format('H:i:s') : '00:00:00',
            ];
        }

        usort($allRows, function ($a, $b) {
            $dateCompare = strcmp($a['date'], $b['date']);
            if ($dateCompare !== 0) {
                return $dateCompare;
            }

            return strcmp($a['time'], $b['time']);
        });

        $balance = $startingBalance;
        foreach ($allRows as $row) {
            $balance += $row['credit'];
            if (! empty($row['is_carry_over']) && $row['debit'] > 0) {
                $balance += $row['debit'];
            } else {
                $balance -= $row['debit'];
            }
        }

        return $balance;
    }

    /**
     * Carry-over debits are shown in the debit column but increase the running balance.
     */
    private function isCarryOverEntry(SoaManualEntry $entry): bool
    {
        if ($entry->is_carry_over) {
            return true;
        }

        return str_starts_with(strtolower($entry->description ?? ''), 'carry-over from last soa');
    }

    private function applyManualEntryToBalance(float $balance, SoaManualEntry $entry): float
    {
        $debitAmt = $entry->debit !== null ? (float) $entry->debit : 0.0;
        $creditAmt = $entry->credit !== null ? (float) $entry->credit : 0.0;

        $balance += $creditAmt;
        if ($this->isCarryOverEntry($entry) && $debitAmt > 0) {
            $balance += $debitAmt;
        } else {
            $balance -= $debitAmt;
        }

        return $balance;
    }

    /**
     * Store or update daily budget.
     */
    public function storeDailyBudget(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method_id' => 'required|exists:payment_methods,id',
            'budget_date' => 'required|date',
            'starting_balance' => 'required|numeric|min:0',
            'added_cash' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $existingBudget = DailyBudget::where('payment_method_id', $request->payment_method_id)
            ->where('budget_date', $request->budget_date)
            ->first();

        $dailyBudget = DailyBudget::updateOrCreate(
            [
                'payment_method_id' => $request->payment_method_id,
                'budget_date' => $request->budget_date,
            ],
            [
                'starting_balance' => $request->starting_balance,
                'added_cash' => $request->added_cash ?? 0,
                'notes' => $request->notes,
            ]
        );

        // Log activity
        if ($existingBudget) {
            $this->logUpdate($dailyBudget, null, "Updated daily budget for payment method ID: {$request->payment_method_id} on {$request->budget_date}", "Daily Budget");
        } else {
            $this->logCreate($dailyBudget, "Created daily budget for payment method ID: {$request->payment_method_id} on {$request->budget_date}", "Daily Budget");
        }

        $this->syncSoaFloatedFund(
            $request->payment_method_id,
            $request->budget_date,
            (float) $request->starting_balance
        );

        return response()->json([
            'success' => true,
            'message' => 'Daily budget saved successfully.',
            'daily_budget' => $dailyBudget
        ]);
    }

    /**
     * Add cash (credit) to a payment method for a specific date.
     */
    public function addCash(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method_id' => 'required|exists:payment_methods,id',
            'addition_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $cashAddition = CashAddition::create([
            'payment_method_id' => $request->payment_method_id,
            'addition_date' => $request->addition_date,
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        // Log activity
        $this->logCreate($cashAddition, "Added cash: ₱" . number_format($request->amount, 2) . " to payment method ID: {$request->payment_method_id} on {$request->addition_date}", "Cash Management");

        return response()->json([
            'success' => true,
            'message' => 'Cash added successfully.',
            'cash_addition' => $cashAddition
        ]);
    }

    /**
     * Update a cash addition (credit).
     */
    public function updateCash(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:1000',
            'addition_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $cashAddition = CashAddition::findOrFail($id);
        
        // Get original values for logging
        $original = $cashAddition->getOriginal();
        
        $cashAddition->update([
            'amount' => $request->amount,
            'description' => $request->description,
            'addition_date' => $request->addition_date,
        ]);

        // Track changes for logging
        $changes = [];
        $updatedData = [
            'amount' => $request->amount,
            'description' => $request->description,
            'addition_date' => $request->addition_date,
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
        $this->logUpdate($cashAddition, !empty($changes) ? $changes : null, "Updated cash addition: ₱" . number_format($request->amount, 2), "Cash Management");

        return response()->json([
            'success' => true,
            'message' => 'Cash addition updated successfully.',
            'cash_addition' => $cashAddition
        ]);
    }

    /**
     * Delete a cash addition (credit).
     */
    public function deleteCash($id)
    {
        $cashAddition = CashAddition::findOrFail($id);
        
        // Log activity before deleting
        $this->logDelete($cashAddition, "Deleted cash addition: ₱" . number_format($cashAddition->amount, 2) . " from payment method ID: {$cashAddition->payment_method_id}", "Cash Management");
        
        $cashAddition->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cash addition deleted successfully.'
        ]);
    }

    /**
     * Get all cash additions for a payment method and date.
     */
    public function getAllCashAdditions(Request $request)
    {
        $paymentMethodId = $request->input('payment_method_id');
        $selectedDate = $request->input('date');
        
        if (!$paymentMethodId) {
            return response()->json([
                'success' => false,
                'message' => 'Payment method is required'
            ], 400);
        }

        if (!$selectedDate) {
            return response()->json([
                'success' => false,
                'message' => 'Date is required'
            ], 400);
        }

        $cashAdditions = CashAddition::where('payment_method_id', $paymentMethodId)
            ->whereDate('addition_date', $selectedDate)
            ->orderBy('addition_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'cash_additions' => $cashAdditions->map(function($addition) {
                return [
                    'id' => $addition->id,
                    'amount' => $addition->amount,
                    'description' => $addition->description,
                    'addition_date' => $addition->addition_date->format('Y-m-d'),
                    'created_at' => $addition->created_at->format('Y-m-d H:i:s'),
                ];
            })
        ]);
    }

    /**
     * Update starting cash for a daily budget.
     */
    public function updateStartingCash(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method_id' => 'required|exists:payment_methods,id',
            'budget_date' => 'required|date',
            'starting_balance' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $existingBudget = DailyBudget::where('payment_method_id', $request->payment_method_id)
            ->where('budget_date', $request->budget_date)
            ->first();

        $dailyBudget = DailyBudget::updateOrCreate(
            [
                'payment_method_id' => $request->payment_method_id,
                'budget_date' => $request->budget_date,
            ],
            [
                'starting_balance' => $request->starting_balance,
            ]
        );

        // Log activity
        if ($existingBudget) {
            $this->logUpdate($dailyBudget, ['starting_balance' => ['old' => $existingBudget->starting_balance, 'new' => $request->starting_balance]], "Updated starting cash: ₱" . number_format($request->starting_balance, 2) . " for payment method ID: {$request->payment_method_id} on {$request->budget_date}", "Cash Management");
        } else {
            $this->logCreate($dailyBudget, "Created starting cash: ₱" . number_format($request->starting_balance, 2) . " for payment method ID: {$request->payment_method_id} on {$request->budget_date}", "Cash Management");
        }

        $this->syncSoaFloatedFund(
            $request->payment_method_id,
            $request->budget_date,
            (float) $request->starting_balance
        );

        return response()->json([
            'success' => true,
            'message' => 'Starting cash updated successfully.',
            'daily_budget' => $dailyBudget
        ]);
    }

    /**
     * Floated funds total and line items (declared starting below prior day's closing).
     */
    public function getFloatedFunds(Request $request)
    {
        $paymentMethodId = $request->input('payment_method_id');
        if (!$paymentMethodId) {
            return response()->json([
                'success' => false,
                'message' => 'Payment method is required',
            ], 400);
        }

        $rows = SoaFloatedFund::where('payment_method_id', $paymentMethodId)
            ->orderBy('budget_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        $total = round((float) $rows->sum('difference_amount'), 2);

        return response()->json([
            'success' => true,
            'total' => $total,
            'entries' => $rows->map(function (SoaFloatedFund $row) {
                return [
                    'id' => $row->id,
                    'budget_date' => $row->budget_date->format('Y-m-d'),
                    'budget_date_formatted' => $row->budget_date->format('F j, Y'),
                    'reference_date' => $row->reference_date->format('Y-m-d'),
                    'reference_date_formatted' => $row->reference_date->format('F j, Y'),
                    'yesterday_closing_balance' => (float) $row->yesterday_closing_balance,
                    'declared_starting_balance' => (float) $row->declared_starting_balance,
                    'difference_amount' => (float) $row->difference_amount,
                    'created_at' => $row->created_at ? $row->created_at->format('Y-m-d H:i') : null,
                ];
            }),
        ]);
    }

    /**
     * When declared starting for budget_date is below the prior calendar day's SOA closing, record the gap as floated funds.
     */
    private function syncSoaFloatedFund($paymentMethodId, string $budgetDateYmd, float $declaredStarting): void
    {
        $referenceDate = $this->findMostRecentPriorSoaDate($paymentMethodId, $budgetDateYmd);

        if (!$referenceDate || !$this->hasSoaActivityForDate($paymentMethodId, $referenceDate)) {
            SoaFloatedFund::where('payment_method_id', $paymentMethodId)
                ->where('budget_date', $budgetDateYmd)
                ->delete();

            return;
        }

        $priorClosing = round($this->computeSoaClosingBalanceForDate($paymentMethodId, $referenceDate), 2);
        $declared = round($declaredStarting, 2);

        if ($declared + 0.0001 < $priorClosing) {
            $difference = round($priorClosing - $declared, 2);
            if ($difference > 0) {
                SoaFloatedFund::updateOrCreate(
                    [
                        'payment_method_id' => $paymentMethodId,
                        'budget_date' => $budgetDateYmd,
                    ],
                    [
                        'reference_date' => $referenceDate,
                        'yesterday_closing_balance' => $priorClosing,
                        'declared_starting_balance' => $declared,
                        'difference_amount' => $difference,
                    ]
                );
            }
        } else {
            SoaFloatedFund::where('payment_method_id', $paymentMethodId)
                ->where('budget_date', $budgetDateYmd)
                ->delete();
        }
    }

    /**
     * Store a newly created SOA transaction.
     */
    public function store(Request $request)
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

        // Calculate totals
        $totalCash = $request->starting_cash + $request->added_cash;

        // Create transaction (without expense items)
        $transaction = ExpenseTransaction::create([
            'transaction_date' => $request->transaction_date,
            'starting_cash' => $request->starting_cash,
            'added_cash' => $request->added_cash,
            'total_cash' => $totalCash,
            'total_expense' => 0,
            'cash_remaining' => $totalCash,
        ]);

        // Log activity
        $this->logCreate($transaction);

        return response()->json([
            'success' => true,
            'message' => 'SOA transaction created successfully.',
            'transaction' => $transaction
        ]);
    }

    /**
     * Store a manual SOA line (description + debit or credit) for a date.
     */
    public function storeSoaManualEntry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method_id' => 'required|exists:payment_methods,id',
            'entry_date' => 'required|date',
            'description' => 'required|string|max:1000',
            'type' => 'required|in:debit,credit',
            'amount' => 'required|numeric|min:0.01',
            'expense_budget' => 'sometimes|boolean',
            'expense_budget_tier' => 'nullable|string|in:flagship,warehouse,annex',
            'is_carry_over' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $markExpenseBudget = $request->boolean('expense_budget');
        $tier = $request->input('expense_budget_tier');

        if ($markExpenseBudget) {
            if (! in_array($tier, SoaManualEntry::EXPENSE_BUDGET_TIERS, true)) {
                return response()->json([
                    'success' => false,
                    'errors' => ['expense_budget_tier' => ['Choose flagship, warehouse, or annex budget.']],
                ], 422);
            }
            if ($this->isExpenseBudgetTierTaken((int) $request->payment_method_id, $request->entry_date, $tier, null)) {
                return response()->json([
                    'success' => false,
                    'message' => 'That expense budget type is already saved for this date.',
                ], 422);
            }
        } else {
            $tier = null;
        }

        $debit = $request->type === 'debit' ? $request->amount : null;
        $credit = $request->type === 'credit' ? $request->amount : null;
        $isCarryOver = $request->boolean('is_carry_over') && $request->type === 'debit';

        $entry = SoaManualEntry::create([
            'payment_method_id' => $request->payment_method_id,
            'entry_date' => $request->entry_date,
            'description' => $request->description,
            'debit' => $debit,
            'credit' => $credit,
            'is_carry_over' => $isCarryOver,
            'is_expense_budget' => $markExpenseBudget,
            'expense_budget_tier' => $markExpenseBudget ? $tier : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'SOA detail saved.',
            'entry' => $entry,
        ]);
    }

    /**
     * Update a manual SOA line.
     */
    public function updateSoaManualEntry(Request $request, SoaManualEntry $soaManualEntry)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:1000',
            'type' => 'required|in:debit,credit',
            'amount' => 'required|numeric|min:0.01',
            'expense_budget' => 'sometimes|boolean',
            'expense_budget_tier' => 'nullable|string|in:flagship,warehouse,annex',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $markExpenseBudget = $request->has('expense_budget')
            ? $request->boolean('expense_budget')
            : $soaManualEntry->is_expense_budget;

        $tier = $request->has('expense_budget_tier')
            ? $request->input('expense_budget_tier')
            : $soaManualEntry->expense_budget_tier;

        if ($markExpenseBudget) {
            if (! in_array($tier, SoaManualEntry::EXPENSE_BUDGET_TIERS, true)) {
                return response()->json([
                    'success' => false,
                    'errors' => ['expense_budget_tier' => ['Choose flagship, warehouse, or annex budget.']],
                ], 422);
            }
            if ($this->isExpenseBudgetTierTaken(
                (int) $soaManualEntry->payment_method_id,
                $soaManualEntry->entry_date->format('Y-m-d'),
                $tier,
                (int) $soaManualEntry->id
            )) {
                return response()->json([
                    'success' => false,
                    'message' => 'That expense budget type is already used for this date.',
                ], 422);
            }
        } else {
            $tier = null;
        }

        $debit = $request->type === 'debit' ? $request->amount : null;
        $credit = $request->type === 'credit' ? $request->amount : null;

        $soaManualEntry->update([
            'description' => $request->description,
            'debit' => $debit,
            'credit' => $credit,
            'is_expense_budget' => $markExpenseBudget,
            'expense_budget_tier' => $markExpenseBudget ? $tier : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'SOA detail updated.',
            'entry' => $soaManualEntry->fresh(),
        ]);
    }

    /**
     * Remove a manual SOA line.
     */
    public function destroySoaManualEntry(SoaManualEntry $soaManualEntry)
    {
        $soaManualEntry->delete();

        return response()->json([
            'success' => true,
            'message' => 'SOA detail deleted.',
        ]);
    }

    /**
     * Delete all SOA data for a payment method on a specific date.
     */
    public function destroySoaForDate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method_id' => 'required|exists:payment_methods,id',
            'date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $paymentMethodId = (int) $request->input('payment_method_id');
        $dateYmd = Carbon::parse($request->input('date'))->format('Y-m-d');

        if (!$this->hasSoaActivityForDate($paymentMethodId, $dateYmd)) {
            return response()->json([
                'success' => false,
                'message' => 'No SOA record found for this date.',
            ], 404);
        }

        $dailyBudget = DailyBudget::where('payment_method_id', $paymentMethodId)
            ->where('budget_date', $dateYmd)
            ->first();

        $manualCount = SoaManualEntry::where('payment_method_id', $paymentMethodId)
            ->whereDate('entry_date', $dateYmd)
            ->count();

        $cashCount = CashAddition::where('payment_method_id', $paymentMethodId)
            ->whereDate('addition_date', $dateYmd)
            ->count();

        DB::transaction(function () use ($paymentMethodId, $dateYmd) {
            SoaManualEntry::where('payment_method_id', $paymentMethodId)
                ->whereDate('entry_date', $dateYmd)
                ->delete();

            CashAddition::where('payment_method_id', $paymentMethodId)
                ->whereDate('addition_date', $dateYmd)
                ->delete();

            DailyBudget::where('payment_method_id', $paymentMethodId)
                ->where('budget_date', $dateYmd)
                ->delete();

            SoaFloatedFund::where('payment_method_id', $paymentMethodId)
                ->where('budget_date', $dateYmd)
                ->delete();
        });

        $nextDay = Carbon::parse($dateYmd)->addDay()->format('Y-m-d');
        $nextBudget = DailyBudget::where('payment_method_id', $paymentMethodId)
            ->where('budget_date', $nextDay)
            ->first();

        if ($nextBudget) {
            $this->syncSoaFloatedFund($paymentMethodId, $nextDay, (float) $nextBudget->starting_balance);
        }

        $dateFormatted = Carbon::parse($dateYmd)->format('F j, Y');
        $description = "Deleted entire SOA for {$dateFormatted} (starting balance, {$cashCount} cash addition(s), {$manualCount} detail line(s))";

        if ($dailyBudget) {
            $this->logDelete($dailyBudget, $description, 'SOA');
        } else {
            $this->logActivity('delete', new DailyBudget(), $description, null, 'SOA');
        }

        return response()->json([
            'success' => true,
            'message' => "SOA for {$dateFormatted} deleted successfully.",
            'date' => $dateYmd,
        ]);
    }
}

