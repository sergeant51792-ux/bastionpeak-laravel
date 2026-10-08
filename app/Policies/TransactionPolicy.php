<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Bastion Peak Internal Banking System — Transaction Policy
 *
 * Customers can view their own transactions. Admins have full access.
 */
class TransactionPolicy
{
    /**
     * Determine whether the user can view the transaction.
     *
     * @param User|null $user
     * @param Transaction $transaction
     * @return bool
     */
    public function view(?User $user, Transaction $transaction): bool
    {
        if ($user === null) {
            return false;
        }

        if ($user->hasRole('Super Admin') || $user->hasRole('Auditor')) {
            return true;
        }

        if ($user->hasRole('Customer')) {
            return $transaction->account->customer->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can approve or reject the transaction.
     *
     * @param User|null $user
     * @param Transaction $transaction
     * @return bool
     */
    public function update(?User $user, Transaction $transaction): bool
    {
        if ($user === null) {
            return false;
        }

        return $user->hasRole('Super Admin');
    }

    /**
     * Determine whether the user can create transactions.
     *
     * @param User|null $user
     * @return bool
     */
    public function create(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        return $user->hasRole('Super Admin') || $user->hasRole('Customer');
    }
}
