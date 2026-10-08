@extends('layouts.admin')

@section('title', $merchant->name)
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.merchants.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--surface-raised)]" aria-label="Back">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="text-xl font-semibold">{{ $merchant->name }}</h1>
                <p class="text-sm text-[var(--text-muted)]">{{ $merchant->category }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($merchant->status === 'active')
                <form method="POST" action="{{ route('admin.merchants.deactivate', $merchant) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl text-sm font-medium hover:bg-red-700 transition-colors">Deactivate</button>
                </form>
            @else
                <span class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-medium">Deactivated</span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
            <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-4">Merchant info</h2>
            <div class="space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-[var(--text-muted)]">Category</span>
                    <span>{{ $merchant->category }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[var(--text-muted)]">Receiving cap</span>
                    <span>${{ number_format((float) $merchant->receiving_cap, 2) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[var(--text-muted)]">Status</span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $merchant->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ ucfirst($merchant->status) }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[var(--text-muted)]">Pending payments</span>
                    <span>{{ $pendingPayments }}</span>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
            <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-4">Recent transactions</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-[var(--surface-raised)]">
                        <tr>
                            <th class="px-4 py-2 text-left">Date</th>
                            <th class="px-4 py-2 text-left">Type</th>
                            <th class="px-4 py-2 text-right">Amount</th>
                            <th class="px-4 py-2 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--line)]">
                        @forelse($transactions as $txn)
                            @php
                                $typeLabel = $txn->type instanceof \App\Enums\TransactionType ? $txn->type->label() : ($txn->type ?? '—');
                                $statusLabel = $txn->status instanceof \App\Enums\TransactionStatus ? $txn->status->label() : ($txn->status ?? '—');
                            @endphp
                            <tr class="hover:bg-[var(--surface-raised)]/50">
                                <td class="px-4 py-2">{{ $txn->created_at->format('d M Y, H:i') }}</td>
                                <td class="px-4 py-2">{{ $typeLabel }}</td>
                                <td class="px-4 py-2 text-right" style="font-variant-numeric: tabular-nums;">{{ number_format((float) $txn->amount, $txn->currency->decimals ?? 2) }} {{ $txn->currency->symbol ?? '' }}</td>
                                <td class="px-4 py-2">{{ $statusLabel }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-4 py-6 text-center text-[var(--text-muted)]">No transactions.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-[var(--line)]">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
