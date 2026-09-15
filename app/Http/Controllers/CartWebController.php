<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CartWebController extends Controller
{
    /**
     * Retrieve or initialize the current user's cart based on session.
     */
    protected function getOrCreateCart(Request $request): Cart
    {
        if (auth()->check()) {
            $userCart = Cart::where('user_id', auth()->id())->first();
            if ($userCart) {
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
     * Display the cart and checkout page.
     */
    public function index(Request $request): View
    {
        $cart = $this->getOrCreateCart($request);
        $cart->load(['items.product.store', 'items.product.images']);

        // Group items by Store
        $groupedItems = $cart->items->groupBy(function ($item) {
            return $item->product?->store?->id ?? 0;
        });

        // Recommended items for "Có thể bạn cũng thích"
        $cartProductIds = $cart->items->pluck('product_id')->toArray();
        $recommendedProducts = Product::with(['store', 'category'])
            ->whereNotIn('id', $cartProductIds)
            ->inRandomOrder()
            ->take(6)
            ->get();

        // User addresses for Step 2 checkout
        $addresses = collect();
        $defaultAddress = null;
        if (auth()->check()) {
            $addresses = auth()->user()->addresses;
            $defaultAddress = auth()->user()->defaultAddress() ?? $addresses->first();
        }

        // Active coupons for voucher modal in Step 2
        $availableCoupons = Coupon::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderBy('min_order_value', 'asc')
            ->get();

        return view('cart', compact('cart', 'groupedItems', 'recommendedProducts', 'availableCoupons', 'addresses', 'defaultAddress'));

    }

    /**
     * Add an item to the cart via AJAX.
     */
    public function add(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1|max:999',
            'variant' => 'nullable|string|max:100',
            'buy_now' => 'nullable|boolean',
        ]);

        $quantity = $data['quantity'] ?? 1;
        $variant = $data['variant'] ?? null;
        $isBuyNow = $request->boolean('buy_now', false);
        $product = Product::findOrFail($data['product_id']);

        if (! auth()->check()) {
            $request->session()->put('pending_cart_action', [
                'action' => $isBuyNow ? 'buy_now' : 'add_to_cart',
                'product_id' => (int) $data['product_id'],
                'quantity' => $quantity,
                'variant' => $variant,
                'return_url' => $isBuyNow ? route('checkout.index') : (url()->previous() ?: route('product.detail', $product->slug)),
            ]);

            return response()->json([
                'success' => false,
                'requires_auth' => true,
                'redirect' => route('login'),
                'message' => 'Vui lòng đăng nhập để '.($isBuyNow ? 'mua sản phẩm' : 'thêm sản phẩm vào giỏ hàng').'.',
            ], 401);
        }

        if ($isBuyNow) {
            // Buy-now: do NOT touch the cart at all.
            // Store the item in session and redirect to checkout.
            $request->session()->put('buy_now_item', [
                'product_id' => $product->id,
                'product_name' => $product->name,
                'quantity' => $quantity,
                'unit_price' => (float) $product->price,
                'selected_variant' => $variant ?? ($product->brand ? $product->brand.' Chính hãng' : null),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Mua ngay!',
                'product_name' => $product->name,
                'redirect' => route('checkout.index', ['buy_now' => 1]),
            ]);
        }

        $cart = $this->getOrCreateCart($request);

        // Look for existing item with product_id and selected_variant
        $cartItem = $cart->items()
            ->where('product_id', $product->id)
            ->where('selected_variant', $variant)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->is_selected = true;
            $cartItem->save();
        } else {
            $cartItem = $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $product->price,
                'selected_variant' => $variant ?? ($product->brand ? $product->brand.' Chính hãng' : null),
                'is_selected' => true,
            ]);
        }

        // Reload cart
        $cart->load('items');

        return response()->json([
            'success' => true,
            'message' => 'Đã thêm vào giỏ hàng thành công!',
            'product_name' => $product->name,
            'cart_item_id' => $cartItem->id,
            'quantity' => $cartItem->quantity,
            'total_items' => $cart->total_items_count,
            'display_count' => $cart->display_count,
            'redirect' => null,
        ]);
    }

    /**
     * Update quantity of a cart item.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $cart = $this->getOrCreateCart($request);
        $item = $cart->items()->findOrFail($id);

        if ($request->has('action')) {
            $action = $request->input('action');
            $newQuantity = in_array($action, ['increment', 'plus'])
                ? $item->quantity + 1
                : max(0, $item->quantity - 1);
        } else {
            $data = $request->validate([
                'quantity' => 'required|integer|min:0|max:999',
            ]);
            $newQuantity = $data['quantity'];
        }

        if ($newQuantity <= 0) {
            $item->delete();
        } else {
            $item->quantity = $newQuantity;
            $item->save();
        }

        $cart->load('items.product');

        return response()->json([
            'success' => true,
            'total_items' => $cart->total_items_count,
            'display_count' => $cart->display_count,
            'selected_count' => $cart->selected_count,
            'selected_total' => $cart->selected_total,
            'formatted_selected_total' => $cart->formatted_selected_total,
            'formatted_original_selected_total' => $cart->formatted_original_selected_total,
            'savings_percent' => $cart->savings_percent,
            'item_quantity' => $item->quantity ?? 0,
            'item_subtotal' => isset($item->subtotal) ? number_format($item->subtotal, 0, ',', '.').'₫' : '0₫',
        ]);
    }

    /**
     * Toggle selection state of item(s).
     */
    public function toggleSelect(Request $request): JsonResponse
    {
        $cart = $this->getOrCreateCart($request);

        $type = $request->input('type'); // 'single', 'store', 'all'
        $itemId = $request->input('item_id');
        $storeId = $request->input('store_id');
        $selected = $request->has('is_selected') ? $request->boolean('is_selected') : $request->boolean('selected', true);

        if ($type === 'all') {
            $cart->items()->update(['is_selected' => $selected]);
        } elseif ($type === 'store' && $storeId) {
            $storeProductIds = Product::where('store_id', $storeId)->pluck('id');
            $cart->items()->whereIn('product_id', $storeProductIds)->update(['is_selected' => $selected]);
        } elseif ($itemId) {
            $item = $cart->items()->find($itemId);
            if ($item) {
                $item->is_selected = $selected;
                $item->save();
            }
        }

        $cart->load('items.product');

        return response()->json([
            'success' => true,
            'selected_count' => $cart->selected_count,
            'selected_total' => $cart->selected_total,
            'formatted_selected_total' => $cart->formatted_selected_total,
            'formatted_original_selected_total' => $cart->formatted_original_selected_total,
            'savings_percent' => $cart->savings_percent,
            'total_items' => $cart->total_items_count,
            'display_count' => $cart->display_count,
        ]);
    }

    /**
     * Remove a single item from the cart.
     */
    public function remove(Request $request, int $id): JsonResponse
    {
        $cart = $this->getOrCreateCart($request);
        $cart->items()->where('id', $id)->delete();
        $cart->load('items.product');

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
            'total_items' => $cart->total_items_count,
            'display_count' => $cart->display_count,
            'selected_count' => $cart->selected_count,
            'selected_total' => $cart->selected_total,
            'formatted_selected_total' => $cart->formatted_selected_total,
            'formatted_original_selected_total' => $cart->formatted_original_selected_total,
            'savings_percent' => $cart->savings_percent,
        ]);
    }

    /**
     * Remove all selected items from the cart.
     */
    public function removeSelected(Request $request): JsonResponse
    {
        $cart = $this->getOrCreateCart($request);
        $cart->items()->where('is_selected', true)->delete();
        $cart->load('items.product');

        return response()->json([
            'success' => true,
            'message' => 'Đã xóa các sản phẩm đã chọn',
            'total_items' => $cart->total_items_count,
            'display_count' => $cart->display_count,
            'selected_count' => 0,
            'selected_total' => 0,
            'formatted_selected_total' => '0₫',
            'formatted_original_selected_total' => '0₫',
            'savings_percent' => 0,
        ]);
    }

    /**
     * Return current cart item counts for header badge.
     */
    public function count(Request $request): JsonResponse
    {
        $cart = $this->getOrCreateCart($request);

        return response()->json([
            'total_items' => $cart->total_items_count,
            'display_count' => $cart->display_count,
        ]);
    }

    /**
     * Process checkout order placement (Step 2 -> Step 3).
     */
    public function processCheckout(Request $request): JsonResponse
    {
        if (! auth()->check()) {
            return response()->json([
                'success' => false,
                'requires_auth' => true,
                'redirect' => route('login'),
                'message' => 'Vui lòng đăng nhập để thanh toán đơn hàng.',
            ], 401);
        }

        $cart = $this->getOrCreateCart($request);
        $cart->load('items.product');

        $selectedItems = $cart->selected_items;
        if ($selectedItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng chọn ít nhất 1 sản phẩm để thanh toán.',
            ], 422);
        }

        $paymentMethod = $request->input('payment_method', 'cod');
        $user = auth()->user();

        // Address: prefer what the user explicitly sent from Step 2, fallback to default saved address
        if ($request->filled('recipient_name') && $request->filled('address_line')) {
            $shippingAddress = [
                'name' => $request->input('recipient_name'),
                'phone' => $request->input('phone', ''),
                'address' => $request->input('address_line'),
            ];
        } else {
            $defaultAddr = $user ? ($user->defaultAddress() ?? $user->addresses()->first()) : null;
            if ($defaultAddr) {
                $shippingAddress = [
                    'name' => $defaultAddr->recipient_name,
                    'phone' => $defaultAddr->phone,
                    'address' => $defaultAddr->address_line,
                ];
            } else {
                return response()->json([
                    'success' => false,
                    'require_address' => true,
                    'message' => 'Bắt buộc phải cài đặt địa chỉ nhận hàng trước khi thanh toán.',
                ], 422);
            }
        }

        if (empty($shippingAddress['name']) || empty($shippingAddress['address'])) {
            return response()->json([
                'success' => false,
                'require_address' => true,
                'message' => 'Bắt buộc phải cài đặt địa chỉ nhận hàng trước khi thanh toán.',
            ], 422);
        }

        // Coupon / discount
        $discountAmount = 0;
        $coupon = null;
        $couponCode = trim((string) $request->input('coupon_code', ''));
        if ($couponCode) {
            $coupon = Coupon::where('code', strtoupper($couponCode))
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->first();
            if ($coupon) {
                $subtotalForCoupon = (float) $cart->selected_total;
                if ($subtotalForCoupon >= (float) $coupon->min_order_value) {
                    $discountAmount = $coupon->discount_type === 'percent'
                        ? round($subtotalForCoupon * $coupon->discount_value / 100)
                        : (float) $coupon->discount_value;
                    if ($coupon->max_discount_value) {
                        $discountAmount = min($discountAmount, (float) $coupon->max_discount_value);
                    }
                }
            }
        }

        $groups = $selectedItems->groupBy(fn ($i) => $i->product?->store_id ?? 0);
        $checkoutGroupId = 'CKG-'.strtoupper(Str::random(10));
        $createdOrders = [];

        DB::transaction(function () use ($groups, $checkoutGroupId, $paymentMethod, $shippingAddress, $coupon, $cart, &$createdOrders) {
            $totalSubtotal = (float) $cart->selected_total;

            foreach ($groups as $storeId => $items) {
                $storeSubtotal = (float) $items->sum('subtotal');
                $orderCode = 'SM-'.strtoupper(dechex(time())).'-'.rand(100, 999);

                $storeDiscount = 0.0;
                if ($coupon) {
                    if ($coupon->store_id && (int) $coupon->store_id === (int) $storeId) {
                        $storeDiscount = $coupon->calculateDiscount($storeSubtotal);
                    } elseif (! $coupon->store_id && $totalSubtotal > 0) {
                        $ratio = $storeSubtotal / $totalSubtotal;
                        $totalDisc = $coupon->calculateDiscount($totalSubtotal);
                        $storeDiscount = round($totalDisc * $ratio);
                    }
                }

                $storeShipping = 0;
                $storeTotal = max(0.0, $storeSubtotal + $storeShipping - $storeDiscount);

                $order = Order::create([
                    'order_code' => $orderCode,
                    'checkout_group_id' => $checkoutGroupId,
                    'store_id' => $storeId ?: null,
                    'user_id' => auth()->id(),
                    'status' => 'pending',
                    'total' => $storeTotal,
                    'subtotal' => $storeSubtotal,
                    'shipping_fee' => $storeShipping,
                    'discount_amount' => $storeDiscount,
                    'payment_method' => $paymentMethod,
                    'shipping_address' => $shippingAddress,
                    'coupon_code' => $coupon ? $coupon->code : null,
                ]);

                foreach ($items as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item->product_id,
                        'product_name' => $item->product?->name ?? 'Sản phẩm',
                        'selected_variant' => $item->selected_variant,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'subtotal' => $item->subtotal,
                    ]);

                    if ($item->product) {
                        $item->product->decrement('stock', min($item->product->stock, $item->quantity));
                        $item->product->increment('sold_count', $item->quantity);
                    }
                }

                $createdOrders[] = $order;
            }

            if ($coupon) {
                $coupon->increment('used_count');
            }

            // Remove ordered items from cart
            $cart->items()->where('is_selected', true)->delete();
        });

        $firstOrder = $createdOrders[0] ?? null;
        $orderCode = $firstOrder ? $firstOrder->order_code : '';
        $redirectUrl = route('checkout.success', ['order_code' => $orderCode, 'group' => $checkoutGroupId]);

        return response()->json([
            'success' => true,
            'order_code' => $orderCode,
            'checkout_group_id' => $checkoutGroupId,
            'redirect_url' => $redirectUrl,
            'total_items' => $cart->fresh()->total_items_count,
            'display_count' => $cart->fresh()->display_count,
            'message' => 'Đặt hàng thành công!',
        ]);
    }

    /**
     * Update selected variant for a specific cart item.
     */
    public function updateVariant(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'variant' => 'required|string|max:150',
        ]);

        $cart = $this->getOrCreateCart($request);
        $item = $cart->items()->with('product')->findOrFail($id);
        $item->selected_variant = trim($data['variant']);
        $item->save();

        // Check if there's a matching color image
        $colorImage = null;
        $variants = $item->product?->variants;
        if (isset($variants['colors']) && is_array($variants['colors'])) {
            foreach ($variants['colors'] as $c) {
                if (is_array($c) && isset($c['label']) && str_contains($item->selected_variant, $c['label'])) {
                    $colorImage = $c['image'] ?? null;
                    break;
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã chuyển sang phân loại: '.$item->selected_variant,
            'variant' => $item->selected_variant,
            'image_url' => $colorImage,
        ]);
    }
}
