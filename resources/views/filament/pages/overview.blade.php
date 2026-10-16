<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="p-4 bg-[var(--surface-raised)] rounded-lg border border-[var(--line)]">
                <p class="text-sm text-[var(--text-muted)]">Total balance</p>
                <p class="text-2xl font-semibold text-[var(--text)]">{{ number_format((float) $systemBalance, 2) }}</p>
                <p class="text-xs text-[var(--text-muted)] mt-1">{{ \App\Models\Account::count() }} accounts</p>
            </div>
            <div class="p-4 bg-[var(--surface-raised)] rounded-lg border border-[var(--line)]">
                <p class="text-sm text-[var(--text-muted)]">Transactions today</p>
                <p class="text-2xl font-semibold text-[var(--text)]">{{ \App\Models\Transaction::whereDate('created_at', today())->count() }}</p>
                <p class="text-sm text-[var(--text-muted)] mt-1">{{ \App\Models\Transaction::whereDate('created_at', today())->whereIn('status', ['approved', 'credited'])->count() }} completed</p>
            </div>
            <div class="p-4 bg-[var(--surface-raised)] rounded-lg border border-[var(--line)]">
                <p class="text-sm text-[var(--text-muted)]">Pending approvals</p>
                <p class="text-2xl font-semibold text-[var(--text)]">{{ \App\Models\Transaction::whereIn('status', ['pending_review', 'pending_match'])->count() }}</p>
                <p class="text-xs text-[var(--text-muted)] mt-1">{{ \App\Models\Transaction::where('type', 'deposit_credit')->whereIn('status', ['pending_review', 'pending_match'])->count() }} deposits, {{ \App\Models\Transaction::where('type', 'payment_debit')->where('status', 'pending_review')->count() }} payments</p>
            </div>
            <div class="p-4 bg-[var(--surface-raised)] rounded-lg border border-[var(--line)]">
                <p class="text-sm text-[var(--text-muted)]">Anomalies</p>
                <p class="text-2xl font-semibold text-[var(--text)]">{{ count($anomalies) }}</p>
                <p class="text-xs text-[var(--text-muted)] mt-1">Requires attention</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-[var(--surface)] rounded-lg border border-[var(--line)] p-6">
                <h2 class="text-lg font-semibold text-[var(--text)] mb-4">Waiting for you</h2>
                <div class="space-y-3">
                    @forelse($pendingDeposits->merge($pendingPayments)->take(4) as $item)
                        <div class="flex items-center justify-between p-3 bg-[var(--line-soft)] rounded">
                            <div>
                                <p class="font-medium text-[var(--text)]">{{ $item->fromUser?->name ?? 'Unknown' }}</p>
                                <p class="text-sm text-[var(--text-muted)]">{{ $item->type->label() }} - {{ number_format((float) $item->amount, $item?->currency?->decimals ?? 2) }} {{ $item?->currency?->symbol ?? '' }}</p>
                            </div>
                            <a href="{{ route('admin.approvals', ['tab' => $item->type === \App\Enums\TransactionType::DepositCredit ? 'deposits' : 'payments']) }}"
                               class="text-sm font-medium text-[var(--accent)] hover:text-[var(--accent-dark)]">
                                Review
                            </a>
                        </div>
                    @empty
                        <p class="text-[var(--text-muted)]">Nothing pending right now.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-[var(--surface)] rounded-lg border border-[var(--line)] p-6">
                <h2 class="text-lg font-semibold text-[var(--text)] mb-4">System balance</h2>
                <p class="text-3xl font-semibold text-[var(--text)]">{{ number_format((float) $systemBalance, 2) }}</p>
                <p class="text-sm text-[var(--text-muted)] mt-1">{{ \App\Models\Account::count() }} accounts</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-[var(--surface)] rounded-lg border border-[var(--line)] p-6">
                <h2 class="text-lg font-semibold text-[var(--text)] mb-4">Needs a look</h2>
                <div class="space-y-3">
                    @forelse($anomalies as $anomaly)
                        <div class="flex items-center gap-2 p-3 bg-[var(--warning-bg)] rounded border border-[var(--warning)]/30">
                            <x-icon name="alert-triangle" class="w-5 h-5 text-[var(--warning)]" />
                            <span class="text-sm text-[var(--text)]">{{ $anomaly['message'] ?? 'Anomaly detected' }}</span>
                        </div>
                    @empty
                        <p class="text-[var(--text-muted)]">No anomalies detected.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-[var(--surface)] rounded-lg border border-[var(--line)] p-6">
                <h2 class="text-lg font-semibold text-[var(--text)] mb-4">Recent admin actions</h2>
                <div class="space-y-2">
                    @forelse($recentActions as $action)
                        <div class="flex items-center justify-between p-2 border-b border-[var(--line)] last:border-0">
                            <div>
                                <p class="text-sm text-[var(--text)]">{{ $action->action }}</p>
                                <p class="text-xs text-[var(--text-muted)]">{{ $action->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="text-xs text-[var(--text-muted)]">{{ $action->admin?->name ?? 'system' }}</span>
                        </div>
                    @empty
                        <p class="text-[var(--text-muted)]">No recent actions.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>

