<?php

namespace App\Livewire\Costs;

use App\Models\CostItem;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public int $setupStep = 1;

    public string $housing_type = '';
    public string $ownership_type = '';
    public string $house_usage_type = '';

    public function mount(): void
    {
        $household = $this->getHousehold();

        if (!$household) return;

        if ($household->cost_setup_completed_at) {
            $this->setupStep = 99; // Setup fertig
        }
    }

    private function getHousehold()
    {
        return Auth::user()?->currentHousehold;
    }

    public function needsSetup(): bool
    {
        $household = $this->getHousehold();

        return !$household?->cost_setup_completed_at;
    }

    public function chooseHousingType(string $type)
    {
        $this->housing_type = $type;

        if ($type === 'rent') {
            $this->setupStep = 2;
        }

        if ($type === 'ownership') {
            $this->setupStep = 2;
        }
    }

    public function chooseOwnershipType(string $type)
    {
        $this->ownership_type = $type;

        if ($type === 'apartment') {
            $this->setupStep = 3;
        }

        if ($type === 'house') {
            $this->setupStep = 3;
        }
    }

    public function chooseHouseUsageType(string $type)
    {
        $this->house_usage_type = $type;
        $this->setupStep = 4;
    }

    public function finishSetup()
    {
        $household = $this->getHousehold();

        if (!$household) return;

        $household->update([
            'housing_type' => $this->housing_type,
            'ownership_type' => $this->ownership_type,
            'house_usage_type' => $this->house_usage_type,
            'cost_setup_completed_at' => now(),
        ]);

        $this->createDefaultCosts($household);

        $this->setupStep = 99;
    }

    private function createDefaultCosts($household)
    {
        if ($household->costItems()->exists()) return;

        $items = [];

        if ($this->housing_type === 'rent') {

            $items = [
                'Miete',
                'Strom',
                'Internet / Telefon',
                'Rundfunkbeitrag',
                'Hausratversicherung'
            ];

        }

        if ($this->housing_type === 'ownership'
            && $this->ownership_type === 'apartment') {

            $items = [
                'Hausgeld',
                'Strom',
                'Internet / Telefon',
                'Grundsteuer',
                'Hausratversicherung',
                'Rücklagen'
            ];
        }

        if ($this->ownership_type === 'house') {

            $items = [
                'Kredit / Finanzierung',
                'Strom',
                'Wasser',
                'Heizung',
                'Internet',
                'Grundsteuer',
                'Gebäudeversicherung',
                'Rücklagen'
            ];
        }

        foreach ($items as $i => $title) {

            $household->costItems()->create([
                'title' => $title,
                'amount' => 0,
                'interval' => 'monthly',
                'sort_order' => $i
            ]);
        }
    }

    public function render()
    {
        $household = $this->getHousehold();

        $costItems = $household
            ? $household->costItems()->orderBy('sort_order')->get()
            : collect();

        return view('costs.index', [
            'needsSetup' => $this->needsSetup(),
            'costItems' => $costItems,
        ]);
    }
}