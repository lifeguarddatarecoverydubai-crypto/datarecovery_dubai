<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Show the application login form.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request): RedirectResponse
    {
        $turnstileEnabled = (bool) config('services.cloudflare.turnstile.enabled');

        $validationRules = [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];

        if ($turnstileEnabled) {
            $validationRules['cf-turnstile-response'] = ['required', 'string', 'max:2048'];
        }

        $credentials = $request->validate($validationRules);

        if ($turnstileEnabled && ! $this->verifyTurnstile($request, $credentials['cf-turnstile-response'])) {
            return back()->withInput($request->only('email', 'remember'))->withErrors([
                'cf-turnstile-response' => 'Security verification failed. Please try again.',
            ]);
        }

        $remember = $request->boolean('remember');

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withInput($request->only('email', 'remember'))->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        if (! $user->is_active) {
            return back()->withErrors([
                'email' => 'Your account has been deactivated. Please contact an administrator.',
            ]);
        }

        // Store pending user session for Two-Factor Authentication
        $request->session()->put('auth.2fa.user_id', $user->id);
        $request->session()->put('auth.2fa.remember', $remember);

        if ($user->hasTwoFactorEnabled()) {
            return redirect()->route('login.two-factor');
        }

        return redirect()->route('login.two-factor.setup');
    }

    /**
     * Verify the single-use login challenge with Cloudflare.
     */
    private function verifyTurnstile(Request $request, string $token): bool
    {
        try {
            $response = Http::asForm()
                ->connectTimeout(3)
                ->timeout(5)
                ->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                    'secret' => config('services.cloudflare.turnstile.secret_key'),
                    'response' => $token,
                ]);
        } catch (ConnectionException $exception) {
            report($exception);

            return false;
        }

        $expectedHostname = strtolower((string) parse_url((string) config('app.url'), PHP_URL_HOST));
        $verifiedHostname = strtolower((string) $response->json('hostname'));

        return $response->successful()
            && $response->json('success') === true
            && $expectedHostname !== ''
            && hash_equals($expectedHostname, $verifiedHostname)
            && hash_equals('admin_login', (string) $response->json('action'));
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->forget([
            'auth.2fa.user_id',
            'auth.2fa.remember',
            'auth.2fa.setup_secret',
            'auth.2fa.setup_recovery_codes',
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'You have been logged out successfully.');
    }
}
