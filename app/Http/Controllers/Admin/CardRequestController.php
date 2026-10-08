<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\CardRequest;
use App\Services\NotificationService;
use App\Services\CardService;

class CardRequestController extends Controller
{
    public function __construct(protected NotificationService $notifications, protected CardService $cards) {}

    public function index(): \Illuminate\View\View
    {
        $requests = CardRequest::with(['user', 'account'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.card-requests', [
            'requests' => $requests,
        ]);
    }

    public function approve(Request $request, CardRequest $cardRequest): \Illuminate\Http\RedirectResponse
    {
        $account = \App\Models\Account::where('id', $cardRequest->account_id)
            ->where('user_id', $cardRequest->user_id)
            ->firstOrFail();

        $card = $this->cards->approveRequest($cardRequest, Auth::id());

        $this->notifications->sendToUser(
            $cardRequest->user_id,
            'card_approved',
            'Card request approved',
            'Your card request has been approved. Your card is now ready to use.'
        );

        return back()->with('status', 'Card request approved.');
    }

    public function reject(Request $request, CardRequest $cardRequest): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:500'],
        ]);

        $cardRequest->update([
            'status' => 'rejected',
            'admin_note' => $validated['admin_note'] ?? null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $this->notifications->sendToUser(
            $cardRequest->user_id,
            'card_rejected',
            'Card request rejected',
            $validated['admin_note'] ?? 'Your card request has been rejected.'
        );

        return back()->with('status', 'Card request rejected.');
    }
}
