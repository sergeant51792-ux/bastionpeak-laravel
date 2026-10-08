@props([
    'open' => false,
    'title' => 'Confirm Action',
    'confirmLabel' => 'Confirm',
    'cancelLabel' => 'Cancel',
    'confirmType' => 'primary',
])

@php
    $confirmClass = match($confirmType) {
        'danger' => 'bg-red-600 hover:bg-red-500 text-white',
        'warning' => 'bg-amber-600 hover:bg-amber-500 text-white',
        default => 'bg-indigo-600 hover:bg-indigo-500 text-white',
    };
@endphp

<div x-data="{ open: @js($open) }" x-show="open" x-transition.opacity class="relative z-50" aria-labelledby="confirm-dialog-title" role="dialog" aria-modal="true">
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" @click="$wire.open = false"></div>

    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0" class="relative w-full max-w-sm rounded-2xl bg-slate-900 border border-slate-700 shadow-2xl p-6">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-slate-800 mb-4">
                    <x-icon name="alert-triangle" class="h-6 w-6 text-amber-400" />
                </div>
                <h3 class="text-lg font-semibold text-slate-100 mb-2" id="confirm-dialog-title">{{ $title }}</h3>
                <div class="mt-2">
                    {{ $slot }}
                </div>
            </div>
            <div class="mt-6 flex items-center justify-center gap-3">
                <button wire:click="$wire.open = false" class="px-4 py-2 text-sm font-medium text-slate-300 bg-slate-800 border border-slate-700 rounded-lg hover:bg-slate-700 transition-colors">{{ $cancelLabel }}</button>
                <button wire:click="{{ $attributes->get('wire:click') }}" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $confirmClass }}">{{ $confirmLabel }}</button>
            </div>
        </div>
    </div>
</div>
