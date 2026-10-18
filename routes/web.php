<?php

use App\Http\Controllers\Admin\AdminBrandController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminFaqController;
use App\Http\Controllers\Admin\AdminFlashSaleController;
use App\Http\Controllers\Admin\AdminMenuController;
use App\Http\Controllers\Admin\AdminNavigationController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminRevenueController;
use App\Http\Controllers\Admin\AdminReviewController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuyerOrderController;
use App\Http\Controllers\CartWebController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ChatAiController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\GeocodeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasswordOtpController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductQuestionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Seller\SellerCouponController;
use App\Http\Controllers\Seller\SellerDashboardController;
use App\Http\Controllers\Seller\SellerFinanceController;
use App\Http\Controllers\Seller\SellerFlashSaleController;
use App\Http\Controllers\Seller\SellerOrderController;
use App\Http\Controllers\Seller\SellerProductController;
use App\Http\Controllers\Seller\SellerProfileController;
use App\Http\Controllers\Seller\SellerRegisterController;
use App\Http\Controllers\Seller\SellerSettingController;
use App\Http\Controllers\StoreChatController;
use App\Http\Controllers\StoreFrontController;
use App\Http\Controllers\SupportChatController;
use App\Http\Controllers\VoucherPageController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/flash-sale', [ProductController::class, 'flashSale'])->name('flash-sale.index');
Route::get('/storage/{path}', function (string $path) {
    abort_if(str_contains($path, '..'), 404);
    $disk = Storage::disk('public');
    abort_unless($disk->exists($path), 404);

    return response()->file($disk->path($path));
})->where('path', '.*')->name('media.file');
Route::get('/search', [CatalogController::class, 'search'])->name('catalog.search');
Route::get('/api/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');
Route::get('/category/{slug}', [CatalogController::class, 'category'])->name('catalog.category');
Route::get('/brand/{brand}', [CatalogController::class, 'brand'])->name('catalog.brand');
Route::get('/vouchers', [VoucherPageController::class, 'index'])->name('vouchers.index');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.detail');
Route::post('/product/{product}/questions', [ProductQuestionController::class, 'store'])->middleware('throttle:product-questions')->name('product.questions.store');
Route::post('/questions/{question}/toggle-visibility', [ProductQuestionController::class, 'toggleVisibility'])->name('questions.toggle-visibility');
Route::delete('/questions/{question}', [ProductQuestionController::class, 'destroy'])->name('questions.destroy');
Route::get('/store/{slug}', [StoreFrontController::class, 'show'])->name('store.show');
Route::get('/shop/{slug}', fn (string $slug) => redirect()->route('store.show', $slug));
Route::get('/orders/track', [BuyerOrderController::class, 'track'])->name('orders.track');
Route::get('/support', [SupportChatController::class, 'index'])->name('support.index');
Route::post('/api/chat-ai', [ChatAiController::class, 'chat'])->middleware('throttle:15,1')->name('chat.ai');
Route::post('/api/recommendations/track', [ProductController::class, 'trackInteraction'])->name('recommendations.track');

/*
|--------------------------------------------------------------------------
| Authentication Routes (Default / Buyer)
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showAuth'])->name('login');
Route::get('/register', [AuthController::class, 'showAuth'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/auth/{provider}', [AuthController::class, 'socialRedirect'])->name('auth.social');
Route::get('/auth/{provider}/callback', [AuthController::class, 'socialCallback'])->name('auth.social.callback');

// Password Reset / Forgot Password with OTP (Email & Phone SMS)
Route::get('/forgot-password', [PasswordOtpController::class, 'showForgotPassword'])->name('password.forgot');
Route::post('/forgot-password/send-otp', [PasswordOtpController::class, 'sendForgotPasswordOtp'])->name('password.forgot.send-otp');
Route::post('/forgot-password/verify-otp', [PasswordOtpController::class, 'verifyForgotPasswordOtp'])->name('password.forgot.verify-otp');
Route::post('/forgot-password/reset', [PasswordOtpController::class, 'resetPasswordWithToken'])->name('password.forgot.reset');

// 1-Click Dev login for fast multi-role testing
Route::get('/dev-login/buyer', [AuthController::class, 'buyerDevLogin'])->name('dev.buyer-login');

/*
|--------------------------------------------------------------------------
| Cart Routes
|--------------------------------------------------------------------------
*/
Route::get('/cart', [CartWebController::class, 'index'])->name('cart');
Route::post('/cart/add', [CartWebController::class, 'add'])->name('cart.add');
Route::match(['patch', 'post'], '/cart/item/{id}', [CartWebController::class, 'update'])->name('cart.update');
Route::post('/cart/toggle-select', [CartWebController::class, 'toggleSelect'])->name('cart.toggle-select');
Route::delete('/cart/item/{id}', [CartWebController::class, 'remove'])->name('cart.remove');
Route::post('/cart/remove-selected', [CartWebController::class, 'removeSelected'])->name('cart.remove-selected');
Route::delete('/cart/selected', [CartWebController::class, 'removeSelected'])->name('cart.remove-selected.delete');
Route::get('/cart/count', [CartWebController::class, 'count'])->name('cart.count');
Route::post('/cart/item/{id}/variant', [CartWebController::class, 'updateVariant'])->name('cart.item.variant');

// Geocoding & Location Helpers
Route::get('/api/geocode/reverse', [GeocodeController::class, 'reverse'])->name('api.geocode.reverse');
Route::get('/api/geocode/ip', [GeocodeController::class, 'ipLocation'])->name('api.geocode.ip');

/*
|--------------------------------------------------------------------------
| Buyer Checkout & Payment Routes
|--------------------------------------------------------------------------
*/
Route::post('/checkout/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.apply-coupon');
Route::get('/checkout/zalopay-return', [CheckoutController::class, 'zaloPayReturn'])->name('checkout.zalopay-return');
Route::post('/checkout/zalopay-callback', [CheckoutController::class, 'zaloPayCallback'])->name('checkout.zalopay-callback');
Route::get('/checkout/success/{order_code}', [CheckoutController::class, 'success'])->name('checkout.success');

Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/order', [CheckoutController::class, 'process'])->name('checkout.order');
    Route::post('/checkout/process', [CartWebController::class, 'processCheckout'])->name('checkout.process');
    Route::post('/checkout/quick-address', [CheckoutController::class, 'quickAddAddress'])->name('checkout.quick-address');
    Route::get('/checkout/retry-payment/{identifier}', [CheckoutController::class, 'retryPayment'])->name('checkout.retry-payment');
});

/*
|--------------------------------------------------------------------------
| Buyer Hub (Authenticated User Routes)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/chat', [StoreChatController::class, 'index'])->name('chat.index');
    Route::post('/stores/{store:slug}/chat', [StoreChatController::class, 'start'])->name('chat.start');
    Route::get('/chat/{conversationId}', [StoreChatController::class, 'show'])->whereNumber('conversationId')->name('chat.show');
    Route::get('/chat/{conversationId}/messages', [StoreChatController::class, 'messages'])->whereNumber('conversationId')->name('chat.messages');
    Route::post('/chat/{conversationId}/messages', [StoreChatController::class, 'send'])->middleware('throttle:30,1')->whereNumber('conversationId')->name('chat.send');

    Route::get('/support/chat', [SupportChatController::class, 'chatIndex'])->name('support.chat');
    Route::post('/support/conversations', [SupportChatController::class, 'start'])->name('support.start');
    Route::get('/support/{conversationId}', [SupportChatController::class, 'show'])->whereNumber('conversationId')->name('support.show');
    Route::get('/support/{conversationId}/messages', [SupportChatController::class, 'messages'])->middleware('throttle:120,1')->whereNumber('conversationId')->name('support.messages');
    Route::post('/support/{conversationId}/messages', [SupportChatController::class, 'send'])->middleware('throttle:30,1')->whereNumber('conversationId')->name('support.send');

    // Profile, Personal Info, Addresses & Security
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/info', [ProfileController::class, 'info'])->name('profile.info');
    Route::post('/profile/update', [ProfileController::class, 'update'])->middleware('throttle:profile-updates')->name('profile.update');
    Route::get('/profile/addresses', [ProfileController::class, 'addresses'])->name('profile.addresses');
    Route::post('/profile/address', [ProfileController::class, 'addAddress'])->name('profile.address.add');
    Route::put('/profile/address/{id}', [ProfileController::class, 'updateAddress'])->name('profile.address.update');
    Route::post('/profile/address/{id}/default', [ProfileController::class, 'setDefaultAddress'])->name('profile.address.default');
    Route::delete('/profile/address/{id}', [ProfileController::class, 'deleteAddress'])->name('profile.address.delete');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/password/send-otp', [PasswordOtpController::class, 'sendProfileOtp'])->name('profile.password.send-otp');
    Route::post('/profile/password/otp-update', [PasswordOtpController::class, 'updateProfilePasswordWithOtp'])->name('profile.password.otp-update');
    Route::delete('/profile/account', [ProfileController::class, 'destroyAccount'])->name('profile.destroy');

    // Buyer Order Management
    Route::get('/user/orders', [BuyerOrderController::class, 'index'])->name('user.orders');
    Route::get('/user/orders/{order_code}', [BuyerOrderController::class, 'show'])->name('user.orders.show');
    Route::get('/user/orders/{order_code}/invoice', [BuyerOrderController::class, 'invoice'])->name('user.orders.invoice');
    Route::post('/user/orders/{order_code}/cancel', [BuyerOrderController::class, 'cancel'])->name('user.orders.cancel');
    Route::post('/user/orders/{order_code}/confirm', [BuyerOrderController::class, 'confirmReceipt'])->name('user.orders.confirm');
    Route::post('/user/orders/{order_code}/reorder', [BuyerOrderController::class, 'reorder'])->name('user.orders.reorder');

    // Social Proof: Reviews
    Route::post('/user/reviews', [ReviewController::class, 'store'])->middleware('throttle:user-reviews')->name('user.reviews.store');
    Route::post('/user/reviews/{review}/toggle-visibility', [ReviewController::class, 'toggleVisibility'])->name('user.reviews.toggle-visibility');
    Route::delete('/user/reviews/{review}', [ReviewController::class, 'destroy'])->name('user.reviews.destroy');

    // Notifications (User, Seller, Admin)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->whereNumber('id')->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
});

/*
|--------------------------------------------------------------------------
| Seller Hub (Vendor Portal)
|--------------------------------------------------------------------------
*/
// Legacy seller auth aliases. All roles now use the shared login endpoint.
Route::prefix('seller')->name('seller.')->group(function () {
    Route::get('/login', fn () => redirect()->route('login'))->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dev-login', [AuthController::class, 'sellerDevLogin'])->name('dev-login');
});

Route::middleware('auth')->prefix('seller')->name('seller.')->group(function () {
    Route::get('/', fn () => redirect()->route('seller.dashboard'));

    // Seller onboarding (open to all auth users)
    Route::get('/register', [SellerRegisterController::class, 'showRegister'])->name('register');
    Route::post('/register', [SellerRegisterController::class, 'register'])->name('register.post');
    Route::post('/register/otp/send', [SellerRegisterController::class, 'sendOtp'])->name('register.otp.send');
    Route::post('/register/otp/verify', [SellerRegisterController::class, 'verifyOtp'])->name('register.otp.verify');

    // Protected by IsSeller middleware
    Route::middleware('is_seller')->group(function () {
        Route::get('/chats', [StoreChatController::class, 'sellerIndex'])->name('chats.index');
        Route::get('/chats/{conversationId}', [StoreChatController::class, 'sellerShow'])->whereNumber('conversationId')->name('chats.show');
        Route::get('/chats/{conversationId}/messages', [StoreChatController::class, 'messages'])->whereNumber('conversationId')->name('chats.messages');
        Route::post('/chats/{conversationId}/messages', [StoreChatController::class, 'send'])->middleware('throttle:30,1')->whereNumber('conversationId')->name('chats.send');

        Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/revenue', [SellerDashboardController::class, 'revenue'])->name('revenue');

        // Products Management
        Route::get('/products', [SellerProductController::class, 'index'])->name('products.index');
        Route::get('/products/export', [SellerProductController::class, 'export'])->name('products.export');
        Route::get('/products/create', [SellerProductController::class, 'create'])->name('products.create');
        Route::post('/products', [SellerProductController::class, 'store'])->middleware('throttle:seller-uploads')->name('products.store');
        Route::get('/products/{id}/edit', [SellerProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{id}', [SellerProductController::class, 'update'])->middleware('throttle:seller-uploads')->name('products.update');
        Route::delete('/products/{id}', [SellerProductController::class, 'destroy'])->name('products.destroy');
        Route::delete('/products/{id}/images/{imageId}', [SellerProductController::class, 'destroyImage'])->name('products.images.destroy');
        Route::post('/questions/{question}/answer', [ProductQuestionController::class, 'answer'])->name('questions.answer');
        Route::post('/questions/{question}/toggle-visibility', [ProductQuestionController::class, 'toggleVisibility'])->name('questions.toggle-visibility');

        // Flash Sale campaigns created by the platform admin.
        Route::get('/flash-sales', [SellerFlashSaleController::class, 'index'])->name('flash-sales.index');
        Route::post('/flash-sales/{id}/products', [SellerFlashSaleController::class, 'apply'])->name('flash-sales.products.store');
        Route::delete('/flash-sales/{id}/products/{itemId}', [SellerFlashSaleController::class, 'remove'])->name('flash-sales.products.destroy');

        // Orders Management
        Route::get('/orders', [SellerOrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/export', [SellerOrderController::class, 'export'])->name('orders.export');
        Route::post('/orders/{id}/status', [SellerOrderController::class, 'updateStatus'])->name('orders.status');

        // Store & Seller Profile
        Route::get('/profile', [SellerProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [SellerProfileController::class, 'update'])->middleware('throttle:seller-uploads')->name('profile.update');

        // Shop Vouchers / Marketing
        Route::get('/coupons', [SellerCouponController::class, 'index'])->name('coupons.index');
        Route::get('/coupons/create', [SellerCouponController::class, 'create'])->name('coupons.create');
        Route::post('/coupons', [SellerCouponController::class, 'store'])->name('coupons.store');
        Route::get('/coupons/{id}/edit', [SellerCouponController::class, 'edit'])->name('coupons.edit');
        Route::put('/coupons/{id}', [SellerCouponController::class, 'update'])->name('coupons.update');
        Route::post('/coupons/{id}/toggle', [SellerCouponController::class, 'toggle'])->name('coupons.toggle');
        Route::delete('/coupons/{id}', [SellerCouponController::class, 'destroy'])->name('coupons.destroy');

        // Finances, Escrow & Wallet Management
        Route::get('/finances', [SellerFinanceController::class, 'index'])->name('finances.index');
        Route::post('/finances/withdraw', [SellerFinanceController::class, 'withdraw'])->name('finances.withdraw');

        // Store Security & Operational Settings
        Route::get('/settings', [SellerSettingController::class, 'index'])->name('settings');
        Route::put('/settings', [SellerSettingController::class, 'update'])->middleware('throttle:seller-uploads')->name('settings.update');
        Route::post('/settings/close-store', [SellerSettingController::class, 'closeStore'])->name('settings.close-store');
    });

    // Reopen closed store route (Accessible for authenticated user whose store is closed)
    Route::post('/reopen-store', [SellerSettingController::class, 'reopenStore'])->name('settings.reopen');
});

/*
|--------------------------------------------------------------------------
| Admin Portal (System Control Panel)
|--------------------------------------------------------------------------
*/
// Legacy admin auth aliases. All roles now use the shared login endpoint.
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', fn () => redirect()->route('login'))->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dev-login', [AuthController::class, 'adminDevLogin'])->name('dev-login');
});

Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => redirect()->route('admin.dashboard'));
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/support-chats', [SupportChatController::class, 'adminIndex'])->name('support-chats.index');
    Route::get('/support-chats/{conversationId}', [SupportChatController::class, 'adminShow'])->whereNumber('conversationId')->name('support-chats.show');
    Route::get('/support-chats/{conversationId}/messages', [SupportChatController::class, 'messages'])->middleware('throttle:120,1')->whereNumber('conversationId')->name('support-chats.messages');
    Route::post('/support-chats/{conversationId}/messages', [SupportChatController::class, 'send'])->middleware('throttle:30,1')->whereNumber('conversationId')->name('support-chats.send');

    // FAQ & Support Management
    Route::resource('faqs', AdminFaqController::class)->except(['show']);
    Route::patch('faqs/{faq}/toggle', [AdminFaqController::class, 'toggle'])->name('faqs.toggle');

    // Admin Profile & Security
    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');

    // Categories CRUD
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::post('/categories/{id}/quick-nav', [AdminCategoryController::class, 'toggleQuick'])->name('categories.quick-nav');
    Route::delete('/categories/{id}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    // Brand catalog and keyword aliases
    Route::get('/brands', [AdminBrandController::class, 'index'])->name('brands.index');
    Route::post('/brands', [AdminBrandController::class, 'store'])->name('brands.store');
    Route::put('/brands/{id}', [AdminBrandController::class, 'update'])->name('brands.update');
    Route::post('/brands/{id}/approve', [AdminBrandController::class, 'approve'])->name('brands.approve');
    Route::post('/brands/{id}/toggle', [AdminBrandController::class, 'toggle'])->name('brands.toggle');
    Route::delete('/brands/{id}', [AdminBrandController::class, 'destroy'])->name('brands.destroy');
    Route::post('/brands/{id}/aliases', [AdminBrandController::class, 'storeAlias'])->name('brands.aliases.store');
    Route::put('/brand-aliases/{id}', [AdminBrandController::class, 'updateAlias'])->name('brands.aliases.update');
    Route::delete('/brand-aliases/{id}', [AdminBrandController::class, 'destroyAlias'])->name('brands.aliases.destroy');

    // Per-category flyout management: child categories and recommendation columns.
    Route::get('/navigation/{categoryId}/flyout', [AdminNavigationController::class, 'index'])->name('navigation.flyout');
    Route::post('/navigation/{categoryId}/recommendations/regenerate', [AdminNavigationController::class, 'regenerateRecommendations'])->name('navigation.recommendations.regenerate');
    Route::post('/navigation/{categoryId}/sections', [AdminNavigationController::class, 'storeSection'])->name('navigation.sections.store');
    Route::put('/navigation/{categoryId}/sections/{sectionId}', [AdminNavigationController::class, 'updateSection'])->name('navigation.sections.update');
    Route::delete('/navigation/{categoryId}/sections/{sectionId}', [AdminNavigationController::class, 'destroySection'])->name('navigation.sections.destroy');
    Route::post('/navigation/{categoryId}/sections/{sectionId}/items', [AdminNavigationController::class, 'storeItem'])->name('navigation.items.store');
    Route::put('/navigation/{categoryId}/sections/{sectionId}/items/{itemId}', [AdminNavigationController::class, 'updateItem'])->name('navigation.items.update');
    Route::delete('/navigation/{categoryId}/sections/{sectionId}/items/{itemId}', [AdminNavigationController::class, 'destroyItem'])->name('navigation.items.destroy');

    // Homepage left menu management
    Route::get('/menus', [AdminMenuController::class, 'index'])->name('menus.index');
    Route::post('/menus', [AdminMenuController::class, 'store'])->name('menus.store');
    Route::put('/menus/{id}', [AdminMenuController::class, 'update'])->name('menus.update');
    Route::delete('/menus/{id}', [AdminMenuController::class, 'destroy'])->name('menus.destroy');

    // Platform product management and category assignment
    Route::resource('products', AdminProductController::class)->except(['show']);

    // Flash Sale campaign scheduling and seller participation.
    Route::get('/flash-sales', [AdminFlashSaleController::class, 'index'])->name('flash-sales.index');
    Route::get('/flash-sales/create', [AdminFlashSaleController::class, 'create'])->name('flash-sales.create');
    Route::get('/flash-sales/schedules/create', [AdminFlashSaleController::class, 'createSchedule'])->name('flash-sales.schedules.create');
    Route::get('/flash-sales/automations/create', [AdminFlashSaleController::class, 'createAutomation'])->name('flash-sales.automations.create');
    Route::post('/flash-sales', [AdminFlashSaleController::class, 'store'])->name('flash-sales.store');
    Route::get('/flash-sales/{id}/edit', [AdminFlashSaleController::class, 'edit'])->name('flash-sales.edit');
    Route::put('/flash-sales/{id}', [AdminFlashSaleController::class, 'update'])->name('flash-sales.update');
    Route::post('/flash-sales/{id}/toggle', [AdminFlashSaleController::class, 'toggle'])->name('flash-sales.toggle');
    Route::delete('/flash-sales/{id}', [AdminFlashSaleController::class, 'destroy'])->name('flash-sales.destroy');

    // Users & Stores Management
    Route::get('/users/export', [AdminUserController::class, 'export'])->name('users.export');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('users.show');
    Route::post('/users/{id}/toggle-status', [AdminUserController::class, 'toggleUserStatus'])->name('users.toggle-status');
    Route::post('/users/{id}/role', [AdminUserController::class, 'updateUserRole'])->name('users.role');
    Route::post('/users/{id}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('/stores/{id}/approve', [AdminUserController::class, 'approveStore'])->name('stores.approve');
    Route::post('/stores/{id}/reject', [AdminUserController::class, 'rejectStore'])->name('stores.reject');
    Route::post('/stores/{id}/toggle-status', [AdminUserController::class, 'toggleStoreStatus'])->name('stores.toggle-status');
    Route::get('/stores/{id}', [AdminUserController::class, 'showStore'])->name('stores.show');

    // Coupons / Vouchers Management
    Route::get('/coupons', [AdminCouponController::class, 'index'])->name('coupons.index');
    Route::get('/coupons/create', [AdminCouponController::class, 'create'])->name('coupons.create');
    Route::post('/coupons', [AdminCouponController::class, 'store'])->name('coupons.store');
    Route::get('/coupons/{id}/edit', [AdminCouponController::class, 'edit'])->name('coupons.edit');
    Route::put('/coupons/{id}', [AdminCouponController::class, 'update'])->name('coupons.update');
    Route::post('/coupons/{id}/toggle', [AdminCouponController::class, 'toggle'])->name('coupons.toggle');
    Route::delete('/coupons/{id}', [AdminCouponController::class, 'destroy'])->name('coupons.destroy');

    // Platform Revenue & Financial Analytics
    Route::get('/revenue/export', [AdminRevenueController::class, 'export'])->name('revenue.export');
    Route::get('/revenue', [AdminRevenueController::class, 'index'])->name('revenue');

    // Platform-wide Order Monitoring
    Route::get('/orders/export', [AdminOrderController::class, 'export'])->name('orders.export');
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [AdminOrderController::class, 'show'])->name('orders.show');

    // Reviews Moderation
    Route::get('/reviews', [AdminReviewController::class, 'index'])->name('reviews.index');
    Route::post('/reviews/{id}/status', [AdminReviewController::class, 'updateStatus'])->name('reviews.status');
    Route::delete('/reviews/{id}', [AdminReviewController::class, 'destroy'])->name('reviews.destroy');
});
