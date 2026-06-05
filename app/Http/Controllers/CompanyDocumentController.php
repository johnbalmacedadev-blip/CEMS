<?php

namespace App\Http\Controllers;

use App\Models\CompanyDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyDocumentController extends Controller
{
    protected function validateType(string $type): void
    {
        $valid = [
            CompanyDocument::TYPE_ONLINE_AR_BOLO,
            CompanyDocument::TYPE_AGENT_BOLO,
            CompanyDocument::TYPE_AR_TEMPLATE,
        ];
        if (! in_array($type, $valid, true)) {
            abort(404);
        }
    }

    /**
     * List documents for a type (online_ar_bolo, agent_bolo, ar_template).
     */
    public function index(Request $request)
    {
        $type = $request->route('document_type');
        $this->validateType($type);

        $documents = CompanyDocument::where('type', $type)
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();

        $typeLabel = CompanyDocument::typeLabels()[$type] ?? $type;
        $routePrefix = $this->routePrefixForType($type);

        return view('company-documents.index', compact('documents', 'type', 'typeLabel', 'routePrefix'));
    }

    /**
     * Show create form.
     */
    public function create(Request $request)
    {
        $type = $request->route('document_type');
        $this->validateType($type);

        $typeLabel = CompanyDocument::typeLabels()[$type] ?? $type;
        $routePrefix = $this->routePrefixForType($type);

        return view('company-documents.create', compact('type', 'typeLabel', 'routePrefix'));
    }

    /**
     * Store new document (file or link).
     */
    public function store(Request $request)
    {
        $type = $request->route('document_type');
        $this->validateType($type);

        $request->validate([
            'title' => 'nullable|string|max:255',
            'file' => 'nullable|file|max:20480',
            'link_url' => 'nullable|url|max:1000',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('company-documents/' . $type, $name, 'public');
        }

        $linkUrl = $request->filled('link_url') ? $request->link_url : null;

        if (! $filePath && ! $linkUrl) {
            return redirect()->back()->withInput()->withErrors(['file' => 'Please upload a file or enter a link.']);
        }

        $maxOrder = CompanyDocument::where('type', $type)->max('sort_order') ?? -1;

        CompanyDocument::create([
            'type' => $type,
            'title' => $request->title,
            'file_path' => $filePath,
            'link_url' => $linkUrl,
            'sort_order' => $maxOrder + 1,
        ]);

        $routeName = $this->routePrefixForType($type) . '.index';

        return redirect()->route($routeName)->with('success', 'Document added successfully.');
    }

    /**
     * Show edit form.
     */
    public function edit(Request $request, CompanyDocument $document)
    {
        $type = $request->route('document_type');
        $this->validateType($type);

        if ($document->type !== $type) {
            abort(404);
        }

        $typeLabel = CompanyDocument::typeLabels()[$type] ?? $type;
        $routePrefix = $this->routePrefixForType($type);

        return view('company-documents.edit', compact('document', 'type', 'typeLabel', 'routePrefix'));
    }

    /**
     * Update document.
     */
    public function update(Request $request, CompanyDocument $document)
    {
        $type = $request->route('document_type');
        $this->validateType($type);

        if ($document->type !== $type) {
            abort(404);
        }

        $request->validate([
            'title' => 'nullable|string|max:255',
            'file' => 'nullable|file|max:20480',
            'link_url' => 'nullable|url|max:1000',
        ]);

        $data = ['title' => $request->title];

        if ($request->hasFile('file')) {
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }
            $file = $request->file('file');
            $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $data['file_path'] = $file->storeAs('company-documents/' . $type, $name, 'public');
            $data['link_url'] = null;
        } elseif ($request->filled('link_url')) {
            if ($document->file_path) {
                Storage::disk('public')->delete($document->file_path);
            }
            $data['file_path'] = null;
            $data['link_url'] = $request->link_url;
        }

        $document->update($data);

        $routeName = $this->routePrefixForType($type) . '.index';

        return redirect()->route($routeName)->with('success', 'Document updated successfully.');
    }

    /**
     * Delete document.
     */
    public function destroy(Request $request, CompanyDocument $document)
    {
        $type = $request->route('document_type');
        $this->validateType($type);

        if ($document->type !== $type) {
            abort(404);
        }

        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }
        $document->delete();

        $routeName = $this->routePrefixForType($type) . '.index';

        return redirect()->route($routeName)->with('success', 'Document removed.');
    }

    protected function routePrefixForType(string $type): string
    {
        $map = [
            CompanyDocument::TYPE_ONLINE_AR_BOLO => 'online-ar-bolo',
            CompanyDocument::TYPE_AGENT_BOLO => 'agent-bolo',
            CompanyDocument::TYPE_AR_TEMPLATE => 'ar-template',
        ];
        return $map[$type] ?? $type;
    }
}
