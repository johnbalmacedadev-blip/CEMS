<?php

namespace App\Http\Controllers;

use App\Models\GasExpense;
use App\Models\PurchaseOrder;
use App\Models\Vehicle;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class GasExpensePoTrackerController extends Controller
{
    /**
     * Display Gas Expenses and P.O. Tracker (combined page).
     */
    public function index(Request $request)
    {
        $gasExpenses = $this->gasExpensesQuery($request)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'gas_page')
            ->withQueryString();

        $purchaseOrders = $this->purchaseOrdersQuery($request)
            ->orderBy('po_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15, ['*'], 'po_page')
            ->withQueryString();

        return view('gas-expense-po-tracker.index', compact('gasExpenses', 'purchaseOrders'));
    }

    /**
     * Export gas expenses or P.O. records (current filters) as PDF.
     */
    public function exportPdf(Request $request)
    {
        $section = $request->get('section', 'gas');
        if (! in_array($section, ['gas', 'po'], true)) {
            abort(422, 'Invalid export section.');
        }

        if ($section === 'gas') {
            $items = $this->gasExpensesQuery($request)
                ->orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
            $totalAmount = $items->sum('gas_amount');
            $filters = $this->gasFilterLabels($request);
            $baseName = 'gas-expenses-' . date('Y-m-d');
        } else {
            $items = $this->purchaseOrdersQuery($request)
                ->orderBy('po_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
            $totalAmount = $items->sum('amount');
            $filters = $this->poFilterLabels($request);
            $baseName = 'purchase-orders-' . date('Y-m-d');
        }

        $pdf = Pdf::loadView('gas-expense-po-tracker.export-pdf', compact('items', 'section', 'filters', 'totalAmount'));
        $pdf->setPaper('a4', $section === 'gas' ? 'landscape' : 'portrait');

        return $pdf->download($baseName . '.pdf');
    }

    private function gasExpensesQuery(Request $request)
    {
        $query = GasExpense::with('vehicle');

        if ($request->filled('gas_date_from')) {
            $query->where('date', '>=', $request->gas_date_from);
        }
        if ($request->filled('gas_date_to')) {
            $query->where('date', '<=', $request->gas_date_to);
        }
        if ($request->filled('gas_plate')) {
            $query->where('plate_number', 'LIKE', '%' . $request->gas_plate . '%');
        }

        return $query;
    }

    private function purchaseOrdersQuery(Request $request)
    {
        $query = PurchaseOrder::query();

        if ($request->filled('po_status')) {
            $query->where('status', $request->po_status);
        }
        if ($request->filled('po_date_from')) {
            $query->where('po_date', '>=', $request->po_date_from);
        }
        if ($request->filled('po_date_to')) {
            $query->where('po_date', '<=', $request->po_date_to);
        }

        return $query;
    }

    private function gasFilterLabels(Request $request): array
    {
        $labels = [];
        if ($request->filled('gas_date_from')) {
            $labels[] = 'From: ' . $request->gas_date_from;
        }
        if ($request->filled('gas_date_to')) {
            $labels[] = 'To: ' . $request->gas_date_to;
        }
        if ($request->filled('gas_plate')) {
            $labels[] = 'Plate: ' . $request->gas_plate;
        }

        return $labels;
    }

    private function poFilterLabels(Request $request): array
    {
        $labels = [];
        if ($request->filled('po_status')) {
            $labels[] = 'Status: ' . $request->po_status;
        }
        if ($request->filled('po_date_from')) {
            $labels[] = 'From: ' . $request->po_date_from;
        }
        if ($request->filled('po_date_to')) {
            $labels[] = 'To: ' . $request->po_date_to;
        }

        return $labels;
    }

    /**
     * Store a new purchase order (P.O.).
     */
    public function storePo(Request $request)
    {
        $validated = $request->validate([
            'po_number' => 'nullable|string|max:100',
            'po_date' => 'required|date',
            'vendor' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:Pending,Ordered,Received,Cancelled',
            'notes' => 'nullable|string|max:2000',
        ]);

        PurchaseOrder::create($validated);

        return redirect()->route('gas-expense-po-tracker.index')->with('success', 'P.O. added successfully.');
    }

    /**
     * Update a purchase order.
     */
    public function updatePo(Request $request, PurchaseOrder $purchase_order)
    {
        $validated = $request->validate([
            'po_number' => 'nullable|string|max:100',
            'po_date' => 'required|date',
            'vendor' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:500',
            'amount' => 'required|numeric|min:0',
            'status' => 'required|in:Pending,Ordered,Received,Cancelled',
            'notes' => 'nullable|string|max:2000',
        ]);

        $purchase_order->update($validated);

        return redirect()->route('gas-expense-po-tracker.index')->with('success', 'P.O. updated successfully.');
    }

    /**
     * Delete a purchase order.
     */
    public function destroyPo(PurchaseOrder $purchase_order)
    {
        $purchase_order->delete();
        return redirect()->route('gas-expense-po-tracker.index')->with('success', 'P.O. deleted successfully.');
    }

    /**
     * Store a gas expense from the tracker (vehicle selected via search).
     */
    public function storeGasExpense(Request $request)
    {
        $validated = $this->validateGasExpenseRequest($request);
        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        $gasExpense = GasExpense::create(array_merge(
            $this->gasExpensePayload($request),
            ['plate_number' => $vehicle->plate_number]
        ));

        Cache::flush();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Gas expense added successfully.',
                'gasExpense' => $gasExpense->load('vehicle'),
            ]);
        }

        return redirect()->route('gas-expense-po-tracker.index')->with('success', 'Gas expense added successfully.');
    }

    /**
     * Update a gas expense from the tracker.
     */
    public function updateGasExpense(Request $request, GasExpense $gasExpense)
    {
        $validated = $this->validateGasExpenseRequest($request);
        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        $gasExpense->update(array_merge(
            $this->gasExpensePayload($request),
            ['plate_number' => $vehicle->plate_number]
        ));

        Cache::flush();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Gas expense updated successfully.',
                'gasExpense' => $gasExpense->fresh()->load('vehicle'),
            ]);
        }

        return redirect()->route('gas-expense-po-tracker.index')->with('success', 'Gas expense updated successfully.');
    }

    /**
     * Delete a gas expense from the tracker.
     */
    public function destroyGasExpense(Request $request, GasExpense $gasExpense)
    {
        $gasExpense->delete();
        Cache::flush();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Gas expense deleted successfully.',
            ]);
        }

        return redirect()->route('gas-expense-po-tracker.index')->with('success', 'Gas expense deleted successfully.');
    }

    private function validateGasExpenseRequest(Request $request): array
    {
        return $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'date' => 'required|date',
            'driver' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'gas_amount' => 'required|numeric|min:0',
            'expense_sent_by' => 'required|string|in:MERLIN,ALYSSA',
            'has_photo_video_in_groupchat' => 'boolean',
            'photo_fuel_gauge_before' => 'boolean',
            'photo_fuel_gauge_after' => 'boolean',
            'photo_car_license_plate_gas_boy' => 'boolean',
            'photo_receipt_next_to_gas_pump' => 'boolean',
            'checked_by' => 'required|string|max:255',
        ]);
    }

    private function gasExpensePayload(Request $request): array
    {
        return [
            'date' => $request->date,
            'driver' => $request->driver,
            'model' => $request->model,
            'gas_amount' => $request->gas_amount,
            'expense_sent_by' => $request->expense_sent_by,
            'has_photo_video_in_groupchat' => $request->boolean('has_photo_video_in_groupchat'),
            'photo_fuel_gauge_before' => $request->boolean('photo_fuel_gauge_before'),
            'photo_fuel_gauge_after' => $request->boolean('photo_fuel_gauge_after'),
            'photo_car_license_plate_gas_boy' => $request->boolean('photo_car_license_plate_gas_boy'),
            'photo_receipt_next_to_gas_pump' => $request->boolean('photo_receipt_next_to_gas_pump'),
            'checked_by' => $request->checked_by,
        ];
    }
}
