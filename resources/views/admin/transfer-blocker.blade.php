@extends('layouts.admin')

@section('title', 'Transfer Blocker')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold text-[var(--text)] tracking-tight">Transfer Blocker</h1>
            <p class="text-sm text-[var(--text-muted)] mt-1">Lock the transfer feature of a customer account with a custom message the customer will see when they try to transfer.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm font-medium bg-[var(--danger-bg)] text-[var(--danger)] border border-[var(--danger)]/30">
                <x-icon name="lock" class="w-4 h-4" />
                {{ $blockedUsers->count() }} account{{ $blockedUsers->count() === 1 ? '' : 's' }} blocked
            </span>
        </div>
    </div>

    @if(session('status'))
        <div class="flex items-center gap-3 p-4 bg-[var(--success-bg)] border border-[var(--success)]/30 rounded-xl text-sm font-medium text-[var(--success)]" role="status">
            <x-icon name="check" class="w-5 h-5 flex-shrink-0" />
            <span>{{ session('status') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 bg-[var(--danger-bg)] border border-[var(--danger)]/30 rounded-xl text-sm text-[var(--danger)]" role="alert">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
        {{-- Block form --}}
        <div class="lg:col-span-2">
            <form method="POST"
                  action="{{ route('admin.transfer-blocker.block') }}"
                  class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-5 space-y-4">
                @csrf

                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-[var(--danger-bg)] flex items-center justify-center text-[var(--danger)]">
                        <x-icon name="lock" class="w-5 h-5" />
                    </div>
                    <h2 class="font-semibold text-[var(--text)]">Lock transfers</h2>
                </div>

                <div>
                    <label for="user_id" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Customer account</label>
                    <select id="user_id" name="user_id" required
                            class="w-full px-4 py-2.5 bg-[var(--surface)] border border-[var(--line)] rounded-xl text-sm min-h-[44px]">
                        <option value="">— Choose a customer —</option>
                        @foreach($selectableUsers as $optionUser)
                            <option value="{{ $optionUser->id }}" @selected(old('user_id', $selectedUser?->id) == $optionUser->id)>
                                {{ $optionUser->name }} ({{ $optionUser->email }}){{ $optionUser->isTransferBlocked() ? ' — transfers locked' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-[var(--text-muted)] mt-1.5">Pick an account from the table below or search above.</p>
                </div>

                <div>
                    <label for="reason" class="block text-sm font-semibold text-[var(--text)] mb-1.5">
                        Customer message <span class="text-[var(--danger)]">*</span>
                    </label>
                    <textarea id="reason" name="reason" rows="4" maxlength="1000" required
                              placeholder="Explain to the customer why transfers are locked, e.g. Receipt of the requested wire fee is still pending."
                              class="w-full px-4 py-2.5 bg-[var(--surface)] border border-[var(--line)] rounded-xl text-sm resize-y">{{ old('reason', $selectedUser && $selectedUser->isTransferBlocked() ? $selectedUser->transfer_block_reason : '') }}</textarea>
                    <p class="text-xs text-[var(--text-muted)] mt-1.5">This message is shown to the customer on the transfer page and sent as a notification.</p>
                </div>

                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[var(--danger)] text-white rounded-xl text-sm font-semibold hover:opacity-90 transition-opacity min-h-[44px]">
                    <x-icon name="lock" class="w-4 h-4" />
                    Lock transfers for selected account
                </button>
            </form>
        </div>

        {{-- Blocked accounts --}}
        <div class="lg:col-span-3 space-y-3">
            <h2 class="font-semibold text-[var(--text)]">Locked accounts</h2>

            @forelse($blockedUsers as $blocked)
                <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="font-semibold text-[var(--text)] truncate">{{ $blocked->name }}</p>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-[var(--danger-bg)] text-[var(--danger)]">
                                    <x-icon name="lock" class="w-3 h-3" />
                                    Locked
                                </span>
                            </div>
                            <p class="text-xs text-[var(--text-muted)] truncate">{{ $blocked->email }}@if($blocked->account_number) &middot; {{ $blocked->account_number }}@endif</p>
                        </div>
                        <form method="POST" action="{{ route('admin.transfer-blocker.unblock', $blocked) }}" class="flex-shrink-0" onsubmit="return confirm('Unlock transfers for {{ $blocked->name }}?')">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 border border-[var(--line)] rounded-lg text-xs font-semibold text-[var(--text-muted)] hover:text-[var(--success)] hover:border-[var(--success)]/50 transition-colors min-h-[44px]">
                                <x-icon name="lock-open" class="w-4 h-4" />
                                Unlock
                            </button>
                        </form>
                    </div>
                    <div class="mt-3 p-3 bg-[var(--line-soft)] rounded-lg">
                        <p class="text-xs font-semibold text-[var(--text-muted)] mb-1">Customer message</p>
                        <p class="text-sm text-[var(--text)] whitespace-pre-line">{{ $blocked->transfer_block_reason }}</p>
                    </div>
                    <p class="text-xs text-[var(--text-muted)] mt-2">
                        Locked {{ $blocked->transfer_blocked_at?->format('d M Y, H:i') }}@if($blocked->transferBlocker) by {{ $blocked->transferBlocker->name }}@endif
                    </p>
                </div>
            @empty
                <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-8 text-center">
                    <div class="w-12 h-12 mx-auto rounded-full bg-[var(--success-bg)] flex items-center justify-center text-[var(--success)] mb-3">
                        <x-icon name="lock-open" class="w-6 h-6" />
                    </div>
                    <p class="text-sm font-medium text-[var(--text)]">No accounts are blocked</p>
                    <p class="text-xs text-[var(--text-muted)] mt-1">Use the form to lock transfers for a customer account.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Customer directory --}}
    <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl overflow-hidden">
        <div class="p-4 border-b border-[var(--line)] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h2 class="font-semibold text-[var(--text)]">Customer accounts</h2>
            <form method="GET" action="{{ route('admin.transfer-blocker.index') }}" class="flex gap-2 w-full sm:w-auto">
                <input type="search" name="search" value="{{ $search }}" placeholder="Search name, email, account no..."
                       class="px-4 py-2 bg-[var(--surface)] border border-[var(--line)] rounded-lg text-sm w-full sm:w-64 min-h-[44px]">
                <button type="submit" class="px-4 py-2 border border-[var(--line)] rounded-lg text-sm font-semibold text-[var(--text-muted)] hover:text-[var(--text)] hover:border-[var(--accent)] transition-colors min-h-[44px]">
                    Search
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[720px]">
                <thead class="bg-[var(--surface-raised)]">
                    <tr>
                        <th class="text-left px-4 py-3 font-semibold text-[var(--text-muted)]">Customer</th>
                        <th class="text-left px-4 py-3 font-semibold text-[var(--text-muted)]">Account no.</th>
                        <th class="text-left px-4 py-3 font-semibold text-[var(--text-muted)]">Account status</th>
                        <th class="text-left px-4 py-3 font-semibold text-[var(--text-muted)]">Transfers</th>
                        <th class="text-right px-4 py-3 font-semibold text-[var(--text-muted)]">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--line)]">
                    @forelse($users as $user)
                        <tr class="hover:bg-[var(--surface-raised)]/50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-[var(--text)]">{{ $user->name }}</p>
                                <p class="text-xs text-[var(--text-muted)]">{{ $user->email }}</p>
                            </td>
                            <td class="px-4 py-3 font-mono text-xs text-[var(--text-muted)]">{{ $user->account_number ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span @class(['px-2.5 py-1 rounded-full text-xs font-medium', 'bg-[var(--success-bg)] text-[var(--success)]' => $user->status === 'active', 'bg-[var(--accent-subtle)] text-[var(--accent)]' => $user->status === 'frozen', 'bg-[var(--danger-bg)] text-[var(--danger)]' => in_array($user->status, ['locked', 'blocked', 'suspended']), 'bg-[var(--line-soft)] text-[var(--text-muted)]' => ! in_array($user->status, ['active', 'frozen', 'locked', 'blocked', 'suspended'])])>
                                    {{ ucfirst($user->status ?? 'unknown') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if($user->isTransferBlocked())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-[var(--danger-bg)] text-[var(--danger)]">
                                        <x-icon name="lock" class="w-3 h-3" />
                                        Blocked
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-[var(--success-bg)] text-[var(--success)]">
                                        <x-icon name="check" class="w-3 h-3" />
                                        Active
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if($user->isTransferBlocked())
                                    <form method="POST" action="{{ route('admin.transfer-blocker.unblock', $user) }}" class="inline" onsubmit="return confirm('Unlock transfers for {{ $user->name }}?')">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 border border-[var(--line)] rounded-lg text-xs font-semibold text-[var(--text-muted)] hover:text-[var(--success)] hover:border-[var(--success)]/50 transition-colors min-h-[44px]">
                                            <x-icon name="lock-open" class="w-4 h-4" />
                                            Unlock
                                        </button>
                                    </form>
                                @else
                                    <button type="button" onclick="pickUser({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ addslashes($user->email) }}')"
                                            class="inline-flex items-center gap-1.5 px-3 py-2 border border-[var(--line)] rounded-lg text-xs font-semibold text-[var(--danger)] hover:bg-[var(--danger-bg)] hover:border-[var(--danger)]/30 transition-colors min-h-[44px]">
                                        <x-icon name="lock" class="w-4 h-4" />
                                        Block
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-[var(--text-muted)]">No customers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-4 py-4 border-t border-[var(--line)] flex items-center justify-between gap-4 text-sm text-[var(--text-muted)]">
                <span>Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}</span>
                <div class="flex gap-2">
                    @if($users->previousPageUrl())
                        <a href="{{ $users->previousPageUrl() }}" class="px-4 py-2 bg-[var(--surface)] border border-[var(--line)] rounded-lg text-xs font-bold hover:border-[var(--accent)] transition-all min-h-[44px] flex items-center">Previous</a>
                    @endif
                    @if($users->nextPageUrl())
                        <a href="{{ $users->nextPageUrl() }}" class="px-4 py-2 bg-[var(--surface)] border border-[var(--line)] rounded-lg text-xs font-bold hover:border-[var(--accent)] transition-all min-h-[44px] flex items-center">Next</a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function pickUser(userId, userName, userEmail) {
        const select = document.getElementById('user_id');
        let option = [...select.options].find(o => o.value == userId);
        if (!option) {
            option = new Option(userName + ' (' + userEmail + ')', userId);
            select.add(option);
        }
        select.value = userId;
        document.getElementById('reason').focus();
        document.getElementById('block-form').scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
</script>

<style>
    #block-form { scroll-margin-top: 16px; }
</style>
@endpush
@endsection
