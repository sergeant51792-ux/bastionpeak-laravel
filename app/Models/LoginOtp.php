<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginOtp extends Model
{
    protected $fillable = [
        'user_id',
        'code',
        'expires_at',
        'used_at',
        'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at->isPast();
    }

    public function isUsed(): bool
    {
        return $this->used_at !== null;
    }

    public static function generate(User $user, int $ttlSeconds = 40, ?string $adminNote = null): self
    {
        $code = (string) random_int(1000, 9999);

        return self::create([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => now()->addSeconds($ttlSeconds),
            'admin_note' => $adminNote,
        ]);
    }

    public function isValid(string $code): bool
    {
        return ! $this->isExpired() && ! $this->isUsed() && $this->code === $code;
    }

    public function markAsUsed(): void
    {
        $this->update(['used_at' => now()]);
    }
}
