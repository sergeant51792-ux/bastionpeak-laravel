<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FinancialService;
use App\Models\FinancialServiceApplication;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;

class FinancialServiceController extends Controller
{
    public function __invoke(Request $request): \Illuminate\View\View
    {
        $user = Auth::user();
        $services = FinancialService::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $applications = FinancialServiceApplication::where('user_id', $user->id)
            ->with('service')
            ->orderByDesc('created_at')
            ->get();

        return view('customer.financial-services', [
            'user' => $user,
            'services' => $services,
            'applications' => $applications,
        ]);
    }

    public function apply(Request $request, FinancialService $service): \Illuminate\View\View
    {
        $user = Auth::user();
        $accounts = $user->accounts()->where('status', 'active')->with('currency')->get();

        return view('customer.financial-service-apply', [
            'user' => $user,
            'service' => $service,
            'accounts' => $accounts,
        ]);
    }

    public function submitApplication(Request $request, FinancialService $service): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'account_id' => ['nullable', 'exists:accounts,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'purpose' => ['nullable', 'string', 'max:1000'],
        ]);

        FinancialServiceApplication::create([
            'user_id' => $user->id,
            'financial_service_id' => $service->id,
            'account_id' => $validated['account_id'],
            'amount' => $validated['amount'],
            'purpose' => $validated['purpose'],
            'status' => 'submitted',
        ]);

        return back()->with('status', $service->name . ' application submitted successfully.');
    }
}
