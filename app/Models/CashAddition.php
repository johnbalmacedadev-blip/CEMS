<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashAddition extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_method_id',
        'addition_date',
        'amount',
        'description',
    ];

    protected $casts = [
        'addition_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the payment method that owns this cash addition.
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
