<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Card;
use App\Models\AuditLog;

class CardController extends Controller
{
    public function __invoke(Request $request): \Illuminate\View\View
    {
        $cards = Card::with(['user', 'account'])->paginate(20);

        return view('admin.cards', ['cards' => $cards]);
    }

    public function show(Card $card): \Illuminate\View\View
    {
        return view('admin.card-detail', [
            'card'          => $card->load('user', 'account'),
            'transactions'  => $card->transactions()->with('currency')->orderByDesc('created_at')->paginate(20),
        ]);
    }

    public function freeze(Request $request, Card $card): RedirectResponse
    {
        $card->update(['status' => 'frozen']);
        AuditLog::create([
            'admin_id' => Auth::id(),
            'action' => 'card_freeze',
            'target_type' => 'card',
            'target_id' => $card->id,
        ]);
        return back()->with('status', 'Card frozen.');
    }

    public function unfreeze(Request $request, Card $card): RedirectResponse
    {
        $card->update(['status' => 'active']);
        AuditLog::create([
            'admin_id' => Auth::id(),
            'action' => 'card_unfreeze',
            'target_type' => 'card',
            'target_id' => $card->id,
        ]);
        return back()->with('status', 'Card unfrozen.');
    }

    public function cancel(Request $request, Card $card): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $card->update(['status' => 'cancelled']);
        AuditLog::create([
            'admin_id' => Auth::id(),
            'action' => 'card_cancel',
            'target_type' => 'card',
            'target_id' => $card->id,
            'new_value' => ['reason' => $validated['reason'], 'old_status' => $card->getOriginal('status')],
        ]);

        return back()->with('status', 'Card cancelled.');
    }

    public function block(Request $request, Card $card): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        $card->update(['status' => 'blocked']);
        AuditLog::create([
            'admin_id' => Auth::id(),
            'action' => 'card_block',
            'target_type' => 'card',
            'target_id' => $card->id,
            'new_value' => ['reason' => $validated['reason'], 'old_status' => $card->getOriginal('status')],
        ]);

        return back()->with('status', 'Card blocked.');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'in:freeze,unfreeze,cancel,block,delete'],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:cards,id'],
        ]);

        if ($validated['action'] === 'delete') {
            return $this->bulkDelete($request);
        }

        $cards = Card::whereIn('id', $validated['ids'])->get();
        $count = 0;

        foreach ($cards as $card) {
            if (!in_array($card->status, ['cancelled', 'expired', 'blocked', 'closed', 'deactivated'])) {
                $newStatus = match ($validated['action']) {
                    'freeze' => 'frozen',
                    'unfreeze' => 'active',
                    'cancel' => 'cancelled',
                    'block' => 'blocked',
                    default => $card->status,
                };

                if ($card->status !== $newStatus) {
                    $card->update(['status' => $newStatus]);
                    AuditLog::create([
                        'admin_id' => Auth::id(),
                        'action' => 'card_bulk_' . $validated['action'],
                        'target_type' => 'card',
                        'target_id' => $card->id,
                    ]);
                    $count++;
                }
            }
        }

        $verb = match($validated['action']) {
            'freeze' => 'frozen',
            'unfreeze' => 'unfrozen',
            'cancel' => 'cancelled',
            'block' => 'blocked',
            default => $validated['action'],
        };

        return back()->with('status', "{$count} cards {$verb}.");
    }

    public function bulkDelete(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:cards,id'],
        ]);

        $count = Card::whereIn('id', $validated['ids'])->delete();

        return back()->with('status', "{$count} cards deleted.");
    }
}
