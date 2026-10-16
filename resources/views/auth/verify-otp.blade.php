<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify code — Bastion Peak</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/css/utilities.css', 'resources/js/app.js'])
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
<script>
        (function() {
            var dbTheme = localStorage.getItem("theme") || "dark";
            var prefersDark = window.matchMedia("(prefers-color-scheme: dark)").matches;
            if (dbTheme === "dark" || (dbTheme === "system" && prefersDark)) {
                document.body.classList.add("dark");
                document.body.setAttribute("data-theme", "dark");
            } else if (dbTheme === "light") {
                document.body.classList.remove("dark");
                document.body.setAttribute("data-theme", "light");
            } else {
                document.body.classList.add("dark");
                document.body.setAttribute("data-theme", "dark");
            }
        })();
    </script>
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
                            <x-icon name="shield-check" class="w-7 h-7" />
                        </div>
                        <h1 class="text-2xl font-bold text-[var(--text)] mb-2">Enter your code</h1>
                        <p class="text-sm text-[var(--text-muted)]">
                            Open your authenticator app and enter the 6-digit code for
                            <span class="font-medium text-[var(--text)]">{{ $email }}</span>.
                        </p>
                    </div>

                    @if($errors->any())
                        <div class="mb-6 p-4 bg-[var(--danger-bg)] border border-[var(--danger)]/30 rounded-xl text-sm text-[var(--danger)] font-medium">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('signin.otp.post') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="code" class="block text-sm font-medium text-[var(--text)] mb-2">Verification code</label>
                            <input type="text" id="code" name="code" required autofocus inputmode="numeric"
                                   autocomplete="one-time-code" maxlength="6" pattern="\d{6}"
                                   class="input-field w-full font-mono text-center text-2xl tracking-[0.5em]"
                                   placeholder="000000"
                                   oninput="this.value = this.value.replace(/\D/g, '').slice(0, 6)">
                            <p class="text-xs text-[var(--text-muted)] mt-2 flex items-center gap-1">
                                <x-icon name="clock" class="w-3.5 h-3.5" />
                                <span>Codes refresh every 30 seconds. <span id="otp-countdown">{{ $secondsRemaining }}</span>s remaining.</span>
                            </p>
                        </div>

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
                <x-icon name="lock" class="w-3.5 h-3.5" />
                <span>Two-step verification keeps your account safe</span>
            </span>
        </footer>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        let remaining = {{ (int) $secondsRemaining }};
        const el = document.getElementById('otp-countdown');

        setInterval(function () {
            remaining = remaining > 1 ? remaining - 1 : 30;
            if (el) el.textContent = remaining;
        }, 1000);
    });
    </script>
</body>
</html>
