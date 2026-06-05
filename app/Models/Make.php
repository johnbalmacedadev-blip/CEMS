<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Make extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the models for the make.
     */
    public function models()
    {
        return $this->hasMany(\App\Models\VehicleModel::class);
    }

    /**
     * Get the vehicles for the make.
     */
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * Scope for active makes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}