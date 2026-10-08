@extends('layouts.admin')

@section('title', 'Add Deposit Method')
@section('content')
<div class="space-y-6 max-w-2xl">
    <header class="page-header">
        <a href="{{ route('admin.deposit-methods.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--line-soft)] transition-colors" aria-label="Back">
            <x-icon name="arrow-left" class="w-5 h-5 text-[var(--text-muted)]" />
        </a>
        <h1 class="text-xl font-semibold">Add Deposit Method</h1>
    </header>

    <form method="POST" action="{{ route('admin.deposit-methods.store') }}" class="space-y-5">
        @csrf
        <div>
            <label for="code" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Code</label>
            <input type="text" id="code" name="code" required maxlength="50" class="input-field" placeholder="e.g. wire_usd, crypto_usdt">
            <p class="text-xs text-[var(--text-muted)] mt-1">Unique identifier used in the system.</p>
        </div>

        <div>
            <label for="name" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Display Name</label>
            <input type="text" id="name" name="name" required maxlength="255" class="input-field" placeholder="e.g. Wire Transfer (USD)">
        </div>

        <div>
            <label for="type" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Type</label>
            <x-custom-select
                name="type"
                :options="[
                    'wire' => 'Wire transfer',
                    'crypto' => 'Cryptocurrency',
                    'swift' => 'SWIFT',
                    'sepa' => 'SEPA',
                    'ach' => 'ACH (US)',
                    'zelle' => 'Zelle (US)',
                    'faster_payments' => 'Faster Payments (UK)',
                    'bacs' => 'BACS (UK)',
                    'chaps' => 'CHAPS (UK)',
                    'other' => 'Other',
                ]"
                placeholder="Select type"
                required
            />
        </div>

        <div>
            <label for="icon" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Icon</label>
            <input type="text" id="icon" name="icon" maxlength="255" class="input-field" placeholder="SVG path or icon name (optional)">
        </div>

        <div>
            <label for="description" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Description</label>
            <textarea id="description" name="description" rows="3" maxlength="2000" class="input-field" placeholder="Brief description shown to users"></textarea>
        </div>

        <div class="flex items-center gap-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded border-[var(--line)] text-[var(--primary)] focus:ring-[var(--accent)]">
                <span class="text-sm font-medium text-[var(--text)]">Active</span>
            </label>
            <div>
                <label for="sort_order" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Sort Order</label>
                <input type="number" id="sort_order" name="sort_order" value="0" min="0" class="input-field w-24">
            </div>
        </div>

        @if($errors->any())
            <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="flex-1 py-3.5 btn-primary">Create deposit method</button>
            <a href="{{ route('admin.deposit-methods.index') }}" class="px-6 py-3.5 bg-white border border-[var(--line)] rounded-xl text-sm font-semibold text-[var(--text-muted)] hover:text-[var(--text)] transition-colors">Cancel</a>
        </div>
    </form>
</div>
@endsection
