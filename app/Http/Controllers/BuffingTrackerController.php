<?php

namespace App\Http\Controllers;

use App\Models\BuffingRecord;
use App\Models\Vehicle;
use App\Models\Employee;
use Illuminate\Http\Request;

class BuffingTrackerController extends Controller
{
    /**
     * Display the buffing tracker (list of buffing records).
     */
    public function index(Request $request)
    {
        $query = BuffingRecord::with(['vehicle', 'employee']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->where('buffing_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('buffing_date', '<=', $request->date_to);
        }

        $records = $query->orderBy('buffing_date', 'desc')->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('buffing-tracker.index', compact('records'));
    }

    /**
     * Show the form for creating a new buffing record.
     */
    public function create()
    {
        $vehicles = Vehicle::with(['make', 'vehicleModel'])->orderBy('created_at', 'desc')->get();
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();

        return view('buffing-tracker.create', compact('vehicles', 'employees'));
    }

    /**
     * Store a newly created buffing record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'employee_id' => 'nullable|exists:employees,id',
            'buffing_date' => 'required|date',
            'status' => 'required|in:Pending,In Progress,Completed',
            'notes' => 'nullable|string|max:2000',
        ]);

        if (($validated['status'] ?? '') === BuffingRecord::STATUS_COMPLETED) {
            $validated['completed_at'] = now();
        }

        BuffingRecord::create($validated);

        return redirect()->route('buffing-tracker.index')->with('success', 'Buffing record added successfully.');
    }

    /**
     * Display the specified buffing record (redirect to edit).
     */
    public function show(BuffingRecord $buffing_tracker)
    {
        return redirect()->route('buffing-tracker.edit', $buffing_tracker);
    }

    /**
     * Show the form for editing the specified buffing record.
     */
    public function edit(BuffingRecord $buffing_tracker)
    {
        $vehicles = Vehicle::with(['make', 'vehicleModel'])->orderBy('created_at', 'desc')->get();
        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();

        return view('buffing-tracker.edit', compact('buffing_tracker', 'vehicles', 'employees'));
    }

    /**
     * Update the specified buffing record.
     */
    public function update(Request $request, BuffingRecord $buffing_tracker)
    {
        $validated = $request->validate([
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'employee_id' => 'nullable|exists:employees,id',
            'buffing_date' => 'required|date',
            'status' => 'required|in:Pending,In Progress,Completed',
            'notes' => 'nullable|string|max:2000',
        ]);

        if ($validated['status'] === BuffingRecord::STATUS_COMPLETED && !$buffing_tracker->completed_at) {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] !== BuffingRecord::STATUS_COMPLETED) {
            $validated['completed_at'] = null;
        }

        $buffing_tracker->update($validated);

        return redirect()->route('buffing-tracker.index')->with('success', 'Buffing record updated successfully.');
    }

    /**
     * Remove the specified buffing record.
     */
    public function destroy(BuffingRecord $buffing_tracker)
    {
        $buffing_tracker->delete();

        return redirect()->route('buffing-tracker.index')->with('success', 'Buffing record deleted successfully.');
    }
}
