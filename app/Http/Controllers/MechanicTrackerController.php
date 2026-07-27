<?php

namespace App\Http\Controllers;

use App\Models\MechanicJob;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class MechanicTrackerController extends Controller
{
    public function index(Request $request)
    {
        $query = MechanicJob::with('vehicle');

        if ($request->filled('job_type')) {
            $query->where('job_type', $request->job_type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('date_from')) {
            $query->where('job_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('job_date', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('mechanic', 'like', "%{$search}%")
                    ->orWhere('year_model', 'like', "%{$search}%")
                    ->orWhere('plate_number', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('labor', 'like', "%{$search}%")
                    ->orWhere('parts', 'like', "%{$search}%")
                    ->orWhere('unit_label', 'like', "%{$search}%")
                    ->orWhere('endorse', 'like', "%{$search}%");
            });
        }

        $records = $query->orderBy('job_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('mechanic-tracker.index', compact('records'));
    }

    public function create()
    {
        return view('mechanic-tracker.create', $this->formData());
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $validated = $this->attachVehicle($validated);

        MechanicJob::create($validated);

        return redirect()->route('mechanic-tracker.index')
            ->with('success', 'Mechanic job added successfully.');
    }

    public function show(MechanicJob $mechanic_tracker)
    {
        return redirect()->route('mechanic-tracker.edit', $mechanic_tracker);
    }

    public function edit(MechanicJob $mechanic_tracker)
    {
        return view('mechanic-tracker.edit', array_merge(
            $this->formData(),
            ['mechanic_tracker' => $mechanic_tracker]
        ));
    }

    public function update(Request $request, MechanicJob $mechanic_tracker)
    {
        $validated = $this->validated($request);
        $validated = $this->attachVehicle($validated);
        $mechanic_tracker->update($validated);

        return redirect()->route('mechanic-tracker.index')
            ->with('success', 'Mechanic job updated successfully.');
    }

    public function destroy(MechanicJob $mechanic_tracker)
    {
        $mechanic_tracker->delete();

        return redirect()->route('mechanic-tracker.index')
            ->with('success', 'Mechanic job deleted successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'job_date' => 'required|date',
            'job_type' => 'required|in:'.implode(',', MechanicJob::jobTypeOptions()),
            'mechanic' => 'nullable|string|max:255',
            'category' => 'nullable|string|max:100',
            'year_model' => 'nullable|string|max:255',
            'plate_number' => 'nullable|string|max:50',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'endorse' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:5000',
            'labor' => 'nullable|string|max:5000',
            'parts' => 'nullable|string|max:5000',
            'parts_cost' => 'nullable|numeric|min:0',
            'status' => 'nullable|string|max:100',
            'unit_label' => 'nullable|string|max:255',
        ]);
    }

    private function attachVehicle(array $validated): array
    {
        if (! empty($validated['vehicle_id'])) {
            $vehicle = Vehicle::find($validated['vehicle_id']);
            if ($vehicle && empty($validated['plate_number'])) {
                $validated['plate_number'] = $vehicle->plate_number;
            }

            return $validated;
        }

        $plate = strtoupper(preg_replace('/\s+/', '', (string) ($validated['plate_number'] ?? '')));
        if ($plate !== '') {
            $validated['plate_number'] = $plate;
            $vehicle = Vehicle::whereRaw("UPPER(REPLACE(plate_number, ' ', '')) = ?", [$plate])->first();
            $validated['vehicle_id'] = $vehicle?->id;
        }

        return $validated;
    }

    private function formData(): array
    {
        $vehicles = Vehicle::with(['make', 'vehicleModel'])->orderBy('created_at', 'desc')->limit(500)->get();

        return compact('vehicles');
    }
}
