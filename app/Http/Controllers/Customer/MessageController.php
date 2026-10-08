<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class MessageController extends Controller
{
    public function __construct(protected NotificationService $notifications) {}

    public function __invoke(Request $request): \Illuminate\View\View
    {
        $user = Auth::user();

        $threadId = $request->query('thread');

        if ($threadId) {
            $thread = \App\Models\Message::where('thread_id', $threadId)
                ->where(function ($q) use ($user) {
                    $q->where('sender_id', $user->id)->orWhere('recipient_id', $user->id);
                })
                ->orderBy('created_at')
                ->get();

            \App\Models\Message::where('thread_id', $threadId)
                ->where('recipient_id', $user->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return view('customer.messages', [
                'threadId'    => $threadId,
                'thread'      => $thread,
                'threads'     => $this->getThreads($user),
            ]);
        }

        return view('customer.messages', [
            'threadId'    => null,
            'thread'      => collect(),
            'threads'     => $this->getThreads($user),
        ]);
    }

    public function send(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'subject'      => ['nullable', 'string', 'max:255'],
            'body'         => ['required', 'string', 'max:2000'],
            'recipient_id' => ['nullable', 'integer', 'exists:users,id'],
            'thread_id'    => ['nullable', 'string'],
        ]);

        $threadId = $validated['thread_id'] ?? bin2hex(random_bytes(16));

        $recipientId = $validated['recipient_id'] ?? null;
        if (empty($recipientId)) {
            $admin = \App\Models\User::whereHas('roles', fn ($q) => $q->where('name', 'Super Admin'))->first();
            $recipientId = $admin?->id;
        }

        \App\Models\Message::create([
            'thread_id'      => $threadId,
            'sender_id'      => $user->id,
            'recipient_id'   => $recipientId,
            'subject'        => $validated['subject'] ?? null,
            'body'           => $validated['body'],
            'is_broadcast'   => false,
            'read_at'        => null,
        ]);

        if ($recipientId) {
            $this->notifications->sendToUser(
                $recipientId,
                'admin_message',
                'New message',
                'You have a new message from ' . $user->name,
                ['in_app', 'email']
            );
        }

        return back()->with('status', 'Message sent.');
    }

    public function markAsRead(Request $request, \App\Models\Message $message): RedirectResponse
    {
        $user = Auth::user();

        abort_if($message->recipient_id !== $user->id, 403);

        $message->update(['read_at' => now()]);

        return back()->with('status', 'Message marked as read.');
    }

    public function destroy(Request $request, \App\Models\Message $message): RedirectResponse
    {
        $user = Auth::user();

        abort_if($message->sender_id !== $user->id && $message->recipient_id !== $user->id, 403);

        $message->delete();

        return back()->with('status', 'Message deleted.');
    }

    public function destroyThread(Request $request, string $threadId): RedirectResponse
    {
        $user = Auth::user();

        \App\Models\Message::where('thread_id', $threadId)
            ->where(function ($q) use ($user) {
                $q->where('sender_id', $user->id)->orWhere('recipient_id', $user->id);
            })
            ->delete();

        return back()->with('status', 'Conversation deleted.');
    }

    private function getThreads($user): \Illuminate\Support\Collection
    {
        $sent = \App\Models\Message::where('sender_id', $user->id)
            ->select('thread_id', \Illuminate\Support\Facades\DB::raw('MAX(created_at) as last_at'))
            ->groupBy('thread_id');

        $received = \App\Models\Message::where('recipient_id', $user->id)
            ->select('thread_id', \Illuminate\Support\Facades\DB::raw('MAX(created_at) as last_at'))
            ->groupBy('thread_id');

        $allThreads = $sent->union($received)->orderByDesc('last_at')->get()->pluck('thread_id');

        return \App\Models\Message::whereIn('thread_id', $allThreads)
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('thread_id')
            ->map(fn ($msgs) => $msgs->first());
    }
}
