<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class AppointmentListController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with('vehicle');

        if ($request->filled('date_visit_from')) {
            $query->where('date_of_visit', '>=', $request->date_visit_from);
        }
        if ($request->filled('date_visit_to')) {
            $query->where('date_of_visit', '<=', $request->date_visit_to);
        }
        if ($request->filled('showroom')) {
            $query->where('showroom', 'LIKE', '%' . $request->showroom . '%');
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_first_name', 'LIKE', "%{$search}%")
                    ->orWhere('customer_last_name', 'LIKE', "%{$search}%")
                    ->orWhere('customer_phone_number', 'LIKE', "%{$search}%")
                    ->orWhere('preferred_unit', 'LIKE', "%{$search}%")
                    ->orWhere('sales_exec_who_assisted', 'LIKE', "%{$search}%")
                    ->orWhere('outcome', 'LIKE', "%{$search}%")
                    ->orWhere('notes', 'LIKE', "%{$search}%")
                    ->orWhere('notes_of_visit', 'LIKE', "%{$search}%");
            });
        }

        $appointments = $query->orderByRaw('CASE WHEN date_of_visit IS NULL THEN 1 ELSE 0 END')
            ->orderBy('date_of_visit', 'desc')
            ->orderBy('date_added_to_schedule', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('appointment-list.index', compact('appointments'));
    }

    public function create()
    {
        $vehicles = Vehicle::with(['make', 'vehicleModel'])->orderBy('created_at', 'desc')->get();
        return view('appointment-list.create', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_added_to_schedule' => 'nullable|date',
            'added_by' => 'nullable|string|max:100',
            'customer_first_name' => 'required|string|max:255',
            'customer_last_name' => 'required|string|max:255',
            'customer_phone_number' => 'nullable|string|max:50',
            'showroom' => 'nullable|string|max:100',
            'date_of_visit' => 'nullable|date',
            'preferred_unit' => 'nullable|string|max:500',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'notes' => 'nullable|string|max:2000',
            'sales_exec_who_assisted' => 'nullable|string|max:100',
            'outcome' => 'nullable|string|max:255',
            'notes_of_visit' => 'nullable|string|max:5000',
        ]);

        Appointment::create($validated);

        return redirect()->route('appointment-list.index')->with('success', 'Appointment added.');
    }

    public function show(Appointment $appointment_list)
    {
        return redirect()->route('appointment-list.edit', $appointment_list);
    }

    public function edit(Appointment $appointment_list)
    {
        $appointment_list->load('vehicle');
        $vehicles = Vehicle::with(['make', 'vehicleModel'])->orderBy('created_at', 'desc')->get();
        return view('appointment-list.edit', compact('appointment_list', 'vehicles'));
    }

    public function update(Request $request, Appointment $appointment_list)
    {
        $validated = $request->validate([
            'date_added_to_schedule' => 'nullable|date',
            'added_by' => 'nullable|string|max:100',
            'customer_first_name' => 'required|string|max:255',
            'customer_last_name' => 'required|string|max:255',
            'customer_phone_number' => 'nullable|string|max:50',
            'showroom' => 'nullable|string|max:100',
            'date_of_visit' => 'nullable|date',
            'preferred_unit' => 'nullable|string|max:500',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'notes' => 'nullable|string|max:2000',
            'sales_exec_who_assisted' => 'nullable|string|max:100',
            'outcome' => 'nullable|string|max:255',
            'notes_of_visit' => 'nullable|string|max:5000',
        ]);

        $appointment_list->update($validated);

        return redirect()->route('appointment-list.index')->with('success', 'Appointment updated.');
    }

    public function destroy(Appointment $appointment_list)
    {
        $appointment_list->delete();
        return redirect()->route('appointment-list.index')->with('success', 'Appointment removed.');
    }
}
