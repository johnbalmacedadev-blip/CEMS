<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_added_to_schedule',
        'added_by',
        'customer_first_name',
        'customer_last_name',
        'customer_phone_number',
        'showroom',
        'date_of_visit',
        'preferred_unit',
        'vehicle_id',
        'notes',
        'sales_exec_who_assisted',
        'outcome',
        'notes_of_visit',
    ];

    protected $casts = [
        'date_added_to_schedule' => 'date',
        'date_of_visit' => 'date',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function getCustomerFullNameAttribute(): string
    {
        return trim($this->customer_first_name . ' ' . $this->customer_last_name);
    }
}
