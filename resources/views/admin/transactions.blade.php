@extends('layouts.admin')

@section('title', 'Transactions')
@section('content')
<div class="space-y-6">
    <form method="GET" action="{{ route('admin.transactions.index') }}" class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4">
        <div class="flex flex-wrap items-center gap-3">
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Search transactions..." class="px-4 py-2.5 bg-[var(--surface)] border border-[var(--line)] rounded-xl text-sm w-48 sm:w-64 min-h-[44px]">
            <x-custom-select
                name="type"
                placeholder="All types"
                :options="[
                    '' => 'All types',
                    'credit' => 'Credit',
                    'debit' => 'Debit',
                    'deposit_credit' => 'Deposit credit',
                    'payment_debit' => 'Payment debit',
                ]"
            />
            <x-custom-select
                name="status"
                placeholder="All statuses"
                :options="[
                    '' => 'All statuses',
                    'pending_review' => 'In review',
                    'approved' => 'Successful',
                    'credited' => 'Successful',
                    'rejected' => 'Failed',
                    'reversed' => 'Failed',
                ]"
            />
            <button type="submit" class="px-4 py-2 bg-[var(--surface)] border border-[var(--line)] rounded-lg text-xs font-medium hover:bg-[var(--line-soft)] transition-colors min-h-[44px]">Apply</button>
        </div>
        <div class="flex gap-2 w-full sm:w-auto">
            <a href="{{ route('admin.transactions.export') }}" class="flex-1 px-4 py-2.5 bg-[var(--surface)] border border-[var(--line)] rounded-xl text-sm font-bold text-[var(--text-muted)] hover:text-[var(--text)] hover:border-[var(--accent)] transition-all min-h-[44px]">Export</a>
        </div>
    </form>

    <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl overflow-x-auto -mx-4 lg:mx-0">
        <table class="w-full text-sm min-w-[640px]">
            <thead class="bg-[var(--surface-raised)]">
                <tr>
                    <th class="px-4 py-3 text-left">ID</th>
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-left">From to To</th>
                    <th class="px-4 py-3 text-left">Type</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-right">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--line)]">
                @foreach($transactions as $txn)
                    @php
                        $fromName = optional($txn->fromAccount)->name ?? '-';
                        $toName = optional($txn->toAccount)->name ?? '-';
                        $status = $txn->status instanceof \App\Enums\TransactionStatus ? $txn->status->value : ($txn->status ?? '-');
                        $typeLabel = $txn->type instanceof \App\Enums\TransactionType ? $txn->type->label() : ($txn->type ?? '-');
                        $statusLabel = match ($status) {
                            'approved', 'credited', 'posted' => 'Successful',
                            'rejected', 'reversed' => 'Failed',
                            'pending_review', 'pending_match' => 'Pending',
                            default => ucfirst($status),
                        };
                    @endphp
                    <tr class="hover:bg-[var(--surface-raised)]/50 cursor-pointer">
                        <td class="px-4 py-3 font-mono text-xs">{{ $txn->transaction_id }}</td>
                        <td class="px-4 py-3">{{ $txn->created_at->format('d M') }}</td>
                        <td class="px-4 py-3">{{ $fromName }} to {{ $toName }}</td>
                        <td class="px-4 py-3">{{ $typeLabel }}</td>
                        <td class="px-4 py-3"><x-status-pill :status="$txn->status" :label="$statusLabel" /></td>
                        <td class="px-4 py-3 text-right" style="font-variant-numeric: tabular-nums;">
                            @php $amount = (float) $txn->amount; @endphp
                            @php $symbol = $txn?->currency?->symbol ?? ''; $decimals = $txn?->currency?->decimals ?? 2; @endphp
                            <span @class(['text-[var(--text)]', 'text-[var(--text-muted)]' => $amount < 0])>
                                {{ $amount >= 0 ? '+' : '-' }}{{ $symbol }}{{ number_format(abs($amount), $decimals) }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-[var(--text-muted)]">
        <span>Showing {{ $transactions->firstItem() ?? 0 }}â€“{{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }}</span>
        <div class="flex gap-2">
            @if($transactions->previousPageUrl())
                <a href="{{ $transactions->previousPageUrl() }}" class="px-4 py-2.5 bg-[var(--surface)] border border-[var(--line)] rounded-lg text-xs font-bold text-[var(--text-muted)] hover:text-[var(--text)] hover:border-[var(--accent)] transition-all min-h-[44px] min-w-[44px] flex items-center justify-center">Previous</a>
            @endif
            @if($transactions->nextPageUrl())
                <a href="{{ $transactions->nextPageUrl() }}" class="px-4 py-2.5 bg-[var(--surface)] border border-[var(--line)] rounded-lg text-xs font-bold text-[var(--text-muted)] hover:text-[var(--text)] hover:border-[var(--accent)] transition-all min-h-[44px] min-w-[44px] flex items-center justify-center">Next</a>
            @endif
        </div>
    </div>
</div>
@endsection

