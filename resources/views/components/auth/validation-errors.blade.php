@if ($errors->any())
    <div class="mb-4 rounded-2xl border border-rose-400/20 bg-rose-500/10 p-4 text-sm text-rose-100 shadow-sm">
        <div class="mb-2 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-rose-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.007v.008H12v-.008z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86 1.82 18a2.25 2.25 0 0 0 1.93 3.38h16.5A2.25 2.25 0 0 0 22.18 18L13.71 3.86a2.25 2.25 0 0 0-3.42 0Z" />
            </svg>
            <span class="font-semibold">Bitte prüfe deine Eingaben</span>
        </div>

        <ul class="space-y-1 pl-7">
            @foreach ($errors->all() as $error)
                <li class="list-disc">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif