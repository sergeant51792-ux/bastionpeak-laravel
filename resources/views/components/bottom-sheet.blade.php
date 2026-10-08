@props([
    'open' => false,
    'maxWidth' => 'md',
])

@php
    $maxWidthClass = match($maxWidth) {
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        default => 'max-w-md',
    };
@endphp

<div x-data="{ open: @js($open) }" x-show="open" x-transition.opacity class="relative z-50" aria-labelledby="bottom-sheet-title" role="dialog" aria-modal="true">
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" @click="$wire.open = false"></div>

    <div class="fixed inset-x-0 bottom-0 {{ $maxWidthClass }} mx-auto z-50">
        <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="translate-y-full opacity-0" x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="translate-y-full opacity-0" class="rounded-t-2xl bg-slate-900 border border-slate-700 border-b-0 shadow-2xl">
            <div class="w-12 h-1.5 bg-slate-700 rounded-full mx-auto mt-3"></div>
            <div class="p-6">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
