@extends('layouts.customer')

@section('title', 'Cards')
@section('content')
<div class="px-3 sm:px-4 lg:px-6 pt-4 space-y-6">
    <div>
        <h1 class="text-xl font-bold text-[var(--text)] tracking-tight">Cards</h1>
        <p class="text-sm text-[var(--text-muted)] mt-0.5">Manage your payment cards</p>
    </div>

    <div class="space-y-6">
        @forelse($cards as $card)
            @php
                $tone = match ($card->status) {
                    'active' => 'success',
                    'frozen' => 'info',
                    'blocked' => 'danger',
                    default => 'neutral',
                };
                $toneClass = match ($tone) {
                    'success' => 'badge-success',
                    'info' => 'badge-info',
                    'danger' => 'badge-danger',
                    default => 'badge-neutral',
                };
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
                                <p class="text-sm font-medium text-white">{{ $card->valid_to?->format('m/y') ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="absolute -bottom-3 right-4">
                    <span class="badge {{ $toneClass }}">{{ ucfirst($card->status ?? 'unknown') }}</span>
                </div>

                <div class="bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl mt-4">
                    <div class="p-5">
                        <dl class="divide-y divide-[var(--line)]">
                            <div class="flex items-center justify-between py-3">
                                <dt class="text-sm text-[var(--text-muted)]">Status</dt>
                                <dd class="text-sm font-medium text-[var(--text)]">{{ ucfirst($card->status ?? 'unknown') }}</dd>
                            </div>
                            <div class="flex items-center justify-between py-3">
                                <dt class="text-sm text-[var(--text-muted)]">Linked to</dt>
                                <dd class="text-sm font-medium text-[var(--text)]">{{ optional($card->account)->name ?? 'Primary account' }}</dd>
                            </div>
                            <div class="flex items-center justify-between py-3">
                                <dt class="text-sm text-[var(--text-muted)]">Per-transaction limit</dt>
                                <dd class="text-sm font-semibold text-[var(--text)] text-balance">{{ number_format((float) ($card->per_transaction_cap ?? 500), 0) }}</dd>
                            </div>
                            <div class="flex items-center justify-between py-3">
                                <dt class="text-sm text-[var(--text-muted)]">Monthly limit</dt>
                                <dd class="text-sm font-semibold text-[var(--text)] text-balance">{{ number_format((float) ($card->monthly_cap ?? 2000), 0) }}</dd>
                            </div>
                            <div class="flex items-center justify-between py-3">
                                <dt class="text-sm text-[var(--text-muted)]">Allowed categories</dt>
                                <dd class="text-sm font-medium text-[var(--text)] text-right">{{ $card->category_restrictions ? implode(', ', $card->category_restrictions) : 'Food, Office supplies' }}</dd>
                            </div>
                        </dl>
                        <div class="pt-4 flex flex-wrap gap-4">
                            @if($card->status === 'frozen')
                                <span class="text-xs font-medium text-[var(--accent)]">Card is frozen</span>
                            @elseif($card->status === 'blocked')
                                <span class="text-xs font-medium" style="color: var(--danger);">Card is blocked</span>
                            @elseif($card->status === 'active')
                                <span class="text-xs font-medium" style="color: var(--success);">Card is active</span>
                            @endif
                            <a href="{{ route('card.pin', $card) }}" class="text-xs font-semibold text-[var(--accent)] hover:text-[var(--accent-dark)] transition-colors">Manage PIN</a>
                            <a href="{{ route('messages', ['subject' => 'Card problem: ' . $card->card_number_masked]) }}" class="text-xs font-semibold text-[var(--accent)] hover:text-[var(--accent-dark)] transition-colors">Report problem</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl">
                <x-icon name="credit-card" class="w-10 h-10 mx-auto mb-3 text-[var(--text-muted)]" />
                <p class="text-sm text-[var(--text-muted)]">No cards issued yet.</p>
            </div>
        @endforelse

        @if(!$pendingRequest)
            <div class="bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
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
                    <button type="submit" class="w-full py-3 btn-primary min-h-[44px]">Submit request</button>
                </form>
            </div>
        @else
            <div class="bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-5">
                <h2 class="text-base font-bold text-[var(--text)] mb-1">Card request</h2>
                <p class="text-sm text-[var(--text-muted)]">A pending request for <span class="font-medium text-[var(--text)]">{{ optional($pendingRequest->account)->name ?? '—' }}</span> is being reviewed.</p>
                <div class="mt-3">
                    <x-status-pill :status="$pendingRequest->status" />
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
