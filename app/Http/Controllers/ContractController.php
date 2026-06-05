<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Employee;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ContractController extends Controller
{
    /**
     * Display a listing of contracts.
     */
    public function index(Request $request)
    {
        $query = Contract::with(['vehicle', 'employee']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('contract_type')) {
            $query->where('contract_type', $request->contract_type);
        }
        if ($request->filled('linked_to')) {
            if ($request->linked_to === 'vehicle') {
                $query->whereNotNull('vehicle_id');
            } elseif ($request->linked_to === 'employee') {
                $query->whereNotNull('employee_id');
            }
        }
        if ($request->filled('date_from')) {
            $query->where('start_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where(function ($q) use ($request) {
                $q->where('end_date', '<=', $request->date_to)
                    ->orWhereNull('end_date');
            });
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('party_name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('notes', 'LIKE', "%{$search}%")
                    ->orWhereHas('vehicle', function ($vq) use ($search) {
                        $vq->where('plate_number', 'LIKE', "%{$search}%")
                            ->orWhereHas('make', fn($mq) => $mq->where('name', 'LIKE', "%{$search}%"))
                            ->orWhereHas('vehicleModel', fn($mq) => $mq->where('name', 'LIKE', "%{$search}%"));
                    })
                    ->orWhereHas('employee', function ($eq) use ($search) {
                        $eq->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%");
                    });
            });
        }

        $contracts = $query->orderBy('start_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('contracts.index', compact('contracts'));
    }

    /**
     * Show the form for creating a new contract.
     */
    public function create()
    {
        $employees = Employee::orderBy('last_name')->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'middle_name']);
        return view('contracts.create', compact('employees'));
    }

    /**
     * Store a newly created contract.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'contract_type' => 'required|in:Employment,Vendor,Lease,Other',
            'linked_to' => 'required|in:vehicle,employee',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'employee_id' => 'nullable|exists:employees,id',
            'party_name' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string|max:2000',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'status' => 'required|in:Active,Expired,Terminated',
            'notes' => 'nullable|string|max:2000',
        ]);

        if ($request->linked_to === 'vehicle') {
            $validated['employee_id'] = null;
            if (empty($validated['vehicle_id'])) {
                return redirect()->back()->withInput()->withErrors(['vehicle_id' => 'Please search and select a vehicle.']);
            }
        } else {
            $validated['vehicle_id'] = null;
            if (empty($validated['employee_id'])) {
                return redirect()->back()->withInput()->withErrors(['employee_id' => 'Please select an employee.']);
            }
        }

        $filePath = null;
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $file = $request->file('file');
            $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('contracts', $name, 'public');
        }

        $validated['file_path'] = $filePath;
        unset($validated['file'], $validated['linked_to']);
        Contract::create($validated);

        return redirect()->route('contracts.index')->with('success', 'Contract added successfully.');
    }

    /**
     * Display the specified contract (redirect to edit).
     */
    public function show(Contract $contract)
    {
        return redirect()->route('contracts.edit', $contract);
    }

    /**
     * Show the form for editing the specified contract.
     */
    public function edit(Contract $contract)
    {
        $employees = Employee::orderBy('last_name')->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'middle_name']);
        return view('contracts.edit', compact('contract', 'employees'));
    }

    /**
     * Update the specified contract.
     */
    public function update(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'contract_type' => 'required|in:Employment,Vendor,Lease,Other',
            'linked_to' => 'required|in:vehicle,employee',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'employee_id' => 'nullable|exists:employees,id',
            'party_name' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'description' => 'nullable|string|max:2000',
            'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'status' => 'required|in:Active,Expired,Terminated',
            'notes' => 'nullable|string|max:2000',
        ]);

        if ($request->linked_to === 'vehicle') {
            $validated['employee_id'] = null;
            if (empty($validated['vehicle_id'])) {
                return redirect()->back()->withInput()->withErrors(['vehicle_id' => 'Please search and select a vehicle.']);
            }
        } else {
            $validated['vehicle_id'] = null;
            if (empty($validated['employee_id'])) {
                return redirect()->back()->withInput()->withErrors(['employee_id' => 'Please select an employee.']);
            }
        }

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            if ($contract->file_path && Storage::disk('public')->exists($contract->file_path)) {
                Storage::disk('public')->delete($contract->file_path);
            }
            $file = $request->file('file');
            $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $validated['file_path'] = $file->storeAs('contracts', $name, 'public');
        } else {
            unset($validated['file_path']);
        }
        unset($validated['file'], $validated['linked_to']);
        $contract->update($validated);

        return redirect()->route('contracts.index')->with('success', 'Contract updated successfully.');
    }

    /**
     * Search vehicles for contract (autocomplete).
     */
    public function searchVehicles(Request $request)
    {
        $query = $request->get('q', '');
        $vehicles = Vehicle::with(['make', 'vehicleModel'])
            ->where(function ($q) use ($query) {
                $q->where('plate_number', 'LIKE', "%{$query}%")
                    ->orWhereHas('make', fn($mq) => $mq->where('name', 'LIKE', "%{$query}%"))
                    ->orWhereHas('vehicleModel', fn($mq) => $mq->where('name', 'LIKE', "%{$query}%"))
                    ->orWhere('year', 'LIKE', "%{$query}%");
            })
            ->limit(15)
            ->get();
        return response()->json($vehicles->map(fn($v) => [
            'id' => $v->id,
            'full_name' => $v->full_name,
            'plate_number' => $v->plate_number,
            'year' => $v->year,
            'make' => $v->make && is_object($v->make) ? $v->make->name : ($v->make ?? ''),
            'series' => $v->vehicleModel && is_object($v->vehicleModel) ? $v->vehicleModel->name : ($v->model ?? ''),
        ]));
    }

    /**
     * Remove the specified contract.
     */
    public function destroy(Contract $contract)
    {
        if ($contract->file_path && Storage::disk('public')->exists($contract->file_path)) {
            Storage::disk('public')->delete($contract->file_path);
        }
        $contract->delete();
        return redirect()->route('contracts.index')->with('success', 'Contract removed.');
    }
}
