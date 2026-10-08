<?php

declare(strict_types=1);

namespace App\Enums;

enum TransactionStatus: string
{
    case PendingReview = 'pending_review';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Reversed = 'reversed';
    case PendingMatch = 'pending_match';
    case Credited = 'credited';
    case Posted = 'posted';

    public function label(): string
    {
        return match ($this) {
            self::PendingReview => 'In review',
            self::Approved => 'Approved',
            self::Rejected => 'Rejected',
            self::Reversed => 'Reversed',
            self::PendingMatch => 'Pending match',
            self::Credited => 'Credited',
            self::Posted => 'Posted',
        };
    }
}
