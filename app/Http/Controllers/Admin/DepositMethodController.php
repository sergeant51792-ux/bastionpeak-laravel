<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DepositMethod;
use Illuminate\Support\Facades\Storage;

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
            'type' => ['required', 'in:wire,crypto,swift,sepa,ach,zelle,faster_payments,bacs,chaps,giftcard,other'],
            'icon' => ['nullable', 'string', 'max:255'],
            'icon_image' => ['nullable', 'image', 'max:2048'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_global' => ['boolean'],
            'address' => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
            'qr_image' => ['nullable', 'image', 'max:2048'],
            'template' => ['nullable', 'array'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_global'] = $request->boolean('is_global', true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        if ($request->hasFile('icon_image')) {
            $validated['icon_image_path'] = $request->file('icon_image')->store('deposit-method-icons', 'public');
        }

        if ($request->hasFile('qr_image')) {
            $validated['qr_path'] = $request->file('qr_image')->store('deposit-qr-codes', 'public');
        }

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
            'type' => ['required', 'in:wire,crypto,swift,sepa,ach,zelle,faster_payments,bacs,chaps,giftcard,other'],
            'icon' => ['nullable', 'string', 'max:255'],
            'icon_image' => ['nullable', 'image', 'max:2048'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_global' => ['boolean'],
            'address' => ['nullable', 'string'],
            'instructions' => ['nullable', 'string'],
            'qr_image' => ['nullable', 'image', 'max:2048'],
            'remove_qr' => ['nullable', 'boolean'],
            'template' => ['nullable', 'array'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_global'] = $request->boolean('is_global', true);
        $validated['sort_order'] = (int) ($validated['sort_order'] ?? 0);

        if ($request->hasFile('icon_image')) {
            if ($depositMethod->icon_image_path) {
                Storage::disk('public')->delete($depositMethod->icon_image_path);
            }
            $validated['icon_image_path'] = $request->file('icon_image')->store('deposit-method-icons', 'public');
        }

        if ($request->hasFile('qr_image')) {
            if ($depositMethod->qr_path) {
                Storage::disk('public')->delete($depositMethod->qr_path);
            }
            $validated['qr_path'] = $request->file('qr_image')->store('deposit-qr-codes', 'public');
        }

        if ($request->boolean('remove_qr')) {
            if ($depositMethod->qr_path) {
                Storage::disk('public')->delete($depositMethod->qr_path);
            }
            $validated['qr_path'] = null;
        }

        $depositMethod->update($validated);

        return redirect()->route('admin.deposit-methods.index')->with('status', 'Deposit method updated successfully.');
    }

    public function destroy(DepositMethod $depositMethod)
    {
        if ($depositMethod->icon_image_path) {
            Storage::disk('public')->delete($depositMethod->icon_image_path);
        }
        if ($depositMethod->qr_path) {
            Storage::disk('public')->delete($depositMethod->qr_path);
        }

        $depositMethod->delete();

        return back()->with('status', 'Deposit method deleted successfully.');
    }
}
