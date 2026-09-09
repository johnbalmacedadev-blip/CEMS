<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Models\WooCommerceSetting;
use App\Support\WooCommerceClient;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use RuntimeException;

class WooCommerceProductController extends Controller
{
    use LogsActivity;

    public function index(Request $request)
    {
        $settings = WooCommerceSetting::current();
        $page = max(1, (int) $request->get('page', 1));
        $search = trim((string) $request->get('search', ''));
        $status = trim((string) $request->get('status', ''));
        $perPage = 20;
        $error = null;
        $products = collect();
        $paginator = null;

        if (! $settings->isConfigured()) {
            $error = 'WooCommerce is not connected yet. Configure your store URL and API keys first.';
        } else {
            try {
                $result = WooCommerceClient::fromSettings($settings)->listProducts($page, $perPage, $search, $status);
                $products = collect($result['items']);
                $paginator = new LengthAwarePaginator(
                    $products,
                    $result['total'],
                    $result['per_page'],
                    $result['page'],
                    [
                        'path' => route('integration.woocommerce.products.index'),
                        'query' => $request->except('page'),
                    ]
                );
            } catch (RuntimeException $e) {
                $error = $e->getMessage();
            }
        }

        return view('integration.woocommerce.products.index', [
            'settings' => $settings,
            'products' => $products,
            'paginator' => $paginator,
            'error' => $error,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function create()
    {
        if ($redirect = $this->redirectIfNotConfigured()) {
            return $redirect;
        }

        return view('integration.woocommerce.products.create', [
            'product' => [
                'name' => '',
                'type' => 'simple',
                'status' => 'draft',
                'sku' => '',
                'regular_price' => '',
                'sale_price' => '',
                'manage_stock' => false,
                'stock_quantity' => '',
                'stock_status' => 'instock',
                'description' => '',
                'short_description' => '',
            ],
        ]);
    }

    public function store(Request $request)
    {
        if ($redirect = $this->redirectIfNotConfigured()) {
            return $redirect;
        }

        $payload = $this->validatedProductPayload($request);

        try {
            $created = WooCommerceClient::fromSettings()->createProduct($payload);
            $settings = WooCommerceSetting::current();
            $this->logCreate(
                $settings,
                'Created WooCommerce product: '.($created['name'] ?? ('#'.($created['id'] ?? '')))
            );

            return redirect()
                ->route('integration.woocommerce.products.index')
                ->with('success', 'Product created on WooCommerce (#'.($created['id'] ?? '—').').');
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(int $product)
    {
        if ($redirect = $this->redirectIfNotConfigured()) {
            return $redirect;
        }

        try {
            $item = WooCommerceClient::fromSettings()->getProduct($product);
        } catch (RuntimeException $e) {
            return redirect()
                ->route('integration.woocommerce.products.index')
                ->with('error', $e->getMessage());
        }

        return view('integration.woocommerce.products.edit', [
            'productId' => $product,
            'product' => [
                'name' => (string) ($item['name'] ?? ''),
                'type' => (string) ($item['type'] ?? 'simple'),
                'status' => (string) ($item['status'] ?? 'draft'),
                'sku' => (string) ($item['sku'] ?? ''),
                'regular_price' => (string) ($item['regular_price'] ?? ''),
                'sale_price' => (string) ($item['sale_price'] ?? ''),
                'manage_stock' => (bool) ($item['manage_stock'] ?? false),
                'stock_quantity' => $item['stock_quantity'] ?? '',
                'stock_status' => (string) ($item['stock_status'] ?? 'instock'),
                'description' => (string) ($item['description'] ?? ''),
                'short_description' => (string) ($item['short_description'] ?? ''),
                'permalink' => (string) ($item['permalink'] ?? ''),
            ],
        ]);
    }

    public function update(Request $request, int $product)
    {
        if ($redirect = $this->redirectIfNotConfigured()) {
            return $redirect;
        }

        $payload = $this->validatedProductPayload($request);

        try {
            $updated = WooCommerceClient::fromSettings()->updateProduct($product, $payload);
            $this->logUpdate(
                WooCommerceSetting::current(),
                null,
                'Updated WooCommerce product: '.($updated['name'] ?? ('#'.$product))
            );

            return redirect()
                ->route('integration.woocommerce.products.index')
                ->with('success', 'Product updated on WooCommerce (#'.$product.').');
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(int $product)
    {
        if ($redirect = $this->redirectIfNotConfigured()) {
            return $redirect;
        }

        try {
            $deleted = WooCommerceClient::fromSettings()->deleteProduct($product, true);
            $this->logDelete(
                WooCommerceSetting::current(),
                'Deleted WooCommerce product: '.($deleted['name'] ?? ('#'.$product))
            );

            return redirect()
                ->route('integration.woocommerce.products.index')
                ->with('success', 'Product deleted from WooCommerce (#'.$product.').');
        } catch (RuntimeException $e) {
            return redirect()
                ->route('integration.woocommerce.products.index')
                ->with('error', $e->getMessage());
        }
    }

    protected function redirectIfNotConfigured()
    {
        if (WooCommerceSetting::current()->isConfigured()) {
            return null;
        }

        return redirect()
            ->route('integration.woocommerce.settings')
            ->with('error', 'Connect WooCommerce before managing products.');
    }

    protected function validatedProductPayload(Request $request): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:simple,variable,grouped,external'],
            'status' => ['required', 'in:publish,draft,pending,private'],
            'sku' => ['nullable', 'string', 'max:100'],
            'regular_price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'manage_stock' => ['nullable', 'boolean'],
            'stock_quantity' => ['nullable', 'integer'],
            'stock_status' => ['required', 'in:instock,outofstock,onbackorder'],
            'description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string'],
        ]);

        $manageStock = $request->boolean('manage_stock');

        $payload = [
            'name' => $validated['name'],
            'type' => $validated['type'],
            'status' => $validated['status'],
            'sku' => $validated['sku'] ?? '',
            'regular_price' => isset($validated['regular_price']) ? (string) $validated['regular_price'] : '',
            'sale_price' => isset($validated['sale_price']) ? (string) $validated['sale_price'] : '',
            'manage_stock' => $manageStock,
            'stock_status' => $validated['stock_status'],
            'description' => $validated['description'] ?? '',
            'short_description' => $validated['short_description'] ?? '',
        ];

        if ($manageStock) {
            $payload['stock_quantity'] = (int) ($validated['stock_quantity'] ?? 0);
        }

        return $payload;
    }
}
