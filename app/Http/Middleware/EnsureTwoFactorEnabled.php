<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->hasTwoFactorEnabled()) {
            if ($request->routeIs('login.two-factor*', 'logout')) {
                return $next($request);
            }

            return redirect()->route('login.two-factor.setup')
                ->with('error', 'Google Two-Factor Authentication is required for all accounts on LifeGuard Data Recovery CMS. Please complete setup to continue.');
        }

        return $next($request);
    }
}
