<x-layouts::auth :title="__('Forgot password')">
    <x-auth.premium-shell
    :title="__('Forgot password')"
    heading="Passwort vergessen"
    descriptionId="forgot-typing"
    badge="Passwort-Hilfe"
    :hide-status="true"
>
        <style>
            a[wire\:navigate].flex.flex-col.items-center.gap-2.font-medium {
                display: none !important;
            }
        </style>

        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9a2.25 2.25 0 1 1 4.5 0c0 1.094-.786 1.71-1.48 2.253-.627.49-1.145.894-1.145 1.497v.375M12 17.25h.008v.008H12v-.008Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
        </x-slot:icon>

       @if (session('status'))
    <div
        id="forgot-success-box"
        class="mb-5 rounded-[24px] border border-emerald-400/20 bg-emerald-400/10 px-4 py-5 text-center text-sm leading-6 text-emerald-100"
    >
        <p class="font-semibold text-base text-white">E-Mail wurde gesendet.</p>

        <p class="mt-3">
            Wir haben dir einen Link zum Zurücksetzen deines Passworts geschickt.
        </p>

        <p class="mt-2">
            Bitte öffne die E-Mail, klicke auf den Link und setze dein neues Passwort.
        </p>

        <p class="mt-3 font-medium text-white">
            Du wirst gleich automatisch wieder zum Login weitergeleitet.
        </p>

        <div class="mt-4 inline-flex items-center justify-center rounded-full border border-white/10 bg-white/10 px-4 py-2 text-sm font-semibold text-white">
            Weiterleitung in
            <span id="forgot-countdown" class="ml-1">9</span>
            <span class="ml-1">Sekunden</span>
        </div>

        <p class="mt-3 text-xs text-emerald-200/90">
            Falls nichts ankommt, prüfe bitte auch deinen Spam-Ordner.
        </p>
    </div>
@endif

        @if (!session('status'))
            <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-5" id="forgot-form">
                @csrf

                <div class="space-y-4">
                    <flux:input
                        name="email"
                        :value="old('email')"
                        label="E-Mail-Adresse"
                        type="email"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="email@example.com"
                    />
                </div>

                <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-xs leading-5 text-slate-300">
                    Gib deine E-Mail-Adresse ein. Wir schicken dir dann einen Link, mit dem du dein Passwort zurücksetzen kannst.
                </div>

                <div class="pt-2">
                    <flux:button
                        variant="primary"
                        type="submit"
                        class="w-full rounded-[20px] border-0 bg-white py-3 text-sm font-semibold text-slate-900 shadow-[0_10px_30px_rgba(255,255,255,0.12)] transition duration-200 hover:scale-[1.01] hover:bg-slate-100 active:scale-[0.99]"
                        data-test="email-password-reset-link-button"
                        id="forgot-submit"
                    >
                        <span class="btn-text">Link zum Zurücksetzen senden</span>
                    </flux:button>
                </div>
            </form>
        @endif

        <x-slot:after>
            <div class="text-center text-sm text-slate-300">
                <span>Zurück zum</span>
                <flux:link
                    :href="route('login')"
                    wire:navigate
                    class="font-semibold text-white transition hover:text-sky-300"
                >
                    Login
                </flux:link>
            </div>
        </x-slot:after>

        <x-slot:scripts>
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    setupAuthTyping('forgot-typing', [
                        'Kein Problem, wir bringen dich zurück.',
                        'Link anfordern und Passwort erneuern.',
                        'Schnell wieder Zugriff bekommen.'
                    ]);

                    setupAuthSubmit('forgot-form', 'forgot-submit', 'Link wird gesendet...');
                    setupAuthCardTilt();

                    @if (session('status'))
    let seconds = 9;
    const countdownEl = document.getElementById('forgot-countdown');

    const countdownInterval = setInterval(() => {
        seconds--;

        if (countdownEl && seconds >= 0) {
            countdownEl.textContent = seconds;
        }

        if (seconds <= 0) {
            clearInterval(countdownInterval);
            window.location.href = "{{ route('login') }}";
        }
    }, 1000);
@endif
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