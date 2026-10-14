@extends('layouts.public')

@section('title', 'Policies')

@section('content')
    <nav class="text-xs text-[var(--text-muted)] mb-6">
        <a href="{{ route('landing') }}" class="hover:text-[var(--primary)]">Home</a>
        <span class="mx-1">/</span>
        <span>Policies</span>
    </nav>

    <h1 class="text-3xl lg:text-4xl font-bold text-[var(--text)] tracking-tight">Policies</h1>
    <p class="mt-3 text-[var(--text-muted)] leading-relaxed">
        The policies that keep Bastion Peak safe, fair, and compliant for everyone who uses the platform.
    </p>

    <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-5">
        <a href="{{ route('terms') }}" class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6 hover:border-[var(--accent)] hover:shadow-md transition-all group">
            <div class="w-11 h-11 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] mb-4 group-hover:bg-[var(--accent)] group-hover:text-white transition-all">
                <x-icon name="file-text" class="w-5 h-5" />
            </div>
            <h2 class="text-base font-semibold text-[var(--text)]">Terms of Service</h2>
            <p class="text-sm text-[var(--text-muted)] mt-1 leading-relaxed">The rules that govern your use of our platform, accounts, and services.</p>
        </a>
        <a href="{{ route('privacy') }}" class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6 hover:border-[var(--accent)] hover:shadow-md transition-all group">
            <div class="w-11 h-11 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] mb-4 group-hover:bg-[var(--accent)] group-hover:text-white transition-all">
                <x-icon name="shield" class="w-5 h-5" />
            </div>
            <h2 class="text-base font-semibold text-[var(--text)]">Privacy Policy</h2>
            <p class="text-sm text-[var(--text-muted)] mt-1 leading-relaxed">How we collect, use, protect, and share your personal information.</p>
        </a>
    </div>

    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
            <x-icon name="lock" class="w-5 h-5 text-[var(--accent)] mb-4" />
            <h2 class="text-sm font-semibold text-[var(--text)] mb-2">Security</h2>
            <p class="text-xs text-[var(--text-muted)] leading-relaxed">We use encryption, role-based access, and continuous monitoring to protect every account and transaction.</p>
        </div>
        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
            <x-icon name="scale" class="w-5 h-5 text-[var(--accent)] mb-4" />
            <h2 class="text-sm font-semibold text-[var(--text)] mb-2">Fairness</h2>
            <p class="text-xs text-[var(--text-muted)] leading-relaxed">Clear fees, transparent statuses, and a review process that treats every request on its merits.</p>
        </div>
        <div class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
            <x-icon name="shield-check" class="w-5 h-5 text-[var(--accent)] mb-4" />
            <h2 class="text-sm font-semibold text-[var(--text)] mb-2">Compliance</h2>
            <p class="text-xs text-[var(--text-muted)] leading-relaxed">We follow financial services regulations and keep full audit trails of account activity.</p>
        </div>
    </div>

    <div class="mt-8 p-6 bg-[var(--accent-subtle)] rounded-2xl">
        <p class="text-sm text-[var(--text)] leading-relaxed">
            <strong>Questions?</strong> Read our Terms of Service or Privacy Policy for details, or <a href="{{ route('support') }}" class="underline hover:text-[var(--primary)]">contact us</a>.
        </p>
    </div>
@endsection