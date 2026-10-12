<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Hash;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens;
    use HasFactory;
    use HasRoles;
    use Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole('Super Admin');
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'account_number',
        'status',
        'theme_preference',
        'hide_balance',
        'profile_picture',
        'tags',
        'pin_hash',
        'pin_attempts',
        'pin_locked_until',
        'totp_secret',
        'totp_enabled',
        'approved_at',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'locked_until' => 'datetime',
            'hide_balance' => 'boolean',
            'failed_login_count' => 'integer',
            'tags' => 'array',
            'totp_enabled' => 'boolean',
            'approved_at' => 'datetime',
            'pin_locked_until' => 'datetime',
        ];
    }

    public function hasTotp(): bool
    {
        return $this->totp_enabled && filled($this->totp_secret);
    }

    public function isPendingApproval(): bool
    {
        return $this->status === 'pending';
    }

    public function primaryAccount(): ?Account
    {
        return $this->accounts()->orderBy('id')->first();
    }

    public function getProfilePictureUrlAttribute(): string
    {
        if ($this->profile_picture) {
            return asset('storage/' . $this->profile_picture);
        }
        return '';
    }

    protected function hidden(): array
    {
        return [
            'password',
            'remember_token',
            'pin_hash',
        ];
    }

    public function accounts(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function cards(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Card::class);
    }

    public function notifications(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function messages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function sentMessages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    public function unreadMessagesCount(): int
    {
        return $this->receivedMessages()->whereNull('read_at')->count();
    }

    public function setPin(string $pin): void
    {
        $this->update(['pin_hash' => Hash::make($pin)]);
    }

    public function verifyPin(string $pin): bool
    {
        return Hash::check($pin, $this->pin_hash);
    }

    public function isPinLocked(): bool
    {
        return $this->pin_locked_until && $this->pin_locked_until->isFuture();
    }
}
