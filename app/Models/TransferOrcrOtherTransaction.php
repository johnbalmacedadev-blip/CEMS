<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransferOrcrOtherTransaction extends Model
{
    protected $fillable = [
        'transfer_orcr_id',
        'description',
        'amount',
        'paid',
        'paid_date',
        'sort_order',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid' => 'boolean',
        'paid_date' => 'date',
    ];

    public function transferOrcr(): BelongsTo
    {
        return $this->belongsTo(TransferOrcr::class);
    }
}
