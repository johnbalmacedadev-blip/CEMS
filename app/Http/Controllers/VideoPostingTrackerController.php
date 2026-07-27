<?php

namespace App\Http\Controllers;

use App\Models\BranchLocation;
use App\Models\Vehicle;
use App\Models\VideoPostingRecord;
use Illuminate\Http\Request;

class VideoPostingTrackerController extends Controller
{
    public function index(Request $request)
    {
        $query = VideoPostingRecord::with('vehicle');

        // Hide incomplete/empty import rows from the list
        $query->whereNotNull('link_url')
            ->where('link_url', '!=', '')
            ->where(function ($q) {
                $q->whereNotNull('date_posted_social')
                    ->orWhereNotNull('date_uploaded_gdrive');
            })
            ->whereNotNull('featured_car_or_client')
            ->where('featured_car_or_client', '!=', '')
            ->where(function ($q) {
                $q->whereNull('gdrive_file_name')
                    ->orWhere('gdrive_file_name', '')
                    ->orWhereColumn('featured_car_or_client', '!=', 'gdrive_file_name');
            });

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('vlogger')) {
            $query->where('vlogger', 'like', '%'.$request->vlogger.'%');
        }
        if ($request->filled('showroom')) {
            $query->whereRaw('LOWER(showroom) = ?', [strtolower($request->showroom)]);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
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
                    ->orWhere('notes', 'LIKE', "%{$search}%")
                    ->orWhere('vlogger', 'LIKE', "%{$search}%")
                    ->orWhere('plate_number', 'LIKE', "%{$search}%")
                    ->orWhere('featured_car_or_client', 'LIKE', "%{$search}%")
                    ->orWhere('gdrive_file_name', 'LIKE', "%{$search}%")
                    ->orWhere('showroom', 'LIKE', "%{$search}%");
            });
        }

        $records = $query->orderByRaw('COALESCE(date_posted_social, record_date) DESC')
            ->orderBy('created_at', 'desc')
            ->paginate(25)
            ->withQueryString();

        $showrooms = BranchLocation::ordered()->pluck('name');

        return view('video-posting-tracker.index', compact('records', 'showrooms'));
    }

    public function create()
    {
        $vehicles = Vehicle::with(['make', 'vehicleModel'])->orderBy('created_at', 'desc')->limit(800)->get();
        $showrooms = BranchLocation::ordered()->pluck('name');

        return view('video-posting-tracker.create', compact('vehicles', 'showrooms'));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $validated = $this->resolveVehicleLink($validated);

        VideoPostingRecord::create($validated);

        return redirect()->route('video-posting-tracker.index')->with('success', 'Record added successfully.');
    }

    public function show(VideoPostingRecord $video_posting_tracker)
    {
        return redirect()->route('video-posting-tracker.edit', $video_posting_tracker);
    }

    public function edit(VideoPostingRecord $video_posting_tracker)
    {
        $vehicles = Vehicle::with(['make', 'vehicleModel'])->orderBy('created_at', 'desc')->limit(800)->get();
        $showrooms = BranchLocation::ordered()->pluck('name');

        return view('video-posting-tracker.edit', compact('video_posting_tracker', 'vehicles', 'showrooms'));
    }

    public function update(Request $request, VideoPostingRecord $video_posting_tracker)
    {
        $validated = $this->validated($request);
        $validated = $this->resolveVehicleLink($validated);

        $video_posting_tracker->update($validated);

        return redirect()->route('video-posting-tracker.index')->with('success', 'Record updated successfully.');
    }

    public function destroy(VideoPostingRecord $video_posting_tracker)
    {
        $video_posting_tracker->delete();

        return redirect()->route('video-posting-tracker.index')->with('success', 'Record deleted successfully.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'record_date' => 'required|date',
            'type' => 'required|in:Video,Post,Boost',
            'platform' => 'nullable|string|max:100',
            'link_url' => 'nullable|url|max:500',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'status' => 'required|in:Scheduled,Posted,Pending',
            'notes' => 'nullable|string|max:2000',
            'vlogger' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:50',
            'showroom' => 'nullable|string|max:100',
            'featured_car_or_client' => 'nullable|string|max:255',
            'plate_number' => 'nullable|string|max:100',
            'active_unit' => 'nullable|string|max:50',
            'date_uploaded_gdrive' => 'nullable|date',
            'date_posted_social' => 'nullable|date',
            'gdrive_file_name' => 'nullable|string|max:255',
            'source_sheet' => 'nullable|string|max:100',
        ]);

        if (! empty($data['plate_number'])) {
            $data['plate_number'] = strtoupper(trim($data['plate_number']));
        }

        return $data;
    }

    private function resolveVehicleLink(array $data): array
    {
        if (! empty($data['vehicle_id'])) {
            return $data;
        }

        if (empty($data['plate_number'])) {
            return $data;
        }

        $first = preg_split('/\s*(?:\/|&|,)\s*/', $data['plate_number'])[0] ?? '';
        $normalized = strtoupper(preg_replace('/[\s\-]+/', '', $first) ?? '');
        if ($normalized === '') {
            return $data;
        }

        $match = Vehicle::query()->get(['id', 'plate_number'])->first(function ($v) use ($normalized) {
            return strtoupper(preg_replace('/[\s\-]+/', '', (string) $v->plate_number) ?? '') === $normalized;
        });

        if ($match) {
            $data['vehicle_id'] = $match->id;
        }

        return $data;
    }
}
