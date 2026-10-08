<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DepositMethod;

class DepositMethodController extends Controller
{
    public function index(Request $request)
    {
        $methods = DepositMethod::query()
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('code', 'like', "%{$request->search}%");
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.deposit-methods.index', [
            'methods' => $methods,
            'filters' => $request->only('search'),
        ]);
    }

    public function create()
    {
        return view('admin.deposit-methods.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:deposit_methods,code'],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:wire,crypto,swift,sepa,ach,zelle,faster_payments,bacs,chaps,other'],
            'icon' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'template' => ['nullable', 'array'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        DepositMethod::create($validated);

        return redirect()->route('admin.deposit-methods.index')->with('status', 'Deposit method created successfully.');
    }

    public function edit(DepositMethod $depositMethod)
    {
        return view('admin.deposit-methods.edit', [
            'method' => $depositMethod,
        ]);
    }

    public function update(Request $request, DepositMethod $depositMethod)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:deposit_methods,code,' . $depositMethod->id],
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:wire,crypto,swift,sepa,ach,zelle,faster_payments,bacs,chaps,other'],
            'icon' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'template' => ['nullable', 'array'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        $depositMethod->update($validated);

        return redirect()->route('admin.deposit-methods.index')->with('status', 'Deposit method updated successfully.');
    }

    public function destroy(DepositMethod $depositMethod)
    {
        $depositMethod->delete();

        return back()->with('status', 'Deposit method deleted successfully.');
    }
}
