@extends('layouts.admin')

@section('title', 'Approvals')
@section('content')
<div class="h-[calc(100vh-120px)] flex flex-col">
    <div class="flex items-center gap-4 mb-4">
        <div class="inline-flex rounded-lg bg-[var(--surface-raised)] p-1">
            <a href="{{ route('admin.approvals', ['tab' => 'deposits']) }}" @class(['px-4 py-2 rounded-md text-sm font-medium transition-colors', 'bg-[var(--surface)] shadow-sm' => $tab === 'deposits', 'text-[var(--text-muted)]' => $tab !== 'deposits'])>
                Deposits {{ \App\Models\Transaction::where('type', 'deposit_credit')->whereIn('status', ['pending_review', 'pending_match'])->count() }}
            </a>
            <a href="{{ route('admin.approvals', ['tab' => 'payments']) }}" @class(['px-4 py-2 rounded-md text-sm font-medium transition-colors', 'bg-[var(--surface)] shadow-sm' => $tab === 'payments', 'text-[var(--text-muted)]' => $tab !== 'payments'])>
                Payments {{ \App\Models\Transaction::where('type', 'payment_debit')->where('status', 'pending_review')->count() }}
            </a>
        </div>
        <div id="bulk-actions" class="hidden items-center gap-2">
            <button type="button" onclick="bulkApprove()" class="py-1.5 px-3 bg-[var(--success)] text-white rounded-lg text-sm font-medium hover:opacity-90 disabled:opacity-50 min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Approve selected">
                <x-icon name="check" class="w-4 h-4" /> Approve
            </button>
            <button type="button" onclick="bulkDelete()" class="py-1.5 px-3 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 disabled:opacity-50 min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Delete selected">
                <x-icon name="trash-2" class="w-4 h-4" /> Delete
            </button>
            <button type="button" onclick="clearSelection()" class="py-1.5 px-3 text-[var(--text-muted)] hover:bg-[var(--surface-raised)] rounded-lg text-sm min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Clear">
                <x-icon name="x" class="w-4 h-4" />
            </button>
        </div>
    </div>

    @push('scripts')
    <script>
    function getSelectedIds() {
        return Array.from(document.querySelectorAll('.approval-checkbox:checked')).map(cb => cb.value);
    }
    function updateBulkActionsVisibility() {
        var bulk = document.getElementById('bulk-actions');
        var selected = getSelectedIds();
        bulk.classList.toggle('hidden', selected.length === 0);
        bulk.querySelectorAll('button').forEach(b => b.disabled = selected.length === 0);
    }
    function clearSelection() {
        document.querySelectorAll('.approval-checkbox:checked').forEach(cb => cb.checked = false);
        updateBulkActionsVisibility();
    }
    function bulkApprove() {
        var ids = getSelectedIds();
        if (!ids.length) return;
        if (!confirm('Approve ' + ids.length + ' selected requests?')) return;
        submitBulk('{{ route('admin.approvals.batch') }}', { ids: ids, type: '{{ $tab }}' });
    }
    function bulkDelete() {
        var ids = getSelectedIds();
        if (!ids.length) return;
        if (!confirm('Delete ' + ids.length + ' selected requests? This action cannot be undone.')) return;
        submitBulk('{{ route('admin.approvals.batch-delete') }}', { ids: ids });
    }
    function submitBulk(url, data) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        var csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = document.querySelector('meta[name="csrf-token"]').content;
        form.appendChild(csrf);
        var idsInput = document.createElement('input');
        idsInput.type = 'hidden';
        idsInput.name = 'ids[]';
        idsInput.value = data.ids.join(',');
        form.appendChild(idsInput);
        if (data.type) {
            var typeInput = document.createElement('input');
            typeInput.type = 'hidden';
            typeInput.name = 'type';
            typeInput.value = data.type;
            form.appendChild(typeInput);
        }
        document.body.appendChild(form);
        form.submit();
    }
    document.addEventListener('change', updateBulkActionsVisibility);
    document.addEventListener('DOMContentLoaded', updateBulkActionsVisibility);
    </script>
    @endpush

    <div class="flex-1 grid grid-cols-1 lg:grid-cols-5 gap-4 min-h-0">
        <!-- Queue -->
        <div class="lg:col-span-2 bg-[var(--surface)] border border-[var(--line)] rounded-2xl overflow-hidden flex flex-col">
            <div class="p-4 border-b border-[var(--line)]">
                <h2 class="font-semibold">Queue</h2>
            </div>
                <div class="flex-1 overflow-y-auto">
                @forelse($queue as $item)
                    @php
                        $isChecked = request('open') == $item->id;
                    @endphp
                     <div class="relative">
                         <input type="checkbox" class="approval-checkbox absolute top-4 left-4 z-10 h-4 w-4 rounded border-gray-300 text-[var(--accent)] focus:ring-[var(--accent)]" value="{{ $item->id }}" @if($isChecked) checked @endif>
                         <a href="{{ route('admin.approvals', array_merge(request()->query(), ['open' => $item->id])) }}" @class(['block p-4 border-b border-[var(--line)] hover:bg-[var(--surface-raised)] transition-colors', 'bg-[var(--success)]/5' => $openTransaction?->id === $item->id])>
                             <div class="flex items-center justify-between ml-6">
                                 <div>
                                     <p class="font-medium text-sm">{{ $item->fromUser?->name ?? 'Unknown' }}</p>
                                     <p class="text-xs text-[var(--text-muted)]">{{ $item->type instanceof \App\Enums\TransactionType ? $item->type->label() : ($item->type ?? '—') }}</p>
                                 </div>
                                 <div class="text-right">
                                      <p class="font-semibold text-sm">{{ number_format((float) $item->amount, $item->currency->decimals ?? 2) }} {{ $item->currency->symbol ?? '' }}</p>
                                     <p class="text-xs text-[var(--text-muted)]">{{ $item->created_at->diffForHumans() }}</p>
                                 </div>
                             </div>
                         </a>
                     </div>
                @empty
                    <div class="p-8 text-center text-[var(--text-muted)]">No pending items.</div>
                @endforelse
            </div>
        </div>

        <!-- Review panel -->
        <div class="lg:col-span-3 bg-[var(--surface)] border border-[var(--line)] rounded-2xl overflow-hidden flex flex-col">
            @if($openTransaction)
                <div class="p-6 border-b border-[var(--line)]">
                    <div class="flex items-start justify-between">
                        <div>
                            <h2 class="text-lg font-semibold">{{ $openTransaction->type instanceof \App\Enums\TransactionType ? $openTransaction->type->label() : ($openTransaction->type ?? '—') }}</h2>
                            <p class="text-2xl font-semibold mt-1">{{ number_format((float) $openTransaction->amount, $openTransaction->currency->decimals ?? 2) }} {{ $openTransaction->currency->symbol ?? '' }}</p>
                            <p class="text-sm text-[var(--text-muted)]">{{ $openTransaction->fromAccount?->name ?? 'External' }} to {{ $openTransaction->toAccount?->name ?? $openTransaction->toUser?->name ?? 'External' }}</p>
                        </div>
                        <span @class(['px-3 py-1 rounded-full text-sm font-medium', 'bg-yellow-100 text-yellow-800' => $openTransaction->status === \App\Enums\TransactionStatus::PendingReview, 'bg-blue-100 text-blue-800' => $openTransaction->status === \App\Enums\TransactionStatus::PendingMatch])>
                            {{ $openTransaction->status instanceof \App\Enums\TransactionStatus ? $openTransaction->status->label() : ($openTransaction->status ?? '—') }}
                        </span>
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto p-6 space-y-6">
                    @if($openTransaction->type === \App\Enums\TransactionType::DepositCredit && $openTransaction->toAccount)
                        @php
                            $account = $openTransaction->toAccount;
                            $depositMethodSlug = $openTransaction->metadata['deposit_method_slug'] ?? null;
                            $configuredMethod = null;
                            if ($depositMethodSlug) {
                                $configuredMethod = $account->depositMethods()
                                    ->wherePivot('is_active', true)
                                    ->where('code', $depositMethodSlug)
                                    ->first();
                            }
                        @endphp
                        @if($configuredMethod)
                            <div class="p-4 bg-[var(--surface-raised)] rounded-xl">
                                <h3 class="text-sm font-medium text-[var(--text-muted)] mb-2">Deposit details (admin-configured)</h3>
                                <div class="space-y-3">
                                    <div>
                                        <span class="text-xs text-[var(--text-muted)]">Method:</span>
                                        <span class="font-medium text-[var(--text)]">{{ $configuredMethod->name }}</span>
                                    </div>
                                    @if($configuredMethod->pivot->address)
                                        <div>
                                            <span class="text-xs text-[var(--text-muted)]">Address:</span>
                                            <code class="text-xs font-mono text-[var(--text)] bg-white px-2 py-1 rounded break-all block">{{ $configuredMethod->pivot->address }}</code>
                                        </div>
                                    @endif
                                    @if($configuredMethod->pivot->qr_path)
                                        <div>
                                            <span class="text-xs text-[var(--text-muted)]">QR code:</span>
                                            <img src="{{ asset('storage/' . $configuredMethod->pivot->qr_path) }}" alt="Deposit QR" class="w-24 h-24 object-contain border border-[var(--line)] rounded-lg mt-1">
                                        </div>
                                    @endif
                                    @if($configuredMethod->pivot->instructions)
                                        <div>
                                            <span class="text-xs text-[var(--text-muted)]">Instructions:</span>
                                            <p class="text-sm text-[var(--text)] whitespace-pre-line">{{ $configuredMethod->pivot->instructions }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endif
                    <div class="p-4 bg-[var(--surface-raised)] rounded-xl">
                        <h3 class="text-sm font-medium text-[var(--text-muted)] mb-2">Customer note</h3>
                        <p class="text-sm">{{ $openTransaction->memo ?? 'No note provided.' }}</p>
                    </div>
                    @if($openTransaction->metadata)
                        <div class="p-4 bg-[var(--surface-raised)] rounded-xl">
                            <h3 class="text-sm font-medium text-[var(--text-muted)] mb-2">Transfer details</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                @foreach($openTransaction->metadata as $key => $value)
                                    @if($value)
                                        <div>
                                            <span class="text-[var(--text-muted)]">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                            <span class="font-medium text-[var(--text)]">{{ is_array($value) ? implode(', ', array_filter($value)) : $value }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                    @if($openTransaction->proof_file_path)
                        <div class="p-4 bg-[var(--surface-raised)] rounded-xl">
                            <h3 class="text-sm font-medium text-[var(--text-muted)] mb-2">Supporting document</h3>
                            @php
                                $proof = $openTransaction->proof_file_path;
                                $extension = strtolower(pathinfo($proof, PATHINFO_EXTENSION));
                            @endphp
                            @if(in_array($extension, ['jpg', 'jpeg', 'png']))
                                <img src="{{ asset('storage/' . $proof) }}" alt="Supporting document" class="max-w-full max-h-80 rounded-lg border border-[var(--line)]">
                            @else
                                <a href="{{ asset('storage/' . $proof) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-[var(--surface)] border border-[var(--line)] rounded-lg text-sm font-medium hover:border-[var(--accent)] transition-all">
                                    <x-icon name="file-text" class="w-4 h-4" />
                                    View document
                                </a>
                            @endif
                        </div>
                    @endif
                    <div class="p-4 bg-[var(--surface-raised)] rounded-xl">
                        <h3 class="text-sm font-medium text-[var(--text-muted)] mb-2">History</h3>
                        <p class="text-sm">Submitted {{ $openTransaction->created_at->format('d M Y, H:i') }}</p>
                        @if($openTransaction->reviewed_at)
                            <p class="text-sm">Reviewed {{ $openTransaction->reviewed_at->format('d M Y, H:i') }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-3 pt-4">
                        <form method="POST" action="{{ route('admin.approvals.reject', $openTransaction) }}" class="flex-1">
                            @csrf
                            <input type="hidden" name="reason" value="Rejected during review">
                            <button type="submit" class="w-full py-2.5 bg-red-600 text-white rounded-xl text-sm font-medium hover:bg-red-700 transition-colors">Reject</button>
                        </form>
                        <form method="POST" action="{{ route('admin.approvals.approve', $openTransaction) }}" class="flex-1">
                            @csrf
                                <button type="submit" class="w-full py-2.5 bg-[var(--success)] text-white rounded-xl text-sm font-medium hover:opacity-90 transition-opacity">
                                    @if($openTransaction->type === \App\Enums\TransactionType::DepositCredit)
                                        Approve and credit {{ number_format((float) $openTransaction->amount, $openTransaction->currency->decimals ?? 2) }} {{ $openTransaction->currency->symbol ?? '' }}
                                    @else
                                        Approve {{ number_format((float) $openTransaction->amount, $openTransaction->currency->decimals ?? 2) }} {{ $openTransaction->currency->symbol ?? '' }}
                                    @endif
                                </button>
                        </form>
                    </div>
                </div>
            @else
                    <div class="flex items-center justify-center h-full text-[var(--text-muted)]">
                        Select an item to review.
                    </div>
            @endif
        </div>
    </div>
</div>
@endsection
