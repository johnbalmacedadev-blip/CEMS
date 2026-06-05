<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyBudget extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_method_id',
        'budget_date',
        'starting_balance',
        'added_cash',
        'notes',
    ];

    protected $casts = [
        'budget_date' => 'date',
        'starting_balance' => 'decimal:2',
        'added_cash' => 'decimal:2',
    ];

    /**
     * Get the payment method that owns this daily budget.
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
