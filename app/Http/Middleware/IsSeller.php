<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsSeller
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để truy cập Kênh Người Bán.');
        }

        $user = auth()->user();

        // If user does not have a store yet, redirect to seller registration
        if (! $user->store) {
            return redirect()->route('seller.register')->with('info', 'Bạn chưa có gian hàng trên ShopMart. Hãy hoàn tất đăng ký để bắt đầu kinh doanh!');
        }

        return $next($request);
    }
}
