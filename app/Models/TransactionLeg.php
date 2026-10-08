<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionLeg extends Model
{
    protected $fillable = [
        'transaction_id',
        'account_id',
        'side',
        'amount',
        'currency_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:18',
        ];
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
