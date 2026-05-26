<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Dashboard | Haushalt App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-900">
    <div class="app-shell mx-auto w-full max-w-md bg-slate-100">

        <!-- Top Hero -->
        <section class="top-safe-area relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 px-4 pb-6 text-white">
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-emerald-400/20 blur-3xl"></div>
            <div class="absolute -left-8 bottom-0 h-24 w-24 rounded-full bg-violet-400/20 blur-2xl"></div>

            <div class="relative z-10">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-slate-300">
                            Dashboard
                        </p>

                        <h1 class="mt-1 truncate text-2xl font-bold">
                            {{ $household?->name ?? 'Kein Haushalt angelegt' }}
                        </h1>

                        <p class="mt-1 text-sm text-slate-300">
                            Hallo {{ $user->name }} · {{ $today->format('d.m.Y') }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-2">
                        <a href="{{ route('household.settings') }}"
                           class="rounded-2xl border border-white/15 bg-white/10 px-3 py-2 text-center text-sm font-medium text-white backdrop-blur">
                            Haushalt
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="w-full rounded-2xl border border-white/15 bg-white/10 px-3 py-2 text-sm font-medium text-white backdrop-blur"
                            >
                                Logout
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-5 rounded-[28px] border border-white/10 bg-white/10 p-4 backdrop-blur">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs font-medium text-slate-300">Heute im Blick</p>
                            <p class="mt-1 text-xl font-bold">Euer Familien-Dashboard</p>
                            <p class="mt-1 text-sm text-slate-300">
                                Aufgaben, Einkäufe und Termine an einem Ort.
                            </p>
                        </div>

                        <div class="flex h-14 w-14 items-center justify-center rounded-3xl bg-white/10 text-lg font-bold ring-1 ring-white/15">
                            {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <main class="app-main">

            <!-- Stats -->
            <section class="grid grid-cols-2 gap-3">
                <div class="rounded-[28px] bg-amber-100 p-4 shadow-sm ring-1 ring-amber-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xl">🧹</span>
                        <span class="rounded-full bg-white/80 px-2 py-1 text-[10px] font-semibold text-amber-700">
                            Aufgaben
                        </span>
                    </div>
                    <p class="mt-4 text-3xl font-bold text-amber-950">{{ $stats['open_chores'] }}</p>
                    <p class="mt-1 text-xs font-medium text-amber-800">offen für heute</p>
                </div>

                <div class="rounded-[28px] bg-sky-100 p-4 shadow-sm ring-1 ring-sky-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xl">🛒</span>
                        <span class="rounded-full bg-white/80 px-2 py-1 text-[10px] font-semibold text-sky-700">
                            Einkauf
                        </span>
                    </div>
                    <p class="mt-4 text-3xl font-bold text-sky-950">{{ $stats['open_shopping'] }}</p>
                    <p class="mt-1 text-xs font-medium text-sky-800">Artikel auf der Liste</p>
                </div>

                <div class="rounded-[28px] bg-violet-100 p-4 shadow-sm ring-1 ring-violet-200">
                    <div class="flex items-center justify-between">
                        <span class="text-xl">📅</span>
                        <span class="rounded-full bg-white/80 px-2 py-1 text-[10px] font-semibold text-violet-700">
                            Termine
                        </span>
                    </div>
                    <p class="mt-4 text-3xl font-bold text-violet-950">{{ $stats['next_events'] }}</p>
                    <p class="mt-1 text-xs font-medium text-violet-800">kommende Einträge</p>
                </div>

                <div class="rounded-[28px] {{ $stats['overdue_chores'] > 0 ? 'bg-rose-100 ring-rose-200' : 'bg-emerald-100 ring-emerald-200' }} p-4 shadow-sm ring-1">
                    <div class="flex items-center justify-between">
                        <span class="text-xl">{{ $stats['overdue_chores'] > 0 ? '⚠️' : '✅' }}</span>
                        <span class="rounded-full bg-white/80 px-2 py-1 text-[10px] font-semibold {{ $stats['overdue_chores'] > 0 ? 'text-rose-700' : 'text-emerald-700' }}">
                            Status
                        </span>
                    </div>
                    <p class="mt-4 text-3xl font-bold {{ $stats['overdue_chores'] > 0 ? 'text-rose-950' : 'text-emerald-950' }}">
                        {{ $stats['overdue_chores'] }}
                    </p>
                    <p class="mt-1 text-xs font-medium {{ $stats['overdue_chores'] > 0 ? 'text-rose-800' : 'text-emerald-800' }}">
                        {{ $stats['overdue_chores'] > 0 ? 'überfällige Aufgaben' : 'nichts überfällig' }}
                    </p>
                </div>
            </section>

            <section class="rounded-[32px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
    <div class="flex items-center justify-between gap-3">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Schnellzugriff</h2>
            <p class="text-sm text-slate-500">Direkt zu den wichtigsten Bereichen</p>
        </div>
    </div>

    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
        <a href="{{ route('chores.index') }}"
           class="rounded-[24px] bg-gradient-to-br from-amber-400 to-orange-500 px-3 py-4 text-center text-white shadow-md">
            <div class="text-xl">🧹</div>
            <div class="mt-2 text-sm font-bold">Putzplan</div>
            <div class="mt-1 text-[11px] text-white/80">Aufgaben</div>
        </a>

        <a href="{{ route('trips.index') }}"
           class="rounded-[24px] bg-gradient-to-br from-emerald-500 to-teal-600 px-3 py-4 text-center text-white shadow-md">
            <div class="text-xl">🚐</div>
            <div class="mt-2 text-sm font-bold">Urlaub</div>
            <div class="mt-1 text-[11px] text-white/80">Trips</div>
        </a>

        <a href="{{ route('insurances.index') }}"
           class="rounded-[24px] bg-gradient-to-br from-emerald-400 to-teal-500 px-3 py-4 text-center text-white shadow-md">
            <div class="text-xl">🛡️</div>
            <div class="mt-2 text-sm font-bold">Versicherung</div>
            <div class="mt-1 text-[11px] text-white/80">Verträge</div>
        </a>

        <a href="{{ route('costs.index') }}"
           class="rounded-[24px] bg-gradient-to-br from-cyan-400 to-sky-500 px-3 py-4 text-center text-white shadow-md">
            <div class="text-xl">💶</div>
            <div class="mt-2 text-sm font-bold">Kosten</div>
            <div class="mt-1 text-[11px] text-white/80">Fixkosten</div>
        </a>

        <a href="{{ route('shopping.index') }}"
           class="rounded-[24px] bg-gradient-to-br from-sky-400 to-blue-500 px-3 py-4 text-center text-white shadow-md">
            <div class="text-xl">🛒</div>
            <div class="mt-2 text-sm font-bold">Einkauf</div>
            <div class="mt-1 text-[11px] text-white/80">Liste</div>
        </a>

        <a href="{{ route('shopping.index') }}#food-check"
           class="rounded-[24px] bg-gradient-to-br from-emerald-400 to-teal-500 px-3 py-4 text-center text-white shadow-md">
            <div class="text-xl">🥣</div>
            <div class="mt-2 text-sm font-bold">Food Check</div>
            <div class="mt-1 text-[11px] text-white/80">Alternativen</div>
        </a>

        <a href="{{ route('events.index') }}"
           class="rounded-[24px] bg-gradient-to-br from-violet-400 to-fuchsia-500 px-3 py-4 text-center text-white shadow-md">
            <div class="text-xl">📅</div>
            <div class="mt-2 text-sm font-bold">Termine</div>
            <div class="mt-1 text-[11px] text-white/80">Kalender</div>
        </a>
    </div>
</section>

            <!-- Open chores -->
            <section class="rounded-[32px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">Heute</p>
                        <h2 class="text-lg font-bold text-slate-900">Offene Aufgaben</h2>
                    </div>

                    <a href="{{ route('chores.index') }}"
                       class="rounded-full bg-amber-100 px-3 py-1.5 text-xs font-semibold text-amber-700">
                        {{ $openChores->count() }}
                    </a>
                </div>

                @if($openChores->isEmpty())
                    <div class="mt-4 rounded-[24px] bg-slate-50 px-4 py-6 text-center ring-1 ring-slate-200">
                        <div class="text-2xl">✨</div>
                        <p class="mt-2 text-sm font-semibold text-slate-800">Keine offenen Aufgaben</p>
                        <p class="mt-1 text-xs text-slate-500">Heute ist in diesem Bereich alles erledigt.</p>
                    </div>
                @else
                    <div class="mt-4 space-y-3">
                        @foreach($openChores as $chore)
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
                                        </div>
                                    </div>

                                    @if($chore->assignedUser)
                                        <span class="rounded-full bg-slate-900 px-2.5 py-1 text-xs font-semibold text-white">
                                            {{ $chore->assignedUser->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="rounded-[32px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-sky-600">Quick</p>
            <h2 class="text-lg font-bold text-slate-900">Diese Woche erledigt</h2>
            <p class="text-xs text-slate-500">{{ $quickWeekRangeLabel }}</p>
        </div>

        <a href="{{ route('chores.index', ['quick_filter' => 'week']) }}"
           class="rounded-full bg-sky-100 px-3 py-1.5 text-xs font-semibold text-sky-700">
            {{ $quickSummaryWeek->sum('count') }}
        </a>
    </div>

    @if($quickSummaryWeek->isEmpty())
        <div class="mt-4 rounded-[24px] bg-slate-50 px-4 py-6 text-center ring-1 ring-slate-200">
            <div class="text-2xl">⚡</div>
            <p class="mt-2 text-sm font-semibold text-slate-800">Noch keine Quick-Einträge</p>
            <p class="mt-1 text-xs text-slate-500">Schnelle Haushaltsaktionen erscheinen später hier.</p>
        </div>
    @else
        <div class="mt-4 rounded-[24px] bg-slate-50 p-3 ring-1 ring-slate-200">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Auswertung</p>
                    <h3 class="text-base font-bold text-slate-900">Diese Woche</h3>
                    <p class="text-xs text-slate-500">{{ $quickWeekRangeLabel }}</p>
                </div>
                <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                    {{ $quickSummaryWeek->sum('count') }} Einträge
                </span>
            </div>

            <div class="mt-3 flex flex-wrap gap-2">
                @foreach($quickSummaryWeek as $stat)
                    <span class="rounded-full bg-white px-3 py-2 text-xs font-semibold text-slate-700 ring-1 ring-slate-200">
                        {{ $stat['label'] }} · {{ $stat['count'] }}x
                    </span>
                @endforeach
            </div>
        </div>
    @endif
</section>

            <section class="rounded-[32px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Versicherung</p>
            <h2 class="text-lg font-bold text-slate-900">Versicherungsüberblick</h2>
        </div>

        <a href="{{ route('insurances.index') }}"
           class="rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-semibold text-emerald-700">
            {{ $insuranceSummary['active_count'] }}
        </a>
    </div>

    <div class="mt-4 grid grid-cols-2 gap-3">
        <div class="rounded-[24px] bg-emerald-50 p-3 ring-1 ring-emerald-100">
            <p class="text-xs font-semibold text-emerald-700">Aktiv</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">{{ $insuranceSummary['active_count'] }}</p>
            <p class="mt-1 text-xs text-slate-500">laufende Versicherungen</p>
        </div>

        <div class="rounded-[24px] bg-amber-50 p-3 ring-1 ring-amber-100">
            <p class="text-xs font-semibold text-amber-700">Bald fällig</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">{{ $insuranceSummary['ending_soon_count'] }}</p>
            <p class="mt-1 text-xs text-slate-500">enden in 30 Tagen</p>
        </div>

        <div class="rounded-[24px] bg-slate-50 p-3 ring-1 ring-slate-200">
            <p class="text-xs font-semibold text-slate-700">Monatlich</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($insuranceSummary['monthly_total'], 2, ',', '.') }} €</p>
            <p class="mt-1 text-xs text-slate-500">umgerechnet</p>
        </div>

        <div class="rounded-[24px] bg-slate-50 p-3 ring-1 ring-slate-200">
            <p class="text-xs font-semibold text-slate-700">Jährlich</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($insuranceSummary['yearly_total'], 2, ',', '.') }} €</p>
            <p class="mt-1 text-xs text-slate-500">umgerechnet</p>
        </div>
    </div>
</section>

<section class="rounded-[32px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-cyan-600">Kosten</p>
            <h2 class="text-lg font-bold text-slate-900">Kostenüberblick</h2>
        </div>

        <a href="{{ route('costs.index') }}"
           class="rounded-full bg-cyan-100 px-3 py-1.5 text-xs font-semibold text-cyan-700">
            {{ $costSummary['cost_count'] }}
        </a>
    </div>

    <div class="mt-4 grid grid-cols-2 gap-3">
        <div class="rounded-[24px] bg-cyan-50 p-3 ring-1 ring-cyan-100">
            <p class="text-xs font-semibold text-cyan-700">Kosten</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">{{ $costSummary['cost_count'] }}</p>
            <p class="mt-1 text-xs text-slate-500">aktive Positionen</p>
        </div>

        <div class="rounded-[24px] bg-slate-50 p-3 ring-1 ring-slate-200">
            <p class="text-xs font-semibold text-slate-700">Monatlich</p>
            <p class="mt-1 text-2xl font-bold text-slate-900">
                {{ number_format($costSummary['monthly_cost_total'], 2, ',', '.') }} €
            </p>
            <p class="mt-1 text-xs text-slate-500">Fixkosten pro Monat</p>
        </div>

        @if($costSummary['show_income_section'])
            <div class="rounded-[24px] bg-emerald-50 p-3 ring-1 ring-emerald-100">
                <p class="text-xs font-semibold text-emerald-700">Einnahmen</p>
                <p class="mt-1 text-2xl font-bold text-slate-900">{{ $costSummary['income_count'] }}</p>
                <p class="mt-1 text-xs text-slate-500">aktive Positionen</p>
            </div>

            <div class="rounded-[24px] bg-emerald-50 p-3 ring-1 ring-emerald-100">
                <p class="text-xs font-semibold text-emerald-700">Einnahmen / Monat</p>
                <p class="mt-1 text-2xl font-bold text-slate-900">
                    {{ number_format($costSummary['monthly_income_total'], 2, ',', '.') }} €
                </p>
                <p class="mt-1 text-xs text-slate-500">umgerechnet</p>
            </div>

            <div class="col-span-2 rounded-[24px] bg-white p-3 ring-1 ring-slate-200">
                <p class="text-xs font-semibold text-slate-700">Netto / Monat</p>
                <p class="mt-1 text-2xl font-bold text-slate-900">
                    {{ number_format($costSummary['monthly_net_total'], 2, ',', '.') }} €
                </p>
                <p class="mt-1 text-xs text-slate-500">Einnahmen minus Kosten</p>
            </div>
        @endif
    </div>
</section>

            <!-- Shopping -->
            <section class="rounded-[32px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-sky-600">Besorgen</p>
                        <h2 class="text-lg font-bold text-slate-900">Einkaufsliste</h2>
                    </div>

                    <a href="{{ route('shopping.index') }}#open-shopping-items"
   class="rounded-full bg-sky-100 px-3 py-1.5 text-xs font-semibold text-sky-700">
    {{ $openShoppingItems->count() }}
</a>
                </div>

                @if($openShoppingItems->isEmpty())
                    <div class="mt-4 rounded-[24px] bg-slate-50 px-4 py-6 text-center ring-1 ring-slate-200">
                        <div class="text-2xl">🧺</div>
                        <p class="mt-2 text-sm font-semibold text-slate-800">Keine offenen Einkäufe</p>
                        <p class="mt-1 text-xs text-slate-500">Im Moment steht nichts auf eurer Liste.</p>
                    </div>
                @else
                    <div class="mt-4 space-y-3">
                        @foreach($openShoppingItems as $item)
                            <div class="rounded-[24px] bg-sky-50 p-3 ring-1 ring-sky-100">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="font-bold text-slate-900">{{ $item->title }}</p>

                                        <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                            <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                                {{ $item->category }}
                                            </span>

                                            @if($item->quantity)
                                                <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                                    Menge: {{ $item->quantity }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <span class="rounded-full bg-sky-600 px-2.5 py-1 text-xs font-semibold text-white">
                                        Offen
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>

            <!-- Events -->
            <section id="upcoming-events" class="scroll-mt-6 rounded-[32px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-violet-600">Planung</p>
                        <h2 class="text-lg font-bold text-slate-900">Nächste Termine</h2>
                    </div>

                    <a href="{{ route('events.index') }}#upcoming-events"
   class="rounded-full bg-violet-100 px-3 py-1.5 text-xs font-semibold text-violet-700">
    {{ $nextEvents->count() }}
</a>
                </div>

                @if($nextEvents->isEmpty())
                    <div class="mt-4 rounded-[24px] bg-slate-50 px-4 py-6 text-center ring-1 ring-slate-200">
                        <div class="text-2xl">📭</div>
                        <p class="mt-2 text-sm font-semibold text-slate-800">Keine Termine geplant</p>
                        <p class="mt-1 text-xs text-slate-500">Lege den nächsten Termin direkt an.</p>
                    </div>
                @else
                    <div class="mt-4 space-y-3">
                        @foreach($nextEvents as $event)
                            <div class="rounded-[24px] bg-violet-50 p-3 ring-1 ring-violet-100">
                                <p class="font-bold text-slate-900">{{ $event->title }}</p>

                                <div class="mt-2 flex flex-wrap gap-2 text-xs">
                                    <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                        {{ $event->starts_at->format('d.m.Y H:i') }}
                                    </span>

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
    <a href="{{ route('events.index') }}#event-{{ $event->id }}"
       class="mt-3 block overflow-hidden rounded-[20px] bg-white ring-1 ring-violet-100">
        <img
            src="{{ asset('storage/' . $imageAttachment->file_path) }}"
            alt="Bild zu {{ $event->title }}"
            class="h-44 w-full object-cover transition hover:scale-[1.02]"
        >
    </a>
@endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </section>
        </main>

        @include('partials.bottom-nav', ['active' => 'dashboard'])
    </div>
</body>
</html>