@props([
    'title' => 'Nothing here yet',
    'description' => 'Get started by taking an action below.',
    'actionLabel' => 'Take Action',
    'actionHref' => null,
    'actionWireClick' => null,
])

<div class="flex flex-col items-center justify-center py-16 px-4 text-center">
    <div class="w-16 h-16 rounded-full bg-[var(--line-soft)] flex items-center justify-center mb-4">
        <x-icon name="clipboard-list" class="h-8 w-8 text-[var(--text-muted)]" />
    </div>
    <h3 class="text-lg font-semibold text-[var(--text)] mb-1">{{ $title }}</h3>
    <p class="text-sm text-[var(--text-muted)] max-w-sm mb-6">{{ $description }}</p>
    @if($actionHref)
        <a href="{{ $actionHref }}" class="inline-flex items-center px-4 py-2 bg-[var(--accent)] text-white text-sm font-medium rounded-lg hover:opacity-90 transition-colors">{{ $actionLabel }}</a>
    @elseif($actionWireClick)
        <button wire:click="{{ $actionWireClick }}" class="inline-flex items-center px-4 py-2 bg-[var(--accent)] text-white text-sm font-medium rounded-lg hover:opacity-90 transition-colors">{{ $actionLabel }}</button>
    @endif
</div>
