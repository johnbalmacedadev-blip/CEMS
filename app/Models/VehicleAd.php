<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleAd extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'posted_date',
        'video_link',
        'video_links',
        'social_media_post_link',
        'social_media_links',
        'ads_boost_link',
        'campaign_id',
        'ad_id',
    ];

    protected $casts = [
        'posted_date' => 'date',
        'video_links' => 'array',
        'social_media_links' => 'array',
    ];

    public static function socialChannelOptions(): array
    {
        return [
            'Facebook',
            'Instagram',
            'TikTok',
            'YouTube',
            'X (Twitter)',
            'Other',
        ];
    }

    /**
     * Normalized list of video URLs (supports legacy single column).
     */
    public function getVideoLinksListAttribute(): array
    {
        $links = array_values(array_filter($this->video_links ?? [], fn ($url) => is_string($url) && trim($url) !== ''));

        if ($links !== []) {
            return $links;
        }

        return $this->video_link ? [$this->video_link] : [];
    }

    /**
     * Normalized list of social posts: [['channel' => '...', 'link' => '...'], ...]
     */
    public function getSocialMediaLinksListAttribute(): array
    {
        $links = collect($this->social_media_links ?? [])
            ->filter(fn ($item) => is_array($item) && ! empty($item['link']))
            ->map(fn ($item) => [
                'channel' => $item['channel'] ?? 'Other',
                'link' => $item['link'],
            ])
            ->values()
            ->all();

        if ($links !== []) {
            return $links;
        }

        if ($this->social_media_post_link) {
            return [['channel' => 'Other', 'link' => $this->social_media_post_link]];
        }

        return [];
    }

    /**
     * Sanitize and normalize link arrays from request input.
     */
    public static function normalizeVideoLinks(?array $links): array
    {
        return array_values(array_filter(array_map(
            fn ($url) => is_string($url) ? trim($url) : '',
            $links ?? []
        )));
    }

    public static function normalizeSocialMediaLinks(?array $links): array
    {
        $allowed = self::socialChannelOptions();

        return collect($links ?? [])
            ->filter(fn ($item) => is_array($item) && ! empty(trim($item['link'] ?? '')))
            ->map(function ($item) use ($allowed) {
                $channel = $item['channel'] ?? 'Other';

                return [
                    'channel' => in_array($channel, $allowed, true) ? $channel : 'Other',
                    'link' => trim($item['link']),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Keep legacy single-link columns in sync for older exports/queries.
     */
    public function syncLegacyLinkColumns(): void
    {
        $this->video_link = $this->video_links_list[0] ?? null;
        $this->social_media_post_link = $this->social_media_links_list[0]['link'] ?? null;
    }

    protected static function booted(): void
    {
        static::saving(function (VehicleAd $ad) {
            $ad->video_links = self::normalizeVideoLinks($ad->video_links);
            $ad->social_media_links = self::normalizeSocialMediaLinks($ad->social_media_links);
            $ad->syncLegacyLinkColumns();
        });
    }

    /**
     * Get the vehicle that owns this ad.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}
