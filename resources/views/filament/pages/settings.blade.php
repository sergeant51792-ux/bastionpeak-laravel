<x-filament-panels::page>
    <div class="space-y-6">
        <div class="inline-flex rounded-lg bg-[var(--line-soft)] p-1">
            <a href="{{ route('admin.settings.index', ['tab' => 'general']) }}"
               @class(['px-4 py-2 rounded-md text-sm font-medium', 'bg-[var(--surface)] shadow-sm' => $tab === 'general', 'text-[var(--text-muted)]' => $tab !== 'general'])>
                General
            </a>
            <a href="{{ route('admin.settings.index', ['tab' => 'security']) }}"
               @class(['px-4 py-2 rounded-md text-sm font-medium', 'bg-[var(--surface)] shadow-sm' => $tab === 'security', 'text-[var(--text-muted)]' => $tab !== 'security'])>
                Security
            </a>
            <a href="{{ route('admin.settings.index', ['tab' => 'transactions']) }}"
               @class(['px-4 py-2 rounded-md text-sm font-medium', 'bg-[var(--surface)] shadow-sm' => $tab === 'transactions', 'text-[var(--text-muted)]' => $tab !== 'transactions'])>
                Transactions
            </a>
            <a href="{{ route('admin.settings.index', ['tab' => 'currencies']) }}"
               @class(['px-4 py-2 rounded-md text-sm font-medium', 'bg-[var(--surface)] shadow-sm' => $tab === 'currencies', 'text-[var(--text-muted)]' => $tab !== 'currencies'])>
                Currencies
            </a>
        </div>

        <div class="bg-[var(--surface)] rounded-lg border border-[var(--line)] p-6">
            <h2 class="text-lg font-semibold text-[var(--text)] mb-4 capitalize">{{ $tab }} settings</h2>
            <p class="text-[var(--text-muted)]">Settings for the {{ $tab }} group. Production implementation uses SystemSetting model with per-field forms.</p>
            <div class="mt-6 p-4 bg-[var(--surface-raised)] rounded-lg">
                <p class="text-sm text-[var(--text-muted)]">Saving shows a diff dialog before applying, and each save is written to the audit log.</p>
            </div>
        </div>
    </div>
</x-filament-panels::page>
