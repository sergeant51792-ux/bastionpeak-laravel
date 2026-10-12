@props([
    'transaction',
    'variant' => 'desktop',
])

@php
    $isMobile = $variant === 'mobile';
    $isIncoming = $transaction->isIncoming();
    $amountPrefix = $isIncoming ? '+' : '−';
    $amountClass = $isIncoming ? 'text-[var(--success)]' : 'text-[var(--text)]';
    $iconName = $isIncoming ? 'arrow-down-left' : 'arrow-up-right';
    $iconClass = $isIncoming ? 'txn-icon txn-icon-in' : 'txn-icon txn-icon-out';

    $decimals = $transaction->currency->decimals ?? 2;
    $symbol = $transaction->currency->symbol ?? '';
    $title = $transaction->memo
        ?: ($transaction->type instanceof \App\Enums\TransactionType
            ? $transaction->type->customerLabel()
            : 'Transaction');
@endphp

@if($isMobile)
<a href="{{ route('activity') }}" class="flex items-center gap-3 p-4 hover:bg-[var(--line-soft)] transition-colors">
    <span class="{{ $iconClass }} w-10 h-10">
        <x-icon name="{{ $iconName }}" class="w-4 h-4" />
    </span>
    <div class="flex-1 min-w-0">
        <div class="flex items-center justify-between gap-2">
            <p class="text-sm font-semibold text-[var(--text)] truncate">{{ $title }}</p>
            <p class="text-sm font-semibold text-balance {{ $amountClass }}">{{ $amountPrefix }}{{ number_format((float) $transaction->amount, $decimals) }} {{ $symbol }}</p>
        </div>
        <div class="flex items-center justify-between gap-2 mt-1">
            <p class="text-xs text-[var(--text-muted)]">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
            <x-status-pill :status="$transaction->status" :label="$transaction->customerStatusLabel()" />
        </div>
    </div>
</a>
@else
<a href="{{ route('activity') }}" class="flex items-center gap-4 p-4 hover:bg-[var(--line-soft)] transition-colors">
    <span class="{{ $iconClass }} w-10 h-10">
        <x-icon name="{{ $iconName }}" class="w-4 h-4" />
    </span>
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2">
            <p class="text-sm font-semibold text-[var(--text)] truncate">{{ $title }}</p>
            <x-status-pill :status="$transaction->status" :label="$transaction->customerStatusLabel()" />
        </div>
        <p class="text-xs text-[var(--text-muted)] mt-0.5">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
    </div>
    <div class="text-right flex-shrink-0">
        <p class="text-sm font-semibold text-balance {{ $amountClass }}">{{ $amountPrefix }}{{ number_format((float) $transaction->amount, $decimals) }} {{ $symbol }}</p>
        <p class="text-xs text-[var(--text-muted)]">{{ $transaction->currency->code ?? '' }}</p>
    </div>
</a>
@endif
