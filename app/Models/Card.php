<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_id',
        'card_number_masked',
        'card_type',
        'status',
        'per_transaction_cap',
        'daily_cap',
        'monthly_cap',
        'valid_from',
        'valid_to',
        'pin_hash',
        'category_restrictions',
        'region_restrictions',
    ];

    protected function casts(): array
    {
        return [
            'per_transaction_cap' => 'decimal:18',
            'daily_cap' => 'decimal:18',
            'monthly_cap' => 'decimal:18',
            'valid_from' => 'datetime',
            'valid_to' => 'datetime',
            'category_restrictions' => 'array',
            'region_restrictions' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'card_id');
    }
}
