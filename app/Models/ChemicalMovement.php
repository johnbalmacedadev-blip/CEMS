<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChemicalMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'location',
        'period_label',
        'item',
        'brand',
        'size_or_pieces',
        'movement_date',
        'movement_type',
        'quantity_text',
        'quantity',
        'moved_by',
        'remaining_count',
    ];

    protected $casts = [
        'movement_date' => 'date',
        'quantity' => 'integer',
        'remaining_count' => 'integer',
    ];
}
