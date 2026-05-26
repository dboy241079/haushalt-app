<x-layouts::auth :title="__('Reset password')">
    <x-auth.premium-shell
        :title="__('Reset password')"
        heading="Passwort zurücksetzen"
        descriptionId="reset-typing"
    >
    <style>
        a[wire\:navigate].flex.flex-col.items-center.gap-2.font-medium {
            display: none !important;
        }
    </style>
        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25m0 0a3.75 3.75 0 10-7.5 0V9m7.5-3.75h1.5A2.25 2.25 0 0119.5 7.5v9A2.25 2.25 0 0117.25 18.75H6.75A2.25 2.25 0 014.5 16.5v-9a2.25 2.25 0 012.25-2.25h1.5" />
            </svg>
        </x-slot:icon>

        <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-5" id="reset-form">
            @csrf

            <input type="hidden" name="token" value="{{ request()->route('token') }}">

            <div class="space-y-4">
                <flux:input
                    name="email"
                    value="{{ request('email') }}"
                    :label="__('Email')"
                    type="email"
                    required
                    autocomplete="email"
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
                Wähle ein sicheres Passwort für deinen neuen Zugang.
            </div>

            <div class="pt-2">
                <flux:button
                    type="submit"
                    variant="primary"
                    class="w-full rounded-[20px] border-0 bg-white py-3 text-sm font-semibold text-slate-900 shadow-[0_10px_30px_rgba(255,255,255,0.12)] transition duration-200 hover:scale-[1.01] hover:bg-slate-100 active:scale-[0.99]"
                    data-test="reset-password-button"
                    id="reset-submit"
                >
                    <span class="btn-text">{{ __('Reset password') }}</span>
                </flux:button>
            </div>
        </form>

        <x-slot:scripts>
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    setupAuthTyping('reset-typing', [
                        'Sicher zurück in dein Konto.',
                        'Ein neues Passwort und weiter.',
                        'Klarer Zugriff ohne Umwege.'
                    ]);

                    setupAuthSubmit('reset-form', 'reset-submit', 'Passwort wird gesetzt...');
                    setupAuthCardTilt();
                });

                function setupAuthTyping(targetId, lines) {
                    const target = document.getElementById(targetId);
                    if (!target) return;

                    let lineIndex = 0;
                    let charIndex = 0;
                    let deleting = false;

                    function animateText() {
                        const current = lines[lineIndex];

                        if (!deleting) {
                            target.textContent = current.slice(0, charIndex++);
                            if (charIndex > current.length) {
                                deleting = true;
                                setTimeout(animateText, 1500);
                                return;
                            }
                        } else {
                            target.textContent = current.slice(0, charIndex--);
                            if (charIndex < 0) {
                                deleting = false;
                                lineIndex = (lineIndex + 1) % lines.length;
                            }
                        }

                        setTimeout(animateText, deleting ? 30 : 55);
                    }

                    animateText();
                }

                function setupAuthSubmit(formId, buttonId, loadingText) {
                    const form = document.getElementById(formId);
                    const submit = document.getElementById(buttonId);

                    if (form && submit) {
                        form.addEventListener('submit', () => {
                            submit.setAttribute('disabled', 'disabled');
                            const text = submit.querySelector('.btn-text');
                            if (text) text.textContent = loadingText;
                        });
                    }
                }

                function setupAuthCardTilt() {
                    const card = document.querySelector('.auth-premium-card');
                    if (!card) return;

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