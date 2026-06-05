<?php

namespace App\Http\Controllers;

use App\Models\FollowUpDocument;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class FollowUpDocumentsController extends Controller
{
    /**
     * Display the follow up documents list.
     */
    public function index(Request $request)
    {
        $query = FollowUpDocument::with('vehicle');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('date_from')) {
            $query->where('due_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('due_date', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('notes', 'LIKE', "%{$search}%");
            });
        }

        $documents = $query->orderByRaw('CASE WHEN due_date IS NULL THEN 1 ELSE 0 END')
            ->orderBy('due_date')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('follow-up-documents.index', compact('documents'));
    }

    /**
     * Show the form for creating a new follow up document.
     */
    public function create()
    {
        $vehicles = Vehicle::with(['make', 'vehicleModel'])->orderBy('created_at', 'desc')->get();
        return view('follow-up-documents.create', compact('vehicles'));
    }

    /**
     * Store a newly created follow up document.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'due_date' => 'nullable|date',
            'status' => 'required|in:Pending,In Progress,Completed',
            'priority' => 'nullable|in:Low,Medium,High',
            'notes' => 'nullable|string|max:2000',
        ]);

        FollowUpDocument::create($validated);

        return redirect()->route('follow-up-documents.index')->with('success', 'Follow up document added successfully.');
    }

    /**
     * Display the specified follow up document (redirect to edit).
     */
    public function show(FollowUpDocument $follow_up_document)
    {
        return redirect()->route('follow-up-documents.edit', $follow_up_document);
    }

    /**
     * Show the form for editing the specified follow up document.
     */
    public function edit(FollowUpDocument $follow_up_document)
    {
        $vehicles = Vehicle::with(['make', 'vehicleModel'])->orderBy('created_at', 'desc')->get();
        return view('follow-up-documents.edit', compact('follow_up_document', 'vehicles'));
    }

    /**
     * Update the specified follow up document.
     */
    public function update(Request $request, FollowUpDocument $follow_up_document)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'due_date' => 'nullable|date',
            'status' => 'required|in:Pending,In Progress,Completed',
            'priority' => 'nullable|in:Low,Medium,High',
            'notes' => 'nullable|string|max:2000',
        ]);

        $follow_up_document->update($validated);

        return redirect()->route('follow-up-documents.index')->with('success', 'Follow up document updated successfully.');
    }

    /**
     * Remove the specified follow up document.
     */
    public function destroy(FollowUpDocument $follow_up_document)
    {
        $follow_up_document->delete();
        return redirect()->route('follow-up-documents.index')->with('success', 'Follow up document deleted successfully.');
    }
}
