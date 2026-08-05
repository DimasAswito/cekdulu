<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class OpenFoodFactsService
{
    protected string $baseUrl;

    protected string $userAgent;

    protected int $cacheTtlHours;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.openfoodfacts.base_url'), '/');
        $this->userAgent = config('services.openfoodfacts.user_agent');
        $this->cacheTtlHours = (int) config('services.openfoodfacts.cache_ttl_hours', 6);
    }

    /**
     * Fetch a product by barcode, caching the response and upserting the local
     * products table. Returns null when the product cannot be found or the
     * request fails.
     */
    public function findByBarcode(string $barcode): ?array
    {
        $cacheKey = "off:product:{$barcode}";

        try {
            return Cache::remember($cacheKey, $this->ttl(), function () use ($barcode) {
                $response = $this->request()->get("{$this->baseUrl}/api/v2/product/{$barcode}.json", [
                    'fields' => 'product_name,brands,nutriscore_grade,nova_group,ingredients_text,nutriments,image_url,allergens_tags',
                ]);

                if ($response->failed()) {
                    throw new RuntimeException("OpenFoodFacts barcode lookup failed with status {$response->status()}");
                }

                $data = $response->json();

                if ((int) ($data['status'] ?? 0) !== 1 || empty($data['product'])) {
                    return null;
                }

                return $this->persistProduct($barcode, $data)->toArray();
            });
        } catch (Throwable $e) {
            Log::error('OpenFoodFacts findByBarcode failed', [
                'barcode' => $barcode,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Search products by name using the legacy OpenFoodFacts search endpoint
     * (full-text search is not available on API v2). Returns an empty array
     * when the request fails.
     */
    public function searchByName(string $query, int $page = 1): array
    {
        $cacheKey = 'off:search:'.md5($query).":{$page}";

        try {
            return Cache::remember($cacheKey, $this->ttl(), function () use ($query, $page) {
                $response = $this->request()->get("{$this->baseUrl}/cgi/search.pl", [
                    'search_terms' => $query,
                    'search_simple' => 1,
                    'action' => 'process',
                    'json' => 1,
                    'page_size' => 20,
                    'page' => $page,
                ]);

                if ($response->failed()) {
                    throw new RuntimeException("OpenFoodFacts search failed with status {$response->status()}");
                }

                return $response->json('products', []);
            });
        } catch (Throwable $e) {
            Log::error('OpenFoodFacts searchByName failed', [
                'query' => $query,
                'page' => $page,
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    protected function persistProduct(string $barcode, array $data): Product
    {
        $product = $data['product'];

        return Product::updateOrCreate(
            ['barcode' => $barcode],
            [
                'name' => $product['product_name'] ?? null,
                'brand' => $product['brands'] ?? null,
                'image_url' => $product['image_url'] ?? null,
                'nutriscore_grade' => isset($product['nutriscore_grade']) ? strtolower((string) $product['nutriscore_grade']) : null,
                'nova_group' => $product['nova_group'] ?? null,
                'ingredients_text' => $product['ingredients_text'] ?? null,
                'nutriments' => $product['nutriments'] ?? [],
                'raw_payload' => $data,
                'fetched_at' => now(),
            ]
        );
    }

    protected function request(): PendingRequest
    {
        return Http::withHeaders(['User-Agent' => $this->userAgent])->timeout(10);
    }

    protected function ttl(): \DateInterval
    {
        return new \DateInterval("PT{$this->cacheTtlHours}H");
    }
}
