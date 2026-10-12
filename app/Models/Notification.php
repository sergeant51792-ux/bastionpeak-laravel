<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'body',
        'data',
        'read_at',
        'channel',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
            'read_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Visual tone for the notification, mapped from its type. Kept
     * deliberately minimal so the alert feed stays calm and professional.
     */
    public function tone(): string
    {
        return match ($this->type) {
            'deposit_approved', 'withdrawal_approved' => 'success',
            'deposit_rejected', 'payment_rejected', 'withdrawal_rejected' => 'danger',
            'account_locked' => 'warning',
            default => 'info',
        };
    }

    /**
     * Leading icon name for the notification, mapped from its type.
     */
    public function icon(): string
    {
        return match ($this->type) {
            'deposit_approved' => 'arrow-down-left',
            'withdrawal_approved', 'payment_approved' => 'arrow-up-right',
            'deposit_rejected', 'payment_rejected', 'withdrawal_rejected' => 'alert-circle',
            'account_locked' => 'lock',
            default => 'bell',
        };
    }
}
