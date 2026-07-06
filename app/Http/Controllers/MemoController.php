<?php

namespace App\Http\Controllers;

use App\Models\CompanyDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MemoController extends Controller
{
    public function index()
    {
        $memos = CompanyDocument::where('type', CompanyDocument::TYPE_MEMO)
            ->orderByDesc('created_at')
            ->get();

        return view('memos.index', compact('memos'));
    }

    public function create()
    {
        return view('memos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'body' => 'nullable|string|max:10000',
            'file' => 'nullable|file|max:20480',
            'link_url' => 'nullable|url|max:1000',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('company-documents/memos', $name, 'public');
        }

        $body = $request->filled('body') ? trim($request->body) : null;
        $linkUrl = $request->filled('link_url') ? $request->link_url : null;

        if (! $body && ! $filePath && ! $linkUrl) {
            return redirect()->back()->withInput()->withErrors([
                'body' => 'Enter memo text, upload a file, or add a link.',
            ]);
        }

        CompanyDocument::create([
            'type' => CompanyDocument::TYPE_MEMO,
            'title' => $request->title,
            'body' => $body,
            'file_path' => $filePath,
            'link_url' => $linkUrl,
            'sort_order' => 0,
        ]);

        return redirect()->route('memos.index')->with('success', 'Memo added successfully.');
    }

    public function edit(CompanyDocument $memo)
    {
        if ($memo->type !== CompanyDocument::TYPE_MEMO) {
            abort(404);
        }

        return view('memos.edit', compact('memo'));
    }

    public function update(Request $request, CompanyDocument $memo)
    {
        if ($memo->type !== CompanyDocument::TYPE_MEMO) {
            abort(404);
        }

        $request->validate([
            'title' => 'nullable|string|max:255',
            'body' => 'nullable|string|max:10000',
            'file' => 'nullable|file|max:20480',
            'link_url' => 'nullable|url|max:1000',
        ]);

        $data = [
            'title' => $request->title,
            'body' => $request->filled('body') ? trim($request->body) : null,
        ];

        if ($request->hasFile('file')) {
            if ($memo->file_path) {
                Storage::disk('public')->delete($memo->file_path);
            }
            $file = $request->file('file');
            $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . Str::random(6) . '.' . $file->getClientOriginalExtension();
            $data['file_path'] = $file->storeAs('company-documents/memos', $name, 'public');
            $data['link_url'] = null;
        } elseif ($request->filled('link_url')) {
            if ($memo->file_path) {
                Storage::disk('public')->delete($memo->file_path);
            }
            $data['file_path'] = null;
            $data['link_url'] = $request->link_url;
        }

        if (! $data['body'] && empty($data['file_path'] ?? $memo->file_path) && empty($data['link_url'] ?? $memo->link_url)) {
            return redirect()->back()->withInput()->withErrors([
                'body' => 'Memo must have text, a file, or a link.',
            ]);
        }

        $memo->update($data);

        return redirect()->route('memos.index')->with('success', 'Memo updated successfully.');
    }

    public function destroy(CompanyDocument $memo)
    {
        if ($memo->type !== CompanyDocument::TYPE_MEMO) {
            abort(404);
        }

        if ($memo->file_path) {
            Storage::disk('public')->delete($memo->file_path);
        }

        $memo->delete();

        return redirect()->route('memos.index')->with('success', 'Memo removed.');
    }
}
