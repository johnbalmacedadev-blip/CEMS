<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VehicleCustomField;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class VehicleCustomFieldController extends Controller
{
    /**
     * Store a newly created custom field
     */
    public function store(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'section_name' => 'required|string|max:255',
            'field_name' => 'required|string|max:255',
            'field_label' => 'required|string|max:255',
            'field_type' => 'required|in:text,textarea,number,date,email,url,select,checkbox,radio',
            'field_value' => 'nullable|string',
            'field_options' => 'nullable|array',
            'is_required' => 'boolean',
        ]);

        try {
            $customField = $vehicle->customFields()->create([
                'section_name' => $request->section_name,
                'field_name' => $request->field_name,
                'field_label' => $request->field_label,
                'field_type' => $request->field_type,
                'field_value' => $request->field_value ?? '',
                'field_options' => $request->field_options ?? [],
                'is_required' => $request->is_required ?? false,
                'sort_order' => $vehicle->customFieldsForSection($request->section_name)->max('sort_order') + 1,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Custom field created successfully',
                'field' => $customField
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating custom field: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a custom field
     */
    public function update(Request $request, Vehicle $vehicle, VehicleCustomField $customField)
    {
        $request->validate([
            'field_name' => 'required|string|max:255',
            'field_label' => 'required|string|max:255',
            'field_type' => 'required|in:text,textarea,number,date,email,url,select,checkbox,radio',
            'field_value' => 'nullable|string',
            'field_options' => 'nullable|array',
            'is_required' => 'boolean',
        ]);

        try {
            $customField->update([
                'field_name' => $request->field_name,
                'field_label' => $request->field_label,
                'field_type' => $request->field_type,
                'field_value' => $request->field_value ?? '',
                'field_options' => $request->field_options ?? [],
                'is_required' => $request->is_required ?? false,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Custom field updated successfully',
                'field' => $customField
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating custom field: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a custom field
     */
    public function destroy(Vehicle $vehicle, VehicleCustomField $customField)
    {
        try {
            $customField->delete();

            return response()->json([
                'success' => true,
                'message' => 'Custom field deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting custom field: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific custom field
     */
    public function show(Vehicle $vehicle, VehicleCustomField $customField)
    {
        // Ensure the custom field belongs to the vehicle
        if ($customField->vehicle_id !== $vehicle->id) {
            return response()->json([
                'success' => false,
                'message' => 'Custom field not found for this vehicle'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'field' => $customField
        ]);
    }

    /**
     * Get custom fields for a specific section
     */
    public function getFieldsForSection(Vehicle $vehicle, $sectionName)
    {
        $fields = $vehicle->customFieldsForSection($sectionName)->get();

        return response()->json([
            'success' => true,
            'fields' => $fields
        ]);
    }
}
