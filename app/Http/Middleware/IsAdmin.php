<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập tài khoản Quản trị viên.');
        }

        if (auth()->user()->role !== 'admin') {
            abort(403, 'Bạn không có quyền truy cập vào Khu vực Quản trị Toàn sàn.');
        }

        return $next($request);
    }
}
