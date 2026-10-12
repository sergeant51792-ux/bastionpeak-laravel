@extends('layouts.public')

@section('title', 'Privacy Policy')

@section('content')
    <nav class="text-xs text-[var(--text-muted)] mb-6">
        <a href="{{ route('landing') }}" class="hover:text-[var(--primary)]">Home</a>
        <span class="mx-1">/</span>
        <a href="{{ route('policies') }}" class="hover:text-[var(--primary)]">Policies</a>
        <span class="mx-1">/</span>
        <span>Privacy Policy</span>
    </nav>

    <h1 class="text-3xl lg:text-4xl font-bold text-[var(--text)] tracking-tight">Privacy Policy</h1>
    <p class="mt-2 text-sm text-[var(--text-muted)]">Last updated: {{ date('F j, Y') }}</p>

    <div class="mt-8 bg-white border border-[var(--line)] rounded-2xl p-6 sm:p-10 space-y-8 text-sm leading-relaxed text-[var(--text)]">
        <section>
            <h2 class="text-lg font-semibold mb-2">1. Information we collect</h2>
            <p class="text-[var(--text-muted)]">We collect information you provide when you register or use the Service, including your name, email address, contact details, and transaction activity. We also collect technical data such as your device and usage information to keep the platform secure.</p>
        </section>
        <section>
            <h2 class="text-lg font-semibold mb-2">2. How we use your information</h2>
            <p class="text-[var(--text-muted)]">We use your information to operate your account, process transactions, provide customer support, prevent fraud and abuse, comply with legal obligations, and improve the Service.</p>
        </section>
        <section>
            <h2 class="text-lg font-semibold mb-2">3. How we protect your information</h2>
            <p class="text-[var(--text-muted)]">We use industry-standard encryption in transit and at rest, role-based access controls, and continuous monitoring. Sensitive data is only accessible to authorised personnel.</p>
        </section>
        <section>
            <h2 class="text-lg font-semibold mb-2">4. Sharing your information</h2>
            <p class="text-[var(--text-muted)]">We do not sell your personal information. We share it only with service providers who help us deliver the Service (subject to confidentiality obligations) or where required by law.</p>
        </section>
        <section>
            <h2 class="text-lg font-semibold mb-2">5. Data retention</h2>
            <p class="text-[var(--text-muted)]">We retain account and transaction records for as long as needed to provide the Service and to meet legal and regulatory obligations, after which they are securely deleted or anonymised.</p>
        </section>
        <section>
            <h2 class="text-lg font-semibold mb-2">6. Your rights</h2>
            <p class="text-[var(--text-muted)]">You may request access to, correction of, or deletion of your personal information, subject to legal and security requirements. Contact us to exercise these rights.</p>
        </section>
        <section>
            <h2 class="text-lg font-semibold mb-2">7. Cookies and tracking</h2>
            <p class="text-[var(--text-muted)]">We use essential cookies and local storage to keep you signed in and to maintain session security. We do not use tracking cookies for advertising.</p>
        </section>
        <section>
            <h2 class="text-lg font-semibold mb-2">8. Contact</h2>
            <p class="text-[var(--text-muted)]">For privacy questions, <a href="{{ route('support') }}" class="underline text-[var(--primary)]">contact our support team</a> or email privacy@bastionpeaks.com.</p>
        </section>
    </div>
@endsection