<?php

namespace App\Http\Controllers;

use App\Models\ClientFollowUp;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class ClientFollowUpListController extends Controller
{
    /**
     * Display the client follow up list.
     */
    public function index(Request $request)
    {
        $query = ClientFollowUp::with('vehicle');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->where('follow_up_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('follow_up_date', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'LIKE', "%{$search}%")
                    ->orWhere('contact_number', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('notes', 'LIKE', "%{$search}%");
            });
        }

        $clients = $query->orderByRaw('CASE WHEN follow_up_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('follow_up_date')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('client-follow-up-list.index', compact('clients'));
    }

    /**
     * Show the form for creating a new client follow-up entry.
     */
    public function create()
    {
        $vehicles = Vehicle::with(['make', 'vehicleModel'])->orderBy('created_at', 'desc')->get();
        return view('client-follow-up-list.create', compact('vehicles'));
    }

    /**
     * Store a newly created client follow-up entry.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_of_first_inquiry' => 'nullable|date',
            'application' => 'nullable|string|max:50',
            'client_name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'unit_inquired' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
            'about_what' => 'nullable|string|max:255',
            'sales_exec_1' => 'nullable|string|max:100',
            'date_followed_up_1' => 'nullable|date',
            'outcome_1' => 'nullable|string|max:255',
            'notes_1' => 'nullable|string|max:2000',
            'sales_exec_2' => 'nullable|string|max:100',
            'date_followed_up_2' => 'nullable|date',
            'outcome_2' => 'nullable|string|max:255',
            'notes_2' => 'nullable|string|max:2000',
            'sales_exec_3' => 'nullable|string|max:100',
            'date_followed_up_3' => 'nullable|date',
            'outcome_3' => 'nullable|string|max:255',
            'notes_3' => 'nullable|string|max:2000',
            'sales_exec_4' => 'nullable|string|max:100',
            'date_followed_up_4' => 'nullable|date',
            'outcome_4' => 'nullable|string|max:255',
            'notes_4' => 'nullable|string|max:2000',
            'follow_up_date' => 'nullable|date',
            'status' => 'required|in:Pending,Contacted,In Progress,Closed',
        ]);

        ClientFollowUp::create($validated);

        return redirect()->route('client-follow-up-list.index')->with('success', 'Client added to follow-up list.');
    }

    /**
     * Display the specified entry (redirect to edit).
     */
    public function show(ClientFollowUp $client_follow_up_list)
    {
        return redirect()->route('client-follow-up-list.edit', $client_follow_up_list);
    }

    /**
     * Show the form for editing the specified client follow-up entry.
     */
    public function edit(ClientFollowUp $client_follow_up_list)
    {
        $vehicles = Vehicle::with(['make', 'vehicleModel'])->orderBy('created_at', 'desc')->get();
        return view('client-follow-up-list.edit', compact('client_follow_up_list', 'vehicles'));
    }

    /**
     * Update the specified client follow-up entry.
     */
    public function update(Request $request, ClientFollowUp $client_follow_up_list)
    {
        $validated = $request->validate([
            'date_of_first_inquiry' => 'nullable|date',
            'application' => 'nullable|string|max:50',
            'client_name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'unit_inquired' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
            'about_what' => 'nullable|string|max:255',
            'sales_exec_1' => 'nullable|string|max:100',
            'date_followed_up_1' => 'nullable|date',
            'outcome_1' => 'nullable|string|max:255',
            'notes_1' => 'nullable|string|max:2000',
            'sales_exec_2' => 'nullable|string|max:100',
            'date_followed_up_2' => 'nullable|date',
            'outcome_2' => 'nullable|string|max:255',
            'notes_2' => 'nullable|string|max:2000',
            'sales_exec_3' => 'nullable|string|max:100',
            'date_followed_up_3' => 'nullable|date',
            'outcome_3' => 'nullable|string|max:255',
            'notes_3' => 'nullable|string|max:2000',
            'sales_exec_4' => 'nullable|string|max:100',
            'date_followed_up_4' => 'nullable|date',
            'outcome_4' => 'nullable|string|max:255',
            'notes_4' => 'nullable|string|max:2000',
            'follow_up_date' => 'nullable|date',
            'status' => 'required|in:Pending,Contacted,In Progress,Closed',
        ]);

        $client_follow_up_list->update($validated);

        return redirect()->route('client-follow-up-list.index')->with('success', 'Client follow-up updated.');
    }

    /**
     * Remove the specified client follow-up entry.
     */
    public function destroy(ClientFollowUp $client_follow_up_list)
    {
        $client_follow_up_list->delete();
        return redirect()->route('client-follow-up-list.index')->with('success', 'Client removed from follow-up list.');
    }
}
