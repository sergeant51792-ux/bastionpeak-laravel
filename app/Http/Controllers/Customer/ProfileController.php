<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use App\Services\NotificationService;

class ProfileController extends Controller
{
    public function __construct(protected NotificationService $notifications)
    {
        $this->middleware('auth');
    }

    public function __invoke(Request $request): \Illuminate\View\View
    {
        return view('customer.profile', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'                 => ['required', 'string', 'max:255'],
            'email'                => ['required', 'string', 'email', 'unique:users,email,' . $user->id],
            'phone'                => ['nullable', 'string', 'max:30'],
            'theme_preference'     => ['nullable', 'in:system,light,dark'],
            'hide_balance'         => ['sometimes', 'boolean'],
        ]);

        $user->update($validated);

        return back()->with('status', 'Profile updated.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)->letters()->numbers()->symbols()],
        ]);

        $request->user()->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
        ]);

        return back()->with('status', 'Password updated.');
    }

    public function uploadPicture(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'picture' => ['required', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'],
        ]);

        if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $path = $request->file('picture')->store('profile-pictures', 'public');

        $user->update([
            'profile_picture' => $path,
        ]);

        return back()->with('status', 'Profile picture updated.');
    }

    public function removePicture(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        $user->update([
            'profile_picture' => null,
        ]);

        return back()->with('status', 'Profile picture removed.');
    }

    public function updatePreferences(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'theme_preference' => ['nullable', 'in:system,light,dark'],
            'hide_balance'     => ['sometimes', 'boolean'],
        ]);

        $user->update($validated);

        return back()->with('status', 'Preferences saved.');
    }
}
