<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create account — Bastion Peak</title>
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
                    Sign in
                </a>
            </div>
        </header>

        <div class="flex-1 flex items-center justify-center">
            <div class="w-full max-w-2xl">
                <div class="bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl shadow-xl p-8 mx-4">
                    <div class="mb-8">
                        <h1 class="text-2xl font-bold text-[var(--text)] mb-2">Create your account</h1>
                        <p class="text-sm text-[var(--text-muted)]">Open your Bastion Peak account in minutes. Set up a 4-digit security PIN for account recovery.</p>
                    </div>

                    @if($errors->any())
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 font-medium">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register.post') }}" class="space-y-6">
                        @csrf

                        <div>
                            <h2 class="text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-3">Personal information</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-[var(--text)] mb-1.5">Full name</label>
                                    <input type="text" id="name" name="name" required value="{{ old('name') }}"
                                           class="input-field w-full" placeholder="John Doe">
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-[var(--text)] mb-1.5">Email address</label>
                                    <input type="email" id="email" name="email" required value="{{ old('email') }}"
                                           class="input-field w-full" placeholder="you@company.com">
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-[var(--text)] mb-1.5">Phone number</label>
                                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                                           class="input-field w-full" placeholder="+1 (555) 000-0000">
                                </div>
                                <div>
                                    <label for="country" class="block text-sm font-medium text-[var(--text)] mb-1.5">Country</label>
                                    <input type="text" id="country" name="country" value="{{ old('country') }}"
                                           class="input-field w-full" placeholder="United States">
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="address" class="block text-sm font-medium text-[var(--text)] mb-1.5">Address</label>
                                    <input type="text" id="address" name="address" value="{{ old('address') }}"
                                           class="input-field w-full" placeholder="123 Main Street">
                                </div>
                                <div>
                                    <label for="city" class="block text-sm font-medium text-[var(--text)] mb-1.5">City</label>
                                    <input type="text" id="city" name="city" value="{{ old('city') }}"
                                           class="input-field w-full" placeholder="New York">
                                </div>
                                <div>
                                    <label for="state" class="block text-sm font-medium text-[var(--text)] mb-1.5">State / Province</label>
                                    <input type="text" id="state" name="state" value="{{ old('state') }}"
                                           class="input-field w-full" placeholder="NY">
                                </div>
                                <div>
                                    <label for="postal_code" class="block text-sm font-medium text-[var(--text)] mb-1.5">Postal code</label>
                                    <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}"
                                           class="input-field w-full" placeholder="10001">
                                </div>
                            </div>
                        </div>

                        <div>
                            <h2 class="text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-3">Security</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="password" class="block text-sm font-medium text-[var(--text)] mb-1.5">Password</label>
                                    <div class="relative">
                                        <input type="password" id="password" name="password" required
                                               class="input-field w-full pr-12" placeholder="At least 8 characters">
                                        <button type="button" id="toggle-password"
                                                class="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 text-[var(--text-muted)] hover:text-[var(--text)] transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center"
                                                aria-label="Toggle password visibility">
                                            <x-icon name="eye-off" class="w-5 h-5" id="icon-password-off" />
                                            <x-icon name="eye" class="w-5 h-5 hidden" id="icon-password-on" />
                                        </button>
                                    </div>
                                    <p class="text-xs text-[var(--text-muted)] mt-1.5">Must be at least 8 characters.</p>
                                </div>
                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-[var(--text)] mb-1.5">Confirm password</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" required
                                           class="input-field w-full" placeholder="Re-enter password">
                                </div>
                            </div>
                        </div>

                        <div>
                            <h2 class="text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-3">Security PIN</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label for="pin" class="block text-sm font-medium text-[var(--text)] mb-1.5">4-digit PIN</label>
                                    <input type="text" id="pin" name="pin" required
                                           class="input-field w-full font-mono text-center text-2xl sm:text-3xl tracking-widest"
                                           placeholder="••••" maxlength="4" inputmode="numeric" pattern="\d{4}"
                                           oninput="this.value = this.value.replace(/\D/g, '').slice(0, 4)">
                                    <p class="text-xs text-[var(--text-muted)] mt-1.5">This PIN will be used to recover your password if forgotten. Choose something memorable but not easily guessed.</p>
                                </div>
                                <div>
                                    <label for="pin_confirmation" class="block text-sm font-medium text-[var(--text)] mb-1.5">Confirm PIN</label>
                                    <input type="text" id="pin_confirmation" name="pin_confirmation" required
                                           class="input-field w-full font-mono text-center text-2xl sm:text-3xl tracking-widest"
                                           placeholder="••••" maxlength="4" inputmode="numeric" pattern="\d{4}"
                                           oninput="this.value = this.value.replace(/\D/g, '').slice(0, 4)">
                                </div>
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full py-3 btn-primary text-sm font-medium">
                                Create account
                            </button>
                        </div>
                    </form>

                    <p class="mt-6 text-center text-xs text-[var(--text-muted)]">
                        By creating an account, you agree to Bastion Peak's Terms of Service and Privacy Policy.
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
        const toggle = document.getElementById('toggle-password');
        const input = document.getElementById('password');
        const iconOff = document.getElementById('icon-password-off');
        const iconOn = document.getElementById('icon-password-on');

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
