<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Change password — Bastion Peak</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[var(--bg)] text-[var(--text)]">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="w-full max-w-sm">
            <div class="text-center mb-8">
                <x-bp-logo :width="56" :height="56" class="mx-auto mb-4" />
                <h1 class="text-2xl font-bold">BASTION PEAK</h1>
                <p class="text-sm text-[var(--text-muted)] mt-1">Set your password</p>
            </div>

            <div class="card p-6 space-y-5">
                <form method="POST" action="{{ route('signin.first.post') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">

                    <div>
                        <label for="current_password" class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Current password</label>
                        <input type="password" id="current_password" name="current_password" required
                               class="input-field" placeholder="••••••••">
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">New password</label>
                        <input type="password" id="password" name="password" required
                               class="input-field" placeholder="••••••••">
                        <ul class="mt-2 space-y-1 text-xs text-[var(--text-muted)]">
                            @foreach($policy as $rule)
                                <li id="policy-{{ $rule[0] }}">{{ $rule[1] }} {{ $rule[2] }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Confirm new password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                               class="input-field" placeholder="••••••••">
                    </div>

                    <button type="submit" class="w-full btn-primary">Change password</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
