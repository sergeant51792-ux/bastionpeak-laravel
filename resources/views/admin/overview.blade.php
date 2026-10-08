@extends('layouts.admin')

@section('title', 'Overview')
@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
            <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-1">Total customers</h2>
            <p class="text-3xl font-semibold" style="font-variant-numeric: tabular-nums;">{{ number_format(\App\Models\User::whereHas('roles', fn ($q) => $q->where('name', 'Customer'))->count()) }}</p>
            <p class="text-sm text-[var(--text-muted)] mt-1">active on the platform</p>
        </div>
        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
            <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-1">System balance</h2>
            <p class="text-3xl font-semibold" style="font-variant-numeric: tabular-nums;">{{ number_format((float) $systemBalance, 2) }} USD</p>
            <p class="text-sm text-[var(--text-muted)] mt-1">across {{ \App\Models\Account::count() }} accounts</p>
        </div>
        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
            <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-1">Needs attention</h2>
            <p class="text-3xl font-semibold" style="font-variant-numeric: tabular-nums;">{{ number_format($needsDecision) }}</p>
            <p class="text-sm text-[var(--text-muted)] mt-1">pending actions</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Pending deposits</h2>
                <a href="{{ route('admin.approvals') }}" class="text-sm text-[var(--accent)] hover:text-[var(--accent-dark)] font-medium">View all</a>
            </div>
            <div class="space-y-3">
                @forelse($pendingDeposits as $deposit)
                    @if($deposit->toAccount)
                    <a href="{{ route('admin.accounts.show', $deposit->toAccount) }}" class="flex items-center justify-between p-4 bg-[var(--surface-raised)] rounded-xl hover:bg-[var(--line)]/50 transition-colors">
                        <div>
                            <p class="font-medium">Deposit request</p>
                            <p class="text-sm text-[var(--text-muted)]">{{ $deposit->memo ?? 'No memo' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold">{{ number_format((float) $deposit->amount, $deposit->currency->decimals ?? 2) }} {{ $deposit->currency->symbol ?? '' }}</p>
                            <p class="text-xs text-[var(--text-muted)]">{{ $deposit->created_at->diffForHumans() }}</p>
                        </div>
                    </a>
                    @endif
                @empty
                    <p class="text-sm text-[var(--text-muted)]">No pending deposits.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">Pending payments</h2>
                <a href="{{ route('admin.approvals') }}" class="text-sm text-[var(--accent)] hover:text-[var(--accent-dark)] font-medium">View all</a>
            </div>
            <div class="space-y-3">
                @forelse($pendingPayments as $payment)
                    @if($payment->fromAccount)
                    <a href="{{ route('admin.accounts.show', $payment->fromAccount) }}" class="flex items-center justify-between p-4 bg-[var(--surface-raised)] rounded-xl hover:bg-[var(--line)]/50 transition-colors">
                        <div>
                            <p class="font-medium">Payment request</p>
                            <p class="text-sm text-[var(--text-muted)]">{{ $payment->memo ?? 'No memo' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold">{{ number_format((float) $payment->amount, $payment->currency->decimals ?? 2) }} {{ $payment->currency->symbol ?? '' }}</p>
                            <p class="text-xs text-[var(--text-muted)]">{{ $payment->created_at->diffForHumans() }}</p>
                        </div>
                    </a>
                    @endif
                @empty
                    <p class="text-sm text-[var(--text-muted)]">No pending payments.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
            <h2 class="text-lg font-semibold mb-4">Quick actions</h2>
            <div class="space-y-3">
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 p-4 bg-[var(--surface-raised)] rounded-xl hover:bg-[var(--line)]/50 transition-colors">
                    <x-icon name="help-circle" class="w-5 h-5 text-[var(--accent)]" />
                    <div>
                        <p class="font-medium">Review approvals</p>
                        <p class="text-sm text-[var(--text-muted)]">Approve or reject pending items</p>
                    </div>
                </a>
                <a href="{{ route('admin.transactions.index') }}" class="flex items-center gap-3 p-4 bg-[var(--surface-raised)] rounded-xl hover:bg-[var(--line)]/50 transition-colors">
                    <x-icon name="list" class="w-5 h-5 text-[var(--accent)]" />
                    <div>
                        <p class="font-medium">All transactions</p>
                        <p class="text-sm text-[var(--text-muted)]">Search and review activity</p>
                    </div>
                </a>
            </div>
        </div>

        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
            <h2 class="text-lg font-semibold mb-4">Recent admin actions</h2>
            <div class="space-y-3">
                @forelse($recentActions as $entry)
                    <div class="flex items-center justify-between py-2 border-b border-[var(--line)]">
                        <div>
                            <p class="text-sm">{{ $entry->action }}</p>
                            <p class="text-xs text-[var(--text-muted)]">{{ class_basename($entry->target_type) }} #{{ $entry->target_id }}</p>
                        </div>
                        <span class="text-xs text-[var(--text-muted)]">{{ $entry->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <p class="text-sm text-[var(--text-muted)]">No recent actions.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
