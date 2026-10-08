<?php

declare(strict_types=1);

namespace App\Enums;

use ArchTech\Enums\Values;

enum AccountStatus: string
{
    use Values;

    case ACTIVE   = 'active';
    case FROZEN   = 'frozen';
    case LOCKED   = 'locked';
    case CLOSED   = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::FROZEN => 'Frozen',
            self::LOCKED => 'Locked',
            self::CLOSED => 'Closed',
        };
    }

    public function isUsable(): bool
    {
        return $this === self::ACTIVE;
    }

    public function color(): string
    {
        return match ($this) {
            self::ACTIVE => 'var(--status-active)',
            self::FROZEN => 'var(--status-frozen)',
            self::LOCKED => 'var(--status-locked)',
            self::CLOSED => 'var(--status-closed)',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::ACTIVE => 'check-circle',
            self::FROZEN => 'snowflake',
            self::LOCKED => 'lock',
            self::CLOSED => 'archive-box',
        };
    }
}
