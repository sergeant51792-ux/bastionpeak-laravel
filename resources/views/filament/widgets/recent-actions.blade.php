<div class="space-y-3">
    @forelse(\App\Models\AuditLog::orderByDesc('created_at')->limit(5)->get() as $action)
        <div class="flex items-center justify-between p-3 bg-[var(--line-soft)] rounded">
            <div>
                <p class="text-sm font-medium text-[var(--text)]">{{ $action->action }}</p>
                <p class="text-xs text-[var(--text-muted)]">{{ $action->target_type }} #{{ $action->target_id }}</p>
            </div>
            <span class="text-xs text-[var(--text-muted)]">{{ $action->created_at->diffForHumans() }}</span>
        </div>
    @empty
        <p class="text-[var(--text-muted)] text-sm">No recent actions.</p>
    @endforelse
</div>
