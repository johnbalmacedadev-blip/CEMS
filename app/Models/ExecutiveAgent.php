<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExecutiveAgent extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'executive_code',
        'department',
        'status',
        'notes',
    ];

    public function salesAgents(): HasMany
    {
        return $this->hasMany(SalesAgent::class, 'executive_agent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
