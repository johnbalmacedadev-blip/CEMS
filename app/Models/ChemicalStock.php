<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChemicalStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'location',
        'item',
        'existing_count',
        'count_date',
        'counted_by',
        'location_stored',
        'verified_by_photo',
    ];

    protected $casts = [
        'existing_count' => 'integer',
        'count_date' => 'date',
    ];

    public static function locationOptions(): array
    {
        return ['Premium', 'Annex'];
    }
}
