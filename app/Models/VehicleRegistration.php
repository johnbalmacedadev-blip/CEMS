<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_location_id',
        'date',
        'vehicle_id',
        'renewal_reg_or',
        'renewal_sop',
        'smoke_na',
        'duplicate_plate',
        'migrate',
        'duplicate_cr',
        'pnp_clearance',
        'confirmation',
        'remarks',
        'coc_no',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'renewal_reg_or' => 'decimal:2',
        'renewal_sop' => 'decimal:2',
        'smoke_na' => 'decimal:2',
        'duplicate_plate' => 'decimal:2',
        'migrate' => 'decimal:2',
        'duplicate_cr' => 'decimal:2',
        'pnp_clearance' => 'decimal:2',
        'confirmation' => 'decimal:2',
    ];

    public static function statusSuggestions(): array
    {
        return ['Pending', 'In Progress', 'DONE', 'DONE NA'];
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function branchLocation()
    {
        return $this->belongsTo(BranchLocation::class);
    }

    public function feeTotal(): float
    {
        return (float) ($this->renewal_reg_or ?? 0)
            + (float) ($this->renewal_sop ?? 0)
            + (float) ($this->smoke_na ?? 0)
            + (float) ($this->duplicate_plate ?? 0)
            + (float) ($this->migrate ?? 0)
            + (float) ($this->duplicate_cr ?? 0)
            + (float) ($this->pnp_clearance ?? 0)
            + (float) ($this->confirmation ?? 0);
    }
}
