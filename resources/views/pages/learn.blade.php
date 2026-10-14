@extends('layouts.public')

@section('title', 'Learn Banking')

@section('content')
    <nav class="text-xs text-[var(--text-muted)] mb-6">
        <a href="{{ route('landing') }}" class="hover:text-[var(--primary)]">Home</a>
        <span class="mx-1">/</span>
        <span>Learn</span>
    </nav>

    <h1 class="text-3xl lg:text-4xl font-bold text-[var(--text)] tracking-tight">Learn about banking</h1>
    <p class="mt-3 text-[var(--text-muted)] leading-relaxed">
        Practical guides to help you manage your money, stay secure, and get the most out of your Bastion Peak account.
    </p>

    <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-6">
        <article class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
            <div class="w-11 h-11 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] mb-4">
                <x-icon name="wallet" class="w-5 h-5" />
            </div>
            <h2 class="text-base font-semibold text-[var(--text)]">Getting money into your account</h2>
            <p class="text-sm text-[var(--text-muted)] mt-2 leading-relaxed">
                Choose the funding method that works best. Cryptocurrency is usually credited quickly but may require network confirmations. Gift cards and bank transfers are reviewed by our team before funds are credited — check the expected time shown for each method.
            </p>
        </article>
        <article class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
            <div class="w-11 h-11 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] mb-4">
                <x-icon name="send" class="w-5 h-5" />
            </div>
            <h2 class="text-base font-semibold text-[var(--text)]">Making transfers</h2>
            <p class="text-sm text-[var(--text-muted)] mt-2 leading-relaxed">
                Always confirm the recipient's details before sending. Once submitted, a transfer may require approval before it leaves your account. You'll see its status update in your transaction history in real time.
            </p>
        </article>
        <article class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
            <div class="w-11 h-11 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] mb-4">
                <x-icon name="shield-check" class="w-5 h-5" />
            </div>
            <h2 class="text-base font-semibold text-[var(--text)]">Keeping your account secure</h2>
            <p class="text-sm text-[var(--text-muted)] mt-2 leading-relaxed">
                Use a strong, unique password and enable authenticator codes when prompted. Never share your login details or one-time codes with anyone — including anyone claiming to be from support. We will never ask for your password or codes.
            </p>
        </article>
        <article class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
            <div class="w-11 h-11 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] mb-4">
                <x-icon name="credit-card" class="w-5 h-5" />
            </div>
            <h2 class="text-base font-semibold text-[var(--text)]">Managing cards</h2>
            <p class="text-sm text-[var(--text-muted)] mt-2 leading-relaxed">
                Freeze any card instantly if it's lost or you spot unusual activity. Virtual cards are issued immediately; physical cards require a request and delivery time. Keep track of limits in the Cards section.
            </p>
        </article>
        <article class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
            <div class="w-11 h-11 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] mb-4">
                <x-icon name="clock" class="w-5 h-5" />
            </div>
            <h2 class="text-base font-semibold text-[var(--text)]">Understanding transaction statuses</h2>
            <p class="text-sm text-[var(--text-muted)] mt-2 leading-relaxed">
                <strong>Pending</strong> — the transaction is being reviewed. <strong>Credited / Completed</strong> — the money is in the destination account. <strong>Debited / Completed</strong> — money left your account. <strong>Failed</strong> — the transaction was declined. If a transfer fails, your funds are returned to balance.
            </p>
        </article>
        <article class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-6">
            <div class="w-11 h-11 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] mb-4">
                <x-icon name="landmark" class="w-5 h-5" />
            </div>
            <h2 class="text-base font-semibold text-[var(--text)]">Financial services</h2>
            <p class="text-sm text-[var(--text-muted)] mt-2 leading-relaxed">
                Loans, insurance, and investment products are available through the Financial Services section. Applications are assessed individually — see <a href="{{ route('recommendations') }}" class="underline hover:text-[var(--primary)]">Recommendations</a> for products matched to your profile.
            </p>
        </article>
    </div>

    <div class="mt-10 p-6 bg-[var(--accent-subtle)] rounded-2xl flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <p class="text-sm font-semibold text-[var(--text)]">Need more help?</p>
            <p class="text-xs text-[var(--text-muted)] mt-1">Our team is ready to answer your questions.</p>
        </div>
        <a href="{{ route('support') }}" class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white bg-[var(--primary)] hover:bg-[var(--primary-dark)] rounded-xl transition-colors">
            Contact support
            <x-icon name="arrow-right" class="w-4 h-4" />
        </a>
    </div>
@endsection