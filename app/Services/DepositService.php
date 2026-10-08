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
     * @param int $userId
     * @param int $accountId
     * @param float $amount
     * @param string|null $note
     * @param string|null $proofFile
     * @param string|null $depositMethod
     * @param array|null $metadata
     * @return Transaction
     * @throws ModelNotFoundException
     */
    public function submitRequest(int $userId, int $accountId, float $amount, ?string $note, ?string $proofFile, ?string $depositMethod = null, ?array $metadata = null): Transaction
    {
        $account = Account::where('id', $accountId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $idempotencyKey = 'dep-req-' . $userId . '-' . $accountId . '-' . md5((string)time());

        $memo = $note ?? 'Deposit request';
        if ($depositMethod) {
            $memo = '[' . ucfirst($depositMethod) . '] ' . $memo;
        }

        return DB::transaction(function () use ($account, $amount, $memo, $proofFile, $idempotencyKey, $depositMethod, $metadata) {
            $transaction = Transaction::create([
                'transaction_id'    => 'TXN-' . strtoupper(uniqid()),
                'from_account_id'   => $account->id,
                'to_account_id'     => $account->id,
                'type'              => 'deposit_credit',
                'amount'            => $amount,
                'currency_id'       => $account->currency_id,
                'status'            => TransactionStatus::PendingReview,
                'fee'               => 0,
                'net_amount'        => $amount,
                'memo'              => $memo,
                'reference'         => 'DEP-' . strtoupper(uniqid()),
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
        if ($transaction->status !== TransactionStatus::PendingReview) {
            return $transaction;
        }

        return DB::transaction(function () use ($transaction, $operatorId) {
            $account = Account::where('id', $transaction->to_account_id)
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
            'status'      => TransactionStatus::Rejected,
            'operator_id' => $operatorId,
            'memo'        => $transaction->memo . ' | Rejected: ' . $reason,
            'rejected_at' => now(),
            'updated_at'  => now(),
        ]);

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
                $this->approve((int)$transactionId, $operatorId);
                $approved++;
            }
        });

        return $approved;
    }
}