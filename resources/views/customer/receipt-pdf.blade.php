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
        'pending_review' => ['label' => 'Pending',    'color' => '#F59E0B', 'icon' => 'clock'],
        'pending_match'  => ['label' => 'Pending',    'color' => '#F59E0B', 'icon' => 'clock'],
        'approved'       => ['label' => 'Successful', 'color' => '#10B981', 'icon' => 'check'],
        'credited'       => ['label' => 'Successful', 'color' => '#10B981', 'icon' => 'check'],
        'posted'         => ['label' => 'Successful', 'color' => '#10B981', 'icon' => 'check'],
        'rejected'       => ['label' => 'Failed',     'color' => '#EF4444', 'icon' => 'x'],
        'reversed'       => ['label' => 'Failed',     'color' => '#EF4444', 'icon' => 'x'],
    ];
    $statusInfo = $statusMap[$statusEnum->value]
        ?? ['label' => ucfirst($statusEnum->value), 'color' => '#64748B', 'icon' => 'circle'];

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

    $description = $transaction->memo ?: ($meta['user_note'] ?? null) ?: 'None';

    $generatedAt = $issuedAt ?? now();
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt - Bastion Peak</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; margin: 0; padding: 0; color: #1F2937; background: #F3F4F6; }
        .page { max-width: 794px; margin: 24px auto; background: #FFFFFF; padding: 40px 48px; font-size: 13px; line-height: 1.5; }
        .row { display: flex; justify-content: space-between; gap: 16px; padding: 6px 0; border-bottom: 1px solid #F3F4F6; }
        .row:last-child { border-bottom: none; }
        .row dt { color: #9CA3AF; font-size: 12px; }
        .row dd { font-weight: 600; text-align: right; word-break: break-word; font-variant-numeric: tabular-nums; }
        .row.total { border-top: 1px solid #E5E7EB; margin-top: 4px; padding-top: 8px; font-size: 14px; }
        .row.total dt { color: #374151; }
        hr { border: 0; height: 1px; background: #E5E7EB; margin: 16px 0; }
        .muted { color: #9CA3AF; }
        .small { font-size: 10px; }
        .center { text-align: center; }
        .amount { font-size: 28px; font-weight: 700; }
        .status-circle { width: 24px; height: 24px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 12px; }
        .section-title { color: #9CA3AF; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; margin: 16px 0 8px; }
        .proof-link { color: #2563EB; text-decoration: none; font-weight: 600; }
        .timeline-item { display: flex; gap: 8px; margin-bottom: 8px; }
        .timeline-dot { width: 16px; height: 16px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: white; font-size: 10px; }
        .watermark { text-align: center; font-size: 48px; font-weight: 800; color: #D1D5DB; margin: 20px 0; letter-spacing: 0.3em; }
        h1 { font-size: 18px; font-weight: 700; color: #1E3A8A; margin-bottom: 2px; }
        h2 { font-size: 12px; font-weight: 600; color: #6B7280; text-transform: uppercase; letter-spacing: 0.5px; margin: 16px 0 8px; }
        .brand { display: flex; align-items: center; gap: 8px; margin-bottom: 20px; }
        .brand-logo { width: 32px; height: 32px; background: #1E3A8A; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 14px; }
        .payment-method-box { background: #F9FAFB; border: 1px solid #E5E7EB; border-radius: 8px; padding: 12px 16px; margin: 8px 0; }
        .stamp { margin-top: 20px; padding-top: 12px; border-top: 1px solid #E5E7EB; font-size: 10px; color: #9CA3AF; }
    </style>
</head>
<body>
<div class="page">
    <div class="watermark">BASTION PEAK</div>

    <div class="brand">
        <div class="brand-logo">BP</div>
        <h1>Bastion Peak</h1>
    </div>

    <div class="row">
        <dt>Status</dt>
        <dd class="flex items-center gap-2 justify-end">
            <span class="status-circle" style="background: {{ $statusInfo['color'] }};">
                @if($statusInfo['icon'] === 'check')
                    ✓
                @elseif($statusInfo['icon'] === 'x')
                    ✕
                @elseif($statusInfo['icon'] === 'clock')
                    ⏳
                @else
                    {{ $statusInfo['icon'] }}
                @endif
            </span>
            <span style="color: {{ $statusInfo['color'] }}; font-weight: 600;">{{ $statusInfo['label'] }}</span>
        </dd>
    </div>

    <hr>

    <h2>Amount</h2>
    <div class="center" style="margin: 16px 0 20px;">
        <p class="muted small">{{ $isDeposit ? 'RECEIVED' : 'SENT' }}</p>
        <p class="amount" style="color: {{ $isDeposit ? '#10B981' : '#1F2937' }};">
            {{ $isDeposit ? '+' : '-' }}{{ $amount }} {{ $symbol }}
        </p>
        <p class="muted small">{{ $code }}</p>
        @if((float) $transaction->fee > 0)
            <p class="muted small" style="margin-top: 4px;">Fee: {{ $fee }} {{ $symbol }}</p>
        @endif
    </div>

    <hr>

    <h2>Details</h2>
    <table width="100%">
        <tr class="row">
            <dt>Reference</dt>
            <dd>{{ $transaction->transaction_id ?? 'TXN-' . strtoupper(substr((string) $transaction->id, 0, 8)) }}</dd>
        </tr>
        @if($transaction->reference)
            <tr class="row">
                <dt>Payment reference</dt>
                <dd>{{ $transaction->reference }}</dd>
            </tr>
        @endif
        <tr class="row">
            <dt>Date &amp; time</dt>
            <dd>{{ $transaction->created_at->format('j M Y, H:i') }}</dd>
        </tr>
        @if($settledAt)
            <tr class="row">
                <dt>Processed at</dt>
                <dd>{{ $settledAt->format('j M Y, H:i') }}</dd>
            </tr>
        @endif
        <tr class="row">
            <dt>Type</dt>
            <dd>{{ $typeLabel }}</dd>
        </tr>
        @if($origin)
            <tr class="row">
                <dt>{{ $isDeposit ? 'From' : 'From account' }}</dt>
                <dd>{{ $origin }}</dd>
            </tr>
        @endif
        @if($destination)
            <tr class="row">
                <dt>Beneficiary / to</dt>
                <dd>{{ $destination }}</dd>
            </tr>
        @endif
        <tr class="row total">
            <dt>Amount</dt>
            <dd>{{ $amount }} {{ $symbol }}</dd>
        </tr>
        <tr class="row">
            <dt>Description</dt>
            <dd style="white-space: pre-line;">{{ $description }}</dd>
        </tr>
    </table>

    @if($paymentMethod || !empty($bankPresent) || !empty($cryptoPresent))
        <hr>
        <h2>Payment method</h2>
        <div class="payment-method-box">
            @php
                $pmLabels = [
                    'wire' => 'Bank Transfer',
                    'bank' => 'Bank Transfer',
                    'card' => 'Card Payment',
                    'crypto' => 'Cryptocurrency',
                    'giftcard' => 'Gift Card',
                ];
                $pmLabel = $pmLabels[$paymentMethod ?? ''] ?? ($paymentMethod ? ucfirst($paymentMethod) : '—');
            @endphp
            <p style="font-weight: 600; margin-bottom: 8px;">{{ $pmLabel }}</p>

            @if($paymentMethod === 'wire' || $paymentMethod === 'bank')
                @foreach($bankPresent as $key => $label)
                    <div class="row">
                        <dt>{{ $label }}</dt>
                        <dd style="font-family: monospace;">{{ $meta[$key] }}</dd>
                    </div>
                @endforeach
            @endif

            @if(!empty($cryptoPresent))
                @foreach($cryptoPresent as $key => $label)
                    <div class="row">
                        <dt>{{ $label }}</dt>
                        <dd style="font-family: monospace;">{{ $meta[$key] }}</dd>
                    </div>
                @endforeach
            @endif
        </div>
    @endif

    @if($transaction->proof_file_path)
        <hr>
        <h2>Proof of payment</h2>
        <a href="{{ route('storage.file', $transaction->proof_file_path) }}" class="proof-link">Open attachment</a>
    @endif

    @if($statusEnum === \App\Enums\TransactionStatus::Rejected && $transaction->rejection_reason)
        <hr>
        <h2 style="color: #DC2626;">Reason for failure</h2>
        <div style="font-size: 11px; white-space: pre-line; color: #374151;">{{ $transaction->rejection_reason }}</div>
    @endif

    <hr>

    <h2>Activity</h2>
    <div class="timeline-item">
        <span class="timeline-dot" style="background: #3B82F6;">•</span>
        <div>
            <div style="font-weight: 600; font-size: 12px;">Request submitted</div>
            <div class="muted small">{{ $transaction->created_at->format('j M Y, H:i') }}</div>
        </div>
    </div>
    @if($settledAt)
        <div class="timeline-item">
            <span class="timeline-dot" style="background: {{ $statusInfo['color'] }};">•</span>
            <div>
                <div style="font-weight: 600; font-size: 12px; color: {{ $statusInfo['color'] }};">{{ $statusInfo['label'] }}</div>
                <div class="muted small">{{ $settledAt->format('j M Y, H:i') }}</div>
            </div>
        </div>
    @else
        <div class="timeline-item">
            <span class="timeline-dot" style="background: #D1D5DB;">•</span>
            <div class="muted small">Awaiting confirmation</div>
        </div>
    @endif

    <div class="stamp">
        <div style="font-weight: 600; color: #1E3A8A; margin-bottom: 4px;">BASTION PEAK FINANCIAL SERVICES</div>
        <div>{{ __('Receipt generated') }} {{ $generatedAt->format('j M Y, H:i') }}</div>
        <div>{{ __('This document confirms the transaction record held by Bastion Peak. Please retain it for your records.') }}</div>
    </div>
</div>
</body>
</html>
