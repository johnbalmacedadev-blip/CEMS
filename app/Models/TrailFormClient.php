<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrailFormClient extends Model
{
    use HasFactory;

    public const STATUS_INQUIRING = 'Inquiring';
    public const STATUS_RESERVATION = 'Reservation';

    protected $fillable = [
        'inquiry_date',
        'client_name',
        'contact_number',
        'email',
        'status',
        'inquiry_source',
        'vehicle_type',
        'vehicle_interest',
        'notes',
    ];

    protected $casts = [
        'inquiry_date' => 'date',
    ];

    public static function statusOptions(): array
    {
        return [self::STATUS_INQUIRING, self::STATUS_RESERVATION];
    }

    public static function inquirySourceOptions(): array
    {
        return [
            'Facebook',
            'Instagram',
            'TikTok',
            'Phone Call',
            'Walk-in',
            'Referral',
            'Website',
            'Email',
            'Other',
        ];
    }

    public static function vehicleTypeOptions(): array
    {
        return [
            'Sedan',
            'SUV',
            'MPV',
            'Pickup',
            'Hatchback',
            'Van',
            'Coupe',
            'Crossover',
            'Other',
        ];
    }
}
