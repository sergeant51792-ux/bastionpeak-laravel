<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\LoginOtp;
use App\Services\TotpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SignInController extends Controller
{
    public function __construct(private readonly TotpService $totp)
    {
    }

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

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        if ($user->status === 'pending') {
            return back()->withErrors([
                'email' => 'Your request is still being processed. You will be notified once your account is ready.',
            ])->onlyInput('email');
        }

        if (in_array($user->status, ['blocked', 'frozen', 'suspended', 'locked'], true)) {
            return back()->withErrors([
                'email' => 'This account is not available right now. Please contact support.',
            ])->onlyInput('email');
        }

        if ($user->hasTotp()) {
            $request->session()->put('auth.pending_id', $user->id);
            $request->session()->put('auth.remember', $request->filled('remember'));

            return redirect()->route('signin.otp');
        }

        if ($user->otp_enabled) {
            $activeOtp = LoginOtp::where('user_id', $user->id)
                ->whereNull('used_at')
                ->where('expires_at', '>', now())
                ->latest('created_at')
                ->first();

            if (! $activeOtp) {
                return back()->withErrors([
                    'email' => 'A 4-digit login PIN has not been generated. Contact support to request one.',
                ])->onlyInput('email');
            }

            $request->session()->put('auth.pending_id', $user->id);
            $request->session()->put('auth.remember', $request->filled('remember'));
            $request->session()->put('auth.otp_type', 'pin');

            return redirect()->route('signin.pin');
        }

        return $this->completeLogin($request, $user, $request->filled('remember'));
    }

    public function showVerifyPin(Request $request): \Illuminate\View|Illuminate\Http\RedirectResponse
    {
        $userId = $request->session()->get('auth.pending_id');

        if (! $userId) {
            return redirect()->route('signin');
        }

        return view('auth.verify-login-pin', [
            'email' => User::find($userId)?->email,
            'secondsRemaining' => 40,
        ]);
    }

    public function verifyPin(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'digits:4'],
        ]);

        $userId = $request->session()->get('auth.pending_id');
        $user = $userId ? User::find($userId) : null;

        if (! $user || ! $user->otp_enabled) {
            $request->session()->forget(['auth.pending_id', 'auth.remember', 'auth.otp_type']);

            return redirect()->route('signin');
        }

        $otp = LoginOtp::where('user_id', $user->id)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest('created_at')
            ->first();

        if (! $otp || ! $otp->isValid((string) $request->input('code'))) {
            return back()->withErrors([
                'code' => 'That PIN is not valid or has expired. Request a new one from support.',
            ]);
        }

        $otp->markAsUsed();

        $remember = (bool) $request->session()->pull('auth.remember', false);
        $request->session()->forget(['auth.pending_id', 'auth.otp_type']);

        return $this->completeLogin($request, $user, $remember);
    }

    public function showVerifyOtp(Request $request): \Illuminate\Http\RedirectResponse|\Illuminate\View\View
    {
        $userId = $request->session()->get('auth.pending_id');

        if (! $userId) {
            return redirect()->route('signin');
        }

        return view('auth.verify-otp', [
            'email' => User::find($userId)?->email,
            'secondsRemaining' => $this->totp->secondsRemaining(),
        ]);
    }

    public function verifyOtp(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $userId = $request->session()->get('auth.pending_id');
        $user = $userId ? User::find($userId) : null;

        if (! $user || ! $user->hasTotp()) {
            $request->session()->forget(['auth.pending_id', 'auth.remember']);

            return redirect()->route('signin');
        }

        if (! $this->totp->verify($user->totp_secret, (string) $request->input('code'))) {
            return back()->withErrors([
                'code' => 'That code is not valid. Enter the current code from your authenticator.',
            ]);
        }

        $remember = (bool) $request->session()->pull('auth.remember', false);
        $request->session()->forget('auth.pending_id');

        return $this->completeLogin($request, $user, $remember);
    }

    private function completeLogin(Request $request, User $user, bool $remember): \Illuminate\Http\RedirectResponse
    {
        Auth::login($user, $remember);

        $user->forceFill([
            'last_login_at' => now(),
            'failed_login_count' => 0,
            'locked_until' => null,
        ])->save();

        $request->session()->regenerate();

        if ($user->hasRole('Super Admin')) {
            return redirect()->intended('/admin');
        }

        return redirect()->intended('/home');
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
