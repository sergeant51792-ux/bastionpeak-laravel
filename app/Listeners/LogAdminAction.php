<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Bastion Peak Internal Banking System — Log Admin Action Listener
 *
 * Writes to audit_logs on any admin action event.
 */
class LogAdminAction implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     *
     * @param mixed $event
     * @return void
     */
    public function handle(mixed $event): void
    {
        $adminId   = $event->adminId ?? null;
        $userId    = $event->userId ?? null;
        $action    = $event->action ?? 'unknown_action';
        $description = $event->description ?? 'Admin action performed';
        $ipAddress = $event->ipAddress ?? '0.0.0.0';
        $userAgent = $event->userAgent ?? 'BastionPeak-Admin/1.0';

        AuditLog::create([
            'admin_id'     => $adminId,
            'user_id'      => $userId,
            'action'       => $action,
            'description'  => $description,
            'ip_address'   => $ipAddress,
            'user_agent'   => $userAgent,
        ]);
    }
}
