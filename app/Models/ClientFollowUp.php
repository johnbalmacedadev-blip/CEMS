<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientFollowUp extends Model
{
    use HasFactory;

    protected $table = 'client_follow_up_list';

    protected $fillable = [
        'date_of_first_inquiry',
        'application',
        'client_name',
        'contact_number',
        'email',
        'vehicle_id',
        'unit_inquired',
        'notes',
        'about_what',
        'sales_exec_1', 'date_followed_up_1', 'outcome_1', 'notes_1',
        'sales_exec_2', 'date_followed_up_2', 'outcome_2', 'notes_2',
        'sales_exec_3', 'date_followed_up_3', 'outcome_3', 'notes_3',
        'sales_exec_4', 'date_followed_up_4', 'outcome_4', 'notes_4',
        'follow_up_date',
        'status',
    ];

    protected $casts = [
        'date_of_first_inquiry' => 'date',
        'follow_up_date' => 'date',
        'date_followed_up_1' => 'date',
        'date_followed_up_2' => 'date',
        'date_followed_up_3' => 'date',
        'date_followed_up_4' => 'date',
    ];

    public static function applicationOptions(): array
    {
        return ['FB', 'IG', 'TIKTOK', 'EMAIL', 'PHONE', 'WALK-IN'];
    }

    const STATUS_PENDING = 'Pending';
    const STATUS_CONTACTED = 'Contacted';
    const STATUS_IN_PROGRESS = 'In Progress';
    const STATUS_CLOSED = 'Closed';

    public static function statusOptions(): array
    {
        return [self::STATUS_PENDING, self::STATUS_CONTACTED, self::STATUS_IN_PROGRESS, self::STATUS_CLOSED];
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
