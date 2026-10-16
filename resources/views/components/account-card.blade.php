@props([
    'account',
    'size' => 'full',
    'theme' => 'default',
])

@php
    $isCompact = $size === 'compact';
    $statusColor = match($account->status) {
        'frozen' => 'bg-[var(--accent)]/20 text-[var(--accent-light)] border-[var(--accent)]/30',
        'locked' => 'bg-[var(--warning)]/20 text-[var(--warning)] border-[var(--warning)]/30',
        'closed' => 'bg-[var(--danger)]/20 text-[var(--danger)] border-[var(--danger)]/30',
        default => 'bg-[var(--success)]/20 text-[var(--success)] border-[var(--success)]/30',
    };

    $typeIcons = [
        'main' => 'landmark',
        'sub' => 'wallet',
    ];
@endphp

<div class="rounded-2xl border border-[var(--line)] bg-[var(--surface)] p-{{ $isCompact ? '4' : '6' }} shadow-lg transition-all hover:shadow-md">
    <div class="flex items-center justify-between mb-{{ $isCompact ? '2' : '4' }}">
        <div class="flex items-center gap-2">
            <x-icon name="{{ $typeIcons[$account->type] ?? $typeIcons['main'] }}" class="h-5 w-5 text-[var(--text-muted)]" />
            <span class="text-sm font-medium text-[var(--text-muted)]">{{ $account->name }}</span>
        </div>
        <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-medium {{ $statusColor }}">
            {{ ucfirst($account->status) }}
        </span>
    </div>
    <div class="flex items-end justify-between">
        <div>
            <p class="text-xs text-[var(--text-muted)] mb-1">Balance</p>
            <p class="text-xl font-bold text-[var(--text)] text-balance">{{ $account->formatted_balance }}</p>
        </div>
        @if(!$isCompact)
            <p class="text-xs text-[var(--text-muted)] font-mono">****{{ substr($account->account_number, -4) }}</p>
        @endif
    </div>
    @if(!$isCompact && $account->currency && in_array(strtolower($account?->currency?->code), ['btc', 'eth']))
        <div class="mt-3 flex items-center gap-1 text-xs text-[var(--text-muted)]">
            <x-icon name="external-link"  class="h-3 w-3" />
             <span>Rate: 1 {{ $account?->currency?->symbol }} = {{ number_format((float) $account?->currency?->exchange_rate, 2) }} USD</span>
        </div>
    @endif
</div>

