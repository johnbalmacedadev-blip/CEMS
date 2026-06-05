<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleExpenseCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    /**
     * Get all categories ordered by name
     */
    public static function getAllOrdered()
    {
        return static::orderBy('name')->get();
    }
}















