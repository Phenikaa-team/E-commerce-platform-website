<?php

use App\Http\Controllers\CartWebController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/product/{slug}', [ProductController::class, 'show'])->name('product.detail');

// Cart & Checkout Web Routes
Route::get('/cart', [CartWebController::class, 'index'])->name('cart');
Route::post('/cart/add', [CartWebController::class, 'add'])->name('cart.add');
Route::patch('/cart/item/{id}', [CartWebController::class, 'update'])->name('cart.update');
Route::post('/cart/toggle-select', [CartWebController::class, 'toggleSelect'])->name('cart.toggle-select');
Route::delete('/cart/item/{id}', [CartWebController::class, 'remove'])->name('cart.remove');
Route::get('/cart/count', [CartWebController::class, 'count'])->name('cart.count');
Route::post('/cart/item/{id}/variant', [CartWebController::class, 'updateVariant'])->name('cart.item.variant');
Route::post('/checkout/process', [CartWebController::class, 'processCheckout'])->name('checkout.process');
