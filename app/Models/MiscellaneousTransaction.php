<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MiscellaneousTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'amount',
        'transaction_date',
        'location',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    /** @return list<string> */
    public static function locationOptions(): array
    {
        return ['Flagship', 'Annex'];
    }
}
