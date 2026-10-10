<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Services\PersonalizedRecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatAiController extends Controller
{
    /**
     * Handle incoming chat requests to Groq AI.
     */
    public function chat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'history' => ['nullable', 'array', 'max:10'],
            'history.*.role' => ['required_with:history', 'string', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string', 'max:5000'],
        ]);

        $apiKey = config('services.groq.api_key');
        if (! $apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Trợ lý AI chưa được kích hoạt API key trên hệ thống.',
            ], 503);
        }

        $user = auth()->user();
        $userRole = 'customer';
        if ($user) {
            if ($user->isAdmin()) {
                $userRole = 'admin';
            } elseif ($user->isSeller()) {
                $userRole = 'seller';
            }
        }

        $userMessage = mb_strtolower($validated['message']);

        // 1. DỮ LIỆU DANH MỤC & THƯƠNG HIỆU CỦA WEBSITE
        $topCategories = Cache::remember('shopmart_ai_categories', 3600, function () {
            return Category::whereNull('parent_id')
                ->with(['children:id,parent_id,name,slug'])
                ->select(['id', 'name', 'slug'])
                ->take(12)
                ->get()
                ->map(function ($c) {
                    $subs = $c->children->pluck('name')->implode(', ');

                    return "- {$c->name} (/catalog/category/{$c->slug})".($subs ? " (Gồm: {$subs})" : '');
                })
                ->implode("\n");
        });

        // 2. DỮ LIỆU MÃ GIẢM GIÁ / VOUCHERS ĐANG CÓ HIỆU LỰC TRÊN SÀN
        $activeCoupons = Cache::remember('shopmart_ai_active_coupons', 600, function () {
            return Coupon::where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                })
                ->where(function ($q) {
                    $q->whereNull('usage_limit')->orWhereRaw('used_count < usage_limit');
                })
                ->take(8)
                ->get()
                ->map(function ($cp) {
                    $typeStr = $cp->discount_type === 'percent' ? "Giảm {$cp->discount_value}%" : 'Giảm '.number_format($cp->discount_value, 0, ',', '.').'đ';
                    $minStr = $cp->min_order_value > 0 ? 'Đơn từ '.number_format($cp->min_order_value, 0, ',', '.').'đ' : 'Mọi đơn hàng';

                    return "- Mã [{$cp->code}]: {$typeStr}, {$minStr}";
                })
                ->implode("\n");
        });

        // 3. TÌM KIẾM SẢN PHẨM PHÙ HỢP THEO TỪ KHÓA (RAG)
        $productQuery = Product::query()
            ->select(['id', 'name', 'slug', 'price', 'original_price', 'rating', 'description', 'brand'])
            ->where('status', 'active');

        $keywords = array_filter(
            preg_split('/[\s,\?!]+/u', $userMessage),
            fn ($w) => mb_strlen($w) >= 2 && ! in_array($w, ['tôi', 'bạn', 'mình', 'muốn', 'mua', 'nào', 'sao', 'gì', 'cho', 'với', 'các', 'những', 'được', 'không', 'này', 'nên', 'cần', 'thế'])
        );

        $matchedProducts = collect();
        if (! empty($keywords)) {
            $matchedQuery = clone $productQuery;
            $matchedQuery->where(function ($q) use ($keywords) {
                foreach ($keywords as $kw) {
                    $q->orWhere('name', 'ilike', "%{$kw}%")
                        ->orWhere('description', 'ilike', "%{$kw}%")
                        ->orWhere('brand', 'ilike', "%{$kw}%");
                }
            });
            $matchedProducts = $matchedQuery->take(10)->get();
        }

        if ($matchedProducts->count() < 8) {
            $fallbackProducts = (clone $productQuery)
                ->whereNotIn('id', $matchedProducts->pluck('id'))
                ->orderByDesc('reviews_count')
                ->take(8 - $matchedProducts->count())
                ->get();
            $matchedProducts = $matchedProducts->concat($fallbackProducts);
        }

        $sampleProducts = $matchedProducts
            ->map(fn ($p) => "- {$p->name} (Giá: ".number_format($p->price, 0, ',', '.')."đ, Đánh giá: {$p->rating}⭐, Link: /product/{$p->slug})")
            ->implode("\n");

        // 4. THÔNG TIN BỔ SUNG THEO ROLE (Customer / Seller / Admin)
        $roleContext = '';
        $startOfMonth = now()->startOfMonth();
        $monthName = now()->format('m/Y');

        if ($userRole === 'admin') {
            $totalUsers = User::count();
            $totalOrders = Order::count();
            $totalProducts = Product::count();

            // Tổng GMV toàn sàn (đơn không hủy)
            $totalGmv = Order::where('status', '!=', 'cancelled')->sum('total');
            // Doanh thu / GMV tháng này
            $thisMonthGmv = Order::where('status', '!=', 'cancelled')
                ->where('created_at', '>=', $startOfMonth)
                ->sum('total');
            $thisMonthOrders = Order::where('created_at', '>=', $startOfMonth)->count();
            // Hoa hồng nền tảng ước tính (~5.5%)
            $platformCommission = $thisMonthGmv * 0.055;
            $completedOrdersCount = Order::where('status', 'completed')->count();
            $aov = $completedOrdersCount > 0 ? ($totalGmv / $completedOrdersCount) : 0;

            $totalGmvFmt = number_format($totalGmv, 0, ',', '.').'đ';
            $thisMonthGmvFmt = number_format($thisMonthGmv, 0, ',', '.').'đ';
            $commissionFmt = number_format($platformCommission, 0, ',', '.').'đ';
            $aovFmt = number_format($aov, 0, ',', '.').'đ';

            $roleContext = <<<ADMIN
BẠN ĐANG HỖ TRỢ: QUẢN TRỊ VIÊN HỆ THỐNG (ADMIN)
Tên Admin: {$user->name}
Số liệu tổng quan & Tài chính toàn sàn (Cập nhật thời gian thực):
- Tổng giá trị giao dịch toàn sàn (Tổng GMV): {$totalGmvFmt}
- Doanh thu / GMV Tháng này ({$monthName}): {$thisMonthGmvFmt} (Tổng {$thisMonthOrders} đơn phát sinh)
- Thu nhập hoa hồng sàn tạm tính tháng này (~5.5%): {$commissionFmt}
- Giá trị đơn hàng trung bình (AOV): {$aovFmt}
- Tổng số đơn hàng toàn sàn: {$totalOrders} đơn
- Tổng số tài khoản người dùng: {$totalUsers}
- Tổng số sản phẩm trong cơ sở dữ liệu: {$totalProducts}
Link điều hành nhanh dành cho Admin:
- Báo cáo tài chính & doanh thu sàn: /admin/revenue
- Dashboard toàn sàn: /admin/dashboard
- Quản lý sản phẩm: /admin/products
- Quản lý đơn hàng: /admin/orders
- Quản lý người dùng: /admin/users
ADMIN;
        } elseif ($userRole === 'seller' && $user->store) {
            $store = $user->store;
            $storeProductsCount = Product::where('store_id', $store->id)->count();
            $pendingOrders = Order::whereHas('items.product', fn ($q) => $q->where('store_id', $store->id))
                ->where('status', 'pending')
                ->count();

            // Doanh thu shop tháng này
            $sellerThisMonthRev = OrderItem::whereHas('product', fn ($q) => $q->where('store_id', $store->id))
                ->whereHas('order', fn ($q) => $q->where('status', '!=', 'cancelled')->where('created_at', '>=', $startOfMonth))
                ->selectRaw('sum(subtotal) as total_rev')
                ->value('total_rev') ?? 0;

            $sellerTotalRev = OrderItem::whereHas('product', fn ($q) => $q->where('store_id', $store->id))
                ->whereHas('order', fn ($q) => $q->where('status', '!=', 'cancelled'))
                ->selectRaw('sum(subtotal) as total_rev')
                ->value('total_rev') ?? 0;

            $sellerMonthFmt = number_format($sellerThisMonthRev, 0, ',', '.').'đ';
            $sellerTotalFmt = number_format($sellerTotalRev, 0, ',', '.').'đ';

            $roleContext = <<<SELLER
BẠN ĐANG HỖ TRỢ: NGƯỜI BÁN HÀNG (SELLER)
Chủ shop: {$user->name} - Gian hàng: {$store->name}
Thống kê cửa hàng của bạn:
- Doanh thu shop Tháng này ({$monthName}): {$sellerMonthFmt}
- Tổng doanh thu tích lũy của shop: {$sellerTotalFmt}
- Số sản phẩm đang bán: {$storeProductsCount}
- Đơn hàng đang chờ xử lý: {$pendingOrders}
Link thao tác nhanh dành cho Người bán:
- Báo cáo doanh thu & ví shop: /seller/revenue
- Bàn làm việc người bán: /seller/dashboard
- Quản lý danh sách sản phẩm: /seller/products
- Quản lý đơn hàng cần giao: /seller/orders
SELLER;
        } elseif ($user) {
            $roleContext = "Khách hàng hiện tại: {$user->name} (Đã đăng nhập). Link tra cứu đơn của bạn: /orders/track";
        }

        // 5. CHÍNH SÁCH VÀ QUY CHẾ SHOPMART
        $policies = <<<'POLICIES'
Chính sách & Hướng dẫn trên ShopMart:
- Đổi trả: Hỗ trợ miễn phí đổi trả trong vòng 15 ngày đối với sản phẩm lỗi từ nhà sản xuất hoặc sai hàng.
- Vận chuyển: Giao hàng toàn quốc với các đơn vị vận chuyển uy tín (Nhanh, Tiết kiệm, Hỏa tốc). Nhiều mã Freeship khi đạt giá trị đơn tối thiểu.
- Thanh toán: Hỗ trợ COD (tiền mặt), Thẻ ngân hàng/VNPAY/Ví điện tử, Ví tiền ShopMart.
- Bán hàng: Bất kỳ ai cũng có thể mở gian hàng tại link: /seller/register
- Theo dõi đơn hàng: Tra cứu trạng thái tại link: /orders/track
POLICIES;

        $systemPrompt = <<<PROMPT
Bạn là ShopMart AI - Trợ lý thông minh cao cấp của sàn thương mại điện tử ShopMart.
Bạn có khả năng hỗ trợ mọi đối tượng: Khách hàng mua sắm, Chủ cửa hàng (Seller) và Quản trị viên (Admin).

[DỮ LIỆU ĐANG CÓ TRÊN SÀN]:
{$roleContext}

* Danh mục nổi bật trên ShopMart:
{$topCategories}

* Mã giảm giá / Vouchers đang áp dụng:
{$activeCoupons}

* Sản phẩm tiêu biểu / khớp tìm kiếm:
{$sampleProducts}

* Quy định & Chính sách:
{$policies}

[QUY TẮC PHẢN HỒI]:
1. Khi khách hỏi về sản phẩm: tư vấn đúng nhu cầu, BẮT BUỘC chèn đường link dạng markdown [Tên sản phẩm](/product/slug) kèm giá tiền và điểm nổi bật.
2. Khi khách hỏi về voucher / mã giảm giá / khuyến mãi:
   - TUYỆT ĐỐI KHÔNG DÙNG BẢNG RỘNG NHIỀU CỘT (dễ bị vỡ giao diện trên khung chat).
   - Hãy trình bày dưới dạng DANH SÁCH GẠCH ĐẦU DÒNG kèm thẻ mã `CODE` rõ ràng, ví dụ:
     - 🏷️ Mã `VOUCHER10`: Giảm 10% (Áp dụng cho đơn từ 200.000đ).
     - 🚚 Mã `FREESHIP`: Miễn phí vận chuyển toàn quốc.
   - Hướng dẫn khách áp dụng mã tại bước thanh toán/giỏ hàng.
3. Khi khách hỏi về ngành hàng: hướng dẫn vào xem danh mục phù hợp theo link [Tên danh mục](/catalog/category/slug).
4. Khi trò chuyện với Người Bán (Seller) hoặc Admin, hoặc khi được yêu cầu "Báo cáo", "Thống kê":
   - Chỉ dùng BẢNG GỌN 2 CỘT (Chỉ số | Giá trị) để vừa vặn khung chat:
     | Chỉ số / Hạng mục | Giá trị |
     | :--- | :--- |
     | 👥 Người dùng | **10** |
     | 📦 Đơn hàng | **12** |
   - Tiêu đề dùng ### Icon Tiêu đề báo cáo.
   - Các lưu ý quan trọng để trong trích dẫn `> 💡 Lưu ý:...`.
   - Cung cấp sẵn các đường link nhanh để họ bấm truy cập trực tiếp.
5. Giao tiếp lịch sự, cấu trúc rõ ràng, icon sinh động, tiếng Việt chuẩn mực. Không bịa đặt đường link bên ngoài ShopMart.
PROMPT;

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        // Append recent conversation history
        if (! empty($validated['history'])) {
            foreach ($validated['history'] as $item) {
                $messages[] = [
                    'role' => $item['role'],
                    'content' => $item['content'],
                ];
            }
        }

        // Append current user message
        $messages[] = [
            'role' => 'user',
            'content' => $validated['message'],
        ];

        try {
            $response = Http::withToken($apiKey)
                ->timeout(20)
                ->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => config('services.groq.model', 'openai/gpt-oss-120b'),
                    'messages' => $messages,
                    'temperature' => 0.7,
                    'max_tokens' => 800,
                ]);

            if ($response->successful()) {
                $reply = $response->json('choices.0.message.content') ?? 'Xin lỗi, tôi chưa thể trả lời lúc này.';

                app(PersonalizedRecommendationService::class)->track(
                    auth()->user(),
                    'chat_inquiry',
                    null,
                    ['message' => mb_substr($validated['message'], 0, 100)]
                );

                return response()->json([
                    'success' => true,
                    'reply' => $reply,
                ]);
            }

            Log::error('Groq AI API Error: '.$response->body());

            return response()->json([
                'success' => false,
                'message' => 'Hệ thống AI đang bận, vui lòng thử lại sau giây lát.',
            ], 500);
        } catch (\Throwable $e) {
            Log::error('ChatAiController exception: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Không thể kết nối đến máy chủ AI: '.$e->getMessage(),
            ], 500);
        }
    }
}
