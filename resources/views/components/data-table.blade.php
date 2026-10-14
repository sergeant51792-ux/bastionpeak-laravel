@props([
    'columns' => [],
    'rows' => [],
    'page' => 1,
    'perPage' => 15,
    'total' => 0,
])

<div class="w-full overflow-hidden rounded-2xl border border-[var(--line)] bg-[var(--surface)]">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-[var(--line-soft)] text-[var(--text-muted)] uppercase text-xs sticky top-0 z-10">
                <tr>
                    @foreach($columns as $column)
                        <th scope="col" class="px-6 py-3 font-medium">{{ $column['label'] ?? $column }}</th>
                    @endforeach
                    <th scope="col" class="px-6 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-[var(--line)]">
                @forelse($rows as $row)
                    <tr class="hover:bg-[var(--line-soft)] transition-colors">
                        @foreach($columns as $column)
                            <td class="px-6 py-4 text-[var(--text-muted)]">{{ data_get($row, $column['key'] ?? $column, '-') }}</td>
                        @endforeach
                        <td class="px-6 py-4 text-right">
                            {{ $actions ?? '' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + 1 }}" class="px-6 py-8 text-center text-[var(--text-muted)]">No records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($total > $perPage)
        <div class="flex items-center justify-between px-6 py-4 border-t border-[var(--line)]">
            <p class="text-sm text-[var(--text-muted)]">Showing {{ ($page - 1) * $perPage + 1 }} to {{ min($page * $perPage, $total) }} of {{ $total }} results</p>
            <div class="flex items-center gap-2">
                <button class="px-3 py-1.5 text-sm rounded-lg bg-[var(--surface-raised)] border border-[var(--line)] text-[var(--text-muted)] hover:bg-[var(--line-soft)] disabled:opacity-50 transition-colors">Previous</button>
                <button class="px-3 py-1.5 text-sm rounded-lg bg-[var(--surface-raised)] border border-[var(--line)] text-[var(--text-muted)] hover:bg-[var(--line-soft)] disabled:opacity-50 transition-colors">Next</button>
            </div>
        </div>
    @endif
</div>
