<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number',
        'po_date',
        'vendor',
        'description',
        'amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'po_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public const STATUS_PENDING = 'Pending';
    public const STATUS_ORDERED = 'Ordered';
    public const STATUS_RECEIVED = 'Received';
    public const STATUS_CANCELLED = 'Cancelled';

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_ORDERED,
            self::STATUS_RECEIVED,
            self::STATUS_CANCELLED,
        ];
    }
}
