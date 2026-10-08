@extends('layouts.admin')

@section('title', 'Card requests')
@section('content')
    <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <h2 class="text-lg font-semibold">Card requests</h2>
        <span class="text-sm text-[var(--text-muted)]">{{ $requests->total() }} total</span>
    </div>

    <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl overflow-x-auto -mx-4 sm:mx-0">
        <table class="w-full text-sm min-w-[600px]">
            <thead class="bg-[var(--surface-raised)]">
                <tr>
                    <th class="px-4 py-3 text-left">User</th>
                    <th class="px-4 py-3 text-left">Account</th>
                    <th class="px-4 py-3 text-left">Reason</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Requested</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--line)]">
                @forelse($requests as $request)
                    <tr class="hover:bg-[var(--surface-raised)]/50">
                        <td class="px-4 py-3">{{ $request->user->name }}</td>
                        <td class="px-4 py-3">{{ $request->account->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $request->reason ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span @class(['px-2.5 py-1 rounded-full text-xs font-medium', 'bg-yellow-100 text-yellow-800' => $request->status === 'pending', 'bg-green-100 text-green-800' => $request->status === 'approved', 'bg-red-100 text-red-800' => $request->status === 'rejected'])>
                                {{ ucfirst($request->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $request->created_at->diffForHumans() }}</td>
                        <td class="px-4 py-3 text-right">
                            @if($request->status === 'pending')
                                <div class="flex gap-2 justify-end">
                                    <form method="POST" action="{{ route('admin.card-requests.approve', $request) }}" onsubmit="return confirm('Approve this card request?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-[var(--success)] text-white rounded-lg text-xs font-bold hover:opacity-90 transition-opacity min-h-[44px] min-w-[44px] flex items-center justify-center">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.card-requests.reject', $request) }}" onsubmit="return confirm('Reject this card request?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded-lg text-xs font-bold hover:bg-red-700 transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center">Reject</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-xs text-[var(--text-muted)]">{{ $request->reviewed_at?->diffForHumans() ?? '—' }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-[var(--text-muted)]">No card requests yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-sm text-[var(--text-muted)]">
        <span>Showing {{ $requests->firstItem() ?? 0 }}–{{ $requests->lastItem() ?? 0 }} of {{ $requests->total() }}</span>
        <div class="flex gap-2">
            @if($requests->previousPageUrl())
                <a href="{{ $requests->previousPageUrl() }}" class="px-3 py-1.5 bg-[var(--surface)] border border-[var(--line)] rounded-lg text-xs font-bold text-[var(--text-muted)] hover:text-[var(--text)] hover:border-[var(--accent)] transition-all min-h-[44px] min-w-[44px] flex items-center justify-center">Previous</a>
            @endif
            @if($requests->nextPageUrl())
                <a href="{{ $requests->nextPageUrl() }}" class="px-3 py-1.5 bg-[var(--surface)] border border-[var(--line)] rounded-lg text-xs font-bold text-[var(--text-muted)] hover:text-[var(--text)] hover:border-[var(--accent)] transition-all min-h-[44px] min-w-[44px] flex items-center justify-center">Next</a>
            @endif
        </div>
    </div>
</div>
@endsection
