<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'currency_id',
        'type',
        'name',
        'account_number',
        'balance',
        'balance_cap',
        'per_transaction_cap',
        'daily_cap',
        'monthly_cap',
        'status',
        'notes',
        'deposit_address',
        'deposit_qr_path',
        'deposit_instructions',
        'opened_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'balance' => 'decimal:18',
            'balance_cap' => 'decimal:18',
            'per_transaction_cap' => 'decimal:18',
            'daily_cap' => 'decimal:18',
            'monthly_cap' => 'decimal:18',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function limits(): HasMany
    {
        return $this->hasMany(LimitUsage::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'from_account_id');
    }

    public function receivedTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'to_account_id');
    }

    public function card(): HasMany
    {
        return $this->hasMany(Card::class);
    }

    public function depositMethods(): BelongsToMany
    {
        return $this->belongsToMany(DepositMethod::class, 'account_deposit_methods')
            ->using(AccountDepositMethod::class)
            ->withPivot(['address', 'qr_path', 'instructions', 'metadata', 'is_active'])
            ->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getFormattedBalanceAttribute(): string
    {
        $currency = $this->currency;
        $decimals = $currency ? $currency->decimals : 2;

        return number_format((float) $this->balance, $decimals, '.', ',') . ' ' . ($currency ? $currency->symbol : '');
    }
}
