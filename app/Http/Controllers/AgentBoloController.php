<?php

namespace App\Http\Controllers;

use App\Models\AgentBoloAgent;
use App\Models\CompanyDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AgentBoloController extends Controller
{
    public function index()
    {
        $agents = AgentBoloAgent::withCount('documents')->orderBy('name')->get();
        return view('agent-bolo.index', compact('agents'));
    }

    public function create()
    {
        return view('agent-bolo.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sales_executive' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'facebook_profile_link' => 'nullable|url|max:500',
            'facebook_page_link' => 'nullable|url|max:500',
            'signed_bolo' => 'nullable|string|max:500',
            'one_valid_id' => 'nullable|string|max:500',
            'joined_sales_associate_gc' => 'nullable|date',
            'notes' => 'nullable|string|max:2000',
        ]);

        AgentBoloAgent::create($request->only([
            'name', 'sales_executive', 'contact_number', 'email',
            'facebook_profile_link', 'facebook_page_link',
            'signed_bolo', 'one_valid_id', 'joined_sales_associate_gc', 'notes',
        ]));

        return redirect()->route('agent-bolo.index')->with('success', 'Agent added successfully.');
    }

    public function show(AgentBoloAgent $agent)
    {
        $agent->load('documents');
        return view('agent-bolo.show', compact('agent'));
    }

    public function edit(AgentBoloAgent $agent)
    {
        return view('agent-bolo.edit', compact('agent'));
    }

    public function update(Request $request, AgentBoloAgent $agent)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sales_executive' => 'nullable|string|max:255',
            'contact_number' => 'nullable|string|max:100',
            'email' => 'nullable|email|max:255',
            'facebook_profile_link' => 'nullable|url|max:500',
            'facebook_page_link' => 'nullable|url|max:500',
            'signed_bolo' => 'nullable|string|max:500',
            'one_valid_id' => 'nullable|string|max:500',
            'joined_sales_associate_gc' => 'nullable|date',
            'notes' => 'nullable|string|max:2000',
        ]);

        $agent->update($request->only([
            'name', 'sales_executive', 'contact_number', 'email',
            'facebook_profile_link', 'facebook_page_link',
            'signed_bolo', 'one_valid_id', 'joined_sales_associate_gc', 'notes',
        ]));

        return redirect()->route('agent-bolo.show', $agent)->with('success', 'Agent updated successfully.');
    }

    public function destroy(AgentBoloAgent $agent)
    {
        foreach ($agent->documents as $doc) {
            if ($doc->file_path) {
                Storage::disk('public')->delete($doc->file_path);
            }
        }
        $agent->delete();
        return redirect()->route('agent-bolo.index')->with('success', 'Agent removed.');
    }

    public function storeDocument(Request $request, AgentBoloAgent $agent)
    {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'file' => 'nullable|file|max:20480',
            'link_url' => 'nullable|url|max:1000',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('company-documents/agent_bolo', $name, 'public');
        }
        $linkUrl = $request->filled('link_url') ? $request->link_url : null;

        if (! $filePath && ! $linkUrl) {
            return redirect()->back()->withInput()->withErrors(['file' => 'Please upload a file or enter a link.']);
        }

        $maxOrder = $agent->documents()->max('sort_order') ?? -1;

        CompanyDocument::create([
            'type' => CompanyDocument::TYPE_AGENT_BOLO,
            'agent_bolo_agent_id' => $agent->id,
            'title' => $request->title,
            'file_path' => $filePath,
            'link_url' => $linkUrl,
            'sort_order' => $maxOrder + 1,
        ]);

        return redirect()->route('agent-bolo.show', $agent)->with('success', 'File/link added.');
    }

    public function destroyDocument(AgentBoloAgent $agent, CompanyDocument $document)
    {
        if ($document->agent_bolo_agent_id != $agent->id || $document->type !== CompanyDocument::TYPE_AGENT_BOLO) {
            abort(404);
        }
        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }
        $document->delete();
        return redirect()->route('agent-bolo.show', $agent)->with('success', 'File/link removed.');
    }
}
