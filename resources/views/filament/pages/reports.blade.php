<x-filament-panels::page>
    <div class="space-y-6">
        <div class="inline-flex rounded-lg bg-[var(--line-soft)] p-1">
            <a href="{{ route('admin.reports.index', ['tab' => 'financial']) }}"
               @class(['px-4 py-2 rounded-md text-sm font-medium', 'bg-[var(--surface)] shadow-sm' => $tab === 'financial', 'text-[var(--text-muted)]' => $tab !== 'financial'])>
                Financial
            </a>
            <a href="{{ route('admin.reports.index', ['tab' => 'audit']) }}"
               @class(['px-4 py-2 rounded-md text-sm font-medium', 'bg-[var(--surface)] shadow-sm' => $tab === 'audit', 'text-[var(--text-muted)]' => $tab !== 'audit'])>
                Audit
            </a>
        </div>

        <div class="bg-[var(--surface)] rounded-lg border border-[var(--line)] p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold text-[var(--text)]">{{ $tab === 'financial' ? 'Financial reports' : 'Audit reports' }}</h2>
                <div class="flex gap-2">
                    <button class="px-4 py-2 bg-[var(--surface-raised)] rounded-lg text-sm font-medium text-[var(--text)] hover:bg-[var(--line-soft)] transition-colors">
                        Export CSV
                    </button>
                    <button class="px-4 py-2 bg-[var(--surface-raised)] rounded-lg text-sm font-medium text-[var(--text)] hover:bg-[var(--line-soft)] transition-colors">
                        Export PDF
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-[var(--surface-raised)] rounded-lg">
                    <p class="text-sm text-[var(--text-muted)]">System balance</p>
                    <p class="text-2xl font-semibold text-[var(--text)]">{{ number_format($systemBalance['total'] ?? 0, 2) }}</p>
                </div>
                <div class="p-4 bg-[var(--surface-raised)] rounded-lg">
                    <p class="text-sm text-[var(--text-muted)]">Volume ({{ $from }} to {{ $to }})</p>
                    <p class="text-2xl font-semibold text-[var(--text)]">{{ $volume }}</p>
                </div>
                <div class="p-4 bg-[var(--surface-raised)] rounded-lg">
                    <p class="text-sm text-[var(--text-muted)]">In vs Out</p>
                    <p class="text-lg text-[var(--text)]">In: {{ number_format($volumeIn, 2) }} / Out: {{ number_format($volumeOut, 2) }}</p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
