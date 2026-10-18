<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\GoogleTwoFactorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    public function __construct(
        protected GoogleTwoFactorService $twoFactorService
    ) {}

    /**
     * Show the 2FA challenge authentication screen.
     */
    public function showChallenge(Request $request): View|RedirectResponse
    {
        $userId = $request->session()->get('auth.2fa.user_id');

        if (! $userId && ! Auth::check()) {
            return redirect()->route('login');
        }

        $user = $userId ? User::find($userId) : Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        // If user hasn't set up 2FA yet, redirect them to mandatory setup
        if (! $user->hasTwoFactorEnabled()) {
            return redirect()->route('login.two-factor.setup');
        }

        return view('auth.two-factor-challenge', [
            'user' => $user,
        ]);
    }

    /**
     * Verify the 2FA challenge code or recovery code.
     */
    public function verifyChallenge(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('auth.2fa.user_id');

        if (! $userId && ! Auth::check()) {
            return redirect()->route('login');
        }

        $user = $userId ? User::find($userId) : Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $remember = (bool) $request->session()->get('auth.2fa.remember', false);

        // Scenario 1: Recovery Code verification
        if ($request->filled('recovery_code')) {
            $request->validate([
                'recovery_code' => ['required', 'string'],
            ]);

            if ($user->verifyTwoFactorRecoveryCode($request->string('recovery_code')->toString())) {
                $this->completeAuthentication($request, $user, $remember);

                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Authenticated using emergency recovery code. Remember to review your remaining recovery codes in your profile.');
            }

            return back()->withErrors([
                'recovery_code' => 'The provided emergency recovery code is invalid or has already been used.',
            ]);
        }

        // Scenario 2: 6-digit Google Authenticator code verification
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        if ($user->verifyTwoFactorCode($request->string('code')->toString())) {
            $this->completeAuthentication($request, $user, $remember);

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', "Welcome back, {$user->name}!");
        }

        return back()->withErrors([
            'code' => 'The provided Google Authenticator code is invalid or has expired. Please enter the current 6-digit code.',
        ]);
    }

    /**
     * Show the mandatory Two-Factor setup screen.
     */
    public function showSetup(Request $request): View|RedirectResponse
    {
        $userId = $request->session()->get('auth.2fa.user_id');

        if (! $userId && ! Auth::check()) {
            return redirect()->route('login');
        }

        $user = $userId ? User::find($userId) : Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        // If already enabled and user is fully logged in, redirect to dashboard
        if ($user->hasTwoFactorEnabled() && Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        // Retrieve or generate setup secret in session
        $secret = $request->session()->get('auth.2fa.setup_secret');
        if (! $secret) {
            $secret = $this->twoFactorService->generateSecretKey();
            $request->session()->put('auth.2fa.setup_secret', $secret);
        }

        // Retrieve or generate recovery codes in session
        $recoveryCodes = $request->session()->get('auth.2fa.setup_recovery_codes');
        if (! $recoveryCodes) {
            $recoveryCodes = $this->twoFactorService->generateRecoveryCodes(8);
            $request->session()->put('auth.2fa.setup_recovery_codes', $recoveryCodes);
        }

        $appName = config('app.name', 'LifeGuard Data Recovery');
        $qrCodeUri = $this->twoFactorService->getQrCodeUri($appName, $user->email, $secret);
        $formattedSecret = $this->twoFactorService->formatSecretForDisplay($secret);

        return view('auth.two-factor-setup', [
            'user' => $user,
            'secret' => $secret,
            'formattedSecret' => $formattedSecret,
            'qrCodeUri' => $qrCodeUri,
            'recoveryCodes' => $recoveryCodes,
        ]);
    }

    /**
     * Confirm 2FA setup by verifying the first 6-digit code.
     */
    public function confirmSetup(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('auth.2fa.user_id');

        if (! $userId && ! Auth::check()) {
            return redirect()->route('login');
        }

        $user = $userId ? User::find($userId) : Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $secret = $request->session()->get('auth.2fa.setup_secret');
        $recoveryCodes = $request->session()->get('auth.2fa.setup_recovery_codes');

        if (! $secret || ! $recoveryCodes) {
            return redirect()->route('login.two-factor.setup')
                ->withErrors(['code' => 'Setup session expired. Please scan the newly generated QR code and try again.']);
        }

        if (! $this->twoFactorService->verifyCode($secret, $request->string('code')->toString())) {
            return back()->withErrors([
                'code' => 'The verification code does not match. Please ensure Google Authenticator clock is synchronized and enter the current 6-digit code.',
            ]);
        }

        // Persist 2FA credentials on user
        $user->two_factor_secret = $secret;
        $user->two_factor_recovery_codes = $recoveryCodes;
        $user->two_factor_confirmed_at = now();
        $user->save();

        $remember = (bool) $request->session()->get('auth.2fa.remember', false);

        $request->session()->forget([
            'auth.2fa.setup_secret',
            'auth.2fa.setup_recovery_codes',
        ]);

        $this->completeAuthentication($request, $user, $remember);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Two-Factor Authentication (Google Authenticator) has been successfully activated for your account!');
    }

    /**
     * Cancel pending 2FA login and return to sign in.
     */
    public function cancel(Request $request): RedirectResponse
    {
        $request->session()->forget([
            'auth.2fa.user_id',
            'auth.2fa.remember',
            'auth.2fa.setup_secret',
            'auth.2fa.setup_recovery_codes',
        ]);

        Auth::logout();

        return redirect()->route('login');
    }

    /**
     * Complete authentication, clear 2FA temporary sessions, and regenerate session ID.
     */
    protected function completeAuthentication(Request $request, User $user, bool $remember): void
    {
        Auth::login($user, $remember);

        $request->session()->forget([
            'auth.2fa.user_id',
            'auth.2fa.remember',
            'auth.2fa.setup_secret',
            'auth.2fa.setup_recovery_codes',
        ]);

        $request->session()->regenerate();
    }
}
