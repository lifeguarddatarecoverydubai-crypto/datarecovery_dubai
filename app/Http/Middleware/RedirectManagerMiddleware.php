<?php

namespace App\Http\Middleware;

use App\Models\RedirectRule;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class RedirectManagerMiddleware
{
    /**
     * Handle an incoming request and redirect if a matching active rule exists.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only inspect safe read requests (GET and HEAD)
        if (! in_array($request->method(), ['GET', 'HEAD'])) {
            return $next($request);
        }

        $requestUri = $request->getRequestUri();
        $requestPath = parse_url($requestUri, PHP_URL_PATH) ?: '/';

        if (preg_match('#^/public(?:/|$)#i', $requestPath) === 1) {
            $canonicalPath = substr($requestPath, strlen('/public')) ?: '/';
            $canonicalUrl = URL::to($canonicalPath);

            if ($request->getQueryString() !== null) {
                $canonicalUrl .= '?'.$request->getQueryString();
            }

            return redirect()->to($canonicalUrl, 301);
        }

        // Enforce canonical non-www host and HTTPS in production
        if (app()->isProduction()) {
            $host = $request->getHost();
            if (str_starts_with($host, 'www.')) {
                $canonicalHost = substr($host, 4);
                $scheme = $request->isSecure() ? 'https' : 'http';

                return redirect()->to("{$scheme}://{$canonicalHost}".$request->getRequestUri(), 301);
            }

            if (! $request->isSecure()) {
                return redirect()->secure($request->getRequestUri(), 301);
            }
        }

        // Never redirect critical system and admin management paths
        if ($request->is('admin') ||
            $request->is('admin/*') ||
            $request->is('api/*') ||
            $request->is('login*') ||
            $request->is('logout') ||
            $request->is('register*') ||
            $request->is('up') ||
            $request->is('_debugbar*')) {
            return $next($request);
        }

        // Never intercept static assets or source files
        $path = $request->path();
        if (preg_match('/\.(css|js|png|jpe?g|gif|svg|webp|avif|ico|woff2?|ttf|eot|map|json|xml|txt)$/i', $path)) {
            return $next($request);
        }

        // Match against active cached redirect rules
        $match = RedirectRule::matchRequest($request);

        if ($match !== null) {
            // Track SEO traffic hit asynchronously / safely
            RedirectRule::recordHitById($match['rule_id']);

            // Status 410 Content Gone
            if ($match['target_url'] === '410_GONE' || $match['status_code'] === 410) {
                abort(410, 'This page has been permanently removed.');
            }

            return redirect()->to($match['target_url'], $match['status_code']);
        }

        return $next($request);
    }
}
