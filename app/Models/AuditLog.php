<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'action',
        'target_type',
        'target_id',
        'old_value',
        'new_value',
        'ip_address',
        'user_agent',
    ];

    protected static function booted()
    {
        static::creating(function (AuditLog $log) {
            if (request()) {
                $log->ip_address = $log->ip_address ?? request()->ip() ?? '127.0.0.1';
                $log->user_agent = $log->user_agent ?? substr(request()->userAgent() ?? 'N/A', 0, 255);
            } else {
                $log->ip_address = $log->ip_address ?? 'console';
                $log->user_agent = $log->user_agent ?? 'scheduled';
            }
        });
    }

    protected function casts(): array
    {
        return [
            'old_value' => 'array',
            'new_value' => 'array',
        ];
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
