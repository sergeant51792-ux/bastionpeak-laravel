@props([
    'columns' => [],
    'rows' => [],
    'page' => 1,
    'perPage' => 15,
    'total' => 0,
])

<div class="w-full overflow-hidden rounded-2xl border border-slate-800 bg-slate-900">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-slate-800/50 text-slate-400 uppercase text-xs sticky top-0 z-10">
                <tr>
                    @foreach($columns as $column)
                        <th scope="col" class="px-6 py-3 font-medium">{{ $column['label'] ?? $column }}</th>
                    @endforeach
                    <th scope="col" class="px-6 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800">
                @forelse($rows as $row)
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        @foreach($columns as $column)
                            <td class="px-6 py-4 text-slate-300">{{ data_get($row, $column['key'] ?? $column, '-') }}</td>
                        @endforeach
                        <td class="px-6 py-4 text-right">
                            {{ $actions ?? '' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ count($columns) + 1 }}" class="px-6 py-8 text-center text-slate-500">No records found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($total > $perPage)
        <div class="flex items-center justify-between px-6 py-4 border-t border-slate-800">
            <p class="text-sm text-slate-500">Showing {{ ($page - 1) * $perPage + 1 }} to {{ min($page * $perPage, $total) }} of {{ $total }} results</p>
            <div class="flex items-center gap-2">
                <button class="px-3 py-1.5 text-sm rounded-lg bg-slate-800 border border-slate-700 text-slate-300 hover:bg-slate-700 disabled:opacity-50 transition-colors">Previous</button>
                <button class="px-3 py-1.5 text-sm rounded-lg bg-slate-800 border border-slate-700 text-slate-300 hover:bg-slate-700 disabled:opacity-50 transition-colors">Next</button>
            </div>
        </div>
    @endif
</div>
