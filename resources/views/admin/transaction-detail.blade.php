@extends('layouts.admin')

@section('title', $transaction->transaction_id)
@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.transactions.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--surface-raised)]" aria-label="Back">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="text-xl font-semibold">Transaction {{ $transaction->transaction_id }}</h1>
                <p class="text-sm text-[var(--text-muted)]">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>
        <span @class(['px-3 py-1 rounded-full text-xs font-medium', 'bg-green-100 text-green-800' => in_array($transaction->status, ['approved', 'posted', 'credited']), 'bg-yellow-100 text-yellow-800' => in_array($transaction->status, ['pending_review', 'pending_match']), 'bg-red-100 text-red-800' => in_array($transaction->status, ['rejected', 'reversed', 'cancelled'])])>
            {{ $transaction->status instanceof \App\Enums\TransactionStatus ? $transaction->status->label() : ($transaction->status ?? '—') }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
                <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-4">Transaction details</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-[var(--text-muted)]">Type</span>
                        <span class="font-medium block">{{ $transaction->type instanceof \App\Enums\TransactionType ? $transaction->type->label() : ($transaction->type ?? '—') }}</span>
                    </div>
                    <div>
                        <span class="text-[var(--text-muted)]">Status</span>
                        <span class="font-medium block">{{ $transaction->status instanceof \App\Enums\TransactionStatus ? $transaction->status->label() : ($transaction->status ?? '—') }}</span>
                    </div>
                    <div>
                        <span class="text-[var(--text-muted)]">From account</span>
                        <span class="font-medium block">{{ $transaction->fromAccount?->name ?? $transaction->fromUser?->name ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-[var(--text-muted)]">To account</span>
                        <span class="font-medium block">{{ $transaction->toAccount?->name ?? $transaction->toUser?->name ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-[var(--text-muted)]">Amount</span>
                        <span class="font-medium block" style="font-variant-numeric: tabular-nums;">{{ number_format((float) $transaction->amount, $transaction->currency->decimals ?? 2) }} {{ $transaction->currency->symbol ?? '' }}</span>
                    </div>
                    <div>
                        <span class="text-[var(--text-muted)]">Fee</span>
                        <span class="font-medium block" style="font-variant-numeric: tabular-nums;">{{ number_format((float) ($transaction->fee ?? 0), $transaction->currency->decimals ?? 2) }} {{ $transaction->currency->symbol ?? '' }}</span>
                    </div>
                    <div>
                        <span class="text-[var(--text-muted)]">Net amount</span>
                        <span class="font-medium block" style="font-variant-numeric: tabular-nums;">{{ number_format((float) ($transaction->net_amount ?? 0), $transaction->currency->decimals ?? 2) }} {{ $transaction->currency->symbol ?? '' }}</span>
                    </div>
                    <div>
                        <span class="text-[var(--text-muted)]">Operator</span>
                        <span class="font-medium block">{{ $transaction->operator?->name ?? '—' }}</span>
                    </div>
                </div>
                @if($transaction->memo)
                    <div class="mt-4">
                        <span class="text-[var(--text-muted)]">Memo</span>
                        <p class="font-medium mt-1">{{ $transaction->memo }}</p>
                    </div>
                @endif
                @if($transaction->reference)
                    <div class="mt-4">
                        <span class="text-[var(--text-muted)]">Reference</span>
                        <p class="font-medium mt-1 font-mono text-xs">{{ $transaction->reference }}</p>
                    </div>
                @endif
            </div>

            @if($transaction->proof_file_path)
                <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
                    <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-4">Supporting document</h2>
                    @php
                        $proof = $transaction->proof_file_path;
                        $extension = strtolower(pathinfo($proof, PATHINFO_EXTENSION));
                    @endphp
                    @if(in_array($extension, ['jpg', 'jpeg', 'png']))
                        <img src="{{ asset('storage/' . $proof) }}" alt="Supporting document" class="max-w-full max-h-80 rounded-lg border border-[var(--line)]">
                    @else
                        <a href="{{ asset('storage/' . $proof) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-[var(--surface-raised)] border border-[var(--line)] rounded-lg text-sm font-medium hover:border-[var(--accent)] transition-all">
                            View document
                        </a>
                    @endif
                </div>
            @endif
        </div>

        <div class="space-y-6">
            <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
                <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-4">Actions</h2>
                <div class="space-y-3">
                    @if($transaction->isReversible())
                        <form method="POST" action="{{ route('admin.transactions.reverse', $transaction) }}">
                            @csrf
                            <input type="hidden" name="reason" value="Reversal by admin">
                            <button type="submit" class="w-full py-2.5 bg-[var(--text)] text-[var(--bg)] rounded-xl text-sm font-medium hover:opacity-90 transition-opacity" onclick="return confirm('Reverse this transaction? This will undo its balance changes.')">Reverse</button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('admin.transactions.destroy', $transaction) }}" onsubmit="return confirm('Delete this transaction? Balance will be reverted.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-2.5 bg-red-600 text-white rounded-xl text-sm font-medium hover:bg-red-700 transition-colors">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
