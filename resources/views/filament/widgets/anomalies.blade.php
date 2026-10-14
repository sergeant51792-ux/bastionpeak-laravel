<div class="space-y-3">
    @forelse(\App\Services\BalanceCheckService::detectAnomalies() ?? [] as $anomaly)
        <div class="flex items-start gap-2 p-3 bg-[var(--warning-bg)] rounded border border-[var(--warning)]/30">
            <x-icon name="alert-triangle" class="w-5 h-5 text-[var(--warning)] mt-0.5" />
            <span class="text-sm text-[var(--text)]">{{ $anomaly['message'] ?? 'Anomaly detected' }}</span>
        </div>
    @empty
        <p class="text-[var(--text-muted)] text-sm">No anomalies detected.</p>
    @endforelse
</div>
