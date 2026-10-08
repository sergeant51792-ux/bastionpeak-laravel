@extends('layouts.admin')

@section('title', 'Financial Services')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Financial Services</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($services as $service)
            <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-[var(--text)]">{{ $service->name }}</h3>
                    <span class="text-xs px-2 py-1 rounded-full
                        @if($service->type === 'loan') bg-blue-100 text-blue-700
                        @elseif($service->type === 'grant') bg-purple-100 text-purple-700
                        @else bg-green-100 text-green-700 @endif">
                        {{ ucfirst($service->type) }}
                    </span>
                </div>
                <p class="text-sm text-[var(--text-muted)] mb-3">{{ Str::limit($service->description, 80) }}</p>
                @if($service->criteria)
                    <div class="mt-2 p-2 bg-[var(--line-soft)] rounded-lg">
                        <p class="text-xs text-[var(--text-muted)]">Criteria:</p>
                        <pre class="text-xs text-[var(--text)] mt-1">{{ json_encode(json_decode($service->criteria), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                @endif
                <a href="{{ route('admin.financial-services.applications', $service->id) }}" class="mt-3 block text-sm font-medium text-[var(--accent)]">View Applications →</a>
            </div>
        @endforeach
    </div>
</div>
@endsection
