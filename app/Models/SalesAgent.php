<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesAgent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'sales_agent_id',
        'executive_agent_id',
        'department',
        'position',
        'hire_date',
        'commission_rate',
        'commission_type',
        'commission_fixed_amount',
        'base_salary',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'status',
        'notes'
    ];

    protected $casts = [
        'hire_date' => 'date',
        'commission_rate' => 'decimal:2',
        'commission_fixed_amount' => 'decimal:2',
        'base_salary' => 'decimal:2',
    ];

    public const COMMISSION_PERCENTAGE = 'percentage';
    public const COMMISSION_FIXED_RATE = 'fixed_rate';
    public const COMMISSION_CUSTOM = 'custom';

    // Accessor for formatted commission (percentage, fixed peso, or custom)
    public function getFormattedCommissionRateAttribute(): string
    {
        $type = $this->commission_type;
        if ($type === self::COMMISSION_CUSTOM) {
            return 'Custom';
        }
        if ($type === self::COMMISSION_FIXED_RATE) {
            $amt = $this->commission_fixed_amount;
            return $amt !== null && (float) $amt > 0
                ? 'Fixed: ₱' . number_format((float) $amt, 2)
                : 'Fixed rate';
        }

        return number_format((float) $this->commission_rate, 2) . '%';
    }

    // Accessor for formatted base salary
    public function getFormattedBaseSalaryAttribute()
    {
        return $this->base_salary ? '₱' . number_format($this->base_salary, 2) : 'N/A';
    }

    // Accessor for formatted hire date
    public function getFormattedHireDateAttribute()
    {
        return $this->hire_date ? $this->hire_date->format('M d, Y') : 'N/A';
    }

    // Scope for active agents
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope for inactive agents
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function executiveAgent(): BelongsTo
    {
        return $this->belongsTo(ExecutiveAgent::class, 'executive_agent_id');
    }

    public function commissions(): HasMany
    {
        return $this->hasMany(SalesAgentCommission::class, 'sales_agent_id');
    }

    /**
     * Total commission earnings (linked rows, or legacy rows matched by agent name).
     * Optional date range uses payment date when set, else reservation date, else record date (date only).
     */
    public function totalCommissionEarnings(?Carbon $rangeStart = null, ?Carbon $rangeEnd = null): float
    {
        $query = SalesAgentCommission::query()
            ->where(function ($q) {
                $q->where('sales_agent_id', $this->id)
                    ->orWhere(function ($q2) {
                        $q2->whereNull('sales_agent_id')
                            ->where('agent_name', $this->name);
                    });
            });

        if ($rangeStart !== null && $rangeEnd !== null) {
            $query->whereRaw(
                'COALESCE(date_of_payment, date_sent, DATE(created_at)) BETWEEN ? AND ?',
                [$rangeStart->toDateString(), $rangeEnd->toDateString()]
            );
        }

        return (float) $query->sum('amount');
    }

    /**
     * Total sales executive commission on this agent’s payouts (linked id or legacy name), same scope as {@see totalCommissionEarnings()}.
     */
    public function totalExecutiveCommissionShare(?Carbon $rangeStart = null, ?Carbon $rangeEnd = null): float
    {
        $query = SalesAgentCommission::query()
            ->where(function ($q) {
                $q->where('sales_agent_id', $this->id)
                    ->orWhere(function ($q2) {
                        $q2->whereNull('sales_agent_id')
                            ->where('agent_name', $this->name);
                    });
            });

        if ($rangeStart !== null && $rangeEnd !== null) {
            $query->whereRaw(
                'COALESCE(date_of_payment, date_sent, DATE(created_at)) BETWEEN ? AND ?',
                [$rangeStart->toDateString(), $rangeEnd->toDateString()]
            );
        }

        return (float) $query->sum('sales_executive_commission');
    }
}