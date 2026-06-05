<?php

namespace App\Http\Controllers;

use App\Models\VideoPostingRecord;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VideoPostingTrackerController extends Controller
{
    /**
     * Display the video and posting tracker list.
     */
    public function index(Request $request)
    {
        $query = VideoPostingRecord::with('vehicle');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->where('record_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('record_date', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('platform', 'LIKE', "%{$search}%")
                    ->orWhere('notes', 'LIKE', "%{$search}%");
            });
        }

        $records = $query->orderBy('record_date', 'desc')->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('video-posting-tracker.index', compact('records'));
    }

    /**
     * Show the form for creating a new record.
     */
    public function create()
    {
        $vehicles = Vehicle::with(['make', 'vehicleModel'])->orderBy('created_at', 'desc')->get();
        return view('video-posting-tracker.create', compact('vehicles'));
    }

    /**
     * Store a newly created record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'record_date' => 'required|date',
            'type' => 'required|in:Video,Post,Boost',
            'platform' => 'nullable|string|max:100',
            'link_url' => 'nullable|url|max:500',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'status' => 'required|in:Scheduled,Posted,Pending',
            'notes' => 'nullable|string|max:2000',
        ]);

        VideoPostingRecord::create($validated);

        return redirect()->route('video-posting-tracker.index')->with('success', 'Record added successfully.');
    }

    /**
     * Display the specified record (redirect to edit).
     */
    public function show(VideoPostingRecord $video_posting_tracker)
    {
        return redirect()->route('video-posting-tracker.edit', $video_posting_tracker);
    }

    /**
     * Show the form for editing the specified record.
     */
    public function edit(VideoPostingRecord $video_posting_tracker)
    {
        $vehicles = Vehicle::with(['make', 'vehicleModel'])->orderBy('created_at', 'desc')->get();
        return view('video-posting-tracker.edit', compact('video_posting_tracker', 'vehicles'));
    }

    /**
     * Update the specified record.
     */
    public function update(Request $request, VideoPostingRecord $video_posting_tracker)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'record_date' => 'required|date',
            'type' => 'required|in:Video,Post,Boost',
            'platform' => 'nullable|string|max:100',
            'link_url' => 'nullable|url|max:500',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'status' => 'required|in:Scheduled,Posted,Pending',
            'notes' => 'nullable|string|max:2000',
        ]);

        $video_posting_tracker->update($validated);

        return redirect()->route('video-posting-tracker.index')->with('success', 'Record updated successfully.');
    }

    /**
     * Remove the specified record.
     */
    public function destroy(VideoPostingRecord $video_posting_tracker)
    {
        $video_posting_tracker->delete();
        return redirect()->route('video-posting-tracker.index')->with('success', 'Record deleted successfully.');
    }
}
