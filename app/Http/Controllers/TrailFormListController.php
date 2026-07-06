<?php

namespace App\Http\Controllers;

use App\Models\TrailFormClient;
use Illuminate\Http\Request;

class TrailFormListController extends Controller
{
    public function index(Request $request)
    {
        $query = TrailFormClient::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('inquiry_source')) {
            $query->where('inquiry_source', $request->inquiry_source);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('client_name', 'LIKE', "%{$search}%")
                    ->orWhere('contact_number', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('vehicle_interest', 'LIKE', "%{$search}%")
                    ->orWhere('notes', 'LIKE', "%{$search}%");
            });
        }

        $clients = $query->orderByDesc('inquiry_date')
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('trail-form-list.index', compact('clients'));
    }

    public function create()
    {
        return view('trail-form-list.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validateClient($request);
        TrailFormClient::create($validated);

        return redirect()->route('trail-form-list.index')->with('success', 'Client added to trail form list.');
    }

    public function show(TrailFormClient $trail_form_list)
    {
        return redirect()->route('trail-form-list.edit', $trail_form_list);
    }

    public function edit(TrailFormClient $trail_form_list)
    {
        return view('trail-form-list.edit', ['client' => $trail_form_list]);
    }

    public function update(Request $request, TrailFormClient $trail_form_list)
    {
        $trail_form_list->update($this->validateClient($request));

        return redirect()->route('trail-form-list.index')->with('success', 'Client record updated.');
    }

    public function destroy(TrailFormClient $trail_form_list)
    {
        $trail_form_list->delete();

        return redirect()->route('trail-form-list.index')->with('success', 'Client removed from trail form list.');
    }

    private function validateClient(Request $request): array
    {
        return $request->validate([
            'inquiry_date' => 'nullable|date',
            'client_name' => 'required|string|max:255',
            'contact_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'status' => 'required|in:' . implode(',', TrailFormClient::statusOptions()),
            'inquiry_source' => 'nullable|string|max:100',
            'vehicle_type' => 'nullable|string|max:100',
            'vehicle_interest' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
        ]);
    }
}
