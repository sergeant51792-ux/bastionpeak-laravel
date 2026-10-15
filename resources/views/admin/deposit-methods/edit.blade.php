@extends('layouts.admin')

@section('title', 'Edit Deposit Method')
@section('content')
<div class="space-y-6 max-w-2xl">
    <header class="page-header">
        <a href="{{ route('admin.deposit-methods.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--line-soft)] transition-colors" aria-label="Back">
            <x-icon name="arrow-left" class="w-5 h-5 text-[var(--text-muted)]" />
        </a>
        <h1 class="text-xl font-semibold">Edit Deposit Method</h1>
    </header>

    <form method="POST" action="{{ route('admin.deposit-methods.update', $method) }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label for="name" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Display Name</label>
            <input type="text" id="name" name="name" required maxlength="255" value="{{ old('name', $method->name) }}" class="input-field">
        </div>

        <div>
            <label for="code" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Code</label>
            <input type="text" id="code" name="code" required maxlength="50" value="{{ old('code', $method->code) }}" class="input-field">
            <p class="text-xs text-[var(--text-muted)] mt-1">Unique identifier used in the system.</p>
        </div>

        <div>
            <label for="type" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Type</label>
            <x-custom-select
                name="type"
                :options="[
                    'wire' => 'Wire transfer',
                    'crypto' => 'Cryptocurrency',
                    'swift' => 'SWIFT',
                    'sepa' => 'SEPA',
                    'ach' => 'ACH (US)',
                    'zelle' => 'Zelle (US)',
                    'faster_payments' => 'Faster Payments (UK)',
                    'bacs' => 'BACS (UK)',
                    'chaps' => 'CHAPS (UK)',
                    'giftcard' => 'Gift card',
                    'other' => 'Other',
                ]"
                placeholder="Select type"
                :value="old('type', $method->type)"
                required
            />
        </div>

        <div class="bg-[var(--surface-raised)] border border-[var(--line)] rounded-xl p-4">
            <h3 class="text-sm font-semibold text-[var(--text-muted)] mb-3">Global Deposit Details</h3>
            <p class="text-xs text-[var(--text-muted)] mb-3">These details appear for ALL accounts when users add money.</p>

            <div>
                <label for="icon" class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Icon</label>
                <input type="text" id="icon" name="icon" maxlength="255" value="{{ old('icon', $method->icon) }}" class="input-field">
            </div>

            <div class="mt-3">
                <label for="icon_image" class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Icon Image</label>
                @if($method->icon_image_path)
                    <div class="mb-3">
                        <img src="{{ url('storage-file/' .$method->icon_image_path) }}" alt="Current icon" class="w-16 h-16 object-contain border border-[var(--line)] rounded-lg">
                    </div>
                @endif
                <input type="file" id="icon_image" name="icon_image" accept="image/*" class="text-sm text-[var(--text-muted)]">
                <p class="text-xs text-[var(--text-muted)] mt-1">Upload a new icon image (optional). Leave blank to keep current.</p>
            </div>

            <div class="mt-3">
                <label for="address" class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Deposit Address</label>
                <textarea id="address" name="address" rows="2" maxlength="1000" class="input-field" placeholder="Bank account, crypto wallet address, etc.">{{ old('address', $method->address) }}</textarea>
                <p class="text-xs text-[var(--text-muted)] mt-1">The address users will send money to.</p>
            </div>

            <div class="mt-3">
                <label for="instructions" class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Instructions</label>
                <textarea id="instructions" name="instructions" rows="3" maxlength="2000" class="input-field" placeholder="Step-by-step instructions for depositing...">{{ old('instructions', $method->instructions) }}</textarea>
            </div>

            <div class="mt-3">
                <label for="qr_image" class="block text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">QR Code Image</label>
                @if($method->qr_path)
                    <div class="mb-3">
                        <img src="{{ url('storage-file/' .$method->qr_path) }}" alt="Current QR Code" class="w-24 h-24 object-contain border border-[var(--line)] rounded-lg p-1">
                    </div>
                    <label class="flex items-center gap-2 cursor-pointer mt-2">
                        <input type="checkbox" name="remove_qr" value="1" class="w-4 h-4 rounded border-[var(--line)] text-[var(--primary)] focus:ring-[var(--accent)]">
                        <span class="text-sm text-[var(--danger)]">Remove QR code</span>
                    </label>
                @endif
                <input type="file" id="qr_image" name="qr_image" accept="image/*" class="text-sm text-[var(--text-muted)] mt-2">
                <p class="text-xs text-[var(--text-muted)] mt-1">Upload a new QR code image (optional).</p>
            </div>
        </div>

        <div>
            <label for="description" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Description</label>
            <textarea id="description" name="description" rows="3" maxlength="2000" class="input-field" placeholder="Brief description shown to users">{{ old('description', $method->description) }}</textarea>
        </div>

        <div class="flex items-center gap-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_global" value="1" {{ old('is_global', $method->is_global) ? 'checked' : '' }} class="w-4 h-4 rounded border-[var(--line)] text-[var(--primary)] focus:ring-[var(--accent)]">
                <span class="text-sm font-medium text-[var(--text)]">Global (applies to all accounts)</span>
            </label>
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $method->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded border-[var(--line)] text-[var(--primary)] focus:ring-[var(--accent)]">
                <span class="text-sm font-medium text-[var(--text)]">Active</span>
            </label>
            <div>
                <label for="sort_order" class="block text-xs font-semibold text-[var(--text-muted)] uppercase">Sort Order</label>
                <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $method->sort_order) }}" min="0" class="input-field w-24">
            </div>
        </div>

        @if($errors->any())
            <div class="p-3 bg-[var(--danger-bg)] border border-[var(--danger)]/30 rounded-xl text-sm text-[var(--danger)]">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="flex-1 py-3.5 btn-primary">Update deposit method</button>
            <a href="{{ route('admin.deposit-methods.index') }}" class="px-6 py-3.5 bg-[var(--surface)] border border-[var(--line)] rounded-xl text-sm font-semibold text-[var(--text-muted)] hover:text-[var(--text)] transition-colors">Cancel</a>
        </div>
    </form>
</div>
@endsection