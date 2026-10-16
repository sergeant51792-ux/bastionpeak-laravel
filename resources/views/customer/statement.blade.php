@php
    $decimals = 2;
    $symbol   = '$';
    $code     = 'USD';
    $totalDeposits   = number_format((float) $totalDeposits, $decimals);
    $totalWithdrawals= number_format((float) $totalWithdrawals, $decimals);
    $closingBalance  = number_format((float) $closingBalance, $decimals);
    $balanceClass    = (float) $closingBalance >= 0 ? '#10B981' : '#EF4444';
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Statement of Account - {{ $user->name }}</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; margin: 0; padding: 0; color: #1F2937; background: #F3F4F6; }
        .page { max-width: 794px; margin: 24px auto; background: #FFFFFF; padding: 40px 48px; font-size: 12px; line-height: 1.4; }
        .row { display: flex; justify-content: space-between; gap: 16px; padding: 5px 0; }
        .row dt { color: #6B7280; }
        .row dd { font-weight: 600; text-align: right; font-variant-numeric: tabular-nums; }
        hr { border: 0; height: 1px; background: #E5E7EB; margin: 16px 0; }
        .center { text-align: center; }
        .muted { color: #9CA3AF; }
        .small { font-size: 10px; }
        .mini { font-size: 9px; color: #9CA3AF; letter-spacing: 0.3px; }
        h1 { font-size: 16px; font-weight: 700; color: #1E3A8A; margin-bottom: 2px; }
        h2 { font-size: 11px; font-weight: 600; color: #6B7280; text-transform: uppercase; letter-spacing: 0.5px; margin: 20px 0 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 4px 6px; color: #9CA3AF; font-size: 9px; text-transform: uppercase; letter-spacing: 0.3px; border-bottom: 1px solid #E5E7EB; }
        td { padding: 5px 6px; border-bottom: 1px solid #F9FAFB; }
        .text-right { text-align: right; }
        .money-in { color: #10B981; }
        .money-out { color: #DC2626; }
        .stamp { margin-top: 24px; padding-top: 12px; border-top: 1px solid #E5E7EB; }
        .watermark { text-align: center; font-size: 36px; font-weight: 800; color: #F3F4F6; margin: 16px 0; letter-spacing: 0.5em; }
        .header { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
        .logo { width: 32px; height: 32px; background: #1E3A8A; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 14px; }
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: 600; }
    </style>
</head>
<body>
<div class="page">
    <div class="watermark">BASTION PEAK</div>

    <div class="header">
        <div class="logo">BP</div>
        <h1>Bastion Peak Financial Services</h1>
    </div>

    <div class="row">
        <dt>Statement period</dt>
        <dd>{{ $startDate }} → {{ $endDate }}</dd>
    </div>
    <div class="row">
        <dt>Account holder</dt>
        <dd>{{ $user->name }}</dd>
    </div>
    @if($user->email)
        <div class="row">
            <dt>Email address</dt>
            <dd>{{ $user->email }}</dd>
        </div>
    @endif
    <div class="row">
        <dt>Generated</dt>
        <dd>{{ $generatedAt->format('j M Y, H:i') }}</div>
    </div>

    <hr>

    <h2>Summary</h2>
    <div class="row">
        <dt>Total credits</dt>
        <dd class="money-in">+{{ $totalDeposits }}</dd>
    </div>
    <div class="row">
        <dt>Total debits</dt>
        <dd class="money-out">−{{ $totalWithdrawals }}</dd>
    </div>
    <div class="row" style="border-top: 2px solid #E5E7EB; padding-top: 6px; margin-top: 4px;">
        <dt>Closing balance</dt>
        <dd style="color: {{ $balanceClass }}; font-size: 14px;">{{ $closingBalance }}</dd>
    </div>

    <hr>

    <h2>Transactions</h2>
    @if($transactions->isEmpty())
        <p class="center muted small" style="padding: 20px 0;">No transactions in the selected period.</p>
    @else
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Memo / description</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $txn)
                    @php
                        $isIncoming = $txn->isIncoming();
                        $statusEnum = $txn->status instanceof \App\Enums\TransactionStatus
                            ? $txn->status
                            : \App\Enums\TransactionStatus::from((string) $txn->status);
                        $statusLabel = match($statusEnum->value) {
                            'pending_review', 'pending_match' => 'Pending',
                            'approved', 'credited', 'posted'  => 'Completed',
                            'rejected'                        => 'Failed',
                            'reversed'                        => 'Reversed',
                            default                           => ucfirst($statusEnum->value),
                        };
                        $statusColor = match($statusEnum->value) {
                            'pending_review', 'pending_match' => '#F59E0B',
                            'approved', 'credited', 'posted'  => '#10B981',
                            'rejected'                        => '#EF4444',
                            'reversed'                        => '#F59E0B',
                            default                           => '#64748B',
                        };
                    @endphp
                    <tr>
                        <td class="mini">{{ $txn->created_at->format('j M Y') }}</td>
                        <td>{{ $txn->memo ?: $txn->type->customerLabel() }}</td>
                        <td class="mini">{{ $txn->transaction_id ?? 'TXN-' . strtoupper(substr((string) $txn->id, 0, 8)) }}</td>
                        <td>
                            <span class="badge" style="background: {{ $statusColor }}20; color: {{ $statusColor }};">{{ $statusLabel }}</span>
                        </td>
                        <td class="text-right">
                            <span class="{{ $isIncoming ? 'money-in' : 'money-out' }}">
                                {{ $isIncoming ? '+' : '−' }}{{ $txn->amount }} {{ $txn->currency->symbol ?? '' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <hr>

    <div class="stamp">
        <div style="font-weight: 600; color: #1E3A8A; margin-bottom: 4px;">BASTION PEAK FINANCIAL SERVICES</div>
        <div class="mini">{{ $user->name }} · {{ $startDate }} to {{ $endDate }} · Generated {{ $generatedAt->format('j M Y, H:i') }}</div>
        <div class="mini" style="margin-top: 2px;">This statement reflects all non-reversed transactions within the selected period.</div>
    </div>
</div>
</body>
</html>
