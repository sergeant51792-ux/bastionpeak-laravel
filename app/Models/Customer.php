<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends User
{
    protected $table = 'users';

    public function accounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }
}
