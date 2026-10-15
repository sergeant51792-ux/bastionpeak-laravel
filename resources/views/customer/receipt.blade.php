@extends('layouts.customer')

@section('title', __('Receipt'))

@php
    $decimals = $transaction->currency?->decimals ?? 2;
    $symbol   = $transaction->currency?->symbol ?? '';
    $code     = $transaction->currency?->code ?? 'USD';
    $amount   = number_format((float) $transaction->amount, $decimals);
    $fee      = number_format((float) $transaction->fee, $decimals);
    $net      = number_format((float) ($transaction->net_amount ?? $transaction->amount), $decimals);
    $meta     = is_array($metadata) ? $metadata : [];

    $statusEnum = $transaction->status instanceof \App\Enums\TransactionStatus
        ? $transaction->status
        : \App\Enums\TransactionStatus::PendingReview;

    $statusMap = [
        'pending_review' => ['label' => 'Pending',   'color' => '#D97706'],
        'pending_match'  => ['label' => 'Pending',   'color' => '#D97706'],
        'approved'       => ['label' => 'Approved',  'color' => '#059669'],
        'credited'       => ['label' => 'Credited',  'color' => '#059669'],
        'posted'         => ['label' => 'Completed', 'color' => '#059669'],
        'rejected'       => ['label' => 'Failed',    'color' => '#DC2626'],
        'reversed'       => ['label' => 'Reversed',  'color' => '#D97706'],
    ];
    $statusInfo = $statusMap[$statusEnum->value]
        ?? ['label' => ucfirst($statusEnum->value), 'color' => '#64748B'];

    $isDeposit = in_array($transaction->type, [
        \App\Enums\TransactionType::DepositCredit,
        \App\Enums\TransactionType::Credit,
        \App\Enums\TransactionType::Refund,
    ], true);

    $typeLabel  = $transaction->type instanceof \App\Enums\TransactionType
        ? $transaction->type->customerLabel()
        : 'Transaction';

    $origin      = $transaction->fromAccount?->name ?: ($meta['bank_name'] ?? null);
    $destination = $transaction->toAccount?->name ?: ($transaction->fromUser?->name ?? null);

    $creditedAt = $transaction->credited_at ?: ($transaction->reviewed_at ?? null);
    $settledAt  = match ($statusEnum->value) {
        'credited', 'posted', 'approved' => $creditedAt ?: $transaction->updated_at,
        'rejected'                       => $transaction->rejected_at ?: $transaction->updated_at,
        default                          => null,
    };

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

    $generatedAt = $issuedAt ?? now();
@endphp

@section('content')
<div class="receipt-print-root font-['Courier_New',Courier,monospace] text-sm text-[var(--text)]">
    {{-- On-screen header --}}
    <div class="bp-header flex items-center justify-between px-4 pt-4 pb-3 sm:px-6">
        <div class="flex items-center gap-2">
            <a href="{{ route('activity') }}" class="p-1 rounded hover:bg-[var(--line-soft)] transition-colors" aria-label="Back">
                <x-icon name="arrow-left" class="w-5 h-5 text-[var(--text-muted)]" />
            </a>
            <h1 class="font-['Courier_New',Courier,monospace] text-sm text-[var(--text-muted)] uppercase tracking-wider font-semibold">Receipt</h1>
        </div>
        <a href="{{ route('activity.receipt.download', $transaction) }}" class="p-1 rounded hover:bg-[var(--line-soft)] transition-colors" aria-label="{{ __('Download PDF') }}">
            <x-icon name="download" class="w-5 h-5 text-[var(--text-muted)]" />
        </a>
    </div>

    {{-- Receipt paper --}}
    <div class="bp-paper mx-auto max-w-md bg-white dark:bg-gray-950 text-black dark:text-gray-100 shadow-xl rounded-none sm:rounded-lg overflow-hidden relative">
        <div class="bp-watermark">BASTION PEAK</div>
        <div class="bp-paper-inner px-5 py-5">

            {{-- Brand + status --}}
            <div class="flex items-baseline justify-between mb-4 pb-2">
                <div class="flex items-center gap-2">
                    <x-icon name="landmark" class="w-5 h-5 text-[var(--accent)]" />
                    <span class="font-bold text-xl text-black dark:text-white">Bastion Peak</span>
                </div>
                <span class="font-bold text-xs uppercase" style="color: {{ $statusInfo['color'] }}">{{ $statusInfo['label'] }}</span>
            </div>

            {{-- Amount block --}}
            <div class="text-center my-5 py-3">
                <p class="text-black/50 dark:text-white/50 text-xs uppercase tracking-wider mb-1">
                    {{ $isDeposit ? __('Received') : __('Sent') }}
                </p>
                <p class="font-bold text-3xl text-black dark:text-white tabular-nums">
                    {{ $isDeposit ? '+' : '-' }}{{ $amount }} <span class="text-base font-normal text-black/60 dark:text-white/60">{{ $symbol }}</span>
                </p>
                <p class="text-black/40 dark:text-white/40 text-xs mt-1">{{ $code }}</p>
                @if((float) $transaction->fee > 0)
                    <p class="text-black/40 dark:text-white/40 text-xs mt-1">Fee: {{ $fee }} {{ $symbol }}</p>
                @endif
            </div>

            {{-- Thin divider (no card border, just a line) --}}
            <hr class="border-0 h-px bg-black/10 dark:bg-white/10 my-3">

            {{-- Details — label / value pairs --}}
            <dl class="bp-details">
                <div class="bp-row flex justify-between py-1">
                    <dt class="text-black/40 dark:text-white/40">{{ __('Reference') }}</dt>
                    <dd class="font-bold text-right font-mono break-all">{{ $transaction->transaction_id ?? 'TXN-' . strtoupper(substr((string) $transaction->id, 0, 8)) }}</dd>
                </div>

                @if($transaction->reference)
                    <div class="bp-row flex justify-between py-1">
                        <dt class="text-black/40 dark:text-white/40">{{ __('Payment reference') }}</dt>
                        <dd class="font-bold text-right font-mono break-all">{{ $transaction->reference }}</dd>
                    </div>
                @endif

                <div class="bp-row flex justify-between py-1">
                    <dt class="text-black/40 dark:text-white/40">{{ __('Date &amp; time') }}</dt>
                    <dd class="font-bold text-right tabular-nums">{{ $transaction->created_at->format('j M Y, H:i') }}</dd>
                </div>

                @if($settledAt)
                    <div class="bp-row flex justify-between py-1">
                        <dt class="text-black/40 dark:text-white/40">{{ __('Processed at') }}</dt>
                        <dd class="font-bold text-right tabular-nums">{{ $settledAt->format('j M Y, H:i') }}</dd>
                    </div>
                @endif

                <div class="bp-row flex justify-between py-1">
                    <dt class="text-black/40 dark:text-white/40">{{ __('Type') }}</dt>
                    <dd class="font-bold text-right">{{ $typeLabel }}</dd>
                </div>

                @if($origin)
                    <div class="bp-row flex justify-between py-1">
                        <dt class="text-black/40 dark:text-white/40">{{ $isDeposit ? __('From') : __('From account') }}</dt>
                        <dd class="font-bold text-right break-words">{{ $origin }}</dd>
                    </div>
                @endif

                @if($destination)
                    <div class="bp-row flex justify-between py-1">
                        <dt class="text-black/40 dark:text-white/40">{{ __('Beneficiary / to') }}</dt>
                        <dd class="font-bold text-right break-words">{{ $destination }}</dd>
                    </div>
                @endif

                <div class="bp-row flex justify-between py-1">
                    <dt class="text-black/40 dark:text-white/40">{{ __('Amount') }}</dt>
                    <dd class="font-bold text-right tabular-nums">{{ $amount }} {{ $symbol }}</dd>
                </div>

                @if((float) $transaction->fee > 0)
                    <div class="bp-row flex justify-between py-1">
                        <dt class="text-black/40 dark:text-white/40">{{ __('Fee') }}</dt>
                        <dd class="font-bold text-right tabular-nums">{{ $fee }} {{ $symbol }}</dd>
                    </div>
                @endif

                <div class="bp-row flex justify-between py-1 font-semibold border-t border-black/10 dark:border-white/10 mt-1 pt-2">
                    <dt class="text-black/40 dark:text-white/40">{{ __('Net amount') }}</dt>
                    <dd class="font-bold text-right tabular-nums">{{ $net }} {{ $symbol }}</dd>
                </div>

                 @if($transaction->memo)
                     <div class="bp-row flex justify-between py-2">
                         <dt class="text-black/40 dark:text-white/40">{{ __('Note') }}</dt>
                         <dd class="font-bold text-right whitespace-pre-line break-words">{{ $transaction->memo }}</dd>
                     </div>
                 @endif
            </dl>

            {{-- Payment channel details --}}
            @if(!empty($present))
                <hr class="border-0 h-px bg-black/10 dark:bg-white/10 my-3">
                <h2 class="text-black/50 dark:text-white/50 text-xs uppercase tracking-wider font-semibold mb-2">{{ __('Payment details') }}</h2>
                <dl class="bp-details">
                    @foreach($present as $key => $label)
                        <div class="bp-row flex justify-between py-1">
                            <dt class="text-black/40 dark:text-white/40">{{ $label }}</dt>
                            <dd class="font-bold text-right font-mono break-all">{{ $meta[$key] }}</dd>
                        </div>
                    @endforeach
                </dl>
            @endif

            {{-- Proof of payment --}}
            @if($transaction->proof_file_path)
                <hr class="border-0 h-px bg-black/10 dark:bg-white/10 my-3">
                <h2 class="text-black/50 dark:text-white/50 text-xs uppercase tracking-wider font-semibold mb-2">{{ __('Proof of payment') }}</h2>
                <a href="{{ route('storage.file', $transaction->proof_file_path) }}" target="_blank" rel="noopener" class="inline-flex items-center text-xs font-bold text-blue-600 dark:text-blue-400 hover:opacity-80">
                    {{ __('Open attachment') }}
                </a>
            @endif

            {{-- Rejection reason --}}
            @if($statusEnum === \App\Enums\TransactionStatus::Rejected && $transaction->rejection_reason)
                <hr class="border-0 h-px bg-black/10 dark:bg-white/10 my-3">
                <h2 class="text-red-600 dark:text-red-400 text-xs uppercase tracking-wider font-semibold mb-1">{{ __('Reason for failure') }}</h2>
                <p class="text-xs text-black/70 dark:text-white/70 whitespace-pre-line">{{ $transaction->rejection_reason }}</p>
            @endif

            {{-- Timeline --}}
            <hr class="border-0 h-px bg-black/10 dark:bg-white/10 my-3">
            <h2 class="text-black/50 dark:text-white/50 text-xs uppercase tracking-wider font-semibold mb-2">{{ __('Activity') }}</h2>
            <ul class="bp-timeline">
                <li class="flex gap-2 mb-2 text-xs">
                    <span class="w-1 h-1 rounded-full bg-blue-500 shrink-0 mt-1"></span>
                    <div>
                        <p class="font-bold text-black dark:text-white">{{ __('Request submitted') }}</p>
                        <p class="text-black/40 dark:text-white/40 tabular-nums">{{ $transaction->created_at->format('j M Y, H:i') }}</p>
                    </div>
                </li>
                @if($settledAt)
                    <li class="flex gap-2 mb-2 text-xs">
                        <span class="w-1 h-1 rounded-full shrink-0 mt-1" style="background-color: {{ $statusInfo['color'] }}"></span>
                        <div>
                            <p class="font-bold text-black dark:text-white">{{ $statusInfo['label'] }}</p>
                            <p class="text-black/40 dark:text-white/40 tabular-nums">{{ $settledAt->format('j M Y, H:i') }}</p>
                        </div>
                    </li>
                @else
                    <li class="flex gap-2 mb-2 text-xs">
                        <span class="w-1 h-1 rounded-full bg-black/30 dark:bg-white/30 shrink-0 mt-1"></span>
                        <div>
                            <p class="text-black/40 dark:text-white/40">{{ __('Awaiting confirmation') }}</p>
                        </div>
                    </li>
                @endif
            </ul>

            {{-- Footer --}}
            <hr class="border-0 h-px bg-black/10 dark:bg-white/10 my-3">
            <p class="text-black/40 dark:text-white/40 text-xs mb-1">{{ __('Receipt generated') }} {{ $generatedAt->format('j M Y, H:i') }}</p>
            <p class="text-black/40 dark:text-white/40 text-xs leading-relaxed">
                {{ __('This document confirms the transaction record held by Bastion Peak. Please retain it for your records. Contact support if any detail appears incorrect.') }}
            </p>

        </div>
    </div>

    {{-- Print button --}}
    <div class="mt-6 text-center">
        <button onclick="window.print()" class="inline-flex items-center gap-2 text-xs font-bold text-blue-600 dark:text-blue-400 hover:opacity-80">
            <x-icon name="printer" class="w-4 h-4" />
            {{ __('Print receipt') }}
        </button>
    </div>
</div>

<style>
    @media print {
        .bp-header,
        .bp-paper + div,
        body > div:not(.receipt-print-root) { display: none !important; }
        .receipt-print-root { padding: 0 !important; }
        .bp-paper {
            box-shadow: none !important;
            border-radius: 0 !important;
            background: white !important;
            color: black !important;
            page-break-inside: avoid;
        }
        .bp-paper-inner { padding: 0.25in !important; }
    }

    .bp-watermark {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-25deg);
        font-size: 3.5rem;
        font-weight: 900;
        color: var(--text-muted);
        opacity: 0.05;
        pointer-events: none;
        white-space: nowrap;
        letter-spacing: 0.25em;
    }

    .bp-row { line-height: 1.5; }
</style>
@endsection
