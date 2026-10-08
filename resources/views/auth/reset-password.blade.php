<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset password — Bastion Peak</title>
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
                        <div class="w-16 h-16 bg-[var(--accent-subtle)] rounded-full flex items-center justify-center mx-auto mb-4">
                            <x-icon name="key" class="w-7 h-7 text-[var(--accent)]" />
                        </div>
                        <h1 class="text-2xl font-bold text-[var(--text)] mb-2">Create new password</h1>
                        <p class="text-sm text-[var(--text-muted)]">Set a new password and security PIN for your account.</p>
                    </div>

                    @if($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 font-medium">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.reset.post') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="password" class="block text-sm font-medium text-[var(--text)] mb-2">New password</label>
                            <div class="relative">
                                <input type="password" id="password" name="password" required minlength="8"
                                       class="input-field w-full pr-12" placeholder="Enter new password">
                                <button type="button" id="toggle-password"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 text-[var(--text-muted)] hover:text-[var(--text)] transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center"
                                        aria-label="Toggle password visibility">
                                    <x-icon name="eye-off" class="w-5 h-5" id="icon-pw-off" />
                                    <x-icon name="eye" class="w-5 h-5 hidden" id="icon-pw-on" />
                                </button>
                            </div>
                            <p class="text-xs text-[var(--text-muted)] mt-1.5">At least 8 characters with letters, numbers, and symbols.</p>
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-[var(--text)] mb-2">Confirm new password</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8"
                                   class="input-field w-full" placeholder="Re-enter password">
                        </div>

                        <div>
                            <label for="pin" class="block text-sm font-medium text-[var(--text)] mb-2">New 4-digit PIN</label>
                            <input type="text" id="pin" name="pin" required maxlength="4" inputmode="numeric" pattern="\d{4}"
                                   class="input-field w-full text-center text-2xl font-mono tracking-widest"
                                   placeholder="• • • •"
                                   oninput="this.value = this.value.replace(/\D/g, '').slice(0, 4)">
                        </div>

                        <div>
                            <label for="pin_confirmation" class="block text-sm font-medium text-[var(--text)] mb-2">Confirm PIN</label>
                            <input type="text" id="pin_confirmation" name="pin_confirmation" required maxlength="4" inputmode="numeric" pattern="\d{4}"
                                   class="input-field w-full text-center text-2xl font-mono tracking-widest"
                                   placeholder="• • • •"
                                   oninput="this.value = this.value.replace(/\D/g, '').slice(0, 4)">
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full py-3 btn-primary text-sm font-medium">
                                Save new password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <footer class="pb-8 text-center text-xs text-[var(--text-muted)]">
            <span>&copy; {{ date('Y') }} Bastion Peak. All rights reserved.</span>
        </footer>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.getElementById('toggle-password');
        const input = document.getElementById('password');
        const iconOff = document.getElementById('icon-pw-off');
        const iconOn = document.getElementById('icon-pw-on');

        if (toggle) {
            toggle.addEventListener('click', function () {
                const type = input.type === 'password' ? 'text' : 'password';
                input.type = type;
                iconOff.classList.toggle('hidden');
                iconOn.classList.toggle('hidden');
            });
        }
    });
    </script>
</body>
</html>
