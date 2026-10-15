@php
    $decimals = $transaction->currency?->decimals ?? 2;
    $symbol = $transaction->currency?->symbol ?? '';
    $code = $transaction->currency?->code ?? 'USD';
    $amount = number_format((float) $transaction->amount, $decimals);
    $fee = number_format((float) $transaction->fee, $decimals);
    $net = number_format((float) ($transaction->net_amount ?? $transaction->amount), $decimals);
    $meta = is_array($metadata) ? $metadata : [];

    $statusEnum = $transaction->status instanceof \App\Enums\TransactionStatus
        ? $transaction->status
        : \App\Enums\TransactionStatus::from((string) $transaction->status);

    $statusMap = [
        'pending_review' => ['label' => 'Pending',      'color' => '#D97706', 'bg' => '#FFFBEB'],
        'pending_match'  => ['label' => 'Pending',      'color' => '#D97706', 'bg' => '#FFFBEB'],
        'approved'       => ['label' => 'Approved',     'color' => '#059669', 'bg' => '#ECFDF5'],
        'credited'       => ['label' => 'Credited',     'color' => '#059669', 'bg' => '#ECFDF5'],
        'posted'         => ['label' => 'Completed',    'color' => '#059669', 'bg' => '#ECFDF5'],
        'rejected'       => ['label' => 'Failed',       'color' => '#DC2626', 'bg' => '#FEF2F2'],
        'reversed'       => ['label' => 'Reversed',     'color' => '#D97706', 'bg' => '#FFFBEB'],
    ];
    $statusInfo = $statusMap[$statusEnum->value] ?? ['label' => ucfirst($statusEnum->value), 'color' => '#64748B', 'bg' => '#F1F5F9'];

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

    $creditedAt = $transaction->credited_at ?: ($transaction->reviewed_at ?? null);
    $settledAt = match ($statusEnum->value) {
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
    <title>{{ __('Receipt') }} — Bastion Peak</title>
    <style>
        body { font-family: "Helvetica Neue", Arial, sans-serif; margin: 0; padding: 0; background: #F1F5F9; color: #0F172A; }
        .page { max-width: 794px; margin: 2rem auto; background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; overflow: hidden; }
        .header { padding: 24px 32px 20px; border-bottom: 1px solid #E2E8F0; display: flex; align-items: center; justify-content: space-between; }
        .brand { display: flex; align-items: center; gap: 12px; }
        .logo { width: 40px; height: 40px; background: #1E3A8A; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #FFFFFF; font-weight: 700; font-size: 18px; }
        .brand-name { font-weight: 700; font-size: 18px; color: #1E3A8A; }
        .status-badge { padding: 6px 14px; border-radius: 9999px; font-size: 12px; font-weight: 600; color: {{ $statusInfo['color'] }}; background: {{ $statusInfo['bg'] }}; }
        .amount-section { padding: 32px; text-align: center; border-bottom: 1px solid #E2E8F0; }
        .amount-label { font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #64748B; margin-bottom: 8px; }
        .amount-value { font-size: 36px; font-weight: 700; color: {{ $isDeposit ? '#059669' : '#0F172A' }}; }
        .amount-currency { font-size: 16px; font-weight: 600; color: #64748B; }
        .amount-fee { font-size: 12px; color: #64748B; margin-top: 8px; }
        .status-note { padding: 16px 32px; border-bottom: 1px solid #E2E8F0; background: #F8FAFC; }
        .status-note-text { font-size: 12px; color: #64748B; line-height: 1.6; }
        .details-section { padding: 0 32px; }
        .details-row { display: flex; justify-content: space-between; gap: 16px; padding: 12px 0; border-bottom: 1px solid #F1F5F9; }
        .details-row dt { font-size: 12px; text-transform: uppercase; letter-spacing: 0.5px; color: #94A3B8; }
        .details-row dd { font-size: 13px; font-weight: 600; color: #0F172A; text-align: right; word-break: break-word; }
        .details-row:last-child { border-bottom: none; }
        .details-title { font-size: 13px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; margin: 24px 0 12px; }
        .timeline-item { display: flex; gap: 8px; padding: 8px 0; }
        .timeline-dot { width: 8px; height: 8px; border-radius: 9999px; background: #3B82F6; margin-top: 2px; flex-shrink: 0; }
        .timeline-dot--danger { background: #DC2626; }
        .timeline-dot--success { background: #059669; }
        .timeline-dot--muted { background: #CBD5E1; }
        .timeline-text { font-size: 13px; color: #64748B; }
        .timeline-head { font-size: 13px; font-weight: 600; color: #0F172A; }
        .rejection-box { margin: 16px 32px; padding: 20px; background: #FEF2F2; border: 1px solid #FECACA; border-radius: 12px; }
        .rejection-title { font-size: 13px; font-weight: 700; color: #DC2626; margin-bottom: 8px; }
        .rejection-text { font-size: 13px; color: #0F172A; line-height: 1.6; white-space: pre-line; }
        .footer { padding: 20px 32px; text-align: center; border-top: 1px solid #E2E8F0; }
        .footer-text { font-size: 11px; color: #94A3B8; line-height: 1.6; max-width: 500px; margin: 0 auto 8px; }
        .footer-generated { font-size: 11px; color: #CBD5E1; margin-bottom: 4px; }
        .proof-link { display: inline-block; padding: 8px 16px; background: #EFF6FF; color: #2563EB; text-decoration: none; border-radius: 8px; font-size: 12px; font-weight: 600; }
        .divider-section { padding: 0 32px; }
        h2 { font-size: 13px; font-weight: 700; color: #64748B; text-transform: uppercase; letter-spacing: 0.5px; margin: 24px 0 12px; }
    </style>
</head>
<body>
<div class="page">
    <div class="header">
        <div class="brand">
            <div class="logo">BP</div>
            <span class="brand-name">Bastion Peak</span>
        </div>
        <span class="status-badge">{{ $statusInfo['label'] }}</span>
    </div>

    <div class="amount-section">
        <p class="amount-label">{{ $isDeposit ? 'Received' : 'Sent' }}</p>
        <p class="amount-value">{{ $isDeposit ? '+' : '-' }}{{ $amount }} <span class="amount-currency">{{ $symbol }}</span></p>
        <p class="amount-currency">{{ $code }}</p>
        @if((float) $transaction->fee > 0)
            <p class="amount-fee">Fee: {{ $fee }} {{ $symbol }}</p>
        @endif
    </div>

    @if($statusInfo['note'] ?? '')
        <div class="status-note">
            <p class="status-note-text">{{ __('This transaction is currently ') . strtolower($statusInfo['label']) . '. ' }} {{ ['pending_review' => 'It has not been applied to your balance yet.', 'pending_match' => 'It has not been applied to your balance yet.'][$statusEnum->value] ?? '' }}</p>
        </div>
    @endif

    <div class="divider-section">
        <h2>{{ __('Details') }}</h2>
        <dl class="details-section">
            <div class="details-row">
                <dt>{{ __('Reference') }}</dt>
                <dd>{{ $transaction->transaction_id ?? 'TXN-' . strtoupper(substr((string) $transaction->id, 0, 8)) }}</dd>
            </div>
            @if($transaction->reference)
                <div class="details-row">
                    <dt>{{ __('Payment reference') }}</dt>
                    <dd>{{ $transaction->reference }}</dd>
                </div>
            @endif
            <div class="details-row">
                <dt>{{ __('Date &amp; time') }}</dt>
                <dd>{{ $transaction->created_at->format('j M Y, H:i') }}</dd>
            </div>
            @if($settledAt)
                <div class="details-row">
                    <dt>{{ __('Processed at') }}</dt>
                    <dd>{{ $settledAt->format('j M Y, H:i') }}</dd>
                </div>
            @endif
            <div class="details-row">
                <dt>{{ __('Type') }}</dt>
                <dd>{{ $typeLabel }}</dd>
            </div>
            @if($origin)
                <div class="details-row">
                    <dt>{{ $isDeposit ? 'From' : 'From account' }}</dt>
                    <dd>{{ $origin }}</dd>
                </div>
            @endif
            @if($destination)
                <div class="details-row">
                    <dt>{{ 'Beneficiary / to' }}</dt>
                    <dd>{{ $destination }}</dd>
                </div>
            @endif
            <div class="details-row">
                <dt>{{ __('Amount') }}</dt>
                <dd>{{ $amount }} {{ $symbol }}</dd>
            </div>
            <div class="details-row">
                <dt>{{ __('Fee') }}</dt>
                <dd>{{ $fee }} {{ $symbol }}</dd>
            </div>
            <div class="details-row">
                <dt>{{ __('Net amount') }}</dt>
                <dd>{{ $net }} {{ $symbol }}</dd>
            </div>
            @if($transaction->memo)
                <div class="details-row">
                    <dt>{{ __('Description') }}</dt>
                    <dd>{{ $transaction->memo }}</dd>
                </div>
            @endif
        </dl>
    </div>

    @if(!empty($present))
        <div class="divider-section">
            <h2>{{ __('Payment details') }}</h2>
            <dl class="details-section">
                @foreach($present as $key => $label)
                    <div class="details-row">
                        <dt>{{ $label }}</dt>
                        <dd>{{ $meta[$key] }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    @endif

    @if($transaction->proof_file_path)
        <div class="divider-section">
            <h2>{{ __('Proof of payment') }}</h2>
            <div>
                <a href="{{ route('storage.file', $transaction->proof_file_path) }}" class="proof-link">{{ __('Open attachment') }}</a>
            </div>
        </div>
    @endif

    @if($statusEnum === \App\Enums\TransactionStatus::Rejected && $transaction->rejection_reason)
        <div class="rejection-box">
            <div class="rejection-title">{{ __('Reason for failure') }}</div>
            <div class="rejection-text">{{ $transaction->rejection_reason }}</div>
        </div>
    @endif

    <div class="divider-section">
        <h2>{{ __('Activity') }}</h2>
        <div class="details-section">
            <div class="timeline-item">
                <span class="timeline-dot"></span>
                <div>
                    <p class="timeline-head">{{ __('Request submitted') }}</p>
                    <p class="timeline-text">{{ $transaction->created_at->format('j M Y, H:i') }}</p>
                </div>
            </div>
            @if($settledAt)
                <div class="timeline-item">
                    <span class="timeline-dot {{ $statusEnum->isFinalFailure() ? 'timeline-dot--danger' : ($statusInfo['color'] === '#DC2626' ? 'timeline-dot--danger' : ($statusInfo['color'] === '#059669' ? 'timeline-dot--success' : 'timeline-dot')) }}"></span>
                    <div>
                        <p class="timeline-head">{{ $statusInfo['label'] }}</p>
                        <p class="timeline-text">{{ $settledAt->format('j M Y, H:i') }}</p>
                    </div>
                </div>
            @else
                <div class="timeline-item">
                    <span class="timeline-dot timeline-dot--muted"></span>
                    <div>
                        <p class="timeline-text">{{ __('Awaiting confirmation') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="footer">
        <p class="footer-generated">{{ __('Receipt generated') }} {{ $generatedAt->format('j M Y, H:i') }}</p>
        <p class="footer-text">{{ __('This document confirms the transaction record held by Bastion Peak. Please retain it for your records. Contact support if any detail appears incorrect.') }}</p>
    </div>
</div>
</body>
</html>
