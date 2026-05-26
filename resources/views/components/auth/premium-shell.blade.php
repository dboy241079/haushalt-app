@props([
    'title' => '',
    'heading' => '',
    'descriptionId' => 'auth-typing',
    'hideStatus' => false,
])

<div class="relative min-h-screen overflow-hidden bg-[#0a0f1c]">
    <div class="absolute inset-0">
        <div class="absolute inset-0 bg-[linear-gradient(180deg,#0a0f1c_0%,#10182b_45%,#0f172a_100%)]"></div>
        <div class="absolute inset-0 opacity-60 bg-[radial-gradient(circle_at_top,rgba(96,165,250,0.22),transparent_35%),radial-gradient(circle_at_bottom,rgba(168,85,247,0.14),transparent_30%)]"></div>

        <div class="absolute -top-24 left-1/2 h-72 w-72 -translate-x-1/2 rounded-full bg-sky-400/20 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 h-64 w-64 rounded-full bg-blue-500/10 blur-3xl"></div>
        <div class="absolute bottom-10 right-0 h-72 w-72 rounded-full bg-fuchsia-500/10 blur-3xl"></div>
    </div>

    <div class="relative z-10 flex min-h-screen items-start justify-center px-4 pt-[max(env(safe-area-inset-top),1.5rem)] pb-10">
        <div class="w-full max-w-md">
            <div class="mb-6 text-center">
                @isset($icon)
                    <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-[22px] border border-white/10 bg-white/10 text-white shadow-[0_10px_40px_rgba(0,0,0,0.25)] backdrop-blur-xl">
                        {{ $icon }}
                    </div>
                @endisset

                <h1 class="text-[2rem] font-semibold tracking-tight text-white">
                    {{ $heading }}
                </h1>

                <p class="mt-2 min-h-[8px] text-sm leading-6 text-slate-300">
                    <span id="{{ $descriptionId }}" class="inline-block">&nbsp;</span>
                </p>
            </div>

            <div class="auth-premium-card rounded-[34px] border border-white/10 bg-white/10 p-6 shadow-[0_20px_80px_rgba(0,0,0,0.35)] backdrop-blur-2xl sm:p-8">
                @if (! $hideStatus)
                    <x-auth-session-status
                        class="mb-4 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-center text-sm text-emerald-100"
                        :status="session('status')"
                    />
                @endif

                <x-auth.validation-errors />

                {{ $slot }}

                @isset($after)
                    <div class="mt-6">
                        {{ $after }}
                    </div>
                @endisset
            </div>
        </div>
    </div>
</div>

@isset($scripts)
    {{ $scripts }}
@endisset