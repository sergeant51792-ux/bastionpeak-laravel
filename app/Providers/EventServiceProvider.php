<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \App\Events\DepositApproved::class => [
            \App\Listeners\SendDepositApprovedNotification::class,
        ],
        \App\Events\DepositRejected::class => [
            \App\Listeners\SendDepositRejectedNotification::class,
        ],
        \App\Events\PaymentApproved::class => [
            \App\Listeners\SendPaymentApprovedNotification::class,
        ],
        \App\Events\PaymentRejected::class => [
            \App\Listeners\SendPaymentRejectedNotification::class,
        ],
        \App\Events\AccountLocked::class => [
            \App\Listeners\SendAccountLockedNotification::class,
        ],
        \App\Events\AdminAction::class => [
            \App\Listeners\LogAdminAction::class,
        ],
        \App\Events\TransactionPosted::class => [
            \App\Listeners\CheckBalanceAfterTransaction::class,
        ],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return true;
    }
}
