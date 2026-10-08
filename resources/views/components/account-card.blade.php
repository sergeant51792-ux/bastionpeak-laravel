@props([
    'account',
    'size' => 'full',
    'theme' => 'default',
])

@php
    $isCompact = $size === 'compact';
    $statusColors = match($account->status) {
        'frozen' => 'bg-sky-500/20 text-sky-300 border-sky-500/30',
        'locked' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
        'closed' => 'bg-red-500/20 text-red-300 border-red-500/30',
        default => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
    };

    $typeIcons = [
        'main' => 'landmark',
        'sub' => 'wallet',
    ];
@endphp

<div class="rounded-2xl border border-slate-800 bg-slate-900 p-{{ $isCompact ? '4' : '6' }} shadow-lg transition-all hover:shadow-indigo-500/10">
    <div class="flex items-center justify-between mb-{{ $isCompact ? '2' : '4' }}">
        <div class="flex items-center gap-2">
            <x-icon name="{{ $typeIcons[$account->type] ?? $typeIcons['main'] }}" class="h-5 w-5" />
            <span class="text-sm font-medium text-slate-300">{{ $account->name }}</span>
        </div>
        <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-xs font-medium {{ $statusColors }}">
            {{ ucfirst($account->status) }}
        </span>
    </div>
    <div class="flex items-end justify-between">
        <div>
            <p class="text-xs text-slate-500 mb-1">Balance</p>
            <p class="text-xl font-bold text-slate-100">{{ $account->formatted_balance }}</p>
        </div>
        @if(!$isCompact)
            <p class="text-xs text-slate-500 font-mono">****{{ substr($account->account_number, -4) }}</p>
        @endif
    </div>
    @if(!$isCompact && $account->currency && in_array(strtolower($account->currency->code), ['btc', 'eth']))
        <div class="mt-3 flex items-center gap-1 text-xs text-slate-400">
            <x-icon name="external-link"  class="h-3 w-3" />
             <span>Rate: 1 {{ $account->currency->symbol }} = {{ number_format((float) $account->currency->exchange_rate, 2) }} USD</span>
        </div>
    @endif
</div>
