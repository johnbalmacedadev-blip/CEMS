<?php

namespace App\Http\Controllers;

use App\Models\InsuranceTracker;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class InsuranceTrackerController extends Controller
{
    public function index(Request $request)
    {
        $query = InsuranceTracker::with('vehicle');

        if ($request->filled('month')) {
            $month = (int) $request->month;
            $year = $request->filled('year') ? (int) $request->year : (int) date('Y');
            $query->whereMonth('release_date', $month)->whereYear('release_date', $year);
        }
        if ($request->filled('showroom')) {
            $query->where('showroom', 'LIKE', '%' . $request->showroom . '%');
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('showroom', 'LIKE', "%{$search}%")
                    ->orWhere('sales', 'LIKE', "%{$search}%")
                    ->orWhere('year', 'LIKE', "%{$search}%")
                    ->orWhere('make', 'LIKE', "%{$search}%")
                    ->orWhere('model', 'LIKE', "%{$search}%")
                    ->orWhere('number', 'LIKE', "%{$search}%")
                    ->orWhere('source', 'LIKE', "%{$search}%")
                    ->orWhereHas('vehicle', function ($vq) use ($search) {
                        $vq->where('plate_number', 'LIKE', "%{$search}%")
                            ->orWhereHas('make', fn($mq) => $mq->where('name', 'LIKE', "%{$search}%"))
                            ->orWhereHas('vehicleModel', fn($mq) => $mq->where('name', 'LIKE', "%{$search}%"));
                    });
            });
        }

        $records = $query->orderBy('release_date', 'desc')
            ->orderBy('reservation_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('insurance-tracker.index', compact('records'));
    }

    public function create()
    {
        $vehicles = Vehicle::with(['make', 'vehicleModel'])->orderBy('created_at', 'desc')->get();
        return view('insurance-tracker.create', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'showroom' => 'nullable|string|max:100',
            'sales' => 'nullable|string|max:100',
            'year' => 'nullable|string|max:20',
            'make' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'number' => 'nullable|string|max:50',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'transaction' => 'nullable|string|max:50',
            'source' => 'nullable|string|max:255',
            'reservation_date' => 'nullable|date',
            'release_date' => 'nullable|date',
            'amount' => 'nullable|numeric|min:0',
        ]);

        InsuranceTracker::create($validated);

        return redirect()->route('insurance-tracker.index')->with('success', 'Insurance record added.');
    }

    public function show(InsuranceTracker $insurance_tracker)
    {
        return redirect()->route('insurance-tracker.edit', $insurance_tracker);
    }

    public function edit(InsuranceTracker $insurance_tracker)
    {
        $insurance_tracker->load('vehicle');
        $vehicles = Vehicle::with(['make', 'vehicleModel'])->orderBy('created_at', 'desc')->get();
        return view('insurance-tracker.edit', compact('insurance_tracker', 'vehicles'));
    }

    public function update(Request $request, InsuranceTracker $insurance_tracker)
    {
        $validated = $request->validate([
            'showroom' => 'nullable|string|max:100',
            'sales' => 'nullable|string|max:100',
            'year' => 'nullable|string|max:20',
            'make' => 'nullable|string|max:100',
            'model' => 'nullable|string|max:100',
            'number' => 'nullable|string|max:50',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'transaction' => 'nullable|string|max:50',
            'source' => 'nullable|string|max:255',
            'reservation_date' => 'nullable|date',
            'release_date' => 'nullable|date',
            'amount' => 'nullable|numeric|min:0',
        ]);

        $insurance_tracker->update($validated);

        return redirect()->route('insurance-tracker.index')->with('success', 'Insurance record updated.');
    }

    public function destroy(InsuranceTracker $insurance_tracker)
    {
        $insurance_tracker->delete();
        return redirect()->route('insurance-tracker.index')->with('success', 'Insurance record removed.');
    }
}
