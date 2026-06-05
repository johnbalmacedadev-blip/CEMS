<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowUpDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'vehicle_id',
        'due_date',
        'status',
        'priority',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    const STATUS_PENDING = 'Pending';
    const STATUS_IN_PROGRESS = 'In Progress';
    const STATUS_COMPLETED = 'Completed';

    const PRIORITY_LOW = 'Low';
    const PRIORITY_MEDIUM = 'Medium';
    const PRIORITY_HIGH = 'High';

    public static function statusOptions(): array
    {
        return [self::STATUS_PENDING, self::STATUS_IN_PROGRESS, self::STATUS_COMPLETED];
    }

    public static function priorityOptions(): array
    {
        return [self::PRIORITY_LOW, self::PRIORITY_MEDIUM, self::PRIORITY_HIGH];
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
