@extends('layouts.admin')

@section('title', 'Applications')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-[var(--text)] tracking-tight">{{ $service->name }} Applications</h1>
        <a href="{{ route('admin.financial-services.index') }}" class="text-sm text-[var(--accent)]">← Back to services</a>
    </div>

    @if(session('status'))
        <div class="p-4 bg-[var(--success-bg)] border border-[var(--success)]/30 rounded-xl text-sm text-[var(--success)]">
            {{ session('status') }}
        </div>
    @endif

    @if($applications->isEmpty())
        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-8 text-center">
            <p class="text-[var(--text-muted)]">No applications yet.</p>
        </div>
    @else
        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-[var(--line)]">
                        <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--text-muted)]">User</th>
                        <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--text-muted)]">Amount</th>
                        <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--text-muted)]">Purpose</th>
                        <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--text-muted)]">Date</th>
                        <th class="text-left py-3 px-4 text-xs font-semibold text-[var(--text-muted)]">Status</th>
                        <th class="text-right py-3 px-4 text-xs font-semibold text-[var(--text-muted)]">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($applications as $app)
                        <tr class="border-b border-[var(--line)]">
                            <td class="py-3 px-4">
                                <p class="font-medium text-[var(--text)]">{{ $app->user->name }}</p>
                                <p class="text-xs text-[var(--text-muted)]">{{ $app->user->email }}</p>
                            </td>
                            <td class="py-3 px-4 text-sm">{{ $service->currency_code }} {{ number_format($app->amount, 2) }}</td>
                            <td class="py-3 px-4 text-sm text-[var(--text-muted)]">{{ Str::limit($app->purpose, 50) }}</td>
                            <td class="py-3 px-4 text-sm text-[var(--text-muted)]">{{ $app->created_at->format('M d, Y') }}</td>
                            <td class="py-3 px-4">
                                <span class="text-xs px-2 py-1 rounded-full
                                    @if($app->status === 'approved') bg-[var(--success-bg)] text-[var(--success)]
                                    @elseif($app->status === 'rejected') bg-[var(--danger-bg)] text-[var(--danger)]
                                    @elseif($app->status === 'pending') bg-[var(--warning-bg)] text-[var(--warning)]
                                    @else bg-[var(--line-soft)] text-[var(--text-muted)] @endif">
                                    {{ ucfirst($app->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                @if($app->status === 'pending')
                                    <form method="POST" action="{{ route('admin.financial-services.applications.approve', $app->id) }}" class="inline-block mr-2">
                                        @csrf
                                        <button type="submit" class="text-xs text-[var(--success)] hover:text-[var(--success)]/80 font-medium">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.financial-services.applications.reject', $app->id) }}" class="inline-block">
                                        @csrf
                                        <input type="hidden" name="notes" value="Rejected during review">
                                        <button type="submit" class="text-xs text-[var(--danger)] hover:text-[var(--danger)]/80 font-medium">Reject</button>
                                    </form>
                                @else
                                    <span class="text-xs text-[var(--text-muted)]">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection