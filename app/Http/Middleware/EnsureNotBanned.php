<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotBanned
{
    /**
     * Handle an incoming request and ensure authenticated user is not banned.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->isBanned()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tài khoản của bạn đã bị khóa do vi phạm quy chế sàn ShopMart.',
                ], 403);
            }

            return redirect()->route('login')->withErrors([
                'login_id' => 'Tài khoản của bạn đã bị khóa do vi phạm quy chế sàn ShopMart. Vui lòng liên hệ bộ phận hỗ trợ.',
            ]);
        }

        return $next($request);
    }
}
