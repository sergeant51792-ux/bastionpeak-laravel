<div class="space-y-3">
    @forelse(\App\Models\AuditLog::orderByDesc('created_at')->limit(5)->get() as $action)
        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded">
            <div>
                <p class="text-sm font-medium">{{ $action->action }}</p>
                <p class="text-xs text-gray-500">{{ $action->target_type }} #{{ $action->target_id }}</p>
            </div>
            <span class="text-xs text-gray-400">{{ $action->created_at->diffForHumans() }}</span>
        </div>
    @empty
        <p class="text-gray-500 text-sm">No recent actions.</p>
    @endforelse
</div>
