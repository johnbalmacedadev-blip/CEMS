<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleForfeitDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class VehicleForfeitDetailController extends Controller
{
    /**
     * Store a newly created forfeit detail.
     */
    public function store(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'previous_forfeit_date' => 'nullable|date',
            'forfeit_amount' => 'required|numeric|min:0',
            'forfeit_date' => 'required|date',
        ]);

        $detail = VehicleForfeitDetail::create([
            'vehicle_id' => $vehicle->id,
            'previous_forfeit_date' => $request->previous_forfeit_date,
            'forfeit_amount' => $request->forfeit_amount,
            'forfeit_date' => $request->forfeit_date,
        ]);

        // Mark vehicle as Forfeited when forfeit details are added
        if ($vehicle->status !== 'Forfeited') {
            $vehicle->update(['status' => 'Forfeited']);
        }

        Cache::flush();

        return response()->json([
            'success' => true,
            'message' => 'Forfeit details added successfully!',
            'detail' => $detail
        ]);
    }
}
