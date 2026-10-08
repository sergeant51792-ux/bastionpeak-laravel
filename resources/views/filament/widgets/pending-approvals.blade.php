<div class="space-y-3">
    @php
        $pendingDeposits = \App\Models\Transaction::where('type', \App\Enums\TransactionType::DepositCredit->value)->whereIn('status', [\App\Enums\TransactionStatus::PendingReview->value, \App\Enums\TransactionStatus::PendingMatch->value])->count();
        $pendingPayments = \App\Models\Transaction::where('type', \App\Enums\TransactionType::PaymentDebit->value)->where('status', \App\Enums\TransactionStatus::PendingReview->value)->count();
        $unreadMessages = \App\Models\Notification::whereNull('read_at')->where('type', 'customer_message')->count();
        $total = $pendingDeposits + $pendingPayments + $unreadMessages;
    @endphp

    @if($total > 0)
        <a href="{{ route('admin.approvals') }}" class="flex items-center justify-between p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg border border-primary-200 dark:border-primary-700 hover:bg-primary-100 transition-colors">
            <div>
                <p class="font-semibold">{{ $total }} items need a decision</p>
                <p class="text-sm text-gray-500">{{ $pendingDeposits }} deposits, {{ $pendingPayments }} payments, {{ $unreadMessages }} messages</p>
            </div>
            <x-icon name="chevron-right" class="w-5 h-5 text-primary-600" />
        </a>
    @else
        <p class="text-gray-500 text-sm">Nothing needs a decision right now.</p>
    @endif
</div>
