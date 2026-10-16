<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in — Bastion Peak</title>
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
        .auth-hero {
            background: linear-gradient(135deg, var(--card-navy) 0%, var(--card-blue) 50%, var(--card-sky) 100%);
            color: white;
            padding: 4rem 1.5rem 3rem;
        }
        .auth-hero::before {
            content: '';
            position: absolute;
            top: -30%;
            right: -20%;
            width: 20rem;
            height: 20rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }
        .auth-hero::after {
            content: '';
            position: absolute;
            bottom: -25%;
            left: -15%;
            width: 14rem;
            height: 14rem;
            background: rgba(255, 255, 255, 0.03);
            border-radius: 50%;
        }
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
                <a href="{{ route('register') }}" class="text-sm font-medium text-[var(--text-muted)] hover:text-[var(--text)] transition-colors px-4 py-2 rounded-lg hover:bg-[var(--surface-raised)]">
                    Create account
                </a>
            </div>
        </header>

        <div class="flex-1 flex items-center justify-center">
            <div class="w-full max-w-md">
                <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl shadow-xl p-8 mx-4">
                    <div class="text-center mb-8">
                        <h1 class="text-2xl font-bold text-[var(--text)] mb-2">Welcome back</h1>
                        <p class="text-sm text-[var(--text-muted)]">Sign in to your Bastion Peak account.</p>
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

                    <form method="POST" action="{{ route('signin.post') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="email" class="block text-sm font-medium text-[var(--text)] mb-2">Email address</label>
                            <input type="email" id="email" name="email" required autofocus autocomplete="email"
                                   value="{{ old('email') }}" class="input-field w-full" placeholder="you@company.com">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-[var(--text)] mb-2">Password</label>
                            <div class="relative">
                                <input type="password" id="password" name="password" required autocomplete="current-password"
                                       class="input-field w-full pr-12" placeholder="Enter your password">
                                <button type="button" id="toggle-password"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 text-[var(--text-muted)] hover:text-[var(--text)] transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center"
                                        aria-label="Toggle password visibility">
                                    <x-icon name="eye-off" class="w-5 h-5" id="icon-eye-off" />
                                    <x-icon name="eye" class="w-5 h-5 hidden" id="icon-eye" />
                                </button>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-[var(--line)] text-[var(--accent)] focus:ring-[var(--accent)]">
                                <span class="text-sm text-[var(--text-muted)]">Remember me</span>
                            </label>
                            <a href="{{ route('password.forgot') }}" class="text-sm font-medium text-[var(--accent)] hover:text-[var(--accent-dark)] transition-colors">
                                Forgot password?
                            </a>
                        </div>

                        <button type="submit" class="w-full py-3 btn-primary text-sm font-medium">
                            Sign in
                        </button>
                    </form>

                    <div class="mt-6 flex items-center gap-2 text-xs text-[var(--text-muted)]">
                        <x-icon name="shield" class="w-4 h-4 text-[var(--accent)]" />
                        <span>Secured with enterprise-grade encryption</span>
                    </div>
                </div>
            </div>
        </div>

        <footer class="pb-8 text-center text-xs text-[var(--text-muted)]">
            <span class="flex items-center justify-center gap-1">
                <span>Made for secure finance</span>
            </span>
        </footer>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('toggle-password');
        const input = document.getElementById('password');
        const iconOff = document.getElementById('icon-eye-off');
        const iconEye = document.getElementById('icon-eye');

        toggle.addEventListener('click', function () {
            const type = input.type === 'password' ? 'text' : 'password';
            input.type = type;
            iconOff.classList.toggle('hidden');
            iconEye.classList.toggle('hidden');
        });
    });
    </script>
</body>
</html>
