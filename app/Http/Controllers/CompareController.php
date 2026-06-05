<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CompareController extends Controller
{
    public function index(Request $request)
    {
        $results = [];
        $marketTable = [];

        if ($request->filled(['year', 'model', 'vehicle_brand'])) {
            $validated = $request->validate([
                'year' => ['required', 'string', 'max:10'],
                'model' => ['required', 'string', 'max:50'],
                'vehicle_brand' => ['required', 'string', 'max:50'],
                'variant' => ['nullable', 'string', 'max:100'],
            ]);

            $variant = $this->normalizeVariantInput((string) ($validated['variant'] ?? ''));

            $results = $this->runComparison(
                (string) $validated['year'],
                (string) $validated['model'],
                (string) $validated['vehicle_brand'],
                $variant
            );
            $marketTable = $this->buildMarketTable($results);
        }

        return view('compare.index', [
            'results' => $results,
            'marketTable' => $marketTable,
            'year' => (string) $request->get('year', ''),
            'model' => (string) $request->get('model', ''),
            'vehicleBrand' => (string) $request->get('vehicle_brand', ''),
            'variant' => (string) $request->get('variant', ''),
        ]);
    }

    protected function runComparison(string $year, string $model, string $vehicleBrand, string $variant): array
    {
        $query = trim($year . ' ' . $vehicleBrand . ' ' . $model . ($variant !== '' ? ' ' . $variant : ''));

        $sites = [
            'allcarsph.com' => [
                // AllCarsPH uses Shopify-style search endpoint
                'https://allcarsph.com/search?q=' . urlencode($query) . '&options%5Bprefix%5D=last',
            ],
            'ugartecars.ph' => [
                // Ugarte Cars inventory search endpoint
                'https://ugartecars.ph/inventory/?stm_keywords=' . urlencode($query),
            ],
            'carmax.com.ph' => [
                // Carmax listing search endpoint
                'https://carmax.com.ph/cars?search=' . urlencode($query),
            ],
        ];

        $criteria = $this->buildMatchCriteria($year, $model, $vehicleBrand, $variant);
        $payload = [];

        foreach ($sites as $siteName => $urls) {
            $payload[$siteName] = $this->fetchSiteMatches($siteName, $urls, $criteria);
        }

        return $payload;
    }

    protected function fetchSiteMatches(string $siteName, array $urls, array $criteria): array
    {
        $html = '';
        $usedUrl = '';
        $error = null;

        foreach ($urls as $url) {
            try {
                $timeout = $siteName === 'ugartecars.ph' ? 20 : 14;
                $response = Http::timeout($timeout)
                    ->retry(2, 300)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/123 Safari/537.36',
                        'Accept' => 'text/html,application/xhtml+xml',
                    ])
                    ->get($url);

                if (! $response->ok()) {
                    continue;
                }

                $body = (string) $response->body();
                if (strlen($body) < 800) {
                    continue;
                }

                $html = $body;
                $usedUrl = $url;
                break;
            } catch (\Throwable $e) {
                $error = $e->getMessage();
            }
        }

        $matches = [];
        if ($html !== '') {
            $matches = $this->extractMatchesFromHtml($siteName, $html, $criteria);
        }

        if ($matches === []) {
            $fallback = $this->fetchSiteMatchesViaSearchEngine($siteName, $criteria);
            if (! empty($fallback['matches'])) {
                return $fallback;
            }
        }

        if ($html === '' && $matches === []) {
            return [
                'source_url' => $urls[0] ?? '',
                'used_url' => '',
                'matches' => [],
                'error' => $error ?: 'Unable to fetch listing page.',
            ];
        }

        $matches = $this->enrichMatches($matches, $criteria, 3);

        return [
            'source_url' => $urls[0] ?? '',
            'used_url' => $usedUrl,
            'matches' => $matches,
            'error' => null,
        ];
    }

    protected function extractMatchesFromHtml(string $siteName, string $html, array $criteria): array
    {
        $host = parse_url('https://' . $siteName, PHP_URL_HOST) ?: $siteName;
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $anchors = $xpath->query('//a[@href]');
        $rows = [];

        if (! $anchors) {
            return [];
        }

        foreach ($anchors as $anchor) {
            $href = trim((string) $anchor->getAttribute('href'));
            $title = trim(preg_replace('/\s+/', ' ', (string) $anchor->textContent));

            if ($href === '' || $title === '' || strlen($title) < 4) {
                continue;
            }

            $absoluteUrl = $this->absoluteUrl($href, $host);
            if (! $absoluteUrl) {
                continue;
            }

            if (! $this->looksLikeVehicleListingUrl($absoluteUrl)) {
                continue;
            }

            $context = $this->extractNodeContext($anchor);
            $combined = mb_strtolower($title . ' ' . $absoluteUrl . ' ' . $context);
            if (! $this->matchesListingCriteria($combined, $criteria)) {
                continue;
            }

            $score = $this->computeListingScore($combined, $criteria);
            $price = $this->extractPrice($context . ' ' . $title);

            $rows[] = [
                'title' => $title,
                'url' => $absoluteUrl,
                'price' => $price,
                'score' => $score,
            ];
        }

        usort($rows, function ($a, $b) {
            if ($a['score'] === $b['score']) {
                return strcmp((string) $a['title'], (string) $b['title']);
            }
            return $b['score'] <=> $a['score'];
        });

        $unique = [];
        $seen = [];
        foreach ($rows as $row) {
            $key = md5((string) $row['url']);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $unique[] = $row;
            if (count($unique) >= 10) {
                break;
            }
        }

        return $unique;
    }

    protected function fetchSiteMatchesViaSearchEngine(string $siteName, array $criteria): array
    {
        $query = sprintf(
            'site:%s %s %s %s%s',
            $siteName,
            $criteria['year'] ?? '',
            $criteria['brand'] ?? '',
            $criteria['model'] ?? '',
            ! empty($criteria['variant_tokens']) ? ' ' . implode(' ', $criteria['variant_tokens']) : ''
        );

        try {
            $headers = [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/123 Safari/537.36',
                'Accept' => 'text/html,application/xhtml+xml',
                'Accept-Language' => 'en-US,en;q=0.9',
                'Referer' => 'https://duckduckgo.com/',
            ];

            // DuckDuckGo HTML endpoint sometimes blocks GET; try GET then POST (form) before giving up.
            $response = Http::timeout(14)->retry(1, 300)->withHeaders($headers)->get('https://html.duckduckgo.com/html/', ['q' => $query]);
            if (! $response->ok()) {
                $response = Http::timeout(14)->retry(1, 300)->withHeaders($headers)->asForm()->post('https://html.duckduckgo.com/html/', ['q' => $query]);
            }
            if (! $response->ok()) {
                // Silently skip fallback if blocked/rate-limited.
                return [
                    'source_url' => '',
                    'used_url' => '',
                    'matches' => [],
                    'error' => null,
                ];
            }

            $matches = $this->extractMatchesFromSearchEngineHtml((string) $response->body(), $siteName, $criteria);
            $matches = $this->enrichMatches($matches, $criteria, 3);

            return [
                'source_url' => 'https://html.duckduckgo.com/html/?q=' . urlencode($query),
                'used_url' => 'https://html.duckduckgo.com/html/?q=' . urlencode($query),
                'matches' => $matches,
                'error' => null,
            ];
        } catch (\Throwable $e) {
            return [
                'source_url' => '',
                'used_url' => '',
                'matches' => [],
                'error' => null,
            ];
        }
    }

    protected function extractMatchesFromSearchEngineHtml(string $html, string $siteName, array $criteria): array
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $anchors = $xpath->query('//a[@href]');
        if (! $anchors) {
            return [];
        }

        $candidates = [];
        foreach ($anchors as $anchor) {
            $href = trim((string) $anchor->getAttribute('href'));
            $title = trim(preg_replace('/\s+/', ' ', (string) $anchor->textContent));

            if ($href === '' || $title === '') {
                continue;
            }

            $decodedUrl = html_entity_decode(urldecode($href));
            if (preg_match('/uddg=([^&]+)/', $decodedUrl, $m)) {
                $decodedUrl = urldecode($m[1]);
            }

            if (! str_contains(mb_strtolower($decodedUrl), mb_strtolower($siteName))) {
                continue;
            }
            if (! $this->looksLikeVehicleListingUrl($decodedUrl)) {
                continue;
            }

            $candidates[] = [
                'title' => $title,
                'url' => $decodedUrl,
            ];
        }

        // Validate candidates by opening the listing page and matching against actual page text.
        $rows = [];
        $limit = 15;
        foreach ($candidates as $cand) {
            if (count($rows) >= 8) {
                break;
            }
            if ($limit-- <= 0) {
                break;
            }

            $detailMeta = $this->fetchListingMeta($cand['url']);
            $combinedWithMeta = mb_strtolower(($cand['title'] ?? '') . ' ' . ($cand['url'] ?? '') . ' ' . ($detailMeta['context'] ?? ''));

            if (! $this->matchesListingCriteria($combinedWithMeta, $criteria)) {
                continue;
            }

            $rows[] = [
                'title' => $detailMeta['title'] ?: ($cand['title'] ?? ''),
                'url' => $cand['url'],
                'price' => $detailMeta['price'],
                'mileage' => $detailMeta['mileage'],
                'transmission' => $detailMeta['transmission'],
                'score' => $this->computeListingScore($combinedWithMeta, $criteria),
            ];
        }

        usort($rows, fn ($a, $b) => ($b['score'] ?? 0) <=> ($a['score'] ?? 0));

        $unique = [];
        $seen = [];
        foreach ($rows as $row) {
            $key = md5((string) $row['url']);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $unique[] = $row;
            if (count($unique) >= 8) {
                break;
            }
        }

        return $unique;
    }

    protected function fetchListingMeta(string $url): array
    {
        try {
            $response = Http::timeout(12)
                ->retry(1, 200)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/123 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml',
                ])
                ->get($url);

            if (! $response->ok()) {
                return ['title' => null, 'price' => null, 'mileage' => null, 'transmission' => null, 'image' => null, 'context' => ''];
            }

            $html = (string) $response->body();
            $context = $this->normalizeText(strip_tags($html));
            $title = null;
            if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $m)) {
                $title = $this->normalizeText(strip_tags($m[1]));
            }

            return [
                'title' => $title,
                'price' => $this->extractPrice($context),
                'mileage' => $this->extractMileage($context),
                'transmission' => $this->extractTransmission($context),
                'image' => $this->extractImageUrl($html, $url),
                'context' => $context,
            ];
        } catch (\Throwable $e) {
            return ['title' => null, 'price' => null, 'mileage' => null, 'transmission' => null, 'image' => null, 'context' => ''];
        }
    }

    protected function enrichMatches(array $matches, array $criteria, int $limit): array
    {
        if ($matches === []) {
            return [];
        }

        $out = [];
        $i = 0;
        foreach ($matches as $m) {
            $row = (array) $m;
            $url = (string) ($row['url'] ?? '');
            if ($url !== '' && $i < $limit) {
                $meta = $this->fetchListingMeta($url);
                $row['title'] = $this->cleanVehicleTitle((string) ($meta['title'] ?? ''), (string) ($row['title'] ?? ''));
                $row['short_title'] = $this->shorten($row['title'] ?? '', 80);
                $row['price'] = $row['price'] ?? null;
                if (empty($row['price']) && ! empty($meta['price'])) {
                    $row['price'] = $meta['price'];
                }
                $row['mileage'] = $meta['mileage'] ?? ($row['mileage'] ?? null);
                $row['transmission'] = $meta['transmission'] ?? ($row['transmission'] ?? null);
                $row['image_url'] = $meta['image'] ?? ($row['image_url'] ?? null);

                $combined = mb_strtolower(($row['title'] ?? '') . ' ' . $url . ' ' . ($meta['context'] ?? ''));
                if (! $this->matchesListingCriteria($combined, $criteria)) {
                    $i++;
                    continue;
                }

                $i++;
            } else {
                $row['short_title'] = $this->shorten((string) ($row['title'] ?? ''), 80);
            }

            $out[] = $row;
        }

        return $out;
    }

    protected function cleanVehicleTitle(string $metaTitle, string $fallbackTitle): string
    {
        $t = trim($metaTitle) !== '' ? trim($metaTitle) : trim($fallbackTitle);
        $t = preg_replace('/\s*\|\s*.*$/', '', $t) ?? $t;
        $t = preg_replace('/\s*-\s*.*$/', '', $t) ?? $t;
        $t = $this->normalizeText($t);

        return $t !== '' ? $t : $this->normalizeText($fallbackTitle);
    }

    protected function normalizeText(string $text): string
    {
        $t = preg_replace('/\s+/', ' ', $text) ?? $text;
        $t = trim($t);
        if (mb_strlen($t) > 3000) {
            $t = mb_substr($t, 0, 3000);
        }

        return $t;
    }

    protected function shorten(string $text, int $max): string
    {
        $t = $this->normalizeText($text);
        if (mb_strlen($t) <= $max) {
            return $t;
        }

        return rtrim(mb_substr($t, 0, $max - 1)) . '…';
    }

    protected function extractImageUrl(string $html, string $baseUrl): ?string
    {
        if (preg_match('/<meta[^>]+property=[\"\']og:image[\"\'][^>]+content=[\"\']([^\"\']+)[\"\']/i', $html, $m)) {
            return $this->absoluteFromBase(trim($m[1]), $baseUrl);
        }
        if (preg_match('/<meta[^>]+name=[\"\']twitter:image[\"\'][^>]+content=[\"\']([^\"\']+)[\"\']/i', $html, $m)) {
            return $this->absoluteFromBase(trim($m[1]), $baseUrl);
        }
        if (preg_match('/<img[^>]+src=[\"\']([^\"\']+)[\"\']/i', $html, $m)) {
            return $this->absoluteFromBase(trim($m[1]), $baseUrl);
        }

        return null;
    }

    protected function absoluteFromBase(string $maybeUrl, string $baseUrl): ?string
    {
        if ($maybeUrl === '' || str_starts_with($maybeUrl, 'data:')) {
            return null;
        }
        if (str_starts_with($maybeUrl, 'http://') || str_starts_with($maybeUrl, 'https://')) {
            return $maybeUrl;
        }
        if (str_starts_with($maybeUrl, '//')) {
            return 'https:' . $maybeUrl;
        }

        $parts = parse_url($baseUrl);
        if (! $parts || empty($parts['host'])) {
            return $maybeUrl;
        }
        $host = $parts['host'];

        if (str_starts_with($maybeUrl, '/')) {
            return 'https://' . $host . $maybeUrl;
        }

        return 'https://' . $host . '/' . ltrim($maybeUrl, '/');
    }

    protected function extractNodeContext(\DOMNode $node): string
    {
        $cursor = $node;
        for ($i = 0; $i < 3; $i++) {
            if (! $cursor->parentNode) {
                break;
            }
            $cursor = $cursor->parentNode;
        }

        return trim(preg_replace('/\s+/', ' ', (string) $cursor->textContent));
    }

    protected function extractPrice(string $text): ?string
    {
        if (preg_match('/(?:₱|PHP|Php|P)\s*[\d,]+(?:\.\d{2})?/u', $text, $m)) {
            return trim($m[0]);
        }

        return null;
    }

    protected function extractMileage(string $text): ?string
    {
        if (preg_match('/(?:mileage|odo(?:meter)?)\s*[:\-]?\s*([\d,]+(?:\.\d+)?\s*(?:km|kms))/iu', $text, $m)) {
            return trim($m[1]);
        }
        if (preg_match('/\b([\d,]+(?:\.\d+)?\s*(?:km|kms))\b/iu', $text, $m)) {
            return trim($m[1]);
        }

        return null;
    }

    protected function extractTransmission(string $text): ?string
    {
        if (preg_match('/\b(automatic|manual|cvt|at|mt)\b/i', $text, $m)) {
            return strtoupper(trim($m[1])) === 'AT' ? 'Automatic' : (strtoupper(trim($m[1])) === 'MT' ? 'Manual' : ucfirst(strtolower(trim($m[1]))));
        }

        return null;
    }

    protected function absoluteUrl(string $href, string $host): ?string
    {
        if (str_starts_with($href, 'javascript:') || str_starts_with($href, '#')) {
            return null;
        }

        if (str_starts_with($href, 'http://') || str_starts_with($href, 'https://')) {
            return $href;
        }

        if (str_starts_with($href, '//')) {
            return 'https:' . $href;
        }

        if (str_starts_with($href, '/')) {
            return 'https://' . $host . $href;
        }

        return 'https://' . $host . '/' . ltrim($href, '/');
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

    protected function buildMatchCriteria(string $year, string $model, string $vehicleBrand, string $variant): array
    {
        $parts = preg_split('/\s+/', trim($vehicleBrand . ' ' . $model . ' ' . $variant)) ?: [];
        $parts = array_map(fn ($v) => mb_strtolower(trim((string) $v)), $parts);
        $parts = array_filter($parts, fn ($v) => $v !== '' && strlen($v) >= 2);

        $variantParts = preg_split('/\s+/', trim($variant)) ?: [];
        $variantParts = array_map(fn ($v) => mb_strtolower(trim((string) $v)), $variantParts);
        $variantParts = array_filter($variantParts, fn ($v) => $v !== '' && strlen($v) >= 2);

        return [
            'year' => mb_strtolower(trim($year)),
            'brand' => mb_strtolower(trim($vehicleBrand)),
            'model' => mb_strtolower(trim($model)),
            'variant_tokens' => array_values(array_unique($variantParts)),
            'all_tokens' => array_values(array_unique($parts)),
        ];
    }

    protected function looksLikeVehicleListingUrl(string $url): bool
    {
        $u = mb_strtolower($url);

        if (str_contains($u, '/wp-content/') || str_contains($u, '.jpg') || str_contains($u, '.png')) {
            return false;
        }

        $patterns = [
            '/inventory/',
            '/vehicle/',
            '/vehicles/',
            '/car/',
            '/cars/',
            '/products/',
            '/cars?',
            '/used',
            '/stock',
            '/listing',
            '/detail',
        ];

        foreach ($patterns as $needle) {
            if (str_contains($u, $needle)) {
                return true;
            }
        }

        return false;
    }

    protected function matchesListingCriteria(string $combined, array $criteria): bool
    {
        $year = (string) ($criteria['year'] ?? '');
        $brand = (string) ($criteria['brand'] ?? '');
        $model = (string) ($criteria['model'] ?? '');
        $variantTokens = $criteria['variant_tokens'] ?? [];

        if ($year !== '' && ! str_contains($combined, $year)) {
            return false;
        }
        if ($brand !== '' && ! str_contains($combined, $brand)) {
            return false;
        }
        if ($model !== '' && ! str_contains($combined, $model)) {
            return false;
        }

        if (! empty($variantTokens)) {
            $hasVariantToken = false;
            foreach ($variantTokens as $tok) {
                if (str_contains($combined, (string) $tok)) {
                    $hasVariantToken = true;
                    break;
                }
            }
            if (! $hasVariantToken) {
                return false;
            }
        }

        return true;
    }

    protected function computeListingScore(string $combined, array $criteria): int
    {
        $score = 0;
        foreach (($criteria['all_tokens'] ?? []) as $token) {
            if ($token !== '' && str_contains($combined, (string) $token)) {
                $score++;
            }
        }

        return $score;
    }

    protected function buildMarketTable(array $results): array
    {
        $sites = [
            'allcarsph.com' => 'AllCarsPH',
            'ugartecars.ph' => 'Ugarte Cars',
            'carmax.com.ph' => 'Carmax PH',
        ];

        $table = [];
        foreach ($sites as $key => $label) {
            $matches = $results[$key]['matches'] ?? [];
            $prices = array_values(array_filter(array_map(fn ($m) => $m['price'] ?? null, $matches)));
            $mileages = array_values(array_filter(array_map(fn ($m) => $m['mileage'] ?? null, $matches)));
            $transmissions = array_values(array_filter(array_map(fn ($m) => $m['transmission'] ?? null, $matches)));
            $top = $matches[0] ?? null;

            $table[$label] = [
                'Availability' => ! empty($matches) ? 'Available' : 'None',
                'Model Variant' => $this->shorten((string) ($top['title'] ?? ''), 80) ?: 'N/A',
                'Price Range' => $this->summarizePriceRange($prices),
                'Mileage' => $mileages[0] ?? 'N/A',
                'Transmission' => $transmissions[0] ?? 'N/A',
                'Source' => $top['url'] ?? ($results[$key]['used_url'] ?? ''),
            ];
        }

        return $table;
    }

    protected function summarizePriceRange(array $prices): string
    {
        if ($prices === []) {
            return 'N/A';
        }

        $numeric = [];
        foreach ($prices as $price) {
            $n = preg_replace('/[^\d.]/', '', (string) $price);
            if ($n !== '') {
                $numeric[] = (float) $n;
            }
        }
        if ($numeric === []) {
            return $prices[0];
        }

        sort($numeric);
        $min = $numeric[0];
        $max = $numeric[count($numeric) - 1];
        if ($min === $max) {
            return '₱' . number_format($min, 0);
        }

        return '₱' . number_format($min, 0) . ' - ₱' . number_format($max, 0);
    }
}

