<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SourceScreenshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'screenshot_date',
        'file_path',
        'link_url',
    ];

    protected $casts = [
        'screenshot_date' => 'date',
    ];

    /**
     * Get the full URL for the screenshot file.
     */
    public function getFileUrlAttribute(): ?string
    {
        if (empty($this->file_path)) {
            return null;
        }
        return Storage::url($this->file_path);
    }

    /**
     * Whether this record has an uploaded file.
     */
    public function getHasFileAttribute(): bool
    {
        if (empty($this->file_path)) {
            return false;
        }
        return Storage::disk('public')->exists($this->file_path);
    }
}
