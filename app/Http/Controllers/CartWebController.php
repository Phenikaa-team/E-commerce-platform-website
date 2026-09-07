<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartWebController extends Controller
{
    /**
     * Retrieve or initialize the current user's cart based on session.
     */
    protected function getOrCreateCart(Request $request): Cart
    {
        $sessionId = $request->session()->getId();
        $cart = Cart::firstOrCreate(['session_id' => $sessionId]);

        // If cart is completely empty, populate initial items matching user's concept mockup
        if ($cart->items()->count() === 0) {
            $this->seedInitialMockupItems($cart);
        }

        return $cart;
    }

    /**
     * Seeds default items to match the user's uploaded mockup for a rich first-load experience.
     */
    protected function seedInitialMockupItems(Cart $cart): void
    {
        $appleStore = Store::where('name', 'like', '%Apple%')->first();
        $samsungStore = Store::where('name', 'like', '%Samsung%')->first();

        // 1. iPhone 15 Pro Max
        $iphone = Product::where('name', 'like', '%iPhone 15 Pro Max%')->first();
        if ($iphone) {
            $cart->items()->create([
                'product_id' => $iphone->id,
                'quantity' => 1,
                'unit_price' => $iphone->price,
                'selected_variant' => 'Titan Đen | 256GB',
                'is_selected' => true,
            ]);
        }

        // 2. AirPods Pro 2 (find or create)
        $airpods = Product::where('name', 'like', '%AirPods%')->first();
        if (! $airpods && $appleStore) {
            $airpods = Product::create([
                'store_id' => $appleStore->id,
                'name' => 'Tai nghe Apple AirPods Pro 2 MagSafe',
                'slug' => 'airpods-pro-2-magsafe',
                'description' => 'Tai nghe chống ồn chủ động đỉnh cao, âm thanh vòm sống động.',
                'price' => 5490000,
                'original_price' => 6990000,
                'discount_percent' => 21,
                'stock' => 50,
                'sold_count' => 1200,
                'rating' => 4.9,
                'is_mall' => true,
                'status' => 'active',
                'main_image_url' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?auto=format&fit=crop&w=400&q=80',
                'variants' => [
                    'colors' => [
                        ['label' => 'Trắng Tinh Tế', 'image' => 'https://images.unsplash.com/photo-1590658268037-6bf12165a8df?auto=format&fit=crop&w=400&q=80'],
                        ['label' => 'Đen Nhám Edition', 'image' => 'https://images.unsplash.com/photo-1572536147248-ac59a8abfa4b?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'options' => ['Bản Tiêu Chuẩn MagSafe', 'Bản Cổng USB-C 2024'],
                ],
            ]);
        }
        if ($airpods) {
            $cart->items()->create([
                'product_id' => $airpods->id,
                'quantity' => 1,
                'unit_price' => $airpods->price,
                'selected_variant' => 'Trắng Tinh Tế | Bản Tiêu Chuẩn MagSafe',
                'is_selected' => true,
            ]);
        }

        // 3. Galaxy Watch6 40mm (find or create)
        $watch = Product::where('name', 'like', '%Galaxy Watch%')->first();
        if (! $watch && $samsungStore) {
            $watch = Product::create([
                'store_id' => $samsungStore->id,
                'name' => 'Đồng hồ thông minh Galaxy Watch6 40mm',
                'slug' => 'galaxy-watch6-40mm',
                'description' => 'Theo dõi sức khỏe và giấc ngủ chuyên sâu, thiết kế viền mỏng tinh tế.',
                'price' => 4490000,
                'original_price' => 6490000,
                'discount_percent' => 31,
                'stock' => 35,
                'sold_count' => 840,
                'rating' => 4.8,
                'is_mall' => true,
                'status' => 'active',
                'main_image_url' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=400&q=80',
                'variants' => [
                    'colors' => [
                        ['label' => 'Graphite Đen', 'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=400&q=80'],
                        ['label' => 'Silver Bạc', 'image' => 'https://images.unsplash.com/photo-1508685096489-7aacd43bd3b1?auto=format&fit=crop&w=400&q=80'],
                        ['label' => 'Gold Vàng Kem', 'image' => 'https://images.unsplash.com/photo-1546868871-7041f2a55e12?auto=format&fit=crop&w=400&q=80'],
                    ],
                    'options' => ['40mm Bluetooth', '40mm LTE (eSIM)', '44mm Bluetooth'],
                ],
            ]);
        }
        if ($watch) {
            $cart->items()->create([
                'product_id' => $watch->id,
                'quantity' => 1,
                'unit_price' => $watch->price,
                'selected_variant' => 'Graphite Đen | 40mm Bluetooth',
                'is_selected' => true,
            ]);
        }
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

        return view('cart', compact('cart', 'groupedItems', 'recommendedProducts'));
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
        ]);

        $quantity = $data['quantity'] ?? 1;
        $variant = $data['variant'] ?? null;

        $cart = $this->getOrCreateCart($request);
        $product = Product::findOrFail($data['product_id']);

        // Look for existing item with identical product and variant
        $cartItem = $cart->items()
            ->where('product_id', $product->id)
            ->where('selected_variant', $variant)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
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
            'total_items' => $cart->total_items_count,
            'display_count' => $cart->display_count,
        ]);
    }

    /**
     * Update quantity of a cart item.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $data = $request->validate([
            'quantity' => 'required|integer|min:0|max:999',
        ]);

        $cart = $this->getOrCreateCart($request);
        $item = $cart->items()->findOrFail($id);

        if ($data['quantity'] <= 0) {
            $item->delete();
        } else {
            $item->quantity = $data['quantity'];
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
        $selected = $request->boolean('selected', true);

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
        $cart = $this->getOrCreateCart($request);
        $cart->load('items.product');

        $selectedItems = $cart->selected_items;
        if ($selectedItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng chọn ít nhất 1 sản phẩm để thanh toán.',
            ], 422);
        }

        $paymentMethod = $request->input('payment_method', 'wallet');
        $shippingAddress = [
            'name' => 'Nguyễn Văn A',
            'phone' => '0123 456 789',
            'address' => 'Số 123 Đường Nguyễn Huệ, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh',
        ];

        // Create Order
        $order = Order::create([
            'status' => 'confirmed',
            'total' => $cart->selected_total,
            'shipping_address' => $shippingAddress,
        ]);

        foreach ($selectedItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'subtotal' => $item->subtotal,
            ]);
        }

        // Remove ordered items from cart
        $cart->items()->where('is_selected', true)->delete();
        $cart->load('items');

        return response()->json([
            'success' => true,
            'order_code' => 'SM-'.strtoupper(dechex(time())).'-'.rand(100, 999),
            'total_items' => $cart->total_items_count,
            'display_count' => $cart->display_count,
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
