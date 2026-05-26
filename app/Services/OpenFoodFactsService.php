<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class OpenFoodFactsService
{
    private const BASE_URL = 'https://world.openfoodfacts.net/api/v2/product/';

    /**
     * Holt Produktdaten per Barcode von Open Food Facts.
     *
     * @throws RuntimeException
     */
    public function fetchByBarcode(string $barcode): array
    {
        $barcode = trim($barcode);

        if ($barcode === '' || !preg_match('/^[0-9A-Za-z\-]+$/', $barcode)) {
            throw new RuntimeException('Ungültiger Barcode.');
        }

        $fields = implode(',', [
            'code',
            'product_name',
            'brands',
            'image_url',
            'nutrition_grades',
            'categories_tags',
            'nutriments',
        ]);

        try {
            $response = Http::acceptJson()
                ->withHeaders([
                    'User-Agent' => config('app.name', 'HouseholdApp') . ' / contact: admin@example.com',
                ])
                ->connectTimeout(3)
                ->timeout(8)
                ->retry(2, 300)
                ->get(self::BASE_URL . $barcode, [
                    'fields' => $fields,
                ]);
        } catch (ConnectionException $e) {
            throw new RuntimeException('Open Food Facts ist gerade nicht erreichbar.');
        }

        if ($response->failed()) {
            throw new RuntimeException('Fehler beim Abruf der Produktdaten.');
        }

        $json = $response->json();

        if (!is_array($json) || ($json['status'] ?? 0) !== 1 || empty($json['product'])) {
            throw new RuntimeException('Produkt wurde nicht gefunden.');
        }

        $product = $json['product'];

        return [
            'barcode' => (string)($json['code'] ?? $barcode),
            'product_name' => $product['product_name'] ?? null,
            'brand' => $product['brands'] ?? null,
            'image_url' => $product['image_url'] ?? null,
            'nutrition_grade' => $product['nutrition_grades'] ?? null,
            'categories' => $product['categories_tags'] ?? [],
            'nutriments' => $product['nutriments'] ?? [],
            'raw_payload' => $json,
        ];
    }
}