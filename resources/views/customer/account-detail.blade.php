@extends('layouts.customer')

@section('title', $account->name)

@section('content')
<div class="space-y-6">
    <header class="page-header">
        <a href="{{ route('home') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--line-soft)] transition-colors" aria-label="Back">
            <x-icon name="arrow-left" class="w-5 h-5 text-[var(--text-muted)]" />
        </a>
        <div class="flex-1 min-w-0">
            <h1 class="text-xl font-bold text-[var(--text)] tracking-tight truncate">{{ $account->name }}</h1>
            <p class="text-sm text-[var(--text-muted)] mt-0.5 truncate">{{ $account?->currency?->code ?? '' }} â€¢ {{ $account->account_number ?: '' }}</p>
        </div>
        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $account->status === 'active' ? 'bg-[var(--success-bg)] text-[var(--success)]' : 'bg-[var(--danger-bg)] text-[var(--danger)]' }} flex-shrink-0">
            {{ ucfirst($account->status) }}
        </span>
    </header>

    <section class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl divide-y divide-[var(--line)]">
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
                            <div class="h-full bg-[var(--accent)] rounded-full transition-all" style="width: {{ $limit->amount > 0 ? min(100, ($limit->used_amount / $limit->amount) * 100) : 0 }}%"></div>
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
                        <span class="text-sm font-medium text-[var(--text)] font-mono">{{ $account->account_number ?: 'â€”' }}</span>
                        <button onclick="copyAccountNumber(this, '{{ $account->account_number }}')" class="text-[var(--text-muted)] hover:text-[var(--text)] transition-colors" aria-label="Copy">
                            <x-icon name="copy" class="w-4 h-4" />
                        </button>
                    </div>
                </div>
                <div class="flex items-center justify-between py-2.5">
                    <span class="text-sm text-[var(--text-muted)]">Currency</span>
                    <span class="text-sm font-medium text-[var(--text)]">{{ $account?->currency?->code ?? 'â€”' }}</span>
                </div>
                <div class="flex items-center justify-between py-2.5">
                    <span class="text-sm text-[var(--text-muted)]">Type</span>
                    <span class="text-sm font-medium text-[var(--text)]">{{ $account->type ?: 'â€”' }}</span>
                </div>
                <div class="flex items-center justify-between py-2.5">
                    <span class="text-sm text-[var(--text-muted)]">Opened</span>
                    <span class="text-sm font-medium text-[var(--text)]">{{ $account->opened_at?->format('d M Y') ?: $account->created_at->format('d M Y') }}</span>
                </div>
            </div>
        </div>
    </section>

    <div class="flex gap-3">
        <a href="{{ route('statements') }}" class="flex-1 py-3 btn-ghost text-center">Download statement</a>
        <a href="{{ route('messages') }}" class="flex-1 py-3 btn-ghost text-center">Message support</a>
    </div>
</div>

<script>
function copyAccountNumber(btn, number) {
    if (!number) return;
    navigator.clipboard.writeText(number).then(function() {
        btn.innerHTML = '<x-icon name="check" class="w-4 h-4 text-[var(--success)]" />';
        setTimeout(function() { btn.innerHTML = '<x-icon name="copy" class="w-4 h-4" />'; }, 2000);
    });
}
</script>
@endsection
