<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDeduction extends Model
{
    protected $fillable = [
        'employee_id',
        'year',
        'period_key',
        'period_label',
        'category',
        'ee_share',
        'er_share',
        'first_name',
        'middle_name',
        'last_name',
        'source_sheet',
    ];

    protected $casts = [
        'year' => 'integer',
        'ee_share' => 'decimal:2',
        'er_share' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getTotalShareAttribute(): float
    {
        return (float) $this->ee_share + (float) $this->er_share;
    }
}
