@extends('layouts.customer')

@section('title', 'Secure messages')
@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-[var(--text)] tracking-tight">Secure messages</h1>
        <button onclick="document.getElementById('new-msg-form').classList.toggle('hidden')" class="px-4 py-2 bg-[var(--primary)] text-white rounded-xl text-sm font-semibold hover:bg-[var(--primary-dark)] transition-colors">
            New message
        </button>
    </div>

    {{-- New Message Form (collapsed) --}}
    <div id="new-msg-form" class="hidden bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
        <form method="POST" action="{{ route('messages.send') }}" class="space-y-4">
            @csrf
            <div class="hidden">
                <input type="text" name="subject" value="Customer message">
            </div>
            <div>
                <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Message</label>
                <textarea name="body" rows="3" required placeholder="Type your message..." class="input-field"></textarea></textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-[var(--primary)] text-white rounded-xl text-sm font-semibold hover:bg-[var(--primary-dark)] transition-colors">Send message</button>
            </div>
        </form>
    </div>

    @if($threadId && $thread->count() > 0)
        <div class="space-y-3">
            @foreach($thread as $msg)
                @php
                    $isMine = $msg->sender_id === auth()->id();
                @endphp
                <div class="{{ $isMine ? 'ml-auto' : 'mr-auto' }} max-w-[85%]">
                    <div @class(['p-4 rounded-2xl', $isMine ? 'bg-[var(--primary)] text-white' : 'bg-white border border-[var(--line)] text-[var(--text)]'])>
                        <p class="text-sm">{{ $msg->body }}</p>
                        <div class="flex items-center justify-between mt-1">
                            <p class="text-xs {{ $isMine ? 'text-white/60' : 'text-[var(--text-muted)]' }}">{{ $msg->created_at->format('d M Y, H:i') }}</p>
                            @if(!$isMine && is_null($msg->read_at))
                                <form method="POST" action="{{ route('messages.read', $msg) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs font-semibold {{ $isMine ? 'text-white/70' : 'text-[var(--accent)]' }} hover:underline transition-colors">Mark as read</button>
                                </form>
                            @endif
                            @if($isMine)
                                <form method="POST" action="{{ route('messages.destroy', $msg) }}" class="inline" onsubmit="return confirm('Delete this message?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold {{ $isMine ? 'text-white/70' : 'text-red-500' }} hover:underline transition-colors">Remove</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <form method="POST" action="{{ route('messages.send') }}" class="flex gap-2 pt-4 border-t border-[var(--line)]">
            @csrf
            <input type="hidden" name="thread_id" value="{{ $threadId }}">
            <input type="text" name="body" required placeholder="Write a reply..."
                   class="flex-1 px-4 py-3 bg-white border border-[var(--line)] rounded-xl text-sm focus:border-[var(--accent)] focus:ring-2 focus:ring-[var(--accent)]/10 transition-all">
            <button type="submit" class="px-5 py-3 bg-[var(--primary)] text-white rounded-xl text-sm font-semibold hover:bg-[var(--primary-dark)] transition-colors shadow-sm">
                Send
            </button>
        </form>
    @else
        <div class="space-y-2">
            @forelse($threads as $thread)
                <div class="flex items-start gap-3 p-4 bg-white border border-[var(--line)] rounded-2xl hover:border-[var(--accent)] hover:shadow-sm transition-all">
                    <a href="{{ route('messages', ['thread' => $thread->thread_id]) }}" class="flex items-start gap-3 flex-1 min-w-0">
                        <div class="w-10 h-10 rounded-full bg-[var(--line-soft)] flex items-center justify-center flex-shrink-0 text-[var(--text-muted)]">
                            <x-icon name="message-circle" class="w-5 h-5" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-[var(--text)] truncate">{{ $thread->subject ?? 'No subject' }}</p>
                                <span class="text-xs text-[var(--text-muted)] flex-shrink-0">{{ $thread->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-[var(--text-muted)] truncate mt-0.5">{{ $thread->body }}</p>
                        </div>
                    </a>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if(is_null($thread->read_at) && $thread->recipient_id === auth()->id())
                            <span class="w-2 h-2 rounded-full bg-[var(--accent)]"></span>
                        @endif
                        <form method="POST" action="{{ route('messages.thread.destroy', $thread->thread_id) }}" onsubmit="return confirm('Remove this conversation?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 text-[var(--text-muted)] hover:text-red-600 transition-colors rounded-lg hover:bg-red-50" aria-label="Delete conversation">
                                <x-icon name="trash-2" class="w-4 h-4" />
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-state bg-white border border-[var(--line)] rounded-2xl p-8">
                    <x-icon name="message-circle" class="w-12 h-12 mx-auto mb-3 text-[var(--text-muted)]" />
                    <p class="text-sm text-[var(--text-muted)] mb-4">No secure messages yet. Send a message to get started.</p>
                    <form method="POST" action="{{ route('messages.send') }}" class="flex gap-2 max-w-md mx-auto">
                        @csrf
                        <input type="text" name="body" required placeholder="Type your message..." class="flex-1 px-4 py-2.5 bg-white border border-[var(--line)] rounded-xl text-sm focus:border-[var(--accent)] transition-all">
                        <button type="submit" class="px-4 py-2.5 bg-[var(--primary)] text-white rounded-xl text-sm font-semibold hover:bg-[var(--primary-dark)] transition-colors">Send</button>
                    </form>
                </div>
            @endforelse
        </div>
    @endif
</div>
@endsection
