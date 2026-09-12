<?php

use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminCouponController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BuyerOrderController;
use App\Http\Controllers\CartWebController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Seller\SellerCouponController;
use App\Http\Controllers\Seller\SellerDashboardController;
use App\Http\Controllers\Seller\SellerOrderController;
use App\Http\Controllers\Seller\SellerProductController;
use App\Http\Controllers\Seller\SellerProfileController;
use App\Http\Controllers\Seller\SellerRegisterController;
use App\Http\Controllers\VoucherPageController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/vouchers', [VoucherPageController::class, 'index'])->name('vouchers.index');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.detail');
Route::get('/api/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showAuth'])->name('login');
Route::get('/register', [AuthController::class, 'showAuth'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/auth/{provider}', [AuthController::class, 'socialLogin'])->name('auth.social');

/*
|--------------------------------------------------------------------------
| Cart Routes
|--------------------------------------------------------------------------
*/
Route::get('/cart', [CartWebController::class, 'index'])->name('cart');
Route::post('/cart/add', [CartWebController::class, 'add'])->name('cart.add');
Route::patch('/cart/item/{id}', [CartWebController::class, 'update'])->name('cart.update');
Route::post('/cart/toggle-select', [CartWebController::class, 'toggleSelect'])->name('cart.toggle-select');
Route::delete('/cart/item/{id}', [CartWebController::class, 'remove'])->name('cart.remove');
Route::get('/cart/count', [CartWebController::class, 'count'])->name('cart.count');
Route::post('/cart/item/{id}/variant', [CartWebController::class, 'updateVariant'])->name('cart.item.variant');

/*
|--------------------------------------------------------------------------
| Buyer Checkout & Payment Routes
|--------------------------------------------------------------------------
*/
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::post('/checkout/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.apply-coupon');
Route::post('/checkout/quick-address', [CheckoutController::class, 'quickAddAddress'])->name('checkout.quick-address');
Route::post('/checkout/order', [CheckoutController::class, 'process'])->name('checkout.order');
Route::get('/checkout/vnpay-return', [CheckoutController::class, 'vnpayReturn'])->name('checkout.vnpay-return');
Route::get('/checkout/success/{order_code}', [CheckoutController::class, 'success'])->name('checkout.success');

/*
|--------------------------------------------------------------------------
| Buyer Hub (Authenticated User Routes)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Profile & Address
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/address', [ProfileController::class, 'addAddress'])->name('profile.address.add');
    Route::post('/profile/address/{id}/default', [ProfileController::class, 'setDefaultAddress'])->name('profile.address.default');
    Route::delete('/profile/address/{id}', [ProfileController::class, 'deleteAddress'])->name('profile.address.delete');

    // Buyer Order Management
    Route::get('/user/orders', [BuyerOrderController::class, 'index'])->name('user.orders');
    Route::get('/user/orders/{order_code}', [BuyerOrderController::class, 'show'])->name('user.orders.show');
    Route::post('/user/orders/{order_code}/cancel', [BuyerOrderController::class, 'cancel'])->name('user.orders.cancel');
    Route::post('/user/orders/{order_code}/reorder', [BuyerOrderController::class, 'reorder'])->name('user.orders.reorder');

    // Social Proof: Reviews & Wishlist
    Route::post('/user/reviews', [ReviewController::class, 'store'])->name('user.reviews.store');
    Route::get('/user/wishlist', [WishlistController::class, 'index'])->name('user.wishlist');
    Route::post('/user/wishlist/toggle', [WishlistController::class, 'toggle'])->name('user.wishlist.toggle');
});

/*
|--------------------------------------------------------------------------
| Seller Hub (Vendor Portal)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('seller')->name('seller.')->group(function () {
    // Seller onboarding (open to all auth users)
    Route::get('/register', [SellerRegisterController::class, 'showRegister'])->name('register');
    Route::post('/register', [SellerRegisterController::class, 'register'])->name('register.post');

    // Protected by IsSeller middleware
    Route::middleware('is_seller')->group(function () {
        Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/revenue', [SellerDashboardController::class, 'revenue'])->name('revenue');

        // Products Management
        Route::get('/products', [SellerProductController::class, 'index'])->name('products.index');
        Route::get('/products/create', [SellerProductController::class, 'create'])->name('products.create');
        Route::post('/products', [SellerProductController::class, 'store'])->name('products.store');
        Route::get('/products/{id}/edit', [SellerProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{id}', [SellerProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{id}', [SellerProductController::class, 'destroy'])->name('products.destroy');

        // Orders Management
        Route::get('/orders', [SellerOrderController::class, 'index'])->name('orders.index');
        Route::post('/orders/{id}/status', [SellerOrderController::class, 'updateStatus'])->name('orders.status');

        // Store & Seller Profile
        Route::get('/profile', [SellerProfileController::class, 'index'])->name('profile');
        Route::put('/profile', [SellerProfileController::class, 'update'])->name('profile.update');

        // Shop Vouchers / Marketing
        Route::get('/coupons', [SellerCouponController::class, 'index'])->name('coupons.index');
        Route::post('/coupons', [SellerCouponController::class, 'store'])->name('coupons.store');
        Route::post('/coupons/{id}/toggle', [SellerCouponController::class, 'toggle'])->name('coupons.toggle');
        Route::delete('/coupons/{id}', [SellerCouponController::class, 'destroy'])->name('coupons.destroy');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Portal (System Control Panel)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'is_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Admin Profile & Security
    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile');
    Route::put('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');

    // Categories CRUD
    Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
    Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{id}', [AdminCategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

    // Users & Stores Management
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [AdminUserController::class, 'show'])->name('users.show');
    Route::post('/users/{id}/toggle-status', [AdminUserController::class, 'toggleUserStatus'])->name('users.toggle-status');
    Route::post('/users/{id}/role', [AdminUserController::class, 'updateUserRole'])->name('users.role');
    Route::post('/users/{id}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('/stores/{id}/toggle-status', [AdminUserController::class, 'toggleStoreStatus'])->name('stores.toggle-status');

    // Coupons / Vouchers Management
    Route::get('/coupons', [AdminCouponController::class, 'index'])->name('coupons.index');
    Route::post('/coupons', [AdminCouponController::class, 'store'])->name('coupons.store');
    Route::post('/coupons/{id}/toggle', [AdminCouponController::class, 'toggle'])->name('coupons.toggle');
    Route::delete('/coupons/{id}', [AdminCouponController::class, 'destroy'])->name('coupons.destroy');
});
