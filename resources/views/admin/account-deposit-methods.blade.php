@extends('layouts.admin')

@section('title', 'Deposit Methods - ' . $account->name)
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.accounts.show', $account) }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--surface-raised)]" aria-label="Back">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="text-xl font-semibold">{{ $account->name }}</h1>
                <p class="text-sm text-[var(--text-muted)]">{{ $account->user->name }} • {{ $account->currency->code }}</p>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-[var(--text)]">Deposit methods</h2>
    </div>

    <div class="space-y-6">
        @foreach($depositMethods as $method)
            <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-5">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-[var(--text)]">{{ $method->name }}</h3>
                        <p class="text-sm text-[var(--text-muted)]">{{ ucfirst($method->type) }} • Code: {{ $method->code }}</p>
                        @if($method->description)
                            <p class="text-xs text-[var(--text-muted)] mt-1">{{ $method->description }}</p>
                        @endif
                    </div>
                    @php
                        $attached = $account->depositMethods()->where('deposit_method_id', $method->id)->first();
                    @endphp
                    @if($attached)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Configured
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-700 border border-gray-200">
                            Not configured
                        </span>
                    @endif
                </div>

                @if(!$attached)
                    <form method="POST" action="{{ route('admin.accounts.deposit-methods.store', $account) }}" class="space-y-4">
                        @csrf
                        <input type="hidden" name="deposit_method_id" value="{{ $method->id }}">
                        <div>
                            <label class="block text-xs font-semibold text-[var(--text-muted)] mb-1.5">Address</label>
                            <input type="text" name="address" class="input-field" placeholder="Wallet address or bank account number">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[var(--text-muted)] mb-1.5">Network / Reference</label>
                            <input type="text" name="metadata[network]" class="input-field" placeholder="e.g. TRC20, ERC20, Routing number">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[var(--text-muted)] mb-1.5">Instructions</label>
                            <textarea name="instructions" rows="2" class="input-field" placeholder="Deposit instructions for this method"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[var(--text-muted)] mb-1.5">QR code</label>
                            <input type="file" name="qr" accept="image/png,image/jpeg" class="text-sm">
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" checked class="w-4 h-4 rounded border-[var(--line)] text-[var(--primary)] focus:ring-[var(--accent)]">
                                <span class="text-sm font-medium text-[var(--text)]">Active</span>
                            </label>
                            <button type="submit" class="px-4 py-2 bg-[var(--text)] text-[var(--bg)] rounded-lg text-xs font-medium hover:opacity-90 transition-opacity">Add</button>
                        </div>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.accounts.deposit-methods.update', [$account, $method]) }}" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-semibold text-[var(--text-muted)] mb-1.5">Address</label>
                            <input type="text" name="address" value="{{ old('address', $attached ? $attached->pivot->address : '') }}" class="input-field" placeholder="Wallet address or bank account number">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-[var(--text-muted)] mb-1.5">Account name</label>
                            <input type="text" name="metadata[account_name]" value="{{ old('metadata.account_name', $attached && $attached->pivot->metadata ? ($attached->pivot->metadata['account_name'] ?? '') : '') }}" class="input-field" placeholder="Account holder name">
                        </div>

                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-muted)] mb-1.5">Network / Reference</label>
                        <input type="text" name="metadata[network]" value="{{ old('metadata.network', $attached && $attached->pivot->metadata ? ($attached->pivot->metadata['network'] ?? '') : '') }}" class="input-field" placeholder="e.g. TRC20, ERC20, Routing number">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-muted)] mb-1.5">Instructions</label>
                        <textarea name="instructions" rows="2" class="input-field" placeholder="Deposit instructions for this method">{{ old('instructions', $attached ? $attached->pivot->instructions : '') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[var(--text-muted)] mb-1.5">QR code</label>
                        @if($attached && $attached->pivot->qr_path)
                            <div class="flex items-center gap-2 mb-2">
                                <img src="{{ asset('storage/' . $attached->pivot->qr_path) }}" alt="QR" class="w-24 h-24 object-contain border border-[var(--line)] rounded-xl">
                                <button type="button" onclick="removeQr(this)" data-id="{{ $attached->pivot->id ?? '' }}" class="p-1 text-red-600 hover:bg-red-50 rounded min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Remove QR">
                                    <x-icon name="x" class="w-4 h-4" />
                                </button>
                            </div>
                            <input type="hidden" name="remove_qr" value="0" id="remove-qr-input">
                        @endif
                        <input type="file" name="qr" accept="image/png,image/jpeg" class="text-sm" {{ $attached && $attached->pivot->qr_path ? '' : '' }}>
                        <p class="text-xs text-[var(--text-muted)] mt-1">Leave empty to keep existing image</p>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ ($attached && $attached->pivot->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded border-[var(--line)] text-[var(--primary)] focus:ring-[var(--accent)]">
                            <span class="text-sm font-medium text-[var(--text)]">Active</span>
                        </label>
                        <button type="submit" class="px-4 py-2 bg-[var(--text)] text-[var(--bg)] rounded-lg text-xs font-medium hover:opacity-90 transition-opacity">Save</button>
                    </div>
                    </form>
                    @endif
                </div>
            @endforeach
    </div>
</div>

@push('scripts')
<script>
function removeQr(btn) {
    const container = btn.closest('.flex.items-center.gap-2');
    if (!container) return;
    const img = container.querySelector('img');
    if (img) img.remove();
    const input = document.getElementById('remove-qr-input');
    if (input) input.value = '1';
    btn.remove();
}
</script>
@endpush
@endsection
