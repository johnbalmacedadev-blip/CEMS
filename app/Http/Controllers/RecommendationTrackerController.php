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
            $query->where(function ($q) use ($request) {
                $q->where('customer', 'like', '%'.$request->customer.'%')
                    ->orWhere('purchased_from', 'like', '%'.$request->customer.'%');
            });
        }
        if ($request->filled('make')) {
            $query->where('make', 'like', '%'.$request->make.'%');
        }
        if ($request->filled('final_status')) {
            $query->where('final_status', $request->final_status);
        }
        if ($request->filled('linked')) {
            if ($request->linked === 'yes') {
                $query->whereNotNull('vehicle_id');
            } elseif ($request->linked === 'no') {
                $query->whereNull('vehicle_id');
            }
        }
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%")
                    ->orWhere('make', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%")
                    ->orWhere('variant', 'like', "%{$search}%")
                    ->orWhere('customer', 'like', "%{$search}%")
                    ->orWhere('purchased_from', 'like', "%{$search}%")
                    ->orWhere('paint_recommendation', 'like', "%{$search}%");
            });
        }

        $records = $query->orderBy('date', 'desc')->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

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
        $validated = $this->validated($request);

        foreach (self::checkboxKeys() as $key) {
            $validated[$key] = $request->boolean($key);
        }

        $validated = $this->resolveVehicleLink($validated);

        $record = RecommendationTracker::create($validated);

        $this->uploadImages($request, $record);

        return redirect()->route('recommendation-tracker.index')->with('success', 'Recommendation record added successfully.');
    }

    /**
     * Display the specified recommendation record.
     */
    public function show(RecommendationTracker $recommendation_tracker)
    {
        return redirect()->route('recommendation-tracker.index');
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
        $validated = $this->validated($request);

        foreach (self::checkboxKeys() as $key) {
            $validated[$key] = $request->boolean($key);
        }

        $validated = $this->resolveVehicleLink($validated);

        $recommendation_tracker->update($validated);

        $this->uploadImages($request, $recommendation_tracker);

        return redirect()->route('recommendation-tracker.index')->with('success', 'Recommendation record updated successfully.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'date' => 'required|date',
            'year' => 'nullable|string|max:20',
            'customer' => 'nullable|string|max:255',
            'make' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'paint' => 'nullable|string|max:255',
            'plate_number' => 'nullable|string|max:50',
            'variant' => 'nullable|string|max:255',
            'transmission' => 'nullable|string|max:50',
            'fuel_type' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:100',
            'purchase_price' => 'nullable|numeric|min:0',
            'purchased_from' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'final_status' => 'nullable|string|max:100',
            'paint_recommendation' => 'nullable|string|max:5000',
            'paint_completion' => 'nullable|string|max:5000',
            'mechanical_recommendation' => 'nullable|string|max:5000',
            'mechanical_completion' => 'nullable|string|max:5000',
            'electrical_recommendation' => 'nullable|string|max:5000',
            'electrical_completion' => 'nullable|string|max:5000',
            'ecu_cluster_recommendation' => 'nullable|string|max:5000',
            'ecu_cluster_completion' => 'nullable|string|max:5000',
            'aircon_recommendation' => 'nullable|string|max:5000',
            'aircon_completion' => 'nullable|string|max:5000',
            'interior_recommendation' => 'nullable|string|max:5000',
            'interior_completion' => 'nullable|string|max:5000',
            'tires_recommendation' => 'nullable|string|max:5000',
            'tires_completion' => 'nullable|string|max:5000',
            'battery_recommendation' => 'nullable|string|max:5000',
            'battery_completion' => 'nullable|string|max:5000',
            'misc_recommendation' => 'nullable|string|max:5000',
            'misc_completion' => 'nullable|string|max:5000',
            'notes' => 'nullable|string|max:2000',
            'odometers' => 'nullable|string|max:100',
            'authorized_drivers' => 'nullable|string|max:255',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
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

        if (! empty($data['plate_number'])) {
            $normalized = RecommendationTracker::normalizePlate($data['plate_number']);
            $match = Vehicle::query()->get(['id', 'plate_number'])->first(function ($v) use ($normalized) {
                return RecommendationTracker::normalizePlate($v->plate_number) === $normalized;
            });
            if ($match) {
                $data['vehicle_id'] = $match->id;
            }
        }

        return $data;
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
