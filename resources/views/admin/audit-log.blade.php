@extends('layouts.admin')

@section('title', 'Audit log')
@section('content')
<div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl overflow-hidden">
    <div class="p-6 border-b border-[var(--line)]">
        <p class="text-sm text-[var(--text-muted)]">Entries can't be changed or deleted.</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-[var(--surface-raised)]">
                <tr>
                    <th class="px-4 py-3 text-left">Time</th>
                    <th class="px-4 py-3 text-left">Admin</th>
                    <th class="px-4 py-3 text-left">Action</th>
                    <th class="px-4 py-3 text-left">Target</th>
                    <th class="px-4 py-3 text-left">Old value</th>
                    <th class="px-4 py-3 text-left">New value</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--line)]">
                @forelse($logs as $entry)
                    <tr class="hover:bg-[var(--surface-raised)]/50">
                        <td class="px-4 py-3">{{ $entry->created_at->format('d M Y, H:i') }}</td>
                        <td class="px-4 py-3">{{ $entry->admin->name ?? 'System' }}</td>
                        <td class="px-4 py-3">{{ $entry->action }}</td>
                        <td class="px-4 py-3">{{ class_basename($entry->target_type) }} #{{ $entry->target_id }}</td>
                        <td class="px-4 py-3 text-[var(--text-muted)]">{{ is_array($entry->old_value) ? json_encode($entry->old_value) : ($entry->old_value ?? '—') }}</td>
                        <td class="px-4 py-3">{{ is_array($entry->new_value) ? json_encode($entry->new_value) : ($entry->new_value ?? '—') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-[var(--text-muted)]">No audit entries.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-[var(--line)]">
        {{ $logs->links() }}
    </div>
</div>
@endsection
