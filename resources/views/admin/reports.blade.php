@extends('layouts.admin')

@section('title', 'Reports')
@section('content')
<div class="space-y-6">
    <div class="inline-flex rounded-lg bg-[var(--surface-raised)] p-1">
        <a href="{{ route('admin.reports.index', ['tab' => 'financial']) }}" @class(['px-4 py-2 rounded-md text-sm font-medium', 'bg-[var(--surface)] shadow-sm' => request('tab') === 'financial', 'text-[var(--text-muted)]' => request('tab') !== 'financial'])>Financial</a>
        <a href="{{ route('admin.reports.index', ['tab' => 'audit']) }}" @class(['px-4 py-2 rounded-md text-sm font-medium', 'bg-[var(--surface)] shadow-sm' => request('tab') === 'audit', 'text-[var(--text-muted)]' => request('tab') !== 'audit'])>Audit</a>
    </div>

    <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-semibold">{{ request('tab') === 'audit' ? 'Audit reports' : 'Financial reports' }}</h2>
            <div class="flex gap-2">
                <button class="px-4 py-2 bg-[var(--surface-raised)] rounded-xl text-sm font-medium hover:bg-[var(--line)]/50 transition-colors">Export CSV</button>
                <button class="px-4 py-2 bg-[var(--surface-raised)] rounded-xl text-sm font-medium hover:bg-[var(--line)]/50 transition-colors">Export PDF</button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-4 bg-[var(--surface-raised)] rounded-xl">
                <p class="text-sm text-[var(--text-muted)]">System balance</p>
                <p class="text-2xl font-semibold" style="font-variant-numeric: tabular-nums;">$1,284,300</p>
            </div>
            <div class="p-4 bg-[var(--surface-raised)] rounded-xl">
                <p class="text-sm text-[var(--text-muted)]">Volume (this month)</p>
                <p class="text-2xl font-semibold">1,247</p>
            </div>
            <div class="p-4 bg-[var(--surface-raised)] rounded-xl">
                <p class="text-sm text-[var(--text-muted)]">In vs Out</p>
                <p class="text-lg">In: $645,200 / Out: $639,100</p>
            </div>
        </div>
    </div>
</div>
@endsection
