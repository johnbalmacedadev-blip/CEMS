<?php

namespace App\Http\Controllers;

use App\Models\ClientFollowUp;
use App\Models\ExecutiveAgent;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class ClientFollowUpListController extends Controller
{
    public function index(Request $request)
    {
        $query = ClientFollowUp::with(['vehicle', 'executiveAgent']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('executive_agent_id')) {
            $query->where('executive_agent_id', $request->executive_agent_id);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('date_of_first_inquiry', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('date_of_first_inquiry', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'LIKE', "%{$search}%")
                    ->orWhere('contact_number', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('unit_inquired', 'LIKE', "%{$search}%")
                    ->orWhere('application', 'LIKE', "%{$search}%")
                    ->orWhere('about_what', 'LIKE', "%{$search}%")
                    ->orWhere('notes', 'LIKE', "%{$search}%")
                    ->orWhere('team_lead', 'LIKE', "%{$search}%")
                    ->orWhere('sales_exec_1', 'LIKE', "%{$search}%")
                    ->orWhere('sales_exec_2', 'LIKE', "%{$search}%")
                    ->orWhere('sales_exec_3', 'LIKE', "%{$search}%")
                    ->orWhere('sales_exec_4', 'LIKE', "%{$search}%")
                    ->orWhere('sales_exec_5', 'LIKE', "%{$search}%");
            });
        }

        $clients = $query->orderByRaw('CASE WHEN date_of_first_inquiry IS NULL THEN 1 ELSE 0 END')
            ->orderByDesc('date_of_first_inquiry')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        $executives = ExecutiveAgent::orderBy('name')->get(['id', 'name']);

        return view('client-follow-up-list.index', compact('clients', 'executives'));
    }

    public function create()
    {
        return view('client-follow-up-list.create', $this->formData());
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        ClientFollowUp::create($validated);

        return redirect()->route('client-follow-up-list.index')->with('success', 'Client added.');
    }

    public function show(ClientFollowUp $client_follow_up_list)
    {
        return redirect()->route('client-follow-up-list.edit', $client_follow_up_list);
    }

    public function edit(ClientFollowUp $client_follow_up_list)
    {
        return view('client-follow-up-list.edit', array_merge(
            $this->formData(),
            ['client_follow_up_list' => $client_follow_up_list]
        ));
    }

    public function update(Request $request, ClientFollowUp $client_follow_up_list)
    {
        $validated = $this->validated($request);
        $client_follow_up_list->update($validated);

        return redirect()->route('client-follow-up-list.index')->with('success', 'Client updated.');
    }

    public function destroy(ClientFollowUp $client_follow_up_list)
    {
        $client_follow_up_list->delete();

        return redirect()->route('client-follow-up-list.index')->with('success', 'Client removed.');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'executive_agent_id' => 'nullable|exists:executive_agents,id',
            'team_lead' => 'nullable|string|max:100',
            'date_of_first_inquiry' => 'nullable|date',
            'application' => 'nullable|string|max:255',
            'client_name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'unit_inquired' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:5000',
            'about_what' => 'nullable|string|max:500',
            'sales_exec_1' => 'nullable|string|max:100',
            'date_followed_up_1' => 'nullable|date',
            'outcome_1' => 'nullable|string|max:255',
            'notes_1' => 'nullable|string|max:5000',
            'sales_exec_2' => 'nullable|string|max:100',
            'date_followed_up_2' => 'nullable|date',
            'outcome_2' => 'nullable|string|max:255',
            'notes_2' => 'nullable|string|max:5000',
            'sales_exec_3' => 'nullable|string|max:100',
            'date_followed_up_3' => 'nullable|date',
            'outcome_3' => 'nullable|string|max:255',
            'notes_3' => 'nullable|string|max:5000',
            'sales_exec_4' => 'nullable|string|max:100',
            'date_followed_up_4' => 'nullable|date',
            'outcome_4' => 'nullable|string|max:255',
            'notes_4' => 'nullable|string|max:5000',
            'sales_exec_5' => 'nullable|string|max:100',
            'date_followed_up_5' => 'nullable|date',
            'outcome_5' => 'nullable|string|max:255',
            'notes_5' => 'nullable|string|max:5000',
            'follow_up_date' => 'nullable|date',
            'status' => 'required|in:Pending,Contacted,In Progress,Closed',
        ]);

        if (! empty($validated['executive_agent_id']) && empty($validated['team_lead'])) {
            $exec = ExecutiveAgent::find($validated['executive_agent_id']);
            if ($exec) {
                $validated['team_lead'] = strtoupper($exec->name);
            }
        }

        // Keep follow_up_date synced to latest follow-up date if blank
        if (empty($validated['follow_up_date'])) {
            for ($i = 5; $i >= 1; $i--) {
                if (! empty($validated["date_followed_up_{$i}"])) {
                    $validated['follow_up_date'] = $validated["date_followed_up_{$i}"];
                    break;
                }
            }
        }

        return $validated;
    }

    private function formData(): array
    {
        $vehicles = Vehicle::with(['make', 'vehicleModel'])->orderBy('created_at', 'desc')->limit(500)->get();
        $executives = ExecutiveAgent::orderBy('name')->get();

        return compact('vehicles', 'executives');
    }
}
