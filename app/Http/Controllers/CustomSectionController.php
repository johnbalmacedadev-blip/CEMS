<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomSection;
use App\Models\CustomSectionField;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class CustomSectionController extends Controller
{
    /**
     * Store a newly created custom section
     */
    public function store(Request $request, Vehicle $vehicle)
    {
        // Debug: Log the incoming request
        \Log::info('Custom section store request', [
            'vehicle_id' => $vehicle->id,
            'request_data' => $request->all()
        ]);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fields' => 'required|array|min:1',
            'fields.*.field_name' => 'required|string|max:255',
            'fields.*.field_label' => 'required|string|max:255',
            'fields.*.field_type' => 'required|in:text,textarea,number,date,email,url,select,checkbox,radio',
            'fields.*.field_value' => 'nullable|string',
            'fields.*.field_options' => 'nullable|array',
            'fields.*.is_required' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Create the custom section
            $customSection = $vehicle->customSections()->create([
                'title' => $request->title,
                'description' => $request->description,
                'sort_order' => $vehicle->customSections()->max('sort_order') + 1,
            ]);

            // Create the fields
            foreach ($request->fields as $index => $fieldData) {
                $customSection->fields()->create([
                    'field_name' => $fieldData['field_name'],
                    'field_label' => $fieldData['field_label'],
                    'field_type' => $fieldData['field_type'],
                    'field_value' => $fieldData['field_value'] ?? '',
                    'field_options' => $fieldData['field_options'] ?? [],
                    'is_required' => $fieldData['is_required'] ?? false,
                    'sort_order' => $index,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Custom section created successfully',
                'section' => $customSection->load('fields')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating custom section: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update a custom section
     */
    public function update(Request $request, Vehicle $vehicle, CustomSection $customSection)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fields' => 'required|array|min:1',
            'fields.*.field_name' => 'required|string|max:255',
            'fields.*.field_label' => 'required|string|max:255',
            'fields.*.field_type' => 'required|in:text,textarea,number,date,email,url,select,checkbox,radio',
            'fields.*.field_value' => 'nullable|string',
            'fields.*.field_options' => 'nullable|array',
            'fields.*.is_required' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            // Update the custom section
            $customSection->update([
                'title' => $request->title,
                'description' => $request->description,
            ]);

            // Delete existing fields and create new ones
            $customSection->fields()->delete();

            foreach ($request->fields as $index => $fieldData) {
                $customSection->fields()->create([
                    'field_name' => $fieldData['field_name'],
                    'field_label' => $fieldData['field_label'],
                    'field_type' => $fieldData['field_type'],
                    'field_value' => $fieldData['field_value'] ?? '',
                    'field_options' => $fieldData['field_options'] ?? [],
                    'is_required' => $fieldData['is_required'] ?? false,
                    'sort_order' => $index,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Custom section updated successfully',
                'section' => $customSection->load('fields')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating custom section: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a custom section
     */
    public function destroy(Vehicle $vehicle, CustomSection $customSection)
    {
        try {
            $customSection->delete();

            return response()->json([
                'success' => true,
                'message' => 'Custom section deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting custom section: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get custom sections for a vehicle
     */
    public function index(Vehicle $vehicle)
    {
        $customSections = $vehicle->customSections()->with('fields')->get();

        return response()->json([
            'success' => true,
            'sections' => $customSections
        ]);
    }

    /**
     * Get a specific custom section for editing
     */
    public function edit(Vehicle $vehicle, CustomSection $customSection)
    {
        // Ensure the custom section belongs to the vehicle
        if ($customSection->vehicle_id !== $vehicle->id) {
            return response()->json([
                'success' => false,
                'message' => 'Custom section not found for this vehicle'
            ], 404);
        }

        $customSection->load('fields');

        return response()->json([
            'success' => true,
            'section' => $customSection
        ]);
    }
}
