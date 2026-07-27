<?php

namespace App\Http\Controllers;

use App\Models\UnitMasterlist;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class UnitsMasterlistController extends Controller
{
    public function index(Request $request)
    {
        $query = UnitMasterlist::with('vehicle');

        if ($request->filled('year')) {
            $query->where('year', 'like', '%'.$request->year.'%');
        }
        if ($request->filled('transmission')) {
            $query->where('transmission', $request->transmission);
        }
        if ($request->filled('linked')) {
            if ($request->linked === 'yes') {
                $query->whereNotNull('vehicle_id');
            } elseif ($request->linked === 'no') {
                $query->whereNull('vehicle_id');
            }
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('make_model', 'like', "%{$search}%")
                    ->orWhere('plate_number', 'like', "%{$search}%")
                    ->orWhere('variant', 'like', "%{$search}%")
                    ->orWhere('fuel_type', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $units = $query->orderByRaw('list_number IS NULL')
            ->orderBy('list_number')
            ->orderBy('id')
            ->paginate(25)
            ->withQueryString();

        return view('units-masterlist.index', compact('units'));
    }

    public function create()
    {
        return view('units-masterlist.create', $this->formData());
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $validated = $this->resolveVehicleLink($validated);

        UnitMasterlist::create($validated);

        return redirect()->route('units-masterlist.index')
            ->with('success', 'Unit added to masterlist.');
    }

    public function show(UnitMasterlist $units_masterlist)
    {
        return redirect()->route('units-masterlist.index');
    }

    public function edit(UnitMasterlist $units_masterlist)
    {
        return view('units-masterlist.edit', array_merge(
            $this->formData(),
            ['unit' => $units_masterlist]
        ));
    }

    public function update(Request $request, UnitMasterlist $units_masterlist)
    {
        $validated = $this->validated($request);
        $validated = $this->resolveVehicleLink($validated);

        $units_masterlist->update($validated);

        return redirect()->route('units-masterlist.index')
            ->with('success', 'Unit masterlist record updated.');
    }

    public function destroy(UnitMasterlist $units_masterlist)
    {
        $units_masterlist->delete();

        return redirect()->route('units-masterlist.index')
            ->with('success', 'Unit removed from masterlist.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'list_number' => 'nullable|integer|min:0',
            'make_model' => 'required|string|max:255',
            'plate_number' => 'nullable|string|max:50',
            'variant' => 'nullable|string|max:500',
            'transmission' => 'nullable|string|max:50',
            'fuel_type' => 'nullable|string|max:50',
            'year' => 'nullable|string|max:50',
            'mileage' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'low_down_payment_option' => 'nullable|string|max:5000',
            'low_monthly_option' => 'nullable|string|max:5000',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'notes' => 'nullable|string|max:2000',
        ]);

        if (! empty($data['plate_number'])) {
            $data['plate_number'] = strtoupper(trim($data['plate_number']));
        }

        return $data;
    }

    private function resolveVehicleLink(array $data): array
    {
        if (! empty($data['vehicle_id'])) {
            $vehicle = Vehicle::find($data['vehicle_id']);
            if ($vehicle && empty($data['plate_number']) && $vehicle->plate_number) {
                $data['plate_number'] = $vehicle->plate_number;
            }

            return $data;
        }

        if (! empty($data['plate_number'])) {
            $normalized = UnitMasterlist::normalizePlate($data['plate_number']);
            $match = Vehicle::query()->get(['id', 'plate_number'])->first(function ($v) use ($normalized) {
                return UnitMasterlist::normalizePlate($v->plate_number) === $normalized;
            });
            if ($match) {
                $data['vehicle_id'] = $match->id;
            }
        }

        return $data;
    }

    private function formData(): array
    {
        $vehicles = Vehicle::with(['make', 'vehicleModel'])
            ->whereNull('archived_at')
            ->orderBy('created_at', 'desc')
            ->limit(800)
            ->get();

        $financingSchemes = \App\Models\FinancingScheme::query()
            ->with(['carFinancingSettings' => function ($q) {
                $q->orderBy('year_model_range');
            }])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->map(function ($scheme) {
                return [
                    'id' => $scheme->id,
                    'name' => $scheme->name,
                    'settings' => $scheme->carFinancingSettings->map(function ($s) {
                        return [
                            'id' => $s->id,
                            'year_model_range' => $s->year_model_range,
                            'chattel' => (float) $s->chattel_fee,
                            'chattel_fee_percent' => $s->chattel_fee_percent !== null ? (float) $s->chattel_fee_percent : null,
                            'insurance' => (float) $s->insurance_initial,
                            'no_pdc' => (float) $s->no_pdc_charge,
                            'pct12' => (float) $s->term_pct_12,
                            'pct24' => (float) $s->term_pct_24,
                            'pct36' => (float) $s->term_pct_36,
                            'pct48' => (float) $s->term_pct_48,
                            'pct60' => (float) $s->term_pct_60,
                            'year_min' => $s->getYearRange()[0],
                            'year_max' => $s->getYearRange()[1],
                        ];
                    })->values(),
                ];
            })
            ->values();

        return compact('vehicles', 'financingSchemes');
    }
}
