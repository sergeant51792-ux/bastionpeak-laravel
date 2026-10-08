@extends('layouts.admin')

@section('title', 'Messages')
@section('content')
<div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl overflow-hidden">
    <div class="p-6 border-b border-[var(--line)]">
        <h2 class="font-semibold mb-4">Messages</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.messages.index') }}" class="px-3 py-1.5 bg-[var(--surface-raised)] rounded-lg text-xs font-medium">All</a>
            <a href="{{ route('admin.messages.index', ['filter' => 'unread']) }}" class="px-3 py-1.5 text-[var(--text-muted)] text-xs">Unread</a>
            <a href="{{ route('admin.messages.index', ['filter' => 'flagged']) }}" class="px-3 py-1.5 text-[var(--text-muted)] text-xs">Flagged</a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-[var(--surface-raised)]">
                <tr>
                    <th class="px-4 py-3 text-left">Thread</th>
                    <th class="px-4 py-3 text-left">Subject</th>
                    <th class="px-4 py-3 text-left">Last message</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--line)]">
                @forelse($messages as $threadId => $threadMessages)
                    @php
                        $last = $threadMessages->first();
                        $subject = $last->subject ?? 'No subject';
                        $lastAt = $last ? $last->created_at->diffForHumans() : '';
                        $unread = $threadMessages->contains(fn ($m) => is_null($m->read_at) && $m->recipient_id === auth()->id());
                    @endphp
                    <tr class="hover:bg-[var(--surface-raised)]/50">
                        <td class="px-4 py-3 font-medium">{{ $last->sender->name ?? 'Unknown' }}</td>
                        <td class="px-4 py-3">{{ $subject }}</td>
                        <td class="px-4 py-3 text-[var(--text-muted)]">{{ $lastAt }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $unread ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $unread ? 'Unread' : 'Read' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.messages.thread', $threadId) }}" class="px-3 py-1.5 bg-[var(--primary)]/10 text-[var(--primary)] rounded-lg text-xs font-medium hover:bg-[var(--primary)]/20 transition-colors">Open</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-6 text-center text-[var(--text-muted)]">No messages.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
