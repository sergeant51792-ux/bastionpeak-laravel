@extends('layouts.customer')

@section('title', 'Transactions')
@section('content')
<div class="space-y-6">
    <h1 class="text-xl font-bold text-[var(--text)] tracking-tight">Transactions</h1>

    <div class="flex gap-2 overflow-x-auto pb-1">
        @foreach(['all' => 'All', 'money_in' => 'Credits', 'money_out' => 'Debits', 'in_review' => 'Pending'] as $key => $label)
            @php
                $url = request()->fullUrlWithQuery(['filter' => $key, 'page' => null]);
            @endphp
            <a href="{{ $url }}" @class(['px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap transition-all min-h-[44px] flex items-center justify-center', 'bg-[var(--primary)] text-white shadow-sm' => $filter === $key, 'bg-white text-[var(--text-muted)] border border-[var(--line)] hover:border-[var(--accent)] hover:text-[var(--text)]' => $filter !== $key])>
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="bg-white border border-[var(--line)] rounded-2xl overflow-hidden">
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
                        <tr class="hover:bg-[var(--line-soft)] transition-colors">
                            <td class="px-3 sm:px-4 py-3">
                                <p class="font-semibold text-[var(--text)] text-sm">{{ $txn->memo ?? $txn->type->label() }}</p>
                                <p class="text-xs text-[var(--text-muted)] mt-0.5">{{ $txn->transaction_id ?? 'TXN-' . strtoupper(substr($txn->id, 0, 8)) }}</p>
                            </td>
                            <td class="px-3 sm:px-4 py-3 text-sm text-[var(--text-muted)] hidden sm:table-cell">{{ $txn->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-3 sm:px-4 py-3 text-right font-semibold text-balance">
                                <span class="{{ in_array($txn->type->value, ['deposit_credit', 'refund']) ? 'text-emerald-600' : 'text-[var(--text)]' }}">
                                    {{ in_array($txn->type->value, ['deposit_credit', 'refund']) ? '+' : '-' }}{{ number_format((float) $txn->amount, $txn->currency->decimals ?? 2) }} {{ $txn->currency->symbol ?? '' }}
                                </span>
                            </td>
                            <td class="px-3 sm:px-4 py-3 hidden sm:table-cell">
                                <span @class(['badge', 'bg-emerald-50 text-emerald-700 border border-emerald-200' => $txn->status === \App\Enums\TransactionStatus::Credited || $txn->status === \App\Enums\TransactionStatus::Approved, 'bg-amber-50 text-amber-700 border border-amber-200' => $txn->status === \App\Enums\TransactionStatus::PendingReview || $txn->status === \App\Enums\TransactionStatus::PendingMatch, 'bg-red-50 text-red-700 border border-red-200' => $txn->status === \App\Enums\TransactionStatus::Rejected || $txn->status === \App\Enums\TransactionStatus::Reversed])>
                                    {{ $txn->status->label() }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-12 text-center text-[var(--text-muted)]">No transactions yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-4 py-4 border-t border-[var(--line)]">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
