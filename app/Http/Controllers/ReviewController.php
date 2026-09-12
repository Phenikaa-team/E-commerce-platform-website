<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
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
            'images.*' => 'nullable|image|max:2048',
        ]);

        $order = Order::where('id', $data['order_id'])
            ->where('user_id', auth()->id())
            ->firstOrFail();

        // Upload images if any
        $imageUrls = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('reviews', 'public');
                $imageUrls[] = '/storage/'.$path;
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

        // Recalculate product rating
        $product = Product::find($data['product_id']);
        if ($product) {
            $avgRating = Review::where('product_id', $product->id)->where('status', 'approved')->avg('rating');
            $reviewsCount = Review::where('product_id', $product->id)->where('status', 'approved')->count();
            $product->update([
                'rating' => round($avgRating, 1),
                'reviews_count' => $reviewsCount,
            ]);
        }

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
