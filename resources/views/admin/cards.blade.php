@extends('layouts.admin')

@section('title', 'Cards')
@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
            <h2 class="text-lg font-semibold">All cards</h2>
            <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                <input type="search" placeholder="Search cards..." class="px-4 py-2.5 bg-[var(--surface)] border border-[var(--line)] rounded-xl text-sm w-full sm:w-64 min-h-[44px]">
                <select id="bulk-action" class="px-3 py-2 bg-[var(--surface)] border border-[var(--line)] rounded-lg text-sm min-h-[44px]">
                    <option>Bulk actions</option>
                    <option value="freeze">Freeze selected</option>
                    <option value="unfreeze">Unfreeze selected</option>
                    <option value="block">Block selected</option>
                    <option value="cancel">Cancel selected</option>
                    <option value="delete" class="text-red-600">Delete selected</option>
                </select>
                <button onclick="runBulkAction()" class="px-4 py-2.5 bg-[var(--primary)] text-white rounded-lg text-xs font-bold hover:opacity-90 transition-opacity min-h-[44px] min-w-[44px]">Apply</button>
            </div>
        </div>

        <form id="bulk-form" method="POST" action="{{ route('admin.cards.bulk') }}">
            @csrf
            <input type="hidden" name="action" id="bulk-action-input">
            <div class="hidden" id="selected-ids"></div>
        </form>

        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl overflow-x-auto -mx-4 lg:mx-0">
            <table class="w-full text-sm min-w-[640px]">
                <thead class="bg-[var(--surface-raised)]">
                    <tr>
                        <th class="px-4 py-3 text-left"><input type="checkbox" id="select-all" class="min-h-[20px] min-w-[20px]"></th>
                        <th class="px-4 py-3 text-left">Card</th>
                        <th class="px-4 py-3 text-left">User</th>
                        <th class="px-4 py-3 text-left">Account</th>
                        <th class="px-4 py-3 text-left">Type</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--line)]">
                    @forelse($cards as $card)
                    <tr class="hover:bg-[var(--surface-raised)]/50" data-card-id="{{ $card->id }}">
                        <td class="px-4 py-3"><input type="checkbox" class="card-checkbox min-h-[20px] min-w-[20px]" value="{{ $card->id }}"></td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.cards.show', $card) }}" class="font-medium text-[var(--accent)] hover:text-[var(--accent-dark)] transition-colors">
                                **** **** **** {{ $card->card_number_masked ? substr($card->card_number_masked, -4) : '0000' }}
                            </a>
                        </td>
                        <td class="px-4 py-3">{{ $card->user->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ $card->account->name ?? '—' }}</td>
                        <td class="px-4 py-3">{{ ucfirst($card->card_type ?? '—') }}</td>
                        <td class="px-4 py-3">
                            <span @class(['px-2.5 py-1 rounded-full text-xs font-medium', 'bg-green-100 text-green-800' => $card->status === 'active', 'bg-blue-100 text-blue-800' => $card->status === 'frozen', 'bg-red-100 text-red-800' => in_array($card->status, ['blocked','cancelled']), 'bg-gray-100 text-gray-800' => in_array($card->status, ['expired','closed','deactivated'])])>
                                {{ ucfirst($card->status ?? 'unknown') }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex gap-1 justify-end">
                                @if($card->status === 'active')
                                    <form method="POST" action="{{ route('admin.cards.freeze', $card) }}" class="inline" onsubmit="return confirm('Freeze this card?')">
                                        @csrf
                                        <button type="submit" class="p-2 text-blue-600 hover:bg-blue-50 rounded min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Freeze">
                                            <x-icon name="pause" class="w-5 h-5" />
                                        </button>
                                    </form>
                                @elseif($card->status === 'frozen')
                                    <form method="POST" action="{{ route('admin.cards.unfreeze', $card) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="p-2 text-green-600 hover:bg-green-50 rounded min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Unfreeze">
                                            <x-icon name="play" class="w-5 h-5" />
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.cards.block', $card) }}" class="inline" onsubmit="return confirm('Block this card?')">
                                        @csrf
                                        <input type="hidden" name="reason" value="Blocked by admin">
                                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Block">
                                            <x-icon name="lock" class="w-5 h-5" />
                                        </button>
                                    </form>
                                @else
                                    <span class="px-3 py-1 text-xs text-[var(--text-muted)]">No actions available</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-[var(--text-muted)]">No cards found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-[var(--text-muted)]">
            <span>Showing {{ $cards->firstItem() ?? 0 }}–{{ $cards->lastItem() ?? 0 }} of {{ $cards->total() }}</span>
            <div class="flex gap-2">
                @if($cards->previousPageUrl())
                    <a href="{{ $cards->previousPageUrl() }}" class="px-4 py-2.5 bg-[var(--surface)] border border-[var(--line)] rounded-lg text-xs font-bold text-[var(--text-muted)] hover:text-[var(--text)] hover:border-[var(--accent)] transition-all min-h-[44px] min-w-[44px] flex items-center justify-center">Previous</a>
                @endif
                @if($cards->nextPageUrl())
                    <a href="{{ $cards->nextPageUrl() }}" class="px-4 py-2.5 bg-[var(--surface)] border border-[var(--line)] rounded-lg text-xs font-bold text-[var(--text-muted)] hover:text-[var(--text)] hover:border-[var(--accent)] transition-all min-h-[44px] min-w-[44px] flex items-center justify-center">Next</a>
                @endif
            </div>
        </div>
    </div>

    <script>
    document.getElementById('select-all').addEventListener('change', function(e) {
        document.querySelectorAll('.card-checkbox').forEach(cb => cb.checked = e.target.checked);
    });

    function runBulkAction() {
        const action = document.getElementById('bulk-action').value;
        const ids = Array.from(document.querySelectorAll('.card-checkbox:checked')).map(cb => cb.value);
        if (!action || !ids.length) return;
        if (action === 'delete' && !confirm('Delete ' + ids.length + ' selected cards? This action cannot be undone.')) return;
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('admin.cards.bulk') }}';
        form.innerHTML = '@csrf<input type="hidden" name="action" value="' + action + '">' +
            ids.map(id => '<input type="hidden" name="ids[]" value="' + id + '">').join('');
        document.body.appendChild(form);
        form.submit();
    }
    </script>
</div>
@endsection
