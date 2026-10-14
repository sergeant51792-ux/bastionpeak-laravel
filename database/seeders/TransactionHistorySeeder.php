<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Account;
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
 * Usage: php artisan db:seed --class=TransactionHistorySeeder -- --reset
 */
class TransactionHistorySeeder extends Seeder
{
    public static bool $reset = false;

    public function run(): void
    {
        $reset = self::$reset;
        $users = User::with('accounts.currency')->get();
        $counter = 1;

        foreach ($users as $user) {
            foreach ($user->accounts as $account) {
                $counter = $this->seedAccountTransactions($user, $account, $counter, $reset);
            }
        }
    }

    private function seedAccountTransactions(
        User $user,
        Account $account,
        int $counter,
        bool $reset = false
    ): int {
        $currencyCode = $account->currency->code;
        $balance = (float) $account->balance;

        if ($balance <= 0 || $currencyCode === 'PTS') {
            return $counter;
        }

        if ($reset) {
            Transaction::where('from_account_id', $account->id)
                ->orWhere('to_account_id', $account->id)
                ->forceDelete();
        }

        $existingCount = Transaction::where(function ($q) use ($account) {
            $q->where('from_account_id', $account->id)
              ->orWhere('to_account_id', $account->id);
        })->count();

        if (!$reset && $existingCount >= 10) {
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

        $dates = [];
        for ($i = 0; $i < $transactionCount; $i++) {
            $dates[] = $startDate->copy()->addDays(rand(2, 425))->addHours(rand(8, 20))->addMinutes(rand(0, 59));
        }
        sort($dates);

        $creditCount = (int) ceil($transactionCount * ($isCrypto ? 0.4 : 0.6));
        $debitCount = $transactionCount - $creditCount;

        $totalNeeded = $balance * 1.3;
        $debitTarget = $balance * 0.3;

        $creditAmounts = $this->generateAmounts($creditCount, $totalNeeded, $decimals);
        $debitAmounts = $this->generateAmounts($debitCount, $debitTarget, $decimals);

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
            }
            if ($di < $debitCount && ($ci + $di <= $transactionCount - 1)) {
                $slots[] = [
                    'date' => $dates[$i],
                    'type' => $isCrypto ? TransactionType::PaymentDebit : TransactionType::Debit,
                    'amount' => $debitAmounts[$di++],
                    'memo' => $debitDescriptors[array_rand($debitDescriptors)],
                ];
            }
        }

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

        usort($slots, fn($a, $b) => $a['date'] <=> $b['date']);

        foreach ($slots as $slot) {
            $isIncoming = in_array($slot['type'], [TransactionType::Credit, TransactionType::DepositCredit, TransactionType::Refund], true);
            $isOutgoing = !$isIncoming;
            $ts = $slot['date']->copy()->setTimezone('UTC');

            Transaction::create([
                'transaction_id'    => 'TXN-' . strtoupper(dechex(1000 + $counter)) . '-' . $ts->format('Ymd'),
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
                'reference'         => 'HIST-' . strtoupper($ts->format('YmdHis')) . '-' . $counter,
                'memo'              => $slot['memo'],
                'idempotency_key'   => 'idemp-hist-' . uniqid(),
                'reviewed_at'       => $ts->copy()->addHour(),
                'credited_at'       => $ts->copy()->addHours(rand(1, 48)),
                'created_at'        => $ts,
                'updated_at'        => $ts,
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

    private function generateAmounts(int $count, float $target, int $decimals): array
    {
        $amounts = [];
        if ($count <= 0) {
            return $amounts;
        }

        $avgAmount = $target / $count;

        for ($i = 0; $i < $count; $i++) {
            $jitter = 0.3 + (mt_rand(0, 140) / 100);
            $amounts[] = round($avgAmount * $jitter, $decimals);
        }

        return $amounts;
    }
}
