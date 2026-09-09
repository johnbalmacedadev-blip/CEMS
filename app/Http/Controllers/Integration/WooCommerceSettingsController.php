<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Models\WooCommerceSetting;
use App\Support\WooCommerceClient;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use RuntimeException;

class WooCommerceSettingsController extends Controller
{
    use LogsActivity;

    public function edit()
    {
        $settings = WooCommerceSetting::current();

        return view('integration.woocommerce.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'store_url' => ['required', 'url', 'max:255'],
            'consumer_key' => ['nullable', 'string', 'max:255'],
            'consumer_secret' => ['nullable', 'string', 'max:255'],
            'is_enabled' => ['nullable', 'boolean'],
        ]);

        $settings = WooCommerceSetting::current();
        $payload = [
            'store_url' => rtrim((string) $validated['store_url'], '/'),
            'is_enabled' => $request->boolean('is_enabled'),
        ];

        // Keep existing encrypted keys when fields are left blank.
        if (filled($validated['consumer_key'] ?? null)) {
            $payload['consumer_key'] = $validated['consumer_key'];
        }
        if (filled($validated['consumer_secret'] ?? null)) {
            $payload['consumer_secret'] = $validated['consumer_secret'];
        }

        $settings->fill($payload)->save();
        $this->logUpdate($settings, null, 'Updated WooCommerce connection settings');

        return redirect()
            ->route('integration.woocommerce.settings')
            ->with('success', 'WooCommerce connection saved.');
    }

    public function test(Request $request)
    {
        $settings = WooCommerceSetting::current();

        try {
            $result = WooCommerceClient::fromSettings($settings)->testConnection();
            $settings->forceFill([
                'last_tested_at' => now(),
                'last_test_status' => $result['ok'] ? 'ok' : 'failed',
                'last_test_message' => $result['message'],
            ])->save();

            return redirect()
                ->route('integration.woocommerce.settings')
                ->with($result['ok'] ? 'success' : 'error', $result['message']);
        } catch (RuntimeException $e) {
            $settings->forceFill([
                'last_tested_at' => now(),
                'last_test_status' => 'failed',
                'last_test_message' => $e->getMessage(),
            ])->save();

            return redirect()
                ->route('integration.woocommerce.settings')
                ->with('error', $e->getMessage());
        }
    }
}
