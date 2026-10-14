@props([
    'limit',
    'used' => 0,
    'label' => 'Limit',
])

@php
    $percentage = $limit > 0 ? min(100, round(($used / $limit) * 100)) : 0;
    $remaining = $limit - $used;
    $barColorClass = match(true) {
        $percentage >= 90 => 'bg-[var(--danger)]',
        $percentage >= 70 => 'bg-[var(--warning)]',
        default => 'bg-[var(--accent)]',
    };
@endphp

<div>
    <div class="flex items-center justify-between mb-1.5">
        <span class="text-xs font-medium text-[var(--text-muted)]">{{ $label }}</span>
        <span class="text-xs text-[var(--text-muted)]">{{ number_format($remaining, 2) }} remaining</span>
    </div>
    <div class="h-2 rounded-full bg-[var(--line)] overflow-hidden">
        <div class="h-full rounded-full {{ $barColorClass }} transition-all duration-500" style="width: {{ $percentage }}%"></div>
    </div>
    <div class="flex items-center justify-between mt-1">
        <span class="text-[10px] text-[var(--text-muted)]">{{ number_format($used, 2) }} used</span>
        <span class="text-[10px] text-[var(--text-muted)]">{{ number_format($limit, 2) }} total</span>
    </div>
</div>
