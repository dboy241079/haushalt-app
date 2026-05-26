@php
    $packingItems = $trip->items->where('list_type', 'packing')->values();
    $preparationItems = $trip->items->where('list_type', 'preparation')->values();
    $budgetItems = $trip->budgets->values();

    $allItems = $trip->items;
    $totalItems = $allItems->count();
    $checkedItems = $allItems->where('is_checked', true)->count();
    $progressPercent = $totalItems > 0 ? (int) round(($checkedItems / $totalItems) * 100) : 0;

    $plannedBudget = (float) $budgetItems->sum(fn ($budget) => (float) $budget->amount);
    $paidBudget = (float) $budgetItems->where('is_paid', true)->sum(fn ($budget) => (float) $budget->amount);
    $openBudget = max($plannedBudget - $paidBudget, 0);
    $perPersonBudget = $trip->persons > 0 ? $plannedBudget / $trip->persons : 0;
    $perDayBudget = ($trip->duration_days && $trip->duration_days > 0)
        ? $plannedBudget / $trip->duration_days
        : null;

    $foodBudget = (float) $budgetItems
        ->where('category', 'food')
        ->sum(fn ($budget) => (float) $budget->amount);

    $spentFood = (float) ($trip->shopping_items_actual_price_sum ?? 0);
    $foodDifference = $foodBudget - $spentFood;
    $foodDifferenceNegative = $foodDifference < 0;

    $paidNonFoodBudget = (float) $budgetItems
        ->filter(fn ($budget) => $budget->is_paid && $budget->category !== 'food')
        ->sum(fn ($budget) => (float) $budget->amount);

    $totalTripBudget = (float) $budgetItems->sum(fn ($budget) => (float) $budget->amount);
    $totalTripSpent = $spentFood + $paidNonFoodBudget;
    $totalTripDifference = $totalTripBudget - $totalTripSpent;
    $totalTripDifferenceNegative = $totalTripDifference < 0;
@endphp

@php
    $foodBudget = $trip->food_budget_value;
    $spentTotal = $trip->spent_shopping_total;
    $difference = $trip->budget_difference_value;
    $differenceNegative = $difference < 0;
@endphp

<details
    id="trip-card-{{ $trip->id }}"
    class="group rounded-[26px] bg-emerald-50 p-3 ring-1 ring-emerald-100"
    data-trip-id="{{ $trip->id }}"
    data-trip-card="1"
>
    <summary class="flex cursor-pointer list-none items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="font-bold text-slate-900">{{ $trip->title }}</p>

            <div class="mt-2 flex flex-wrap gap-2 text-xs">
                @if($trip->destination_name)
                    <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                        {{ $trip->destination_name }}
                    </span>
                @endif

                <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                    {{ $travelModeLabels[$trip->travel_mode] ?? $trip->travel_mode }}
                </span>

                <span
                    id="trip-status-badge-{{ $trip->id }}"
                    class="rounded-full bg-white px-2 py-1 font-medium text-emerald-700 ring-1 ring-emerald-200"
                >
                    {{ $statusLabels[$trip->status] ?? $trip->status }}
                </span>
            </div>
        </div>

        <div class="text-right">
            <p class="text-sm font-bold text-slate-900">
                {{ $trip->duration_days ? $trip->duration_days . ' Tage' : 'offen' }}
            </p>
            <p class="mt-1 text-xs font-semibold text-slate-500 group-open:hidden">
                Öffnen
            </p>
        </div>
    </summary>

    <div class="mt-4 border-t border-emerald-100 pt-4">
        <div class="mb-4 rounded-[20px] bg-white p-3 ring-1 ring-slate-200">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Fortschritt</p>
                    <p id="trip-progress-text-{{ $trip->id }}" class="text-sm font-bold text-slate-900">
                        {{ $checkedItems }} von {{ $totalItems }} erledigt
                    </p>
                </div>
                <span
                    id="trip-progress-badge-{{ $trip->id }}"
                    class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700"
                >
                    {{ $progressPercent }}%
                </span>
            </div>

            <div class="mt-3 h-2.5 w-full overflow-hidden rounded-full bg-slate-200">
                <div
                    id="trip-progress-bar-{{ $trip->id }}"
                    class="h-full rounded-full bg-gradient-to-r from-emerald-400 to-teal-500 transition-all duration-300"
                    style="width: {{ $progressPercent }}%;"
                ></div>
            </div>
        </div>

        <div class="mb-4 rounded-[20px] bg-white p-3 ring-1 ring-slate-200">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Finanzen</p>
            <p class="text-sm font-bold text-slate-900">Trip gesamt</p>
        </div>

        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
            {{ number_format($totalTripBudget, 2, ',', '.') }} €
        </span>
    </div>

    <div class="mt-3 grid grid-cols-3 gap-3 text-center">
        <div class="rounded-[18px] bg-slate-50 p-3 ring-1 ring-slate-200">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Budget</p>
            <p class="mt-1 text-sm font-bold text-slate-900">
                {{ number_format($totalTripBudget, 2, ',', '.') }} €
            </p>
        </div>

        <div class="rounded-[18px] bg-slate-50 p-3 ring-1 ring-slate-200">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Ausgegeben</p>
            <p class="mt-1 text-sm font-bold text-slate-900">
                {{ number_format($totalTripSpent, 2, ',', '.') }} €
            </p>
        </div>

        <div class="rounded-[18px] p-3 ring-1 {{ $totalTripDifferenceNegative ? 'bg-rose-50 ring-rose-200' : 'bg-emerald-50 ring-emerald-200' }}">
            <p class="text-xs font-semibold uppercase tracking-wide {{ $totalTripDifferenceNegative ? 'text-rose-600' : 'text-emerald-600' }}">
                Differenz
            </p>
            <p class="mt-1 text-sm font-bold {{ $totalTripDifferenceNegative ? 'text-rose-700' : 'text-emerald-700' }}">
                {{ $totalTripDifference > 0 ? '+' : '' }}{{ number_format($totalTripDifference, 2, ',', '.') }} €
            </p>
        </div>
    </div>
</div>

<div class="mb-4 rounded-[20px] bg-white p-3 ring-1 ring-slate-200">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Lebensmittel</p>
            <p class="text-sm font-bold text-slate-900">Shop / Einkauf</p>
        </div>

        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
            {{ number_format($foodBudget, 2, ',', '.') }} €
        </span>
    </div>

    <div class="mt-3 grid grid-cols-3 gap-3 text-center">
        <div class="rounded-[18px] bg-slate-50 p-3 ring-1 ring-slate-200">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Budget</p>
            <p class="mt-1 text-sm font-bold text-slate-900">
                {{ number_format($foodBudget, 2, ',', '.') }} €
            </p>
        </div>

        <div class="rounded-[18px] bg-slate-50 p-3 ring-1 ring-slate-200">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Ausgegeben</p>
            <p class="mt-1 text-sm font-bold text-slate-900">
                {{ number_format($spentFood, 2, ',', '.') }} €
            </p>
        </div>

        <div class="rounded-[18px] p-3 ring-1 {{ $foodDifferenceNegative ? 'bg-rose-50 ring-rose-200' : 'bg-emerald-50 ring-emerald-200' }}">
            <p class="text-xs font-semibold uppercase tracking-wide {{ $foodDifferenceNegative ? 'text-rose-600' : 'text-emerald-600' }}">
                Differenz
            </p>
            <p class="mt-1 text-sm font-bold {{ $foodDifferenceNegative ? 'text-rose-700' : 'text-emerald-700' }}">
                {{ $foodDifference > 0 ? '+' : '' }}{{ number_format($foodDifference, 2, ',', '.') }} €
            </p>
        </div>
    </div>
</div>

        <div class="flex flex-wrap gap-2 text-xs">
            @if($trip->start_date)
                <span class="rounded-full bg-white px-3 py-2 font-semibold text-slate-700 ring-1 ring-slate-200">
                    Start: {{ $trip->start_date->format('d.m.Y') }}
                </span>
            @endif

            @if($trip->end_date)
                <span class="rounded-full bg-white px-3 py-2 font-semibold text-slate-700 ring-1 ring-slate-200">
                    Ende: {{ $trip->end_date->format('d.m.Y') }}
                </span>
            @endif

            <span class="rounded-full bg-white px-3 py-2 font-semibold text-slate-700 ring-1 ring-slate-200">
                {{ $trip->persons }} Person{{ $trip->persons > 1 ? 'en' : '' }}
            </span>
        </div>

        @if($trip->destination_address)
            <div class="mt-3">
                <p class="text-sm font-semibold text-slate-900">Adresse</p>
                <p class="mt-1 text-sm text-slate-600">{{ $trip->destination_address }}</p>

                @if($trip->maps_url)
                    <a
                        href="{{ $trip->maps_url }}"
                        target="_blank"
                        class="mt-3 inline-flex rounded-full bg-white px-3 py-2 text-xs font-semibold text-emerald-700 ring-1 ring-emerald-200"
                    >
                        📍 In Google Maps öffnen
                    </a>
                @endif
            </div>
        @endif

        @if($trip->notes)
            <div class="mt-3 rounded-[20px] bg-white p-3 text-sm text-slate-600 ring-1 ring-slate-200">
                {{ $trip->notes }}
            </div>
        @endif

        <div class="mt-4 rounded-[22px] bg-white p-3 ring-1 ring-slate-200">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Bearbeiten</p>
                    <h3 class="text-sm font-bold text-slate-900">Trip-Daten anpassen</h3>
                </div>
            </div>

            <form
                action="{{ route('trips.update', $trip) }}"
                method="POST"
                class="mt-3 space-y-3"
                data-ajax="trip-update"
                data-trip-id="{{ $trip->id }}"
            >
                @csrf
                @method('PATCH')

                <input
                    name="title"
                    type="text"
                    value="{{ $trip->title }}"
                    class="w-full rounded-[18px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                    placeholder="Titel"
                    required
                >

                <div class="grid grid-cols-2 gap-3">
                    <input
                        name="start_date"
                        type="date"
                        value="{{ $trip->start_date?->format('Y-m-d') }}"
                        class="w-full rounded-[18px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                    >

                    <input
                        name="end_date"
                        type="date"
                        value="{{ $trip->end_date?->format('Y-m-d') }}"
                        class="w-full rounded-[18px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                    >
                </div>

                <input
                    name="destination_name"
                    type="text"
                    value="{{ $trip->destination_name }}"
                    class="w-full rounded-[18px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                    placeholder="Zielname"
                >

                <input
                    name="destination_address"
                    type="text"
                    value="{{ $trip->destination_address }}"
                    class="w-full rounded-[18px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                    placeholder="Zieladresse"
                >

                <div class="grid grid-cols-2 gap-3">
                    <input
                        name="persons"
                        type="number"
                        min="1"
                        max="20"
                        value="{{ $trip->persons }}"
                        class="w-full rounded-[18px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                        required
                    >

                    <select
                        name="travel_mode"
                        class="w-full rounded-[18px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                        required
                    >
                        <option value="camper" {{ $trip->travel_mode === 'camper' ? 'selected' : '' }}>Camper</option>
                        <option value="car" {{ $trip->travel_mode === 'car' ? 'selected' : '' }}>Auto</option>
                        <option value="other" {{ $trip->travel_mode === 'other' ? 'selected' : '' }}>Sonstiges</option>
                    </select>
                </div>

                <textarea
                    name="notes"
                    rows="3"
                    class="w-full rounded-[18px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                    placeholder="Notiz"
                >{{ $trip->notes }}</textarea>

                <button
                    type="submit"
                    class="w-full rounded-[18px] bg-emerald-600 px-3 py-2.5 text-xs font-semibold text-white shadow-sm"
                >
                    Trip speichern
                </button>
            </form>
        </div>

        @if(!empty($coachPrompts))
            <div class="mt-4 rounded-[22px] bg-white p-3 ring-1 ring-slate-200" data-coach-box="1">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-violet-600">KI-Check</p>
                        <h3 class="text-sm font-bold text-slate-900">Kurz noch prüfen</h3>
                    </div>
                    <span class="rounded-full bg-violet-100 px-3 py-1 text-xs font-semibold text-violet-700">
                        {{ count($coachPrompts) }}
                    </span>
                </div>

                <div class="mt-3 space-y-3">
                    @foreach($coachPrompts as $prompt)
                        <div class="smart-coach-card rounded-[18px] bg-violet-50 p-3 ring-1 ring-violet-100">
                            <p class="text-sm font-medium text-slate-800">
                                🤖 {{ $prompt['question'] }}
                            </p>

                            <form
                                action="{{ route('trips.items.store', $trip) }}"
                                method="POST"
                                class="mt-3"
                                data-ajax="coach-add"
                                data-trip-id="{{ $trip->id }}"
                            >
                                @csrf
                                <input type="hidden" name="list_type" value="{{ $prompt['list_type'] }}">
                                <input type="hidden" name="category" value="{{ $prompt['category'] }}">
                                <input type="hidden" name="title" value="{{ $prompt['title'] }}">
                                <input type="hidden" name="quantity" value="{{ $prompt['quantity'] }}">
                                <input type="hidden" name="is_suggested" value="1">

                                <button
                                    type="submit"
                                    class="rounded-full bg-violet-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-violet-700"
                                >
                                    Jetzt hinzufügen
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if(!empty($smartSuggestions))
            <div class="mt-4 rounded-[22px] bg-white p-3 ring-1 ring-slate-200" data-suggestions-box="1">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Vorschläge</p>
                        <h3 class="text-sm font-bold text-slate-900">Schnell hinzufügen</h3>
                    </div>
                </div>

                <div class="mt-3 flex flex-wrap gap-2">
                    @foreach($smartSuggestions as $suggestion)
                        <div class="smart-suggestion-item">
                            <form
                                action="{{ route('trips.items.store', $trip) }}"
                                method="POST"
                                data-ajax="coach-add"
                                data-trip-id="{{ $trip->id }}"
                            >
                                @csrf
                                <input type="hidden" name="list_type" value="{{ $suggestion['list_type'] }}">
                                <input type="hidden" name="category" value="{{ $suggestion['category'] }}">
                                <input type="hidden" name="title" value="{{ $suggestion['title'] }}">
                                <input type="hidden" name="quantity" value="{{ $suggestion['quantity'] }}">
                                <input type="hidden" name="is_suggested" value="1">

                                <button
                                    type="submit"
                                    class="rounded-full bg-emerald-100 px-3 py-2 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-200"
                                >
                                    + {{ $suggestion['title'] }}
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="mt-4 grid grid-cols-1 gap-4">
            <div class="rounded-[22px] bg-white p-3 ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-sky-600">Packliste</p>
                        <h3 class="text-base font-bold text-slate-900">Mitnehmen</h3>
                    </div>
                    <span
                        id="packing-count-{{ $trip->id }}"
                        class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700"
                    >
                        {{ $packingItems->count() }}
                    </span>
                </div>

                <form
                    action="{{ route('trips.items.store', $trip) }}"
                    method="POST"
                    class="mt-3 space-y-3"
                    data-ajax="item-add"
                    data-trip-id="{{ $trip->id }}"
                >
                    @csrf
                    <input type="hidden" name="list_type" value="packing">

                    <input
                        name="title"
                        type="text"
                        class="w-full rounded-[18px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-sky-500 focus:bg-white"
                        placeholder="z. B. Handtücher"
                        required
                    >

                    <div class="grid grid-cols-2 gap-3">
                        <input
                            name="category"
                            type="text"
                            class="w-full rounded-[18px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-sky-500 focus:bg-white"
                            placeholder="Kategorie"
                        >

                        <input
                            name="quantity"
                            type="text"
                            class="w-full rounded-[18px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-sky-500 focus:bg-white"
                            placeholder="Menge"
                        >
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-[18px] bg-sky-500 px-3 py-2.5 text-xs font-semibold text-white shadow-sm"
                    >
                        Zur Packliste hinzufügen
                    </button>
                </form>

                <div class="mt-3 space-y-2" id="packing-items-{{ $trip->id }}">
                    @forelse($packingItems as $item)
                        <div
                            class="rounded-[18px] bg-slate-50 px-3 py-2 ring-1 ring-slate-200"
                            data-item-id="{{ $item->id }}"
                            data-list-type="packing"
                        >
                            <div class="flex items-center gap-2">
                                <form
                                    action="{{ route('trips.items.toggle', $item) }}"
                                    method="POST"
                                    data-ajax="item-toggle"
                                    data-trip-id="{{ $trip->id }}"
                                >
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        type="submit"
                                        data-item-toggle-button="1"
                                        class="flex h-8 w-8 items-center justify-center rounded-full {{ $item->is_checked ? 'bg-emerald-500 text-white' : 'bg-white text-slate-500 ring-1 ring-slate-300' }}"
                                    >
                                        {{ $item->is_checked ? '✓' : '○' }}
                                    </button>
                                </form>

                                <div class="min-w-0 flex-1">
                                    <p class="trip-item-title text-sm font-semibold {{ $item->is_checked ? 'text-slate-400 line-through' : 'text-slate-800' }}">
                                        {{ $item->title }}
                                    </p>

                                    <div class="mt-1 flex flex-wrap gap-2 text-[11px]">
                                        @if($item->category)
                                            <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                                {{ $item->category }}
                                            </span>
                                        @endif

                                        @if($item->quantity)
                                            <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                                {{ $item->quantity }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <form
                                    action="{{ route('trips.items.destroy', $item) }}"
                                    method="POST"
                                    data-ajax="item-delete"
                                    data-confirm="Eintrag wirklich löschen?"
                                    data-trip-id="{{ $trip->id }}"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700"
                                    >
                                        X
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p data-empty-state="1" class="text-sm text-slate-500">Noch nichts eingetragen.</p>
                    @endforelse
                </div>
            </div>

            <div class="rounded-[22px] bg-white p-3 ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">Vorbereitung</p>
                        <h3 class="text-base font-bold text-slate-900">Vor Abfahrt</h3>
                    </div>
                    <span
                        id="preparation-count-{{ $trip->id }}"
                        class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700"
                    >
                        {{ $preparationItems->count() }}
                    </span>
                </div>

                <form
                    action="{{ route('trips.items.store', $trip) }}"
                    method="POST"
                    class="mt-3 space-y-3"
                    data-ajax="item-add"
                    data-trip-id="{{ $trip->id }}"
                >
                    @csrf
                    <input type="hidden" name="list_type" value="preparation">

                    <input
                        name="title"
                        type="text"
                        class="w-full rounded-[18px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:bg-white"
                        placeholder="z. B. Reifendruck prüfen"
                        required
                    >

                    <div class="grid grid-cols-2 gap-3">
                        <input
                            name="category"
                            type="text"
                            class="w-full rounded-[18px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:bg-white"
                            placeholder="Kategorie"
                        >

                        <input
                            name="quantity"
                            type="text"
                            class="w-full rounded-[18px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:bg-white"
                            placeholder="Menge"
                        >
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-[18px] bg-amber-500 px-3 py-2.5 text-xs font-semibold text-white shadow-sm"
                    >
                        Zur Vorbereitung hinzufügen
                    </button>
                </form>

                <div class="mt-3 space-y-2" id="preparation-items-{{ $trip->id }}">
                    @forelse($preparationItems as $item)
                        <div
                            class="rounded-[18px] bg-slate-50 px-3 py-2 ring-1 ring-slate-200"
                            data-item-id="{{ $item->id }}"
                            data-list-type="preparation"
                        >
                            <div class="flex items-center gap-2">
                                <form
                                    action="{{ route('trips.items.toggle', $item) }}"
                                    method="POST"
                                    data-ajax="item-toggle"
                                    data-trip-id="{{ $trip->id }}"
                                >
                                    @csrf
                                    @method('PATCH')
                                    <button
                                        type="submit"
                                        data-item-toggle-button="1"
                                        class="flex h-8 w-8 items-center justify-center rounded-full {{ $item->is_checked ? 'bg-emerald-500 text-white' : 'bg-white text-slate-500 ring-1 ring-slate-300' }}"
                                    >
                                        {{ $item->is_checked ? '✓' : '○' }}
                                    </button>
                                </form>

                                <div class="min-w-0 flex-1">
                                    <p class="trip-item-title text-sm font-semibold {{ $item->is_checked ? 'text-slate-400 line-through' : 'text-slate-800' }}">
                                        {{ $item->title }}
                                    </p>

                                    <div class="mt-1 flex flex-wrap gap-2 text-[11px]">
                                        @if($item->category)
                                            <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                                {{ $item->category }}
                                            </span>
                                        @endif

                                        @if($item->quantity)
                                            <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                                {{ $item->quantity }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <form
                                    action="{{ route('trips.items.destroy', $item) }}"
                                    method="POST"
                                    data-ajax="item-delete"
                                    data-confirm="Eintrag wirklich löschen?"
                                    data-trip-id="{{ $trip->id }}"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700"
                                    >
                                        X
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p data-empty-state="1" class="text-sm text-slate-500">Noch nichts eingetragen.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="mt-4 rounded-[22px] bg-white p-3 ring-1 ring-slate-200">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-rose-600">Budget</p>
                    <h3 class="text-base font-bold text-slate-900">Kosten & Planung</h3>
                </div>
                <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700">
                    {{ $budgetItems->count() }}
                </span>
            </div>

            <div class="mt-3 grid grid-cols-2 gap-2 text-xs">
                <div class="rounded-[16px] bg-slate-50 p-3 ring-1 ring-slate-200">
                    <p class="font-medium text-slate-500">Geplant</p>
                    <p class="mt-1 text-sm font-bold text-slate-900">{{ number_format($plannedBudget, 2, ',', '.') }} €</p>
                </div>

                <div class="rounded-[16px] bg-slate-50 p-3 ring-1 ring-slate-200">
                    <p class="font-medium text-slate-500">Bezahlt</p>
                    <p class="mt-1 text-sm font-bold text-emerald-700">{{ number_format($paidBudget, 2, ',', '.') }} €</p>
                </div>

                <div class="rounded-[16px] bg-slate-50 p-3 ring-1 ring-slate-200">
                    <p class="font-medium text-slate-500">Offen</p>
                    <p class="mt-1 text-sm font-bold text-rose-700">{{ number_format($openBudget, 2, ',', '.') }} €</p>
                </div>

                <div class="rounded-[16px] bg-slate-50 p-3 ring-1 ring-slate-200">
                    <p class="font-medium text-slate-500">Pro Person</p>
                    <p class="mt-1 text-sm font-bold text-slate-900">{{ number_format($perPersonBudget, 2, ',', '.') }} €</p>
                </div>

                <div class="col-span-2 rounded-[16px] bg-slate-50 p-3 ring-1 ring-slate-200">
                    <p class="font-medium text-slate-500">Pro Tag</p>
                    <p class="mt-1 text-sm font-bold text-slate-900">
                        {{ $perDayBudget !== null ? number_format($perDayBudget, 2, ',', '.') . ' €' : '–' }}
                    </p>
                </div>
            </div>

            <form
                action="{{ route('trips.budgets.store', $trip) }}"
                method="POST"
                class="mt-4 space-y-3"
                data-ajax="budget-add"
                data-trip-id="{{ $trip->id }}"
            >
                @csrf

                <select
                    name="category"
                    class="w-full rounded-[18px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-rose-500 focus:bg-white"
                    required
                >
                    <option value="">Kategorie wählen</option>
                    @foreach($budgetCategoryLabels as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                <input
                    name="title"
                    type="text"
                    class="w-full rounded-[18px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-rose-500 focus:bg-white"
                    placeholder="z. B. Campingplatz Henne Strand"
                    required
                >

                <div class="grid grid-cols-2 gap-3">
                    <input
                        name="amount"
                        type="number"
                        min="0"
                        step="0.01"
                        class="w-full rounded-[18px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-rose-500 focus:bg-white"
                        placeholder="Betrag"
                        required
                    >

                    <label class="flex items-center justify-center gap-2 rounded-[18px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700">
                        <input
                            type="checkbox"
                            name="is_paid"
                            value="1"
                            class="h-4 w-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500"
                        >
                        Schon bezahlt
                    </label>
                </div>

                <textarea
                    name="notes"
                    rows="2"
                    class="w-full rounded-[18px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-rose-500 focus:bg-white"
                    placeholder="Optional"
                ></textarea>

                <button
                    type="submit"
                    class="w-full rounded-[18px] bg-rose-600 px-3 py-2.5 text-xs font-semibold text-white shadow-sm"
                >
                    Budgeteintrag hinzufügen
                </button>
            </form>

            <div class="mt-4 space-y-2">
                @forelse($budgetItems as $budget)
                    <div
                        class="rounded-[18px] bg-slate-50 p-3 ring-1 ring-slate-200"
                        data-budget-id="{{ $budget->id }}"
                    >
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold text-slate-900">{{ $budget->title }}</p>

                                <div class="mt-1 flex flex-wrap gap-2 text-[11px]">
                                    <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                        {{ $budgetCategoryLabels[$budget->category] ?? $budget->category }}
                                    </span>

                                    @if($budget->category === 'food')
    <span class="rounded-full bg-sky-100 px-2 py-1 font-medium text-sky-700 ring-1 ring-sky-200">
        Über Shop
    </span>
@elseif($budget->is_paid)
    <span class="rounded-full bg-emerald-100 px-2 py-1 font-medium text-emerald-700 ring-1 ring-emerald-200">
        Bezahlt
    </span>
@else
    <span class="rounded-full bg-rose-100 px-2 py-1 font-medium text-rose-700 ring-1 ring-rose-200">
        Offen
    </span>
@endif
                                </div>

                                @if($budget->notes)
                                    <p class="mt-2 text-xs text-slate-500">{{ $budget->notes }}</p>
                                @endif
                            </div>

                            <div class="shrink-0 text-right">
                                <p class="text-sm font-bold text-slate-900">
                                    {{ number_format((float) $budget->amount, 2, ',', '.') }} €
                                </p>
                            </div>
                        </div>

                        <div class="mt-3 flex flex-wrap gap-2">
                            @if($budget->category !== 'food')
    <form
        action="{{ route('trips.budgets.toggle', $budget) }}"
        method="POST"
        data-ajax="budget-toggle"
        data-trip-id="{{ $trip->id }}"
    >
        @csrf
        @method('PATCH')
        <button
            type="submit"
            class="rounded-full px-3 py-2 text-xs font-semibold {{ $budget->is_paid ? 'bg-white text-slate-700 ring-1 ring-slate-300' : 'bg-emerald-600 text-white' }}"
        >
            {{ $budget->is_paid ? 'Wieder offen' : 'Als bezahlt markieren' }}
        </button>
    </form>
@endif

                            <form
                                action="{{ route('trips.budgets.destroy', $budget) }}"
                                method="POST"
                                data-ajax="budget-delete"
                                data-confirm="Budgeteintrag wirklich löschen?"
                                data-trip-id="{{ $trip->id }}"
                            >
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="rounded-full bg-red-100 px-3 py-2 text-xs font-semibold text-red-700"
                                >
                                    Löschen
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Noch kein Budget vorhanden.</p>
                @endforelse
            </div>
        </div>

        <form
            action="{{ route('trips.destroy', $trip) }}"
            method="POST"
            class="mt-4"
            data-ajax="trip-delete"
            data-confirm="Reise wirklich löschen?"
            data-trip-id="{{ $trip->id }}"
        >
            @csrf
            @method('DELETE')

            <button
                type="submit"
                class="w-full rounded-[18px] bg-red-600 px-3 py-2.5 text-xs font-semibold text-white shadow-sm"
            >
                Reise löschen
            </button>
        </form>
    </div>
</details>