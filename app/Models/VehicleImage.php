<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class VehicleImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'image_path',
        'original_name',
        'mime_type',
        'file_size',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    /**
     * Get the vehicle that owns the image.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the full URL for the image.
     */
    public function getUrlAttribute()
    {
        if (empty($this->image_path)) {
            return null;
        }
        
        // Use Storage::url() which properly handles the storage link
        // The image_path is stored as 'vehicles/images/filename.jpg'
        // Storage::url() will generate the correct URL through the storage link
        return Storage::url($this->image_path);
    }

    /**
     * Get the thumbnail URL for the image.
     */
    public function getThumbnailUrlAttribute()
    {
        // For now, return the original image URL since we don't have GD extension
        // In production with GD extension, you would create proper thumbnails
        return $this->url;
    }

    /**
     * Scope for primary images
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope for ordering images
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }
}