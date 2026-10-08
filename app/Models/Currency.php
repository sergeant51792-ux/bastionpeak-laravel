<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'symbol',
        'name',
        'card_color',
        'decimals',
        'is_base',
        'exchange_rate',
        'rate_updated_at',
        'is_enabled',
    ];

    protected function casts(): array
    {
        return [
            'is_base' => 'boolean',
            'is_enabled' => 'boolean',
            'rate_updated_at' => 'datetime',
        ];
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }
}
