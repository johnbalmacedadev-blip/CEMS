<?php

namespace App\Http\Controllers;

use App\Models\ExecutiveAgent;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
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

        $executive = ExecutiveAgent::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'executive_code' => $this->generateNextExecutiveCode(),
            'department' => $request->input('department'),
            'status' => $request->input('status'),
            'notes' => $request->input('notes'),
        ]);

        $this->logCreate($executive);

        return redirect()->route('staff-reports.executive-agents')
            ->with('success', 'Executive agent created successfully (code: ' . $executive->executive_code . ').');
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

        return view('staff-reports.executive-agent-show', [
            'executive' => $executiveAgent,
            'clientLeadCount' => $clientLeadCount,
        ]);
    }

    /**
     * Next EA### code from existing executive_code values matching that pattern.
     */
    private function generateNextExecutiveCode(): string
    {
        $prefix = 'EA';
        $max = 0;
        foreach (ExecutiveAgent::query()->pluck('executive_code') as $code) {
            if (preg_match('/^' . preg_quote($prefix, '/') . '(\d+)$/i', (string) $code, $m)) {
                $max = max($max, (int) $m[1]);
            }
        }

        return $prefix . str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
    }
}
