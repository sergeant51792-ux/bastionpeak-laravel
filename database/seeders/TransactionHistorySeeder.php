<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Account;
use App\Models\Currency;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * Seeds realistic transaction history spanning over 1 year.
 *
 * For each user account the net sum of all seeded transactions
 * (credits minus debits) equals the account's current balance.
 *
 * Usage: php artisan db:seed --class=TransactionHistorySeeder
 */
class TransactionHistorySeeder extends Seeder
{
    public function run(): void
    {
        $users = User::with('accounts.currency')->get();
        $counter = 1;

        foreach ($users as $user) {
            foreach ($user->accounts as $account) {
                $counter = $this->seedAccountTransactions($user, $account, $counter);
            }
        }
    }

    private function seedAccountTransactions(
        User $user,
        Account $account,
        int $counter
    ): int {
        $currencyCode = $account->currency->code;
        $balance = (float) $account->balance;

        if ($balance <= 0 || $currencyCode === 'PTS') {
            return $counter;
        }

        // Skip if account already has transaction history
        $existingCount = Transaction::where(function ($q) use ($account) {
            $q->where('from_account_id', $account->id)
              ->orWhere('to_account_id', $account->id);
        })->count();
        if ($existingCount >= 10) {
            $this->command->info(sprintf(
                '  %s — %s %s: skipping (%d existing transactions)',
                $user->name,
                $account->name,
                $currencyCode,
                $existingCount
            ));
            return $counter;
        }

        $isCrypto = in_array($currencyCode, ['BTC', 'ETH']);
        $decimals = $account->currency->decimals ?? ($isCrypto ? 8 : 2);

        $startDate = Carbon::now()->subDays(425);
        $endDate = Carbon::now();
        $transactionCount = $isCrypto ? 15 : rand(25, 40);

        $creditDescriptors = $isCrypto
            ? ['BTC purchase', 'Crypto deposit', 'Crypto earnings', 'Staking reward', 'Crypto sale']
            : ['Salary deposit', 'Freelance payment', 'Vendor refund', 'Cash deposit', 'Investment return', 'Side income', 'Bonus credit', 'Dividend received'];

        $debitDescriptors = $isCrypto
            ? ['BTC withdrawal', 'Crypto swap', 'Trading fee', 'Network fee', 'Crypto purchase']
            : ['Utility payment', 'Grocery shopping', 'Coffee', 'Streaming subscription', 'Fuel purchase', 'Dining out', 'Online shopping', 'Phone bill', 'Insurance premium', 'Transfer fee'];

        // Generate dates spread over the full year
        $dates = [];
        for ($i = 0; $i < $transactionCount; $i++) {
            $dates[] = $startDate->copy()->addDays(rand(2, 425))->addHours(rand(8, 20))->addMinutes(rand(0, 59));
        }
        sort($dates);

        // Split into credits and debits
        $creditCount = (int) ceil($transactionCount * ($isCrypto ? 0.4 : 0.6));
        $debitCount = $transactionCount - $creditCount;

        // For credits: total should be more than balance (since debits subtract)
        // For debits: total should be less than credit total
        $creditTarget = $balance * 1.3; // credits exceed balance because debits will subtract
        $debitTarget = $balance * 0.3;  // debits are smaller

        $creditAmounts = $this->generateAmounts($creditCount, $creditTarget, $decimals, $isCrypto, true);
        $debitAmounts = $this->generateAmounts($debitCount, $debitTarget, $decimals, $isCrypto, false);

        $slots = [];
        $ci = 0;
        $di = 0;

        for ($i = 0; $i < $transactionCount; $i++) {
            if ($ci < $creditCount && ($di >= $debitCount || $i % 2 === 0)) {
                $slots[] = [
                    'date' => $dates[$i],
                    'type' => $isCrypto ? TransactionType::DepositCredit : TransactionType::Credit,
                    'amount' => $creditAmounts[$ci++],
                    'memo' => $creditDescriptors[array_rand($creditDescriptors)],
                ];
            } elseif ($di < $debitCount) {
                $slots[] = [
                    'date' => $dates[$i],
                    'type' => $isCrypto ? TransactionType::PaymentDebit : TransactionType::Debit,
                    'amount' => $debitAmounts[$di++],
                    'memo' => $debitDescriptors[array_rand($debitDescriptors)],
                ];
            }
        }

        // Calculate running balance and add adjustment if needed
        $runningBalance = 0.0;
        foreach ($slots as $slot) {
            $isIncoming = in_array($slot['type'], [TransactionType::Credit, TransactionType::DepositCredit, TransactionType::Refund], true);
            $runningBalance += $isIncoming ? $slot['amount'] : -$slot['amount'];
        }

        $diff = $balance - $runningBalance;
        if (abs($diff) > ($isCrypto ? 0.00001 : 0.01)) {
            $slots[] = [
                'date' => $endDate->copy(),
                'type' => $diff > 0 ? TransactionType::DepositCredit : TransactionType::PaymentDebit,
                'amount' => abs($diff),
                'memo' => $diff > 0 ? 'Account adjustment credit' : 'Account adjustment debit',
            ];
        }

        // Sort by date
        usort($slots, fn($a, $b) => $a['date'] <=> $b['date']);

        foreach ($slots as $slot) {
            $isIncoming = in_array($slot['type'], [TransactionType::Credit, TransactionType::DepositCredit, TransactionType::Refund], true);
            $isOutgoing = !$isIncoming;

            $txn = Transaction::create([
                'transaction_id'    => 'TXN-' . strtoupper(dechex(1000 + $counter)) . '-' . $slot['date']->format('Ymd'),
                'from_account_id'   => $isOutgoing ? $account->id : null,
                'to_account_id'     => $isIncoming ? $account->id : null,
                'from_user_id'      => $isOutgoing ? $user->id : null,
                'to_user_id'        => $isIncoming ? $user->id : null,
                'type'              => $slot['type'],
                'status'            => TransactionStatus::Approved,
                'amount'            => $slot['amount'],
                'net_amount'        => $slot['amount'],
                'currency_id'       => $account->currency_id,
                'fee'               => $isCrypto ? round($slot['amount'] * 0.001, $decimals) : 0.0,
                'reference'         => 'HIST-' . strtoupper($slot['date']->format('YmdHis')) . '-' . $counter,
                'memo'              => $slot['memo'],
                'idempotency_key'   => 'idemp-hist-' . uniqid(),
                'reviewed_at'       => $slot['date']->copy()->addHour(),
                'credited_at'       => $slot['date']->copy()->addHours(rand(1, 48)),
                'created_at'        => $slot['date'],
                'updated_at'        => $slot['date'],
            ]);

            $counter++;
        }

        $this->command->info(sprintf(
            '  %s — %s %s: %d transactions (balance: $%s)',
            $user->name,
            $account->name,
            $currencyCode,
            count($slots),
            number_format($balance, $decimals)
        ));

        return $counter;
    }

    private function generateAmounts(int $count, float $target, int $decimals, bool $isCrypto, bool $isCredit): array
    {
        $amounts = [];
        if ($count <= 0) {
            return $amounts;
        }

        $avgAmount = $target / $count;

        for ($i = 0; $i < $count; $i++) {
            $jitter = 0.3 + (mt_rand(0, 140) / 100);
            $amount = round($avgAmount * $jitter, $decimals);
            if ($isCrypto) {
                $amount = max($amount, pow(10, -$decimals));
            }
            $amounts[] = $amount;
        }

        return $amounts;
    }
}
