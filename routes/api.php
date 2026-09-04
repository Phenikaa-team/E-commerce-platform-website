<?php

use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/health', fn () => ['status' => 'ok', 'service' => 'marketplace-api']);
    Route::get('/categories', [CatalogController::class, 'categories']);
    Route::get('/stores', [CatalogController::class, 'stores']);
    Route::get('/products', [CatalogController::class, 'products']);
    Route::get('/products/{product}', [CatalogController::class, 'showProduct']);
    Route::post('/cart/items', [CartController::class, 'add']);
    Route::get('/cart/{cart}', [CartController::class, 'show']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
});
