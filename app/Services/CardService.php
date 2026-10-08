<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Card;
use App\Models\CardRequest;
use App\Models\Account;

class CardService
{
    public function __construct()
    {
    }

    public function generateCard(CardRequest $cardRequest, Account $account): Card
    {
        $cardNumber = $this->generateCardNumber();
        $cardType = $this->pickCardType($cardNumber);
        $validFrom = now();
        $validTo = now()->addYears(3);

        return Card::create([
            'user_id' => $cardRequest->user_id,
            'account_id' => $account->id,
            'card_number_masked' => $this->maskCardNumber($cardNumber),
            'card_type' => $cardType,
            'status' => 'active',
            'per_transaction_cap' => (float) ($account->per_transaction_cap ?? 500),
            'daily_cap' => (float) ($account->daily_cap ?? 2000),
            'monthly_cap' => (float) ($account->monthly_cap ?? 10000),
            'valid_from' => $validFrom,
            'valid_to' => $validTo,
        ]);
    }

    public function approveRequest(CardRequest $cardRequest, ?int $reviewerId = null): Card
    {
        $account = Account::where('id', $cardRequest->account_id)
            ->where('user_id', $cardRequest->user_id)
            ->lockForUpdate()
            ->firstOrFail();

        $card = $this->generateCard($cardRequest, $account);

        $cardRequest->update([
            'status' => 'approved',
            'reviewed_by' => $reviewerId ?? $cardRequest->reviewed_by,
            'reviewed_at' => now(),
        ]);

        return $card;
    }

    private function generateCardNumber(): string
    {
        $prefix = match (random_int(0, 3)) {
            0 => '4',
            1 => '5',
            2 => '34',
            3 => '37',
            default => '4',
        };

        $length = match ($prefix) {
            '34', '37' => 13,
            default => 15,
        };

        $body = '';
        for ($i = 0; $i < $length; $i++) {
            $body .= (string) random_int(0, 9);
        }

        return $prefix . $body;
    }

    private function maskCardNumber(string $cardNumber): string
    {
        $visible = substr($cardNumber, -4);
        return '•••• •••• •••• ' . $visible;
    }

    private function pickCardType(string $cardNumber): string
    {
        if (str_starts_with($cardNumber, '4')) {
            return 'VISA';
        }

        if (str_starts_with($cardNumber, '5')) {
            return 'Mastercard';
        }

        if (str_starts_with($cardNumber, '34') || str_starts_with($cardNumber, '37')) {
            return 'AMEX';
        }

        return 'VISA';
    }
}
