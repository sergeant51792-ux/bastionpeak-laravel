<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'type',
        'status',
        'from_account_id',
        'to_account_id',
        'from_user_id',
        'to_user_id',
        'merchant_id',
        'card_id',
        'amount',
        'currency_id',
        'fee',
        'net_amount',
        'reference',
        'memo',
        'proof_file_path',
        'metadata',
        'operator_id',
        'operator_type',
        'idempotency_key',
        'reversal_of_id',
        'parent_transaction_id',
        'matched_transaction_id',
        'reviewed_at',
        'credited_at',
        'rejected_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'string',
            'fee' => 'string',
            'net_amount' => 'string',
            'metadata' => 'array',
            'reviewed_at' => 'datetime',
            'credited_at' => 'datetime',
            'rejected_at' => 'datetime',
            'type' => \App\Enums\TransactionType::class,
            'status' => \App\Enums\TransactionStatus::class,
        ];
    }

    public function fromAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'from_account_id');
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'to_account_id');
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function reversal(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'reversal_of_id');
    }

    public function parentTransaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'parent_transaction_id');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending_review');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function isReversible(): bool
    {
        return in_array($this->status, ['approved', 'credited']) && is_null($this->reversal_of_id);
    }
}
