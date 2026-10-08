@extends('layouts.customer')

@section('title', $account->name)
@section('content')
<div class="space-y-6">
    <header class="page-header">
        <a href="{{ route('home') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--line-soft)] transition-colors" aria-label="Back">
            <x-icon name="arrow-left" class="w-5 h-5 text-[var(--text-muted)]" />
                    {{ ucfirst($account->status) }}
                </span>
            </div>
        </div>
    </div>

    <section class="bg-white border border-[var(--line)] rounded-2xl divide-y divide-[var(--line)]">
        <div class="px-5 py-4">
            <h2 class="section-title mb-3">Limits</h2>
            <div class="space-y-4">
                @foreach($limits->take(3) as $limit)
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1.5">
                            <span class="font-medium text-[var(--text)] capitalize">{{ str_replace('_', ' ', $limit->type) }}</span>
                            <span class="font-semibold text-[var(--text)] text-balance">{{ number_format((float) $limit->amount, 2) }}</span>
                        </div>
                        <div class="h-1.5 bg-[var(--line-soft)] rounded-full overflow-hidden">
                            <div class="h-full bg-[var(--accent)] rounded-full transition-all" style="width: {{ min(100, ($limit->used_amount / $limit->amount) * 100) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="px-5 py-4">
            <h2 class="section-title mb-3">Account details</h2>
            <div class="space-y-0">
                <div class="flex items-center justify-between py-2.5">
                    <span class="text-sm text-[var(--text-muted)]">Account number</span>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-medium text-[var(--text)]">10 8388 2210</span>
                        <button class="text-[var(--text-muted)] hover:text-[var(--text)] transition-colors" aria-label="Copy">
                            <x-icon name="copy" class="w-4 h-4" />
                        </button>
                    </div>
                </div>
                <div class="flex items-center justify-between py-2.5">
                    <span class="text-sm text-[var(--text-muted)]">Currency</span>
                    <span class="text-sm font-medium text-[var(--text)]">{{ $account->currency->code }}</span>
                </div>
                <div class="flex items-center justify-between py-2.5">
                    <span class="text-sm text-[var(--text-muted)]">Opened</span>
                    <span class="text-sm font-medium text-[var(--text)]">{{ $account->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </section>

    <div class="flex gap-3">
        <button class="flex-1 py-3 btn-ghost">Download statement</button>
        <a href="{{ route('messages') }}" class="flex-1 py-3 btn-ghost text-center">Message support</a>
    </div>
</div>
@endsection
