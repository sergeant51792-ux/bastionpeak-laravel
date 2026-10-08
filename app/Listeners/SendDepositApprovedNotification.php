<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\DepositApproved;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Bastion Peak Internal Banking System — Send Deposit Approved Notification Listener
 *
 * Sends an in-app and email notification when a deposit is approved.
 */
class SendDepositApprovedNotification implements ShouldQueue
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
     * @param DepositApproved $event
     * @return void
     */
    public function handle(DepositApproved $event): void
    {
        $transaction = $event->transaction;
        $customerId = $transaction->customer_id;

        $this->notificationService->sendToUser(
            $customerId,
            'deposit_approved',
            'Deposit Approved',
            'Your deposit of ' . $transaction->amount . ' has been approved and credited.',
            'in-app'
        );

        $this->notificationService->sendToUser(
            $customerId,
            'deposit_approved',
            'Deposit Approved',
            'Your deposit of ' . $transaction->amount . ' has been approved and credited.',
            'email'
        );
    }
}
