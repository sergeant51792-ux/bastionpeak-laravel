<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Bastion Peak Internal Banking System — Notification Service
 *
 * Handles in-app and queued notification dispatch for the Bastion Peak platform.
 */
class NotificationService
{
    /**
     * Send a notification to a specific user.
     *
     * @param int $userId
     * @param string $type
     * @param string $title
     * @param string $body
     * @param string $channel 'in-app' | 'email' | 'sms'
     * @return Notification
     */
    public function sendToUser(int $userId, string $type, string $title, string $body, string|array $channel = 'in-app'): Notification
    {
        $channels = is_array($channel) ? $channel : [$channel];

        return Notification::create([
            'user_id'    => $userId,
            'type'       => $type,
            'title'      => $title,
            'body'       => $body,
            'channel'    => $channels[0],
        ]);
    }

    /**
     * Send a notification to all Super Admin users.
     *
     * @param string $type
     * @param string $title
     * @param string $body
     * @return void
     */
    public function sendToAdmin(string $type, string $title, string $body): void
    {
        $superAdmins = User::role('Super Admin')->get();

        foreach ($superAdmins as $admin) {
            $this->sendToUser($admin->id, $type, $title, $body, 'in-app');
        }
    }

    /**
     * Mark all notifications as read for a user.
     *
     * @param int $userId
     * @return int Number of notifications updated
     */
    public function markAllRead(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now(), 'updated_at' => now()]);
    }

    /**
     * Get the unread notification count for a user.
     *
     * @param int $userId
     * @return int
     */
    public function getUnreadCount(int $userId): int
    {
        return (int)Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();
    }
}