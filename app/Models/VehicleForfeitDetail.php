<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleForfeitDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'previous_forfeit_date',
        'forfeit_amount',
        'forfeit_date',
    ];

    protected $casts = [
        'previous_forfeit_date' => 'date',
        'forfeit_date' => 'date',
        'forfeit_amount' => 'decimal:2',
    ];

    /**
     * Get the vehicle that owns this forfeit detail.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
