<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxRefund extends Model
{
    protected $fillable = [
        'user_id',
        'application_id',
        'tax_year',
        'filing_status',
        'claimed_amount',
        'expected_refund',
        'irs_reference',
        'document_path',
    ];

    protected function casts(): array
    {
        return [
            'claimed_amount' => 'decimal:4',
            'expected_refund' => 'decimal:4',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(FinancialServiceApplication::class);
    }
}
