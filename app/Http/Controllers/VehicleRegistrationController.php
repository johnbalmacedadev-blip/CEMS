<?php

namespace App\Http\Controllers;

use App\Models\BranchLocation;
use App\Models\Vehicle;
use App\Models\VehicleRegistration;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;

class VehicleRegistrationController extends Controller
{
    use LogsActivity;

    public function index(Request $request)
    {
        $query = VehicleRegistration::with('vehicle.make', 'vehicle.vehicleModel', 'branchLocation');

        if ($request->filled('branch_location_id')) {
            $query->where('branch_location_id', $request->branch_location_id);
        }
        if ($request->filled('status')) {
            $query->where('status', 'LIKE', '%' . $request->status . '%');
        }
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('remarks', 'LIKE', "%{$search}%")
                    ->orWhere('coc_no', 'LIKE', "%{$search}%")
                    ->orWhere('status', 'LIKE', "%{$search}%")
                    ->orWhereHas('vehicle', function ($vq) use ($search) {
                        $vq->where('plate_number', 'LIKE', "%{$search}%")
                            ->orWhereHas('make', fn($mq) => $mq->where('name', 'LIKE', "%{$search}%"))
                            ->orWhereHas('vehicleModel', fn($mq) => $mq->where('name', 'LIKE', "%{$search}%"))
                            ->orWhere('year', 'LIKE', "%{$search}%");
                    });
            });
        }

        $records = $query->orderBy('date', 'desc')->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        $branches = BranchLocation::ordered()->get();

        return view('vehicle-registration.index', compact('records', 'branches'));
    }

    public function create()
    {
        $vehicles = Vehicle::with(['make', 'vehicleModel'])->orderBy('created_at', 'desc')->get();
        $branches = BranchLocation::active()->ordered()->get();

        return view('vehicle-registration.create', compact('vehicles', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateRecord($request);
        $record = VehicleRegistration::create($validated);
        $record->load('vehicle');
        $this->logCreate(
            $record,
            'Created Vehicle Registration: ' . $this->logLabel($record),
            'Vehicle Registration'
        );

        return redirect()
            ->route('vehicle-registration.index')
            ->with('success', 'Vehicle registration record saved successfully.')
            ->with('swal_title', 'Saved');
    }

    public function show(VehicleRegistration $vehicle_registration)
    {
        return redirect()->route('vehicle-registration.edit', $vehicle_registration);
    }

    public function edit(VehicleRegistration $vehicle_registration)
    {
        $vehicle_registration->load('vehicle.make', 'vehicle.vehicleModel');
        $vehicles = collect([$vehicle_registration->vehicle])->filter();

        $branches = BranchLocation::active()->ordered()->get();

        return view('vehicle-registration.edit', compact('vehicle_registration', 'vehicles', 'branches'));
    }

    public function update(Request $request, VehicleRegistration $vehicle_registration)
    {
        $validated = $this->validateRecord($request);
        $original = $vehicle_registration->getOriginal();
        $vehicle_registration->update($validated);
        $vehicle_registration->load('vehicle');

        $changes = [];
        foreach ($validated as $key => $value) {
            if (array_key_exists($key, $original) && $original[$key] != $value) {
                $changes[$key] = ['old' => $original[$key], 'new' => $value];
            }
        }

        $this->logUpdate(
            $vehicle_registration,
            !empty($changes) ? $changes : null,
            'Updated Vehicle Registration: ' . $this->logLabel($vehicle_registration),
            'Vehicle Registration'
        );

        return redirect()
            ->route('vehicle-registration.index')
            ->with('success', 'Vehicle registration record saved successfully.')
            ->with('swal_title', 'Saved');
    }

    public function destroy(VehicleRegistration $vehicle_registration)
    {
        $vehicle_registration->load('vehicle');
        $label = $this->logLabel($vehicle_registration);
        $this->logDelete($vehicle_registration, 'Deleted Vehicle Registration: ' . $label, 'Vehicle Registration');
        $vehicle_registration->delete();

        return redirect()
            ->route('vehicle-registration.index')
            ->with('success', 'Vehicle registration record removed.')
            ->with('swal_title', 'Deleted');
    }

    private function logLabel(VehicleRegistration $record): string
    {
        $plate = $record->vehicle?->plate_number ?: 'No plate';
        $date = $record->date?->format('j M Y') ?? '';

        return trim("{$plate}" . ($date ? " ({$date})" : ''));
    }

    private function validateRecord(Request $request): array
    {
        return $request->validate([
            'branch_location_id' => 'nullable|exists:branch_locations,id',
            'date' => 'required|date',
            'vehicle_id' => 'required|exists:vehicles,id',
            'renewal_reg_or' => 'nullable|numeric|min:0',
            'renewal_sop' => 'nullable|numeric|min:0',
            'smoke_na' => 'nullable|numeric|min:0',
            'duplicate_plate' => 'nullable|numeric|min:0',
            'migrate' => 'nullable|numeric|min:0',
            'duplicate_cr' => 'nullable|numeric|min:0',
            'pnp_clearance' => 'nullable|numeric|min:0',
            'confirmation' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string|max:2000',
            'coc_no' => 'nullable|string|max:100',
            'status' => 'nullable|string|max:100',
        ]);
    }
}
