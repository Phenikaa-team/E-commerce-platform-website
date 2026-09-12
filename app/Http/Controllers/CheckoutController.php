<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $cart = $this->getCart($request);
        $cart->load(['items.product.store', 'items.product.images']);

        $selectedItems = $cart->items->where('is_selected', true);
        if ($selectedItems->isEmpty()) {
            if ($cart->items->isNotEmpty()) {
                // If items exist but none selected, select all for checkout
                $cart->items()->update(['is_selected' => true]);
                $cart->load(['items.product.store', 'items.product.images']);
                $selectedItems = $cart->items;
            } else {
                return redirect()->route('cart')->with('warning', 'Giỏ hàng của bạn đang trống. Hãy chọn sản phẩm để thanh toán!');
            }
        }

        $subtotal = (float) $cart->selected_total;
        $shippingFee = $subtotal >= 500000 ? 0.0 : 30000.0;
        $total = $subtotal + $shippingFee;

        // User addresses
        $addresses = collect();
        $defaultAddress = null;
        if (auth()->check()) {
            $addresses = auth()->user()->addresses;
            $defaultAddress = auth()->user()->defaultAddress() ?? $addresses->first();
        }

        // Active coupons available for suggestions & Shopee voucher modal
        $availableCoupons = Coupon::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->orderBy('min_order_value', 'asc')
            ->get();

        return view('checkout', compact(
            'cart',
            'selectedItems',
            'subtotal',
            'shippingFee',
            'total',
            'addresses',
            'defaultAddress',
            'availableCoupons'
        ));
    }

    /**
     * Validate and apply coupon code via AJAX.
     */
    public function applyCoupon(Request $request): JsonResponse
    {
        $code = trim((string) $request->input('code', ''));
        $subtotal = (float) $request->input('subtotal', 0);

        if ($subtotal <= 0) {
            $cart = $this->getCart($request);
            $subtotal = (float) $cart->selected_total;
            if ($subtotal <= 0) {
                $subtotal = (float) $cart->items->sum(fn ($i) => $i->quantity * $i->unit_price);
            }
        }

        if (empty($code)) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng nhập mã giảm giá.',
            ], 422);
        }

        $coupon = Coupon::where('code', strtoupper($code))->first();

        if (! $coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Mã giảm giá không tồn tại hoặc đã hết hạn.',
            ], 404);
        }

        $discount = $coupon->calculateDiscount($subtotal);
        if ($discount <= 0) {
            // If subtotal is still 0 (e.g. user checking voucher without items), provide estimate
            if ($subtotal <= 0) {
                $discount = $coupon->discount_type === 'percent' ? 25000.0 : min(50000.0, (float) $coupon->discount_value);
            } else {
                $msg = 'Mã giảm giá không áp dụng được cho đơn hàng này.';
                if ($subtotal < (float) $coupon->min_order_value) {
                    $msg = 'Đơn hàng tối thiểu để áp dụng mã là '.number_format((float) $coupon->min_order_value, 0, ',', '.').'₫';
                }

                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 422);
            }
        }

        $shippingFee = $subtotal >= 500000 ? 0.0 : 30000.0;
        $newTotal = max(0, $subtotal + $shippingFee - $discount);

        return response()->json([
            'success' => true,
            'message' => 'Áp dụng mã giảm giá thành công!',
            'coupon_code' => $coupon->code,
            'coupon_name' => $coupon->name,
            'discount_amount' => $discount,
            'formatted_discount' => '-'.number_format($discount, 0, ',', '.').'₫',
            'new_total' => $newTotal,
            'formatted_new_total' => number_format($newTotal, 0, ',', '.').'₫',
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
            'is_default' => 'nullable|boolean',
        ]);

        if (! auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để lưu sổ địa chỉ.',
            ], 401);
        }

        $isDefault = $request->boolean('is_default', false);
        if ($isDefault || auth()->user()->addresses()->count() === 0) {
            auth()->user()->addresses()->update(['is_default' => false]);
            $isDefault = true;
        }

        $address = auth()->user()->addresses()->create([
            'recipient_name' => $data['recipient_name'],
            'phone' => $data['phone'],
            'address_line' => $data['address_line'],
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
        $request->validate([
            'recipient_name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'address_line' => 'required|string|max:255',
            'payment_method' => 'required|in:cod,vnpay,momo',
            'coupon_code' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:500',
        ]);

        $cart = $this->getCart($request);
        $cart->load('items.product');

        $selectedItems = $cart->items->where('is_selected', true);
        if ($selectedItems->isEmpty()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Vui lòng chọn sản phẩm để thanh toán.'], 422);
            }

            return redirect()->route('cart')->with('error', 'Vui lòng chọn sản phẩm để thanh toán.');
        }

        $subtotal = (float) $cart->selected_total;
        $shippingFee = $subtotal >= 500000 ? 0.0 : 30000.0;
        $discountAmount = 0.0;
        $couponCode = null;

        if ($request->filled('coupon_code')) {
            $coupon = Coupon::where('code', strtoupper(trim($request->input('coupon_code'))))->first();
            if ($coupon) {
                $discountAmount = $coupon->calculateDiscount($subtotal);
                if ($discountAmount > 0) {
                    $couponCode = $coupon->code;
                }
            }
        }

        $total = max(0, $subtotal + $shippingFee - $discountAmount);
        $orderCode = 'SM-'.strtoupper(Str::random(8));

        $shippingAddress = [
            'name' => $request->input('recipient_name'),
            'phone' => $request->input('phone'),
            'address' => $request->input('address_line'),
        ];

        // Save order inside DB transaction
        $order = DB::transaction(function () use ($request, $orderCode, $subtotal, $shippingFee, $discountAmount, $couponCode, $total, $shippingAddress, $selectedItems, $cart) {
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_code' => $orderCode,
                'status' => 'pending',
                'payment_method' => $request->input('payment_method'),
                'payment_status' => 'pending',
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'discount_amount' => $discountAmount,
                'coupon_code' => $couponCode,
                'total' => $total,
                'shipping_address' => $shippingAddress,
                'notes' => $request->input('notes'),
            ]);

            foreach ($selectedItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'selected_variant' => $item->selected_variant,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->subtotal,
                ]);

                // Decrement product stock safely
                if ($item->product) {
                    $item->product->decrement('stock', min($item->product->stock, $item->quantity));
                    $item->product->increment('sold_count', $item->quantity);
                }
            }

            // If coupon applied, increment usage count
            if ($couponCode) {
                Coupon::where('code', $couponCode)->increment('used_count');
            }

            // Remove checked-out items from cart
            $cart->items()->where('is_selected', true)->delete();

            return $order;
        });

        // 1. Handle VNPay Sandbox Payment Gateway
        if ($order->payment_method === 'vnpay') {
            $vnpayUrl = $this->createVnPayPaymentUrl($order);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'payment_type' => 'vnpay',
                    'redirect_url' => $vnpayUrl,
                ]);
            }

            return redirect()->away($vnpayUrl);
        }

        // 2. Handle COD or MoMo (mock)
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'payment_type' => 'cod',
                'order_code' => $order->order_code,
                'redirect_url' => route('checkout.success', $order->order_code),
            ]);
        }

        return redirect()->route('checkout.success', $order->order_code);
    }

    /**
     * Generate standard VNPay Sandbox Payment URL using HMAC-SHA512.
     */
    protected function createVnPayPaymentUrl(Order $order): string
    {
        $vnp_TmnCode = config('services.vnpay.tmn_code', '2QXUI457');
        $vnp_HashSecret = config('services.vnpay.hash_secret', 'RAIQUIOWGHGUDGUTRHGUBVTNY0987YTR');
        $vnp_Url = config('services.vnpay.url', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        $vnp_Returnurl = route('checkout.vnpay-return');

        $vnp_TxnRef = $order->order_code;
        $vnp_OrderInfo = 'Thanh toan don hang ShopMart #'.$order->order_code;
        $vnp_OrderType = 'billpayment';
        $vnp_Amount = (int) ($order->total * 100); // VNPay requires multiplying by 100
        $vnp_Locale = 'vn';
        $vnp_IpAddr = request()->ip() ?: '127.0.0.1';

        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => $vnp_TmnCode,
            'vnp_Amount' => $vnp_Amount,
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $vnp_IpAddr,
            'vnp_Locale' => $vnp_Locale,
            'vnp_OrderInfo' => $vnp_OrderInfo,
            'vnp_OrderType' => $vnp_OrderType,
            'vnp_ReturnUrl' => $vnp_Returnurl,
            'vnp_TxnRef' => $vnp_TxnRef,
        ];

        ksort($inputData);
        $query = '';
        $i = 0;
        $hashdata = '';
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&'.urlencode($key).'='.urlencode($value);
            } else {
                $hashdata .= urlencode($key).'='.urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key).'='.urlencode($value).'&';
        }

        $vnp_Url = $vnp_Url.'?'.$query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash='.$vnpSecureHash;
        }

        return $vnp_Url;
    }

    /**
     * Handle return response callback from VNPay Sandbox.
     */
    public function vnpayReturn(Request $request): RedirectResponse
    {
        $vnp_HashSecret = config('services.vnpay.hash_secret', 'RAIQUIOWGHGUDGUTRHGUBVTNY0987YTR');
        $inputData = [];
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == 'vnp_') {
                $inputData[$key] = $value;
            }
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash']);
        unset($inputData['vnp_SecureHashType']);

        ksort($inputData);
        $i = 0;
        $hashData = '';
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&'.urlencode($key).'='.urlencode($value);
            } else {
                $hashData .= urlencode($key).'='.urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $orderCode = $request->input('vnp_TxnRef');
        $order = Order::where('order_code', $orderCode)->first();

        if (! $order) {
            return redirect()->route('home')->with('error', 'Không tìm thấy đơn hàng cần thanh toán.');
        }

        // Check if hash matches and response code is 00 (Success)
        if ($secureHash === $vnp_SecureHash) {
            if ($request->input('vnp_ResponseCode') == '00') {
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing',
                ]);

                return redirect()->route('checkout.success', $order->order_code)
                    ->with('success', 'Thanh toán trực tuyến VNPay thành công!');
            } else {
                $order->update([
                    'payment_status' => 'failed',
                ]);

                return redirect()->route('checkout.index')
                    ->with('error', 'Thanh toán qua VNPay không thành công hoặc đã bị hủy (Mã lỗi: '.$request->input('vnp_ResponseCode').').');
            }
        }

        return redirect()->route('checkout.index')
            ->with('error', 'Chữ ký số VNPay không hợp lệ. Giao dịch đã bị từ chối.');
    }

    /**
     * Display order confirmation page.
     */
    public function success(string $order_code): View
    {
        $order = Order::with(['items.product.store', 'user'])
            ->where('order_code', $order_code)
            ->firstOrFail();

        return view('order-success', compact('order'));
    }
}
