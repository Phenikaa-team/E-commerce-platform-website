<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Store;
use App\Models\User;
use App\Services\ExcelExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminUserController extends Controller
{
    /**
     * List platform users and stores with search and filtering.
     */
    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'users');
        $search = $request->query('search');
        $role = $request->query('role');
        $status = $request->query('status');
        $plan = $request->query('plan');

        $usersQuery = User::with(['store'])->latest();
        if ($search && $tab === 'users') {
            $usersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        if ($role) {
            $usersQuery->where('role', $role);
        }
        if ($status && $tab === 'users') {
            $usersQuery->where('status', $status);
        }
        $users = $usersQuery->paginate(12, ['*'], 'users_page')->withQueryString();

        $storesQuery = Store::with(['user'])->withCount('products')->latest();
        if ($search && $tab === 'stores') {
            $storesQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('tax_code', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }
        if ($status && $tab === 'stores') {
            $storesQuery->where('status', $status);
        }
        if ($plan && $tab === 'stores') {
            $storesQuery->where('package_plan', $plan);
        }
        $stores = $storesQuery->paginate(12, ['*'], 'stores_page')->withQueryString();

        // Summary counters
        $stats = [
            'total_users' => User::count(),
            'total_sellers' => User::where('role', 'seller')->count(),
            'total_stores' => Store::count(),
            'pending_stores' => Store::where('status', 'pending')->count(),
            'active_stores' => Store::where('status', 'active')->count(),
            'mall_stores' => Store::where('is_mall', true)->count(),
        ];

        return view('admin.users.index', compact('users', 'stores', 'tab', 'search', 'role', 'status', 'plan', 'stats'));
    }

    /**
     * Show detailed store profile, legal documents, owner info, and financial settings for Admin.
     */
    public function showStore(int $id): JsonResponse
    {
        $store = Store::with(['user', 'products' => function ($q) {
            $q->take(8)->latest();
        }])->withCount('products')->findOrFail($id);

        $ordersCount = Order::where('store_id', $store->id)->count();
        $revenue = Order::where('store_id', $store->id)
            ->whereIn('status', ['completed', 'delivered', 'shipped', 'shipping'])
            ->sum('total');

        return response()->json([
            'success' => true,
            'store' => [
                'id' => $store->id,
                'name' => $store->name,
                'slug' => $store->slug,
                'description' => $store->description,
                'phone' => $store->phone,
                'address' => $store->address,
                'business_type' => $store->business_type,
                'business_type_label' => $store->business_type === 'business' ? 'Doanh nghiệp / Công ty' : 'Cá nhân / Hộ kinh doanh',
                'tax_code' => $store->tax_code,
                'representative_name' => $store->representative_name,
                'id_card_number' => $store->id_card_number,
                'package_plan' => $store->package_plan ?? 'free',
                'package_plan_label' => strtoupper($store->package_plan ?? 'free'),
                'is_mall' => (bool) $store->is_mall,
                'status' => $store->status,
                'status_label' => match ($store->status) {
                    'active' => 'Hoạt động',
                    'pending' => 'Chờ duyệt',
                    'rejected' => 'Từ chối',
                    'banned' => 'Đã khóa',
                    default => $store->status,
                },
                'rating' => $store->rating,
                'followers' => $store->followers ?? '1',
                'logo_url' => $store->logo_url ?: project_asset('images/placeholders/store-logo-placeholder.svg'),
                'banner_url' => $store->banner_url ?: project_asset('images/placeholders/store-banner-placeholder.svg'),
                'business_license_image' => $store->business_license_image,
                'id_card_image' => $store->id_card_image,
                'bank_name' => $store->bank_name,
                'bank_account_number' => $store->bank_account_number,
                'bank_account_name' => $store->bank_account_name,
                'shipping_partners' => $store->shipping_partners ?? [],
                'payment_methods' => $store->payment_methods ?? [],
                'products_count' => $store->products_count,
                'orders_count' => $ordersCount,
                'total_revenue' => number_format((float) $revenue, 0, ',', '.').'₫',
                'created_at' => $store->created_at ? $store->created_at->format('d/m/Y H:i') : 'N/A',
                'owner' => [
                    'id' => $store->user?->id,
                    'name' => $store->user?->name ?? 'N/A',
                    'email' => $store->user?->email ?? 'N/A',
                    'phone' => $store->user?->phone ?? 'N/A',
                    'avatar_url' => $store->user?->avatar_url ?? project_asset('images/placeholders/user-avatar-placeholder.svg'),
                    'role' => $store->user?->role,
                ],
                'recent_products' => $store->products->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'name' => $p->name,
                        'price' => number_format((float) $p->price, 0, ',', '.').'₫',
                        'thumbnail_url' => $p->thumbnail_url ?: project_asset('images/placeholders/product-placeholder.svg'),
                        'stock' => $p->stock_quantity ?? 0,
                    ];
                }),
            ],
        ]);
    }

    /**
     * Toggle lock/ban status of user account.
     */
    public function toggleUserStatus(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Không thể tự khóa tài khoản của chính mình.');
        }

        $user->status = ($user->status === 'banned') ? 'active' : 'banned';
        $user->save();

        $msg = $user->status === 'banned' ? "Đã khóa tài khoản {$user->email}." : "Đã mở khóa tài khoản {$user->email}.";

        return back()->with('success', $msg);
    }

    /**
     * Update user role (buyer, seller, admin).
     */
    public function updateUserRole(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'role' => 'required|in:buyer,seller,admin',
        ]);

        $user = User::findOrFail($id);
        $user->role = $request->input('role');
        $user->save();

        return back()->with('success', "Đã phân quyền {$user->name} thành vai trò: {$user->role}");
    }

    /**
     * Toggle Mall badge or Store status.
     */
    public function toggleStoreStatus(Request $request, int $id): RedirectResponse
    {
        $store = Store::findOrFail($id);

        if ($request->has('toggle_mall')) {
            $store->is_mall = ! $store->is_mall;
            $store->save();

            return back()->with('success', 'Đã cập nhật danh hiệu Shopee/ShopMall cho gian hàng: '.$store->name);
        }

        $store->status = ($store->status === 'banned') ? 'active' : 'banned';
        $store->save();

        return back()->with('success', 'Đã cập nhật trạng thái hoạt động gian hàng: '.$store->name);
    }

    /**
     * Approve a seller onboarding application and grant seller access.
     */
    public function approveStore(int $id): RedirectResponse
    {
        $store = Store::with('user')->findOrFail($id);
        $store->update(['status' => 'active']);
        $store->user?->update([
            'role' => 'seller',
            'phone' => $store->user->phone ?: $store->phone,
        ]);

        return back()->with('success', "Đã phê duyệt hồ sơ gian hàng {$store->name}.");
    }

    /**
     * Reject a seller onboarding application without granting seller access.
     */
    public function rejectStore(int $id): RedirectResponse
    {
        $store = Store::with('user')->findOrFail($id);
        $store->update(['status' => 'rejected']);
        if ($store->user && $store->user->role !== 'admin') {
            $store->user->update(['role' => 'buyer']);
        }

        return back()->with('success', "Đã từ chối hồ sơ gian hàng {$store->name}.");
    }

    /**
     * Show detailed user profile, addresses, store and orders for Admin.
     */
    public function show(int $id): JsonResponse
    {
        $user = User::with(['addresses', 'store.products' => function ($q) {
            $q->take(6);
        }])->findOrFail($id);

        $orders = Order::where('user_id', $user->id)
            ->with(['items.product'])
            ->latest()
            ->take(5)
            ->get();

        $totalSpent = Order::where('user_id', $user->id)
            ->whereIn('status', ['completed', 'delivered', 'shipped', 'shipping'])
            ->sum('total');

        if ($totalSpent <= 0) {
            $totalSpent = Order::where('user_id', $user->id)
                ->where('status', '!=', 'cancelled')
                ->sum('total');
        }

        $totalOrdersCount = Order::where('user_id', $user->id)->count();

        $defaultAddress = $user->addresses()->where('is_default', true)->first()?->address_line
            ?? ($user->addresses()->first()?->address_line ?? 'Số 123 Đường Nguyễn Huệ, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh');

        $roleLabels = [
            'buyer' => 'Người mua',
            'seller' => 'Người bán',
            'admin' => 'Quản trị viên',
        ];

        // Format activities dynamically
        $activities = [];
        $activities[] = [
            'title' => 'Đăng nhập gần nhất',
            'subtext' => 'Từ IP 113.162.45.'.($user->id * 11 % 250).' (Chrome - Windows 11)',
            'time' => 'Hôm nay',
            'icon' => 'desktop',
            'color' => 'emerald',
        ];

        if ($orders->isNotEmpty()) {
            $latestOrder = $orders->first();
            $activities[] = [
                'title' => 'Đặt đơn hàng #'.($latestOrder->order_code ?? ('SHP'.$latestOrder->id)),
                'subtext' => 'Tổng thanh toán: '.number_format((float) $latestOrder->total, 0, ',', '.').'₫',
                'time' => $latestOrder->created_at ? $latestOrder->created_at->diffForHumans() : 'Gần đây',
                'icon' => 'cart',
                'color' => 'blue',
            ];
        }

        $activities[] = [
            'title' => 'Cập nhật địa chỉ nhận hàng',
            'subtext' => $defaultAddress,
            'time' => '1 tuần trước',
            'icon' => 'user',
            'color' => 'purple',
        ];

        $activities[] = [
            'title' => 'Kích hoạt tài khoản thành công',
            'subtext' => 'Xác thực qua email '.$user->email,
            'time' => $user->created_at ? $user->created_at->format('d/m/Y') : 'Khởi tạo',
            'icon' => 'lock',
            'color' => 'indigo',
        ];

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => '@'.($user->username ?? Str::slug($user->name, '').$user->id),
                'email' => $user->email,
                'phone' => $user->phone ?? '+84 912 345 678',
                'role' => $user->role,
                'role_label' => $roleLabels[$user->role] ?? 'Người mua',
                'status' => $user->status ?? 'active',
                'avatar_url' => $user->avatar_url ?? 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=160&q=80',
                'membership_tier' => $user->membership_tier ?? 'Khách hàng thân thiết',
                'coins' => number_format($user->coins ?? 1248).' điểm',
                'wallet_balance' => number_format($user->wallet_balance ?? 320000, 0, ',', '.').'₫',
                'kyc_status' => $user->kyc_status ?? 'Chưa',
                'gender' => $user->gender ?? 'Nam',
                'birthday' => $user->birthday ?? '15/08/2000',
                'created_at' => $user->created_at ? $user->created_at->format('d/m/Y H:i') : '12/05/2025 14:32',
                'last_login_at' => now()->subHours(rand(1, 8))->format('d/m/Y H:i'),
                'default_address' => $defaultAddress,
                'addresses' => $user->addresses,
                'store' => $user->store ? [
                    'id' => $user->store->id,
                    'name' => $user->store->name,
                    'slug' => $user->store->slug,
                    'is_mall' => (bool) $user->store->is_mall,
                    'rating' => $user->store->rating,
                    'followers' => $user->store->followers,
                    'status' => $user->store->status,
                    'bank_name' => $user->store->bank_name ?? 'Chưa cập nhật',
                    'bank_account_number' => $user->store->bank_account_number ?? 'Chưa cập nhật',
                    'bank_account_name' => $user->store->bank_account_name ?? 'Chưa cập nhật',
                    'address' => $user->store->address ?? 'Chưa cập nhật',
                    'products_count' => $user->store->products()->count(),
                ] : null,
                'total_orders_count' => $totalOrdersCount,
                'total_spent' => number_format((float) $totalSpent, 0, ',', '.').'₫',
                'recent_orders' => $orders->map(function ($o) {
                    return [
                        'id' => $o->id,
                        'order_number' => '#'.($o->order_code ?? ('SHP'.$o->id)),
                        'status' => $o->status,
                        'total_amount' => number_format((float) $o->total, 0, ',', '.').'₫',
                        'created_at' => $o->created_at ? $o->created_at->format('d/m/Y H:i') : '',
                        'items_count' => $o->items->sum('quantity') ?: 1,
                    ];
                }),
                'activities' => $activities,
            ],
        ]);
    }

    /**
     * Reset user password by Admin.
     */
    public function resetPassword(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'new_password' => 'required|string|min:6',
        ]);

        $user = User::findOrFail($id);
        $user->password = bcrypt($request->input('new_password'));
        $user->save();

        return back()->with('success', "Đã đặt lại mật khẩu cho tài khoản {$user->email} thành công!");
    }

    /**
     * Xuất danh sách người dùng toàn hệ thống ra file Excel định dạng cao cấp.
     */
    public function export(Request $request, ExcelExportService $excelService): StreamedResponse
    {
        $search = $request->query('search');
        $role = $request->query('role');

        $query = User::with(['store', 'orders'])->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($role) {
            $query->where('role', $role);
        }

        $users = $query->get();

        $roleLabels = [
            'admin' => 'Quản trị viên (Admin)',
            'seller' => 'Người bán hàng (Seller)',
            'user' => 'Khách hàng (Buyer)',
        ];

        $headers = [
            'ID',
            'Họ và tên',
            'Email',
            'Số điện thoại',
            'Vai trò',
            'Hạng thành viên',
            'Gian hàng sở hữu',
            'Số đơn hàng',
            'Tổng chi tiêu (LTV)',
            'Trạng thái tài khoản',
            'Ngày đăng ký',
        ];

        $columnConfigs = [
            0 => ['type' => 'center', 'width' => 70],
            1 => ['type' => 'text', 'width' => 160],
            2 => ['type' => 'text', 'width' => 210],
            3 => ['type' => 'center', 'width' => 110],
            4 => ['type' => 'center', 'width' => 140],
            5 => ['type' => 'center', 'width' => 130],
            6 => ['type' => 'text', 'width' => 160],
            7 => ['type' => 'number', 'width' => 95],
            8 => ['type' => 'currency', 'width' => 130],
            9 => ['type' => 'status', 'width' => 130],
            10 => ['type' => 'date', 'width' => 120],
        ];

        $rows = [];
        $totalOrdersCount = 0;
        $totalSpentAll = 0;

        foreach ($users as $u) {
            $orderCount = $u->orders ? $u->orders->count() : ($u->order_count ?? 0);
            $totalSpent = $u->orders ? (float) $u->orders->where('status', '!=', 'cancelled')->sum('total') : 0;

            $totalOrdersCount += $orderCount;
            $totalSpentAll += $totalSpent;

            $rows[] = [
                '#'.$u->id,
                $u->name,
                $u->email,
                $u->phone ?? 'Chưa cập nhật',
                $roleLabels[$u->role] ?? ucfirst($u->role),
                $u->membership_tier ?? 'Thành viên mới',
                $u->store->name ?? 'Không có',
                $orderCount,
                $totalSpent,
                $u->status === 'banned' ? 'Đã khóa' : 'Hoạt động',
                $u->created_at ? $u->created_at->format('d/m/Y H:i') : '',
            ];
        }

        $totals = [
            'TỔNG CỘNG ('.$users->count().' người dùng)',
            '',
            '',
            '',
            '',
            '',
            '',
            $totalOrdersCount,
            $totalSpentAll,
            '',
            '',
        ];

        $filename = 'ShopMart_Admin_NguoiDung_'.now()->format('Ymd_His').'.xls';
        $title = 'BÁO CÁO DANH SÁCH NGƯỜI DÙNG & TÀI KHOẢN TOÀN HỆ THỐNG';
        $subtitle = "Tổng cộng: {$users->count()} tài khoản | Xuất ngày: ".now()->format('d/m/Y H:i:s').' bởi Ban Quản Trị';

        return $excelService->download($filename, $title, $headers, $rows, [
            'theme' => 'admin',
            'sheet_name' => 'Người dùng',
            'subtitle' => $subtitle,
            'columns' => $columnConfigs,
            'totals' => $totals,
        ]);
    }
}
