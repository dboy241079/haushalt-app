<nav class="bottom-safe-nav fixed bottom-0 left-1/2 z-40 w-full max-w-md -translate-x-1/2">
    <div class="grid grid-cols-8 rounded-[28px] border border-slate-200 bg-white/95 p-2 shadow-2xl backdrop-blur">
        <a href="{{ route('dashboard') }}"
           class="rounded-2xl px-1 py-2 text-center transition {{ ($active ?? '') === 'dashboard' ? 'scale-[1.03] bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'text-slate-500' }}">
            <span class="flex flex-col items-center gap-1">
                <span class="text-sm">🏠</span>
                <span class="text-[9px] font-{{ ($active ?? '') === 'dashboard' ? 'semibold' : 'medium' }}">Heute</span>
            </span>
        </a>

        <a href="{{ route('chores.index') }}"
           class="rounded-2xl px-1 py-2 text-center transition {{ ($active ?? '') === 'chores' ? 'scale-[1.03] bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'text-slate-500' }}">
            <span class="flex flex-col items-center gap-1">
                <span class="text-sm">🧹</span>
                <span class="text-[9px] font-{{ ($active ?? '') === 'chores' ? 'semibold' : 'medium' }}">Putz</span>
            </span>
        </a>

        <a href="{{ route('shopping.index') }}"
           class="rounded-2xl px-1 py-2 text-center transition {{ ($active ?? '') === 'shopping' ? 'scale-[1.03] bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'text-slate-500' }}">
            <span class="flex flex-col items-center gap-1">
                <span class="text-sm">🛒</span>
                <span class="text-[9px] font-{{ ($active ?? '') === 'shopping' ? 'semibold' : 'medium' }}">Shop</span>
            </span>
        </a>

        <a href="{{ route('events.index') }}"
           class="rounded-2xl px-1 py-2 text-center transition {{ ($active ?? '') === 'events' ? 'scale-[1.03] bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'text-slate-500' }}">
            <span class="flex flex-col items-center gap-1">
                <span class="text-sm">📅</span>
                <span class="text-[9px] font-{{ ($active ?? '') === 'events' ? 'semibold' : 'medium' }}">Termine</span>
            </span>
        </a>

        <a href="{{ route('trips.index') }}"
           class="rounded-2xl px-1 py-2 text-center transition {{ ($active ?? '') === 'trips' ? 'scale-[1.03] bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'text-slate-500' }}">
            <span class="flex flex-col items-center gap-1">
                <span class="text-sm">🚐</span>
                <span class="text-[9px] font-{{ ($active ?? '') === 'trips' ? 'semibold' : 'medium' }}">Urlaub</span>
            </span>
        </a>

        <a href="{{ route('insurances.index') }}"
           class="rounded-2xl px-1 py-2 text-center transition {{ ($active ?? '') === 'insurances' ? 'scale-[1.03] bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'text-slate-500' }}">
            <span class="flex flex-col items-center gap-1">
                <span class="text-sm">🛡️</span>
                <span class="text-[9px] font-{{ ($active ?? '') === 'insurances' ? 'semibold' : 'medium' }}">Versich.</span>
            </span>
        </a>

        <a href="{{ route('costs.index') }}"
           class="rounded-2xl px-1 py-2 text-center transition {{ ($active ?? '') === 'costs' ? 'scale-[1.03] bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'text-slate-500' }}">
            <span class="flex flex-col items-center gap-1">
                <span class="text-sm">💶</span>
                <span class="text-[9px] font-{{ ($active ?? '') === 'costs' ? 'semibold' : 'medium' }}">Kosten</span>
            </span>
        </a>

        <a href="{{ route('household.settings') }}"
           class="rounded-2xl px-1 py-2 text-center transition {{ ($active ?? '') === 'household' ? 'scale-[1.03] bg-slate-900 text-white shadow-lg shadow-slate-900/20' : 'text-slate-500' }}">
            <span class="flex flex-col items-center gap-1">
                <span class="text-sm">⚙️</span>
                <span class="text-[9px] font-{{ ($active ?? '') === 'household' ? 'semibold' : 'medium' }}">Haus</span>
            </span>
        </a>
    </div>
</nav>