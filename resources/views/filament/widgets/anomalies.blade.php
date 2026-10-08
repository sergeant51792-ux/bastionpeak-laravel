<div class="space-y-3">
    @forelse(\App\Services\BalanceCheckService::detectAnomalies() ?? [] as $anomaly)
        <div class="flex items-start gap-2 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded border border-yellow-200 dark:border-yellow-700">
            <x-icon name="alert-triangle" class="w-5 h-5 text-yellow-600 mt-0.5" />
            <span class="text-sm">{{ $anomaly['message'] ?? 'Anomaly detected' }}</span>
        </div>
    @empty
        <p class="text-gray-500 text-sm">No anomalies detected.</p>
    @endforelse
</div>
