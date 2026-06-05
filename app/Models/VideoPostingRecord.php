<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoPostingRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'record_date',
        'type',
        'platform',
        'link_url',
        'vehicle_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'record_date' => 'date',
    ];

    const TYPE_VIDEO = 'Video';
    const TYPE_POST = 'Post';
    const TYPE_BOOST = 'Boost';

    const STATUS_SCHEDULED = 'Scheduled';
    const STATUS_POSTED = 'Posted';
    const STATUS_PENDING = 'Pending';

    public static function typeOptions(): array
    {
        return [self::TYPE_VIDEO, self::TYPE_POST, self::TYPE_BOOST];
    }

    public static function statusOptions(): array
    {
        return [self::STATUS_SCHEDULED, self::STATUS_POSTED, self::STATUS_PENDING];
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
