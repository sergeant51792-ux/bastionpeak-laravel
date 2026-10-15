<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\DepositRejected;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Bastion Peak Internal Banking System — Send Deposit Rejected Notification Listener
 *
 * Sends an in-app and email notification when a deposit is rejected.
 */
class SendDepositRejectedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @param NotificationService $notificationService
     * @return void
     */
    public function __construct(protected NotificationService $notificationService)
    {
    }

    /**
     * Handle the event.
     *
     * @param DepositRejected $event
     * @return void
     */
    public function handle(DepositRejected $event): void
    {
        $transaction = $event->transaction;

        $customerId = $transaction->to_user_id ?? $transaction->from_user_id;
        if (!$customerId) {
            $accountId = $transaction->to_account_id ?? $transaction->from_account_id;
            $customerId = \App\Models\Account::where('id', $accountId)->value('user_id');
        }
        $reason = $transaction->rejection_reason ?: 'Please contact support for details.';
        $title  = 'Deposit declined';
        $body   = 'Your deposit of ' . $transaction->formattedAmount() . ' was declined. Reason: ' . $reason;

        $this->notificationService->sendToUser($customerId, 'deposit_rejected', $title, $body, 'in-app');
        $this->notificationService->sendToUser($customerId, 'deposit_rejected', $title, $body, 'email');
    }
}
