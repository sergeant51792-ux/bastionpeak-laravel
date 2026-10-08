<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AccountLimit extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_id',
        'period',
        'type',
        'amount',
        'used_amount',
        'reset_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:18',
            'used_amount' => 'decimal:18',
            'reset_at' => 'datetime',
        ];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
