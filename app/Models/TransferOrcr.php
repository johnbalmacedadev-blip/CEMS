<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferOrcr extends Model
{
    use HasFactory;

    protected $table = 'transfer_orcr';

    protected $fillable = [
        'branch_location_id',
        'date',
        'vehicle_id',
        'transaction_type',
        'remark',
        'release_date',
        'lto_file_no',
        'transfer_sop',
        'transfer_or',
        'others',
        'others_note',
        'notary',
        'pnp_clearance',
        'confirmation',
        'rd',
        'rd_sop',
        'rd_or',
        'renewal_reg_or',
        'renewal_sop',
        'smoke_na',
        'remarks',
        'status',
        'transfer_sop_paid',
        'transfer_sop_paid_date',
        'transfer_or_paid',
        'transfer_or_paid_date',
        'pnp_clearance_paid',
        'pnp_clearance_paid_date',
        'rd_sop_paid',
        'rd_sop_paid_date',
        'rd_or_paid',
        'rd_or_paid_date',
    ];

    protected $casts = [
        'date' => 'date',
        'release_date' => 'date',
        'transfer_sop_paid_date' => 'date',
        'transfer_or_paid_date' => 'date',
        'pnp_clearance_paid_date' => 'date',
        'rd_sop_paid_date' => 'date',
        'rd_or_paid_date' => 'date',
        'transfer_sop' => 'decimal:2',
        'transfer_or' => 'decimal:2',
        'others' => 'decimal:2',
        'notary' => 'decimal:2',
        'pnp_clearance' => 'decimal:2',
        'confirmation' => 'decimal:2',
        'rd_sop' => 'decimal:2',
        'rd_or' => 'decimal:2',
        'renewal_reg_or' => 'decimal:2',
        'renewal_sop' => 'decimal:2',
        'transfer_sop_paid' => 'boolean',
        'transfer_or_paid' => 'boolean',
        'pnp_clearance_paid' => 'boolean',
        'rd_sop_paid' => 'boolean',
        'rd_or_paid' => 'boolean',
    ];

    const STATUS_PENDING = 'Pending';
    const STATUS_IN_PROGRESS = 'In Progress';
    const STATUS_DONE = 'DONE';

    public static function statusOptions(): array
    {
        return [self::STATUS_PENDING, self::STATUS_IN_PROGRESS, self::STATUS_DONE];
    }

    public static function transactionTypeOptions(): array
    {
        return ['ASIALINK', 'BERJAYA', 'RUSH', 'ORICO', 'ORCR'];
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function branchLocation()
    {
        return $this->belongsTo(BranchLocation::class);
    }

    public function otherTransactions()
    {
        return $this->hasMany(TransferOrcrOtherTransaction::class)->orderBy('sort_order');
    }

    /**
     * Sum of all fee columns (matches spreadsheet TOTAL).
     */
    public function feeTotal(): float
    {
        $base = (float) $this->transfer_sop
            + (float) $this->transfer_or
            + (float) ($this->others ?? 0)
            + (float) ($this->notary ?? 0)
            + (float) $this->pnp_clearance
            + (float) ($this->confirmation ?? 0)
            + (float) ($this->rd_sop ?? 0)
            + (float) ($this->rd_or ?? 0)
            + (float) ($this->renewal_reg_or ?? 0)
            + (float) ($this->renewal_sop ?? 0);

        $otherSum = $this->relationLoaded('otherTransactions')
            ? (float) $this->otherTransactions->sum('amount')
            : (float) $this->otherTransactions()->sum('amount');

        return $base + $otherSum;
    }
}
