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
        $customerId = $this->resolveCustomerId($transaction);

        if (!$customerId) {
            return;
        }

        $title = 'Payment approved';
        $body  = 'Your payment of ' . $transaction->formattedAmount() . ' has been approved and processed.';

        $this->notificationService->sendToUser($customerId, 'payment_approved', $title, $body, 'in-app');
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
