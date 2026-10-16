@extends('layouts.admin')

@section('title', $user->name)
@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.users.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--surface-raised)]" aria-label="Back">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="text-xl font-semibold text-[var(--text)]">{{ $user->name }}</h1>
                <p class="text-sm text-[var(--text-muted)]">{{ $user->email }}</p>
            </div>
        </div>
        <span @class(['px-3 py-1 rounded-full text-xs font-medium', 'bg-[var(--success-bg)] text-[var(--success)]' => $user->status === 'active', 'bg-[var(--accent-subtle)] text-[var(--accent)]' => $user->status === 'frozen', 'bg-[var(--danger-bg)] text-[var(--danger)]' => $user->status === 'locked', 'bg-[var(--line-soft)] text-[var(--text-muted)]' => $user->status === 'closed', 'bg-[var(--warning-bg)] text-[var(--warning)]' => $user->status === 'pending'])>
            {{ ucfirst($user->status) }}
        </span>
        @if($user->status === 'pending')
            <form method="POST" action="{{ route('admin.users.approve', $user) }}" class="inline" onsubmit="return confirm('Approve this signup and activate their account?')">
                @csrf
                <button type="submit" class="ml-2 px-4 py-1.5 bg-[var(--success)] text-white rounded-lg text-xs font-medium hover:opacity-90 transition-colors">
                    Approve signup
                </button>
            </form>
        @endif
        @if($user->status !== 'locked')
            @if($user->status === 'frozen')
                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="inline">
                    @csrf
                    <input type="hidden" name="name" value="{{ $user->name }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">
                    <input type="hidden" name="status" value="active">
                    <button type="submit" class="ml-2 px-2 py-1 bg-[var(--success-bg)] text-[var(--success)] rounded-lg text-xs font-medium hover:bg-[var(--success-bg)]/50 transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center" title="Unfreeze">
                        <x-icon name="play" class="w-4 h-4" />
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('admin.users.freeze', $user) }}" class="ml-2 inline" onsubmit="return confirmFreeze()">
                    @csrf
                    <input type="hidden" name="reason" id="freeze-reason" value="">
                    <button type="submit" class="p-2 bg-[var(--accent-subtle)] text-[var(--accent)] rounded-lg text-xs font-medium hover:bg-[var(--accent-subtle)]/50 transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center" title="Freeze account">
                        <x-icon name="pause" class="w-4 h-4" />
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.users.block', $user) }}" class="ml-1 inline" onsubmit="return confirmBlock()">
                    @csrf
                    <input type="hidden" name="reason" id="block-reason" value="Account locked">
                    <button type="submit" class="p-2 bg-[var(--danger-bg)] text-[var(--danger)] rounded-lg text-xs font-medium hover:bg-[var(--danger-bg)]/50 transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center">
                        <x-icon name="lock" class="w-4 h-4" />
                    </button>
                </form>
            @endif
        @endif
    </div>

    @php
        $tabs = [
            'overview'    => 'Overview',
            'accounts'    => 'Accounts',
            'approvals'   => 'Approvals',
            'transactions' => 'Transactions',
            'messages'    => 'Messages',
            'activity'    => 'Activity',
        ];
    @endphp
    <div class="border-b border-[var(--line)]">
        <nav class="-mb-px flex overflow-x-auto gap-2">
            @foreach($tabs as $tabKey => $tabLabel)
                <a href="?tab={{ $tabKey }}" @class([
                    'px-4 py-2 border-b-2 text-sm font-medium whitespace-nowrap transition-colors',
                    'border-[var(--accent)] text-[var(--accent)]' => $tab === $tabKey,
                    'border-transparent text-[var(--text-muted)] hover:text-[var(--text)] hover:border-[var(--line)]' => $tab !== $tabKey,
                ])>{{ $tabLabel }}</a>
            @endforeach
        </nav>
    </div>

    {{-- Overview Tab --}}
    @if($tab === 'overview')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
                    <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-4">User information</h2>
                    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Full name</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="input-field">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Email</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="input-field">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Phone</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="input-field" placeholder="Phone number">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Status</label>
                            <x-custom-select name="status" :value="$user->status" :options="[
                                'active' => 'Active',
                                'frozen' => 'Frozen',
                                'locked' => 'Locked',
                                'closed' => 'Closed',
                            ]" />
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Address</label>
                            <input type="text" name="address" value="{{ old('address', $user->address) }}" class="input-field" placeholder="Street address">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">City</label>
                            <input type="text" name="city" value="{{ old('city', $user->city) }}" class="input-field">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">State</label>
                            <input type="text" name="state" value="{{ old('state', $user->state) }}" class="input-field">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Country</label>
                            <input type="text" name="country" value="{{ old('country', $user->country) }}" class="input-field">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Postal code</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', $user->postal_code) }}" class="input-field">
                        </div>
                        <div class="sm:col-span-2 flex justify-end">
                            <button type="submit" class="px-4 py-2.5 bg-[var(--primary)] text-white rounded-xl text-sm font-medium hover:opacity-90 transition-opacity">Save changes</button>
                        </div>
                    </form>
                </div>

                <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
                    <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-4">Change password</h2>
                    <form method="POST" action="{{ route('admin.users.password', $user) }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">New password</label>
                            <input type="password" name="password" required class="input-field">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Confirm password</label>
                            <input type="password" name="password_confirmation" required class="input-field">
                        </div>
                        <div class="sm:col-span-2 flex justify-end">
                            <button type="submit" class="px-4 py-2.5 bg-[var(--surface-raised)] border border-[var(--line)] rounded-xl text-sm font-medium hover:bg-[var(--line-soft)] transition-colors">Update password</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="space-y-6">
                {{-- Profile picture --}}
                <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-5 text-center">
                    @if($user->profile_picture)
                        <img src="{{ $user->profile_picture_url }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-full object-cover mx-auto mb-3 border-4 border-[var(--line)]">
                    @else
                        <div class="w-20 h-20 rounded-full bg-[var(--primary)] flex items-center justify-center text-white mx-auto mb-3 font-bold text-2xl">
                            {{ strtoupper(substr($user->name ?? $user->email, 0, 2)) }}
                        </div>
                    @endif

                    <p class="font-semibold text-[var(--text)]">{{ $user->name }}</p>
                    <p class="text-xs text-[var(--text-muted)] mt-1">{{ $user->email }}</p>
                    <p class="text-xs text-[var(--text-muted)] mt-1">Member since {{ $user->created_at->format('M Y') }}</p>

                    <div class="mt-3 flex gap-2 justify-center">
                        <form method="POST" action="{{ route('admin.users.picture.upload', $user) }}" enctype="multipart/form-data" class="inline-flex items-center gap-2" onsubmit="document.getElementById('picture-input').click(); return false;">
                            @csrf
                            <input type="file" name="picture" accept="image/*" class="hidden" id="picture-input" onchange="this.form.submit()">
                            <button type="button" onclick="document.getElementById('picture-input').click()" class="px-3 py-1.5 bg-[var(--surface-raised)] border border-[var(--line)] rounded-lg text-xs font-medium text-[var(--text)] hover:bg-[var(--line-soft)] transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center">
                                Upload photo
                            </button>
                        </form>
                        @if($user->profile_picture)
                            <form method="POST" action="{{ route('admin.users.picture.remove', $user) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Remove this profile picture?')"
                                        class="px-3 py-1.5 bg-[var(--danger-bg)] text-[var(--danger)] rounded-lg text-xs font-medium hover:bg-[var(--danger-bg)]/50 transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center">
                                    Remove
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-5 space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-[var(--text-muted)]">Accounts</span>
                        <span class="font-medium">{{ $user->accounts->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-[var(--text-muted)]">Total transactions</span>
                        <span class="font-medium">{{ $transactions->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-[var(--text-muted)]">Pending approvals</span>
                        <span class="font-medium text-[var(--warning)]">{{ $requests->count() }}</span>
                    </div>
                </div>

                <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-5 space-y-3">
                    <h2 class="text-sm font-semibold text-[var(--text-muted)]">Authenticator (2-step)</h2>

                    @if(session('totp_issued'))
                        <div class="p-3 bg-[var(--success-bg)] border border-[var(--success)]/30 rounded-xl">
                            <p class="text-xs text-[var(--text-muted)] mb-1">Setup key issued. Share this key with the customer:</p>
                            <p class="font-mono text-sm font-semibold text-[var(--success)] break-all">{{ session('totp_issued') }}</p>
                        </div>
                    @endif

                    @if($user->hasTotp())
                        <p class="text-xs text-[var(--text-muted)]">Enabled - the customer must enter an authenticator code to sign in.</p>
                        <p class="text-xs text-[var(--text-muted)]">Current code:
                            <span class="font-mono font-semibold text-[var(--text)]">{{ app(\App\Services\TotpService::class)->code($user->totp_secret) }}</span>
                            <span class="opacity-60">({{ app(\App\Services\TotpService::class)->secondsRemaining() }}s)</span>
                        </p>
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('admin.users.authenticator', $user) }}" class="flex-1">
                                @csrf
                                <button type="submit" class="w-full px-3 py-2 bg-[var(--surface-raised)] border border-[var(--line)] rounded-lg text-xs font-medium hover:bg-[var(--line-soft)] transition-colors">Re-issue key</button>
                            </form>
                            <form method="POST" action="{{ route('admin.users.authenticator.destroy', $user) }}" class="flex-1" onsubmit="return confirm('Disable authenticator for this customer?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-3 py-2 bg-[var(--danger-bg)] text-[var(--danger)] border border-[var(--danger)]/20 rounded-lg text-xs font-medium hover:bg-[var(--danger-bg)]/50 transition-colors">Disable</button>
                            </form>
                        </div>
                    @else
                        <p class="text-xs text-[var(--text-muted)]">Not enabled. Issue a setup key and share the current code with the customer so they can add it to their authenticator app.</p>
                        <form method="POST" action="{{ route('admin.users.authenticator', $user) }}">
                            @csrf
                            <button type="submit" class="w-full px-3 py-2 bg-[var(--primary)] text-white rounded-lg text-xs font-medium hover:opacity-90 transition-colors">Issue authenticator key</button>
                        </form>
                    @endif
                </div>

                <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-5 space-y-3">
                    <h2 class="text-sm font-semibold text-[var(--text-muted)]">Login PIN (4-digit)</h2>

                    @if(session('otp_code'))
                        <div class="p-3 bg-[var(--accent-subtle)] border border-[var(--accent)]/20 rounded-xl">
                            <p class="text-xs text-[var(--text-muted)] mb-1">PIN generated. Share this PIN with the customer (valid for 40 seconds):</p>
                            <p class="font-mono text-2xl font-bold text-[var(--accent)] break-all">{{ session('otp_code') }}</p>
                        </div>
                    @endif

                    @if($user->otp_enabled)
                        <p class="text-xs text-[var(--text-muted)]">Login PIN is <strong>enabled</strong>. The customer must enter a 4-digit PIN to sign in. Generate a new PIN below.</p>
                        <form method="POST" action="{{ route('admin.users.pin', $user) }}" onsubmit="return confirm('Generate a new 4-digit PIN for this user?')" class="pt-1">
                            @csrf
                            <button type="submit" class="px-3 py-2 bg-[var(--primary)] text-white rounded-lg text-xs font-medium hover:opacity-90 transition-opacity">Generate new PIN</button>
                        </form>
                    @else
                        <p class="text-xs text-[var(--text-muted)]">Login PIN is not enabled. Enable it to require a 4-digit PIN on sign-in.</p>
                        <form method="POST" action="{{ route('admin.users.pin', $user) }}" class="pt-1">
                            @csrf
                            <button type="submit" class="px-3 py-2 bg-[var(--primary)] text-white rounded-lg text-xs font-medium hover:opacity-90 transition-opacity">Enable &amp; generate PIN</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Accounts Tab --}}
    @if($tab === 'accounts')
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-[var(--text)]">Accounts</h2>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @forelse($accounts as $account)
                    <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-5">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="font-semibold text-[var(--text)]">{{ $account->name }}</h3>
                            <span @class(['px-2.5 py-1 rounded-full text-xs font-medium', 'bg-[var(--success-bg)] text-[var(--success)]' => $account->status === 'active', 'bg-[var(--line-soft)] text-[var(--text-muted)]' => $account->status !== 'active'])>
                                {{ ucfirst($account->status) }}
                            </span>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-[var(--text-muted)]">Account number</span>
                                <span class="font-mono">{{ $account->account_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[var(--text-muted)]">Currency</span>
                                <span>{{ $account?->currency?->code }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-[var(--text-muted)]">Balance</span>
                                <span style="font-variant-numeric: tabular-nums;">{{ number_format((float) $account->balance, $account?->currency?->decimals ?? 2) }} {{ $account?->currency?->symbol }}</span>
                            </div>
                        </div>
                        <a href="{{ route('admin.accounts.deposit-methods.index', $account) }}" class="mt-3 inline-flex items-center gap-2 px-3 py-2 bg-[var(--primary)]/10 text-[var(--primary)] rounded-lg text-xs font-medium hover:bg-[var(--primary)]/20 transition-colors">
                            <x-icon name="wallet" class="w-4 h-4" />
                            Manage deposit methods
                        </a>
                    </div>
                @empty
                    <div class="col-span-2 text-center py-8 text-[var(--text-muted)]">No accounts found.</div>
                @endforelse
            </div>
        </div>
    @endif

    {{-- Approvals Tab --}}
    @if($tab === 'approvals')
        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl overflow-hidden">
            <h2 class="text-sm font-semibold text-[var(--text-muted)] p-5 pb-3">Pending approvals ({{ $requests->count() }})</h2>
            <div class="divide-y divide-[var(--line)]">
                @forelse($requests as $req)
                    @php
                        $reqTypeLabel = $req->type instanceof \App\Enums\TransactionType ? $req->type->label() : ($req->type ?? '-');
                        $reqStatus = $req->status instanceof \App\Enums\TransactionStatus ? $req->status->value : ($req->status ?? '-');
                        $reqStatusLabel = match($reqStatus) {
                            'pending_review', 'pending_match' => 'Pending',
                            default => ucfirst($reqStatus),
                        };
                    @endphp
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div>
                                <p class="font-medium">{{ $reqTypeLabel }}</p>
                                <p class="text-sm text-[var(--text-muted)]">{{ Str::limit($req->memo ?? 'No memo', 60) }}</p>
                                <p class="text-xs text-[var(--text-muted)] mt-1">{{ $req->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="font-semibold" style="font-variant-numeric: tabular-nums;">{{ number_format((float) $req->amount, $req->currency?->decimals ?? 2) }} {{ $req->currency?->symbol ?? '' }}</p>
                                <span class="text-xs text-[var(--text-muted)]">{{ $reqStatusLabel }}</span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <form method="POST" action="{{ route('admin.approvals.approve', $req) }}" class="flex-1">
                                @csrf
                                <button type="submit"
                                        onclick="return confirm('Approve this {{ $reqTypeLabel }} request? Funds will be processed immediately.')"
                                        class="w-full py-2 bg-[#059669] text-white rounded-xl text-xs font-semibold hover:opacity-90 transition-opacity min-h-[44px] flex items-center justify-center gap-1">
                                    <x-icon name="check" class="w-3.5 h-3.5" />
                                    Approve
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.approvals.reject', $req) }}" class="flex-1">
                                @csrf
                                <input type="hidden" name="reason" value="Transaction did not meet verification requirements">
                                    <button type="submit"
                                            onclick="return confirm('Decline this {{ $reqTypeLabel }} request? The customer will be notified.')"
                                            class="w-full py-2 bg-[#dc2626] text-white rounded-xl text-xs font-semibold hover:opacity-90 transition-opacity min-h-[44px] flex items-center justify-center gap-1">
                                        <x-icon name="x" class="w-3.5 h-3.5" />
                                        Decline
                                    </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-[var(--text-muted)]">No pending approvals.</div>
                @endforelse
            </div>
        </div>
    @endif

    {{-- Transactions Tab --}}
    @if($tab === 'transactions')
        <div class="space-y-4">
            <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
                <h2 class="text-base font-bold text-[var(--text)] mb-4">Add transaction</h2>
                <form method="POST" action="{{ route('admin.transactions.store') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-4">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
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
                        <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Account</label>
                        <select name="account_id" required class="input-field">
                            <option value="">Select account</option>
                            @foreach($accounts as $account)
                                <option value="{{ $account->id }}">{{ $account->name }} ({{ $account?->currency?->code }})</option>
                            @endforeach
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
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Memo</label>
                        <input type="text" name="memo" placeholder="Reason or note" class="input-field">
                    </div>
                    <div class="sm:col-span-5 flex justify-end">
                        <button type="submit" class="btn-primary">Add transaction</button>
                    </div>
                </form>
            </div>

            <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl overflow-hidden">
                <h2 class="text-sm font-semibold text-[var(--text-muted)] p-5 pb-3">Transaction history</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-[var(--surface-raised)]">
                            <tr>
                                <th class="px-4 py-2 text-left">Date</th>
                                <th class="px-4 py-2 text-left">Type</th>
                                <th class="px-4 py-2 text-left">Memo</th>
                                <th class="px-4 py-2 text-right">Amount</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-left">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--line)]">
                            @forelse($transactions as $txn)
                                 @php
                                     $typeLabel = $txn->type instanceof \App\Enums\TransactionType ? $txn->type->label() : ($txn->type ?? '-');
                                     $status = $txn->status instanceof \App\Enums\TransactionStatus ? $txn->status->value : ($txn->status ?? '-');
                                     $statusLabel = match($status) {
                                         'approved', 'credited', 'posted' => 'Successful',
                                         'rejected', 'reversed' => 'Failed',
                                         'pending_review', 'pending_match' => 'Pending',
                                         default => ucfirst($status),
                                     };
                                 @endphp
                                 <tr class="hover:bg-[var(--surface-raised)]/50" data-txn-row="{{ $txn->id }}">
                                     <td class="px-4 py-2">{{ $txn->created_at->format('d M Y, H:i') }}</td>
                                     <td class="px-4 py-2">{{ $typeLabel }}</td>
                                     <td class="px-4 py-2">{{ Str::limit($txn->memo ?? '-', 40) }}</td>
                                     <td class="px-4 py-2 text-right" style="font-variant-numeric: tabular-nums;">{{ number_format((float) $txn->amount, $txn?->currency?->decimals ?? 2) }} {{ $txn?->currency?->symbol ?? '' }}</td>
                                       <td class="px-4 py-2">
                                           <x-status-pill :status="$txn->status" :label="$statusLabel" />
                                       </td>
                                     <td class="px-4 py-2">
                                         <div class="flex items-center gap-1 justify-end">
                                             <button type="button" onclick="toggleTxnEdit({{ $txn->id }})"
                                                     class="px-2.5 py-1.5 bg-[var(--surface-raised)] border border-[var(--line)] rounded-lg text-xs font-medium text-[var(--accent)] hover:text-[var(--accent-dark)] hover:border-[var(--accent)] transition-all min-h-[44px] min-w-[44px] flex items-center justify-center"
                                                     title="Edit">
                                                 <x-icon name="edit-3" class="w-3.5 h-3.5" />
                                             </button>
                                             <a href="{{ route('admin.transactions.show', $txn) }}" class="px-2.5 py-1.5 bg-[var(--surface-raised)] border border-[var(--line)] rounded-lg text-xs font-medium text-[var(--accent)] hover:text-[var(--accent-dark)] hover:border-[var(--accent)] transition-all min-h-[44px] min-w-[44px] flex items-center justify-center">
                                                 <x-icon name="eye" class="w-3.5 h-3.5" />
                                             </a>
                                             @if($txn->status !== \App\Enums\TransactionStatus::Posted && $txn->status !== \App\Enums\TransactionStatus::Reversed)
                                                 <form method="POST" action="{{ route('admin.transactions.update', $txn) }}" class="inline">
                                                     @csrf
                                                     <input type="hidden" name="type" value="{{ $txn->type }}">
                                                     <input type="hidden" name="amount" value="{{ $txn->amount }}">
                                                     <input type="hidden" name="account_id" value="{{ $txn->account_id }}">
                                                     <input type="hidden" name="memo" value="{{ $txn->memo }}">
                                                     <input type="hidden" name="reference" value="{{ $txn->reference }}">
                                                     <input type="hidden" name="status" value="posted">
                                                      <button type="submit" onclick="return confirm('Mark as posted?')" class="px-2.5 py-1.5 bg-[var(--success-bg)] text-[var(--success)] rounded-lg text-xs font-medium hover:bg-[var(--success-bg)]/50 transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center" title="Approve">
                                                         <x-icon name="check-circle" class="w-3.5 h-3.5" />
                                                     </button>
                                                 </form>
                                             @endif
                                             <form method="POST" action="{{ route('admin.transactions.destroy', $txn) }}" class="inline" onsubmit="return confirm('Delete this transaction? This cannot be undone.')">
                                                 @csrf
                                                 @method('DELETE')
                                                 <button type="submit" class="px-2.5 py-1.5 bg-[var(--danger-bg)] text-[var(--danger)] rounded-lg text-xs font-medium hover:bg-[var(--danger-bg)]/50 transition-colors min-h-[44px] min-w-[44px] flex items-center justify-center" title="Delete">
                                                     <x-icon name="trash-2" class="w-3.5 h-3.5" />
                                                 </button>
                                             </form>
                                         </div>
                                     </td>
                                 </tr>
                                 <tr id="txn-edit-row-{{ $txn->id }}" class="hidden bg-[var(--surface-raised)]/30">
                                     <td colspan="6" class="px-4 py-3">
                                         <form method="POST" action="{{ route('admin.transactions.update', $txn) }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                                             @csrf
                                             <input type="hidden" name="type" value="{{ $txn->type }}">
                                             <input type="hidden" name="account_id" value="{{ $txn->account_id }}">
                                             <input type="hidden" name="reference" value="{{ $txn->reference }}">
                                             <div>
                                                 <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1">Amount</label>
                                                 <input type="number" step="0.01" name="amount" value="{{ old('amount', $txn->amount) }}" class="input-field w-full">
                                             </div>
                                             <div>
                                                 <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1">Status</label>
                                                 <select name="status" class="input-field w-full">
                                                     <option value="posted" {{ $txn->status === \App\Enums\TransactionStatus::Posted ? 'selected' : '' }}>Posted</option>
                                                     <option value="approved" {{ $txn->status === \App\Enums\TransactionStatus::Approved ? 'selected' : '' }}>Approved</option>
                                                     <option value="pending_review" {{ $txn->status === \App\Enums\TransactionStatus::PendingReview ? 'selected' : '' }}>Pending review</option>
                                                     <option value="pending_match" {{ $txn->status === \App\Enums\TransactionStatus::PendingMatch ? 'selected' : '' }}>Pending match</option>
                                                     <option value="rejected" {{ $txn->status === \App\Enums\TransactionStatus::Rejected ? 'selected' : '' }}>Rejected</option>
                                                     <option value="reversed" {{ $txn->status === \App\Enums\TransactionStatus::Reversed ? 'selected' : '' }}>Reversed</option>
                                                 </select>
                                             </div>
                                              <div>
                                                  <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1">Date &amp; time</label>
                                                  <input type="datetime-local" name="created_at" value="{{ old('created_at', $txn->created_at ? $txn->created_at->format('Y-m-d\TH:i') : '') }}" class="input-field w-full">
                                              </div>
                                             <div class="md:col-span-2">
                                                 <label class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1">Memo</label>
                                                 <input type="text" name="memo" value="{{ old('memo', $txn->memo) }}" class="input-field w-full" placeholder="Reason or note">
                                             </div>
                                             <div class="md:col-span-5 flex justify-end gap-2 pt-2">
                                                 <button type="button" onclick="toggleTxnEdit({{ $txn->id }})"
                                                         class="px-4 py-2 text-sm font-medium text-[var(--text-muted)] hover:text-[var(--text)] border border-[var(--line)] rounded-lg min-h-[44px] transition-colors">
                                                     Cancel
                                                 </button>
                                                 <button type="submit"
                                                         class="px-4 py-2 bg-[var(--primary)] text-white rounded-xl text-sm font-medium hover:opacity-90 transition-opacity min-h-[44px]">
                                                     Save changes
                                                 </button>
                                             </div>
                                         </form>
                                     </td>
                                 </tr>
                             @empty
                                 <tr><td colspan="6" class="px-4 py-6 text-center text-[var(--text-muted)]">No transactions.</td></tr>
                             @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- Messages Tab --}}
    @if($tab === 'messages')
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
                <h2 class="text-base font-bold text-[var(--text)] mb-4">Send message</h2>
                 <form method="POST" action="{{ route('admin.users.message', $user) }}" class="space-y-4">
                     @csrf
                     @php
                         $existingThread = $messages->first()?->thread_id;
                     @endphp
                     @if($existingThread)
                     <input type="hidden" name="thread_id" value="{{ $existingThread }}">
                     @endif
                     <div>
                        <label for="subject" class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Subject</label>
                        <input type="text" id="subject" name="subject" required class="input-field">
                    </div>
                    <div>
                        <label for="body" class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Message</label>
                        <textarea id="body" name="body" rows="4" required class="input-field"></textarea>
                    </div>
                    <button type="submit" class="btn-primary">Send message</button>
                </form>
            </div>
            <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
                <h2 class="text-base font-bold text-[var(--text)] mb-4">Conversation</h2>
                <div class="space-y-3 max-h-[400px] overflow-y-auto">
                    @forelse($messages as $message)
                        <div class="p-3 rounded-xl {{ $message->sender_id === Auth::id() ? 'bg-[var(--accent-subtle)] ml-8' : 'bg-[var(--surface-raised)] mr-8' }}">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-semibold">{{ $message->sender->name ?? 'Unknown' }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-[var(--text-muted)]">{{ $message->created_at->diffForHumans() }}</span>
                                    @if(is_null($message->read_at) && $message->sender_id !== Auth::id())
                                        <span class="w-2 h-2 rounded-full bg-[var(--accent)]"></span>
                                    @endif
                                    <form method="POST" action="{{ route('admin.messages.thread.destroy', $message) }}" class="inline" onsubmit="return confirm('Delete this message?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-[var(--text-muted)] hover:text-[var(--danger)] transition-colors">Delete</button>
                                    </form>
                                </div>
                            </div>
                            <p class="text-sm font-medium">{{ $message->subject }}</p>
                            <p class="text-sm text-[var(--text-muted)] mt-1">{{ $message->body }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-[var(--text-muted)]">No messages yet.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    {{-- Activity Tab --}}
    @if($tab === 'activity')
        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl overflow-hidden">
            <h2 class="text-sm font-semibold text-[var(--text-muted)] p-5 pb-3">Activity log</h2>
            <div class="divide-y divide-[var(--line)]">
                @forelse($activityLog as $log)
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-semibold">{{ $log->action ?? 'Activity' }}</span>
                            <span class="text-xs text-[var(--text-muted)]">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        @if($log->details)
                            <p class="text-xs text-[var(--text-muted)]">{{ Str::limit($log->details, 100) }}</p>
                        @endif
                        @if($log->actor)
                            <p class="text-xs text-[var(--text-muted)] mt-1">By: {{ $log->actor->name }}</p>
                        @endif
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-[var(--text-muted)]">No activity recorded.</div>
                @endforelse
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
function toggleTxnEdit(id) {
    var row = document.getElementById('txn-edit-row-' + id);
    if (row) {
        row.classList.toggle('hidden');
    }
}

function confirmFreeze() {
    var reason = prompt('Enter a reason for freezing this account (optional):', 'Frozen due to policy violation');
    if (reason !== null) {
        document.getElementById('freeze-reason').value = reason || 'Frozen due to policy violation';
        return confirm('Freeze this account? The user will be unable to add money, receive, or transfer funds.');
    }
    return false;
}

function confirmBlock() {
    var reason = prompt('Enter a reason for locking this account (optional):', 'Account locked');
    if (reason !== null) {
        document.getElementById('block-reason').value = reason || 'Account locked';
        return confirm('Lock this account? The user will not be able to sign in.');
    }
    return false;
}
</script>
@endpush
@endsection

