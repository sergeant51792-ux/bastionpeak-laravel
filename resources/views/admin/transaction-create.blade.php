@extends('layouts.admin')

@section('title', 'Create transaction')
@section('content')
<div class="space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.transactions.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--surface-raised)]" aria-label="Back">
            <x-icon name="arrow-left" class="w-5 h-5" />
        </a>
        <h1 class="text-xl font-semibold">Create transaction</h1>
    </div>

    <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
        <form method="POST" action="{{ route('admin.transactions.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @if($user)
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">User</label>
                        <p class="text-sm font-medium">{{ $user->name }} ({{ $user->email }})</p>
                    </div>
                @else
                    <div>
                        <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">User ID (optional)</label>
                        <input type="number" name="user_id" placeholder="User ID" class="input-field">
                    </div>
                @end
                <div>
                    <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Account</label>
                    <select name="account_id" required class="input-field">
                        <option value="">Select account</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->currency->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Type</label>
                    <select name="type" required class="input-field">
                        <option value="">Select type</option>
                        <option value="credit">Credit</option>
                        <option value="debit">Debit</option>
                        <option value="deposit_credit">Deposit credit</option>
                        <option value="payment_debit">Payment debit</option>
                        <option value="refund">Refund</option>
                        <option value="adjustment">Adjustment</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Amount</label>
                    <input type="number" step="0.01" name="amount" required placeholder="0.00" class="input-field">
                </div>
                <div>
                    <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Status</label>
                    <select name="status" class="input-field">
                        <option value="posted">Posted</option>
                        <option value="approved">Approved</option>
                        <option value="pending_review">Pending review</option>
                        <option value="credited">Credited</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Memo</label>
                    <input type="text" name="memo" placeholder="Reason or note" class="input-field">
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="btn-primary">Create transaction</button>
            </div>
        </form>
    </div>
</div>
@endsection
