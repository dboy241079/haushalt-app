<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haushalt verwalten | Haushalt App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-900">
    <div class="mx-auto min-h-screen w-full max-w-md bg-slate-100">

        <section class="relative overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950 px-4 pb-6 pt-5 text-white">
            <div class="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-emerald-400/20 blur-3xl"></div>
            <div class="absolute -left-8 bottom-0 h-24 w-24 rounded-full bg-teal-400/20 blur-2xl"></div>

            <div class="relative z-10">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-slate-300">
                            Haushalt
                        </p>
                        <h1 class="mt-1 truncate text-2xl font-bold">
                            {{ $household->name }}
                        </h1>
                        <p class="mt-1 text-sm text-slate-300">
                            Rolle: {{ $membership->role === 'admin' ? 'Admin' : 'Mitglied' }}
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
                            <p class="text-xs font-medium text-slate-300">Gemeinsam nutzbar</p>
                            <p class="mt-1 text-xl font-bold">Mitglieder und Name verwalten</p>
                            <p class="mt-1 text-sm text-slate-300">
                                Haushalt personalisieren und andere hinzufügen.
                            </p>
                        </div>

                        <div class="flex h-14 w-14 items-center justify-center rounded-3xl bg-white/10 text-2xl ring-1 ring-white/15">
                            🏠
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <main <main class="app-main">
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
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Name</p>
                    <h2 class="text-lg font-bold text-slate-900">Haushaltsname</h2>
                </div>

                @if($isAdmin)
                    <form action="{{ route('household.updateName') }}" method="POST" class="mt-4 space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <label for="name" class="mb-1 block text-sm font-medium text-slate-700">Name</label>
                            <input
                                id="name"
                                name="name"
                                type="text"
                                value="{{ old('name', $household->name) }}"
                                class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                                required
                            >
                        </div>

                        <button
                            type="submit"
                            class="w-full rounded-[24px] bg-gradient-to-r from-emerald-400 to-teal-500 px-4 py-3 text-sm font-semibold text-white shadow-md">
                            Namen speichern
                        </button>
                    </form>
                @else
                    <div class="mt-4 rounded-[24px] bg-slate-50 px-4 py-5 ring-1 ring-slate-200">
                        <p class="text-sm text-slate-500">Nur Admins können den Haushaltsnamen ändern.</p>
                    </div>
                @endif
            </section>

            <section class="rounded-[32px] bg-white p-4 shadow-sm ring-1 ring-slate-200">
    <div class="flex items-center justify-between gap-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Mitglieder</p>
            <h2 class="text-lg font-bold text-slate-900">Haushalt gemeinsam nutzen</h2>
        </div>
        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">
            {{ $members->count() }}
        </span>
    </div>

    @if($isAdmin)
        <form action="{{ route('household.members.store') }}" method="POST" class="mt-4 space-y-4">
            @csrf

            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-slate-700">Benutzer per E-Mail einladen</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                    placeholder="name@mail.de"
                    required
                >
            </div>

            <div>
                <label for="role" class="mb-1 block text-sm font-medium text-slate-700">Rolle</label>
                <select
                    id="role"
                    name="role"
                    class="w-full rounded-[20px] border border-slate-300 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-emerald-500 focus:bg-white"
                    required
                >
                    <option value="member" {{ old('role', 'member') === 'member' ? 'selected' : '' }}>Mitglied</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>

            <button
                type="submit"
                class="w-full rounded-[24px] bg-gradient-to-r from-emerald-400 to-teal-500 px-4 py-3 text-sm font-semibold text-white shadow-md"
            >
                Einladung senden
            </button>
        </form>
    @endif

    <div class="mt-5 space-y-3">
        @forelse($members as $member)
            <div class="rounded-[24px] bg-emerald-50 p-3 ring-1 ring-emerald-100">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-bold text-slate-900">
                            {{ $member->user?->name ?? $member->display_name ?? 'Unbekannt' }}

                            @if((int) $member->user_id === (int) $user->id)
                                <span class="ml-1 rounded-full bg-slate-900 px-2 py-0.5 text-[10px] font-semibold text-white">
                                    Du
                                </span>
                            @endif
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            {{ $member->user?->email ?? 'Keine E-Mail' }}
                        </p>
                    </div>

                    <span class="rounded-full {{ $member->role === 'admin' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-700 ring-1 ring-slate-200' }} px-2.5 py-1 text-xs font-semibold">
                        {{ $member->role === 'admin' ? 'Admin' : 'Mitglied' }}
                    </span>
                </div>

                @if($isAdmin)
                    <div class="mt-3 flex flex-col gap-2">
                        <form action="{{ route('household.members.update', $member) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')

                            <select
                                name="role"
                                class="flex-1 rounded-[18px] border border-slate-300 bg-white px-3 py-2 text-sm outline-none transition focus:border-emerald-500"
                            >
                                <option value="member" {{ $member->role === 'member' ? 'selected' : '' }}>Mitglied</option>
                                <option value="admin" {{ $member->role === 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>

                            <button
                                type="submit"
                                class="rounded-[18px] bg-slate-900 px-3 py-2 text-xs font-semibold text-white shadow-sm"
                            >
                                Speichern
                            </button>
                        </form>

                        @if((int) $member->user_id !== (int) $user->id)
                            <form action="{{ route('household.members.destroy', $member) }}" method="POST" onsubmit="return confirm('Mitglied wirklich entfernen?');">
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="rounded-[18px] bg-red-600 px-3 py-2 text-xs font-semibold text-white shadow-sm"
                                >
                                    Entfernen
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <div class="rounded-[24px] bg-slate-50 px-4 py-6 text-center ring-1 ring-slate-200">
                <p class="text-sm font-semibold text-slate-800">Noch keine Mitglieder vorhanden</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6 border-t border-slate-200 pt-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">Einladungen</p>
                <h3 class="text-base font-bold text-slate-900">Warten auf Annahme</h3>
            </div>

            <span class="rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">
                {{ $pendingInvitations->count() }}
            </span>
        </div>

        <div class="mt-4 space-y-3">
            @forelse($pendingInvitations as $invitation)
                <div class="rounded-[24px] bg-amber-50 p-3 ring-1 ring-amber-100">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900">
                                Einladung offen
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $invitation->email }}
                            </p>

                            <div class="mt-2 flex flex-wrap gap-2 text-[11px]">
                                <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                    {{ $invitation->role === 'admin' ? 'Admin' : 'Mitglied' }}
                                </span>

                                <span class="rounded-full bg-amber-100 px-2 py-1 font-medium text-amber-700 ring-1 ring-amber-200">
                                    Wartet auf Annahme
                                </span>

                                @if($invitation->expires_at)
                                    <span class="rounded-full bg-white px-2 py-1 font-medium text-slate-600 ring-1 ring-slate-200">
                                        bis {{ $invitation->expires_at->format('d.m.Y H:i') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($isAdmin)
                        <div class="mt-3 flex flex-wrap gap-2">
                            <form action="{{ route('household.invitations.resend', $invitation) }}" method="POST">
                                @csrf
                                <button
                                    type="submit"
                                    class="rounded-[18px] bg-slate-900 px-3 py-2 text-xs font-semibold text-white shadow-sm"
                                >
                                    Erneut senden
                                </button>
                            </form>

                            <form action="{{ route('household.invitations.destroy', $invitation) }}" method="POST" onsubmit="return confirm('Einladung wirklich löschen?');">
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="rounded-[18px] bg-red-600 px-3 py-2 text-xs font-semibold text-white shadow-sm"
                                >
                                    Einladung löschen
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            @empty
                <div class="rounded-[24px] bg-slate-50 px-4 py-6 text-center ring-1 ring-slate-200">
                    <p class="text-sm font-semibold text-slate-800">Keine offenen Einladungen</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
        </main>

        @include('partials.bottom-nav', ['active' => 'household'])
    </div>
</body>
</html>