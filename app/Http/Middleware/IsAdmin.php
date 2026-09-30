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
            return redirect()->route('admin.login')->with('error', 'Tài khoản hiện tại không có quyền Admin.');
        }

        return $next($request);
    }
}
