<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoaManualEntry extends Model
{
    public const EXPENSE_BUDGET_TIER_FLAGSHIP = 'flagship';

    public const EXPENSE_BUDGET_TIER_WAREHOUSE = 'warehouse';

    public const EXPENSE_BUDGET_TIER_ANNEX = 'annex';

    /** @var list<string> */
    public const EXPENSE_BUDGET_TIERS = [
        self::EXPENSE_BUDGET_TIER_FLAGSHIP,
        self::EXPENSE_BUDGET_TIER_WAREHOUSE,
        self::EXPENSE_BUDGET_TIER_ANNEX,
    ];

    protected $fillable = [
        'payment_method_id',
        'entry_date',
        'description',
        'debit',
        'credit',
        'is_carry_over',
        'is_expense_budget',
        'expense_budget_tier',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'is_carry_over' => 'boolean',
        'is_expense_budget' => 'boolean',
    ];

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
