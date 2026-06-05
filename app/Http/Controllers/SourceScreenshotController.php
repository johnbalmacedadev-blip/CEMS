<?php

namespace App\Http\Controllers;

use App\Models\SourceScreenshot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SourceScreenshotController extends Controller
{
    /**
     * Display the source screenshots list.
     */
    public function index(Request $request)
    {
        $query = SourceScreenshot::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('date_from')) {
            $query->where('screenshot_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('screenshot_date', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('category', 'LIKE', "%{$search}%");
            });
        }

        $screenshots = $query->orderBy('screenshot_date', 'desc')->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        $categories = SourceScreenshot::whereNotNull('category')->where('category', '!=', '')->distinct()->pluck('category')->sort()->values();

        return view('source-screenshots.index', compact('screenshots', 'categories'));
    }

    /**
     * Show the form for creating a new source screenshot.
     */
    public function create()
    {
        return view('source-screenshots.create');
    }

    /**
     * Store a newly created source screenshot.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'category' => 'nullable|string|max:100',
            'screenshot_date' => 'required|date',
            'link_url' => 'nullable|url|max:500',
            'screenshot_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $path = null;
        if ($request->hasFile('screenshot_file')) {
            $file = $request->file('screenshot_file');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('source-screenshots', $fileName, 'public');
        }

        SourceScreenshot::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'] ?? null,
            'screenshot_date' => $validated['screenshot_date'],
            'link_url' => $validated['link_url'] ?? null,
            'file_path' => $path,
        ]);

        return redirect()->route('source-screenshots.index')->with('success', 'Source screenshot added successfully.');
    }

    /**
     * Display the specified source screenshot (redirect to edit).
     */
    public function show(SourceScreenshot $source_screenshot)
    {
        return redirect()->route('source-screenshots.edit', $source_screenshot);
    }

    /**
     * Show the form for editing the specified source screenshot.
     */
    public function edit(SourceScreenshot $source_screenshot)
    {
        return view('source-screenshots.edit', compact('source_screenshot'));
    }

    /**
     * Update the specified source screenshot.
     */
    public function update(Request $request, SourceScreenshot $source_screenshot)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'category' => 'nullable|string|max:100',
            'screenshot_date' => 'required|date',
            'link_url' => 'nullable|url|max:500',
            'screenshot_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        $path = $source_screenshot->file_path;
        if ($request->hasFile('screenshot_file')) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
            $file = $request->file('screenshot_file');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('source-screenshots', $fileName, 'public');
        }

        $source_screenshot->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'] ?? null,
            'screenshot_date' => $validated['screenshot_date'],
            'link_url' => $validated['link_url'] ?? null,
            'file_path' => $path,
        ]);

        return redirect()->route('source-screenshots.index')->with('success', 'Source screenshot updated successfully.');
    }

    /**
     * Remove the specified source screenshot.
     */
    public function destroy(SourceScreenshot $source_screenshot)
    {
        if ($source_screenshot->file_path && Storage::disk('public')->exists($source_screenshot->file_path)) {
            Storage::disk('public')->delete($source_screenshot->file_path);
        }
        $source_screenshot->delete();

        return redirect()->route('source-screenshots.index')->with('success', 'Source screenshot deleted successfully.');
    }
}
