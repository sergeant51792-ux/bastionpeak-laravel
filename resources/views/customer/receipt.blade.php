@extends('layouts.customer')

{{--
    Transaction receipt.
    Every value is read from the live transaction record, so the status line,
    the amount direction and the "processed at" stamp all update on their own
    when the transaction moves from pending to credited / debited.
--}}
@section('title', __('Receipt'))

@php
    $decimals = $transaction->currency?->decimals ?? 2;
    $symbol = $transaction->currency?->symbol ?? '';
    $amount = number_format((float) $transaction->amount, $decimals);
    $fee = number_format((float) $transaction->fee, $decimals);
    $net = number_format((float) ($transaction->net_amount ?? $transaction->amount), $decimals);
    $meta = is_array($metadata) ? $metadata : [];

    $statusEnum = $transaction->status instanceof \App\Enums\TransactionStatus
        ? $transaction->status
        : \App\Enums\TransactionStatus::PendingReview;

    // Customer-facing status wording. A single source of truth per status so the
    // badge, the headline and the timeline can never disagree.
    $statusMap = [
        'pending_review' => ['label' => 'Pending', 'tone' => 'pending', 'note' => 'This transaction is awaiting review. It has not been applied to your balance yet.'],
        'pending_match'  => ['label' => 'Pending', 'tone' => 'pending', 'note' => 'This transaction is awaiting review. It has not been applied to your balance yet.'],
        'approved'       => ['label' => 'Approved', 'tone' => 'success', 'note' => 'This transaction has been approved and processed.'],
        'credited'       => ['label' => 'Credited', 'tone' => 'success', 'note' => 'The funds have been credited to the receiving account.'],
        'posted'         => ['label' => 'Completed', 'tone' => 'success', 'note' => 'This transaction has been completed successfully.'],
        'rejected'       => ['label' => 'Failed', 'tone' => 'danger', 'note' => 'This transaction was rejected. No funds moved.'],
        'reversed'       => ['label' => 'Reversed', 'tone' => 'warning', 'note' => 'This transaction was reversed.'],
    ];
    $statusInfo = $statusMap[$statusEnum->value] ?? ['label' => ucfirst($statusEnum->value), 'tone' => 'pending', 'note' => ''];

    $toneClasses = [
        'pending' => 'bg-[var(--warning-bg)] text-[var(--warning)] border-[var(--warning)]/30',
        'success' => 'bg-[var(--success-bg)] text-[var(--success)] border-[var(--success)]/30',
        'danger'  => 'bg-[var(--danger-bg)] text-[var(--danger)] border-[var(--danger)]/30',
        'warning' => 'bg-[var(--warning-bg)] text-[var(--warning)] border-[var(--warning)]/30',
    ];
    $badgeClass = $toneClasses[$statusInfo['tone']] ?? $toneClasses['pending'];

    // Direction wording: a deposit is money in, a payment is money out.
    $isDeposit = in_array($transaction->type, [
        \App\Enums\TransactionType::DepositCredit,
        \App\Enums\TransactionType::Credit,
        \App\Enums\TransactionType::Refund,
    ], true);

    $typeLabel = $transaction->type instanceof \App\Enums\TransactionType
        ? $transaction->type->customerLabel()
        : 'Transaction';

    $origin = $transaction->fromAccount?->name ?: ($meta['bank_name'] ?? null);
    $destination = $transaction->toAccount?->name ?: ($transaction->fromUser?->name ?? null);

    // Determine whether the money left or arrived on the customer's side.
    $creditedAt = $transaction->credited_at ?: ($transaction->reviewed_at ?? null);
    $settledAt = match ($statusEnum->value) {
        'credited', 'posted', 'approved' => $creditedAt ?: $transaction->updated_at,
        'rejected'                       => $transaction->rejected_at ?: $transaction->updated_at,
        default                          => null,
    };
@endphp

@section('content')
<div class="px-3 sm:px-4 lg:px-6 pt-4 pb-10 space-y-5 max-w-2xl mx-auto">
    <div class="flex items-center justify-between">
        <header class="flex items-center gap-3">
            <a href="{{ route('activity') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--line-soft)] transition-colors" aria-label="Back">
                <x-icon name="arrow-left" class="w-5 h-5 text-[var(--text-muted)]" />
            </a>
            <h1 class="text-lg font-semibold text-[var(--text)]">{{ __('Transaction receipt') }}</h1>
        </header>
        <a href="{{ route('activity.receipt.download', $transaction) }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--line-soft)] transition-colors" aria-label="{{ __('Download PDF') }}">
            <x-icon name="download" class="w-5 h-5 text-[var(--text-muted)]" />
        </a>
    </div>

    <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl overflow-hidden">
        {{-- Brand + status --}}
        <div class="p-5 border-b border-[var(--line)] flex items-start justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] shrink-0">
                    <x-icon name="landmark" class="w-5 h-5" />
                </div>
                <div>
                    <p class="text-xs text-[var(--text-muted)] uppercase tracking-wider font-semibold">Bastion Peak</p>
                    <p class="text-sm font-semibold text-[var(--text)]">{{ $typeLabel }}</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border text-xs font-semibold shrink-0 {{ $badgeClass }}">
                @if($statusInfo['tone'] === 'pending')
                    <x-icon name="clock" class="w-3.5 h-3.5" />
                @elseif($statusInfo['tone'] === 'success')
                    <x-icon name="check" class="w-3.5 h-3.5" />
                @else
                    <x-icon name="x" class="w-3.5 h-3.5" />
                @endif
                {{ $statusInfo['label'] }}
            </span>
        </div>

        {{-- Amount --}}
        <div class="px-5 py-7 text-center border-b border-[var(--line)]">
            <p class="text-xs text-[var(--text-muted)] mb-1.5">
                {{ $isDeposit ? __('Received') : __('Sent') }}
            </p>
            <p class="text-3xl sm:text-4xl font-bold tracking-tight {{ $isDeposit ? 'text-[var(--success)]' : 'text-[var(--text)]' }}">
                {{ $isDeposit ? '+' : '−' }}{{ $amount }} <span class="text-lg font-semibold">{{ $symbol }}</span>
            </p>
            <p class="text-xs text-[var(--text-muted)] mt-2">
                {{ $transaction->currency?->code ?? 'USD' }}
                @if((float) $transaction->fee > 0)
                    &middot; {{ __('Fee') }} {{ $fee }} {{ $symbol }}
                @endif
            </p>
        </div>

        {{-- Status explanation --}}
        @if($statusInfo['note'])
            <div class="px-5 py-3.5 border-b border-[var(--line)] bg-[var(--surface-raised)]">
                <p class="text-xs text-[var(--text-muted)] leading-relaxed">{{ $statusInfo['note'] }}</p>
            </div>
        @endif

        {{-- Details --}}
        <dl class="divide-y divide-[var(--line)]">
            <div class="flex items-baseline justify-between gap-4 px-5 py-3.5">
                <dt class="text-xs text-[var(--text-muted)] shrink-0">{{ __('Reference') }}</dt>
                <dd class="text-sm font-semibold text-[var(--text)] text-right font-mono break-all">{{ $transaction->transaction_id ?? 'TXN-' . strtoupper(substr((string) $transaction->id, 0, 8)) }}</dd>
            </div>

            @if($transaction->reference)
                <div class="flex items-baseline justify-between gap-4 px-5 py-3.5">
                    <dt class="text-xs text-[var(--text-muted)] shrink-0">{{ __('Payment reference') }}</dt>
                    <dd class="text-sm text-[var(--text)] text-right font-mono break-all">{{ $transaction->reference }}</dd>
                </div>
            @endif

            <div class="flex items-baseline justify-between gap-4 px-5 py-3.5">
                <dt class="text-xs text-[var(--text-muted)] shrink-0">{{ __('Date &amp; time') }}</dt>
                <dd class="text-sm text-[var(--text)] text-right tabular-nums">{{ $transaction->created_at->format('j M Y, H:i') }}</dd>
            </div>

            @if($settledAt)
                <div class="flex items-baseline justify-between gap-4 px-5 py-3.5">
                    <dt class="text-xs text-[var(--text-muted)] shrink-0">{{ __('Processed at') }}</dt>
                    <dd class="text-sm text-[var(--text)] text-right tabular-nums">{{ $settledAt->format('j M Y, H:i') }}</dd>
                </div>
            @endif

            <div class="flex items-baseline justify-between gap-4 px-5 py-3.5">
                <dt class="text-xs text-[var(--text-muted)] shrink-0">{{ __('Type') }}</dt>
                <dd class="text-sm text-[var(--text)] text-right">{{ $transaction->type instanceof \App\Enums\TransactionType ? $transaction->type->label() : $transaction->type }}</dd>
            </div>

            @if($origin)
                <div class="flex items-baseline justify-between gap-4 px-5 py-3.5">
                    <dt class="text-xs text-[var(--text-muted)] shrink-0">{{ $isDeposit ? __('From') : __('From account') }}</dt>
                    <dd class="text-sm text-[var(--text)] text-right break-words">{{ $origin }}</dd>
                </div>
            @endif

            @if($destination)
                <div class="flex items-baseline justify-between gap-4 px-5 py-3.5">
                    <dt class="text-xs text-[var(--text-muted)] shrink-0">{{ __('Beneficiary / to') }}</dt>
                    <dd class="text-sm text-[var(--text)] text-right break-words">{{ $destination }}</dd>
                </div>
            @endif

            <div class="flex items-baseline justify-between gap-4 px-5 py-3.5">
                <dt class="text-xs text-[var(--text-muted)] shrink-0">{{ __('Amount') }}</dt>
                <dd class="text-sm text-[var(--text)] text-right tabular-nums">{{ $amount }} {{ $symbol }}</dd>
            </div>

            <div class="flex items-baseline justify-between gap-4 px-5 py-3.5">
                <dt class="text-xs text-[var(--text-muted)] shrink-0">{{ __('Fee') }}</dt>
                <dd class="text-sm text-[var(--text)] text-right tabular-nums">{{ $fee }} {{ $symbol }}</dd>
            </div>

            <div class="flex items-baseline justify-between gap-4 px-5 py-3.5">
                <dt class="text-sm font-semibold text-[var(--text)] shrink-0">{{ __('Net amount') }}</dt>
                <dd class="text-base font-bold text-[var(--text)] text-right tabular-nums">{{ $net }} {{ $symbol }}</dd>
            </div>

            @if($transaction->memo)
                <div class="px-5 py-3.5">
                    <dt class="text-xs text-[var(--text-muted)] mb-1">{{ __('Description') }}</dt>
                    <dd class="text-sm text-[var(--text)] whitespace-pre-line leading-relaxed">{{ $transaction->memo }}</dd>
                </div>
            @endif
        </dl>
    </div>

    {{-- Payment channel details --}}
    @php
        $channelFields = [
            'bank_name'       => __('Bank name'),
            'account_number'  => __('Account number'),
            'routing_number'  => __('Routing number'),
            'swift_code'      => __('SWIFT / BIC'),
            'iban'            => __('IBAN'),
            'crypto_currency' => __('Cryptocurrency'),
            'network'         => __('Network'),
            'wallet_address'  => __('Wallet address'),
        ];
        $present = collect($channelFields)
            ->filter(fn($label, $key) => !empty($meta[$key]))
            ->all();
    @endphp

    @if(!empty($present))
        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl overflow-hidden">
            <div class="px-5 py-3.5 border-b border-[var(--line)]">
                <h2 class="text-sm font-semibold text-[var(--text)]">{{ __('Payment details') }}</h2>
            </div>
            <dl class="divide-y divide-[var(--line)]">
                @foreach($present as $key => $label)
                    <div class="flex items-baseline justify-between gap-4 px-5 py-3.5">
                        <dt class="text-xs text-[var(--text-muted)] shrink-0">{{ $label }}</dt>
                        <dd class="text-sm text-[var(--text)] text-right font-mono break-all">{{ $meta[$key] }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    @endif

    {{-- Proof of payment --}}
    @if($transaction->proof_file_path)
        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl overflow-hidden">
            <div class="px-5 py-3.5 border-b border-[var(--line)]">
                <h2 class="text-sm font-semibold text-[var(--text)]">{{ __('Proof of payment') }}</h2>
            </div>
            <div class="p-5">
                 <a href="{{ route('storage.file', $transaction->proof_file_path) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-sm font-medium text-[var(--accent)] hover:opacity-80 transition-opacity">
                    <x-icon name="external-link" class="w-4 h-4" />
                    {{ __('Open attachment') }}
                </a>
            </div>
        </div>
    @endif

    {{-- Rejection reason --}}
    @if($statusEnum === \App\Enums\TransactionStatus::Rejected && $transaction->rejection_reason)
        <div class="bg-[var(--danger-bg)] border border-[var(--danger)]/30 rounded-2xl p-5">
            <h2 class="text-sm font-semibold text-[var(--danger)] mb-1.5">{{ __('Reason for failure') }}</h2>
            <p class="text-sm text-[var(--text)] leading-relaxed whitespace-pre-line">{{ $transaction->rejection_reason }}</p>
        </div>
    @endif

    {{-- Timeline --}}
    <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl overflow-hidden">
        <div class="px-5 py-3.5 border-b border-[var(--line)]">
            <h2 class="text-sm font-semibold text-[var(--text)]">{{ __('Activity') }}</h2>
        </div>
        <ol class="px-5 py-4 space-y-3">
            <li class="flex items-start gap-3">
                <span class="mt-1 w-2 h-2 rounded-full bg-[var(--accent)] shrink-0"></span>
                <div class="min-w-0">
                    <p class="text-sm text-[var(--text)]">{{ __('Request submitted') }}</p>
                    <p class="text-xs text-[var(--text-muted)] tabular-nums">{{ $transaction->created_at->format('j M Y, H:i') }}</p>
                </div>
            </li>
            @if($settledAt)
                <li class="flex items-start gap-3">
                    <span class="mt-1 w-2 h-2 rounded-full {{ $statusInfo['tone'] === 'danger' ? 'bg-[var(--danger)]' : ($statusInfo['tone'] === 'warning' ? 'bg-[var(--warning)]' : 'bg-[var(--success)]') }} shrink-0"></span>
                    <div class="min-w-0">
                        <p class="text-sm text-[var(--text)]">{{ $statusInfo['label'] }}</p>
                        <p class="text-xs text-[var(--text-muted)] tabular-nums">{{ $settledAt->format('j M Y, H:i') }}</p>
                    </div>
                </li>
            @else
                <li class="flex items-start gap-3">
                    <span class="mt-1 w-2 h-2 rounded-full bg-[var(--line)] shrink-0"></span>
                    <div class="min-w-0">
                        <p class="text-sm text-[var(--text-muted)]">{{ __('Awaiting confirmation') }}</p>
                    </div>
                </li>
            @endif
        </ol>
    </div>

    {{-- Footer --}}
    <div class="text-center space-y-3 pt-1">
        <p class="text-xs text-[var(--text-muted)]">
            {{ __('Receipt generated') }} {{ $issuedAt->format('j M Y, H:i') }}
        </p>
        <p class="text-xs text-[var(--text-muted)] max-w-md mx-auto leading-relaxed">
            {{ __('This document confirms the transaction record held by Bastion Peak. Please retain it for your records. Contact support if any detail appears incorrect.') }}
        </p>
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium text-[var(--accent)] border border-[var(--accent)]/30 rounded-lg hover:bg-[var(--accent-subtle)] transition-colors">
            <x-icon name="printer" class="w-4 h-4" />
            {{ __('Print receipt') }}
        </button>
    </div>
</div>
@endsection
