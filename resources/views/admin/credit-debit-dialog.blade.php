@extends('layouts.admin')

@section('title', 'Manual credit / debit')
@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.accounts.show', $account) }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--surface-raised)]">
            <x-icon name="arrow-left" class="w-5 h-5" />
        </a>
        <div>
            <h1 class="text-xl font-semibold">Debit account</h1>
            <p class="text-sm text-[var(--text-muted)]">{{ $account->name }}, {{ $account->user->name }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.accounts.debit', $account) }}" class="space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-medium mb-2">Type</label>
            <div class="flex gap-3">
                <label class="flex items-center gap-2 p-3 bg-[var(--surface)] border border-[var(--line)] rounded-xl flex-1 cursor-pointer">
                    <input type="radio" name="type" value="fee" checked class="w-4 h-4">
                    <span class="text-sm">Fee</span>
                </label>
                <label class="flex items-center gap-2 p-3 bg-[var(--surface)] border border-[var(--line)] rounded-xl flex-1 cursor-pointer">
                    <input type="radio" name="type" value="adjustment" class="w-4 h-4">
                    <span class="text-sm">Adjustment</span>
                </label>
                <label class="flex items-center gap-2 p-3 bg-[var(--surface)] border border-[var(--line)] rounded-xl flex-1 cursor-pointer">
                    <input type="radio" name="type" value="settlement" class="w-4 h-4">
                    <span class="text-sm">Settlement</span>
                </label>
            </div>
        </div>

        <div>
            <label for="amount" class="block text-sm font-medium mb-1.5">Amount</label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--text-muted)]">{{ $account->currency->symbol }}</span>
                <input type="text" inputmode="decimal" id="amount" name="amount" required class="w-full pl-8 pr-4 py-3 bg-[var(--surface)] border border-[var(--line)] rounded-xl" placeholder="0.00">
            </div>
        </div>

        <div>
            <label for="reason" class="block text-sm font-medium mb-1.5">Reason</label>
            <input type="text" id="reason" name="reason" required maxlength="500" class="w-full px-4 py-3 bg-[var(--surface)] border border-[var(--line)] rounded-xl" placeholder="Required">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1.5">Attach <span class="text-[var(--text-muted)]">(optional)</span></label>
            <input type="file" class="text-sm">
        </div>

        <div class="p-4 bg-[var(--surface-raised)] rounded-xl space-y-2">
            <div class="flex items-center justify-between text-sm">
                <span class="text-[var(--text-muted)]">Balance now</span>
                <span style="font-variant-numeric: tabular-nums;">{{ number_format((float) $account->balance, $account->currency->decimals ?? 2) }} {{ $account->currency->symbol }}</span>
            </div>
            <div class="flex items-center justify-between text-sm">
                <span class="text-[var(--text-muted)]">Balance after</span>
                <span id="balance-after" style="font-variant-numeric: tabular-nums;">{{ number_format((float) $account->balance, $account->currency->decimals ?? 2) }} {{ $account->currency->symbol }}</span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.accounts.show', $account) }}" class="flex-1 py-3 border border-[var(--line)] rounded-xl text-sm font-medium text-center hover:bg-[var(--surface-raised)]">Cancel</a>
            <button type="submit" class="flex-1 py-3 bg-red-600 text-white rounded-xl text-sm font-medium hover:bg-red-700 transition-colors">Debit</button>
        </div>
    </form>
</div>
@endsection
