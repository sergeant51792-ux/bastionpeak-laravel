<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Bastion Peak Internal Banking System — Send Notification Job
 *
 * Queued notification dispatch for email and in-app channels.
 */
class SendNotification implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The user ID to notify.
     *
     * @var int
     */
    public int $userId;

    /**
     * The notification type.
     *
     * @var string
     */
    public string $type;

    /**
     * The notification title.
     *
     * @var string
     */
    public string $title;

    /**
     * The notification body.
     *
     * @var string
     */
    public string $body;

    /**
     * The delivery channel.
     *
     * @var string
     */
    public string $channel;

    /**
     * Create a new job instance.
     *
     * @param int $userId
     * @param string $type
     * @param string $title
     * @param string $body
     * @param string $channel
     * @return void
     */
    public function __construct(int $userId, string $type, string $title, string $body, string $channel = 'in-app')
    {
        $this->userId = $userId;
        $this->type = $type;
        $this->title = $title;
        $this->body = $body;
        $this->channel = $channel;
    }

    /**
     * Execute the job.
     *
     * @param NotificationService $notificationService
     * @return void
     */
    public function handle(NotificationService $notificationService): void
    {
        $notificationService->sendToUser($this->userId, $this->type, $this->title, $this->body, $this->channel);
    }
}
