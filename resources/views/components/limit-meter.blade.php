@props([
    'limit',
    'used' => 0,
    'label' => 'Limit',
])

@php
    $percentage = $limit > 0 ? min(100, round(($used / $limit) * 100)) : 0;
    $remaining = $limit - $used;
    $colorClass = match(true) {
        $percentage >= 90 => 'bg-red-500',
        $percentage >= 70 => 'bg-amber-500',
        default => 'bg-indigo-500',
    };
@endphp

<div>
    <div class="flex items-center justify-between mb-1.5">
        <span class="text-xs font-medium text-slate-400">{{ $label }}</span>
        <span class="text-xs text-slate-500">{{ number_format($remaining, 2) }} remaining</span>
    </div>
    <div class="h-2 rounded-full bg-slate-800 overflow-hidden">
        <div class="h-full rounded-full {{ $colorClass }} transition-all duration-500" style="width: {{ $percentage }}%"></div>
    </div>
    <div class="flex items-center justify-between mt-1">
        <span class="text-[10px] text-slate-600">{{ number_format($used, 2) }} used</span>
        <span class="text-[10px] text-slate-600">{{ number_format($limit, 2) }} total</span>
    </div>
</div>
