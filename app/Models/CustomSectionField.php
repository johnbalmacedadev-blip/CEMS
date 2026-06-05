<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomSectionField extends Model
{
    use HasFactory;

    protected $fillable = [
        'custom_section_id',
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

    public function customSection()
    {
        return $this->belongsTo(CustomSection::class);
    }

    public function getFieldOptionsAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function setFieldOptionsAttribute($value)
    {
        $this->attributes['field_options'] = $value ? json_encode($value) : null;
    }
}
