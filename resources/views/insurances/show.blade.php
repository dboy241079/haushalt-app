<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Versicherung bearbeiten | Haushalt App</title>
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
                            Versicherung
                        </p>
                        <h1 class="mt-1 truncate text-2xl font-bold">
                            {{ $insurance->title }}
                        </h1>
                        <p class="mt-1 text-sm text-slate-300">
                            Vertrag bearbeiten und Dokumente verwalten
                        </p>
                    </div>

                    <a href="{{ route('insurances.index') }}"
                       class="rounded-2xl border border-white/15 bg-white/10 px-3 py-2 text-center text-sm font-medium text-white backdrop-blur">
                        Zurück
                    </a>
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
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Details</p>
                        <h2 class="text-lg font-bold text-slate-900">Versicherung bearbeiten</h2>
                    </div>
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                        {{ $statusOptions[$insurance->status] ?? $insurance->status }}
                    </span>
                </div>

                <form action="{{ route('insurances.update', $insurance) }}" method="POST" class="mt-4 space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="title" class="mb-1 block text-sm font-medium text-slate-700">Versicherung</label>
                        <input
                            id="title"
                            name="title"
                            type="text"
                            value="{{ old('title', $insurance->title) }}"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                            required
                        >
                    </div>

                    <div>
                        <label for="provider" class="mb-1 block text-sm font-medium text-slate-700">Anbieter</label>
                        <input
                            id="provider"
                            name="provider"
                            type="text"
                            value="{{ old('provider', $insurance->provider) }}"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                        >
                    </div>

                    <div>
                        <label for="provider_street" class="mb-1 block text-sm font-medium text-slate-700">Straße / Hausnummer der Gesellschaft</label>
                        <input
                            id="provider_street"
                            name="provider_street"
                            type="text"
                            value="{{ old('provider_street', $insurance->provider_street) }}"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                        >
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="provider_zip" class="mb-1 block text-sm font-medium text-slate-700">PLZ</label>
                            <input
                                id="provider_zip"
                                name="provider_zip"
                                type="text"
                                value="{{ old('provider_zip', $insurance->provider_zip) }}"
                                class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                            >
                        </div>

                        <div>
                            <label for="provider_city" class="mb-1 block text-sm font-medium text-slate-700">Ort</label>
                            <input
                                id="provider_city"
                                name="provider_city"
                                type="text"
                                value="{{ old('provider_city', $insurance->provider_city) }}"
                                class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                            >
                        </div>
                    </div>

                    <div>
                        <label for="provider_email" class="mb-1 block text-sm font-medium text-slate-700">E-Mail der Gesellschaft</label>
                        <input
                            id="provider_email"
                            name="provider_email"
                            type="email"
                            value="{{ old('provider_email', $insurance->provider_email) }}"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
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
                                    value="{{ old('insured_first_name', $insurance->insured_first_name) }}"
                                    class="w-full rounded-[20px] border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-500"
                                >
                            </div>

                            <div>
                                <label for="insured_last_name" class="mb-1 block text-sm font-medium text-slate-700">Nachname</label>
                                <input
                                    id="insured_last_name"
                                    name="insured_last_name"
                                    type="text"
                                    value="{{ old('insured_last_name', $insurance->insured_last_name) }}"
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
                                value="{{ old('insured_street', $insurance->insured_street) }}"
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
                                    value="{{ old('insured_zip', $insurance->insured_zip) }}"
                                    class="w-full rounded-[20px] border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-500"
                                >
                            </div>

                            <div>
                                <label for="insured_city" class="mb-1 block text-sm font-medium text-slate-700">Ort</label>
                                <input
                                    id="insured_city"
                                    name="insured_city"
                                    type="text"
                                    value="{{ old('insured_city', $insurance->insured_city) }}"
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
                                    value="{{ old('insured_email', $insurance->insured_email) }}"
                                    class="w-full rounded-[20px] border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-500"
                                >
                            </div>

                            <div>
                                <label for="insured_phone" class="mb-1 block text-sm font-medium text-slate-700">Telefon</label>
                                <input
                                    id="insured_phone"
                                    name="insured_phone"
                                    type="text"
                                    value="{{ old('insured_phone', $insurance->insured_phone) }}"
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
                            value="{{ old('policy_number', $insurance->policy_number) }}"
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
                                <option value="{{ $type }}" {{ old('insurance_type', $insurance->insurance_type) === $type ? 'selected' : '' }}>
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
                                value="{{ old('starts_at', optional($insurance->starts_at)->format('Y-m-d')) }}"
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
                                value="{{ old('ends_at', optional($insurance->ends_at)->format('Y-m-d')) }}"
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
                            value="{{ old('cancellation_notice_days', $insurance->cancellation_notice_days) }}"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
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
                                    <option value="{{ $value }}" {{ old('payment_interval', $insurance->payment_interval) === $value ? 'selected' : '' }}>
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
                                value="{{ old('amount', $insurance->amount) }}"
                                class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
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
                                <option value="{{ $value }}" {{ old('status', $insurance->status) === $value ? 'selected' : '' }}>
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
                            rows="4"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                        >{{ old('notes', $insurance->notes) }}</textarea>
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-[24px] bg-gradient-to-r from-emerald-400 to-teal-500 px-4 py-3 text-sm font-semibold text-white shadow-md"
                    >
                        Änderungen speichern
                    </button>
                </form>
            </section>

            @php
                $noticeDays = $insurance->cancellation_notice_days ?? 30;
                $noticeDate = $insurance->ends_at ? $insurance->ends_at->copy()->subDays((int) $noticeDays) : null;
                $canPrepareCancellation = $insurance->ends_at && $noticeDate && now()->startOfDay()->gte($noticeDate->startOfDay());
            @endphp

            @if($canPrepareCancellation)
                <section class="rounded-[32px] border border-rose-200 bg-rose-50 p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-rose-600">Kündigung</p>
                            <h2 class="text-lg font-bold text-slate-900">Hier Kündigung vorbereiten</h2>
                            <p class="mt-1 text-sm text-slate-600">
                                Die Kündigungsfrist ist erreicht. Kündigung jetzt als PDF erzeugen.
                            </p>
                        </div>
                    </div>

                    <div class="mt-3 flex flex-wrap gap-2 text-xs">
                        <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                            Kündigungsfrist: {{ $noticeDays }} Tage
                        </span>

                        <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                            Frist aktiv seit: {{ $noticeDate->format('d.m.Y') }}
                        </span>

                        <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                            Vertragsende: {{ $insurance->ends_at->format('d.m.Y') }}
                        </span>
                    </div>

                    <p class="mt-3 text-xs text-slate-500">
                        Beim Klick wird die Kündigung als PDF direkt heruntergeladen.
                    </p>

                    <div class="mt-4">
                        <form action="{{ route('insurances.cancellation-pdf', $insurance) }}" method="POST">
                            @csrf
                            <button
                                type="submit"
                                class="w-full rounded-[24px] bg-gradient-to-r from-rose-500 to-red-600 px-4 py-3 text-sm font-semibold text-white shadow-md"
                            >
                                Jetzt kündigen
                            </button>
                        </form>
                    </div>
                </section>
            @endif

            <section class="rounded-[32px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Upload</p>
                        <h2 class="text-lg font-bold text-slate-900">Neue Dokumente hochladen</h2>
                    </div>
                </div>

                <form action="{{ route('insurances.documents.store', $insurance) }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                    @csrf

                    <div>
                        <label for="document_type" class="mb-1 block text-sm font-medium text-slate-700">Dokumenttyp</label>
                        <select
                            id="document_type"
                            name="document_type"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                        >
                            @foreach($documentTypes as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="document_title" class="mb-1 block text-sm font-medium text-slate-700">Dokumentenname</label>
                        <input
                            id="document_title"
                            name="document_title"
                            type="text"
                            value="{{ old('document_title') }}"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                            placeholder="z. B. Beitragsanpassung 2026"
                            required
                        >
                    </div>

                    <div>
                        <label for="documents" class="mb-1 block text-sm font-medium text-slate-700">Datei / Foto</label>
                        <input
                            id="documents"
                            name="documents[]"
                            type="file"
                            accept="image/*,.pdf,.doc,.docx"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                            required
                        >
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-[24px] bg-slate-900 px-4 py-3 text-sm font-semibold text-white shadow-md"
                    >
                        Dokument hochladen
                    </button>
                </form>
            </section>

            <section class="rounded-[32px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Dokumente</p>
                        <h2 class="text-lg font-bold text-slate-900">Vorhandene Unterlagen</h2>
                    </div>
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
                        {{ $insurance->documents->count() }}
                    </span>
                </div>

                @if($insurance->documents->isEmpty())
                    <div class="mt-4 rounded-[24px] bg-slate-50 px-4 py-6 text-center ring-1 ring-slate-200">
                        <div class="text-2xl">📂</div>
                        <p class="mt-2 text-sm font-semibold text-slate-800">Noch keine Dokumente vorhanden</p>
                    </div>
                @else
                    <div class="mt-4 space-y-3">
                        @foreach($insurance->documents as $document)
                            @php
                                $isImage = str_starts_with((string) $document->file_type, 'image/');
                                $isPdf = $document->file_type === 'application/pdf';
                                $isWord = in_array($document->file_type, [
                                    'application/msword',
                                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                                ], true);

                                $fileIcon = $isImage ? '🖼️' : ($isPdf ? '📄' : ($isWord ? '📝' : '📎'));
                            @endphp

                            <div class="rounded-[24px] bg-emerald-50 p-3 ring-1 ring-emerald-100">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-white text-2xl ring-1 ring-emerald-100">
                                        {{ $fileIcon }}
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <a href="{{ asset('storage/' . $document->file_path) }}"
                                           target="_blank"
                                           class="block truncate font-semibold text-slate-900 underline-offset-2 hover:underline">
                                            {{ $document->document_title ?: $document->original_name }}
                                        </a>

                                        <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                            <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                                {{ $documentTypes[$document->document_type] ?? 'Sonstiges' }}
                                            </span>

                                            <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                                Hochgeladen von: {{ $document->uploadedByUser?->name ?? 'Unbekannt' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3 grid grid-cols-2 gap-2 w-full">
                                    <a
                                        href="{{ asset('storage/' . $document->file_path) }}"
                                        target="_blank"
                                        class="flex w-full items-center justify-center rounded-[18px] bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm ring-1 ring-slate-200"
                                    >
                                        Öffnen
                                    </a>

                                    <form action="{{ route('insurances.documents.destroy', [$insurance, $document]) }}" method="POST" onsubmit="return confirm('Dokument wirklich löschen?');" class="w-full">
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
</body>
</html>