<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleAd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class VehicleAdController extends Controller
{
    /**
     * Store a newly created vehicle ad.
     */
    public function store(Request $request, Vehicle $vehicle)
    {
        $validated = $this->validateVehicleAdRequest($request);

        $ad = VehicleAd::create(array_merge(
            $this->vehicleAdPayload($request),
            ['vehicle_id' => $vehicle->id]
        ));

        Cache::flush();

        return response()->json([
            'success' => true,
            'message' => 'Video ad details added successfully!',
            'ad' => $ad,
        ]);
    }

    /**
     * Update the specified vehicle ad.
     */
    public function update(Request $request, Vehicle $vehicle, VehicleAd $vehicleAd)
    {
        if ($vehicleAd->vehicle_id !== $vehicle->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $validated = $this->validateVehicleAdRequest($request);

        $vehicleAd->update($this->vehicleAdPayload($request));

        Cache::flush();

        return response()->json([
            'success' => true,
            'message' => 'Video ad details updated successfully!',
            'ad' => $vehicleAd->fresh(),
        ]);
    }

    /**
     * Remove the specified vehicle ad.
     */
    public function destroy(Vehicle $vehicle, VehicleAd $vehicleAd)
    {
        if ($vehicleAd->vehicle_id !== $vehicle->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        $vehicleAd->delete();

        Cache::flush();

        return response()->json([
            'success' => true,
            'message' => 'Video ad details deleted successfully!',
        ]);
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
