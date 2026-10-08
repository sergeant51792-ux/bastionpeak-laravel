@extends('layouts.admin')

@section('title', $account->name)
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.users.show', $account->user) }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--surface-raised)]" aria-label="Back">
                <x-icon name="arrow-left" class="w-5 h-5" />
                    </a>
                </div>
                @if($depositMethods && $depositMethods->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($depositMethods as $method)
                            <div class="border border-[var(--line)] rounded-xl p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="font-semibold text-sm text-[var(--text)]">{{ $method->name }}</p>
                                        <p class="text-xs text-[var(--text-muted)] mt-0.5">{{ ucfirst($method->type) }} • Code: {{ $method->code }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $method->pivot->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-gray-50 text-gray-700 border border-gray-200' }}">
                                        {{ $method->pivot->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                                @if($method->pivot->address)
                                    <p class="text-xs text-[var(--text-muted)] mt-2 break-all font-mono">{{ $method->pivot->address }}</p>
                                @endif
                                @if($method->pivot->metadata && isset($method->pivot->metadata['network']))
                                    <p class="text-xs text-[var(--text-muted)] mt-1">Network: {{ $method->pivot->metadata['network'] }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-[var(--text-muted)]">No deposit methods configured for this account.</p>
                @endif
            </div>

            <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-5">
                <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-4">Quick actions</h2>
                <div class="space-y-3">
                    <form method="POST" action="{{ route('admin.accounts.balance.update', $account) }}" class="flex gap-2">
                        @csrf
                        <input type="number" step="{{ pow(10, -$account->currency->decimals) }}" name="balance" value="{{ old('balance', $account->balance) }}" required class="flex-1 px-3 py-2 bg-white border border-[var(--line)] rounded-lg text-sm">
                        <button type="submit" class="px-4 py-2 bg-[var(--text)] text-[var(--bg)] rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">Update balance</button>
                    </form>

                    <form method="POST" action="{{ route('admin.accounts.status.update', $account) }}" class="flex gap-2">
                        @csrf
                        <select name="status" class="flex-1 px-3 py-2 bg-white border border-[var(--line)] rounded-lg text-sm">
                            <option value="active" {{ $account->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="frozen" {{ $account->status === 'frozen' ? 'selected' : '' }}>Frozen</option>
                            <option value="locked" {{ $account->status === 'locked' ? 'selected' : '' }}>Locked</option>
                            <option value="closed" {{ $account->status === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                        <button type="submit" class="px-4 py-2 bg-[var(--text)] text-[var(--bg)] rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">Update status</button>
                    </form>

                    <div class="border border-[var(--line)] rounded-lg p-3 space-y-2">
                        <label class="text-xs font-bold text-[var(--text-muted)] uppercase">Limits ({{ $account->currency->code }})</label>
                        <form method="POST" action="{{ route('admin.accounts.caps.update', $account) }}" class="grid grid-cols-2 gap-2">
                            @csrf
                            <div>
                                <label class="text-xs text-[var(--text-muted)]">Per-transaction</label>
                                <input type="number" step="{{ pow(10, -$account->currency->decimals) }}" name="per_transaction_cap" value="{{ old('per_transaction_cap', $account->per_transaction_cap) }}" class="w-full px-2 py-1 bg-white border border-[var(--line)] rounded text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-[var(--text-muted)]">Daily</label>
                                <input type="number" step="{{ pow(10, -$account->currency->decimals) }}" name="daily_cap" value="{{ old('daily_cap', $account->daily_cap) }}" class="w-full px-2 py-1 bg-white border border-[var(--line)] rounded text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-[var(--text-muted)]">Monthly</label>
                                <input type="number" step="{{ pow(10, -$account->currency->decimals) }}" name="monthly_cap" value="{{ old('monthly_cap', $account->monthly_cap) }}" class="w-full px-2 py-1 bg-white border border-[var(--line)] rounded text-sm">
                            </div>
                            <div>
                                <label class="text-xs text-[var(--text-muted)]">Balance cap</label>
                                <input type="number" step="{{ pow(10, -$account->currency->decimals) }}" name="balance_cap" value="{{ old('balance_cap', $account->balance_cap) }}" class="w-full px-2 py-1 bg-white border border-[var(--line)] rounded text-sm">
                            </div>
                            <div class="col-span-2">
                                <button type="submit" class="w-full px-4 py-2 bg-[var(--text)] text-[var(--bg)] rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">Update limits</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
