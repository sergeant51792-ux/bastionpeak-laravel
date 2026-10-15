<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TotpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Google Authenticator (TOTP) setup and challenge.
 *
 * A single authenticator enrolment covers all three protected actions — signing
 * in, sending a transfer and changing the password — because they all verify
 * against the same `users.totp_secret`.
 */
class TwoFactorController extends Controller
{
    /** How long an OTP challenge stays usable. */
    private const CHALLENGE_TTL_MINUTES = 15;

    public function __construct(private readonly TotpService $totp) {}

    /**
     * Show the enrolment page: the shared secret, a deep link that opens the
     * authenticator app, and the form that confirms the first code.
     */
    public function setup(Request $request): \Illuminate\View\View
    {
        $user = $request->user();

        // Keep the same secret while the user is enrolling so the QR/secret and
        // the confirmation form always agree.
        if (! $user->totp_secret) {
            $user->forceFill(['totp_secret' => $this->totp->generateSecret()])->save();
        }

        return view('customer.two-factor-setup', [
            'secret'          => $user->totp_secret,
            'provisioningUri' => $this->provisioningUri($user),
            'enabled'         => $user->hasTotp(),
            'secondsRemaining' => $this->totp->secondsRemaining(),
        ]);
    }

    /**
     * Confirm enrolment with a code from the authenticator app.
     */
    public function enable(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'digits:6']]);

        $user = $request->user();

        if (! $user->totp_secret || ! $this->totp->verify($user->totp_secret, (string) $request->input('code'))) {
            return back()->withErrors([
                'code' => 'That code is not valid. Enter the current code from your authenticator.',
            ]);
        }

        $user->forceFill(['totp_enabled' => true])->save();

        return redirect()
            ->route('two-factor.setup')
            ->with('status', 'Authenticator enabled. It now protects sign in, transfers and password changes.');
    }

    /**
     * Turn the authenticator off again.
     */
    public function disable(Request $request): RedirectResponse
    {
        $request->validate(['code' => ['required', 'digits:6']]);

        $user = $request->user();

        if (! $user->hasTotp() || ! $this->totp->verify($user->totp_secret, (string) $request->input('code'))) {
            return back()->withErrors([
                'code' => 'That code is not valid. Enter the current code from your authenticator.',
            ]);
        }

        $user->forceFill([
            'totp_enabled' => false,
            'totp_secret'  => null,
        ])->save();

        return redirect()
            ->route('two-factor.setup')
            ->with('status', 'Authenticator disabled.');
    }

    /**
     * Challenge page for a protected action that is waiting to proceed.
     */
    public function challenge(Request $request): \Illuminate\View\View|RedirectResponse
    {
        $purpose = $request->session()->get('otp.pending.purpose');

        if (! in_array($purpose, ['transfer', 'password'], true)) {
            return redirect()->route('home');
        }

        if ($this->expired()) {
            $request->session()->forget('otp.pending');

            return redirect()
                ->route($purpose === 'transfer' ? 'pay' : 'profile')
                ->withErrors(['code' => 'Your request expired. Please try again.']);
        }

        return view('customer.two-factor-challenge', [
            'purpose'          => $purpose,
            'secondsRemaining' => $this->totp->secondsRemaining(),
        ]);
    }

    /**
     * Verify the code and hand the stashed action back to its owner.
     */
    public function verify(Request $request, \App\Http\Controllers\Customer\PayController $payments, \App\Http\Controllers\Customer\ProfileController $profile): RedirectResponse
    {
        $request->validate(['code' => ['required', 'digits:6']]);

        /** @var User $user */
        $user = Auth::user();
        $pending = $request->session()->get('otp.pending');

        if (! is_array($pending) || ! in_array($pending['purpose'] ?? '', ['transfer', 'password'], true)) {
            return redirect()->route('home');
        }

        if ($this->expired()) {
            $request->session()->forget('otp.pending');

            return redirect()->route('home')->withErrors(['code' => 'Your request expired. Please try again.']);
        }

        if (! $user->hasTotp() || ! $this->totp->verify($user->totp_secret, (string) $request->input('code'))) {
            return back()->withErrors([
                'code' => 'That code is not valid. Enter the current code from your authenticator.',
            ]);
        }

        $payload = $pending['payload'] ?? [];
        $purpose = $pending['purpose'];
        $request->session()->forget('otp.pending');

        // The code was good, so the action may now run behind the scenes.
        session(['otp.approved.' . $purpose => now()->timestamp]);

        return $purpose === 'transfer'
            ? $payments->processStashedTransfer($request->merge($payload))
            : $profile->applyStashedPassword($request->merge($payload));
    }

    /**
     * Stash an action that is waiting on OTP and bounce to the challenge page.
     */
    public static function requireOtp(Request $request, string $purpose, array $payload): RedirectResponse
    {
        $request->session()->put('otp.pending', [
            'purpose'    => $purpose,
            'payload'    => $payload,
            'expires_at' => now()->addMinutes(self::CHALLENGE_TTL_MINUTES)->timestamp,
        ]);

        return redirect()->route('two-factor.challenge');
    }

    /**
     * Whether the stashed action has a live OTP approval.
     */
    public static function isApproved(string $purpose): bool
    {
        $approvedAt = session('otp.approved.' . $purpose);

        return is_int($approvedAt)
            && ($approvedAt + (self::CHALLENGE_TTL_MINUTES * 60)) > time();
    }

    private function expired(): bool
    {
        $expiresAt = session('otp.pending.expires_at');

        return ! is_int($expiresAt) || $expiresAt < time();
    }

    private function provisioningUri(User $user): string
    {
        $label = rawurlencode('Bastion Peak: ' . $user->email);

        return 'otpauth://totp/' . $label
            . '?secret=' . $user->totp_secret
            . '&issuer=' . rawurlencode('Bastion Peak')
            . '&algorithm=SHA1&digits=6&period=30';
    }
}
