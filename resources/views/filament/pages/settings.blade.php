<x-filament::page>
    <div class="space-y-6">
        <div class="inline-flex rounded-lg bg-gray-100 dark:bg-gray-800 p-1">
            <a href="{{ route('admin.settings.index', ['tab' => 'general']) }}"
               @class(['px-4 py-2 rounded-md text-sm font-medium', 'bg-white dark:bg-gray-700 shadow-sm' => $tab === 'general', 'text-gray-600 dark:text-gray-300' => $tab !== 'general'])>
                General
            </a>
            <a href="{{ route('admin.settings.index', ['tab' => 'security']) }}"
               @class(['px-4 py-2 rounded-md text-sm font-medium', 'bg-white dark:bg-gray-700 shadow-sm' => $tab === 'security', 'text-gray-600 dark:text-gray-300' => $tab !== 'security'])>
                Security
            </a>
            <a href="{{ route('admin.settings.index', ['tab' => 'transactions']) }}"
               @class(['px-4 py-2 rounded-md text-sm font-medium', 'bg-white dark:bg-gray-700 shadow-sm' => $tab === 'transactions', 'text-gray-600 dark:text-gray-300' => $tab !== 'transactions'])>
                Transactions
            </a>
            <a href="{{ route('admin.settings.index', ['tab' => 'currencies']) }}"
               @class(['px-4 py-2 rounded-md text-sm font-medium', 'bg-white dark:bg-gray-700 shadow-sm' => $tab === 'currencies', 'text-gray-600 dark:text-gray-300' => $tab !== 'currencies'])>
                Currencies
            </a>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
            <h2 class="text-lg font-semibold mb-4 capitalize">{{ $tab }} settings</h2>
            <p class="text-gray-500">Settings for the {{ $tab }} group. Production implementation uses SystemSetting model with per-field forms.</p>
            <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                <p class="text-sm text-gray-500">Saving shows a diff dialog before applying, and each save is written to the audit log.</p>
            </div>
        </div>
    </div>
</x-filament::page>
