@extends('layouts.admin')

@section('title', $account->name)

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.users.show', $account->user) }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--line-soft)] transition-colors" aria-label="Back">
                <x-icon name="arrow-left" class="w-5 h-5 text-[var(--text-muted)]" />
            </a>
            <div>
                <h1 class="text-xl font-bold text-[var(--text)]">{{ $account->name }}</h1>
                <p class="text-sm text-[var(--text-muted)] mt-0.5">{{ $account->user->name ?? 'Unknown user' }} • {{ $account->currency->code ?? '' }} {{ $account->account_number ?: '' }}</p>
            </div>
        </div>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $account->status === 'active' ? 'bg-[var(--success-bg)] text-[var(--success)]' : ($account->status === 'frozen' ? 'bg-[var(--warning-bg)] text-[var(--warning)]' : 'bg-[var(--danger-bg)] text-[var(--danger)]') }}">
            {{ ucfirst($account->status) }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
                <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-4">Balance</h2>
                <p class="text-3xl font-bold text-[var(--text)]">{{ number_format((float) $account->balance, $account->currency->decimals ?? 2) }} {{ $account->currency->symbol ?? '' }}</p>
            </div>

            <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
                <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-4">Quick actions</h2>
                <div class="space-y-3">
                    <form method="POST" action="{{ route('admin.accounts.balance.update', $account) }}" class="flex gap-2">
                        @csrf
                        <input type="number" step="{{ pow(10, -($account->currency->decimals ?? 2)) }}" name="balance" value="{{ old('balance', $account->balance) }}" required class="flex-1 px-3 py-2 bg-[var(--surface)] border border-[var(--line)] rounded-lg text-sm">
                        <button type="submit" class="px-4 py-2 bg-[var(--text)] text-[var(--bg)] rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">Update balance</button>
                    </form>

                    <form method="POST" action="{{ route('admin.accounts.status.update', $account) }}" class="flex gap-2">
                        @csrf
                        <select name="status" class="flex-1 px-3 py-2 bg-[var(--surface)] border border-[var(--line)] rounded-lg text-sm">
                            <option value="active" {{ $account->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="frozen" {{ $account->status === 'frozen' ? 'selected' : '' }}>Frozen</option>
                            <option value="locked" {{ $account->status === 'locked' ? 'selected' : '' }}>Locked</option>
                            <option value="closed" {{ $account->status === 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                        <button type="submit" class="px-4 py-2 bg-[var(--text)] text-[var(--bg)] rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">Update status</button>
                    </form>
                </div>
            </div>

            <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
                <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-4">Limits ({{ $account->currency->code }})</h2>
                <form method="POST" action="{{ route('admin.accounts.caps.update', $account) }}" class="grid grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="text-xs text-[var(--text-muted)]">Per-transaction</label>
                        <input type="number" step="{{ pow(10, -($account->currency->decimals ?? 2)) }}" name="per_transaction_cap" value="{{ old('per_transaction_cap', $account->per_transaction_cap) }}" class="w-full px-3 py-2 bg-[var(--surface)] border border-[var(--line)] rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-[var(--text-muted)]">Daily</label>
                        <input type="number" step="{{ pow(10, -($account->currency->decimals ?? 2)) }}" name="daily_cap" value="{{ old('daily_cap', $account->daily_cap) }}" class="w-full px-3 py-2 bg-[var(--surface)] border border-[var(--line)] rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-[var(--text-muted)]">Monthly</label>
                        <input type="number" step="{{ pow(10, -($account->currency->decimals ?? 2)) }}" name="monthly_cap" value="{{ old('monthly_cap', $account->monthly_cap) }}" class="w-full px-3 py-2 bg-[var(--surface)] border border-[var(--line)] rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="text-xs text-[var(--text-muted)]">Balance cap</label>
                        <input type="number" step="{{ pow(10, -($account->currency->decimals ?? 2)) }}" name="balance_cap" value="{{ old('balance_cap', $account->balance_cap) }}" class="w-full px-3 py-2 bg-[var(--surface)] border border-[var(--line)] rounded-lg text-sm">
                    </div>
                    <div class="col-span-2">
                        <button type="submit" class="w-full px-4 py-2 bg-[var(--text)] text-[var(--bg)] rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">Update limits</button>
                    </div>
                </form>
            </div>

            <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
                <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-4">Deposit Details</h2>
                <form method="POST" action="{{ route('admin.accounts.deposit.update', $account) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="text-xs text-[var(--text-muted)] font-semibold uppercase mb-1.5">Deposit Address</label>
                        <input type="text" name="deposit_address" value="{{ old('deposit_address', $account->deposit_address) }}" class="w-full px-3 py-2 bg-[var(--surface)] border border-[var(--line)] rounded-lg text-sm" placeholder="e.g. 0x1234...abcd">
                    </div>
                    <div>
                        <label class="text-xs text-[var(--text-muted)] font-semibold uppercase mb-1.5">Instructions</label>
                        <textarea name="deposit_instructions" rows="3" class="w-full px-3 py-2 bg-[var(--surface)] border border-[var(--line)] rounded-lg text-sm" placeholder="Deposit instructions for users">{{ old('deposit_instructions', $account->deposit_instructions) }}</textarea>
                    </div>
                    <div>
                        <label class="text-xs text-[var(--text-muted)] font-semibold uppercase mb-1.5">QR Code Image</label>
                        @if($account->deposit_qr_path)
                            <div class="mb-3">
                                <img src="{{ Storage::url($account->deposit_qr_path) }}" alt="Current QR Code" class="w-24 h-24 object-contain border border-[var(--line)] rounded-lg">
                            </div>
                        @endif
                        <input type="file" name="deposit_qr" accept="image/*" class="text-sm text-[var(--text-muted)]">
                    </div>
                    <div class="pt-2">
                        <button type="submit" class="px-4 py-2 bg-[var(--text)] text-[var(--bg)] rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">Update deposit details</button>
                    </div>
                </form>
            </div>

            <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
                <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-4">Deposit Methods</h2>
                @php
                    $globalMethods = $depositMethods->filter(fn($m) => $m->is_global);
                    $accountMethods = $depositMethods->filter(fn($m) => !$m->is_global);
                @endphp
                @if($globalMethods->isNotEmpty())
                    <div class="space-y-4 mb-4">
                        <p class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider">System-wide deposit methods (visible to all accounts)</p>
                        @foreach($globalMethods as $method)
                            <div class="border border-[var(--line)] rounded-xl p-4">
                                <div class="flex items-center gap-3">
                                    @if($method->icon_image_path)
                                        <img src="{{ Storage::url($method->icon_image_path) }}" alt="{{ $method->name }}" class="w-10 h-10 object-contain rounded-lg">
                                    @else
                                        <div class="w-10 h-10 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] shrink-0">
                                            <x-icon name="{{ $method->icon ?: 'settings' }}" class="w-5 h-5" />
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-semibold text-[var(--text)]">{{ $method->name }}</p>
                                        <p class="text-xs text-[var(--text-muted)]">{{ ucfirst($method->type) }} • Code: {{ $method->code }}</p>
                                    </div>
                                </div>
                                @if($method->address)
                                    <div class="mt-2">
                                        <p class="text-xs text-[var(--text-muted)] font-semibold uppercase mb-1">Address</p>
                                        <p class="text-xs text-[var(--text-muted)] break-all font-mono bg-[var(--line-soft)] px-2 py-1 rounded">{{ $method->address }}</p>
                                    </div>
                                @endif
                                @if($method->qr_path)
                                    <div class="mt-2">
                                        <p class="text-xs text-[var(--text-muted)] font-semibold uppercase mb-1">QR Code</p>
                                        <img src="{{ Storage::url($method->qr_path) }}" alt="QR Code" class="w-24 h-24 object-contain border border-[var(--line)] rounded-lg">
                                    </div>
                                @endif
                                @if($method->instructions)
                                    <div class="mt-2">
                                        <p class="text-xs text-[var(--text-muted)] font-semibold uppercase mb-1">Instructions</p>
                                        <p class="text-xs text-[var(--text-muted)] whitespace-pre-line">{{ $method->instructions }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        <div class="pt-2">
                            <a href="{{ route('admin.deposit-methods.index') }}" class="text-xs font-medium text-[var(--accent)] hover:opacity-80 transition-opacity">Manage global deposit methods →</a>
                        </div>
                    </div>
                @endif

                @if($accountMethods->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($accountMethods as $method)
                            <div class="border border-[var(--line)] rounded-xl p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-3">
                                        @if($method->icon_image_path)
                                            <img src="{{ Storage::url($method->icon_image_path) }}" alt="{{ $method->name }}" class="w-10 h-10 object-contain rounded-lg">
                                        @else
                                            <div class="w-10 h-10 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)]">
                                                <x-icon name="{{ $method->icon ?: 'settings' }}" class="w-5 h-5" />
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-semibold text-[var(--text)]">{{ $method->name }}</p>
                                            <p class="text-xs text-[var(--text-muted)]">{{ ucfirst($method->type) }} • Code: {{ $method->code }}</p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $method->pivot->is_active ? 'bg-[var(--success-bg)] text-[var(--success)]' : 'bg-[var(--line-soft)] text-[var(--text-muted)]' }}">
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

            <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
                <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-4">Recent transactions</h2>
                <div class="border border-[var(--line)] rounded-xl overflow-hidden">
                    @forelse($ledger as $txn)
                        <div class="flex items-center gap-3 p-3 border-b border-[var(--line)] last:border-0">
                            <span class="txn-icon w-8 h-8">
                                <x-icon name="{{ $txn->type === 'deposit' || $txn->type === 'deposit_credit' ? 'arrow-down-circle' : 'arrow-up-circle' }}" class="w-4 h-4" />
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-[var(--text)] truncate">{{ $txn->memo ?: $txn->type }}</p>
                                <p class="text-xs text-[var(--text-muted)]">{{ $txn->created_at->format('d M Y, H:i') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium {{ ($txn->from_account_id === $account->id) ? 'text-[var(--danger)]' : 'text-[var(--success)]' }}">

                                    {{ $txn->from_account_id === $account->id ? '-' : '+' }}{{ number_format((float) $txn->amount, $account->currency->decimals ?? 2) }} {{ $account->currency->symbol ?? '' }}
                                </p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs {{ $txn->status === 'posted' ? 'bg-[var(--success-bg)] text-[var(--success)]' : 'bg-[var(--warning-bg)] text-[var(--warning)]' }}">
                                    {{ ucfirst($txn->status) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-[var(--text-muted)] p-4">No transactions yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
                <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-4">Account info</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-[var(--text-muted)]">Account ID</span>
                        <span class="text-[var(--text)]">{{ $account->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[var(--text-muted)]">Account number</span>
                        <span class="text-[var(--text)] font-mono">{{ $account->account_number ?: '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[var(--text-muted)]">Currency</span>
                        <span class="text-[var(--text)]">{{ $account->currency->name ?? '—' }} ({{ $account->currency->code ?? '—' }})</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[var(--text-muted)]">Type</span>
                        <span class="text-[var(--text)]">{{ $account->type ?: '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-[var(--text-muted)]">Opened</span>
                        <span class="text-[var(--text)]">{{ $account->opened_at?->format('d M Y') ?: '—' }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
                <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-4">Audit log (last 20)</h2>
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @forelse($lockHistory as $log)
                        <div class="text-xs">
                            <p class="font-medium text-[var(--text)]">{{ $log->action }}</p>
                            <p class="text-[var(--text-muted)]">{{ $log->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-[var(--text-muted)]">No audit log entries.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection