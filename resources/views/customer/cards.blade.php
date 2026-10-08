@extends('layouts.customer')

@section('title', 'Cards')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-bold text-[var(--text)] tracking-tight">Cards</h1>
            <p class="text-sm text-[var(--text-muted)] mt-1">Manage payment cards</p>
        </div>
    </div>

    <div class="space-y-6">
            @forelse($cards as $card)
            @php
                $statusColors = [
                    'active' => 'bg-green-100 text-green-800',
                    'frozen' => 'bg-blue-100 text-blue-800',
                    'blocked' => 'bg-red-100 text-red-800',
                    'cancelled' => 'bg-gray-100 text-gray-800',
                    'closed' => 'bg-gray-100 text-gray-800',
                ];
                $statusColor = $statusColors[$card->status] ?? 'bg-gray-100 text-gray-800';
                $isUsable = $card->status === 'active';
            @endphp
            <div class="relative">
                <div class="card-credit {{ $isUsable ? '' : 'opacity-60' }}">
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-6">
                            <div>
                                <p class="text-xs text-white/60 uppercase tracking-wider font-medium mb-0.5">Internal card</p>
                                <p class="font-bold text-lg text-white tracking-wide">BASTION PEAK</p>
                            </div>
                            <span class="text-sm font-semibold text-white/90 bg-white/10 px-3 py-1.5 rounded-lg backdrop-blur-sm border border-white/10">{{ $card->card_type ?? 'VISA' }}</span>
                        </div>
                        <p class="text-lg sm:text-xl font-mono tracking-[0.2em] mb-6 text-white">{{ $card->card_number_masked ?? '•••• •••• •••• 4821' }}</p>
                        <div class="flex items-end justify-between">
                            <div>
                                <p class="text-xs text-white/50 mb-0.5">Cardholder</p>
                                <p class="text-sm font-medium text-white">{{ optional($card->user)->name ?? '—' }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-white/50 mb-0.5">Expires</p>
                                <p class="text-sm font-medium text-white">Valid {{ $card->valid_to?->format('m/y') ?? '09/28' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="absolute -bottom-3 right-4">
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColor }}">{{ ucfirst($card->status ?? 'unknown') }}</span>
                </div>

                <div class="bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl mt-4">
                    <div class="p-5 space-y-0">
                        <div class="flex items-center justify-between py-3">
                            <span class="text-sm text-[var(--text-muted)]">Status</span>
                            <span class="text-sm font-medium text-[var(--text)]">{{ ucfirst($card->status ?? 'unknown') }}</span>
                        </div>
                        <hr class="border-[var(--line)]">
                        <div class="flex items-center justify-between py-3">
                            <span class="text-sm text-[var(--text-muted)]">Linked to</span>
                            <span class="text-sm font-medium text-[var(--text)]">{{ optional($card->account)->name ?? 'Main wallet' }}</span>
                        </div>
                        <hr class="border-[var(--line)]">
                        <div class="flex items-center justify-between py-3">
                            <span class="text-sm text-[var(--text-muted)]">Per-transaction limit</span>
                            <span class="text-sm font-semibold text-[var(--text)]">{{ number_format((float) ($card->per_transaction_cap ?? 500), 0) }}</span>
                        </div>
                        <hr class="border-[var(--line)]">
                        <div class="flex items-center justify-between py-3">
                            <span class="text-sm text-[var(--text-muted)]">Monthly limit</span>
                            <span class="text-sm font-semibold text-[var(--text)]">{{ number_format((float) ($card->monthly_cap ?? 2000), 0) }}</span>
                        </div>
                        <hr class="border-[var(--line)]">
                        <div class="flex items-center justify-between py-3">
                            <span class="text-sm text-[var(--text-muted)]">Allowed categories</span>
                            <span class="text-sm font-medium text-[var(--text)]">{{ $card->category_restrictions ? implode(', ', $card->category_restrictions) : 'Food, Office supplies' }}</span>
                        </div>
                        <hr class="border-[var(--line)]">
                        <div class="pt-3 flex gap-3">
                            @if($card->status === 'frozen')
                                <span class="text-xs text-blue-600 font-medium">Card is frozen</span>
                            @elseif($card->status === 'blocked')
                                <span class="text-xs text-red-600 font-medium">Card is blocked</span>
                            @elseif($card->status === 'active')
                                <span class="text-xs text-green-600 font-medium">Card is active</span>
                            @endif
                            <a href="{{ route('card.pin', $card) }}" class="text-xs font-semibold text-[var(--accent)] hover:text-[var(--accent-dark)] transition-colors">Manage PIN</a>
                            <a href="{{ route('messages', ['subject' => 'Card problem: ' . $card->card_number_masked]) }}" class="text-xs font-semibold text-[var(--accent)] hover:text-[var(--accent-dark)] transition-colors">Report problem</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state bg-white border border-[var(--line)] rounded-2xl">
                <x-icon name="file-text" class="w-12 h-12 mx-auto mb-3 text-[var(--text-muted)]" />
                <p class="text-sm text-[var(--text-muted)]">No cards issued yet.</p>
            </div>
        @endforelse

        @if(!$pendingRequest)
            <div class="bg-white border border-[var(--line)] rounded-2xl p-6">
                <h2 class="text-base font-bold text-[var(--text)] mb-1">Request a card</h2>
                <p class="text-sm text-[var(--text-muted)] mb-4">Submit a request and it will be reviewed shortly.</p>
                <form method="POST" action="{{ route('cards.request') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="account_id" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Account</label>
                        <x-custom-select
                            name="account_id"
                            :options="$accounts->mapWithKeys(fn($a) => [$a->id => $a->name . ' (' . $a->currency->symbol . ')' ])"
                            placeholder="Select account"
                            required
                        />
                    </div>
                    <div>
                        <label for="reason" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Reason</label>
                        <input type="text" id="reason" name="reason" required maxlength="255" class="input-field" placeholder="Why do you need this card?">
                    </div>
                    <button type="submit" class="w-full py-3 btn-primary">Submit request</button>
                </form>
            </div>
        @else
            <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                <h2 class="text-base font-bold text-[var(--text)] mb-1">Card request</h2>
                <p class="text-sm text-[var(--text-muted)]">A pending request for <span class="font-medium">{{ optional($pendingRequest->account)->name ?? '—' }}</span> is being reviewed.</p>
                <span @class(['badge mt-3 inline-block', 'bg-amber-50 text-amber-700 border border-amber-200' => $pendingRequest->status === 'pending', 'bg-green-50 text-green-700 border border-green-200' => $pendingRequest->status === 'approved', 'bg-red-50 text-red-700 border border-red-200' => $pendingRequest->status === 'rejected'])>
                    {{ ucfirst($pendingRequest->status) }}
                </span>
            </div>
        @endif
    </div>
</div>
@endsection
