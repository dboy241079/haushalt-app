<?php

namespace App\Services;

use App\Models\FoodSuggestion;

class FoodSuggestionService
{
    public function getSuggestion(array $product, string $goal = 'allgemein'): ?array
    {
        $tokens = $this->buildTokens($product);

        if (empty($tokens)) {
            return null;
        }

        $suggestions = FoodSuggestion::query()
            ->where('is_active', true)
            ->where(function ($query) use ($goal) {
                $query->where('goal', $goal)
                      ->orWhere('goal', 'allgemein');
            })
            ->orderBy('priority')
            ->get();

        foreach ($suggestions as $suggestion) {
            $triggerTokens = $this->tokenize($suggestion->trigger_term);

            if (empty($triggerTokens)) {
                continue;
            }

            $allMatched = true;

            foreach ($triggerTokens as $triggerToken) {
                if (!in_array($triggerToken, $tokens, true)) {
                    $allMatched = false;
                    break;
                }
            }

            if ($allMatched) {
                return [
                    'matched_term' => $suggestion->trigger_term,
                    'alternative' => $suggestion->alternative,
                    'alternative_label' => $suggestion->alternative_label,
                    'alternative_search_term' => $suggestion->alternative_search_term,
                    'alternative_barcode' => $suggestion->alternative_barcode,
                    'reason' => $suggestion->reason,
                    'goal' => $suggestion->goal,
                ];
            }
        }

        return $this->fallbackNutritionSuggestion($product, $goal);
    }

    private function buildTokens(array $product): array
    {
        $parts = [];

        if (!empty($product['product_name'])) {
            $parts[] = (string) $product['product_name'];
        }

        if (!empty($product['brand'])) {
            $parts[] = (string) $product['brand'];
        }

        if (!empty($product['categories']) && is_array($product['categories'])) {
            $parts[] = implode(' ', $product['categories']);
        }

        $allText = implode(' ', $parts);

        return $this->tokenize($allText);
    }

    private function tokenize(string $text): array
    {
        $text = mb_strtolower($text);
        $text = str_replace(['_', '-', ':', ',', '.', '/', '(', ')'], ' ', $text);
        $text = preg_replace('/[^a-z0-9äöüß\s]/u', ' ', $text);
        $text = preg_replace('/\s+/u', ' ', $text);
        $text = trim($text);

        if ($text === '') {
            return [];
        }

        $tokens = explode(' ', $text);
        $tokens = array_filter($tokens, fn ($token) => $token !== '');

        return array_values(array_unique($tokens));
    }

    private function fallbackNutritionSuggestion(array $product, string $goal): ?array
    {
        $nutriments = $product['nutriments'] ?? [];

        $sugars = $this->toFloat($nutriments['sugars_100g'] ?? null);
        $fat = $this->toFloat($nutriments['fat_100g'] ?? null);
        $protein = $this->toFloat($nutriments['proteins_100g'] ?? null);

        if ($goal === 'abnehmen') {
            if ($sugars !== null && $sugars > 10) {
                return [
                    'matched_term' => 'nährwert-regel',
                    'alternative' => 'zuckerärmere Alternative prüfen',
                    'alternative_label' => null,
                    'alternative_search_term' => null,
                    'alternative_barcode' => null,
                    'reason' => 'Dieses Produkt hat relativ viel Zucker pro 100 g.',
                    'goal' => $goal,
                ];
            }

            if ($fat !== null && $fat > 15) {
                return [
                    'matched_term' => 'nährwert-regel',
                    'alternative' => 'fettärmere Alternative prüfen',
                    'alternative_label' => null,
                    'alternative_search_term' => null,
                    'alternative_barcode' => null,
                    'reason' => 'Dieses Produkt hat relativ viel Fett pro 100 g.',
                    'goal' => $goal,
                ];
            }

            if ($protein !== null && $protein >= 10) {
                return [
                    'matched_term' => 'nährwert-regel',
                    'alternative' => 'passt gut zum Ziel',
                    'alternative_label' => null,
                    'alternative_search_term' => null,
                    'alternative_barcode' => null,
                    'reason' => 'Dieses Produkt ist eiweißreich und kann gut sättigen.',
                    'goal' => $goal,
                ];
            }
        }

        return null;
    }

    private function toFloat(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_numeric($value)) {
            return null;
        }

        return (float) $value;
    }
}