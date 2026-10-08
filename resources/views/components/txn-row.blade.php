@props([
    'transaction',
    'variant' => 'desktop',
])

@php
    $isMobile = $variant === 'mobile';
    $isIncoming = optional($transaction->toAccount)->user_id === auth()->id();
    $amountPrefix = $isIncoming ? '+' : '-';
    $amountClass = $isIncoming ? 'text-emerald-400' : 'text-slate-300';
    $iconName = $isIncoming ? 'arrow-up-right' : 'arrow-down-left';
    $iconClass = 'h-5 w-5';
@endphp

@if($isMobile)
<div class="flex items-center gap-3 p-4 bg-slate-900/50 rounded-xl border border-slate-800/50">
    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400">
        <x-icon name="{{ $iconName }}" class="{{ $iconClass }}" />
    </div>
    <div class="flex-1 min-w-0">
        <div class="flex items-center justify-between">
            <p class="text-sm font-medium text-slate-200 truncate">{{ $transaction->memo ?: $transaction->reference ?: 'Transaction' }}</p>
            <p class="text-sm font-semibold {{ $amountClass }}">{{ $amountPrefix }}{{ number_format((float) $transaction->amount, $transaction->currency->decimals ?? 2) }} {{ $transaction->currency->symbol ?? '' }}</p>
        </div>
        <div class="flex items-center justify-between mt-0.5">
            <p class="text-xs text-slate-500">{{ $transaction->created_at->diffForHumans() }}</p>
            <x-status-pill :status="$transaction->status" />
        </div>
    </div>
</div>
@else
<div class="flex items-center gap-4 p-4 bg-slate-900/50 rounded-xl border border-slate-800/50 hover:border-slate-700 transition-colors">
    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-slate-400">
        <x-icon name="{{ $iconName }}" class="{{ $iconClass }}" />
    </div>
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2">
            <p class="text-sm font-medium text-slate-200 truncate">{{ $transaction->memo ?: $transaction->reference ?: 'Transaction' }}</p>
            <x-status-pill :status="$transaction->status" />
        </div>
        <p class="text-xs text-slate-500 mt-0.5">Ref: {{ $transaction->transaction_id }} &middot; {{ $transaction->created_at->format('M d, Y H:i') }}</p>
    </div>
    <div class="text-right flex-shrink-0">
        <p class="text-sm font-semibold {{ $amountClass }}">{{ $amountPrefix }}{{ number_format((float) $transaction->amount, $transaction->currency->decimals ?? 2) }} {{ $transaction->currency->symbol ?? '' }}</p>
        <p class="text-xs text-slate-500">{{ $transaction->currency->code ?? '' }}</p>
    </div>
</div>
@endif
