<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Message;
use App\Models\MessageThread;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Bastion Peak Internal Banking System — Message Policy
 *
 * Customers can view their own message threads. Admins have full access.
 */
class MessagePolicy
{
    /**
     * Determine whether the user can view the message thread.
     *
     * @param User|null $user
     * @param MessageThread $thread
     * @return bool
     */
    public function view(?User $user, MessageThread $thread): bool
    {
        if ($user === null) {
            return false;
        }

        if ($user->hasRole('Super Admin') || $user->hasRole('Auditor')) {
            return true;
        }

        if ($user->hasRole('Customer')) {
            return $thread->customer_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can view the individual message.
     *
     * @param User|null $user
     * @param Message $message
     * @return bool
     */
    public function viewMessage(?User $user, Message $message): bool
    {
        if ($user === null) {
            return false;
        }

        if ($user->hasRole('Super Admin') || $user->hasRole('Auditor')) {
            return true;
        }

        if ($user->hasRole('Customer')) {
            return $message->thread->customer_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create a message thread.
     *
     * @param User|null $user
     * @return bool
     */
    public function create(?User $user): bool
    {
        if ($user === null) {
            return false;
        }

        return $user->hasRole('Customer') || $user->hasRole('Super Admin');
    }
}
