@props([
    'type' => 'success',
    'message' => '',
])

@php
    $colorMap = [
        'success' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
        'error' => 'bg-red-500/20 text-red-300 border-red-500/30',
        'info' => 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30',
        'warning' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
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
