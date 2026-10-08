<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Bastion Peak Internal Banking System — Balance Check Service
 *
 * Monitors account health, detects anomalies, and enforces security thresholds.
 */
class BalanceCheckService
{
    /**
     * Detect accounts with balance mismatches or unusual transaction volumes.
     *
     * @return array Array of anomalies with account_id and description
     */
    public function detectAnomalies(): array
    {
        $anomalies = [];
        $accounts = Account::all();

        foreach ($accounts as $account) {
            $recentTxCount = Transaction::where('from_account_id', $account->id)
                ->orWhere('to_account_id', $account->id)
                ->where('created_at', '>=', now()->subDays(7))
                ->count();

            if ($recentTxCount > 50) {
                $anomalies[] = [
                    'account_id' => $account->id,
                    'type'       => 'high_volume',
                    'description' => 'Unusual transaction volume detected in the last 7 days.',
                ];
            }

            if ((float)$account->balance < 0) {
                $anomalies[] = [
                    'account_id' => $account->id,
                    'type'       => 'negative_balance',
                    'description' => 'Account balance is negative.',
                ];
            }
        }

        return $anomalies;
    }

    /**
     * Check if a transaction amount requires additional confirmation.
     *
     * @param float $amount
     * @param float $threshold
     * @return bool
     */
    public function checkLargeTransaction(float $amount, float $threshold): bool
    {
        return $amount > $threshold;
    }

    /**
     * Check if a user has exceeded failed login thresholds.
     *
     * @param int $userId
     * @param int $maxAttempts
     * @return bool True if account should be auto-locked
     */
    public function checkFailedLogins(int $userId, int $maxAttempts = 5): bool
    {
        $user = User::find($userId);
        if (!$user) {
            return false;
        }

        return (int)$user->failed_login_count >= $maxAttempts;
    }
}