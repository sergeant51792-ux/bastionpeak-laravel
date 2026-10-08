<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Merchant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'receiving_cap',
        'receiving_cap_period',
        'status',
        'account_numbers',
        'instructions',
    ];

    protected function casts(): array
    {
        return [
            'receiving_cap' => 'decimal:18',
            'account_numbers' => 'array',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
