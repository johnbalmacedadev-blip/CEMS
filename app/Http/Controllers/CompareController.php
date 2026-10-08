<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\Pool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CompareController extends Controller
{
    public function index(Request $request)
    {
        $links = [];
        $priceSummary = [];
        $searched = false;
        $queryLabel = '';

        if ($request->filled(['year', 'model', 'vehicle_brand'])) {
            $validated = $request->validate([
                'year' => ['required', 'string', 'max:10'],
                'model' => ['required', 'string', 'max:50'],
                'vehicle_brand' => ['required', 'string', 'max:50'],
                'variant' => ['nullable', 'string', 'max:100'],
            ]);

            $year = trim((string) $validated['year']);
            $brand = trim((string) $validated['vehicle_brand']);
            $model = trim((string) $validated['model']);
            $variant = $this->normalizeVariantInput((string) ($validated['variant'] ?? ''));

            $queryLabel = trim($year . ' ' . $brand . ' ' . $model . ($variant !== '' ? ' ' . $variant : ''));
            $links = $this->buildCompetitorLinks($year, $brand, $model, $variant);
            $links = $this->attachAveragePrices($links);
            $priceSummary = $this->buildPriceSummary($links);
            $searched = true;
        }

        return view('compare.index', [
            'links' => $links,
            'priceSummary' => $priceSummary,
            'searched' => $searched,
            'queryLabel' => $queryLabel,
            'year' => (string) $request->get('year', ''),
            'model' => (string) $request->get('model', ''),
            'vehicleBrand' => (string) $request->get('vehicle_brand', ''),
            'variant' => (string) $request->get('variant', ''),
        ]);
    }

    /**
     * Build competitor + search-engine links for the same vehicle details.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function buildCompetitorLinks(string $year, string $brand, string $model, string $variant): array
    {
        $query = trim($year . ' ' . $brand . ' ' . $model . ($variant !== '' ? ' ' . $variant : ''));
        $q = urlencode($query);

        $links = [
            [
                'site' => 'allcarsph.com',
                'label' => 'AllCarsPH',
                'description' => 'Search inventory on AllCarsPH',
                'url' => 'https://allcarsph.com/search?q=' . $q . '&options%5Bprefix%5D=last',
                'type' => 'competitor',
                'fetch_prices' => true,
            ],
            [
                'site' => 'ugartecars.ph',
                'label' => 'Ugarte Cars',
                'description' => 'Search inventory on Ugarte Cars',
                'url' => 'https://ugartecars.ph/inventory/?stm_keywords=' . $q,
                'type' => 'competitor',
                'fetch_prices' => true,
            ],
            [
                'site' => 'carmax.com.ph',
                'label' => 'Carmax PH',
                'description' => 'Search listings on Carmax PH',
                'url' => 'https://carmax.com.ph/cars?search=' . $q,
                'type' => 'competitor',
                'fetch_prices' => true,
            ],
            [
                'site' => 'carmudi.com.ph',
                'label' => 'Carmudi PH',
                'description' => 'Search used cars on Carmudi',
                'url' => 'https://www.carmudi.com.ph/cars-for-sale/?q=' . $q,
                'type' => 'competitor',
                'fetch_prices' => true,
            ],
            [
                'site' => 'facebook.com',
                'label' => 'Facebook Marketplace',
                'description' => 'Marketplace search for this vehicle',
                'url' => 'https://www.facebook.com/marketplace/search/?query=' . $q,
                'type' => 'marketplace',
                'fetch_prices' => false,
            ],
            [
                'site' => 'google.com',
                'label' => 'Google (AllCarsPH)',
                'description' => 'Google results limited to allcarsph.com',
                'url' => 'https://www.google.com/search?q=' . urlencode('site:allcarsph.com ' . $query),
                'type' => 'search',
                'fetch_prices' => false,
            ],
            [
                'site' => 'google.com',
                'label' => 'Google (Ugarte Cars)',
                'description' => 'Google results limited to ugartecars.ph',
                'url' => 'https://www.google.com/search?q=' . urlencode('site:ugartecars.ph ' . $query),
                'type' => 'search',
                'fetch_prices' => false,
            ],
            [
                'site' => 'google.com',
                'label' => 'Google (Carmax PH)',
                'description' => 'Google results limited to carmax.com.ph',
                'url' => 'https://www.google.com/search?q=' . urlencode('site:carmax.com.ph ' . $query),
                'type' => 'search',
                'fetch_prices' => false,
            ],
            [
                'site' => 'google.com',
                'label' => 'Google (Carmudi PH)',
                'description' => 'Google results limited to carmudi.com.ph',
                'url' => 'https://www.google.com/search?q=' . urlencode('site:carmudi.com.ph ' . $query),
                'type' => 'search',
                'fetch_prices' => false,
            ],
            [
                'site' => 'google.com',
                'label' => 'Google (web-wide)',
                'description' => 'General Google search for this year / brand / model',
                'url' => 'https://www.google.com/search?q=' . $q . '+for+sale+philippines',
                'type' => 'search',
                'fetch_prices' => false,
            ],
        ];

        $unique = [];
        $seen = [];
        foreach ($links as $link) {
            $key = md5((string) $link['url']);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $unique[] = $link;
        }

        return $unique;
    }

    /**
     * Fetch competitor search pages in parallel and attach average price stats.
     *
     * @param  array<int, array<string, mixed>>  $links
     * @return array<int, array<string, mixed>>
     */
    protected function attachAveragePrices(array $links): array
    {
        $fetchIndexes = [];
        foreach ($links as $index => $link) {
            if (! empty($link['fetch_prices']) && ($link['type'] ?? '') === 'competitor') {
                $fetchIndexes[] = $index;
            }
        }

        if ($fetchIndexes === []) {
            return $links;
        }

        $headers = [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language' => 'en-US,en;q=0.9',
        ];

        $responses = Http::pool(function (Pool $pool) use ($fetchIndexes, $links, $headers) {
            $requests = [];
            foreach ($fetchIndexes as $index) {
                $url = (string) ($links[$index]['url'] ?? '');
                $timeout = str_contains((string) ($links[$index]['site'] ?? ''), 'ugarte') ? 20 : 14;
                $requests[$index] = $pool->as((string) $index)
                    ->timeout($timeout)
                    ->withHeaders($headers)
                    ->get($url);
            }

            return $requests;
        });

        foreach ($fetchIndexes as $index) {
            $response = $responses[(string) $index] ?? null;
            $stats = [
                'avg_price' => null,
                'avg_price_label' => null,
                'min_price' => null,
                'max_price' => null,
                'price_count' => 0,
                'price_note' => 'No prices found on results page',
            ];

            try {
                if ($response && method_exists($response, 'ok') && $response->ok()) {
                    $prices = $this->extractPricesFromHtml((string) $response->body());
                    if ($prices !== []) {
                        $avg = array_sum($prices) / count($prices);
                        $stats = [
                            'avg_price' => $avg,
                            'avg_price_label' => '₱' . number_format($avg, 0),
                            'min_price' => min($prices),
                            'max_price' => max($prices),
                            'min_price_label' => '₱' . number_format(min($prices), 0),
                            'max_price_label' => '₱' . number_format(max($prices), 0),
                            'price_count' => count($prices),
                            'price_note' => null,
                        ];
                    }
                } else {
                    $stats['price_note'] = 'Could not load results page';
                }
            } catch (\Throwable $e) {
                $stats['price_note'] = 'Could not load results page';
            }

            $links[$index] = array_merge($links[$index], $stats);
        }

        return $links;
    }

    /**
     * @param  array<int, array<string, mixed>>  $links
     * @return array<int, array<string, mixed>>
     */
    protected function buildPriceSummary(array $links): array
    {
        $summary = [];
        foreach ($links as $link) {
            if (($link['type'] ?? '') !== 'competitor') {
                continue;
            }
            $summary[] = [
                'label' => $link['label'] ?? ($link['site'] ?? 'Site'),
                'site' => $link['site'] ?? '',
                'url' => $link['url'] ?? '#',
                'avg_price_label' => $link['avg_price_label'] ?? null,
                'min_price_label' => $link['min_price_label'] ?? null,
                'max_price_label' => $link['max_price_label'] ?? null,
                'price_count' => (int) ($link['price_count'] ?? 0),
                'price_note' => $link['price_note'] ?? null,
            ];
        }

        return $summary;
    }

    /**
     * Pull plausible PHP car prices from a competitor search HTML page.
     *
     * @return array<int, float>
     */
    protected function extractPricesFromHtml(string $html): array
    {
        if ($html === '') {
            return [];
        }

        // Prefer JSON-LD / product price fields when present.
        $prices = [];
        if (preg_match_all('/"price"\s*:\s*"?(?<price>\d+(?:\.\d+)?)"?/i', $html, $jsonMatches)) {
            foreach ($jsonMatches['price'] as $raw) {
                $n = (float) $raw;
                if ($this->isPlausibleCarPrice($n)) {
                    $prices[] = $n;
                }
            }
        }

        $text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;

        if (preg_match_all('/(?:₱|PHP|Php|P)\s*([0-9]{1,3}(?:,[0-9]{3})+(?:\.[0-9]{2})?|[0-9]{5,}(?:\.[0-9]{2})?)/u', $text, $matches)) {
            foreach ($matches[1] as $raw) {
                $n = (float) str_replace(',', '', (string) $raw);
                if ($this->isPlausibleCarPrice($n)) {
                    $prices[] = $n;
                }
            }
        }

        // Fallback: bare large numbers near "price" words.
        if ($prices === [] && preg_match_all('/price[^0-9]{0,20}([0-9]{1,3}(?:,[0-9]{3}){1,3})/iu', $text, $near)) {
            foreach ($near[1] as $raw) {
                $n = (float) str_replace(',', '', (string) $raw);
                if ($this->isPlausibleCarPrice($n)) {
                    $prices[] = $n;
                }
            }
        }

        $prices = array_values(array_unique(array_map(fn ($n) => round((float) $n, 2), $prices)));

        // Cap outliers by IQR-ish clip if we have many samples.
        if (count($prices) >= 6) {
            sort($prices);
            $q1 = $prices[(int) floor((count($prices) - 1) * 0.25)];
            $q3 = $prices[(int) floor((count($prices) - 1) * 0.75)];
            $iqr = max($q3 - $q1, 1);
            $low = $q1 - (1.5 * $iqr);
            $high = $q3 + (1.5 * $iqr);
            $prices = array_values(array_filter($prices, fn ($n) => $n >= $low && $n <= $high));
        }

        return $prices;
    }

    protected function isPlausibleCarPrice(float $amount): bool
    {
        return $amount >= 80000 && $amount <= 25000000;
    }

    protected function normalizeVariantInput(string $variant): string
    {
        $v = trim($variant);
        if ($v === '') {
            return '';
        }
        if (mb_strtolower($v) === 'any variant') {
            return '';
        }

        return $v;
    }
}
