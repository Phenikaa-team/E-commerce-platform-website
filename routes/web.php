<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartWebController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.detail');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showAuth'])->name('login');
Route::get('/register', [AuthController::class, 'showAuth'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/auth/{provider}', [AuthController::class, 'socialLogin'])->name('auth.social');

// Cart & Checkout Web Routes
Route::get('/cart', [CartWebController::class, 'index'])->name('cart');
Route::post('/cart/add', [CartWebController::class, 'add'])->name('cart.add');
Route::patch('/cart/item/{id}', [CartWebController::class, 'update'])->name('cart.update');
Route::post('/cart/toggle-select', [CartWebController::class, 'toggleSelect'])->name('cart.toggle-select');
Route::delete('/cart/item/{id}', [CartWebController::class, 'remove'])->name('cart.remove');
Route::get('/cart/count', [CartWebController::class, 'count'])->name('cart.count');
Route::post('/cart/item/{id}/variant', [CartWebController::class, 'updateVariant'])->name('cart.item.variant');
Route::post('/checkout/process', [CartWebController::class, 'processCheckout'])->name('checkout.process');

// User Profile Routes (Protected)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/address', [ProfileController::class, 'addAddress'])->name('profile.address.add');
    Route::post('/profile/address/{id}/default', [ProfileController::class, 'setDefaultAddress'])->name('profile.address.default');
    Route::delete('/profile/address/{id}', [ProfileController::class, 'deleteAddress'])->name('profile.address.delete');
});
