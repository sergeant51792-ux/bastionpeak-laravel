<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showLinkRequest(): \Illuminate\View\View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user->pin_hash) {
            return back()->withErrors(['email' => 'No security PIN is set for this account. Contact support.'])->onlyInput('email');
        }

        $token = Str::random(64);

        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            ['token' => $token, 'created_at' => now()]
        );

        session([
            'password_reset_email' => $user->email,
            'password_reset_token' => $token,
        ]);

        return redirect()->route('password.verify.pin')->with('status', 'Please enter your security PIN to continue.');
    }

    public function showPinVerification(): \Illuminate\View\View
    {
        return view('auth.verify-pin');
    }

    public function verifyPin(Request $request)
    {
        $request->validate([
            'pin' => ['required', 'digits:4'],
        ]);

        $email = session('password_reset_email');
        $token = session('password_reset_token');

        if (!$email || !$token) {
            return redirect()->route('password.forgot')->with('status', 'Session expired. Please start again.');
        }

        $user = User::where('email', $email)->first();

        if (!$user || !$user->verifyPin($request->pin)) {
            return back()->withErrors(['pin' => 'The security PIN does not match.'])->withInput();
        }

        session(['password_reset_confirmed' => true]);

        return redirect()->route('password.reset');
    }

    public function showResetForm(): \Illuminate\View\View
    {
        if (!session('password_reset_confirmed')) {
            return redirect()->route('password.forgot')->with('status', 'Please verify your identity first.');
        }

        return view('auth.reset-password');
    }

    public function reset(Request $request)
    {
        $request->validate([
            'password' => ['required', 'min:8', 'confirmed', 'different:pin'],
            'pin' => ['required', 'digits:4', 'confirmed'],
        ]);

        $email = session('password_reset_email');

        if (!$email || !session('password_reset_confirmed')) {
            return redirect()->route('password.forgot')->with('status', 'Session expired. Please start again.');
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            return redirect()->route('password.forgot')->withErrors(['email' => 'Account not found.']);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'pin_hash' => Hash::make($request->pin),
        ]);

        \DB::table('password_reset_tokens')->where('email', $email)->delete();

        session()->forget(['password_reset_email', 'password_reset_token', 'password_reset_confirmed']);

        auth()->login($user);

        return redirect()->route('home')->with('status', 'Password reset successfully. Welcome back.');
    }
}
