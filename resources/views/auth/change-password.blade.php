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
                <h1 class="text-2xl font-semibold tracking-tight">BASTION PEAK</h1>
                <p class="text-[var(--text-muted)] text-sm mt-1">Change your password</p>
            </div>

            <form method="POST" action="{{ route('password.change') }}" class="space-y-5">
                @csrf

                @if(session('status'))
                    <div class="p-3 bg-green-900/30 border border-green-700 rounded-lg text-sm text-green-300">
                        {{ session('status') }}
                    </div>
                @endif

                <div>
                    <label for="current_password" class="block text-sm font-medium mb-1.5">Current password</label>
                    <input type="password" id="current_password" name="current_password" required
                           class="w-full px-4 py-3 bg-[var(--surface)] border border-[var(--line)] rounded-lg text-[var(--text)] focus:outline-none focus:ring-2 focus:ring-[var(--focus)]">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium mb-1.5">New password</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-4 py-3 bg-[var(--surface)] border border-[var(--line)] rounded-lg text-[var(--text)] focus:outline-none focus:ring-2 focus:ring-[var(--focus)]">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium mb-1.5">Confirm new password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full px-4 py-3 bg-[var(--surface)] border border-[var(--line)] rounded-lg text-[var(--text)] focus:outline-none focus:ring-2 focus:ring-[var(--focus)]">
                </div>

                <button type="submit" class="w-full py-3 bg-[var(--text)] text-[var(--bg)] rounded-lg font-medium hover:opacity-90 transition-opacity">
                    Update password
                </button>
            </form>
        </div>
    </div>
</body>
</html>
