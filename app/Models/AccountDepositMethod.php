<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AccountDepositMethod extends Pivot
{
    protected $fillable = [
        'account_id',
        'deposit_method_id',
        'address',
        'qr_path',
        'instructions',
        'metadata',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
