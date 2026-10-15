@extends('layouts.customer')

@section('title', __('Verify it is you'))

@php
    $totalSeconds = $secondsRemaining ?? 30;
    $progress = max(0, min(100, ($totalSeconds / 30) * 100));
    $labels = [
        'transfer' => ['Confirm your transfer', 'Enter the code from your authenticator app to authorise this transfer.'],
        'password' => ['Confirm your password change', 'Enter the code from your authenticator app to change your password.'],
    ];
    $copy = $labels[$purpose] ?? ['Verify it is you', 'Enter the code from your authenticator app.'];
@endphp

@section('content')
<div class="min-h-[70vh] flex items-center justify-center px-3 py-10">
    <div class="w-full max-w-sm">
        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6 text-center">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] mb-5">
                <x-icon name="shield-check" class="w-8 h-8" />
            </div>

            <h1 class="text-lg font-semibold text-[var(--text)]">{{ __($copy[0]) }}</h1>
            <p class="text-sm text-[var(--text-muted)] mt-2 mb-6">{{ __($copy[1]) }}</p>

            <form method="POST" action="{{ route('two-factor.verify') }}" class="space-y-4">
                @csrf
                <div>
                    <input type="text" id="code" name="code" required inputmode="numeric" autocomplete="one-time-code"
                           maxlength="6" pattern="[0-9]{6}" autofocus
                           class="input-field font-mono text-2xl tracking-[0.5em] text-center py-3.5"
                           placeholder="000000" aria-label="6-digit code">
                    @error('code')
                        <p class="mt-2 text-sm text-[var(--danger)]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-xs text-[var(--text-muted)]">
                    <span>Code refreshes in</span>
                    <span id="otp-seconds" class="tabular-nums font-medium">{{ $totalSeconds }}s</span>
                </div>
                <div class="h-1.5 rounded-full bg-[var(--line)] overflow-hidden">
                    <div id="otp-bar" class="h-full rounded-full bg-[var(--accent)] transition-all duration-500" style="width: {{ $progress }}%"></div>
                </div>

                <button type="submit" class="w-full py-3.5 btn-primary text-sm font-semibold">
                    {{ __('Verify and continue') }}
                </button>
            </form>
        </div>

        <p class="text-xs text-[var(--text-muted)] text-center mt-4">
            Lost your authenticator? Contact support before continuing.
        </p>
    </div>
</div>

<script>
    (function () {
        var secondsEl = document.getElementById('otp-seconds');
        var barEl = document.getElementById('otp-bar');
        if (!secondsEl || !barEl) return;

        var remaining = {{ $totalSeconds }};
        setInterval(function () {
            remaining = remaining > 0 ? remaining - 1 : 30;
            secondsEl.textContent = remaining + 's';
            barEl.style.width = ((remaining / 30) * 100) + '%';
        }, 1000);
    })();
</script>
@endsection
