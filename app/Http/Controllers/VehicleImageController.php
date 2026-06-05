<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehicleImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
// use Intervention\Image\Facades\Image; // Commented out - GD extension not available

class VehicleImageController extends Controller
{
    /**
     * Display the Car Photos Folder page (all vehicles with their photos).
     */
    public function carPhotosFolder()
    {
        $vehicles = Vehicle::with(['primaryImage', 'images', 'make', 'vehicleModel'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('car-photos-folder.index', compact('vehicles'));
    }

    /**
     * Display the vehicle images management page
     */
    public function index(Vehicle $vehicle)
    {
        $vehicle->load('images');
        return view('vehicles.images.index', compact('vehicle'));
    }

    /**
     * Upload images for a vehicle
     */
    public function store(Request $request, Vehicle $vehicle)
    {
        try {
            \Log::info('Image upload started', [
                'vehicle_id' => $vehicle->id,
                'files_count' => $request->hasFile('images') ? count($request->file('images')) : 0
            ]);

            $request->validate([
                'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max per image
            ]);

        // Check if vehicle already has 5 images
        if ($vehicle->images()->count() >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Maximum of 5 images allowed per vehicle.'
            ], 422);
        }

        $uploadedImages = [];
        $uploadedCount = 0;
        $maxImages = 5 - $vehicle->images()->count();

        foreach ($request->file('images') as $image) {
            if ($uploadedCount >= $maxImages) {
                break;
            }

            $originalName = $image->getClientOriginalName();
            $extension = $image->getClientOriginalExtension();
            $fileName = Str::uuid() . '.' . $extension;
            $path = 'vehicles/images/' . $fileName;

            // Store the original image
            $image->storeAs('public/vehicles/images', $fileName);

            // Create thumbnail (simplified - just copy the original for now)
            $thumbnailPath = 'vehicles/thumbnails/' . Str::uuid() . '_thumb.' . $extension;
            $image->storeAs('public/vehicles/thumbnails', basename($thumbnailPath));

            // Save image record
            $vehicleImage = VehicleImage::create([
                'vehicle_id' => $vehicle->id,
                'image_path' => $path,
                'original_name' => $originalName,
                'mime_type' => $image->getMimeType(),
                'file_size' => $image->getSize(),
                'is_primary' => $vehicle->images()->count() === 0, // First image is primary
                'sort_order' => $vehicle->images()->count(),
            ]);

            $uploadedImages[] = $vehicleImage;
            $uploadedCount++;
        }

            return response()->json([
                'success' => true,
                'message' => 'Images uploaded successfully.',
                'images' => $uploadedImages
            ]);
        } catch (\Exception $e) {
            \Log::error('Image upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set primary image
     */
    public function setPrimary(Vehicle $vehicle, VehicleImage $image)
    {
        // Ensure the image belongs to the vehicle
        if ($image->vehicle_id !== $vehicle->id) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found for this vehicle.'
            ], 404);
        }

        // Remove primary from all other images
        $vehicle->images()->update(['is_primary' => false]);

        // Set this image as primary
        $image->update(['is_primary' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Primary image updated successfully.'
        ]);
    }

    /**
     * Delete an image
     */
    public function destroy(Vehicle $vehicle, VehicleImage $image)
    {
        // Ensure the image belongs to the vehicle
        if ($image->vehicle_id !== $vehicle->id) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found for this vehicle.'
            ], 404);
        }

        // Delete files from storage
        Storage::delete('public/' . $image->image_path);
        
        // Delete thumbnail
        $pathInfo = pathinfo($image->image_path);
        $thumbnailPath = 'vehicles/thumbnails/' . $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
        Storage::delete('public/' . $thumbnailPath);

        // If this was the primary image, set another as primary
        if ($image->is_primary) {
            $nextImage = $vehicle->images()->where('id', '!=', $image->id)->first();
            if ($nextImage) {
                $nextImage->update(['is_primary' => true]);
            }
        }

        // Delete the image record
        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully.'
        ]);
    }

    /**
     * Create thumbnail for image (simplified version without GD extension)
     */
    private function createThumbnail($image, $thumbnailPath)
    {
        try {
            // For now, just copy the original image as thumbnail
            // In production, you would want to install GD extension and use Intervention Image
            $image->storeAs('public/vehicles/thumbnails', basename($thumbnailPath));
        } catch (\Exception $e) {
            // If thumbnail creation fails, continue without thumbnail
            \Log::error('Thumbnail creation failed: ' . $e->getMessage());
        }
    }
}