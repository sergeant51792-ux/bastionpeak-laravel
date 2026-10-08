@extends('layouts.admin')

@section('title', 'New user')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.users.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--surface-raised)]">
            <x-icon name="arrow-left" class="w-5 h-5" />
        </a>
        <h1 class="text-xl font-semibold">New user</h1>
    </div>

    <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
        @csrf
        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6 space-y-5">
            <h2 class="font-semibold">User details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Full name</label>
                    <input type="text" name="name" required class="w-full px-4 py-2.5 bg-[var(--surface-raised)] border border-[var(--line)] rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Email</label>
                    <input type="email" name="email" required class="w-full px-4 py-2.5 bg-[var(--surface-raised)] border border-[var(--line)] rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Phone</label>
                    <input type="tel" name="phone" class="w-full px-4 py-2.5 bg-[var(--surface-raised)] border border-[var(--line)] rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Password</label>
                    <input type="password" name="password" required class="w-full px-4 py-2.5 bg-[var(--surface-raised)] border border-[var(--line)] rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Confirm password</label>
                    <input type="password" name="password_confirmation" required class="w-full px-4 py-2.5 bg-[var(--surface-raised)] border border-[var(--line)] rounded-xl text-sm">
                </div>
            </div>
        </div>

        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6 space-y-5">
            <h2 class="font-semibold">First account</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Type</label>
                    <x-custom-select
                        name="type"
                        :options="[
                            'main' => 'Main account',
                            'sub' => 'Sub-account',
                        ]"
                        placeholder="Select type"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Currency</label>
                    <x-custom-select
                        name="currency"
                        :options="[
                            'USD' => 'USD',
                            'BTC' => 'BTC',
                        ]"
                        placeholder="Select currency"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Opening balance</label>
                    <input type="text" inputmode="decimal" name="opening_balance" placeholder="0.00" class="w-full px-4 py-2.5 bg-[var(--surface-raised)] border border-[var(--line)] rounded-xl text-sm">
                    <p class="text-xs text-[var(--text-muted)] mt-1">Above zero creates an Admin Credit entry.</p>
                </div>
            </div>
        </div>

        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6 space-y-5">
            <h2 class="font-semibold">Limits</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Per-transaction cap</label>
                    <input type="text" inputmode="decimal" name="per_transaction_cap" placeholder="5,000.00" class="w-full px-4 py-2.5 bg-[var(--surface-raised)] border border-[var(--line)] rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Daily cap</label>
                    <input type="text" inputmode="decimal" name="daily_cap" placeholder="3,000.00" class="w-full px-4 py-2.5 bg-[var(--surface-raised)] border border-[var(--line)] rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Monthly cap</label>
                    <input type="text" inputmode="decimal" name="monthly_cap" placeholder="20,000.00" class="w-full px-4 py-2.5 bg-[var(--surface-raised)] border border-[var(--line)] rounded-xl text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Balance cap</label>
                    <input type="text" inputmode="decimal" name="balance_cap" placeholder="100,000.00" class="w-full px-4 py-2.5 bg-[var(--surface-raised)] border border-[var(--line)] rounded-xl text-sm">
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}" class="flex-1 py-3 border border-[var(--line)] rounded-xl text-sm font-medium text-center hover:bg-[var(--surface-raised)]">Cancel</a>
            <button type="submit" class="flex-1 py-3 bg-[var(--text)] text-[var(--bg)] rounded-xl text-sm font-medium hover:opacity-90 transition-opacity">Create user</button>
        </div>
    </form>
</div>
@endsection
