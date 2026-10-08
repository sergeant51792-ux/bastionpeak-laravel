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
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'flex flex-col items-center gap-1.5 ' . ($disabled ? 'opacity-40 pointer-events-none' : 'text-slate-400 hover:text-indigo-400 transition-colors')]) }}>
@elseif($wireClick)
    <button wire:click="{{ $wireClick }}" {{ $attributes->merge(['class' => 'flex flex-col items-center gap-1.5 ' . ($disabled ? 'opacity-40 pointer-events-none' : 'text-slate-400 hover:text-indigo-400 transition-colors'), 'disabled' => $disabled]) }}>
@else
    <div {{ $attributes->merge(['class' => 'flex flex-col items-center gap-1.5 ' . ($disabled ? 'opacity-40' : 'text-slate-400 hover:text-indigo-400 transition-colors')]) }}>
@endif
    <span class="flex items-center justify-center w-14 h-14 rounded-full bg-slate-800 border border-slate-700 {{ $disabled ? '' : 'hover:bg-indigo-500/20 hover:border-indigo-500/30' }}">
        <x-icon name="{{ $iconMap[$icon] ?? $iconMap['home'] }}" class="h-6 w-6" />
    </span>
    <span class="text-xs font-medium">{{ $label }}</span>
@if($href)</a>@elseif($wireClick)</button>@else</div>@endif
