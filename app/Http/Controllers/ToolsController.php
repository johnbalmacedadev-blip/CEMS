<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\LogsActivity;

class ToolsController extends Controller
{
    use LogsActivity;
    /**
     * Display a listing of tools grouped by date
     */
    public function index()
    {
        $tools = Tool::orderBy('date_acquired', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Group tools by date_acquired
        $groupedTools = $tools->groupBy(function($tool) {
            return $tool->date_acquired->format('Y-m-d');
        });

        // Calculate totals for each date
        $dateTotals = [];
        foreach ($groupedTools as $date => $dateTools) {
            $dateTotals[$date] = $dateTools->sum('amount');
        }

        return view('operations-tracker', compact('groupedTools', 'dateTotals'));
    }

    /**
     * Store a newly created tool
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'date_acquired' => 'required|date',
        ]);

        $tool = Tool::create($request->all());

        // Log activity
        $this->logCreate($tool);

        return response()->json([
            'success' => true,
            'message' => 'Tool added successfully!'
        ]);
    }

    /**
     * Update the specified tool
     */
    public function update(Request $request, Tool $tool)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'amount' => 'required|numeric|min:0',
            'date_acquired' => 'required|date',
        ]);

        // Get original values for logging
        $original = $tool->getOriginal();
        $tool->update($request->all());

        // Track changes for logging
        $changes = [];
        foreach ($request->all() as $key => $value) {
            if (isset($original[$key]) && $original[$key] != $value) {
                $changes[$key] = [
                    'old' => $original[$key],
                    'new' => $value
                ];
            }
        }

        // Log activity
        $this->logUpdate($tool, !empty($changes) ? $changes : null);

        return response()->json([
            'success' => true,
            'message' => 'Tool updated successfully!'
        ]);
    }

    /**
     * Display the specified tool
     */
    public function show(Tool $tool)
    {
        return response()->json([
            'success' => true,
            'tool' => [
                'id' => $tool->id,
                'name' => $tool->name,
                'quantity' => $tool->quantity,
                'amount' => $tool->amount,
                'date_acquired' => $tool->date_acquired->format('Y-m-d'),
            ]
        ]);
    }

    /**
     * Remove the specified tool
     */
    public function destroy(Tool $tool)
    {
        // Log activity before deleting
        $this->logDelete($tool);

        $tool->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tool deleted successfully!'
        ]);
    }

    /**
     * Get purchase history for a specific tool name
     */
    public function history(Request $request)
    {
        $name = $request->get('name');
        
        if (!$name) {
            return response()->json([
                'success' => false,
                'message' => 'Tool name is required'
            ], 400);
        }

        $tools = Tool::where('name', $name)
            ->orderBy('date_acquired', 'desc')
            ->orderBy('created_at', 'desc')
            ->get(['id', 'name', 'quantity', 'amount', 'date_acquired', 'created_at']);

        return response()->json([
            'success' => true,
            'tools' => $tools
        ]);
    }

    /**
     * Search for tool names (autocomplete)
     */
    public function search(Request $request)
    {
        $query = trim($request->get('q', ''));
        
        // If empty query, return all unique tool names
        if (strlen($query) < 1) {
            $tools = Tool::select('name')
                ->distinct()
                ->orderBy('name', 'asc')
                ->get()
                ->map(function($tool) {
                    return ['name' => $tool->name];
                })
                ->values();
            
            return response()->json($tools);
        }

        $tools = Tool::select('name')
            ->whereRaw('UPPER(name) LIKE ?', ['%' . strtoupper($query) . '%'])
            ->distinct()
            ->orderBy('name', 'asc')
            ->limit(20)
            ->get()
            ->map(function($tool) {
                return ['name' => $tool->name];
            })
            ->values();

        return response()->json($tools);
    }
}