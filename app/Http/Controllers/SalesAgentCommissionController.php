<?php

namespace App\Http\Controllers;

use App\Models\SalesAgent;
use App\Models\SalesAgentCommission;
use App\Models\Showroom;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SalesAgentCommissionController extends Controller
{
    public function index(Request $request)
    {
        $query = SalesAgentCommission::with([
            'vehicle.make',
            'vehicle.vehicleModel',
            'salesAgent.executiveAgent',
        ]);

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }
        if ($request->filled('agent')) {
            $term = '%' . $request->agent . '%';
            $query->where(function ($q) use ($term) {
                $q->where('agent_name', 'LIKE', $term)
                    ->orWhereHas('salesAgent', fn ($sq) => $sq->where('name', 'LIKE', $term));
            });
        }
        if ($request->filled('date_from')) {
            $query->where('date_sent', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date_sent', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('agent_name', 'LIKE', "%{$search}%")
                    ->orWhere('client_name', 'LIKE', "%{$search}%")
                    ->orWhere('unit', 'LIKE', "%{$search}%")
                    ->orWhere('plate_number', 'LIKE', "%{$search}%")
                    ->orWhereHas('vehicle', function ($vq) use ($search) {
                        $vq->where('plate_number', 'LIKE', "%{$search}%")
                            ->orWhereHas('make', fn($mq) => $mq->where('name', 'LIKE', "%{$search}%"))
                            ->orWhereHas('vehicleModel', fn($mq) => $mq->where('name', 'LIKE', "%{$search}%"));
                    })
                    ->orWhereHas('salesAgent', function ($sq) use ($search) {
                        $sq->where('name', 'LIKE', "%{$search}%")
                            ->orWhereHas('executiveAgent', fn($eq) => $eq->where('name', 'LIKE', "%{$search}%"));
                    });
            });
        }

        $commissions = $query->orderBy('date_sent', 'desc')->orderBy('created_at', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('sales-agent-commissions.index', compact('commissions'));
    }

    /**
     * JSON typeahead for commission forms: match name or staff ID code.
     */
    public function searchAgents(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $like = '%' . addcslashes($q, '%_\\') . '%';

        $agents = SalesAgent::query()
            ->where(function ($query) use ($like) {
                $query->where('name', 'LIKE', $like)
                    ->orWhere('sales_agent_id', 'LIKE', $like);
            })
            ->orderByRaw("CASE WHEN LOWER(status) = 'active' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->limit(30)
            ->get(['id', 'name', 'sales_agent_id', 'status']);

        return response()->json($agents->map(function (SalesAgent $a) {
            $suffix = $a->sales_agent_id ? ' (' . $a->sales_agent_id . ')' : '';

            return [
                'id' => $a->id,
                'name' => $a->name,
                'sales_agent_id' => $a->sales_agent_id,
                'display' => $a->name . $suffix,
            ];
        }));
    }

    public function create(Request $request)
    {
        $preselectedVehicle = null;
        if ($request->filled('vehicle_id')) {
            $preselectedVehicle = \App\Models\Vehicle::with('make', 'vehicleModel')->find($request->vehicle_id);
        }
        $showroomNames = $this->showroomNamesForSelect();

        return view('sales-agent-commissions.create', compact('preselectedVehicle', 'showroomNames'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'showroom' => ['required', 'string', 'max:100', Rule::in($this->allowedCommissionShowrooms())],
            'sales_agent_id' => 'nullable|exists:sales_agents,id',
            'agent_name' => 'required|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:255',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'plate_number' => 'nullable|string|max:50',
            'transaction_type' => 'required|in:CASH,FINANCING',
            'release_date' => 'nullable|date',
            'amount' => 'required|numeric|min:0',
            'agents_folder_amount' => 'nullable|numeric|min:0',
            'sales_executive_commission' => 'nullable|numeric|min:0',
            'proof_of_appointment' => 'nullable|boolean',
            'sign_client_with_agent' => 'nullable|boolean',
            'date_sent' => 'nullable|date',
            'date_of_payment' => 'nullable|date',
            'notes' => 'nullable|string|max:2000',
        ]);

        $validated['proof_of_appointment'] = $request->boolean('proof_of_appointment');
        $validated['sign_client_with_agent'] = $request->boolean('sign_client_with_agent');

        if (! empty($validated['sales_agent_id'])) {
            $agent = SalesAgent::find($validated['sales_agent_id']);
            if ($agent) {
                $validated['agent_name'] = $agent->name;
            }
        }

        SalesAgentCommission::create($validated);
        return redirect()->route('sales-agent-commissions.index')->with('success', 'Commission record added.');
    }

    public function show(SalesAgentCommission $sales_agent_commission)
    {
        return redirect()->route('sales-agent-commissions.edit', $sales_agent_commission);
    }

    public function edit(SalesAgentCommission $sales_agent_commission)
    {
        $sales_agent_commission->load(['vehicle', 'salesAgent']);
        $showroomNames = $this->showroomNamesForSelect($sales_agent_commission);

        return view('sales-agent-commissions.edit', compact('sales_agent_commission', 'showroomNames'));
    }

    public function update(Request $request, SalesAgentCommission $sales_agent_commission)
    {
        $validated = $request->validate([
            'showroom' => ['required', 'string', 'max:100', Rule::in($this->allowedCommissionShowrooms($sales_agent_commission))],
            'sales_agent_id' => 'nullable|exists:sales_agents,id',
            'agent_name' => 'required|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'unit' => 'nullable|string|max:255',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'plate_number' => 'nullable|string|max:50',
            'transaction_type' => 'required|in:CASH,FINANCING',
            'release_date' => 'nullable|date',
            'amount' => 'required|numeric|min:0',
            'agents_folder_amount' => 'nullable|numeric|min:0',
            'sales_executive_commission' => 'nullable|numeric|min:0',
            'proof_of_appointment' => 'nullable|boolean',
            'sign_client_with_agent' => 'nullable|boolean',
            'date_sent' => 'nullable|date',
            'date_of_payment' => 'nullable|date',
            'notes' => 'nullable|string|max:2000',
        ]);

        $validated['proof_of_appointment'] = $request->boolean('proof_of_appointment');
        $validated['sign_client_with_agent'] = $request->boolean('sign_client_with_agent');

        if (! empty($validated['sales_agent_id'])) {
            $agent = SalesAgent::find($validated['sales_agent_id']);
            if ($agent) {
                $validated['agent_name'] = $agent->name;
            }
        }

        $sales_agent_commission->update($validated);
        return redirect()->route('sales-agent-commissions.index')->with('success', 'Commission record updated.');
    }

    /**
     * @return list<string>
     */
    private function allowedCommissionShowrooms(?SalesAgentCommission $existing = null): array
    {
        $names = Showroom::query()->orderBy('name')->pluck('name')->all();
        if (! in_array('FLAGSHIP', $names, true)) {
            $names[] = 'FLAGSHIP';
        }
        $legacy = $existing?->showroom;
        if ($legacy && ! in_array($legacy, $names, true)) {
            $names[] = $legacy;
        }

        return $names;
    }

    /**
     * Active showrooms for the select; always includes FLAGSHIP; on edit, prepends a legacy value if missing.
     *
     * @return list<string>
     */
    private function showroomNamesForSelect(?SalesAgentCommission $existing = null): array
    {
        $names = Showroom::active()->orderBy('name')->pluck('name')->all();
        if (! in_array('FLAGSHIP', $names, true)) {
            $names = array_merge(['FLAGSHIP'], $names);
        }
        $legacy = $existing?->showroom;
        if ($legacy && ! in_array($legacy, $names, true)) {
            $names = array_merge([$legacy], $names);
        }

        return $names;
    }

    public function destroy(SalesAgentCommission $sales_agent_commission)
    {
        $sales_agent_commission->delete();
        return redirect()->route('sales-agent-commissions.index')->with('success', 'Commission record removed.');
    }
}
