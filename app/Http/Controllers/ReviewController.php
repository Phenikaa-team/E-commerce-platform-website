<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Store customer review for a completed order item.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:6|max:1000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,avif|max:3072',
        ], [
            'comment.min' => 'Nội dung đánh giá cần tối thiểu 6 ký tự.',
            'rating.min' => 'Vui lòng chọn số sao từ 1 đến 5.',
        ]);

        $order = Order::where('id', $data['order_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // 1. Ensure order is completed
        if ($order->status !== 'completed') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chỉ có thể đánh giá sản phẩm từ đơn hàng đã hoàn thành.',
                ], 422);
            }

            return back()->withErrors(['order_id' => 'Chỉ có thể đánh giá sản phẩm từ đơn hàng đã hoàn thành.']);
        }

        // 2. Ensure product was actually purchased in this order
        $hasItem = $order->items()->where('product_id', $data['product_id'])->exists();
        if (! $hasItem) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sản phẩm này không thuộc đơn hàng của bạn.',
                ], 422);
            }

            return back()->withErrors(['product_id' => 'Sản phẩm này không thuộc đơn hàng của bạn.']);
        }

        // 3. Prevent duplicate reviews
        $existing = Review::where('user_id', auth()->id())
            ->where('order_id', $order->id)
            ->where('product_id', $data['product_id'])
            ->first();

        if ($existing) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã đánh giá sản phẩm này trong đơn hàng rồi.',
                ], 422);
            }

            return back()->withErrors(['review' => 'Bạn đã đánh giá sản phẩm này trong đơn hàng rồi.']);
        }

        // Upload images if any
        $imageUrls = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $uploaded = FileUploadService::upload($file, 'reviews');
                $imageUrls[] = $uploaded['url'];
            }
        }

        $review = Review::create([
            'user_id' => auth()->id(),
            'product_id' => $data['product_id'],
            'order_id' => $order->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'],
            'images' => $imageUrls,
            'status' => 'approved',
        ]);

        // Recalculate product rating & reviews count
        $product = Product::find($data['product_id']);
        if ($product) {
            $avgRating = Review::where('product_id', $product->id)->where('status', 'approved')->avg('rating') ?: 5.0;
            $reviewsCount = Review::where('product_id', $product->id)->where('status', 'approved')->count();
            $product->update([
                'rating' => round($avgRating, 1),
                'reviews_count' => $reviewsCount,
            ]);

            // Recalculate store rating
            if ($product->store_id) {
                $storeAvg = Review::whereHas('product', fn ($q) => $q->where('store_id', $product->store_id))
                    ->where('status', 'approved')
                    ->avg('rating') ?: 4.9;
                $product->store?->update([
                    'rating' => round($storeAvg, 1),
                ]);
            }
        }

        // Update user profile review count
        $userReviewsCount = Review::where('user_id', auth()->id())->count();
        auth()->user()->update(['review_count' => $userReviewsCount]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cảm ơn bạn đã đánh giá sản phẩm!',
                'review' => $review,
            ]);
        }

        return back()->with('success', 'Đánh giá sản phẩm thành công!');
    }
}
