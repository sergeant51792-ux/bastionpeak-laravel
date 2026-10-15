<?php

declare(strict_types=1);

namespace App\Support;

use App\Enums\TransactionStatus;
use App\Models\Transaction;

/**
 * Guards transaction creation against duplicates.
 *
 * Two independent protections live here:
 *
 * 1. Idempotency keys. The form generates a stable key per submission and sends
 *    it back with the POST. If the same key arrives twice (double click, network
 *    retry, browser replay) the existing transaction is returned instead of a
 *    second one being written.
 * 2. A duplicate guard for page reloads, which produce a *fresh* form key and so
 *    defeat protection 1. An identical still-pending request on the same account
 *    within a short window is treated as the same request.
 *
 * `transaction_id` and `reference` are generated collision-free because both
 * carry a unique index in the database.
 */
class TransactionIdentifiers
{
    /** Window (minutes) in which an identical pending request is a duplicate. */
    public const DUPLICATE_WINDOW_MINUTES = 5;

    /**
     * A transaction_id that is guaranteed unused.
     */
    public static function uniqueTransactionId(): string
    {
        do {
            $id = 'TXN-' . strtoupper(bin2hex(random_bytes(8)));
        } while (Transaction::where('transaction_id', $id)->exists());

        return $id;
    }

    /**
     * A reference that is guaranteed unused.
     */
    public static function uniqueReference(string $prefix): string
    {
        do {
            $reference = $prefix . '-' . strtoupper(bin2hex(random_bytes(6)));
        } while (Transaction::where('reference', $reference)->exists());

        return $reference;
    }

    /**
     * The transaction already created for this idempotency key, if any.
     */
    public static function findForIdempotencyKey(string $key): ?Transaction
    {
        return Transaction::where('idempotency_key', $key)->first();
    }

    /**
     * Detect a probable duplicate submission.
     *
     * Matches only requests that are identical in every field that matters and
     * are still awaiting review, so a *completed* request is never treated as a
     * duplicate of a new one.
     *
     * @param  array{user_id:int, account_id:int, type:string, amount:float|string} $attributes
     */
    public static function recentDuplicate(array $attributes): ?Transaction
    {
        return Transaction::where('from_user_id', $attributes['user_id'])
            ->where('from_account_id', $attributes['account_id'])
            ->where('to_account_id', $attributes['account_id'])
            ->where('type', $attributes['type'])
            ->where('amount', $attributes['amount'])
            ->where('status', TransactionStatus::PendingReview)
            ->where('created_at', '>=', now()->subMinutes(self::DUPLICATE_WINDOW_MINUTES))
            ->orderBy('id')
            ->first();
    }

    /**
     * Whether this submission should be rejected as a duplicate, and whether an
     * earlier identical transaction should be reused instead.
     */
    public static function duplicateDecision(string $idempotencyKey, array $guardAttributes): ?Transaction
    {
        // Exact replay of a key we have already seen wins, because it is certain.
        $existing = self::findForIdempotencyKey($idempotencyKey);
        if ($existing) {
            return $existing;
        }

        // Otherwise use the shape of the request to spot a re-submitted form.
        return self::recentDuplicate($guardAttributes);
    }
}
