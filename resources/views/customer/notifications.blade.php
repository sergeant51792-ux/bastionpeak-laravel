@extends('layouts.customer')

@section('title', 'Alerts')
@section('content')
<div class="px-3 sm:px-4 lg:px-6 pt-4 space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-[var(--text)] tracking-tight">Alerts</h1>
            <p class="text-sm text-[var(--text-muted)] mt-0.5">Notices on your account activity</p>
        </div>
        @if($notifications->contains(fn ($n) => is_null($n->read_at)))
            <form method="POST" action="{{ route('notifications.mark-read') }}" class="inline">
                @csrf
                <button type="submit" class="text-sm font-semibold text-[var(--accent)] hover:text-[var(--accent-dark)] transition-colors">Mark all read</button>
            </form>
        @endif
    </div>

    <div class="space-y-2.5">
        @forelse($notifications as $notification)
            @php
                $tone = $notification->tone();
                $toneClass = match ($tone) {
                    'success' => 'bg-[var(--success-bg)] text-[var(--success)] border-[var(--success)]',
                    'danger' => 'bg-[var(--danger-bg)] text-[var(--danger)] border-[var(--danger)]',
                    'warning' => 'bg-[var(--warning-bg)] text-[var(--warning)] border-[var(--warning)]',
                    default => 'bg-[var(--accent-subtle)] text-[var(--accent)] border-[var(--accent)]',
                };
                $isUnread = is_null($notification->read_at);
            @endphp
            <button type="button" class="flex items-start gap-3 p-4 bg-white dark:bg-[var(--surface)] border rounded-2xl transition-all {{ $isUnread ? 'border-[var(--accent)]/40 shadow-sm' : 'border-[var(--line)] hover:border-[var(--accent)]' }}">
                <span class="txn-icon w-10 h-10 {{ $toneClass }}" style="border-color: rgba(5, 150, 105, 0.15);">
                    <x-icon name="{{ $notification->icon() }}" class="w-4 h-4" />
                </span>
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-3">
                        <p class="text-sm font-semibold text-[var(--text)]">{{ $notification->title }}</p>
                        @if($isUnread)
                            <span class="mt-1.5 w-2 h-2 rounded-full bg-[var(--accent)] flex-shrink-0" aria-label="Unread"></span>
                        @endif
                    </div>
                    <p class="text-sm text-[var(--text-muted)] mt-0.5">{{ $notification->body }}</p>
                    <p class="text-xs text-[var(--text-muted)] mt-1.5">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
            </button>
        @empty
            <div class="empty-state bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl">
                <x-icon name="bell" class="w-10 h-10 mx-auto mb-3 text-[var(--text-muted)]" />
                <p class="text-sm text-[var(--text-muted)]">No alerts yet.</p>
            </div>
        @endforelse
    </div>

    {{ $notifications->links() }}
</div>
@endsection
