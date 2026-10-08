@extends('layouts.admin')

@section('title')
{{ $service->name }} Applications
@endsection

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">{{ $service->name }} Applications</h1>
        <a href="{{ route('admin.financial-services.index') }}" class="text-sm text-[var(--accent)]">← Back to services</a>
    </div>

    @if(session('status'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    @if($applications->isEmpty())
        <div class="bg-white border border-[var(--line)] rounded-2xl p-8 text-center">
            <p class="text-[var(--text-muted)]">No applications yet.</p>
        </div>
    @else
        <div class="bg-white border border-[var(--line)] rounded-2xl overflow-hidden">
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
                                    @if($app->status === 'approved') bg-green-100 text-green-700
                                    @elseif($app->status === 'rejected') bg-red-100 text-red-700
                                    @elseif($app->status === 'pending_review') bg-yellow-100 text-yellow-700
                                    @else bg-gray-100 text-gray-700 @endif">
                                    {{ ucfirst($app->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                @if($app->status === 'submitted')
                                    <form method="POST" action="{{ route('admin.financial-services.applications.approve', $app->id) }}" class="inline-block">
                                        @csrf
                                        <button type="submit" class="text-xs text-green-600 hover:text-green-700 font-medium">Approve</button>
                                    </form>
                                    <button onclick="openRejectModal({{ $app->id }})" type="button" class="text-xs text-red-600 hover:text-red-700 font-medium">Reject</button>
                                @else
                                    <span class="text-xs text-[var(--text-muted)]">—</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

{{-- Reject Modal --}}
<div id="reject-modal" class="fixed inset-0 bg-black/40 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl w-full max-w-md p-6">
        <h3 class="text-lg font-semibold text-[var(--text)] mb-4">Reject Application</h3>
        <form id="reject-form" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-[var(--text)] mb-1">Reason</label>
                <textarea name="notes" required rows="3" class="input-field" placeholder="Why is this being rejected?"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeRejectModal()" class="flex-1 py-2.5 bg-[var(--surface)] border border-[var(--line)] rounded-xl text-sm hover:bg-[var(--line-soft)]">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 bg-red-600 text-white rounded-xl text-sm font-medium hover:bg-red-700">Reject</button>
            </div>
        </form>
    </div>
</div>

<script>
let currentRejectId = null;

function openRejectModal(id) {
    currentRejectId = id;
    const form = document.getElementById('reject-form');
    form.action = '/admin/financial-services/applications/' + id + '/reject';
    document.getElementById('reject-modal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('reject-modal').classList.add('hidden');
    currentRejectId = null;
}
</script>
@endsection
