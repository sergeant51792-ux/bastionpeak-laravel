<x-filament::layouts.app>
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach(\App\Models\Account::with('user')->get()->take(6) as $account)
                <div class="p-4 bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-gray-500">{{ $account->name }}</p>
                    <p class="font-semibold">{{ $account->user->name }}</p>
                    <p class="text-lg mt-1">{{ number_format((float) $account->balance, $account->currency->decimals ?? 2) }} {{ $account->currency->symbol }}</p>
                    <span class="inline-block mt-2 px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                        {{ $account->status }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>
</x-filament::layouts.app>
