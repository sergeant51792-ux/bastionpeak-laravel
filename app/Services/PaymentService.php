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
    public function __construct(
        protected NotificationService $notifications,
    ) {}

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
        ?array $metadata = null,
    ): Transaction {
        $account = Account::where('id', $fromAccountId)
            ->where('user_id', $userId)
            ->firstOrFail();

        $merchant = $toMerchantId ? Merchant::findOrFail($toMerchantId) : null;

        if ($toMerchantId) {
            $this->validatePayment($fromAccountId, $toMerchantId, $amount);
        }

        $idempotencyKey = 'pay-req-' . $userId . '-' . $fromAccountId . '-' . ($toMerchantId ?? 'external') . '-' . md5(uniqid((string)mt_rand(), true));

        $memo = $purpose;
        if ($recipientName || $recipientDetail || $paymentMethod) {
            $memo = trim(($paymentMethod ? '[' . $paymentMethod . '] ' : '') . ($recipientName ? $recipientName . ' - ' : '') . ($recipientDetail ?? ''));
        }

        return DB::transaction(function () use ($account, $merchant, $amount, $memo, $proofFile, $idempotencyKey, $toMerchantId, $metadata, $userId) {
            $transaction = Transaction::create([
                'transaction_id'    => 'TXN-' . strtoupper(uniqid()),
                'from_account_id'   => $account->id,
                'to_account_id'     => $account->id,
                'from_user_id'      => $userId,
                'to_user_id'        => $userId,
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
                'metadata'          => $metadata ?? [],
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
        if (!in_array($transaction->status, [TransactionStatus::PendingReview, TransactionStatus::PendingMatch])) {
            return $transaction;
        }

        $result = DB::transaction(function () use ($transaction, $operatorId) {
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

        try {
            event(new \App\Events\PaymentApproved($result));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('PaymentApproved notification failed', [
                'transaction_id' => $result->id,
                'error'          => $e->getMessage(),
            ]);
        }

        return $result;
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
            'status'           => TransactionStatus::Rejected,
            'operator_id'      => $operatorId,
            'rejection_reason' => $reason,
            'rejected_at'      => now(),
            'updated_at'       => now(),
        ]);

        try {
            event(new \App\Events\PaymentRejected($transaction, $reason));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('PaymentRejected notification failed', [
                'transaction_id' => $transaction->id,
                'error'          => $e->getMessage(),
            ]);
        }

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