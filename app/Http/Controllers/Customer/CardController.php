<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Card;
use App\Models\CardRequest;
use App\Services\NotificationService;

class CardController extends Controller
{
    public function __construct(protected NotificationService $notifications) {}

    public function __invoke(Request $request): \Illuminate\View\View
    {
        $user = Auth::user();

        return view('customer.cards', [
            'cards' => $user->cards()->with(['account', 'user'])->get(),
            'pendingRequest' => CardRequest::where('user_id', $user->id)
                ->where('status', 'pending')
                ->latest()
                ->first(),
            'accounts' => $user->accounts()->where('status', 'active')->with('currency')->get(),
        ]);
    }

    public function requestCard(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'account_id' => ['required', 'exists:accounts,id'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $account = \App\Models\Account::where('id', $validated['account_id'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        CardRequest::create([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'reason' => $validated['reason'],
        ]);

        $this->notifications->sendToAdmin(
            'card_request',
            'New card request',
            $user->name . ' requested a card for ' . $account->name
        );

        return back()->with('status', 'Card request submitted. Admin will review it shortly.');
    }

    public function reportProblem(Request $request, Card $card): RedirectResponse
    {
        $user = Auth::user();

        abort_if($card->user_id !== $user->id, 403);

        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'body'    => ['required', 'string', 'max:2000'],
        ]);

        $this->notifications->sendToAdmin(
            'card_problem',
            'Card problem reported',
            $validated['subject'] . ' — ' . $validated['body']
        );

        return back()->with('status', 'Problem reported. Admin will respond shortly.');
    }

    public function showPin(Card $card): \Illuminate\View\View
    {
        $user = Auth::user();
        abort_if($card->user_id !== $user->id, 403);

        $maskedPin = $card->pin_hash ? '••••' : null;

        return view('customer.card-pin', [
            'card' => $card->load('account.currency'),
            'hasPin' => (bool) $card->pin_hash,
        ]);
    }

    public function setPin(Request $request, Card $card): RedirectResponse
    {
        $user = Auth::user();
        abort_if($card->user_id !== $user->id, 403);

        $validated = $request->validate([
            'pin' => ['required', 'digits:4'],
            'pin_confirmation' => ['required', 'same:pin'],
        ]);

        $card->update([
            'pin_hash' => Hash::make($validated['pin']),
        ]);

        return back()->with('status', 'Card PIN set successfully.');
    }

    public function changePin(Request $request, Card $card): RedirectResponse
    {
        $user = Auth::user();
        abort_if($card->user_id !== $user->id, 403);

        $validated = $request->validate([
            'current_pin' => ['required', 'digits:4'],
            'pin' => ['required', 'digits:4', 'different:current_pin'],
            'pin_confirmation' => ['required', 'same:pin'],
        ]);

        if (!Hash::check($validated['current_pin'], $card->pin_hash)) {
            return back()->withErrors(['current_pin' => 'Current PIN is incorrect.']);
        }

        $card->update([
            'pin_hash' => Hash::make($validated['pin']),
        ]);

        return back()->with('status', 'Card PIN changed successfully.');
    }
}
