<?php

namespace App\Http\Controllers;

use App\Models\SupportConversation;
use App\Models\SupportMessage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SupportChatController extends Controller
{
    public function index(Request $request): View
    {
        if ($request->user() && $request->user()->isAdmin()) {
            return $this->adminIndex();
        }

        $faqs = [
            'orders' => [
                'title' => 'Đơn hàng & Mua sắm',
                'icon' => '📦',
                'items' => [
                    [
                        'q' => 'Làm sao để theo dõi lộ trình và trạng thái đơn hàng của tôi?',
                        'a' => 'Bạn có thể vào mục "Theo dõi đơn hàng" trên thanh điều hướng hoặc truy cập trang cá nhân > "Đơn mua". Tại đây mọi thông tin như chuẩn bị hàng, đang giao, thời gian dự kiến đều được cập nhật theo thời gian thực.',
                        'prompt' => 'Hướng dẫn tôi cách kiểm tra và theo dõi lộ trình đơn hàng đã đặt',
                    ],
                    [
                        'q' => 'Tôi có thể hủy đơn hàng sau khi đã thanh toán không?',
                        'a' => 'Bạn có thể hủy đơn trực tiếp nếu đơn hàng đang ở trạng thái "Chờ xác nhận" hoặc "Chờ xử lý". Nếu shop đã bàn giao hàng cho đơn vị vận chuyển, bạn cần liên hệ shop hoặc chờ đơn đến để xử lý hoàn hàng.',
                        'prompt' => 'Chính sách và cách hủy đơn hàng trên ShopMart như thế nào?',
                    ],
                    [
                        'q' => 'Làm cách nào để áp dụng mã giảm giá và mã miễn phí vận chuyển?',
                        'a' => 'Tại bước Giỏ hàng hoặc Thanh toán, bạn chỉ cần chọn mục "Voucher ShopMart" để tick chọn mã giảm giá và mã Freeship khả dụng phù hợp với giá trị đơn hàng.',
                        'prompt' => 'Cách áp dụng mã voucher và mã miễn phí vận chuyển khi thanh toán',
                    ],
                ],
            ],
            'shipping' => [
                'title' => 'Vận chuyển & Giao nhận',
                'icon' => '🚚',
                'items' => [
                    [
                        'q' => 'Thời gian giao hàng tiêu chuẩn của ShopMart là bao lâu?',
                        'a' => 'Nội thành Hà Nội & TP.HCM thường nhận hàng từ 1-2 ngày làm việc. Các tỉnh thành khác dao động từ 2-4 ngày làm việc tuỳ theo vị trí kho của nhà bán.',
                        'prompt' => 'Thời gian và các gói giao hàng trên ShopMart',
                    ],
                    [
                        'q' => 'Tôi có được đồng kiểm (kiểm tra hàng trước khi nhận) không?',
                        'a' => 'ShopMart hỗ trợ đồng kiểm ngoại quan (kiểm tra tình trạng hộp, số lượng sản phẩm) cùng nhân viên giao hàng mà không cần bóc tem niêm phong sản phẩm của hãng.',
                        'prompt' => 'Quy định đồng kiểm hàng hóa khi nhận hàng tại ShopMart',
                    ],
                ],
            ],
            'payment' => [
                'title' => 'Thanh toán & Hoàn tiền',
                'icon' => '💳',
                'items' => [
                    [
                        'q' => 'ShopMart hỗ trợ những phương thức thanh toán nào?',
                        'a' => 'Hệ thống hỗ trợ thanh toán khi nhận hàng (COD), Chuyển khoản ngân hàng qua mã QR VietQR tự động, và các cổng thanh toán điện tử an toàn.',
                        'prompt' => 'Các phương thức thanh toán khả dụng trên ShopMart',
                    ],
                    [
                        'q' => 'Nếu thanh toán online bị lỗi hoặc trừ tiền mà đơn chưa tạo thì sao?',
                        'a' => 'Hệ thống đối soát tự động sẽ kiểm tra giao dịch trong vòng 15 phút. Nếu sau 15 phút chưa cập nhật, bạn hãy nhấn "Liên hệ Admin" để được nhân viên hỗ trợ hoàn tiền hoặc kích hoạt đơn.',
                        'prompt' => 'Xử lý sự cố thanh toán bị trừ tiền nhưng đơn hàng chưa cập nhật',
                    ],
                ],
            ],
            'returns' => [
                'title' => 'Đổi trả & Bảo hành',
                'icon' => '🛡️',
                'items' => [
                    [
                        'q' => 'Thời hạn và điều kiện yêu cầu Trả hàng / Hoàn tiền là gì?',
                        'a' => 'Bạn có thể gửi yêu cầu Trả hàng/Hoàn tiền trong vòng 7 ngày kể từ khi đơn hàng giao thành công đối với trường hợp hàng lỗi, thiếu phụ kiện hoặc không đúng mô tả.',
                        'prompt' => 'Chính sách và thủ tục yêu cầu trả hàng hoàn tiền',
                    ],
                    [
                        'q' => 'Quy trình hoàn tiền diễn ra trong bao lâu?',
                        'a' => 'Sau khi shop hoặc ShopMart nhận lại hàng và xác nhận tình trạng, tiền sẽ được hoàn về tài khoản ngân hàng hoặc ví gốc của bạn trong vòng 24 - 48 giờ làm việc.',
                        'prompt' => 'Thời gian nhận lại tiền khi trả hàng thành công',
                    ],
                ],
            ],
            'account' => [
                'title' => 'Tài khoản & Gian hàng',
                'icon' => '🏪',
                'items' => [
                    [
                        'q' => 'Tôi muốn mở gian hàng kinh doanh trên ShopMart thì làm thế nào?',
                        'a' => 'Bạn chỉ cần truy cập "Kênh Người Bán" hoặc mục "Đăng ký mở gian hàng" trong menu tài khoản, điền tên shop, mã số thuế/CCCD và xác minh OTP là có thể bắt đầu đăng bán.',
                        'prompt' => 'Hướng dẫn thủ tục và các bước đăng ký mở gian hàng trên ShopMart',
                    ],
                    [
                        'q' => 'Làm sao để đổi số điện thoại hoặc mật khẩu tài khoản?',
                        'a' => 'Bạn vào trang "Tài khoản của tôi" > "Thông tin cá nhân" hoặc chọn "Đổi mật khẩu" để cập nhật thông tin bảo mật an toàn.',
                        'prompt' => 'Cách thay đổi mật khẩu và cập nhật thông tin cá nhân',
                    ],
                ],
            ],
        ];

        return view('support.faq', [
            'faqs' => $faqs,
            'user' => $request->user(),
        ]);
    }

    public function chatIndex(Request $request): View
    {
        abort_if($request->user()->isAdmin(), 403);

        $conversation = SupportConversation::where('customer_id', $request->user()->id)->first();
        if ($conversation) {
            $conversation->update(['customer_unread_count' => 0]);

            return $this->renderConversation($conversation, false);
        }

        return view('support.index', [
            'conversations' => collect(),
            'conversation' => null,
            'messages' => collect(),
            'isAdmin' => false,
        ]);
    }

    public function start(Request $request): RedirectResponse
    {
        abort_if($request->user()->isAdmin(), 403);

        $conversation = SupportConversation::firstOrCreate([
            'customer_id' => $request->user()->id,
        ]);

        return redirect()->route('support.show', $conversation->id);
    }

    public function show(Request $request, int $conversationId): View
    {
        $conversation = $this->customerConversation($conversationId, $request->user()->id);
        $conversation->update(['customer_unread_count' => 0]);

        return $this->renderConversation($conversation, false);
    }

    public function adminIndex(): View
    {
        $conversations = SupportConversation::with(['customer', 'lastMessage.sender'])
            ->orderByDesc('last_message_at')
            ->paginate(20);

        return view('support.index', [
            'conversations' => $conversations,
            'conversation' => null,
            'messages' => collect(),
            'isAdmin' => true,
        ]);
    }

    public function adminShow(int $conversationId): View
    {
        $conversation = SupportConversation::findOrFail($conversationId);
        $conversation->update(['admin_unread_count' => 0]);

        return $this->renderConversation($conversation, true);
    }

    public function messages(Request $request, int $conversationId): JsonResponse
    {
        $conversation = $this->authorizedConversation($request, $conversationId);
        $isAdmin = $request->routeIs('admin.support-chats.messages');
        $conversation->update([$isAdmin ? 'admin_unread_count' : 'customer_unread_count' => 0]);
        $validated = $request->validate([
            'after' => ['sometimes', 'integer', 'min:0'],
        ]);

        return response()->json(
            $conversation->messages()
                ->where('id', '>', $validated['after'] ?? 0)
                ->with('sender:id,name')
                ->oldest('id')
                ->limit(50)
                ->get()
                ->map(fn (SupportMessage $message): array => $this->serializeMessage($message))
        );
    }

    public function send(Request $request, int $conversationId): JsonResponse
    {
        $conversation = $this->authorizedConversation($request, $conversationId);
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000', 'not_regex:/^\s*$/u'],
        ]);
        $isAdmin = $request->routeIs('admin.support-chats.send');

        $message = DB::transaction(function () use ($conversation, $request, $validated, $isAdmin): SupportMessage {
            $message = $conversation->messages()->create([
                'sender_id' => $request->user()->id,
                'body' => trim($validated['body']),
            ]);

            $conversation->update(['last_message_at' => $message->created_at]);
            $conversation->increment($isAdmin ? 'customer_unread_count' : 'admin_unread_count');

            return $message;
        });

        return response()->json($this->serializeMessage($message->load('sender:id,name')), 201);
    }

    private function renderConversation(SupportConversation $conversation, bool $isAdmin): View
    {
        $conversation->load('customer');
        $messages = $conversation->messages()
            ->with('sender:id,name')
            ->latest('id')
            ->limit(60)
            ->get()
            ->reverse()
            ->values();
        $conversations = $this->conversationList($isAdmin, $conversation->customer_id);

        return view('support.index', compact('conversations', 'conversation', 'messages', 'isAdmin'));
    }

    private function conversationList(bool $isAdmin, ?int $customerId = null): Collection|LengthAwarePaginator
    {
        if ($isAdmin) {
            return SupportConversation::with(['customer', 'lastMessage.sender'])
                ->orderByDesc('last_message_at')
                ->paginate(20);
        }

        return SupportConversation::where('customer_id', $customerId ?? auth()->id())
            ->with(['customer', 'lastMessage.sender'])
            ->get();
    }

    private function authorizedConversation(Request $request, int $conversationId): SupportConversation
    {
        return $request->routeIs('admin.support-chats.*')
            ? SupportConversation::findOrFail($conversationId)
            : $this->customerConversation($conversationId, $request->user()->id);
    }

    private function customerConversation(int $conversationId, int $customerId): SupportConversation
    {
        return SupportConversation::where('customer_id', $customerId)->findOrFail($conversationId);
    }

    /** @return array{id: int, sender_id: int, sender_name: string, body: string, created_at: string} */
    private function serializeMessage(SupportMessage $message): array
    {
        return [
            'id' => $message->id,
            'sender_id' => $message->sender_id,
            'sender_name' => $message->sender->name,
            'body' => $message->body,
            'created_at' => $message->created_at->toIso8601String(),
        ];
    }
}
