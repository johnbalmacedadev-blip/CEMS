<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleCustomField extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'section_name',
        'field_name',
        'field_label',
        'field_type',
        'field_value',
        'field_options',
        'is_required',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'field_options' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getFieldOptionsAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function setFieldOptionsAttribute($value)
    {
        $this->attributes['field_options'] = $value ? json_encode($value) : null;
    }

    // Scope for specific sections
    public function scopeForSection($query, $sectionName)
    {
        return $query->where('section_name', $sectionName);
    }

    // Scope for active fields
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
