<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\PaymentRejected;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Bastion Peak Internal Banking System — Send Payment Rejected Notification Listener
 *
 * Sends an in-app and email notification when a payment is rejected.
 */
class SendPaymentRejectedNotification implements ShouldQueue
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
     * @param PaymentRejected $event
     * @return void
     */
    public function handle(PaymentRejected $event): void
    {
        $transaction = $event->transaction;
        $customerId = $transaction->customer_id;

        $this->notificationService->sendToUser(
            $customerId,
            'withdrawal_approved',
            'Payment Rejected',
            'Your payment of ' . $transaction->amount . ' was rejected. Reason: ' . $transaction->memo,
            'in-app'
        );

        $this->notificationService->sendToUser(
            $customerId,
            'withdrawal_approved',
            'Payment Rejected',
            'Your payment of ' . $transaction->amount . ' was rejected. Reason: ' . $transaction->memo,
            'email'
        );
    }
}
