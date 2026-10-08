<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot password — Bastion Peak</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[var(--bg)] text-[var(--text)]">
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
                <div class="bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl shadow-xl p-8 mx-4">
                    <div class="text-center mb-8">
                        <x-icon name="lock" class="w-10 h-10 mx-auto mb-3 text-[var(--accent)]" />
                        <h1 class="text-2xl font-bold text-[var(--text)] mb-2">Forgot password?</h1>
                        <p class="text-sm text-[var(--text-muted)]">Enter your email address to begin the recovery process. You will be asked to verify your 4-digit security PIN.</p>
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
            <span>&copy; {{ date('Y') }} Bastion Peak. All rights reserved.</span>
        </footer>
    </div>
</body>
</html>
