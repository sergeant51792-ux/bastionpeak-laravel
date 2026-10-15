@extends('layouts.customer')

@section('title', __('Authenticator'))

@php
    // Group the secret in fours so it is easy to read and to type by hand.
    $grouped = trim(chunk_split($secret, 4, ' '));
    $totalSeconds = $secondsRemaining ?? 30;
    $progress = max(0, min(100, ($totalSeconds / 30) * 100));
@endphp

@section('content')
<div class="px-3 sm:px-4 lg:px-6 pt-4 pb-10 space-y-6 max-w-2xl mx-auto">
    <header class="page-header">
        <a href="{{ route('profile') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--line-soft)] transition-colors" aria-label="Back">
            <x-icon name="arrow-left" class="w-5 h-5 text-[var(--text-muted)]" />
        </a>
        <h1 class="text-xl font-semibold">{{ __('Google Authenticator') }}</h1>
    </header>

    @if(session('status'))
        <div class="flex items-center gap-3 p-4 bg-[var(--success-bg)] border border-[var(--success)]/30 rounded-xl text-sm font-medium text-[var(--success)]" role="status">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-width="2.5" stroke="currentColor" d="M5 13l4 4 7-7"></path>
            </svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if(! $enabled)
        {{-- Enrolment --}}
        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-5 space-y-5">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] shrink-0">
                    <x-icon name="shield-check" class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-[var(--text)]">{{ __('Add your authenticator') }}</h2>
                    <p class="text-xs text-[var(--text-muted)] mt-0.5">One authenticator protects sign in, transfers and password changes.</p>
                </div>
            </div>

            <ol class="space-y-4">
                <li class="flex gap-3">
                    <span class="w-6 h-6 rounded-full bg-[var(--accent-subtle)] text-[var(--accent)] text-xs font-semibold flex items-center justify-center shrink-0">1</span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-[var(--text)]">Install Google Authenticator on your phone, then choose <strong>Add an account</strong> and select <strong>Enter a setup key</strong>.</p>
                        <a href="{{ $provisioningUri }}" class="mt-2 inline-flex items-center gap-1.5 text-sm font-medium text-[var(--accent)] hover:opacity-80 transition-opacity">
                            <x-icon name="smartphone" class="w-4 h-4" />
                            Open in authenticator app
                        </a>
                    </div>
                </li>
                <li class="flex gap-3">
                    <span class="w-6 h-6 rounded-full bg-[var(--accent-subtle)] text-[var(--accent)] text-xs font-semibold flex items-center justify-center shrink-0">2</span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-[var(--text)] mb-2">Enter this setup key.</p>
                        <p class="font-mono text-sm text-[var(--text)] bg-[var(--surface-raised)] border border-[var(--line)] rounded-xl p-3 break-all" id="totp-secret">{{ $grouped }}</p>
                        <button type="button" onclick="copySecret()" class="mt-2 inline-flex items-center gap-1.5 text-sm font-medium text-[var(--accent)] hover:opacity-80 transition-opacity">
                            <x-icon name="copy" class="w-4 h-4" />
                            Copy key
                        </button>
                    </div>
                </li>
                <li class="flex gap-3">
                    <span class="w-6 h-6 rounded-full bg-[var(--accent-subtle)] text-[var(--accent)] text-xs font-semibold flex items-center justify-center shrink-0">3</span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-[var(--text)]">Enter the 6-digit code the app now shows to finish setup.</p>
                    </div>
                </li>
            </ol>

            <div class="pt-4 border-t border-[var(--line)]">
                <div class="flex items-center justify-between text-xs text-[var(--text-muted)] mb-1.5">
                    <span>{{ __('Current code') }}</span>
                    <span id="totp-seconds" class="tabular-nums">{{ $totalSeconds }}s</span>
                </div>
                <div class="h-1.5 rounded-full bg-[var(--line)] overflow-hidden">
                    <div id="totp-bar" class="h-full rounded-full bg-[var(--accent)] transition-all duration-500" style="width: {{ $progress }}%"></div>
                </div>
            </div>

            <form method="POST" action="{{ route('two-factor.enable') }}" class="space-y-3">
                @csrf
                @error('code')
                    <p class="text-sm text-[var(--danger)]">{{ $message }}</p>
                @enderror
                <div>
                    <label for="code" class="block text-sm font-semibold text-[var(--text)] mb-1.5">6-digit code</label>
                    <input type="text" id="code" name="code" required inputmode="numeric" autocomplete="one-time-code"
                           maxlength="6" pattern="[0-9]{6}"
                           class="input-field font-mono text-lg tracking-[0.4em]" placeholder="000000">
                </div>
                <button type="submit" class="w-full py-3 btn-primary text-sm font-semibold">
                    Enable authenticator
                </button>
            </form>
        </div>
    @else
        {{-- Authenticator on --}}
        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-5 space-y-4">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-[var(--success-bg)] flex items-center justify-center text-[var(--success)] shrink-0">
                    <x-icon name="shield-check" class="w-5 h-5" />
                </div>
                <div>
                    <h2 class="text-sm font-semibold text-[var(--text)]">Authenticator is on</h2>
                    <p class="text-xs text-[var(--text-muted)] mt-0.5">Required when signing in, sending a transfer and changing your password.</p>
                </div>
            </div>

            <div class="bg-[var(--surface-raised)] border border-[var(--line)] rounded-xl p-4">
                <p class="text-xs text-[var(--text-muted)] mb-1.5">Setup key</p>
                <p class="font-mono text-sm text-[var(--text)] break-all">{{ $grouped }}</p>
            </div>

            <form method="POST" action="{{ route('two-factor.disable') }}" class="space-y-2"
                  onsubmit="return confirm('Turn off the authenticator? You will be asked for a code to sign in, send transfers and change your password no longer.');">
                @csrf
                @method('DELETE')
                @error('code')
                    <p class="text-sm text-[var(--danger)]">{{ $message }}</p>
                @enderror
                <div>
                    <label for="disable-code" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Confirm with your current code</label>
                    <input type="text" id="disable-code" name="code" required inputmode="numeric" autocomplete="one-time-code"
                           maxlength="6" pattern="[0-9]{6}"
                           class="input-field font-mono text-lg tracking-[0.4em]" placeholder="000000">
                </div>
                <button type="submit" class="w-full py-3 border border-[var(--danger)]/40 text-[var(--danger)] rounded-xl text-sm font-semibold hover:opacity-90 transition-opacity">
                    Turn off authenticator
                </button>
            </form>
        </div>
    @endif
</div>

<script>
    function copySecret() {
        var text = document.getElementById('totp-secret').textContent.trim();
        navigator.clipboard.writeText(text).then(function () {
            var btn = event.currentTarget;
            var original = btn.innerHTML;
            btn.innerHTML = 'Copied';
            setTimeout(function () { btn.innerHTML = original; }, 1500);
        });
    }

    @if(! $enabled)
    (function () {
        var secondsEl = document.getElementById('totp-seconds');
        var barEl = document.getElementById('totp-bar');
        if (!secondsEl || !barEl) return;

        var remaining = {{ $totalSeconds }};
        setInterval(function () {
            remaining = remaining > 0 ? remaining - 1 : 30;
            secondsEl.textContent = remaining + 's';
            barEl.style.width = ((remaining / 30) * 100) + '%';
        }, 1000);
    })();
    @endif
</script>
@endsection
