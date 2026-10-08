<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\AccountLocked;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Bastion Peak Internal Banking System — Send Account Locked Notification Listener
 *
 * Sends an in-app and email notification when an account is locked.
 */
class SendAccountLockedNotification implements ShouldQueue
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
     * @param AccountLocked $event
     * @return void
     */
    public function handle(AccountLocked $event): void
    {
        $customerId = $event->customerId;

        $this->notificationService->sendToUser(
            $customerId,
            'account_locked',
            'Account Locked',
            'Your Bastion Peak account has been locked. Please contact support for assistance.',
            'in-app'
        );

        $this->notificationService->sendToUser(
            $customerId,
            'account_locked',
            'Account Locked',
            'Your Bastion Peak account has been locked. Please contact support for assistance.',
            'email'
        );
    }
}
