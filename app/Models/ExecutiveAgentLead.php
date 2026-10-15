<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExecutiveAgentLead extends Model
{
    public $incrementing = false;

    protected $fillable = [
        'id',
        'executive_agent_id',
        'application_date',
        'application_source',
        'customer_name',
        'customer_phone',
        'unit_inquired',
        'notes',
        'about_what',
        'followup_1_exec',
        'followup_1_date',
        'followup_1_outcome',
        'followup_1_notes',
        'followup_2_exec',
        'followup_2_date',
        'followup_2_outcome',
        'followup_2_notes',
        'followup_3_exec',
        'followup_3_date',
        'followup_3_outcome',
        'followup_3_notes',
        'followup_4_exec',
        'followup_4_date',
        'followup_4_outcome',
        'followup_4_notes',
        'source_sheet',
        'source_row',
    ];

    protected $casts = [
        'application_date' => 'date',
        'followup_1_date' => 'date',
        'followup_2_date' => 'date',
        'followup_3_date' => 'date',
        'followup_4_date' => 'date',
    ];

    public function executiveAgent(): BelongsTo
    {
        return $this->belongsTo(ExecutiveAgent::class);
    }
}
