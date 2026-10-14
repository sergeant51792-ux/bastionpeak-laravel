@props([
    'type' => 'success',
    'message' => '',
])

@php
    $colorMap = [
        'success' => 'bg-[var(--success-bg)] text-[var(--success)] border-[var(--success)]/30',
        'error' => 'bg-[var(--danger-bg)] text-[var(--danger)] border-[var(--danger)]/30',
        'info' => 'bg-[var(--accent-subtle)] text-[var(--accent-dark)] border-[var(--accent)]/30',
        'warning' => 'bg-[var(--warning-bg)] text-[var(--warning)] border-[var(--warning)]/30',
    ];

    $iconMap = [
        'success' => 'check-circle',
        'error' => 'x-circle',
        'info' => 'info',
        'warning' => 'alert-triangle',
    ];
@endphp

<div x-data="{ show: true }" x-show="show" x-transition class="flex items-start gap-3 rounded-xl border px-4 py-3 {{ $colorMap[$type] ?? $colorMap['info'] }}">
    <span class="flex-shrink-0 mt-0.5">
        <x-icon name="{{ $iconMap[$type] ?? 'info' }}" class="h-5 w-5" />
    </span>
    <div class="flex-1">
        <p class="text-sm font-medium">{{ $message }}</p>
    </div>
    <button @click="show = false" class="flex-shrink-0 opacity-70 hover:opacity-100 transition-opacity">
        <x-icon name="x" class="h-4 w-4" />
    </button>
</div>
