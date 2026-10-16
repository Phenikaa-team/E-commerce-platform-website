<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\CouponRequest;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SellerCouponController extends Controller
{
    /**
     * Display list of shop-specific discount coupons.
     */
    public function index(Request $request): View
    {
        $store = auth()->user()->store;
        $status = $request->query('status', 'all');
        $search = trim((string) $request->query('search', ''));

        $baseQuery = Coupon::where('store_id', $store?->id);

        if (! empty($search)) {
            $baseQuery->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        $now = now();

        // Status counts for banner & tab indicators
        $activeCount = (clone $baseQuery)
            ->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', $now);
            })
            ->count();

        $expiredCount = (clone $baseQuery)
            ->where(function ($q) use ($now) {
                $q->where('is_active', false)
                    ->orWhere('expires_at', '<=', $now);
            })
            ->count();

        $totalCount = (clone $baseQuery)->count();

        // Apply tab filter
        $query = clone $baseQuery;
        if ($status === 'active') {
            $query->where('is_active', true)
                ->where(function ($q) use ($now) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', $now);
                });
        } elseif ($status === 'expired') {
            $query->where(function ($q) use ($now) {
                $q->where('is_active', false)
                    ->orWhere('expires_at', '<=', $now);
            });
        }

        $coupons = $query->latest()->paginate(10)->withQueryString();

        return view('seller.coupons.index', compact(
            'store',
            'coupons',
            'status',
            'search',
            'activeCount',
            'expiredCount',
            'totalCount'
        ));
    }

    /**
     * Show form to create a new coupon.
     */
    public function create(): View
    {
        $store = auth()->user()->store;

        return view('seller.coupons.create', compact('store'));
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

        return redirect()->route('seller.coupons.index')->with('success', "Đã tạo mã giảm giá gian hàng [{$validated['code']}] thành công!");
    }

    /**
     * Show form to edit an existing coupon.
     */
    public function edit(int $id): View
    {
        $store = auth()->user()->store;
        $coupon = Coupon::where('store_id', $store?->id)->findOrFail($id);

        return view('seller.coupons.edit', compact('store', 'coupon'));
    }

    /**
     * Update an existing coupon.
     */
    public function update(CouponRequest $request, int $id): RedirectResponse
    {
        $store = auth()->user()->store;
        $coupon = Coupon::where('store_id', $store?->id)->findOrFail($id);

        $validated = $request->validated();
        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['min_order_value'] = $validated['min_order_value'] ?? 0;

        $coupon->update($validated);

        return redirect()->route('seller.coupons.index')->with('success', "Đã cập nhật mã giảm giá [{$coupon->code}] thành công!");
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

        return redirect()->route('seller.coupons.index')->with('success', "Đã {$statusStr} mã giảm giá [{$coupon->code}].");
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

        return redirect()->route('seller.coupons.index')->with('success', "Đã xóa mã giảm giá [{$code}].");
    }
}
