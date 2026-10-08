<x-filament::page>
    <div class="space-y-6">
        <div class="inline-flex rounded-lg bg-gray-100 dark:bg-gray-800 p-1">
            <a href="{{ route('admin.reports.index', ['tab' => 'financial']) }}"
               @class(['px-4 py-2 rounded-md text-sm font-medium', 'bg-white dark:bg-gray-700 shadow-sm' => $tab === 'financial', 'text-gray-600 dark:text-gray-300' => $tab !== 'financial'])>
                Financial
            </a>
            <a href="{{ route('admin.reports.index', ['tab' => 'audit']) }}"
               @class(['px-4 py-2 rounded-md text-sm font-medium', 'bg-white dark:bg-gray-700 shadow-sm' => $tab === 'audit', 'text-gray-600 dark:text-gray-300' => $tab !== 'audit'])>
                Audit
            </a>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-lg font-semibold">{{ $tab === 'financial' ? 'Financial reports' : 'Audit reports' }}</h2>
                <div class="flex gap-2">
                    <button class="px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                        Export CSV
                    </button>
                    <button class="px-4 py-2 bg-gray-100 dark:bg-gray-800 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                        Export PDF
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <p class="text-sm text-gray-500">System balance</p>
                    <p class="text-2xl font-semibold">{{ number_format($systemBalance['total'] ?? 0, 2) }}</p>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <p class="text-sm text-gray-500">Volume ({{ $from }} to {{ $to }})</p>
                    <p class="text-2xl font-semibold">{{ $volume }}</p>
                </div>
                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                    <p class="text-sm text-gray-500">In vs Out</p>
                    <p class="text-lg">In: {{ number_format($volumeIn, 2) }} / Out: {{ number_format($volumeOut, 2) }}</p>
                </div>
            </div>
        </div>
    </div>
</x-filament::page>
