<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termine | Haushalt App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-900">
    <div class="mx-auto min-h-screen w-full max-w-md bg-slate-100">

        <section class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-violet-950 px-4 pb-6 pt-5 text-white">
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-violet-400/20 blur-3xl"></div>
            <div class="absolute -left-8 bottom-0 h-24 w-24 rounded-full bg-fuchsia-400/20 blur-2xl"></div>

            <div class="relative z-10">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-slate-300">
                            Termine
                        </p>
                        <h1 class="mt-1 truncate text-2xl font-bold">
                            {{ $household->name }}
                        </h1>
                        <p class="mt-1 text-sm text-slate-300">
                            Alles Wichtige im Blick
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
                            <p class="text-xs font-medium text-slate-300">Planung</p>
                            <p class="mt-1 text-xl font-bold">Termine für euren Alltag</p>
                            <p class="mt-1 text-sm text-slate-300">
                                Arzt, Schule, Freizeit und alles dazwischen.
                            </p>
                        </div>

                        <div class="flex h-14 w-14 items-center justify-center rounded-3xl bg-white/10 text-2xl ring-1 ring-white/15">
                            📅
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
                        <p class="text-xs font-semibold uppercase tracking-wide text-violet-600">Monat</p>
                        <h2 class="text-lg font-bold text-slate-900">Kalenderübersicht</h2>
                    </div>
                    <span class="rounded-full bg-violet-100 px-3 py-1 text-xs font-semibold text-violet-700">
                        Überblick
                    </span>
                </div>

                <div class="mt-4 overflow-hidden rounded-[24px] border border-slate-200 bg-white p-2">
                    <div
    id="events-calendar"
    data-feed-url="{{ request()->getBaseUrl() . route('events.feed', [], false) }}"
></div>
                </div>
            </section>

            <section id="event-form-card" class="rounded-[32px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-violet-600">Neu</p>
                        <h2 class="text-lg font-bold text-slate-900">Termin anlegen</h2>
                    </div>
                    <span class="rounded-full bg-violet-100 px-3 py-1 text-xs font-semibold text-violet-700">
                        Formular
                    </span>
                </div>

       <form
    id="event-form"
    action="{{ route('events.store') }}"
    method="POST"
    enctype="multipart/form-data"
    data-store-url="{{ route('events.store') }}"
    data-update-url-template="{{ url('/events/__ID__') }}"
    class="mt-4 space-y-4"
>
    @csrf
    <input type="hidden" name="_method" id="event-form-method" value="">
    <input type="hidden" name="event_id" id="event_id" value="">

    <div>
        <label for="title" class="mb-1 block text-sm font-medium text-slate-700">Titel</label>
        <input
            id="title"
            name="title"
            type="text"
            value="{{ old('title') }}"
            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-violet-500 focus:bg-white"
            placeholder="z. B. Kinderarzt"
            required
        >
    </div>

    <div>
        <label for="type" class="mb-1 block text-sm font-medium text-slate-700">Typ</label>
        <select
            id="type"
            name="type"
            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-violet-500 focus:bg-white"
            required
        >
            @foreach($types as $type)
                <option value="{{ $type }}" {{ old('type', 'Sonstiges') === $type ? 'selected' : '' }}>
                    {{ $type }}
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="starts_at" class="mb-1 block text-sm font-medium text-slate-700">Start</label>
        <input
            id="starts_at"
            name="starts_at"
            type="datetime-local"
            value="{{ old('starts_at') }}"
            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-violet-500 focus:bg-white"
            required
        >
    </div>

    <div>
        <label for="ends_at" class="mb-1 block text-sm font-medium text-slate-700">Ende</label>
        <input
            id="ends_at"
            name="ends_at"
            type="datetime-local"
            value="{{ old('ends_at') }}"
            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-violet-500 focus:bg-white"
        >
    </div>

    <div class="rounded-[20px] bg-slate-50 px-4 py-3 ring-1 ring-slate-200">
        <label class="flex items-center gap-3">
            <input
                type="checkbox"
                name="all_day"
                value="1"
                {{ old('all_day') ? 'checked' : '' }}
                class="h-4 w-4 rounded border-slate-300 text-violet-600 focus:ring-violet-500"
            >
            <span class="text-sm font-medium text-slate-700">Ganztägig</span>
        </label>

        <p class="mt-2 text-xs text-slate-500">
            Für Urlaub, Ferien oder Geburtstag sinnvoll.
        </p>
    </div>

    <div>
        <label for="location" class="mb-1 block text-sm font-medium text-slate-700">Ort</label>
        <input
            id="location"
            name="location"
            type="text"
            value="{{ old('location') }}"
            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-violet-500 focus:bg-white"
            placeholder="z. B. Hannover"
        >
    </div>

    <div>
        <label for="description" class="mb-1 block text-sm font-medium text-slate-700">Notiz</label>
        <textarea
            id="description"
            name="description"
            rows="3"
            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-violet-500 focus:bg-white"
            placeholder="Optional"
        >{{ old('description') }}</textarea>
    </div>

    <div>
    <label for="attachments" class="mb-1 block text-sm font-medium text-slate-700">Dateien / Fotos</label>
    <input
        id="attachments"
        name="attachments[]"
        type="file"
        multiple
        accept="image/*,.pdf,.doc,.docx"
        class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-violet-500 focus:bg-white"
    >
    <p class="mt-2 text-xs text-slate-500">
        Mehrere Dateien möglich. Bilder, PDF und Word-Dateien.
    </p>
</div>


<div>
    <label for="camera_attachment" class="mb-1 block text-sm font-medium text-slate-700">Foto direkt aufnehmen</label>
    <input
        id="camera_attachment"
        name="attachments[]"
        type="file"
        accept="image/*"
        capture="environment"
        class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-violet-500 focus:bg-white"
    >
</div>

    <div id="event-edit-hint" class="hidden rounded-[20px] border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
    <div class="flex flex-wrap items-center gap-2">
        <span>Du bearbeitest gerade einen bestehenden Termin.</span>

        <span id="event-insurance-badge" class="hidden rounded-full bg-emerald-100 px-2 py-1 text-[11px] font-semibold text-emerald-700">
            Versicherung
        </span>

        <a
            id="event-insurance-link"
            href="#"
            class="hidden rounded-full bg-slate-900 px-3 py-1 text-[11px] font-semibold text-white"
        >
            Zur Versicherung
        </a>
    </div>

    <p id="event-insurance-note" class="hidden mt-2 text-xs font-medium text-emerald-700">
        Hier Kündigung vorbereiten!
    </p>
</div>

    <div class="grid grid-cols-2 gap-3">
        <button
            type="button"
            id="event-form-reset"
            class="rounded-[24px] border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm"
        >
            Formular leeren
        </button>

        <button
            type="submit"
            id="event-submit-button"
            class="rounded-[24px] bg-gradient-to-r from-violet-400 to-fuchsia-500 px-4 py-3 text-sm font-semibold text-white shadow-md"
        >
            Termin speichern
        </button>
    </div>
</form>
            </section>

            <section id="upcoming-events" class="scroll-mt-6 rounded-[32px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-violet-600">Kommend</p>
            <h2 class="text-lg font-bold text-slate-900">Nächste Termine</h2>
        </div>
                    <span class="rounded-full bg-violet-100 px-3 py-1 text-xs font-semibold text-violet-700">
                        {{ $upcomingEvents->count() }}
                    </span>
                </div>

                @if($upcomingEvents->isEmpty())
                    <div class="mt-4 rounded-[24px] bg-slate-50 px-4 py-6 text-center ring-1 ring-slate-200">
                        <div class="text-2xl">📭</div>
                        <p class="mt-2 text-sm font-semibold text-slate-800">Keine kommenden Termine</p>
                        <p class="mt-1 text-xs text-slate-500">Lege oben euren nächsten Termin an.</p>
                    </div>
                @else
                    <div class="mt-4 space-y-3">
                        @foreach($upcomingEvents as $event)
    <div id="event-{{ $event->id }}" class="scroll-mt-6 rounded-[24px] bg-violet-50 p-3 ring-1 ring-violet-100">
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-900">{{ $event->title }}</p>

                                    <div class="mt-2">
                                        <span class="rounded-full bg-violet-100 px-2 py-1 text-xs font-semibold text-violet-700">
                                            {{ $event->type ?? 'Sonstiges' }}
                                        </span>

                                        @if($event->all_day)
                                            <span class="ml-2 rounded-full bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-700">
                                                Ganztägig
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                        <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                            {{ $event->all_day ? $event->starts_at->format('d.m.Y') : $event->starts_at->format('d.m.Y H:i') }}
                                        </span>

                                        @if($event->ends_at)
                                            <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                                bis {{ $event->all_day ? $event->ends_at->copy()->subDay()->format('d.m.Y') : $event->ends_at->format('d.m.Y H:i') }}
                                            </span>
                                        @endif

                                        @if($event->location)
                                            <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                                {{ $event->location }}
                                            </span>
                                        @endif
                                    </div>

                                    @if($event->description)
                                        <p class="mt-2 text-sm text-slate-600">{{ $event->description }}</p>
                                    @endif
                                    @php
    $imageAttachment = $event->attachments
        ->first(fn ($attachment) => str_starts_with((string) $attachment->file_type, 'image/'));
@endphp

@if($imageAttachment)
    <div class="mt-3 overflow-hidden rounded-[20px] bg-white ring-1 ring-violet-100">
        <img
            src="{{ asset('storage/' . $imageAttachment->file_path) }}"
            alt="Bild zu {{ $event->title }}"
            class="h-44 w-full object-cover"
        >
    </div>
@endif
                                </div>
                                @if($event->attachments && $event->attachments->count())
    <div class="mt-3 flex flex-wrap gap-2">
        @foreach($event->attachments as $attachment)
            <a
                href="{{ asset('storage/' . $attachment->file_path) }}"
                target="_blank"
                class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-violet-700 ring-1 ring-violet-200"
            >
                📎 {{ $attachment->original_name }}
            </a>
        @endforeach
    </div>
@endif

                                <div class="mt-3">
                                    <form action="{{ route('events.destroy', $event) }}" method="POST" onsubmit="return confirm('Termin wirklich löschen?');">
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="rounded-[18px] bg-red-600 px-3 py-2 text-xs font-semibold text-white shadow-sm"
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

        @include('partials.bottom-nav', ['active' => 'events'])
    </div>
    <script>
    window.eventsFeedUrl = @json(request()->getBaseUrl() . route('events.feed', [], false));
</script>
</body>
</html>