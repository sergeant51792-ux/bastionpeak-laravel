<?php

declare(strict_types=1);

namespace App\Pages\Admin;

use App\Services\LedgerService;
use App\Services\BalanceCheckService;
use App\Services\DepositService;
use App\Services\PaymentService;
use App\Services\NotificationService;
use App\Models\Transaction;
use App\Models\AuditLog;
use Filament\Pages\Page;
use App\Widgets\StatsOverview;
use App\Widgets\PendingApprovalsWidget;
use App\Widgets\AnomalyWidget;
use App\Widgets\RecentActionsWidget;

class Overview extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Overview';
    protected static ?string $title = 'Overview';
    protected static ?string $slug = 'overview';
    protected static ?string $navigationGroup = 'Management';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.overview';

    public function getHeading(): string
    {
        return 'Overview';
    }

    public function getSubheading(): ?string
    {
        return 'Bastion Peak internal banking system';
    }

    public function pendingDeposits()
    {
        return Transaction::where('type', 'deposit_credit')
            ->whereIn('status', ['pending_review', 'pending_match'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
    }

    public function pendingPayments()
    {
        return Transaction::where('type', 'payment_debit')
            ->whereIn('status', ['pending_review'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();
    }

    public function systemBalance(): float
    {
        return app(LedgerService::class)->getSystemBalance();
    }

    public function anomalies(): array
    {
        return app(BalanceCheckService::class)->detectAnomalies();
    }

    public function recentActions()
    {
        return AuditLog::orderByDesc('created_at')
            ->limit(10)
            ->get();
    }
}
