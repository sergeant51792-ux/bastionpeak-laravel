@extends('layouts.customer')

@section('title', 'Payments')

@section('content')
<div class="space-y-6" id="transfer-page">
    <header class="page-header">
        <a href="{{ route('home') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--line-soft)] transition-colors" aria-label="Back">
            <x-icon name="arrow-left" class="w-5 h-5 text-[var(--text-muted)]" />
        </a>
        <h1 class="text-xl font-bold text-[var(--text)] tracking-tight">Transfers</h1>
    </header>

    @if(request('status') === 'submitted')
        <div id="success-popup" class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm">
            <div class="bg-[var(--surface)] border border-[var(--line)] rounded-3xl shadow-xl w-full max-w-md mx-4 p-8 text-center animate-scale-in">
                <div class="w-24 h-24 mx-auto bg-[var(--success-bg)] rounded-full flex items-center justify-center mb-6 border-4 border-[var(--success)]/10">
                    <svg class="w-12 h-12 text-[var(--success)]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-width="2.5" stroke="currentColor" d="M5 13l4 4 7-7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-[var(--text)] mb-3">{{ __('Request submitted') }}</h2>
                <p class="text-[var(--text-muted)] mb-6">{{ __('Your transaction request has been sent for admin approval. Your balance will update once it is processed.') }}</p>
                <a href="{{ route('activity') }}" class="inline-flex items-center justify-center w-full px-6 py-3 bg-[var(--accent)] text-white rounded-xl text-sm font-semibold hover:opacity-90 transition-opacity">
                    <x-icon name="activity" class="w-4 h-4 mr-2" />
                    {{ __('View activity') }}
                </a>
                <button onclick="window.location.href='{{ route('home') }}'" class="mt-4 text-sm text-[var(--text-muted)] hover:text-[var(--text)] transition-colors">
                    ← {{ __('Back to dashboard') }}
                </button>
            </div>
        </div>
    @else
        <div class="space-y-3">
            <div class="border border-[var(--line)] rounded-xl p-4 cursor-pointer hover:border-[var(--accent)] hover:bg-[var(--line-soft)] transition-all" onclick="showTransferForm('crypto')">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)]">
                            <x-icon name="bitcoin" class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="font-semibold text-[var(--text)]">Cryptocurrency</p>
                            <p class="text-xs text-[var(--text-muted)]">USDT, BTC, ETH, and other crypto</p>
                        </div>
                    </div>
                    <x-icon name="chevron-right" class="w-5 h-5 text-[var(--text-muted)]" />
                </div>
            </div>

            <div class="border border-[var(--line)] rounded-xl p-4 cursor-pointer hover:border-[var(--accent)] hover:bg-[var(--line-soft)] transition-all" onclick="showTransferForm('wire')">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[var(--success-bg)] flex items-center justify-center text-[var(--success)]">
                            <x-icon name="landmark" class="w-5 h-5" />
                        </div>
                        <div>
                            <p class="font-semibold text-[var(--text)]">Bank transfers</p>
                            <p class="text-xs text-[var(--text-muted)]">ACH, wire, SWIFT, SEPA</p>
                        </div>
                    </div>
                    <x-icon name="chevron-right" class="w-5 h-5 text-[var(--text-muted)]" />
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    const transferUrls = {
        'crypto': '/pay?method=crypto',
        'wire': '/pay?method=wire',
    };

    function showTransferForm(method) {
        window.location.href = transferUrls[method] || '/pay';
    }
</script>
@endpush
@endsection
