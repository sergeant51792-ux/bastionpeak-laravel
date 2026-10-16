@php
    $decimals = $transaction->currency?->decimals ?? 2;
    $symbol   = $transaction->currency?->symbol ?? '$';
    $code     = $transaction->currency?->code ?? 'USD';
    $amount   = number_format((float) $transaction->amount, $decimals);
    $fee      = (float) ($transaction->fee ?? 0);
    $net      = number_format((float) ($transaction->net_amount ?? $transaction->amount), $decimals);
    $meta     = is_array($metadata) ? $metadata : [];

    $statusEnum = $transaction->status instanceof \App\Enums\TransactionStatus
        ? $transaction->status
        : \App\Enums\TransactionStatus::from((string) $transaction->status);

    $statusMap = [
        'pending_review' => ['label' => 'Pending',     'color' => '#F59E0B', 'dot' => true],
        'pending_match'  => ['label' => 'Pending',     'color' => '#F59E0B', 'dot' => true],
        'approved'       => ['label' => 'Successful',  'color' => '#10B981', 'dot' => false],
        'credited'       => ['label' => 'Successful',  'color' => '#10B981', 'dot' => false],
        'posted'         => ['label' => 'Successful',  'color' => '#10B981', 'dot' => false],
        'rejected'       => ['label' => 'Failed',      'color' => '#EF4444', 'dot' => false],
        'reversed'       => ['label' => 'Failed',      'color' => '#EF4444', 'dot' => false],
    ];
    $statusInfo = $statusMap[$statusEnum->value]
        ?? ['label' => ucfirst($statusEnum->value), 'color' => '#64748B', 'dot' => false];

    $isDeposit = in_array($transaction->type, [
        \App\Enums\TransactionType::DepositCredit,
        \App\Enums\TransactionType::Credit,
        \App\Enums\TransactionType::Refund,
    ], true);

    $typeLabel  = $transaction->type instanceof \App\Enums\TransactionType
        ? $transaction->type->customerLabel()
        : ucfirst(str_replace('_', ' ', (string) $transaction->type));

    $origin      = $transaction->fromAccount?->name ?: ($meta['bank_name'] ?? null);
    $destination = $transaction->toAccount?->name ?: ($transaction->fromUser?->name ?? null);

    $creditedAt = $transaction->credited_at ?: ($transaction->reviewed_at ?? null);
    $settledAt  = match ($statusEnum->value) {
        'credited', 'posted', 'approved' => $creditedAt ?: $transaction->updated_at,
        'rejected'                       => $transaction->rejected_at ?: $transaction->updated_at,
        default                          => null,
    };

    $paymentMethod = $meta['deposit_method_slug'] ?? ($meta['payment_method'] ?? null);
    $bankFields = [
        'bank_name'      => 'Bank name',
        'account_number' => 'Account number',
        'routing_number' => 'Routing number',
        'swift_code'     => 'SWIFT / BIC',
        'iban'           => 'IBAN',
    ];
    $cryptoFields = [
        'crypto_currency' => 'Cryptocurrency',
        'network'         => 'Network',
        'wallet_address'  => 'Wallet address',
    ];
    $bankPresent   = collect($bankFields)->filter(fn($label, $key) => !empty($meta[$key]))->all();
    $cryptoPresent = collect($cryptoFields)->filter(fn($label, $key) => !empty($meta[$key]))->all();

    $description = $transaction->memo ?: ($meta['user_note'] ?? null) ?: 'No description provided.';
    $ref         = $transaction->transaction_id ?? 'TXN-' . strtoupper(substr((string) $transaction->id, 0, 8));
    $generatedAt = $issuedAt ?? now();
@endphp

@extends('layouts.customer')

@section('title', 'Receipt')

@section('content')
<div class="pb-16">
    <div class="max-w-2xl mx-auto px-4 pt-6 space-y-4">
        <div class="flex justify-between items-baseline">
            <div>
                <h1 class="text-lg font-semibold text-[var(--text)]">Receipt</h1>
                <p class="text-xs text-[var(--text-muted)]">#{{ $ref }}</p>
            </div>
            <a href="{{ route('activity.receipt.download', $transaction) }}"
               class="text-xs font-medium text-[var(--accent)] hover:text-[var(--accent-dark)]">
                Download PDF
            </a>
        </div>

        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6 space-y-5">
            <div class="flex justify-between items-baseline">
                <div>
                    <p class="text-xs text-[var(--text-muted)] uppercase tracking-wider">Status</p>
                    <p class="mt-1 font-medium" style="color: {{ $statusInfo['color'] }};">{{ $statusInfo['label'] }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-[var(--text-muted)] uppercase tracking-wider">{{ $isDeposit ? 'Credited' : 'Debited' }}</p>
                    <div class="mt-1">
                        <span class="text-3xl font-bold tabular-nums" style="color: {{ $statusInfo['color'] }};">
                            {{ $isDeposit ? '+' : '−' }}{{ $amount }} {{ $symbol }}
                        </span>
                    </div>
                    @if($fee > 0)
                        <p class="text-xs text-[var(--text-muted)] mt-0.5">Fee: {{ $fee }} {{ $symbol }}</p>
                    @endif
                </div>
            </div>

            <hr class="border-[var(--line)]">

            <div class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                <div>
                    <p class="text-xs text-[var(--text-muted)] uppercase tracking-wider">Reference</p>
                    <p class="font-mono text-xs mt-0.5 break-all">{{ $ref }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-[var(--text-muted)] uppercase tracking-wider">Date</p>
                    <p class="font-mono text-xs mt-0.5 tabular-nums">{{ $transaction->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <p class="text-xs text-[var(--text-muted)] uppercase tracking-wider">Type</p>
                    <p class="mt-0.5">{{ $typeLabel }}</p>
                </div>
                @if($settledAt)
                    <div class="text-right">
                        <p class="text-xs text-[var(--text-muted)] uppercase tracking-wider">Processed</p>
                        <p class="font-mono text-xs mt-0.5 tabular-nums">{{ $settledAt->format('d M Y, H:i') }}</p>
                    </div>
                @endif
                @if($origin)
                    <div>
                        <p class="text-xs text-[var(--text-muted)] uppercase tracking-wider">{{ $isDeposit ? 'From' : 'Source' }}</p>
                        <p class="mt-0.5">{{ $origin }}</p>
                    </div>
                @endif
                @if($destination)
                    <div class="text-right">
                        <p class="text-xs text-[var(--text-muted)] uppercase tracking-wider">{{ $isDeposit ? 'To' : 'Destination' }}</p>
                        <p class="mt-0.5">{{ $destination }}</p>
                    </div>
                @endif
            </div>

            <div class="text-sm">
                <p class="text-xs text-[var(--text-muted)] uppercase tracking-wider mb-1">Description</p>
                <p class="whitespace-pre-line">{{ $description }}</p>
            </div>

            @if($paymentMethod || !empty($bankPresent) || !empty($cryptoPresent))
                <hr class="border-[var(--line)]">
                <div class="text-sm">
                    <p class="text-xs text-[var(--text-muted)] uppercase tracking-wider mb-1">Payment method</p>
                    @php
                        $pmLabels = [
                            'wire' => 'Bank transfer',
                            'bank' => 'Bank transfer',
                            'card' => 'Card payment',
                            'crypto' => 'Cryptocurrency',
                            'giftcard' => 'Gift card',
                        ];
                        $pmLabel = $pmLabels[$paymentMethod ?? ''] ?? ($paymentMethod ? ucfirst($paymentMethod) : null);
                    @endphp
                    @if($pmLabel)
                        <p class="font-medium mb-2">{{ $pmLabel }}</p>
                    @endif
                    @foreach($bankPresent as $key => $label)
                        <div class="grid grid-cols-2 gap-1 py-1 border-b border-[var(--line)]/50">
                            <span class="text-[var(--text-muted)]">{{ $label }}</span>
                            <span class="font-mono text-xs text-right break-all">{{ $meta[$key] }}</span>
                        </div>
                    @endforeach
                    @foreach($cryptoPresent as $key => $label)
                        <div class="grid grid-cols-2 gap-1 py-1 border-b border-[var(--line)]/50">
                            <span class="text-[var(--text-muted)]">{{ $label }}</span>
                            <span class="font-mono text-xs text-right break-all">{{ $meta[$key] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($transaction->proof_file_path)
                <hr class="border-[var(--line)]">
                <div class="flex items-center gap-2 text-sm">
                    <x-icon name="paperclip" class="w-4 h-4 text-[var(--text-muted)]" />
                    <a href="{{ route('storage.file', $transaction->proof_file_path) }}" class="font-medium text-[var(--accent)] hover:text-[var(--accent-dark)]">
                        Supporting document
                    </a>
                </div>
            @endif

            @if($statusEnum === \App\Enums\TransactionStatus::Rejected && $transaction->rejection_reason)
                <hr class="border-[var(--line)]">
                <div>
                    <p class="text-xs text-[var(--text-muted)] uppercase tracking-wider mb-1">Reason for decline</p>
                    <p class="text-sm">{{ $transaction->rejection_reason }}</p>
                </div>
            @endif
        </div>

        <div class="text-center pt-4">
            <p class="text-xs text-[var(--text-muted)]">
                Generated {{ $generatedAt->format('d M Y, H:i') }} · Bastion Peak
            </p>
        </div>
    </div>
</div>
@endsection
