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
     * Show form to create a new admin platform coupon.
     */
    public function create(): View
    {
        return view('admin.coupons.create');
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

        return redirect()->route('admin.coupons.index')->with('success', "Đã tạo mã giảm giá toàn sàn [{$data['code']}] thành công!");
    }

    /**
     * Show form to edit an existing admin coupon.
     */
    public function edit(int $id): View
    {
        $coupon = Coupon::findOrFail($id);

        return view('admin.coupons.edit', compact('coupon'));
    }

    /**
     * Update an existing admin coupon.
     */
    public function update(CouponRequest $request, int $id): RedirectResponse
    {
        $coupon = Coupon::findOrFail($id);
        $data = $request->validated();

        $coupon->update([
            'code' => strtoupper(trim($data['code'])),
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'discount_type' => $data['discount_type'],
            'discount_value' => $data['discount_value'],
            'min_order_value' => $data['min_order_value'] ?? 0,
            'max_discount_amount' => $data['max_discount_amount'] ?? null,
            'usage_limit' => $data['usage_limit'] ?? null,
            'expires_at' => $data['expires_at'] ?? null,
        ]);

        return redirect()->route('admin.coupons.index')->with('success', "Đã cập nhật mã giảm giá [{$coupon->code}] thành công!");
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
