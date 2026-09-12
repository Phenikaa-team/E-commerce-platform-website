<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\CouponRequest;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SellerCouponController extends Controller
{
    /**
     * Display list of shop-specific discount coupons.
     */
    public function index(): View
    {
        $store = auth()->user()->store;
        $coupons = Coupon::where('store_id', $store?->id)->latest()->paginate(10);

        return view('seller.coupons.index', compact('store', 'coupons'));
    }

    /**
     * Store a newly created shop coupon.
     */
    public function store(CouponRequest $request): RedirectResponse
    {
        $store = auth()->user()->store;
        if (! $store) {
            return back()->with('error', 'Bạn cần thiết lập thông tin Gian hàng trước.');
        }

        $validated = $request->validated();

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['store_id'] = $store->id;
        $validated['is_active'] = true;
        $validated['min_order_value'] = $validated['min_order_value'] ?? 0;

        Coupon::create($validated);

        return back()->with('success', "Đã tạo mã giảm giá gian hàng [{$validated['code']}] thành công!");
    }

    /**
     * Toggle coupon status.
     */
    public function toggle(int $id): RedirectResponse
    {
        $store = auth()->user()->store;
        $coupon = Coupon::where('store_id', $store?->id)->findOrFail($id);

        $coupon->is_active = ! $coupon->is_active;
        $coupon->save();

        $statusStr = $coupon->is_active ? 'bật hoạt động' : 'tạm dừng';

        return back()->with('success', "Đã {$statusStr} mã giảm giá [{$coupon->code}].");
    }

    /**
     * Delete a shop coupon.
     */
    public function destroy(int $id): RedirectResponse
    {
        $store = auth()->user()->store;
        $coupon = Coupon::where('store_id', $store?->id)->findOrFail($id);

        $code = $coupon->code;
        $coupon->delete();

        return back()->with('success', "Đã xóa mã giảm giá [{$code}].");
    }
}
