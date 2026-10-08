<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'thread_id',
        'sender_id',
        'recipient_id',
        'subject',
        'body',
        'attachment_paths',
        'is_broadcast',
        'is_flagged',
        'is_archived',
        'read_at',
        'transaction_id',
    ];

    protected function casts(): array
    {
        return [
            'attachment_paths' => 'array',
            'is_broadcast'     => 'boolean',
            'is_flagged'     => 'boolean',
            'is_archived'    => 'boolean',
            'read_at'        => 'datetime',
        ];
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeThread($query, ?string $threadId)
    {
        return $query->where('thread_id', $threadId)->orderBy('created_at');
    }

    public function scopeConversation($query, int $userA, int $userB)
    {
        return $query->where(function ($q) use ($userA, $userB) {
            $q->where('sender_id', $userA)->where('recipient_id', $userB)
              ->orWhere('sender_id', $userB)->where('recipient_id', $userA);
        })->orderBy('created_at');
    }

    public function getThreadKey(): string
    {
        $pair = [$this->sender_id, $this->recipient_id];
        sort($pair);
        return implode('_', $pair);
    }
}
