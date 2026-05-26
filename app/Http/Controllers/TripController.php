<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\TripItem;
use App\Models\TripTemplate;
use App\Models\TripTemplateItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\TripBudget;

class TripController extends Controller
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

        $trips = $household->trips()
    ->with([
        'items' => fn ($query) => $query
            ->orderBy('is_checked')
            ->orderBy('sort_order')
            ->orderBy('title'),
        'budgets' => fn ($query) => $query->latest('id'),
    ])
    ->withSum([
        'shoppingItems as shopping_items_actual_price_sum' => fn ($query) => $query
            ->whereNotNull('actual_price'),
    ], 'actual_price')
    ->orderByRaw("
        CASE status
            WHEN 'preparing' THEN 1
            WHEN 'planned' THEN 2
            WHEN 'ready' THEN 3
            WHEN 'done' THEN 4
            ELSE 5
        END
    ")
    ->orderBy('start_date')
    ->get();

        $tripTemplates = TripTemplate::query()
            ->where('household_id', $household->id)
            ->withCount('items')
            ->orderBy('name')
            ->get();

        $travelModeLabels = $this->travelModeLabels();
        $statusLabels = $this->statusLabels();
        $budgetCategoryLabels = $this->budgetCategoryLabels();

        $smartSuggestionsByTrip = $trips->mapWithKeys(function (Trip $trip) {
            return [$trip->id => $this->buildSmartSuggestions($trip)];
        });

        $smartCoachPromptsByTrip = $trips->mapWithKeys(function (Trip $trip) {
            return [$trip->id => $this->buildSmartCoachPrompts($trip)];
        });

        return view('trips.index', [
            'household' => $household,
            'trips' => $trips,
            'tripTemplates' => $tripTemplates,
            'travelModeLabels' => $travelModeLabels,
            'statusLabels' => $statusLabels,
            'smartSuggestionsByTrip' => $smartSuggestionsByTrip,
            'smartCoachPromptsByTrip' => $smartCoachPromptsByTrip,
            'budgetCategoryLabels' => $budgetCategoryLabels,
        ]);
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $user = auth()->user();
        $household = $user->households()->first();

        if (!$household) {
            return $this->errorResponse($request, 'Kein Haushalt gefunden.', 'dashboard');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'destination_name' => ['nullable', 'string', 'max:255'],
            'destination_address' => ['nullable', 'string', 'max:255'],
            'persons' => ['required', 'integer', 'min:1', 'max:20'],
            'travel_mode' => ['required', 'in:camper,car,other'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $trip = Trip::create([
            'household_id' => $household->id,
            'created_by_user_id' => $user->id,
            'title' => $validated['title'],
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'destination_name' => $validated['destination_name'] ?? null,
            'destination_address' => $validated['destination_address'] ?? null,
            'persons' => $validated['persons'],
            'travel_mode' => $validated['travel_mode'],
            'status' => 'planned',
            'notes' => $validated['notes'] ?? null,
        ]);

        $trip = $trip->fresh('items');
        $this->recalculateTripStatus($trip);
        $trip = $trip->fresh('items');

        if ($this->wantsJson($request)) {
            return response()->json([
                'ok' => true,
                'message' => 'Reise wurde angelegt.',
                'trip' => $this->buildTripStats($trip),
                'card_html' => $this->renderTripCardHtml($trip),
                'trip_count' => $household->trips()->count(),
            ]);
        }

        return redirect()
            ->route('trips.index')
            ->with('status', 'Reise wurde angelegt.');
    }

    public function update(Request $request, Trip $trip): RedirectResponse|JsonResponse
    {
        $this->ensureTripBelongsToCurrentHousehold($trip);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'destination_name' => ['nullable', 'string', 'max:255'],
            'destination_address' => ['nullable', 'string', 'max:255'],
            'persons' => ['required', 'integer', 'min:1', 'max:20'],
            'travel_mode' => ['required', 'in:camper,car,other'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $trip->update([
            'title' => $validated['title'],
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'destination_name' => $validated['destination_name'] ?? null,
            'destination_address' => $validated['destination_address'] ?? null,
            'persons' => $validated['persons'],
            'travel_mode' => $validated['travel_mode'],
            'notes' => $validated['notes'] ?? null,
        ]);

        $trip = $trip->fresh('items');
        $this->recalculateTripStatus($trip);
        $trip = $trip->fresh('items');

        if ($this->wantsJson($request)) {
            return response()->json([
                'ok' => true,
                'message' => 'Reise wurde aktualisiert.',
                'trip' => $this->buildTripStats($trip),
                'card_html' => $this->renderTripCardHtml($trip),
            ]);
        }

        return redirect()
            ->route('trips.index')
            ->with('status', 'Reise wurde aktualisiert.');
    }

    public function destroy(Request $request, Trip $trip): RedirectResponse|JsonResponse
    {
        $this->ensureTripBelongsToCurrentHousehold($trip);

        $householdId = $trip->household_id;
        $tripId = $trip->id;

        $trip->delete();

        $remainingCount = Trip::query()
            ->where('household_id', $householdId)
            ->count();

        if ($this->wantsJson($request)) {
            return response()->json([
                'ok' => true,
                'message' => 'Reise wurde gelöscht.',
                'trip_id' => $tripId,
                'trip_count' => $remainingCount,
            ]);
        }

        return redirect()
            ->route('trips.index')
            ->with('status', 'Reise wurde gelöscht.');
    }

    public function storeItem(Request $request, Trip $trip): RedirectResponse|JsonResponse
    {
        $this->ensureTripBelongsToCurrentHousehold($trip);

        $validated = $request->validate([
            'list_type' => ['required', 'in:packing,preparation'],
            'category' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'quantity' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'is_suggested' => ['nullable', 'boolean'],
        ]);

        if ($this->tripItemExists($trip, $validated['list_type'], $validated['title'])) {
            if ($this->wantsJson($request)) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Dieser Eintrag existiert in dieser Liste bereits.',
                ], 422);
            }

            return redirect()
                ->route('trips.index')
                ->withErrors(['title' => 'Dieser Eintrag existiert in dieser Liste bereits.']);
        }

        $nextSortOrder = (int) $trip->items()->max('sort_order') + 1;

        TripItem::create([
            'trip_id' => $trip->id,
            'created_by_user_id' => auth()->id(),
            'list_type' => $validated['list_type'],
            'category' => $validated['category'] ?? null,
            'title' => $validated['title'],
            'quantity' => $validated['quantity'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_checked' => false,
            'is_suggested' => (bool) ($validated['is_suggested'] ?? false),
            'sort_order' => $nextSortOrder,
        ]);

        $trip = $trip->fresh('items');
        $this->recalculateTripStatus($trip);
        $trip = $trip->fresh('items');

        if ($this->wantsJson($request)) {
            return response()->json([
                'ok' => true,
                'message' => 'Eintrag wurde hinzugefügt.',
                'trip' => $this->buildTripStats($trip),
                'card_html' => $this->renderTripCardHtml($trip),
            ]);
        }

        return redirect()
            ->route('trips.index')
            ->with('status', 'Eintrag wurde hinzugefügt.');
    }

    public function toggleItem(Request $request, TripItem $item): RedirectResponse|JsonResponse
    {
        $this->ensureItemBelongsToCurrentHousehold($item);

        $newCheckedState = !$item->is_checked;

        $item->update([
            'is_checked' => $newCheckedState,
            'checked_at' => $newCheckedState ? now() : null,
        ]);

        $trip = $item->trip->fresh('items');
        $this->recalculateTripStatus($trip);
        $trip = $trip->fresh('items');

        if ($this->wantsJson($request)) {
            return response()->json([
                'ok' => true,
                'message' => $newCheckedState ? 'Eintrag erledigt.' : 'Eintrag wieder geöffnet.',
                'trip' => $this->buildTripStats($trip),
                'card_html' => $this->renderTripCardHtml($trip),
            ]);
        }

        return redirect()
            ->route('trips.index')
            ->with('status', 'Eintrag wurde aktualisiert.');
    }

    public function destroyItem(Request $request, TripItem $item): RedirectResponse|JsonResponse
    {
        $this->ensureItemBelongsToCurrentHousehold($item);

        $trip = $item->trip;
        $item->delete();

        $trip = $trip->fresh('items');
        $this->recalculateTripStatus($trip);
        $trip = $trip->fresh('items');

        if ($this->wantsJson($request)) {
            return response()->json([
                'ok' => true,
                'message' => 'Eintrag wurde gelöscht.',
                'trip' => $this->buildTripStats($trip),
                'card_html' => $this->renderTripCardHtml($trip),
            ]);
        }

        return redirect()
            ->route('trips.index')
            ->with('status', 'Eintrag wurde gelöscht.');
    }

    public function storeTemplate(Request $request, Trip $trip): RedirectResponse|JsonResponse
    {
        $this->ensureTripBelongsToCurrentHousehold($trip);
        $trip->loadMissing('items');

        if ($trip->items->isEmpty()) {
            if ($this->wantsJson($request)) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Der Trip enthält keine Einträge für eine Vorlage.',
                ], 422);
            }

            return redirect()
                ->route('trips.index')
                ->withErrors(['template' => 'Der Trip enthält keine Einträge für eine Vorlage.']);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $householdId = $trip->household_id;
        $normalizedName = $this->normalizeTitle($validated['name']);

        $templateExists = TripTemplate::query()
            ->where('household_id', $householdId)
            ->get()
            ->contains(function (TripTemplate $template) use ($normalizedName) {
                return $this->normalizeTitle($template->name) === $normalizedName;
            });

        if ($templateExists) {
            if ($this->wantsJson($request)) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Eine Vorlage mit diesem Namen existiert bereits.',
                ], 422);
            }

            return redirect()
                ->route('trips.index')
                ->withErrors(['template' => 'Eine Vorlage mit diesem Namen existiert bereits.']);
        }

        $template = TripTemplate::create([
            'household_id' => $trip->household_id,
            'created_by_user_id' => auth()->id(),
            'name' => $validated['name'],
        ]);

        foreach ($trip->items as $index => $item) {
            TripTemplateItem::create([
                'trip_template_id' => $template->id,
                'list_type' => $item->list_type,
                'category' => $item->category,
                'title' => $item->title,
                'quantity' => $item->quantity,
                'notes' => $item->notes,
                'sort_order' => $item->sort_order ?? ($index + 1),
            ]);
        }

        if ($this->wantsJson($request)) {
            return response()->json([
                'ok' => true,
                'message' => 'Vorlage wurde gespeichert.',
                'template' => [
                    'id' => $template->id,
                    'name' => $template->name,
                ],
            ]);
        }

        return redirect()
            ->route('trips.index')
            ->with('status', 'Vorlage wurde gespeichert.');
    }

    public function applyTemplate(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'trip_id' => ['required', 'integer'],
            'template_id' => ['required', 'integer'],
        ]);

        $trip = Trip::with('items')->findOrFail($validated['trip_id']);
        $this->ensureTripBelongsToCurrentHousehold($trip);

        $template = TripTemplate::with('items')->findOrFail($validated['template_id']);
        $this->ensureTemplateBelongsToCurrentHousehold($template);

        if ($template->items->isEmpty()) {
            if ($this->wantsJson($request)) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Diese Vorlage enthält keine Einträge.',
                ], 422);
            }

            return redirect()
                ->route('trips.index')
                ->withErrors(['template' => 'Diese Vorlage enthält keine Einträge.']);
        }

        $nextSortOrder = (int) $trip->items()->max('sort_order') + 1;
        $addedCount = 0;

        foreach ($template->items as $templateItem) {
            if ($this->tripItemExists($trip, $templateItem->list_type, $templateItem->title)) {
                continue;
            }

            TripItem::create([
                'trip_id' => $trip->id,
                'created_by_user_id' => auth()->id(),
                'list_type' => $templateItem->list_type,
                'category' => $templateItem->category,
                'title' => $templateItem->title,
                'quantity' => $templateItem->quantity,
                'notes' => $templateItem->notes,
                'is_checked' => false,
                'is_suggested' => false,
                'sort_order' => $nextSortOrder++,
            ]);

            $addedCount++;
        }

        $trip = $trip->fresh('items');
        $this->recalculateTripStatus($trip);
        $trip = $trip->fresh('items');

        $message = $addedCount > 0
            ? "Vorlage wurde angewendet. {$addedCount} Einträge hinzugefügt."
            : 'Vorlage wurde geprüft. Alle Einträge waren bereits vorhanden.';

        if ($this->wantsJson($request)) {
            return response()->json([
                'ok' => true,
                'message' => $message,
                'trip' => $this->buildTripStats($trip),
                'card_html' => $this->renderTripCardHtml($trip),
            ]);
        }

        return redirect()
            ->route('trips.index')
            ->with('status', $message);
    }
private function renderTripCardHtml(Trip $trip): string
{
    $trip->loadMissing([
        'items' => fn ($query) => $query
            ->orderBy('is_checked')
            ->orderBy('sort_order')
            ->orderBy('title'),
        'budgets' => fn ($query) => $query->latest('id'),
    ]);

    $trip->loadSum([
        'shoppingItems as shopping_items_actual_price_sum' => fn ($query) => $query
            ->whereNotNull('actual_price'),
    ], 'actual_price');

    return view('trips.partials.trip-card', [
        'trip' => $trip,
        'travelModeLabels' => $this->travelModeLabels(),
        'statusLabels' => $this->statusLabels(),
        'budgetCategoryLabels' => $this->budgetCategoryLabels(),
        'smartSuggestions' => $this->buildSmartSuggestions($trip),
        'coachPrompts' => $this->buildSmartCoachPrompts($trip),
    ])->render();
}

    private function travelModeLabels(): array
    {
        return [
            'camper' => 'Camper',
            'car' => 'Auto',
            'other' => 'Sonstiges',
        ];
    }

    private function statusLabels(): array
    {
        return [
            'planned' => 'Geplant',
            'preparing' => 'In Vorbereitung',
            'ready' => 'Bereit',
            'done' => 'Abgeschlossen',
        ];
    }

    private function ensureTripBelongsToCurrentHousehold(Trip $trip): void
    {
        $householdIds = auth()->user()->households()->pluck('households.id');

        if (!$householdIds->contains($trip->household_id)) {
            abort(403);
        }
    }

    private function ensureItemBelongsToCurrentHousehold(TripItem $item): void
    {
        $householdIds = auth()->user()->households()->pluck('households.id');

        if (!$householdIds->contains($item->trip->household_id)) {
            abort(403);
        }
    }

    private function ensureTemplateBelongsToCurrentHousehold(TripTemplate $template): void
    {
        $householdIds = auth()->user()->households()->pluck('households.id');

        if (!$householdIds->contains($template->household_id)) {
            abort(403);
        }
    }

    private function wantsJson(Request $request): bool
    {
        return $request->expectsJson()
            || $request->wantsJson()
            || $request->ajax()
            || $request->header('X-Requested-With') === 'XMLHttpRequest';
    }

    private function errorResponse(Request $request, string $message, string $redirectRoute): RedirectResponse|JsonResponse
    {
        if ($this->wantsJson($request)) {
            return response()->json([
                'ok' => false,
                'message' => $message,
            ], 422);
        }

        return redirect()
            ->route($redirectRoute)
            ->withErrors(['household' => $message]);
    }

    private function normalizeTitle(string $value): string
    {
        $value = preg_replace('/\s+/u', ' ', trim($value)) ?? trim($value);

        return mb_strtolower($value);
    }

    private function tripItemExists(Trip $trip, string $listType, string $title): bool
    {
        $normalizedTitle = $this->normalizeTitle($title);

        return $trip->items()
            ->where('list_type', $listType)
            ->get()
            ->contains(function (TripItem $item) use ($normalizedTitle) {
                return $this->normalizeTitle($item->title) === $normalizedTitle;
            });
    }

 private function buildTripStats(Trip $trip): array
{
    $trip->loadMissing(['items', 'budgets']);

    $trip->loadSum([
        'shoppingItems as shopping_items_actual_price_sum' => fn ($query) => $query
            ->whereNotNull('actual_price'),
    ], 'actual_price');

    $packingCount = $trip->items->where('list_type', 'packing')->count();
    $preparationCount = $trip->items->where('list_type', 'preparation')->count();
    $totalItems = $trip->items->count();
    $checkedItems = $trip->items->where('is_checked', true)->count();
    $progressPercent = $totalItems > 0 ? (int) round(($checkedItems / $totalItems) * 100) : 0;

    $budgetItems = $trip->budgets->values();

    $foodBudget = (float) $budgetItems
        ->where('category', 'food')
        ->sum(fn ($budget) => (float) $budget->amount);

    $spentFood = (float) ($trip->shopping_items_actual_price_sum ?? 0);

    $paidNonFoodBudget = (float) $budgetItems
        ->filter(fn ($budget) => $budget->is_paid && $budget->category !== 'food')
        ->sum(fn ($budget) => (float) $budget->amount);

    $totalBudgetValue = (float) $budgetItems->sum(fn ($budget) => (float) $budget->amount);
    $totalSpentValue = $spentFood + $paidNonFoodBudget;
    $totalDifferenceValue = $totalBudgetValue - $totalSpentValue;

    return [
        'id' => $trip->id,
        'status' => $trip->status,
        'packing_count' => $packingCount,
        'preparation_count' => $preparationCount,
        'total_items' => $totalItems,
        'checked_items' => $checkedItems,
        'progress_percent' => $progressPercent,

        'food_budget_value' => round($foodBudget, 2),
        'spent_shopping_total' => round($spentFood, 2),
        'budget_difference_value' => round($foodBudget - $spentFood, 2),

        'total_budget_value' => round($totalBudgetValue, 2),
        'total_spent_value' => round($totalSpentValue, 2),
        'total_difference_value' => round($totalDifferenceValue, 2),
    ];
}
    private function recalculateTripStatus(Trip $trip): void
    {
        $trip->loadMissing('items');

        $totalItems = $trip->items->count();
        $checkedItems = $trip->items->where('is_checked', true)->count();

        $newStatus = 'planned';

        if ($totalItems > 0 && $checkedItems < $totalItems) {
            $newStatus = 'preparing';
        }

        if ($totalItems > 0 && $checkedItems === $totalItems) {
            $newStatus = 'ready';
        }

        if (
            $totalItems > 0
            && $checkedItems === $totalItems
            && $trip->end_date
            && now()->startOfDay()->gt($trip->end_date->copy()->endOfDay())
        ) {
            $newStatus = 'done';
        }

        if ($trip->status !== $newStatus) {
            $trip->update(['status' => $newStatus]);
        }
    }

    private function buildSmartSuggestions(Trip $trip): array
    {
        $existingTitles = $trip->items
            ->map(fn ($item) => $this->normalizeTitle($item->title))
            ->values()
            ->all();

        $suggestions = [];

        $push = function (
            string $title,
            string $listType,
            ?string $category = null,
            ?string $quantity = null
        ) use (&$suggestions, $existingTitles) {
            $key = $this->normalizeTitle($title);

            if (in_array($key, $existingTitles, true)) {
                return;
            }

            foreach ($suggestions as $suggestion) {
                if ($this->normalizeTitle($suggestion['title']) === $key) {
                    return;
                }
            }

            $suggestions[] = [
                'title' => $title,
                'list_type' => $listType,
                'category' => $category,
                'quantity' => $quantity,
            ];
        };

        $month = $trip->start_date ? (int) $trip->start_date->format('n') : null;
        $days = $trip->duration_days;
        $context = $this->normalizeTitle(
            trim(($trip->title ?? '') . ' ' . ($trip->destination_name ?? '') . ' ' . ($trip->destination_address ?? '') . ' ' . ($trip->notes ?? ''))
        );

        $push('Fahrzeugpapiere', 'preparation', 'Dokumente');
        $push('Ausweise prüfen', 'preparation', 'Dokumente');

        if ($trip->destination_address) {
            $push('Route prüfen', 'preparation', 'Route');
            $push('Adresse offline speichern', 'preparation', 'Route');
        }

        if ($trip->travel_mode === 'camper') {
            $push('Stromkabel', 'packing', 'Camper');
            $push('Wasserschlauch', 'packing', 'Camper');
            $push('Gasflasche prüfen', 'preparation', 'Camper');
            $push('Reifendruck prüfen', 'preparation', 'Camper');
            $push('Campingtisch', 'packing', 'Außenbereich');
            $push('Campingstühle', 'packing', 'Außenbereich');
        }

        if ($trip->persons >= 4) {
            $push('Zusätzliche Stühle', 'packing', 'Außenbereich');
            $push('Große Kühlbox', 'packing', 'Küche');
        }

        if ($days && $days >= 5) {
            $push('Wäscheleine', 'packing', 'Haushalt');
            $push('Mehr Handtücher', 'packing', 'Bad');
            $push('Einkauf vor Abfahrt planen', 'preparation', 'Versorgung');
        }

        if ($month !== null && in_array($month, [6, 7, 8], true)) {
            $push('Sonnenschutz', 'packing', 'Sommer');
            $push('Badesachen', 'packing', 'Sommer');
            $push('Markise prüfen', 'preparation', 'Camper');
        }

        if ($month !== null && in_array($month, [11, 12, 1, 2, 3], true)) {
            $push('Warme Decken', 'packing', 'Winter');
            $push('Heizung prüfen', 'preparation', 'Camper');
        }

        if (str_contains($context, 'hund')) {
            $push('Hundefutter', 'packing', 'Hund');
            $push('Leine', 'packing', 'Hund');
            $push('EU-Heimtierausweis', 'preparation', 'Hund');
        }

        if (str_contains($context, 'strand') || str_contains($context, 'meer') || str_contains($context, 'see')) {
            $push('Windschutz', 'packing', 'Strand');
            $push('Badeschuhe', 'packing', 'Strand');
        }

        if (str_contains($context, 'fahrrad') || str_contains($context, 'bike')) {
            $push('Fahrradschloss', 'packing', 'Fahrrad');
            $push('Luftpumpe', 'packing', 'Fahrrad');
        }

        return array_slice($suggestions, 0, 12);
    }

    private function buildSmartCoachPrompts(Trip $trip): array
    {
        $missingSuggestions = $this->buildSmartSuggestions($trip);
        $prompts = [];

        foreach (array_slice($missingSuggestions, 0, 3) as $suggestion) {
            $prompts[] = [
                'question' => $this->makeCoachQuestion($suggestion['title']),
                'title' => $suggestion['title'],
                'list_type' => $suggestion['list_type'],
                'category' => $suggestion['category'] ?? null,
                'quantity' => $suggestion['quantity'] ?? null,
            ];
        }

        return $prompts;
    }

    private function makeCoachQuestion(string $title): string
    {
        $value = mb_strtolower(trim($title));

        return match (true) {
            str_contains($value, 'gasflasche') => 'Hast du die Gasflasche schon geprüft oder eingeplant?',
            str_contains($value, 'reifendruck') => 'Ist der Reifendruck vor der Abfahrt schon geprüft?',
            str_contains($value, 'stromkabel') => 'Hast du das Stromkabel schon eingepackt?',
            str_contains($value, 'wasserschlauch') => 'Ist der Wasserschlauch schon dabei?',
            str_contains($value, 'fahrzeugpapiere') => 'Sind die Fahrzeugpapiere griffbereit?',
            str_contains($value, 'ausweise') => 'Sind alle Ausweise geprüft und eingepackt?',
            str_contains($value, 'route') => 'Habt ihr die Route schon geprüft und gespeichert?',
            str_contains($value, 'campingstühle') => 'Sind die Campingstühle schon auf eurer Liste?',
            str_contains($value, 'campingtisch') => 'Habt ihr den Campingtisch schon eingeplant?',
            str_contains($value, 'markise') => 'Ist die Markise vor der Abfahrt geprüft?',
            str_contains($value, 'sonnenschutz') => 'Habt ihr an Sonnenschutz gedacht?',
            str_contains($value, 'badesachen') => 'Sind die Badesachen schon eingeplant?',
            str_contains($value, 'warme decken') => 'Sind warme Decken mit dabei?',
            str_contains($value, 'heizung') => 'Ist die Heizung schon geprüft?',
            str_contains($value, 'wäscheleine') => 'Willst du eine Wäscheleine mitnehmen?',
            str_contains($value, 'handtücher') => 'Sind genug Handtücher eingeplant?',
            str_contains($value, 'kühlbox') => 'Braucht ihr für den Trip noch eine Kühlbox?',
            str_contains($value, 'einkauf vor abfahrt planen') => 'Habt ihr den Einkauf vor der Abfahrt schon geplant?',
            default => 'Hast du „' . $title . '“ schon bedacht?',
        };
    }
    public function storeBudget(Request $request, Trip $trip): RedirectResponse|JsonResponse
{
    $this->ensureTripBelongsToCurrentHousehold($trip);

    $validated = $request->validate([
        'category' => ['required', 'string', 'in:' . implode(',', array_keys($this->budgetCategoryLabels()))],
        'title' => ['required', 'string', 'max:255'],
        'amount' => ['required', 'numeric', 'min:0'],
        'is_paid' => ['nullable', 'boolean'],
        'notes' => ['nullable', 'string', 'max:2000'],
    ]);

    $trip->budgets()->create([
        'created_by_user_id' => auth()->id(),
        'category' => $validated['category'],
        'title' => $validated['title'],
        'amount' => $validated['amount'],
        'is_paid' => (bool) ($validated['is_paid'] ?? false),
        'notes' => $validated['notes'] ?? null,
    ]);

    $trip = $trip->fresh(['items', 'budgets']);
    $this->recalculateTripStatus($trip);
    $trip = $trip->fresh(['items', 'budgets']);

    if ($this->wantsJson($request)) {
        return response()->json([
            'ok' => true,
            'message' => 'Budgeteintrag wurde hinzugefügt.',
            'trip' => $this->buildTripStats($trip),
            'budget' => $this->buildBudgetStats($trip),
            'card_html' => $this->renderTripCardHtml($trip),
        ]);
    }

    return redirect()
        ->route('trips.index')
        ->with('status', 'Budgeteintrag wurde hinzugefügt.');
}

public function toggleBudget(Request $request, TripBudget $budget): RedirectResponse|JsonResponse
{
    $this->ensureBudgetBelongsToCurrentHousehold($budget);

    $budget->update([
        'is_paid' => !$budget->is_paid,
    ]);

    $trip = $budget->trip->fresh(['items', 'budgets']);
    $this->recalculateTripStatus($trip);
    $trip = $trip->fresh(['items', 'budgets']);

    if ($this->wantsJson($request)) {
        return response()->json([
            'ok' => true,
            'message' => $budget->is_paid ? 'Budget als bezahlt markiert.' : 'Budget wieder als offen markiert.',
            'trip' => $this->buildTripStats($trip),
            'budget' => $this->buildBudgetStats($trip),
            'card_html' => $this->renderTripCardHtml($trip),
        ]);
    }

    return redirect()
        ->route('trips.index')
        ->with('status', 'Budgeteintrag wurde aktualisiert.');
}

public function destroyBudget(Request $request, TripBudget $budget): RedirectResponse|JsonResponse
{
    $this->ensureBudgetBelongsToCurrentHousehold($budget);

    $trip = $budget->trip;
    $budget->delete();

    $trip = $trip->fresh(['items', 'budgets']);
    $this->recalculateTripStatus($trip);
    $trip = $trip->fresh(['items', 'budgets']);

    if ($this->wantsJson($request)) {
        return response()->json([
            'ok' => true,
            'message' => 'Budgeteintrag wurde gelöscht.',
            'trip' => $this->buildTripStats($trip),
            'budget' => $this->buildBudgetStats($trip),
            'card_html' => $this->renderTripCardHtml($trip),
        ]);
    }

    return redirect()
        ->route('trips.index')
        ->with('status', 'Budgeteintrag wurde gelöscht.');
}

private function ensureBudgetBelongsToCurrentHousehold(TripBudget $budget): void
{
    $householdIds = auth()->user()->households()->pluck('households.id');

    if (!$householdIds->contains($budget->trip->household_id)) {
        abort(403);
    }
}

private function budgetCategoryLabels(): array
{
    return [
        'camping' => 'Camping / Stellplatz',
        'fuel' => 'Sprit / Laden',
        'toll' => 'Maut / Fähre',
        'food' => 'Lebensmittel',
        'restaurant' => 'Restaurant',
        'activity' => 'Ausflug / Freizeit',
        'shopping' => 'Einkauf',
        'emergency' => 'Notfall / Reserve',
        'other' => 'Sonstiges',
    ];
}

private function buildBudgetStats(Trip $trip): array
{
    $trip->loadMissing('budgets');

    $planned = (float) $trip->budgets->sum(fn ($budget) => (float) $budget->amount);
    $paid = (float) $trip->budgets->where('is_paid', true)->sum(fn ($budget) => (float) $budget->amount);
    $open = max($planned - $paid, 0);

    $perPerson = $trip->persons > 0 ? $planned / $trip->persons : 0;
    $perDay = ($trip->duration_days && $trip->duration_days > 0)
        ? $planned / $trip->duration_days
        : 0;

    return [
        'planned_total' => round($planned, 2),
        'paid_total' => round($paid, 2),
        'open_total' => round($open, 2),
        'per_person_total' => round($perPerson, 2),
        'per_day_total' => round($perDay, 2),
        'count' => $trip->budgets->count(),
    ];
}
}