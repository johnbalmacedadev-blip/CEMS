<?php

namespace App\Support;

use App\Models\WooCommerceSetting;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class WooCommerceClient
{
    public function __construct(protected WooCommerceSetting $settings)
    {
    }

    public static function fromSettings(?WooCommerceSetting $settings = null): self
    {
        $settings ??= WooCommerceSetting::current();

        return new self($settings);
    }

    public function assertConfigured(): void
    {
        if (! $this->settings->isConfigured()) {
            throw new RuntimeException('WooCommerce is not connected. Open INTEGRATION → WooCommerce Connection and save your store URL and API keys.');
        }
    }

    public function testConnection(): array
    {
        $this->assertConfigured();

        $response = $this->request()->get($this->endpoint('system_status'));
        if (! $response->successful()) {
            // Some sites disable system_status; fall back to a lightweight products call.
            $response = $this->request()->get($this->endpoint('products'), ['per_page' => 1]);
        }

        if (! $response->successful()) {
            return [
                'ok' => false,
                'message' => $this->errorMessage($response),
            ];
        }

        $storeName = data_get($response->json(), 'settings.general.woocommerce_email_from_name')
            ?? data_get($response->json(), 'environment.site_url')
            ?? $this->settings->normalizedStoreUrl();

        return [
            'ok' => true,
            'message' => 'Connected successfully to '.$storeName,
        ];
    }

    /**
     * @return array{items: array<int, array>, total: int, total_pages: int, page: int, per_page: int}
     */
    public function listProducts(int $page = 1, int $perPage = 20, string $search = '', string $status = ''): array
    {
        $this->assertConfigured();

        $query = [
            'page' => max(1, $page),
            'per_page' => min(100, max(1, $perPage)),
            'orderby' => 'date',
            'order' => 'desc',
        ];
        if ($search !== '') {
            $query['search'] = $search;
        }
        if ($status !== '' && in_array($status, ['publish', 'draft', 'pending', 'private'], true)) {
            $query['status'] = $status;
        }

        $response = $this->request()->get($this->endpoint('products'), $query);
        $this->throwIfFailed($response, 'Failed to fetch WooCommerce products.');

        return [
            'items' => $response->json() ?? [],
            'total' => (int) $response->header('X-WP-Total', 0),
            'total_pages' => (int) $response->header('X-WP-TotalPages', 1),
            'page' => $query['page'],
            'per_page' => $query['per_page'],
        ];
    }

    public function getProduct(int $id): array
    {
        $this->assertConfigured();
        $response = $this->request()->get($this->endpoint('products/'.$id));
        $this->throwIfFailed($response, 'Failed to load WooCommerce product #'.$id.'.');

        return $response->json() ?? [];
    }

    public function createProduct(array $payload): array
    {
        $this->assertConfigured();
        $response = $this->request()->post($this->endpoint('products'), $payload);
        $this->throwIfFailed($response, 'Failed to create WooCommerce product.');

        return $response->json() ?? [];
    }

    public function updateProduct(int $id, array $payload): array
    {
        $this->assertConfigured();
        $response = $this->request()->put($this->endpoint('products/'.$id), $payload);
        $this->throwIfFailed($response, 'Failed to update WooCommerce product #'.$id.'.');

        return $response->json() ?? [];
    }

    public function deleteProduct(int $id, bool $force = true): array
    {
        $this->assertConfigured();
        $response = $this->request()->delete($this->endpoint('products/'.$id), [
            'force' => $force ? 'true' : 'false',
        ]);
        $this->throwIfFailed($response, 'Failed to delete WooCommerce product #'.$id.'.');

        return $response->json() ?? [];
    }

    protected function request(): PendingRequest
    {
        return Http::withBasicAuth(
            (string) $this->settings->consumer_key,
            (string) $this->settings->consumer_secret
        )
            ->acceptJson()
            ->asJson()
            ->timeout(45)
            ->withOptions([
                'verify' => true,
            ]);
    }

    protected function endpoint(string $path): string
    {
        return $this->settings->normalizedStoreUrl().'/wp-json/wc/v3/'.ltrim($path, '/');
    }

    protected function throwIfFailed(Response $response, string $fallback): void
    {
        if ($response->successful()) {
            return;
        }

        throw new RuntimeException($this->errorMessage($response, $fallback));
    }

    protected function errorMessage(Response $response, string $fallback = 'WooCommerce request failed.'): string
    {
        $body = $response->json();
        $apiMessage = data_get($body, 'message')
            ?? data_get($body, 'data.params')
            ?? null;

        if (is_array($apiMessage)) {
            $apiMessage = collect($apiMessage)->flatten()->filter()->implode('; ');
        }

        $status = $response->status();
        if (is_string($apiMessage) && $apiMessage !== '') {
            return "WooCommerce error ({$status}): {$apiMessage}";
        }

        return "{$fallback} HTTP {$status}.";
    }
}
