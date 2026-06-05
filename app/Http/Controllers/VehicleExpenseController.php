<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleExpense;
use App\Models\ExpenseItem;
use App\Models\ExpenseTransaction;
use App\Models\PaymentMethod;
use App\Models\VehicleExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VehicleExpenseController extends Controller
{
    /**
     * Show the form for creating/editing vehicle expenses
     */
    public function create(Vehicle $vehicle)
    {
        $expense = $vehicle->expense;
        
        // Get all expense items for this vehicle grouped by transaction date
        $expenseItems = ExpenseItem::where('vehicle_id', $vehicle->id)
            ->where('payment_tag', 'Vehicle')
            ->with(['expenseTransaction', 'receipts'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Group expense items by transaction date
        $groupedExpenseItems = $expenseItems->groupBy(function($item) {
            return $item->expenseTransaction 
                ? $item->expenseTransaction->transaction_date->format('Y-m-d')
                : 'no-date';
        });
        
        // Get all unique categories from expense items
        $categories = [];
        foreach ($expenseItems as $item) {
            if ($item->description) {
                // Split by comma and get unique categories
                $itemCategories = array_map('trim', explode(',', $item->description));
                $categories = array_unique(array_merge($categories, $itemCategories));
            }
        }
        sort($categories);
        
        // Get all active payment methods
        $paymentMethods = PaymentMethod::active()->orderBy('sort_order')->get();
        
        // Get all vehicle expense categories from database
        $vehicleExpenseCategories = VehicleExpenseCategory::orderBy('name')->get(['id', 'name']);
        
        return view('vehicles.expenses.create', compact('vehicle', 'expense', 'groupedExpenseItems', 'categories', 'paymentMethods', 'vehicleExpenseCategories'));
    }

    /**
     * Store a newly created expense record
     */
    public function store(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'paint_items' => 'nullable|string',
            'paint_costs' => 'nullable|numeric|min:0',
            'mechanical_electrical_items' => 'nullable|string',
            'mechanical_electrical_costs' => 'nullable|numeric|min:0',
            'cluster_items' => 'nullable|string',
            'cluster_costs' => 'nullable|numeric|min:0',
            'aircon_items' => 'nullable|string',
            'aircon_cost' => 'nullable|numeric|min:0',
            'interior_items' => 'nullable|string',
            'interior_costs' => 'nullable|numeric|min:0',
            'papers_items' => 'nullable|string',
            'papers_costs' => 'nullable|numeric|min:0',
            'tyres_battery_items' => 'nullable|string',
            'tyres_battery_cost' => 'nullable|numeric|min:0',
            'misc_items' => 'nullable|string',
            'misc_costs' => 'nullable|numeric|min:0',
            'total_repair_items' => 'nullable|string',
            'total_repair_cost' => 'nullable|numeric|min:0',
            'post_reservation_repairs' => 'nullable|string',
            'post_reservation_repairs_cost' => 'nullable|numeric|min:0',
            'total_capital_repair_capital_posted' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
        ]);

        $expenseData = $request->all();
        $expenseData['plate_number'] = $vehicle->plate_number;

        // Check if expense record already exists
        $expense = $vehicle->expense;
        
        if ($expense) {
            $expense->update($expenseData);
            $message = 'Vehicle expenses updated successfully!';
        } else {
            VehicleExpense::create($expenseData);
            $message = 'Vehicle expenses added successfully!';
        }

        // Clear cache
        Cache::flush();

        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', $message);
    }

    /**
     * Update the specified expense record
     */
    public function update(Request $request, Vehicle $vehicle, VehicleExpense $expense)
    {
        $request->validate([
            'paint_items' => 'nullable|string',
            'paint_costs' => 'nullable|numeric|min:0',
            'mechanical_electrical_items' => 'nullable|string',
            'mechanical_electrical_costs' => 'nullable|numeric|min:0',
            'cluster_items' => 'nullable|string',
            'cluster_costs' => 'nullable|numeric|min:0',
            'aircon_items' => 'nullable|string',
            'aircon_cost' => 'nullable|numeric|min:0',
            'interior_items' => 'nullable|string',
            'interior_costs' => 'nullable|numeric|min:0',
            'papers_items' => 'nullable|string',
            'papers_costs' => 'nullable|numeric|min:0',
            'tyres_battery_items' => 'nullable|string',
            'tyres_battery_cost' => 'nullable|numeric|min:0',
            'misc_items' => 'nullable|string',
            'misc_costs' => 'nullable|numeric|min:0',
            'total_repair_items' => 'nullable|string',
            'total_repair_cost' => 'nullable|numeric|min:0',
            'post_reservation_repairs' => 'nullable|string',
            'post_reservation_repairs_cost' => 'nullable|numeric|min:0',
            'total_capital_repair_capital_posted' => 'nullable|numeric|min:0',
            'price' => 'nullable|numeric|min:0',
        ]);

        $expense->update($request->all());

        // Clear cache
        Cache::flush();

        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', 'Vehicle expenses updated successfully!');
    }

    /**
     * Remove the specified expense record
     */
    public function destroy(Vehicle $vehicle, VehicleExpense $expense)
    {
        $expense->delete();

        // Clear cache
        Cache::flush();

        return redirect()->route('vehicles.show', $vehicle)
            ->with('success', 'Vehicle expenses deleted successfully!');
    }

    /**
     * Store a post reservation expense
     */
    public function storePostReservationExpense(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'expense_date' => 'required|date',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'description' => 'required|string|max:255',
            'description_details' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'requested_by' => 'nullable|string|max:255',
            'approved_by' => 'nullable|string|max:255',
            'store_shop' => 'nullable|string|max:255',
        ]);

        // Create a new expense transaction for this expense
        $transaction = ExpenseTransaction::create([
            'transaction_date' => $request->expense_date,
            'starting_cash' => 0,
            'added_cash' => 0,
            'total_cash' => 0,
            'total_expense' => $request->cost,
            'cash_remaining' => -$request->cost,
        ]);

        // Create the expense item with Post Reservation category
        $expenseItem = ExpenseItem::create([
            'expense_transaction_id' => $transaction->id,
            'expense_date' => $request->expense_date,
            'payment_method_id' => $request->payment_method_id,
            'description' => $request->description,
            'description_details' => $request->description_details ?? null,
            'cost' => $request->cost,
            'payment_tag' => 'Vehicle',
            'expense_category' => 'Post Reservation',
            'vehicle_id' => $vehicle->id,
            'requested_by' => $request->requested_by ?? null,
            'approved_by' => $request->approved_by ?? null,
            'store_shop' => $request->store_shop ?? null,
        ]);

        // Clear cache
        Cache::flush();

        return response()->json([
            'success' => true,
            'message' => 'Post reservation expense added successfully!',
            'expense_item' => $expenseItem
        ]);
    }

    /**
     * Store a post release expense
     */
    public function storePostReleaseExpense(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'expense_date' => 'required|date',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'description' => 'required|string|max:255',
            'description_details' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'requested_by' => 'nullable|string|max:255',
            'approved_by' => 'nullable|string|max:255',
            'store_shop' => 'nullable|string|max:255',
        ]);

        // Create a new expense transaction for this expense
        $transaction = ExpenseTransaction::create([
            'transaction_date' => $request->expense_date,
            'starting_cash' => 0,
            'added_cash' => 0,
            'total_cash' => 0,
            'total_expense' => $request->cost,
            'cash_remaining' => -$request->cost,
        ]);

        // Create the expense item with Post Release category
        $expenseItem = ExpenseItem::create([
            'expense_transaction_id' => $transaction->id,
            'expense_date' => $request->expense_date,
            'payment_method_id' => $request->payment_method_id,
            'description' => $request->description,
            'description_details' => $request->description_details ?? null,
            'cost' => $request->cost,
            'payment_tag' => 'Vehicle',
            'expense_category' => 'Post Release',
            'vehicle_id' => $vehicle->id,
            'requested_by' => $request->requested_by ?? null,
            'approved_by' => $request->approved_by ?? null,
            'store_shop' => $request->store_shop ?? null,
        ]);

        // Clear cache
        Cache::flush();

        return response()->json([
            'success' => true,
            'message' => 'Post release expense added successfully!',
            'expense_item' => $expenseItem
        ]);
    }
}