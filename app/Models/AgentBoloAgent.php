<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentBoloAgent extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sales_executive',
        'contact_number',
        'email',
        'facebook_profile_link',
        'facebook_page_link',
        'signed_bolo',
        'one_valid_id',
        'joined_sales_associate_gc',
        'notes',
    ];

    protected $casts = [
        'joined_sales_associate_gc' => 'date',
    ];

    public function documents()
    {
        return $this->hasMany(CompanyDocument::class, 'agent_bolo_agent_id')
            ->where('type', CompanyDocument::TYPE_AGENT_BOLO)
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc');
    }
}
