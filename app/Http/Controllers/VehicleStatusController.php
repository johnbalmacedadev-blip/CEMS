<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\SalesAgent;
use App\Models\SalesAgentCommission;
use App\Models\VehicleStatusDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class VehicleStatusController extends Controller
{
    /**
     * Update vehicle status
     */
    public function updateStatus(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'status' => 'required|in:Available,Under Maintenance,Reserved,Released,Forfeited',
        ]);

        // If status is Reserved, check if reservation details exist
        if ($request->status === 'Reserved') {
            $statusDetail = $vehicle->statusDetail;
            
            // Check if required reservation details are present
            if (!$statusDetail || 
                !$statusDetail->sale_date || 
                !$statusDetail->sales_person_reserved || 
                !$statusDetail->sale_reservation_amount) {
                
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot change status to Reserved. Please fill in Vehicle Reservation Details first. Required fields: Sale Date, Sales Person Reserved (S.E), and Sale Reservation Amount.',
                    'requireDetails' => true
                ], 422);
            }
        }

        // Update the vehicle status
        $vehicle->update(['status' => $request->status]);

        // Also update the sale_status in vehicle_status_details (create if doesn't exist)
        VehicleStatusDetail::updateOrCreate(
            ['plate_number' => $vehicle->plate_number],
            ['sale_status' => $request->status]
        );

        $vehicle->load('statusDetail', 'make', 'vehicleModel');
        $this->syncSalesAgentCommission($vehicle, $vehicle->statusDetail);

        // Clear cache
        Cache::flush();

        return response()->json([
            'success' => true,
            'message' => 'Vehicle status updated successfully!',
            'status' => $request->status
        ]);
    }

    /**
     * Store or update vehicle status details
     */
    public function storeStatusDetails(Request $request, Vehicle $vehicle)
    {
        if ($request->input('financing_company') === '') {
            $request->merge(['financing_company' => null]);
        }
        if ($request->input('sale_origin') === '') {
            $request->merge(['sale_origin' => null]);
        }

        $saleOriginAllowed = VehicleStatusDetail::saleOriginOptions();
        if ($vehicle->statusDetail && $vehicle->statusDetail->sale_origin
            && !in_array($vehicle->statusDetail->sale_origin, VehicleStatusDetail::saleOriginOptions(), true)) {
            $saleOriginAllowed[] = $vehicle->statusDetail->sale_origin;
        }

        // If status is Reserved, make required fields mandatory
        $rules = [
            'showroom' => 'nullable|string|max:255',
            'sale_date' => 'nullable|date',
            'sales_price' => 'nullable|numeric|min:0',
            'sale_reservation_amount' => 'nullable|numeric|min:0',
            'sales_person_reserved' => 'nullable|string|max:255',
            'sales_person_release' => 'nullable|string|max:255',
            'good_sales_review' => 'nullable|boolean',
            'cash_financing' => 'nullable|in:Cash,Financing',
            'financing_company' => ['nullable', 'string', 'max:100', Rule::in(VehicleStatusDetail::financingCompanyOptions())],
            'sale_origin' => ['nullable', 'string', 'max:100', Rule::in($saleOriginAllowed)],
            'sales_agent_name' => 'nullable|string|max:255',
            'agent_cost' => 'nullable|numeric|min:0',
            'finance_revenue_1' => 'nullable|numeric|min:0',
            'finance_revenue_2' => 'nullable|numeric|min:0',
            'sale_status' => 'required|in:Available,Under Maintenance,Reserved,Released,Forfeited',
            'transfer_cost' => 'nullable|numeric|min:0',
            'release_date' => 'nullable|date',
            'has_insurance' => 'nullable|boolean',
            'insurance_value' => 'nullable|numeric|min:0',
            'has_trade_in' => 'nullable|boolean',
            'trade_in_value' => 'nullable|numeric|min:0',
            'days_from_acquisition_to_reservation' => 'nullable|integer|min:0',
            'customer_first_name' => 'nullable|string|max:255',
            'customer_last_name' => 'nullable|string|max:255',
            'customer_middle_name' => 'nullable|string|max:255',
            'customer_contact_number' => 'nullable|string|max:50',
            'customer_date_of_birth' => 'nullable|date',
            'customer_gender' => 'nullable|in:Male,Female,Other',
            'customer_location' => 'nullable|string|max:255',
            'customer_purpose' => 'nullable|string',
        ];
        
        // If status is Reserved, require reservation details
        if ($request->sale_status === 'Reserved') {
            $rules['sale_date'] = 'required|date';
            $rules['sales_person_reserved'] = 'required|string|max:255';
            $rules['sale_reservation_amount'] = 'required|numeric|min:0';
        }
        
        $request->validate($rules);

        // Calculate days from reservation to release if both dates are provided
        $daysFromReservationToRelease = null;
        if ($request->sale_date && $request->release_date) {
            $saleDate = \Carbon\Carbon::parse($request->sale_date);
            $releaseDate = \Carbon\Carbon::parse($request->release_date);
            $daysFromReservationToRelease = $saleDate->diffInDays($releaseDate);
        }

        // Calculate days from acquisition to reservation if both dates are provided
        $daysFromAcquisitionToReservation = null;
        // Use sale_date from request, or existing sale_date if updating
        $saleDateForCalculation = $request->sale_date ?? ($vehicle->statusDetail->sale_date ?? null);
        
        if ($saleDateForCalculation && $vehicle->purchase_date) {
            $purchaseDate = \Carbon\Carbon::parse($vehicle->purchase_date);
            $saleDate = \Carbon\Carbon::parse($saleDateForCalculation);
            $daysFromAcquisitionToReservation = $purchaseDate->diffInDays($saleDate);
        } elseif ($request->days_from_acquisition_to_reservation) {
            // Use the value from the request if provided (from JavaScript calculation)
            $daysFromAcquisitionToReservation = $request->days_from_acquisition_to_reservation;
        } elseif ($vehicle->statusDetail && $vehicle->statusDetail->sale_date && $vehicle->purchase_date) {
            // Recalculate if we have existing dates but no stored value
            $purchaseDate = \Carbon\Carbon::parse($vehicle->purchase_date);
            $saleDate = \Carbon\Carbon::parse($vehicle->statusDetail->sale_date);
            $daysFromAcquisitionToReservation = $purchaseDate->diffInDays($saleDate);
        }

        $statusDetailData = $request->all();
        $statusDetailData['plate_number'] = $vehicle->plate_number;
        $statusDetailData['days_from_reservation_to_release'] = $daysFromReservationToRelease;
        $statusDetailData['days_from_acquisition_to_reservation'] = $daysFromAcquisitionToReservation;
        
        // Handle checkbox values - ensure they're boolean
        $statusDetailData['has_insurance'] = isset($statusDetailData['has_insurance']) ? (bool)$statusDetailData['has_insurance'] : false;
        $statusDetailData['has_trade_in'] = isset($statusDetailData['has_trade_in']) ? (bool)$statusDetailData['has_trade_in'] : false;
        
        // Clear insurance/trade-in values if checkboxes are unchecked
        if (!$statusDetailData['has_insurance']) {
            $statusDetailData['insurance_value'] = null;
        }
        if (!$statusDetailData['has_trade_in']) {
            $statusDetailData['trade_in_value'] = null;
        }

        if (($statusDetailData['cash_financing'] ?? '') !== 'Financing') {
            $statusDetailData['financing_company'] = null;
        }

        if (($statusDetailData['sale_origin'] ?? '') !== 'Agent') {
            $statusDetailData['sales_agent_name'] = null;
            $statusDetailData['agent_cost'] = 0;
        }

        // If no sale_status is provided, default to the current vehicle status
        if (empty($request->sale_status)) {
            $statusDetailData['sale_status'] = $vehicle->status;
        }

        // Update or create status detail
        $statusDetail = VehicleStatusDetail::updateOrCreate(
            ['plate_number' => $vehicle->plate_number],
            $statusDetailData
        );

        // If status is Reserved, validate required fields are present before updating
        if ($statusDetailData['sale_status'] === 'Reserved') {
            if (!$statusDetail->sale_date || !$statusDetail->sales_person_reserved || !$statusDetail->sale_reservation_amount) {
                // Don't update vehicle status if required fields are missing
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot set status to Reserved. Please fill in all required fields: Sale Date, Sales Person Reserved (S.E), and Sale Reservation Amount.',
                    'statusDetail' => $statusDetail
                ], 422);
            }
        }

        // Update the vehicle status to match the sale_status
        $vehicle->update(['status' => $statusDetailData['sale_status']]);

        $vehicle->load('make', 'vehicleModel');
        $this->syncSalesAgentCommission($vehicle, $statusDetail);

        // Clear cache
        Cache::flush();

        return response()->json([
            'success' => true,
            'message' => 'Vehicle status details saved successfully!',
            'statusDetail' => $statusDetail
        ]);
    }

    /**
     * Get status details for a vehicle
     */
    public function getStatusDetails(Vehicle $vehicle)
    {
        $statusDetail = $vehicle->statusDetail;
        
        return response()->json([
            'success' => true,
            'statusDetail' => $statusDetail
        ]);
    }

    /**
     * Show reservation details page
     */
    public function showReservationDetails(Vehicle $vehicle)
    {
        $vehicle->load([
            'statusDetail',
            'reservationDocuments.files',
            'acquisitionDocuments.files'
        ]);

        // Load showrooms for the modal
        $showrooms = \App\Models\Showroom::active()->orderBy('name')->get();

        $executiveAgents = \App\Models\ExecutiveAgent::query()
            ->orderByRaw("CASE WHEN LOWER(status) = 'active' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get(['id', 'name', 'executive_code', 'status']);

        $salesAgentsList = \App\Models\SalesAgent::query()
            ->orderByRaw("CASE WHEN LOWER(status) = 'active' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get(['id', 'name', 'sales_agent_id', 'status']);

        return view('vehicles.reservation-details.show', compact('vehicle', 'showrooms', 'executiveAgents', 'salesAgentsList'));
    }

    /**
     * Delete status details for a vehicle
     */
    public function deleteStatusDetails(Vehicle $vehicle)
    {
        // Store the current status before deletion
        $currentStatus = $vehicle->status;
        
        // Delete the status detail
        $vehicle->statusDetail()->delete();
        
        // If the vehicle was Reserved or Released, reset status to Available
        if ($currentStatus === 'Reserved' || $currentStatus === 'Released') {
            $vehicle->update(['status' => 'Available']);
        }

        // Clear cache
        Cache::flush();

        return response()->json([
            'success' => true,
            'message' => 'Vehicle reservation details deleted successfully!' . (($currentStatus === 'Reserved' || $currentStatus === 'Released') ? ' Vehicle status has been changed to Available.' : '')
        ]);
    }

    /**
     * Keep an auto-generated sales-agent commission row in sync with reservation details.
     */
    private function syncSalesAgentCommission(Vehicle $vehicle, ?VehicleStatusDetail $statusDetail): void
    {
        $autoNotePrefix = '[AUTO_RESERVATION_COMMISSION]';
        $existingAuto = SalesAgentCommission::query()
            ->where('vehicle_id', $vehicle->id)
            ->where('notes', 'LIKE', $autoNotePrefix . '%')
            ->first();

        if (!$statusDetail) {
            if ($existingAuto) {
                $existingAuto->delete();
            }
            return;
        }

        $isAgentOrigin = ($statusDetail->sale_origin ?? '') === 'Agent';
        $agentName = trim((string) ($statusDetail->sales_agent_name ?? ''));
        $commissionAmount = (float) ($statusDetail->agent_cost ?? 0);
        $isEligible = $isAgentOrigin && $agentName !== '' && $commissionAmount > 0;

        if (!$isEligible) {
            if ($existingAuto) {
                $existingAuto->delete();
            }
            return;
        }

        $salesAgent = SalesAgent::query()->where('name', $agentName)->first();
        $saleStatus = (string) ($statusDetail->sale_status ?? $vehicle->status ?? '');
        $isPosted = $saleStatus === 'Released';

        $clientName = trim(implode(' ', array_filter([
            $statusDetail->customer_first_name ?? '',
            $statusDetail->customer_middle_name ?? '',
            $statusDetail->customer_last_name ?? '',
        ])));

        $payload = [
            'showroom' => $statusDetail->showroom ?: ($vehicle->purchased_from ?: 'FLAGSHIP'),
            'commission_status' => $isPosted ? SalesAgentCommission::STATUS_POSTED : SalesAgentCommission::STATUS_PENDING,
            'sales_agent_id' => $salesAgent?->id,
            'agent_name' => $agentName,
            'client_name' => $clientName !== '' ? $clientName : null,
            'unit' => $vehicle->full_name,
            'vehicle_id' => $vehicle->id,
            'plate_number' => $vehicle->plate_number,
            'transaction_type' => (($statusDetail->cash_financing ?? '') === 'Financing')
                ? SalesAgentCommission::TRANSACTION_FINANCING
                : SalesAgentCommission::TRANSACTION_CASH,
            'release_date' => $isPosted ? ($statusDetail->release_date ?? now()->toDateString()) : null,
            'amount' => $commissionAmount,
            // Reservation date for pending; release date for posted if available.
            'date_sent' => $statusDetail->sale_date ?: ($isPosted ? ($statusDetail->release_date ?? now()->toDateString()) : now()->toDateString()),
            'notes' => $autoNotePrefix . ' Synced from vehicle reservation details.',
        ];

        if ($existingAuto) {
            $existingAuto->update($payload);
        } else {
            SalesAgentCommission::create($payload);
        }
    }
}