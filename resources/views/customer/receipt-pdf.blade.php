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
        : \App\Enums\TransactionStatus::from((string) $transaction->status);

    $statusMap = [
        'pending_review' => ['label' => 'Pending',    'color' => '#D97706'],
        'pending_match'  => ['label' => 'Pending',    'color' => '#D97706'],
        'approved'       => ['label' => 'Successful', 'color' => '#059669'],
        'credited'       => ['label' => 'Successful', 'color' => '#059669'],
        'posted'         => ['label' => 'Successful', 'color' => '#059669'],
        'rejected'       => ['label' => 'Failed',     'color' => '#DC2626'],
        'reversed'       => ['label' => 'Reversed',   'color' => '#D97706'],
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
        'bank_name'       => 'Bank name',
        'account_number'  => 'Account number',
        'routing_number'  => 'Routing number',
        'swift_code'      => 'SWIFT / BIC',
        'iban'            => 'IBAN',
        'crypto_currency' => 'Cryptocurrency',
        'network'         => 'Network',
        'wallet_address'  => 'Wallet address',
    ];
    $present = collect($channelFields)
        ->filter(fn($label, $key) => !empty($meta[$key]))
        ->all();

    $generatedAt = $issuedAt ?? now();
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt - Bastion Peak</title>
    <style>
        body { font-family: "Courier New", Courier, monospace; margin: 0; padding: 0; color: #1a1a1a; }
        .page { width: 100%; max-width: 794px; margin: 0 auto; padding: 32px 48px; font-size: 12px; line-height: 1.4; }
        h1 { font-size: 20px; font-weight: bold; margin-bottom: 4px; }
        .muted { color: #666; font-size: 11px; }
        .small { font-size: 10px; }
        .center { text-align: center; }
        .row { padding: 6px 0; border-bottom: 1px solid #eee; }
        .row dt { color: #666; display: inline-block; width: 45%; }
        .row dd { color: #111; display: inline-block; width: 55%; text-align: right; font-weight: bold; word-break: break-word; }
        .total { font-weight: bold; border-top: 1px solid #ccc; }
        .total dt { color: #333; }
        .status { font-weight: bold; font-size: 10px; text-transform: uppercase; }
        .divider { border: 0; height: 1px; background: #eee; margin: 14px 0; }
        .section-title { color: #666; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; margin: 14px 0 6px; }
        .amount { font-size: 24px; font-weight: bold; }
        .watermark { text-align: center; font-size: 48px; font-weight: bold; color: #ddd; margin: 20px 0; }
        .proof-link { color: #2563eb; text-decoration: none; font-weight: bold; }
        .timeline { list-style: none; padding: 0; }
        .timeline li { padding: 5px 0; display: block; }
        .dot { display: inline-block; width: 6px; height: 6px; border-radius: 50%; margin-right: 6px; vertical-align: middle; }
    </style>
</head>
<body>
<div class="page">
    <div class="watermark">BASTION PEAK</div>

    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 20px;">
        <span style="font-weight: 700; font-size: 20px; color: #1E3A8A;">Bastion Peak</span>
        <span class="status" style="color: {{ $statusInfo['color'] }}">{{ $statusInfo['label'] }}</span>
    </div>

    <table width="100%" class="center" style="margin: 20px 0 24px;">
        <tr>
            <td class="muted small">{{ $isDeposit ? 'RECEIVED' : 'SENT' }}</td>
        </tr>
        <tr>
            <td class="amount">{{ $isDeposit ? '+' : '-' }}{{ $amount }} {{ $symbol }}</td>
        </tr>
        <tr>
            <td class="muted small">{{ $code }}</td>
        </tr>
        @if((float) $transaction->fee > 0)
            <tr>
                <td class="muted small">Fee: {{ $fee }} {{ $symbol }}</td>
            </tr>
        @endif
    </table>

    <hr class="divider">

    <p class="section-title">Details</p>
    <table width="100%" class="row">
        <tr>
            <dt>Reference</dt>
            <dd>{{ $transaction->transaction_id ?? 'TXN-' . strtoupper(substr((string) $transaction->id, 0, 8)) }}</dd>
        </tr>
        <tr>
            <dt>Date &amp; time</dt>
            <dd>{{ $transaction->created_at->format('j M Y, H:i') }}</dd>
        </tr>
        @if($settledAt)
            <tr>
                <dt>Processed at</dt>
                <dd>{{ $settledAt->format('j M Y, H:i') }}</dd>
            </tr>
        @endif
        <tr>
            <dt>Type</dt>
            <dd>{{ $typeLabel }}</dd>
        </tr>
        @if($origin)
            <tr>
                <dt>{{ $isDeposit ? 'From' : 'From account' }}</dt>
                <dd>{{ $origin }}</dd>
            </tr>
        @endif
        @if($destination)
            <tr>
                <dt>Beneficiary / to</dt>
                <dd>{{ $destination }}</dd>
            </tr>
        @endif
        <tr>
            <dt>Amount</dt>
            <dd>{{ $amount }} {{ $symbol }}</dd>
        </tr>
        @if((float) $transaction->fee > 0)
            <tr>
                <dt>Fee</dt>
                <dd>{{ $fee }} {{ $symbol }}</dd>
            </tr>
        @endif
        <tr class="total">
            <dt>Net amount</dt>
            <dd>{{ $net }} {{ $symbol }}</dd>
        </tr>
        @if($transaction->memo)
            <tr>
                <dt>Note</dt>
                <dd style="white-space: pre-line;">{{ $transaction->memo }}</dd>
            </tr>
        @endif
    </table>

    @if(!empty($present))
        <hr class="divider">
        <p class="section-title">Payment details</p>
        <table width="100%">
            @foreach($present as $key => $label)
                <tr class="row">
                    <dt>{{ $label }}</dt>
                    <dd>{{ $meta[$key] }}</dd>
                </tr>
            @endforeach
        </table>
    @endif

    @if($transaction->proof_file_path)
        <hr class="divider">
        <p class="section-title">Proof of payment</p>
        <a href="{{ route('storage.file', $transaction->proof_file_path) }}" class="proof-link">Open attachment</a>
    @endif

    @if($statusEnum === \App\Enums\TransactionStatus::Rejected && $transaction->rejection_reason)
        <hr class="divider">
        <p class="section-title" style="color: #dc2626;">Reason for failure</p>
        <p style="font-size: 11px; white-space: pre-line;">{{ $transaction->rejection_reason }}</p>
    @endif

    <hr class="divider">

    <p class="section-title">Activity</p>
    <ul class="timeline">
        <li>
            <span class="dot" style="background: #3b82f6;"></span>
            <span class="muted">Request submitted</span>
            <span class="muted small" style="float: right;">{{ $transaction->created_at->format('j M Y, H:i') }}</span>
        </li>
        @if($settledAt)
            <li>
                <span class="dot" style="background: {{ $statusInfo['color'] }};"></span>
                <span style="color: {{ $statusInfo['color'] }}; font-weight: bold;">{{ $statusInfo['label'] }}</span>
                <span class="muted small" style="float: right;">{{ $settledAt->format('j M Y, H:i') }}</span>
            </li>
        @else
            <li>
                <span class="dot" style="background: #ccc;"></span>
                <span class="muted small">Awaiting confirmation</span>
            </li>
        @endif
    </ul>

    <hr class="divider">

    <p class="muted small">{{ __('Receipt generated') }} {{ $generatedAt->format('j M Y, H:i') }}</p>
    <p class="muted small">{{ __('This document confirms the transaction record held by Bastion Peak. Please retain it for your records. Contact support if any detail appears incorrect.') }}</p>
</div>
</body>
</html>
