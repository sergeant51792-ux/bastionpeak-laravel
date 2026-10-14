@extends('layouts.customer')

@section('title', 'Financial Services')

@section('content')
<div class="space-y-6">
    <header class="page-header">
        <h1 class="text-xl font-bold text-[var(--text)] tracking-tight">Financial Services</h1>
    </header>

    @if(session('status'))
        <div class="p-4 bg-[var(--success-bg)] border border-[var(--success)]/30 rounded-xl text-sm text-[var(--success)]">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
        @foreach($services as $service)
            @php
                $iconColor = 'var(--accent)';
                $bgColor = 'var(--accent-subtle)';
                if ($service->type === 'loan') { $iconColor = 'var(--warning)'; $bgColor = 'var(--warning-bg)'; }
                if ($service->type === 'grant') { $iconColor = 'var(--success)'; $bgColor = 'var(--success-bg)'; }
                if ($service->type === 'refund') { $iconColor = 'var(--primary)'; $bgColor = 'var(--accent-subtle)'; }
            @endphp
            <a href="{{ route('financial-service.apply', $service->id) }}" class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-5 text-center transition-all hover:border-[var(--accent)] hover:shadow-md group">
                <div class="w-12 h-12 mx-auto mb-3 rounded-xl flex items-center justify-center" style="background-color: {{ $bgColor }}; color: {{ $iconColor }};">
                    @if($service->type === 'loan')
                        <x-icon name="landmark" class="w-6 h-6" />
                    @elseif($service->type === 'grant')
                        <x-icon name="hand-helping" class="w-6 h-6" />
                    @elseif($service->type === 'refund')
                        <x-icon name="refresh-ccw" class="w-6 h-6" />
                    @else
                        <x-icon name="layout" class="w-6 h-6" />
                    @endif
                </div>
                <h3 class="font-semibold text-[var(--text)]">{{ $service->name }}</h3>
                <p class="text-xs text-[var(--text-muted)] mt-1 line-clamp-2">{{ Str::limit($service->description, 80) }}</p>
            </a>
        @endforeach
    </div>

    @if($applications->isNotEmpty())
        <div class="mt-8">
            <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-3">My Applications</h2>
            <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl overflow-hidden">
                @foreach($applications as $app)
                    <div class="p-4 border-b border-[var(--line)] last:border-b-0 flex items-center justify-between">
                        <div>
                            <p class="font-medium text-[var(--text)]">{{ $app->service->name }}</p>
                            <p class="text-xs text-[var(--text-muted)] mt-0.5">
                                Applied {{ $app->created_at->format('M d, Y') }} • {{ $app->service->currency_code }} {{ number_format($app->amount, 2) }}
                            </p>
                        </div>
                        <span class="text-xs px-2.5 py-1 rounded-full
                            @if($app->status === 'approved') bg-[var(--success-bg)] text-[var(--success)]
                            @elseif($app->status === 'rejected') bg-[var(--danger-bg)] text-[var(--danger)]
                            @else bg-[var(--warning-bg)] text-[var(--warning)] @endif">
                            {{ ucfirst($app->status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
