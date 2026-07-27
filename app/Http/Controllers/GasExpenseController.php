<?php

namespace App\Http\Controllers;

use App\Models\GasExpense;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GasExpenseController extends Controller
{
    /**
     * Display a listing of gas expenses for a vehicle.
     */
    public function index(Vehicle $vehicle)
    {
        try {
            \Log::info('Gas expense index request', [
                'vehicle_id' => $vehicle->id,
                'vehicle_plate' => $vehicle->plate_number
            ]);
            
            $gasExpenses = $vehicle->gasExpenses()->orderBy('date', 'desc')->get();
            return response()->json([
                'success' => true,
                'gasExpenses' => $gasExpenses
            ]);
        } catch (\Exception $e) {
            \Log::error('Gas expense index error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading gas expenses: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show the form for creating a new gas expense.
     */
    public function create(Vehicle $vehicle)
    {
        return response()->json([
            'success' => true,
            'vehicle' => $vehicle
        ]);
    }

    /**
     * Store a newly created gas expense.
     */
    public function store(Request $request, Vehicle $vehicle)
    {
        try {
            \Log::info('Gas expense store request', [
                'vehicle_id' => $vehicle->id,
                'vehicle_plate' => $vehicle->plate_number,
                'request_data' => $request->all()
            ]);
            
            $request->validate([
                'date' => 'required|date',
                'driver' => 'required|string|max:255',
                'model' => 'required|string|max:255',
                'gas_amount' => 'required|numeric|min:0',
                'expense_sent_by' => 'required|string|max:255',
            'po_number' => 'nullable|string|max:100',
            'photo_po_slip' => 'boolean',
                'has_photo_video_in_groupchat' => 'boolean',
                'photo_fuel_gauge_before' => 'boolean',
                'photo_fuel_gauge_after' => 'boolean',
                'photo_car_license_plate_gas_boy' => 'boolean',
                'photo_receipt_next_to_gas_pump' => 'boolean',
                'checked_by' => 'required|string|max:255',
            ]);

            $gasExpenseData = $request->all();
            $gasExpenseData['plate_number'] = $vehicle->plate_number;

            $gasExpense = GasExpense::create($gasExpenseData);

            // Clear cache
            Cache::flush();

            return response()->json([
                'success' => true,
                'message' => 'Gas expense added successfully!',
                'gasExpense' => $gasExpense
            ]);
        } catch (\Exception $e) {
            \Log::error('Gas expense store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving gas expense: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified gas expense.
     */
    public function show(Vehicle $vehicle, GasExpense $gasExpense)
    {
        return response()->json([
            'success' => true,
            'gasExpense' => $gasExpense
        ]);
    }

    /**
     * Show the form for editing the specified gas expense.
     */
    public function edit(Vehicle $vehicle, GasExpense $gasExpense)
    {
        return response()->json([
            'success' => true,
            'gasExpense' => $gasExpense
        ]);
    }

    /**
     * Update the specified gas expense.
     */
    public function update(Request $request, Vehicle $vehicle, GasExpense $gasExpense)
    {
        $request->validate([
            'date' => 'required|date',
            'driver' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'gas_amount' => 'required|numeric|min:0',
            'expense_sent_by' => 'required|string|max:255',
            'po_number' => 'nullable|string|max:100',
            'photo_po_slip' => 'boolean',
            'has_photo_video_in_groupchat' => 'boolean',
            'photo_fuel_gauge_before' => 'boolean',
            'photo_fuel_gauge_after' => 'boolean',
            'photo_car_license_plate_gas_boy' => 'boolean',
            'photo_receipt_next_to_gas_pump' => 'boolean',
            'checked_by' => 'required|string|max:255',
        ]);

        $gasExpense->update($request->all());

        // Clear cache
        Cache::flush();

        return response()->json([
            'success' => true,
            'message' => 'Gas expense updated successfully!',
            'gasExpense' => $gasExpense
        ]);
    }

    /**
     * Remove the specified gas expense.
     */
    public function destroy(Vehicle $vehicle, GasExpense $gasExpense)
    {
        $gasExpense->delete();

        // Clear cache
        Cache::flush();

        return response()->json([
            'success' => true,
            'message' => 'Gas expense deleted successfully!'
        ]);
    }
}
