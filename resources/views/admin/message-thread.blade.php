@extends('layouts.admin')

@section('title', 'Message thread')
@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.messages.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--surface-raised)]" aria-label="Back">
            <x-icon name="arrow-left" class="w-5 h-5" />
        </a>
        <div class="flex items-center gap-3">
            @if($participant)
                <div class="w-8 h-8 rounded-full bg-[var(--primary)]/10 flex items-center justify-center text-[var(--primary)] font-semibold">{{ substr($participant->name, 0, 2) }}</div>
                <div>
                    <h1 class="text-xl font-semibold">{{ $participant->name }}</h1>
                    <p class="text-sm text-[var(--text-muted)]">{{ $participant->email }}</p>
                </div>
            @else
                <h1 class="text-xl font-semibold">Conversation</h1>
            @endif
        </div>
    </div>

    <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
        <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-4">Conversation</h2>
        <div class="space-y-3 max-h-[500px] overflow-y-auto">
            @forelse($thread as $msg)
                @php
                    $isMine = $msg->sender_id === auth()->id();
                @endphp
                <div class="{{ $isMine ? 'ml-auto' : 'mr-auto' }} max-w-[80%]">
                    <div class="{{ $isMine ? 'bg-[var(--primary)] text-white' : 'bg-[var(--surface-raised)]' }} p-4 rounded-2xl">
                        <p class="text-sm">{{ $msg->body }}</p>
                        <div class="flex items-center justify-between mt-1">
                            <p class="text-xs {{ $isMine ? 'text-white/60' : 'text-[var(--text-muted)]' }}">{{ $msg->created_at->format('d M Y, H:i') }}</p>
                            @if(!$isMine && is_null($msg->read_at))
                                <span class="text-[10px] font-semibold text-[var(--accent)]">Unread</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-[var(--text-muted)]">No messages in this thread.</p>
            @endforelse
        </div>
    </div>

    @if($participant)
    <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
        <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-4">Reply</h2>
        <form method="POST" action="{{ route('admin.messages.reply') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="thread_id" value="{{ $threadId }}">
            <input type="hidden" name="recipient_id" value="{{ $participant->id }}">
            <div>
                <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Message</label>
                <textarea name="body" rows="4" required placeholder="Type your reply..." class="input-field"></textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="btn-primary">Send reply</button>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection
