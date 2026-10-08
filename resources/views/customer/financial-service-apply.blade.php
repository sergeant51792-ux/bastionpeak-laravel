@extends('layouts.customer')

@section('title')
Apply for {{ $service->name }}
@endsection

@section('content')
<div class="space-y-6" id="apply-app">
    <header class="page-header">
        <a href="{{ route('financial-services') }}" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-[var(--line-soft)] transition-colors" aria-label="Back">
            <x-icon name="arrow-left" class="w-5 h-5 text-[var(--text-muted)]" />
        </a>
        <h1 class="text-xl font-semibold">{{ $service->name }}</h1>
    </header>

    <div class="bg-white border border-[var(--line)] rounded-2xl p-5">
        <h2 class="text-sm font-semibold text-[var(--text-muted)] mb-2">About this service</h2>
        <p class="text-sm text-[var(--text)] mb-4">{{ $service->description }}</p>
        @if($service->interest_rate)
            <p class="text-xs text-[var(--text-muted)]">Interest rate: {{ number_format($service->interest_rate, 1) }}%</p>
        @endif
        @if($service->term_months)
            <p class="text-xs text-[var(--text-muted)]">Term: {{ $service->term_months }} months</p>
        @endif
        @if($service->min_amount || $service->max_amount)
            <p class="text-xs text-[var(--text-muted)]">
                Amount range:
                @if($service->min_amount) {{ $service->currency_code }} {{ number_format($service->min_amount, 2) }} @endif
                @if($service->max_amount) - {{ $service->currency_code }} {{ number_format($service->max_amount, 2) }} @endif
            </p>
        @endif
    </div>

    <form method="POST" action="{{ route('financial-service.submit', $service->id) }}" class="space-y-5">
        @csrf

        <div>
            <label for="account_id" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Destination Account</label>
            <select id="account_id" name="account_id" class="input-field">
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->name }} ({{ $account->currency->code }} {{ $account->currency->symbol }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="amount" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Amount ({{ $service->currency_code }})</label>
            <input type="number" id="amount" name="amount" step="0.01" required
                   class="input-field"
                   placeholder="0.00">
        </div>

        <div>
            <label for="purpose" class="block text-sm font-semibold text-[var(--text)] mb-1.5">Purpose <span class="text-[var(--text-muted)] font-normal">(optional)</span></label>
            <textarea id="purpose" name="purpose" rows="3" maxlength="1000"
                      class="input-field"
                      placeholder="What will this be used for?"></textarea>
        </div>

        @if($errors->any())
            <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <button type="submit" class="w-full py-3.5 btn-primary">Submit Application</button>
    </form>
</div>
@endsection
