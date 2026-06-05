<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'contract_type',
        'vehicle_id',
        'employee_id',
        'party_name',
        'start_date',
        'end_date',
        'description',
        'file_path',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    const TYPE_EMPLOYMENT = 'Employment';
    const TYPE_VENDOR = 'Vendor';
    const TYPE_LEASE = 'Lease';
    const TYPE_OTHER = 'Other';

    const STATUS_ACTIVE = 'Active';
    const STATUS_EXPIRED = 'Expired';
    const STATUS_TERMINATED = 'Terminated';

    public static function typeOptions(): array
    {
        return [self::TYPE_EMPLOYMENT, self::TYPE_VENDOR, self::TYPE_LEASE, self::TYPE_OTHER];
    }

    public static function statusOptions(): array
    {
        return [self::STATUS_ACTIVE, self::STATUS_EXPIRED, self::STATUS_TERMINATED];
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_path ? Storage::disk('public')->url($this->file_path) : null;
    }

    public function getHasFileAttribute(): bool
    {
        return !empty($this->file_path) && Storage::disk('public')->exists($this->file_path);
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
