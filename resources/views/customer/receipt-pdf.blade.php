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
    <title>Receipt &mdash; Bastion Peak</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Courier New", Courier, monospace; background: #f5f5f5; color: #1a1a1a; padding: 20px; }
        .page { max-width: 794px; margin: 0 auto; background: #ffffff; padding: 24px 40px; font-size: 13px; line-height: 1.4; position: relative; }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-25deg);
            font-size: 64px;
            font-weight: 900;
            color: #000;
            opacity: 0.03;
            pointer-events: none;
            white-space: nowrap;
            letter-spacing: 0.3em;
        }
        .row { display: flex; justify-content: space-between; gap: 16px; padding: 6px 0; line-height: 1.5; }
        .row dt { color: #666; font-size: 11px; }
        .row dd { font-weight: bold; text-align: right; word-break: break-word; font-variant-numeric: tabular-nums; }
        .row.total { border-top: 1px solid #e0e0e0; margin-top: 4px; padding-top: 6px; font-size: 14px; }
        .row.total dt { font-weight: bold; color: #333; }
        hr { border: 0; height: 1px; background: #e0e0e0; margin: 12px 0; }
        .label { color: #666; font-size: 11px; }
        .value { font-weight: bold; }
        .center { text-align: center; }
        .amount { font-size: 24px; font-weight: bold; font-variant-numeric: tabular-nums; }
        .small { font-size: 10px; }
        .muted { color: #999; }
        .status { font-weight: bold; font-size: 10px; text-transform: uppercase; }
        .proof-link { color: #2563eb; text-decoration: none; font-weight: bold; }
        h2 { color: #666; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; margin: 12px 0 6px; }
        .timeline { list-style: none; }
        .timeline li { display: flex; gap: 6px; margin-bottom: 6px; }
        .dot { width: 4px; height: 4px; border-radius: 50%; flex-shrink: 0; margin-top: 3px; }
        .rejection { color: #dc2626; margin-top: 8px; }
    </style>
</head>
<body>
<div class="page">
    <div class="watermark">BASTION PEAK</div>

    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 24px;">
        <span style="font-weight: 700; font-size: 20px; color: #1E3A8A;">Bastion Peak</span>
    </div>
    <div class="row" style="margin-bottom: 16px;">
        <dt class="label">BASTION PEAK</dt>
        <dd class="value" style="font-size: 13px;">{{ $transaction->transaction_id ?? 'TXN-' . strtoupper(substr((string) $transaction->id, 0, 8)) }}</dd>
    </div>

    <div class="center" style="margin: 20px 0;">
        <p class="label" style="margin-bottom: 6px;">{{ $isDeposit ? 'RECEIVED' : 'SENT' }}</p>
        <p class="amount">{{ $isDeposit ? '+' : '-' }}{{ $amount }} {{ $symbol }}</p>
        <p class="muted small">{{ $code }}</p>
        @if((float) $transaction->fee > 0)
            <p class="muted small" style="margin-top: 4px;">Fee: {{ $fee }} {{ $symbol }}</p>
        @endif
    </div>

    <div class="row">
        <dt class="label">Status</dt>
        <dd class="value" style="color: {{ $statusInfo['color'] }}">{{ $statusInfo['label'] }}</dd>
    </div>

    <hr>

    <h2>Details</h2>
    <div class="row">
        <dt class="label">Reference</dt>
        <dd class="value">{{ $transaction->transaction_id ?? 'TXN-' . strtoupper(substr((string) $transaction->id, 0, 8)) }}</dd>
    </div>

    @if($transaction->reference)
        <div class="row">
            <dt class="label">Payment reference</dt>
            <dd class="value">{{ $transaction->reference }}</dd>
        </div>
    @endif

    <div class="row">
        <dt class="label">Date &amp; time</dt>
        <dd class="value">{{ $transaction->created_at->format('j M Y, H:i') }}</dd>
    </div>

    @if($settledAt)
        <div class="row">
            <dt class="label">Processed at</dt>
            <dd class="value">{{ $settledAt->format('j M Y, H:i') }}</dd>
        </div>
    @endif

    <div class="row">
        <dt class="label">Type</dt>
        <dd class="value">{{ $typeLabel }}</dd>
    </div>

    @if($origin)
        <div class="row">
            <dt class="label">{{ $isDeposit ? 'From' : 'From account' }}</dt>
            <dd class="value">{{ $origin }}</dd>
        </div>
    @endif

    @if($destination)
        <div class="row">
            <dt class="label">Beneficiary / to</dt>
            <dd class="value">{{ $destination }}</dd>
        </div>
    @endif

    <div class="row">
        <dt class="label">Amount</dt>
        <dd class="value">{{ $amount }} {{ $symbol }}</dd>
    </div>

    @if((float) $transaction->fee > 0)
        <div class="row">
            <dt class="label">Fee</dt>
            <dd class="value">{{ $fee }} {{ $symbol }}</dd>
        </div>
    @endif

    <div class="row total">
        <dt class="label">Net amount</dt>
        <dd class="value">{{ $net }} {{ $symbol }}</dd>
    </div>

    @if($transaction->memo)
        <div class="row">
            <dt class="label">Note</dt>
            <dd class="value" style="white-space: pre-line;">{{ $transaction->memo }}</dd>
        </div>
    @endif

    @if(!empty($present))
        <hr>
        <h2>Payment details</h2>
        @foreach($present as $key => $label)
            <div class="row">
                <dt class="label">{{ $label }}</dt>
                <dd class="value">{{ $meta[$key] }}</dd>
            </div>
        @endforeach
    @endif

    @if($transaction->proof_file_path)
        <hr>
        <h2>Proof of payment</h2>
        <div class="row">
            <dd class="value" style="text-align: left;">
                <a href="{{ route('storage.file', $transaction->proof_file_path) }}" class="proof-link">{{ __('Open attachment') }}</a>
            </dd>
        </div>
    @endif

    @if($statusEnum === \App\Enums\TransactionStatus::Rejected && $transaction->rejection_reason)
        <hr>
        <h2 class="rejection">Reason for failure</h2>
        <div class="row">
            <dd class="value" style="text-align: left; color: #333;">{{ $transaction->rejection_reason }}</dd>
        </div>
    @endif

    <hr>

    <h2>Activity</h2>
    <ul class="timeline">
        <li>
            <span class="dot" style="background: #3b82f6;"></span>
            <div>
                <p class="value" style="font-size: 12px;">Request submitted</p>
                <p class="muted small">{{ $transaction->created_at->format('j M Y, H:i') }}</p>
            </div>
        </li>
        @if($settledAt)
            <li>
                <span class="dot" style="background: {{ $statusInfo['color'] }};"></span>
                <div>
                    <p class="value" style="font-size: 12px; color: {{ $statusInfo['color'] }}">{{ $statusInfo['label'] }}</p>
                    <p class="muted small">{{ $settledAt->format('j M Y, H:i') }}</p>
                </div>
            </li>
        @else
            <li>
                <span class="dot" style="background: #ccc;"></span>
                <p class="muted small">Awaiting confirmation</p>
            </li>
        @endif
    </ul>

    <hr style="margin-top: 16px;">

    <p class="muted small" style="margin-bottom: 4px;">{{ __('Receipt generated') }} {{ $generatedAt->format('j M Y, H:i') }}</p>
    <p class="muted small">{{ __('This document confirms the transaction record held by Bastion Peak. Please retain it for your records. Contact support if any detail appears incorrect.') }}</p>
</div>
</body>
</html>
