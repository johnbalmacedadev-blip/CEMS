<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitMasterlist extends Model
{
    protected $table = 'units_masterlist';

    protected $fillable = [
        'list_number',
        'make_model',
        'plate_number',
        'variant',
        'transmission',
        'fuel_type',
        'year',
        'mileage',
        'price',
        'low_down_payment_option',
        'low_monthly_option',
        'vehicle_id',
        'notes',
    ];

    protected $casts = [
        'list_number' => 'integer',
        'mileage' => 'integer',
        'price' => 'decimal:2',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public static function normalizePlate(?string $plate): string
    {
        return strtoupper(preg_replace('/[\s\-]+/', '', (string) $plate) ?? '');
    }
}
