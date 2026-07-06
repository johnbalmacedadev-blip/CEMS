<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CompanyDocument extends Model
{
    use HasFactory;

    public const TYPE_ONLINE_AR_BOLO = 'online_ar_bolo';
    public const TYPE_AGENT_BOLO = 'agent_bolo';
    public const TYPE_AR_TEMPLATE = 'ar_template';
    public const TYPE_MEMO = 'memo';

    protected $fillable = ['type', 'title', 'body', 'file_path', 'link_url', 'sort_order', 'agent_bolo_agent_id'];

    public function agentBoloAgent()
    {
        return $this->belongsTo(AgentBoloAgent::class, 'agent_bolo_agent_id');
    }

    public static function typeLabels(): array
    {
        return [
            self::TYPE_ONLINE_AR_BOLO => 'Online AR BOLO',
            self::TYPE_AGENT_BOLO => 'Agent BOLO',
            self::TYPE_AR_TEMPLATE => 'AR Template',
            self::TYPE_MEMO => 'Memo',
        ];
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeLabels()[$this->type] ?? $this->type;
    }

    public function hasBody(): bool
    {
        return ! empty(trim((string) $this->body));
    }

    public function isFile(): bool
    {
        return ! empty($this->file_path);
    }

    public function isLink(): bool
    {
        return ! empty($this->link_url);
    }

    public function getDisplayUrlAttribute(): ?string
    {
        if ($this->file_path) {
            return Storage::url($this->file_path);
        }
        return $this->link_url;
    }
}
