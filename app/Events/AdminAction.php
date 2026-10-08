<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminAction
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public ?int $adminId,
        public string $action,
        public string $targetType,
        public int $targetId,
        public ?array $oldValue,
        public ?array $newValue,
        public ?string $ipAddress,
        public ?string $userAgent,
    ) {}
}
