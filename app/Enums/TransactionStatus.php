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
            self::PendingReview, self::PendingMatch => 'Pending',
            self::Approved, self::Credited, self::Posted => 'Successful',
            self::Rejected, self::Reversed => 'Failed',
        };
    }

    public function tone(): string
    {
        return match ($this) {
            self::Approved, self::Credited, self::Posted => 'success',
            self::Rejected => 'danger',
            self::Reversed => 'warning',
            self::PendingReview, self::PendingMatch => 'pending',
        };
    }

    public function isFinalFailure(): bool
    {
        return $this === self::Rejected;
    }
}
