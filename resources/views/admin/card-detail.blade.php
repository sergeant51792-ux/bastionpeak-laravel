@extends('layouts.admin')

@section('title', $card->card_number_masked ? 'Card ****' . substr($card->card_number_masked, -4) : 'Card detail')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.cards.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--surface-raised)]" aria-label="Back">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="text-xl font-semibold">Card ****{{ $card->card_number_masked ? substr($card->card_number_masked, -4) : '0000' }}</h1>
                <p class="text-sm text-[var(--text-muted)]">{{ $card->user->name ?? '—' }} • {{ $card->account->name ?? '—' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($card->status === 'active')
                <form method="POST" action="{{ route('admin.cards.freeze', $card) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-[var(--surface)] border border-[var(--line)] rounded-xl text-sm font-bold text-[var(--accent)] hover:text-[var(--accent-dark)] hover:border-[var(--accent)] transition-all">Freeze</button>
                </form>
            @elseif($card->status === 'frozen')
                <form method="POST" action="{{ route('admin.cards.unfreeze', $card) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-[var(--success)] text-white rounded-xl text-sm font-bold hover:opacity-90 transition-opacity shadow-sm">Unfreeze</button>
                </form>
            @endif
            @if(!in_array($card->status, ['blocked', 'cancelled', 'cancelled', 'expired', 'closed', 'deactivated']))
                <form method="POST" action="{{ route('admin.cards.block', $card) }}" class="inline" onsubmit="return confirm('Block this card?')">
                    @csrf
                    <input type="hidden" name="reason" value="Blocked by admin">
                    <button type="submit" class="px-4 py-2 bg-[var(--surface)] border border-[var(--line)] rounded-xl text-sm font-bold text-red-600 hover:bg-red-50 transition-all">Block</button>
                </form>
            @endif
            <form method="POST" action="{{ route('admin.cards.cancel', $card) }}" class="inline" onsubmit="return confirm('Cancel this card?')">
                @csrf
                <input type="hidden" name="reason" value="Cancelled by admin">
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-xl text-sm font-bold hover:bg-red-700 transition-colors shadow-sm">Cancel card</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
            <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-4">Card info</h2>
            <div class="space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-[var(--text-muted)]">Card number</span>
                    <span class="font-mono">**** {{ $card->card_number_masked ? substr($card->card_number_masked, -4) : '0000' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[var(--text-muted)]">Type</span>
                    <span>{{ ucfirst($card->card_type ?? '—') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[var(--text-muted)]">Status</span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $card->status === 'active' ? 'bg-green-100 text-green-800' : ($card->status === 'frozen' ? 'bg-blue-100 text-blue-800' : ($card->status === 'blocked' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">{{ ucfirst($card->status ?? 'unknown') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[var(--text-muted)]">Valid from</span>
                    <span>{{ $card->valid_from ? $card->valid_from->format('d M Y') : '—' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[var(--text-muted)]">Valid to</span>
                    <span>{{ $card->valid_to ? $card->valid_to->format('d M Y') : '—' }}</span>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
            <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-4">Limits</h2>
            <div class="space-y-3">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-[var(--text-muted)]">Per transaction</span>
                    <span class="font-medium">${{ number_format((float) ($card->per_transaction_cap ?? 0), 2) }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-[var(--text-muted)]">Daily</span>
                    <span class="font-medium">${{ number_format((float) ($card->daily_cap ?? 0), 2) }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-[var(--text-muted)]">Monthly</span>
                    <span class="font-medium">${{ number_format((float) ($card->monthly_cap ?? 0), 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl overflow-hidden">
        <h2 class="text-sm font-semibold text-[var(--text-muted)] p-5 pb-3">Recent transactions</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[var(--surface-raised)]">
                    <tr>
                        <th class="px-4 py-2 text-left">Date</th>
                        <th class="px-4 py-2 text-left">Details</th>
                        <th class="px-4 py-2 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--line)]">
                    @forelse($transactions as $txn)
                        @php
                            $txnTypeLabel = $txn->type instanceof \App\Enums\TransactionType ? $txn->type->label() : '—';
                        @endphp
                        <tr class="hover:bg-[var(--surface-raised)]/50">
                            <td class="px-4 py-2">{{ $txn->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-4 py-2">{{ $txn->memo ?? $txnTypeLabel }}</td>
                            <td class="px-4 py-2 text-right" style="font-variant-numeric: tabular-nums;">{{ number_format((float) $txn->amount, $txn->currency->decimals ?? 2) }} {{ $txn->currency->symbol ?? '' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-4 py-6 text-center text-[var(--text-muted)]">No transactions.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[var(--line)]">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
