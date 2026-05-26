<?php

namespace App\Http\Controllers;

use App\Models\HouseholdCostItem;
use App\Models\HouseholdIncomeItem;
use App\Models\HouseholdInsurance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HouseholdCostController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();
        $household = $user->households()->first();

        if (!$household) {
            return redirect()
                ->route('dashboard')
                ->withErrors(['household' => 'Kein Haushalt gefunden.']);
        }

        $costItems = $household->costItems()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        $incomeItems = $household->incomeItems()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('title')
            ->get();

        $monthlyCostTotal = $costItems->sum(
            fn ($item) => $this->toMonthlyAmount((float) ($item->amount ?? 0), $item->interval)
        );

        $yearlyCostTotal = $costItems->sum(
            fn ($item) => $this->toYearlyAmount((float) ($item->amount ?? 0), $item->interval)
        );

        $monthlyIncomeTotal = $incomeItems->sum(
            fn ($item) => $this->toMonthlyAmount((float) ($item->amount ?? 0), $item->interval)
        );

        $yearlyIncomeTotal = $incomeItems->sum(
            fn ($item) => $this->toYearlyAmount((float) ($item->amount ?? 0), $item->interval)
        );

        $summary = [
            'count' => $costItems->count(),
            'income_count' => $incomeItems->count(),
            'monthly_total' => $monthlyCostTotal,
            'yearly_total' => $yearlyCostTotal,
            'monthly_income_total' => $monthlyIncomeTotal,
            'yearly_income_total' => $yearlyIncomeTotal,
            'monthly_net_total' => $monthlyIncomeTotal - $monthlyCostTotal,
            'yearly_net_total' => $yearlyIncomeTotal - $yearlyCostTotal,
        ];

        $livingModeLabels = [
            'rent' => 'Miete',
            'ownership' => 'Eigentum',
        ];

        $ownershipKindLabels = [
            'apartment' => 'Eigentumswohnung',
            'house' => 'Haus',
        ];

        $houseUsageLabels = [
            'self' => 'Haus selbst bewohnt',
            'partial_rent' => 'Haus teilweise vermietet',
            'full_rent' => 'Haus komplett vermietet',
        ];

        $intervalLabels = [
            'monthly' => 'Monatlich',
            'quarterly' => 'Vierteljährlich',
            'half_yearly' => 'Halbjährlich',
            'yearly' => 'Jährlich',
            'one_time' => 'Einmalig',
        ];

        $recommendedInsuranceTypes = $this->getRecommendedInsuranceTypes(
            $household->living_mode,
            $household->ownership_kind,
            $household->house_usage
        );

        $existingInsuranceTypes = HouseholdInsurance::query()
            ->where('household_id', $household->id)
            ->where('status', 'active')
            ->pluck('insurance_type')
            ->unique()
            ->values()
            ->all();

        $missingInsuranceTypes = collect($recommendedInsuranceTypes)
            ->reject(fn ($type) => in_array($type, $existingInsuranceTypes, true))
            ->values();

        $showIncomeSection = in_array($household->house_usage, ['partial_rent', 'full_rent'], true);

        return view('costs.index', [
            'household' => $household,
            'costItems' => $costItems,
            'incomeItems' => $incomeItems,
            'summary' => $summary,
            'livingModeLabels' => $livingModeLabels,
            'ownershipKindLabels' => $ownershipKindLabels,
            'houseUsageLabels' => $houseUsageLabels,
            'intervalLabels' => $intervalLabels,
            'missingInsuranceTypes' => $missingInsuranceTypes,
            'showIncomeSection' => $showIncomeSection,
        ]);
    }

    public function runSetup(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $household = $user->households()->first();

        if (!$household) {
            return redirect()
                ->route('dashboard')
                ->withErrors(['household' => 'Kein Haushalt gefunden.']);
        }

        $hadSetupBefore = !is_null($household->costs_setup_completed_at);

        $validated = $request->validate([
            'living_mode' => ['required', 'in:rent,ownership'],
            'ownership_kind' => ['nullable', 'in:apartment,house'],
            'house_usage' => ['nullable', 'in:self,partial_rent,full_rent'],
        ]);

        if (($validated['living_mode'] ?? null) === 'ownership' && empty($validated['ownership_kind'])) {
            return redirect()->route('costs.index')->withErrors([
                'ownership_kind' => 'Bitte wähle Wohnung oder Haus aus.',
            ])->withInput();
        }

        if (($validated['ownership_kind'] ?? null) === 'house' && empty($validated['house_usage'])) {
            return redirect()->route('costs.index')->withErrors([
                'house_usage' => 'Bitte wähle die Nutzung des Hauses aus.',
            ])->withInput();
        }

        $livingMode = $validated['living_mode'];
        $ownershipKind = $livingMode === 'ownership' ? ($validated['ownership_kind'] ?? null) : null;
        $houseUsage = $ownershipKind === 'house' ? ($validated['house_usage'] ?? null) : null;

        DB::transaction(function () use ($household, $user, $livingMode, $ownershipKind, $houseUsage) {
            $household->forceFill([
                'living_mode' => $livingMode,
                'ownership_kind' => $ownershipKind,
                'house_usage' => $houseUsage,
                'costs_setup_completed_at' => now(),
            ])->save();

            $household->costItems()
                ->where('is_auto_generated', true)
                ->delete();

            $household->incomeItems()
                ->where('is_auto_generated', true)
                ->delete();

            $costTemplates = $this->buildDefaultCosts($livingMode, $ownershipKind, $houseUsage);
            $incomeTemplates = $this->buildDefaultIncomes($livingMode, $ownershipKind, $houseUsage);

            foreach ($costTemplates as $index => $item) {
                HouseholdCostItem::create([
                    'household_id' => $household->id,
                    'title' => $item['title'],
                    'category' => 'Wohnen & Fixkosten',
                    'interval' => $item['interval'],
                    'amount' => null,
                    'is_active' => true,
                    'is_auto_generated' => true,
                    'sort_order' => $index + 1,
                    'notes' => $item['notes'] ?? null,
                    'created_by_user_id' => $user->id,
                ]);
            }

            foreach ($incomeTemplates as $index => $item) {
                HouseholdIncomeItem::create([
                    'household_id' => $household->id,
                    'title' => $item['title'],
                    'category' => 'Vermietung & Einnahmen',
                    'interval' => $item['interval'],
                    'amount' => null,
                    'is_active' => true,
                    'is_auto_generated' => true,
                    'sort_order' => $index + 1,
                    'notes' => $item['notes'] ?? null,
                    'created_by_user_id' => $user->id,
                ]);
            }
        });

        return redirect()
            ->route('costs.index')
            ->with(
                'status',
                $hadSetupBefore
                    ? 'Wohnmodell wurde aktualisiert. Standardkosten und Standard-Einnahmen wurden angepasst.'
                    : 'Setup abgeschlossen. Standardkosten und Standard-Einnahmen wurden angelegt.'
            );
    }

    public function storeItem(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $household = $user->households()->first();

        if (!$household) {
            return redirect()
                ->route('dashboard')
                ->withErrors(['household' => 'Kein Haushalt gefunden.']);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'interval' => ['required', 'in:monthly,quarterly,half_yearly,yearly,one_time'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $nextSortOrder = (int) $household->costItems()->max('sort_order') + 1;

        HouseholdCostItem::create([
            'household_id' => $household->id,
            'title' => $validated['title'],
            'category' => 'Wohnen & Fixkosten',
            'interval' => $validated['interval'],
            'amount' => $validated['amount'] ?? null,
            'is_active' => true,
            'is_auto_generated' => false,
            'sort_order' => $nextSortOrder,
            'notes' => $validated['notes'] ?? null,
            'created_by_user_id' => $user->id,
        ]);

        return redirect()
            ->route('costs.index')
            ->with('status', 'Neue Kostenposition wurde angelegt.');
    }

    public function updateItem(Request $request, HouseholdCostItem $costItem): RedirectResponse
    {
        $this->ensureCostBelongsToCurrentHousehold($costItem);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'interval' => ['required', 'in:monthly,quarterly,half_yearly,yearly,one_time'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $costItem->update([
            'title' => $validated['title'],
            'interval' => $validated['interval'],
            'amount' => $validated['amount'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('costs.index')
            ->with('status', 'Kostenposition wurde aktualisiert.');
    }

    public function destroyItem(HouseholdCostItem $costItem): RedirectResponse
    {
        $this->ensureCostBelongsToCurrentHousehold($costItem);

        $costItem->delete();

        return redirect()
            ->route('costs.index')
            ->with('status', 'Kostenposition wurde gelöscht.');
    }

    public function storeIncomeItem(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $household = $user->households()->first();

        if (!$household) {
            return redirect()
                ->route('dashboard')
                ->withErrors(['household' => 'Kein Haushalt gefunden.']);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'interval' => ['required', 'in:monthly,quarterly,half_yearly,yearly,one_time'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $nextSortOrder = (int) $household->incomeItems()->max('sort_order') + 1;

        HouseholdIncomeItem::create([
            'household_id' => $household->id,
            'title' => $validated['title'],
            'category' => 'Vermietung & Einnahmen',
            'interval' => $validated['interval'],
            'amount' => $validated['amount'] ?? null,
            'is_active' => true,
            'is_auto_generated' => false,
            'sort_order' => $nextSortOrder,
            'notes' => $validated['notes'] ?? null,
            'created_by_user_id' => $user->id,
        ]);

        return redirect()
            ->route('costs.index')
            ->with('status', 'Neue Einnahme wurde angelegt.');
    }

    public function updateIncomeItem(Request $request, HouseholdIncomeItem $incomeItem): RedirectResponse
    {
        $this->ensureIncomeBelongsToCurrentHousehold($incomeItem);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'interval' => ['required', 'in:monthly,quarterly,half_yearly,yearly,one_time'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $incomeItem->update([
            'title' => $validated['title'],
            'interval' => $validated['interval'],
            'amount' => $validated['amount'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('costs.index')
            ->with('status', 'Einnahme wurde aktualisiert.');
    }

    public function destroyIncomeItem(HouseholdIncomeItem $incomeItem): RedirectResponse
    {
        $this->ensureIncomeBelongsToCurrentHousehold($incomeItem);

        $incomeItem->delete();

        return redirect()
            ->route('costs.index')
            ->with('status', 'Einnahme wurde gelöscht.');
    }

    private function ensureCostBelongsToCurrentHousehold(HouseholdCostItem $costItem): void
    {
        $householdIds = auth()->user()->households()->pluck('households.id');

        if (!$householdIds->contains($costItem->household_id)) {
            abort(403);
        }
    }

    private function ensureIncomeBelongsToCurrentHousehold(HouseholdIncomeItem $incomeItem): void
    {
        $householdIds = auth()->user()->households()->pluck('households.id');

        if (!$householdIds->contains($incomeItem->household_id)) {
            abort(403);
        }
    }

    private function buildDefaultCosts(string $livingMode, ?string $ownershipKind, ?string $houseUsage): array
    {
        if ($livingMode === 'rent') {
            return [
                ['title' => 'Miete', 'interval' => 'monthly'],
                ['title' => 'Strom', 'interval' => 'monthly'],
                ['title' => 'Internet / Telefon', 'interval' => 'monthly'],
                ['title' => 'Rundfunkbeitrag', 'interval' => 'quarterly'],
            ];
        }

        if ($ownershipKind === 'apartment') {
            return [
                ['title' => 'Hausgeld', 'interval' => 'monthly'],
                ['title' => 'Strom', 'interval' => 'monthly'],
                ['title' => 'Internet / Telefon', 'interval' => 'monthly'],
                ['title' => 'Rundfunkbeitrag', 'interval' => 'quarterly'],
                ['title' => 'Grundsteuer', 'interval' => 'quarterly'],
                ['title' => 'Rücklagen / Instandhaltung', 'interval' => 'monthly'],
            ];
        }

        if ($ownershipKind === 'house' && $houseUsage === 'self') {
            return [
                ['title' => 'Kredit / Finanzierung', 'interval' => 'monthly'],
                ['title' => 'Strom', 'interval' => 'monthly'],
                ['title' => 'Wasser', 'interval' => 'monthly'],
                ['title' => 'Heizung / Gas', 'interval' => 'monthly'],
                ['title' => 'Internet / Telefon', 'interval' => 'monthly'],
                ['title' => 'Müll', 'interval' => 'monthly'],
                ['title' => 'Grundsteuer', 'interval' => 'quarterly'],
                ['title' => 'Rücklagen / Instandhaltung', 'interval' => 'monthly'],
            ];
        }

        if ($ownershipKind === 'house' && $houseUsage === 'partial_rent') {
            return [
                ['title' => 'Kredit / Finanzierung', 'interval' => 'monthly'],
                ['title' => 'Strom', 'interval' => 'monthly'],
                ['title' => 'Wasser', 'interval' => 'monthly'],
                ['title' => 'Heizung / Gas', 'interval' => 'monthly'],
                ['title' => 'Internet / Telefon', 'interval' => 'monthly'],
                ['title' => 'Müll', 'interval' => 'monthly'],
                ['title' => 'Grundsteuer', 'interval' => 'quarterly'],
                ['title' => 'Rücklagen / Instandhaltung', 'interval' => 'monthly'],
                ['title' => 'Verwaltung / laufende Objektkosten', 'interval' => 'monthly'],
                ['title' => 'Gemeinschaftliche Nebenkosten', 'interval' => 'monthly'],
                ['title' => 'Rücklagen Vermietung', 'interval' => 'monthly'],
            ];
        }

        if ($ownershipKind === 'house' && $houseUsage === 'full_rent') {
            return [
                ['title' => 'Kredit / Finanzierung', 'interval' => 'monthly'],
                ['title' => 'Grundsteuer', 'interval' => 'quarterly'],
                ['title' => 'Verwaltung', 'interval' => 'monthly'],
                ['title' => 'Rücklagen / Instandhaltung', 'interval' => 'monthly'],
                ['title' => 'Müll / Wasser / Energie (falls Eigentümer trägt)', 'interval' => 'monthly'],
            ];
        }

        return [];
    }

    private function buildDefaultIncomes(string $livingMode, ?string $ownershipKind, ?string $houseUsage): array
    {
        if ($ownershipKind === 'house' && $houseUsage === 'partial_rent') {
            return [
                ['title' => 'Kaltmiete', 'interval' => 'monthly'],
                ['title' => 'Nebenkostenvorauszahlung', 'interval' => 'monthly'],
            ];
        }

        if ($ownershipKind === 'house' && $houseUsage === 'full_rent') {
            return [
                ['title' => 'Kaltmiete', 'interval' => 'monthly'],
                ['title' => 'Nebenkostenvorauszahlung', 'interval' => 'monthly'],
                ['title' => 'Stellplatz / Garage', 'interval' => 'monthly'],
            ];
        }

        return [];
    }

    private function toMonthlyAmount(float $amount, string $interval): float
    {
        return match ($interval) {
            'monthly' => $amount,
            'quarterly' => $amount / 3,
            'half_yearly' => $amount / 6,
            'yearly' => $amount / 12,
            default => 0,
        };
    }

    private function toYearlyAmount(float $amount, string $interval): float
    {
        return match ($interval) {
            'monthly' => $amount * 12,
            'quarterly' => $amount * 4,
            'half_yearly' => $amount * 2,
            'yearly' => $amount,
            default => 0,
        };
    }

    private function getRecommendedInsuranceTypes(?string $livingMode, ?string $ownershipKind, ?string $houseUsage): array
    {
        if ($livingMode === 'rent') {
            return ['Haftpflicht', 'Hausrat'];
        }

        if ($ownershipKind === 'apartment') {
            return ['Haftpflicht', 'Hausrat'];
        }

        if ($ownershipKind === 'house' && $houseUsage === 'self') {
            return ['Haftpflicht', 'Hausrat', 'Gebäudeversicherung'];
        }

        if ($ownershipKind === 'house' && in_array($houseUsage, ['partial_rent', 'full_rent'], true)) {
            return ['Haftpflicht', 'Gebäudeversicherung'];
        }

        return [];
    }
}