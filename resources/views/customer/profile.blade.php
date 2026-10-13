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

    {{-- Profile Picture & Info --}}
    <section class="bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-5">
        <h2 class="text-base font-bold text-[var(--text)] mb-4">{{ __('Profile') }}</h2>

        <div class="flex items-center gap-5">
            <div class="relative">
                <div class="w-20 h-20 rounded-full overflow-hidden border-2 border-white shadow-lg flex items-center justify-center bg-[var(--accent-subtle)]">
                    @if($user->profile_picture_url)
                        <img src="{{ $user->profile_picture_url }}" alt="{{ $user->name ?? 'Profile' }}" class="w-full h-full object-cover">
                    @else
                        @php
                            $initials = strtoupper(substr($user->name ?? $user->email ?? 'BP', 0, 2));
                        @endphp
                        <span class="text-2xl font-bold" style="background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">{{ $initials }}</span>
                    @endif
                </div>
                <label for="picture-input" class="absolute bottom-0 right-0 w-7 h-7 rounded-full bg-[var(--accent)] hover:bg-[var(--accent-dark)] border-2 border-white flex items-center justify-center text-white transition-colors cursor-pointer min-w-[44px] min-h-[44px]">
                    <x-icon name="camera" class="w-3.5 h-3.5" />
                    <input type="file" id="picture-input" name="picture" accept="image/*" class="hidden">
                </label>
            </div>

            <div class="flex flex-col gap-2 ml-auto">
                @if($user->profile_picture)
                    <form method="POST" action="{{ route('profile.picture.remove') }}" class="inline">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 text-xs font-semibold rounded-lg min-h-[40px] transition-colors" style="color: var(--danger); border-color: rgba(220, 38, 38, 0.25);">
                            Remove photo
                        </button>
                    </form>
                @endif
                <label for="picture-input" class="cursor-pointer w-full px-4 py-2 text-xs font-semibold text-center text-[var(--accent)] hover:text-[var(--accent-dark)] border border-[var(--accent)]/30 rounded-lg min-h-[40px] flex items-center justify-center transition-colors">
                    Change photo
                </label>
            </div>
        </div>
    </section>

    {{-- Personal Information Form --}}
    <section class="bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-bold text-[var(--text)]">{{ __('Personal Information') }}</h2>
            <div class="flex gap-2">
                <button type="button" id="cancel-edit" class="hidden px-4 py-2.5 text-sm font-medium text-[var(--text-muted)] hover:text-[var(--text)] transition-colors min-h-[44px]">{{ __('Cancel') }}</button>
                <button type="submit" id="save-profile" form="profile-form" class="hidden px-6 py-2.5 btn-primary text-sm font-medium justify-center min-h-[44px]">{{ __('Save Changes') }}</button>
                <button type="button" id="edit-profile-btn" class="px-6 py-2.5 bg-[var(--accent)] text-white rounded-xl text-sm font-medium hover:bg-[var(--accent-dark)] transition-colors min-h-[44px]">{{ __('Edit Profile') }}</button>
            </div>
        </div>

        <form id="profile-form" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">{{ __('Full Name') }}</label>
                <input type="text" id="name" name="name" value="{{ $user->name }}" required
                       class="input-field" placeholder="Enter your name" disabled>
            </div>
            <div>
                <label for="email" class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">{{ __('Email Address') }}</label>
                <input type="email" id="email" name="email" value="{{ $user->email }}" required
                       class="input-field" placeholder="Enter your email" readonly>
            </div>
            <div>
                <label for="phone" class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">{{ __('Phone Number') }}</label>
                <input type="tel" id="phone" name="phone" value="{{ $user->phone ?? '' }}"
                       class="input-field" placeholder="Enter your phone number" disabled>
            </div>
            <div>
                <label for="address" class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">{{ __('Address') }}</label>
                <input type="text" id="address" name="address" value="{{ $user->address ?? '' }}"
                       class="input-field" placeholder="Enter your address" disabled>
            </div>
        </form>
    </section>

    {{-- Security --}}
    <section class="bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-5 space-y-5">
        <h2 class="text-base font-bold text-[var(--text)]">{{ __('Security') }}</h2>

        <form id="password-form" method="POST" action="{{ route('password.change') }}" class="space-y-4">
            @csrf
            <div>
                <label for="current_password" class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">{{ __('Current Password') }}</label>
                <input type="password" id="current_password" name="current_password" required
                       class="input-field" placeholder="Enter current password">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">{{ __('New Password') }}</label>
                <input type="password" id="password" name="password" required
                       class="input-field" placeholder="Enter new password">
                <p class="text-xs text-[var(--text-muted)] mt-1.5">Must be at least 8 characters with letters, numbers, and symbols.</p>
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">{{ __('Confirm New Password') }}</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required
                       class="input-field" placeholder="Re-enter new password">
            </div>

            <div class="flex items-center justify-end pt-2">
                <button type="submit" class="w-full sm:w-auto px-8 py-3.5 btn-primary justify-center min-h-[44px]">
                    <x-icon name="key" class="w-4 h-4" />
                    Update Password
                </button>
            </div>
        </form>
    </section>

    {{-- Preferences --}}
    <section class="bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-base font-bold text-[var(--text)]">{{ __('Preferences') }}</h2>
            <button type="button" id="save-preferences-btn" class="hidden px-4 py-2 bg-[var(--accent)] text-white rounded-xl text-sm font-medium min-h-[44px]">
                Save Settings
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
                    <div class="w-11 h-6 bg-[var(--line)] peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[var(--accent)] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[var(--accent)]"></div>
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
                <select name="language" id="language-select" class="input-field w-40">
                    <option value="en" {{ ($user->language ?? session('locale') ?? app()->getLocale()) === 'en' ? 'selected' : '' }}>English</option>
                    <option value="es" {{ ($user->language ?? session('locale') ?? app()->getLocale()) === 'es' ? 'selected' : '' }}>Español</option>
                    <option value="fr" {{ ($user->language ?? session('locale') ?? app()->getLocale()) === 'fr' ? 'selected' : '' }}>Français</option>
                    <option value="de" {{ ($user->language ?? session('locale') ?? app()->getLocale()) === 'de' ? 'selected' : '' }}>Deutsch</option>
                    <option value="zh" {{ ($user->language ?? session('locale') ?? app()->getLocale()) === 'zh' ? 'selected' : '' }}>中文</option>
                    <option value="ja" {{ ($user->language ?? session('locale') ?? app()->getLocale()) === 'ja' ? 'selected' : '' }}>日本語</option>
                    <option value="pt" {{ ($user->language ?? session('locale') ?? app()->getLocale()) === 'pt' ? 'selected' : '' }}>Português</option>
                    <option value="ru" {{ ($user->language ?? session('locale') ?? app()->getLocale()) === 'ru' ? 'selected' : '' }}>Русский</option>
                    <option value="ar" {{ ($user->language ?? session('locale') ?? app()->getLocale()) === 'ar' ? 'selected' : '' }}>العربية</option>
                    <option value="hi" {{ ($user->language ?? session('locale') ?? app()->getLocale()) === 'hi' ? 'selected' : '' }}>हिन्दी</option>
                </select>
            </div>
        </form>
    </section>

    {{-- Logout --}}
    <form method="POST" action="{{ route('signout') }}" class="pt-2">
        @csrf
        <button type="submit" class="w-full py-3.5 btn-danger min-h-[44px]">
            Log out
        </button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var editBtn = document.getElementById('edit-profile-btn');
    var cancelBtn = document.getElementById('cancel-edit');
    var saveProfileBtn = document.getElementById('save-profile');
    var formInputs = document.querySelectorAll('#profile-form input:not([type=hidden])');

    if (editBtn) {
        editBtn.addEventListener('click', function () {
            formInputs.forEach(function (input) { input.removeAttribute('disabled'); });
            editBtn.classList.add('hidden');
            cancelBtn.classList.remove('hidden');
            saveProfileBtn.classList.remove('hidden');
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function () {
            formInputs.forEach(function (input) { input.setAttribute('disabled', 'disabled'); });
            editBtn.classList.remove('hidden');
            cancelBtn.classList.add('hidden');
            saveProfileBtn.classList.add('hidden');
        });
    }

    var savePrefBtn = document.getElementById('save-preferences-btn');
    var hideToggle = document.getElementById('hide-balance-toggle');
    var themeOptions = document.querySelectorAll('.theme-option');
    var themeInput = document.getElementById('theme-input');
    var languageSelect = document.getElementById('language-select');
    var prefsForm = document.getElementById('preferences-form');

    function showPrefBtn() {
        if (savePrefBtn) savePrefBtn.classList.remove('hidden');
    }

    if (hideToggle) {
        hideToggle.addEventListener('change', function () {
            if (this.checked) {
                document.body.setAttribute('data-hide-balance', 'true');
            } else {
                document.body.removeAttribute('data-hide-balance');
            }
            showPrefBtn();
        });
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
            btn.classList.remove('bg-white', 'shadow-md', 'dark:bg-[var(--surface)]', 'dark:border', 'dark:border-[var(--accent)]');
        });
        var activeBtn = document.querySelector('.theme-option[data-theme="' + theme + '"]');
        if (activeBtn) {
            var isDark = document.body.classList.contains('dark');
            activeBtn.classList.add(isDark ? 'bg-[var(--surface-raised)]' : 'bg-white', 'shadow-md');
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

    var input = document.getElementById('picture-input');
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
