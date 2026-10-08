<?php

declare(strict_types=1);

namespace App\Enums;

enum AccountType: string
{
    case MAIN = 'main';
    case SUB = 'sub';
    case MAIN_WALLET = 'main_wallet';
    case CRYPTO = 'crypto';
    case TRAVEL = 'travel';
    case POINTS = 'points';

    public function label(): string
    {
        return match ($this) {
            self::MAIN => 'Main account',
            self::SUB => 'Sub-account',
            self::MAIN_WALLET => 'Main wallet',
            self::CRYPTO => 'Crypto',
            self::TRAVEL => 'Travel',
            self::POINTS => 'Points',
        };
    }
}
