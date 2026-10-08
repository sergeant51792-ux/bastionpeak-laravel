<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LimitUsage extends Model
{
    protected $table = 'account_limits';

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
