<?php

namespace App\Http\Controllers;

use App\Models\VehicleAd;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class CarVideoBoostReportController extends Controller
{
    /**
     * Display the Car Video Boost Report (all vehicle ads/boosting details).
     */
    public function index(Request $request)
    {
        $query = VehicleAd::with(['vehicle' => function ($q) {
            $q->with(['make', 'vehicleModel']);
        }]);

        if ($request->filled('date_from')) {
            $query->where('posted_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('posted_date', '<=', $request->date_to);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('vehicle', function ($q) use ($search) {
                $q->where('plate_number', 'LIKE', "%{$search}%")
                    ->orWhere('make', 'LIKE', "%{$search}%")
                    ->orWhere('model', 'LIKE', "%{$search}%")
                    ->orWhereHas('make', fn($mq) => $mq->where('name', 'LIKE', "%{$search}%"))
                    ->orWhereHas('vehicleModel', fn($mq) => $mq->where('name', 'LIKE', "%{$search}%"));
            });
        }

        $ads = $query->orderBy('posted_date', 'desc')->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return view('car-video-boost-report.index', compact('ads'));
    }

    /**
     * Store a video ad record from the report page.
     */
    public function storeAd(Request $request)
    {
        $validated = $this->validateVehicleAdRequest($request);
        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        $ad = VehicleAd::create(array_merge(
            $this->vehicleAdPayload($request),
            ['vehicle_id' => $vehicle->id]
        ));

        Cache::flush();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Video ad details added successfully.',
                'ad' => $ad->load('vehicle'),
            ]);
        }

        return redirect()->route('car-video-boost-report.index')->with('success', 'Video ad details added successfully.');
    }

    /**
     * Update a video ad record from the report page.
     */
    public function updateAd(Request $request, VehicleAd $vehicleAd)
    {
        $validated = $this->validateVehicleAdRequest($request);
        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        $vehicleAd->update(array_merge(
            $this->vehicleAdPayload($request),
            ['vehicle_id' => $vehicle->id]
        ));

        Cache::flush();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Video ad details updated successfully.',
                'ad' => $vehicleAd->fresh()->load('vehicle'),
            ]);
        }

        return redirect()->route('car-video-boost-report.index')->with('success', 'Video ad details updated successfully.');
    }

    /**
     * Delete a video ad record from the report page.
     */
    public function destroyAd(Request $request, VehicleAd $vehicleAd)
    {
        $vehicleAd->delete();
        Cache::flush();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Video ad details deleted successfully.',
            ]);
        }

        return redirect()->route('car-video-boost-report.index')->with('success', 'Video ad details deleted successfully.');
    }

    private function validateVehicleAdRequest(Request $request): array
    {
        $request->merge([
            'video_links' => VehicleAd::normalizeVideoLinks($request->input('video_links')),
            'social_media_links' => VehicleAd::normalizeSocialMediaLinks($request->input('social_media_links')),
            'ads_boost_link' => $request->filled('ads_boost_link') ? $request->ads_boost_link : null,
            'campaign_id' => $request->filled('campaign_id') ? $request->campaign_id : null,
            'ad_id' => $request->filled('ad_id') ? $request->ad_id : null,
        ]);

        return $request->validate(array_merge([
            'vehicle_id' => 'required|exists:vehicles,id',
            'posted_date' => 'required|date',
            'video_links' => 'nullable|array',
            'video_links.*' => 'url|max:500',
            'social_media_links' => 'nullable|array',
            'social_media_links.*.channel' => ['required', 'string', Rule::in(VehicleAd::socialChannelOptions())],
            'social_media_links.*.link' => 'required|url|max:500',
            'ads_boost_link' => 'nullable|url|max:500',
            'campaign_id' => 'nullable|string|max:255',
            'ad_id' => 'nullable|string|max:255',
        ]));
    }

    private function vehicleAdPayload(Request $request): array
    {
        return [
            'posted_date' => $request->posted_date,
            'video_links' => $request->input('video_links', []),
            'social_media_links' => $request->input('social_media_links', []),
            'ads_boost_link' => $request->ads_boost_link,
            'campaign_id' => $request->campaign_id,
            'ad_id' => $request->ad_id,
        ];
    }
}
