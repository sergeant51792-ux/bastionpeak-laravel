@extends('layouts.customer')

@section('title', 'Profile')

@section('content')
<div class="px-3 sm:px-4 lg:px-6 pt-4 space-y-6" id="profile-page">
    @if(session('status'))
        <div class="sticky top-0 z-10 mb-4 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-xl text-sm text-green-700 dark:text-green-300 flex items-center gap-2">
            <x-icon name="check-circle" class="w-4 h-4 flex-shrink-0" />
            {{ session('status') }}
        </div>
    @endif

    {{-- Profile Picture & Info --}}
    <section class="bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-5">
        <h2 class="text-base font-bold text-[var(--text)] mb-4">Profile</h2>

        <div class="flex items-center gap-6">
            <div class="flex-shrink-0">
                @if($user->profile_picture_url)
                    <img src="{{ $user->profile_picture_url }}" alt="Profile" class="w-20 h-20 rounded-full object-cover ring-2 ring-[var(--accent)]/20">
                @else
                    <div class="w-20 h-20 rounded-full bg-[var(--primary)] flex items-center justify-center text-white text-2xl font-bold">
                        {{ strtoupper(substr($user->name ?? $user->email, 0, 2)) }}
                    </div>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm text-[var(--text-muted)]">Profile picture</p>
                <p class="text-xs text-[var(--text-muted)] mt-0.5">{{ $user->profile_picture ? 'Uploaded' : 'No picture set' }}</p>
            </div>
            <div class="flex flex-col gap-2">
                @if($user->profile_picture)
                    <form method="POST" action="{{ route('profile.picture.remove') }}" class="inline">
                        @csrf
                        <button type="submit" class="w-full px-3 py-2 text-xs font-medium text-red-600 hover:text-red-700 border border-red-200 rounded-lg min-h-[44px] transition-colors">
                            Remove
                        </button>
                    </form>
                @endif
                <label for="picture-input" class="cursor-pointer w-full px-3 py-2 text-xs font-medium text-[var(--accent)] hover:text-[var(--accent-dark)] border border-[var(--accent)]/30 rounded-lg min-h-[44px] flex items-center justify-center transition-colors">
                    Change
                </label>
            </div>
            <input type="file" id="picture-input" name="picture" accept="image/*" class="hidden">
        </div>

        <form id="profile-form" method="POST" action="{{ route('profile.update') }}" class="mt-5 space-y-4">
            @csrf
            @method('POST')
            <div>
                <label for="name" class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Full Name</label>
                <input type="text" id="name" name="name" value="{{ $user->name }}" required
                       class="input-field" placeholder="Enter your name">
            </div>
            <div>
                <label for="email" class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Email Address</label>
                <input type="email" id="email" name="email" value="{{ $user->email }}" required
                       class="input-field" placeholder="Enter your email" readonly>
            </div>
            <div>
                <label for="phone" class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Phone Number</label>
                <input type="tel" id="phone" name="phone" value="{{ $user->phone ?? '' }}"
                       class="input-field" placeholder="Enter your phone number">
            </div>
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" id="cancel-edit" class="hidden px-4 py-2.5 text-sm font-medium text-[var(--text-muted)] hover:text-[var(--text)] transition-colors min-h-[44px]">
                    Cancel
                </button>
                <button type="submit" id="save-profile" class="hidden px-6 py-2.5 btn-primary text-sm font-medium justify-center min-h-[44px]">
                    Save Changes
                </button>
                <button type="button" id="edit-profile-btn" class="px-6 py-2.5 bg-[var(--accent)] text-white rounded-xl text-sm font-medium hover:bg-[var(--accent-dark)] transition-colors min-h-[44px]">
                    Edit Profile
                </button>
            </div>
        </form>
    </section>

    {{-- Security --}}
    <section class="bg-white dark:bg-[var(--surface)] border border-[var(--line)] rounded-2xl p-5 space-y-5">
        <h2 class="text-base font-bold text-[var(--text)]">Security</h2>

        <form id="password-form" method="POST" action="{{ route('password.change') }}" class="space-y-4">
            @csrf
            <div>
                <label for="current_password" class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Current Password</label>
                <input type="password" id="current_password" name="current_password" required
                       class="input-field" placeholder="Enter current password">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">New Password</label>
                <input type="password" id="password" name="password" required
                       class="input-field" placeholder="Enter new password">
                <p class="text-xs text-[var(--text-muted)] mt-1.5">Must be at least 8 characters with letters, numbers, and symbols.</p>
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-[var(--text-muted)] uppercase tracking-wider mb-1.5">Confirm New Password</label>
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
            <h2 class="text-base font-bold text-[var(--text)]">Preferences</h2>
            <button type="button" id="save-preferences-btn" class="hidden px-4 py-2 bg-[var(--accent)] text-white rounded-xl text-sm font-medium min-h-[44px]">
                Save Settings
            </button>
        </div>
        <form id="preferences-form" method="POST" action="{{ route('profile.preferences') }}" class="space-y-5">
            @csrf
            <div class="flex items-center justify-between py-2">
                <div>
                    <p class="text-sm font-semibold text-[var(--text)]">Hide Balances</p>
                    <p class="text-xs text-[var(--text-muted)]">Hide amounts on home and cards</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="hidden" name="hide_balance" value="0">
                    <input type="checkbox" id="hide-balance-toggle" name="hide_balance" value="1" class="sr-only peer" {{ $user->hide_balance ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-[var(--line)] peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-[var(--accent)] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[var(--accent)]"></div>
                </label>
            </div>

            <div class="flex items-center justify-between py-2">
                <div>
                    <p class="text-sm font-semibold text-[var(--text)]">Display Mode</p>
                    <p class="text-xs text-[var(--text-muted)]">Choose your preferred appearance</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" id="theme-btn" class="flex items-center gap-2 px-3 py-1.5 bg-[var(--line-soft)] rounded-lg text-sm font-medium text-[var(--text)] min-h-[44px] min-w-[44px]">
                        <span id="theme-icon">
                            <x-icon name="sun" class="w-4 h-4" />
                        </span>
                        <span id="theme-label">Light</span>
                    </button>
                    <input type="hidden" name="theme_preference" id="theme-input" value="{{ $user->theme_preference ?: 'light' }}">
                </div>
            </div>
        </form>
    </section>

    {{-- Logout --}}
    <form method="POST" action="{{ route('signout') }}" class="pt-2">
        @csrf
        <button type="submit" class="w-full py-3.5 border-2 border-red-200 text-red-600 rounded-xl text-sm font-bold hover:bg-red-50 hover:border-red-300 transition-all min-h-[44px]">
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

    var prefBtn = document.getElementById('save-preferences-btn');
    var hideToggle = document.getElementById('hide-balance-toggle');
    var themeBtn = document.getElementById('theme-btn');
    var themeInput = document.getElementById('theme-input');
    var themeLabel = document.getElementById('theme-label');

    function showPrefBtn() {
        if (prefBtn) prefBtn.classList.remove('hidden');
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

    var currentTheme = '{{ $user->theme_preference ?: "light" }}';

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

    function updateThemeDisplay() {
        if (currentTheme === 'dark') {
            themeLabel.textContent = 'Dark';
            themeInput.value = 'dark';
        } else if (currentTheme === 'light') {
            themeLabel.textContent = 'Light';
            themeInput.value = 'light';
        } else {
            themeLabel.textContent = 'System';
            themeInput.value = 'system';
        }
    }

    applyTheme(currentTheme);
    updateThemeDisplay();

    // Persist theme preference to localStorage for consistency before save
    if (currentTheme !== 'system') {
        localStorage.setItem('theme', currentTheme);
    }

    if (themeBtn) {
        themeBtn.addEventListener('click', function () {
            var themes = ['light', 'dark', 'system'];
            var idx = themes.indexOf(currentTheme);
            currentTheme = themes[(idx + 1) % themes.length];
            applyTheme(currentTheme);
            updateThemeDisplay();
            if (currentTheme !== 'system') {
                localStorage.setItem('theme', currentTheme);
            } else {
                localStorage.removeItem('theme');
            }
            showPrefBtn();
        });
    }
});

// Apply stored theme preference on page load
(function() {
    var stored = localStorage.getItem('theme');
    if (stored && ['light', 'dark'].includes(stored)) {
        var body = document.body;
        if (stored === 'dark') {
            body.classList.add('dark');
            body.setAttribute('data-theme', 'dark');
        } else {
            body.classList.remove('dark');
            body.setAttribute('data-theme', 'light');
        }
    }
})();
</script>
@endsection
