<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SoaFloatedFund extends Model
{
    protected $fillable = [
        'payment_method_id',
        'budget_date',
        'reference_date',
        'yesterday_closing_balance',
        'declared_starting_balance',
        'difference_amount',
    ];

    protected $casts = [
        'budget_date' => 'date',
        'reference_date' => 'date',
        'yesterday_closing_balance' => 'decimal:2',
        'declared_starting_balance' => 'decimal:2',
        'difference_amount' => 'decimal:2',
    ];

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
