<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Kosten | Haushalt App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-900">
    <div class="app-shell mx-auto w-full max-w-md bg-slate-100">

@php
    $costHelpTexts = [
        'Kredit / Finanzierung' => 'Die monatliche Rate für dein Darlehen oder deinen Immobilienkredit.',
        'Strom' => 'Abschlag oder laufende Kosten für den Stromverbrauch im Haushalt.',
        'Wasser' => 'Kosten für Frischwasser und je nach Vertrag teilweise auch Abwasser.',
        'Heizung / Gas' => 'Monatliche Abschläge für Heizung oder Gasversorgung.',
        'Internet / Telefon' => 'Laufende Kosten für Internet, Festnetz oder Kombiverträge.',
        'Müll' => 'Gebühren für die Müllentsorgung.',
        'Grundsteuer' => 'Regelmäßige Steuer auf Grundstück oder Immobilie.',
        'Rücklagen / Instandhaltung' => 'Geld, das regelmäßig für Reparaturen und zukünftige Instandsetzungen eingeplant wird.',
        'Verwaltung / laufende Objektkosten' => 'Kosten für Verwaltung oder organisatorische Betreuung der Immobilie.',
        'Gemeinschaftliche Nebenkosten' => 'Nebenkosten, die gemeinschaftlich für das Objekt anfallen.',
        'Rücklagen Vermietung' => 'Zurückgelegtes Geld speziell für vermietete Einheiten oder spätere Reparaturen.',
        'Miete' => 'Die monatliche Kalt- oder Warmmiete für deine Wohnung oder dein Haus.',
        'Hausgeld' => 'Monatliche Zahlung bei Eigentumswohnungen für gemeinschaftliche Kosten und Rücklagen.',
        'Rundfunkbeitrag' => 'Gesetzlich vorgeschriebener Rundfunkbeitrag pro Haushalt.',
        'Verwaltung' => 'Laufende Kosten für die Verwaltung einer vermieteten Immobilie.',
        'Müll / Wasser / Energie (falls Eigentümer trägt)' => 'Kosten, die beim Vermieter verbleiben, wenn sie nicht auf Mieter umgelegt werden.',
    ];
@endphp
   
        <section class="top-safe-area relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950 px-4 pb-6 text-white">
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-cyan-400/20 blur-3xl"></div>
            <div class="absolute -left-8 bottom-0 h-24 w-24 rounded-full bg-sky-400/20 blur-2xl"></div>

            <div class="relative z-10 pt-5">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-slate-300">
                Kosten
            </p>
            <h1 class="mt-1 truncate text-2xl font-bold">
                {{ $household->name }}
            </h1>
            <p class="mt-1 text-sm text-slate-300">
                Wohnen & Fixkosten im Überblick
            </p>
        </div>

        <a href="{{ route('dashboard') }}"
           class="rounded-2xl border border-white/15 bg-white/10 px-3 py-2 text-center text-sm font-medium text-white backdrop-blur">
            Zurück
        </a>
    </div>

    <div class="mt-5 grid grid-cols-2 gap-3">
    <div class="rounded-[24px] border border-white/10 bg-white/10 p-4 backdrop-blur">
        <p class="text-xs text-slate-300">Kosten</p>
        <p class="mt-1 text-2xl font-bold">{{ $summary['count'] }}</p>
        <p class="mt-1 text-xs text-slate-300">aktive Positionen</p>
    </div>

    <div class="rounded-[24px] border border-white/10 bg-white/10 p-4 backdrop-blur">
        <p class="text-xs text-slate-300">Kosten / Monat</p>
        <p class="mt-1 text-2xl font-bold">{{ number_format($summary['monthly_total'], 2, ',', '.') }} €</p>
        <p class="mt-1 text-xs text-slate-300">umgerechnet</p>
    </div>

    @if($showIncomeSection)
        <div class="rounded-[24px] border border-white/10 bg-white/10 p-4 backdrop-blur">
            <p class="text-xs text-slate-300">Einnahmen</p>
            <p class="mt-1 text-2xl font-bold">{{ $summary['income_count'] }}</p>
            <p class="mt-1 text-xs text-slate-300">aktive Positionen</p>
        </div>

        <div class="rounded-[24px] border border-white/10 bg-white/10 p-4 backdrop-blur">
            <p class="text-xs text-slate-300">Einnahmen / Monat</p>
            <p class="mt-1 text-2xl font-bold">{{ number_format($summary['monthly_income_total'], 2, ',', '.') }} €</p>
            <p class="mt-1 text-xs text-slate-300">umgerechnet</p>
        </div>

        <div class="col-span-2 rounded-[24px] border border-white/10 bg-white/10 p-4 backdrop-blur">
            <p class="text-xs text-slate-300">Netto / Monat</p>
            <p class="mt-1 text-2xl font-bold">{{ number_format($summary['monthly_net_total'], 2, ',', '.') }} €</p>
            <p class="mt-1 text-xs text-slate-300">Einnahmen minus Kosten</p>
        </div>
    @endif
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

            @if($household->costs_setup_completed_at && isset($missingInsuranceTypes) && $missingInsuranceTypes->isNotEmpty())
                <section class="rounded-[32px] border border-amber-200 bg-amber-50 p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">Check</p>
                            <h2 class="text-lg font-bold text-slate-900">Versicherungen prüfen</h2>
                            <p class="mt-1 text-sm text-slate-600">
                                Für euer Wohnmodell fehlen noch passende Versicherungen.
                            </p>
                        </div>

                        <a href="{{ route('insurances.index') }}"
                           class="rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-amber-700 ring-1 ring-amber-200">
                            Öffnen
                        </a>
                    </div>

                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($missingInsuranceTypes as $type)
                            <span class="rounded-full bg-white px-3 py-2 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                                {{ $type }} fehlt
                            </span>
                        @endforeach
                    </div>
                </section>
            @endif

             @if($showIncomeSection)
    <section class="rounded-[32px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Einnahmen</p>
                <h2 class="text-lg font-bold text-slate-900">Mieteinnahmen & Co.</h2>
            </div>
            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                {{ $incomeItems->count() }}
            </span>
        </div>

        <form action="{{ route('costs.incomes.store') }}" method="POST" class="mt-4 space-y-4">
            @csrf

            <div>
                <label for="income_title" class="mb-1 block text-sm font-medium text-slate-700">Bezeichnung</label>
                <input
                    id="income_title"
                    name="title"
                    type="text"
                    class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                    placeholder="z. B. Kaltmiete"
                    required
                >
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label for="income_interval" class="mb-1 block text-sm font-medium text-slate-700">Intervall</label>
                    <select
                        id="income_interval"
                        name="interval"
                        class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                        required
                    >
                        @foreach($intervalLabels as $value => $label)
                            <option value="{{ $value }}" {{ $value === 'monthly' ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="income_amount" class="mb-1 block text-sm font-medium text-slate-700">Betrag</label>
                    <input
                        id="income_amount"
                        name="amount"
                        type="number"
                        step="0.01"
                        min="0"
                        class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                        placeholder="z. B. 850.00"
                    >
                </div>
            </div>

            <div>
                <label for="income_notes" class="mb-1 block text-sm font-medium text-slate-700">Notiz</label>
                <textarea
                    id="income_notes"
                    name="notes"
                    rows="3"
                    class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                    placeholder="Optional"
                ></textarea>
            </div>

            <button
                type="submit"
                class="w-full rounded-[24px] bg-gradient-to-r from-emerald-400 to-teal-500 px-4 py-3 text-sm font-semibold text-white shadow-md"
            >
                Einnahme speichern
            </button>
        </form>

        @if($incomeItems->isNotEmpty())
            <div class="mt-4 space-y-3">
                @foreach($incomeItems as $item)
                    <details class="group rounded-[24px] bg-emerald-50 p-3 ring-1 ring-emerald-100">
                        <summary class="flex cursor-pointer list-none items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-start gap-2">
    <p class="font-bold text-slate-900">{{ $item->title }}</p>

    @if(!empty($costHelpTexts[$item->title]))
        <button
            type="button"
            class="cost-help-toggle mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-white text-[11px] font-bold text-cyan-700 ring-1 ring-cyan-200"
            data-help-target="help-{{ $item->id }}"
            aria-expanded="false"
            onclick="event.preventDefault(); event.stopPropagation();"
        >
            i
        </button>
    @endif
</div>

@if(!empty($costHelpTexts[$item->title]))
    <div
        id="help-{{ $item->id }}"
        class="cost-help-box mt-2 hidden rounded-[16px] border border-cyan-200 bg-white/80 px-3 py-2 text-xs leading-5 text-slate-600 ring-1 ring-cyan-100"
    >
        {{ $costHelpTexts[$item->title] }}
    </div>
@endif

                                <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                    <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                        {{ $item->category }}
                                    </span>

                                    <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                        {{ $intervalLabels[$item->interval] ?? $item->interval }}
                                    </span>

                                    @if($item->is_auto_generated)
                                        <span class="rounded-full bg-white px-2 py-1 font-medium text-emerald-700 ring-1 ring-emerald-200">
                                            Standard
                                        </span>
                                    @endif
                                </div>

                                @if($item->notes)
                                    <p class="mt-2 text-sm text-slate-600">{{ $item->notes }}</p>
                                @endif
                            </div>

                            <div class="text-right">
                                <span class="rounded-full bg-emerald-600 px-2.5 py-1 text-xs font-semibold text-white">
                                    {{ $item->amount !== null ? number_format((float) $item->amount, 2, ',', '.') . ' €' : 'offen' }}
                                </span>
                                <p class="mt-2 text-xs font-semibold text-slate-500 group-open:hidden">Bearbeiten</p>
                            </div>
                        </summary>

                        <div class="mt-4 border-t border-emerald-100 pt-4">
                            <form action="{{ route('costs.incomes.update', $item) }}" method="POST" class="space-y-4">
                                @csrf
                                @method('PATCH')

                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700">Bezeichnung</label>
                                    <input
                                        name="title"
                                        type="text"
                                        value="{{ $item->title }}"
                                        class="w-full rounded-[20px] border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-500"
                                        required
                                    >
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700">Intervall</label>
                                        <select
                                            name="interval"
                                            class="w-full rounded-[20px] border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-500"
                                            required
                                        >
                                            @foreach($intervalLabels as $value => $label)
                                                <option value="{{ $value }}" {{ $item->interval === $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div>
                                        <label class="mb-1 block text-sm font-medium text-slate-700">Betrag</label>
                                        <input
                                            name="amount"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            value="{{ $item->amount }}"
                                            class="w-full rounded-[20px] border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-500"
                                        >
                                    </div>
                                </div>

                                <div>
                                    <label class="mb-1 block text-sm font-medium text-slate-700">Notiz</label>
                                    <textarea
                                        name="notes"
                                        rows="3"
                                        class="w-full rounded-[20px] border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-500"
                                    >{{ $item->notes }}</textarea>
                                </div>

                                <button
                                    type="submit"
                                    class="w-full rounded-[18px] bg-slate-900 px-3 py-2.5 text-xs font-semibold text-white shadow-sm"
                                >
                                    Speichern
                                </button>
                            </form>

                            <form action="{{ route('costs.incomes.destroy', $item) }}" method="POST" class="mt-2" onsubmit="return confirm('Einnahme wirklich löschen?');">
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="w-full rounded-[18px] bg-red-600 px-3 py-2.5 text-xs font-semibold text-white shadow-sm"
                                >
                                    Löschen
                                </button>
                            </form>
                        </div>
                    </details>
                @endforeach
            </div>
        @endif
    </section>
@endif

            @if(!$household->costs_setup_completed_at)
                <section id="cost-setup-card" class="rounded-[32px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-600">Setup</p>
                            <h2 class="text-lg font-bold text-slate-900">Wohnmodell festlegen</h2>
                            <p class="mt-1 text-sm text-slate-500">Darauf basierend erzeugen wir die passenden Standardkosten.</p>
                        </div>
                        <span class="rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold text-cyan-700">
                            Assistent
                        </span>
                    </div>

                    <div class="mt-4">
                        <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200">
                            <div id="setup-progress-bar" class="h-full w-[33%] rounded-full bg-gradient-to-r from-cyan-400 to-sky-500 transition-all duration-300"></div>
                        </div>
                        <p id="setup-progress-text" class="mt-2 text-xs font-medium text-slate-500">Schritt 1 von 3</p>
                    </div>

                    <form action="{{ route('costs.setup') }}" method="POST" class="mt-5 space-y-5" id="cost-setup-form">
                        @csrf

                        <div>
                            <p class="mb-2 block text-sm font-medium text-slate-700">1. Wohnst du zur Miete oder im Eigentum?</p>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" name="living_mode" value="rent" class="peer sr-only" {{ old('living_mode') === 'rent' ? 'checked' : '' }}>
                                    <span class="flex min-h-[60px] items-center justify-center rounded-[22px] border border-slate-300 bg-slate-50 px-4 py-3 text-center text-sm font-semibold text-slate-700 transition peer-checked:border-cyan-500 peer-checked:bg-cyan-500 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-cyan-500/20">
                                        Miete
                                    </span>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="living_mode" value="ownership" class="peer sr-only" {{ old('living_mode') === 'ownership' ? 'checked' : '' }}>
                                    <span class="flex min-h-[60px] items-center justify-center rounded-[22px] border border-slate-300 bg-slate-50 px-4 py-3 text-center text-sm font-semibold text-slate-700 transition peer-checked:border-cyan-500 peer-checked:bg-cyan-500 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-cyan-500/20">
                                        Eigentum
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div id="ownership-kind-wrap" class="hidden">
                            <p class="mb-2 block text-sm font-medium text-slate-700">2. Ist es eine Wohnung oder ein Haus?</p>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="cursor-pointer">
                                    <input type="radio" name="ownership_kind" value="apartment" class="peer sr-only" {{ old('ownership_kind') === 'apartment' ? 'checked' : '' }}>
                                    <span class="flex min-h-[60px] items-center justify-center rounded-[22px] border border-slate-300 bg-slate-50 px-4 py-3 text-center text-sm font-semibold text-slate-700 transition peer-checked:border-cyan-500 peer-checked:bg-cyan-500 peer-checked:text-white">
                                        Wohnung
                                    </span>
                                </label>

                                <label class="cursor-pointer">
                                    <input type="radio" name="ownership_kind" value="house" class="peer sr-only" {{ old('ownership_kind') === 'house' ? 'checked' : '' }}>
                                    <span class="flex min-h-[60px] items-center justify-center rounded-[22px] border border-slate-300 bg-slate-50 px-4 py-3 text-center text-sm font-semibold text-slate-700 transition peer-checked:border-cyan-500 peer-checked:bg-cyan-500 peer-checked:text-white">
                                        Haus
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div id="house-usage-wrap" class="hidden">
                            <p class="mb-2 block text-sm font-medium text-slate-700">3. Wie wird das Haus genutzt?</p>
                            <div class="space-y-3">
                                <label class="cursor-pointer block">
                                    <input type="radio" name="house_usage" value="self" class="peer sr-only" {{ old('house_usage') === 'self' ? 'checked' : '' }}>
                                    <span class="flex min-h-[56px] items-center rounded-[22px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition peer-checked:border-cyan-500 peer-checked:bg-cyan-500 peer-checked:text-white">
                                        Haus selbst bewohnt
                                    </span>
                                </label>

                                <label class="cursor-pointer block">
                                    <input type="radio" name="house_usage" value="partial_rent" class="peer sr-only" {{ old('house_usage') === 'partial_rent' ? 'checked' : '' }}>
                                    <span class="flex min-h-[56px] items-center rounded-[22px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition peer-checked:border-cyan-500 peer-checked:bg-cyan-500 peer-checked:text-white">
                                        Haus teilweise vermietet
                                    </span>
                                </label>

                                <label class="cursor-pointer block">
                                    <input type="radio" name="house_usage" value="full_rent" class="peer sr-only" {{ old('house_usage') === 'full_rent' ? 'checked' : '' }}>
                                    <span class="flex min-h-[56px] items-center rounded-[22px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-700 transition peer-checked:border-cyan-500 peer-checked:bg-cyan-500 peer-checked:text-white">
                                        Haus komplett vermietet
                                    </span>
                                </label>
                            </div>
                        </div>

                        <button
                            type="submit"
                            class="w-full rounded-[24px] bg-gradient-to-r from-cyan-400 to-sky-500 px-4 py-3 text-sm font-semibold text-white shadow-md"
                        >
                            Setup abschließen
                        </button>
                    </form>
                </section>
            @else
                <section class="rounded-[32px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-600">Wohnmodell</p>
            <h2 class="text-lg font-bold text-slate-900">Einstellungen</h2>
        </div>
        <span class="rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold text-cyan-700">
            Aktiv
        </span>
    </div>

    <div class="mt-4 flex flex-wrap gap-2 text-xs">
        @if($household->living_mode)
            <span class="rounded-full bg-white px-3 py-2 font-semibold text-slate-700 ring-1 ring-slate-200">
                {{ $livingModeLabels[$household->living_mode] ?? $household->living_mode }}
            </span>
        @endif

        @if($household->ownership_kind)
            <span class="rounded-full bg-white px-3 py-2 font-semibold text-slate-700 ring-1 ring-slate-200">
                {{ $ownershipKindLabels[$household->ownership_kind] ?? $household->ownership_kind }}
            </span>
        @endif

        @if($household->house_usage)
            <span class="rounded-full bg-white px-3 py-2 font-semibold text-slate-700 ring-1 ring-slate-200">
                {{ $houseUsageLabels[$household->house_usage] ?? $household->house_usage }}
            </span>
        @endif
    </div>

    <div class="mt-4">
        <button
            type="button"
            id="cost-model-edit-toggle"
            class="rounded-full bg-white px-3 py-1.5 text-xs font-semibold text-cyan-700 ring-1 ring-cyan-200 transition hover:bg-cyan-50"
        >
            Bearbeiten
        </button>
    </div>

    <div
        id="cost-model-edit-panel"
        class="mt-4 max-h-0 overflow-hidden opacity-0 transition-all duration-500 ease-in-out"
    >
        <div class="rounded-[20px] border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800">
            Nur die automatisch erzeugten Standardkosten werden neu aufgebaut. Eigene manuell angelegte Kosten bleiben erhalten.
        </div>

        <form action="{{ route('costs.setup') }}" method="POST" class="mt-4 space-y-5">
            @csrf

            <div id="cost-question-1">
                <p class="mb-2 block text-sm font-medium text-slate-700">1. Wohnst du zur Miete oder im Eigentum?</p>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input
                            type="radio"
                            name="living_mode"
                            value="rent"
                            class="peer sr-only"
                            {{ old('living_mode', $household->living_mode) === 'rent' ? 'checked' : '' }}
                        >
                        <span class="flex min-h-[60px] items-center justify-center rounded-[22px] border border-slate-300 bg-white px-4 py-3 text-center text-sm font-semibold text-slate-700 transition-all duration-200 peer-checked:scale-[1.02] peer-checked:border-cyan-500 peer-checked:bg-cyan-500 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-cyan-500/20">
                            Miete
                        </span>
                    </label>

                    <label class="cursor-pointer">
                        <input
                            type="radio"
                            name="living_mode"
                            value="ownership"
                            class="peer sr-only"
                            {{ old('living_mode', $household->living_mode) === 'ownership' ? 'checked' : '' }}
                        >
                        <span class="flex min-h-[60px] items-center justify-center rounded-[22px] border border-slate-300 bg-white px-4 py-3 text-center text-sm font-semibold text-slate-700 transition-all duration-200 peer-checked:scale-[1.02] peer-checked:border-cyan-500 peer-checked:bg-cyan-500 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-cyan-500/20">
                            Eigentum
                        </span>
                    </label>
                </div>
            </div>

            <div
                id="cost-question-2"
                class="max-h-0 overflow-hidden opacity-0 transition-all duration-500 ease-in-out"
            >
                <p class="mb-2 block text-sm font-medium text-slate-700">2. Ist es eine Wohnung oder ein Haus?</p>
                <div class="grid grid-cols-2 gap-3">
                    <label class="cursor-pointer">
                        <input
                            type="radio"
                            name="ownership_kind"
                            value="apartment"
                            class="peer sr-only"
                            {{ old('ownership_kind', $household->ownership_kind) === 'apartment' ? 'checked' : '' }}
                        >
                        <span class="flex min-h-[60px] items-center justify-center rounded-[22px] border border-slate-300 bg-white px-4 py-3 text-center text-sm font-semibold text-slate-700 transition-all duration-200 peer-checked:scale-[1.02] peer-checked:border-cyan-500 peer-checked:bg-cyan-500 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-cyan-500/20">
                            Wohnung
                        </span>
                    </label>

                    <label class="cursor-pointer">
                        <input
                            type="radio"
                            name="ownership_kind"
                            value="house"
                            class="peer sr-only"
                            {{ old('ownership_kind', $household->ownership_kind) === 'house' ? 'checked' : '' }}
                        >
                        <span class="flex min-h-[60px] items-center justify-center rounded-[22px] border border-slate-300 bg-white px-4 py-3 text-center text-sm font-semibold text-slate-700 transition-all duration-200 peer-checked:scale-[1.02] peer-checked:border-cyan-500 peer-checked:bg-cyan-500 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-cyan-500/20">
                            Haus
                        </span>
                    </label>
                </div>
            </div>

            <div
                id="cost-question-3"
                class="max-h-0 overflow-hidden opacity-0 transition-all duration-500 ease-in-out"
            >
                <p class="mb-2 block text-sm font-medium text-slate-700">3. Wie wird das Haus genutzt?</p>
                <div class="space-y-3">
                    <label class="cursor-pointer block">
                        <input
                            type="radio"
                            name="house_usage"
                            value="self"
                            class="peer sr-only"
                            {{ old('house_usage', $household->house_usage) === 'self' ? 'checked' : '' }}
                        >
                        <span class="flex min-h-[56px] items-center rounded-[22px] border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition-all duration-200 peer-checked:scale-[1.01] peer-checked:border-cyan-500 peer-checked:bg-cyan-500 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-cyan-500/20">
                            Haus selbst bewohnt
                        </span>
                    </label>

                    <label class="cursor-pointer block">
                        <input
                            type="radio"
                            name="house_usage"
                            value="partial_rent"
                            class="peer sr-only"
                            {{ old('house_usage', $household->house_usage) === 'partial_rent' ? 'checked' : '' }}
                        >
                        <span class="flex min-h-[56px] items-center rounded-[22px] border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition-all duration-200 peer-checked:scale-[1.01] peer-checked:border-cyan-500 peer-checked:bg-cyan-500 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-cyan-500/20">
                            Haus teilweise vermietet
                        </span>
                    </label>

                    <label class="cursor-pointer block">
                        <input
                            type="radio"
                            name="house_usage"
                            value="full_rent"
                            class="peer sr-only"
                            {{ old('house_usage', $household->house_usage) === 'full_rent' ? 'checked' : '' }}
                        >
                        <span class="flex min-h-[56px] items-center rounded-[22px] border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition-all duration-200 peer-checked:scale-[1.01] peer-checked:border-cyan-500 peer-checked:bg-cyan-500 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-cyan-500/20">
                            Haus komplett vermietet
                        </span>
                    </label>
                </div>
            </div>

            <div
                id="cost-model-submit-wrap"
                class="max-h-0 overflow-hidden opacity-0 transition-all duration-500 ease-in-out"
            >
                <button
                    type="submit"
                    class="w-full rounded-[24px] bg-gradient-to-r from-cyan-400 to-sky-500 px-4 py-3 text-sm font-semibold text-white shadow-md"
                >
                    Wohnmodell aktualisieren
                </button>
            </div>
        </form>
    </div>
</section>

                <section class="rounded-[32px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-600">Neu</p>
                            <h2 class="text-lg font-bold text-slate-900">Eigene Kosten hinzufügen</h2>
                        </div>
                        <span class="rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold text-cyan-700">
                            Formular
                        </span>
                    </div>

                    <form action="{{ route('costs.items.store') }}" method="POST" class="mt-4 space-y-4">
                        @csrf

                        <div>
                            <label for="title" class="mb-1 block text-sm font-medium text-slate-700">Bezeichnung</label>
                            <input
                                id="title"
                                name="title"
                                type="text"
                                value="{{ old('title') }}"
                                class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-cyan-500 focus:bg-white"
                                placeholder="z. B. Abwasser"
                                required
                            >
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label for="interval" class="mb-1 block text-sm font-medium text-slate-700">Intervall</label>
                                <select
                                    id="interval"
                                    name="interval"
                                    class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-cyan-500 focus:bg-white"
                                    required
                                >
                                    @foreach($intervalLabels as $value => $label)
                                        <option value="{{ $value }}" {{ old('interval', 'monthly') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="amount" class="mb-1 block text-sm font-medium text-slate-700">Betrag</label>
                                <input
                                    id="amount"
                                    name="amount"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    value="{{ old('amount') }}"
                                    class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-cyan-500 focus:bg-white"
                                    placeholder="z. B. 49.90"
                                >
                            </div>
                        </div>

                        <div>
                            <label for="notes" class="mb-1 block text-sm font-medium text-slate-700">Notiz</label>
                            <textarea
                                id="notes"
                                name="notes"
                                rows="3"
                                class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-cyan-500 focus:bg-white"
                                placeholder="Optional"
                            >{{ old('notes') }}</textarea>
                        </div>

                        <button
                            type="submit"
                            class="w-full rounded-[24px] bg-gradient-to-r from-cyan-400 to-sky-500 px-4 py-3 text-sm font-semibold text-white shadow-md"
                        >
                            Kosten speichern
                        </button>
                    </form>
                </section>

                <section class="rounded-[32px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-600">Kosten</p>
                            <h2 class="text-lg font-bold text-slate-900">Aktive Positionen</h2>
                        </div>
                        <span class="rounded-full bg-cyan-100 px-3 py-1 text-xs font-semibold text-cyan-700">
                            {{ $costItems->count() }}
                        </span>
                    </div>

                    @if($costItems->isEmpty())
                        <div class="mt-4 rounded-[24px] bg-slate-50 px-4 py-6 text-center ring-1 ring-slate-200">
                            <div class="text-2xl">🏠</div>
                            <p class="mt-2 text-sm font-semibold text-slate-800">Noch keine Kosten vorhanden</p>
                        </div>
                    @else
                        <div class="mt-4 space-y-3">
                            @foreach($costItems as $item)
                                <details class="group rounded-[24px] bg-cyan-50 p-3 ring-1 ring-cyan-100">
                                    <summary class="flex cursor-pointer list-none items-start justify-between gap-3">
                                        

<div class="min-w-0">
    <div class="flex items-start gap-2">
        <p class="font-bold text-slate-900">{{ $item->title }}</p>

        @if(!empty($costHelpTexts[$item->title]))
            <button
                type="button"
                class="cost-help-toggle mt-0.5 inline-flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-white text-[11px] font-bold text-cyan-700 ring-1 ring-cyan-200"
                data-help-target="help-{{ $item->id }}"
                aria-expanded="false"
                onclick="event.preventDefault(); event.stopPropagation();"
            >
                i
            </button>
        @endif
    </div>

    @if(!empty($costHelpTexts[$item->title]))
        <div
            id="help-{{ $item->id }}"
            class="cost-help-box mt-2 hidden rounded-[16px] border border-cyan-200 bg-white/80 px-3 py-2 text-xs leading-5 text-slate-600 ring-1 ring-cyan-100"
        >
            {{ $costHelpTexts[$item->title] }}
        </div>
    @endif

    <div class="mt-2 flex flex-wrap gap-2 text-xs">
        <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
            {{ $item->category }}
        </span>

        <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
            {{ $intervalLabels[$item->interval] ?? $item->interval }}
        </span>

        @if($item->is_auto_generated)
            <span class="rounded-full bg-white px-2 py-1 font-medium text-cyan-700 ring-1 ring-cyan-200">
                Standard
            </span>
        @endif
    </div>

    @if($item->notes)
        <p class="mt-2 text-sm text-slate-600">{{ $item->notes }}</p>
    @endif
</div>

                                        <div class="text-right">
                                            <span class="rounded-full bg-cyan-600 px-2.5 py-1 text-xs font-semibold text-white">
                                                {{ $item->amount !== null ? number_format((float) $item->amount, 2, ',', '.') . ' €' : 'offen' }}
                                            </span>
                                            <p class="mt-2 text-xs font-semibold text-slate-500 group-open:hidden">Bearbeiten</p>
                                        </div>
                                    </summary>

                                    <div class="mt-4 border-t border-cyan-100 pt-4">
                                        <form action="{{ route('costs.items.update', $item) }}" method="POST" class="space-y-4">
                                            @csrf
                                            @method('PATCH')

                                            <div>
                                                <label class="mb-1 block text-sm font-medium text-slate-700">Bezeichnung</label>
                                                <input
                                                    name="title"
                                                    type="text"
                                                    value="{{ old('title', $item->title) }}"
                                                    class="w-full rounded-[20px] border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-cyan-500"
                                                    required
                                                >
                                            </div>

                                            <div class="grid grid-cols-2 gap-3">
                                                <div>
                                                    <label class="mb-1 block text-sm font-medium text-slate-700">Intervall</label>
                                                    <select
                                                        name="interval"
                                                        class="w-full rounded-[20px] border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-cyan-500"
                                                        required
                                                    >
                                                        @foreach($intervalLabels as $value => $label)
                                                            <option value="{{ $value }}" {{ $item->interval === $value ? 'selected' : '' }}>
                                                                {{ $label }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="mb-1 block text-sm font-medium text-slate-700">Betrag</label>
                                                    <input
                                                        name="amount"
                                                        type="number"
                                                        step="0.01"
                                                        min="0"
                                                        value="{{ old('amount', $item->amount) }}"
                                                        class="w-full rounded-[20px] border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-cyan-500"
                                                        placeholder="z. B. 59.90"
                                                    >
                                                </div>
                                            </div>

                                            <div>
                                                <label class="mb-1 block text-sm font-medium text-slate-700">Notiz</label>
                                                <textarea
                                                    name="notes"
                                                    rows="3"
                                                    class="w-full rounded-[20px] border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-cyan-500"
                                                    placeholder="Optional"
                                                >{{ old('notes', $item->notes) }}</textarea>
                                            </div>

                                            <div class="grid grid-cols-2 gap-2">
                                                <button
                                                    type="submit"
                                                    class="w-full rounded-[18px] bg-slate-900 px-3 py-2.5 text-xs font-semibold text-white shadow-sm"
                                                >
                                                    Speichern
                                                </button>
                                            </div>
                                        </form>

                                        <form action="{{ route('costs.items.destroy', $item) }}" method="POST" class="mt-2" onsubmit="return confirm('Kostenposition wirklich löschen?');">
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="w-full rounded-[18px] bg-red-600 px-3 py-2.5 text-xs font-semibold text-white shadow-sm"
                                            >
                                                Löschen
                                            </button>
                                        </form>
                                    </div>
                                </details>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endif
        </main>

        @include('partials.bottom-nav', ['active' => 'costs'])
    </div>

    <script>
document.addEventListener('DOMContentLoaded', () => {
    const setupProgressBar = document.getElementById('setup-progress-bar');
    const setupProgressText = document.getElementById('setup-progress-text');

    const setupLivingModeInputs = document.querySelectorAll('#cost-setup-card input[name="living_mode"]');
    const setupOwnershipKindInputs = document.querySelectorAll('#cost-setup-card input[name="ownership_kind"]');
    const setupHouseUsageInputs = document.querySelectorAll('#cost-setup-card input[name="house_usage"]');

    const setupOwnershipWrap = document.querySelector('#cost-setup-card #ownership-kind-wrap');
    const setupHouseUsageWrap = document.querySelector('#cost-setup-card #house-usage-wrap');

    document.querySelectorAll('.cost-help-toggle').forEach((button) => {
    button.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();

        const targetId = button.dataset.helpTarget;
        const box = document.getElementById(targetId);

        if (!box) {
            return;
        }

        const isHidden = box.classList.contains('hidden');

        document.querySelectorAll('.cost-help-box').forEach((item) => {
            item.classList.add('hidden');
        });

        document.querySelectorAll('.cost-help-toggle').forEach((item) => {
            item.setAttribute('aria-expanded', 'false');
        });

        if (isHidden) {
            box.classList.remove('hidden');
            button.setAttribute('aria-expanded', 'true');
        }
    });
});

    function getCheckedValue(scope, name) {
        return scope.querySelector(`input[name="${name}"]:checked`)?.value || '';
    }

    function showStep(element, maxHeight = '500px') {
        if (!element) return;
        element.style.maxHeight = maxHeight;
        element.style.opacity = '1';
        element.style.marginTop = '1rem';
    }

    function hideStep(element) {
        if (!element) return;
        element.style.maxHeight = '0px';
        element.style.opacity = '0';
        element.style.marginTop = '0px';
    }

    function updateSetupView() {
        const setupCard = document.getElementById('cost-setup-card');
        if (!setupCard || !setupProgressBar || !setupProgressText) {
            return;
        }

        const livingMode = getCheckedValue(setupCard, 'living_mode');
        const ownershipKind = getCheckedValue(setupCard, 'ownership_kind');

        if (livingMode === 'ownership') {
            setupOwnershipWrap?.classList.remove('hidden');
        } else {
            setupOwnershipWrap?.classList.add('hidden');
            setupHouseUsageWrap?.classList.add('hidden');
        }

        if (livingMode === 'ownership' && ownershipKind === 'house') {
            setupHouseUsageWrap?.classList.remove('hidden');
        } else {
            setupHouseUsageWrap?.classList.add('hidden');
        }

        let width = '33%';

        if (livingMode === 'rent') {
            width = '100%';
            setupProgressText.textContent = 'Fertig nach Schritt 1';
        } else if (livingMode === 'ownership' && ownershipKind !== 'house' && ownershipKind !== '') {
            width = '100%';
            setupProgressText.textContent = 'Fertig nach Schritt 2';
        } else if (livingMode === 'ownership' && ownershipKind === 'house') {
            const houseUsage = getCheckedValue(setupCard, 'house_usage');
            width = houseUsage ? '100%' : '66%';
            setupProgressText.textContent = houseUsage ? 'Fertig nach Schritt 3' : 'Schritt 3 von 3';
        } else if (livingMode === 'ownership') {
            width = '66%';
            setupProgressText.textContent = 'Schritt 2 von 3';
        } else {
            setupProgressText.textContent = 'Schritt 1 von 3';
        }

        setupProgressBar.style.width = width;
    }

    [...setupLivingModeInputs, ...setupOwnershipKindInputs, ...setupHouseUsageInputs].forEach(input => {
        input.addEventListener('change', updateSetupView);
    });

    updateSetupView();

    const editToggle = document.getElementById('cost-model-edit-toggle');
    const editPanel = document.getElementById('cost-model-edit-panel');

    const question2 = document.getElementById('cost-question-2');
    const question3 = document.getElementById('cost-question-3');
    const submitWrap = document.getElementById('cost-model-submit-wrap');

    const editLivingModeInputs = document.querySelectorAll('#cost-model-edit-panel input[name="living_mode"]');
    const editOwnershipKindInputs = document.querySelectorAll('#cost-model-edit-panel input[name="ownership_kind"]');
    const editHouseUsageInputs = document.querySelectorAll('#cost-model-edit-panel input[name="house_usage"]');

    function updateEditFlow() {
        if (!editPanel) return;

        const livingMode = getCheckedValue(editPanel, 'living_mode');
        const ownershipKind = getCheckedValue(editPanel, 'ownership_kind');
        const houseUsage = getCheckedValue(editPanel, 'house_usage');

        if (livingMode === 'ownership') {
            showStep(question2, '220px');
        } else {
            hideStep(question2);
            hideStep(question3);
            showStep(submitWrap, '120px');
            return;
        }

        if (ownershipKind === 'house') {
            showStep(question3, '320px');

            if (houseUsage) {
                showStep(submitWrap, '120px');
            } else {
                hideStep(submitWrap);
            }

            return;
        }

        if (ownershipKind === 'apartment') {
            hideStep(question3);
            showStep(submitWrap, '120px');
            return;
        }

        hideStep(question3);
        hideStep(submitWrap);
    }

    editToggle?.addEventListener('click', () => {
        const isOpen = editPanel.style.maxHeight && editPanel.style.maxHeight !== '0px';

        if (isOpen) {
            editPanel.style.maxHeight = '0px';
            editPanel.style.opacity = '0';
            editPanel.style.marginTop = '0px';
            return;
        }

        editPanel.style.maxHeight = '1200px';
        editPanel.style.opacity = '1';
        editPanel.style.marginTop = '1rem';

        updateEditFlow();

        setTimeout(() => {
            editPanel.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        }, 180);
    });

    [...editLivingModeInputs, ...editOwnershipKindInputs, ...editHouseUsageInputs].forEach(input => {
        input.addEventListener('change', () => {
            updateEditFlow();
        });
    });

    updateEditFlow();
});
</script>
</body>
</html>