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
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--text-muted)] text-sm">$</span>
                <input type="text" inputmode="decimal" id="amount" name="amount" required
                       class="input-field pl-8 pr-4 py-3"
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
            <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <button type="submit" class="w-full py-3.5 btn-primary">Review request</button>
    </form>
</div>
@endsection
