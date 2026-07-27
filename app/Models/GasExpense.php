<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GasExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'po_number',
        'driver',
        'model',
        'plate_number',
        'gas_amount',
        'expense_sent_by',
        'has_photo_video_in_groupchat',
        'photo_po_slip',
        'photo_fuel_gauge_before',
        'photo_fuel_gauge_after',
        'photo_car_license_plate_gas_boy',
        'photo_receipt_next_to_gas_pump',
        'checked_by',
    ];

    protected $casts = [
        'date' => 'date',
        'gas_amount' => 'decimal:2',
        'has_photo_video_in_groupchat' => 'boolean',
        'photo_po_slip' => 'boolean',
        'photo_fuel_gauge_before' => 'boolean',
        'photo_fuel_gauge_after' => 'boolean',
        'photo_car_license_plate_gas_boy' => 'boolean',
        'photo_receipt_next_to_gas_pump' => 'boolean',
    ];

    /**
     * Get the vehicle that owns the gas expense.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'plate_number', 'plate_number');
    }

    /**
     * Get formatted gas amount.
     */
    public function getFormattedGasAmountAttribute(): string
    {
        return '₱' . number_format($this->gas_amount, 2);
    }
}
