<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\FinancialSettlementService;
use App\Services\PersonalizedRecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * Retrieve or initialize the current user's cart.
     */
    protected function getCart(Request $request): Cart
    {
        if (auth()->check()) {
            $userCart = Cart::where('user_id', auth()->id())->first();
            if ($userCart && $userCart->items()->count() > 0) {
                return $userCart;
            }
        }

        $sessionId = $request->session()->getId();
        $cart = Cart::firstOrCreate(['session_id' => $sessionId]);

        if (auth()->check() && ! $cart->user_id) {
            $cart->user_id = auth()->id();
            $cart->save();
        }

        return $cart;
    }

    /**
     * Display checkout review & payment page.
     */
    public function index(Request $request): View|RedirectResponse
    {
        if (! auth()->check()) {
            return redirect()->guest(route('login'))->with('warning', 'Vui lòng đăng nhập để tiến hành thanh toán đơn hàng.');
        }

        // Buy-now flow: use session item only if buy_now query param is present
        $isBuyNowRequested = $request->has('buy_now') || $request->boolean('buy_now');
        $buyNowItem = $isBuyNowRequested ? $request->session()->get('buy_now_item') : null;
        if (! $isBuyNowRequested) {
            $request->session()->forget('buy_now_item');
        }

        if ($buyNowItem) {
            $request->session()->forget('buy_now_item');
            $product = Product::with(['store', 'images'])->findOrFail($buyNowItem['product_id']);

            // Synthetic CartItem-like object so the view template works unchanged
            $fakeItem = (object) [
                'product_id' => $product->id,
                'product' => $product,
                'quantity' => $buyNowItem['quantity'],
                'unit_price' => $buyNowItem['unit_price'],
                'selected_variant' => $buyNowItem['selected_variant'],
                'subtotal' => $buyNowItem['unit_price'] * $buyNowItem['quantity'],
            ];
            $selectedItems = collect([$fakeItem]);
            $subtotal = (float) $fakeItem->subtotal;
            $shippingFee = $subtotal >= 500000 ? 0.0 : 30000.0;
            $total = $subtotal + $shippingFee;
            $cart = null; // not needed
            $isBuyNow = true;

            $addresses = auth()->user()->addresses;
            $defaultAddress = auth()->user()->defaultAddress() ?? $addresses->first();
            $availableCoupons = Coupon::where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->orderBy('min_order_value', 'asc')->get();

            $freeshipCoupons = $availableCoupons->filter(function ($c) {
                return str_contains(strtoupper($c->code), 'FREESHIP') || str_contains(strtolower($c->name), 'vận chuyển');
            });
            $shopCoupons = $availableCoupons->filter(function ($c) use ($product) {
                return $c->store_id === $product->store_id || str_contains(strtoupper($c->code), 'SHOP') || str_contains(strtoupper($c->code), 'SAMSUNG') || str_contains(strtoupper($c->code), 'SAMZ') || str_contains(strtoupper($c->code), 'FASHION');
            });
            $platformCoupons = $availableCoupons->reject(function ($c) use ($freeshipCoupons, $shopCoupons) {
                return $freeshipCoupons->contains('id', $c->id) || $shopCoupons->contains('id', $c->id);
            });
            $productCoupons = $availableCoupons->reject(function ($c) {
                return str_contains(strtoupper($c->code), 'FREESHIP') || str_contains(strtolower($c->name), 'vận chuyển');
            });

            $storeGroups = $selectedItems->groupBy(fn ($item) => $item->product?->store_id ?? 0);
            $storeSubtotals = [];
            $shopCouponsByStore = [];
            foreach ($storeGroups as $sId => $sItems) {
                $storeSubtotals[$sId] = (float) $sItems->sum(fn ($i) => $i->subtotal ?? ($i->unit_price * $i->quantity));
                $shopCouponsByStore[$sId] = $availableCoupons->filter(function ($c) use ($sId) {
                    return (int) $c->store_id === (int) $sId;
                });
            }

            $recommendedVouchers = Coupon::recommendOptimalVouchers(
                subtotal: $subtotal,
                shippingFee: $shippingFee,
                freeshipCoupons: $freeshipCoupons,
                platformCoupons: $platformCoupons,
                shopCouponsByStore: $shopCouponsByStore,
                storeSubtotals: $storeSubtotals
            );

            // Re-flash so process() can read it when the form is submitted
            $request->session()->put('buy_now_item', $buyNowItem);

            return view('checkout', compact(
                'cart',
                'selectedItems',
                'subtotal',
                'shippingFee',
                'total',
                'addresses',
                'defaultAddress',
                'availableCoupons',
                'freeshipCoupons',
                'shopCoupons',
                'platformCoupons',
                'productCoupons',
                'storeGroups',
                'storeSubtotals',
                'shopCouponsByStore',
                'recommendedVouchers',
                'isBuyNow'
            ));
        }

        // Normal cart flow
        $cart = $this->getCart($request);
        $cart->load(['items.product.store', 'items.product.images']);

        $selectedItems = $cart->items->where('is_selected', true);
        if ($selectedItems->isEmpty()) {
            return redirect()->route('cart')->with('warning', 'Giỏ hàng của bạn đang trống hoặc không có sản phẩm nào được chọn. Hãy chọn sản phẩm để thanh toán!');
        }

        $subtotal = (float) $cart->selected_total;
        $shippingFee = $subtotal >= 500000 ? 0.0 : 30000.0;
        $total = $subtotal + $shippingFee;
        $isBuyNow = false;

        $addresses = auth()->user()->addresses;
        $defaultAddress = auth()->user()->defaultAddress() ?? $addresses->first();

        $availableCoupons = Coupon::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderBy('min_order_value', 'asc')
            ->get();

        $freeshipCoupons = $availableCoupons->filter(function ($c) {
            return str_contains(strtoupper($c->code), 'FREESHIP') || str_contains(strtolower($c->name), 'vận chuyển');
        });
        $shopCoupons = $availableCoupons->filter(function ($c) {
            return $c->store_id !== null || str_contains(strtoupper($c->code), 'SHOP') || str_contains(strtoupper($c->code), 'SAMSUNG') || str_contains(strtoupper($c->code), 'SAMZ') || str_contains(strtoupper($c->code), 'FASHION');
        });
        $platformCoupons = $availableCoupons->reject(function ($c) use ($freeshipCoupons, $shopCoupons) {
            return $freeshipCoupons->contains('id', $c->id) || $shopCoupons->contains('id', $c->id);
        });
        $productCoupons = $availableCoupons->reject(function ($c) {
            return str_contains(strtoupper($c->code), 'FREESHIP') || str_contains(strtolower($c->name), 'vận chuyển');
        });

        $storeGroups = $selectedItems->groupBy(fn ($item) => $item->product?->store_id ?? 0);
        $storeSubtotals = [];
        $shopCouponsByStore = [];
        foreach ($storeGroups as $sId => $sItems) {
            $storeSubtotals[$sId] = (float) $sItems->sum(fn ($i) => $i->subtotal ?? ($i->unit_price * $i->quantity));
            $shopCouponsByStore[$sId] = $availableCoupons->filter(function ($c) use ($sId) {
                return (int) $c->store_id === (int) $sId;
            });
        }

        $recommendedVouchers = Coupon::recommendOptimalVouchers(
            subtotal: $subtotal,
            shippingFee: $shippingFee,
            freeshipCoupons: $freeshipCoupons,
            platformCoupons: $platformCoupons,
            shopCouponsByStore: $shopCouponsByStore,
            storeSubtotals: $storeSubtotals
        );

        return view('checkout', compact(
            'cart',
            'selectedItems',
            'subtotal',
            'shippingFee',
            'total',
            'addresses',
            'defaultAddress',
            'availableCoupons',
            'freeshipCoupons',
            'shopCoupons',
            'platformCoupons',
            'productCoupons',
            'storeGroups',
            'storeSubtotals',
            'shopCouponsByStore',
            'recommendedVouchers',
            'isBuyNow'
        ));
    }

    /**
     * Helper to calculate multi-coupon discounts including freeship, shop voucher, platform voucher and points.
     */
    private function calculateOrderDiscounts(
        float $subtotal,
        float $baseShippingFee,
        ?string $freeshipCode,
        $shopCodeOrCodes,
        ?string $platformCode,
        bool $usePoints = false,
        array $storeSubtotals = []
    ): array {
        $appliedCodes = [];
        $appliedCoupons = [];
        $freeshipDiscount = 0.0;
        $platformDiscount = 0.0;
        $totalShopDiscount = 0.0;
        $storeShopDiscounts = [];
        $pointsDiscount = 0.0;

        // 1. FreeShip Voucher (applies to shipping fee)
        if ($freeshipCode) {
            $fsCoupon = Coupon::where('code', strtoupper($freeshipCode))->where('is_active', true)->first();
            if ($fsCoupon && (! $fsCoupon->expires_at || $fsCoupon->expires_at->isFuture()) && $subtotal >= (float) $fsCoupon->min_order_value) {
                $disc = $fsCoupon->discount_type === 'percent'
                    ? $baseShippingFee * ((float) $fsCoupon->discount_value / 100)
                    : (float) $fsCoupon->discount_value;
                $freeshipDiscount = min($baseShippingFee, $disc);
                $appliedCodes[] = $fsCoupon->code;
                $appliedCoupons[] = $fsCoupon;
            }
        }

        // 2. Shop Vouchers (applies to store subtotals or overall subtotal)
        $shopCodesArray = is_array($shopCodeOrCodes) ? $shopCodeOrCodes : ($shopCodeOrCodes ? [0 => $shopCodeOrCodes] : []);
        foreach ($shopCodesArray as $sId => $sCode) {
            if (! $sCode) {
                continue;
            }
            $shCoupon = Coupon::where('code', strtoupper($sCode))->where('is_active', true)->first();
            if ($shCoupon && (! $shCoupon->expires_at || $shCoupon->expires_at->isFuture())) {
                $storeSub = (float) ($storeSubtotals[$sId] ?? (! empty($storeSubtotals) ? 0 : $subtotal));
                if ($storeSub >= (float) $shCoupon->min_order_value) {
                    $disc = (float) $shCoupon->calculateDiscount($storeSub);
                    if ($disc > 0) {
                        $storeShopDiscounts[$sId] = $disc;
                        $totalShopDiscount += $disc;
                        $appliedCodes[] = $shCoupon->code;
                        $appliedCoupons[] = $shCoupon;
                    }
                }
            }
        }

        // 3. Platform Voucher (applies to subtotal)
        $activeShopCodesUpper = array_map('strtoupper', array_filter($shopCodesArray));
        if ($platformCode && ! in_array(strtoupper($platformCode), $activeShopCodesUpper, true)) {
            $plCoupon = Coupon::where('code', strtoupper($platformCode))->where('is_active', true)->first();
            if ($plCoupon && (! $plCoupon->expires_at || $plCoupon->expires_at->isFuture()) && $subtotal >= (float) $plCoupon->min_order_value) {
                $disc = (float) $plCoupon->calculateDiscount($subtotal);
                if ($disc > 0) {
                    $platformDiscount = $disc;
                    $appliedCodes[] = $plCoupon->code;
                    $appliedCoupons[] = $plCoupon;
                }
            }
        }

        $pointsDiscount = 0.0;

        $effectiveShippingFee = max(0.0, $baseShippingFee - $freeshipDiscount);
        $productDiscount = $totalShopDiscount + $platformDiscount;
        $totalDiscount = $productDiscount + $pointsDiscount;
        $grandTotal = max(0.0, $subtotal + $effectiveShippingFee - $totalDiscount);

        return [
            'freeship_discount' => $freeshipDiscount,
            'platform_discount' => $platformDiscount,
            'shop_discount' => $totalShopDiscount,
            'store_shop_discounts' => $storeShopDiscounts,
            'product_discount' => $productDiscount,
            'points_discount' => $pointsDiscount,
            'total_discount' => $totalDiscount + $freeshipDiscount,
            'base_shipping_fee' => $baseShippingFee,
            'effective_shipping_fee' => $effectiveShippingFee,
            'grand_total' => $grandTotal,
            'applied_codes' => $appliedCodes,
            'applied_coupons' => $appliedCoupons,
        ];
    }

    /**
     * Validate and apply coupon code via AJAX.
     */
    public function applyCoupon(Request $request): JsonResponse
    {
        $rawCode = trim((string) $request->input('code', ''));
        $freeshipCode = trim((string) $request->input('freeship_code', ''));
        $shopCode = trim((string) $request->input('shop_code', ''));
        $shopCodes = (array) $request->input('shop_codes', []);
        $platformCode = trim((string) $request->input('platform_code', ''));
        $usePoints = $request->boolean('use_points', false);
        $storeSubtotals = (array) $request->input('store_subtotals', []);

        $subtotal = (float) $request->input('subtotal', 0);
        if ($subtotal <= 0) {
            $cart = $this->getCart($request);
            $subtotal = (float) $cart->selected_total;
            if ($subtotal <= 0) {
                $subtotal = (float) $cart->items->sum(fn ($i) => $i->quantity * $i->unit_price);
            }
        }

        // Normalize shop codes if single shop_code provided with store_id
        if ($shopCode && empty($shopCodes)) {
            $storeId = (int) $request->input('store_id', 0);
            $shopCodes[$storeId] = $shopCode;
        }

        // If single rawCode passed, classify it
        if ($rawCode !== '' && ! $freeshipCode && empty($shopCodes) && ! $platformCode) {
            $singleCoupon = Coupon::where('code', strtoupper($rawCode))->first();
            if (! $singleCoupon) {
                return response()->json([
                    'success' => false,
                    'message' => 'Mã giảm giá không tồn tại hoặc đã hết hạn.',
                ], 404);
            }
            $isFs = str_contains(strtoupper($singleCoupon->code), 'FREESHIP') || str_contains(strtolower($singleCoupon->name), 'vận chuyển');
            if ($isFs) {
                $freeshipCode = $singleCoupon->code;
            } elseif ($singleCoupon->store_id) {
                $shopCodes[$singleCoupon->store_id] = $singleCoupon->code;
            } else {
                $platformCode = $singleCoupon->code;
            }
        }

        if (empty($rawCode) && empty($freeshipCode) && empty($shopCodes) && empty($platformCode) && ! $usePoints) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng chọn hoặc nhập mã giảm giá.',
            ], 422);
        }

        $baseShippingFee = $subtotal >= 500000 ? 0.0 : 30000.0;
        $calc = $this->calculateOrderDiscounts(
            $subtotal,
            $baseShippingFee,
            $freeshipCode ?: null,
            $shopCodes,
            $platformCode ?: null,
            $usePoints,
            $storeSubtotals
        );

        if (empty($calc['applied_codes'])) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không áp dụng được cho đơn hàng này (chưa đạt giá trị tối thiểu hoặc đã hết hạn).',
            ], 422);
        }

        $appliedNames = collect($calc['applied_coupons'])->pluck('name')->all();
        if ($usePoints) {
            $appliedNames[] = '50.000 ShopMart Xu';
        }

        app(PersonalizedRecommendationService::class)->track(auth()->user(), 'coupon_applied', null, [
            'coupons' => $calc['applied_codes'],
            'discount' => $calc['total_discount'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng khuyến mãi thành công!',
            'coupon_code' => $calc['applied_codes'][0] ?? '',
            'discount_amount' => $calc['total_discount'],
            'applied_codes' => $calc['applied_codes'],
            'applied_codes_string' => implode(', ', $calc['applied_codes']),
            'applied_names' => $appliedNames,
            'freeship_code' => $freeshipCode,
            'shop_code' => reset($shopCodes) ?: '',
            'shop_codes' => $shopCodes,
            'platform_code' => $platformCode,
            'use_points' => $usePoints,
            'freeship_discount' => $calc['freeship_discount'],
            'formatted_freeship_discount' => '-'.number_format($calc['freeship_discount'], 0, ',', '.').'₫',
            'product_discount' => $calc['product_discount'],
            'formatted_product_discount' => '-'.number_format($calc['product_discount'], 0, ',', '.').'₫',
            'shop_discount' => $calc['shop_discount'],
            'formatted_shop_discount' => '-'.number_format($calc['shop_discount'], 0, ',', '.').'₫',
            'store_shop_discounts' => $calc['store_shop_discounts'],
            'platform_discount' => $calc['platform_discount'],
            'formatted_platform_discount' => '-'.number_format($calc['platform_discount'], 0, ',', '.').'₫',
            'points_discount' => $calc['points_discount'],
            'formatted_points_discount' => '-'.number_format($calc['points_discount'], 0, ',', '.').'₫',
            'total_discount' => $calc['total_discount'],
            'formatted_discount' => '-'.number_format($calc['total_discount'], 0, ',', '.').'₫',
            'effective_shipping_fee' => $calc['effective_shipping_fee'],
            'formatted_shipping' => $calc['effective_shipping_fee'] == 0 ? 'MIỄN PHÍ' : number_format($calc['effective_shipping_fee'], 0, ',', '.').'₫',
            'new_total' => $calc['grand_total'],
            'formatted_new_total' => number_format($calc['grand_total'], 0, ',', '.').'₫',
        ]);
    }

    /**
     * Add quick address from checkout modal via AJAX.
     */
    public function quickAddAddress(Request $request): JsonResponse
    {
        $data = $request->validate([
            'recipient_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'address_line' => 'required|string|max:255',
            'city_district' => 'nullable|string|max:255',
            'is_default' => 'nullable|boolean',
        ]);

        if (! auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để lưu sổ địa chỉ.',
            ], 401);
        }

        $fullAddress = trim($data['address_line']);
        if (! empty($data['city_district']) && ! str_contains($fullAddress, trim($data['city_district']))) {
            $fullAddress .= ', '.trim($data['city_district']);
        }

        $isDefault = $request->boolean('is_default', false);
        if ($isDefault || auth()->user()->addresses()->count() === 0) {
            auth()->user()->addresses()->update(['is_default' => false]);
            $isDefault = true;
        }

        $address = auth()->user()->addresses()->create([
            'recipient_name' => $data['recipient_name'],
            'phone' => $data['phone'],
            'address_line' => $fullAddress,
            'is_default' => $isDefault,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm địa chỉ giao hàng mới!',
            'address' => $address,
        ]);
    }

    /**
     * Process order creation within database transaction.
     */
    public function process(Request $request): JsonResponse|RedirectResponse
    {
        if (! auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'requires_auth' => true,
                    'redirect' => route('login'),
                    'message' => 'Vui lòng đăng nhập để hoàn tất đặt hàng.',
                ], 401);
            }

            return redirect()->guest(route('login'))->with('warning', 'Vui lòng đăng nhập để hoàn tất đặt hàng.');
        }

        $request->validate([
            'recipient_name' => 'required|string|min:2|max:100',
            'phone' => 'required|string|min:8|max:20',
            'address_line' => 'required|string|min:5|max:255',
            'payment_method' => 'required|in:cod,vnpay,momo,zalopay,bank_transfer,wallet',
            'coupon_code' => 'nullable|string|max:255',
            'freeship_code' => 'nullable|string|max:50',
            'shop_voucher_code' => 'nullable|string|max:50',
            'platform_voucher_code' => 'nullable|string|max:50',
            'use_points' => 'nullable|boolean',
            'notes' => 'nullable|string|max:500',
        ], [
            'recipient_name.required' => 'Vui lòng nhập họ và tên người nhận hàng.',
            'phone.required' => 'Vui lòng nhập số điện thoại nhận hàng.',
            'address_line.required' => 'Bắt buộc phải cài đặt địa chỉ nhận hàng trước khi đặt hàng.',
        ]);

        // Buy-now flow: items from session, not cart
        $isBuyNowRequested = $request->boolean('buy_now');
        $buyNowItem = $isBuyNowRequested ? $request->session()->pull('buy_now_item') : null;
        if (! $isBuyNowRequested) {
            $request->session()->forget('buy_now_item');
        }

        if ($buyNowItem) {
            $product = Product::findOrFail($buyNowItem['product_id']);
            $fakeItem = (object) [
                'product_id' => $product->id,
                'product' => $product,
                'quantity' => $buyNowItem['quantity'],
                'unit_price' => $buyNowItem['unit_price'],
                'selected_variant' => $buyNowItem['selected_variant'],
                'subtotal' => $buyNowItem['unit_price'] * $buyNowItem['quantity'],
            ];
            $selectedItems = collect([$fakeItem]);
            $isBuyNow = true;
            $cart = null;
        } else {
            $cart = $this->getCart($request);
            $cart->load('items.product.store');
            $selectedItems = $cart->items->where('is_selected', true);
            $isBuyNow = false;
        }

        if ($selectedItems->isEmpty()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Vui lòng chọn sản phẩm để thanh toán.'], 422);
            }

            return redirect()->route('cart')->with('error', 'Vui lòng chọn sản phẩm để thanh toán.');
        }

        // Validate stock availability
        foreach ($selectedItems as $item) {
            $product = $item->product ?? null;
            if ($product) {
                if (! empty($item->selected_variant)) {
                    $variantModel = $product->productVariants()
                        ->where(function ($q) use ($item) {
                            $q->where('name', $item->selected_variant)
                                ->orWhere('name', trim(str_replace(' | ', ' - ', $item->selected_variant)))
                                ->orWhere('color', $item->selected_variant)
                                ->orWhere('option', $item->selected_variant);
                        })->first();

                    if ($variantModel && (int) $variantModel->stock < (int) $item->quantity) {
                        $stockMsg = "Sản phẩm \"{$product->name}\" (phân loại: {$variantModel->name}) chỉ còn {$variantModel->stock} sản phẩm trong kho.";
                        if ($request->expectsJson()) {
                            return response()->json(['success' => false, 'message' => $stockMsg], 422);
                        }

                        return redirect()->route('cart')->with('error', $stockMsg);
                    }
                }

                if ((int) $product->stock < (int) $item->quantity) {
                    $stockMsg = "Sản phẩm \"{$product->name}\" chỉ còn {$product->stock} sản phẩm trong kho.";
                    if ($request->expectsJson()) {
                        return response()->json(['success' => false, 'message' => $stockMsg], 422);
                    }

                    return redirect()->route('cart')->with('error', $stockMsg);
                }
            }
        }

        // Parse coupon inputs
        $freeshipCode = $request->input('freeship_code');
        $shopCode = $request->input('shop_voucher_code');
        $shopCodes = (array) $request->input('shop_voucher_codes', []);
        $platformCode = $request->input('platform_voucher_code');
        $usePoints = $request->boolean('use_points');

        // Fallback: parse comma-separated codes in coupon_code
        if (! $freeshipCode && ! $shopCode && empty($shopCodes) && ! $platformCode && $request->filled('coupon_code')) {
            $rawCodes = array_map('trim', explode(',', $request->input('coupon_code')));
            foreach ($rawCodes as $rc) {
                if ($rc === 'XU') {
                    $usePoints = true;
                } else {
                    $cObj = Coupon::where('code', strtoupper($rc))->first();
                    if ($cObj) {
                        $isFs = str_contains(strtoupper($cObj->code), 'FREESHIP') || str_contains(strtolower($cObj->name), 'vận chuyển');
                        if ($isFs) {
                            $freeshipCode = $cObj->code;
                        } elseif ($cObj->store_id) {
                            $shopCode = $cObj->code;
                        } else {
                            $platformCode = $cObj->code;
                        }
                    }
                }
            }
        }

        // Group items by store
        $storeGroups = $selectedItems->groupBy(fn ($item) => $item->product?->store_id ?? 0);
        $totalSubtotal = (float) $selectedItems->sum('subtotal');

        // Resolve platform voucher
        $platformCoupon = null;
        $totalPlatformDiscount = 0.0;
        if ($platformCode) {
            $plC = Coupon::where('code', strtoupper($platformCode))->where('is_active', true)->first();
            if ($plC && (! $plC->expires_at || $plC->expires_at->isFuture()) && $totalSubtotal >= (float) $plC->min_order_value) {
                $platformCoupon = $plC;
                $totalPlatformDiscount = (float) $plC->calculateDiscount($totalSubtotal);
            }
        }

        // Resolve freeship voucher
        $freeshipCoupon = null;
        $totalFreeshipDiscount = 0.0;
        $storeShippingFees = [];
        foreach ($storeGroups as $sId => $sItems) {
            $sSub = (float) $sItems->sum('subtotal');
            $storeShippingFees[$sId] = $sSub >= 500000 ? 0.0 : 30000.0;
        }
        $totalShippingFee = array_sum($storeShippingFees);

        if ($freeshipCode) {
            $fsC = Coupon::where('code', strtoupper($freeshipCode))->where('is_active', true)->first();
            if ($fsC && (! $fsC->expires_at || $fsC->expires_at->isFuture()) && $totalSubtotal >= (float) $fsC->min_order_value) {
                $freeshipCoupon = $fsC;
                $disc = $fsC->discount_type === 'percent'
                    ? $totalShippingFee * ((float) $fsC->discount_value / 100)
                    : (float) $fsC->discount_value;
                $totalFreeshipDiscount = min($totalShippingFee, $disc);
            }
        }

        $totalPointsDiscount = 0.0;

        $checkoutGroupId = 'CKG-'.strtoupper(Str::random(10));
        $shippingAddress = [
            'name' => $request->input('recipient_name'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address_line'),
        ];

        $createdOrders = [];
        $appliedCoupons = [];
        if ($platformCoupon && $totalPlatformDiscount > 0) {
            $appliedCoupons[] = $platformCoupon;
        }
        if ($freeshipCoupon && $totalFreeshipDiscount > 0) {
            $appliedCoupons[] = $freeshipCoupon;
        }

        $remainingFreeship = $totalFreeshipDiscount;

        DB::transaction(function () use (
            $storeGroups,
            $totalSubtotal,
            $totalPlatformDiscount,
            $totalPointsDiscount,
            $storeShippingFees,
            &$remainingFreeship,
            $shopCodes,
            $shopCode,
            $checkoutGroupId,
            $shippingAddress,
            $request,
            $cart,
            $isBuyNow,
            &$createdOrders,
            &$appliedCoupons
        ) {
            foreach ($storeGroups as $storeId => $items) {
                $storeSubtotal = (float) $items->sum('subtotal');
                $baseShipping = $storeShippingFees[$storeId] ?? 30000.0;

                // Allocate freeship discount to this store
                $storeFreeshipDisc = min($baseShipping, $remainingFreeship);
                $remainingFreeship = max(0.0, $remainingFreeship - $storeFreeshipDisc);
                $effectiveShipping = max(0.0, $baseShipping - $storeFreeshipDisc);

                // Shop voucher for this store
                $storeShopCode = $shopCodes[$storeId] ?? $shopCode;
                $storeShopDiscount = 0.0;
                $storeAppliedCodes = [];

                if ($storeShopCode) {
                    $shC = Coupon::where('code', strtoupper($storeShopCode))->where('is_active', true)->first();
                    if ($shC && (! $shC->store_id || (int) $shC->store_id === (int) $storeId)
                        && (! $shC->expires_at || $shC->expires_at->isFuture())
                        && $storeSubtotal >= (float) $shC->min_order_value
                    ) {
                        $storeShopDiscount = (float) $shC->calculateDiscount($storeSubtotal);
                        if ($storeShopDiscount > 0) {
                            $storeAppliedCodes[] = $shC->code;
                            $appliedCoupons[] = $shC;
                        }
                    }
                }

                // Allocate platform discount proportionally
                $storePlatformDisc = $totalSubtotal > 0
                    ? round($totalPlatformDiscount * ($storeSubtotal / $totalSubtotal))
                    : 0.0;

                // Allocate points discount proportionally
                $storePointsDisc = $totalSubtotal > 0
                    ? round($totalPointsDiscount * ($storeSubtotal / $totalSubtotal))
                    : 0.0;

                if ($storePlatformDisc > 0 && $request->filled('platform_voucher_code')) {
                    $storeAppliedCodes[] = $request->input('platform_voucher_code');
                }
                if ($storeFreeshipDisc > 0 && $request->filled('freeship_code')) {
                    $storeAppliedCodes[] = $request->input('freeship_code');
                }
                if ($storePointsDisc > 0) {
                    $storeAppliedCodes[] = 'XU';
                }

                $storeTotalDiscount = $storeShopDiscount + $storePlatformDisc + $storePointsDisc;
                $storeGrandTotal = max(0.0, $storeSubtotal + $effectiveShipping - $storeTotalDiscount);
                $orderCode = 'SM-'.strtoupper(Str::random(8));

                $order = Order::create([
                    'store_id' => $storeId ?: null,
                    'checkout_group_id' => $checkoutGroupId,
                    'user_id' => auth()->id(),
                    'order_code' => $orderCode,
                    'status' => 'pending',
                    'payment_method' => $request->input('payment_method'),
                    'payment_status' => 'pending',
                    'subtotal' => $storeSubtotal,
                    'shipping_fee' => $effectiveShipping,
                    'discount_amount' => $storeTotalDiscount,
                    'coupon_code' => ! empty($storeAppliedCodes) ? implode(', ', array_unique($storeAppliedCodes)) : null,
                    'total' => $storeGrandTotal,
                    'shipping_address' => $shippingAddress,
                    'notes' => $request->input('notes'),
                ]);

                if ($storeId) {
                    app(FinancialSettlementService::class)->recordOrderCreation($order, [
                        'gross_merchandise_amount' => $storeSubtotal,
                        'shop_discount' => $storeShopDiscount,
                        'platform_discount' => $storePlatformDisc,
                        'freeship_discount' => $storeFreeshipDisc,
                        'points_discount' => $storePointsDisc,
                        'shipping_fee_paid' => $effectiveShipping,
                        'total_buyer_paid' => $storeGrandTotal,
                    ]);
                }

                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product->name,
                        'selected_variant' => $item->selected_variant,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'subtotal' => $item->subtotal,
                    ]);

                    app(PersonalizedRecommendationService::class)->track(auth()->user(), 'purchase', $item->product, [
                        'quantity' => $item->quantity,
                        'order_id' => $order->id,
                    ]);

                    if ($item->product) {
                        $item->product->decrement('stock', min($item->product->stock, $item->quantity));
                        $item->product->increment('sold_count', $item->quantity);

                        if (! empty($item->selected_variant)) {
                            $variantModel = $item->product->productVariants()
                                ->where(function ($q) use ($item) {
                                    $q->where('name', $item->selected_variant)
                                        ->orWhere('name', trim(str_replace(' | ', ' - ', $item->selected_variant)))
                                        ->orWhere('color', $item->selected_variant)
                                        ->orWhere('option', $item->selected_variant);
                                })->first();
                            if ($variantModel) {
                                $variantModel->decrement('stock', min($variantModel->stock, $item->quantity));
                            }
                        }
                    }
                }

                $createdOrders[] = $order;
            }

            // Increment usage count for unique applied coupons
            $uniqueCoupons = collect($appliedCoupons)->unique('id');
            foreach ($uniqueCoupons as $cp) {
                $cp->increment('used_count');
            }

            // Remove checked-out items from cart (skip for buy-now)
            if (! $isBuyNow && $cart) {
                $cart->items()->where('is_selected', true)->delete();
            }
        });

        // 1. Handle ZaloPay Sandbox Payment Gateway
        if ($request->input('payment_method') === 'zalopay') {
            $totalCheckoutAmount = (float) collect($createdOrders)->sum('total');
            $redirectUrl = $this->createZaloPayPaymentUrl($checkoutGroupId, $totalCheckoutAmount);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'payment_type' => 'zalopay',
                    'checkout_group_id' => $checkoutGroupId,
                    'redirect_url' => $redirectUrl,
                ]);
            }

            return redirect()->to($redirectUrl);
        }

        // 2. Handle COD or other payment methods
        $firstOrder = $createdOrders[0];
        $redirectUrl = route('checkout.success', [
            'order_code' => $firstOrder->order_code,
            'group' => $checkoutGroupId,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'payment_type' => $request->input('payment_method'),
                'order_code' => $firstOrder->order_code,
                'checkout_group_id' => $checkoutGroupId,
                'orders_count' => count($createdOrders),
                'redirect_url' => $redirectUrl,
            ]);
        }

        return redirect()->to($redirectUrl);
    }

    /**
     * Generate standard ZaloPay Sandbox Payment Gateway URL via v2 API.
     */
    protected function createZaloPayPaymentUrl(string $txnRef, float $totalAmount): string
    {
        $appId = (int) config('services.zalopay.app_id', 2553);
        $key1 = config('services.zalopay.key1', 'PcY4iZIKFCIdgZvA6ueMcMHHUbRLYjPL');
        $endpoint = config('services.zalopay.endpoint', 'https://sb-openapi.zalopay.vn/v2/create');

        $appTime = (int) round(microtime(true) * 1000);
        $appTransId = date('ymd').'_'.substr($txnRef, -6).'_'.rand(100, 999);

        $embedData = json_encode([
            'redirecturl' => route('checkout.zalopay-return', ['txn_ref' => $txnRef]),
            'txn_ref' => $txnRef,
        ]);

        $orderPayload = [
            'app_id' => $appId,
            'app_time' => $appTime,
            'app_trans_id' => $appTransId,
            'app_user' => (string) (auth()->id() ?? 'guest_buyer'),
            'item' => '[]',
            'embed_data' => $embedData,
            'amount' => max(1000, (int) $totalAmount),
            'description' => 'Thanh toan don hang ShopMart #'.$txnRef,
            'bank_code' => '',
        ];

        // Format: app_id|app_trans_id|app_user|amount|app_time|embed_data|item
        $signData = $orderPayload['app_id'].'|'
            .$orderPayload['app_trans_id'].'|'
            .$orderPayload['app_user'].'|'
            .$orderPayload['amount'].'|'
            .$orderPayload['app_time'].'|'
            .$orderPayload['embed_data'].'|'
            .$orderPayload['item'];

        $orderPayload['mac'] = hash_hmac('sha256', $signData, $key1);

        try {
            $response = Http::asForm()->timeout(10)->post($endpoint, $orderPayload);
            $result = $response->json();

            if (isset($result['return_code']) && $result['return_code'] === 1 && ! empty($result['order_url'])) {
                return $result['order_url'];
            }
        } catch (\Throwable $e) {
            Log::error('ZaloPay Create Order Exception: '.$e->getMessage());
        }

        // Fallback: If gateway network fails, redirect to success or friendly message
        return route('checkout.zalopay-return', [
            'txn_ref' => $txnRef,
            'status' => '1',
        ]);
    }

    /**
     * Handle return response callback from ZaloPay Sandbox.
     */
    public function zaloPayReturn(Request $request): RedirectResponse
    {
        $txnRef = (string) $request->input('txn_ref');
        $status = $request->input('status', '1');

        $orders = Order::where('checkout_group_id', $txnRef)->get();
        if ($orders->isEmpty()) {
            $singleOrder = Order::where('order_code', $txnRef)->first();
            if ($singleOrder) {
                $orders = collect([$singleOrder]);
            }
        }

        if ($orders->isEmpty()) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng cần thanh toán.');
        }

        // Status 1 means payment completed successfully on ZaloPay
        if ($status == '1' || $status == 1) {
            foreach ($orders as $o) {
                $o->update([
                    'payment_status' => 'paid',
                    'status' => 'processing',
                ]);
            }

            $firstOrder = $orders->first();
            $groupId = $firstOrder->checkout_group_id;

            return redirect()->route('checkout.success', [
                'order_code' => $firstOrder->order_code,
                'group' => $groupId,
            ])->with('success', 'Thanh toán trực tuyến ZaloPay thành công!');
        }

        foreach ($orders as $o) {
            $o->update([
                'payment_status' => 'failed',
            ]);
        }

        return redirect()->route('checkout.index')
            ->with('error', 'Thanh toán qua ZaloPay không thành công hoặc đã bị hủy.');
    }

    /**
     * Display order confirmation page.
     */
    public function success(Request $request, string $order_code): View
    {
        $groupId = $request->query('group');
        if ($groupId) {
            $orders = Order::with(['items.product.store', 'store', 'user'])
                ->where('checkout_group_id', $groupId)
                ->get();
        } else {
            $order = Order::with(['items.product.store', 'store', 'user'])
                ->where('order_code', $order_code)
                ->firstOrFail();

            if ($order->checkout_group_id) {
                $orders = Order::with(['items.product.store', 'store', 'user'])
                    ->where('checkout_group_id', $order->checkout_group_id)
                    ->get();
            } else {
                $orders = collect([$order]);
            }
        }

        if ($orders->isEmpty()) {
            abort(404);
        }

        $order = $orders->first();

        return view('order-success', compact('orders', 'order'));
    }
}
