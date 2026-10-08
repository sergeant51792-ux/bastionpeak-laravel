@extends('layouts.customer')

@section('title', 'Alerts')
@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-[var(--text)] tracking-tight">Alerts</h1>
        <form method="POST" action="{{ route('notifications.mark-read') }}" class="inline">
            @csrf
            <button type="submit" class="text-sm font-medium text-[var(--accent)] hover:text-[var(--accent-dark)] transition-colors">Mark all read</button>
        </form>
    </div>

    <div class="space-y-2">
        @forelse($notifications as $notification)
            <a href="#" class="flex items-start gap-3 p-4 bg-white border rounded-2xl transition-all @if(is_null($notification->read_at)) border-[var(--accent)]/30 shadow-sm @else border-[var(--line)] hover:border-[var(--accent)] @endif">
                <div class="w-2 h-2 rounded-full bg-[var(--accent)] mt-2 flex-shrink-0 @if(is_null($notification->read_at)) @else opacity-30 @endif"></div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-[var(--text)]">{{ $notification->title }}</p>
                    <p class="text-sm text-[var(--text-muted)] mt-0.5">{{ $notification->body }}</p>
                    <p class="text-xs text-[var(--text-muted)] mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                </div>
            </a>
        @empty
            <div class="empty-state bg-white border border-[var(--line)] rounded-2xl">
                <x-icon name="bell" class="w-12 h-12 mx-auto mb-3 text-[var(--text-muted)]" />
                 <p class="text-sm text-[var(--text-muted)]">No alerts.</p>
            </div>
        @endforelse
    </div>

    {{ $notifications->links() }}
</div>
@endsection
