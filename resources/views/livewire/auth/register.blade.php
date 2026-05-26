<x-layouts::auth :title="__('Register')">
    <x-auth.premium-shell
        :title="__('Register')"
        heading="Konto erstellen"
        descriptionId="register-typing"
    >
    <style>
        a[wire\:navigate].flex.flex-col.items-center.gap-2.font-medium {
            display: none !important;
        }
    </style>
        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 9.75V6.75A2.25 2.25 0 0015.75 4.5h-7.5A2.25 2.25 0 006 6.75v10.5A2.25 2.25 0 008.25 19.5h7.5A2.25 2.25 0 0018 17.25v-3M12 8.25h.008v.008H12V8.25zm0 3.75h.008v.008H12V12zm0 3.75h.008v.008H12v-.008z" />
            </svg>
        </x-slot:icon>

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-5" id="register-form">
            @csrf

            <div class="space-y-4">
                <flux:input
                    name="name"
                    :label="__('Name')"
                    :value="old('name')"
                    type="text"
                    required
                    autofocus
                    autocomplete="name"
                    :placeholder="__('Full name')"
                />

                <flux:input
                    name="email"
                    :label="__('Email address')"
                    :value="old('email')"
                    type="email"
                    required
                    autocomplete="email"
                    placeholder="email@example.com"
                />

                <flux:input
                    name="password"
                    :label="__('Password')"
                    type="password"
                    required
                    autocomplete="new-password"
                    :placeholder="__('Password')"
                    viewable
                />

                <flux:input
                    name="password_confirmation"
                    :label="__('Confirm password')"
                    type="password"
                    required
                    autocomplete="new-password"
                    :placeholder="__('Confirm password')"
                    viewable
                />
            </div>

            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-xs leading-5 text-slate-300">
                Schnell registrieren und direkt loslegen.
            </div>

            <div class="pt-2">
                <flux:button
                    type="submit"
                    variant="primary"
                    class="w-full rounded-[20px] border-0 bg-white py-3 text-sm font-semibold text-slate-900 shadow-[0_10px_30px_rgba(255,255,255,0.12)] transition duration-200 hover:scale-[1.01] hover:bg-slate-100 active:scale-[0.99]"
                    data-test="register-user-button"
                    id="register-submit"
                >
                    <span class="btn-text">{{ __('Create account') }}</span>
                </flux:button>
            </div>
        </form>

        <x-slot:after>
            <div class="text-center text-sm text-slate-300">
                <span>{{ __('Already have an account?') }}</span>
                <flux:link
                    :href="route('login')"
                    wire:navigate
                    class="font-semibold text-white transition hover:text-emerald-300"
                >
                    {{ __('Log in') }}
                </flux:link>
            </div>
        </x-slot:after>

        <x-slot:scripts>
    <script>
        function initRegisterPage() {
            setupAuthTyping('register-typing', [
                'Dein Start in eine saubere App.',
                'Modern registrieren.',
                'Wenige Sekunden bis zum Konto.'
            ]);

            setupAuthSubmit('register-form', 'register-submit', 'Konto wird erstellt...');
            setupAuthCardTilt();
        }

        document.addEventListener('DOMContentLoaded', initRegisterPage);
        document.addEventListener('livewire:navigated', initRegisterPage);

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