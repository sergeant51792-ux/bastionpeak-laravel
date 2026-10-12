@extends('layouts.customer')

@section('title', 'Transactions')
@section('content')
<div class="px-3 sm:px-4 lg:px-6 pt-4 space-y-5">
    <div>
        <h1 class="text-xl font-bold text-[var(--text)] tracking-tight">Transactions</h1>
        <p class="text-sm text-[var(--text-muted)] mt-0.5">Your account activity</p>
    </div>

    <div class="flex gap-2 overflow-x-auto pb-1">
        @foreach(['all' => 'All', 'money_in' => 'Credits', 'money_out' => 'Debits', 'in_review' => 'Pending'] as $key => $label)
            @php $url = request()->fullUrlWithQuery(['filter' => $key, 'page' => null]); @endphp
            <a href="{{ $url }}" @class([
                'px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-all min-h-[44px] flex items-center justify-center',
                'bg-[var(--primary)] text-white shadow-sm' => $filter === $key,
                'bg-white dark:bg-[var(--surface)] text-[var(--text-muted)] border border-[var(--line)] hover:border-[var(--accent)] hover:text-[var(--text)]' => $filter !== $key,
            ])>
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl overflow-hidden">
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
                        @php $isIncoming = $txn->isIncoming(); @endphp
                        <tr class="hover:bg-[var(--line-soft)] transition-colors">
                            <td class="px-3 sm:px-4 py-3">
                                <p class="font-semibold text-[var(--text)] text-sm">{{ $txn->memo ?: $txn->type->customerLabel() }}</p>
                                <p class="text-xs text-[var(--text-muted)] mt-0.5">{{ $txn->transaction_id ?? 'TXN-' . strtoupper(substr($txn->id, 0, 8)) }}</p>
                            </td>
                            <td class="px-3 sm:px-4 py-3 text-sm text-[var(--text-muted)] hidden sm:table-cell">{{ $txn->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-3 sm:px-4 py-3 text-right font-semibold text-balance">
                                <span class="{{ $isIncoming ? 'text-[var(--success)]' : 'text-[var(--text)]' }}">
                                    {{ $isIncoming ? '+' : '−' }}{{ number_format((float) $txn->amount, $txn->currency->decimals ?? 2) }} {{ $txn->currency->symbol ?? '' }}
                                </span>
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
