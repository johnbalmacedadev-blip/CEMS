<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'employee_id',
        'activity_date',
        'activity_type',
        'destination',
        'status',
        'notes',
        'completed_at',
    ];

    protected $casts = [
        'activity_date' => 'date',
        'completed_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'Pending';
    public const STATUS_IN_PROGRESS = 'In Progress';
    public const STATUS_COMPLETED = 'Completed';

    public const TYPE_DELIVERY = 'Delivery';
    public const TYPE_PICKUP = 'Pickup';
    public const TYPE_TRANSFER = 'Transfer';
    public const TYPE_TEST_DRIVE = 'Test Drive';
    public const TYPE_ERRAND = 'Errand';
    public const TYPE_OTHER = 'Other';

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_IN_PROGRESS,
            self::STATUS_COMPLETED,
        ];
    }

    public static function activityTypeOptions(): array
    {
        return [
            self::TYPE_DELIVERY,
            self::TYPE_PICKUP,
            self::TYPE_TRANSFER,
            self::TYPE_TEST_DRIVE,
            self::TYPE_ERRAND,
            self::TYPE_OTHER,
        ];
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
