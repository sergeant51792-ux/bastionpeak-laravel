<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot password — Bastion Peak</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
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
            <div class="max-w-7xl mx-auto flex items-center justify-between px-6">
                <a href="{{ route('landing') }}">
                    <x-bp-logo :width="40" :height="40" />
                </a>
                <a href="{{ route('signin') }}" class="text-sm font-medium text-[var(--text-muted)] hover:text-[var(--text)] transition-colors px-4 py-2 rounded-lg hover:bg-[var(--surface-raised)]">
                    Back to sign in
                </a>
            </div>
        </header>

        <div class="flex-1 flex items-center justify-center">
            <div class="w-full max-w-md">
                <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl shadow-xl p-8 mx-4">
                    <div class="text-center mb-8">
                        <x-icon name="lock" class="w-10 h-10 mx-auto mb-3 text-[var(--accent)]" />
                        <h1 class="text-2xl font-bold text-[var(--text)] mb-2">Forgot password?</h1>
                        <p class="text-sm text-[var(--text-muted)]">Enter your email address to begin the recovery process. You will be asked to verify your 4-digit security PIN.</p>
                    </div>

                    @if($errors->any())
                        <div class="mb-6 p-4 bg-[var(--danger-bg)] border border-[var(--danger)]/30 rounded-xl text-sm text-[var(--danger)] font-medium">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    @if(session('status'))
                        <div class="mb-6 p-4 bg-[var(--success-bg)] border border-[var(--success)]/30 rounded-xl text-sm text-[var(--success)] font-medium">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.forgot.post') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="email" class="block text-sm font-medium text-[var(--text)] mb-2">Email address</label>
                            <input type="email" id="email" name="email" required autofocus autocomplete="email"
                                   value="{{ old('email') }}" class="input-field w-full" placeholder="you@company.com">
                        </div>

                        <button type="submit" class="w-full py-3 btn-primary text-sm font-medium">
                            Continue
                        </button>
                    </form>

                    <div class="mt-6 flex items-center gap-2 text-xs text-[var(--text-muted)] justify-center">
                        <x-icon name="shield" class="w-4 h-4 text-[var(--accent)]" />
                        <span>Your identity is verified using your 4-digit security PIN</span>
                    </div>
                </div>
            </div>
        </div>

        <footer class="pb-8 text-center text-xs text-[var(--text-muted)]">
            <span>&copy; 1998–2026 Bastion Peak. All rights reserved.</span>
        </footer>
    </div>
</body>
</html>
