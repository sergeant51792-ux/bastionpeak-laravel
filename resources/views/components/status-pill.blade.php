@props([
    'status',
    'label' => null,
])

@php
    $value = $status instanceof \BackedEnum ? $status->value : (string) $status;

    $label = $label ?? ($status instanceof \App\Enums\TransactionStatus
        ? $status->label()
        : ucfirst(str_replace('_', ' ', $value)));

    if ($status instanceof \App\Enums\TransactionStatus) {
        $tone = $status->tone();
    } else {
        $tone = match ($value) {
            'active', 'approved', 'credited', 'posted', 'success' => 'success',
            'pending_review', 'pending_match', 'locked', 'reversed', 'warning' => 'warning',
            'closed', 'rejected', 'blocked', 'failed', 'danger' => 'danger',
            'frozen' => 'info',
            default => 'neutral',
        };
    }

    $badgeClass = match ($tone) {
        'success' => 'badge-success',
        'warning' => 'badge-warning',
        'danger' => 'badge-danger',
        'info' => 'badge-info',
        default => 'badge-neutral',
    };

    $dotClass = match ($tone) {
        'success' => 'bg-[var(--success)]',
        'warning' => 'bg-[var(--warning)]',
        'danger' => 'bg-[var(--danger)]',
        'info' => 'bg-[var(--accent)]',
        default => 'bg-[var(--text-muted)]',
    };
@endphp

<span class="badge {{ $badgeClass }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $dotClass }}" aria-hidden="true"></span>
    {{ $label }}
</span>
