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
        $customerId = $this->resolveCustomerId($transaction);

        if (!$customerId) {
            return;
        }

        $reason = $transaction->rejection_reason ?: 'Please contact support for details.';
        $title  = 'Payment declined';
        $body   = 'Your payment of ' . $transaction->formattedAmount() . ' was declined. Reason: ' . $reason;

        $this->notificationService->sendToUser($customerId, 'payment_rejected', $title, $body, 'in-app');
    }

    /**
     * Resolve the customer (user) that owns a payment transaction.
     */
    protected function resolveCustomerId(\App\Models\Transaction $transaction): ?int
    {
        $customerId = $transaction->from_user_id ?? $transaction->to_user_id;

        if (!$customerId) {
            $accountId = $transaction->from_account_id ?? $transaction->to_account_id;
            $customerId = $accountId ? \App\Models\Account::where('id', $accountId)->value('user_id') : null;
        }

        return $customerId ? (int) $customerId : null;
    }
}
