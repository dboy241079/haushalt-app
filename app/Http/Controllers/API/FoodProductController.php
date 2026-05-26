<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FoodProductCache;
use App\Services\FoodSuggestionService;
use App\Services\OpenFoodFactsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class FoodProductController extends Controller
{
    public function show(
        Request $request,
        string $barcode,
        OpenFoodFactsService $openFoodFactsService,
        FoodSuggestionService $foodSuggestionService
    ): JsonResponse {
        $goal = (string) $request->query('goal', 'allgemein');
        $forceRefresh = filter_var($request->query('refresh', false), FILTER_VALIDATE_BOOLEAN);

        if (!preg_match('/^[0-9A-Za-z\-]+$/', $barcode)) {
            return response()->json([
                'ok' => false,
                'message' => 'Ungültiger Barcode.',
            ], 422);
        }

        $cache = FoodProductCache::where('barcode', $barcode)->first();

        $cacheIsFresh = $cache
            && $cache->last_synced_at
            && $cache->last_synced_at->gt(now()->subDays(30));

        if ($cache && $cacheIsFresh && !$forceRefresh) {
            $product = [
                'barcode' => $cache->barcode,
                'product_name' => $cache->product_name,
                'brand' => $cache->brand,
                'image_url' => $cache->image_url,
                'nutrition_grade' => $cache->nutrition_grade,
                'categories' => $cache->categories ?? [],
                'nutriments' => $cache->nutriments ?? [],
            ];

            $suggestion = $foodSuggestionService->getSuggestion($product, $goal);
            $alternativeProduct = $this->buildAlternativeProduct($suggestion, $openFoodFactsService);

            return response()->json([
                'ok' => true,
                'source' => 'cache',
                'product' => $product,
                'suggestion' => $suggestion,
                'alternative_product' => $alternativeProduct,
            ]);
        }

        try {
            $product = $openFoodFactsService->fetchByBarcode($barcode);
        } catch (RuntimeException $e) {
            if ($cache) {
                $product = [
                    'barcode' => $cache->barcode,
                    'product_name' => $cache->product_name,
                    'brand' => $cache->brand,
                    'image_url' => $cache->image_url,
                    'nutrition_grade' => $cache->nutrition_grade,
                    'categories' => $cache->categories ?? [],
                    'nutriments' => $cache->nutriments ?? [],
                ];

                $suggestion = $foodSuggestionService->getSuggestion($product, $goal);
                $alternativeProduct = $this->buildAlternativeProduct($suggestion, $openFoodFactsService);

                return response()->json([
                    'ok' => true,
                    'source' => 'stale-cache',
                    'warning' => 'Externe API nicht erreichbar, veraltete Cache-Daten verwendet.',
                    'product' => $product,
                    'suggestion' => $suggestion,
                    'alternative_product' => $alternativeProduct,
                ]);
            }

            return response()->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], 503);
        }

        $saved = FoodProductCache::updateOrCreate(
            ['barcode' => $product['barcode']],
            [
                'product_name' => $product['product_name'],
                'brand' => $product['brand'],
                'image_url' => $product['image_url'],
                'nutrition_grade' => $product['nutrition_grade'],
                'categories' => $product['categories'],
                'nutriments' => $product['nutriments'],
                'raw_payload' => $product['raw_payload'],
                'last_synced_at' => Carbon::now(),
            ]
        );

        $savedProduct = [
            'barcode' => $saved->barcode,
            'product_name' => $saved->product_name,
            'brand' => $saved->brand,
            'image_url' => $saved->image_url,
            'nutrition_grade' => $saved->nutrition_grade,
            'categories' => $saved->categories ?? [],
            'nutriments' => $saved->nutriments ?? [],
        ];

        $suggestion = $foodSuggestionService->getSuggestion($savedProduct, $goal);
        $alternativeProduct = $this->buildAlternativeProduct($suggestion, $openFoodFactsService);

        return response()->json([
            'ok' => true,
            'source' => 'openfoodfacts',
            'product' => $savedProduct,
            'suggestion' => $suggestion,
            'alternative_product' => $alternativeProduct,
        ]);
    }

   public function search(Request $request): JsonResponse
{
    $query = trim((string) $request->query('q', ''));
    $goal = (string) $request->query('goal', 'allgemein');

    if (mb_strlen($query) < 2) {
        return response()->json([
            'ok' => false,
            'message' => 'Suchbegriff zu kurz.',
        ], 422);
    }

    try {
        $response = Http::acceptJson()
            ->withHeaders([
                'User-Agent' => config('app.name', 'HouseholdApp') . ' / contact: admin@example.com',
            ])
            ->connectTimeout(3)
            ->timeout(8)
            ->retry(2, 300)
            ->get('https://world.openfoodfacts.org/cgi/search.pl', [
                'search_terms' => $query,
                'search_simple' => 1,
                'action' => 'process',
                'json' => 1,
                'page_size' => 10,
            ]);

        if ($response->failed()) {
            return response()->json([
                'ok' => false,
                'message' => 'Fehler bei der Produktsuche.',
                'status' => $response->status(),
                'body' => $response->body(),
            ], 500);
        }

        $json = $response->json();

        if (!is_array($json)) {
            return response()->json([
                'ok' => false,
                'message' => 'Ungültige Antwort von Open Food Facts.',
                'debug' => $response->body(),
            ], 500);
        }

        $rawProducts = $json['products'] ?? [];

        if (!is_array($rawProducts)) {
            return response()->json([
                'ok' => false,
                'message' => 'Produkte konnten nicht gelesen werden.',
                'debug' => $json,
            ], 500);
        }

        $products = collect($rawProducts)
            ->map(function ($product) {
                if (!is_array($product)) {
                    return null;
                }

                return [
                    'barcode' => $product['code'] ?? null,
                    'product_name' => $product['product_name'] ?? 'Unbekanntes Produkt',
                    'brand' => $product['brands'] ?? '',
                    'image_url' => $product['image_front_small_url'] ?? null,
                ];
            })
            ->filter(fn ($product) => is_array($product) && !empty($product['barcode']))
            ->unique('barcode')
            ->values();

        return response()->json([
            'ok' => true,
            'query' => $query,
            'goal' => $goal,
            'count' => $products->count(),
            'products' => $products,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'ok' => false,
            'message' => 'Suche fehlgeschlagen.',
            'debug' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
        ], 500);
    }
}

    private function buildAlternativeProduct(
        ?array $suggestion,
        OpenFoodFactsService $openFoodFactsService
    ): ?array {
        if (!$suggestion) {
            return null;
        }

        if (!empty($suggestion['alternative_barcode'])) {
            try {
                $altProduct = $openFoodFactsService->fetchByBarcode($suggestion['alternative_barcode']);

                return $this->mapResolvedProduct($altProduct);
            } catch (\Throwable $e) {
                return null;
            }
        }

        if (!empty($suggestion['alternative_search_term'])) {
            return $this->resolveAlternativeProduct(
                $suggestion['alternative_search_term'],
                $openFoodFactsService
            );
        }

        return null;
    }

    private function resolveAlternativeProduct(
        ?string $searchTerm,
        OpenFoodFactsService $openFoodFactsService
    ): ?array {
        $searchTerm = trim((string) $searchTerm);

        if ($searchTerm === '' || mb_strlen($searchTerm) < 2) {
            return null;
        }

        try {
            $response = Http::acceptJson()
                ->withHeaders([
                    'User-Agent' => config('app.name', 'HouseholdApp') . ' / contact: admin@example.com',
                ])
                ->connectTimeout(3)
                ->timeout(8)
                ->retry(2, 300)
                ->get('https://world.openfoodfacts.org/cgi/search.pl', [
                    'search_terms' => $searchTerm,
                    'search_simple' => 1,
                    'action' => 'process',
                    'json' => 1,
                    'page_size' => 5,
                ]);

            if ($response->failed()) {
                return null;
            }

            $json = $response->json();
            $products = collect($json['products'] ?? [])
                ->map(function (array $product) {
                    return [
                        'barcode' => $product['code'] ?? null,
                        'product_name' => $product['product_name'] ?? 'Unbekanntes Produkt',
                    ];
                })
                ->filter(fn ($product) => !empty($product['barcode']))
                ->values();

            if ($products->isEmpty()) {
                return null;
            }

            $first = $products->first();

            if (empty($first['barcode'])) {
                return null;
            }

            $altProduct = $openFoodFactsService->fetchByBarcode($first['barcode']);

            return $this->mapResolvedProduct($altProduct);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function mapResolvedProduct(array $product): array
    {
        return [
            'barcode' => $product['barcode'] ?? null,
            'product_name' => $product['product_name'] ?? 'Unbekanntes Produkt',
            'brand' => $product['brand'] ?? '',
            'image_url' => $product['image_url'] ?? null,
            'nutrition_grade' => $product['nutrition_grade'] ?? null,
            'categories' => $product['categories'] ?? [],
            'nutriments' => $product['nutriments'] ?? [],
        ];
    }
}