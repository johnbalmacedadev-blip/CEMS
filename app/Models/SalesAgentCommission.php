<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesAgentCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'showroom',
        'commission_status',
        'sales_agent_id',
        'agent_name',
        'client_name',
        'unit',
        'vehicle_id',
        'plate_number',
        'transaction_type',
        'release_date',
        'amount',
        'agents_folder_amount',
        'sales_executive_commission',
        'proof_of_appointment',
        'sign_client_with_agent',
        'date_sent',
        'date_of_payment',
        'notes',
    ];

    protected $casts = [
        'release_date' => 'date',
        'date_sent' => 'date',
        'date_of_payment' => 'date',
        'amount' => 'decimal:2',
        'agents_folder_amount' => 'decimal:2',
        'sales_executive_commission' => 'decimal:2',
        'proof_of_appointment' => 'boolean',
        'sign_client_with_agent' => 'boolean',
    ];

    const TRANSACTION_CASH = 'CASH';
    const TRANSACTION_FINANCING = 'FINANCING';
    const STATUS_PENDING = 'Pending';
    const STATUS_POSTED = 'Posted';

    public static function transactionTypeOptions(): array
    {
        return [self::TRANSACTION_CASH, self::TRANSACTION_FINANCING];
    }

    public static function commissionStatusOptions(): array
    {
        return [self::STATUS_PENDING, self::STATUS_POSTED];
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function salesAgent()
    {
        return $this->belongsTo(SalesAgent::class, 'sales_agent_id');
    }

    /**
     * Sales agent who earned the commission (e.g. Sarah Yao) — linked record or legacy agent_name.
     */
    public function getSalesAgentDisplayAttribute(): string
    {
        $agent = $this->salesAgent;

        return $agent ? $agent->name : ($this->agent_name ?: '—');
    }

    /**
     * Executive over that sales agent (e.g. THYRA), from the linked sales agent’s executive assignment.
     */
    public function getSalesExecutiveDisplayAttribute(): string
    {
        $agent = $this->salesAgent;
        if (! $agent) {
            return '—';
        }

        $exec = $agent->executiveAgent;

        return $exec ? $exec->name : '—';
    }

    public function getProofOfAppointmentLabelAttribute(): string
    {
        return ($this->proof_of_appointment ?? false) ? 'Yes' : 'No';
    }

    public function getSignClientWithAgentLabelAttribute(): string
    {
        return ($this->sign_client_with_agent ?? false) ? 'Yes' : 'No';
    }

    /**
     * Payment date for display; uses date_of_payment when set, otherwise date_sent (legacy).
     */
    public function getDateOfPaymentDisplayAttribute(): ?string
    {
        $d = $this->date_of_payment ?? $this->date_sent;

        return $d ? $d->format('j M Y') : null;
    }

    public function getDisplayUnitAttribute(): string
    {
        if ($this->vehicle_id && $this->vehicle) {
            return $this->vehicle->full_name;
        }
        return $this->unit ?? '—';
    }
}
