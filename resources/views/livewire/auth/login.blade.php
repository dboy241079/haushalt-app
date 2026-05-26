<x-layouts::auth :title="__('Log in')">
    <x-auth.premium-shell :title="__('Log in')" heading="Willkommen zurück" descriptionId="login-typing">
        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V7.875a4.125 4.125 0 10-8.25 0V10.5m-1.5 0h11.25A1.125 1.125 0 0119.125 11.625v7.125A1.125 1.125 0 0118 19.875H6A1.125 1.125 0 014.875 18.75v-7.125A1.125 1.125 0 016 10.5z" />
            </svg>
        </x-slot:icon> 
        <style>
        a[wire\:navigate].flex.flex-col.items-center.gap-2.font-medium {
            display: none !important;
        }
    </style>

        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-5" id="login-form">
            @csrf

            <div class="space-y-4">

           
                <flux:input
                    name="email"
                    :label="__('Email address')"
                    :value="old('email')"
                    type="email"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="email@example.com"
                />

                <div class="relative">
                    <flux:input
                        name="password"
                        :label="__('Password')"
                        type="password"
                        required
                        autocomplete="current-password"
                        :placeholder="__('Password')"
                        viewable
                    />

                    @if (Route::has('password.request'))
                        <flux:link
                            class="absolute end-0 top-0 text-sm font-medium text-sky-300 transition hover:text-white"
                            :href="route('password.request')"
                            wire:navigate
                        >
                            {{ __('Forgot your password?') }}
                        </flux:link>
                    @endif
                </div>
            </div>

            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-slate-200">
                <flux:checkbox name="remember" :label="__('Remember me')" :checked="old('remember')" />
            </div>

            <div class="pt-2">
                <flux:button
                    variant="primary"
                    type="submit"
                    class="w-full rounded-[20px] border-0 bg-white py-3 text-sm font-semibold text-slate-900 shadow-[0_10px_30px_rgba(255,255,255,0.12)] transition duration-200 hover:scale-[1.01] hover:bg-slate-100 active:scale-[0.99]"
                    data-test="login-button"
                    id="login-submit"
                >
                    <span class="btn-text">{{ __('Log in') }}</span>
                </flux:button>
            </div>
        </form>

        @if (Route::has('register'))
            <x-slot:after>
                <div class="text-center text-sm text-slate-300">
                    <span>{{ __('Don\'t have an account?') }}</span>
                    <flux:link
                        :href="route('register')"
                        wire:navigate
                        class="font-semibold text-white transition hover:text-sky-300"
                    >
                        {{ __('Sign up') }}
                    </flux:link>
                </div>
            </x-slot:after>
        @endif

        <x-slot:scripts>
    <script>
        function initLoginPage() {
            setupAuthTyping('login-typing', [
                'Melde dich an und mach direkt weiter.',
                'Klar. Ruhig. Modern.',
                'Alles an einem Ort.'
            ]);

            setupAuthSubmit('login-form', 'login-submit', 'Anmeldung läuft...');
            setupAuthCardTilt();
        }

        document.addEventListener('DOMContentLoaded', initLoginPage);
        document.addEventListener('livewire:navigated', initLoginPage);

        function setupAuthTyping(targetId, lines) {
            const target = document.getElementById(targetId);
            if (!target) return;

            if (target._typingTimeout) {
                clearTimeout(target._typingTimeout);
            }

            target.textContent = '';

            let lineIndex = 0;
            let charIndex = 0;
            let deleting = false;

            function animateText() {
                if (!document.body.contains(target)) return;

                const current = lines[lineIndex];

                if (!deleting) {
                    target.textContent = current.slice(0, charIndex++);
                    if (charIndex > current.length) {
                        deleting = true;
                        target._typingTimeout = setTimeout(animateText, 1500);
                        return;
                    }
                } else {
                    target.textContent = current.slice(0, charIndex--);
                    if (charIndex < 0) {
                        deleting = false;
                        lineIndex = (lineIndex + 1) % lines.length;
                    }
                }

                target._typingTimeout = setTimeout(animateText, deleting ? 30 : 55);
            }

            animateText();
        }

        function setupAuthSubmit(formId, buttonId, loadingText) {
            const form = document.getElementById(formId);
            const submit = document.getElementById(buttonId);

            if (!form || !submit || form.dataset.authSubmitBound === '1') return;

            form.dataset.authSubmitBound = '1';

            form.addEventListener('submit', () => {
                submit.setAttribute('disabled', 'disabled');
                const text = submit.querySelector('.btn-text');
                if (text) text.textContent = loadingText;
            });
        }

        function setupAuthCardTilt() {
            const card = document.querySelector('.auth-premium-card');
            if (!card || card.dataset.authTiltBound === '1') return;

            card.dataset.authTiltBound = '1';
            card.classList.add('transition-transform', 'duration-300');

            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width - 0.5) * 4;
                const y = ((e.clientY - rect.top) / rect.height - 0.5) * -4;
                card.style.transform = `rotateX(${y}deg) rotateY(${x}deg)`;
            });

            card.addEventListener('mouseleave', () => {
                card.style.transform = 'rotateX(0deg) rotateY(0deg)';
            });
        }
    </script>
</x-slot:scripts>
    </x-auth.premium-shell>
</x-layouts::auth>