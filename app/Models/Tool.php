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
    ];

    protected $casts = [
        'date_acquired' => 'date',
        'quantity' => 'integer',
        'amount' => 'decimal:2',
    ];

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