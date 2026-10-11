<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get notifications for the current authenticated user/context
     * Optional query param ?context=user|seller|admin
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'unread_count' => 0,
                'notifications' => [],
            ]);
        }

        $context = $request->query('context');
        if (! $context) {
            if ($user->isAdmin() && $request->is('admin*')) {
                $context = 'admin';
            } elseif ($user->isSeller() && $request->is('seller*')) {
                $context = 'seller';
            } else {
                $context = 'user';
            }
        }

        $query = Notification::query();

        if ($context === 'admin') {
            $query->where('for_role', 'admin');
        } elseif ($context === 'seller') {
            $currentStore = Store::where('user_id', $user->id)->first();
            $storeId = $currentStore?->id;
            $query->where(function ($q) use ($user, $storeId) {
                $q->where('user_id', $user->id)
                    ->where('for_role', 'seller');
                if ($storeId) {
                    $q->orWhere('store_id', $storeId);
                }
            });
        } else {
            // Buyer/User context
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->whereIn('for_role', ['user', 'buyer']);
            });
        }

        // Auto-seed welcoming/sample notifications if none exist so user can see it works right away
        if ($query->count() === 0) {
            $this->seedInitialNotifications($user, $context);
        }

        $notifications = (clone $query)
            ->latest()
            ->limit(15)
            ->get();

        $unreadCount = (clone $query)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'unread_count' => $unreadCount,
            'notifications' => $notifications->map(function (Notification $n) {
                return [
                    'id' => $n->id,
                    'type' => $n->type,
                    'title' => $n->title,
                    'message' => $n->message,
                    'link' => $n->link,
                    'is_read' => $n->isRead(),
                    'created_at_human' => $n->created_at?->diffForHumans() ?? 'Vừa xong',
                ];
            }),
        ]);
    }

    /**
     * Mark a single notification as read
     */
    public function markAsRead(Request $request, int $id): JsonResponse
    {
        $notification = Notification::findOrFail($id);
        $user = $request->user();

        // Check ownership/permissions
        if ($notification->user_id && $notification->user_id !== $user->id && ! $user->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'id' => $notification->id,
        ]);
    }

    /**
     * Mark all notifications as read for current context
     */
    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['success' => true]);
        }

        $context = $request->input('context', 'user');
        $query = Notification::whereNull('read_at');

        if ($context === 'admin') {
            if (! $user->isAdmin()) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            $query->where('for_role', 'admin');
        } elseif ($context === 'seller') {
            $currentStore = Store::where('user_id', $user->id)->first();
            $storeId = $currentStore?->id;
            $query->where(function ($q) use ($user, $storeId) {
                $q->where('user_id', $user->id)
                    ->where('for_role', 'seller');
                if ($storeId) {
                    $q->orWhere('store_id', $storeId);
                }
            });
        } else {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                    ->whereIn('for_role', ['user', 'buyer']);
            });
        }

        $query->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    private function seedInitialNotifications($user, string $context): void
    {
        if ($context === 'admin') {
            Notification::create([
                'user_id' => $user->id,
                'for_role' => 'admin',
                'type' => 'system',
                'title' => 'Chào mừng đến hệ thống quản trị',
                'message' => 'Hệ thống giám sát giao dịch và người dùng ShopMart đang vận hành ổn định.',
                'link' => route('admin.dashboard'),
            ]);
            Notification::create([
                'user_id' => $user->id,
                'for_role' => 'admin',
                'type' => 'order',
                'title' => 'Đơn hàng mới trên sàn',
                'message' => 'Có đơn hàng mới vừa được người mua đặt trên nền tảng ShopMart.',
                'link' => route('admin.orders.index'),
            ]);
            Notification::create([
                'user_id' => $user->id,
                'for_role' => 'admin',
                'type' => 'review',
                'title' => 'Yêu cầu hỗ trợ từ người dùng',
                'message' => 'Người mua đang cần hỗ trợ trực tiếp từ Quản trị viên ShopMart.',
                'link' => route('admin.support-chats.index'),
            ]);
        } elseif ($context === 'seller') {
            $currentStore = Store::where('user_id', $user->id)->first();
            Notification::create([
                'user_id' => $user->id,
                'store_id' => $currentStore?->id,
                'for_role' => 'seller',
                'type' => 'order',
                'title' => 'Đơn hàng mới cần xác nhận',
                'message' => 'Gian hàng của bạn có đơn đặt hàng mới đang chờ xử lý và chuẩn bị gói hàng.',
                'link' => route('seller.orders.index'),
            ]);
            Notification::create([
                'user_id' => $user->id,
                'store_id' => $currentStore?->id,
                'for_role' => 'seller',
                'type' => 'wallet',
                'title' => 'Cập nhật ví doanh thu',
                'message' => 'Doanh thu đơn hàng đã hoàn tất được cộng vào ví cửa hàng của bạn.',
                'link' => route('seller.finances.index'),
            ]);
            Notification::create([
                'user_id' => $user->id,
                'store_id' => $currentStore?->id,
                'for_role' => 'seller',
                'type' => 'system',
                'title' => 'Gian hàng hoạt động tốt',
                'message' => 'Chào mừng bạn đến với Kênh Người Bán ShopMart. Chúc bạn buôn may bán đắt!',
                'link' => route('seller.dashboard'),
            ]);
        } else {
            // Buyer/User
            Notification::create([
                'user_id' => $user->id,
                'for_role' => 'user',
                'type' => 'promo',
                'title' => 'Voucher ưu đãi chào bạn mới',
                'message' => 'Tặng bạn mã giảm giá tới 30% cho đơn hàng đầu tiên. Thu thập ngay trong Kho Voucher!',
                'link' => route('vouchers.index'),
            ]);
            Notification::create([
                'user_id' => $user->id,
                'for_role' => 'user',
                'type' => 'order',
                'title' => 'Cập nhật hành trình đơn hàng',
                'message' => 'Theo dõi lộ trình giao nhận các đơn hàng đã đặt dễ dàng tại mục Đơn mua.',
                'link' => route('user.orders'),
            ]);
            Notification::create([
                'user_id' => $user->id,
                'for_role' => 'user',
                'type' => 'system',
                'title' => 'Trung tâm trợ giúp ShopMart',
                'message' => 'Trợ lý AI và đội ngũ Quản trị viên luôn sẵn sàng giải đáp thắc mắc 24/7.',
                'link' => route('support.index'),
            ]);
        }
    }
}
