<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WooCommerceSetting extends Model
{
    protected $table = 'woocommerce_settings';

    protected $fillable = [
        'store_url',
        'consumer_key',
        'consumer_secret',
        'is_enabled',
        'last_tested_at',
        'last_test_status',
        'last_test_message',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'last_tested_at' => 'datetime',
        'consumer_key' => 'encrypted',
        'consumer_secret' => 'encrypted',
    ];

    public static function current(): self
    {
        return static::query()->first() ?? static::query()->create([]);
    }

    public function isConfigured(): bool
    {
        return $this->is_enabled
            && filled($this->store_url)
            && filled($this->consumer_key)
            && filled($this->consumer_secret);
    }

    public function normalizedStoreUrl(): string
    {
        return rtrim((string) $this->store_url, '/');
    }
}
