@extends('layouts.customer')

@section('title', 'Profile')

@section('content')
<div class="px-3 sm:px-4 lg:px-6 pt-4 space-y-6" id="profile-page">
    @if(session('status'))
        <div class="mb-4 p-4 rounded-xl text-sm flex items-center gap-2 border" style="background: var(--success-bg); color: var(--success); border-color: rgba(5, 150, 105, 0.2);">
            <x-icon name="check-circle" class="w-4 h-4 flex-shrink-0" />
            {{ session('status') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-4 rounded-xl text-sm flex items-center gap-2 border" style="background: var(--danger-bg); color: var(--danger); border-color: rgba(220, 38, 38, 0.2);">
            <x-icon name="x-circle" class="w-4 h-4 flex-shrink-0" />
            {{ session('error') }}
        </div>
    @endif

    {{-- Profile Picture (display only) --}}
    <section class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-5">
        <h2 class="text-base font-bold text-[var(--text)] mb-4">{{ __('Profile') }}</h2>

        <div class="flex items-center gap-5">
            <span class="avatar-ring w-20 h-20">
                @if($user->profile_picture_url)
                    <img src="{{ $user->profile_picture_url }}" alt="{{ $user->name ?? 'Profile' }}" class="w-full h-full object-cover">
                @else
                    @php
                        $initials = strtoupper(substr($user->name ?? $user->email ?? 'BP', 0, 2));
                    @endphp
                    <span class="avatar-initials text-2xl">{{ $initials }}</span>
                @endif
            </span>
        </div>

        <p class="text-xs text-[var(--text-muted)] mt-4">
            {{ __('Once your profile is set, it cannot be changed. Contact support if any detail needs to be corrected.') }}
        </p>
    </section>

    {{-- Personal Information (read only) --}}
    <section class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-5">
        <h2 class="text-base font-bold text-[var(--text)] mb-4">{{ __('Personal Information') }}</h2>

        <dl class="divide-y divide-[var(--line)]">
            <div class="flex items-baseline justify-between gap-4 py-3">
                <dt class="text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider">{{ __('Full Name') }}</dt>
                <dd class="text-sm font-semibold text-[var(--text)] text-right break-words">{{ $user->name }}</dd>
            </div>
            <div class="flex items-baseline justify-between gap-4 py-3">
                <dt class="text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider">{{ __('Email Address') }}</dt>
                <dd class="text-sm font-semibold text-[var(--text)] text-right break-all">{{ $user->email }}</dd>
            </div>
            <div class="flex items-baseline justify-between gap-4 py-3">
                <dt class="text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider">{{ __('Phone Number') }}</dt>
                <dd class="text-sm font-semibold text-[var(--text)] text-right break-all">{{ $user->phone ?: '—' }}</dd>
            </div>
            <div class="flex items-baseline justify-between gap-4 py-3">
                <dt class="text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider">{{ __('Address') }}</dt>
                <dd class="text-sm font-semibold text-[var(--text)] text-right break-words">{{ $user->address ?: '—' }}</dd>
            </div>
        </dl>
    </section>

    {{-- Security --}}
    <section class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-5 space-y-5">
        <h2 class="text-base font-bold text-[var(--text)]">{{ __('Security') }}</h2>

        @if($user->hasTotp())
            {{-- TOTP locked --}}
            <div class="flex items-center gap-3 p-4 border border-[var(--line)] rounded-xl bg-[var(--success-bg)]">
                <span class="w-10 h-10 rounded-xl bg-[var(--success-bg)] flex items-center justify-center text-[var(--success)] shrink-0">
                    <x-icon name="shield-check" class="w-5 h-5" />
                </span>
                <div class="flex-1 min-w-0">
                    <span class="block text-sm font-semibold text-[var(--text)]">{{ __('Google Authenticator') }}</span>
                    <span class="block text-xs text-[var(--text-muted)] mt-0.5">{{ __('Required for sign in, transfers and password changes') }}</span>
                </div>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-[var(--success-bg)] text-[var(--success)] shrink-0">
                    {{ __('On') }}
                </span>
            </div>
            <p class="text-xs text-[var(--text-muted)]">{{ __('Authenticator cannot be disabled once enabled. Contact support if you need assistance.') }}</p>
        @else
            {{-- TOTP not yet set up --}}
            <a href="{{ route('two-factor.setup') }}" class="flex items-center gap-3 p-4 border border-[var(--line)] rounded-xl hover:border-[var(--accent)] hover:bg-[var(--line-soft)] transition-colors">
                <span class="w-10 h-10 rounded-xl bg-[var(--accent-subtle)] flex items-center justify-center text-[var(--accent)] shrink-0">
                    <x-icon name="shield-check" class="w-5 h-5" />
                </span>
                <span class="flex-1 min-w-0">
                    <span class="block text-sm font-semibold text-[var(--text)]">{{ __('Google Authenticator') }}</span>
                    <span class="block text-xs text-[var(--text-muted)] mt-0.5">{{ __('Set up a one-time code for extra security') }}</span>
                </span>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full shrink-0 bg-[var(--line-soft)] text-[var(--text-muted)]">
                    {{ __('Off') }}
                </span>
                <x-icon name="chevron-right" class="w-5 h-5 text-[var(--text-muted)] shrink-0" />
            </a>
        @endif
    </section>

    {{-- Preferences --}}
    <section class="bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-bold text-[var(--text)]">{{ __('Preferences') }}</h2>
            <button type="submit" form="preferences-form" id="save-preferences-btn" class="hidden px-4 py-2 bg-[var(--accent)] text-white rounded-xl text-sm font-medium min-h-[44px]">
                {{ __('Save Settings') }}
            </button>
        </div>
        <form id="preferences-form" method="POST" action="{{ route('profile.preferences') }}" class="space-y-5">
            @csrf
            <div class="flex items-center justify-between py-2">
                <div>
                <p class="text-sm font-semibold text-[var(--text)]">{{ __('Hide Balances') }}</p>
                <p class="text-xs text-[var(--text-muted)]">{{ __('Hide amounts on home and cards') }}</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="hide_balance" value="0">
                    <input type="checkbox" id="hide-balance-toggle" name="hide_balance" value="1" class="sr-only peer" {{ $user->hide_balance ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-[var(--line)] peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[var(--accent)] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-[var(--line)] after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[var(--accent)]"></div>
                </label>
            </div>

            <div class="flex items-center justify-between py-2">
                <div>
                <p class="text-sm font-semibold text-[var(--text)]">{{ __('Display Mode') }}</p>
                <p class="text-xs text-[var(--text-muted)]">{{ __('Choose your preferred appearance') }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex items-center gap-1 bg-[var(--line-soft)] rounded-lg p-1 text-sm">
                        <button type="button" id="theme-light" data-theme="light" class="px-3 py-1.5 rounded-lg min-h-[44px] min-w-[44px] transition-all theme-option">
                            <x-icon name="sun" class="w-4 h-4" />
                        </button>
                        <button type="button" id="theme-dark" data-theme="dark" class="px-3 py-1.5 rounded-lg min-h-[44px] min-w-[44px] transition-all theme-option">
                            <x-icon name="moon" class="w-4 h-4" />
                        </button>
                        <button type="button" id="theme-system" data-theme="system" class="px-3 py-1.5 rounded-lg min-h-[44px] min-w-[44px] transition-all theme-option">
                            <x-icon name="monitor" class="w-4 h-4" />
                        </button>
                    </div>
                    <input type="hidden" name="theme_preference" id="theme-input" value="{{ $user->theme_preference ?: 'system' }}">
                </div>
            </div>

                <div class="flex items-center justify-between py-2">
                    <div>
                        <p class="text-sm font-semibold text-[var(--text)]">{{ __('Language') }}</p>
                        <p class="text-xs text-[var(--text-muted)]">{{ __('Select your display language') }}</p>
                    </div>
                    <div class="relative w-44">
                        <select name="language" id="language-select" class="input-field">
                            <option value="en" {{ (($user->language ?? session('locale') ?? app()->getLocale())) === 'en' ? 'selected' : '' }}>English</option>
                            <option value="es" {{ (($user->language ?? session('locale') ?? app()->getLocale())) === 'es' ? 'selected' : '' }}>Español</option>
                            <option value="fr" {{ (($user->language ?? session('locale') ?? app()->getLocale())) === 'fr' ? 'selected' : '' }}>Français</option>
                            <option value="de" {{ (($user->language ?? session('locale') ?? app()->getLocale())) === 'de' ? 'selected' : '' }}>Deutsch</option>
                            <option value="zh" {{ (($user->language ?? session('locale') ?? app()->getLocale())) === 'zh' ? 'selected' : '' }}>中文</option>
                            <option value="ja" {{ (($user->language ?? session('locale') ?? app()->getLocale())) === 'ja' ? 'selected' : '' }}>日本語</option>
                            <option value="pt" {{ (($user->language ?? session('locale') ?? app()->getLocale())) === 'pt' ? 'selected' : '' }}>Português</option>
                            <option value="ru" {{ (($user->language ?? session('locale') ?? app()->getLocale())) === 'ru' ? 'selected' : '' }}>Русский</option>
                            <option value="ar" {{ (($user->language ?? session('locale') ?? app()->getLocale())) === 'ar' ? 'selected' : '' }}>العربية</option>
                            <option value="hi" {{ (($user->language ?? session('locale') ?? app()->getLocale())) === 'hi' ? 'selected' : '' }}>हिन्दी</option>
                        </select>
                    </div>
                </div>
        </form>
    </section>

    {{-- Logout --}}
    <form method="POST" action="{{ route('signout') }}" class="pt-2">
        @csrf
        <button type="submit" class="w-full py-3.5 btn-danger min-h-[44px]">
            {{ __('Sign out') }}
        </button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var savePrefBtn = document.getElementById('save-preferences-btn');
    var hideToggle = document.getElementById('hide-balance-toggle');
    var themeOptions = document.querySelectorAll('.theme-option');
    var themeInput = document.getElementById('theme-input');
    var languageSelect = document.getElementById('language-select');
    var prefsForm = document.getElementById('preferences-form');

    function showPrefBtn() {
        if (savePrefBtn) savePrefBtn.classList.remove('hidden');
    }

    var currentTheme = localStorage.getItem('theme') || '{{ $user->theme_preference ?: "system" }}';

    function applyTheme(theme) {
        if (theme === 'dark') {
            document.body.classList.add('dark');
            document.body.setAttribute('data-theme', 'dark');
        } else if (theme === 'light') {
            document.body.classList.remove('dark');
            document.body.setAttribute('data-theme', 'light');
        } else {
            var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (prefersDark) {
                document.body.classList.add('dark');
                document.body.setAttribute('data-theme', 'dark');
            } else {
                document.body.classList.remove('dark');
                document.body.setAttribute('data-theme', 'light');
            }
        }
    }

    if (themeInput) {
        themeInput.value = currentTheme;
    }

        function updateThemeButtons(theme) {
        themeOptions.forEach(function (btn) {
            btn.classList.remove('bg-[var(--surface)]', 'shadow-md', 'border', 'border-[var(--accent)]');
        });
        var activeBtn = document.querySelector('.theme-option[data-theme="' + theme + '"]');
        if (activeBtn) {
            activeBtn.classList.add('bg-[var(--surface)]', 'shadow-md');
        }
    }

    applyTheme(currentTheme);
    updateThemeButtons(currentTheme);

    if (currentTheme !== 'system') {
        localStorage.setItem('theme', currentTheme);
    }

    themeOptions.forEach(function (btn) {
        btn.addEventListener('click', function () {
            currentTheme = this.getAttribute('data-theme');
            applyTheme(currentTheme);
            updateThemeButtons(currentTheme);
            if (themeInput) themeInput.value = currentTheme;
            if (currentTheme !== 'system') {
                localStorage.setItem('theme', currentTheme);
            } else {
                localStorage.removeItem('theme');
            }
            showPrefBtn();
            savePrefBtn.click();
        });
    });

    if (languageSelect) {
        languageSelect.addEventListener('change', function () {
            showPrefBtn();
            setTimeout(function() {
                prefsForm.submit();
            }, 300);
        });
    }

    if (hideToggle) {
        hideToggle.addEventListener('change', function () {
            if (this.checked) {
                document.body.setAttribute('data-hide-balance', 'true');
            } else {
                document.body.removeAttribute('data-hide-balance');
            }
            showPrefBtn();
            setTimeout(function() {
                prefsForm.submit();
            }, 300);
        });
    }

    if (input) {
        input.addEventListener('change', function(e) {
            if (this.files.length > 0) {
                var formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('picture', this.files[0]);
                 fetch('{{ route('profile.picture.upload') }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                 }).then(function(r) {
                    if (!r.ok) {
                        return r.json().then(function(err) {
                            var msg = err.error || err.message || (r.status + ': ' + r.statusText);
                            throw new Error(msg);
                        }).catch(function() {
                            throw new Error(r.status + ': ' + r.statusText);
                        });
                    }
                    return r.json();
                }).then(function(data) {
                    var statusDiv = document.createElement('div');
                    statusDiv.className = 'mb-4 p-4 rounded-xl text-sm flex items-center gap-2 border';
                    statusDiv.style.cssText = 'background: var(--success-bg); color: var(--success); border-color: rgba(5,150,105,.2);';
                    statusDiv.textContent = '{{ __('Profile picture updated.') }}';
                    document.querySelector('#profile-page').prepend(statusDiv);
                    setTimeout(function() { location.reload(); }, 1500);
                }).catch(function(err) {
                    var statusDiv = document.createElement('div');
                    statusDiv.className = 'mb-4 p-4 rounded-xl text-sm flex items-center gap-2 border';
                    statusDiv.style.cssText = 'background: var(--danger-bg); color: var(--danger); border-color: rgba(220,38,38,.2);';
                    statusDiv.textContent = '{{ __('Upload failed:') }} ' + err.message;
                    document.querySelector('#profile-page').prepend(statusDiv);
                });
                this.value = '';
            }
        });
    }
});
</script>
@endsection
