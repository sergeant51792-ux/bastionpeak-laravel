<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\TransactionLeg;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

/**
 * Bastion Peak Internal Banking System — Deposit Service
 *
 * Handles deposit request submission, approval, rejection, and batch approval.
 */
class DepositService
{
    /**
     * Submit a new deposit request.
     *
     * Passing the caller's own idempotency key makes a repeat POST return the
     * original transaction instead of creating a second one.
     *
     * @param int $userId
     * @param int $accountId
     * @param float $amount
     * @param string|null $note
     * @param string|null $proofFile
     * @param string|null $depositMethod
     * @param array|null $metadata
     * @param string|null $idempotencyKey Stable key from the submitted form.
     * @return Transaction
     * @throws ModelNotFoundException
     */
    public function submitRequest(int $userId, int $accountId, float $amount, ?string $note, ?string $proofFile, ?string $depositMethod = null, ?array $metadata = null, ?string $idempotencyKey = null): Transaction
    {
        $account = Account::where('id', $accountId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $idempotencyKey = $idempotencyKey
            ?: 'dep-req-' . $userId . '-' . $accountId . '-' . bin2hex(random_bytes(16));

        // Idempotent replay: the same key always maps to the same transaction.
        $existing = \App\Support\TransactionIdentifiers::findForIdempotencyKey($idempotencyKey);
        if ($existing) {
            return $existing;
        }

        $memo = $note ?: 'Deposit';

        // A page reload yields a brand new form key, so also reject an identical
        // request that is still pending within the duplicate window.
        $duplicate = \App\Support\TransactionIdentifiers::recentDuplicate([
            'user_id'    => $userId,
            'account_id' => $account->id,
            'type'       => 'deposit_credit',
            'amount'     => $amount,
        ]);

        if ($duplicate) {
            \Illuminate\Support\Facades\Log::info('Duplicate deposit submission absorbed', [
                'user_id'         => $userId,
                'original_txn'    => $duplicate->transaction_id,
                'amount'          => $amount,
                'idempotency_key' => $idempotencyKey,
            ]);

            return $duplicate;
        }

        return DB::transaction(function () use ($account, $amount, $memo, $proofFile, $idempotencyKey, $depositMethod, $metadata, $userId) {
            $transaction = Transaction::create([
                'transaction_id'    => \App\Support\TransactionIdentifiers::uniqueTransactionId(),
                'from_account_id'   => $account->id,
                'to_account_id'     => $account->id,
                'from_user_id'      => $userId,
                'to_user_id'        => $userId,
                'type'              => 'deposit_credit',
                'amount'            => $amount,
                'currency_id'       => $account->currency_id,
                'status'            => TransactionStatus::PendingReview,
                'fee'               => 0,
                'net_amount'        => $amount,
                'memo'              => $memo,
                'reference'         => \App\Support\TransactionIdentifiers::uniqueReference('DEP'),
                'idempotency_key'   => $idempotencyKey,
                'proof_file_path'   => $proofFile,
                'metadata'          => $metadata,
            ]);

            return $transaction;
        });
    }

    /**
     * Approve a deposit request, credit the account, and notify the customer.
     *
     * @param int $transactionId
     * @param int $operatorId
     * @param array $matchingData
     * @return Transaction
     * @throws ModelNotFoundException
     */
    public function approve(int $transactionId, int $operatorId, array $matchingData = []): Transaction
    {
        $transaction = Transaction::findOrFail($transactionId);
        if (!in_array($transaction->status, [TransactionStatus::PendingReview, TransactionStatus::PendingMatch])) {
            return $transaction;
        }

        $targetAccountId = $transaction->to_account_id ?? $transaction->from_account_id;
        if (!$targetAccountId) {
            $transaction->update([
                'status'           => TransactionStatus::Rejected,
                'operator_id'      => $operatorId,
                'rejection_reason' => 'No destination account configured — cannot auto-approve.',
                'rejected_at'      => now(),
                'updated_at'       => now(),
            ]);
            event(new \App\Events\DepositRejected($transaction, 'No destination account configured — cannot auto-approve.'));
            return $transaction;
        }

        $result = DB::transaction(function () use ($transaction, $operatorId, $targetAccountId) {
            $account = Account::where('id', $targetAccountId)
                ->lockForUpdate()
                ->firstOrFail();

            $transaction->update([
                'status'      => TransactionStatus::Credited,
                'operator_id' => $operatorId,
                'reviewed_at' => now(),
                'credited_at' => now(),
                'updated_at'  => now(),
            ]);

            TransactionLeg::create([
                'transaction_id'  => $transaction->id,
                'account_id'      => $account->id,
                'side'            => 'credit',
                'amount'          => $transaction->amount,
                'currency_id'     => $transaction->currency_id,
            ]);

            $account->balance = (float)$account->balance + (float)$transaction->amount;
            $account->save();

            return $transaction;
        });

        try {
            event(new \App\Events\DepositApproved($result));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('DepositApproved notification failed', [
                'transaction_id' => $result->id,
                'error'          => $e->getMessage(),
            ]);
        }

        return $result;
    }

    /**
     * Reject a deposit request and notify the customer.
     *
     * @param int $transactionId
     * @param int $operatorId
     * @param string $reason
     * @return Transaction
     * @throws ModelNotFoundException
     */
    public function reject(int $transactionId, int $operatorId, string $reason): Transaction
    {
        $transaction = Transaction::findOrFail($transactionId);

        $transaction->update([
            'status'           => TransactionStatus::Rejected,
            'operator_id'      => $operatorId,
            'rejection_reason' => $reason,
            'rejected_at'      => now(),
            'updated_at'       => now(),
        ]);

        try {
            event(new \App\Events\DepositRejected($transaction, $reason));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('DepositRejected notification failed', [
                'transaction_id' => $transaction->id,
                'error'          => $e->getMessage(),
            ]);
        }

        return $transaction;
    }

    /**
     * Batch approve multiple deposit requests in a single DB transaction.
     *
     * @param array $transactionIds
     * @param int $operatorId
     * @return int Number of transactions approved
     */
    public function batchApprove(array $transactionIds, int $operatorId): int
    {
        $approved = 0;

        DB::transaction(function () use ($transactionIds, $operatorId, &$approved) {
            foreach ($transactionIds as $transactionId) {
                $transaction = Transaction::find((int)$transactionId);
                if ($transaction && in_array($transaction->status, [TransactionStatus::PendingReview, TransactionStatus::PendingMatch])) {
                    $this->approve((int)$transactionId, $operatorId);
                    $transaction->refresh();
                    if ($transaction->status === TransactionStatus::Credited) {
                        $approved++;
                    }
                }
            }
        });

        return $approved;
    }
}