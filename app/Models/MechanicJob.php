<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MechanicJob extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_date',
        'job_type',
        'mechanic',
        'category',
        'year_model',
        'plate_number',
        'vehicle_id',
        'endorse',
        'description',
        'labor',
        'parts',
        'parts_cost',
        'status',
        'unit_label',
    ];

    protected $casts = [
        'job_date' => 'date',
        'parts_cost' => 'decimal:2',
    ];

    public const TYPE_INTERNAL = 'Internal';
    public const TYPE_EXTERNAL = 'External';

    public const STATUS_PENDING = 'Pending';
    public const STATUS_ONGOING = 'Ongoing';
    public const STATUS_COMPLETE = 'Complete';
    public const STATUS_NOT_STATED = 'Not Stated';
    public const STATUS_TRANSFERRED = 'Transferred';

    public static function jobTypeOptions(): array
    {
        return [self::TYPE_INTERNAL, self::TYPE_EXTERNAL];
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_ONGOING,
            self::STATUS_COMPLETE,
            self::STATUS_NOT_STATED,
            self::STATUS_TRANSFERRED,
        ];
    }

    public static function categoryOptions(): array
    {
        return ['Mechanical', 'Electrical', 'Other'];
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
