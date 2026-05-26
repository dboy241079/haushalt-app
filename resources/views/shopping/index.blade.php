<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Einkaufsliste | Haushalt App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-900">
    <div class="mx-auto min-h-screen w-full max-w-md bg-slate-100">

        <section class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-sky-950 px-4 pb-6 pt-5 text-white">
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-sky-400/20 blur-3xl"></div>
            <div class="absolute -left-8 bottom-0 h-24 w-24 rounded-full bg-cyan-400/20 blur-2xl"></div>

            <div class="relative z-10">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-slate-300">
                            Einkauf
                        </p>
                        <h1 class="mt-1 truncate text-2xl font-bold">
                            {{ $household->name }}
                        </h1>
                        <p class="mt-1 text-sm text-slate-300">
                            Gemeinsam planen und abhaken
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
                            <p class="text-xs font-medium text-slate-300">Einkaufsliste</p>
                            <p class="mt-1 text-xl font-bold">Alles, was noch fehlt</p>
                            <p class="mt-1 text-sm text-slate-300">
                                Artikel strukturieren, kaufen und nachvollziehen.
                            </p>
                        </div>

                        <div class="flex h-14 w-14 items-center justify-center rounded-3xl bg-white/10 text-2xl ring-1 ring-white/15">
                            🛒
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <main class="app-main">
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
                        <p class="text-xs font-semibold uppercase tracking-wide text-sky-600">Neu</p>
                        <h2 class="text-lg font-bold text-slate-900">Artikel hinzufügen</h2>
                    </div>
                    <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">
                        Formular
                    </span>
                </div>

                <form action="{{ route('shopping.store') }}" method="POST" class="mt-4 space-y-4" data-ajax="shopping-create">
                    @csrf

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Artikel</label>
                        <input
                            type="text"
                            name="title"
                            value="{{ old('title') }}"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-sky-500 focus:bg-white"
                            placeholder="z. B. Grillkohle"
                            required
                        >
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Menge</label>
                            <input
                                type="text"
                                name="quantity"
                                value="{{ old('quantity') }}"
                                class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-sky-500 focus:bg-white"
                                placeholder="z. B. 2"
                            >
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Kategorie</label>
                            <input
                                type="text"
                                name="category"
                                value="{{ old('category') }}"
                                class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-sky-500 focus:bg-white"
                                placeholder="z. B. Lebensmittel"
                            >
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Trip zuordnen</label>
                        <select
                            name="trip_id"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-sky-500 focus:bg-white"
                        >
                            <option value="">Kein Trip / normaler Haushaltseinkauf</option>
                            @foreach($trips as $trip)
                                <option value="{{ $trip->id }}" {{ old('trip_id') == $trip->id ? 'selected' : '' }}>
                                    {{ $trip->title }}
                                    @if($trip->start_date)
                                        ({{ $trip->start_date->format('d.m.Y') }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Notiz</label>
                        <textarea
                            name="note"
                            rows="3"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-sky-500 focus:bg-white"
                            placeholder="Optional"
                        >{{ old('note') }}</textarea>
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-[24px] bg-gradient-to-r from-sky-400 to-blue-500 px-4 py-3 text-sm font-semibold text-white shadow-md"
                    >
                        Einkauf speichern
                    </button>
                </form>
            </section>

            <div class="mt-6 space-y-6">
                @php
                    $householdItems = $groupedItems['household'] ?? collect();
                @endphp

                <section class="rounded-[24px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Haushalt</p>
                            <h2 class="text-lg font-bold text-slate-900">Normale Einkaufsliste</h2>
                        </div>

                        <span
                            data-shopping-count="household"
                            class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700"
                        >
                            {{ $householdItems->count() }}
                        </span>
                    </div>

                    <div class="mt-4 space-y-2" id="shopping-items-household">
                        @forelse($householdItems as $item)
                            <div
                                class="rounded-[18px] bg-slate-50 px-3 py-3 ring-1 ring-slate-200"
                                data-shopping-item-id="{{ $item->id }}"
                                data-shopping-list="household"
                                data-shopping-bought="{{ $item->is_bought ? '1' : '0' }}"
                            >
                                <div class="flex items-center gap-3">
                                    <form
                                        action="{{ route('shopping.toggleBought', $item) }}"
                                        method="POST"
                                        data-ajax="shopping-toggle"
                                    >
                                        @csrf
                                        <button
                                            type="submit"
                                            data-shopping-toggle-button="1"
                                            class="flex h-8 w-8 items-center justify-center rounded-full {{ $item->is_bought ? 'bg-emerald-500 text-white' : 'bg-white text-slate-500 ring-1 ring-slate-300' }}"
                                        >
                                            {{ $item->is_bought ? '✓' : '○' }}
                                        </button>
                                    </form>

                                    <div class="min-w-0 flex-1">
                                        <p class="shopping-item-title text-sm font-semibold {{ $item->is_bought ? 'line-through text-slate-400' : 'text-slate-800' }}">
                                            {{ $item->title }}
                                        </p>

                                        <div class="mt-1 flex flex-wrap gap-2 text-[11px]">
                                            @if($item->quantity)
                                                <span class="rounded-full bg-white px-2 py-1 ring-1 ring-slate-200">{{ $item->quantity }}</span>
                                            @endif

                                            @if($item->category)
                                                <span class="rounded-full bg-white px-2 py-1 ring-1 ring-slate-200">{{ $item->category }}</span>
                                            @endif

                                            @if(!is_null($item->actual_price))
                                                <span
                                                    data-shopping-price="1"
                                                    class="rounded-full bg-emerald-100 px-2 py-1 ring-1 ring-emerald-200 text-emerald-700"
                                                >
                                                    {{ number_format((float) $item->actual_price, 2, ',', '.') }} €
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <form
                                        action="{{ route('shopping.destroy', $item) }}"
                                        method="POST"
                                        data-ajax="shopping-delete"
                                        data-confirm="Eintrag löschen?"
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
                            <p data-shopping-empty="household" class="text-sm text-slate-500">
                                Noch keine normalen Einkaufseinträge.
                            </p>
                        @endforelse
                    </div>
                </section>

                @foreach($trips as $trip)
                    @php
                        $tripKey = 'trip_' . $trip->id;
                        $tripItems = $groupedItems[$tripKey] ?? collect();
                    @endphp

                    <section class="rounded-[24px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Urlaubseinkauf</p>
                                <h2 class="text-lg font-bold text-slate-900">{{ $trip->title }}</h2>
                            </div>

                            <span
                                data-shopping-count="{{ $tripKey }}"
                                class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700"
                            >
                                {{ $tripItems->count() }}
                            </span>
                        </div>

                        <div class="mt-4 space-y-2" id="shopping-items-{{ $tripKey }}">
                            @forelse($tripItems as $item)
                                <div
                                    class="rounded-[18px] bg-slate-50 px-3 py-3 ring-1 ring-slate-200"
                                    data-shopping-item-id="{{ $item->id }}"
                                    data-shopping-list="{{ $tripKey }}"
                                    data-shopping-bought="{{ $item->is_bought ? '1' : '0' }}"
                                >
                                    <div class="flex items-center gap-3">
                                        <form
                                            action="{{ route('shopping.toggleBought', $item) }}"
                                            method="POST"
                                            data-ajax="shopping-toggle"
                                        >
                                            @csrf
                                            <button
                                                type="submit"
                                                data-shopping-toggle-button="1"
                                                class="flex h-8 w-8 items-center justify-center rounded-full {{ $item->is_bought ? 'bg-emerald-500 text-white' : 'bg-white text-slate-500 ring-1 ring-slate-300' }}"
                                            >
                                                {{ $item->is_bought ? '✓' : '○' }}
                                            </button>
                                        </form>

                                        <div class="min-w-0 flex-1">
                                            <p class="shopping-item-title text-sm font-semibold {{ $item->is_bought ? 'line-through text-slate-400' : 'text-slate-800' }}">
                                                {{ $item->title }}
                                            </p>

                                            <div class="mt-1 flex flex-wrap gap-2 text-[11px]">
                                                @if($item->quantity)
                                                    <span class="rounded-full bg-white px-2 py-1 ring-1 ring-slate-200">{{ $item->quantity }}</span>
                                                @endif

                                                @if($item->category)
                                                    <span class="rounded-full bg-white px-2 py-1 ring-1 ring-slate-200">{{ $item->category }}</span>
                                                @endif

                                                @if(!is_null($item->actual_price))
                                                    <span
                                                        data-shopping-price="1"
                                                        class="rounded-full bg-emerald-100 px-2 py-1 ring-1 ring-emerald-200 text-emerald-700"
                                                    >
                                                        {{ number_format((float) $item->actual_price, 2, ',', '.') }} €
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <form
                                            action="{{ route('shopping.destroy', $item) }}"
                                            method="POST"
                                            data-ajax="shopping-delete"
                                            data-confirm="Eintrag löschen?"
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
                                <p data-shopping-empty="{{ $tripKey }}" class="text-sm text-slate-500">
                                    Noch keine Urlaubseinkäufe für diesen Trip.
                                </p>
                            @endforelse
                        </div>
                    </section>
                @endforeach
            </div>
        </main>

        @include('partials.bottom-nav', ['active' => 'shopping'])
    </div>

    <div
        id="shopping-toast"
        class="hidden fixed left-1/2 top-4 z-50 w-[calc(100%-2rem)] max-w-md -translate-x-1/2 rounded-[18px] px-4 py-3 text-center text-sm font-semibold text-white shadow-xl"
    ></div>

    <div
        id="shopping-price-modal"
        class="fixed inset-0 z-50 hidden items-end justify-center bg-slate-900/60 p-4 sm:items-center"
    >
        <div class="w-full max-w-sm rounded-[28px] bg-white shadow-2xl ring-1 ring-slate-200">
            <div class="flex items-center justify-between border-b border-slate-200 px-4 py-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Einkauf erfassen</p>
                    <h3 class="text-lg font-bold text-slate-900">Wie viel hast du bezahlt?</h3>
                </div>

                <button
                    id="shopping-price-close"
                    type="button"
                    class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-lg font-bold text-slate-700"
                >
                    ×
                </button>
            </div>

            <form id="shopping-price-form" class="space-y-4 px-4 py-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Betrag in €</label>
                    <input
                        id="shopping-price-input"
                        type="number"
                        step="0.01"
                        min="0"
                        inputmode="decimal"
                        class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                        placeholder="z. B. 18.90"
                        required
                    >
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button
                        id="shopping-price-cancel"
                        type="button"
                        class="w-full rounded-[20px] bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700"
                    >
                        Abbrechen
                    </button>

                    <button
                        type="submit"
                        class="w-full rounded-[20px] bg-emerald-600 px-4 py-3 text-sm font-semibold text-white"
                    >
                        Speichern
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function getCsrfToken() {
            return document.querySelector('input[name="_token"]')?.value || '';
        }

        let pendingShoppingToggleForm = null;

        document.addEventListener('submit', async (event) => {
            const form = event.target.closest('form[data-ajax]');
            if (!form) return;

            event.preventDefault();

            const actionType = form.dataset.ajax;
            const confirmText = form.dataset.confirm;

            if (confirmText && !window.confirm(confirmText)) {
                return;
            }

            if (actionType === 'shopping-toggle') {
                const itemCard = form.closest('[data-shopping-item-id]');
                const isBought = itemCard?.dataset.shoppingBought === '1';

                if (!isBought) {
                    openShoppingPriceModal(form);
                    return;
                }
            }

            await executeShoppingRequest(form);
        });

        document.getElementById('shopping-price-form')?.addEventListener('submit', async (event) => {
            event.preventDefault();

            if (!pendingShoppingToggleForm) return;

            const priceInput = document.getElementById('shopping-price-input');
            const priceValue = priceInput?.value?.trim();

            if (!priceValue) {
                priceInput?.focus();
                return;
            }

            await executeShoppingRequest(pendingShoppingToggleForm, {
                actual_price: priceValue,
            });

            closeShoppingPriceModal();
        });

        document.getElementById('shopping-price-close')?.addEventListener('click', closeShoppingPriceModal);
        document.getElementById('shopping-price-cancel')?.addEventListener('click', closeShoppingPriceModal);

        async function executeShoppingRequest(form, extraData = {}) {
            const actionType = form.dataset.ajax;
            const button = form.querySelector('button[type="submit"]');

            setShoppingButtonLoading(button, true);

            try {
                const formData = new FormData(form);

                Object.entries(extraData).forEach(([key, value]) => {
                    formData.set(key, value);
                });

                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok || !data.ok) {
                    throw new Error(extractShoppingError(data) || 'Aktion fehlgeschlagen.');
                }

                if (actionType === 'shopping-create') {
                    insertShoppingItem(data.item);
                    updateShoppingCount(data.item.list_key, data.list_count);
                    form.reset();
                    showShoppingToast(data.message || 'Artikel wurde hinzugefügt.', 'success');
                    return;
                }

                if (actionType === 'shopping-toggle') {
                    updateShoppingItemState(data.item_id, data.is_bought, data.actual_price);
                    updateShoppingCount(data.list_key, data.list_count);
                    showShoppingToast(data.message || 'Artikel wurde aktualisiert.', 'success');
                    return;
                }

                if (actionType === 'shopping-delete') {
                    removeShoppingItem(data.item_id);
                    updateShoppingCount(data.list_key, data.list_count);
                    refreshShoppingListUi(data.list_key);
                    showShoppingToast(data.message || 'Artikel wurde gelöscht.', 'success');
                    return;
                }
            } catch (error) {
                showShoppingToast(error.message || 'Fehler beim Speichern.', 'error');
            } finally {
                setShoppingButtonLoading(button, false);
            }
        }

        function openShoppingPriceModal(form) {
            pendingShoppingToggleForm = form;

            const modal = document.getElementById('shopping-price-modal');
            const input = document.getElementById('shopping-price-input');

            if (!modal || !input) return;

            input.value = '';
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            setTimeout(() => input.focus(), 50);
        }

        function closeShoppingPriceModal() {
            const modal = document.getElementById('shopping-price-modal');
            const input = document.getElementById('shopping-price-input');

            pendingShoppingToggleForm = null;

            if (input) {
                input.value = '';
            }

            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }
        }

        function insertShoppingItem(item) {
            if (!item || !item.list_key) return;

            const container = document.getElementById(`shopping-items-${item.list_key}`);
            if (!container) return;

            const emptyState = container.querySelector(`[data-shopping-empty="${item.list_key}"]`);
            if (emptyState) {
                emptyState.remove();
            }

            container.insertAdjacentHTML('beforeend', buildShoppingItemHtml(item));
        }

        function buildShoppingItemHtml(item) {
            const quantityHtml = item.quantity
                ? `<span class="rounded-full bg-white px-2 py-1 ring-1 ring-slate-200">${escapeHtml(item.quantity)}</span>`
                : '';

            const categoryHtml = item.category
                ? `<span class="rounded-full bg-white px-2 py-1 ring-1 ring-slate-200">${escapeHtml(item.category)}</span>`
                : '';

            const priceHtml = item.actual_price !== null && item.actual_price !== undefined
                ? `<span data-shopping-price="1" class="rounded-full bg-emerald-100 px-2 py-1 ring-1 ring-emerald-200 text-emerald-700">${formatEuro(item.actual_price)}</span>`
                : '';

            return `
                <div
                    class="rounded-[18px] bg-slate-50 px-3 py-3 ring-1 ring-slate-200"
                    data-shopping-item-id="${item.id}"
                    data-shopping-list="${item.list_key}"
                    data-shopping-bought="${item.is_bought ? '1' : '0'}"
                >
                    <div class="flex items-center gap-3">
                        <form action="${item.toggle_url}" method="POST" data-ajax="shopping-toggle">
                            <input type="hidden" name="_token" value="${getCsrfToken()}">
                            <button
                                type="submit"
                                data-shopping-toggle-button="1"
                                class="flex h-8 w-8 items-center justify-center rounded-full ${item.is_bought ? 'bg-emerald-500 text-white' : 'bg-white text-slate-500 ring-1 ring-slate-300'}"
                            >
                                ${item.is_bought ? '✓' : '○'}
                            </button>
                        </form>

                        <div class="min-w-0 flex-1">
                            <p class="shopping-item-title text-sm font-semibold ${item.is_bought ? 'line-through text-slate-400' : 'text-slate-800'}">
                                ${escapeHtml(item.title)}
                            </p>

                            <div class="mt-1 flex flex-wrap gap-2 text-[11px]">
                                ${quantityHtml}
                                ${categoryHtml}
                                ${priceHtml}
                            </div>
                        </div>

                        <form
                            action="${item.delete_url}"
                            method="POST"
                            data-ajax="shopping-delete"
                            data-confirm="Eintrag löschen?"
                        >
                            <input type="hidden" name="_token" value="${getCsrfToken()}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button
                                type="submit"
                                class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700"
                            >
                                X
                            </button>
                        </form>
                    </div>
                </div>
            `;
        }

        function updateShoppingItemState(itemId, isBought, actualPrice = null) {
            const itemCard = document.querySelector(`[data-shopping-item-id="${itemId}"]`);
            if (!itemCard) return;

            itemCard.dataset.shoppingBought = isBought ? '1' : '0';

            const button = itemCard.querySelector('[data-shopping-toggle-button="1"]');
            const title = itemCard.querySelector('.shopping-item-title');
            const metaWrap = title?.nextElementSibling;
            let priceBadge = itemCard.querySelector('[data-shopping-price="1"]');

            if (button) {
                if (isBought) {
                    button.className = 'flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500 text-white';
                    button.textContent = '✓';
                } else {
                    button.className = 'flex h-8 w-8 items-center justify-center rounded-full bg-white text-slate-500 ring-1 ring-slate-300';
                    button.textContent = '○';
                }
            }

            if (title) {
                if (isBought) {
                    title.classList.remove('text-slate-800');
                    title.classList.add('line-through', 'text-slate-400');
                } else {
                    title.classList.remove('line-through', 'text-slate-400');
                    title.classList.add('text-slate-800');
                }
            }

            if (isBought && actualPrice !== null && actualPrice !== undefined && metaWrap) {
                if (!priceBadge) {
                    metaWrap.insertAdjacentHTML(
                        'beforeend',
                        `<span data-shopping-price="1" class="rounded-full bg-emerald-100 px-2 py-1 ring-1 ring-emerald-200 text-emerald-700">${formatEuro(actualPrice)}</span>`
                    );
                } else {
                    priceBadge.textContent = formatEuro(actualPrice);
                }
            }

            if (!isBought && priceBadge) {
                priceBadge.remove();
            }
        }

        function removeShoppingItem(itemId) {
            const itemCard = document.querySelector(`[data-shopping-item-id="${itemId}"]`);
            if (!itemCard) return;

            itemCard.remove();
        }

        function refreshShoppingListUi(listKey) {
            if (!listKey) return;

            const container = document.getElementById(`shopping-items-${listKey}`);
            if (!container) return;

            const items = container.querySelectorAll('[data-shopping-item-id]');
            const emptyState = container.querySelector(`[data-shopping-empty="${listKey}"]`);

            if (items.length === 0 && !emptyState) {
                const text = listKey === 'household'
                    ? 'Noch keine normalen Einkaufseinträge.'
                    : 'Noch keine Urlaubseinkäufe für diesen Trip.';

                container.innerHTML = `<p data-shopping-empty="${listKey}" class="text-sm text-slate-500">${text}</p>`;
            }

            if (items.length > 0 && emptyState) {
                emptyState.remove();
            }
        }

        function updateShoppingCount(listKey, forcedCount = null) {
            const badge = document.querySelector(`[data-shopping-count="${listKey}"]`);
            if (!badge) return;

            if (forcedCount !== null && forcedCount !== undefined) {
                badge.textContent = forcedCount;
                return;
            }

            const container = document.getElementById(`shopping-items-${listKey}`);
            if (!container) return;

            const count = container.querySelectorAll('[data-shopping-item-id]').length;
            badge.textContent = count;
        }

        function extractShoppingError(data) {
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

        function setShoppingButtonLoading(button, isLoading) {
            if (!button) return;

            if (isLoading) {
                button.disabled = true;
                button.classList.add('opacity-60', 'pointer-events-none');
            } else {
                button.disabled = false;
                button.classList.remove('opacity-60', 'pointer-events-none');
            }
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function formatEuro(value) {
            const number = Number(value ?? 0);
            return `${number.toFixed(2).replace('.', ',')} €`;
        }

        function showShoppingToast(message, type = 'success') {
            const toast = document.getElementById('shopping-toast');
            if (!toast) return;

            toast.textContent = message;
            toast.classList.remove('hidden', 'bg-emerald-600', 'bg-rose-600');
            toast.classList.add(type === 'error' ? 'bg-rose-600' : 'bg-emerald-600');

            clearTimeout(toast._timeout);
            toast._timeout = setTimeout(() => {
                toast.classList.add('hidden');
            }, 2200);
        }
    </script>
</body>
</html>