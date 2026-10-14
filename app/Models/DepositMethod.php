<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DepositMethod extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'icon',
        'icon_image_path',
        'description',
        'template',
        'is_active',
        'sort_order',
        'address',
        'instructions',
        'qr_path',
        'is_global',
    ];

    protected function casts(): array
    {
        return [
            'template' => 'array',
            'is_active' => 'boolean',
            'is_global' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function getSlugAttribute(): string
    {
        return $this->attributes['code'];
    }

    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'account_deposit_methods')
            ->using(AccountDepositMethod::class)
            ->withPivot(['address', 'qr_path', 'instructions', 'metadata', 'is_active'])
            ->withTimestamps();
    }
}
