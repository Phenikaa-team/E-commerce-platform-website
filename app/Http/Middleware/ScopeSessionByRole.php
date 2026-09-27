<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ScopeSessionByRole
{
    /**
     * Handle an incoming request and isolate session cookie by role/portal.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config()->has('session.base_cookie')) {
            config(['session.base_cookie' => config('session.cookie')]);
        }
        $baseCookie = config('session.base_cookie');

        $targetCookie = $baseCookie;

        // Check if request is under /admin or targeted for admin portal
        if ($request->is('admin') || $request->is('admin/*') || $request->query('portal') === 'admin' || $request->input('portal') === 'admin') {
            $targetCookie = $baseCookie.'_admin';
        }
        // Check if request is under /seller or targeted for seller portal
        elseif ($request->is('seller') || $request->is('seller/*') || $request->query('portal') === 'seller' || $request->input('portal') === 'seller') {
            $targetCookie = $baseCookie.'_seller';
        }

        config(['session.cookie' => $targetCookie]);

        if (app()->bound('session')) {
            app('session')->driver()->setName($targetCookie);
        }

        return $next($request);
    }
}
