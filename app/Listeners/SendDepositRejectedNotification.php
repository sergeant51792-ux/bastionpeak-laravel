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
        $customerId = $transaction->customer_id;

        $this->notificationService->sendToUser(
            $customerId,
            'deposit_rejected',
            'Deposit Rejected',
            'Your deposit of ' . $transaction->amount . ' was rejected. Reason: ' . $transaction->memo,
            'in-app'
        );

        $this->notificationService->sendToUser(
            $customerId,
            'deposit_rejected',
            'Deposit Rejected',
            'Your deposit of ' . $transaction->amount . ' was rejected. Reason: ' . $transaction->memo,
            'email'
        );
    }
}
