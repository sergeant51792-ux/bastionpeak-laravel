@extends('layouts.customer')

@section('title', 'Transactions')
@section('content')
<div class="px-3 sm:px-4 lg:px-6 pt-4 space-y-5">
    <div>
        <h1 class="text-xl font-bold text-[var(--text)] tracking-tight">Transactions</h1>
        <p class="text-sm text-[var(--text-muted)] mt-0.5">Your account activity</p>
    </div>

    <div class="flex items-center justify-between gap-4 mb-4">
        <div class="flex gap-2 overflow-x-auto pb-1">
            @foreach(['all' => 'All', 'money_in' => 'Credits', 'money_out' => 'Debits', 'in_review' => 'Pending'] as $key => $label)
                @php $url = request()->fullUrlWithQuery(['filter' => $key, 'page' => null]); @endphp
                <a href="{{ $url }}" @class([
                    'px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-all min-h-[44px] flex items-center justify-center',
                    'bg-[var(--primary)] text-white shadow-sm' => $filter === $key,
                    'bg-[var(--line-soft)] text-[var(--text-muted)] border border-[var(--line)] hover:border-[var(--accent)] hover:text-[var(--text)]' => $filter !== $key,
                ])>
                    {{ $label }}
                </a>
            @endforeach
        </div>
        <button type="button" onclick="document.getElementById('statement-modal').classList.remove('hidden')" class="shrink-0 px-3 py-1.5 bg-[var(--accent-subtle)] text-[var(--accent)] rounded-lg text-sm font-medium hover:bg-[var(--accent-subtle)]/50 transition-colors min-h-[44px] flex items-center justify-center">
            <x-icon name="file-text" class="w-4 h-4 mr-1" />
            <span class="hidden sm:inline">Statement</span>
        </button>
    </div>

    {{-- Statement modal --}}
    <div id="statement-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl shadow-xl w-full max-w-md mx-4">
            <div class="p-5">
                <h2 class="text-lg font-semibold text-[var(--text)] mb-1">{{ __('Statement of account') }}</h2>
                <p class="text-xs text-[var(--text-muted)] mb-4">{{ __('Select a date range to download your account statement as a PDF.') }}</p>

                <form method="POST" action="{{ route('activity.statement') }}">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">{{ __('Start date') }}</label>
                            <input type="date" name="start_date" required class="input-field w-full" max="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">{{ __('End date') }}</label>
                            <input type="date" name="end_date" required class="input-field w-full" max="{{ now()->format('Y-m-d') }}">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">{{ __('Account (optional)') }}</label>
                            <select name="account_id" class="input-field w-full">
                                <option value="0">All accounts</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->currency->code }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-2 mt-5">
                        <button type="button" onclick="document.getElementById('statement-modal').classList.add('hidden')" class="flex-1 px-4 py-2.5 text-sm font-medium text-[var(--text-muted)] hover:text-[var(--text)] border border-[var(--line)] rounded-xl transition-colors min-h-[44px]">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2.5 bg-[var(--primary)] text-white rounded-xl text-sm font-medium hover:opacity-90 transition-opacity min-h-[44px]">
                            {{ __('Download PDF') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="border border-[var(--line)] rounded-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--line)]">
                        <th class="px-3 sm:px-4 py-3 text-left text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider">Transaction</th>
                        <th class="px-3 sm:px-4 py-3 text-left text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider hidden sm:table-cell">Date</th>
                        <th class="px-3 sm:px-4 py-3 text-right text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider">Amount</th>
                        <th class="px-3 sm:px-4 py-3 text-left text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider hidden sm:table-cell">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--line)]">
                    @forelse($transactions as $txn)
                         @php $isIncoming = $txn->isIncoming(); $tone = $txn->customerStatusTone(); @endphp
                         <tr class="transition-colors hover:bg-[var(--line-soft)] cursor-pointer" onclick="window.location='{{ route('activity.receipt', $txn) }}'">
                             <td class="px-3 sm:px-4 py-3">
                                 <p class="font-semibold text-[var(--text)] text-sm">{{ $txn->memo ?: $txn->type->customerLabel() }}</p>
                                 <p class="text-xs text-[var(--text-muted)] mt-0.5">{{ $txn->transaction_id ?? 'TXN-' . strtoupper(substr($txn->id, 0, 8)) }}</p>
                                 <p class="text-xs text-[var(--text-muted)] mt-0.5 sm:hidden tabular-nums">{{ $txn->created_at->format('d M Y, H:i') }}</p>
                             </td>
                             <td class="px-3 sm:px-4 py-3 text-sm text-[var(--text-muted)] hidden sm:table-cell whitespace-nowrap tabular-nums">{{ $txn->created_at->format('d M Y, H:i') }}</td>
                             <td class="px-3 sm:px-4 py-3 text-right font-semibold text-balance">
                                 @php
                                     $amountClass = match($tone) {
                                         'success' => 'text-[var(--success)]',
                                         'danger' => 'text-[var(--danger)]',
                                         'warning' => 'text-[var(--warning)]',
                                         default => 'text-[var(--text-muted)]',
                                     };
                                 @endphp
                                 <a href="{{ route('activity.receipt', $txn) }}" class="{{ $amountClass }}">
                                     {{ $isIncoming ? '+' : '−' }}{{ number_format((float) $txn->amount, $txn->currency->decimals ?? 2) }} {{ $txn->currency->symbol ?? '' }}
                                 </a>
                             </td>
                             <td class="px-3 sm:px-4 py-3 hidden sm:table-cell">
                                 <x-status-pill :status="$txn->status" :label="$txn->customerStatusLabel()" />
                             </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center">
                                <x-icon name="list-tree" class="w-10 h-10 mx-auto mb-3 text-[var(--text-muted)]" />
                                <p class="text-sm text-[var(--text-muted)]">No transactions yet.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transactions->hasPages())
            <div class="px-4 py-4 border-t border-[var(--line)]">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
