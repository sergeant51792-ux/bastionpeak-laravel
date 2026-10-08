<x-filament::page>
    <div class="h-[calc(100vh-120px)] flex flex-col">
        <div class="flex items-center gap-4 mb-4">
            <div class="inline-flex rounded-lg bg-gray-100 dark:bg-gray-800 p-1">
                <button wire:click="$set('activeTab', 'deposits')"
                        @class(['px-4 py-2 rounded-md text-sm font-medium transition-colors', 'bg-white dark:bg-gray-700 shadow-sm' => $activeTab === 'deposits', 'text-gray-600 dark:text-gray-300 hover:text-gray-900' => $activeTab !== 'deposits'])>
                    Deposits {{ \App\Models\Transaction::where('type', 'deposit_credit')->whereIn('status', ['pending_review', 'pending_match'])->count() }}
                </button>
                <button wire:click="$set('activeTab', 'payments')"
                        @class(['px-4 py-2 rounded-md text-sm font-medium transition-colors', 'bg-white dark:bg-gray-700 shadow-sm' => $activeTab === 'payments', 'text-gray-600 dark:text-gray-300 hover:text-gray-900' => $activeTab !== 'payments'])>
                    Payments {{ \App\Models\Transaction::where('type', 'payment_debit')->where('status', 'pending_review')->count() }}
                </button>
            </div>
        </div>

        <div class="flex-1 grid grid-cols-1 lg:grid-cols-5 gap-4 min-h-0">
            <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="font-semibold">Queue</h2>
                </div>
                <div class="flex-1 overflow-y-auto">
                    @forelse($queue as $item)
                        <a href="{{ route('admin.approvals', array_merge(request()->query(), ['open' => $item->id])) }}"
                           @class(['block p-4 border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors', 'bg-primary-50 dark:bg-primary-900/20' => $openTransaction?->id === $item->id])>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium">{{ $item->fromUser?->name ?? 'Unknown' }}</p>
                                    <p class="text-sm text-gray-500">{{ $item->type->label() }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold">{{ $item->amount }}</p>
                                    <p class="text-xs text-gray-400">{{ $item->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center text-gray-500">No items in queue.</div>
                    @endforelse
                </div>
            </div>

            <div class="lg:col-span-3 bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col">
                @if($openTransaction)
                    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="text-lg font-semibold">{{ $openTransaction->type->label() }}</h2>
                                <p class="text-2xl font-semibold mt-1">{{ $openTransaction->amount }}</p>
                                <p class="text-sm text-gray-500">{{ $openTransaction->fromAccount?->name ?? 'External' }} → {{ $openTransaction->toAccount?->name ?? $openTransaction->toUser?->name ?? 'External' }}</p>
                            </div>
                            <span @class(['px-3 py-1 rounded-full text-sm font-medium', 'bg-yellow-100 text-yellow-800' => $openTransaction->status === \App\Enums\TransactionStatus::PendingReview, 'bg-blue-100 text-blue-800' => $openTransaction->status === \App\Enums\TransactionStatus::PendingMatch])>
                                {{ $openTransaction->status->label() }}
                            </span>
                        </div>
                    </div>
                    <div class="flex-1 overflow-y-auto p-6 space-y-6">
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Customer note</h3>
                            <p>{{ $openTransaction->memo ?? 'No note provided.' }}</p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">History</h3>
                            <p class="text-sm">Submitted {{ $openTransaction->created_at->format('d M Y, H:i') }}</p>
                            @if($openTransaction->reviewed_at)
                                <p class="text-sm">Reviewed {{ $openTransaction->reviewed_at->format('d M Y, H:i') }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 pt-4">
                            <form method="POST" action="{{ route('admin.approvals.reject', $openTransaction) }}" class="flex-1" wire:submit="reject({{ $openTransaction->id }})">
                                @csrf
                                <input type="hidden" name="reason" value="Rejected during review">
                                <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                    Reject
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.approvals.approve', $openTransaction) }}" class="flex-1" wire:submit="approve({{ $openTransaction->id }})">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                                    Approve and credit {{ $openTransaction->amount }}
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <div class="flex items-center justify-center h-full text-gray-400">
                        Select an item from the queue to review.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament::page>
