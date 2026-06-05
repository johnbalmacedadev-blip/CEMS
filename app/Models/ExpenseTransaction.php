<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_date',
        'starting_cash',
        'added_cash',
        'total_cash',
        'total_expense',
        'cash_remaining',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'starting_cash' => 'decimal:2',
        'added_cash' => 'decimal:2',
        'total_cash' => 'decimal:2',
        'total_expense' => 'decimal:2',
        'cash_remaining' => 'decimal:2',
    ];

    /**
     * Get the expense items for this transaction.
     */
    public function expenseItems()
    {
        return $this->hasMany(ExpenseItem::class);
    }

    /**
     * Get formatted total cash
     */
    public function getFormattedTotalCashAttribute()
    {
        return '₱' . number_format($this->total_cash, 2);
    }

    /**
     * Get formatted total expense
     */
    public function getFormattedTotalExpenseAttribute()
    {
        return '₱' . number_format($this->total_expense, 2);
    }

    /**
     * Get formatted cash remaining
     */
    public function getFormattedCashRemainingAttribute()
    {
        return '₱' . number_format($this->cash_remaining, 2);
    }
}
