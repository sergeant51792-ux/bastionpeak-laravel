<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\PaymentApproved;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Bastion Peak Internal Banking System — Send Payment Approved Notification Listener
 *
 * Sends an in-app and email notification when a payment is approved.
 */
class SendPaymentApprovedNotification implements ShouldQueue
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
     * @param PaymentApproved $event
     * @return void
     */
    public function handle(PaymentApproved $event): void
    {
        $transaction = $event->transaction;
        $customerId = $transaction->customer_id;

        $title = 'Payment approved';
        $body  = 'Your payment of ' . $transaction->formattedAmount() . ' has been approved and processed.';

        $this->notificationService->sendToUser($customerId, 'withdrawal_approved', $title, $body, 'in-app');
        $this->notificationService->sendToUser($customerId, 'withdrawal_approved', $title, $body, 'email');
    }
}
