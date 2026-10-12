@extends('layouts.public')

@section('title', 'Help & Support')

@section('content')
    <nav class="text-xs text-[var(--text-muted)] mb-6">
        <a href="{{ route('landing') }}" class="hover:text-[var(--primary)]">Home</a>
        <span class="mx-1">/</span>
        <span>Support</span>
    </nav>

    <h1 class="text-3xl lg:text-4xl font-bold text-[var(--text)] tracking-tight">Help &amp; support</h1>
    <p class="mt-3 text-[var(--text-muted)] leading-relaxed">
        We're here to help. Send us a message and our team will get back to you as soon as possible.
    </p>

    @if(session('support_submitted'))
        <div class="mt-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-start gap-3">
            <x-icon name="check" class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" />
            <div>
                <p class="text-sm font-semibold text-emerald-800">Message received</p>
                <p class="text-xs text-emerald-700 mt-1">Thanks for reaching out. A member of our team will respond shortly.</p>
            </div>
        </div>
    @endif

    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white border border-[var(--line)] rounded-2xl p-6 sm:p-8">
            <h2 class="text-lg font-semibold text-[var(--text)]">Send us a message</h2>
            <form method="POST" action="{{ route('support.submit') }}" class="mt-5 space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-[var(--text)] mb-1.5">Full name</label>
                        <input type="text" id="name" name="name" required maxlength="255"
                               value="{{ old('name', auth()->user()->name ?? '') }}"
                               class="w-full px-4 py-2.5 bg-[var(--bg)] border border-[var(--line)] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[var(--accent)]">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-[var(--text)] mb-1.5">Email address</label>
                        <input type="email" id="email" name="email" required maxlength="255"
                               value="{{ old('email', auth()->user()->email ?? '') }}"
                               class="w-full px-4 py-2.5 bg-[var(--bg)] border border-[var(--line)] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[var(--accent)]">
                        @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label for="subject" class="block text-sm font-medium text-[var(--text)] mb-1.5">Subject</label>
                    <input type="text" id="subject" name="subject" required maxlength="255"
                           value="{{ old('subject') }}" placeholder="What do you need help with?"
                           class="w-full px-4 py-2.5 bg-[var(--bg)] border border-[var(--line)] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[var(--accent)]">
                    @error('subject') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="message" class="block text-sm font-medium text-[var(--text)] mb-1.5">Message</label>
                    <textarea id="message" name="message" required rows="6" maxlength="5000"
                              placeholder="Describe the issue and include any relevant details."
                              class="w-full px-4 py-2.5 bg-[var(--bg)] border border-[var(--line)] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[var(--accent)]">{{ old('message') }}</textarea>
                    @error('message') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <button type="submit" class="inline-flex items-center justify-center px-6 py-3 text-sm font-semibold text-white bg-[var(--primary)] hover:bg-[var(--primary-dark)] rounded-xl transition-colors shadow-sm">
                        Send message
                        <x-icon name="send" class="ml-2 w-4 h-4" />
                    </button>
                </div>
            </form>
        </div>

        <div class="space-y-4">
            <div class="bg-white border border-[var(--line)] rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-[var(--text)] mb-4">Other ways to reach us</h3>
                <ul class="space-y-4 text-sm">
                    <li class="flex items-start gap-3">
                        <x-icon name="message-circle" class="w-5 h-5 text-[var(--accent)] flex-shrink-0" />
                        <div>
                            <p class="font-medium text-[var(--text)]">In-app messages</p>
                            <p class="text-xs text-[var(--text-muted)]">Log in and use the Messages section for faster responses.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-icon name="mail" class="w-5 h-5 text-[var(--accent)] flex-shrink-0" />
                        <div>
                            <p class="font-medium text-[var(--text)]">Email</p>
                            <p class="text-xs text-[var(--text-muted)]">support@bastionpeaks.com</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-icon name="life-buoy" class="w-5 h-5 text-[var(--accent)] flex-shrink-0" />
                        <div>
                            <p class="font-medium text-[var(--text)]">Help center</p>
                            <p class="text-xs text-[var(--text-muted)]">Browse our guides on the <a href="{{ route('learn') }}" class="underline hover:text-[var(--primary)]">Learn</a> page.</p>
                        </div>
                    </li>
                </ul>
            </div>
            <div class="bg-white border border-[var(--line)] rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-[var(--text)] mb-2">Response times</h3>
                <p class="text-xs text-[var(--text-muted)] leading-relaxed">Most enquiries are answered within one business day. Urgent account issues are prioritised.</p>
            </div>
        </div>
    </div>
@endsection