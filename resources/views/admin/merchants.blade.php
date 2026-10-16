@extends('layouts.admin')

@section('title', 'Merchants')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <input type="search" placeholder="Search merchants..." class="px-4 py-2 bg-[var(--surface)] border border-[var(--line)] rounded-xl text-sm w-64">
            <x-custom-select
                name="status"
                placeholder="All statuses"
                :options="[
                    '' => 'All statuses',
                    'active' => 'Active',
                    'deactivated' => 'Deactivated',
                ]"
            />
        </div>
        <button type="button" disabled class="px-4 py-2 bg-[var(--text-muted)] text-[var(--bg)] rounded-xl text-sm font-medium opacity-50 cursor-not-allowed">Add merchant</button>
    </div>

    <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-[var(--surface-raised)]">
                <tr>
                    <th class="px-4 py-3 text-left">Name</th>
                    <th class="px-4 py-3 text-left">Category</th>
                    <th class="px-4 py-3 text-left">Receiving cap</th>
                    <th class="px-4 py-3 text-left">MTD received</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--line)]">
                @foreach($merchants as $merchant)
                    @php
                        $status = $merchant->status === 'active' ? 'active' : 'deactivated';
                    @endphp
                    <tr class="hover:bg-[var(--surface-raised)]/50 cursor-pointer">
                        <td class="px-4 py-3 font-medium">{{ $merchant->name }}</td>
                        <td class="px-4 py-3">{{ $merchant->category }}</td>
                        <td class="px-4 py-3" style="font-variant-numeric: tabular-nums;">${{ number_format($merchant->receiving_cap, 0) }}</td>
                            <td class="px-4 py-3" style="font-variant-numeric: tabular-nums;">{{ number_format($merchant->transactions->sum('amount'), 2) }} {{ $merchant->transactions->first()?->currency?->symbol ?? '$' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $status === 'active' ? 'bg-[var(--success-bg)] text-[var(--success)]' : 'bg-[var(--danger-bg)] text-[var(--danger)]' }}">{{ ucfirst($status) }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.merchants.show', $merchant) }}" class="px-3 py-1.5 bg-[var(--surface)] border border-[var(--line)] rounded-lg text-xs font-bold text-[var(--accent)] hover:text-[var(--accent-dark)] hover:border-[var(--accent)] transition-all">View</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
