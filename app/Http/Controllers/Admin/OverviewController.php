<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\DepositService;
use App\Services\PaymentService;
use App\Services\LedgerService;
use App\Services\NotificationService;
use App\Services\BalanceCheckService;
use App\Models\Transaction;

class OverviewController extends Controller
{
    public function __construct(
        protected DepositService    $deposits,
        protected PaymentService    $payments,
        protected BalanceCheckService $balanceCheck,
        protected LedgerService     $ledger,
        protected NotificationService $notifications,
    ) {}

    public function __invoke(Request $request): \Illuminate\View\View
    {
        $pendingDeposits = Transaction::where('type', 'deposit_credit')
            ->whereIn('status', ['pending_review', 'pending_match'])
            ->with('currency')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $pendingPayments = Transaction::where('type', 'payment_debit')
            ->whereIn('status', ['pending_review'])
            ->with('currency')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $needsDecision = $pendingDeposits->count() + $pendingPayments->count()
            + \App\Models\Notification::where('read_at', null)
                ->where('type', 'customer_message')
                ->count();

        $anomalies = $this->balanceCheck->detectAnomalies();

        $recentActions = \App\Models\AuditLog::orderByDesc('created_at')
            ->limit(10)
            ->get();

        $systemBalance = $this->ledger->getSystemBalance();

        return view('admin.overview', [
            'pendingDeposits'   => $pendingDeposits,
            'pendingPayments'   => $pendingPayments,
            'needsDecision'     => $needsDecision,
            'anomalies'         => $anomalies,
            'recentActions'     => $recentActions,
            'systemBalance'     => $systemBalance,
            'volume30Days'      => Transaction::where('created_at', '>=', now()->subDays(30))->count(),
            'todayCount'        => Transaction::whereDate('created_at', today())->count(),
        ]);
    }
}
