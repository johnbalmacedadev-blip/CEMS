<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleModel extends Model
{
    use HasFactory;

    protected $table = 'models';

    protected $fillable = [
        'make_id',
        'name',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the make that owns the model.
     */
    public function make()
    {
        return $this->belongsTo(Make::class);
    }

    /**
     * Get the vehicles for the model.
     */
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'model_id');
    }

    /**
     * Scope for active models
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the full name (Make + Model)
     */
    public function getFullNameAttribute()
    {
        return $this->make->name . ' ' . $this->name;
    }
}





















