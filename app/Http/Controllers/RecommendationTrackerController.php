<?php

namespace App\Http\Controllers;

use App\Models\RecommendationTracker;
use App\Models\RecommendationTrackerImage;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RecommendationTrackerController extends Controller
{
    protected static function checkboxKeys(): array
    {
        return [
            'hood', 'front_bumper', 'grille', 'fender_right', 'fender_left',
            'driver_passenger_door', 'driver_side_door', 'step_board_left', 'step_board_right',
            'trunk_lid', 'quarter_panels_left', 'rear_bumper', 'quarter_panel_right',
            'passenger_door_right_rear', 'passenger_door_right_front', 'roof', 'spoiler',
            'tire_1', 'tire_2', 'tire_3', 'tire_4',
            'rims_1', 'rims_2', 'rims_3', 'rims_4',
            'front_headlight_1', 'front_headlight_2',
            'inner_rear_taillight_1', 'inner_rear_taillight_2',
            'taillight_1', 'taillight_2',
            'side_mirror_left', 'side_mirror_right', 'mud_guard',
            'windshield_front', 'windshield_rear',
            'with_spare_key', 'with_spare_tire', 'with_tools', 'with_matting_complete',
            'row_2nd', 'row_3rd', 'row_1st', 'dash_cam',
        ];
    }

    /**
     * Display a listing of recommendation records.
     */
    public function index(Request $request)
    {
        $query = RecommendationTracker::with('vehicle');

        if ($request->filled('customer')) {
            $query->where('customer', 'like', '%' . $request->customer . '%');
        }
        if ($request->filled('make')) {
            $query->where('make', 'like', '%' . $request->make . '%');
        }
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        $records = $query->orderBy('date', 'desc')->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('recommendation-tracker.index', compact('records'));
    }

    /**
     * Show the form for creating a new recommendation record.
     */
    public function create()
    {
        $vehicles = Vehicle::with(['make', 'vehicleModel', 'statusDetail'])->orderBy('created_at', 'desc')->get();
        return view('recommendation-tracker.create', compact('vehicles'));
    }

    /**
     * Store a newly created recommendation record.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'year' => 'nullable|string|max:20',
            'customer' => 'nullable|string|max:255',
            'make' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'paint' => 'nullable|string|max:255',
            'odometers' => 'nullable|string|max:100',
            'authorized_drivers' => 'nullable|string|max:255',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        foreach (self::checkboxKeys() as $key) {
            $validated[$key] = $request->boolean($key);
        }

        $record = RecommendationTracker::create($validated);

        $this->uploadImages($request, $record);

        return redirect()->route('recommendation-tracker.edit', $record)->with('success', 'Recommendation record added successfully. You can add more images below.');
    }

    /**
     * Display the specified recommendation record.
     */
    public function show(RecommendationTracker $recommendation_tracker)
    {
        $recommendation_tracker->load(['vehicle', 'images']);
        return view('recommendation-tracker.show', compact('recommendation_tracker'));
    }

    /**
     * Show the form for editing the specified recommendation record.
     */
    public function edit(RecommendationTracker $recommendation_tracker)
    {
        $recommendation_tracker->load('images');
        $vehicles = Vehicle::with(['make', 'vehicleModel', 'statusDetail'])->orderBy('created_at', 'desc')->get();
        return view('recommendation-tracker.edit', compact('recommendation_tracker', 'vehicles'));
    }

    /**
     * Update the specified recommendation record.
     */
    public function update(Request $request, RecommendationTracker $recommendation_tracker)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'year' => 'nullable|string|max:20',
            'customer' => 'nullable|string|max:255',
            'make' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'paint' => 'nullable|string|max:255',
            'odometers' => 'nullable|string|max:100',
            'authorized_drivers' => 'nullable|string|max:255',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        foreach (self::checkboxKeys() as $key) {
            $validated[$key] = $request->boolean($key);
        }

        $recommendation_tracker->update($validated);

        $this->uploadImages($request, $recommendation_tracker);

        return redirect()->route('recommendation-tracker.edit', $recommendation_tracker)->with('success', 'Recommendation record updated successfully.');
    }

    /**
     * Upload and attach images to a recommendation record.
     */
    protected function uploadImages(Request $request, RecommendationTracker $record): void
    {
        if (! $request->hasFile('images')) {
            return;
        }

        $sortOrder = $record->images()->max('sort_order') ?? -1;

        foreach ($request->file('images') as $file) {
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $fileName = Str::uuid() . '.' . $extension;
            $path = $file->storeAs('recommendation-tracker/images', $fileName, 'public');

            RecommendationTrackerImage::create([
                'recommendation_tracker_id' => $record->id,
                'file_path' => $path,
                'original_name' => $originalName,
                'sort_order' => ++$sortOrder,
            ]);
        }
    }

    /**
     * Delete a single image.
     */
    public function destroyImage(RecommendationTracker $recommendation_tracker, RecommendationTrackerImage $image)
    {
        if ($image->recommendation_tracker_id !== $recommendation_tracker->id) {
            abort(404);
        }
        Storage::disk('public')->delete($image->file_path);
        $image->delete();
        return redirect()->back()->with('success', 'Image removed.');
    }

    /**
     * Remove the specified recommendation record.
     */
    public function destroy(RecommendationTracker $recommendation_tracker)
    {
        foreach ($recommendation_tracker->images as $img) {
            Storage::disk('public')->delete($img->file_path);
        }
        $recommendation_tracker->delete();
        return redirect()->route('recommendation-tracker.index')->with('success', 'Recommendation record deleted successfully.');
    }
}
