<x-filament::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{ \App\Widgets\StatsOverview::make()->render() }}
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold mb-4">Waiting for you</h2>
                <div class="space-y-3">
                    @forelse($pendingDeposits->merge($pendingPayments)->take(4) as $item)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded">
                            <div>
                                <p class="font-medium">{{ $item->fromUser?->name ?? 'Unknown' }}</p>
                                <p class="text-sm text-gray-500">{{ $item->type->label() }} — {{ $item->amount }}</p>
                            </div>
                            <a href="{{ route('admin.approvals', ['tab' => $item->type === \App\Enums\TransactionType::DepositCredit ? 'deposits' : 'payments']) }}"
                               class="text-sm font-medium text-primary-600 hover:text-primary-500">
                                Review
                            </a>
                        </div>
                    @empty
                        <p class="text-gray-500">Nothing pending right now.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold mb-4">System balance</h2>
                <p class="text-3xl font-semibold">{{ number_format((float) $systemBalance, 2) }}</p>
                <p class="text-sm text-gray-500 mt-1">{{ \App\Models\Account::count() }} accounts</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold mb-4">Needs a look</h2>
                <div class="space-y-3">
                    @forelse($anomalies as $anomaly)
                        <div class="flex items-center gap-2 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded border border-yellow-200 dark:border-yellow-700">
                            <x-icon name="alert-triangle" class="w-5 h-5 text-yellow-600" />
                            <span class="text-sm">{{ $anomaly['message'] ?? 'Anomaly detected' }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500">No anomalies detected.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
                <h2 class="text-lg font-semibold mb-4">Recent admin actions</h2>
                <div class="space-y-2">
                    @forelse($recentActions as $action)
                        <div class="flex items-center justify-between p-2 border-b border-gray-100 last:border-0">
                            <div>
                                <p class="text-sm">{{ $action->action }}</p>
                                <p class="text-xs text-gray-500">{{ $action->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="text-xs text-gray-400">{{ $action->admin?->name ?? 'system' }}</span>
                        </div>
                    @empty
                        <p class="text-gray-500">No recent actions.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-filament::page>
