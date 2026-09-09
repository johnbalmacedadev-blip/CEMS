<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MechanicExpenseRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'record_type',
        'description',
        'amount',
        'repaired_by',
        'unit_label',
        'expense_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function scopeParts($query)
    {
        return $query->where('record_type', 'parts');
    }

    public function scopeExternal($query)
    {
        return $query->where('record_type', 'external');
    }
}
