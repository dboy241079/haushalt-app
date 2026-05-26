<?php

namespace Database\Seeders;

use App\Models\FoodSuggestion;
use Illuminate\Database\Seeder;

class FoodSuggestionSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'trigger_term' => 'joghurt',
                'alternative' => 'Skyr',
                'goal' => 'abnehmen',
                'reason' => 'Skyr hat oft mehr Eiweiß und kann länger sättigen.',
                'priority' => 10,
            ],
            [
                'trigger_term' => 'fruchtjoghurt',
                'alternative' => 'Naturjoghurt',
                'goal' => 'abnehmen',
                'reason' => 'Naturjoghurt hat oft weniger Zucker als Fruchtjoghurt.',
                'priority' => 5,
            ],
            [
                'trigger_term' => 'toast',
                'alternative' => 'Vollkornbrot',
                'goal' => 'abnehmen',
                'reason' => 'Vollkorn sättigt meist länger.',
                'priority' => 10,
            ],
            [
                'trigger_term' => 'cornflakes',
                'alternative' => 'Haferflocken',
                'goal' => 'abnehmen',
                'reason' => 'Haferflocken enthalten oft weniger Zucker und sättigen besser.',
                'priority' => 10,
            ],
            [
                'trigger_term' => 'cola',
                'alternative' => 'Wasser oder Zero-Alternative',
                'goal' => 'abnehmen',
                'reason' => 'Damit lässt sich Zucker leichter einsparen.',
                'priority' => 10,
            ],
            [
    'trigger_term' => 'coca cola',
    'alternative' => 'Cola Zero',
    'alternative_label' => 'Coca-Cola Zero',
    'alternative_search_term' => 'coca cola zero',
    'alternative_barcode' => null,
    'goal' => 'abnehmen',
    'reason' => 'Zero-Varianten sparen Zucker ein.',
    'priority' => 10,
],
[
    'trigger_term' => 'joghurt',
    'alternative' => 'Skyr',
    'alternative_label' => 'Skyr Natur',
    'alternative_search_term' => 'skyr natur',
    'alternative_barcode' => null,
    'goal' => 'abnehmen',
    'reason' => 'Skyr hat oft mehr Eiweiß und kann länger sättigen.',
    'priority' => 10,
],
        ];

        foreach ($rows as $row) {
            FoodSuggestion::updateOrCreate(
                [
                    'trigger_term' => $row['trigger_term'],
                    'goal' => $row['goal'],
                ],
                $row
            );
        }
    }
}