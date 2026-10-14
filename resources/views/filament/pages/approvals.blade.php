<x-filament-panels::page>
    <div class="h-[calc(100vh-120px)] flex flex-col">
        <div class="flex items-center gap-4 mb-4">
                <div class="inline-flex rounded-lg bg-[var(--line-soft)] p-1">
                 <button wire:click="$set('activeTab', 'deposits'); $navigate('?tab=deposits')"
                         @class(['px-4 py-2 rounded-md text-sm font-medium transition-colors', 'bg-[var(--surface)] shadow-sm' => $activeTab === 'deposits', 'text-[var(--text-muted)]' => $activeTab !== 'deposits'])>
                     Deposits {{ \App\Models\Transaction::where('type', 'deposit_credit')->whereIn('status', ['pending_review', 'pending_match'])->count() }}
                 </button>
                 <button wire:click="$set('activeTab', 'payments'); $navigate('?tab=payments')"
                         @class(['px-4 py-2 rounded-md text-sm font-medium transition-colors', 'bg-[var(--surface)] shadow-sm' => $activeTab === 'payments', 'text-[var(--text-muted)]' => $activeTab !== 'payments'])>
                     Payments {{ \App\Models\Transaction::where('type', 'payment_debit')->where('status', 'pending_review')->count() }}
                 </button>
             </div>
        </div>

        <div class="flex-1 grid grid-cols-1 lg:grid-cols-5 gap-4 min-h-0" wire:poll="refreshQueue">
            <div class="lg:col-span-2 bg-[var(--surface)] rounded-lg border border-[var(--line)] overflow-hidden flex flex-col">
                <div class="p-4 border-b border-[var(--line)] flex items-center justify-between">
                    <h2 class="font-semibold text-[var(--text)]">Queue</h2>
                    <span class="text-xs text-[var(--text-muted)]">{{ \Carbon\Carbon::now()->format('H:i') }}</span>
                </div>
                </div>
                <div class="flex-1 overflow-y-auto">
                    @forelse($queue as $item)
                        <a href="{{ request()->url() }}?tab={{ $activeTab }}&open={{ $item->id }}"
                           @class(['block p-4 border-b border-[var(--line)] hover:bg-[var(--line-soft)] transition-colors', 'bg-[var(--accent-subtle)]' => $openTransaction?->id === $item->id])>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-medium text-[var(--text)]">{{ $item->fromUser?->name ?? 'Unknown' }}</p>
                                    <p class="text-sm text-[var(--text-muted)]">{{ $item->type->label() }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-[var(--text)]">{{ $item->amount }}</p>
                                    <p class="text-xs text-[var(--text-muted)]">{{ $item->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center text-[var(--text-muted)]">No items in queue.</div>
                    @endforelse
                </div>
            </div>

            <div class="lg:col-span-3 bg-[var(--surface)] rounded-lg border border-[var(--line)] overflow-hidden flex flex-col">
                @if($openTransaction)
                    <div class="p-6 border-b border-[var(--line)]">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="text-lg font-semibold text-[var(--text)]">{{ $openTransaction->type->label() }}</h2>
                                <p class="text-2xl font-semibold mt-1 text-[var(--text)]">{{ $openTransaction->amount }}</p>
                                <p class="text-sm text-[var(--text-muted)]">{{ $openTransaction->fromAccount?->name ?? 'External' }} → {{ $openTransaction->toAccount?->name ?? $openTransaction->toUser?->name ?? 'External' }}</p>
                            </div>
                            <span @class(['px-3 py-1 rounded-full text-sm font-medium', 'bg-[var(--warning-bg)] text-[var(--warning)]' => $openTransaction->status === \App\Enums\TransactionStatus::PendingReview, 'bg-[var(--accent-subtle)] text-[var(--accent)]' => $openTransaction->status === \App\Enums\TransactionStatus::PendingMatch])>
                                {{ $openTransaction->status->label() }}
                            </span>
                        </div>
                    </div>
                    <div class="flex-1 overflow-y-auto p-6 space-y-6">
                        <div class="bg-[var(--line-soft)] rounded-lg p-4">
                            <h3 class="text-sm font-medium text-[var(--text-muted)] mb-2">Customer note</h3>
                            <p class="text-[var(--text)]">{{ $openTransaction->memo ?? 'No note provided.' }}</p>
                        </div>
                        <div class="bg-[var(--line-soft)] rounded-lg p-4">
                            <h3 class="text-sm font-medium text-[var(--text-muted)] mb-2">History</h3>
                            <p class="text-sm text-[var(--text)]">Submitted {{ $openTransaction->created_at->format('d M Y, H:i') }}</p>
                            @if($openTransaction->reviewed_at)
                                <p class="text-sm text-[var(--text-muted)]">Reviewed {{ $openTransaction->reviewed_at->format('d M Y, H:i') }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 pt-4">
                            <form wire:submit="reject({{ $openTransaction->id }})" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-[var(--danger)] text-white rounded-lg hover:opacity-90 transition-colors">
                                    Reject
                                </button>
                            </form>
                        <form wire:submit="approve({{ $openTransaction->id }})" class="flex-1">
                            @csrf
                            <button type="submit" class="w-full px-4 py-2 bg-[var(--primary)] text-white rounded-lg hover:opacity-90 transition-colors">
                                @if($openTransaction->type === \App\Enums\TransactionType::DepositCredit)
                                    Approve and credit {{ $openTransaction->amount }}
                                @else
                                    Approve {{ $openTransaction->amount }}
                                @endif
                            </button>
                        </form>
                        </div>
                    </div>
                @else
                    <div class="flex items-center justify-center h-full text-[var(--text-muted)]">
                        Select an item from the queue to review.
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
