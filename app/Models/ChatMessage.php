<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ChatMessage extends Model
{
    protected $fillable = [
        'user_id',
        'body',
        'link_url',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachmentUrl(): ?string
    {
        if (!$this->attachment_path) {
            return null;
        }

        return Storage::disk('public')->url($this->attachment_path);
    }

    public function toChatArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_name' => $this->user?->name ?? 'Unknown',
            'user_initials' => self::initials($this->user?->name ?? '?'),
            'body' => $this->body,
            'link_url' => $this->link_url,
            'attachment_url' => $this->attachmentUrl(),
            'attachment_name' => $this->attachment_name,
            'attachment_mime' => $this->attachment_mime,
            'is_image' => $this->attachment_mime && str_starts_with($this->attachment_mime, 'image/'),
            'created_at' => $this->created_at?->toIso8601String(),
            'created_label' => $this->created_at?->format('M j, g:i A'),
        ];
    }

    public static function initials(string $name): string
    {
        $parts = preg_split('/\s+/', trim($name)) ?: [];
        $initials = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
        }

        return $initials ?: '?';
    }
}
