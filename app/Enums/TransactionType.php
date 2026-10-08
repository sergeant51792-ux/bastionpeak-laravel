<?php

declare(strict_types=1);

namespace App\Enums;

enum TransactionType: string
{
    case Credit = 'credit';
    case Debit = 'debit';
    case DepositCredit = 'deposit_credit';
    case PaymentDebit = 'payment_debit';
    case Adjustment = 'adjustment';
    case Refund = 'refund';
    case Reversal = 'reversal';

    public function label(): string
    {
        return match ($this) {
            self::Credit => 'Credit',
            self::Debit => 'Debit',
            self::DepositCredit => 'Deposit credit',
            self::PaymentDebit => 'Payment debit',
            self::Adjustment => 'Adjustment',
            self::Refund => 'Refund',
            self::Reversal => 'Reversal',
        };
    }
}
