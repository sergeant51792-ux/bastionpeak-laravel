@props([
    'icon' => 'home',
    'label' => 'Action',
    'href' => null,
    'wireClick' => null,
    'disabled' => false,
])

@php
    $iconMap = [
        'home' => 'home',
        'plus' => 'plus',
        'send' => 'send',
        'receive' => 'arrow-up-right',
        'card' => 'credit-card',
        'settings' => 'settings',
    ];
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'flex flex-col items-center gap-1.5 ' . ($disabled ? 'opacity-40 pointer-events-none' : 'text-[var(--text-muted)] hover:text-[var(--accent)] transition-colors')]) }}>
@elseif($wireClick)
    <button wire:click="{{ $wireClick }}" {{ $attributes->merge(['class' => 'flex flex-col items-center gap-1.5 ' . ($disabled ? 'opacity-40 pointer-events-none' : 'text-[var(--text-muted)] hover:text-[var(--accent)] transition-colors'), 'disabled' => $disabled]) }}>
@else
    <div {{ $attributes->merge(['class' => 'flex flex-col items-center gap-1.5 ' . ($disabled ? 'opacity-40' : 'text-[var(--text-muted)] hover:text-[var(--accent)] transition-colors')]) }}>
@endif
    <span class="flex items-center justify-center w-14 h-14 rounded-full bg-[var(--line-soft)] border border-[var(--line)] {{ $disabled ? '' : 'hover:bg-[var(--accent)]/20 hover:border-[var(--accent)]/30' }}">
        <x-icon name="{{ $iconMap[$icon] ?? $iconMap['home'] }}" class="h-6 w-6 text-[var(--text-muted)]" />
    </span>
    <span class="text-xs font-medium">{{ $label }}</span>
@if($href)</a>@elseif($wireClick)</button>@else</div>@endif
