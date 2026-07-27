<?php

namespace App\Http\Controllers;

use App\Models\DriverActivity;
use App\Models\Employee;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class DriverActivityTrackerController extends Controller
{
    public function index(Request $request)
    {
        $query = DriverActivity::with(['vehicle', 'employee']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('activity_type')) {
            $query->where('activity_type', $request->activity_type);
        }
        if ($request->filled('date_from')) {
            $query->where('activity_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('activity_date', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('destination', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%")
                    ->orWhereHas('employee', function ($eq) use ($search) {
                        $eq->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('vehicle', function ($vq) use ($search) {
                        $vq->where('plate_number', 'like', "%{$search}%");
                    });
            });
        }

        $records = $query->orderBy('activity_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('driver-activity-tracker.index', compact('records'));
    }

    public function create()
    {
        return view('driver-activity-tracker.create', $this->formData());
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);

        if (($validated['status'] ?? '') === DriverActivity::STATUS_COMPLETED) {
            $validated['completed_at'] = now();
        }

        DriverActivity::create($validated);

        return redirect()->route('driver-activity-tracker.index')
            ->with('success', 'Driver activity record added successfully.');
    }

    public function show(DriverActivity $driver_activity_tracker)
    {
        return redirect()->route('driver-activity-tracker.edit', $driver_activity_tracker);
    }

    public function edit(DriverActivity $driver_activity_tracker)
    {
        return view('driver-activity-tracker.edit', array_merge(
            $this->formData(),
            ['driver_activity_tracker' => $driver_activity_tracker]
        ));
    }

    public function update(Request $request, DriverActivity $driver_activity_tracker)
    {
        $validated = $this->validated($request);

        if ($validated['status'] === DriverActivity::STATUS_COMPLETED && ! $driver_activity_tracker->completed_at) {
            $validated['completed_at'] = now();
        } elseif ($validated['status'] !== DriverActivity::STATUS_COMPLETED) {
            $validated['completed_at'] = null;
        }

        $driver_activity_tracker->update($validated);

        return redirect()->route('driver-activity-tracker.index')
            ->with('success', 'Driver activity record updated successfully.');
    }

    public function destroy(DriverActivity $driver_activity_tracker)
    {
        $driver_activity_tracker->delete();

        return redirect()->route('driver-activity-tracker.index')
            ->with('success', 'Driver activity record deleted successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'employee_id' => 'nullable|exists:employees,id',
            'activity_date' => 'required|date',
            'activity_type' => 'required|in:'.implode(',', DriverActivity::activityTypeOptions()),
            'destination' => 'nullable|string|max:255',
            'status' => 'required|in:'.implode(',', DriverActivity::statusOptions()),
            'notes' => 'nullable|string|max:2000',
        ]);
    }

    private function formData(): array
    {
        $vehicles = Vehicle::with(['make', 'vehicleModel'])->orderBy('created_at', 'desc')->get();

        $drivers = Employee::where('status', 'active')
            ->where(function ($q) {
                $q->where('role', 'like', '%DRIVER%')
                    ->orWhere('role', 'like', '%Quality Checker%')
                    ->orWhere('role', 'like', '%QUALITY CHECKER%');
            })
            ->orderBy('first_name')
            ->get();

        if ($drivers->isEmpty()) {
            $drivers = Employee::where('status', 'active')->orderBy('first_name')->get();
        }

        $employees = Employee::where('status', 'active')->orderBy('first_name')->get();

        return compact('vehicles', 'drivers', 'employees');
    }
}
