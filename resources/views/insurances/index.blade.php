<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Versicherungen | Haushalt App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-900">
    <div class="mx-auto min-h-screen w-full max-w-md bg-slate-100">

        <section class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950 px-4 pb-6 pt-5 text-white">
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-emerald-400/20 blur-3xl"></div>
            <div class="absolute -left-8 bottom-0 h-24 w-24 rounded-full bg-cyan-400/20 blur-2xl"></div>

            <div class="relative z-10">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-slate-300">
                            Versicherungen
                        </p>
                        <h1 class="mt-1 truncate text-2xl font-bold">
                            {{ $household->name }}
                        </h1>
                        <p class="mt-1 text-sm text-slate-300">
                            Verträge, Laufzeiten und Dokumente im Blick
                        </p>
                    </div>

                    <a href="{{ route('dashboard') }}"
                       class="rounded-2xl border border-white/15 bg-white/10 px-3 py-2 text-center text-sm font-medium text-white backdrop-blur">
                        Zurück
                    </a>
                </div>

                <div class="mt-5 grid grid-cols-2 gap-3">
                    <div class="rounded-[24px] border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-xs text-slate-300">Aktiv</p>
                        <p class="mt-1 text-2xl font-bold">{{ $summary['active_count'] }}</p>
                        <p class="mt-1 text-xs text-slate-300">Versicherungen</p>
                    </div>

                    <div class="rounded-[24px] border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-xs text-slate-300">Bald fällig</p>
                        <p class="mt-1 text-2xl font-bold">{{ $summary['ending_soon_count'] }}</p>
                        <p class="mt-1 text-xs text-slate-300">enden in 30 Tagen</p>
                    </div>

                    <div class="rounded-[24px] border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-xs text-slate-300">Monatlich</p>
                        <p class="mt-1 text-2xl font-bold">{{ number_format($summary['monthly_total'], 2, ',', '.') }} €</p>
                        <p class="mt-1 text-xs text-slate-300">umgerechnet</p>
                    </div>

                    <div class="rounded-[24px] border border-white/10 bg-white/10 p-4 backdrop-blur">
                        <p class="text-xs text-slate-300">Jährlich</p>
                        <p class="mt-1 text-2xl font-bold">{{ number_format($summary['yearly_total'], 2, ',', '.') }} €</p>
                        <p class="mt-1 text-xs text-slate-300">umgerechnet</p>
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

            <section class="rounded-[24px] bg-white p-3 shadow-sm ring-1 ring-slate-200">
                <label for="insurance-search" class="mb-2 block text-xs font-semibold uppercase tracking-wide text-emerald-600">
                    Suche
                </label>

                <input
                    id="insurance-search"
                    type="text"
                    placeholder="Versicherung oder Anbieter suchen..."
                    class="w-full rounded-[18px] border border-slate-300 bg-slate-50 px-4 py-2.5 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                >
            </section>

            <div id="insurance-search-empty" class="hidden rounded-[24px] border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-600 shadow-sm">
                Keine passende Versicherung gefunden.
            </div>

            <section class="rounded-[32px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Neu</p>
                        <h2 class="text-lg font-bold text-slate-900">Versicherung anlegen</h2>
                    </div>
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                        Formular
                    </span>
                </div>

                <form action="{{ route('insurances.store') }}" method="POST" class="mt-4 space-y-4">
                    @csrf

                    <div>
                        <label for="title" class="mb-1 block text-sm font-medium text-slate-700">Versicherung</label>
                        <input
                            id="title"
                            name="title"
                            type="text"
                            value="{{ old('title') }}"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                            placeholder="z. B. Haftpflicht"
                            required
                        >
                    </div>

                    <div>
                        <label for="provider" class="mb-1 block text-sm font-medium text-slate-700">Anbieter</label>
                        <input
                            id="provider"
                            name="provider"
                            type="text"
                            value="{{ old('provider') }}"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                            placeholder="z. B. Allianz"
                        >
                    </div>

                    <div>
                        <label for="provider_street" class="mb-1 block text-sm font-medium text-slate-700">Straße / Hausnummer der Gesellschaft</label>
                        <input
                            id="provider_street"
                            name="provider_street"
                            type="text"
                            value="{{ old('provider_street') }}"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                            placeholder="z. B. VGH Platz 1"
                        >
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="provider_zip" class="mb-1 block text-sm font-medium text-slate-700">PLZ</label>
                            <input
                                id="provider_zip"
                                name="provider_zip"
                                type="text"
                                value="{{ old('provider_zip') }}"
                                class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                                placeholder="30159"
                            >
                        </div>

                        <div>
                            <label for="provider_city" class="mb-1 block text-sm font-medium text-slate-700">Ort</label>
                            <input
                                id="provider_city"
                                name="provider_city"
                                type="text"
                                value="{{ old('provider_city') }}"
                                class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                                placeholder="Hannover"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="provider_email" class="mb-1 block text-sm font-medium text-slate-700">E-Mail der Gesellschaft</label>
                        <input
                            id="provider_email"
                            name="provider_email"
                            type="email"
                            value="{{ old('provider_email') }}"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                            placeholder="z. B. service@vgh.de"
                        >
                    </div>

                    <section class="rounded-[24px] bg-slate-50 p-4 ring-1 ring-slate-200">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Versicherte Person</p>
                            <h3 class="mt-1 text-sm font-bold text-slate-900">Versicherungsnehmer</h3>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div>
                                <label for="insured_first_name" class="mb-1 block text-sm font-medium text-slate-700">Vorname</label>
                                <input
                                    id="insured_first_name"
                                    name="insured_first_name"
                                    type="text"
                                    value="{{ old('insured_first_name') }}"
                                    class="w-full rounded-[20px] border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-500"
                                >
                            </div>

                            <div>
                                <label for="insured_last_name" class="mb-1 block text-sm font-medium text-slate-700">Nachname</label>
                                <input
                                    id="insured_last_name"
                                    name="insured_last_name"
                                    type="text"
                                    value="{{ old('insured_last_name') }}"
                                    class="w-full rounded-[20px] border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-500"
                                >
                            </div>
                        </div>

                        <div class="mt-3">
                            <label for="insured_street" class="mb-1 block text-sm font-medium text-slate-700">Straße / Hausnummer</label>
                            <input
                                id="insured_street"
                                name="insured_street"
                                type="text"
                                value="{{ old('insured_street') }}"
                                class="w-full rounded-[20px] border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-500"
                            >
                        </div>

                        <div class="mt-3 grid grid-cols-2 gap-3">
                            <div>
                                <label for="insured_zip" class="mb-1 block text-sm font-medium text-slate-700">PLZ</label>
                                <input
                                    id="insured_zip"
                                    name="insured_zip"
                                    type="text"
                                    value="{{ old('insured_zip') }}"
                                    class="w-full rounded-[20px] border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-500"
                                >
                            </div>

                            <div>
                                <label for="insured_city" class="mb-1 block text-sm font-medium text-slate-700">Ort</label>
                                <input
                                    id="insured_city"
                                    name="insured_city"
                                    type="text"
                                    value="{{ old('insured_city') }}"
                                    class="w-full rounded-[20px] border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-500"
                                >
                            </div>
                        </div>

                        <div class="mt-3 grid grid-cols-2 gap-3">
                            <div>
                                <label for="insured_email" class="mb-1 block text-sm font-medium text-slate-700">E-Mail</label>
                                <input
                                    id="insured_email"
                                    name="insured_email"
                                    type="email"
                                    value="{{ old('insured_email') }}"
                                    class="w-full rounded-[20px] border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-500"
                                >
                            </div>

                            <div>
                                <label for="insured_phone" class="mb-1 block text-sm font-medium text-slate-700">Telefon</label>
                                <input
                                    id="insured_phone"
                                    name="insured_phone"
                                    type="text"
                                    value="{{ old('insured_phone') }}"
                                    class="w-full rounded-[20px] border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-500"
                                >
                            </div>
                        </div>
                    </section>

                    <div>
                        <label for="policy_number" class="mb-1 block text-sm font-medium text-slate-700">Versicherungsnummer</label>
                        <input
                            id="policy_number"
                            name="policy_number"
                            type="text"
                            value="{{ old('policy_number') }}"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                        >
                    </div>

                    <div>
                        <label for="insurance_type" class="mb-1 block text-sm font-medium text-slate-700">Art</label>
                        <select
                            id="insurance_type"
                            name="insurance_type"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                            required
                        >
                            @foreach($insuranceTypes as $type)
                                <option value="{{ $type }}" {{ old('insurance_type') === $type ? 'selected' : '' }}>
                                    {{ $type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="starts_at" class="mb-1 block text-sm font-medium text-slate-700">Beginn</label>
                            <input
                                id="starts_at"
                                name="starts_at"
                                type="date"
                                value="{{ old('starts_at') }}"
                                class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                                required
                            >
                        </div>

                        <div>
                            <label for="ends_at" class="mb-1 block text-sm font-medium text-slate-700">Ende</label>
                            <input
                                id="ends_at"
                                name="ends_at"
                                type="date"
                                value="{{ old('ends_at') }}"
                                class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="cancellation_notice_days" class="mb-1 block text-sm font-medium text-slate-700">Kündigungsfrist (Tage)</label>
                        <input
                            id="cancellation_notice_days"
                            name="cancellation_notice_days"
                            type="number"
                            min="0"
                            value="{{ old('cancellation_notice_days') }}"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                            placeholder="z. B. 30"
                        >
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="payment_interval" class="mb-1 block text-sm font-medium text-slate-700">Zahlintervall</label>
                            <select
                                id="payment_interval"
                                name="payment_interval"
                                class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                                required
                            >
                                @foreach($paymentIntervals as $value => $label)
                                    <option value="{{ $value }}" {{ old('payment_interval', 'monthly') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="amount" class="mb-1 block text-sm font-medium text-slate-700">Kosten</label>
                            <input
                                id="amount"
                                name="amount"
                                type="number"
                                step="0.01"
                                min="0"
                                value="{{ old('amount') }}"
                                class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                                placeholder="z. B. 25.00"
                                required
                            >
                        </div>
                    </div>

                    <div>
                        <label for="status" class="mb-1 block text-sm font-medium text-slate-700">Status</label>
                        <select
                            id="status"
                            name="status"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                            required
                        >
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}" {{ old('status', 'active') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="notes" class="mb-1 block text-sm font-medium text-slate-700">Notiz</label>
                        <textarea
                            id="notes"
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
                        Versicherung speichern
                    </button>
                </form>
            </section>

            <section data-insurance-section class="rounded-[32px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Aktiv</p>
                        <h2 class="text-lg font-bold text-slate-900">Laufende Versicherungen</h2>
                    </div>
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                        {{ $activeInsurances->count() }}
                    </span>
                </div>

                @if($activeInsurances->isEmpty())
                    <div class="mt-4 rounded-[24px] bg-slate-50 px-4 py-6 text-center ring-1 ring-slate-200">
                        <div class="text-2xl">🛡️</div>
                        <p class="mt-2 text-sm font-semibold text-slate-800">Noch keine aktiven Versicherungen</p>
                        <p class="mt-1 text-xs text-slate-500">Lege oben euren ersten Vertrag an.</p>
                    </div>
                @else
                    <div class="mt-4 space-y-3">
                        @foreach($activeInsurances as $insurance)
                            <div data-insurance-card class="rounded-[24px] bg-emerald-50 p-3 ring-1 ring-emerald-100">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-start justify-between gap-3">
                                            <p class="font-bold text-slate-900">{{ $insurance->title }}</p>

                                            @if($insurance->policy_number)
                                                <span class="shrink-0 rounded-full bg-white px-2 py-1 text-xs font-medium text-slate-600 ring-1 ring-slate-200">
                                                    {{ $insurance->policy_number }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                            @if($insurance->provider)
                                                <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                                    Gesellschaft: {{ $insurance->provider }}
                                                </span>
                                            @endif

                                            <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                                {{ number_format((float) $insurance->amount, 2, ',', '.') }} € · {{ $paymentIntervals[$insurance->payment_interval] ?? $insurance->payment_interval }}
                                            </span>
                                        </div>

                                        <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                            <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                                Beginn: {{ $insurance->starts_at?->format('d.m.Y') }}
                                            </span>

                                            @if($insurance->ends_at)
                                                <span class="rounded-full px-2 py-1 font-medium ring-1 {{ $insurance->ends_at->isPast() ? 'bg-rose-100 text-rose-700 ring-rose-200' : 'bg-white text-slate-600 ring-slate-200' }}">
                                                    Ende: {{ $insurance->ends_at->format('d.m.Y') }}
                                                </span>
                                            @endif
                                        </div>

                                        @if($insurance->notes)
                                            <p class="mt-2 text-sm text-slate-600">{{ $insurance->notes }}</p>
                                        @endif

                                        <div class="mt-3 flex flex-wrap gap-2 text-xs">
                                            @if($insurance->ends_at && $insurance->ends_at->between(now()->startOfDay(), now()->copy()->addDays(30)->endOfDay()))
                                                <span class="rounded-full bg-amber-100 px-2 py-1 font-semibold text-amber-700 ring-1 ring-amber-200">
                                                    Endet bald
                                                </span>
                                            @endif

                                            @if($insurance->ends_at && $insurance->cancellation_notice_days)
                                                @php
                                                    $noticeDate = $insurance->ends_at->copy()->subDays((int) $insurance->cancellation_notice_days);
                                                @endphp

                                                @if($noticeDate->between(now()->startOfDay(), now()->copy()->addDays(30)->endOfDay()))
                                                    <span class="rounded-full bg-rose-100 px-2 py-1 font-semibold text-rose-700 ring-1 ring-rose-200">
                                                        Kündigungsfrist bald
                                                    </span>
                                                @endif
                                            @endif
                                        </div>
                                    </div>

                                    <span class="shrink-0 rounded-full bg-emerald-600 px-2.5 py-1 text-xs font-semibold text-white">
                                        Aktiv
                                    </span>
                                </div>

                                <div class="mt-3 grid grid-cols-2 gap-2 w-full">
                                    <a
                                        href="{{ route('insurances.show', $insurance) }}"
                                        class="flex w-full items-center justify-center rounded-[18px] bg-slate-900 px-3 py-2 text-xs font-semibold text-white shadow-sm"
                                    >
                                        Bearbeiten
                                    </a>

                                    <form action="{{ route('insurances.destroy', $insurance) }}" method="POST" onsubmit="return confirm('Versicherung wirklich löschen?');" class="w-full">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="flex w-full items-center justify-center rounded-[18px] bg-red-600 px-3 py-2 text-xs font-semibold text-white shadow-sm"
                                        >
                                            Versicherung löschen
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <section data-insurance-section class="rounded-[32px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Archiv</p>
                        <h2 class="text-lg font-bold text-slate-900">Gekündigt / beendet</h2>
                    </div>
                    <span class="rounded-full bg-slate-200 px-3 py-1 text-xs font-semibold text-slate-700">
                        {{ $inactiveInsurances->count() }}
                    </span>
                </div>

                @if($inactiveInsurances->isEmpty())
                    <div class="mt-4 rounded-[24px] bg-slate-50 px-4 py-6 text-center ring-1 ring-slate-200">
                        <div class="text-2xl">📁</div>
                        <p class="mt-2 text-sm font-semibold text-slate-800">Noch keine archivierten Versicherungen</p>
                    </div>
                @else
                    <div class="mt-4 space-y-3">
                        @foreach($inactiveInsurances as $insurance)
                            <div data-insurance-card class="rounded-[24px] bg-slate-50 p-3 ring-1 ring-slate-200">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-start justify-between gap-3">
                                            <p class="font-bold text-slate-900">{{ $insurance->title }}</p>

                                            @if($insurance->policy_number)
                                                <span class="shrink-0 rounded-full bg-white px-2 py-1 text-xs font-medium text-slate-600 ring-1 ring-slate-200">
                                                    {{ $insurance->policy_number }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                            @if($insurance->provider)
                                                <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                                    Gesellschaft: {{ $insurance->provider }}
                                                </span>
                                            @endif

                                            <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                                {{ number_format((float) $insurance->amount, 2, ',', '.') }} € · {{ $paymentIntervals[$insurance->payment_interval] ?? $insurance->payment_interval }}
                                            </span>
                                        </div>

                                        <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                            <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                                Beginn: {{ $insurance->starts_at?->format('d.m.Y') }}
                                            </span>

                                            @if($insurance->ends_at)
                                                <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                                    Ende: {{ $insurance->ends_at->format('d.m.Y') }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <span class="shrink-0 rounded-full bg-slate-300 px-2.5 py-1 text-xs font-semibold text-slate-700">
                                        {{ $statusOptions[$insurance->status] ?? $insurance->status }}
                                    </span>
                                </div>

                                <div class="mt-3 grid grid-cols-2 gap-2 w-full">
                                    <a
                                        href="{{ route('insurances.show', $insurance) }}"
                                        class="flex w-full items-center justify-center rounded-[18px] bg-slate-900 px-3 py-2 text-xs font-semibold text-white shadow-sm"
                                    >
                                        Öffnen
                                    </a>

                                    <form action="{{ route('insurances.destroy', $insurance) }}" method="POST" onsubmit="return confirm('Versicherung wirklich löschen?');" class="w-full">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="flex w-full items-center justify-center rounded-[18px] bg-red-600 px-3 py-2 text-xs font-semibold text-white shadow-sm"
                                        >
                                            Löschen
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </main>

        @include('partials.bottom-nav', ['active' => 'insurances'])
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('insurance-search');
            const cards = Array.from(document.querySelectorAll('[data-insurance-card]'));
            const sections = Array.from(document.querySelectorAll('[data-insurance-section]'));
            const emptyState = document.getElementById('insurance-search-empty');

            if (!input || !cards.length) {
                return;
            }

            const filterCards = () => {
                const term = input.value.trim().toLowerCase();
                let visibleCount = 0;

                cards.forEach((card) => {
                    const text = (card.innerText || '').toLowerCase();
                    const match = term === '' || text.includes(term);

                    card.classList.toggle('hidden', !match);

                    if (match) {
                        visibleCount++;
                    }
                });

                sections.forEach((section) => {
                    const visibleCardsInSection = section.querySelectorAll('[data-insurance-card]:not(.hidden)').length;
                    section.classList.toggle('hidden', visibleCardsInSection === 0);
                });

                if (emptyState) {
                    emptyState.classList.toggle('hidden', visibleCount !== 0 || term === '');
                }
            };

            input.addEventListener('input', filterCards);
        });
    </script>
</body>
</html>