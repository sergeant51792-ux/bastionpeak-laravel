<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    public function __construct(protected NotificationService $notifications) {}

    public function __invoke(Request $request): \Illuminate\View\View
    {
        $user = Auth::user();

        $this->notifications->markAllRead($user->id);

        $notifications = \App\Models\Notification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('customer.notifications', [
            'notifications' => $notifications,
        ]);
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $this->notifications->markAllRead($user->id);

        return back()->with('status', 'All notifications marked as read.');
    }
}
