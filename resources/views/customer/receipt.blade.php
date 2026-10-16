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
        'pending_review' => ['label' => 'Pending',    'color' => '#F59E0B', 'icon' => 'clock',  'desc' => 'This transaction is awaiting review.'],
        'pending_match'  => ['label' => 'Pending',    'color' => '#F59E0B', 'icon' => 'clock',  'desc' => 'This transaction is awaiting review.'],
        'approved'       => ['label' => 'Successful', 'color' => '#10B981', 'icon' => 'check',  'desc' => 'This transaction has been completed.'],
        'credited'       => ['label' => 'Successful', 'color' => '#10B981', 'icon' => 'check',  'desc' => 'The funds have been credited.'],
        'posted'         => ['label' => 'Successful', 'color' => '#10B981', 'icon' => 'check',  'desc' => 'This transaction has been completed.'],
        'rejected'       => ['label' => 'Failed',     'color' => '#EF4444', 'icon' => 'x',      'desc' => 'This transaction was rejected.'],
        'reversed'       => ['label' => 'Failed',     'color' => '#EF4444', 'icon' => 'x',      'desc' => 'This transaction was reversed and marked as failed.'],
    ];
    $statusInfo = $statusMap[$statusEnum->value]
        ?? ['label' => ucfirst($statusEnum->value), 'color' => '#64748B', 'icon' => 'circle', 'desc' => ''];

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

    // Payment method detection
    $paymentMethod = $meta['deposit_method_slug'] ?? ($meta['payment_method'] ?? $transaction->metadata['payment_method'] ?? null);
    $paymentMethodLabel = [
        'wire' => 'Bank Transfer',
        'bank' => 'Bank Transfer',
        'card' => 'Card Payment',
        'crypto' => 'Cryptocurrency',
        'giftcard' => 'Gift Card',
        'gift_card' => 'Gift Card',
        'other' => 'Other',
    ][$paymentMethod ?? ''] ?? ($paymentMethod ? ucfirst($paymentMethod) : null);

    // Channel-specific fields grouped by payment method
    $bankFields = [
        'bank_name'      => __('Bank name'),
        'account_number' => __('Account number'),
        'routing_number' => __('Routing number'),
        'swift_code'     => __('SWIFT / BIC'),
        'iban'           => __('IBAN'),
    ];
    $cryptoFields = [
        'crypto_currency' => __('Cryptocurrency'),
        'network'         => __('Network'),
        'wallet_address'  => __('Wallet address'),
    ];

    $bankPresent   = collect($bankFields)->filter(fn($label, $key) => !empty($meta[$key]))->all();
    $cryptoPresent = collect($cryptoFields)->filter(fn($label, $key) => !empty($meta[$key]))->all();

    $description = $transaction->memo ?: $meta['user_note'] ?? null;
    $description = $description ?: __('None');

    $generatedAt = $issuedAt ?? now();
@endphp

@section('content')
<div class="receipt-print-root font-sans text-sm">
    {{-- On-screen header --}}
    <div class="bp-header flex items-center justify-between px-4 pt-4 pb-3 sm:px-6">
        <a href="{{ route('activity') }}" class="p-1 rounded hover:bg-[var(--line-soft)] transition-colors" aria-label="Back">
            <x-icon name="arrow-left" class="w-5 h-5 text-[var(--text-muted)]" />
        </a>
        <a href="{{ route('activity.receipt.download', $transaction) }}" class="p-1 rounded hover:bg-[var(--line-soft)] transition-colors" aria-label="{{ __('Download PDF') }}">
            <x-icon name="download" class="w-5 h-5 text-[var(--text-muted)]" />
        </a>
    </div>

    {{-- Receipt paper --}}
    <div class="bp-paper mx-auto max-w-md bg-white text-black shadow-xl rounded-none sm:rounded-lg overflow-hidden relative">
        <div class="bp-paper-inner px-6 py-6">

            {{-- Header: Logo + Status circle --}}
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-[#E5E7EB]">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-[#1E3A8A]/10 flex items-center justify-center">
                        <x-icon name="landmark" class="w-5 h-5 text-[#1E3A8A]" />
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-[#6B7280] uppercase tracking-wider">Bastion Peak</p>
                        <p class="text-sm font-semibold text-black mt-0.5">{{ $typeLabel }}</p>
                    </div>
                </div>

                {{-- Status circle --}}
                <div class="flex items-center gap-2">
                    <span class="w-7 h-7 rounded-full flex items-center justify-center text-white font-bold text-xs shrink-0" style="background-color: {{ $statusInfo['color'] }};">
                        <x-icon name="{{ $statusInfo['icon'] }}" class="w-3.5 h-3.5" />
                    </span>
                    <span class="font-semibold text-xs uppercase text-[#374151]" style="color: {{ $statusInfo['color'] }};">{{ $statusInfo['label'] }}</span>
                </div>
            </div>

            {{-- Amount block --}}
            <div class="text-center my-6 py-4 border-b border-[#E5E7EB]">
                <p class="text-[#9CA3AF] text-xs uppercase tracking-wider mb-2">
                    {{ $isDeposit ? __('Received') : __('Sent') }}
                </p>
                <p class="font-bold text-4xl text-black tabular-nums mb-1">
                    {{ $isDeposit ? '+' : '-' }}{{ $amount }} <span class="text-lg font-normal text-[#6B7280]">{{ $symbol }}</span>
                </p>
                <p class="text-[#9CA3AF] text-xs">{{ $code }}</p>
                @if((float) $transaction->fee > 0)
                    <p class="text-[#9CA3AF] text-xs mt-1">Fee: {{ $fee }} {{ $symbol }}</p>
                @endif
            </div>

            {{-- Details --}}
            <div class="space-y-0.5 mb-6">
                <div class="flex justify-between py-2 border-b border-[#F3F4F6]">
                    <span class="text-[#9CA3AF]">Reference</span>
                    <span class="font-mono font-semibold text-sm text-right break-all">{{ $transaction->transaction_id ?? 'TXN-' . strtoupper(substr((string) $transaction->id, 0, 8)) }}</span>
                </div>

                @if($transaction->reference)
                    <div class="flex justify-between py-2 border-b border-[#F3F4F6]">
                        <span class="text-[#9CA3AF]">Payment reference</span>
                        <span class="font-mono font-semibold text-sm text-right break-all">{{ $transaction->reference }}</span>
                    </div>
                @endif

                <div class="flex justify-between py-2 border-b border-[#F3F4F6]">
                    <span class="text-[#9CA3AF]">Date &amp; time</span>
                    <span class="font-semibold text-sm text-right tabular-nums">{{ $transaction->created_at->format('j M Y, H:i') }}</span>
                </div>

                @if($settledAt)
                    <div class="flex justify-between py-2 border-b border-[#F3F4F6]">
                        <span class="text-[#9CA3AF]">Processed at</span>
                        <span class="font-semibold text-sm text-right tabular-nums">{{ $settledAt->format('j M Y, H:i') }}</span>
                    </div>
                @endif

                <div class="flex justify-between py-2 border-b border-[#F3F4F6]">
                    <span class="text-[#9CA3AF]">Type</span>
                    <span class="font-semibold text-sm text-right">{{ $typeLabel }}</span>
                </div>

                @if($origin)
                    <div class="flex justify-between py-2 border-b border-[#F3F4F6]">
                        <span class="text-[#9CA3AF]">{{ $isDeposit ? 'From' : 'From account' }}</span>
                        <span class="font-semibold text-sm text-right break-words">{{ $origin }}</span>
                    </div>
                @endif

                @if($destination)
                    <div class="flex justify-between py-2 border-b border-[#F3F4F6]">
                        <span class="text-[#9CA3AF]">Beneficiary / to</span>
                        <span class="font-semibold text-sm text-right break-words">{{ $destination }}</span>
                    </div>
                @endif

                {{-- Amount (single row, no duplicate with net) --}}
                <div class="flex justify-between py-2 border-b border-[#F3F4F6] font-semibold">
                    <span class="text-[#9CA3AF]">Amount</span>
                    <span class="text-lg font-bold text-black tabular-nums">{{ $amount }} {{ $symbol }}</span>
                </div>

                {{-- Description --}}
                <div class="flex justify-between py-2 border-b border-[#F3F4F6]">
                    <span class="text-[#9CA3AF]">Description</span>
                    <span class="font-semibold text-sm text-right break-words">{{ $description }}</span>
                </div>
            </div>

            {{-- Payment method section --}}
            @if($paymentMethodLabel)
                <div class="mb-6">
                    <p class="text-xs font-semibold text-[#9CA3AF] uppercase tracking-wider mb-3">{{ __('Payment method') }}</p>
                    <div class="bg-[#F9FAFB] rounded-xl p-4 space-y-2">
                        <p class="font-semibold text-sm text-black">{{ $paymentMethodLabel }}</p>

                        @if($paymentMethod === 'wire' || $paymentMethod === 'bank')
                            @foreach($bankPresent as $key => $label)
                                <div class="flex justify-between py-1">
                                    <span class="text-[#9CA3AF]">{{ $label }}</span>
                                    <span class="font-mono text-sm text-right break-all">{{ $meta[$key] }}</span>
                                </div>
                            @endforeach
                        @endif

                        @if($paymentMethod === 'crypto' || !empty($cryptoPresent))
                            @foreach($cryptoPresent as $key => $label)
                                <div class="flex justify-between py-1">
                                    <span class="text-[#9CA3AF]">{{ $label }}</span>
                                    <span class="font-mono text-sm text-right break-all">{{ $meta[$key] }}</span>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif

            {{-- Proof of payment --}}
            @if($transaction->proof_file_path)
                <div class="mb-6">
                    <p class="text-xs font-semibold text-[#9CA3AF] uppercase tracking-wider mb-2">{{ __('Proof of payment') }}</p>
                    <a href="{{ route('storage.file', $transaction->proof_file_path) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-sm font-medium text-[#2563EB] hover:opacity-80">
                        {{ __('Open attachment') }}
                    </a>
                </div>
            @endif

            {{-- Rejection reason --}}
            @if($statusEnum === \App\Enums\TransactionStatus::Rejected && $transaction->rejection_reason)
                <div class="mb-6 pb-4 border-b border-[#FEE2E2]">
                    <p class="text-xs font-semibold text-[#EF4444] uppercase tracking-wider mb-2">{{ __('Reason for failure') }}</p>
                    <p class="text-sm text-[#374151] whitespace-pre-line">{{ $transaction->rejection_reason }}</p>
                </div>
            @endif

            {{-- Timeline --}}
            <div class="mb-6">
                <p class="text-xs font-semibold text-[#9CA3AF] uppercase tracking-wider mb-3">{{ __('Activity') }}</p>
                <ul class="space-y-2">
                    <li class="flex gap-3 items-start">
                        <span class="w-5 h-5 rounded-full bg-[#3B82F6] flex items-center justify-center shrink-0 mt-0.5">
                            <span class="w-2 h-2 rounded-full bg-white"></span>
                        </span>
                        <div>
                            <p class="font-semibold text-sm text-black">{{ __('Request submitted') }}</p>
                            <p class="text-xs text-[#9CA3AF] tabular-nums">{{ $transaction->created_at->format('j M Y, H:i') }}</p>
                        </div>
                    </li>

                    @if($settledAt)
                        <li class="flex gap-3 items-start">
                            <span class="w-5 h-5 rounded-full flex items-center justify-center shrink-0 mt-0.5" style="background-color: {{ $statusInfo['color'] }};">
                                <x-icon name="{{ $statusInfo['icon'] }}" class="w-2.5 h-2.5 text-white" />
                            </span>
                            <div>
                                <p class="font-semibold text-sm text-black">{{ $statusInfo['label'] }}</p>
                                <p class="text-xs text-[#9CA3AF] tabular-nums">{{ $settledAt->format('j M Y, H:i') }}</p>
                            </div>
                        </li>
                    @else
                        <li class="flex gap-3 items-start">
                            <span class="w-5 h-5 rounded-full bg-[#D1D5DB] flex items-center justify-center shrink-0 mt-0.5">
                                <x-icon name="clock" class="w-2.5 h-2.5 text-white" />
                            </span>
                            <div>
                                <p class="text-sm text-[#9CA3AF]">{{ __('Awaiting confirmation') }}</p>
                            </div>
                        </li>
                    @endif
                </ul>
            </div>

            {{-- Footer with Bastion stamp --}}
            <div class="border-t border-[#E5E7EB] pt-4 text-center">
                <p class="text-[#9CA3AF] text-xs mb-1">{{ __('Receipt generated') }} {{ $generatedAt->format('j M Y, H:i') }}</p>
                <p class="text-[#9CA3AF] text-xs leading-relaxed max-w-sm mx-auto">
                    {{ __('This document confirms the transaction record held by Bastion Peak. Please retain it for your records.') }}
                </p>
                <div class="mt-3 pt-3 border-t border-[#E5E7EB]">
                    <p class="text-xs text-[#9CA3AF]">{{ __('Bastion Peak Financial Services') }}</p>
                </div>
            </div>

        </div>
    </div>

    {{-- Print button --}}
    <div class="mt-6 text-center">
        <button onclick="window.print()" class="inline-flex items-center gap-2 text-xs font-bold text-[#2563EB] hover:opacity-80">
            <x-icon name="printer" class="w-4 h-4" />
            {{ __('Print receipt') }}
        </button>
    </div>
</div>

<style>
    @media print {
        #customer-sidebar,
        #sidebar-overlay,
        header,
        .bottom-nav,
        .bp-header,
        .bp-paper + div {
            display: none !important;
        }

        body > div,
        body > div > div,
        main#main-content,
        #main-content {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            background: white !important;
        }

        .receipt-print-root { padding: 0 !important; }
        .bp-paper {
            box-shadow: none !important;
            border-radius: 0 !important;
            background: white !important;
            margin: 0 !important;
            page-break-inside: avoid;
        }
        .bp-paper-inner { padding: 0.25in !important; }
        * { color-adjust: exact !important; }
    }
</style>
@endsection
