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

        if (! $user->store) {
            return redirect()->route('seller.register')->with('info', 'Bạn chưa có gian hàng trên ShopMart. Hãy hoàn tất đăng ký để bắt đầu kinh doanh!');
        }

        if ($user->store->status === 'pending') {
            return redirect()->route('seller.register')->with('info', 'Hồ sơ mở gian hàng của bạn đang chờ quản trị viên phê duyệt.');
        }

        if ($user->store->status !== 'active' || ! $user->isSeller()) {
            return redirect()->route('seller.register')->with('error', 'Gian hàng của bạn chưa được phê duyệt để truy cập Kênh Người Bán.');
        }

        return $next($request);
    }
}
