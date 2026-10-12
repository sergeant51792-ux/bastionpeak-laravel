@extends('layouts.customer')

@section('title', 'Secure messages')
@section('content')
<div class="px-3 sm:px-4 lg:px-6 pt-4 space-y-5">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-[var(--text)] tracking-tight">Secure messages</h1>
            <p class="text-sm text-[var(--text-muted)] mt-0.5">Communicate with our support team</p>
        </div>
        <button onclick="document.getElementById('new-msg-form').classList.toggle('hidden')" class="px-4 py-2.5 bg-[var(--primary)] text-white rounded-xl text-sm font-semibold hover:bg-[var(--primary-dark)] transition-colors min-h-[44px]">
            New message
        </button>
    </div>

    {{-- New Message Form (collapsed) --}}
    <div id="new-msg-form" class="hidden bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-5">
        <form method="POST" action="{{ route('messages.send') }}" class="space-y-4">
            @csrf
            <input type="hidden" name="subject" value="Customer message">
            <div>
                <label for="new-message-body" class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Message</label>
                <textarea id="new-message-body" name="body" rows="3" required placeholder="Type your message..." class="input-field"></textarea>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-5 py-2.5 bg-[var(--primary)] text-white rounded-xl text-sm font-semibold hover:bg-[var(--primary-dark)] transition-colors min-h-[44px]">Send message</button>
            </div>
        </form>
    </div>

    @if($threadId && $thread->count() > 0)
        <div class="space-y-3">
            @foreach($thread as $msg)
                @php $isMine = $msg->sender_id === auth()->id(); @endphp
                <div class="{{ $isMine ? 'ml-auto' : 'mr-auto' }} max-w-[85%]">
                    <div @class([
                        'p-4 rounded-2xl',
                        'bg-[var(--primary)] text-white' => $isMine,
                        'bg-white dark:bg-[var(--surface)] border border-[var(--line)] text-[var(--text)]' => ! $isMine,
                    ])>
                        <p class="text-sm">{{ $msg->body }}</p>
                        <div class="flex items-center justify-between gap-3 mt-1.5">
                            <p class="text-xs {{ $isMine ? 'text-white/60' : 'text-[var(--text-muted)]' }}">{{ $msg->created_at->format('d M Y, H:i') }}</p>
                            @if(!$isMine && is_null($msg->read_at))
                                <form method="POST" action="{{ route('messages.read', $msg) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs font-semibold text-[var(--accent)] hover:underline transition-colors">Mark as read</button>
                                </form>
                            @endif
                            @if($isMine)
                                <form method="POST" action="{{ route('messages.destroy', $msg) }}" class="inline" onsubmit="return confirm('Delete this message?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-white/70 hover:text-white hover:underline transition-colors">Remove</button>
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
                   class="input-field flex-1">
            <button type="submit" class="px-5 py-3 bg-[var(--primary)] text-white rounded-xl text-sm font-semibold hover:bg-[var(--primary-dark)] transition-colors shadow-sm min-h-[44px]">
                Send
            </button>
        </form>
    @else
        <div class="space-y-2.5">
            @forelse($threads as $thread)
                <div class="flex items-start gap-3 p-4 bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl hover:border-[var(--accent)] hover:shadow-sm transition-all">
                    <a href="{{ route('messages', ['thread' => $thread->thread_id]) }}" class="flex items-start gap-3 flex-1 min-w-0">
                        <span class="txn-icon w-10 h-10">
                            <x-icon name="message-square" class="w-4 h-4" />
                        </span>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2">
                                <p class="text-sm font-semibold text-[var(--text)] truncate">{{ $thread->subject ?? 'No subject' }}</p>
                                <span class="text-xs text-[var(--text-muted)] flex-shrink-0">{{ $thread->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-[var(--text-muted)] truncate mt-0.5">{{ $thread->body }}</p>
                        </div>
                    </a>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        @if(is_null($thread->read_at) && $thread->recipient_id === auth()->id())
                            <span class="w-2 h-2 rounded-full bg-[var(--accent)]" aria-label="Unread"></span>
                        @endif
                        <form method="POST" action="{{ route('messages.thread.destroy', $thread->thread_id) }}" onsubmit="return confirm('Remove this conversation?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-[var(--text-muted)] hover:text-[var(--danger)] transition-colors rounded-lg" aria-label="Delete conversation">
                                <x-icon name="trash-2" class="w-4 h-4" />
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-state bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-8">
                    <x-icon name="message-square" class="w-10 h-10 mx-auto mb-3 text-[var(--text-muted)]" />
                    <p class="text-sm text-[var(--text-muted)] mb-4">No secure messages yet. Send a message to get started.</p>
                    <form method="POST" action="{{ route('messages.send') }}" class="flex gap-2 max-w-md mx-auto">
                        @csrf
                        <input type="text" name="body" required placeholder="Type your message..." class="input-field flex-1">
                        <button type="submit" class="px-4 py-2.5 bg-[var(--primary)] text-white rounded-xl text-sm font-semibold hover:bg-[var(--primary-dark)] transition-colors min-h-[44px]">Send</button>
                    </form>
                </div>
            @endforelse
        </div>
    @endif
</div>
@endsection
