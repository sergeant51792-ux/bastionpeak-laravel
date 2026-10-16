@extends('layouts.admin')

@section('title', 'Users')
@section('content')
    <div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
        <div class="flex items-center gap-3">
            <input type="search" placeholder="Search users..." class="px-4 py-2 bg-[var(--surface)] border border-[var(--line)] rounded-xl text-sm w-full sm:w-64 min-h-[44px]">
            <x-custom-select
                name="status"
                placeholder="All statuses"
                :options="[
                    '' => 'All statuses',
                    'pending' => 'Pending',
                    'active' => 'Active',
                    'frozen' => 'Frozen',
                    'locked' => 'Locked',
                    'closed' => 'Closed',
                ]"
            />
        </div>
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2.5 bg-[var(--primary)] text-white rounded-xl text-sm font-bold hover:bg-[var(--primary-dark)] transition-colors shadow-sm min-h-[44px] flex items-center justify-center">New user</a>
    </div>

    <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl overflow-x-auto -mx-4 lg:mx-0">
        <table class="w-full text-sm min-w-[600px]">
                <thead class="bg-[var(--surface-raised)]">
                    <tr>
                        <th class="px-4 py-3 text-left">Name</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">Accounts</th>
                        <th class="px-4 py-3 text-right">Total balance</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--line)]">
                    @forelse($users as $user)
                    @php
                        $accountCount = $user->accounts->count();
                        $totalBalance = 0;
                        foreach ($user->accounts as $acc) {
                            $rate = max((float) ($acc?->currency?->exchange_rate ?? 1), 0.0000000001);
                            $totalBalance += (float) $acc->balance * $rate;
                        }
                    @endphp
                    <tr class="hover:bg-[var(--surface-raised)]/50">
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.users.show', $user) }}" class="font-medium text-[var(--accent)] hover:text-[var(--accent-dark)] transition-colors">
                                {{ $user->name }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-[var(--text-muted)]">{{ $user->email }}</td>
                        <td class="px-4 py-3">{{ $accountCount }}</td>
                         <td class="px-4 py-3 text-right" style="font-variant-numeric: tabular-nums;">{{ number_format((float) $totalBalance, 2) }} USD</td>
                        <td class="px-4 py-3">
                            <span @class(['px-2.5 py-1 rounded-full text-xs font-medium', 'bg-[var(--success-bg)] text-[var(--success)]' => $user->status === 'active', 'bg-[var(--accent-subtle)] text-[var(--accent)]' => $user->status === 'frozen', 'bg-[var(--danger-bg)] text-[var(--danger)]' => $user->status === 'locked', 'bg-[var(--line-soft)] text-[var(--text-muted)]' => $user->status === 'closed', 'bg-[var(--warning-bg)] text-[var(--warning)]' => $user->status === 'pending'])>
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex gap-1 justify-end">
                                @if($user->status === 'pending')
                                    <form method="POST" action="{{ route('admin.users.approve', $user) }}" class="inline" onsubmit="return confirm('Approve this signup and activate their account?')">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-[var(--success)] text-white rounded-lg text-xs font-medium hover:opacity-90 transition-colors" aria-label="Approve signup">
                                            Approve
                                        </button>
                                    </form>
                                @elseif($user->status === 'active')
                                    <form method="POST" action="{{ route('admin.users.freeze', $user) }}" class="inline" onsubmit="return confirm('Freeze this user account?')">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-[var(--accent)] hover:bg-[var(--line-soft)] rounded min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Freeze">
                                            <x-icon name="pause" class="w-4 h-4" />
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.block', $user) }}" class="inline" onsubmit="return confirm('Block this user account?')" title="Block account">
                                        @csrf
                                        <input type="hidden" name="reason" value="Account locked">
                                        <button type="submit" class="p-1.5 text-[var(--danger)] hover:bg-[var(--danger-bg)] rounded min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Block">
                                            <x-icon name="lock" class="w-4 h-4" />
                                        </button>
                                    </form>
                                @elseif($user->status === 'frozen')
                                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="name" value="{{ $user->name }}">
                                        <input type="hidden" name="email" value="{{ $user->email }}">
                                        <input type="hidden" name="status" value="active">
                                        <button type="submit" class="p-1.5 text-[var(--success)] hover:bg-[var(--success-bg)] rounded min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Unfreeze">
                                            <x-icon name="play" class="w-4 h-4" />
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.block', $user) }}" class="inline" onsubmit="return confirm('Block this user account?')" title="Block account">
                                        @csrf
                                        <input type="hidden" name="reason" value="Account locked">
                                        <button type="submit" class="p-1.5 text-[var(--danger)] hover:bg-[var(--danger-bg)] rounded min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Block">
                                            <x-icon name="lock" class="w-4 h-4" />
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('admin.users.show', $user) }}" class="p-1.5 text-[var(--text-muted)] hover:bg-[var(--line-soft)] rounded min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Actions">
                                        <x-icon name="more-vertical" class="w-4 h-4" />
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-[var(--text-muted)]">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-[var(--text-muted)]">
        <span>Showing {{ $users->firstItem() ?? 0 }}â€“{{ $users->lastItem() ?? 0 }} of {{ $users->total() }}</span>
        <div class="flex gap-2">
            @if($users->previousPageUrl())
                <a href="{{ $users->previousPageUrl() }}" class="px-4 py-2.5 bg-[var(--surface)] border border-[var(--line)] rounded-lg text-xs font-bold text-[var(--text-muted)] hover:text-[var(--text)] hover:border-[var(--accent)] transition-all min-h-[44px] min-w-[44px] flex items-center justify-center">Previous</a>
            @endif
            @if($users->nextPageUrl())
                <a href="{{ $users->nextPageUrl() }}" class="px-4 py-2.5 bg-[var(--surface)] border border-[var(--line)] rounded-lg text-xs font-bold text-[var(--text-muted)] hover:text-[var(--text)] hover:border-[var(--accent)] transition-all min-h-[44px] min-w-[44px] flex items-center justify-center">Next</a>
            @endif
        </div>
    </div>
</div>
@endsection

