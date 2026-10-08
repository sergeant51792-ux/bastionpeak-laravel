<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Message;
use App\Models\User;
use App\Services\NotificationService;

class MessageController extends Controller
{
    public function __construct(protected NotificationService $notifications) {}

    public function __invoke(Request $request): \Illuminate\View\View
    {
        $filter = $request->query('filter', 'all');

        $threads = Message::select('thread_id', \Illuminate\Support\Facades\DB::raw('MAX(created_at) as last_at'))
            ->groupBy('thread_id')
            ->orderByDesc('last_at')
            ->get()
            ->pluck('thread_id');

        $messages = Message::whereIn('thread_id', $threads)
            ->orderByDesc('created_at')
            ->get()
            ->groupBy('thread_id');

        return view('admin.messages', [
            'messages' => $messages,
            'filter'   => $filter,
        ]);
    }

    public function showThread(string $threadId): \Illuminate\View\View
    {
        $thread = Message::where('thread_id', $threadId)
            ->orderBy('created_at')
            ->get();

        $thread->each(function ($msg) {
            if ($msg->recipient_id === Auth::id() && is_null($msg->read_at)) {
                $msg->update(['read_at' => now()]);
            }
        });

        $participantIds = $thread->pluck('sender_id')->merge($thread->pluck('recipient_id'))->unique()->filter(fn ($id) => $id !== Auth::id());
        $participant = User::find($participantIds->first());

        return view('admin.message-thread', [
            'thread'      => $thread,
            'threadId'    => $threadId,
            'participant' => $participant,
        ]);
    }

    public function reply(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'thread_id'    => ['required', 'string'],
            'recipient_id' => ['required', 'integer', 'exists:users,id'],
            'body'         => ['required', 'string', 'max:2000'],
        ]);

        $message = Message::create([
            'thread_id'    => $validated['thread_id'],
            'sender_id'    => Auth::id(),
            'recipient_id' => $validated['recipient_id'],
            'subject'      => null,
            'body'         => $validated['body'],
            'is_broadcast' => false,
            'read_at'      => null,
        ]);

        $this->notifications->sendToUser(
            $validated['recipient_id'],
            'support_reply',
            'New message',
            'You have a new message from support',
            ['in_app', 'email']
        );

        return redirect()->route('admin.messages.thread', $validated['thread_id'])->with('status', 'Reply sent.');
    }

    public function broadcast(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body'    => ['required', 'string', 'max:5000'],
            'group'   => ['required', 'in:all,custom'],
        ]);

        $recipients = User::whereHas('roles', fn ($q) => $q->where('name', 'Customer'))
            ->get();

        foreach ($recipients as $user) {
            Message::create([
                'thread_id'    => 'broadcast-' . bin2hex(random_bytes(16)),
                'sender_id'    => Auth::id(),
                'recipient_id'   => $user->id,
                'subject'        => $validated['subject'],
                'body'           => $validated['body'],
                'is_broadcast'   => true,
                'read_at'        => null,
            ]);

            $this->notifications->sendToUser(
                $user->id,
                'announcement',
                $validated['subject'],
                Str::limit(strip_tags($validated['body']), 100),
                ['in_app', 'email']
            );
        }

        return back()->with('status', "Broadcast sent to {$recipients->count()} users.");
    }

    public function destroyThread(Request $request, Message $message): RedirectResponse
    {
        $message->delete();

        return back()->with('status', 'Message deleted.');
    }
}
