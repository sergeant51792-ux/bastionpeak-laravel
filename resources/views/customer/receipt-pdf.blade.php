@php
    $decimals = $transaction->currency?->decimals ?? 2;
    $symbol   = $transaction->currency?->symbol ?? '$';
    $code     = $transaction->currency?->code ?? 'USD';
    $amount   = number_format((float) $transaction->amount, $decimals);
    $fee      = (float) ($transaction->fee ?? 0);
    $meta     = is_array($metadata) ? $metadata : [];

    $statusEnum = $transaction->status instanceof \App\Enums\TransactionStatus
        ? $transaction->status
        : \App\Enums\TransactionStatus::from((string) $transaction->status);

    $statusMap = [
        'pending_review' => ['label' => 'Pending',     'color' => '#F59E0B'],
        'pending_match'  => ['label' => 'Pending',     'color' => '#F59E0B'],
        'approved'       => ['label' => 'Successful',  'color' => '#10B981'],
        'credited'       => ['label' => 'Successful',  'color' => '#10B981'],
        'posted'         => ['label' => 'Successful',  'color' => '#10B981'],
        'rejected'       => ['label' => 'Failed',      'color' => '#EF4444'],
        'reversed'       => ['label' => 'Failed',      'color' => '#EF4444'],
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
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt - Bastion Peak</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; margin: 0; padding: 0; color: #1F2937; background: #FFFFFF; }
        .page { max-width: 794px; margin: 32px auto; background: #FFFFFF; padding: 48px 56px; font-size: 12px; line-height: 1.5; }
        .row { display: flex; justify-content: space-between; padding: 4px 0; }
        .row dt { color: #9CA3AF; font-size: 10px; }
        .row dd { font-weight: 500; text-align: right; font-variant-numeric: tabular-nums; }
        .section { margin-top: 16px; }
        .section-title { color: #9CA3AF; font-size: 9px; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 8px; }
        hr { border: 0; height: 1px; background: #E5E7EB; margin: 14px 0; }
        .muted { color: #9CA3AF; }
        .mono { font-family: "SF Mono", "Fira Code", Consolas, "Liberation Mono", Menlo, monospace; }
        .amount { font-size: 24px; font-weight: 700; }
        .status-pill { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 11px; font-weight: 600; }
        .watermark { text-align: center; font-size: 10px; color: #D1D5DB; margin: 16px 0; letter-spacing: 1px; }
        .grid2 { display: grid; grid-template-columns: 1fr 1fr; gap: 2px 16px; }
        .proof-link { color: #2563EB; text-decoration: none; font-size: 11px; }
        .footer { margin-top: 24px; padding-top: 12px; border-top: 1px solid #E5E7EB; text-align: center; }
        .small { font-size: 10px; color: #9CA3AF; }
    </style>
</head>
<body>
<div class="page">

    <div class="row section">
        <dt style="color: #9CA3AF; font-size: 9px; text-transform: uppercase;">Bastion Peak</dt>
        <dd style="color: #1E3A8A; font-weight: 700; font-size: 14px;">Receipt</dd>
    </div>

    <div class="row">
        <dt class="muted">Status</dt>
        <dd>
            <span class="status-pill" style="background: {{ $statusInfo['color'] }}20; color: {{ $statusInfo['color'] }};">
                {{ $statusInfo['label'] }}
            </span>
        </dd>
    </div>

    <div class="row" style="margin-top: 8px;">
        <dt class="muted">{{ $isDeposit ? 'CREDITED' : 'DEBITED' }}</dt>
        <dd class="amount" style="color: {{ $statusInfo['color'] }};">
            {{ $isDeposit ? '+' : '−' }}{{ $amount }} {{ $symbol }}
        </dd>
    </div>

    @if($fee > 0)
        <div class="row">
            <dt class="muted">Fee</dt>
            <dd class="mono">{{ $fee }} {{ $symbol }}</dd>
        </div>
    @endif

    <hr>

    <div class="grid2 section">
        <div>
            <div class="section-title">Reference</div>
            <div class="mono">{{ $ref }}</div>
        </div>
        <div style="text-align: right;">
            <div class="section-title">Date</div>
            <div class="mono">{{ $transaction->created_at->format('d M Y, H:i') }}</div>
        </div>
        <div>
            <div class="section-title">Type</div>
            <div>{{ $typeLabel }}</div>
        </div>
        @if($settledAt)
            <div style="text-align: right;">
                <div class="section-title">Processed</div>
                <div class="mono">{{ $settledAt->format('d M Y, H:i') }}</div>
            </div>
        @else
            <div></div>
        @endif
        @if($origin)
            <div>
                <div class="section-title">{{ $isDeposit ? 'FROM' : 'SOURCE' }}</div>
                <div>{{ $origin }}</div>
            </div>
        @endif
        @if($destination)
            <div style="text-align: right;">
                <div class="section-title">{{ $isDeposit ? 'TO' : 'DESTINATION' }}</div>
                <div>{{ $destination }}</div>
            </div>
        @else
            <div></div>
        @endif
    </div>

    <div class="section">
        <div class="section-title">Description</div>
        <div style="white-space: pre-line;">{{ $description }}</div>
    </div>

    @if($paymentMethod || !empty($bankPresent) || !empty($cryptoPresent))
        <hr>
        <div class="section">
            <div class="section-title">Payment method</div>
            @php
                $pmLabels = [
                    'wire'   => 'Bank transfer',
                    'bank'   => 'Bank transfer',
                    'card'   => 'Card payment',
                    'crypto' => 'Cryptocurrency',
                    'giftcard' => 'Gift card',
                ];
                $pmLabel = $pmLabels[$paymentMethod ?? ''] ?? ($paymentMethod ? ucfirst($paymentMethod) : null);
            @endphp
            @if($pmLabel)
                <div style="font-weight: 600; margin-bottom: 6px;">{{ $pmLabel }}</div>
            @endif
            @foreach($bankPresent as $key => $label)
                <div class="row">
                    <dt class="muted">{{ $label }}</dt>
                    <dd class="mono">{{ $meta[$key] }}</dd>
                </div>
            @endforeach
            @foreach($cryptoPresent as $key => $label)
                <div class="row">
                    <dt class="muted">{{ $label }}</dt>
                    <dd class="mono">{{ $meta[$key] }}</dd>
                </div>
            @endforeach
        </div>
    @endif

    @if($transaction->proof_file_path)
        <hr>
        <div class="row">
            <dt class="muted">Supporting document</dt>
            <dd><a href="{{ route('storage.file', $transaction->proof_file_path) }}" class="proof-link">Open attachment</a></dd>
        </div>
    @endif

    @if($statusEnum == \App\Enums\TransactionStatus::Rejected && $transaction->rejection_reason)
        <hr>
        <div class="section">
            <div class="section-title">Reason for decline</div>
            <div style="white-space: pre-line; color: #DC2626;">{{ $transaction->rejection_reason }}</div>
        </div>
    @endif

    <div class="watermark">BASTION PEAK</div>

    <div class="footer">
        <div class="small">Generated {{ $generatedAt->format('d M Y, H:i') }} · Reference {{ $ref }}</div>
    </div>

</div>
</body>
</html>
