@php
    $type = $method->type ?? '';
    $iconName = match(true) {
        $type === 'crypto' => 'bitcoin',
        in_array($type, ['giftcard', 'gift_card']) => 'gift',
        default => 'landmark',
    };
@endphp
<button type="button"
        onclick="openDepositModal({{ $accountId }}, {{ $method->id }})"
        class="text-left border border-[var(--line)] rounded-xl p-4 hover:border-[var(--accent)] hover:bg-[var(--line-soft)] transition-all group">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] shrink-0">
            @if($method->icon_image_path)
                <img src="{{ url('storage-file/' .$method->icon_image_path) }}" alt="{{ $method->name }}" class="w-6 h-6 object-contain">
            @else
                <x-icon name="{{ $iconName }}" class="w-5 h-5" />
            @endif
        </div>
        <div class="flex-1 min-w-0">
            <p class="font-semibold text-[var(--text)]">{{ $method->name }}</p>
            <p class="text-xs text-[var(--text-muted)] mt-0.5">{{ ucfirst($type) }}</p>
        </div>
        <x-icon name="chevron-right" class="w-5 h-5 text-[var(--text-muted)] group-hover:text-[var(--accent)] transition-colors" />
    </div>
</button>
