<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

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
    ];

    public static function current(): self
    {
        return static::query()->first() ?? static::query()->create([]);
    }

    public function getConsumerKeyAttribute(?string $value): ?string
    {
        return $this->safeDecrypt($value);
    }

    public function setConsumerKeyAttribute(?string $value): void
    {
        $this->attributes['consumer_key'] = $this->safeEncrypt($value);
    }

    public function getConsumerSecretAttribute(?string $value): ?string
    {
        return $this->safeDecrypt($value);
    }

    public function setConsumerSecretAttribute(?string $value): void
    {
        $this->attributes['consumer_secret'] = $this->safeEncrypt($value);
    }

    /**
     * True when a ciphertext is stored (even if it cannot be decrypted with the current APP_KEY).
     */
    public function hasStoredConsumerKey(): bool
    {
        return filled($this->attributes['consumer_key'] ?? null);
    }

    public function hasStoredConsumerSecret(): bool
    {
        return filled($this->attributes['consumer_secret'] ?? null);
    }

    /**
     * Drop credentials that cannot be decrypted with the current APP_KEY
     * (common after deploy when APP_KEY differs from the key that wrote them).
     */
    public function clearUndecryptableCredentials(): bool
    {
        $changed = false;

        foreach (['consumer_key', 'consumer_secret'] as $field) {
            $raw = $this->attributes[$field] ?? null;
            if (! filled($raw)) {
                continue;
            }
            if ($this->safeDecrypt((string) $raw) !== null) {
                continue;
            }
            $this->attributes[$field] = null;
            $changed = true;
        }

        if ($changed) {
            $this->saveQuietly();
            Log::warning('Cleared WooCommerce credentials that could not be decrypted with the current APP_KEY.');
        }

        return $changed;
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

    protected function safeEncrypt(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        // Match Laravel's default `encrypted` cast (serialize + encrypt).
        return Crypt::encrypt($value);
    }

    protected function safeDecrypt(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            $decrypted = Crypt::decrypt($value);

            return is_string($decrypted) ? $decrypted : (is_scalar($decrypted) ? (string) $decrypted : null);
        } catch (DecryptException $e) {
            try {
                return Crypt::decryptString($value);
            } catch (\Throwable $ignored) {
                return null;
            }
        } catch (\Throwable $e) {
            return null;
        }
    }
}
