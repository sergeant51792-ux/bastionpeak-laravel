<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Merchant;
use App\Services\NotificationService;

class MerchantController extends Controller
{
    public function __construct(protected NotificationService $notifications) {}

    public function index(Request $request): \Illuminate\View\View
    {
        $query = Merchant::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $merchants = $query->orderBy('name')->paginate(20);

        return view('admin.merchants', [
            'merchants' => $merchants,
            'filters'   => $request->only('search', 'status'),
        ]);
    }

    public function show(Merchant $merchant): \Illuminate\View\View
    {
        return view('admin.merchant-detail', [
            'merchant'         => $merchant,
            'transactions'     => \App\Models\Transaction::where('merchant_id', $merchant->id)
                ->with('currency')
                ->orderByDesc('created_at')
                ->paginate(20),
            'pendingPayments'  => \App\Models\Transaction::where('merchant_id', $merchant->id)
                ->whereIn('status', ['pending_review'])
                ->count(),
        ]);
    }

    public function deactivate(Request $request, Merchant $merchant): RedirectResponse
    {
        $pending = \App\Models\Transaction::where('merchant_id', $merchant->id)
            ->whereIn('status', ['pending_review'])
            ->count();

        if ($pending > 0) {
            return back()->with('warning', "{$pending} pending payments will be blocked. Review them first.");
        }

        $merchant->update(['status' => 'deactivated']);

        return back()->with('status', 'Merchant deactivated.');
    }
}
