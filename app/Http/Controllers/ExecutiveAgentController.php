<?php

namespace App\Http\Controllers;

use App\Models\ExecutiveAgent;
use App\Models\ExecutiveAgentLead;
use App\Support\ExecutiveAgentLeadsWorkbookParser;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ExecutiveAgentController extends Controller
{
    use LogsActivity;

    /**
     * Staff report: executive agents and their sales teams with earnings.
     */
    public function index()
    {
        $executives = ExecutiveAgent::query()
            ->active()
            ->with(['salesAgents' => function ($q) {
                $q->orderBy('name');
            }])
            ->withCount(['masterlistLeads', 'salesAgents'])
            ->orderBy('name')
            ->get();

        foreach ($executives as $executive) {
            $execOwn = 0.0;
            $team = 0.0;
            foreach ($executive->salesAgents as $agent) {
                $agent->staff_report_executive_commission = $agent->totalExecutiveCommissionShare();
                $agent->staff_report_sales_agent_earnings = $agent->totalCommissionEarnings();
                $execOwn += $agent->staff_report_executive_commission;
                $team += $agent->staff_report_sales_agent_earnings;
            }
            $executive->executive_own_earnings = $execOwn;
            $executive->team_total_earnings = $team;
            $executive->total_earnings = $execOwn + $team;
        }

        return view('staff-reports.executive-agents', compact('executives'));
    }

    /**
     * Create executive from staff report modal (quick-add).
     */
    public function store(Request $request)
    {
        if (! $request->boolean('from_executive_staff_report')) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $executive = new ExecutiveAgent([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'executive_code' => $this->generateNextExecutiveCode(),
            'department' => $request->input('department'),
            'status' => $request->input('status'),
            'notes' => $request->input('notes'),
        ]);
        $this->saveWithManualId($executive, ((int) ExecutiveAgent::query()->max('id')) + 1);

        $this->logCreate($executive);

        return redirect()->route('staff-reports.executive-agents')
            ->with('success', 'Executive agent created successfully (code: '.$executive->executive_code.').');
    }

    /**
     * Update executive account information from staff report list.
     */
    public function update(Request $request, ExecutiveAgent $executiveAgent)
    {
        if (! $request->boolean('from_executive_staff_report')) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'executive_code' => 'nullable|string|max:50',
            'department' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->route('staff-reports.executive-agents')
                ->withErrors($validator)
                ->withInput()
                ->with('edit_executive_id', $executiveAgent->id);
        }

        $code = trim((string) $request->input('executive_code', ''));
        if ($code !== '') {
            $codeTaken = ExecutiveAgent::query()
                ->where('executive_code', $code)
                ->where('id', '!=', $executiveAgent->id)
                ->exists();
            if ($codeTaken) {
                return redirect()->route('staff-reports.executive-agents')
                    ->withErrors(['executive_code' => 'This executive code is already in use.'])
                    ->withInput()
                    ->with('edit_executive_id', $executiveAgent->id);
            }
        }

        $before = $executiveAgent->only([
            'name', 'email', 'phone', 'executive_code', 'department', 'status', 'notes',
        ]);

        $executiveAgent->fill([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'executive_code' => $code !== '' ? $code : $executiveAgent->executive_code,
            'department' => $request->input('department'),
            'status' => $request->input('status'),
            'notes' => $request->input('notes'),
        ]);
        $executiveAgent->save();

        $this->logUpdate($executiveAgent, [
            'before' => $before,
            'after' => $executiveAgent->only(array_keys($before)),
        ]);

        return redirect()->route('staff-reports.executive-agents')
            ->with('success', 'Executive agent "'.$executiveAgent->name.'" updated successfully.');
    }

    /**
     * Executive agent account / profile.
     */
    public function show(ExecutiveAgent $executiveAgent)
    {
        $executiveAgent->load(['salesAgents' => function ($q) {
            $q->orderBy('name');
        }]);

        $clientLeadCount = $executiveAgent->clientLeads()->count();
        $masterlistLeadCount = $executiveAgent->masterlistLeads()->count();

        return view('staff-reports.executive-agent-show', [
            'executive' => $executiveAgent,
            'clientLeadCount' => $clientLeadCount,
            'masterlistLeadCount' => $masterlistLeadCount,
        ]);
    }

    /**
     * Leads Master List for one executive agent.
     */
    public function leads(Request $request, ExecutiveAgent $executiveAgent)
    {
        $search = trim((string) $request->get('q', ''));
        $query = $executiveAgent->masterlistLeads()
            ->orderByDesc('application_date')
            ->orderByDesc('id');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', '%'.$search.'%')
                    ->orWhere('customer_phone', 'like', '%'.$search.'%')
                    ->orWhere('unit_inquired', 'like', '%'.$search.'%')
                    ->orWhere('application_source', 'like', '%'.$search.'%')
                    ->orWhere('about_what', 'like', '%'.$search.'%')
                    ->orWhere('notes', 'like', '%'.$search.'%');
            });
        }

        $leads = $query->paginate(25)->withQueryString();

        return view('staff-reports.executive-agent-leads', [
            'executive' => $executiveAgent,
            'leads' => $leads,
            'search' => $search,
        ]);
    }

    /**
     * Manually add one lead to an executive's master list.
     */
    public function storeLead(Request $request, ExecutiveAgent $executiveAgent)
    {
        $validator = Validator::make($request->all(), $this->leadValidationRules());

        if ($validator->fails()) {
            return redirect()
                ->route('staff-reports.executive-agents.leads', $executiveAgent)
                ->withErrors($validator)
                ->withInput()
                ->with('open_add_lead_modal', true);
        }

        $data = $validator->validated();
        $data['executive_agent_id'] = $executiveAgent->id;
        $data['source_sheet'] = 'Manual entry';
        $data['source_row'] = null;

        $lead = new ExecutiveAgentLead($data);
        $this->saveWithManualId($lead, ((int) ExecutiveAgentLead::query()->max('id')) + 1);

        $this->logCreate($lead, 'Added lead "'.$lead->customer_name.'" for executive '.$executiveAgent->name);

        return redirect()
            ->route('staff-reports.executive-agents.leads', $executiveAgent)
            ->with('success', 'Lead "'.$lead->customer_name.'" added successfully.');
    }

    /**
     * Update one lead on an executive's master list.
     */
    public function updateLead(Request $request, ExecutiveAgent $executiveAgent, ExecutiveAgentLead $lead)
    {
        if ((int) $lead->executive_agent_id !== (int) $executiveAgent->id) {
            abort(404);
        }

        $validator = Validator::make($request->all(), $this->leadValidationRules());

        if ($validator->fails()) {
            return redirect()
                ->route('staff-reports.executive-agents.leads', $executiveAgent)
                ->withErrors($validator)
                ->withInput()
                ->with('open_edit_lead_modal', true)
                ->with('edit_lead_id', $lead->id);
        }

        $before = $lead->only(array_keys($this->leadValidationRules()));
        $lead->fill($validator->validated());
        $lead->save();

        $this->logUpdate($lead, [
            'before' => $before,
            'after' => $lead->only(array_keys($before)),
        ], 'Updated lead "'.$lead->customer_name.'" for executive '.$executiveAgent->name);

        return redirect()
            ->route('staff-reports.executive-agents.leads', $executiveAgent)
            ->with('success', 'Lead "'.$lead->customer_name.'" updated successfully.');
    }

    /**
     * Delete one lead from an executive's master list.
     */
    public function destroyLead(ExecutiveAgent $executiveAgent, ExecutiveAgentLead $lead)
    {
        if ((int) $lead->executive_agent_id !== (int) $executiveAgent->id) {
            abort(404);
        }

        $name = $lead->customer_name;
        $this->logDelete($lead, 'Deleted lead "'.$name.'" for executive '.$executiveAgent->name);
        $lead->delete();

        return redirect()
            ->route('staff-reports.executive-agents.leads', $executiveAgent)
            ->with('success', 'Lead "'.$name.'" deleted successfully.');
    }

    /**
     * Import Leads Master List Excel for a selected executive (skip existing).
     */
    public function importLeads(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'executive_agent_id' => 'required|exists:executive_agents,id',
            'file' => 'required|file|mimes:xlsx,xls|max:51200',
        ]);

        if ($validator->fails()) {
            return redirect()->route('staff-reports.executive-agents')
                ->withErrors($validator)
                ->withInput();
        }

        $executive = ExecutiveAgent::findOrFail((int) $request->input('executive_agent_id'));
        $uploaded = $request->file('file');
        $tmpPath = $uploaded->getRealPath() ?: $uploaded->getPathname();

        $parser = new ExecutiveAgentLeadsWorkbookParser();
        try {
            $rows = $parser->parse($tmpPath);
        } catch (\Throwable $e) {
            return redirect()->route('staff-reports.executive-agents')
                ->with('error', 'Could not read Excel: '.$e->getMessage());
        }

        if ($rows === []) {
            return redirect()->route('staff-reports.executive-agents')
                ->with('error', 'No lead rows found in the workbook.');
        }

        $existingKeys = [];
        foreach (
            ExecutiveAgentLead::query()
                ->where('executive_agent_id', $executive->id)
                ->get(['application_date', 'customer_name', 'customer_phone']) as $lead
        ) {
            $existingKeys[$this->leadKey(
                optional($lead->application_date)->format('Y-m-d'),
                $lead->customer_name,
                $lead->customer_phone
            )] = true;
        }

        $created = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($rows as $row) {
            try {
                $key = $this->leadKey(
                    $row['application_date'] ?? null,
                    $row['customer_name'] ?? '',
                    $row['customer_phone'] ?? ''
                );
                if (isset($existingKeys[$key])) {
                    $skipped++;
                    continue;
                }

                DB::transaction(function () use ($executive, $row, &$created, &$existingKeys, $key) {
                    $lead = new ExecutiveAgentLead(array_merge($row, [
                        'executive_agent_id' => $executive->id,
                    ]));
                    $this->saveWithManualId($lead, ((int) ExecutiveAgentLead::query()->max('id')) + 1);
                    $existingKeys[$key] = true;
                    $created++;
                });
            } catch (\Throwable $e) {
                $errors++;
            }
        }

        $msg = "Imported leads for {$executive->name}: {$created} created, {$skipped} skipped (already exist)";
        if ($errors > 0) {
            $msg .= ", {$errors} errors";
        }

        return redirect()
            ->route('staff-reports.executive-agents.leads', $executive)
            ->with('success', $msg);
    }

    /**
     * Next EA### code from existing executive_code values matching that pattern.
     */
    private function generateNextExecutiveCode(): string
    {
        $prefix = 'EA';
        $max = 0;
        foreach (ExecutiveAgent::query()->pluck('executive_code') as $code) {
            if (preg_match('/^'.preg_quote($prefix, '/').'(\d+)$/i', (string) $code, $m)) {
                $max = max($max, (int) $m[1]);
            }
        }

        return $prefix.str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
    }

    private function leadKey(?string $date, ?string $name, ?string $phone): string
    {
        $normPhone = strtoupper(preg_replace('/\D+/', '', (string) $phone) ?? '');
        $normName = strtoupper(preg_replace('/\s+/', ' ', trim((string) $name)) ?? '');

        return ($date ?: '').'|'.$normName.'|'.$normPhone;
    }

    /**
     * @return array<string, string>
     */
    private function leadValidationRules(): array
    {
        return [
            'application_date' => 'nullable|date',
            'application_source' => 'nullable|string|max:100',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'nullable|string|max:100',
            'unit_inquired' => 'nullable|string|max:255',
            'about_what' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'followup_1_exec' => 'nullable|string|max:100',
            'followup_1_date' => 'nullable|date',
            'followup_1_outcome' => 'nullable|string|max:255',
            'followup_1_notes' => 'nullable|string',
            'followup_2_exec' => 'nullable|string|max:100',
            'followup_2_date' => 'nullable|date',
            'followup_2_outcome' => 'nullable|string|max:255',
            'followup_2_notes' => 'nullable|string',
            'followup_3_exec' => 'nullable|string|max:100',
            'followup_3_date' => 'nullable|date',
            'followup_3_outcome' => 'nullable|string|max:255',
            'followup_3_notes' => 'nullable|string',
            'followup_4_exec' => 'nullable|string|max:100',
            'followup_4_date' => 'nullable|date',
            'followup_4_outcome' => 'nullable|string|max:255',
            'followup_4_notes' => 'nullable|string',
        ];
    }

    private function saveWithManualId(\Illuminate\Database\Eloquent\Model $model, int $id): void
    {
        $model->id = $id;
        $model->incrementing = false;
        $model->save();
    }
}
