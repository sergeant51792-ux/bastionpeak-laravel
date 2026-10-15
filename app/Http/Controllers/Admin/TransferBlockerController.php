<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Transfer Blocker — lets a Super Admin lock the transfer/payout feature of a
 * customer account with a custom message explaining why.
 */
class TransferBlockerController extends Controller
{
    public function __construct(protected NotificationService $notifications) {}

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $query = User::whereHas('roles', fn ($q) => $q->where('name', 'Customer'));

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%");
            });
        }

        $users = $query->with('accounts.currency')
            ->orderByDesc('transfer_blocked')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $blockedUsers = User::transferBlocked()
            ->whereHas('roles', fn ($q) => $q->where('name', 'Customer'))
            ->with('transferBlocker')
            ->orderByDesc('transfer_blocked_at')
            ->get();

        $selectedUser = null;
        if ($request->filled('user')) {
            $selectedUser = User::whereHas('roles', fn ($q) => $q->where('name', 'Customer'))
                ->find($request->query('user'));
        }

        $selectableUsers = User::whereHas('roles', fn ($q) => $q->where('name', 'Customer'))
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('account_number', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->limit(150)
            ->get();

        if ($selectedUser && ! $selectableUsers->contains('id', $selectedUser->id)) {
            $selectableUsers->prepend($selectedUser);
        }

        return view('admin.transfer-blocker', [
            'users'           => $users,
            'blockedUsers'    => $blockedUsers,
            'selectedUser'    => $selectedUser,
            'selectableUsers' => $selectableUsers,
            'search'          => $search,
        ]);
    }

    public function block(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $reason = trim($validated['reason']);

        $user->update([
            'transfer_blocked'      => true,
            'transfer_block_reason' => $reason,
            'transfer_blocked_at'   => now(),
            'transfer_blocked_by'   => auth()->id(),
        ]);

        AuditLog::create([
            'admin_id'    => auth()->id(),
            'action'      => 'transfer_block',
            'target_type' => 'user',
            'target_id'   => $user->id,
            'new_value'   => ['reason' => $reason],
        ]);

        $this->notifications->sendToUser(
            $user->id,
            'security_alert',
            'Transfers locked',
            $reason,
            ['in_app', 'email'],
        );

        return redirect()
            ->route('admin.transfer-blocker.index', ['user' => $user->id])
            ->with('status', "Transfers locked for {$user->name}.");
    }

    public function unblock(Request $request, User $user): RedirectResponse
    {
        $user->update([
            'transfer_blocked'      => false,
            'transfer_block_reason' => null,
            'transfer_blocked_at'   => null,
            'transfer_blocked_by'   => null,
        ]);

        AuditLog::create([
            'admin_id'    => auth()->id(),
            'action'      => 'transfer_unblock',
            'target_type' => 'user',
            'target_id'   => $user->id,
        ]);

        $this->notifications->sendToUser(
            $user->id,
            'security_alert',
            'Transfers restored',
            'The transfer feature on your account has been unlocked again.',
        );

        return back()->with('status', "Transfers unlocked for {$user->name}.");
    }
}
