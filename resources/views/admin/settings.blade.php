@extends('layouts.admin')

@section('title', 'Settings')
@section('content')
<div class="space-y-6">
    <div class="inline-flex rounded-lg bg-[var(--surface-raised)] p-1">
        <a href="{{ route('admin.settings.index', ['tab' => 'general']) }}" @class(['px-4 py-2 rounded-md text-sm font-medium', 'bg-[var(--surface)] shadow-sm' => request('tab') === 'general', 'text-[var(--text-muted)]' => request('tab') !== 'general'])>General</a>
        <a href="{{ route('admin.settings.index', ['tab' => 'security']) }}" @class(['px-4 py-2 rounded-md text-sm font-medium', 'bg-[var(--surface)] shadow-sm' => request('tab') === 'security', 'text-[var(--text-muted)]' => request('tab') !== 'security'])>Security</a>
        <a href="{{ route('admin.settings.index', ['tab' => 'transactions']) }}" @class(['px-4 py-2 rounded-md text-sm font-medium', 'bg-[var(--surface)] shadow-sm' => request('tab') === 'transactions', 'text-[var(--text-muted)]' => request('tab') !== 'transactions'])>Transactions</a>
        <a href="{{ route('admin.settings.index', ['tab' => 'currencies']) }}" @class(['px-4 py-2 rounded-md text-sm font-medium', 'bg-[var(--surface)] shadow-sm' => request('tab') === 'currencies', 'text-[var(--text-muted)]' => request('tab') !== 'currencies'])>Currencies</a>
    </div>

    <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
        <h2 class="text-lg font-semibold mb-4 capitalize">{{ request('tab') }} settings</h2>
        <p class="text-[var(--text-muted)]">Settings for the {{ request('tab') }} group. Production implementation uses SystemSetting model.</p>
        <div class="mt-6 p-4 bg-[var(--surface-raised)] rounded-xl">
            <p class="text-sm text-[var(--text-muted)]">Saving shows a diff dialog before applying, and each save is written to the audit log.</p>
        </div>
    </div>
</div>
@endsection
