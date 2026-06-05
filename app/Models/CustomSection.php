<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'title',
        'description',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function fields()
    {
        return $this->hasMany(CustomSectionField::class)->orderBy('sort_order');
    }

    public function activeFields()
    {
        return $this->hasMany(CustomSectionField::class)->where('is_active', true)->orderBy('sort_order');
    }
}
