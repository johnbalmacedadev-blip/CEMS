<?php

namespace App\Http\Controllers;

use App\Models\SalesAgent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Traits\LogsActivity;

class SalesAgentController extends Controller
{
    use LogsActivity;
    /**
     * Display a listing of the resource.
     */
    /**
     * Staff report: sales agents with executive and total commission earnings.
     */
    public function staffReport(Request $request)
    {
        $period = $request->input('period', 'all');
        if (! in_array($period, ['all', 'day', 'week', 'month', 'year'], true)) {
            $period = 'all';
        }

        $sort = $request->input('sort', 'earnings_desc');
        if (! in_array($sort, ['earnings_desc', 'earnings_asc', 'name_asc', 'name_desc'], true)) {
            $sort = 'earnings_desc';
        }

        $anchor = $request->filled('anchor')
            ? Carbon::parse($request->string('anchor'))->startOfDay()
            : Carbon::now()->startOfDay();

        $rangeStart = null;
        $rangeEnd = null;
        $periodLabel = 'All time';

        switch ($period) {
            case 'day':
                $rangeStart = $anchor->copy()->startOfDay();
                $rangeEnd = $anchor->copy()->endOfDay();
                $periodLabel = $anchor->format('M j, Y');
                break;
            case 'week':
                $rangeStart = $anchor->copy()->startOfWeek(Carbon::MONDAY)->startOfDay();
                $rangeEnd = $anchor->copy()->endOfWeek(Carbon::SUNDAY)->endOfDay();
                $periodLabel = $rangeStart->format('M j') . ' – ' . $rangeEnd->format('M j, Y');
                break;
            case 'month':
                $rangeStart = $anchor->copy()->startOfMonth()->startOfDay();
                $rangeEnd = $anchor->copy()->endOfMonth()->endOfDay();
                $periodLabel = $anchor->format('F Y');
                break;
            case 'year':
                $rangeStart = $anchor->copy()->startOfYear()->startOfDay();
                $rangeEnd = $anchor->copy()->endOfYear()->endOfDay();
                $periodLabel = $anchor->format('Y');
                break;
            default:
                break;
        }

        $agents = SalesAgent::query()
            ->with('executiveAgent')
            ->active()
            ->orderBy('name')
            ->get();

        foreach ($agents as $agent) {
            $agent->total_commission_earnings = $agent->totalCommissionEarnings($rangeStart, $rangeEnd);
        }

        $agents = match ($sort) {
            'earnings_asc' => $agents->sortBy(fn (SalesAgent $a) => $a->total_commission_earnings)->values(),
            'name_asc' => $agents->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values(),
            'name_desc' => $agents->sortByDesc('name', SORT_NATURAL | SORT_FLAG_CASE)->values(),
            default => $agents->sortByDesc(fn (SalesAgent $a) => $a->total_commission_earnings)->values(),
        };

        $referencedExecIds = $agents->pluck('executive_agent_id')->filter()->unique()->values();

        $executives = \App\Models\ExecutiveAgent::query()
            ->where(function ($q) use ($referencedExecIds) {
                $q->where('status', 'active');
                if ($referencedExecIds->isNotEmpty()) {
                    $q->orWhereIn('id', $referencedExecIds);
                }
            })
            ->orderBy('name')
            ->get();

        $agentsJson = $agents->map(function (SalesAgent $a) {
            return [
                'id' => $a->id,
                'name' => $a->name,
                'phone' => $a->phone,
                'sales_agent_id' => $a->sales_agent_id,
                'executive_agent_id' => $a->executive_agent_id,
                'department' => $a->department,
                'position' => $a->position,
                'hire_date' => $a->hire_date?->format('Y-m-d'),
                'status' => $a->status,
                'commission_type' => $a->commission_type ?: SalesAgent::COMMISSION_PERCENTAGE,
                'commission_rate' => $a->commission_rate !== null ? (float) $a->commission_rate : null,
                'commission_fixed_amount' => $a->commission_fixed_amount !== null ? (float) $a->commission_fixed_amount : null,
                'address' => $a->address,
                'emergency_contact_name' => $a->emergency_contact_name,
                'emergency_contact_phone' => $a->emergency_contact_phone,
                'notes' => $a->notes,
            ];
        })->values();

        return view('staff-reports.sales-agents', compact(
            'agents',
            'executives',
            'agentsJson',
            'period',
            'sort',
            'anchor',
            'periodLabel'
        ));
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');
        
        $query = SalesAgent::query()->with('executiveAgent');
        
        // Filter by status if specified
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        // Add search functionality
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('sales_agent_id', 'LIKE', "%{$search}%")
                  ->orWhere('phone', 'LIKE', "%{$search}%")
                  ->orWhere('department', 'LIKE', "%{$search}%")
                  ->orWhere('position', 'LIKE', "%{$search}%");
            });
        }
        
        $agents = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Get counts for each status
        $activeCount = SalesAgent::where('status', 'active')->count();
        $inactiveCount = SalesAgent::where('status', 'inactive')->count();
        
        return view('sales-agents.index', compact('agents', 'status', 'search', 'activeCount', 'inactiveCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $executives = \App\Models\ExecutiveAgent::active()->orderBy('name')->get();

        return view('sales-agents.create', compact('executives'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $fromStaffReport = $request->boolean('from_staff_report');

        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'commission_fixed_amount' => 'nullable|numeric|min:0',
            'base_salary' => 'nullable|numeric|min:0',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
            'executive_agent_id' => 'nullable|exists:executive_agents,id',
        ];

        if ($fromStaffReport) {
            $rules['email'] = 'nullable';
            $rules['sales_agent_id'] = 'nullable';
            $rules['commission_type'] = 'required|in:percentage,fixed_rate,custom';
            $rules['commission_rate'] = [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
                Rule::requiredIf(fn () => $request->input('commission_type') === SalesAgent::COMMISSION_PERCENTAGE),
            ];
            $rules['commission_fixed_amount'] = [
                'nullable',
                'numeric',
                'min:0',
                Rule::requiredIf(fn () => $request->input('commission_type') === SalesAgent::COMMISSION_FIXED_RATE),
            ];
        } else {
            $rules['email'] = 'required|email|unique:sales_agents,email';
            $rules['sales_agent_id'] = 'required|string|unique:sales_agents,sales_agent_id';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->only([
            'name', 'email', 'phone', 'sales_agent_id', 'executive_agent_id', 'department', 'position',
            'hire_date', 'commission_rate', 'commission_type', 'commission_fixed_amount', 'base_salary', 'address', 'emergency_contact_name',
            'emergency_contact_phone', 'status', 'notes',
        ]);

        if ($fromStaffReport) {
            $data['sales_agent_id'] = $this->generateNextSalesAgentCode();
            $data['email'] = $this->generateUniquePlaceholderEmail($data['name']);
            $data['position'] = trim((string) ($data['position'] ?? '')) !== ''
                ? $data['position']
                : 'Sales Agent';
            $data['base_salary'] = null;

            $this->normalizeStaffReportCommissionFromRequest($request, $data);
        }

        $salesAgent = SalesAgent::create($data);

        // Log activity
        $this->logCreate($salesAgent);

        if ($fromStaffReport) {
            return redirect()->route('staff-reports.sales-agents')
                ->with('success', 'Sales agent created successfully (ID: ' . $salesAgent->sales_agent_id . ').');
        }

        return redirect()->route('sales-agents.index')
            ->with('success', 'Sales agent created successfully!');
    }

    /**
     * Next SA### code from existing sales_agent_id values (staff modal quick-add).
     */
    private function generateNextSalesAgentCode(): string
    {
        $prefix = 'SA';
        $max = 0;
        foreach (SalesAgent::query()->pluck('sales_agent_id') as $code) {
            if (preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/i', (string) $code, $m)) {
                $max = max($max, (int) $m[1]);
            }
        }

        return $prefix . str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
    }

    /**
     * Internal-only email so the staff modal can omit the email field (column is required + unique).
     */
    private function generateUniquePlaceholderEmail(string $name): string
    {
        $slug = Str::slug(Str::limit($name, 48, ''), '.');
        if ($slug === '') {
            $slug = 'agent';
        }

        do {
            $email = $slug . '.' . Str::lower(Str::random(10)) . '@noreply.carempire.local';
        } while (SalesAgent::where('email', $email)->exists());

        return $email;
    }

    /**
     * Display the specified resource.
     */
    public function show(SalesAgent $salesAgent)
    {
        return view('sales-agents.show', compact('salesAgent'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalesAgent $salesAgent)
    {
        $executives = \App\Models\ExecutiveAgent::active()->orderBy('name')->get();

        return view('sales-agents.edit', compact('salesAgent', 'executives'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SalesAgent $salesAgent)
    {
        $fromStaff = $request->boolean('from_staff_report');

        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'sales_agent_id' => 'required|string|unique:sales_agents,sales_agent_id,' . $salesAgent->id,
            'department' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
            'executive_agent_id' => 'nullable|exists:executive_agents,id',
        ];

        if ($fromStaff) {
            $rules['commission_type'] = 'required|in:percentage,fixed_rate,custom';
            $rules['commission_rate'] = [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
                Rule::requiredIf(fn () => $request->input('commission_type') === SalesAgent::COMMISSION_PERCENTAGE),
            ];
            $rules['commission_fixed_amount'] = [
                'nullable',
                'numeric',
                'min:0',
                Rule::requiredIf(fn () => $request->input('commission_type') === SalesAgent::COMMISSION_FIXED_RATE),
            ];
        } else {
            $rules['email'] = 'required|email|unique:sales_agents,email,' . $salesAgent->id;
            $rules['commission_rate'] = 'nullable|numeric|min:0|max:100';
            $rules['base_salary'] = 'nullable|numeric|min:0';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        if ($fromStaff) {
            $data = $request->only([
                'name', 'phone', 'sales_agent_id', 'executive_agent_id', 'department', 'position',
                'hire_date', 'commission_rate', 'commission_type', 'commission_fixed_amount',
                'address', 'emergency_contact_name', 'emergency_contact_phone', 'status', 'notes',
            ]);
            $this->normalizeStaffReportCommissionFromRequest($request, $data);
            if (trim((string) ($data['position'] ?? '')) === '') {
                $data['position'] = 'Sales Agent';
            }
        } else {
            $data = $request->only([
                'name', 'email', 'phone', 'sales_agent_id', 'executive_agent_id', 'department', 'position',
                'hire_date', 'commission_rate', 'base_salary', 'address', 'emergency_contact_name',
                'emergency_contact_phone', 'status', 'notes',
            ]);
        }

        $original = $salesAgent->getOriginal();
        $salesAgent->update($data);

        $changes = [];
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $original) && $original[$key] != $value) {
                $changes[$key] = [
                    'old' => $original[$key],
                    'new' => $value,
                ];
            }
        }

        $this->logUpdate($salesAgent, !empty($changes) ? $changes : null);

        if ($fromStaff) {
            return redirect()->route('staff-reports.sales-agents')
                ->with('success', 'Sales agent updated successfully.');
        }

        return redirect()->route('sales-agents.index')
            ->with('success', 'Sales agent updated successfully!');
    }

    /**
     * Normalize commission_type / rate / fixed amount from staff report modal (add or edit).
     */
    private function normalizeStaffReportCommissionFromRequest(Request $request, array &$data): void
    {
        $type = $request->string('commission_type')->toString();
        $data['commission_type'] = $type;
        if ($type === SalesAgent::COMMISSION_PERCENTAGE) {
            $data['commission_rate'] = $request->input('commission_rate');
            $data['commission_fixed_amount'] = null;
        } elseif ($type === SalesAgent::COMMISSION_FIXED_RATE) {
            $data['commission_rate'] = 0;
            $data['commission_fixed_amount'] = $request->input('commission_fixed_amount');
        } else {
            $data['commission_rate'] = 0;
            $data['commission_fixed_amount'] = null;
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesAgent $salesAgent)
    {
        // Log activity before deleting
        $this->logDelete($salesAgent);
        
        $salesAgent->delete();

        return redirect()->route('sales-agents.index')
            ->with('success', 'Sales agent deleted successfully!');
    }
}