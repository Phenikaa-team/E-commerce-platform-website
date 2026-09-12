<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
    public function store(Request $request): RedirectResponse
    {
        $store = auth()->user()->store;
        if (! $store) {
            return back()->with('error', 'Bạn cần thiết lập thông tin Gian hàng trước.');
        }

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'name' => 'required|string|max:255',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:1',
            'min_order_value' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date|after:today',
        ], [
            'code.required' => 'Vui lòng nhập mã Voucher.',
            'code.unique' => 'Mã Voucher này đã tồn tại trên hệ thống, vui lòng chọn mã khác.',
            'name.required' => 'Vui lòng nhập tên chương trình.',
            'discount_value.required' => 'Vui lòng nhập mức giảm giá.',
            'expires_at.after' => 'Ngày hết hạn phải là một ngày trong tương lai.',
        ]);

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
