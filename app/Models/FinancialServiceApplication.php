<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinancialServiceApplication extends Model
{
    protected $fillable = [
        'user_id',
        'financial_service_id',
        'account_id',
        'amount',
        'purpose',
        'metadata',
        'status',
        'approved_amount',
        'interest_rate',
        'term_months',
        'reviewed_at',
        'reviewer_id',
        'review_notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:4',
            'approved_amount' => 'decimal:4',
            'interest_rate' => 'decimal:2',
            'term_months' => 'integer',
            'metadata' => 'array',
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(FinancialService::class, 'financial_service_id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
