<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Enter PIN — Bastion Peak</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('fonts/schibsted.css') }}">
    <link rel="stylesheet" href="{{ asset('fonts/ibm-plex.css') }}">
    <style>
        :root {
            --font-display: "Schibsted Grotesk", system-ui, sans-serif;
            --font-body: "IBM Plex Sans", system-ui, sans-serif;
        }
        body { font-family: var(--font-body); }
        h1, h2, h3, h4, h5, .font-display { font-family: var(--font-display); }
    </style>
</head>
<body class="bg-[var(--bg)] text-[var(--text)]">
    <div class="min-h-screen flex flex-col">
        <header class="absolute top-6 left-0 right-0 z-20">
            <div class="max-w-7xl mx-auto flex items-center justify-between px-4 sm:px-6">
                <a href="{{ route('landing') }}">
                    <x-bp-logo :width="40" :height="40" />
                </a>
            </div>
        </header>

        <div class="flex-1 flex items-center justify-center">
            <div class="w-full max-w-md">
                <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl shadow-xl p-8 mx-4">
                    <div class="text-center mb-8">
                        <div class="w-14 h-14 rounded-2xl bg-[var(--accent-subtle)] text-[var(--accent)] flex items-center justify-center mx-auto mb-4">
                            <x-icon name="lock" class="w-7 h-7" />
                        </div>
                        <h1 class="text-2xl font-bold text-[var(--text)] mb-2">Enter your login PIN</h1>
                        <p class="text-sm text-[var(--text-muted)]">
                            Enter the 4-digit PIN provided by support to complete sign-in for
                            <span class="font-medium text-[var(--text)]">{{ $email }}</span>.
                        </p>
                        <p class="text-xs text-[var(--text-muted)] mt-2 flex items-center justify-center gap-1">
                            <x-icon name="clock" class="w-3.5 h-3.5" />
                            <span>This PIN expires in <strong id="pin-countdown">40</strong> seconds.</span>
                        </p>
                    </div>

                        @if($errors->any())
                        <div class="mb-6 p-4 bg-[var(--danger-bg)] border border-[var(--danger)]/30 rounded-xl text-sm text-[var(--danger)] font-medium">
                            {{ $errors->first() }}
                        </div>
                        @endif

                    <form method="POST" action="{{ route('signin.pin.post') }}" class="space-y-5">
                        @csrf

                         <div>
                             <label for="code" class="block text-sm font-medium text-[var(--text)] mb-2">Login PIN</label>
                             <input type="text" id="code" name="code" required autofocus inputmode="numeric"
                                    autocomplete="one-time-code" maxlength="4" pattern="\d{4}"
                                    class="input-field w-full font-mono text-center text-4xl tracking-[0.5em]"
                                    placeholder="0000"
                                    oninput="this.value = this.value.replace(/\D/g, '').slice(0, 4)">
                         </div>

                         <input type="hidden" name="email" value="{{ $email }}">

                         <button type="submit" class="w-full py-3 btn-primary text-sm font-medium">
                             Verify and continue
                        </button>
                    </form>

                    <div class="mt-6 text-center">
                        <a href="{{ route('signin') }}" class="text-sm font-medium text-[var(--accent)] hover:text-[var(--accent-dark)] transition-colors">
                            Back to sign in
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <footer class="pb-8 text-center text-xs text-[var(--text-muted)]">
            <span class="flex items-center justify-center gap-1">
                <x-icon name="shield" class="w-3.5 h-3.5" />
                <span>Two-step verification keeps your account safe</span>
            </span>
        </footer>
    </div>

    <script>
        let remaining = 40;
        const el = document.getElementById('pin-countdown');

        const timer = setInterval(function () {
            remaining = remaining > 1 ? remaining - 1 : 0;
            if (el) el.textContent = remaining;
            if (remaining === 0) {
                clearInterval(timer);
                el.textContent = 'expired';
                el.classList.add('text-[var(--danger)]');
            }
        }, 1000);
    </script>
</body>
</html>
