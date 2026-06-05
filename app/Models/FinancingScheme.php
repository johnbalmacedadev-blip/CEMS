<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancingScheme extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'sort_order'];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function carFinancingSettings()
    {
        return $this->hasMany(CarFinancingSetting::class, 'financing_scheme_id');
    }
}
