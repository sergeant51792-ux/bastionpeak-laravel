@extends('layouts.customer')

@section('title', 'Financial Services')

@section('content')
<div class="space-y-6">
    <header class="page-header">
        <h1 class="text-xl font-bold text-[var(--text)] tracking-tight">Financial Services</h1>
    </header>

    @if(session('status'))
        <div class="p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-sm text-green-700 dark:text-green-400">
            {{ session('status') }}
        </div>
    @endif

    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
        @foreach($services as $service)
            @php
                $iconColor = '#6366f1';
                $bgColor = '#e0e7ff';
                if ($service->type === 'loan') { $iconColor = '#f59e0b'; $bgColor = '#fef3c7'; }
                if ($service->type === 'grant') { $iconColor = '#059669'; $bgColor = '#dcfce7'; }
                if ($service->type === 'refund') { $iconColor = '#0891b2'; $bgColor = '#cffaffe'; }
            @endphp
            <a href="{{ route('financial-service.apply', $service->id) }}" class="bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-5 text-center transition-all hover:border-[var(--accent)] hover:shadow-md group">
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
            <div class="bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl overflow-hidden">
                @foreach($applications as $app)
                    <div class="p-4 border-b border-[var(--line)] last:border-b-0 flex items-center justify-between">
                        <div>
                            <p class="font-medium text-[var(--text)]">{{ $app->service->name }}</p>
                            <p class="text-xs text-[var(--text-muted)] mt-0.5">
                                Applied {{ $app->created_at->format('M d, Y') }} • {{ $app->service->currency_code }} {{ number_format($app->amount, 2) }}
                            </p>
                        </div>
                        <span class="text-xs px-2.5 py-1 rounded-full
                            @if($app->status === 'approved') bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400
                            @elseif($app->status === 'rejected') bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400
                            @else bg-amber-100 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 @endif">
                            {{ ucfirst($app->status) }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
