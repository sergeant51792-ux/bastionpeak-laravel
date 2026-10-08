<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Policies\AccountPolicy;
use App\Policies\TransactionPolicy;
use App\Policies\MessagePolicy;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Message;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Account::class   => AccountPolicy::class,
        Transaction::class => TransactionPolicy::class,
        Message::class   => MessagePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
