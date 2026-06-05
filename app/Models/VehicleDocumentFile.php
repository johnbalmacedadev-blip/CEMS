<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class VehicleDocumentFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_document_id',
        'type',
        'file_path',
        'file_name',
        'file_link',
        'sort_order',
    ];

    /**
     * Get the document that owns this file.
     */
    public function vehicleDocument()
    {
        return $this->belongsTo(VehicleDocument::class);
    }

    /**
     * Get the file URL for file type.
     */
    public function getUrlAttribute()
    {
        if ($this->type === 'file' && $this->file_path) {
            return asset('storage/' . $this->file_path);
        }
        return null;
    }

    /**
     * Delete the file from storage when the model is deleted.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($file) {
            if ($file->type === 'file' && $file->file_path) {
                Storage::disk('public')->delete($file->file_path);
            }
        });
    }
}
