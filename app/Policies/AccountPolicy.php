<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Account;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Bastion Peak Internal Banking System — Account Policy
 *
 * Customers can view their own accounts. Admins have full access.
 */
class AccountPolicy
{
    /**
     * Determine whether the user can view the account.
     *
     * @param User|null $user
     * @param Account $account
     * @return bool
     */
    public function view(?User $user, Account $account): bool
    {
        if ($user === null) {
            return false;
        }

        if ($user->hasRole('Super Admin') || $user->hasRole('Auditor')) {
            return true;
        }

        if ($user->hasRole('Customer')) {
            return $account->customer->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can update the account.
     *
     * @param User|null $user
     * @param Account $account
     * @return bool
     */
    public function update(?User $user, Account $account): bool
    {
        if ($user === null) {
            return false;
        }

        return $user->hasRole('Super Admin');
    }

    /**
     * Determine whether the user can delete the account.
     *
     * @param User|null $user
     * @param Account $account
     * @return bool
     */
    public function delete(?User $user, Account $account): bool
    {
        if ($user === null) {
            return false;
        }

        return $user->hasRole('Super Admin');
    }

    /**
     * Determine whether the user can create accounts.
     *
     * @param User|null $user
     * @return bool
     */
    public function create(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        return $user->hasRole('Super Admin');
    }

    /**
     * Determine whether the user can lock or unlock the account.
     *
     * @param User|null $user
     * @param Account $account
     * @return bool
     */
    public function lock(?User $user, Account $account): bool
    {
        if ($user === null) {
            return false;
        }

        return $user->hasRole('Super Admin');
    }
}
