<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in — Bastion Peak</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
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
    <div class="min-h-screen flex flex-col">
        <header class="absolute top-6 left-0 right-0 z-20">
            <div class="max-w-7xl mx-auto flex items-center justify-between px-6">
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
                <div class="bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl shadow-xl p-8 mx-4">
                    <div class="text-center mb-8">
                        <h1 class="text-2xl font-bold text-[var(--text)] mb-2">Welcome back</h1>
                        <p class="text-sm text-[var(--text-muted)]">Sign in to your Bastion Peak account.</p>
                    </div>

                    @if($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 font-medium">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    @if(session('status'))
                        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700 font-medium">
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
