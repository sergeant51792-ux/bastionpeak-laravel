<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Models\LimitUsage;
use App\Models\Transaction;
use App\Models\TransactionLeg;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

/**
 * Bastion Peak Internal Banking System — Ledger Service
 *
 * Core double-entry ledger engine for the Bastion Peak banking system.
 * All monetary operations use database transactions with row locking.
 */
class LedgerService
{
    /**
     * Create a transaction atomically with debit and credit legs.
     *
     * @param string $type
     * @param int|null $fromAccountId
     * @param int|null $toAccountId
     * @param float $amount
     * @param int $currencyId
     * @param int|null $operatorId
     * @param string|null $memo
     * @param string|null $reference
     * @param string $idempotencyKey
     * @return Transaction
     * @throws ModelNotFoundException
     */
    public function createTransaction(
        string $type,
        ?int $fromAccountId,
        ?int $toAccountId,
        float $amount,
        int $currencyId,
        ?int $operatorId,
        ?string $memo,
        ?string $reference,
        string $idempotencyKey
    ): Transaction {
        return DB::transaction(function () use (
            $type, $fromAccountId, $toAccountId, $amount, $currencyId, $operatorId, $memo, $reference, $idempotencyKey
        ) {
            $fromAccount = $fromAccountId ? Account::where('id', $fromAccountId)->lockForUpdate()->firstOrFail() : null;
            $toAccount = $toAccountId ? Account::where('id', $toAccountId)->lockForUpdate()->firstOrFail() : null;

            if (Transaction::where('idempotency_key', $idempotencyKey)->exists()) {
                return Transaction::where('idempotency_key', $idempotencyKey)->firstOrFail();
            }

            $transaction = Transaction::create([
                'transaction_id'    => 'TXN-' . strtoupper(uniqid()),
                'from_account_id'   => $fromAccountId,
                'to_account_id'     => $toAccountId,
                'type'              => $type,
                'amount'            => $amount,
                'currency_id'       => $currencyId,
                'status'            => 'pending_review',
                'fee'               => 0,
                'net_amount'        => $amount,
                'operator_id'       => $operatorId,
                'memo'              => $memo,
                'reference'         => $reference,
                'idempotency_key'   => $idempotencyKey,
            ]);

            if ($fromAccountId) {
                TransactionLeg::create([
                    'transaction_id'  => $transaction->id,
                    'account_id'      => $fromAccountId,
                    'side'            => 'debit',
                    'amount'          => $amount,
                    'currency_id'     => $currencyId,
                ]);
            }

            if ($toAccountId) {
                TransactionLeg::create([
                    'transaction_id'  => $transaction->id,
                    'account_id'      => $toAccountId,
                    'side'            => 'credit',
                    'amount'          => $amount,
                    'currency_id'     => $currencyId,
                ]);
            }

            return $transaction;
        });
    }

    /**
     * Recalculate and update account balance from transaction history.
     *
     * @param int $accountId
     * @return float
     * @throws ModelNotFoundException
     */
    public function recalculateBalance(int $accountId): float
    {
        $account = Account::findOrFail($accountId);

        $credits = TransactionLeg::where('account_id', $accountId)
            ->where('side', 'credit')
            ->join('transactions', 'transaction_legs.transaction_id', '=', 'transactions.id')
            ->whereIn('transactions.status', ['approved', 'credited'])
            ->sum('transaction_legs.amount');

        $debits = TransactionLeg::where('account_id', $accountId)
            ->where('side', 'debit')
            ->join('transactions', 'transaction_legs.transaction_id', '=', 'transactions.id')
            ->whereIn('transactions.status', ['approved', 'credited'])
            ->sum('transaction_legs.amount');

        $newBalance = $credits - $debits;

        DB::transaction(function () use ($account, $newBalance) {
            Account::where('id', $account->id)
                ->lockForUpdate()
                ->update(['balance' => $newBalance, 'updated_at' => now()]);
        });

        return $newBalance;
    }

    /**
     * Check balance integrity — compare calculated vs stored balance.
     *
     * @param int $accountId
     * @return float Mismatch amount (0.0 means clean)
     * @throws ModelNotFoundException
     */
    public function checkBalanceIntegrity(int $accountId): float
    {
        $account = Account::findOrFail($accountId);
        $calculated = $this->recalculateBalance($accountId);
        $stored = (float)$account->balance;

        return abs($calculated - $stored);
    }

    /**
     * Create reversal entries for a transaction, linking to the original.
     *
     * @param int $transactionId
     * @param int|null $operatorId
     * @param string $reason
     * @return Transaction
     * @throws ModelNotFoundException
     */
    public function createReversal(int $transactionId, ?int $operatorId, string $reason): Transaction
    {
        $original = Transaction::findOrFail($transactionId);

        return DB::transaction(function () use ($original, $operatorId, $reason) {
            $reversal = Transaction::create([
                'transaction_id'    => 'REV-' . strtoupper(uniqid()),
                'from_account_id'   => $original->to_account_id,
                'to_account_id'     => $original->from_account_id,
                'type'              => 'reversal',
                'amount'            => $original->amount,
                'currency_id'       => $original->currency_id,
                'status'            => 'posted',
                'fee'               => 0,
                'net_amount'        => $original->amount,
                'operator_id'       => $operatorId,
                'memo'              => 'Reversal: ' . $reason,
                'reference'         => 'REV-' . strtoupper(uniqid()),
                'idempotency_key'   => 'rev-' . $original->id . '-' . time(),
                'reversal_of_id'    => $original->id,
            ]);

            TransactionLeg::create([
                'transaction_id'  => $reversal->id,
                'account_id'      => $original->to_account_id,
                'side'            => 'debit',
                'amount'          => $original->amount,
                'currency_id'     => $original->currency_id,
            ]);

            TransactionLeg::create([
                'transaction_id'  => $reversal->id,
                'account_id'      => $original->from_account_id,
                'side'            => 'credit',
                'amount'          => $original->amount,
                'currency_id'     => $original->currency_id,
            ]);

            if ($original->to_account_id) {
                Account::where('id', $original->to_account_id)->decrement('balance', $original->amount);
            }
            if ($original->from_account_id) {
                Account::where('id', $original->from_account_id)->increment('balance', $original->amount);
            }

            $original->update(['status' => 'reversed', 'updated_at' => now()]);

            return $reversal;
        });
    }

    /**
     * Track limit consumption for an account.
     *
     * @param int $accountId
     * @param float $amount
     * @param string $period 'daily' | 'monthly'
     * @return LimitUsage
     */
    public function applyLimitUsage(int $accountId, float $amount, string $period): LimitUsage
    {
        $limit = LimitUsage::firstOrCreate(
            ['account_id' => $accountId, 'period' => $period, 'reset_at' => now()->startOf($period)],
            ['type' => $period, 'amount' => 0.0, 'used_amount' => 0.0]
        );

        DB::transaction(function () use ($limit, $amount) {
            $limit->lockForUpdate();
            $limit->used_amount = (float)$limit->used_amount + $amount;
            $limit->save();
        });

        return $limit;
    }

    /**
     * Reset limit usage counters (called by scheduled job).
     *
     * @return int Number of records reset
     */
    public function resetLimitUsage(): int
    {
        return DB::transaction(function () {
            return LimitUsage::where('period', 'daily')
                ->where('reset_at', '<', now()->startOfDay())
                ->update([
                    'used_amount' => 0.0,
                    'reset_at'    => now()->startOfDay(),
                    'updated_at'  => now(),
                ]);
        });
    }

    /**
     * Get the total system balance across all accounts.
     *
     * @return float
     */
    public function getSystemBalance(): float
    {
        $total = 0.0;
        $accounts = Account::with('currency')->get();
        foreach ($accounts as $account) {
            $rate = max((float) ($account->currency->exchange_rate ?? 1), 0.0000000001);
            $total += (float) $account->balance * $rate;
        }
        return $total;
    }
}