@extends('layouts.customer')

@section('title', 'Account statements')
@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-[var(--text)] tracking-tight">Account statements</h1>

    <form method="POST" action="{{ route('statements.generate') }}" class="space-y-5">
        @csrf
            <div>
                <label for="account_id" class="block text-sm font-medium text-[var(--text)] mb-1.5">Account</label>
                <x-custom-select
                    name="account_id"
                    :options="$accounts->mapWithKeys(fn($a) => [$a->id => $a->name . ' (' . $a->currency->symbol . ')' ])"
                    placeholder="Select account"
                    required
                />
            </div>

        <div class="grid grid-cols-2 gap-3">
            <div>
                <label for="from_date" class="block text-sm font-semibold text-[var(--text)] mb-1.5">From</label>
                <input type="date" id="from_date" name="from_date" required class="input-field text-sm">
            </div>
            <div>
                <label for="to_date" class="block text-sm font-semibold text-[var(--text)] mb-1.5">To</label>
                <input type="date" id="to_date" name="to_date" required class="input-field text-sm">
            </div>
        </div>

        <div>
            <label class="block text-sm font-semibold text-[var(--text)] mb-2">Format</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="flex items-center justify-center gap-2 p-3.5 bg-white border border-[var(--line)] rounded-xl cursor-pointer hover:border-[var(--accent)] transition-all has-[:checked]:border-[var(--accent)] has-[:checked]:bg-[var(--accent-subtle)]">
                    <input type="radio" name="format" value="pdf" checked class="w-4 h-4 text-[var(--accent)]">
                    <span class="text-sm font-medium text-[var(--text)]">PDF</span>
                </label>
                <label class="flex items-center justify-center gap-2 p-3.5 bg-white border border-[var(--line)] rounded-xl cursor-pointer hover:border-[var(--accent)] transition-all has-[:checked]:border-[var(--accent)] has-[:checked]:bg-[var(--accent-subtle)]">
                    <input type="radio" name="format" value="csv" class="w-4 h-4 text-[var(--accent)]">
                    <span class="text-sm font-medium text-[var(--text)]">CSV</span>
                </label>
            </div>
        </div>

        <button type="submit" class="w-full py-3.5 btn-primary">Generate statement</button>
    </form>

    @if(session('status'))
        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700 font-medium">
            {{ session('status') }}
        </div>
    @endif

    <section>
        <h2 class="section-title">Recent statements</h2>
        <div class="space-y-2">
            @forelse($history->take(5) as $stmt)
                <div class="flex items-center justify-between p-4 bg-white border border-[var(--line)] rounded-xl hover:border-[var(--accent)] transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-[var(--line-soft)] flex items-center justify-center text-[var(--text-muted)]">
                            <x-icon name="file-text"  class="w-4 h-4" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-[var(--text)]">{{ $stmt->type->label() }}</p>
                            <p class="text-xs text-[var(--text-muted)]">{{ $stmt->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                    <button class="text-sm font-medium text-[var(--accent)] hover:text-[var(--accent-dark)] transition-colors">Download</button>
                </div>
            @empty
                <div class="empty-state bg-white border border-[var(--line)] rounded-2xl">
                    <p class="text-sm text-[var(--text-muted)]">No statements yet.</p>
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
