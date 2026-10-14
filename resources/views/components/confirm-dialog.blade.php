@props([
    'open' => false,
    'title' => 'Confirm Action',
    'confirmLabel' => 'Confirm',
    'cancelLabel' => 'Cancel',
    'confirmType' => 'primary',
])

@php
    $confirmClass = match($confirmType) {
        'danger' => 'bg-[var(--danger)] hover:opacity-90 text-white',
        'warning' => 'bg-[var(--warning)] hover:opacity-90 text-white',
        default => 'bg-[var(--primary)] hover:bg-[var(--primary-dark)] text-white',
    };
@endphp

<div x-data="{ open: @js($open) }" x-show="open" x-transition.opacity class="relative z-50" aria-labelledby="confirm-dialog-title" role="dialog" aria-modal="true">
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-[var(--text)]/40 backdrop-blur-sm" @click="$wire.open = false"></div>

    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="scale-95 opacity-0" x-transition:enter-end="scale-100 opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="scale-100 opacity-100" x-transition:leave-end="scale-95 opacity-0" class="relative w-full max-w-sm rounded-2xl bg-[var(--surface)] border border-[var(--line)] shadow-lg p-6">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-[var(--surface-raised)] mb-4">
                    <x-icon name="alert-triangle" class="h-6 w-6 text-[var(--warning)]" />
                </div>
                <h3 class="text-lg font-semibold text-[var(--text)] mb-2" id="confirm-dialog-title">{{ $title }}</h3>
                <div class="mt-2">
                    {{ $slot }}
                </div>
            </div>
            <div class="mt-6 flex items-center justify-center gap-3">
                <button wire:click="$wire.open = false" class="px-4 py-2 text-sm font-medium text-[var(--text-muted)] bg-[var(--surface-raised)] border border-[var(--line)] rounded-lg hover:bg-[var(--line-soft)] transition-colors">{{ $cancelLabel }}</button>
                <button wire:click="{{ $attributes->get('wire:click') }}" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $confirmClass }}">{{ $confirmLabel }}</button>
            </div>
        </div>
    </div>
</div>
