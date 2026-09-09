<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    use HasFactory;

    protected $table = 'tools_inventory';

    protected $fillable = [
        'name',
        'quantity',
        'amount',
        'date_acquired',
        'entry_type',
    ];

    protected $casts = [
        'date_acquired' => 'date',
        'quantity' => 'integer',
        'amount' => 'decimal:2',
    ];

    public function scopePurchases($query)
    {
        return $query->where('entry_type', 'purchase');
    }

    public function scopeInventory($query)
    {
        return $query->where('entry_type', 'inventory');
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return '₱' . number_format($this->amount, 2);
    }

    /**
     * Get formatted date acquired
     */
    public function getFormattedDateAcquiredAttribute()
    {
        return $this->date_acquired->format('d-M-y');
    }
}