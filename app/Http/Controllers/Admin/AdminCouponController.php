<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CouponRequest;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminCouponController extends Controller
{
    /**
     * Display all vouchers and coupons.
     */
    public function index(): View
    {
        $coupons = Coupon::latest()->paginate(10);

        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Store new coupon.
     */
    public function store(CouponRequest $request): RedirectResponse
    {
        $data = $request->validated();

        Coupon::create([
            'code' => strtoupper(trim($data['code'])),
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'discount_type' => $data['discount_type'],
            'discount_value' => $data['discount_value'],
            'min_order_value' => $data['min_order_value'] ?? 0,
            'max_discount_amount' => $data['max_discount_amount'] ?? null,
            'usage_limit' => $data['usage_limit'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
            'is_active' => true,
        ]);

        return back()->with('success', 'Đã tạo mã giảm giá mới thành công!');
    }

    /**
     * Toggle coupon active state.
     */
    public function toggle(int $id): RedirectResponse
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->is_active = ! $coupon->is_active;
        $coupon->save();

        return back()->with('success', 'Đã cập nhật trạng thái hoạt động của mã: '.$coupon->code);
    }

    /**
     * Delete coupon.
     */
    public function destroy(int $id): RedirectResponse
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->delete();

        return back()->with('success', 'Đã xóa mã giảm giá: '.$coupon->code);
    }
}
