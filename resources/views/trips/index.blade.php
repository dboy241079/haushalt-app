<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Urlaub | Haushalt App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-900">
    <div class="app-shell mx-auto w-full max-w-md bg-slate-100">

        <section class="top-safe-area relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950 px-4 pb-6 text-white">
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-emerald-400/20 blur-3xl"></div>
            <div class="absolute -left-8 bottom-0 h-24 w-24 rounded-full bg-teal-400/20 blur-2xl"></div>

            <div class="relative z-10 pt-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-slate-300">
                            Urlaub
                        </p>
                        <h1 class="mt-1 truncate text-2xl font-bold">
                            Reisen & Camper
                        </h1>
                        <p class="mt-1 text-sm text-slate-300">
                            Trips, Packlisten und Vorbereitung an einem Ort
                        </p>
                    </div>

                    <a href="{{ route('dashboard') }}"
                       class="rounded-2xl border border-white/15 bg-white/10 px-3 py-2 text-center text-sm font-medium text-white backdrop-blur">
                        Zurück
                    </a>
                </div>

                <div class="mt-5 rounded-[28px] border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-medium text-slate-300">Urlaubsplanung</p>
                            <p class="mt-1 text-xl font-bold">Bereit für den nächsten Trip</p>
                            <p class="mt-1 text-sm text-slate-300">
                                Mit Packliste, Vorbereitung und schnellen Vorschlägen.
                            </p>
                        </div>

                        <div class="flex h-14 w-14 items-center justify-center rounded-3xl bg-white/10 text-2xl ring-1 ring-white/15">
                            🚐
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <main class="app-main pb-[calc(7.5rem+env(safe-area-inset-bottom))]">
            @if (session('status'))
                <div class="rounded-[24px] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 shadow-sm">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-[24px] border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 shadow-sm">
                    <p class="font-semibold">Bitte prüfe deine Eingaben.</p>
                    <ul class="mt-2 list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="rounded-[32px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Neu</p>
                        <h2 class="text-lg font-bold text-slate-900">Trip anlegen</h2>
                    </div>
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                        Reise
                    </span>
                </div>

                <form
                    id="trip-create-form"
                    action="{{ route('trips.store') }}"
                    method="POST"
                    class="mt-4 space-y-4"
                    data-ajax="trip-create"
                >
                    @csrf

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Titel</label>
                        <input
                            name="title"
                            type="text"
                            value="{{ old('title') }}"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                            placeholder="z. B. Henne Strand Sommerurlaub"
                            required
                        >
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Start</label>
                            <input
                                name="start_date"
                                type="date"
                                value="{{ old('start_date') }}"
                                class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                            >
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Ende</label>
                            <input
                                name="end_date"
                                type="date"
                                value="{{ old('end_date') }}"
                                class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Zielname</label>
                        <input
                            name="destination_name"
                            type="text"
                            value="{{ old('destination_name') }}"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                            placeholder="z. B. Henne Strand Camping"
                        >
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Zieladresse</label>
                        <input
                            name="destination_address"
                            type="text"
                            value="{{ old('destination_address') }}"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                            placeholder="Straße, Ort, Land"
                        >
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Personen</label>
                            <input
                                name="persons"
                                type="number"
                                min="1"
                                max="20"
                                value="{{ old('persons', 2) }}"
                                class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                                required
                            >
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Reiseart</label>
                            <select
                                name="travel_mode"
                                class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                                required
                            >
                                <option value="camper" {{ old('travel_mode', 'camper') === 'camper' ? 'selected' : '' }}>Camper</option>
                                <option value="car" {{ old('travel_mode') === 'car' ? 'selected' : '' }}>Auto</option>
                                <option value="other" {{ old('travel_mode') === 'other' ? 'selected' : '' }}>Sonstiges</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Notiz</label>
                        <textarea
                            name="notes"
                            rows="3"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                            placeholder="Optional"
                        >{{ old('notes') }}</textarea>
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-[24px] bg-gradient-to-r from-emerald-400 to-teal-500 px-4 py-3 text-sm font-semibold text-white shadow-md"
                    >
                        Trip speichern
                    </button>
                </form>
            </section>

            <section class="rounded-[32px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Übersicht</p>
                        <h2 class="text-lg font-bold text-slate-900">Reisen</h2>
                    </div>
                    <span
                        id="trip-count"
                        class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700"
                    >
                        {{ $trips->count() }}
                    </span>
                </div>

                <div
                    id="trip-empty-state"
                    class="{{ $trips->isEmpty() ? 'mt-4' : 'mt-4 hidden' }} rounded-[24px] bg-slate-50 px-4 py-6 text-center ring-1 ring-slate-200"
                >
                    <div class="text-2xl">🏕️</div>
                    <p class="mt-2 text-sm font-semibold text-slate-800">Noch keine Reise angelegt</p>
                    <p class="mt-1 text-xs text-slate-500">Lege oben euren ersten Trip an.</p>
                </div>

                <div
                    id="trip-list"
                    class="{{ $trips->isEmpty() ? 'hidden' : '' }} mt-4 space-y-4"
                >
                    @foreach($trips as $trip)
                        @include('trips.partials.trip-card', [
    'trip' => $trip,
    'travelModeLabels' => $travelModeLabels,
    'statusLabels' => $statusLabels,
    'budgetCategoryLabels' => $budgetCategoryLabels,
    'smartSuggestions' => $smartSuggestionsByTrip[$trip->id] ?? [],
    'coachPrompts' => $smartCoachPromptsByTrip[$trip->id] ?? [],
])
                    @endforeach
                </div>
            </section>
        </main>
    </div>

    @include('partials.bottom-nav', ['active' => 'trips'])

    <div
        id="trip-toast"
        class="hidden fixed left-1/2 top-4 z-50 w-[calc(100%-2rem)] max-w-md -translate-x-1/2 rounded-[18px] px-4 py-3 text-center text-sm font-semibold shadow-xl"
    ></div>

    <script>
    document.addEventListener('submit', async (event) => {
        const form = event.target.closest('form[data-ajax]');
        if (!form) return;

        event.preventDefault();

        const actionType = form.dataset.ajax;
        const confirmText = form.dataset.confirm;

        if (confirmText && !window.confirm(confirmText)) {
            return;
        }

        const submitButton = form.querySelector('button[type="submit"]');
        setButtonLoading(submitButton, true);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: new FormData(form),
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok || !data.ok) {
                throw new Error(extractErrorMessage(data) || 'Aktion konnte nicht ausgeführt werden.');
            }

            switch (actionType) {
                case 'trip-create':
                    if (data.card_html) {
                        insertTripCard(data.card_html);
                    }
                    updateTripCount(data.trip_count ?? getTripCardCount());
                    toggleTripEmptyState();
                    form.reset();
                    showToast(data.message || 'Reise wurde angelegt.', 'success');
                    break;

                case 'trip-delete':
                    if (data.trip_id) {
                        removeTripCard(data.trip_id);
                    }
                    updateTripCount(data.trip_count ?? getTripCardCount());
                    toggleTripEmptyState();
                    showToast(data.message || 'Reise wurde gelöscht.', 'success');
                    break;

                case 'trip-update':
                case 'item-add':
                case 'item-toggle':
                case 'item-delete':
                case 'coach-add':
                case 'budget-add':
                case 'budget-toggle':
                case 'budget-delete': {
                    const tripId = data.trip?.id ?? getTripIdFromForm(form);

                    if (tripId && data.card_html) {
                        replaceTripCard(tripId, data.card_html, true);
                    }

                    showToast(data.message || 'Gespeichert.', 'success');
                    break;
                }
            }
        } catch (error) {
            showToast(error.message || 'Fehler.', 'error');
        } finally {
            setButtonLoading(submitButton, false);
        }
    });

    function parseHtmlToElement(html) {
        const wrapper = document.createElement('div');
        wrapper.innerHTML = html.trim();
        return wrapper.firstElementChild;
    }

    function insertTripCard(html) {
        const tripList = document.getElementById('trip-list');
        if (!tripList || !html) return;

        const newCard = parseHtmlToElement(html);
        if (!newCard) return;

        newCard.setAttribute('open', 'open');
        tripList.prepend(newCard);
        newCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function replaceTripCard(tripId, html, keepOpen = false) {
        const oldCard = document.getElementById(`trip-card-${tripId}`);
        if (!oldCard || !html) return;

        const newCard = parseHtmlToElement(html);
        if (!newCard) return;

        if (keepOpen || oldCard.hasAttribute('open')) {
            newCard.setAttribute('open', 'open');
        }

        oldCard.replaceWith(newCard);
    }

    function removeTripCard(tripId) {
        const card = document.getElementById(`trip-card-${tripId}`);
        if (card) {
            card.remove();
        }
    }

    function getTripIdFromForm(form) {
        return form.dataset.tripId
            || form.closest('[data-trip-id]')?.dataset.tripId
            || null;
    }

    function getTripCardCount() {
        return document.querySelectorAll('[data-trip-card="1"]').length;
    }

    function updateTripCount(count) {
        const tripCount = document.getElementById('trip-count');
        if (tripCount) {
            tripCount.textContent = count;
        }
    }

    function toggleTripEmptyState() {
        const tripList = document.getElementById('trip-list');
        const emptyState = document.getElementById('trip-empty-state');
        const hasTrips = getTripCardCount() > 0;

        if (tripList) {
            tripList.classList.toggle('hidden', !hasTrips);
        }

        if (emptyState) {
            emptyState.classList.toggle('hidden', hasTrips);
        }
    }

    function extractErrorMessage(data) {
        if (!data) return null;

        if (typeof data.message === 'string' && data.message.trim() !== '') {
            return data.message;
        }

        if (data.errors && typeof data.errors === 'object') {
            const firstKey = Object.keys(data.errors)[0];
            if (firstKey && Array.isArray(data.errors[firstKey]) && data.errors[firstKey][0]) {
                return data.errors[firstKey][0];
            }
        }

        return null;
    }

    function setButtonLoading(button, isLoading) {
        if (!button) return;

        if (isLoading) {
            button.disabled = true;
            button.classList.add('opacity-60', 'pointer-events-none');
        } else {
            button.disabled = false;
            button.classList.remove('opacity-60', 'pointer-events-none');
        }
    }

    function showToast(message, type = 'success') {
        const toast = document.getElementById('trip-toast');
        if (!toast) return;

        toast.textContent = message;
        toast.classList.remove('hidden', 'bg-emerald-600', 'bg-rose-600', 'text-white');
        toast.classList.add(type === 'error' ? 'bg-rose-600' : 'bg-emerald-600', 'text-white');

        clearTimeout(toast._timeout);
        toast._timeout = setTimeout(() => {
            toast.classList.add('hidden');
        }, 2200);
    }
</script>
</body>
</html>