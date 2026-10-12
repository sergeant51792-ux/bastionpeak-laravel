@extends('layouts.public')

@section('title', 'Recommendations')

@section('content')
    <nav class="text-xs text-[var(--text-muted)] mb-6">
        <a href="{{ route('landing') }}" class="hover:text-[var(--primary)]">Home</a>
        <span class="mx-1">/</span>
        <span>Recommendations</span>
    </nav>

    <h1 class="text-3xl lg:text-4xl font-bold text-[var(--text)] tracking-tight">Recommendations</h1>
    <p class="mt-3 text-[var(--text-muted)] leading-relaxed">
        Products and services our team commonly recommends. Log in to see recommendations tailored to your account.
    </p>

    <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white border border-[var(--line)] rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-11 h-11 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)]">
                    <x-icon name="piggy-bank" class="w-5 h-5" />
                </div>
                <h2 class="text-base font-semibold text-[var(--text)]">High-yield savings</h2>
            </div>
            <p class="text-sm text-[var(--text-muted)] leading-relaxed">
                Grow your money in a dedicated savings account with competitive interest, no lock-in, and instant access when you need funds.
            </p>
        </div>
        <div class="bg-white border border-[var(--line)] rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-11 h-11 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)]">
                    <x-icon name="trending-up" class="w-5 h-5" />
                </div>
                <h2 class="text-base font-semibold text-[var(--text)]">Managed investments</h2>
            </div>
            <p class="text-sm text-[var(--text-muted)] leading-relaxed">
                Portfolio managed by professionals, built around your risk profile and goals. Start small and scale as your balance grows.
            </p>
        </div>
        <div class="bg-white border border-[var(--line)] rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-11 h-11 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)]">
                    <x-icon name="shield" class="w-5 h-5" />
                </div>
                <h2 class="text-base font-semibold text-[var(--text)]">Insurance</h2>
            </div>
            <p class="text-sm text-[var(--text-muted)] leading-relaxed">
                Protection for you and your family — from device and travel cover to life insurance — with simple claims through your account.
            </p>
        </div>
        <div class="bg-white border border-[var(--line)] rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-11 h-11 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)]">
                    <x-icon name="percent" class="w-5 h-5" />
                </div>
                <h2 class="text-base font-semibold text-[var(--text)]">Short-term credit</h2>
            </div>
            <p class="text-sm text-[var(--text-muted)] leading-relaxed">
                Personal loans and credit products with clear terms and fast decisions. Check the Financial Services section to apply when logged in.
            </p>
        </div>
    </div>

    <div class="mt-10 p-6 bg-[var(--accent-subtle)] rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-[var(--text)]">See what fits you</p>
            <p class="text-xs text-[var(--text-muted)] mt-1">Log in and visit Financial Services for tailored offers.</p>
        </div>
        <a href="{{ route('signin') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-[var(--primary)] hover:bg-[var(--primary-dark)] rounded-xl transition-colors">
            Log in
            <x-icon name="arrow-right" class="w-4 h-4" />
        </a>
    </div>
@endsection