<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminReviewController extends Controller
{
    /**
     * Display list of all product reviews on the platform.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');
        $rating = $request->query('rating');
        $search = trim((string) $request->query('q', ''));

        $query = Review::with(['user', 'product.store'])->latest();

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        if ($rating) {
            $query->where('rating', (int) $rating);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('product', fn ($pq) => $pq->where('name', 'like', "%{$search}%"));
            });
        }

        $reviews = $query->paginate(15)->withQueryString();

        $statusCounts = [
            'all' => Review::count(),
            'approved' => Review::where('status', 'approved')->count(),
            'pending' => Review::where('status', 'pending')->count(),
            'rejected' => Review::where('status', 'rejected')->count(),
        ];

        return view('admin.reviews.index', compact('reviews', 'status', 'rating', 'search', 'statusCounts'));
    }

    /**
     * Toggle or update review moderation status.
     */
    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $review = Review::findOrFail($id);
        $status = $request->input('status', 'approved');

        if (in_array($status, ['approved', 'pending', 'rejected'])) {
            $review->status = $status;
            $review->save();

            // Recalculate product rating if approved or unapproved
            if ($review->product) {
                $avgRating = Review::where('product_id', $review->product_id)
                    ->where('status', 'approved')
                    ->avg('rating') ?? 5.0;
                $review->product->update(['rating' => round($avgRating, 1)]);
            }
        }

        return back()->with('success', "Đã cập nhật trạng thái đánh giá thành: {$status}");
    }

    /**
     * Delete an abusive or spam review.
     */
    public function destroy(int $id): RedirectResponse
    {
        $review = Review::findOrFail($id);
        $productId = $review->product_id;
        $review->delete();

        if ($productId) {
            $product = Product::find($productId);
            if ($product) {
                $avgRating = Review::where('product_id', $productId)
                    ->where('status', 'approved')
                    ->avg('rating') ?? 5.0;
                $product->update(['rating' => round($avgRating, 1)]);
            }
        }

        return back()->with('success', 'Đã xóa đánh giá thành công.');
    }
}
