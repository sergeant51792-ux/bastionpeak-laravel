@extends('layouts.customer')

@section('title', 'Deposit')
@section('content')
<div class="space-y-6">
    <header class="page-header">
        <a href="{{ route('transfer') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--line-soft)] transition-colors" aria-label="Back">
            <x-icon name="arrow-left" class="w-5 h-5 text-[var(--text-muted)]" />
        </a>
        <h1 class="text-xl font-semibold">Deposit</h1>
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

    <form method="POST" action="{{ route('top-up.submit') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        <input type="hidden" name="idempotency" value="{{ $idempotency }}">

        <div>
            <label for="account_id" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Credit account</label>
            <x-custom-select
                name="account_id"
                :options="$accounts->mapWithKeys(fn($a) => [$a->id => $a->name . ' (' . $a->currency->symbol . ')' ])"
                placeholder="Select account"
                required
            />
        </div>

        <div>
            <label for="amount" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Amount</label>
            <div class="input-symbol-prefix">
                <span class="symbol">$</span>
                <input type="text" inputmode="decimal" id="amount" name="amount" required
                       class="input-field pr-4 py-3"
                       placeholder="0.00">
            </div>
        </div>

        <div>
            <label for="payment_method" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Settlement method</label>
            <x-custom-select
                name="payment_method"
                :options="[
                    'wire' => 'Wire transfer',
                    'card' => 'Card',
                    'crypto' => 'Crypto',
                    'other' => 'Other',
                ]"
                placeholder="Select method"
                required
            />
        </div>

        <div>
            <label for="note" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Reference <span class="text-[var(--text-muted)] font-normal">(optional)</span></label>
            <input type="text" id="note" name="note" maxlength="500"
                   class="input-field"
                   placeholder="Payment reference or note">
        </div>

        <div>
            <p class="block text-sm font-semibold text-[var(--text)] mb-2">Payment confirmation</p>
            <div class="border-2 border-dashed border-[var(--line)] rounded-xl p-6 text-center hover:border-[var(--accent)] transition-colors">
                <x-icon name="file-text" class="w-8 h-8 mx-auto mb-2 text-[var(--text-muted)]" />
                <p class="text-sm text-[var(--text-muted)]">Take photo or Choose file</p>
                <p class="text-xs text-[var(--text-muted)] mt-1">JPG, PNG or PDF, up to 5 MB</p>
                <input type="file" name="proof" accept="image/jpeg,image/jpg,image/png,application/pdf,.jpg,.jpeg,.png,.pdf" class="mt-3 text-sm">
            </div>
        </div>

        @if($errors->any())
            <div class="p-3 bg-[var(--danger-bg)] border border-[var(--danger)]/30 rounded-xl text-sm text-[var(--danger)]">
                {{ $errors->first() }}
            </div>
        @endif

        <button type="submit" class="w-full py-3.5 btn-primary">Review request</button>
    </form>
</div>
    @endif
@endsection
