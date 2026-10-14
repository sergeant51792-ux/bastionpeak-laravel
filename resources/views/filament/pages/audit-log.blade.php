<x-filament-panels::page>
    <div class="bg-[var(--surface)] rounded-lg border border-[var(--line)]">
        <div class="p-6 border-b border-[var(--line)]">
            <p class="text-sm text-[var(--text-muted)]">Entries can't be changed or deleted.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-[var(--surface-raised)]">
                    <tr>
                        <th class="px-4 py-3 text-left text-[var(--text)]">Time</th>
                        <th class="px-4 py-3 text-left text-[var(--text)]">Admin</th>
                        <th class="px-4 py-3 text-left text-[var(--text)]">Action</th>
                        <th class="px-4 py-3 text-left text-[var(--text)]">Target</th>
                        <th class="px-4 py-3 text-left text-[var(--text)]">Old value</th>
                        <th class="px-4 py-3 text-left text-[var(--text)]">New value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--line)]">
                    @forelse($logs as $log)
                        <tr class="hover:bg-[var(--surface-raised)]/50">
                            <td class="px-4 py-3">{{ $log->created_at->format('d M Y, H:i') }}</td>
                            <td class="px-4 py-3">{{ $log->admin?->name ?? 'system' }}</td>
                            <td class="px-4 py-3">{{ $log->action }}</td>
                            <td class="px-4 py-3">{{ $log->target_type }} #{{ $log->target_id }}</td>
                            <td class="px-4 py-3 text-[var(--text-muted)]">{{ json_encode($log->old_value) }}</td>
                            <td class="px-4 py-3">{{ json_encode($log->new_value) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-8 text-center text-[var(--text-muted)]">No entries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-[var(--line)]">
            {{ $logs->links() }}
        </div>
    </div>
</x-filament-panels::page>
