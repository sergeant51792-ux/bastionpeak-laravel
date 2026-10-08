<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SignInController extends Controller
{
    public function showSignIn(): \Illuminate\View\View
    {
        return view('auth.signin');
    }

    public function signIn(Request $request): \Illuminate\Http\RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->hasRole('Super Admin')) {
                return redirect()->intended('/admin');
            }

            return redirect()->intended('/home');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function signOut(Request $request): \Illuminate\Http\RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showFirstSignIn(): \Illuminate\View\View
    {
        return view('auth.first-signin', [
            'email' => request('email'),
            'policy' => [
                ['min', 'At least', '8 characters'],
                ['letter', 'At least', '1 letter'],
                ['number', 'At least', '1 number'],
                ['symbol', 'At least', '1 symbol'],
            ],
        ]);
    }

    public function firstSignIn(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(8)->letters()->numbers()->symbols()],
        ]);

        $user = \App\Models\User::where('email', $validated['email'])->firstOrFail();

        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        ]);

        \Illuminate\Support\Facades\Auth::login($user);

        return redirect()->route('home');
    }
}
