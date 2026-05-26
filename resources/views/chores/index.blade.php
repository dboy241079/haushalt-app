<!DOCTYPE html>'needsSetup' => $this->needsSetup(),
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Putzplan | Haushalt App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-900">
    <div class="app-shell mx-auto w-full max-w-md bg-slate-100">

        <!-- Hero -->
        <section class="top-safe-area relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-amber-950 px-4 pb-6 text-white">
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-amber-400/20 blur-3xl"></div>
            <div class="absolute -left-8 bottom-0 h-24 w-24 rounded-full bg-orange-400/20 blur-2xl"></div>

            <div class="relative z-10 pt-5">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-slate-300">
                            Putzplan
                        </p>
                        <h1 class="mt-1 truncate text-2xl font-bold">
                            {{ $household->name }}
                        </h1>
                        <p class="mt-1 text-sm text-slate-300">
                            Aufgaben verwalten und abhaken
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
                            <p class="text-xs font-medium text-slate-300">Haushaltspflege</p>
                            <p class="mt-1 text-xl font-bold">Heute sauber im Blick</p>
                            <p class="mt-1 text-sm text-slate-300">
                                Neue Aufgaben anlegen, verteilen und direkt erledigen.
                            </p>
                        </div>

                        <div class="flex h-14 w-14 items-center justify-center rounded-3xl bg-white/10 text-2xl ring-1 ring-white/15">
                            🧹
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

            <!-- Quick -->
            <section class="rounded-[32px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-sky-600">Quick</p>
                        <h2 class="text-lg font-bold text-slate-900">Heute schnell erledigt</h2>
                    </div>
                    <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold text-sky-700">
                        Schnell
                    </span>
                </div>

                <form action="{{ route('chores.quickStore') }}" method="POST" class="mt-4 space-y-4">
                    @csrf

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-700">Schnellwahl</label>

                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                            @foreach($quickPrimaryOptions as $quickType)
                                <label class="cursor-pointer">
                                    <input
                                        type="radio"
                                        name="quick_type_primary"
                                        value="{{ $quickType }}"
                                        class="peer sr-only"
                                        {{ old('quick_type_primary') === $quickType ? 'checked' : '' }}
                                    >
                                    <span class="flex min-h-[52px] items-center justify-center rounded-[20px] border border-slate-300 bg-slate-50 px-3 py-3 text-center text-sm font-medium text-slate-700 transition-all duration-200 peer-checked:scale-[1.02] peer-checked:border-sky-500 peer-checked:bg-sky-500 peer-checked:text-white peer-checked:shadow-lg peer-checked:shadow-sky-500/20">
                                        {{ $quickType }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label for="quick_type_secondary" class="mb-1 block text-sm font-medium text-slate-700">Weitere Aktion</label>
                        <select
                            id="quick_type_secondary"
                            name="quick_type_secondary"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-sky-500 focus:bg-white"
                        >
                            <option value="">Bitte auswählen</option>
                            @foreach($quickSecondaryOptions as $quickType)
                                <option value="{{ $quickType }}" {{ old('quick_type_secondary') === $quickType ? 'selected' : '' }}>
                                    {{ $quickType }}
                                </option>
                            @endforeach
                        </select>

                        <p class="mt-2 text-xs text-slate-500">
                            Nutze oben die Schnellwahl oder wähle hier eine weitere Aktion.
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label for="quick_room" class="mb-1 block text-sm font-medium text-slate-700">Raum</label>
                            <select
                                id="quick_room"
                                name="room"
                                class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-sky-500 focus:bg-white"
                            >
                                <option value="">Kein Raum</option>
                                @foreach($quickRoomOptions as $room)
                                    <option value="{{ $room }}" {{ old('room') === $room ? 'selected' : '' }}>
                                        {{ $room }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="quick_note" class="mb-1 block text-sm font-medium text-slate-700">Kurznotiz</label>
                            <input
                                id="quick_note"
                                name="note"
                                type="text"
                                value="{{ old('note') }}"
                                class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-sky-500 focus:bg-white"
                                placeholder="z. B. fertig"
                            >
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-[24px] bg-gradient-to-r from-sky-400 to-blue-500 px-4 py-3 text-sm font-semibold text-white shadow-md"
                    >
                        Schnell speichern
                    </button>
                </form>

                <div class="mt-5 rounded-[24px] bg-slate-50 p-3 ring-1 ring-slate-200">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Auswertung</p>
                            <h3 class="text-base font-bold text-slate-900">Diese Woche</h3>
                            <p class="text-xs text-slate-500">{{ $weekRangeLabel }}</p>
                        </div>
                        <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                            {{ $quickStatsWeek->sum('count') }} Einträge
                        </span>
                    </div>

                    @if($quickStatsWeek->isEmpty())
                        <p class="mt-3 text-sm text-slate-500">Noch keine Quick-Einträge in dieser Woche.</p>
                    @else
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($quickStatsWeek as $stat)
                                <span class="rounded-full bg-white px-3 py-2 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                                    {{ $stat['label'] }} · {{ $stat['count'] }}x
                                </span>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="mt-4 grid grid-cols-1 gap-3">
                    <div class="rounded-[24px] bg-slate-50 p-3 ring-1 ring-slate-200">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Diese Woche</p>
                                <h3 class="text-base font-bold text-slate-900">Nach Person</h3>
                            </div>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                                {{ $quickStatsByUserWeek->sum('count') }}
                            </span>
                        </div>

                        @if($quickStatsByUserWeek->isEmpty())
                            <p class="mt-3 text-sm text-slate-500">Noch keine Einträge nach Personen vorhanden.</p>
                        @else
                            <div class="mt-3 space-y-2">
                                @foreach($quickStatsByUserWeek as $stat)
                                    <div class="flex items-center justify-between rounded-[18px] bg-white px-3 py-2 ring-1 ring-slate-200">
                                        <span class="text-sm font-medium text-slate-700">{{ $stat['label'] }}</span>
                                        <span class="rounded-full bg-sky-100 px-2.5 py-1 text-xs font-semibold text-sky-700">
                                            {{ $stat['count'] }}x
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="rounded-[24px] bg-slate-50 p-3 ring-1 ring-slate-200">
                        <div class="flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Diese Woche</p>
                                <h3 class="text-base font-bold text-slate-900">Nach Raum</h3>
                            </div>
                            <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                                {{ $quickStatsByRoomWeek->sum('count') }}
                            </span>
                        </div>

                        @if($quickStatsByRoomWeek->isEmpty())
                            <p class="mt-3 text-sm text-slate-500">Noch keine Einträge nach Räumen vorhanden.</p>
                        @else
                            <div class="mt-3 space-y-2">
                                @foreach($quickStatsByRoomWeek as $stat)
                                    <div class="flex items-center justify-between rounded-[18px] bg-white px-3 py-2 ring-1 ring-slate-200">
                                        <span class="text-sm font-medium text-slate-700">{{ $stat['label'] }}</span>
                                        <span class="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                            {{ $stat['count'] }}x
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="mt-5">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Verlauf</p>
                            <h3 class="text-base font-bold text-slate-900">Quick-Einträge</h3>
                        </div>

                        <div class="flex items-center gap-2 rounded-full bg-slate-100 p-1 ring-1 ring-slate-200">
                            <a
                                href="{{ route('chores.index', ['quick_filter' => 'today']) }}"
                                class="rounded-full px-3 py-1.5 text-xs font-semibold transition {{ $quickFilter === 'today' ? 'bg-slate-900 text-white' : 'text-slate-600' }}"
                            >
                                Heute
                            </a>
                            <a
                                href="{{ route('chores.index', ['quick_filter' => 'week']) }}"
                                class="rounded-full px-3 py-1.5 text-xs font-semibold transition {{ $quickFilter === 'week' ? 'bg-slate-900 text-white' : 'text-slate-600' }}"
                            >
                                Diese Woche
                            </a>
                        </div>
                    </div>

                    @if($quickEntries->isEmpty())
                        <div class="mt-3 rounded-[24px] bg-slate-50 px-4 py-5 text-center ring-1 ring-slate-200">
                            <div class="text-2xl">⚡</div>
                            <p class="mt-2 text-sm font-semibold text-slate-800">Keine Einträge im gewählten Zeitraum</p>
                            <p class="mt-1 text-xs text-slate-500">Perfekt für spontane Haushaltsaktionen.</p>
                        </div>
                    @else
                        <div class="mt-3 space-y-3">
                            @foreach($quickEntries as $entry)
                                <div class="rounded-[24px] bg-sky-50 p-3 ring-1 ring-sky-100">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="font-bold text-slate-900">{{ $entry->quick_type }}</p>

                                            <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                                @if($entry->room)
                                                    <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                                        {{ $entry->room }}
                                                    </span>
                                                @endif

                                                <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                                    {{ $entry->done_on->format('d.m.Y') }}
                                                </span>

                                                <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                                    {{ $entry->created_at->format('H:i') }} Uhr
                                                </span>

                                                <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                                    {{ $entry->user?->name ?? 'Unbekannt' }}
                                                </span>
                                            </div>

                                            @if($entry->note)
                                                <p class="mt-2 text-sm text-slate-600">{{ $entry->note }}</p>
                                            @endif
                                        </div>

                                        <form action="{{ route('chores.quickDestroy', $entry) }}" method="POST" onsubmit="return confirm('Eintrag wirklich löschen?');">
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
                </div>
            </section>

            <!-- Neue Aufgabe -->
            <section class="rounded-[32px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">Neu</p>
                        <h2 class="text-lg font-bold text-slate-900">Aufgabe anlegen</h2>
                    </div>
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                        Formular
                    </span>
                </div>

                <form action="{{ route('chores.store') }}" method="POST" class="mt-4 space-y-4">
                    @csrf

                    <div>
                        <label for="title" class="mb-1 block text-sm font-medium text-slate-700">Titel</label>
                        <input
                            id="title"
                            name="title"
                            type="text"
                            value="{{ old('title') }}"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:bg-white"
                            placeholder="z. B. Bad putzen"
                            required
                        >
                    </div>

                    <div>
                        <label for="room" class="mb-1 block text-sm font-medium text-slate-700">Bereich / Raum</label>
                        <input
                            id="room"
                            name="room"
                            type="text"
                            value="{{ old('room') }}"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:bg-white"
                            placeholder="z. B. Badezimmer"
                        >
                    </div>

                    <div>
                        <label for="frequency" class="mb-1 block text-sm font-medium text-slate-700">Intervall</label>
                        <select
                            id="frequency"
                            name="frequency"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:bg-white"
                            required
                        >
                            <option value="daily" {{ old('frequency') === 'daily' ? 'selected' : '' }}>Täglich</option>
                            <option value="weekly" {{ old('frequency', 'weekly') === 'weekly' ? 'selected' : '' }}>Wöchentlich</option>
                            <option value="biweekly" {{ old('frequency') === 'biweekly' ? 'selected' : '' }}>Alle 2 Wochen</option>
                            <option value="monthly" {{ old('frequency') === 'monthly' ? 'selected' : '' }}>Monatlich</option>
                        </select>
                    </div>

                    <div>
                        <label for="assigned_user_id" class="mb-1 block text-sm font-medium text-slate-700">Zuständig</label>
                        <select
                            id="assigned_user_id"
                            name="assigned_user_id"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:bg-white"
                        >
                            <option value="">Niemand fest zugewiesen</option>
                            @foreach ($members as $member)
                                <option
                                    value="{{ $member->id }}"
                                    {{ (string) old('assigned_user_id') === (string) $member->id ? 'selected' : '' }}
                                >
                                    {{ $member->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="due_date" class="mb-1 block text-sm font-medium text-slate-700">Erstes Fälligkeitsdatum</label>
                        <input
                            id="due_date"
                            name="due_date"
                            type="date"
                            value="{{ old('due_date', now()->toDateString()) }}"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:bg-white"
                        >
                    </div>

                    <div>
                        <label for="description" class="mb-1 block text-sm font-medium text-slate-700">Notiz</label>
                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-amber-500 focus:bg-white"
                            placeholder="Optional"
                        >{{ old('description') }}</textarea>
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-[24px] bg-gradient-to-r from-amber-400 to-orange-500 px-4 py-3 text-sm font-semibold text-white shadow-md"
                    >
                        Aufgabe speichern
                    </button>
                </form>
            </section>

            <!-- Aufgabenliste -->
            <section class="rounded-[32px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">Übersicht</p>
                        <h2 class="text-lg font-bold text-slate-900">Aufgaben</h2>
                    </div>
                    <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                        {{ $chores->count() }}
                    </span>
                </div>

                @if($chores->isEmpty())
                    <div class="mt-4 rounded-[24px] bg-slate-50 px-4 py-6 text-center ring-1 ring-slate-200">
                        <div class="text-2xl">✨</div>
                        <p class="mt-2 text-sm font-semibold text-slate-800">Noch keine Aufgaben vorhanden</p>
                        <p class="mt-1 text-xs text-slate-500">Lege oben eure erste Aufgabe an.</p>
                    </div>
                @else
                    <div class="mt-4 space-y-3">
                        @foreach($chores as $chore)
                            <div class="rounded-[24px] bg-amber-50 p-3 ring-1 ring-amber-100">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900">{{ $chore->title }}</p>

                                        <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                            @if($chore->room)
                                                <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                                    {{ $chore->room }}
                                                </span>
                                            @endif

                                            <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                                {{ ucfirst($chore->frequency) }}
                                            </span>

                                            @if($chore->due_date)
                                                <span class="rounded-full px-2 py-1 font-medium ring-1 {{ $chore->due_date->isPast() ? 'bg-rose-100 text-rose-700 ring-rose-200' : 'bg-white text-slate-600 ring-slate-200' }}">
                                                    Fällig: {{ $chore->due_date->format('d.m.Y') }}
                                                </span>
                                            @endif

                                            @if($chore->last_completed_date)
                                                <span class="rounded-full bg-emerald-100 px-2 py-1 font-medium text-emerald-700 ring-1 ring-emerald-200">
                                                    Letztmals: {{ $chore->last_completed_date->format('d.m.Y') }}
                                                </span>
                                            @endif
                                        </div>

                                        @if($chore->description)
                                            <p class="mt-2 text-sm text-slate-600">{{ $chore->description }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="mt-3 flex items-center justify-between gap-3">
                                    <div class="text-xs text-slate-500">
                                        @if($chore->assignedUser)
                                            Zuständig:
                                            <span class="font-semibold text-slate-700">{{ $chore->assignedUser->name }}</span>
                                        @else
                                            Keine feste Zuweisung
                                        @endif
                                    </div>

                                    <form action="{{ route('chores.complete', $chore) }}" method="POST">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="rounded-[18px] bg-emerald-600 px-3 py-2 text-xs font-semibold text-white shadow-sm"
                                        >
                                            Erledigt
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </main>

        @include('partials.bottom-nav', ['active' => 'chores'])
    </div>
</body>
</html>