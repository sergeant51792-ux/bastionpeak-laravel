<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

    public function uploadPicture(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'picture' => ['required', 'max:2048'],
        ]);

        $file = $request->file('picture');
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, $allowedExtensions)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'The file must be an image (JPEG, PNG, GIF, or WebP).'], 422);
            }
            return back()->with('error', 'The file must be an image (JPEG, PNG, GIF, or WebP).');
        }

        $storageRoot = dirname(base_path()) . '/public_html/storage';
        $storagePath = $storageRoot . '/' . $user->profile_picture;

        if ($user->profile_picture && @file_exists($storagePath)) {
            @unlink($storagePath);
        }

        $dir = $storageRoot . '/profile-pictures';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $filename = uniqid('pp_') . '.' . $extension;
        $destPath = $dir . '/' . $filename;

        if (!move_uploaded_file($file->getRealPath(), $destPath)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Upload failed. Please try again.'], 500);
            }
            return back()->with('error', 'Upload failed. Please try again.');
        }

        $path = 'profile-pictures/' . $filename;

        try {
            $user->update([
                'profile_picture' => $path,
            ]);
        } catch (\Throwable $e) {
            @unlink($destPath);
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Upload failed. Please try again.'], 500);
            }
            return back()->with('error', 'Upload failed. Please try again.');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => asset('storage/' . $path),
            ], 200);
        }

        return back()->with('status', 'Profile picture updated.');
    }

    public function removePicture(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        $storageRoot = dirname(base_path()) . '/public_html/storage';
        $storagePath = $storageRoot . '/' . $user->profile_picture;

        if ($user->profile_picture && @file_exists($storagePath)) {
            @unlink($storagePath);
        }

        $user->update([
            'profile_picture' => null,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true], 200);
        }

        return back()->with('status', 'Profile picture removed.');
    }

    public function updatePreferences(Request $request): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'theme_preference' => ['nullable', 'in:system,light,dark'],
            'hide_balance'     => ['sometimes', 'boolean'],
            'language'         => ['nullable', 'in:en,es,fr,de,zh,ja,pt,ru,ar,hi'],
        ]);

        $user->update($validated);

        if (isset($validated['language'])) {
            session(['locale' => $validated['language']]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'hide_balance' => $user->hide_balance], 200);
        }

        return back()->with('status', 'Preferences saved.');
    }

    public function setLanguage(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'language' => ['required', 'in:en,es,fr,de,zh,ja,pt,ru,ar,hi'],
        ]);

        $user->update([
            'language' => $request->language,
        ]);

        session(['locale' => $request->language]);

        return back()->with('status', 'Language updated.');
    }
}
