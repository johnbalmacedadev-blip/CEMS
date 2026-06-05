<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_transaction_id',
        'expense_date',
        'payment_method_id',
        'description',
        'description_details',
        'care_of',
        'requested_by',
        'approved_by',
        'store_shop',
        'receipt_checked',
        'receipt_checker',
        'receipt_check_date',
        'cost',
        'payment_tag',
        'expense_category',
        'vehicle_id',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'expense_date' => 'date',
        'receipt_checked' => 'boolean',
        'receipt_check_date' => 'date',
    ];

    /**
     * Get the expense transaction that owns this item.
     */
    public function expenseTransaction()
    {
        return $this->belongsTo(ExpenseTransaction::class);
    }

    /**
     * Get the vehicle associated with this expense item.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the payment method for this expense item.
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Get the receipts for this expense item.
     */
    public function receipts()
    {
        return $this->hasMany(ExpenseItemReceipt::class)->ordered();
    }

    /**
     * Get formatted cost
     */
    public function getFormattedCostAttribute()
    {
        return '₱' . number_format($this->cost, 2);
    }
}
