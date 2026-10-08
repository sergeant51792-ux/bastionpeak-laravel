<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\DepositMethod;
use Illuminate\Support\Facades\Storage;

class AccountDepositMethodController extends Controller
{
    public function index(Request $request, Account $account): \Illuminate\View\View
    {
        $account->load('currency', 'user');

        $depositMethods = DepositMethod::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('admin.account-deposit-methods', [
            'account' => $account,
            'depositMethods' => $depositMethods,
        ]);
    }

    public function store(Request $request, Account $account)
    {
        $validated = $request->validate([
            'deposit_method_id' => ['required', 'exists:deposit_methods,id'],
            'address' => ['nullable', 'string', 'max:500'],
            'instructions' => ['nullable', 'string', 'max:2000'],
            'qr' => ['nullable', 'file', 'mimetypes:image/png,image/jpeg', 'max:2048'],
            'metadata' => ['nullable', 'array'],
            'is_active' => ['boolean'],
        ]);

        $pivotData = [
            'address' => $validated['address'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
            'metadata' => $validated['metadata'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('qr')) {
            $existing = $account->depositMethods()->where('deposit_method_id', $validated['deposit_method_id'])->first();
            if ($existing && $existing->pivot->qr_path && Storage::disk('public')->exists($existing->pivot->qr_path)) {
                Storage::disk('public')->delete($existing->pivot->qr_path);
            }
            $pivotData['qr_path'] = $request->file('qr')->store('deposit-qr', 'public');
        }

        $account->depositMethods()->syncWithoutDetaching([
            $validated['deposit_method_id'] => $pivotData,
        ]);

        return back()->with('status', 'Deposit method added to account successfully.');
    }

    public function update(Request $request, Account $account, DepositMethod $depositMethod)
    {
        $validated = $request->validate([
            'address' => ['nullable', 'string', 'max:500'],
            'instructions' => ['nullable', 'string', 'max:2000'],
            'qr' => ['nullable', 'file', 'mimetypes:image/png,image/jpeg', 'max:2048'],
            'metadata' => ['nullable', 'array'],
            'is_active' => ['boolean'],
        ]);

        $pivotData = [
            'address' => $validated['address'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
            'metadata' => $validated['metadata'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('qr')) {
            $existing = $account->depositMethods()->where('deposit_method_id', $depositMethod->id)->first();
            if ($existing && $existing->pivot->qr_path && Storage::disk('public')->exists($existing->pivot->qr_path)) {
                Storage::disk('public')->delete($existing->pivot->qr_path);
            }
            $pivotData['qr_path'] = $request->file('qr')->store('deposit-qr', 'public');
        }

        if ($request->input('remove_qr') === '1') {
            $existing = $account->depositMethods()->where('deposit_method_id', $depositMethod->id)->first();
            if ($existing && $existing->pivot->qr_path && Storage::disk('public')->exists($existing->pivot->qr_path)) {
                Storage::disk('public')->delete($existing->pivot->qr_path);
            }
            $pivotData['qr_path'] = null;
        }

        $account->depositMethods()->updateExistingPivot($depositMethod->id, $pivotData);

        return back()->with('status', 'Deposit method details updated successfully.');
    }

    public function destroy(Request $request, Account $account, DepositMethod $depositMethod)
    {
        $account->depositMethods()->detach($depositMethod->id);

        return back()->with('status', 'Deposit method removed from account successfully.');
    }
}
