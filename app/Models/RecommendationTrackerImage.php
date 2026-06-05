<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class RecommendationTrackerImage extends Model
{
    use HasFactory;

    protected $fillable = ['recommendation_tracker_id', 'file_path', 'original_name', 'sort_order'];

    public function recommendationTracker()
    {
        return $this->belongsTo(RecommendationTracker::class);
    }

    /**
     * URL for display (public disk).
     */
    public function getUrlAttribute(): string
    {
        return Storage::url($this->file_path);
    }
}
