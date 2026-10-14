<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach(\App\Models\Account::with('user')->get()->take(6) as $account)
                <div class="p-4 bg-[var(--surface)] rounded-lg border border-[var(--line)]">
                    <p class="text-sm text-[var(--text-muted)]">{{ $account->name }}</p>
                    <p class="font-semibold text-[var(--text)]">{{ $account->user->name }}</p>
                    <p class="text-lg mt-1 text-[var(--text)]">{{ number_format((float) $account->balance, $account->currency->decimals ?? 2) }} {{ $account->currency->symbol }}</p>
                    <span class="inline-block mt-2 px-2 py-1 rounded text-xs font-medium bg-[var(--success-bg)] text-[var(--success)]">
                        {{ $account->status }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
