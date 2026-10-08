<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\TransactionStatus;
use App\Models\Account;
use App\Models\LimitUsage;
use App\Models\Merchant;
use App\Models\Transaction;
use App\Models\TransactionLeg;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

/**
 * Bastion Peak Internal Banking System — Payment Service
 *
 * Handles payment request submission, approval, rejection, and validation.
 */
class PaymentService
{
    /**
     * Submit a new payment request.
     *
     * @param int $userId
     * @param int $fromAccountId
     * @param int|null $toMerchantId
     * @param float $amount
     * @param string $purpose
     * @param string|null $proofFile
     * @param string|null $paymentMethod
     * @param string|null $recipientName
     * @param string|null $recipientDetail
     * @param array|null $metadata
     * @return Transaction
     * @throws ModelNotFoundException
     */
    public function submitRequest(
        int $userId,
        int $fromAccountId,
        ?int $toMerchantId,
        float $amount,
        string $purpose,
        ?string $proofFile,
        ?string $paymentMethod = null,
        ?string $recipientName = null,
        ?string $recipientDetail = null,
        ?array $metadata = null
    ): Transaction {
        $account = Account::where('id', $fromAccountId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $merchant = $toMerchantId ? Merchant::findOrFail($toMerchantId) : null;

        if ($toMerchantId) {
            $this->validatePayment($fromAccountId, $toMerchantId, $amount);
        }

        $idempotencyKey = 'pay-req-' . $userId . '-' . $fromAccountId . '-' . ($toMerchantId ?? 'external') . '-' . md5((string)time());

        $memo = $purpose;
        if ($recipientName || $recipientDetail || $paymentMethod) {
            $memo = trim(($paymentMethod ? '[' . $paymentMethod . '] ' : '') . ($recipientName ? $recipientName . ' - ' : '') . ($recipientDetail ?? ''));
        }

        return DB::transaction(function () use ($account, $merchant, $amount, $memo, $proofFile, $idempotencyKey, $toMerchantId, $metadata) {
            $transaction = Transaction::create([
                'transaction_id'    => 'TXN-' . strtoupper(uniqid()),
                'from_account_id'   => $account->id,
                'to_account_id'     => $account->id,
                'merchant_id'       => $toMerchantId,
                'type'              => 'payment_debit',
                'amount'            => $amount,
                'currency_id'       => $account->currency_id,
                'status'            => TransactionStatus::PendingReview,
                'fee'               => 0,
                'net_amount'        => $amount,
                'memo'              => $memo,
                'reference'         => 'PAY-' . strtoupper(uniqid()),
                'idempotency_key'   => $idempotencyKey,
                'proof_file_path'   => $proofFile,
                'metadata'          => $metadata,
            ]);

            return $transaction;
        });
    }

    /**
     * Approve a payment request, deduct from account, and notify customer.
     *
     * @param int $transactionId
     * @param int $operatorId
     * @return Transaction
     * @throws ModelNotFoundException
     */
    public function approve(int $transactionId, int $operatorId): Transaction
    {
        $transaction = Transaction::findOrFail($transactionId);
        if ($transaction->status !== TransactionStatus::PendingReview) {
            return $transaction;
        }

        return DB::transaction(function () use ($transaction, $operatorId) {
            $account = Account::where('id', $transaction->from_account_id)
                ->lockForUpdate()
                ->firstOrFail();

            $transaction->update([
                'status'      => TransactionStatus::Approved,
                'operator_id' => $operatorId,
                'reviewed_at' => now(),
                'updated_at'  => now(),
            ]);

            TransactionLeg::create([
                'transaction_id'  => $transaction->id,
                'account_id'      => $account->id,
                'side'            => 'debit',
                'amount'          => $transaction->amount,
                'currency_id'     => $transaction->currency_id,
            ]);

            $account->balance = (float)$account->balance - (float)$transaction->amount;
            $account->save();

            return $transaction;
        });
    }

    /**
     * Reject a payment request and notify the customer.
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
     * Validate payment against limits and merchant status.
     *
     * @param int $accountId
     * @param int $merchantId
     * @param float $amount
     * @return array Validation result ['valid' => bool, 'message' => string]
     * @throws ModelNotFoundException
     */
    public function validatePayment(int $accountId, int $merchantId, float $amount): array
    {
        $account = Account::findOrFail($accountId);
        $merchant = Merchant::findOrFail($merchantId);

        if ($merchant->status !== 'active') {
            return ['valid' => false, 'message' => 'Merchant is currently deactivated.'];
        }

        if ($account->status !== 'active') {
            return ['valid' => false, 'message' => 'Account is not active.'];
        }

        if ($amount <= 0) {
            return ['valid' => false, 'message' => 'Payment amount must be greater than zero.'];
        }

        if ((float)$account->balance < $amount) {
            return ['valid' => false, 'message' => 'Insufficient account balance.'];
        }

        $monthlyLimit = LimitUsage::where('account_id', $accountId)
            ->where('period', 'monthly')
            ->where('reset_at', '>=', now()->startOfMonth())
            ->first();

        if ($monthlyLimit && ((float)$monthlyLimit->used_amount + $amount) > (float)$monthlyLimit->amount) {
            return ['valid' => false, 'message' => 'Monthly payment limit would be exceeded.'];
        }

        $merchantUsage = DB::table('transactions')
            ->where('merchant_id', $merchantId)
            ->where('type', 'payment_debit')
            ->where('status', 'approved')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('amount');

        if (($merchantUsage + $amount) > (float)$merchant->receiving_cap) {
            return ['valid' => false, 'message' => 'Merchant monthly cap would be exceeded.'];
        }

        return ['valid' => true, 'message' => 'Payment is valid.'];
    }
}