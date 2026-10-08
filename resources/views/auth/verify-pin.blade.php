<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify security PIN — Bastion Peak</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .pin-input {
            width: 3.5rem;
            height: 3.5rem;
            font-size: 1.5rem;
            text-align: center;
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
                <a href="{{ route('signin') }}" class="text-sm font-medium text-[var(--text-muted)] hover:text-[var(--text)] transition-colors px-4 py-2 rounded-lg hover:bg-[var(--surface-raised)]">
                    Back to sign in
                </a>
            </div>
        </header>

        <div class="flex-1 flex items-center justify-center">
            <div class="w-full max-w-md">
                <div class="bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl shadow-xl p-8 mx-4">
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-[var(--accent-subtle)] rounded-full flex items-center justify-center mx-auto mb-4">
                            <x-icon name="shield-check" class="w-7 h-7 text-[var(--accent)]" />
                        </div>
                        <h1 class="text-2xl font-bold text-[var(--text)] mb-2">Verify your PIN</h1>
                        <p class="text-sm text-[var(--text-muted)]">Enter your 4-digit security PIN to verify your identity.</p>
                    </div>

                    @if($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 font-medium">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.verify.pin.post') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="pin" class="block text-sm font-medium text-[var(--text)] mb-2">Security PIN</label>
                            <input type="text" id="pin" name="pin" required maxlength="4" inputmode="numeric" pattern="\d{4}"
                                   autocomplete="one-time-code"
                                   class="pin-input mx-auto input-field font-mono text-2xl tracking-widest focus:text-center"
                                   oninput="this.value = this.value.replace(/\D/g, '').slice(0, 4)">
                        </div>

                        <button type="submit" class="w-full py-3 btn-primary text-sm font-medium">
                            Verify PIN
                        </button>
                    </form>

                    <p class="mt-6 text-center text-sm text-[var(--text-muted)]">
                        <a href="{{ route('password.forgot') }}" class="text-[var(--accent)] hover:text-[var(--accent-dark)] font-medium transition-colors">
                            Use a different email
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <footer class="pb-8 text-center text-xs text-[var(--text-muted)]">
            <span>&copy; {{ date('Y') }} Bastion Peak. All rights reserved.</span>
        </footer>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const pinInput = document.getElementById('pin');
        pinInput.focus();

        pinInput.addEventListener('input', function () {
            if (this.value.length === 4) {
                const form = this.closest('form');
                setTimeout(() => form.submit(), 200);
            }
        });
    });
    </script>
</body>
</html>
