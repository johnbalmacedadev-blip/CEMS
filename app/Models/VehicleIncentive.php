<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleIncentive extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'sa_origin',
        'sa_origin_link',
        'sa_origin_file_path',
        'reserved_by',
        'no_look',
        'no_look_link',
        'no_look_file_path',
        'insurance',
        'insurance_link',
        'insurance_file_path',
        'testimonial',
        'testimonial_link',
        'testimonial_file_path',
        'review',
        'review_link',
        'review_file_path',
    ];

    protected $casts = [
        'no_look' => 'boolean',
        'insurance' => 'boolean',
        'testimonial' => 'boolean',
        'review' => 'boolean',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
