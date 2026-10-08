<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialService extends Model
{
    protected $fillable = [
        'type',
        'name',
        'description',
        'currency_code',
        'min_amount',
        'max_amount',
        'interest_rate',
        'term_months',
        'is_active',
        'criteria',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'criteria' => 'array',
            'min_amount' => 'decimal:4',
            'max_amount' => 'decimal:4',
            'interest_rate' => 'decimal:2',
            'term_months' => 'integer',
        ];
    }

    public function applications(): HasMany
    {
        return $this->hasMany(FinancialServiceApplication::class);
    }
}
