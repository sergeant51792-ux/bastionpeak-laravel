@extends('layouts.public')

@section('title', 'Terms of Service')

@section('content')
    <nav class="text-xs text-[var(--text-muted)] mb-6">
        <a href="{{ route('landing') }}" class="hover:text-[var(--primary)]">Home</a>
        <span class="mx-1">/</span>
        <a href="{{ route('policies') }}" class="hover:text-[var(--primary)]">Policies</a>
        <span class="mx-1">/</span>
        <span>Terms of Service</span>
    </nav>

    <h1 class="text-3xl lg:text-4xl font-bold text-[var(--text)] tracking-tight">Terms of Service</h1>
    <p class="mt-2 text-sm text-[var(--text-muted)]">Last updated: {{ date('F j, Y') }}</p>

    <div class="mt-8 bg-white border border-[var(--line)] rounded-2xl p-6 sm:p-10 space-y-8 text-sm leading-relaxed text-[var(--text)]">
        <section>
            <h2 class="text-lg font-semibold mb-2">1. Acceptance of terms</h2>
            <p class="text-[var(--text-muted)]">By creating an account or using the Bastion Peak platform ("the Service"), you agree to be bound by these Terms of Service. If you do not agree, please do not use the Service.</p>
        </section>
        <section>
            <h2 class="text-lg font-semibold mb-2">2. Eligibility</h2>
            <p class="text-[var(--text-muted)]">You must be at least 18 years old and capable of entering into a binding agreement to use the Service. All information you provide during registration must be accurate and complete.</p>
        </section>
        <section>
            <h2 class="text-lg font-semibold mb-2">3. Account security</h2>
            <p class="text-[var(--text-muted)]">You are responsible for safeguarding your login credentials, one-time passwords, and any authenticator codes. Never share your credentials. Notify us immediately if you suspect unauthorised access to your account.</p>
        </section>
        <section>
            <h2 class="text-lg font-semibold mb-2">4. Acceptable use</h2>
            <p class="text-[var(--text-muted)]">You agree not to use the Service for any unlawful, fraudulent, or abusive purpose, including money laundering, fraud, or transactions with prohibited counterparties. We may suspend or close accounts that violate these terms.</p>
        </section>
        <section>
            <h2 class="text-lg font-semibold mb-2">5. Transactions and approvals</h2>
            <p class="text-[var(--text-muted)]">Transactions are subject to review, which may be automated or manual. A transaction is not final until it has been credited to the recipient's account. We may require additional verification before processing certain transactions.</p>
        </section>
        <section>
            <h2 class="text-lg font-semibold mb-2">6. Fees and limits</h2>
            <p class="text-[var(--text-muted)]">Any applicable fees, charges, or limits will be shown to you before you confirm a transaction. By confirming, you authorise the associated fees to be debited from your account.</p>
        </section>
        <section>
            <h2 class="text-lg font-semibold mb-2">7. Intellectual property</h2>
            <p class="text-[var(--text-muted)]">All software, branding, and content that make up the Service are owned by Bastion Peak or its licensors and may not be reproduced without permission.</p>
        </section>
        <section>
            <h2 class="text-lg font-semibold mb-2">8. Limitation of liability</h2>
            <p class="text-[var(--text-muted)]">To the maximum extent permitted by law, Bastion Peak is not liable for indirect, incidental, or consequential damages arising from your use of the Service, including loss of profits or data.</p>
        </section>
        <section>
            <h2 class="text-lg font-semibold mb-2">9. Changes to these terms</h2>
            <p class="text-[var(--text-muted)]">We may update these terms from time to time. Significant changes will be communicated to you in advance. Continued use of the Service after changes take effect constitutes acceptance.</p>
        </section>
        <section>
            <h2 class="text-lg font-semibold mb-2">10. Contact</h2>
            <p class="text-[var(--text-muted)]">Questions about these terms? <a href="{{ route('support') }}" class="underline text-[var(--primary)]">Contact our support team</a>.</p>
        </section>
    </div>
@endsection