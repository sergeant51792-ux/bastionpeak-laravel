<?php

declare(strict_types=1);

namespace App\Pages\Admin;

use App\Enums\TransactionType;
use App\Models\Transaction;
use Filament\Pages\Page;

class Approvals extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $navigationLabel = 'Approvals';
    protected static ?string $title = 'Approvals';
    protected static ?string $slug = 'approvals';
    protected static ?string $navigationGroup = 'Management';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.approvals';

    public ?string $activeTab = 'deposits';
    public ?int $openTransactionId = null;

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getQueueProperty()
    {
        if ($this->activeTab === 'deposits') {
            return Transaction::where('type', 'deposit_credit')
                ->whereIn('status', ['pending_review', 'pending_match'])
                ->orderByDesc('created_at')
                ->get();
        }

        return Transaction::where('type', 'payment_debit')
            ->whereIn('status', ['pending_review'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function getOpenTransactionProperty()
    {
        if ($this->openTransactionId) {
            return Transaction::find($this->openTransactionId);
        }

        return $this->queue->first();
    }

    public function approve(int $transactionId): void
    {
        $transaction = Transaction::findOrFail($transactionId);
        if ($transaction->type === TransactionType::DepositCredit) {
            app(\App\Services\DepositService::class)->approve($transactionId, auth()->id());
        } elseif ($transaction->type === TransactionType::PaymentDebit) {
            app(\App\Services\PaymentService::class)->approve($transactionId, auth()->id());
        }
    }

    public function reject(int $transactionId): void
    {
        $transaction = Transaction::findOrFail($transactionId);
        $reason = 'Rejected during review';

        if ($transaction->type === TransactionType::DepositCredit) {
            app(\App\Services\DepositService::class)->reject($transactionId, auth()->id(), $reason);
        } elseif ($transaction->type === TransactionType::PaymentDebit) {
            app(\App\Services\PaymentService::class)->reject($transactionId, auth()->id(), $reason);
        }
    }
}
